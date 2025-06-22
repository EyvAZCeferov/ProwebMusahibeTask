<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\RegisterAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected TranslationService $translationService;
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="Eyvaz"),
     *             @OA\Property(property="email", type="string", example="eyvaz@mail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="İstifadəçi uğurla qeydiyyatdan keçdi."),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Eyvaz")
     *             ),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci...")
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request, RegisterAction $registerAction)
    {
        $data = $request->validated();
        $data['user_role'] = 'person';

        $result = $registerAction->execute($data);

        $responseText = $this->translationService->get('İstifadəçi uğurla qeydiyyatdan keçdi.');
        return response()->json([
            'message' => $responseText,
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 201);
    }
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        if (!Auth::attempt(array_merge($data, ['status' => 1]))) {

            throw ValidationException::withMessages([
                'email' => ['Daxil edilən məlumatlar yanlışdır.'],
            ]);
        }

        $user = Auth::user();
        $user->load(['roles', 'accounts']);

        $token = $user->createToken('api-token')->plainTextToken;

        $responseText = $this->translationService->get('İstifadəçi uğurla giriş etdi.');
        return response()->json([
            'message' => $responseText,
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $responseText = $this->translationService->get('İstifadəçi hesabdan çıxış etdi');

        return response()->json([
            'message' => $responseText
        ], 200);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(["accounts", 'roles']);

        return response()->json([
            'user' => new UserResource($user)
        ], 200);
    }
}
