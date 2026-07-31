<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Manajemen Kursus & Perencanaan API",
    description: "L5 Swagger API documentation for Manajemen Kursus"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer"
)]
class CheckoutController extends Controller
{
    #[OA\Post(
        path: "/api/checkout",
        operationId: "checkoutCourse",
        summary: "Checkout kursus (pilih metode pembayaran)",
        description: "Membuat transaksi baru untuk pembelian kursus",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["course_id", "payment_method"],
            properties: [
                new OA\Property(property: "course_id", type: "string", example: "uuid-course"),
                new OA\Property(property: "payment_method", type: "string", example: "bank_transfer"),
                new OA\Property(property: "bank_code", type: "string", example: "BCA"),
                new OA\Property(property: "voucher_code", type: "string", example: "PROMO2026")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Checkout berhasil dilakukan",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Checkout course")
            ]
        )
    )]
    public function checkout(Request $request): JsonResponse
    {
        // TODO: Implement checkout
        return response()->json(['message' => 'Checkout course']);
    }

    #[OA\Get(
        path: "/api/checkout/status/{transaction}",
        operationId: "checkCheckoutStatus",
        summary: "Cek status pembayaran pembelian kursus",
        description: "Mengecek status pembayaran untuk transaksi tertentu",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\Parameter(
        name: "transaction",
        description: "Transaction ID",
        required: true,
        in: "path",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Response(
        response: 200,
        description: "Status pembayaran",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Check payment status")
            ]
        )
    )]
    public function checkStatus($transaction): JsonResponse
    {
        // TODO: Implement checkStatus
        return response()->json(['message' => 'Check payment status']);
    }

    #[OA\Post(
        path: "/api/checkout/cancel/{transaction}",
        operationId: "cancelCheckout",
        summary: "Batalkan pembelian kursus",
        description: "Membatalkan transaksi pembelian kursus",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\Parameter(
        name: "transaction",
        description: "Transaction ID",
        required: true,
        in: "path",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\RequestBody(
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "reason", type: "string", example: "Salah pilih kursus")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Checkout berhasil dibatalkan",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Cancel checkout")
            ]
        )
    )]
    public function cancel(Request $request, $transaction): JsonResponse
    {
        // TODO: Implement cancel
        return response()->json(['message' => 'Cancel checkout']);
    }
}
