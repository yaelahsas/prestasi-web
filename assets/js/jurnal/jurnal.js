$(document).ready(function() {
    // Inisialisasi DataTable
    let table = $('#jurnalTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": base_url + "jurnal/get_data",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "0" },
            { "data": "1" },
            { "data": "2" },
            { "data": "3" },
            { "data": "4" },
            { "data": "5" },
            { "data": "6" },
            { "data": "7" },
            { "data": "8" }
        ],
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
        },
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
    });

    // Load data guru, kelas, dan mapel untuk dropdown
    loadGuru();
    loadKelas();
    loadMapel();
    
    // Update statistik jurnal
    updateStats();

    // Event handlers
    $('#jurnalForm').on('submit', function(e) {
        e.preventDefault();
        saveJurnal();
    });

    $('#refreshBtn').on('click', function() {
        refreshTable();
    });

    $('#searchBtn').on('click', function() {
        searchJurnal();
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            searchJurnal();
        }
    });

    // Sidebar toggle untuk mobile
    $('#btnSidebar').on('click', function() {
        $('#sidebar').toggleClass('-translate-x-full');
        $('#overlay').toggleClass('hidden');
    });

    $('#overlay').on('click', function() {
        $('#sidebar').addClass('-translate-x-full');
        $('#overlay').addClass('hidden');
    });

    // Close modal saat klik di luar modal
    $(window).on('click', function(e) {
        if ($(e.target).hasClass('modal')) {
            closeModal();
            closeViewModal();
            closeFilterModal();
        }
        if ($(e.target).hasClass('image-modal')) {
            closeImageModal();
        }
    });
});

// Fungsi untuk membuka modal
function openModal() {
    resetForm();
    $('#modalTitle').text('Tambah Jurnal');
    $('#jurnalModal').addClass('show');
    // Set tanggal default ke hari ini
    $('#tanggal').val(new Date().toISOString().split('T')[0]);
}

// Fungsi untuk menutup modal
function closeModal() {
    $('#jurnalModal').removeClass('show');
    resetForm();
}

// Fungsi untuk mereset form
function resetForm() {
    $('#jurnalForm')[0].reset();
    $('#id_jurnal').val('');
    $('.error-message').hide();
    $('.form-control').removeClass('border-red-500');
    // Set tanggal default ke hari ini
    $('#tanggal').val(new Date().toISOString().split('T')[0]);
}

// Fungsi untuk load data guru
function loadGuru() {
    $.ajax({
        url: base_url + 'jurnal/get_guru',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                let options = '<option value="">-- Pilih Guru --</option>';
                $.each(response.data, function(index, guru) {
                    options += '<option value="' + guru.id_guru + '">' + guru.nama_guru + '</option>';
                });
                $('#id_guru').html(options);
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat data guru'
            });
        }
    });
}

// Fungsi untuk load data kelas
function loadKelas() {
    $.ajax({
        url: base_url + 'jurnal/get_kelas',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                let options = '<option value="">-- Pilih Kelas --</option>';
                $.each(response.data, function(index, kelas) {
                    options += '<option value="' + kelas.id_kelas + '">' + kelas.nama_kelas + '</option>';
                });
                $('#id_kelas').html(options);
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat data kelas'
            });
        }
    });
}

// Fungsi untuk load data mapel
function loadMapel() {
    $.ajax({
        url: base_url + 'jurnal/get_mapel',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                let options = '<option value="">-- Pilih Mata Pelajaran --</option>';
                $.each(response.data, function(index, mapel) {
                    options += '<option value="' + mapel.id_mapel + '">' + mapel.nama_mapel + '</option>';
                });
                $('#id_mapel').html(options);
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat data mata pelajaran'
            });
        }
    });
}

// Fungsi untuk menyimpan data jurnal
function saveJurnal() {
    // Show loading
    $('#saveBtn').addClass('loading');
    
    // Clear previous errors
    $('.error-message').hide();
    $('.form-control').removeClass('border-red-500');
    
    // Create FormData untuk handle file upload
    let formData = new FormData($('#jurnalForm')[0]);
    
    $.ajax({
        url: base_url + 'jurnal/save',
        type: 'POST',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(response) {
            $('#saveBtn').removeClass('loading');
            
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                closeModal();
                refreshTable();
                updateStats();
            } else {
                // Show validation errors
                if (response.errors) {
                    $.each(response.errors, function(key, value) {
                        $('#' + key + '_error').text(value).show();
                        $('#' + key).addClass('border-red-500');
                    });
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function() {
            $('#saveBtn').removeClass('loading');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menyimpan data'
            });
        }
    });
}

