<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookTest extends TestCase
{

    #[Test]
    public function test_guest_cannot_access_books()
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(401);
    }

    #[Test]
    public function test_user_can_see_books()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/books');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'links', 'meta']);
    }

    #[Test]
    public function test_user_can_see_single_book()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $book = Book::factory()->create();

        $response = $this->getJson('/api/books/' . $book->id);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    #[Test]
    public function test_admin_can_create_book()
    {
        $admin = User::factory()->create(['role_id' => 1]); // assuming 1 is admin role
        Sanctum::actingAs($admin);

        $bookData = [
            'title' => 'New Book',
            'author' => 'New Author',
            'description' => 'New Description',
            'status' => 'available',
        ];

        $response = $this->postJson('/api/books', $bookData);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data', 'message']);
    }

    #[Test]
    public function test_user_cannot_create_book()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $bookData = [
            'title' => 'New Book',
            'author' => 'New Author',
            'description' => 'New Description',
            'status' => 'available',
        ];

        $response = $this->postJson('/api/books', $bookData);

        $response->assertStatus(403);
    }

    #[Test]
    public function test_admin_can_update_book()
    {
        $admin = User::factory()->create(['role_id' => 1]); // assuming 1 is admin role
        Sanctum::actingAs($admin);

        $book = Book::factory()->create();

        $bookData = [
            'title' => 'Updated Book',
            'author' => 'Updated Author',
            'description' => 'Updated Description',
            'status' => 'borrowed',
        ];

        $response = $this->putJson('/api/books/' . $book->id, $bookData);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'message']);
    }

    #[Test]
    public function test_admin_can_delete_book()
    {
        $admin = User::factory()->create(['role_id' => 1]); // assuming 1 is admin role
        Sanctum::actingAs($admin);

        $book = Book::factory()->create();

        $response = $this->deleteJson('/api/books/' . $book->id);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Book deleted successfully']);
    }
}