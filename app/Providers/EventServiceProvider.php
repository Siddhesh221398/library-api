<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\BookBorrowed;
use App\Events\BookReturned;
use App\Listeners\SendBookNotification;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(BookBorrowed::class, SendBookNotification::class);
        Event::listen(BookReturned::class, SendBookNotification::class);
    }
}
