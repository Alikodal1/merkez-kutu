<?php
session_start();

// Eğer zaten giriş yapmışsa direkt panele yönlendir
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: panel.php");
    exit;
}

$hata = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kullanici = $_POST['kullanici'] ?? '';
    $sifre = $_POST['sifre'] ?? '';

    // Kullanıcı adı ve şifre (İstediğin gibi değiştirebilirsin)
    $dogru_kullanici = "admin";
    $dogru_sifre = "merkez123";

    if ($kullanici === $dogru_kullanici && $sifre === $dogru_sifre) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: panel.php");
        exit;
    } else {
        $hata = "Kullanıcı adı veya şifre hatalı!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Girişi | Merkez Kutu</title>
    <link rel="stylesheet" href="../global.css">
    <style>
        body {
            background-color: #f2f4f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        .login-card {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            border: 1px solid #e2e8f0;
        }
        .login-card h2 {
            margin-bottom: 25px;
            font-size: 24px;
            color: #111;
            text-align: center;
        }
        .login-card input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 1px solid #dcdcdc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
        }
        .login-card input:focus {
            border-color: #a11b1b;
            box-shadow: 0 0 0 3px rgba(161, 27, 27, 0.1);
        }
        .login-card button {
            width: 100%;
            background-color: #a11b1b;
            color: #ffffff;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .login-card button:hover {
            background-color: #d90429;
        }
        .error-msg {
            color: #d90429;
            font-size: 13px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Admin Girişi</h2>
        <?php if (!empty($hata)): ?>
            <div class="error-msg"><?php echo $hata; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="kullanici" placeholder="Kullanıcı Adı" required>
            <input type="password" name="sifre" placeholder="Şifre" required>
            <button type="submit">Giriş Yap</button>
        </form>
    </div>

</body>
</html>