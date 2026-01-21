$(document).ready(function() {
    // Inisialisasi DataTable
    let table = $('#mapelTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": base_url + "mapel/get_data",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "0" },
            { "data": "1" },
            { "data": "2" },
            { "data": "3" }
        ],
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
        },
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
    });
    
    // Update statistik mapel
    updateStats();

    // Event handlers
    $('#mapelForm').on('submit', function(e) {
        e.preventDefault();
        saveMapel();
    });

    $('#refreshBtn').on('click', function() {
        refreshTable();
    });

    $('#searchBtn').on('click', function() {
        searchMapel();
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            searchMapel();
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
    $('#modalTitle').text('Tambah Mata Pelajaran');
    $('#mapelModal').addClass('show');
}

// Fungsi untuk menutup modal
function closeModal() {
    $('#mapelModal').removeClass('show');
    resetForm();
}

// Fungsi untuk mereset form
function resetForm() {
    $('#mapelForm')[0].reset();
    $('#id_mapel').val('');
    $('.error-message').hide();
    $('.form-control').removeClass('border-red-500');
}

// Fungsi untuk menyimpan data mapel
function saveMapel() {
    // Show loading
    $('#saveBtn').addClass('loading');
    
    // Clear previous errors
    $('.error-message').hide();
    $('.form-control').removeClass('border-red-500');
    
    $.ajax({
        url: base_url + 'mapel/save',
        type: 'POST',
        data: $('#mapelForm').serialize(),
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

// Fungsi untuk edit mapel
function editMapel(id) {
    $.ajax({
        url: base_url + 'mapel/get_by_id/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                let mapel = response.data;
                
                $('#modalTitle').text('Edit Mata Pelajaran');
                $('#id_mapel').val(mapel.id_mapel);
                $('#nama_mapel').val(mapel.nama_mapel);
                $('#status').val(mapel.status);
                
                $('#mapelModal').addClass('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data mata pelajaran'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memuat data mata pelajaran'
            });
        }
    });
}

// Fungsi untuk hapus mapel
function deleteMapel(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data mata pelajaran yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'mapel/delete/' + id,
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

// Fungsi untuk toggle status mapel
function toggleStatus(id, currentStatus) {
    let newStatus = currentStatus === 'aktif' ? 'nonaktif' : 'aktif';
    let actionText = newStatus === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';
    
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Anda akan ' + actionText + ' mata pelajaran ini!',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ' + actionText + '!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'mapel/toggle_status/' + id,
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
    $('#mapelTable').DataTable().ajax.reload();
}

// Fungsi untuk search mapel
function searchMapel() {
    let keyword = $('#searchInput').val();
    
    if (keyword.trim() === '') {
        refreshTable();
        return;
    }
    
    $.ajax({
        url: base_url + 'mapel/search',
        type: 'GET',
        data: { keyword: keyword },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Clear current table data
                $('#mapelTable').DataTable().clear();
                
                // Add new data
                let data = [];
                $.each(response.data, function(index, mapel) {
                    let statusBadge = '<span class="px-3 py-1 rounded-full text-xs font-medium ' + 
                                     (mapel.status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') + 
                                     '">' + mapel.status.charAt(0).toUpperCase() + mapel.status.slice(1) + '</span>';
                    
                    let actionButtons = '<div class="flex gap-1">' +
                                       '<button onclick="editMapel(' + mapel.id_mapel + ')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">' +
                                       '<i class="fas fa-edit"></i>' +
                                       '</button>' +
                                       '<button onclick="deleteMapel(' + mapel.id_mapel + ')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">' +
                                       '<i class="fas fa-trash"></i>' +
                                       '</button>' +
                                       '<button onclick="toggleStatus(' + mapel.id_mapel + ', \'' + mapel.status + '\')" class="px-3 py-1 ' + 
                                       (mapel.status == 'aktif' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600') + 
                                       ' text-white rounded transition-colors">' +
                                       '<i class="fas ' + (mapel.status == 'aktif' ? 'fa-eye-slash' : 'fa-eye') + '"></i>' +
                                       '</button>' +
                                       '</div>';
                    
                    data.push([
                        mapel.id_mapel,
                        mapel.nama_mapel,
                        statusBadge,
                        actionButtons
                    ]);
                });
                
                $('#mapelTable').DataTable().rows.add(data).draw();
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

// Fungsi untuk update statistik mapel
function updateStats() {
    $.ajax({
        url: base_url + 'mapel/get_data',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' || response.data) {
                let data = response.data || [];
                let totalMapel = data.length;
                let mapelAktif = 0;
                let mapelNonaktif = 0;
                
                $.each(data, function(index, mapel) {
                    if (mapel[2].includes('Aktif')) {
                        mapelAktif++;
                    } else {
                        mapelNonaktif++;
                    }
                });
                
                $('#totalMapel').text(totalMapel);
                $('#mapelAktif').text(mapelAktif);
                $('#mapelNonaktif').text(mapelNonaktif);
            }
        },
        error: function() {
            console.log('Error updating stats');
        }
    });
}