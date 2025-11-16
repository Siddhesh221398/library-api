<?php

namespace App\Events;

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookReturned
{
    use Dispatchable, SerializesModels;

    public $user;
    public $book;

    public function __construct(User $user, Book $book)
    {
        $this->user = $user;
        $this->book = $book;
    }
}

