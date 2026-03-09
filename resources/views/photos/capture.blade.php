@extends('layouts.app')

@section('title', 'Take Photo - Tealmont Image Organizer')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-teal-600 transition-colors">Dashboard</a>
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
            <a href="{{ route('albums.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition-colors">
                Go to Albums
            </a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            {{-- Album Selection --}}
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <label for="album-select" class="block text-sm font-medium text-gray-700 mb-2">Save to Album</label>
                <select id="album-select" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                    @foreach($albums as $album)
                        <option value="{{ $album->id }}" {{ request('album') == $album->id ? 'selected' : '' }}>{{ $album->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Location Status --}}
            <div id="location-bar" class="px-4 sm:px-6 py-3 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center gap-2 text-sm">
                    <div id="loc-loading" class="flex items-center gap-2 text-gray-500">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Getting location...
                    </div>
                    <div id="loc-success" class="hidden flex items-center gap-2 text-green-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span id="loc-address">Location acquired</span>
                    </div>
                    <div id="loc-error" class="hidden flex items-center gap-2 text-amber-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                        <span id="loc-error-msg">Location unavailable</span>
                    </div>
                </div>
            </div>

            {{-- Camera Preview --}}
            <div class="relative bg-black" style="min-height: 240px;">
                <video id="camera-preview" autoplay playsinline muted class="w-full h-auto object-contain mx-auto" style="max-height: 70vh;"></video>
                <canvas id="photo-canvas" class="hidden"></canvas>

                {{-- Captured Preview --}}
                <div id="captured-preview" class="hidden">
                    <img id="captured-image" class="w-full h-auto object-contain mx-auto" style="max-height: 70vh;" alt="Captured photo">
                </div>

                {{-- Camera switch button (mobile) --}}
                <button id="btn-switch-camera" onclick="switchCamera()" class="hidden absolute top-3 right-3 p-2 bg-black/50 text-white rounded-full hover:bg-black/70 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                </button>

                {{-- Loading overlay --}}
                <div id="camera-loading" class="absolute inset-0 flex items-center justify-center bg-gray-900">
                    <div class="text-center text-white">
                        <svg class="w-10 h-10 animate-spin mx-auto mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <p class="text-sm">Starting camera...</p>
                    </div>
                </div>
            </div>

            {{-- Controls --}}
            <div class="p-4 sm:p-6">
                {{-- Camera Controls --}}
                <div id="camera-controls" class="flex items-center justify-center gap-4">
                    <button id="btn-capture" onclick="capturePhoto()" class="inline-flex items-center justify-center w-16 h-16 sm:w-auto sm:h-auto sm:px-6 sm:py-3 bg-teal-600 text-white font-medium rounded-full hover:bg-teal-700 transition-colors shadow-lg active:scale-95">
                        <svg class="w-8 h-8 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="hidden sm:inline ml-2">Take Photo</span>
                    </button>
                </div>

                {{-- Review Controls --}}
                <div id="review-controls" class="hidden">
                    {{-- Location preview on capture --}}
                    <div id="capture-location-info" class="hidden mb-4 p-3 bg-teal-50 border border-teal-200 rounded-lg">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-teal-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <div class="text-sm">
                                <p class="font-medium text-teal-800" id="capture-loc-address"></p>
                                <p class="text-teal-600" id="capture-loc-coords"></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-4">
                        <button onclick="retakePhoto()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Retake
                        </button>
                        <form id="save-form" method="POST" action="{{ route('photos.store') }}">
                            @csrf
                            <input type="hidden" name="album_id" id="form-album-id">
                            <input type="hidden" name="photo" id="form-photo-data">
                            <input type="hidden" name="latitude" id="form-latitude">
                            <input type="hidden" name="longitude" id="form-longitude">
                            <input type="hidden" name="address" id="form-address">
                            <button type="submit" id="btn-save" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Save to Album
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Error message --}}
                <div id="camera-error" class="hidden mt-4 p-4 rounded-lg bg-red-50 border border-red-200 text-center">
                    <svg class="w-8 h-8 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm font-medium text-red-700 mb-1">Camera access failed</p>
                    <p id="camera-error-detail" class="text-xs text-red-600">Please allow camera permissions in your browser settings.</p>
                    <button onclick="startCamera()" class="mt-3 px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors">
                        Try Again
                    </button>
                </div>

                {{-- HTTPS warning --}}
                <div id="https-warning" class="hidden mt-4 p-4 rounded-lg bg-amber-50 border border-amber-200 text-center">
                    <p class="text-sm text-amber-700">Camera requires a secure connection (HTTPS). Please access this page via HTTPS.</p>
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
    const cameraLoading = document.getElementById('camera-loading');
    const switchBtn = document.getElementById('btn-switch-camera');

    let stream = null;
    let currentFacing = 'environment';
    let hasMultipleCameras = false;
    let currentLat = null;
    let currentLng = null;
    let currentAddress = null;

    // Check for HTTPS (required for camera on mobile)
    if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
        document.getElementById('https-warning').classList.remove('hidden');
    }

    // Detect available cameras
    async function detectCameras() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(d => d.kind === 'videoinput');
            hasMultipleCameras = videoDevices.length > 1;
            if (hasMultipleCameras) {
                switchBtn.classList.remove('hidden');
            }
        } catch (e) {}
    }

    async function startCamera() {
        cameraError.classList.add('hidden');
        cameraLoading.classList.remove('hidden');

        // Stop any existing stream
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }

        // Try different constraint strategies for maximum mobile compat
        const constraintSets = [
            { video: { facingMode: { ideal: currentFacing }, width: { ideal: 1920 }, height: { ideal: 1080 } }, audio: false },
            { video: { facingMode: currentFacing }, audio: false },
            { video: true, audio: false }
        ];

        for (const constraints of constraintSets) {
            try {
                stream = await navigator.mediaDevices.getUserMedia(constraints);
                video.srcObject = stream;

                await new Promise((resolve, reject) => {
                    video.onloadedmetadata = () => {
                        video.play().then(resolve).catch(resolve);
                    };
                    setTimeout(reject, 5000);
                });

                cameraLoading.classList.add('hidden');
                detectCameras();
                return;
            } catch (err) {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
            }
        }

        // All attempts failed
        cameraLoading.classList.add('hidden');
        cameraError.classList.remove('hidden');

        const detail = document.getElementById('camera-error-detail');
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            detail.textContent = 'Your browser does not support camera access. Try Chrome, Safari, or Firefox.';
        } else if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
            detail.textContent = 'Camera requires HTTPS. Please access this site via a secure connection.';
        } else {
            detail.textContent = 'Please allow camera permissions in your browser settings, then tap "Try Again".';
        }

        document.getElementById('btn-capture').disabled = true;
        document.getElementById('btn-capture').classList.add('opacity-50', 'cursor-not-allowed');
    }

    async function switchCamera() {
        currentFacing = currentFacing === 'environment' ? 'user' : 'environment';
        await startCamera();
    }

    function capturePhoto() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
        capturedImage.src = dataUrl;

        video.classList.add('hidden');
        capturedPreview.classList.remove('hidden');
        cameraControls.classList.add('hidden');
        reviewControls.classList.remove('hidden');
        switchBtn.classList.add('hidden');

        document.getElementById('form-album-id').value = document.getElementById('album-select').value;
        document.getElementById('form-photo-data').value = dataUrl;

        if (currentLat !== null && currentLng !== null) {
            document.getElementById('form-latitude').value = currentLat;
            document.getElementById('form-longitude').value = currentLng;
            document.getElementById('form-address').value = currentAddress || '';

            const locInfo = document.getElementById('capture-location-info');
            locInfo.classList.remove('hidden');
            document.getElementById('capture-loc-coords').textContent = currentLat.toFixed(6) + ', ' + currentLng.toFixed(6);
            document.getElementById('capture-loc-address').textContent = currentAddress || 'Location acquired';
        }
    }

    function retakePhoto() {
        video.classList.remove('hidden');
        capturedPreview.classList.add('hidden');
        cameraControls.classList.remove('hidden');
        reviewControls.classList.add('hidden');
        document.getElementById('capture-location-info').classList.add('hidden');
        if (hasMultipleCameras) switchBtn.classList.remove('hidden');
    }

    // Geolocation
    function getLocation() {
        if (!navigator.geolocation) {
            showLocError('Geolocation not supported by this browser');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            async (position) => {
                currentLat = position.coords.latitude;
                currentLng = position.coords.longitude;

                try {
                    const resp = await fetch(
                        'https://nominatim.openstreetmap.org/reverse?format=json&lat=' +
                        encodeURIComponent(currentLat) + '&lon=' + encodeURIComponent(currentLng) +
                        '&zoom=18&addressdetails=1',
                        { headers: { 'Accept-Language': 'en' } }
                    );
                    const data = await resp.json();
                    if (data.display_name) {
                        currentAddress = data.display_name;
                    }
                } catch (e) {
                    currentAddress = currentLat.toFixed(6) + ', ' + currentLng.toFixed(6);
                }

                document.getElementById('loc-loading').classList.add('hidden');
                document.getElementById('loc-success').classList.remove('hidden');
                document.getElementById('loc-address').textContent = currentAddress || (currentLat.toFixed(6) + ', ' + currentLng.toFixed(6));
            },
            (error) => {
                let msg = 'Location unavailable';
                if (error.code === 1) msg = 'Location permission denied — photos will save without location';
                else if (error.code === 2) msg = 'Location unavailable — photos will save without location';
                else if (error.code === 3) msg = 'Location timed out — photos will save without location';
                showLocError(msg);
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
        );
    }

    function showLocError(msg) {
        document.getElementById('loc-loading').classList.add('hidden');
        document.getElementById('loc-error').classList.remove('hidden');
        document.getElementById('loc-error-msg').textContent = msg;
    }

    // Prevent form double-submit
    document.getElementById('save-form').addEventListener('submit', function() {
        document.getElementById('btn-save').disabled = true;
        document.getElementById('btn-save').textContent = 'Saving...';
    });

    // Start everything
    startCamera();
    getLocation();

    // Watch location updates
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
            (position) => {
                currentLat = position.coords.latitude;
                currentLng = position.coords.longitude;
            },
            () => {},
            { enableHighAccuracy: true, maximumAge: 30000 }
        );
    }

    // Clean up camera on page leave
    window.addEventListener('pagehide', () => {
        if (stream) stream.getTracks().forEach(track => track.stop());
    });
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden' && stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        } else if (document.visibilityState === 'visible' && !stream && !video.classList.contains('hidden')) {
            startCamera();
        }
    });
</script>
@endif
@endsection
