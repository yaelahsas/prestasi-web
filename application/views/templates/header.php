<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - Sistem Prestasi' : 'Sistem Prestasi'; ?></title>

    <!-- Tailwind config MUST be set before the CDN script loads -->
    <script>
        window.tailwind = { config: {
            theme: {
                extend: {
                    colors: {
                        'school-green':        '#22c55e',
                        'school-yellow':       '#facc15',
                        'school-dark-green':   '#16a34a',
                        'school-light-green':  '#86efac',
                        'school-light-yellow': '#fef3c7',
                        'school-blue':         '#3b82f6',
                        'school-purple':       '#8b5cf6',
                        'school-orange':       '#f97316',
                    },
                    animation: {
                        'fade-in':    'fadeIn 0.5s ease-in-out',
                        'slide-up':   'slideUp 0.3s ease-out',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow':'bounce 2s infinite',
                    },
                    keyframes: {
                        fadeIn:  { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { transform: 'translateY(20px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } },
                    }
                }
            }
        }};
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
    <link rel="stylesheet" href="<?= $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (isset($extra_js_head)): ?>
        <?php foreach ($extra_js_head as $js): ?>
    <script src="<?= $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        const base_url = '<?= base_url(); ?>';
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ===== GLOBAL STYLES ===== */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }
        @keyframes ripple-animation {
            to { transform: scale(4); opacity: 0; }
        }

        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #22c55e; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #16a34a; }

        /* Nav active state */
        .nav-active { background-color: #dcfce7; color: #16a34a; font-weight: 600; }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.5s ease both; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }

        /* Stat card hover */
        .stat-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.10); }

        /* Progress bar */
        .progress-bar { transition: width 1.2s ease; }

        /* Table row hover */
        .table-row:hover { background-color: #f0fdf4; }

        /* DataTable Custom Styling */
        .dataTables_wrapper { padding: 20px; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .dataTables_filter input { border: 2px solid #e5e7eb; border-radius: 8px; padding: 8px 12px; margin-left: 8px; }
        .dataTables_filter input:focus { border-color: #22c55e; outline: none; box-shadow: 0 0 0 3px rgba(34,197,94,0.1); }
        .dataTables_length select { border: 2px solid #e5e7eb; border-radius: 8px; padding: 6px 10px; }
        .dataTables_paginate .paginate_button { border-radius: 6px; margin: 0 2px; }
        .dataTables_paginate .paginate_button.current { background: #22c55e !important; color: white !important; border: 1px solid #22c55e !important; }
        .dataTables_paginate .paginate_button:hover { background: #86efac !important; color: #16a34a !important; border: 1px solid #86efac !important; }

        /* Modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); animation: fadeIn 0.3s; }
        .modal.show { display: flex; align-items: center; justify-content: center; }
        .modal-content { background-color: white; margin: auto; padding: 0; border-radius: 16px; width: 90%; max-width: 600px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); animation: slideIn 0.3s; }
        .modal-header { background: linear-gradient(to right, #22c55e, #16a34a); color: white; padding: 20px; border-radius: 16px 16px 0 0; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px; border-radius: 0 0 16px 16px; }

        /* Form */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; }
        .form-control { width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.3s; }
        .form-control:focus { border-color: #22c55e; outline: none; box-shadow: 0 0 0 3px rgba(34,197,94,0.1); }
        .error-message { color: #ef4444; font-size: 12px; margin-top: 4px; display: none; }

        /* Buttons */
        .btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(to right, #22c55e, #16a34a); color: white; }
        .btn-primary:hover { background: linear-gradient(to right, #16a34a, #15803d); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(34,197,94,0.3); }
        .btn-secondary { background: #6b7280; color: white; }
        .btn-secondary:hover { background: #4b5563; }
        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-warning { background: #f59e0b; color: white; }
        .btn-warning:hover { background: #d97706; }

        /* Spinner */
        .spinner { border: 3px solid #f3f3f3; border-top: 3px solid #22c55e; border-radius: 50%; width: 20px; height: 20px; animation: spin 1s linear infinite; display: none; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .loading .spinner { display: inline-block; }
        .loading .btn-text { display: none; }
    </style>

    <?php if (isset($extra_style)): ?>
    <style>
        <?= $extra_style; ?>
    </style>
    <?php endif; ?>
</head>

<body class="<?= isset($body_class) ? $body_class : 'bg-gray-50'; ?> min-h-screen">
<!-- Tailwind safelist: ensure gradient/color classes are always generated -->
<div class="hidden bg-gray-50 bg-gradient-to-br from-school-light-green to-white"></div>
