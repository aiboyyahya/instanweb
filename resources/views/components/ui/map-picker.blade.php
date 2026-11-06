@props([
    'latitudeName' => 'latitude',
    'longitudeName' => 'longitude',
    'lat' => null,
    'lng' => null,
    'originLat' => null,
    'originLng' => null,
    'originLabel' => 'Lokasi toko',
    'label' => 'Titik Lokasi',
    'hint' => null,
    'required' => false,
    'height' => 'h-72',
    'useGeolocation' => true,
    'emptyHint' => 'Klik peta atau gunakan lokasi saya untuk menentukan titik pengiriman.',
    'defaultLat' => -2.548926,
    'defaultLng' => 118.0148634,
    'defaultZoom' => 5,
])

@php
    $mapId = 'map-' . \Illuminate\Support\Str::slug($latitudeName) . '-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));
    $statusId = $mapId . '-status';
    $hasPoint = filled($lat) && filled($lng);
    $hasOrigin = filled($originLat) && filled($originLng);
@endphp

@push('head')
    @once
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    @endonce
@endpush

<x-ui.field :label="$label" :name="$latitudeName" :hint="$hint" :required="$required" :input-id="$latitudeName">
    <div class="space-y-3" data-map-picker
        data-map-id="{{ $mapId }}"
        data-lat-name="{{ $latitudeName }}"
        data-lng-name="{{ $longitudeName }}"
        data-origin-lat="{{ $hasOrigin ? $originLat : '' }}"
        data-origin-lng="{{ $hasOrigin ? $originLng : '' }}"
        data-origin-label="{{ $originLabel }}"
        data-empty-hint="{{ $emptyHint }}"
        data-current-lat="{{ $hasPoint ? $lat : '' }}"
        data-current-lng="{{ $hasPoint ? $lng : '' }}"
        data-default-lat="{{ $defaultLat }}"
        data-default-lng="{{ $defaultLng }}"
        data-default-zoom="{{ $defaultZoom }}">
        <div id="{{ $mapId }}" data-map-canvas
            class="{{ $height }} relative z-0 w-full overflow-hidden rounded-card border border-line bg-surface-soft"
            role="application" aria-label="{{ $label }}"></div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($useGeolocation)
                <button type="button" data-map-locate
                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-field border border-line-strong bg-surface px-3 text-xs font-semibold text-fg transition hover:bg-surface-soft">
                    <x-ui.icon name="map-pin" class="h-4 w-4" />
                    Gunakan lokasi saya
                </button>
            @endif

            <button type="button" data-map-clear
                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-field px-3 text-xs font-semibold text-fg-muted transition hover:bg-surface-soft hover:text-fg">
                Hapus titik
            </button>

            @if ($hasOrigin)
                <span class="inline-flex items-center gap-1.5 text-xs text-fg-muted">
                    <span class="h-2.5 w-2.5 rounded-full bg-brand" aria-hidden="true"></span>
                    Asal: {{ $originLabel }}
                </span>
            @endif
        </div>

        <input type="hidden" name="{{ $latitudeName }}" id="{{ $latitudeName }}"
            value="{{ $hasPoint ? $lat : '' }}">
        <input type="hidden" name="{{ $longitudeName }}" id="{{ $longitudeName }}"
            value="{{ $hasPoint ? $lng : '' }}">

        <p id="{{ $statusId }}" data-map-status class="text-xs leading-relaxed text-fg-muted" aria-live="polite">
            @if ($hasPoint)
                Titik dipilih: {{ number_format((float) $lat, 6, '.', '') }}, {{ number_format((float) $lng, 6, '.', '') }}
            @else
                {{ $emptyHint }}
            @endif
        </p>
    </div>
</x-ui.field>

