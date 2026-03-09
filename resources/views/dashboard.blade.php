@extends('layouts.app')

@section('title', 'Dashboard - Tealmont Image Organizer')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    {{-- Welcome Banner with Tealmont gradient --}}
    <div class="mb-8 bg-gradient-to-r from-teal-600 to-teal-500 rounded-2xl shadow-lg p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Welcome back, {{ $user->name }}! 👋</h1>
                <p class="mt-2 text-teal-50 text-lg max-w-2xl">
                    @if($user->isAdmin())
                        You have full admin access to manage photos and users. Your dashboard is ready.
                    @else
                        You can view and organize your assigned photos. Let's get started!
                    @endif
                </p>
            </div>
            <div class="hidden md:block">
                <svg class="w-24 h-24 text-teal-200 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 5h16v2H4V5zm0 4h16v2H4V9zm0 4h16v2H4v-2zm0 4h10v2H4v-2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Quick Actions with improved tealmont styling --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-teal-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Quick Actions
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('photos.create') }}" class="group flex flex-col items-center p-6 rounded-xl border-2 border-teal-100 hover:border-teal-500 transition-all hover:shadow-lg bg-white">
                    <div class="bg-teal-50 group-hover:bg-teal-100 rounded-full p-4 mb-3 transition-colors">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 group-hover:text-teal-600">Take Photo</span>
                    <span class="text-xs text-gray-500 mt-1 text-center">Capture new moments</span>
                </a>

                <a href="{{ route('albums.index') }}" class="group flex flex-col items-center p-6 rounded-xl border-2 border-teal-100 hover:border-teal-500 transition-all hover:shadow-lg bg-white">
                    <div class="bg-teal-50 group-hover:bg-teal-100 rounded-full p-4 mb-3 transition-colors">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 group-hover:text-teal-600">Create Album</span>
                    <span class="text-xs text-gray-500 mt-1 text-center">Organize your photos</span>
                </a>

                <a href="{{ route('albums.index') }}" class="group flex flex-col items-center p-6 rounded-xl border-2 border-teal-100 hover:border-teal-500 transition-all hover:shadow-lg bg-white">
                    <div class="bg-teal-50 group-hover:bg-teal-100 rounded-full p-4 mb-3 transition-colors">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 group-hover:text-teal-600">Browse Albums</span>
                    <span class="text-xs text-gray-500 mt-1 text-center">View your collections</span>
                </a>

                <a href="{{ route('albums.index') }}" class="group flex flex-col items-center p-6 rounded-xl border-2 border-teal-100 hover:border-teal-500 transition-all hover:shadow-lg bg-white">
                    <div class="bg-teal-50 group-hover:bg-teal-100 rounded-full p-4 mb-3 transition-colors">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 group-hover:text-teal-600">View All Photos</span>
                    <span class="text-xs text-gray-500 mt-1 text-center">Browse your library</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Activity Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-teal-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Recent Activity
            </h2>
        </div>
        <div class="p-6">
            @if($recentPhotos->isNotEmpty())
                <div class="space-y-4">
                    @foreach($recentPhotos as $photo)
                        <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="bg-teal-50 rounded-lg p-2">
                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Photo added to <span class="font-medium">{{ $photo->album->name }}</span></p>
                                <p class="text-xs text-gray-500">{{ $photo->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-500">No recent activity to show</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection