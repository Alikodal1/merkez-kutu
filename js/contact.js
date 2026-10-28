document.addEventListener("DOMContentLoaded", () => {
    /* ── İletişim formu ── */
    const contactForm = document.getElementById("contact-form");

    if (contactForm) {
        const formMessage = document.getElementById("form-message");

        contactForm.addEventListener("submit", function (e) {
            e.preventDefault(); // sayfanın yenilenmesini/yönlenmesini engeller

            const formData = new FormData(contactForm);

            fetch("mail.php", {
                method: "POST",
                body: formData
            })
                .then(function (response) {
                    return response.text();
                })
                .then(function (result) {
                    formMessage.textContent = result;
                    formMessage.style.color = result.indexOf("başarısız") !== -1 ? "#d90429" : "#2e7d32";

                    if (result.indexOf("başarısız") === -1) {
                        contactForm.reset(); // başarılıysa formu temizle
                    }
                })
                .catch(function () {
                    formMessage.textContent = "Bir hata oluştu, lütfen tekrar deneyin.";
                    formMessage.style.color = "#d90429";
                });
        });
    }
});