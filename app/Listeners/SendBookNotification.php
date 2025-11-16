<?php

namespace App\Listeners;

use App\Events\BookBorrowed;
use App\Events\BookReturned;
use Illuminate\Support\Facades\Log;

class SendBookNotification
{

    public function handle($event): void
    {
        if ($event instanceof BookBorrowed) {
            Log::info("Book borrowed: {$event->book->title} by {$event->user->name}");
        }

        if ($event instanceof BookReturned) {
            Log::info("Book returned: {$event->book->title} by {$event->user->name}");
        }
    }
}
