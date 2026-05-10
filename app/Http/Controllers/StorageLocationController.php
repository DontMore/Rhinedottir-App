<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\StorageLocation;
use RealRashid\SweetAlert\Facades\Alert;

class StorageLocationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $locations = StorageLocation::where('organization_guid', $user->organization_guid)
            ->withCount('reagenIn')
            ->orderBy('code')
            ->paginate(15);

        $hazardOptions = [
            'Toxic', 'Corrosive', 'Explosive', 'Carcinogen', 
            'Environment', 'Flammable', 'Irritant', 'Oxidising'
        ];

        return view('storage.index', compact('locations', 'hazardOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:storage_locations,code,NULL,id,organization_guid,' . auth()->user()->organization_guid,
            'name' => 'required|string|max:100',
            'state_type' => 'required|in:solid,liquid',
            'allowed_hazards' => 'nullable|array',
            'capacity' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:500'
        ]);

        $validated['organization_guid'] = auth()->user()->organization_guid;
        $validated['allowed_hazards'] = $validated['allowed_hazards'] ?? [];

        StorageLocation::create($validated);
        Alert::success('Success', 'Storage location created successfully.');
        return back();
    }

    public function update(Request $request, $guid)
    {
        $user = auth()->user();
        $location = StorageLocation::where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:storage_locations,code,'.$location->id.',id,organization_guid,'.$user->organization_guid,
            'name' => 'required|string|max:100',
            'state_type' => 'required|in:solid,liquid',
            'allowed_hazards' => 'nullable|array',
            'capacity' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:500'
        ]);

        $validated['allowed_hazards'] = $validated['allowed_hazards'] ?? [];
        $location->update($validated);

        Alert::success('Success', 'Storage location updated successfully.');
        return back();
    }

    public function destroy($guid)
    {
        $user = auth()->user();
        $location = StorageLocation::where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        if ($location->reagenIn()->count() > 0) {
            Alert::error('Error', 'Cannot delete location that contains reagents. Move items first.');
            return back();
        }

        $location->delete();
        Alert::success('Success', 'Storage location deleted successfully.');
        return back();
    }
}