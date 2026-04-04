<?php

namespace App\Http\Controllers;

use App\Enums\SinisterStatusEnum;
use Illuminate\Http\Request;

use App\Models\Sinister;
use App\Models\SinisterComment;

class SupervisorSinisterController extends Controller
{
    public function edit($id)
    {
        $sinister = Sinister::with(['policy.vehicle.vehicleModel', 'policy.insured', 'multimedia', 'comments.user'])
            ->findOrFail($id);

        return view('supervisor.sinister-manage', compact('sinister'));
    }

    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        $sinister = Sinister::findOrFail($id);

        $request->validate([
            'status' => 'required|string',
            'comment' => 'nullable|string|max:255'
        ]);

        $newStatusValue = $request->input('status');
        $newStatusEnum = SinisterStatusEnum::tryFrom($newStatusValue);

        if (!$newStatusEnum) {
            return back()->withErrors(['status' => 'El estatus seleccinado no es valido'])->withInput();
        }

        if ($newStatusEnum->value === $sinister->status->value) {
            if ($request->filled('comment')) {
                SinisterComment::create([
                    'sinister_id' => $sinister->id,
                    'user_id' => $user->id,
                    'comment' => $request->input('comment')
                ]);
                return redirect()->route('sinisterDetail', $sinister->id)
                    ->with('success', 'Se ha modificado el estatus del siniestro');
            }
            return redirect()->route('sinisterDetail', $sinister->id);
        }

        if (!$sinister->status->canTransitionTo($newStatusEnum)) {
            return back()->withErrors(['status' => 'Transicion de estatus no validad'])->withInput();
        }

        $sinister->status = $newStatusEnum;

        if (in_array($newStatusEnum, [SinisterStatusEnum::CLOSED])) {
            $sinister->close_date = now();
        }
        
        $sinister->save();

        if ($request->filled('comment')) {
            SinisterComment::create([
                'sinister_id' => $sinister->id,
                'user_id' => $user->id,
                'comment' => "Se actualizo el Estatus a {$newStatusEnum->label()}: " . $request->input('comment')
            ]);
        } else {
            SinisterComment::create([
                'sinister_id' => $sinister->id,
                'user_id' => $user->id,
                'comment' => "Actualizacion de Estatus a {$newStatusEnum->label()}"
            ]);
        }

        return redirect()->route('sinisterDetail', $sinister->id)
            ->with('success', 'Estado modificaco a ' . $newStatusEnum->label() . ' exitosamente');
    }
}