// Fungsi untuk edit jurnal
function editJurnal(id) {
    $.ajax({
        url: base_url + 'jurnal/get_by_id/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                let jurnal = response.data;
                
                $('#modalTitle').text('Edit Jurnal');
                $('#id_jurnal').val(jurnal.id_jurnal);
                $('#tanggal').val(jurnal.tanggal);
                $('#id_guru').val(jurnal.id_guru);
                $('#id_kelas').val(jurnal.id_kelas);
                $('#id_mapel').val(jurnal.id_mapel);
                $('#materi').val(jurnal.materi);
                $('#jumlah_siswa').val(jurnal.jumlah_siswa);
                $('#keterangan').val(jurnal.keterangan);
                
                $('#jurnalModal').addClass('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data jurnal'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memuat data jurnal'
            });
        }
    });
}

// Fungsi untuk hapus jurnal
function deleteJurnal(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data jurnal yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'jurnal/delete/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        refreshTable();
                        updateStats();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menghapus data'
                    });
                }
            });
        }
    });
}

// Fungsi untuk view detail jurnal
function viewJurnal(id) {
    $.ajax({
        url: base_url + 'jurnal/get_by_id/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                let jurnal = response.data;
                
                let content = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="font-semibold text-gray-700">Tanggal:</label>
                                <p class="text-gray-900">${jurnal.tanggal}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-700">Guru:</label>
                                <p class="text-gray-900">${jurnal.nama_guru} ${jurnal.nip ? '(' + jurnal.nip + ')' : ''}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-700">Kelas:</label>
                                <p class="text-gray-900">${jurnal.nama_kelas}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-700">Mata Pelajaran:</label>
                                <p class="text-gray-900">${jurnal.nama_mapel}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-700">Jumlah Siswa:</label>
                                <p class="text-gray-900">${jurnal.jumlah_siswa} siswa</p>
                            </div>
                            <div>
                                <label class="font-semibold text-gray-700">Penginput:</label>
                                <p class="text-gray-900">${jurnal.nama_penginput}</p>
                            </div>
                        </div>
                        <div>
                            <label class="font-semibold text-gray-700">Materi:</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded">${jurnal.materi}</p>
                        </div>
                        ${jurnal.keterangan ? `
                        <div>
                            <label class="font-semibold text-gray-700">Keterangan:</label>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded">${jurnal.keterangan}</p>
                        </div>
                        ` : ''}
                        ${jurnal.foto_bukti ? `
                        <div>
                            <label class="font-semibold text-gray-700">Foto Bukti:</label>
                            <div class="mt-2">
                                <img src="${base_url}assets/uploads/foto_kegiatan/${jurnal.foto_bukti}" 
                                     alt="Foto Bukti" 
                                     class="w-64 h-64 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
                                     onclick="viewImage('${jurnal.foto_bukti}')">
                            </div>
                        </div>
                        ` : ''}
                        <div>
                            <label class="font-semibold text-gray-700">Dibuat:</label>
                            <p class="text-gray-900">${jurnal.created_at}</p>
                        </div>
                    </div>
                `;
                
                $('#viewJurnalContent').html(content);
                $('#viewJurnalModal').addClass('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data jurnal'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memuat data jurnal'
            });
        }
    });
}

// Fungsi untuk menutup modal view
function closeViewModal() {
    $('#viewJurnalModal').removeClass('show');
}

// Fungsi untuk membuka modal filter
function openFilterModal() {
    $('#filterModal').addClass('show');
    // Set default tanggal
    $('#tanggal_awal').val(new Date().toISOString().split('T')[0]);
    $('#tanggal_akhir').val(new Date().toISOString().split('T')[0]);
}

// Fungsi untuk menutup modal filter
function closeFilterModal() {
    $('#filterModal').removeClass('show');
}

// Fungsi untuk apply filter
function applyFilter() {
    let tanggalAwal = $('#tanggal_awal').val();
    let tanggalAkhir = $('#tanggal_akhir').val();
    
    if (!tanggalAwal || !tanggalAkhir) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Tanggal awal dan tanggal akhir harus diisi'
        });
        return;
    }
    
    $.ajax({
        url: base_url + 'jurnal/filter_by_tanggal',
        type: 'GET',
        data: {
            tanggal_awal: tanggalAwal,
            tanggal_akhir: tanggalAkhir
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Clear current table data
                $('#jurnalTable').DataTable().clear();
                
                // Add new data
                let data = [];
                $.each(response.data, function(index, jurnal) {
                    let fotoHtml = jurnal.foto_bukti ? 
                        '<img src="' + base_url + 'assets/uploads/foto_kegiatan/' + jurnal.foto_bukti + '" alt="Foto Bukti" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; cursor: pointer;" onclick="viewImage(\'' + jurnal.foto_bukti + '\')">' : 
                        '<span class="text-gray-400">Tidak ada</span>';
                    
                    let actionButtons = '<div class="flex gap-1">' +
                                       '<button onclick="editJurnal(' + jurnal.id_jurnal + ')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">' +
                                       '<i class="fas fa-edit"></i>' +
                                       '</button>' +
                                       '<button onclick="deleteJurnal(' + jurnal.id_jurnal + ')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">' +
                                       '<i class="fas fa-trash"></i>' +
                                       '</button>' +
                                       '<button onclick="viewJurnal(' + jurnal.id_jurnal + ')" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">' +
                                       '<i class="fas fa-eye"></i>' +
                                       '</button>' +
                                       '</div>';
                    
                    data.push([
                        jurnal.id_jurnal,
                        jurnal.tanggal,
                        jurnal.nama_guru,
                        jurnal.nama_kelas,
                        jurnal.nama_mapel,
                        jurnal.materi.length > 50 ? jurnal.materi.substring(0, 50) + '...' : jurnal.materi,
                        jurnal.jumlah_siswa,
                        fotoHtml,
                        actionButtons
                    ]);
                });
                
                $('#jurnalTable').DataTable().rows.add(data).draw();
                closeFilterModal();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data berhasil difilter',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memfilter data'
            });
        }
    });
}

// Fungsi untuk view image
function viewImage(filename) {
    $('#previewImage').attr('src', base_url + 'assets/uploads/foto_kegiatan/' + filename);
    $('#imageModal').addClass('show');
}

// Fungsi untuk menutup image modal
function closeImageModal() {
    $('#imageModal').removeClass('show');
}

// Fungsi untuk refresh tabel
function refreshTable() {
    $('#jurnalTable').DataTable().ajax.reload();
}

// Fungsi untuk search jurnal
function searchJurnal() {
    let keyword = $('#searchInput').val();
    
    if (keyword.trim() === '') {
        refreshTable();
        return;
    }
    
    $.ajax({
        url: base_url + 'jurnal/search',
        type: 'GET',
        data: { keyword: keyword },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Clear current table data
                $('#jurnalTable').DataTable().clear();
                
                // Add new data
                let data = [];
                $.each(response.data, function(index, jurnal) {
                    let fotoHtml = jurnal.foto_bukti ? 
                        '<img src="' + base_url + 'assets/uploads/foto_kegiatan/' + jurnal.foto_bukti + '" alt="Foto Bukti" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; cursor: pointer;" onclick="viewImage(\'' + jurnal.foto_bukti + '\')">' : 
                        '<span class="text-gray-400">Tidak ada</span>';
                    
                    let actionButtons = '<div class="flex gap-1">' +
                                       '<button onclick="editJurnal(' + jurnal.id_jurnal + ')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">' +
                                       '<i class="fas fa-edit"></i>' +
                                       '</button>' +
                                       '<button onclick="deleteJurnal(' + jurnal.id_jurnal + ')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">' +
                                       '<i class="fas fa-trash"></i>' +
                                       '</button>' +
                                       '<button onclick="viewJurnal(' + jurnal.id_jurnal + ')" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">' +
                                       '<i class="fas fa-eye"></i>' +
                                       '</button>' +
                                       '</div>';
                    
                    data.push([
                        jurnal.id_jurnal,
                        jurnal.tanggal,
                        jurnal.nama_guru,
                        jurnal.nama_kelas,
                        jurnal.nama_mapel,
                        jurnal.materi.length > 50 ? jurnal.materi.substring(0, 50) + '...' : jurnal.materi,
                        jurnal.jumlah_siswa,
                        fotoHtml,
                        actionButtons
                    ]);
                });
                
                $('#jurnalTable').DataTable().rows.add(data).draw();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal melakukan pencarian'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat melakukan pencarian'
            });
        }
    });
}

// Fungsi untuk update statistik jurnal
function updateStats() {
    $.ajax({
        url: base_url + 'jurnal/get_data',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' || response.data) {
                let data = response.data || [];
                let totalJurnal = data.length;
                let jurnalHariIni = 0;
                let jurnalBulanIni = 0;
                
                let today = new Date().toISOString().split('T')[0];
                let currentMonth = new Date().getMonth() + 1;
                let currentYear = new Date().getFullYear();
                
                $.each(data, function(index, jurnal) {
                    let jurnalDate = new Date(jurnal[1]);
                    let jurnalDateStr = jurnalDate.toISOString().split('T')[0];
                    
                    if (jurnalDateStr === today) {
                        jurnalHariIni++;
                    }
                    
                    if (jurnalDate.getMonth() + 1 === currentMonth && jurnalDate.getFullYear() === currentYear) {
                        jurnalBulanIni++;
                    }
                });
                
                $('#totalJurnal').text(totalJurnal);
                $('#jurnalHariIni').text(jurnalHariIni);
                $('#jurnalBulanIni').text(jurnalBulanIni);
            }
        },
        error: function() {
            console.log('Error updating stats');
        }
    });
}