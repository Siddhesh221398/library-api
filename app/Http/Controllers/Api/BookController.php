<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/books",
     *     operationId="getBooks",
     *     tags={"Books"},     * 
     *     security={{"sanctum": {}}},
     *     summary="Get paginated list of books",
     *     description="Returns a paginated list of available books.",
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */

    public function index(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = min(50, $request->get('per_page', 15));
            $cacheKey = "books:page:{$page}:per:{$perPage}";

            $books = Cache::remember($cacheKey, 60, function () use ($perPage) {
                return Book::query()->orderBy('title')->paginate($perPage);
            });

            return BookResource::collection($books);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to fetch books'], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/books/{id}",
     *     operationId="getBookById",
     *     tags={"Books"},     * 
     *     security={{"sanctum": {}}},
     *     summary="Get details of a specific book",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Book found"),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */

    public function show(Book $book)
    {
        try {
            return new BookResource($book);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to fetch book'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/books",
     *     operationId="createBook",
     *     tags={"Books"},
     *     security={{"sanctum": {}}},
     *     summary="Create a new book (Admin only)",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","author"},
     *             @OA\Property(property="title", type="string", example="Clean Code"),
     *             @OA\Property(property="author", type="string", example="Robert C. Martin"),
     *             @OA\Property(property="description", type="string", example="A guide to writing clean code."),
     *             @OA\Property(property="status", type="string", example="available")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Book created successfully"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function store(StoreBookRequest $request)
    {
        try {
            $book = Book::create($request->validated());
            Cache::flush();
            return (new BookResource($book))
                ->additional(['message' => 'Book created successfully'])
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to create book'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/books/{id}",
     *     operationId="updateBook",
     *     tags={"Books"},
     *     summary="Update an existing book (Admin only)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Refactoring"),
     *             @OA\Property(property="author", type="string", example="Martin Fowler"),
     *             @OA\Property(property="description", type="string", example="Improving the design of existing code."),
     *             @OA\Property(property="status", type="string", example="borrowed")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Book updated successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */

    public function update(UpdateBookRequest $request, Book $book)
    {
        try {
            $book->update($request->validated());
            Cache::flush();
            return (new BookResource($book))
                ->additional(['message' => 'Book updated successfully']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to update book'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/books/{id}",
     *     operationId="deleteBook",
     *     tags={"Books"},
     *     summary="Delete a book (Admin only)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Book deleted successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */
    public function destroy(Book $book)
    {
        try {
            $book->delete();
            Cache::flush();
            return response()->json(['message' => 'Book deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to delete book'], 500);
        }
    }
}
