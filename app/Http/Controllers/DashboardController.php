<?php

namespace App\Http\Controllers;

use Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function show()
    {
        return Inertia::render('Dashboard', Auth::user()->getFavorites());
    }
}
