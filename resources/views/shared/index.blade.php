@extends('layouts.app')

@section('title', 'Shared Photos - Tealmont Image Organizer')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Shared Photos</h1>
            <p class="mt-1 text-sm text-gray-500">Browse albums and photos from all team members</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Dashboard
        </a>
    </div>

    @if($albums->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No shared photos yet</h3>
            <p class="mt-1 text-sm text-gray-500">No team members have created any albums yet.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($albums as $album)
                <a href="{{ route('albums.show', $album) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 hover:border-teal-300 hover:shadow-md transition-all overflow-hidden group">
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
                        <h3 class="font-semibold text-gray-900 group-hover:text-teal-600 transition-colors">{{ $album->name }}</h3>
                        @if($album->description)
                            <p class="mt-1 text-sm text-gray-500 truncate">{{ $album->description }}</p>
                        @endif
                        <div class="mt-2 flex items-center justify-between">
                            <p class="text-xs text-gray-400">{{ $album->photos_count }} {{ Str::plural('photo', $album->photos_count) }}</p>
                            <span class="inline-flex items-center gap-1 text-xs text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $album->user->name }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
