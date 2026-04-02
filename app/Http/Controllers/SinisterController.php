<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sinister;
use App\Models\SinisterComment;
use App\Models\Policy;

class SinisterController extends Controller
{
    public function show(Request $request, int $id)
    {
        $user     = $request->user();
        $sinister = Sinister::with([
            'policy.vehicle.vehicleModel',
            'policy.insured',
            'policy.plan',
            'adjuster',
            'supervisor',
            'multimedia',
            'comments.user',
        ])->findOrFail($id);

        // Autorización: asegurado solo puede ver sus propios siniestros
        if ($user->isInsured()) {
            $ownPolicyIds = Policy::where('insured_id', $user->id)->pluck('id');
            abort_unless($ownPolicyIds->contains($sinister->policy_id), 403);
        }

        // Ajustador solo puede ver los asignados
        if ($user->isAdjuster()) {
            abort_unless($sinister->adjuster_id === $user->id, 403);
        }

        return view('sinister-detail', compact('sinister'));
    }

    public function addComment(Request $request, int $id)
    {
        $user     = $request->user();
        $sinister = Sinister::findOrFail($id);

        // Misma autorización
        if ($user->isInsured()) {
            $ownPolicyIds = Policy::where('insured_id', $user->id)->pluck('id');
            abort_unless($ownPolicyIds->contains($sinister->policy_id), 403);
        }
        if ($user->isAdjuster()) {
            abort_unless($sinister->adjuster_id === $user->id, 403);
        }

        $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        SinisterComment::create([
            'comment'     => $request->comment,
            'sinister_id' => $sinister->id,
            'user_id'     => $user->id,
        ]);

        return redirect()
            ->route('sinisterDetail', $id)
            ->with('success', 'Comentario agregado correctamente.');
    }
}
