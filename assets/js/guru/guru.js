$(document).ready(function() {
    // Inisialisasi DataTable
    let table = $('#guruTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": base_url + "guru/get_data",
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
            { "data": "6" }
        ],
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
        },
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
    });

    // Load data kelas dan mapel untuk dropdown
    loadKelas();
    loadMapel();
    
    // Update statistik guru
    updateStats();

    // Event handlers
    $('#guruForm').on('submit', function(e) {
        e.preventDefault();
        saveGuru();
    });

    $('#refreshBtn').on('click', function() {
        refreshTable();
    });

    $('#searchBtn').on('click', function() {
        searchGuru();
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            searchGuru();
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
        }
    });
});

// Fungsi untuk membuka modal
function openModal() {
    resetForm();
    $('#modalTitle').text('Tambah Guru');
    $('#guruModal').addClass('show');
}

// Fungsi untuk menutup modal
function closeModal() {
    $('#guruModal').removeClass('show');
    resetForm();
}

// Fungsi untuk mereset form
function resetForm() {
    $('#guruForm')[0].reset();
    $('#id_guru').val('');
    $('.error-message').hide();
    $('.form-control').removeClass('border-red-500');
}

// Fungsi untuk load data kelas
function loadKelas() {
    $.ajax({
        url: base_url + 'guru/get_kelas',
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
        url: base_url + 'guru/get_mapel',
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

// Fungsi untuk menyimpan data guru
function saveGuru() {
    // Show loading
    $('#saveBtn').addClass('loading');
    
    // Clear previous errors
    $('.error-message').hide();
    $('.form-control').removeClass('border-red-500');
    
    $.ajax({
        url: base_url + 'guru/save',
        type: 'POST',
        data: $('#guruForm').serialize(),
        dataType: 'json',
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

// Fungsi untuk edit guru
function editGuru(id) {
    $.ajax({
        url: base_url + 'guru/get_by_id/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                let guru = response.data;
                
                $('#modalTitle').text('Edit Guru');
                $('#id_guru').val(guru.id_guru);
                $('#nama_guru').val(guru.nama_guru);
                $('#nip').val(guru.nip);
                $('#id_kelas').val(guru.id_kelas);
                $('#id_mapel').val(guru.id_mapel);
                $('#status').val(guru.status);
                
                $('#guruModal').addClass('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data guru'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memuat data guru'
            });
        }
    });
}

// Fungsi untuk hapus guru
function deleteGuru(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data guru yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'guru/delete/' + id,
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

// Fungsi untuk toggle status guru
function toggleStatus(id, currentStatus) {
    let newStatus = currentStatus === 'aktif' ? 'nonaktif' : 'aktif';
    let actionText = newStatus === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';
    
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Anda akan ' + actionText + ' guru ini!',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ' + actionText + '!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'guru/toggle_status/' + id,
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
                        text: 'Terjadi kesalahan saat mengubah status'
                    });
                }
            });
        }
    });
}

// Fungsi untuk refresh tabel
function refreshTable() {
    $('#guruTable').DataTable().ajax.reload();
}

// Fungsi untuk search guru
function searchGuru() {
    let keyword = $('#searchInput').val();
    
    if (keyword.trim() === '') {
        refreshTable();
        return;
    }
    
    $.ajax({
        url: base_url + 'guru/search',
        type: 'GET',
        data: { keyword: keyword },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Clear current table data
                $('#guruTable').DataTable().clear();
                
                // Add new data
                let data = [];
                $.each(response.data, function(index, guru) {
                    let statusBadge = '<span class="px-3 py-1 rounded-full text-xs font-medium ' + 
                                     (guru.status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') + 
                                     '">' + guru.status.charAt(0).toUpperCase() + guru.status.slice(1) + '</span>';
                    
                    let actionButtons = '<div class="flex gap-1">' +
                                       '<button onclick="editGuru(' + guru.id_guru + ')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">' +
                                       '<i class="fas fa-edit"></i>' +
                                       '</button>' +
                                       '<button onclick="deleteGuru(' + guru.id_guru + ')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">' +
                                       '<i class="fas fa-trash"></i>' +
                                       '</button>' +
                                       '<button onclick="toggleStatus(' + guru.id_guru + ', \'' + guru.status + '\')" class="px-3 py-1 ' + 
                                       (guru.status == 'aktif' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600') + 
                                       ' text-white rounded transition-colors">' +
                                       '<i class="fas ' + (guru.status == 'aktif' ? 'fa-eye-slash' : 'fa-eye') + '"></i>' +
                                       '</button>' +
                                       '</div>';
                    
                    data.push([
                        guru.id_guru,
                        guru.nama_guru,
                        guru.nip ? guru.nip : '-',
                        guru.nama_kelas,
                        guru.nama_mapel,
                        statusBadge,
                        actionButtons
                    ]);
                });
                
                $('#guruTable').DataTable().rows.add(data).draw();
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

// Fungsi untuk update statistik guru
function updateStats() {
    $.ajax({
        url: base_url + 'guru/get_data',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' || response.data) {
                let data = response.data || [];
                let totalGuru = data.length;
                let guruAktif = 0;
                let guruNonaktif = 0;
                
                $.each(data, function(index, guru) {
                    if (guru[5].includes('Aktif')) {
                        guruAktif++;
                    } else {
                        guruNonaktif++;
                    }
                });
                
                $('#totalGuru').text(totalGuru);
                $('#guruAktif').text(guruAktif);
                $('#guruNonaktif').text(guruNonaktif);
            }
        },
        error: function() {
            console.log('Error updating stats');
        }
    });
}