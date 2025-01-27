<?php

namespace App\Http\Controllers;

use App\Models\Collector;
use App\Models\Survey;
use App\Traits\HasBreadcrumbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    use HasBreadcrumbs;
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        if ($user->tenants->count() == 1) {
            $tenant = $user->tenants->first();
            return redirect()->route('tenants.show', $tenant);
        }

        return view('dashboard', [
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }
}
