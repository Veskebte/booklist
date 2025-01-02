<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Book::all(), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'required|unique:books',
            'judul' => 'required',
            'penulis' => 'required',
            'penerbit' => 'required',
            'genre' => 'required',
            'deskripsi' => 'required',
            'foto' => 'nullable|file|mimes:png,jpg,jpeg',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('books', 'public');
            $validated['foto'] = $path;
        }

        $book = Book::create($validated);

        return response()->json($book, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $book = Book::withCount('favorites')->findOrFail($id);

        return response()->json([
            'book' => $book,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Buku tidak ditemukan']. 404);
        }

        $validated = $request->validate([
            'isbn' => 'required|unique:books,isbn'.$id,
            'judul' => 'required',
            'penulis' => 'required',
            'penerbit' => 'required',
            'genre' => 'required',
            'deskripsi' => 'required',
            'foto' => 'nullable|file|mimes:png,jpg,jpeg'
        ]);

        if ($request->hasFile('foto')) {
            if ($book->foto && \Storage::exists('public/'.$book->foto)) {
                \Storage::delete('public/'.$book->foto);
            }

            $path = $request->file('foto')->storage('books', 'public');
            $validated['foto'] = $path;
        }

        $book->update($validated);

        return response()->json($book, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Buku tidak ditemukan.'], 404);
        }

        $book->delete();

        return response()->json(['message' => 'Buku sudah dihapus!'], 200);
    }

    public function favorite(Request $request, $id) {
        $book = Book::findOrFail($id);
        $user = $request->user();

        if ($user->favorites()->where('book_id', $book->id)->exists()) {
            return response()->json(['message' => 'Anda sudah menfavoritkan buku ini']. 400);
        }

        $user->favorites()->create(['book_id' => $book->id]);

        return response()->json(['message' => 'Favorited successfully']);
    }

    public function unfavorite($id) {
        $favorite = Favorite::where('user_id', auth()->id())->where('book_id', $id)->first();

        if (!$favorite) {
            return response()->json(['message' => 'Anda belum menfavoritkan buku ini'], 400);
        }

        $favorite->delete();

        return response()->json(['message' => 'Unfavorited successfully']);
    }
}
