<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Sinister;
use App\Models\Policy;
use App\Models\InsuredVehicle;
use App\Models\Role;
use App\Traits\UsesDBObjects;

class DashboardController extends Controller
{
    use UsesDBObjects;

    public function index(Request $request)
    {
        $user = $request->user();
        $usingDBObjects = $this->useDBObjects();
        if ($user->isInsured()) {
            if ($usingDBObjects) {
                $stats = collect(DB::select('SELECT * FROM vw_insured_sinister_data WHERE user_id = ?', [$user->id]))->first();
                $sinisters = DB::select('SELECT * FROM vw_insured_sinisters WHERE insured_id = ?', [$user->id]);
            } else {
                $stats = User::where('id', $user->id)
                    ->withCount([
                        'policies as total_policies',
                        'policies as active_policies' => fn($q) => $q
                            ->where('status', 'active')
                            ->where('end_validity', '>=', today()),
                    ])
                    ->first();

                $policyIds = Policy::where('insured_id', $user->id)->pluck('id')->whereNull('deleted_at');

                $stats = [
                    'user_id'               => $user->id,
                    'total_sinisters'       => Sinister::whereIn('policy_id', $policyIds)->count(),
                    'in_review_sinisters'   => Sinister::whereIn('policy_id', $policyIds)
                        ->where('status', 'in_review')->count(),
                    'approved_sinisters'    => Sinister::whereIn('policy_id', $policyIds)
                        ->whereIn('status', [
                            'approved',
                            'approved_with_deductible',
                            'approved_without_deductible',
                            'applies_payment_for_repairs'
                        ])->count(),
                    'rejected_sinisters'    => Sinister::whereIn('policy_id', $policyIds)
                        ->where('status', 'rejected')->count(),
                    'total_policies'        => Policy::where('insured_id', $user->id)->count(),
                    'active_policies'       => Policy::where('insured_id', $user->id)
                        ->where('status', 'active')->count(),
                    'total_vehicles'        => InsuredVehicle::where('user_id', $user->id)->count()
                ];

                $sinisters = Sinister::whereIn('policy_id', $policyIds)
                    ->with([
                        'policy.vehicle.vehicleModel',
                        'multimedia' => fn($q) => $q->where('type', 'photo')->limit(1),
                    ])
                    ->latest('report_date')
                    ->paginate(6);
            }
        } elseif ($user->isAdjuster()) {
            if ($this->useDBObjects()) {
                $stats = collect(DB::select('SELECT * FROM vw_adjuster_sinister_data WHERE user_id = ?', [$user->id]))->first();
                $sinisters = DB::select('SELECT * FROM vw_adjuster_sinisters WHERE insured_id = ?', [$user->id]);
            } else {
                $stats = [
                    'total_sinisters' => Sinister::where('adjuster_id', $user->id)->count(),
                    'aproved_sinisters' => Sinister::where('adjuster_id', $user->id)
                        ->whereIn('status', [
                            'approved',
                            'approved_with_deductible',
                            'approved_without_deductible',
                            'applies_payment_for_repairs'
                        ])->count(),
                    'in_review_sinister' => Sinister::where('adjuster_id', $user->id)
                        ->where('status', 'in_review')->count(),
                    'rejected_sinisters' => Sinister::where('adjuster_id', $user->id)
                        ->where('status', 'rejected')->count()
                ];

                $sinisters = Sinister::where('adjuster_id', $user->id)
                    ->with([
                        'policy.vehicle.vehicleModel',
                        'multimedia' => fn($q) => $q->where('type', 'photo')->limit(1),
                    ])
                    ->latest('report_date')
                    ->paginate(6);
            }
        } elseif ($user->isSupervisor() || $user->isAdmin()) {
            if ($this->useDBObjects()) {
                $stats = collect(DB::select("SELECT * FROM vw_all_sinister_data"))->first();
                $sinisters = DB::select("SELECT * FROM vw_all_sinisters");
            } else {
                $closedStatuses = [
                    'closed',
                    'approved',
                    'approved_with_deductible',
                    'approved_without_deductible',
                    'applies_payment_for_repairs',
                    'total_loss'
                ];
                $insuredRoleId = Role::where('name', 'insured')->value('id');
                $adjusterRoleId = Role::where('name', 'adjuster')->value('id');
                $supervisorRoleId = Role::where('name', 'supervisor')->value('id');

                $stats = [
                    'total_sinisters' => Sinister::count(),
                    'in_review_sinisters' => Sinister::where('status', 'in_review')->count(),
                    'approved_sinisters' => Sinister::whereIn('status', $closedStatuses)->count(),
                    'rejected_sinisters' => Sinister::where('status', 'rejected')->count(),
                    'sinisters_this_month' => Sinister::whereMonth('report_date', now()->month)
                        ->whereYear('report_date', now()->year)->count(),
                    'total_insured' => User::where('role_id', $insuredRoleId)->count(),
                    'total_adjuster' => User::where('role_id', $adjusterRoleId)->count(),
                    'total_supervisor' => User::where('role_id', $supervisorRoleId)->count(),
                    'active_policies' => Policy::where('status', 'active')
                        ->where('end_validity', '>=', today())->count(),
                ];

                $sinisters = Sinister::with([
                    'policy.insured',
                    'policy.vehicle.vehicleModel',
                    'adjuster',
                    'multimedia' => fn($q) => $q->where('type', 'photo')->limit(1),
                ])
                    ->latest('report_date')
                    ->paginate(6);
            }
        }
        //dd($stats);
        //dd($sinisters);
        return view('dashboard', compact('stats', 'sinisters'));
    }
}
