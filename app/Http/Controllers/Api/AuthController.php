<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

/**
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your Bearer token from login response."
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created"),
     *     @OA\Response(response=400, description="Bad request")
     * )
     */

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $role = Role::where('name', 'user')->first();

            if (!$role) {
                return response()->json(['error' => 'Role not found'], 500);
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => $role->id,
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'user'  => new UserResource($user),
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create user'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login a user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User logged in"),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Invalid credentials provided.'],
                ]);
            }

            $user->tokens()->delete();

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'user'  => new UserResource($user),
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to login'], 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the authenticated user",
     *     description="Logs out the currently authenticated user by deleting their Sanctum token.",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function logout(): JsonResponse
    {
        try {
            $user = request()->user();

            if (! $user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            $user->tokens()->delete();

            return response()->json(['message' => 'Logged out successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to logout'], 500);
        }
    }
}
