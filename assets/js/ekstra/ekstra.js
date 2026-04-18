$(document).ready(function () {
	// Inisialisasi DataTable
	let table = $("#ekstraTable").DataTable({
		processing: true,
		serverSide: false,
		ajax: {
			url: base_url + "ekstra/get_data",
			type: "GET",
			dataSrc: "data",
		},
		columns: [
			{ data: "0", className: "text-center", responsivePriority: 1 }, // No
			{ data: "1", className: "text-left", responsivePriority: 2 }, // Nama
			{ data: "2", className: "text-center", responsivePriority: 3 }, // Jml Pembina
			{ data: "3", className: "text-center", responsivePriority: 4 }, // Status
			{ data: "4", className: "text-center", responsivePriority: 5 }, // Aksi
		],
		responsive: {
			details: {
				type: 'column',
				target: 'tr',
				renderer: function ( api, rowIdx, columns ) {
					var data = $.map( columns, function ( col, i ) {
						return col.hidden ?
							'<tr data-dt-row="'+rowIdx+'" data-dt-column="'+i+'">'+
								'<td class="font-semibold text-gray-700 bg-gray-50 px-3 py-2" style="width: 40%;">'+col.title+':'+'</td> '+
								'<td class="px-3 py-2">'+col.data+'</td>'+
							'</tr>' :
							'';
					} ).join('');

					return data ?
						$('<table class="w-full"/>').append( data ) :
						false;
				}
			}
		},
		language: {
			url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json",
		},
		pageLength: 10,
		lengthMenu: [
			[5, 10, 25, 50, -1],
			[5, 10, 25, 50, "Semua"],
		],
		dom: '<"top"f>rt<"bottom"lip>',
	});

	// Update statistik ekstra
	updateStats();

	// Event handlers
	$("#ekstraForm").on("submit", function (e) {
		e.preventDefault();
		saveEkstra();
	});

	$("#refreshBtn").on("click", function () {
		refreshTable();
	});

	$("#searchBtn").on("click", function () {
		searchEkstra();
	});

	$("#searchInput").on("keypress", function (e) {
		if (e.which === 13) {
			searchEkstra();
		}
	});

	// Sidebar toggle untuk mobile
	$("#btnSidebar").on("click", function () {
		$("#sidebar").toggleClass("-translate-x-full");
		$("#overlay").toggleClass("hidden");
	});

	$("#overlay").on("click", function () {
		$("#sidebar").addClass("-translate-x-full");
		$("#overlay").addClass("hidden");
	});

	// Close modal saat klik di luar modal
	$(window).on("click", function (e) {
		if ($(e.target).hasClass("modal")) {
			closeModal();
			closeGuruModal();
		}
	});
});

// Fungsi untuk membuka modal
function openModal() {
    resetForm();

    loadGuru(); // 🔥 WAJIB BANGET

    $('#modalTitle').text('Tambah Ekstrakurikuler');
    $('#ekstraModal').addClass('show');
}

// Fungsi untuk menutup modal
function closeModal() {
	// Destroy Select2 before closing modal
	if ($("#guru_ids").data("select2")) {
		$("#guru_ids").select2("destroy");
	}
	
	$("#ekstraModal").removeClass("show");
	
	// Reset form after modal is closed
	setTimeout(function() {
		$("#ekstraForm")[0].reset();
		$("#id_ekstra").val("");
		$(".error-message").hide();
		$(".form-control").removeClass("border-red-500");
	}, 300);
}

// Fungsi untuk mereset form
function resetForm() {
	$("#ekstraForm")[0].reset();
	$("#id_ekstra").val("");
	$(".error-message").hide();
	$(".form-control").removeClass("border-red-500");
	
	// Reset Select2 if it exists
	if ($("#guru_ids").data("select2")) {
		$("#guru_ids").select2("val", "").trigger("change");
	}
}

// fungsi untuk memuat data guru ke dalam select2
function loadGuru(selected = []) {
	$.ajax({
		url: base_url + "ekstra/get_guru",
		type: "GET",
		dataType: "json",
		success: function (res) {
			let html = "";
			if (res.data && res.data.length > 0) {
				res.data.forEach((g) => {
					let isSelected = selected.includes(g.id_guru.toString())
						? "selected"
						: "";
					html += `<option value="${g.id_guru}" ${isSelected}>
                            ${g.nama_guru} (${g.nip})
                         </option>`;
				});
			} else {
				html = '<option value="">Tidak ada guru tersedia</option>';
			}

			$("#guru_ids").html(html);

			// aktifkan select2
			$("#guru_ids").select2({
				placeholder: "Pilih guru (bisa lebih dari satu)",
				width: "100%",
				allowClear: true,
				dropdownParent: $("#ekstraModal"),
			});
		},
		error: function (xhr, status, error) {
			console.error("Error loading guru:", error);
			$("#guru_ids").html('<option value="">Gagal memuat data guru</option>');
		},
	});
}

