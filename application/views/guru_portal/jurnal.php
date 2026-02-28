<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Saya - Portal Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = { theme: { extend: { colors: {
            'school-green': '#22c55e', 'school-dark-green': '#16a34a',
        }}}}
    </script>
    <style>
        .nav-active { background-color: #dcfce7; color: #16a34a; font-weight: 600; }
        #jurnalTable_wrapper .dataTables_filter input { border: 1px solid #e5e7eb; border-radius: 8px; padding: 6px 12px; font-size: 13px; }
        #jurnalTable_wrapper .dataTables_length select { border: 1px solid #e5e7eb; border-radius: 8px; padding: 4px 8px; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- SIDEBAR -->
<div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full md:translate-x-0 transition-all duration-300 z-50 flex flex-col">
    <div class="bg-gradient-to-r from-school-green to-school-dark-green p-5 flex items-center gap-3">
        <i class="fas fa-chalkboard-teacher text-3xl text-white"></i>
        <div>
            <span class="font-bold text-xl text-white block">Portal Guru</span>
            <span class="text-green-100 text-xs">Sistem Prestasi</span>
        </div>
    </div>
    <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
        <a href="<?= base_url('guru_portal') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-home w-5 text-center"></i> Dashboard
        </a>
        <a href="<?= base_url('guru_portal/jurnal') ?>" class="nav-item nav-active flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all">
            <i class="fas fa-book w-5 text-center"></i> Jurnal Saya
        </a>
        <a href="<?= base_url('guru_portal/absensi') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-calendar-check w-5 text-center"></i> Absensi Saya
        </a>
        <a href="<?= base_url('guru_portal/profil') ?>" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all">
            <i class="fas fa-user w-5 text-center"></i> Profil
        </a>
    </nav>
    <div class="p-4 border-t border-gray-100">
        <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-2 text-sm text-red-500 hover:text-red-700 transition-colors px-2 py-1 rounded-lg hover:bg-red-50">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>
<div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

<!-- MAIN -->
<div class="md:ml-64 min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between sticky top-0 z-30">
        <div class="flex items-center gap-3">
            <button id="btnSidebar" class="md:hidden text-gray-500"><i class="fas fa-bars text-xl"></i></button>
            <h1 class="text-lg font-bold text-gray-800">Jurnal Saya</h1>
        </div>
        <button onclick="openModal()" class="flex items-center gap-2 bg-school-green text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-school-dark-green transition-colors">
            <i class="fas fa-plus"></i> Tambah Jurnal
        </button>
    </header>

    <main class="flex-1 p-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <p class="text-sm text-gray-500">Total: <span class="font-bold text-gray-800"><?= $total; ?></span> jurnal</p>
            </div>
            <div class="p-5 overflow-x-auto">
                <table id="jurnalTable" class="w-full text-sm" style="width:100%">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mapel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Materi</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Siswa</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Foto</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- MODAL TAMBAH/EDIT JURNAL -->
<div id="modalJurnal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Tambah Jurnal</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form id="formJurnal" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" id="id_jurnal" name="id_jurnal">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" id="tanggal" name="tanggal" max="<?= date('Y-m-d'); ?>"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                <select id="id_kelas" name="id_kelas" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green">
                    <option value="">-- Pilih Kelas --</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                <select id="id_mapel" name="id_mapel" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green">
                    <option value="">-- Pilih Mapel --</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Materi <span class="text-red-500">*</span></label>
                <textarea id="materi" name="materi" rows="3" maxlength="500"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green resize-none"
                    placeholder="Tuliskan materi yang diajarkan..."></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Siswa <span class="text-red-500">*</span></label>
                <input type="number" id="jumlah_siswa" name="jumlah_siswa" min="1"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green"
                    placeholder="Contoh: 25">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="2"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green resize-none"
                    placeholder="Keterangan tambahan (opsional)"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Bukti</label>
                <input type="file" id="foto_bukti" name="foto_bukti" accept="image/*"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-school-green">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, GIF. Maks 2MB</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-school-green text-white rounded-xl text-sm font-semibold hover:bg-school-dark-green transition-colors">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL LIHAT FOTO -->
<div id="modalFoto" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4" onclick="closeFoto()">
    <img id="fotoImg" src="" class="max-w-full max-h-full rounded-xl shadow-2xl">
</div>

<script>
const base_url = '<?= base_url(); ?>';

$(document).ready(function() {
    // Sidebar
    $('#btnSidebar').on('click', function() { $('#sidebar').removeClass('-translate-x-full'); $('#overlay').removeClass('hidden'); });
    $('#overlay').on('click', function() { $('#sidebar').addClass('-translate-x-full'); $('#overlay').addClass('hidden'); });

    // Load dropdown
    loadKelas(); loadMapel();

    // DataTable
    $('#jurnalTable').DataTable({
        ajax: { url: base_url + 'guru_portal/get_jurnal_data', type: 'GET' },
        columns: [
            { data: 0, width: '50px' },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5, className: 'text-center' },
            { data: 6, className: 'text-center', orderable: false },
            { data: 7, className: 'text-center', orderable: false },
        ],
        order: [[0, 'desc']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
        pageLength: 10,
    });

    // Submit form
    $('#formJurnal').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: base_url + 'guru_portal/save_jurnal',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    closeModal();
                    $('#jurnalTable').DataTable().ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            }
        });
    });
});

