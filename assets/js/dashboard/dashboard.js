/**
 * JavaScript untuk Dashboard
 * Menangani interaksi, chart, dan shortcut pada halaman dashboard
 */

// Base URL dari aplikasi
const base_url = window.location.origin + "/prestasi/";

// Global variables
let isRefreshing = false;
let chartBulan = null;
let chartMapelInstance = null;

// =============================================
// CHART INITIALIZATION
// =============================================

/**
 * Inisialisasi chart tren jurnal per bulan (line chart)
 */
function initChartJurnalBulan() {
    const ctx = document.getElementById('chartJurnalBulan');
    if (!ctx) return;

    if (chartBulan) {
        chartBulan.destroy();
    }

    chartBulan = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartBulanLabels,
            datasets: [{
                label: 'Jumlah Jurnal',
                data: chartBulanData,
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#22c55e',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#f9fafb',
                    bodyColor: '#d1fae5',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(ctx) {
                            return ' ' + ctx.parsed.y + ' jurnal';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 10 },
                        color: '#9ca3af',
                        maxRotation: 45,
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.04)',
                        drawBorder: false,
                    },
                    ticks: {
                        font: { size: 10 },
                        color: '#9ca3af',
                        stepSize: 1,
                        precision: 0,
                    }
                }
            }
        }
    });
}

/**
 * Inisialisasi chart distribusi jurnal per mapel (doughnut chart)
 */
function initChartMapel() {
    const ctx = document.getElementById('chartMapel');
    if (!ctx) return;
    if (!chartMapelLabels || chartMapelLabels.length === 0) return;

    if (chartMapelInstance) {
        chartMapelInstance.destroy();
    }

    const colors = [
        '#22c55e', '#3b82f6', '#f59e0b', '#8b5cf6',
        '#ef4444', '#06b6d4', '#f97316', '#84cc16'
    ];

    chartMapelInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: chartMapelLabels,
            datasets: [{
                data: chartMapelData,
                backgroundColor: colors.slice(0, chartMapelLabels.length),
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 10 },
                        color: '#6b7280',
                        padding: 8,
                        usePointStyle: true,
                        pointStyleWidth: 8,
                    }
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#f9fafb',
                    bodyColor: '#d1fae5',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? Math.round((ctx.parsed / total) * 100) : 0;
                            return ' ' + ctx.parsed + ' jurnal (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
}

// =============================================
// COUNTER ANIMATION
// =============================================

/**
 * Fungsi untuk inisialisasi animasi counter
 */
function initCounters() {
    $(".counter").each(function () {
        const target = parseInt($(this).data("target")) || 0;
        if (target === 0) {
            $(this).text(0);
            return;
        }
        const duration = 1500;
        const step = target / (duration / 16);
        let current = 0;
        const el = $(this);

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.text(Math.floor(current));
        }, 16);
    });
}

/**
 * Fungsi untuk animasi counter saat refresh
 */
function animateCounter(element, target) {
    const duration = 1000;
    const step = Math.max(target / (duration / 16), 1);
    let current = parseInt(element.text()) || 0;

    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.text(Math.floor(current));
    }, 16);
}

// =============================================
// DATETIME DISPLAY
// =============================================

/**
 * Update tampilan tanggal dan waktu real-time
 */
function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: 'long', year: 'numeric', month: 'long',
        day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit'
    };
    const formatted = now.toLocaleDateString('id-ID', options);
    $('#currentDateTime').text(formatted);
}

// =============================================
// REFRESH DASHBOARD
// =============================================

/**
 * Fungsi untuk refresh data dashboard dengan Ajax
 */
function refreshDashboard() {
    if (isRefreshing) return;
    isRefreshing = true;

    // Tampilkan loading pada tombol refresh
    $("#refreshBtn i").addClass("fa-spin");
    $(".stat-card").addClass("opacity-60");

    $.ajax({
        url: base_url + "dashboard/get_stats",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                const d = response.data;

                // Update stat counters
                updateStatValue('[data-target]', d.ringkasan);

                // Update charts
                if (d.jurnal_per_bulan && chartBulan) {
                    chartBulan.data.labels = d.jurnal_per_bulan.map(x => x.label);
                    chartBulan.data.datasets[0].data = d.jurnal_per_bulan.map(x => x.count);
                    chartBulan.update('active');
                }

                if (d.jurnal_per_mapel && chartMapelInstance) {
                    chartMapelInstance.data.labels = d.jurnal_per_mapel.map(x => x.nama_mapel);
                    chartMapelInstance.data.datasets[0].data = d.jurnal_per_mapel.map(x => x.total);
                    chartMapelInstance.update('active');
                }

                showNotification("Data berhasil diperbarui", "success");
            } else {
                showNotification("Gagal memperbarui data", "error");
            }
        },
        error: function () {
            showNotification("Terjadi kesalahan saat memperbarui data", "error");
        },
        complete: function () {
            $("#refreshBtn i").removeClass("fa-spin");
            $(".stat-card").removeClass("opacity-60");
            isRefreshing = false;
        }
    });
}

