// ===============================
// SHOWROOM MOBIL - MAIN JAVASCRIPT
// ===============================

console.log("JavaScript showroom mobil berhasil terhubung!");

// ===============================
// PREVIEW GAMBAR UPLOAD
// ===============================

const imageInput = document.querySelector('#gambar');
const previewImage = document.querySelector('#preview-gambar');

if (imageInput && previewImage) {

    imageInput.addEventListener('change', function () {

        const file = this.files[0];

        if (file) {

            const reader = new FileReader();

            reader.onload = function (e) {

                previewImage.src = e.target.result;
                previewImage.style.display = 'block';

            }

            reader.readAsDataURL(file);

        }

    });

}

// ===============================
// KONFIRMASI HAPUS DATA
// ===============================

const deleteButtons = document.querySelectorAll('.btn-hapus');

if (deleteButtons.length > 0) {

    deleteButtons.forEach(button => {

        button.addEventListener('click', function (e) {

            const konfirmasi = confirm('Yakin ingin menghapus data ini?');

            if (!konfirmasi) {

                e.preventDefault();

            }

        });

    });

}

// ===============================
// ALERT AUTO HILANG
// ===============================

const alerts = document.querySelectorAll('.alert-success, .alert-error');

if (alerts.length > 0) {

    setTimeout(() => {

        alerts.forEach(alert => {

            alert.style.transition = '0.5s';
            alert.style.opacity = '0';

            setTimeout(() => {

                alert.style.display = 'none';

            }, 500);

        });

    }, 3000);

}

// ===============================
// SEARCH REALTIME TABLE
// ===============================

const searchInput = document.querySelector('#search');
const tableRows = document.querySelectorAll('table tbody tr');

if (searchInput && tableRows.length > 0) {

    searchInput.addEventListener('keyup', function () {

        const keyword = this.value.toLowerCase();

        tableRows.forEach(row => {

            const text = row.textContent.toLowerCase();

            if (text.includes(keyword)) {

                row.style.display = '';

            } else {

                row.style.display = 'none';

            }

        });

    });

}

// ===============================
// ANIMASI CARD
// ===============================

const cards = document.querySelectorAll('.menu-card');

if (cards.length > 0) {

    cards.forEach((card, index) => {

        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';

        setTimeout(() => {

            card.style.transition = '0.5s';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';

        }, index * 150);

    });

}

// ===============================
// VALIDASI FORM
// ===============================

const forms = document.querySelectorAll('form');

if (forms.length > 0) {

    forms.forEach(form => {

        form.addEventListener('submit', function (e) {

            const requiredInputs = form.querySelectorAll('input[required], textarea[required], select[required]');

            let valid = true;

            requiredInputs.forEach(input => {

                if (input.value.trim() === '') {

                    valid = false;
                    input.style.border = '1px solid red';

                } else {

                    input.style.border = '1px solid #ccc';

                }

            });

            if (!valid) {

                e.preventDefault();
                alert('Semua field wajib diisi!');

            }

        });

    });

}

// ===============================
// TOMBOL SCROLL KE ATAS
// ===============================

const scrollButton = document.createElement('button');

scrollButton.innerHTML = '↑';
scrollButton.id = 'scrollTopBtn';

document.body.appendChild(scrollButton);

scrollButton.style.position = 'fixed';
scrollButton.style.bottom = '20px';
scrollButton.style.right = '20px';
scrollButton.style.padding = '12px 16px';
scrollButton.style.border = 'none';
scrollButton.style.borderRadius = '12px';
scrollButton.style.cursor = 'pointer';
scrollButton.style.background = '#2563eb';
scrollButton.style.color = 'white';
scrollButton.style.fontSize = '18px';
scrollButton.style.display = 'none';
scrollButton.style.zIndex = '999';

window.addEventListener('scroll', function () {

    if (window.scrollY > 200) {

        scrollButton.style.display = 'block';

    } else {

        scrollButton.style.display = 'none';

    }

});

scrollButton.addEventListener('click', function () {

    window.scrollTo({

        top: 0,
        behavior: 'smooth'

    });

});

// ===============================
// JAM REALTIME
// ===============================

const jamElement = document.querySelector('#jam');

if (jamElement) {

    setInterval(() => {

        const sekarang = new Date();

        const jam = String(sekarang.getHours()).padStart(2, '0');
        const menit = String(sekarang.getMinutes()).padStart(2, '0');
        const detik = String(sekarang.getSeconds()).padStart(2, '0');

        jamElement.innerHTML = `${jam}:${menit}:${detik}`;

    }, 1000);

}