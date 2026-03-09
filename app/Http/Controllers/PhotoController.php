<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    public function create(Request $request)
    {
        $albums = Album::where('user_id', $request->user()->id)->latest()->get();

        return view('photos.capture', compact('albums'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'album_id' => ['required', 'exists:albums,id'],
            'photo' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $album = Album::findOrFail($validated['album_id']);

        if ($album->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403);
        }

        // Decode the base64 image from the camera
        $imageData = $validated['photo'];
        if (str_contains($imageData, ',')) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
        }
        $imageData = base64_decode($imageData);

        if ($imageData === false) {
            return back()->withErrors(['photo' => 'Invalid image data.']);
        }

        $filename = Str::uuid() . '.jpg';
        $path = 'photos/' . $album->id . '/' . $filename;

        Storage::disk('public')->put($path, $imageData);

        Photo::create([
            'album_id' => $album->id,
            'user_id' => $request->user()->id,
            'filename' => $filename,
            'path' => $path,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        return redirect()->route('albums.show', $album)->with('success', 'Photo saved successfully.');
    }

    public function show(Photo $photo, Request $request)
    {
        if ($photo->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403);
        }

        $photo->load('album', 'user');

        return view('photos.show', compact('photo'));
    }

    public function update(Photo $photo, Request $request)
    {
        if ($photo->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'filename' => ['required', 'string', 'max:255'],
        ]);

        $photo->update(['filename' => $validated['filename']]);

        return back()->with('success', 'Filename updated successfully.');
    }

    public function download(Photo $photo, Request $request)
    {
        if ($photo->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403);
        }

        $filePath = Storage::disk('public')->path($photo->path);

        return response()->download($filePath, $photo->filename);
    }

    public function destroy(Photo $photo, Request $request)
    {
        if ($photo->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403);
        }

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return back()->with('success', 'Photo deleted successfully.');
    }
}
