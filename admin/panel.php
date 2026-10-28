<?php
session_start();

// Güvenlik kontrolü: Giriş yapılmadıysa login sayfasına yönlendir
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: giris.php");
    exit;
}

$json_dosya = '../products.json';
$mesaj = '';

// JSON dosyası yoksa boş bir array ile oluştur
if (!file_exists($json_dosya)) {
    file_put_contents($json_dosya, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// ÜRÜN EKLEME İŞLEMİ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ekle') {
    $baslik = trim($_POST['baslik'] ?? '');
    $aciklama = trim($_POST['aciklama'] ?? '');
    $olcu = trim($_POST['olcu'] ?? '');

    if (!empty($baslik) && isset($_FILES['gorsel']) && $_FILES['gorsel']['error'] === 0) {
        $upload_dir = '../images/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $dosya_adi = uniqid('kutu_') . '.' . strtolower(pathinfo($_FILES['gorsel']['name'], PATHINFO_EXTENSION));
        $hedef_yol = $upload_dir . $dosya_adi;
        $db_yol = 'images/uploads/' . $dosya_adi; // Sitede gösterilecek yol

        $izinli_uzantilar = ['jpg', 'jpeg', 'png', 'webp'];
        $uzanti = strtolower(pathinfo($hedef_yol, PATHINFO_EXTENSION));

        if (in_array($uzanti, $izinli_uzantilar)) {
            if (move_uploaded_file($_FILES['gorsel']['tmp_name'], $hedef_yol)) {
                // JSON verisini oku
                $urunler = json_decode(file_get_contents($json_dosya), true);

                // Yeni ürünü ekle
                $yeni_urun = [
                    'id' => time(),
                    'baslik' => $baslik,
                    'aciklama' => $aciklama,
                    'olcu' => $olcu,
                    'gorsel' => $db_yol
                ];

                array_unshift($urunler, $yeni_urun); // En üste ekle
                file_put_contents($json_dosya, json_encode($urunler, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                $mesaj = "Kutu başarıyla eklendi!";
            } else {
                $mesaj = "Görsel yüklenirken hata oluştu!";
            }
        } else {
            $mesaj = "Geçersiz dosya formatı! Sadece JPG, PNG, WEBP yükleyebilirsiniz.";
        }
    } else {
        $mesaj = "Lütfen başlık doldurun ve bir görsel seçin!";
    }
}

// ÜRÜN SİLME İŞLEMİ
if (isset($_GET['sil'])) {
    $sil_id = intval($_GET['sil']);
    $urunler = json_decode(file_get_contents($json_dosya), true);

    foreach ($urunler as $key => $urun) {
        if ($urun['id'] === $sil_id) {
            // Sunucudaki görseli de sil
            if (file_exists('../' . $urun['gorsel'])) {
                unlink('../' . $urun['gorsel']);
            }
            unset($urunler[$key]);
            break;
        }
    }

    file_put_contents($json_dosya, json_encode(array_values($urunler), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: panel.php");
    exit;
}

$urunler = json_decode(file_get_contents($json_dosya), true);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli | Merkez Kutu</title>
    <link rel="stylesheet" href="../global.css">
    <style>
        body { background-color: #f2f4f7; font-family: 'Poppins', sans-serif; margin: 0; padding: 20px; }
        .admin-container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .admin-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 25px; }
        .admin-header h2 { margin: 0; color: #111; }
        .logout-btn { background: #333; color: #fff; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; }
        .logout-btn:hover { background: #a11b1b; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #333; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 14px; box-sizing: border-box; }
        .submit-btn { background: #a11b1b; color: #fff; border: none; padding: 12px 20px; border-radius: 6px; font-weight: 700; cursor: pointer; }
        .submit-btn:hover { background: #d90429; }
        .msg { background: #e6fffa; color: #007055; padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
        .product-list { margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .product-item { display: flex; align-items: center; justify-content: space-between; background: #fafafa; padding: 10px 15px; border-radius: 6px; margin-bottom: 10px; border: 1px solid #eee; }
        .product-info { display: flex; align-items: center; gap: 15px; }
        .product-info img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
        .delete-btn { color: #d90429; text-decoration: none; font-weight: 600; font-size: 13px; }
        .delete-btn:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="admin-header">
        <h2>Karton Kutu Yönetim Paneli</h2>
        <a href="cikis.php" class="logout-btn">Çıkış Yap</a>
    </div>

    <?php if (!empty($mesaj)): ?>
        <div class="msg"><?php echo $mesaj; ?></div>
    <?php endif; ?>

    <!-- ÜRÜN EKLEME FORMU -->
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="ekle">
        <div class="form-group">
            <label>Kutu Başlığı / Modeli:</label>
            <input type="text" name="baslik" placeholder="Örn: Oluklu Mukavva Kutu" required>
        </div>
        <div class="form-group">
            <label>Ölçüler / Özellikler:</label>
            <input type="text" name="olcu" placeholder="Örn: 30x20x15 cm">
        </div>
        <div class="form-group">
            <label>Açıklama:</label>
            <textarea name="aciklama" rows="3" placeholder="Kutu hakkında kısa açıklama..."></textarea>
        </div>
        <div class="form-group">
            <label>Kutu Görseli:</label>
            <input type="file" name="gorsel" accept="image/*" required>
        </div>
        <button type="submit" class="submit-btn">Kutuyu Ekle</button>
    </form>

    <!-- MEVCUT ÜRÜNLER LİSTESİ -->
    <div class="product-list">
        <h3>Eklenmiş Kutular (<?php echo count($urunler); ?>)</h3>
        <?php if (empty($urunler)): ?>
            <p style="color: #777; font-size: 14px;">Henüz kutu eklenmemiş.</p>
        <?php else: ?>
            <?php foreach ($urunler as $urun): ?>
                <div class="product-item">
                    <div class="product-info">
                        <img src="../<?php echo htmlspecialchars($urun['gorsel']); ?>" alt="">
                        <div>
                            <strong><?php echo htmlspecialchars($urun['baslik']); ?></strong><br>
                            <small style="color: #666;"><?php echo htmlspecialchars($urun['olcu']); ?></small>
                        </div>
                    </div>
                    <a href="panel.php?sil=<?php echo $urun['id']; ?>" class="delete-btn" onclick="return confirm('Bu kutuyu silmek istediğinize emin misiniz?');">Sil</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>