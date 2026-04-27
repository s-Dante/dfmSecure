<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sinister;
use App\Models\Policy;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Sinister::with([
            'policy.vehicle.vehicleModel',
            'multimedia' => fn($q) => $q->where('type', 'photo')->limit(1),
        ]);

        // Scope por rol
        if ($user->isInsured()) {
            $policyIds = Policy::where('insured_id', $user->id)->pluck('id');
            $query->whereIn('policy_id', $policyIds);
        } elseif ($user->isAdjuster()) {
            $query->where('adjuster_id', $user->id);
        }
        // Supervisor y Admin ven todos

        // Filtro: fecha inicio
        if ($request->filled('fecha_inicio')) {
            $query->where('report_date', '>=', $request->fecha_inicio);
        }

        // Filtro: fecha fin
        if ($request->filled('fecha_fin')) {
            $query->where('report_date', '<=', $request->fecha_fin);
        }

        // Filtro: folio / sinister_number / vin
        if ($request->filled('folio')) {
            $query->where(function ($q) use ($request) {
                $q->where('sinister_number', 'like', '%' . $request->folio . '%')
                  ->orWhere('folio', 'like', '%' . $request->folio . '%')
                  ->orWhereHas('policy.vehicle', function ($vQuery) use ($request) {
                      $vQuery->where('vin', 'like', '%' . $request->folio . '%');
                  });
            });
        }

        // Filtro: status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sinisters = $query->latest('report_date')->paginate(6)->withQueryString();

        return view('consultation', compact('sinisters'));
    }
}
