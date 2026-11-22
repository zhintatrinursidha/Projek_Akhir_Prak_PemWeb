const formRegister = document.getElementById('formRegister');
if(formRegister) {
    formRegister.addEventListener('submit', function(e) {
        const f = formRegister;
        const pesan = [];

        if (semuaKosong) {
            pesan.push('Semua kolom wajib diisi.');
        } else {
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.email.value)) {
                pesan.push('Format email tidak valid.');
            }

            if (f.password.value.length < 8) {
                pesan.push('Password minimal 8 karakter.');
            }

            if (f.password.value !== f.konfirmasi.value) {
                pesan.push('Konfirmasi password tidak cocok.');
            }
        }
        if (pesan.length > 0) {
            e.preventDefault();
            const kotak = document.getElementById('pesanValidasi');
            kotak.innerHTML = '<div class="alert alert-danger">' + pesan.join('<br>') + '</div>';
        }
    });
}

const togglePassword = document.getElementById('togglePassword');
if (togglePassword) {
    togglePassword.addEventListener('click', function () {
        const input = document.getElementById('password');

        if (input.type === 'password') {
            input.type = 'text';
            this.textContent = 'Sembunyikan';
        } else {
            input.type = 'password';
            this.textContent = 'Lihat';
        }
    });
}

let halamanSekarang = window.location.pathname;
if (halamanSekarang.endsWith('/')) {
    halamanSekarang += 'index.php';
}

document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
    if (link.pathname === halamanSekarang) {
        link.classList.add('active');
    }
});

const modalKategori = document.getElementById('modalKategori');
if (modalKategori) {
    modalKategori.addEventListener('show.bs.modal', function (event) {
        // tombol yang membuka modal ini
        const tombol = event.relatedTarget;
        const id = tombol.getAttribute('data-id');
        const nama = tombol.getAttribute('data-nama');

        const judul = document.getElementById('judulModal');
        const inputId = document.getElementById('idKategori');
        const inputNama = document.getElementById('namaKategori');

        if (id) {
            judul.textContent = 'Ubah Kategori';
            inputId.value = id;
            inputNama.value = nama;
        } else {
            judul.textContent = 'Tambah Kategori';
            inputId.value = '';
            inputNama.value = '';
        }
    });
}

const formHapus = document.querySelectorAll('.form-hapus');
formHapus.forEach(function (form) {
    form.addEventListener('submit', function (e) {
        const nama = form.getAttribute('data-nama');
        const yakin = confirm('Hapus kategori "' + nama + '"?\nBuku di kategori ini tidak ikut terhapus, tapi jadi tanpa kategori.');

        if (!yakin) {
            e.preventDefault();
        }
    });
});

const inputCover = document.getElementById('inputCover');
if (inputCover) {
    inputCover.addEventListener('change', function () {
        const file = inputCover.files[0];
        const preview = document.getElementById('previewCover');

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
}

document.querySelectorAll('.form-konfirmasi').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        if (!confirm(form.getAttribute('data-pesan'))) {
            e.preventDefault();
        }
    });
});


const cariBuku = document.getElementById('cariBuku');
if (cariBuku) {
    cariBuku.addEventListener('input', function () {
        const kata = cariBuku.value.toLowerCase().trim();
        const semuaBuku = document.querySelectorAll('.item-buku');
        let jumlahTampil = 0;

        semuaBuku.forEach(function (item) {
            const teks = item.getAttribute('data-cari');

            if (teks.includes(kata)) {
                item.style.display = '';
                jumlahTampil++;
            } else {
                item.style.display = 'none';
            }
        });

        document.getElementById('kosongBuku').style.display = jumlahTampil == 0 ? '' : 'none';
    });
}

document.querySelectorAll('.input-bukti').forEach(function (input) {
    input.addEventListener('change', function () {
        const file = input.files[0];
        const preview = document.getElementById(input.getAttribute('data-target'));

        if (file && preview) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
});

const modalTolak = document.getElementById('modalTolak');
if (modalTolak) {
    modalTolak.addEventListener('show.bs.modal', function (event) {
        const tombol = event.relatedTarget;
        document.getElementById('idPembayaran').value = tombol.getAttribute('data-id');
        modalTolak.querySelector('textarea').value = '';
    });
}

const tombolCetak = document.getElementById('tombolCetak');
if (tombolCetak) {
    tombolCetak.addEventListener('click', function () {
        window.print();
    });
}

const formLogin = document.getElementById('formLogin');
if (formLogin) {
    formLogin.addEventListener('submit', function (e) {
        const email = formLogin.email.value.trim();
        const password = formLogin.password.value;

        if (email === '' || password === '') {
            e.preventDefault();
            document.getElementById('pesanValidasi').innerHTML ='<div class="alert alert-danger">Email dan password wajib diisi.</div>';
        }
    });
}

document.querySelectorAll('input[type="file"]').forEach(function (input) {
    input.addEventListener('change', function () {
        const file = input.files[0];
        if (!file) {
            return;
        }

        const jenisBoleh = ['image/jpeg', 'image/png'];
        if (!jenisBoleh.includes(file.type)) {
            alert('File harus berupa gambar JPG atau PNG.');
            input.value = '';
        } else if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2 MB.');
            input.value = '';
        }
    });
});