function loadKelas() {
    $.get(base_url + 'jurnal/get_kelas', function(res) {
        if (res.status === 'success') {
            res.data.forEach(function(k) {
                $('#id_kelas').append('<option value="' + k.id_kelas + '">' + k.nama_kelas + '</option>');
            });
        }
    });
}

function loadMapel() {
    $.get(base_url + 'jurnal/get_mapel', function(res) {
        if (res.status === 'success') {
            res.data.forEach(function(m) {
                $('#id_mapel').append('<option value="' + m.id_mapel + '">' + m.nama_mapel + '</option>');
            });
        }
    });
}

function openModal() {
    $('#modalTitle').text('Tambah Jurnal');
    $('#formJurnal')[0].reset();
    $('#id_jurnal').val('');
    $('#tanggal').val('<?= date('Y-m-d'); ?>');
    $('#modalJurnal').removeClass('hidden');
}

function closeModal() {
    $('#modalJurnal').addClass('hidden');
}

function editJurnal(id) {
    $.get(base_url + 'guru_portal/get_jurnal_by_id/' + id, function(res) {
        if (res.status === 'success') {
            const j = res.data;
            $('#modalTitle').text('Edit Jurnal');
            $('#id_jurnal').val(j.id_jurnal);
            $('#tanggal').val(j.tanggal);
            $('#id_kelas').val(j.id_kelas);
            $('#id_mapel').val(j.id_mapel);
            $('#materi').val(j.materi);
            $('#jumlah_siswa').val(j.jumlah_siswa);
            $('#keterangan').val(j.keterangan);
            $('#modalJurnal').removeClass('hidden');
        }
    });
}

function deleteJurnal(id) {
    Swal.fire({
        title: 'Hapus Jurnal?',
        text: 'Data jurnal akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.get(base_url + 'guru_portal/delete_jurnal/' + id, function(res) {
                if (res.status === 'success') {
                    $('#jurnalTable').DataTable().ajax.reload();
                    Swal.fire({ icon: 'success', title: 'Dihapus!', text: res.message, timer: 2000, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            });
        }
    });
}

function viewImage(foto) {
    $('#fotoImg').attr('src', base_url + 'assets/uploads/foto_kegiatan/' + foto);
    $('#modalFoto').removeClass('hidden');
}

function closeFoto() {
    $('#modalFoto').addClass('hidden');
}
</script>
</body>
</html>
