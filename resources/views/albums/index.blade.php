@extends('layouts.app')

@section('title', 'Albums - Tealmont Image Organizer')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Albums</h1>
        <button onclick="document.getElementById('create-album-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Album
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if($albums->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No albums yet</h3>
            <p class="mt-1 text-sm text-gray-500">Create your first album to start organizing photos.</p>
            <button onclick="document.getElementById('create-album-modal').classList.remove('hidden')" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                Create Album
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($albums as $album)
                <a href="{{ route('albums.show', $album) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all overflow-hidden group">
                    <div class="h-40 bg-gray-100 flex items-center justify-center">
                        @if($album->photos->first())
                            <img src="{{ asset('storage/' . $album->photos->first()->path) }}" alt="" class="w-full h-full object-cover">
                        @else
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $album->name }}</h3>
                        @if($album->description)
                            <p class="mt-1 text-sm text-gray-500 truncate">{{ $album->description }}</p>
                        @endif
                        <p class="mt-2 text-xs text-gray-400">{{ $album->photos_count }} {{ Str::plural('photo', $album->photos_count) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

{{-- Create Album Modal --}}
<div id="create-album-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('create-album-modal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Create New Album</h2>
        <form method="POST" action="{{ route('albums.store') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Album Name</label>
                <input type="text" id="name" name="name" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="e.g. Wedding Photos">
            </div>
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                <textarea id="description" name="description" rows="3" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Describe this album..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('create-album-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">Create Album</button>
            </div>
        </form>
    </div>
</div>
@endsection
