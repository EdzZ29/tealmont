@extends('layouts.app')

@section('title', 'Take Photo - Tealmont Image Organizer')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        <span class="text-gray-900">Take Photo</span>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Take Photo</h1>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
            @foreach($errors->all() as $error)
                <p class="text-sm text-red-600">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if($albums->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No albums yet</h3>
            <p class="mt-1 text-sm text-gray-500">You need to create an album first before taking photos.</p>
            <a href="{{ route('albums.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                Go to Albums
            </a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            {{-- Folder Selection --}}
            <div class="p-6 border-b border-gray-200">
                <label for="album-select" class="block text-sm font-medium text-gray-700 mb-2">Save to Album</label>
                <select id="album-select" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    @foreach($albums as $album)
                        <option value="{{ $album->id }}" {{ request('album') == $album->id ? 'selected' : '' }}>{{ $album->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Camera Preview --}}
            <div class="relative bg-black">
                <video id="camera-preview" autoplay playsinline class="w-full h-auto max-h-[480px] object-contain mx-auto"></video>
                <canvas id="photo-canvas" class="hidden"></canvas>

                {{-- Captured Preview --}}
                <div id="captured-preview" class="hidden">
                    <img id="captured-image" class="w-full h-auto max-h-[480px] object-contain mx-auto" alt="Captured photo">
                </div>
            </div>

            {{-- Controls --}}
            <div class="p-6">
                {{-- Camera Controls --}}
                <div id="camera-controls" class="flex items-center justify-center gap-4">
                    <button id="btn-capture" onclick="capturePhoto()" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-medium rounded-full hover:bg-indigo-700 transition-colors shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Take Photo
                    </button>
                </div>

                {{-- Review Controls (hidden initially) --}}
                <div id="review-controls" class="hidden flex items-center justify-center gap-4">
                    <button onclick="retakePhoto()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Retake
                    </button>
                    <form id="save-form" method="POST" action="{{ route('photos.store') }}">
                        @csrf
                        <input type="hidden" name="album_id" id="form-album-id">
                        <input type="hidden" name="photo" id="form-photo-data">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save to Album
                        </button>
                    </form>
                </div>

                {{-- Error message --}}
                <div id="camera-error" class="hidden mt-4 p-4 rounded-lg bg-red-50 border border-red-200 text-center">
                    <p class="text-sm text-red-600">Unable to access camera. Please allow camera permissions or use a device with a camera.</p>
                </div>
            </div>
        </div>
    @endif
</div>

@if($albums->isNotEmpty())
<script>
    const video = document.getElementById('camera-preview');
    const canvas = document.getElementById('photo-canvas');
    const capturedPreview = document.getElementById('captured-preview');
    const capturedImage = document.getElementById('captured-image');
    const cameraControls = document.getElementById('camera-controls');
    const reviewControls = document.getElementById('review-controls');
    const cameraError = document.getElementById('camera-error');
    let stream = null;

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1920 }, height: { ideal: 1080 } },
                audio: false
            });
            video.srcObject = stream;
        } catch (err) {
            cameraError.classList.remove('hidden');
            document.getElementById('btn-capture').disabled = true;
            document.getElementById('btn-capture').classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function capturePhoto() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        capturedImage.src = dataUrl;

        video.classList.add('hidden');
        capturedPreview.classList.remove('hidden');
        cameraControls.classList.add('hidden');
        reviewControls.classList.remove('hidden');

        document.getElementById('form-album-id').value = document.getElementById('album-select').value;
        document.getElementById('form-photo-data').value = dataUrl;
    }

    function retakePhoto() {
        video.classList.remove('hidden');
        capturedPreview.classList.add('hidden');
        cameraControls.classList.remove('hidden');
        reviewControls.classList.add('hidden');
    }

    startCamera();

    // Clean up camera on page leave
    window.addEventListener('beforeunload', () => {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });
</script>
@endif
@endsection
