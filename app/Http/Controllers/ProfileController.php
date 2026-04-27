<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Sinister;
use App\Models\Policy;
use App\Models\User;
use App\Models\Role;
use App\Models\Address;
use App\Models\Fiscal;
use App\Enums\GenderEnum;
use App\Enums\TaxRegimeEnum;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load([
            'role',
            'address',
            'fiscalData',
            'vehicles.vehicleModel',
            'policies.vehicle.vehicleModel',
            'policies.plan',
        ]);

        $stats = $this->buildStats($user);

        return view('profile', compact('user', 'stats'));
    }

    public function edit(Request $request)
    {
        $user = $request->user()->load(['role', 'address', 'fiscalData']);

        $genderOptions    = GenderEnum::cases();
        $taxRegimeOptions = TaxRegimeEnum::cases();

        return view('profile-edit', compact('user', 'genderOptions', 'taxRegimeOptions'));
    }

    public function update(Request $request)
    {
        $user = $request->user()->load(['address', 'fiscalData']);

        // ── Validación base ──
        $rules = [
            'name'            => ['required', 'string', 'max:100'],
            'father_lastname' => ['required', 'string', 'max:100'],
            'mother_lastname' => ['nullable', 'string', 'max:100'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'birth_date'      => ['nullable', 'date'],
            'gender'          => ['nullable', 'string'],
            'photo'           => ['nullable', 'image', 'max:5120'], // 5 MB
            'photo_storage'   => ['nullable', 'in:url,blob'],
        ];

        // Validación extra para asegurado
        if ($user->isInsured()) {
            $rules = array_merge($rules, [
                'street'          => ['nullable', 'string', 'max:200'],
                'external_number' => ['nullable', 'string', 'max:20'],
                'internal_number' => ['nullable', 'string', 'max:20'],
                'neighborhood'    => ['nullable', 'string', 'max:100'],
                'city'            => ['nullable', 'string', 'max:100'],
                'state'           => ['nullable', 'string', 'max:100'],
                'country'         => ['nullable', 'string', 'max:100'],
                'zip_code'        => ['nullable', 'string', 'max:10'],
                'rfc'             => ['nullable', 'string', 'max:13'],
                'company_name'    => ['nullable', 'string', 'max:200'],
                'tax_regime'      => ['nullable', 'string'],
            ]);
        }

        $data = $request->validate($rules);

        // ── Foto de perfil ──
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $storage = $request->input('photo_storage', 'url');
            if ($storage === 'blob') {
                $bytes = file_get_contents($request->file('photo')->getRealPath());
                $user->profile_picture_blob = base64_encode($bytes);
                $user->profile_picture_url  = null;
            } else {
                // Guardar como archivo en storage/app/public/profiles/
                $path = $request->file('photo')->store('profiles', 'public');
                $user->profile_picture_url  = 'storage/' . $path;
                $user->profile_picture_blob = null;
            }
        }

        // ── Datos básicos ──
        $user->name            = $data['name'];
        $user->father_lastname = $data['father_lastname'];
        $user->mother_lastname = $data['mother_lastname'] ?? $user->mother_lastname;
        $user->phone           = $data['phone'] ?? $user->phone;
        $user->birth_date      = $data['birth_date'] ?? $user->birth_date;
        $user->gender          = $data['gender'] ?? $user->gender;
        $user->save();

        // ── Dirección (solo asegurado) ──
        if ($user->isInsured()) {
            $addressFields = [
                'street', 'external_number', 'internal_number',
                'neighborhood', 'city', 'state', 'country', 'zip_code',
            ];
            $addressData = array_filter(
                array_intersect_key($data, array_flip($addressFields)),
                fn($v) => $v !== null
            );

            if ($addressData) {
                if ($user->address) {
                    $user->address->update($addressData);
                } else {
                    $address = Address::create(array_merge($addressData, ['type' => 'personal']));
                    $user->address_id = $address->id;
                    $user->save();
                }
            }

            // ── Datos Fiscales (solo asegurado) ──
            if (!empty($data['rfc'])) {
                $fiscalData = [
                    'rfc'          => $data['rfc'],
                    'company_name' => $data['company_name'] ?? null,
                    'tax_regime'   => $data['tax_regime'] ?? null,
                ];

                if ($user->fiscalData) {
                    $user->fiscalData->update($fiscalData);
                } else {
                    $fiscal = Fiscal::create(array_merge($fiscalData, [
                        'user_id'     => $user->id,
                        'fiscal_type' => 'fisica',
                    ]));
                }
            }
        }

        return redirect()->route('profile')->with('success', 'Perfil actualizado correctamente.');
    }

    // ── Stats por rol ──
    private function buildStats(User $user): array
    {
        if ($user->isInsured()) {
            $policyIds = $user->policies->pluck('id');
            return [
                'total_policies'  => $user->policies->count(),
                'active_policies' => $user->policies->filter(fn($p) => $p->isActive())->count(),
                'total_sinisters' => Sinister::whereIn('policy_id', $policyIds)->count(),
                'in_review'       => Sinister::whereIn('policy_id', $policyIds)->where('status', 'in_review')->count(),
            ];
        }

        if ($user->isAdjuster()) {
            return [
                'active_sinisters' => Sinister::where('adjuster_id', $user->id)
                    ->whereNotIn('status', ['closed', 'rejected', 'total_loss'])->count(),
                'total_sinisters'  => Sinister::where('adjuster_id', $user->id)->count(),
            ];
        }

        if ($user->isSupervisor()) {
            return [
                'supervised' => Sinister::where('supervisor_id', $user->id)->count(),
                'in_review'  => Sinister::where('supervisor_id', $user->id)->where('status', 'in_review')->count(),
                'closed'     => Sinister::where('supervisor_id', $user->id)
                    ->whereIn('status', ['closed', 'rejected', 'total_loss', 'approved'])->count(),
            ];
        }

        if ($user->isAdmin()) {
            $insuredRoleId    = Role::where('name', 'insured')->value('id');
            $adjusterRoleId   = Role::where('name', 'adjuster')->value('id');
            $supervisorRoleId = Role::where('name', 'supervisor')->value('id');
            return [
                'total_insured'    => User::where('role_id', $insuredRoleId)->count(),
                'total_adjuster'   => User::where('role_id', $adjusterRoleId)->count(),
                'total_supervisor' => User::where('role_id', $supervisorRoleId)->count(),
                'active_policies'  => Policy::where('status', 'active')->where('end_validity', '>=', today())->count(),
                'total_sinisters'  => Sinister::count(),
                'sinisters_month'  => Sinister::whereMonth('report_date', now()->month)
                    ->whereYear('report_date', now()->year)->count(),
            ];
        }

        return [];
    }
}
