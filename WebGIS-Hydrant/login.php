<?php
include 'koneksi.php';

?>

<!-- login.html -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="text-center">Login for Update</h4>
            <form action="" method="post">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required />
              </div>
              <button type="submit" name ="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <a href="index.php" class="btn btn-secondary w-100 mt-2">Back</a>
            <div id="loginMsg" class="mt-2 text-center">
            <?php
             if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $user = mysqli_real_escape_string($conn, $_POST['username']);
                    $pass = mysqli_real_escape_string($conn, $_POST['password']);
                    
                    // Query untuk memverifikasi username dan password
                    $cek = mysqli_query($conn, "SELECT * FROM user WHERE username = '$user'");
                    if (mysqli_num_rows($cek) > 0) {
                        $d = mysqli_fetch_object($cek);
                        // Verify password using password_verify if hashed, else plain compare
                        if ($pass === $d->password) {
                            // Jika login berhasil, simpan nama dan status login dalam sesi
                            $_SESSION['id'] = $d->id;
                            $_SESSION['username'] = $d->username; // Menyimpan nama pengguna ke dalam sesi
                
                            header('Location: dashboard.php');  
                            exit();
                        } else {
                            echo '<div class="alert alert-danger">Password Salah</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger">Username tidak ditemukan</div>';
                    }
                }
            
            ?>
            </div>
