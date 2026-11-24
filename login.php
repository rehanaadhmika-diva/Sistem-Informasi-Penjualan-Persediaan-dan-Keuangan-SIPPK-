<?php
session_start();
require __DIR__ . "/config/koneksi.php";

$error = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Email dan kata sandi wajib diisi.";
    } else {
        $stmt = $pdo->prepare("SELECT id_pengguna, nama, password_hash, peran FROM pengguna WHERE email = ?");
        $stmt->execute([$email]);
        $pengguna = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pengguna && password_verify($password, $pengguna["password_hash"])) {
            session_regenerate_id(true);
            $_SESSION["id_pengguna"] = $pengguna["id_pengguna"];
            $_SESSION["nama"] = $pengguna["nama"];
            $_SESSION["peran"] = $pengguna["peran"];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Email atau kata sandi salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIPPK Sari Bunga</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: #eef5ee;
        }
        .card {
            width: 100%;
            max-width: 380px;
            padding: 32px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        h1 { margin: 0 0 4px; font-size: 22px; color: #2e7d32; }
        p.sub { margin: 0 0 20px; color: #666; font-size: 14px; }
        label { display: block; margin-bottom: 6px; font-size: 14px; }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 11px;
            border: none;
            border-radius: 6px;
            background: #2e7d32;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
        }
        button:hover { background: #276a2b; }
        .error {
            margin-bottom: 16px;
            padding: 10px;
            background: #fdecea;
            color: #b71c1c;
            border-radius: 6px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>SIPPK Sari Bunga</h1>
        <p class="sub">Masuk untuk melanjutkan</p>

        <?php if ($error !== ""): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="password">Kata sandi</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
