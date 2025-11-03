<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Menampilkan halaman bantuan/tutorial.
     * * @return \Illuminate\View\View
     */
    public function index()
    {
        // Controller ini hanya bertugas menampilkan view statis
        return view('help.index');
    }
}
