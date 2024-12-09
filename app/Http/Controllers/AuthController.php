<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     *  @OA\Post(
     *      path="/register",
     *      summary="Creater user",
     *      description="Register a user",
     *      tags={"Users"}, 
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string"
     *                 ),
     *                 example={"name": "John Smith", "email": "Johnsmith@mail.com", "password": "johnsmith12", "password_confirmation": "johnsmith12"}
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response="201",
     *          description="CREATED",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *  )
     */
    public function register(Request $request): JsonResponse
    {
        $userValidated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($userValidated->fails()) {
            return response()->json($userValidated->errors(), 403);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'user' => $user
            ], 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 403);
        }
    }

    /**
     *  @OA\Post(
     *      path="/login",
     *      summary="Authenticate user",
     *      description="Authenticate a user registered",
     *      tags={"Users"}, 
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"email": "Johnsmith@mail.com", "password": "johnsmith12"}
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response="200",
     *          description="OK",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *  )
     */
    public function login(Request $request): JsonResponse
    {
        $userValidated = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($userValidated->fails()) {
            return response()->json($userValidated->errors(), 403);
        }

        $credentials = ['email' => $request->email, 'password' => $request->password];

        try {
            if (!auth()->attempt($credentials)) {
                return response()->json(['error', 'Credênciais inválidas'], 403);
            }

            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'user' => $user
            ], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 403);
        }
    }

    /**
     *  @OA\Get(
     *      path="/user",
     *      summary="Find the authenticated user",
     *      description="Find the current authenticated user",
     *      security={ {"sanctum": {} }},
     *      tags={"Users"}, 
     *      @OA\Response(
     *          response="200",
     *          description="OK",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *  )
     */
    public function findLoggedUser(Request $request): User
    {
        return $request->user();
    }

    /**
     *  @OA\Post(
     *      path="/logout",
     *      summary="Logout",
     *      description="Log out the user",
     *      security={ {"sanctum": {} }},
     *      tags={"Users"}, 
     *      @OA\Response(
     *          response="200",
     *          description="OK",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *  )
     */

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Usuário deslogado com sucesso!',
        ], 200);
    }
}
