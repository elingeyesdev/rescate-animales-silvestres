@once
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
window.initMapWithGeolocation = function initMapWithGeolocation(opts) {
    const {
        mapId,
        latInputId,
        lonInputId,
        dirInputId = null,
        start = { lat: -17.7833, lon: -63.1821, zoom: 13 }, // Santa Cruz
        enableReverseGeocode = true,
    } = opts || {};
    const mapEl = document.getElementById(mapId);
    if (!mapEl) return null;
    const latInput = document.getElementById(latInputId);
    const lonInput = document.getElementById(lonInputId);
    const dirInput = dirInputId ? document.getElementById(dirInputId) : null;
    const dirText = dirInputId ? document.getElementById(dirInputId + '_text') : null;

    const initLat = latInput && latInput.value ? parseFloat(latInput.value) : null;
    const initLon = lonInput && lonInput.value ? parseFloat(lonInput.value) : null;
    const hasInitCoords = (initLat !== null && !Number.isNaN(initLat)) && (initLon !== null && !Number.isNaN(initLon));
    const map = L.map(mapId).setView([
        initLat ?? start.lat,
        initLon ?? start.lon,
    ], (initLat && initLon) ? 15 : start.zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
    let marker = null;
    let userLocked = false; // si el usuario fija manualmente la posición, no sobrescribir con geolocalización
    let hasCentered = false; // centrar/zoom solo la primera vez por geolocalización

    function setMarker(lat, lon, setView = false, fromUser = false) {
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lon]).addTo(map);
        if (setView) map.setView([lat, lon], 16);
        if (latInput) latInput.value = lat;
        if (lonInput) lonInput.value = lon;
        if (enableReverseGeocode && dirInput) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`)
                .then(r => r.json())
                .then(d => {
                    const display = d.display_name || '';
                    dirInput.value = display;
                    if (dirText) dirText.textContent = display;
                })
                .catch(() => { if (dirText) dirText.textContent = ''; });
        }
        if (fromUser && watchId !== null) {
            try { navigator.geolocation.clearWatch(watchId); } catch (e) {}
            watchId = null;
            userLocked = true;
        }
    }

    // If there is an existing value, place marker there
    if (initLat && initLon) setMarker(initLat, initLon, true);

    // Prefer navigator.geolocation with high accuracy only when no initial coords
    // --- TRACKING EN TIEMPO REAL ---
    let watchId = null;

    // Reinicia el watch cuando vuelves a la pestaña (solo si no hay coordenadas iniciales)
    document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === "visible") {
            // Evita saltos visuales
            setTimeout(() => map.invalidateSize(), 150);

            // Reiniciar watch
            if (!hasInitCoords && !userLocked && navigator.geolocation) {
                if (watchId !== null) navigator.geolocation.clearWatch(watchId);
                watchId = navigator.geolocation.watchPosition(
                    (pos) => {
                        if (userLocked) return;
                        const lat = pos.coords.latitude.toFixed(6);
                        const lon = pos.coords.longitude.toFixed(6);
                        setMarker(lat, lon, true, false);
                        hasCentered = true;
                    },
                    () => {},
                    {
                        enableHighAccuracy: true,
                        maximumAge: 0,
                        timeout: 5000,
                    }
                );
            }
        } else {
            // Stop para ahorrar recursos
            if (watchId !== null) navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
    });

    // Iniciar seguimiento solo si no hay coordenadas iniciales
    if (!hasInitCoords && navigator.geolocation) {
        watchId = navigator.geolocation.watchPosition(
            (pos) => {
                if (userLocked) return;
                const lat = pos.coords.latitude.toFixed(6);
                const lon = pos.coords.longitude.toFixed(6);
                setMarker(lat, lon, true, false);
                hasCentered = true;
            },
            () => {},
            {
                enableHighAccuracy: true,
                maximumAge: 0,
                timeout: 5000,
            }
        );
    }

    if (!hasInitCoords) {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude.toFixed(6);
                const lon = pos.coords.longitude.toFixed(6);
                setMarker(lat, lon, true);
            },
            () => {
                // Fallback to Leaflet locate or default coordinates
                map.locate({ setView: true, maxZoom: 16, enableHighAccuracy: true, timeout: 5000 })
                    .on('locationfound', (e) => setMarker(e.latitude.toFixed(6), e.longitude.toFixed(6), true))
                    .on('locationerror', () => {
                        // Fallback a coordenadas específicas si no se encuentra la ubicación
                        const fallbackLat = -17.722934202854344;
                        const fallbackLon = -63.17431917469625;
                        setMarker(fallbackLat, fallbackLon, true);
                    });
            },
            { enableHighAccuracy: true, timeout: 7000 }
        );
      } else {
        map.locate({ setView: true, maxZoom: 16, enableHighAccuracy: true, timeout: 5000 })
          .on('locationfound', (e) => setMarker(e.latitude.toFixed(6), e.longitude.toFixed(6), true))
          .on('locationerror', () => {
            // Fallback a coordenadas específicas si no se encuentra la ubicación
            const fallbackLat = -17.722934202854344;
            const fallbackLon = -63.17431917469625;
            setMarker(fallbackLat, fallbackLon, true);
          });
      }
    }

    map.on('click', (e) => setMarker(e.latlng.lat.toFixed(6), e.latlng.lng.toFixed(6), true, true));
    return map;
}

// Simple, read-only map initializer for show views
window.initStaticMap = function initStaticMap(opts) {
    const {
        mapId,
        lat,
        lon,
        zoom = 15,
        popup = null,
        start = { lat: -17.7833, lon: -63.1821, zoom: 13 },
    } = opts || {};
    const mapEl = document.getElementById(mapId);
    if (!mapEl) return null;
    const hasCoords = typeof lat === 'number' && typeof lon === 'number' && !Number.isNaN(lat) && !Number.isNaN(lon);
    const map = L.map(mapId).setView(
        hasCoords ? [lat, lon] : [start.lat, start.lon],
        hasCoords ? zoom : start.zoom
    );
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
    if (hasCoords) {
        const marker = L.marker([lat, lon]).addTo(map);
        if (popup) marker.bindPopup(popup).openPopup();
    }
    return map;
}
</script>
@endonce

