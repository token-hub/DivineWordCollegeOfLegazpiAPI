<?php

namespace App\Jobs;

use App\Mail\ContactUsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessWebContactUsMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $contactUs;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    // public function __construct($contactUs)
    // {
    //     $this->contactUs = $contactUs;
    // }

    public function __construct(ContactUsMail $contactUs)
    {
        $this->contactUs = $contactUs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // dd($this->contactUs);
        // Mail::send(new ContactUsMail($this->contactUs));
        Mail::send($this->contactUs);
    }
}
