-- ============================================================
-- WhatsApp Bot Management - Database Migration
-- Tabel untuk manajemen sesi WhatsApp Bot (Baileys)
-- ============================================================

-- Tabel: whatsapp_sessions
-- Menyimpan informasi sesi WhatsApp Bot
CREATE TABLE IF NOT EXISTS `whatsapp_sessions` (
    `id`           INT(11)      NOT NULL AUTO_INCREMENT,
    `session_id`   VARCHAR(100) NOT NULL COMMENT 'ID unik sesi (digunakan di Baileys)',
    `session_name` VARCHAR(255) NOT NULL COMMENT 'Nama tampilan sesi',
    `description`  TEXT         DEFAULT NULL COMMENT 'Deskripsi sesi',
    `status`       ENUM('connected','disconnected','connecting') NOT NULL DEFAULT 'disconnected' COMMENT 'Status koneksi',
    `phone_number` VARCHAR(20)  DEFAULT NULL COMMENT 'Nomor WhatsApp yang terhubung',
    `created_by`   INT(11)      DEFAULT NULL COMMENT 'ID user yang membuat sesi',
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_session_id` (`session_id`),
    KEY `idx_status` (`status`),
    KEY `fk_ws_created_by` (`created_by`),
    CONSTRAINT `fk_ws_created_by` FOREIGN KEY (`created_by`) REFERENCES `bimbel_users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sesi WhatsApp Bot';

-- Tabel: whatsapp_message_logs
-- Menyimpan log pesan yang dikirim melalui bot
CREATE TABLE IF NOT EXISTS `whatsapp_message_logs` (
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `session_id` VARCHAR(100) NOT NULL COMMENT 'ID sesi yang digunakan',
    `receiver`   VARCHAR(30)  NOT NULL COMMENT 'Nomor penerima (format: 628xxx)',
    `message`    TEXT         NOT NULL COMMENT 'Isi pesan',
    `type`       ENUM('text','image','document','audio','video') NOT NULL DEFAULT 'text' COMMENT 'Tipe pesan',
    `status`     ENUM('sent','failed','pending') NOT NULL DEFAULT 'pending' COMMENT 'Status pengiriman',
    `sent_by`    INT(11)      DEFAULT NULL COMMENT 'ID user yang mengirim (NULL jika dari bot otomatis)',
    `sent_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_session_id` (`session_id`),
    KEY `idx_status` (`status`),
    KEY `idx_sent_at` (`sent_at`),
    KEY `fk_wml_sent_by` (`sent_by`),
    CONSTRAINT `fk_wml_sent_by` FOREIGN KEY (`sent_by`) REFERENCES `bimbel_users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log pesan WhatsApp Bot';

-- ============================================================
-- Contoh data awal (opsional)
-- ============================================================
-- INSERT INTO `whatsapp_sessions` (`session_id`, `session_name`, `description`, `status`, `created_by`)
-- VALUES ('bot_utama', 'Bot Utama Bimbel', 'Bot WhatsApp utama untuk input jurnal dan laporan', 'disconnected', 1);
