<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Str;
use App\Models\User; // ✅ Import model User

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar dengan relasi user untuk hindari N+1 query
        $query = Audit::with('user')->latest('created_at');

        // 🔍 Filter berdasarkan Model
        if ($request->filled('model')) {
            $query->where('auditable_type', $request->model);
        }

        // 🔍 Filter berdasarkan Event
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // 🔍 Filter berdasarkan User (gunakan user_id yang berupa GUID)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $audits = $query->paginate(25)->withQueryString();

        // Data untuk dropdown filter
        $models = Audit::distinct()->pluck('auditable_type');
        $events = Audit::distinct()->pluck('event');
        
        // ✅ PERBAIKAN: Query ke tabel users, bukan audits
        // Ambil distinct user_id dari audits, lalu join ke users untuk ambil nama
        $userIds = Audit::distinct()->pluck('user_id')->filter(); // Filter null
        $users = User::whereIn('guid', $userIds)->pluck('name', 'guid'); // guid sebagai key, name sebagai value

        return view('audit-logs.index', compact('audits', 'models', 'events', 'users'));
    }
}