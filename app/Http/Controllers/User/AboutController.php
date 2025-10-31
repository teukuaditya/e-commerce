<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $company = [
            'name' => 'DRVN',
            'tagline' => 'all about essentials',
            'email' => 'hello@drvn.store',
            'address' => 'Jl. Contoh No. 123, Purwokerto',
        ];

        return view('pages.user.about.index', compact('company'));
    }
}
