@extends('layouts.app')

@section('title', 'Photo Details - Tealmont Image Organizer')

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

    {{-- Breadcrumbs --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('albums.index') }}" class="hover:text-teal-600 transition-colors">Albums</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        <a href="{{ route('albums.show', $photo->album) }}" class="hover:text-teal-600 transition-colors">{{ $photo->album->name }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        <span class="text-gray-900">Photo Details</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Photo --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->filename }}" class="w-full h-auto object-contain bg-gray-900" style="max-height: 70vh;">
            </div>
        </div>

        {{-- Details Sidebar --}}
        <div class="space-y-6">
            {{-- Photo Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-900">Photo Details</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Filename</p>
                        <div class="mt-1 flex items-center gap-2" id="filename-display">
                            <p class="text-sm text-gray-900" id="filename-text">{{ $photo->filename }}</p>
                            <button onclick="showFilenameEdit()" class="text-gray-400 hover:text-teal-600 transition-colors" title="Edit filename">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                        </div>
                        <form id="filename-edit" method="POST" action="{{ route('photos.update', $photo) }}" class="hidden mt-1">
                            @csrf
                            @method('PATCH')
                            <div class="flex items-center gap-2">
                                <input type="text" name="filename" value="{{ $photo->filename }}" class="flex-1 text-sm rounded-md border border-gray-300 px-2.5 py-1.5 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                <button type="submit" class="p-1.5 bg-teal-600 text-white rounded-md hover:bg-teal-700 transition-colors" title="Save">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </button>
                                <button type="button" onclick="hideFilenameEdit()" class="p-1.5 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 transition-colors" title="Cancel">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Album</p>
                        <a href="{{ route('albums.show', $photo->album) }}" class="mt-1 text-sm text-teal-600 hover:text-teal-700">{{ $photo->album->name }}</a>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Taken by</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $photo->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Date</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $photo->created_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Time ago</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $photo->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            {{-- Location Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Location
                    </h2>
                </div>

                @if($photo->latitude && $photo->longitude)
                    {{-- OpenStreetMap Embed --}}
                    <div class="aspect-video">
                        <iframe
                            width="100%"
                            height="100%"
                            frameborder="0"
                            scrolling="no"
                            src="https://www.openstreetmap.org/export/embed.html?bbox={{ $photo->longitude - 0.005 }},{{ $photo->latitude - 0.003 }},{{ $photo->longitude + 0.005 }},{{ $photo->latitude + 0.003 }}&layer=mapnik&marker={{ $photo->latitude }},{{ $photo->longitude }}"
                            class="border-0">
                        </iframe>
                    </div>
                    <div class="p-5 space-y-3">
                        @if($photo->address)
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Address</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $photo->address }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Coordinates</p>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ number_format($photo->latitude, 6) }}, {{ number_format($photo->longitude, 6) }}</p>
                        </div>
                        <a href="https://www.openstreetmap.org/?mlat={{ $photo->latitude }}&mlon={{ $photo->longitude }}#map=17/{{ $photo->latitude }}/{{ $photo->longitude }}"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 text-sm text-teal-600 hover:text-teal-700 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            Open in Maps
                        </a>
                    </div>
                @else
                    <div class="p-5 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <p class="text-sm text-gray-500">No location data available</p>
                        <p class="text-xs text-gray-400 mt-1">Location was not captured for this photo</p>
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="space-y-3">
                <a href="{{ route('photos.download', $photo) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Download Photo
                </a>
                <div class="flex gap-3">
                    <a href="{{ route('albums.show', $photo->album) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                        Back to Album
                    </a>
                    <form method="POST" action="{{ route('photos.destroy', $photo) }}" onsubmit="return confirm('Delete this photo permanently?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showFilenameEdit() {
        document.getElementById('filename-display').classList.add('hidden');
        document.getElementById('filename-edit').classList.remove('hidden');
        document.querySelector('#filename-edit input').focus();
        document.querySelector('#filename-edit input').select();
    }
    function hideFilenameEdit() {
        document.getElementById('filename-display').classList.remove('hidden');
        document.getElementById('filename-edit').classList.add('hidden');
    }
</script>
@endsection
