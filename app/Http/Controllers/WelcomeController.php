<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\UsesDBObjects;
use App\Models\Plan;

class WelcomeController extends Controller
{
    use UsesDBObjects;

    public function index()
    {
        if ($this->useDBObjects()) {
            $plans = collect($this->callProcedure('sp_get_active_plans'));
        } else {
            $plans = Plan::where('status', 'active')->orderBy('price')->get();
        }
        return view('welcome', compact('plans'));
    }
}
