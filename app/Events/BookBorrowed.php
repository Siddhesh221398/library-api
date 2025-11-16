<?php

namespace App\Events;

use App\Models\Book;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class BookBorrowed
{
    use SerializesModels;

    public $book;
    public $user;

    public function __construct(User $user, Book $book)
    {
        $this->user = $user;
        $this->book = $book;
    }
}
