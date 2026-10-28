<?php
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Basit doğrulama - boş veya geçersiz e-posta gelirse durdur
    $ad = trim($_POST["ad"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mesaj = trim($_POST["mesaj"] ?? "");

    if (!$ad || !$mesaj || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Lütfen tüm alanları doğru şekilde doldurun.";
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // ── SUNUCU AYARLARI ── // BURAYI DEĞİŞTİR
        $mail->isSMTP();
        $mail->Host       = 'baris.kebirhost.com';      // sertifikanın CN'iyle eşleşen gerçek sunucu adı
        $mail->SMTPAuth   = true;
        $mail->Username   = 'merkezkutu@merkezkutu.com'; // gönderici kutusu
        $mail->Password   = 'f2lhfwtr8h';       // BURAYI DEĞİŞTİR - webmail şifren
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;                         // 465 kullanıyorsan ENCRYPTION_SMTPS yap
        $mail->CharSet    = 'UTF-8';

        // ── ALICI / GÖNDERİCİ ──
        $mail->setFrom('merkezkutu@merkezkutu.com', 'Merkez Kutu Web Sitesi');
        $mail->addAddress('merkezkutu@merkezkutu.com');
        $mail->addReplyTo($email, $ad);

        // ── İÇERİK ──
        $mail->isHTML(false);
        $mail->Subject = "Web Sitesinden Yeni Mesaj";
        $mail->Body    = "Ad: $ad\nEmail: $email\n\nMesaj:\n$mesaj";

        $mail->send();
        echo "Mesajınız gönderildi!";
    } catch (Exception $e) {
        // Geliştirme aşamasında gerçek hatayı görmek için:
        echo "Gönderim başarısız. Hata: {$mail->ErrorInfo}";
    }
}