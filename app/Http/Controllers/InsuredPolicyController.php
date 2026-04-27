<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Plan;
use App\Models\Policy;

use App\Enums\PolicyStatusEnum;

class InsuredPolicyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $policies = $user->policies()->with(['vehicle', 'plan'])->get();
        return view('insured.my-policies', compact('policies'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $uninsuredVehicles = $user->vehicles()->whereDoesntHave('policy')->get();
        $plansJSON = json_decode(file_get_contents(database_path('data/plans.json')), true);
        $dbPlans = Plan::all()->pluck('id', 'name');

        return view('insured.my-policies-create', compact('uninsuredVehicles', 'plansJSON', 'dbPlans'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:insured_vehicles,id',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $vehicle = $user->vehicles()->findOrFail($validated['vehicle_id']);
        if ($vehicle->policy()->exists()) {
            return back()->withErrors(['error' => 'Este vehiculo ya tiene una poliza actica']);
        }

        $startDate = now();
        $endDate = clone $startDate;
        $endDate->addYear();

        $policy = Policy::create([
            'folio' => Str::uuid(),
            'status' => PolicyStatusEnum::ACTIVE,
            'begin_validity' => $startDate,
            'end_validity' => $endDate,
            'vehicle_id' => $vehicle->id,
            'insured_id' => $user->id,
            'plan_id' => $validated['plan_id']
        ]);

        return redirect()->route('myPolicies')->with('success', '¡Poliza adquirida exitosamente! Ahora tu vehiclo esta asegurado');
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $policy = $user->policies()->with(['vehicle.vehicleModel', 'plan'])->findOrFail($id);
        return view('insured.my-policies-show', compact('policy'));
    }

    public function downloadPdf(Request $request, $id)
    {
        $user = $request->user();
        $policy = $user->policies()->with(['vehicle.vehicleModel', 'plan'])->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.policy', compact('policy', 'user'));
        return $pdf->download('Poliza_DFM_' . $policy->folio . '.pdf');
    }
}
