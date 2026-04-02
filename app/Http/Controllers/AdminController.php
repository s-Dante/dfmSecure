<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Enums\RoleEnum;

class AdminController extends Controller
{
    /** Roles que el admin puede gestionar */
    private const MANAGED_ROLES = ['adjuster', 'supervisor', 'admin'];

    /**
     * Lista de empleados con filtro de rol y búsqueda.
     */
    public function index(Request $request)
    {
        $roleFilter = $request->get('role', 'all');
        $search     = $request->get('search', '');

        $managedRoleIds = Role::whereIn('name', self::MANAGED_ROLES)->pluck('id');

        $query = User::with('role')
            ->whereIn('role_id', $managedRoleIds)
            ->withTrashed();

        // Filtro por rol
        if ($roleFilter !== 'all') {
            $filteredRoleId = Role::where('name', $roleFilter)->value('id');
            if ($filteredRoleId) {
                $query->where('role_id', $filteredRoleId);
            }
        }

        // Búsqueda por nombre o email
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('father_lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest()->paginate(10)->withQueryString();

        // Contadores por rol para las tabs
        $counts = [
            'all'        => User::whereIn('role_id', $managedRoleIds)->withTrashed()->count(),
            'adjuster'   => User::whereIn('role_id', Role::where('name', 'adjuster')->pluck('id'))->withTrashed()->count(),
            'supervisor' => User::whereIn('role_id', Role::where('name', 'supervisor')->pluck('id'))->withTrashed()->count(),
            'admin'      => User::whereIn('role_id', Role::where('name', 'admin')->pluck('id'))->withTrashed()->count(),
        ];

        $roles = Role::whereIn('name', self::MANAGED_ROLES)->get();

        return view('admin.employes-manage', compact('employees', 'counts', 'roles', 'roleFilter', 'search'));
    }

    /**
     * Crea un nuevo empleado.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'            => ['required', 'string', 'max:100'],
            'father_lastname' => ['required', 'string', 'max:100'],
            'mother_lastname' => ['nullable', 'string', 'max:100'],
            'email'           => ['required', 'email', 'unique:users,email'],
            'phone'           => ['required', 'string', 'max:20'],
            'birth_date'      => ['required', 'date', 'before_or_equal:-18 years'],
            'role'            => ['required', 'string', 'in:' . implode(',', self::MANAGED_ROLES)],
            'password'        => ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[\/\-\_\*\&\(\)]/', 'regex:/^[a-zA-Z0-9\/\-\_\*\&\(\)]+$/'],
        ]);

        $role = Role::where('name', $request->role)->firstOrFail();

        // Generar username único
        $baseUsername = Str::slug(
            strtolower(substr($request->name, 0, 1) . $request->father_lastname)
        );
        $username = $baseUsername . rand(10, 999);
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . rand(10, 999);
        }

        User::create([
            'name'            => $request->name,
            'father_lastname' => $request->father_lastname,
            'mother_lastname' => $request->mother_lastname,
            'email'           => $request->email,
            'username'        => $username,
            'phone'           => $request->phone ?? '',
            'birth_date'      => $request->birth_date,
            'role_id'         => $role->id,
            'password'        => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('manage')
            ->with('success', "Empleado '{$request->name} {$request->father_lastname}' creado correctamente.");
    }

    /**
     * Formulario de edición de un empleado.
     */
    public function edit(int $id)
    {
        $employee = User::with('role')->findOrFail($id);
        $this->authorizeEmployee($employee);

        $roles = Role::whereIn('name', self::MANAGED_ROLES)->get();

        return view('admin.employee-edit', compact('employee', 'roles'));
    }

    /**
     * Actualiza los datos de un empleado.
     */
    public function update(Request $request, int $id)
    {
        $employee = User::findOrFail($id);
        $this->authorizeEmployee($employee);

        $request->validate([
            'name'            => ['required', 'string', 'max:100'],
            'father_lastname' => ['required', 'string', 'max:100'],
            'mother_lastname' => ['nullable', 'string', 'max:100'],
            'email'           => ['required', 'email', "unique:users,email,{$id}"],
            'phone'           => ['nullable', 'string', 'max:20'],
            'birth_date'      => ['nullable', 'date'],
            'role'            => ['required', 'string', 'in:' . implode(',', self::MANAGED_ROLES)],
            'password'        => ['nullable', 'string', 'min:8'],
        ]);

        $role = Role::where('name', $request->role)->firstOrFail();

        $employee->name            = $request->name;
        $employee->father_lastname = $request->father_lastname;
        $employee->mother_lastname = $request->mother_lastname;
        $employee->email           = $request->email;
        $employee->phone           = $request->phone ?? $employee->phone;
        $employee->birth_date      = $request->birth_date;
        $employee->role_id         = $role->id;

        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }

        $employee->save();

        return redirect()
            ->route('manage')
            ->with('success', "Datos de '{$employee->name} {$employee->father_lastname}' actualizados.");
    }

    /**
     * Elimina (soft delete) a un empleado.
     */
    public function destroy(Request $request, int $id)
    {
        $employee = User::findOrFail($id);
        $this->authorizeEmployee($employee);

        // No puede darse de baja a sí mismo
        abort_if($employee->id === $request->user()->id, 403, 'No puedes darte de baja a ti mismo.');

        $employee->delete(); // SoftDelete

        return redirect()
            ->route('manage')
            ->with('success', "Empleado '{$employee->name} {$employee->father_lastname}' dado de baja.");
    }

    /**
     * Reestablece un empleado dado de baja (un-soft delete).
     */
    public function restore(Request $request, int $id)
    {
        $employee = User::withTrashed()->findOrFail($id);
        $this->authorizeEmployee($employee);

        abort_if(!$employee->trashed(), 400, 'El empleado no está dado de baja.');

        $employee->restore();

        return redirect()
            ->route('manage')
            ->with('success', "Empleado '{$employee->name} {$employee->father_lastname}' reestablecido.");
    }

    /**
     * Verifica que el usuario a gestionar sea un empleado (no un asegurado).
     */
    private function authorizeEmployee(User $user): void
    {
        $managedRoleIds = Role::whereIn('name', self::MANAGED_ROLES)->pluck('id');
        abort_unless($managedRoleIds->contains($user->role_id), 403);
    }
}
