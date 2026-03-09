<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $totalAlbums = Album::where('user_id', $user->id)->count();
        $totalPhotos = Photo::where('user_id', $user->id)->count();

        // Calculate storage used
        $bytes = Photo::where('user_id', $user->id)->get()->sum(function ($photo) {
            return Storage::disk('public')->exists($photo->path)
                ? Storage::disk('public')->size($photo->path)
                : 0;
        });
        $storageUsed = $bytes < 1048576
            ? round($bytes / 1024, 1) . ' KB'
            : round($bytes / 1048576, 1) . ' MB';

        $recentPhotos = Photo::where('user_id', $user->id)
            ->with('album')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'totalAlbums',
            'totalPhotos',
            'storageUsed',
            'recentPhotos',
        ));
    }
}
