<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = User::orderBy('id')->first();

        return view('profile', compact('user'));
    }
}
