<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class SharedController extends Controller
{
    public function index()
    {
        $albums = Album::with(['user', 'photos'])
            ->withCount('photos')
            ->latest()
            ->get();

        return view('shared.index', compact('albums'));
    }
}
