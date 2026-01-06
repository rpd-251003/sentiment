<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'admin':
            case 'kaprodi':
                return redirect()->route('admin.dashboard');
            case 'dosen':
                return redirect()->route('dosen.dashboard');
            case 'pembimbing_lapangan':
                return redirect()->route('pembimbing-lapangan.dashboard');
            case 'mahasiswa':
                return redirect()->route('mahasiswa.dashboard');
            default:
                abort(403);
        }
    }
}
