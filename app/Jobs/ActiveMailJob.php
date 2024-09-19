<?php

namespace App\Jobs;

use App\Mail\Authentication\ActiveMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ActiveMailJob implements ShouldQueue
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
        Mail::to($this->email)->send(new ActiveMail($this->activeToken));
    }
}
