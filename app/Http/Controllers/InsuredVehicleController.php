<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsuredVehicleController extends Controller
{
    public function index()
    {
        $vehicles = auth()->user()->vehicles()->with(['vehicleModel', 'policy'])->get();
        $vehiclesJson = file_get_contents(database_path('data/vehicles.json'));
        return view('insured.my-vehicles', compact('vehicles', 'vehiclesJson'));
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
            'plate' => 'required|string'
        ]);

        $vm = \App\Models\VehicleModel::firstOrCreate([
            'year' => $validated['year'],
            'brand' => $validated['brand'],
            'sub_brand' => $validated['sub_brand'],
            'version' => $validated['version'],
            'color' => $validated['color']
        ]);

        auth()->user()->vehicles()->create([
            'vin' => strtoupper($validated['vin']),
            'plate' => strtoupper($validated['plate']),
            'vehicle_model_id' => $vm->id
        ]);

        return redirect()->route('myVehicles')->with('success', 'Vehículo registrado exitosamente.');
    }

    public function edit($id)
    {
        $vehicle = auth()->user()->vehicles()->with('vehicleModel')->findOrFail($id);
        return view('insured.my-vehicles-edit', compact('vehicle'));
    }

    public function update(Request $request, $id)
    {
        $vehicle = auth()->user()->vehicles()->findOrFail($id);

        $validated = $request->validate([
            'color' => 'required|string',
            'vin' => 'required|string|unique:insured_vehicles,vin,' . $vehicle->id,
            'plate' => 'required|string',
        ]);

        // Per user request, maybe allow changing brand/model? They asked "bloqueamos la marca/modelo y solo dejamos editar Placas, Color y VIN?"
        // I will only allow Color, vin, plate to maintain policy integrity.
        
        $vehicle->update([
            'vin' => strtoupper($validated['vin']),
            'plate' => strtoupper($validated['plate'])
        ]);

        $oldVm = $vehicle->vehicleModel;
        $newVm = \App\Models\VehicleModel::firstOrCreate([
            'year' => $oldVm->year,
            'brand' => $oldVm->brand,
            'sub_brand' => $oldVm->sub_brand,
            'version' => $oldVm->version,
            'color' => $validated['color']
        ]);

        if ($vehicle->vehicle_model_id !== $newVm->id) {
            $vehicle->update(['vehicle_model_id' => $newVm->id]);
        }

        return redirect()->route('myVehicles')->with('success', 'Vehículo actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $vehicle = auth()->user()->vehicles()->findOrFail($id);
        
        if (\App\Models\Policy::where('vehicle_id', $vehicle->id)->exists()) {
            return back()->withErrors(['error' => 'No puedes eliminar un vehículo que tiene una póliza asociada.']);
        }

        $vehicle->delete();
        return redirect()->route('myVehicles')->with('success', 'Vehículo eliminado.');
    }
}