@push('scripts')
    @once
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        <script>
            (function () {
                'use strict';

                function toNumber(value) {
                    var parsed = parseFloat(value);
                    return Number.isFinite(parsed) ? parsed : null;
                }

                function formatCoordinate(value) {
                    return Number(value).toFixed(6);
                }

                function initPicker(root) {
                    var canvas = root.querySelector('[data-map-canvas]');
                    var latInput = root.querySelector('input[name="' + root.dataset.latName + '"]');
                    var lngInput = root.querySelector('input[name="' + root.dataset.lngName + '"]');
                    var statusEl = root.querySelector('[data-map-status]');

                    if (!canvas || !latInput || !lngInput || typeof L === 'undefined') {
                        return;
                    }

                    var originLat = toNumber(root.dataset.originLat);
                    var originLng = toNumber(root.dataset.originLng);
                    var currentLat = toNumber(root.dataset.currentLat) ?? toNumber(latInput.value);
                    var currentLng = toNumber(root.dataset.currentLng) ?? toNumber(lngInput.value);
                    var defaultLat = toNumber(root.dataset.defaultLat) ?? -2.548926;
                    var defaultLng = toNumber(root.dataset.defaultLng) ?? 118.0148634;
                    var defaultZoom = toNumber(root.dataset.defaultZoom) ?? 5;

                    var centerLat = currentLat ?? originLat ?? defaultLat;
                    var centerLng = currentLng ?? originLng ?? defaultLng;
                    var zoom = currentLat !== null ? 15 : (originLat !== null ? 13 : defaultZoom);

                    var map = L.map(canvas).setView([centerLat, centerLng], zoom);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    if (originLat !== null && originLng !== null) {
                        L.circleMarker([originLat, originLng], {
                            radius: 9,
                            color: '#0f766e',
                            fillColor: '#14b8a6',
                            fillOpacity: 0.9,
                            weight: 2,
                        }).addTo(map).bindTooltip(root.dataset.originLabel || 'Lokasi toko', {
                            permanent: false,
                            direction: 'top',
                        });
                    }

                    var marker = null;

                    function setStatus(lat, lng) {
                        if (lat === null || lng === null) {
                            statusEl.textContent = root.dataset.emptyHint || 'Klik peta untuk menentukan titik lokasi.';
                            return;
                        }

                        statusEl.textContent = 'Titik dipilih: ' + formatCoordinate(lat) + ', ' + formatCoordinate(lng);
                    }

                    function writePoint(lat, lng, notify) {
                        latInput.value = lat === null ? '' : String(lat);
                        lngInput.value = lng === null ? '' : String(lng);
                        setStatus(lat, lng);

                        if (notify) {
                            root.dispatchEvent(new CustomEvent('map-picker:change', {
                                bubbles: true,
                                detail: { latitude: lat, longitude: lng },
                            }));
                        }
                    }

                    function placeMarker(lat, lng, notify) {
                        if (marker) {
                            marker.setLatLng([lat, lng]);
                        } else {
                            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                            marker.on('dragend', function () {
                                var position = marker.getLatLng();
                                writePoint(position.lat, position.lng, true);
                            });
                        }

                        writePoint(lat, lng, notify);
                    }

                    if (currentLat !== null && currentLng !== null) {
                        placeMarker(currentLat, currentLng, false);
                    }

                    map.on('click', function (event) {
                        placeMarker(event.latlng.lat, event.latlng.lng, true);
                    });

                    var locateButton = root.querySelector('[data-map-locate]');

                    if (locateButton) {
                        locateButton.addEventListener('click', function () {
                            if (!navigator.geolocation) {
                                statusEl.textContent = 'Perangkat ini tidak mendukung deteksi lokasi. Silakan klik peta.';
                                return;
                            }

                            locateButton.disabled = true;
                            statusEl.textContent = 'Mendeteksi lokasi Anda...';

                            navigator.geolocation.getCurrentPosition(function (position) {
                                locateButton.disabled = false;
                                var lat = position.coords.latitude;
                                var lng = position.coords.longitude;
                                map.setView([lat, lng], 16);
                                placeMarker(lat, lng, true);
                            }, function () {
                                locateButton.disabled = false;
                                statusEl.textContent = 'Lokasi tidak dapat diakses. Silakan klik peta secara manual.';
                            }, {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 60000,
                            });
                        });
                    }

                    var clearButton = root.querySelector('[data-map-clear]');

                    if (clearButton) {
                        clearButton.addEventListener('click', function () {
                            if (marker) {
                                map.removeLayer(marker);
                                marker = null;
                            }

                            writePoint(null, null, true);
                        });
                    }
                }

                function boot() {
                    document.querySelectorAll('[data-map-picker]').forEach(initPicker);
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', boot);
                } else {
                    boot();
                }
            })();
        </script>
    @endonce
@endpush