// Fungsi untuk menyimpan data ekstra
function saveEkstra() {
	// Show loading
	$("#saveBtn").addClass("loading");

	// Clear previous errors
	$(".error-message").hide();
	$(".form-control").removeClass("border-red-500");

	// Get selected guru IDs from Select2
	const selectedGuru = $("#guru_ids").select2("data");
	const guruIds = selectedGuru.map(function (item) {
		return item.id;
	});

	// Check if at least one guru is selected
	if (guruIds.length === 0) {
		$("#saveBtn").removeClass("loading");
		Swal.fire({
			icon: "warning",
			title: "Peringatan",
			text: "Harap pilih minimal satu guru",
		});
		return;
	}

	// Create form data object
	const formData = {
		id_ekstra: $("#id_ekstra").val(),
		nama_ekstra: $("#nama_ekstra").val(),
		deskripsi: $("#deskripsi").val(),
		status: $("#status").val(),
		guru_ids: guruIds
	};

	$.ajax({
		url: base_url + "ekstra/save",
		type: "POST",
		data: formData,
		dataType: "json",
		success: function (response) {
			$("#saveBtn").removeClass("loading");

			if (response.status === "success") {
				Swal.fire({
					icon: "success",
					title: "Berhasil",
					text: response.message,
					timer: 2000,
					showConfirmButton: false,
				});

				closeModal();
				refreshTable();
				updateStats();
			} else {
				// Show validation errors
				if (response.errors) {
					$.each(response.errors, function (key, value) {
						$("#" + key + "_error")
							.text(value)
							.show();
						$("#" + key).addClass("border-red-500");
					});
				}

				Swal.fire({
					icon: "error",
					title: "Gagal",
					text: response.message,
				});
			}
		},
		error: function (xhr, status, error) {
			$("#saveBtn").removeClass("loading");

			Swal.fire({
				icon: "error",
				title: "Error",
				text: "Terjadi kesalahan saat menyimpan data",
			});
		},
	});
}

// Fungsi untuk mengedit data ekstra
function editEkstra(id) {
	// Show loading
	Swal.fire({
		title: "Memuat data...",
		allowOutsideClick: false,
		didOpen: () => {
			Swal.showLoading();
		},
	});

	$.ajax({
		url: base_url + "ekstra/edit/" + id,
		type: "GET",
		dataType: "json",
		success: function (response) {
			Swal.close();

			if (response.status === "success") {
				const data = response.data;

				$("#id_ekstra").val(data.id_ekstra);
				$("#nama_ekstra").val(data.nama_ekstra);
				$("#deskripsi").val(data.deskripsi);
				$("#status").val(data.status);

				// Load guru dengan selected values
				const guruIds = response.guru_ids || [];
				loadGuru(guruIds);

				$("#modalTitle").text("Edit Ekstrakurikuler");
				$("#ekstraModal").addClass("show");
			} else {
				Swal.fire({
					icon: "error",
					title: "Gagal",
					text: response.message,
				});
			}
		},
		error: function (xhr, status, error) {
			Swal.close();

			Swal.fire({
				icon: "error",
				title: "Error",
				text: "Terjadi kesalahan saat memuat data",
			});
		},
	});
}

// Fungsi untuk menghapus data ekstra
function deleteEkstra(id) {
	Swal.fire({
		title: "Apakah Anda yakin?",
		text: "Data yang dihapus tidak dapat dikembalikan!",
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: "#d33",
		cancelButtonColor: "#3085d6",
		confirmButtonText: "Ya, hapus!",
		cancelButtonText: "Batal",
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: base_url + "ekstra/delete/" + id,
				type: "GET",
				dataType: "json",
				success: function (response) {
					if (response.status === "success") {
						Swal.fire({
							icon: "success",
							title: "Berhasil",
							text: response.message,
							timer: 2000,
							showConfirmButton: false,
						});

						refreshTable();
						updateStats();
					} else {
						Swal.fire({
							icon: "error",
							title: "Gagal",
							text: response.message,
						});
					}
				},
				error: function (xhr, status, error) {
					Swal.fire({
						icon: "error",
						title: "Error",
						text: "Terjadi kesalahan saat menghapus data",
					});
				},
			});
		}
	});
}

