<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function download(Album $album)
    {
        $album->load('photos');

        if ($album->photos->isEmpty()) {
            return back()->with('error', 'This album has no photos to download.');
        }

        $zipFileName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $album->name) . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Could not create ZIP file.');
        }

        foreach ($album->photos as $photo) {
            $filePath = Storage::disk('public')->path($photo->path);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $photo->filename);
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}
