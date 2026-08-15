<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

abstract class Controller
{
    use AuthorizesRequests;

    protected function user(Request $request): User
    {
        $user = $request->user();

        assert($user instanceof User);

        return $user;
    }
}
