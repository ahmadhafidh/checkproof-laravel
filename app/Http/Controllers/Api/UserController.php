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
    /**
     * Get paginated list of active users with filters
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            "search" => "nullable|string|max:255",
            "page" => "nullable|integer|min:1",
            "sortBy" => "nullable|in:name,email,created_at",
        ]);

        $authUser = auth()->guard("api")->user();

        if (!$authUser) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Unauthenticated",
                ],
                401,
            );
        }

        $query = User::where("active", true)->withCount("orders");

        if (!empty($validated["search"])) {
            $search = $validated["search"];
            $query->where(function ($q) use ($search) {
                $q->where("name", "like", "%{$search}%")->orWhere(
                    "email",
                    "like",
                    "%{$search}%",
                );
            });
        }

        $sortBy = $validated["sortBy"] ?? "created_at";
        $query->orderBy($sortBy, "asc");

        $users = $query->paginate(10);

        // Check can edit
        $users->getCollection()->transform(function ($user) use ($authUser) {
            return [
                "id" => $user->id,
                "email" => $user->email,
                "name" => $user->name,
                "role" => $user->role,
                "active" => $user->active,
                "created_at" => $user->created_at->toIso8601String(),
                "updated_at" => $user->updated_at->toIso8601String(),
                "orders_count" => $user->orders_count,
                "can_edit" => $this->canEditUser($authUser, $user),
            ];
        });

        return response()->json(
            [
                "page" => $users->currentPage(),
                "users" => $users->items(),
            ],
            200,
        );
    }

    /**
     * Determine if the authenticated user can edit the given user
     */
    private function canEditUser(User $authUser, User $targetUser): bool
    {
        // Administrator can edit any user
        if ($authUser->role === "administrator") {
            return true;
        }

        // Manager can edit users with 'user' role
        if ($authUser->role === "manager" && $targetUser->role === "user") {
            return true;
        }

        // User can only edit themselves
        if ($authUser->id === $targetUser->id) {
            return true;
        }

        return false;
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'name' => 'required|string|min:3|max:50',
                'role' => 'nullable|string|in:user,manager,administrator',
            ], [
                'role.in' => 'Role not found',
            ]);
    
            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'name' => $validated['name'],
                'role' => $validated['role'] ?? 'user',
            ]);
    
            // Send email to new user
            Mail::to($user->email)->send(new UserWelcomeMail($user));
    
            // Notify admin of new user
            $adminEmail = config('mail.admin_address', env('MAIL_ADMIN_ADDRESS', 'admin@example.com'));
            Mail::to($adminEmail)->send(new AdminNewUserMail($user));
    
            return response()->json([
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'created_at' => $user->created_at->toIso8601String(),
            ], 201);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
