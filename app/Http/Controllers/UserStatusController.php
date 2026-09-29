<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserStatusController extends Controller
{
    public function heartbeat(Request $request): Response
    {
        if (! Auth::check()) {
            return response()->noContent(401);
        }

        Auth::user()->updateQuietly([
            'last_seen_at' => now(),
        ]);

        return response()->noContent(204);
    }
}
