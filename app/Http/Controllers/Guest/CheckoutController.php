<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    /**
     * Checkout kursus (pilih metode pembayaran)
     */
    public function checkout(Request $request): JsonResponse
    {
        // TODO: Implement checkout
        return response()->json(['message' => 'Checkout course']);
    }

    /**
     * Cek status pembayaran pembelian kursus
     */
    public function checkStatus($transaction): JsonResponse
    {
        // TODO: Implement checkStatus
        return response()->json(['message' => 'Check payment status']);
    }

    /**
     * Batalkan pembelian kursus
     */
    public function cancel(Request $request, $transaction): JsonResponse
    {
        // TODO: Implement cancel
        return response()->json(['message' => 'Cancel checkout']);
    }
}
