<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\User;
use App\Models\Borrow;

class BorrowingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role_id', 2)->get();

        foreach ($users as $user) {
            $book = Book::inRandomOrder()->first();
            if ($book && $book->status === 'available') {
                Borrow::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'borrowed_at' => now(),
                    'returned_at' => null,
                ]);

                $book->update(['status' => 'borrowed']);
            }
        }
    }
}
