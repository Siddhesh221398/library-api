<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrow;

class BorrowingTest extends TestCase
{
    #[Test]
    public function test_user_can_borrow_a_book()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['status' => 'available']);

        $this->actingAs($user);

        $response = $this->postJson('/api/borrow', [
            'book_id' => $book->id
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Book borrowed successfully'
            ]);

        $this->assertDatabaseHas('borrows', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'returned_at' => null
        ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'status' => 'borrowed'
        ]);
    }

    #[Test]
    public function test_user_cannot_borrow_already_borrowed_book()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['status' => 'borrowed']);

        $this->actingAs($user);

        $response = $this->postJson('/api/borrow', [
            'book_id' => $book->id
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Book is already borrowed.'
            ]);
    }

    #[Test]
    public function test_user_can_return_a_book()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['status' => 'borrowed']);

        $borrow = Borrow::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'returned_at' => null
        ]);

        $this->actingAs($user);

        $response = $this->postJson("/api/return/{$book->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Book returned successfully'
            ]);

        $this->assertDatabaseHas('borrows', [
            'id' => $borrow->id,
            'returned_at' => now()
        ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'status' => 'available'
        ]);
    }

    #[Test]
    public function test_user_cannot_return_book_not_borrowed()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['status' => 'available']);

        $this->actingAs($user);

        $response = $this->postJson("/api/return/{$book->id}");

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'You have not borrowed this book or it is already returned.'
            ]);
    }
}
