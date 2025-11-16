<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Borrow;
use App\Http\Requests\BorrowBookRequest;
use App\Http\Resources\BorrowingResource;
use Illuminate\Support\Facades\Auth;
use App\Events\BookBorrowed;
use App\Events\BookReturned;
use Illuminate\Support\Facades\Cache;

class BorrowingController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/borrow",
     *     summary="Borrow a book",
     *     tags={"Borrowings"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(required={"book_id"}, @OA\Property(property="book_id", type="integer", example=1))
     *     ),
     *     @OA\Response(response=200, description="Book borrowed successfully"),
     *     @OA\Response(response=400, description="Book already borrowed")
     * )
     */
    public function borrow(BorrowBookRequest $request)
    {
        $book = Book::findOrFail($request->book_id);

        if ($book->status === 'borrowed') {
            return response()->json(['message' => 'Book is already borrowed.'], 400);
        }

        $borrowing = Borrow::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        $book->status = 'borrowed';
        $book->save();

        // Dispatch event
        event(new BookBorrowed(Auth::user(), $book));


        return (new BorrowingResource($borrowing))
            ->additional(['message' => 'Book borrowed successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/return/{book_id}",
     *     summary="Return a borrowed book",
     *     tags={"Borrowings"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="book_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Book returned successfully"),
     *     @OA\Response(response=400, description="Book not borrowed by this user")
     * )
     */
    public function returnBook(int $book_id)
    {
        $borrowing = Borrow::where('user_id', Auth::id())
            ->where('book_id', $book_id)
            ->whereNull('returned_at')
            ->first();

        if (!$borrowing) {
            return response()->json(['message' => 'You have not borrowed this book or it is already returned.'], 400);
        }

        $borrowing->update(['returned_at' => now()]);
        $borrowing->book->update(['status' => 'available']);

        event(new BookReturned(Auth::user(), $borrowing->book));

        return (new BorrowingResource($borrowing))
            ->additional(['message' => 'Book returned successfully']);
    }

    /**
     * @OA\Get(
     *     path="/api/borrowings",
     *     summary="List all borrowings of current user",
     *     tags={"Borrowings"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="List of borrowings")
     * )
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = min(50, $request->get('per_page', 15));
        $cacheKey = "books:page:{$page}:per:{$perPage}";

        $borrowings = Cache::remember($cacheKey, 60, function () use ($perPage) {
            return Borrow::with(['book', 'user'])
                ->where('user_id', Auth::id())->paginate($perPage);
        });

        return BorrowingResource::collection($borrowings);
    }
}
