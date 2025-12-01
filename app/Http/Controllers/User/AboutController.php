<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $company = [
            'name' => 'Drive Venture',
            'tagline' => 'all about essentials',
            'email' => 'driveventure719@gmail.com',
            'address' => 'Jl. Farida, Purwokerto, Jawa Tengah, Indonesia',
        ];

        return view('pages.user.about.index', compact('company'));
    }
}
