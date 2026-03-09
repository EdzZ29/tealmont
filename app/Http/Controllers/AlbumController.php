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

        // Build ZIP in memory using raw binary format (no ext-zip needed)
        $files = [];
        foreach ($album->photos as $photo) {
            $filePath = Storage::disk('public')->path($photo->path);
            if (file_exists($filePath)) {
                $files[] = [
                    'name' => $photo->filename,
                    'path' => $filePath,
                ];
            }
        }

        if (empty($files)) {
            return back()->with('error', 'No downloadable photos found.');
        }

        $zipData = '';
        $centralDir = '';
        $offset = 0;
        $fileCount = 0;

        foreach ($files as $file) {
            $content = file_get_contents($file['path']);
            $name = $file['name'];
            $crc = crc32($content);
            $size = strlen($content);
            $compressedContent = gzdeflate($content);
            $compressedSize = strlen($compressedContent);

            // Local file header
            $localHeader = pack('V', 0x04034b50)           // Local file header signature
                . pack('v', 20)                              // Version needed
                . pack('v', 0)                               // General purpose bit flag
                . pack('v', 8)                               // Compression method (deflate)
                . pack('v', 0) . pack('v', 0)               // Mod time/date
                . pack('V', $crc)                            // CRC-32
                . pack('V', $compressedSize)                 // Compressed size
                . pack('V', $size)                           // Uncompressed size
                . pack('v', strlen($name))                   // Filename length
                . pack('v', 0)                               // Extra field length
                . $name;

            $zipData .= $localHeader . $compressedContent;

            // Central directory entry
            $centralDir .= pack('V', 0x02014b50)            // Central dir signature
                . pack('v', 20)                              // Version made by
                . pack('v', 20)                              // Version needed
                . pack('v', 0)                               // General purpose bit flag
                . pack('v', 8)                               // Compression method
                . pack('v', 0) . pack('v', 0)               // Mod time/date
                . pack('V', $crc)                            // CRC-32
                . pack('V', $compressedSize)                 // Compressed size
                . pack('V', $size)                           // Uncompressed size
                . pack('v', strlen($name))                   // Filename length
                . pack('v', 0)                               // Extra field length
                . pack('v', 0)                               // File comment length
                . pack('v', 0)                               // Disk number start
                . pack('v', 0)                               // Internal file attributes
                . pack('V', 32)                              // External file attributes
                . pack('V', $offset)                         // Relative offset of local header
                . $name;

            $offset += strlen($localHeader) + $compressedSize;
            $fileCount++;
        }

        $centralDirOffset = strlen($zipData);
        $centralDirSize = strlen($centralDir);

        // End of central directory record
        $endOfCentralDir = pack('V', 0x06054b50)             // End of central dir signature
            . pack('v', 0)                                    // Number of this disk
            . pack('v', 0)                                    // Disk where central dir starts
            . pack('v', $fileCount)                           // Central dir entries on this disk
            . pack('v', $fileCount)                           // Total central dir entries
            . pack('V', $centralDirSize)                      // Size of central directory
            . pack('V', $centralDirOffset)                    // Offset of central dir
            . pack('v', 0);                                   // Comment length

        $zipData .= $centralDir . $endOfCentralDir;

        return response($zipData)
            ->header('Content-Type', 'application/zip')
            ->header('Content-Disposition', 'attachment; filename="' . $zipFileName . '"')
            ->header('Content-Length', strlen($zipData));
    }
}
