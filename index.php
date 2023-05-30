<?php
session_start();

// Koneksi ke database
$conn = mysqli_connect('localhost', 'root', '', 'login_system');
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Cek jika users sudah login, maka akan diarahkan ke halaman dashboard
if (isset($_SESSION['username'])) {
    header("location: dashboard.php");
    exit;
}

// Proses login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk memeriksa username
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        // Memeriksa password dengan fungsi password_verify()
        if (password_verify($password, $row['password'])) {
            // Jika login berhasil, simpan username dalam session
            $_SESSION['username'] = $username;
            header("location: dashboard.php");
            exit;
        } else {
            $error = "Password salah";
        }
    } else {
        $error = "Username tidak ditemukan";
    }
}

// Proses registrasi
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk memeriksa apakah username sudah digunakan
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $error = "Username sudah digunakan";
    } else {
        // Enkripsi password sebelum disimpan ke database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Query untuk menyimpan akun baru ke dalam tabel users
        $query = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $success = "Registrasi berhasil. Silakan login.";
        } else {
            $error = "Registrasi gagal";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="container bg-gradient-to-tr from-sky-300 from-40% to-yellow-400 w-full min-h-screen flex justify-center items-center text-white">
        <div class="w-3/4 md:w-80 h-fit bg-slate-600 px-1 py-5 text-center rounded-md">
            <h2 class="text-2xl font-bold text-teal-600">Selamat Datang !</h2>
            <?php if (isset($error)) { ?>
                <p><?php echo $error; ?></p>
            <?php } ?>
            <?php if (isset($success)) { ?>
                <p><?php echo $success; ?></p>
            <?php } ?>
            <form action="" method="post">
                <label for="username" class="text-lg font-semibold">Username :</label><br>
                <input type="text" id="username" name="username" class="rounded-lg p-2 text-black" required>
                <br>
                <label for="password" class="text-lg font-semibold">Password :</label><br>
                <input type="password" id="password" name="password" class="rounded-lg p-2 mb-5 text-black" required>
                <br>
                <input type="submit" name="register" value="Register" class="p-2 text-black bg-sky-400 font-semibold hover:bg-sky-800 hover:text-white rounded-md transition">
                <input type="submit" name="login" value="Login" class="p-2 text-black bg-sky-400 font-semibold hover:bg-sky-800 hover:text-white rounded-md transition">
            </form>
        </div>
    </div>
</body>

</html>