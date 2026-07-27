<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ad = $_POST["ad"];
    $email = $_POST["email"];
    $mesaj = $_POST["mesaj"];

    $to = "alikodal2005@gmail.com"; // BURAYI DEĞİŞTİR
    $subject = "Web Sitesinden Yeni Mesaj";

    $body = "Ad: $ad\n";
    $body .= "Email: $email\n\n";
    $body .= "Mesaj:\n$mesaj";

    $headers = "From: $email";

    if (mail($to, $subject, $body, $headers)) {
        echo "Mesajınız gönderildi!";
    } else {
        echo "Gönderim başarısız.";
    }
}

?>