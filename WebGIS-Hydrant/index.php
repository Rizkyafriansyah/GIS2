<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>WebGIS Hydrant Kota Medan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
  
  <style>
    #map {
      height: 100vh;
      width: 100%;
      position: relative;
      z-index: 1;
    }

    .btn-refresh {
      position: absolute;
      bottom: 20px;
      right: 30px;
      z-index: 1000;
      background: white;
      color: black;
      padding: 10px 14px;
      border-radius: 6px;
      font-weight: bold;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
      cursor: pointer;
    }

    .btn-locate {
      position: absolute;
      bottom: 20px;
      left: 220px;
      z-index: 1000;
      background: white;
      padding: 10px;
      border-radius: 5px;
      font-weight: bold;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
      cursor: pointer;
    }

    .btn-update {
      position: absolute;
      bottom: 20px;
      left: 30px;
      z-index: 1000;
      background: white;
      color: black;
      padding: 10px 14px;
      border-radius: 6px;
      font-weight: bold;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
      cursor: pointer;
    }

    /* RESPONSIVE UNTUK LAYAR KECIL */
    @media (max-width: 768px) {
      .btn-update,
      .btn-locate,
      .btn-refresh {
        bottom: auto;
        top: auto;
        position: fixed;
        left: 10px;
        right: 10px;
        display: block;
        margin: 5px auto;
        width: 90%;
        text-align: center;
      }
      .btn-refresh{
        bottom: 20px;
      }
      .btn-locate{
        bottom: 50px;
      }
      .btn-update{
        bottom: 80px;
      }
    }
  </style>
</head>
<body>

  <div id="map"></div>

  <div class="btn-refresh" onclick="confirmReload()" title="Muat Ulang Halaman">🔄 Refresh Peta</div>
  <script>
    function confirmReload() {
      if (confirm("Yakin ingin memuat ulang peta?")) {
        location.reload();
      }
    }
  </script>

  <div class="btn-update" onclick="window.location.href='login.php'" title="Update Hydrant">🔧 Update Hydrant</div> 
  <div class="btn-locate" onclick="findNearestHydrant()" title="Cari Hydrant Terdekat">🔍 Hydrant Terdekat</div>

  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.min.js"></script>

  <script>
    let map = L.map('map').setView([3.5952, 98.6722], 13); // Pusat kota Medan

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let hydrantData = [];
    let markers = [];
    let routingControl;

    // Ambil data hydrant dari server
    fetch("get_hydrant.php")
      .then(response => response.json())
      .then(data => {
        hydrantData = data;
        data.forEach(hydrant => {
          const marker = L.marker([hydrant.latitude, hydrant.longitude]).addTo(map);
          marker.bindPopup(`<b>${hydrant.nama}</b><br>Status: ${hydrant.status}`);
          markers.push({ ...hydrant, marker });
        });
      });

    // Fungsi cari hydrant terdekat dan navigasi dari posisi user
    function findNearestHydrant() {
      if (!navigator.geolocation) {
        alert("Geolocation tidak didukung browser ini.");
        return;
      }

      const locateBtn = document.querySelector('.btn-locate');
      locateBtn.textContent = 'Mencari...';
      locateBtn.style.pointerEvents = 'none';

      navigator.geolocation.getCurrentPosition(position => {
        const userLat = position.coords.latitude;
        const userLon = position.coords.longitude;

        let nearest = null;
        let minDistance = Infinity;

        hydrantData.forEach(h => {
          if (h.status === 'Layak') {
            const distance = getDistance(userLat, userLon, h.latitude, h.longitude);
            if (distance < minDistance) {
              minDistance = distance;
              nearest = h;
            }
          }
        });

        if (nearest) {
          if (routingControl) {
            map.removeControl(routingControl);
          }

          routingControl = L.Routing.control({
            waypoints: [
              L.latLng(userLat, userLon),
              L.latLng(nearest.latitude, nearest.longitude)
            ],
            routeWhileDragging: true,
            showAlternatives: true,
            altLineOptions: {
              styles: [
                {color: 'black', opacity: 0.15, weight: 9},
                {color: 'white', opacity: 0.8, weight: 6},
                {color: 'blue', opacity: 0.5, weight: 2}
              ]
            },
            router: L.Routing.osrmv1({
              language: 'id',
              profile: 'car'
            }),
            collapsible: true,
            autoRoute: true,
            fitSelectedRoutes: true,
            containerClassName: 'routing-container'
          }).addTo(map);

          map.fitBounds([
            [userLat, userLon],
            [nearest.latitude, nearest.longitude]
          ]);
        }

        locateBtn.textContent = '🔍 Cari Hydrant Terdekat';
        locateBtn.style.pointerEvents = 'auto';

      }, error => {
        console.log('Geolocation error:', error);
        alert("Gagal mendapatkan lokasi perangkat.");
        locateBtn.textContent = '🔍 Cari Hydrant Terdekat';
        locateBtn.style.pointerEvents = 'auto';
      }, {timeout: 10000});
    }

    // Fungsi hitung jarak (haversine)
    function getDistance(lat1, lon1, lat2, lon2) {
      function toRad(x) { return x * Math.PI / 180; }
      const R = 6371;
      const dLat = toRad(lat2 - lat1);
      const dLon = toRad(lon2 - lon1);
      const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c;
    }
  </script>
</body>
</html>
