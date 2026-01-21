/**
 * JavaScript untuk Dashboard
 * Menangani interaksi dan shortcut pada halaman dashboard
 */

// Base URL dari aplikasi
const base_url = window.location.origin + "/prestasi/";

// Global variables
let isRefreshing = false;
let refreshInterval;

/**
 * Fungsi untuk membuka form tambah jurnal
 */
function tambahJurnal() {
	Swal.fire({
		title: "Tambah Jurnal",
		text: "Apakah Anda ingin menambah jurnal baru?",
		icon: "question",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Ya, tambah jurnal",
		cancelButtonText: "Batal",
	}).then((result) => {
		if (result.isConfirmed) {
			// Redirect ke halaman jurnal dengan parameter tambah
			window.location.href = base_url + "jurnal/tambah";
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
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Pilih Laporan",
		cancelButtonText: "Batal",
	}).then((result) => {
		if (result.isConfirmed) {
			// Redirect ke halaman laporan
			window.location.href = base_url + "laporan";
		}
	});
}

/**
 * Fungsi untuk refresh data dashboard dengan Ajax
 */
function refreshDashboard() {
	if (isRefreshing) return;
	
	isRefreshing = true;
	
	// Tampilkan loading pada tombol refresh
	$("#refreshBtn i").addClass("fa-spin");
	
	// Tampilkan loading pada stat cards
	$(".stat-card").addClass("opacity-50");
	
	// Ambil data terbaru via Ajax
	$.ajax({
		url: base_url + "dashboard/get_stats",
		type: "GET",
		dataType: "json",
		success: function(response) {
			if (response.status === "success") {
				// Update data dengan animasi
				updateStatCards(response.data);
				
				// Tampilkan notifikasi sukses
				showNotification("Data berhasil diperbarui", "success");
			} else {
				showNotification("Gagal memperbarui data", "error");
			}
		},
		error: function() {
			showNotification("Terjadi kesalahan saat memperbarui data", "error");
		},
		complete: function() {
			// Hapus loading
			$("#refreshBtn i").removeClass("fa-spin");
			$(".stat-card").removeClass("opacity-50");
			isRefreshing = false;
		}
	});
}

/**
 * Fungsi untuk memperbarui stat cards dengan animasi
 */
function updateStatCards(data) {
	// Update total guru
	animateCounter($('[data-target="total_guru"]'), data.total_guru);
	
	// Update total kelas
	animateCounter($('[data-target="total_kelas"]'), data.total_kelas);
	
	// Update jurnal bulan ini
	animateCounter($('[data-target="total_jurnal_bulan_ini"]'), data.total_jurnal_bulan_ini);
	
	// Update jurnal hari ini
	animateCounter($('[data-target="total_jurnal_hari_ini"]'), data.total_jurnal_hari_ini);
}

/**
 * Fungsi untuk animasi counter
 */
function animateCounter(element, target) {
	const duration = 1000;
	const step = target / (duration / 16);
	let current = 0;
	
	const timer = setInterval(() => {
		current += step;
		if (current >= target) {
			current = target;
			clearInterval(timer);
		}
		element.text(Math.floor(current));
	}, 16);
}

/**
 * Fungsi untuk menampilkan notifikasi
 */
function showNotification(message, type) {
	const toast = Swal.mixin({
		toast: true,
		position: 'top-end',
		showConfirmButton: false,
		timer: 3000,
		timerProgressBar: true,
		didOpen: (toast) => {
			toast.addEventListener('mouseenter', Swal.stopTimer)
			toast.addEventListener('mouseleave', Swal.resumeTimer)
		}
	});
	
	toast.fire({
		icon: type,
		title: message
	});
}

/**
 * Fungsi untuk menampilkan notifikasi sukses
 * @param {string} message - Pesan notifikasi
 */
function showSuccess(message) {
	Swal.fire({
		title: "Berhasil!",
		text: message,
		icon: "success",
		timer: 2000,
		timerProgressBar: true,
		showConfirmButton: false,
	});
}

/**
 * Fungsi untuk menampilkan notifikasi error
 * @param {string} message - Pesan error
 */
function showError(message) {
	Swal.fire({
		title: "Error!",
		text: message,
		icon: "error",
		confirmButtonText: "OK",
	});
}

/**
 * Document ready function
 */
$(document).ready(function () {
	// Inisialisasi animasi counter untuk stat cards
	initCounters();
	
	// Event listener untuk tombol refresh
	$("#refreshBtn").on("click", function() {
		refreshDashboard();
	});
	
	// Animasi untuk cards saat hover
	$(".stat-card, .group").hover(
		function () {
			$(this).addClass("transform scale-105");
		},
		function () {
			$(this).removeClass("transform scale-105");
		},
	);

	// Auto refresh dashboard setiap 5 menit (300000 ms)
	refreshInterval = setInterval(function () {
		// Cek apakah user masih aktif di halaman
		if (document.visibilityState === "visible" && !isRefreshing) {
			refreshDashboard();
		}
	}, 300000);

	// Keyboard shortcuts
	$(document).on("keydown", function (e) {
		// Ctrl + N untuk tambah jurnal
		if (e.ctrlKey && e.key === "n") {
			e.preventDefault();
			tambahJurnal();
		}
		// Ctrl + P untuk cetak laporan
		if (e.ctrlKey && e.key === "p") {
			e.preventDefault();
			cetakLaporan();
		}
		// F5 untuk refresh
		if (e.key === "F5") {
			e.preventDefault();
			refreshDashboard();
		}
		// ESC untuk tutup sidebar mobile
		if (e.key === "Escape") {
			$("#sidebar").addClass("-translate-x-full");
			$("#overlay").addClass("hidden");
		}
	});
	
	// Sidebar mobile
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
	
	// Smooth scroll untuk navigasi
	$(".nav-item").on("click", function() {
		const href = $(this).attr("href");
		if (href && href !== "#") {
			$("body").addClass("opacity-50");
			setTimeout(() => {
				window.location.href = href;
			}, 200);
		}
	});
	
	// Tambahkan efek ripple pada tombol
	$(".group, button, .nav-item").on("click", function(e) {
		const ripple = $("<span class='ripple'></span>");
		$(this).append(ripple);
		
		const x = e.pageX - $(this).offset().left;
		const y = e.pageY - $(this).offset().top;
		
		ripple.css({
			left: x,
			top: y
		});
		
		setTimeout(() => {
			ripple.remove();
		}, 600);
	});
});

/**
 * Fungsi untuk inisialisasi animasi counter
 */
function initCounters() {
	$(".counter").each(function() {
		const target = parseInt($(this).data("target"));
		const duration = 1500;
		const step = target / (duration / 16);
		let current = 0;
		
		const timer = setInterval(() => {
			current += step;
			if (current >= target) {
				current = target;
				clearInterval(timer);
			}
			$(this).text(Math.floor(current));
		}, 16);
	});
}
