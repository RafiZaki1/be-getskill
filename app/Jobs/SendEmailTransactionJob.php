<?php

namespace App\Jobs;

use App\Mail\TransactionMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to;
    protected $subject;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($to, $subject, $data = [])
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->to)->send(new TransactionMail($this->subject, $this->data));
    }
}
