<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\UserWelcomeMail;
use App\Mail\AdminNewUserMail;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Validate input
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'name' => 'required|string|min:3|max:50',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'name' => $validated['name'],
        ]);

        // Send email to the new user
        Mail::to($user->email)->send(new UserWelcomeMail($user));

        // Notify the administrator of the new user.
        $adminEmail = config('mail.admin_address', env('MAIL_ADMIN_ADDRESS', 'admin@example.com'));
        Mail::to($adminEmail)->send(new AdminNewUserMail($user));

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'created_at' => $user->created_at->toIso8601String(),
        ], 201);
    }
}
