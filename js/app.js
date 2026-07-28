document.addEventListener("DOMContentLoaded", () => {
  const slides = document.querySelector(".slides");
  const items = document.querySelectorAll(".slide");

  let index = 0;

  document.querySelector(".next").addEventListener("click", () => {
    index++;
    if (index >= items.length) index = 0;
    update();
  });

  document.querySelector(".prev").addEventListener("click", () => {
    index--;
    if (index < 0) index = items.length - 1;
    update();
  });

  function update() {
    // Yüzde (%) yerine vw (ekran genişliği) kullanıyoruz. 
    // Bu sayede kapsayıcı ne kadar geniş olursa olsun tam 1 ekran kayar.
    slides.style.transform = `translateX(-${index * 100}vw)`;
  }
});

const menuToggle = document.getElementById("menu-toggle");
const navLinks = document.querySelector("nav-links");

menuToggle.addEventListener("click", () => {
  navLinks.classList.toggle("active");
});



/* ============================================================
   baski-hizmetleri.js
   Merkez Kutu – Baskı Hizmetleri Sayfası
   ============================================================ */

/* ── Video oynatma / durdurma ── */
function togglePlay(thumb) {
  const video = thumb.querySelector('video');

  if (thumb.classList.contains('playing')) {
    video.pause();
    thumb.classList.remove('playing');
  } else {
    /* Diğer tüm videoları durdur */
    document.querySelectorAll('.video-thumb.playing').forEach(function (t) {
      t.querySelector('video').pause();
      t.classList.remove('playing');
    });

    video.play().catch(function () { });
    thumb.classList.add('playing');
  }
}

/* Video bitince play overlay geri gelsin */
document.querySelectorAll('video').forEach(function (v) {
  v.addEventListener('ended', function () {
    v.closest('.video-thumb').classList.remove('playing');
  });
});

/* ── Kategori filtreleme ── */
var filterBtns = document.querySelectorAll('.filter-btn');
var cards = document.querySelectorAll('.video-card');
var counter = document.getElementById('visible-count');
var noResults = document.getElementById('no-results');

filterBtns.forEach(function (btn) {
  btn.addEventListener('click', function () {

    /* Aktif butonu güncelle */
    filterBtns.forEach(function (b) { b.classList.remove('active'); });
    btn.classList.add('active');

    var filter = btn.dataset.filter;
    var visible = 0;

    /* Kartları göster / gizle */
    cards.forEach(function (card) {
      var show = filter === 'all' || card.dataset.cat === filter;
      card.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    /* Sayacı ve "sonuç yok" mesajını güncelle */
    counter.textContent = visible;
    noResults.style.display = visible === 0 ? 'block' : 'none';
  });
});