/**
 * Update nilai stat cards berdasarkan data ringkasan
 */
function updateStatValue(selector, ringkasan) {
    const mapping = {
        'total_guru': 0,
        'total_kelas': 1,
        'total_mapel': 2,
        'total_users': 3,
        'total_jurnal': 4,
        'total_jurnal_bulan_ini': 5,
        'total_jurnal_hari_ini': 6,
        'total_siswa_bulan_ini': 7,
    };

    const counters = $(".counter");
    $.each(mapping, function (key, idx) {
        if (ringkasan[key] !== undefined && counters.eq(idx).length) {
            animateCounter(counters.eq(idx), ringkasan[key]);
            counters.eq(idx).data('target', ringkasan[key]);
        }
    });
}

// =============================================
// QUICK ACTIONS
// =============================================

/**
 * Fungsi untuk membuka form tambah jurnal
 */
function tambahJurnal() {
    Swal.fire({
        title: "Tambah Jurnal",
        text: "Apakah Anda ingin menambah jurnal baru?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#22c55e",
        cancelButtonColor: "#6b7280",
        confirmButtonText: '<i class="fas fa-plus mr-1"></i> Ya, tambah jurnal',
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = base_url + "jurnal";
        }
    });
}

/**
 * Fungsi untuk membuka halaman cetak laporan
 */
function cetakLaporan() {
    Swal.fire({
        title: "Cetak Laporan",
        text: "Pilih jenis laporan yang ingin dicetak",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#f59e0b",
        cancelButtonColor: "#6b7280",
        confirmButtonText: '<i class="fas fa-file-pdf mr-1"></i> Pilih Laporan',
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = base_url + "laporan";
        }
    });
}

// =============================================
// NOTIFICATIONS
// =============================================

/**
 * Fungsi untuk menampilkan notifikasi toast
 */
function showNotification(message, type) {
    const toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    toast.fire({ icon: type, title: message });
}

// =============================================
// DOCUMENT READY
// =============================================

$(document).ready(function () {

    // Inisialisasi counter animasi
    initCounters();

    // Inisialisasi charts
    initChartJurnalBulan();
    initChartMapel();

    // Update datetime real-time
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Tombol refresh
    $("#refreshBtn").on("click", function () {
        refreshDashboard();
    });

    // Auto refresh setiap 5 menit
    setInterval(function () {
        if (document.visibilityState === "visible" && !isRefreshing) {
            refreshDashboard();
        }
    }, 300000);

    // Keyboard shortcuts
    $(document).on("keydown", function (e) {
        if (e.ctrlKey && e.key === "n") {
            e.preventDefault();
            tambahJurnal();
        }
        if (e.ctrlKey && e.key === "p") {
            e.preventDefault();
            cetakLaporan();
        }
        if (e.key === "F5") {
            e.preventDefault();
            refreshDashboard();
        }
        if (e.key === "Escape") {
            $("#sidebar").addClass("-translate-x-full");
            $("#overlay").addClass("hidden");
            $("body").removeClass("overflow-hidden");
        }
    });

    // Sidebar mobile toggle
    $("#btnSidebar").on("click", function () {
        $("#sidebar").removeClass("-translate-x-full");
        $("#overlay").removeClass("hidden");
        $("body").addClass("overflow-hidden");
    });

    $("#overlay").on("click", function () {
        $("#sidebar").addClass("-translate-x-full");
        $("#overlay").addClass("hidden");
        $("body").removeClass("overflow-hidden");
    });

    // Smooth page transition saat klik nav
    $(".nav-item").on("click", function (e) {
        const href = $(this).attr("href");
        if (href && href !== "#" && !$(this).hasClass("nav-active")) {
            e.preventDefault();
            $("body").css({ opacity: 0.7, transition: "opacity 0.2s" });
            setTimeout(() => {
                window.location.href = href;
            }, 200);
        }
    });

    // Ripple effect pada tombol
    $("button, .stat-card").on("click", function (e) {
        const ripple = $("<span class='ripple'></span>");
        $(this).css("position", "relative").css("overflow", "hidden").append(ripple);
        const x = e.pageX - $(this).offset().left;
        const y = e.pageY - $(this).offset().top;
        ripple.css({ left: x, top: y });
        setTimeout(() => ripple.remove(), 600);
    });

});
