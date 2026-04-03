<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sinister;
use App\Models\SinisterComment;
use App\Enums\SinisterStatusEnum;

class SupervisorSinisterController extends Controller
{
    /**
     * Show the form for managing the specified sinister.
     */
    public function edit($id)
    {
        $sinister = Sinister::with(['policy.vehicle.vehicleModel', 'policy.insured', 'multimedia', 'comments.user'])
            ->findOrFail($id);

        return view('supervisor.sinister-manage', compact('sinister'));
    }

    /**
     * Update the sinister's status and add optional comment.
     */
    public function updateStatus(Request $request, $id)
    {
        $sinister = Sinister::findOrFail($id);
        
        $request->validate([
            'status' => ['required', 'string'],
            'comment' => ['nullable', 'string', 'max:1000']
        ]);

        $newStatusValue = $request->input('status');
        $newStatusEnum = SinisterStatusEnum::tryFrom($newStatusValue);

        if (!$newStatusEnum) {
            return back()->withErrors(['status' => 'El estatus seleccionado no es válido.'])->withInput();
        }

        // Si el estado enviado es exactamente el que ya tenía, no es error critico, solo avisa
        if ($newStatusEnum->value === $sinister->status->value) {
            // Guardar solo comentario si agrego algo adicional sin cambiar de status
            if ($request->filled('comment')) {
                 SinisterComment::create([
                    'sinister_id' => $sinister->id,
                    'user_id' => auth()->id(),
                    'comment' => $request->input('comment')
                ]);
                return redirect()->route('sinisterDetail', $sinister->id)
                             ->with('success', 'Se agregó un nuevo comentario/dictamen al siniestro.');
            }
            return redirect()->route('sinisterDetail', $sinister->id);
        }

        // Validate allowed transition
        if (!$sinister->status->canTransitionTo($newStatusEnum)) {
            return back()->withErrors(['status' => 'Transición de estado no autorizada.'])->withInput();
        }

        // Update status
        $sinister->status = $newStatusEnum;
        
        // Cierre logic based on transitions
        if (in_array($newStatusEnum, [SinisterStatusEnum::CLOSED])) {
            $sinister->close_date = now();
        }

        $sinister->save();

        // Optional Comment Record
        if ($request->filled('comment')) {
            SinisterComment::create([
                'sinister_id' => $sinister->id,
                'user_id' => auth()->id(),
                'comment' => "Actualización de Estatus a {$newStatusEnum->label()}: " . $request->input('comment')
            ]);
        } else {
             SinisterComment::create([
                'sinister_id' => $sinister->id,
                'user_id' => auth()->id(),
                'comment' => "El supervisor modificó el estado del siniestro a: {$newStatusEnum->label()}."
            ]);
        }

        return redirect()->route('sinisterDetail', $sinister->id)
                         ->with('success', 'Estado modificado a ' . $newStatusEnum->label() . ' exitosamente.');
    }
}
