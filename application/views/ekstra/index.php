<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Ekstrakurikuler - Sistem Prestasi</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <script>
        const base_url = '<?= base_url(); ?>';
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

    <style>
        /* Ripple effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Smooth transitions */
        * {
            transition: all 0.3s ease;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #22c55e;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #16a34a;
        }

        /* DataTable Custom Styling */
        .dataTables_wrapper {
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dataTables_filter input {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 12px;
            margin-left: 8px;
        }

        .dataTables_filter input:focus {
            border-color: #22c55e;
            outline: none;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        .dataTables_length select {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 6px 10px;
        }

        .dataTables_paginate .paginate_button {
            border-radius: 6px;
            margin: 0 2px;
        }

        .dataTables_paginate .paginate_button.current {
            background: #22c55e !important;
            color: white !important;
            border: 1px solid #22c55e !important;
        }

        .dataTables_paginate .paginate_button:hover {
            background: #86efac !important;
            color: #16a34a !important;
            border: 1px solid #86efac !important;
        }

        /* Modal Custom Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            margin: auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(to right, #22c55e, #16a34a);
            color: white;
            padding: 20px;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-radius: 0 0 16px 16px;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #22c55e;
            outline: none;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }

        /* Button Styling */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(to right, #22c55e, #16a34a);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #16a34a, #15803d);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        /* Loading Spinner */
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #22c55e;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: none;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading .spinner {
            display: inline-block;
        }

        .loading .btn-text {
            display: none;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 640px) {
            .modal-content {
                width: 95%;
                max-height: 90vh;
                overflow-y: auto;
            }

            .modal-body {
                padding: 16px;
            }

            .modal-footer {
                flex-direction: column;
                gap: 8px;
            }

            .modal-footer .btn {
                width: 100%;
                justify-content: center;
            }

            .form-group {
                margin-bottom: 16px;
            }

            .form-control {
                padding: 8px 12px;
                font-size: 14px;
            }

            /* DataTable Mobile Styles */
            .dataTables_wrapper {
                padding: 10px;
            }

            .dataTables_length,
            .dataTables_filter {
                margin-bottom: 10px;
                font-size: 12px;
            }

            .dataTables_paginate {
                margin-top: 10px;
            }

            .dataTables_paginate .paginate_button {
                padding: 4px 8px;
                font-size: 12px;
            }

            /* Action buttons in table */
            .flex.gap-1 {
                flex-wrap: wrap;
            }

            .flex.gap-1 button {
                padding: 4px 8px;
                font-size: 12px;
            }

            /* Guru badges mobile */
            .flex.flex-wrap.gap-1 {
                gap: 4px;
            }

            .flex.flex-wrap.gap-1 span {
                font-size: 10px;
                padding: 2px 6px;
            }

            /* DataTable Responsive Styles */
            table.dataTable thead th,
            table.dataTable thead td {
                border-bottom: 2px solid #e5e7eb;
            }

            table.dataTable tbody th,
            table.dataTable tbody td {
                padding: 8px 10px;
            }

            /* Responsive child row */
            table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child:before,
            table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child:before {
                top: 8px;
                left: 8px;
            }

            /* Hide columns on mobile */
            @media (max-width: 640px) {
                .hidden-mobile {
                    display: none !important;
                }
            }

            /* Teacher count button mobile improvements */
            button[onclick^="showGuruDetails"] {
                padding: 8px 16px;
                font-size: 12px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            button[onclick^="showGuruDetails"]:hover {
                transform: scale(1.05);
            }

            button[onclick^="showGuruDetails"]:active {
                transform: scale(0.95);
            }

            /* Improved mobile table layout */
            table.dataTable tbody td {
                vertical-align: middle;
            }

            /* Better spacing for mobile table rows */
            table.dataTable tbody tr {
                border-bottom: 1px solid #e5e7eb;
            }

            table.dataTable tbody tr:last-child {
                border-bottom: none;
            }

            /* Compact action buttons on mobile */
            .flex.gap-1 button {
                min-width: 32px;
                min-height: 32px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            /* Status badge mobile optimization */
            table.dataTable tbody td:nth-child(4) span {
                display: inline-block;
                padding: 4px 8px;
                font-size: 11px;
                border-radius: 9999px;
            }

            /* Teacher count button mobile optimization */
            table.dataTable tbody td:nth-child(3) button {
                width: 100%;
                justify-content: center;
                min-height: 36px;
            }

            /* Table header improvements */
            table.dataTable thead th {
                font-size: 11px;
                padding: 8px 6px;
                text-align: left;
            }

            /* Better table cell spacing */
            table.dataTable tbody td {
                padding: 10px 6px;
            }

            /* Name column optimization */
            table.dataTable tbody td:nth-child(2) {
                font-weight: 500;
                color: #1f2937;
            }

            /* Action column centering */
            table.dataTable tbody td:last-child {
                text-align: center;
            }

            /* Responsive table container */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Hide horizontal scrollbar */
            .table-responsive::-webkit-scrollbar {
                height: 4px;
            }

            .table-responsive::-webkit-scrollbar-thumb {
                background: #22c55e;
                border-radius: 4px;
            }

            /* DataTable Responsive Child Row Styling */
            table.dataTable tbody > tr.child {
                background-color: #f9fafb;
            }

            table.dataTable tbody > tr.child td {
                padding: 8px 12px;
                border-bottom: 1px solid #e5e7eb;
            }

            table.dataTable tbody > tr.child td:first-child {
                font-weight: 600;
                color: #374151;
                background-color: #f3f4f6;
                width: 40%;
            }

            table.dataTable tbody > tr.child td:last-child {
                color: #6b7280;
            }

            /* Control icon for responsive table */
            table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child:before {
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                margin-top: 0;
                margin-left: 0;
                width: 20px;
                height: 20px;
                line-height: 20px;
                border-radius: 50%;
                text-align: center;
                font-size: 12px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child {
                position: relative;
                padding-left: 30px;
            }

            /* Better mobile table visibility */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                display: block;
                width: 100%;
                margin-bottom: 8px;
            }

            .dataTables_wrapper .dataTables_filter {
                text-align: right;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 150px;
                display: inline-block;
            }

            /* Pagination improvements */
            .dataTables_wrapper .dataTables_paginate {
                display: flex;
                justify-content: center;
                gap: 4px;
                flex-wrap: wrap;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button {
                margin: 2px;
            }

            /* Responsive table child row table styling */
            table.dataTable tbody > tr.child table {
                width: 100%;
                border-collapse: collapse;
            }

            table.dataTable tbody > tr.child table tr {
                border-bottom: 1px solid #e5e7eb;
            }

            table.dataTable tbody > tr.child table tr:last-child {
                border-bottom: none;
            }

            table.dataTable tbody > tr.child table td {
                padding: 8px 12px;
                vertical-align: top;
            }

            table.dataTable tbody > tr.child table td:first-child {
                font-weight: 600;
                color: #374151;
                background-color: #f3f4f6;
                width: 40%;
            }

            table.dataTable tbody > tr.child table td:last-child {
                color: #6b7280;
            }

            /* Action buttons in child row */
            table.dataTable tbody > tr.child table td:last-child .flex.gap-1 {
                flex-wrap: wrap;
                gap: 4px;
            }

            table.dataTable tbody > tr.child table td:last-child .flex.gap-1 button {
                padding: 6px 12px;
                font-size: 12px;
            }

            /* Status badge in child row */
            table.dataTable tbody > tr.child table td:last-child span {
                display: inline-block;
                padding: 4px 8px;
                font-size: 11px;
                border-radius: 9999px;
            }

            /* Teacher count button in child row */
            table.dataTable tbody > tr.child table td:last-child button[onclick^="showGuruDetails"] {
                width: 100%;
                justify-content: center;
                min-height: 36px;
                margin-top: 4px;
            }
        }

        @media (max-width: 480px) {
            /* Extra small screens */
            .stat-card {
                padding: 12px;
            }

            .stat-card h2 {
                font-size: 1.5rem;
            }

            .modal-content {
                width: 98%;
                margin: 10px;
            }

            .modal-header {
                padding: 12px 16px;
            }

            .modal-header h3 {
                font-size: 1rem;
            }

            .modal-body {
                padding: 12px;
            }

            .modal-footer {
                padding: 12px 16px;
            }

            /* Guru details modal mobile improvements */
            #guruDetailsModal .modal-content {
                max-height: 85vh;
                overflow-y: auto;
            }

            #guruDetailsModal .space-y-3 > div {
                padding: 12px;
            }

            #guruDetailsModal .w-10 {
                width: 40px;
                height: 40px;
            }

            /* Teacher count button mobile */
            button[onclick^="showGuruDetails"] {
                padding: 6px 12px;
                font-size: 11px;
            }

            button[onclick^="showGuruDetails"] i {
                font-size: 10px;
            }

            /* Extra small screen table improvements */
            .dataTables_wrapper {
                padding: 8px;
            }

            table.dataTable thead th {
                font-size: 10px;
                padding: 6px 4px;
            }

            table.dataTable tbody td {
                font-size: 11px;
                padding: 6px 4px;
            }

            /* Compact action buttons */
            .flex.gap-1 button {
                padding: 3px 6px;
                font-size: 10px;
                min-width: 28px;
                min-height: 28px;
            }

            /* Status badge compact */
            table.dataTable tbody td:nth-child(4) span {
                padding: 3px 6px;
                font-size: 10px;
            }

            /* Teacher count button compact */
            table.dataTable tbody td:nth-child(3) button {
                padding: 4px 8px;
                font-size: 10px;
                min-height: 28px;
            }

            table.dataTable tbody td:nth-child(3) button i {
                font-size: 9px;
            }
        }

        /* Touch-friendly button sizes */
        @media (hover: none) and (pointer: coarse) {
            button[onclick^="showGuruDetails"] {
                min-height: 44px;
                min-width: 44px;
            }

            .btn {
                min-height: 44px;
            }

            /* Make table action buttons touch-friendly */
            table.dataTable tbody .flex.gap-1 button {
                min-height: 40px;
                min-width: 40px;
            }
        }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'school-green': '#22c55e',
                        'school-yellow': '#facc15',
                        'school-dark-green': '#16a34a',
                        'school-light-green': '#86efac',
                        'school-light-yellow': '#fef3c7'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 2s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            },
                        },
                        slideUp: {
                            '0%': {
                                transform: 'translateY(20px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            },
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gradient-to-br from-school-light-green to-white min-h-screen">

    <!-- ===== SIDEBAR ===== -->
    <div id="sidebar"
        class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out z-50 md:shadow-2xl flex flex-col">

        <div class="bg-gradient-to-r from-school-green to-school-dark-green p-5 flex items-center gap-3 shadow-md">
            <i class="fas fa-graduation-cap text-3xl text-white animate-pulse-slow"></i>
            <span class="font-bold text-xl text-white">Sistem Prestasi</span>
        </div>

        <nav class="p-4 space-y-2 flex-1 overflow-y-auto">
            <a href="<?= base_url('dashboard') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-home w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="<?= base_url('jurnal') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-book w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Jurnal</span>
            </a>

            <a href="<?= base_url('guru') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-chalkboard-teacher w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Guru</span>
            </a>

            <a href="<?= base_url('kelas') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-school w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Kelas</span>
            </a>

            <a href="<?= base_url('mapel') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-book-open w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Mata Pelajaran</span>
            </a>

            <a href="<?= base_url('ekstra') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg bg-school-light-green text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-star w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Ekstrakurikuler</span>
            </a>

            <a href="<?= base_url('users') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-users w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Pengguna</span>
            </a>

            <a href="<?= base_url('sekolah') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-school w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Sekolah</span>
            </a>

            <a href="<?= base_url('laporan') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-file-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Laporan</span>
            </a>

            <a href="<?= base_url('whatsapp') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fab fa-whatsapp w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">WhatsApp Bot</span>
            </a>
            <a href="<?= base_url('billing') ?>" class="nav-item flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-school-light-green hover:text-school-dark-green transition-all duration-200 group">
                <i class="fas fa-file-invoice-dollar w-5 text-center group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Billing</span>
            </a>

            <div class="pt-4 mt-4 border-t border-gray-200">
                <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-3 p-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200 group">
                    <i class="fas fa-sign-out-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </nav>

        <!-- Sidebar footer -->
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-school-green to-school-dark-green flex items-center justify-center text-white font-bold text-sm">
                    <?= strtoupper(substr($user['nama'], 0, 1)); ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate"><?= $user['nama']; ?></p>
                    <p class="text-xs text-gray-500 capitalize"><?= $user['role']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- overlay mobile -->
    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>

    <!-- ===== MAIN ===== -->
    <div class="md:ml-64 min-h-screen">

        <!-- TOPBAR -->
        <header class="bg-white shadow-md flex items-center justify-between px-4 h-16 sticky top-0 z-40 backdrop-blur-lg bg-opacity-95">

            <div class="flex items-center gap-4">
                <button id="btnSidebar" class="md:hidden text-2xl text-school-green hover:text-school-dark-green transition-colors">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="text-xl font-semibold text-gray-800 hidden sm:block">Data Ekstrakurikuler</h2>
            </div>

            <div class="flex items-center gap-4">
                <button id="refreshBtn" class="p-2 rounded-full bg-school-light-green hover:bg-school-green text-school-dark-green hover:text-white transition-all duration-200 group">
                    <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-500"></i>
                </button>
                <div class="text-sm text-gray-700 bg-school-light-yellow px-3 py-1 rounded-full">
                    <i class="fas fa-user-circle text-school-green mr-1"></i>
                    <span class="font-medium"><?= $user['nama']; ?></span>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="p-4">

            <!-- Page Title -->
            <div class="mb-6 animate-fade-in">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-star text-school-green"></i>
                    Data Ekstrakurikuler
                </h1>
                <p class="text-gray-600 mt-1">Kelola data kegiatan ekstrakurikuler</p>
            </div>

            <!-- STAT CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

                <!-- Total Ekstra -->
                <div class="stat-card bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs text-gray-600 font-medium flex items-center gap-2 mb-1">
                                <i class="fas fa-star text-school-green text-sm"></i>
                                Total Ekstra
                            </p>
                            <h2 class="text-2xl font-bold text-gray-800" id="totalEkstra">
                                <?= $total_ekstra; ?>
                            </h2>
                            <div class="mt-1 flex items-center text-xs text-green-600">
                                <i class="fas fa-star mr-1"></i>
                                <span>Semua status</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-md ml-3 flex-shrink-0">
                            <i class="fas fa-star text-white text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- Ekstra Aktif -->
                <div class="stat-card bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green" style="animation-delay: 0.1s">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs text-gray-600 font-medium flex items-center gap-2 mb-1">
                                <i class="fas fa-check-circle text-school-green text-sm"></i>
                                Ekstra Aktif
                            </p>
                            <h2 class="text-2xl font-bold text-gray-800" id="ekstraAktif">
                                0
                            </h2>
                            <div class="mt-1 flex items-center text-xs text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                <span>Sedang berjalan</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-school-light-green to-school-green flex items-center justify-center shadow-md ml-3 flex-shrink-0">
                            <i class="fas fa-check-circle text-white text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- Ekstra Nonaktif -->
                <div class="stat-card bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-yellow" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs text-gray-600 font-medium flex items-center gap-2 mb-1">
                                <i class="fas fa-pause-circle text-school-yellow text-sm"></i>
                                Ekstra Nonaktif
                            </p>
                            <h2 class="text-2xl font-bold text-gray-800" id="ekstraNonaktif">
                                0
                            </h2>
                            <div class="mt-1 flex items-center text-xs text-yellow-600">
                                <i class="fas fa-pause-circle mr-1"></i>
                                <span>Tidak berjalan</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-school-light-yellow to-school-yellow flex items-center justify-center shadow-md ml-3 flex-shrink-0">
                            <i class="fas fa-pause-circle text-white text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- Search Box -->
                <div class="stat-card bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 animate-slide-up border-l-4 border-school-green" style="animation-delay: 0.3s">
                    <div class="flex items-center justify-between">
                        <div class="w-full">
                            <p class="text-xs text-gray-600 font-medium flex items-center gap-2 mb-2">
                                <i class="fas fa-search text-school-green text-sm"></i>
                                Pencarian
                            </p>
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Cari ekstrakurikuler..."
                                    class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:border-school-green text-sm">
                                <button id="searchBtn" class="absolute right-2 top-2 text-school-green hover:text-school-dark-green">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex flex-wrap gap-2 mb-6 animate-slide-up" style="animation-delay: 0.4s">
                <button onclick="openModal()" class="btn btn-primary text-sm sm:text-base">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Tambah Ekstrakurikuler</span>
                    <span class="sm:hidden">Tambah</span>
                </button>
                <button onclick="refreshTable()" class="btn btn-success text-sm sm:text-base">
                    <i class="fas fa-sync-alt"></i>
                    <span class="hidden sm:inline">Refresh Data</span>
                    <span class="sm:hidden">Refresh</span>
                </button>
            </div>

            <!-- DATA TABLE -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-slide-up" style="animation-delay: 0.5s">
                <div class="p-3 sm:p-4 border-b border-gray-200">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-table text-school-green text-sm sm:text-base"></i>
                        <span class="hidden sm:inline">Tabel Data Ekstrakurikuler</span>
                        <span class="sm:hidden">Data Ekstra</span>
                    </h3>
                </div>
                <div class="table-responsive overflow-x-auto">
                    <table id="ekstraTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-xs sm:text-sm text-center" style="width: 50px;">No</th>
                                <th class="text-xs sm:text-sm">Nama Ekstrakurikuler</th>
                                <th class="text-xs sm:text-sm text-center" style="width: 120px;">Jml Pembina</th>
                                <th class="text-xs sm:text-sm text-center" style="width: 100px;">Status</th>
                                <th class="text-xs sm:text-sm text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

    </div>

    <!-- Modal Form -->
    <div id="ekstraModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-lg sm:text-xl font-bold" id="modalTitle">Tambah Ekstrakurikuler</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeModal()">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>
            <form id="ekstraForm">
                <div class="modal-body">
                    <input type="hidden" id="id_ekstra" name="id_ekstra">

                    <div class="form-group">
                        <label for="nama_ekstra" class="text-sm sm:text-base">Nama Ekstrakurikuler <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_ekstra" name="nama_ekstra" class="form-control text-sm sm:text-base" required>
                        <div class="error-message" id="nama_ekstra_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="guru_ids" class="text-sm sm:text-base">Pembina <span class="text-red-500">*</span></label>
                        <select id="guru_ids" name="guru_ids[]" class="form-control text-sm sm:text-base" multiple>
                        </select>
                        <small class="text-gray-500 text-xs">Pilih satu atau lebih guru</small>
                        <div class="error-message" id="guru_ids_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi" class="text-sm sm:text-base">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control text-sm sm:text-base" rows="3"></textarea>
                        <div class="error-message" id="deskripsi_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="status" class="text-sm sm:text-base">Status <span class="text-red-500">*</span></label>
                        <select id="status" name="status" class="form-control text-sm sm:text-base" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                        <div class="error-message" id="status_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-sm sm:text-base" onclick="closeModal()">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </button>
                    <button type="submit" class="btn btn-primary text-sm sm:text-base" id="saveBtn">
                        <div class="spinner"></div>
                        <span class="btn-text">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Guru Details -->
    <div id="guruDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-lg sm:text-xl font-bold" id="guruModalTitle">Detail Pembina</h3>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeGuruModal()">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="guruDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary text-sm sm:text-base" onclick="closeGuruModal()">
                    <i class="fas fa-times"></i>
                    <span>Tutup</span>
                </button>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/ekstra/ekstra.js'); ?>"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>