<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function create()
    {
        return view('organization.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'note' => 'nullable|string'
        ]);

        Organization::create($validated);

        return redirect()->route('superadmin.index')->with('success', 'Organization created successfully');
    }

    public function show($id)
    {
        $organization = Organization::findOrFail($id);
        return view('organization.show', compact('organization'));
    }

    public function edit($id)
    {
        $organization = Organization::findOrFail($id);
        return view('organization.edit', compact('organization'));
    }

    public function update(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'note' => 'nullable|string'
        ]);

        $organization->update($validated);

        return redirect()->route('superadmin.index')->with('success', 'Organization updated successfully');
    }

    public function destroy($id)
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();

        return redirect()->route('superadmin.index')->with('success', 'Organization deleted successfully');
    }

    public function settings($id)
    {
        $organization = Organization::findOrFail($id);
        $users = User::where('organization_id', $id)->get();

        return view('organization.settings', compact('organization', 'users'));
    }

    public function addUser($id)
    {
        $organization = Organization::findOrFail($id);
        return view('organization.add-user', compact('organization'));
    }

    public function storeUser(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['organization_id'] = $id;

        User::create($validated);

        return redirect()->route('organization.settings', $id)->with('success', 'User added successfully');
    }
}
