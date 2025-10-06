<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Logika untuk menampilkan data di dashboard admin
        // Misalnya: jumlah buku, jumlah user, dll.
        return view('admin.dashboard');
    }
}
