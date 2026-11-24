        <section class="mt-8 rounded-xl border border-brand-border p-5 sm:p-6" aria-labelledby="location-heading">
            <div class="flex flex-wrap items-center gap-3">
                <h2 id="location-heading" class="text-lg font-bold text-slate-950">Member location</h2>
                <span class="text-xs font-semibold text-slate-500">Optional</span>
            </div>
            <p class="mt-2 text-sm leading-6 text-slate-600">Choose a point on the map or enter coordinates below.</p>
            <div class="mt-4"><button type="button" id="location-current" class="rounded-xl border border-brand-border px-4 py-3 text-sm font-bold text-slate-900">Use current location</button></div>
            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div><label for="latitude" class="text-sm font-bold text-slate-700">Latitude</label><input id="latitude" @if ($saveCoordinates) name="latitude" @endif type="number" step="0.0000001" min="-90" max="90" value="{{ old('latitude') }}" class="mt-2 w-full rounded-xl border border-brand-border bg-white px-3.5 py-3 text-sm text-slate-900">@error('latitude')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror</div>
                <div><label for="longitude" class="text-sm font-bold text-slate-700">Longitude</label><input id="longitude" @if ($saveCoordinates) name="longitude" @endif type="number" step="0.0000001" min="-180" max="180" value="{{ old('longitude') }}" class="mt-2 w-full rounded-xl border border-brand-border bg-white px-3.5 py-3 text-sm text-slate-900">@error('longitude')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror</div>
            </div>
            <div class="mt-5 overflow-hidden rounded-xl border border-brand-border bg-brand-canvas">
                <div id="member-map" class="w-full" style="height: 18rem;" aria-label="Select a member location on the map"></div>
                <div class="px-5 py-4">
                    <p id="map-status" class="text-sm leading-6 text-slate-600" role="status">Loading map…</p>
                    <p class="mt-1 text-xs text-slate-500">Address data: <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer" class="underline">© OpenStreetMap contributors</a></p>
                    <div class="mt-3 rounded-xl border border-brand-border bg-white p-5">
                        <div class="flex items-center gap-3"><span aria-hidden="true" class="text-brand-primary"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M8 18h9a4 4 0 0 0 .3-8A6 6 0 0 0 6 11a3.5 3.5 0 0 0 2 7Z"/></svg></span><p class="text-sm font-bold text-slate-950">Current weather</p></div>
                        <p id="weather-status" class="text-sm leading-6 text-slate-600" style="margin-top: 0.375rem;" role="status" aria-live="polite">Select a location to see current weather.</p>
                        <dl id="weather-details" hidden style="display: none; gap: 0.5rem; margin-top: 1rem;" class="text-sm leading-6 text-slate-600">
                            <dt class="sr-only">Temperature</dt><dd id="weather-temperature" class="text-slate-950" style="font-size: 2.25rem; line-height: 1.15; font-weight: 700; letter-spacing: -0.03em;">—</dd>
                            <dt class="sr-only">Location</dt><dd id="weather-location" class="text-slate-950" style="overflow-wrap: anywhere; font-weight: 600;">—</dd>
                            <dt class="sr-only">Conditions</dt><dd id="weather-conditions" class="text-slate-600" style="text-transform: capitalize;">—</dd>
                            <dt class="sr-only">Humidity</dt><dd class="text-slate-600">Humidity <span id="weather-humidity" class="text-slate-950" style="font-weight: 600;">—</span></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </section>

    <script>
        (() => {
            const lat = document.getElementById('latitude'), lng = document.getElementById('longitude');
            const mapStatus = document.getElementById('map-status'), weather = document.getElementById('weather-status');
            const address = document.getElementById('address');
            const weatherLocation = document.getElementById('weather-location');
            const weatherDetails = document.getElementById('weather-details');
            const key = {{ Js::from(config('services.google_maps.key')) }}, weatherUrl = {{ Js::from(route('weather.show')) }};
            const reverseUrl = {{ Js::from(route('location.reverse')) }};
            let map, marker, geocoder, timer, request, lookupId = 0, reverseTimer;
            const update = (reverse = true) => {
                clearTimeout(timer);
                clearTimeout(reverseTimer);
                if (request) request.abort();
                weatherDetails.style.display = 'none'; weatherDetails.hidden = true;
                const latitude = Number(lat.value), longitude = Number(lng.value);
                if (!lat.value || !lng.value || !Number.isFinite(latitude) || !Number.isFinite(longitude) || Math.abs(latitude) > 90 || Math.abs(longitude) > 180) {
                    lookupId++; weatherLocation.textContent = '';
                    if (marker) marker.setMap(null);
                    weather.textContent = 'Enter valid coordinates to see current weather.'; return;
                }
                if (map && marker) { const position = { lat: latitude, lng: longitude }; marker.setMap(map); marker.setPosition(position); map.panTo(position); }
                const id = ++lookupId;
                if (reverse && geocoder) geocoder.geocode({ location: { lat: latitude, lng: longitude } }, (results, status) => {
                    if (id !== lookupId) return;
                    if (status === 'OK' && results[0]) { address.value = results[0].formatted_address; weatherLocation.textContent = address.value; }
                    else {
                        mapStatus.textContent = 'Looking up the selected address…';
                        reverseTimer = setTimeout(async () => {
                            try {
                                const url = new URL(reverseUrl); url.searchParams.set('latitude', latitude); url.searchParams.set('longitude', longitude);
                                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                                if (!response.ok) throw new Error('Address unavailable');
                                const data = await response.json();
                                if (id !== lookupId) return;
                                address.value = data.address; weatherLocation.textContent = data.address; mapStatus.textContent = 'Address selected.';
                            } catch (error) { if (id === lookupId) { weatherLocation.textContent = 'Selected location'; mapStatus.textContent = 'Address lookup unavailable; coordinates are still selected.'; } }
                        }, 500);
                    }
                });
                weather.textContent = 'Loading weather…';
                timer = setTimeout(async () => {
                    request = new AbortController();
                    try {
                        const url = new URL(weatherUrl); url.searchParams.set('latitude', latitude); url.searchParams.set('longitude', longitude);
                        const response = await fetch(url, { signal: request.signal, headers: { Accept: 'application/json' } });
                        if (!response.ok) throw new Error('Weather unavailable');
                        const data = await response.json();
                        weatherLocation.textContent = address.value || 'Selected location';
                        document.getElementById('weather-temperature').textContent = `${data.temperature} °C`;
                        document.getElementById('weather-conditions').textContent = data.description;
                        document.getElementById('weather-humidity').textContent = `${data.humidity}%`;
                        weather.textContent = ''; weatherDetails.hidden = false; weatherDetails.style.display = 'grid';
                    } catch (error) { if (error.name !== 'AbortError') weather.textContent = 'Weather information is unavailable. Please try again.'; }
                }, 400);
            };
            lat.addEventListener('input', update); lng.addEventListener('input', update);
            address.addEventListener('input', () => { lookupId++; });
            const memberSelect = document.getElementById('member_id');
            const selectLocation = () => { const option = memberSelect?.selectedOptions[0]; address.value = option?.dataset.address || ''; lat.value = option?.dataset.latitude || ''; lng.value = option?.dataset.longitude || ''; update(); };
            memberSelect?.addEventListener('change', selectLocation);
            if (memberSelect) selectLocation();
            document.getElementById('location-current').addEventListener('click', () => {
                if (!navigator.geolocation) { mapStatus.textContent = 'Location is not supported by this browser.'; return; }
                mapStatus.textContent = 'Finding your location…';
                navigator.geolocation.getCurrentPosition((position) => { lat.value = position.coords.latitude.toFixed(7); lng.value = position.coords.longitude.toFixed(7); mapStatus.textContent = 'Current location selected.'; update(); }, (error) => { mapStatus.textContent = error.code === 1 ? 'Location permission denied. Select a point on the map instead.' : 'Unable to find your location. Select a point on the map instead.'; }, { timeout: 10000 });
            });
            window.initMemberMap = () => {
                const hasLocation = lat.value !== '' && lng.value !== '';
                const center = hasLocation ? { lat: Number(lat.value), lng: Number(lng.value) } : { lat: 14.5995, lng: 120.9842 };
                map = new google.maps.Map(document.getElementById('member-map'), { center, zoom: 12, streetViewControl: false, mapTypeControl: true });
                geocoder = new google.maps.Geocoder();
                marker = new google.maps.Marker({ map, position: hasLocation ? center : null, draggable: true, icon: { path: 'M 0,-20 C -11,-20 -16,-12 -16,-5 C -16,5 0,20 0,20 C 0,20 16,5 16,-5 C 16,-12 11,-20 0,-20 Z', fillColor: '#623547', fillOpacity: 1, strokeColor: '#ffffff', strokeWeight: 2.5, scale: 1.25, anchor: new google.maps.Point(0, 20) } });
                const autocomplete = new google.maps.places.Autocomplete(address, { fields: ['geometry', 'formatted_address'] });
                autocomplete.addListener('place_changed', () => { const place = autocomplete.getPlace(); if (!place.geometry?.location) { mapStatus.textContent = 'Select an address from the suggestions.'; return; } const point = place.geometry.location; lat.value = point.lat().toFixed(7); lng.value = point.lng().toFixed(7); address.value = place.formatted_address || address.value; weatherLocation.textContent = address.value; update(false); });
                marker.addListener('dragend', (event) => { lat.value = event.latLng.lat().toFixed(7); lng.value = event.latLng.lng().toFixed(7); update(); });
                map.addListener('click', (event) => { lat.value = event.latLng.lat().toFixed(7); lng.value = event.latLng.lng().toFixed(7); update(); });
                mapStatus.textContent = 'Select a point on the map or enter coordinates above.';
                if (hasLocation) update();
            };
            window.gm_authFailure = () => { mapStatus.textContent = 'Map authorization failed. You can enter coordinates manually.'; };
            if (!key) { mapStatus.textContent = 'Map is unavailable. You can enter coordinates manually.'; }
            else {
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(key)}&libraries=places&loading=async&callback=initMemberMap`;
                script.async = true; script.onerror = () => { mapStatus.textContent = 'Map could not be loaded. You can enter coordinates manually.'; };
                document.head.appendChild(script);
            }
            if (lat.value && lng.value) update();
        })();
    </script>

