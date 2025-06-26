<?php
include 'koneksi.php';
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
</head>
<body class="bg-light">
    <div class="container mt-4">
  <h3>Update Status dan Tambah Lokasi Hydrant</h3>
  <form id="combinedForm">
    <div class="mb-3">
      <label for="hydrant_id" class="form-label">Pilih Hydrant</label>
      <select id="hydrant_id" name="id" class="form-select" required>
        <option value="001">001</option>
        <option value="002">002</option>
        <option value="003">003</option>
        <option value="004">004</option>
      </select>
    </div>
    <div class="mb-3">
      <label for="status" class="form-label">Status</label>
      <select id="status" name="status" class="form-select" required>
        <option value="Layak">Layak</option>
        <option value="Perawatan Ringan">Perawatan Ringan</option>
        <option value="Perawatan Berat">Perawatan Berat</option>
        <option value="Tidak Layak">Tidak Layak</option>
      </select>
    </div>
    <hr>
    <div class="mb-3">
      <label for="nama" class="form-label">Nama Hydrant</label>
      <input type="text" id="nama" name="nama" class="form-control" />
    </div>
    <div class="mb-3">
      <label for="latitude" class="form-label">Latitude</label>
      <input type="number" step="any" id="latitude" name="latitude" class="form-control" />
    </div>
    <div class="mb-3">
      <label for="longitude" class="form-label">Longitude</label>
      <input type="number" step="any" id="longitude" name="longitude" class="form-control" />
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
    <div id="msg" class="mt-2 text-success"></div>
    <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
  </form>
</div>

<script>
  // Ambil list hydrant
  fetch("php/get_hydrant.php")
    .then(res => res.json())
    .then(data => {
      const select = document.getElementById("hydrant_id");
      data.forEach(h => {
        const option = document.createElement("option");
        option.value = h.id;
        option.textContent = `${h.nama} (${h.status})`;
        select.appendChild(option);
      });
    });

  // Gabungkan update status dan tambah hydrant
  document.getElementById("combinedForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const form = new FormData(this);

    // Jika field nama, latitude, dan longitude diisi, lakukan tambah hydrant
    const nama = form.get("nama");
    const latitude = form.get("latitude");
    const longitude = form.get("longitude");

    if (nama && latitude && longitude) {
      fetch("php/add_hydrant.php", {
        method: "POST",
        body: form
      })
      .then(res => res.text())
      .then(response => {
        document.getElementById("msg").textContent = "Hydrant berhasil ditambahkan!";

        // Reset form
        document.getElementById("combinedForm").reset();

        // Update dropdown hydrant_id
        fetch("php/get_hydrant.php")
          .then(res => res.json())
          .then(data => {
            const select = document.getElementById("hydrant_id");
            select.innerHTML = "";
            data.forEach(h => {
              const option = document.createElement("option");
              option.value = h.id;
              option.textContent = `${h.nama} (${h.status})`;
              select.appendChild(option);
            });
          });
      });
    } else {
      // Jika tidak ada data hydrant baru, lakukan update status
      fetch("php/update_status.php", {
        method: "POST",
        body: form
      })
      .then(res => res.text())
      .then(response => {
        document.getElementById("msg").textContent = "Status berhasil diperbarui!";

        // Reset form
        document.getElementById("combinedForm").reset();

        // Update dropdown hydrant_id
        fetch("php/get_hydrant.php")
          .then(res => res.json())
          .then(data => {
            const select = document.getElementById("hydrant_id");
            select.innerHTML = "";
            data.forEach(h => {
              const option = document.createElement("option");
              option.value = h.id;
              option.textContent = `${h.nama} (${h.status})`;
              select.appendChild(option);
            });
          });
      });
    }
  });
</script>
</body>
</html>
