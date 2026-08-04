<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class PasswordResetController extends Controller
{
    #[OA\Post(
        path: "/api/forgot-password",
        operationId: "authForgotPassword",
        summary: "Lupa password (forgot password)",
        description: "Mengirimkan OTP ke email untuk mereset password",
        tags: ["Auth"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Berhasil mengirim OTP",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "An OTP has been sent to your email address.")
            ]
        )
    )]
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $otpCode = (string) random_int(100000, 999999);
        $otpExpiresAt = now()->addMinutes(10);

        $user->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => $otpExpiresAt,
        ]);

        // In a real application, send the OTP via Email or SMS here.
        Log::info("Password Reset OTP for {$user->email} is {$otpCode}");

        return response()->json([
            'message' => 'An OTP has been sent to your email address.'
        ]);
    }

    #[OA\Post(
        path: "/api/reset-password",
        operationId: "authResetPassword",
        summary: "Reset password",
        description: "Mereset password menggunakan OTP yang dikirim ke email",
        tags: ["Auth"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "otp_code", "password", "password_confirmation"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                new OA\Property(property: "otp_code", type: "string", example: "123456"),
                new OA\Property(property: "password", type: "string", format: "password", example: "newpassword123"),
                new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "newpassword123")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Berhasil reset password",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Password has been reset successfully.")
            ]
        )
    )]
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user->otp_code || $user->otp_code !== $request->otp_code) {
            return response()->json(['message' => 'Invalid OTP code.'], 400);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP code has expired.'], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json([
            'message' => 'Password has been reset successfully.'
        ]);
    }
}
