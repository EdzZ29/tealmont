<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index(Request $request)
    {
        $albums = Album::where('user_id', $request->user()->id)
            ->withCount('photos')
            ->latest()
            ->get();

        return view('albums.index', compact('albums'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->albums()->create($validated);

        return redirect()->route('albums.index')->with('success', 'Album created successfully.');
    }

    public function show(Album $album, Request $request)
    {
        if ($album->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403);
        }

        $album->load('photos');

        return view('albums.show', compact('album'));
    }

    public function destroy(Album $album, Request $request)
    {
        if ($album->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403);
        }

        $album->delete();

        return redirect()->route('albums.index')->with('success', 'Album deleted successfully.');
    }
}
