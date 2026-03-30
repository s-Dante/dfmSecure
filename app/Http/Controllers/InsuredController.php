<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsuredController extends Controller
{
    /**
     * Muestra las pólizas del asegurado.
     */
    public function myPolicies()
    {
        // Supongamos que queremos obtener las pólizas del usuario actual
        $user = auth()->user();
        $policies = $user->policies; // Gracias a las relaciones en el modelo User

        // Pasamos los datos a la vista usando compact()
        return view('insured.my-policies', compact('policies'));
    }

    /**
     * Muestra los vehículos del asegurado.
     */
    public function myVehicles()
    {
        $user = auth()->user();
        $vehicles = $user->vehicles;

        return view('insured.my-vehicles', compact('vehicles', 'user'));
    }
}