// Fungsi untuk mengubah status ekstra
function toggleStatus(id, currentStatus) {
	const newStatus = currentStatus === "aktif" ? "nonaktif" : "aktif";
	const statusText = newStatus === "aktif" ? "mengaktifkan" : "menonaktifkan";

	Swal.fire({
		title: "Apakah Anda yakin?",
		text: `Anda akan ${statusText} ekstrakurikuler ini`,
		icon: "question",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Ya, lanjutkan!",
		cancelButtonText: "Batal",
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: base_url + "ekstra/toggle_status/" + id,
				type: "GET",
				data: { status: newStatus },
				dataType: "json",
				success: function (response) {
					if (response.status === "success") {
						Swal.fire({
							icon: "success",
							title: "Berhasil",
							text: response.message,
							timer: 2000,
							showConfirmButton: false,
						});

						refreshTable();
						updateStats();
					} else {
						Swal.fire({
							icon: "error",
							title: "Gagal",
							text: response.message,
						});
					}
				},
				error: function (xhr, status, error) {
					Swal.fire({
						icon: "error",
						title: "Error",
						text: "Terjadi kesalahan saat mengubah status",
					});
				},
			});
		}
	});
}

// Fungsi untuk merefresh tabel
function refreshTable() {
	$("#ekstraTable").DataTable().ajax.reload();
}

// Fungsi untuk mencari ekstra
function searchEkstra() {
	const keyword = $("#searchInput").val();

	if (keyword === "") {
		refreshTable();
		return;
	}

	$.ajax({
		url: base_url + "ekstra/search",
		type: "GET",
		data: { keyword: keyword },
		dataType: "json",
		success: function (response) {
			if (response.status === "success") {
				// Clear table and reload with search results
				$("#ekstraTable").DataTable().clear();
				$("#ekstraTable").DataTable().rows.add(response.data);
				$("#ekstraTable").DataTable().draw();
			} else {
				Swal.fire({
					icon: "info",
					title: "Pencarian",
					text: response.message,
				});

				// Clear table if no results
				$("#ekstraTable").DataTable().clear();
				$("#ekstraTable").DataTable().draw();
			}
		},
		error: function (xhr, status, error) {
			Swal.fire({
				icon: "error",
				title: "Error",
				text: "Terjadi kesalahan saat mencari data",
			});
		},
	});
}

// Fungsi untuk update statistik
function updateStats() {
	$.ajax({
		url: base_url + "ekstra/get_stats",
		type: "GET",
		dataType: "json",
		success: function (response) {
			if (response.status === "success") {
				$("#ekstraAktif").text(response.data.aktif);
				$("#ekstraNonaktif").text(response.data.nonaktif);
			}
		},
		error: function (xhr, status, error) {
			console.log("Error updating stats:", error);
		},
	});
}

// Fungsi untuk menampilkan detail guru
function showGuruDetails(id) {
	// Show loading
	Swal.fire({
		title: "Memuat data...",
		allowOutsideClick: false,
		didOpen: () => {
			Swal.showLoading();
		},
	});

	$.ajax({
		url: base_url + "ekstra/get_guru_details/" + id,
		type: "GET",
		dataType: "json",
		success: function (response) {
			Swal.close();

			if (response.status === "success") {
				const ekstra = response.data.ekstra;
				const guruList = response.data.guru_list;

				// Set modal title
				$("#guruModalTitle").text("Detail Pembina - " + ekstra.nama_ekstra);

				// Build guru list HTML
				let guruHtml = '';
				if (guruList && guruList.length > 0) {
					guruHtml = '<div class="space-y-3">';
					guruList.forEach((guru, index) => {
						guruHtml += `
							<div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
								<div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0">
									<i class="fas fa-user text-white"></i>
								</div>
								<div class="flex-1">
									<h4 class="font-semibold text-gray-800">${guru.nama_guru}</h4>
									<p class="text-sm text-gray-600">
										<i class="fas fa-id-card mr-1"></i>NIP: ${guru.nip}
									</p>
								</div>
							</div>
						`;
					});
					guruHtml += '</div>';
				} else {
					guruHtml = `
						<div class="text-center py-8">
							<i class="fas fa-user-slash text-4xl text-gray-300 mb-3"></i>
							<p class="text-gray-500">Belum ada pembina untuk ekstrakurikuler ini</p>
						</div>
					`;
				}

				// Set content
				$("#guruDetailsContent").html(guruHtml);

				// Show modal
				$("#guruDetailsModal").addClass("show");
			} else {
				Swal.fire({
					icon: "error",
					title: "Gagal",
					text: response.message,
				});
			}
		},
		error: function (xhr, status, error) {
			Swal.close();

			Swal.fire({
				icon: "error",
				title: "Error",
				text: "Terjadi kesalahan saat memuat data guru",
			});
		},
	});
}

// Fungsi untuk menutup modal guru
function closeGuruModal() {
	$("#guruDetailsModal").removeClass("show");
}
