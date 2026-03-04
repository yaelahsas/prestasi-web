-- ============================================================
-- Tabel: whatsapp_bot_settings
-- Deskripsi: Menyimpan konfigurasi ENV untuk WhatsApp Bot (Baileys)
-- ============================================================

CREATE TABLE IF NOT EXISTS `bimbel_whatsapp_bot_settings` (
    `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key`   VARCHAR(100)     NOT NULL,
    `setting_value` TEXT             DEFAULT NULL,
    `description`   VARCHAR(255)     DEFAULT NULL,
    `created_at`    DATETIME         DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME         DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Data default pengaturan bot
-- ============================================================

INSERT INTO `bimbel_whatsapp_bot_settings` (`setting_key`, `setting_value`, `description`) VALUES
('BAILEYS_API_URL',             'http://localhost:3000',     'URL server Baileys API (Node.js)'),
('APP_PORT',                    '3000',                      'Port server Baileys berjalan'),
('MAX_RETRIES',                 '0',                         'Jumlah maksimal percobaan reconnect (-1 = unlimited, 0 = tidak reconnect)'),
('RECONNECT_INTERVAL',          '5000',                      'Interval reconnect dalam milidetik'),
('AUTO_READ_MESSAGES',          'false',                     'Otomatis tandai pesan sebagai dibaca (true/false)'),
('APP_WEBHOOK_URL',             '',                          'URL webhook untuk menerima event dari Baileys'),
('APP_WEBHOOK_ALLOWED_EVENTS',  'ALL',                       'Event yang dikirim ke webhook (ALL atau pisahkan dengan koma: MESSAGES_UPSERT,CONNECTION_UPDATE)'),
('APP_WEBHOOK_FILE_IN_BASE64',  'false',                     'Kirim file media sebagai base64 ke webhook (true/false)'),
('BOT_API_URL',                 'http://localhost:9998/api', 'URL API backend untuk perintah bot (#jurnal, #laporan)'),
('BOT_API_KEY',                 'whatsapp_bot_key_2024',     'API Key untuk autentikasi ke backend API'),
('BOT_AUTHORIZED_NUMBERS',      '',                          'Nomor HP yang diizinkan menggunakan perintah bot (pisahkan dengan koma, contoh: 6281234567890,6289876543210)')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);
