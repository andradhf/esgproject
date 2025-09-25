-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Sep 2025 pada 14.56
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_esgsyariah`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_governance`
--

CREATE TABLE `data_governance` (
  `id` int(11) NOT NULL,
  `umkm_id` int(11) DEFAULT NULL,
  `legalitas` varchar(100) DEFAULT NULL,
  `kepatuhan_syariah` varchar(100) DEFAULT NULL,
  `transparansi` varchar(100) DEFAULT NULL,
  `integritas` varchar(100) DEFAULT NULL,
  `maqasid_legalitas` varchar(255) DEFAULT NULL,
  `maqasid_syariah` varchar(255) DEFAULT NULL,
  `maqasid_transparansi` varchar(255) DEFAULT NULL,
  `maqasid_integritas` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_governance`
--

INSERT INTO `data_governance` (`id`, `umkm_id`, `legalitas`, `kepatuhan_syariah`, `transparansi`, `integritas`, `maqasid_legalitas`, `maqasid_syariah`, `maqasid_transparansi`, `maqasid_integritas`, `created_at`) VALUES
(5, 1, '1', '1', '1', '1', 'Hifz al-Din', 'Hifz al-Din', 'Hifz al-Mal', 'Hifz al-Mal', '2025-09-25 12:14:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_keuangan`
--

CREATE TABLE `data_keuangan` (
  `id` int(11) NOT NULL,
  `umkm_id` int(11) DEFAULT NULL,
  `pendapatan_halal` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ziswaf` decimal(15,2) DEFAULT 0.00,
  `pembiayaan` varchar(100) DEFAULT NULL,
  `maqasid_pendapatan` varchar(255) DEFAULT NULL,
  `maqasid_ziswaf` varchar(255) DEFAULT NULL,
  `maqasid_pembiayaan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_keuangan`
--

INSERT INTO `data_keuangan` (`id`, `umkm_id`, `pendapatan_halal`, `created_at`, `ziswaf`, `pembiayaan`, `maqasid_pendapatan`, `maqasid_ziswaf`, `maqasid_pembiayaan`) VALUES
(1, NULL, 0.00, '2025-09-25 12:29:18', 1.00, 'Murabahah', 'Hifz al-Din', 'Hifz al-Din', 'Hifz al-Mal');

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_sosial`
--

CREATE TABLE `data_sosial` (
  `id` int(11) NOT NULL,
  `umkm_id` int(11) DEFAULT NULL,
  `karyawan` int(11) DEFAULT NULL,
  `karyawan_perempuan` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `insiden_k3` int(11) DEFAULT NULL,
  `kejadian` varchar(255) DEFAULT NULL,
  `pelatihan_sdm` int(11) DEFAULT NULL,
  `produk_halal` varchar(10) DEFAULT NULL,
  `csr` varchar(10) DEFAULT NULL,
  `ziswaf` bigint(20) DEFAULT NULL,
  `maqasid_tenaga` varchar(255) DEFAULT NULL,
  `maqasid_k3` varchar(255) DEFAULT NULL,
  `maqasid_sdm` varchar(255) DEFAULT NULL,
  `maqasid_produk` varchar(255) DEFAULT NULL,
  `maqasid_sosial` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_sosial`
--

INSERT INTO `data_sosial` (`id`, `umkm_id`, `karyawan`, `karyawan_perempuan`, `created_at`, `insiden_k3`, `kejadian`, `pelatihan_sdm`, `produk_halal`, `csr`, `ziswaf`, `maqasid_tenaga`, `maqasid_k3`, `maqasid_sdm`, `maqasid_produk`, `maqasid_sosial`) VALUES
(70, 1, -1, -1, '2025-09-25 11:24:53', -1, 'fff', -2, 'Ya', 'Ya', -1, 'Hifz al-Nafs', 'Hifz al-Aql', 'Hifz al-Aql', 'Hifz al-Din', 'Hifz al-Nasl'),
(71, 1, 1, -1, '2025-09-25 12:00:29', 1, 'qww', -1, 'Ya', 'Ya', -1, 'Hifz al-Nafs', 'Hifz al-Aql', 'Hifz al-Aql', 'Hifz al-Din', 'Hifz al-Mal');

-- --------------------------------------------------------

--
-- Struktur dari tabel `environmental`
--

CREATE TABLE `environmental` (
  `id` int(11) NOT NULL,
  `umkm_id` int(11) DEFAULT NULL,
  `listrik` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `air` decimal(10,2) DEFAULT 0.00,
  `limbah` decimal(10,2) DEFAULT 0.00,
  `bahan_baku` decimal(5,2) DEFAULT 0.00,
  `hifzmal` tinyint(1) DEFAULT 0,
  `hifzmal2` tinyint(1) DEFAULT 0,
  `hifznafs` tinyint(1) DEFAULT 0,
  `hifzdin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `environmental`
--

INSERT INTO `environmental` (`id`, `umkm_id`, `listrik`, `created_at`, `air`, `limbah`, `bahan_baku`, `hifzmal`, `hifzmal2`, `hifznafs`, `hifzdin`) VALUES
(14, 3, 1.00, '2025-09-25 09:47:35', -1.00, 1.00, 1.00, 0, 0, 0, 0),
(16, 3, 66.00, '2025-09-25 10:08:35', 55.00, 44.00, 999.99, 0, 0, 0, 0),
(17, 3, 66.00, '2025-09-25 10:16:22', 55.00, 44.00, 999.99, 0, 0, 0, 0),
(18, 3, 66.00, '2025-09-25 10:30:32', 55.00, 44.00, 44.00, 0, 0, 0, 0),
(19, 3, 66.00, '2025-09-25 10:50:34', 55.00, 22.00, 999.99, 0, 0, 0, 0),
(24, 3, 66.00, '2025-09-25 12:35:47', 55.00, 44.00, 44.00, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_esg`
--

CREATE TABLE `laporan_esg` (
  `id` int(11) NOT NULL,
  `umkm_id` int(11) DEFAULT NULL,
  `periode` varchar(50) DEFAULT NULL,
  `ringkasan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `maqasid_syariah`
--

CREATE TABLE `maqasid_syariah` (
  `id` int(11) NOT NULL,
  `umkm_id` int(11) DEFAULT NULL,
  `indikator` varchar(100) DEFAULT NULL,
  `skor` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `umkm_accounts`
--

CREATE TABLE `umkm_accounts` (
  `id` int(11) NOT NULL,
  `nama_umkm` varchar(150) NOT NULL,
  `nama_pemilik` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `umkm_accounts`
--

INSERT INTO `umkm_accounts` (`id`, `nama_umkm`, `nama_pemilik`, `email`, `no_telepon`, `alamat`, `created_at`) VALUES
(1, 'alam indra', 'alam indra', 'alam@gmail.com', '', '', '2025-09-25 03:48:11'),
(2, 'alal al', 'alal al', 'al@gmail.com', '', '', '2025-09-25 05:47:20'),
(3, 'aku alam', 'aku alam', 'aku@gmail.com', '', '', '2025-09-25 08:56:20'),
(4, 'aaaa aaaa', 'aaaa aaaa', 'a@gmail', '', '', '2025-09-25 09:10:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_accounts`
--

CREATE TABLE `user_accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user_accounts`
--

INSERT INTO `user_accounts` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'alam760', '$2y$10$5scx8P/hPhNA56Q24.O6U..Zys3RgTHJBDtzxyXTKrCclOcQjirCe', 'alam@gmail.com', 'user', '2025-09-25 03:48:11'),
(2, 'admin123', '$2y$10$w0bQwnXjC2sZ0Rj4u7mJ5Ox1f6LJY7cJbz5aH5lE9fTtI5bA1x0L6', 'admin@esg.com', 'admin', '2025-09-25 03:52:41'),
(3, 'alal511', '$2y$10$e8I0up8sOL1P.zGhprhgUeza6mBl/xzMMiVwkrex.mIw0a5O/EZEa', 'al@gmail.com', 'user', '2025-09-25 05:47:20'),
(4, 'aku120', '$2y$10$LhAeVt3HbSpfVBtD74tLPeE1xUctx2Usxt4SJfg00BxZMXJeIH9MS', 'aku@gmail.com', 'user', '2025-09-25 08:56:20'),
(5, 'aaaa254', '$2y$10$czvbGiAXG1m5Z28au9LNhOPmnrOoIP7Ru/2AaeFw4HgSY7zXfn2Na', 'a@gmail', 'user', '2025-09-25 09:10:03');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `data_governance`
--
ALTER TABLE `data_governance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `umkm_id` (`umkm_id`);

--
-- Indeks untuk tabel `data_keuangan`
--
ALTER TABLE `data_keuangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `umkm_id` (`umkm_id`);

--
-- Indeks untuk tabel `data_sosial`
--
ALTER TABLE `data_sosial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `umkm_id` (`umkm_id`);

--
-- Indeks untuk tabel `environmental`
--
ALTER TABLE `environmental`
  ADD PRIMARY KEY (`id`),
  ADD KEY `umkm_id` (`umkm_id`);

--
-- Indeks untuk tabel `laporan_esg`
--
ALTER TABLE `laporan_esg`
  ADD PRIMARY KEY (`id`),
  ADD KEY `umkm_id` (`umkm_id`);

--
-- Indeks untuk tabel `maqasid_syariah`
--
ALTER TABLE `maqasid_syariah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `umkm_id` (`umkm_id`);

--
-- Indeks untuk tabel `umkm_accounts`
--
ALTER TABLE `umkm_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `user_accounts`
--
ALTER TABLE `user_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `data_governance`
--
ALTER TABLE `data_governance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `data_keuangan`
--
ALTER TABLE `data_keuangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `data_sosial`
--
ALTER TABLE `data_sosial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT untuk tabel `environmental`
--
ALTER TABLE `environmental`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `laporan_esg`
--
ALTER TABLE `laporan_esg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `maqasid_syariah`
--
ALTER TABLE `maqasid_syariah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `umkm_accounts`
--
ALTER TABLE `umkm_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `user_accounts`
--
ALTER TABLE `user_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `data_governance`
--
ALTER TABLE `data_governance`
  ADD CONSTRAINT `data_governance_ibfk_1` FOREIGN KEY (`umkm_id`) REFERENCES `umkm_accounts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `data_keuangan`
--
ALTER TABLE `data_keuangan`
  ADD CONSTRAINT `data_keuangan_ibfk_1` FOREIGN KEY (`umkm_id`) REFERENCES `umkm_accounts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `data_sosial`
--
ALTER TABLE `data_sosial`
  ADD CONSTRAINT `data_sosial_ibfk_1` FOREIGN KEY (`umkm_id`) REFERENCES `umkm_accounts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `environmental`
--
ALTER TABLE `environmental`
  ADD CONSTRAINT `environmental_ibfk_1` FOREIGN KEY (`umkm_id`) REFERENCES `umkm_accounts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `laporan_esg`
--
ALTER TABLE `laporan_esg`
  ADD CONSTRAINT `laporan_esg_ibfk_1` FOREIGN KEY (`umkm_id`) REFERENCES `umkm_accounts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `maqasid_syariah`
--
ALTER TABLE `maqasid_syariah`
  ADD CONSTRAINT `maqasid_syariah_ibfk_1` FOREIGN KEY (`umkm_id`) REFERENCES `umkm_accounts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
