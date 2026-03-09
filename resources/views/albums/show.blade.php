@extends('layouts.app')

@section('title', $album->name . ' - Tealmont Image Organizer')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('albums.index') }}" class="hover:text-indigo-600 transition-colors">Albums</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-gray-900">{{ $album->name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $album->name }}</h1>
            @if($album->description)
                <p class="mt-1 text-sm text-gray-500">{{ $album->description }}</p>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('photos.create', ['album' => $album->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Take Photo
            </a>
            <form method="POST" action="{{ route('albums.destroy', $album) }}" onsubmit="return confirm('Delete this album and all its photos?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if($album->photos->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No photos yet</h3>
            <p class="mt-1 text-sm text-gray-500">Take a photo to add it to this album.</p>
            <a href="{{ route('photos.create', ['album' => $album->id]) }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                Take Photo
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($album->photos as $photo)
                <div class="group relative bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->filename }}" class="w-full h-48 object-cover">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-end justify-end p-2 opacity-0 group-hover:opacity-100">
                        <form method="POST" action="{{ route('photos.destroy', $photo) }}" onsubmit="return confirm('Delete this photo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                    <div class="p-2">
                        <p class="text-xs text-gray-400">{{ $photo->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
