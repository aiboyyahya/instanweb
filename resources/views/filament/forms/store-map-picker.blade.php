<div
    x-data="{
        map: null,
        marker: null,
        findModelInput(model) {
            const inputs = this.$el.closest('form') ? this.$el.closest('form').querySelectorAll('input, textarea, select') : document.querySelectorAll('input, textarea, select');

            for (let i = 0; i < inputs.length; i++) {
                for (let j = 0; j < inputs[i].attributes.length; j++) {
                    const attribute = inputs[i].attributes[j];

                    if (attribute.name.indexOf('wire:model') === 0 && attribute.value === model) {
                        return inputs[i];
                    }
                }
            }

            return null;
        },
        async init() {
            try {
                await this.ensureLeaflet();
            } catch (error) {
                this.$refs.status.textContent = 'Peta gagal dimuat. Isi koordinat secara manual.';
                return;
            }

            await this.$nextTick();

            const canvas = this.$refs.canvas;
            const latInput = this.findModelInput(this.$el.dataset.latitudeModel);
            const lngInput = this.findModelInput(this.$el.dataset.longitudeModel);

            if (!canvas || !latInput || !lngInput || !window.L) {
                return;
            }

            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            const hasPoint = Number.isFinite(lat) && Number.isFinite(lng);

            this.map = window.L.map(canvas).setView(
                [hasPoint ? lat : -2.548926, hasPoint ? lng : 118.0148634],
                hasPoint ? 15 : 5
            );

            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href=&quot;https://www.openstreetmap.org/copyright&quot;>OpenStreetMap</a>'
            }).addTo(this.map);

            if (hasPoint) {
                this.placeMarker(lat, lng, false);
            }

            this.map.on('click', (event) => this.placeMarker(event.latlng.lat, event.latlng.lng, true));

            this.$nextTick(() => this.map.invalidateSize());
        },
        ensureLeaflet() {
            if (window.L) {
                return Promise.resolve();
            }

            if (!document.querySelector('link[data-leaflet-css]')) {
                const css = document.createElement('link');
                css.rel = 'stylesheet';
                css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                css.setAttribute('data-leaflet-css', 'true');
                document.head.appendChild(css);
            }

            if (!window.__iwLeafletPromise) {
                window.__iwLeafletPromise = new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                    script.onload = () => resolve();
                    script.onerror = () => reject(new Error('Leaflet gagal dimuat'));
                    document.head.appendChild(script);
                });
            }

            return window.__iwLeafletPromise;
        },
        syncInput(input) {
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            input.dispatchEvent(new Event('blur', { bubbles: true }));
        },
        placeMarker(lat, lng, notify) {
            const latInput = this.findModelInput(this.$el.dataset.latitudeModel);
            const lngInput = this.findModelInput(this.$el.dataset.longitudeModel);

            if (!this.map || !latInput || !lngInput) {
                return;
            }

            if (!this.marker) {
                this.marker = window.L.marker([lat, lng], { draggable: true }).addTo(this.map);
                this.marker.on('dragend', () => {
                    const position = this.marker.getLatLng();
                    this.placeMarker(position.lat, position.lng, true);
                });
            } else {
                this.marker.setLatLng([lat, lng]);
            }

            latInput.value = Number(lat).toFixed(7);
            lngInput.value = Number(lng).toFixed(7);
            this.$refs.status.textContent = 'Titik toko: ' + Number(lat).toFixed(6) + ', ' + Number(lng).toFixed(6);

            if (notify) {
                this.syncInput(latInput);
                this.syncInput(lngInput);
            }
        },
        locate() {
            if (!navigator.geolocation) {
                this.$refs.status.textContent = 'Perangkat ini tidak mendukung deteksi lokasi. Klik peta secara manual.';
                return;
            }

            this.$refs.status.textContent = 'Mendeteksi lokasi...';

            navigator.geolocation.getCurrentPosition((position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                this.map.setView([lat, lng], 16);
                this.placeMarker(lat, lng, true);
            }, () => {
                this.$refs.status.textContent = 'Lokasi tidak dapat diakses. Klik peta secara manual.';
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000,
            });
        },
        clearMarker() {
            const latInput = this.findModelInput(this.$el.dataset.latitudeModel);
            const lngInput = this.findModelInput(this.$el.dataset.longitudeModel);

            if (this.marker) {
                this.map.removeLayer(this.marker);
                this.marker = null;
            }

            if (latInput && lngInput) {
                latInput.value = '';
                lngInput.value = '';
                this.syncInput(latInput);
                this.syncInput(lngInput);
            }

            this.$refs.status.textContent = 'Belum ada titik. Klik peta untuk menentukan lokasi toko.';
        }
    }"
    x-init="init()"
    data-latitude-model="data.latitude"
    data-longitude-model="data.longitude"
    class="space-y-3"
>
    <div x-ref="canvas" class="z-0 h-72 w-full overflow-hidden rounded-lg border border-gray-200 bg-gray-50"
        role="application" aria-label="Peta titik lokasi toko"></div>

    <div class="flex flex-wrap items-center gap-2">
        <button type="button" x-on:click="locate"
            class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 text-xs font-semibold text-gray-700 transition hover:bg-gray-50">
            Gunakan lokasi saya
        </button>

        <button type="button" x-on:click="clearMarker"
            class="inline-flex min-h-9 items-center justify-center rounded-lg px-3 text-xs font-semibold text-gray-500 transition hover:bg-gray-100 hover:text-gray-700">
            Hapus titik
        </button>
    </div>

    <p x-ref="status" class="text-xs leading-relaxed text-gray-500" aria-live="polite">
        Klik peta untuk menentukan titik asal pengiriman toko.
    </p>
</div>
