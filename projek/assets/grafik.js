fetch('api/grafik.php')
    .then(function (res) {
        if (!res.ok) {
            throw new Error('Gagal memuat data grafik');
        }
        return res.json();
    })
    .then(function (data) {
        new Chart(document.getElementById('grafikKunjungan'), {
            type: 'line',
            data: {
                labels: data.kunjungan.label,
                datasets: [{
                    label: 'Kunjungan',
                    data: data.kunjungan.data,
                    borderColor: '#1e5aa8',
                    backgroundColor: 'rgba(30, 90, 168, 0.15)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        new Chart(document.getElementById('grafikTerlaris'), {
            type: 'bar',
            data: {
                labels: data.terlaris.label,
                datasets: [{
                    label: 'Jumlah dipinjam',
                    data: data.terlaris.data,
                    backgroundColor: '#1e5aa8'
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        const warna = {
            menunggu: '#ffc107',
            dipinjam: '#0d6efd',
            dikembalikan: '#198754',
            ditolak: '#dc3545'
        };

        new Chart(document.getElementById('grafikStatus'), {
            type: 'doughnut',
            data: {
                labels: data.status.label,
                datasets: [{
                    data: data.status.data,
                    backgroundColor: data.status.label.map(function (s) {
                        return warna[s];
                    })
                }]
            }
        });
    })
    .catch(function (err) {
        document.getElementById('pesanGrafik').textContent = err.message;
    });