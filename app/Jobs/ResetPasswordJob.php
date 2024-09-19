<?php

namespace App\Jobs;

use App\Mail\Authentication\ResetPasswordMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ResetPasswordJob implements ShouldQueue
{
    use Queueable;

    private string $email;
    private string $activeToken;

    /**
     * Create a new job instance.
     */
    public function __construct(string $email, string $activeToken)
    {
        $this->email = $email;
        $this->activeToken = $activeToken;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new ResetPasswordMail($this->activeToken));
    }
}
