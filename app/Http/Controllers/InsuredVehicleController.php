<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleModel;
use App\Models\Policy;
use Symfony\Contracts\Service\Attribute\Required;

class InsuredVehicleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $vehicles = $user->vehicles()->with(['vehicleModel', 'policy'])->get();
        $vehiclesJSON = json_decode(file_get_contents(database_path('data/vehicles.json')), true);
        //dd($vehiclesJSON);
        return view('insured.my-vehicles', compact('vehicles', 'vehiclesJSON'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer',
            'brand' => 'required|string',
            'sub_brand' => 'required|string',
            'version' => 'required|string',
            'color' => 'required|string',
            'vin' => 'required|string|unique:insured_vehicles,vin',
            'plate' => 'required|string|unique:insured_vehicles,plate'
        ]);

        $vm = VehicleModel::firstOrCreate([
            'year' => $validated['year'],
            'brand' => $validated['brand'],
            'sub_brand' => $validated['sub_brand'],
            'version' => $validated['version'],
            'color' => $validated['color']
        ]);

        $user = $request->user();
        $user->vehicles()->create([
            'vin' => $validated['vin'],
            'plate' => $validated['plate'],
            'vehicle_model_id' => $vm->id
        ]);

        return redirect()->route('myVehicles')->with('success', 'Vehículo agregado correctamente');
    }

    public function edit(Request $request, $id)
    {
        $user = $request->user();
        $vehicle = $user->vehicles()->with('vehicleModel')->findOrFail($id);
        return view('insured.my-vehicles-edit', compact('vehicle'));
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $vehicle = $user->vehicles()->findOrFail($id);

        $validated = $request->validate([
            'color' => 'required|string',
            'vin' => 'required|string|unique:insured_vehicles,vin,' . $vehicle->id,
            'plate' => 'required|string|unique:insured_vehicles,plate,' . $vehicle->id
        ]);

        $vehicle->update([
            'vin' => strtoupper($validated['vin']),
            'plate' => strtoupper($validated['plate'])
        ]);

        $oldVIM = $vehicle->vehicleModel;
        $newVIM = VehicleModel::firstOrCreate([
            'year' => $oldVIM->year,
            'brand' => $oldVIM->brand,
            'sub_brand' => $oldVIM->sub_brand,
            'version' => $oldVIM->version,
            'color' => $validated['color']
        ]);

        if ($vehicle->vehicle_model_id != $newVIM->id) {
            $vehicle->update(['vehicle_model_id' => $newVIM->id]);
        }

        return redirect()->route('myVehicles')->with('success', 'Vehículo actualizado correctamente');
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $vehicle = $user->vehicles()->findOrFail($id);
        if (Policy::where('vehicle_id', $vehicle->id)->exists()) {
            return back()->withErrors(['error' => 'No puedes eliminar un vehiculo que tiene una poliza asociada']);
        }
        $vehicle->delete();
        return redirect()->route('myVehicles')->with('success', 'Vehículo eliminado correctamente');
    }
}
