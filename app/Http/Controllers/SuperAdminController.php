<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;

class SuperAdminController extends Controller
{
    public function index()
    {
        $organizations = Organization::all();
        return view('superadmin.index', compact('organizations'));
    }
}
