<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $price = $this->event ? $this->event->price : ($this->course->promotional_price == 0 ? $this->course->price : $this->course->promotional_price);
        
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'product_type' => $this->event ? 'event' : 'course',
            'product' => $this->event ? [
                'id' => $this->event->id,
                'title' => $this->event->title,
                'description' => $this->event->description,
                'photo' => url($this->event->image),
                'sub_category' => $this->event->eventSubCategory?->name ?? null,
                'price' => $this->event->price,
            ] : CourseResource::make($this->course), 
            'invoice_id' => $this->invoice_id,  
            'fee_amount' => $this->fee_amount, 
            'amount' => $this->amount,
            'invoice_url' => $this->invoice_url,
            'expiry_date' => $this->expiry_date,
            'paid_amount' => $this->paid_amount,
            'payment_channel' => $this->payment_channel, 
            'payment_method' => $this->payment_method, 
            'invoice_status' => $this->invoice_status,  
            'course_voucher' => $this->courseVoucher ? $price * ($this->courseVoucher->discount / 100)  : null,  
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at->format('j F Y')
        ];
    }
}
