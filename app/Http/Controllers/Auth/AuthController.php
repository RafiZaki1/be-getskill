<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/register",
        operationId: "authRegister",
        summary: "Register user baru",
        description: "Mendaftarkan user baru dan mengirimkan kode OTP ke email",
        tags: ["Auth"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "email", "password", "password_confirmation"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "John Doe"),
                new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123")
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Registrasi berhasil",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "User registered successfully. Please check your email for the OTP.")
            ]
        )
    )]
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $otpCode = (string) random_int(100000, 999999);
        $otpExpiresAt = now()->addMinutes(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp_code' => $otpCode,
            'otp_expires_at' => $otpExpiresAt,
        ]);

        Log::info("OTP for {$user->email} is {$otpCode}");

        return response()->json([
            'message' => 'User registered successfully. Please check your email for the OTP.',
        ], 201);
    }

    #[OA\Post(
        path: "/api/verify-otp",
        operationId: "authVerifyOtp",
        summary: "Verifikasi OTP",
        description: "Memverifikasi akun menggunakan OTP yang dikirim ke email",
        tags: ["Auth"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "otp_code"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                new OA\Property(property: "otp_code", type: "string", example: "123456")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Verifikasi berhasil",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Email verified successfully. You can now login.")
            ]
        )
    )]
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user->otp_code || $user->otp_code !== $request->otp_code) {
            return response()->json(['message' => 'Invalid OTP code.'], 400);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP code has expired.'], 400);
        }

        $user->update([
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json([
            'message' => 'Email verified successfully. You can now login.'
        ]);
    }

    #[OA\Post(
        path: "/api/login",
        operationId: "authLogin",
        summary: "Login ke aplikasi",
        description: "Login untuk mendapatkan token akses",
        tags: ["Auth"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "password"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "password123")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Login berhasil",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "data", type: "object", properties: [
                    new OA\Property(property: "user", type: "object"),
                    new OA\Property(property: "token", type: "string", example: "1|xxx...")
                ]),
                new OA\Property(property: "message", type: "string", example: "Berhasil login")
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Kredensial tidak valid")]
    #[OA\Response(response: 403, description: "Email belum diverifikasi")]
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && is_null($user->email_verified_at)) {
            return response()->json(['message' => 'Please verify your email before logging in.'], 403);
        }

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return \App\Helpers\ResponseHelper::success([
                'user' => $user,
                'token' => $token,
            ], 'Berhasil login');
        }

        return response()->json([
            'message' => 'The provided credentials do not match our records.'
        ], 401);
    }

    #[OA\Post(
        path: "/api/logout",
        operationId: "authLogout",
        summary: "Logout dari aplikasi",
        description: "Menghapus token akses saat ini",
        security: [["bearerAuth" => []]],
        tags: ["Auth"]
    )]
    #[OA\Response(
        response: 200,
        description: "Logout berhasil",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Logged out successfully")
            ]
        )
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
