import os
from PIL import Image

# Görsellerin bulunduğu tam klasör yolu
image_dir = os.path.join("images", "optimize")

dosya_adi = input("Dönüştürülecek PNG dosyasının adını yazın (optimize klasöründeki tümü için Enter'a basın): ").strip()

if dosya_adi:
    # Tek bir dosyayı dönüştürme
    png_yolu = os.path.join(image_dir, dosya_adi)
    if os.path.exists(png_yolu):
        webp_yolu = os.path.splitext(png_yolu)[0] + ".webp"
        with Image.open(png_yolu) as img:
            img.save(webp_yolu, "WEBP", quality=85)
        os.remove(png_yolu)
        print(f"{dosya_adi} başarıyla WebP formatına dönüştürüldü!")
    else:
        print("Dosya bulunamadı!")
else:
    # optimize klasöründeki tüm PNG'leri tek tek işleme
    if os.path.exists(image_dir):
        donusturulen = 0
        for root, dirs, files in os.walk(image_dir):
            for file in files:
                if file.lower().endswith(".png"):
                    p_yolu = os.path.join(root, file)
                    w_yolu = os.path.splitext(p_yolu)[0] + ".webp"
                    with Image.open(p_yolu) as img:
                        img.save(w_yolu, "WEBP", quality=85)
                    os.remove(p_yolu)
                    print(f"Dönüştürüldü: {file}")
                    donusturulen += 1
        print(f"\nİşlem tamamlandı! Toplam {donusturulen} adet PNG dosyası WebP'ye çevrildi.")
    else:
        print("images/optimize klasörü bulunamadı!")