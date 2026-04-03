<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsuredPolicyController extends Controller
{
    public function index()
    {
        $policies = auth()->user()->policies()->with(['vehicle', 'plan'])->get();
        return view('insured.my-policies', compact('policies'));
    }

    public function create()
    {
        // Obtener vehículos sin póliza (whereDoesntHave)
        $uninsuredVehicles = auth()->user()->vehicles()->whereDoesntHave('policy')->get();
        // Cargar json de planes
        $plansJson = json_decode(file_get_contents(database_path('data/plans.json')), true);

        // Si tenemos la tabla plans y queremos empatar los IDs
        $dbPlans = \App\Models\Plan::all()->keyBy('name');

        return view('insured.my-policies-create', compact('uninsuredVehicles', 'plansJson', 'dbPlans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:insured_vehicles,id',
            'plan_id' => 'required|exists:plans,id',
            'payment_period' => 'required|string'
        ]);

        // Verificar si el vehículo ya tiene póliza
        $vehicle = auth()->user()->vehicles()->findOrFail($validated['vehicle_id']);
        if ($vehicle->policy()->exists()) {
            return back()->withErrors(['error' => 'Este vehículo ya tiene una póliza activa.']);
        }

        // Crear la póliza a futuro 1 año
        $startDate = now();
        $endDate = clone $startDate;
        $endDate->addYear();

        $policy = \App\Models\Policy::create([
            'folio' => strtoupper(\Illuminate\Support\Str::random(10)), // O generado
            'status' => \App\Enums\PolicyStatusEnum::ACTIVE,
            'begin_validity' => $startDate,
            'end_validity' => $endDate,
            'vehicle_id' => $vehicle->id,
            'insured_id' => auth()->id(),
            'plan_id' => $validated['plan_id']
        ]);

        return redirect()->route('myPolicies')->with('success', '¡Póliza adquirida exitosamente! Ahora tu vehículo está asegurado.');
    }
}
