<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Billing Guru - <?= $billing->kode_billing ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 12px;
            color: #666;
        }
        
        .title {
            text-align: center;
            margin: 30px 0;
            padding: 15px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }
        
        .title h3 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .info-section h4 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            background: #f0f0f0;
            padding: 8px;
            border-left: 4px solid #22c55e;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .info-item {
            padding: 8px;
            background: #fafafa;
            border: 1px solid #eee;
        }
        
        .info-item label {
            font-weight: bold;
            display: block;
            margin-bottom: 3px;
            color: #555;
        }
        
        .info-item span {
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background: #22c55e;
            color: white;
        }
        
        table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #1a8f4a;
        }
        
        table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .total-section {
            margin-top: 20px;
            padding: 15px;
            background: #f0fdf4;
            border: 2px solid #22c55e;
            border-radius: 5px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .total-row.grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #22c55e;
            border-top: 2px solid #22c55e;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-selesai {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-diproses {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-dibayar {
            background: #f3e8ff;
            color: #7c3aed;
        }
        
        .status-draft {
            background: #f3f4f6;
            color: #4b5563;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-box h5 {
            font-size: 12px;
            margin-bottom: 60px;
        }
        
        .signature-box p {
            font-size: 11px;
            margin-bottom: 5px;
        }
        
        .signature-box .name {
            font-weight: bold;
            font-size: 12px;
            text-decoration: underline;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><?= $sekolah->nama_sekolah ?? 'Sistem Prestasi' ?></h1>
            <h2><?= $sekolah->alamat ?? '' ?></h2>
            <p><?= $sekolah->telepon ?? '' ?> | <?= $sekolah->email ?? '' ?></p>
        </div>
        
        <!-- Title -->
        <div class="title">
            <h3>Billing Honor Guru</h3>
            <p>Kode: <?= $billing->kode_billing ?></p>
        </div>
        
        <!-- Info Guru -->
        <div class="info-section">
            <h4>Informasi Guru</h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Nama Guru:</label>
                    <span><?= $billing->nama_guru ?></span>
                </div>
                <div class="info-item">
                    <label>NIP:</label>
                    <span><?= $billing->nip ?: '-' ?></span>
                </div>
                <div class="info-item">
                    <label>Telepon:</label>
                    <span><?= $billing->telepon ?: '-' ?></span>
                </div>
                <div class="info-item">
                    <label>Status:</label>
                    <span class="status-badge status-<?= $billing->status ?>"><?= ucfirst($billing->status) ?></span>
                </div>
            </div>
        </div>
        
        <!-- Info Periode -->
        <div class="info-section">
            <h4>Informasi Periode</h4>
            <div class="info-grid">
                <div class="info-item">
                    <label>Periode:</label>
                    <span><?= $billing->nama_period ?></span>
                </div>
                <div class="info-item">
                    <label>Tanggal:</label>
                    <span><?= date('d/m/Y', strtotime($billing->tanggal_mulai)) ?> - <?= date('d/m/Y', strtotime($billing->tanggal_selesai)) ?></span>
                </div>
                <div class="info-item">
                    <label>Total Jurnal:</label>
                    <span><?= $billing->total_jurnal ?> Jurnal</span>
                </div>
                <div class="info-item">
                    <label>Tanggal Generate:</label>
                    <span><?= date('d/m/Y H:i', strtotime($billing->created_at)) ?></span>
                </div>
            </div>
        </div>
        
        <!-- Detail Billing -->
        <div class="info-section">
            <h4>Rincian Honor per Jenis Kegiatan</h4>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Kegiatan</th>
                        <th>Jumlah Jurnal</th>
                        <th>Tarif per Jurnal</th>
                        <th>Subtotal Honor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($details as $detail): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= ucfirst($detail->jenis_kegiatan) ?></td>
                        <td><?= $detail->jumlah_jurnal ?></td>
                        <td>Rp <?= number_format($detail->tarif_per_jurnal, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($detail->subtotal_honor, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Total Honor -->
        <div class="total-section">
            <div class="total-row">
                <span>Total Jurnal:</span>
                <span><?= $billing->total_jurnal ?> Jurnal</span>
            </div>
            <div class="total-row grand-total">
                <span>Total Honor:</span>
                <span>Rp <?= number_format($billing->total_honor, 0, ',', '.') ?></span>
            </div>
        </div>
        
        <!-- Daftar Jurnal -->
        <div class="info-section">
            <h4>Daftar Jurnal yang Dihitung</h4>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Materi</th>
                        <th>Jenis Kegiatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($jurnals as $jurnal): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($jurnal->tanggal)) ?></td>
                        <td><?= $jurnal->nama_kelas ?></td>
                        <td><?= $jurnal->nama_mapel ?></td>
                        <td><?= $jurnal->materi ?: '-' ?></td>
                        <td><?= ucfirst($jurnal->jenis_kegiatan) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <h5>Mengetahui,</h5>
                <p>Kepala Sekolah</p>
                <br><br><br>
                <p class="name"><?= $sekolah->kepala_sekolah ?? '________________' ?></p>
                <p>NIP. <?= $sekolah->nip_kepsek ?? '________________' ?></p>
            </div>
            <div class="signature-box">
                <h5><?= date('d/m/Y') ?></h5>
                <p>Guru Yang Bersangkutan</p>
                <br><br><br>
                <p class="name"><?= $billing->nama_guru ?></p>
                <p>NIP. <?= $billing->nip ?: '________________' ?></p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini diterbitkan secara otomatis oleh Sistem Prestasi</p>
            <p>Tanggal Cetak: <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>
</html>
