-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2026 at 07:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `showroom_mobil`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `id_user`, `nama`, `alamat`, `no_hp`, `foto`) VALUES
(1, 18, 'Mahesa Ibrahim', 'JL ARIA SANTIKA GG SAMAUN RT 3 RW 3', '089627912778', '1778812497_profil_18.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `administrasi_kendaraan`
--

CREATE TABLE `administrasi_kendaraan` (
  `id_administrasi` int(11) NOT NULL,
  `id_mobil` int(11) NOT NULL,
  `no_stnk` varchar(100) DEFAULT NULL,
  `tanggal_stnk` date DEFAULT NULL,
  `status_stnk` enum('aktif','hampir_habis','mati') DEFAULT 'aktif',
  `no_bpkb` varchar(100) DEFAULT NULL,
  `status_bpkb` enum('ada','proses','belum_ada') DEFAULT 'belum_ada',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrasi_kendaraan`
--

INSERT INTO `administrasi_kendaraan` (`id_administrasi`, `id_mobil`, `no_stnk`, `tanggal_stnk`, `status_stnk`, `no_bpkb`, `status_bpkb`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 3, '21313212312', '2027-06-30', 'aktif', '12313123123', 'ada', 'anjay gurinjay makan bajay', '2026-05-16 15:14:24', '2026-05-16 15:14:24');

-- --------------------------------------------------------

--
-- Table structure for table `kurir`
--

CREATE TABLE `kurir` (
  `id_kurir` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kurir`
--

INSERT INTO `kurir` (`id_kurir`, `id_user`, `nama`, `alamat`, `no_hp`, `foto`) VALUES
(1, 20, 'Arjuna Meiureksa', 'Jl wisma mas bumi indah blok f no 22', '087527275275', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kwitansi`
--

CREATE TABLE `kwitansi` (
  `id_kwitansi` int(11) NOT NULL,
  `id_pembayaran` int(11) DEFAULT NULL,
  `tanggal_cetak` datetime DEFAULT NULL,
  `total` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL,
  `periode` varchar(30) DEFAULT NULL,
  `total_penjualan` decimal(15,2) DEFAULT NULL,
  `total_pendapatan` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mobil`
--

CREATE TABLE `mobil` (
  `id_mobil` int(11) NOT NULL,
  `id_penjual` int(11) DEFAULT NULL,
  `nama_mobil` varchar(100) DEFAULT NULL,
  `harga` decimal(15,2) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `status` enum('tersedia','dipesan','terjual') DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `tahun` year(4) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mobil`
--

INSERT INTO `mobil` (`id_mobil`, `id_penjual`, `nama_mobil`, `harga`, `stok`, `status`, `deskripsi`, `tahun`, `foto`) VALUES
(3, 1, 'BMW', 650000000.00, 1, 'tersedia', 'BMW (Bayerische Motoren Werke) adalah produsen kendaraan premium dan mewah asal Jerman yang didirikan pada tahun 1916. Merek ini sangat terkenal dengan performa mesinnya, warisan motorsport melalui divisi BMW M, serta desain yang elegan', '2022', '1778780640_bmw.jpg'),
(6, 2, 'Pajero Sport', 230000000.00, 1, 'tersedia', 'Pajero Sport tangguh untuk perjalanan keluarga dan offroad', '2023', 'pajero.jpeg'),
(7, 1, 'Lamborghini', 850000000.00, 1, 'tersedia', 'Mobil Lamborghini adalah salah satu merek supercar terkemuka yang berasal dari Italia, dikenal karena desain yang menawan dan performa yang sangat tinggi. Sejak didirikan pada tahun 1963 oleh Ferruccio Lamborghini, perusahaan ini telah menjadi simbol kecepatan, kekuatan, dan kemewahan dalam dunia otomotif  Lamborghini telah menghasilkan berbagai model legendaris yang mendefinisikan ulang standar mobil super, dan terus berinovasi dalam desain dan teknologi', '2010', '1778780553_lamborghini.jpg'),
(11, 1, 'Ferrari', 15000000000.00, 1, 'tersedia', 'Ferrari adalah produsen mobil super (\r\n) dan hypercar performa tinggi asal Italia yang didirikan oleh Enzo Ferrari pada tahun 1947 di Maranello. Dikenal dengan logo \"Kuda Jingkrak\", Ferrari identik dengan kecepatan, teknologi balap Formula 1, desain aerodinamis yang menawan, serta mesin bertenaga besar, seringkali menggunakan konfigurasi V8 atau V12', '2020', 'ferrari.jpg'),
(13, 1, 'Bugatti', 50000000000.00, 1, 'tersedia', 'Bugatti adalah produsen hypercar dan mobil mewah asal Prancis yang didirikan oleh Ettore Bugatti pada tahun 1909. Merek ini terkenal secara global karena menggabungkan seni (Ettore berasal dari keluarga seniman) dengan teknologi mesin ekstrem, menghasilkan kendaraan tercepat, terkuat, dan termahal di dunia', '2025', '1778930598_bugatti.jpg'),
(14, 1, 'MClaren', 40000000000.00, 1, 'tersedia', 'McLaren adalah produsen supercar dan hypercar mewah asal Woking, Inggris, yang terkenal karena memproduksi mobil dengan teknologi Formula 1 untuk jalan raya. Lahir langsung dari lintasan balap, setiap mobil McLaren dirancang dengan fokus mutlak pada kecepatan ekstrem, bobot super ringan, dan aerodinamika radikal banget', '2023', '1778931027_mclaren.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pemesanan` int(11) DEFAULT NULL,
  `metode_bayar` enum('tunai','transfer') DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL,
  `status` enum('pending','verifikasi','diterima') DEFAULT NULL,
  `bukti_pembayaran` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pemesanan`, `metode_bayar`, `jumlah`, `status`, `bukti_pembayaran`) VALUES
(11, 14, 'transfer', NULL, '', '1778223780_1.jpeg'),
(15, 21, 'transfer', NULL, '', '1778322141_Screenshot_8-5-2026_17547_www.bing.com.jpeg'),
(16, 22, 'transfer', NULL, '', '1778780711_fddd9fb4dd5e11c8ad0c27e2d416ee6f.jpg'),
(17, 23, '', NULL, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `pembeli`
--

CREATE TABLE `pembeli` (
  `id_pembeli` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembeli`
--

INSERT INTO `pembeli` (`id_pembeli`, `id_user`, `nama`, `alamat`, `no_hp`, `foto`) VALUES
(2, 6, 'lita', 'sondol', '083324234243', NULL),
(3, 7, 'reva', 'bumi indah', '083243423424', NULL),
(4, 9, 'lala', 'gelam', '081345668921', NULL),
(5, 10, 'Fitri-ana member ke-9 H2H', 'Walet City Gacor Abies', '085210494158', NULL),
(6, 11, 'Inong', 'Pondok Sukatani Permai', '081315352350', NULL),
(8, 19, 'Ibrahim Mahesa', 'JL ARIA SANTIKA GG SAMAUN RT 5 RW 5', '085693419679', '1778812854_profil_19.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id_pemesanan` int(11) NOT NULL,
  `id_pembeli` int(11) DEFAULT NULL,
  `id_mobil` int(11) DEFAULT NULL,
  `tanggal_pesan` datetime DEFAULT NULL,
  `total_harga` decimal(15,2) DEFAULT NULL,
  `status` enum('booking','dp','lunas','batal') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemesanan`
--

INSERT INTO `pemesanan` (`id_pemesanan`, `id_pembeli`, `id_mobil`, `tanggal_pesan`, `total_harga`, `status`) VALUES
(14, 4, 3, '2026-05-08 14:01:58', 9999999999999.99, 'lunas'),
(21, 6, 6, '2026-05-09 17:21:48', 6000000000.00, 'lunas'),
(22, 3, 7, '2026-05-15 00:44:32', 850000000.00, 'lunas'),
(23, 8, 6, '2026-05-16 18:51:24', 250000000.00, 'lunas'),
(24, 8, 3, '2026-05-16 19:06:18', 700000000.00, 'lunas'),
(25, 8, 6, '2026-05-16 21:39:33', 250000000.00, 'booking');

-- --------------------------------------------------------

--
-- Table structure for table `penawaran`
--

CREATE TABLE `penawaran` (
  `id_penawaran` int(11) NOT NULL,
  `id_penjual` int(11) DEFAULT NULL,
  `id_mobil` int(11) DEFAULT NULL,
  `harga_tawar` decimal(15,2) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status` enum('menunggu','diterima','ditolak') DEFAULT 'menunggu',
  `metode_pembayaran` enum('tunai','transfer') DEFAULT NULL,
  `tanggal_keputusan` datetime DEFAULT NULL,
  `catatan_admin` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penawaran`
--

INSERT INTO `penawaran` (`id_penawaran`, `id_penjual`, `id_mobil`, `harga_tawar`, `tanggal`, `status`, `metode_pembayaran`, `tanggal_keputusan`, `catatan_admin`, `catatan`, `bukti_pembayaran`) VALUES
(2, 3, 14, 35000000000.00, '2026-05-17', 'diterima', 'transfer', '2026-05-17 00:38:29', 'sudah di tf', 'mantap', 'bukti_penawaran_1778953109_1245.jpg'),
(3, 3, 6, 230000000.00, '2026-05-17', 'diterima', 'transfer', '2026-05-17 00:50:16', 'nih sudah', 'promo gasgasgass!!!', 'bukti_penawaran_1778953816_1852.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman`
--

CREATE TABLE `pengiriman` (
  `id_pengiriman` int(11) NOT NULL,
  `id_pemesanan` int(11) DEFAULT NULL,
  `id_kurir` int(11) DEFAULT NULL,
  `alamat_kirim` text DEFAULT NULL,
  `status` enum('diproses','dikirim','selesai','terkirim') DEFAULT 'diproses',
  `bukti_pengiriman` varchar(255) DEFAULT NULL,
  `tanggal_terkirim` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengiriman`
--

INSERT INTO `pengiriman` (`id_pengiriman`, `id_pemesanan`, `id_kurir`, `alamat_kirim`, `status`, `bukti_pengiriman`, `tanggal_terkirim`) VALUES
(1, 24, 1, 'JL ARIA SANTIKA GG SAMAUN RT 5 RW 5', 'terkirim', 'bukti_pengiriman_1778948968_1731.jpg', '2026-05-16 23:29:28'),
(2, 23, 1, 'JL ARIA SANTIKA GG SAMAUN RT 5 RW 5', 'terkirim', 'bukti_pengiriman_1778949454_3374.jpg', '2026-05-16 23:37:34'),
(3, 22, 1, 'perumahan wisma mas blok z no 99 rt 99 rw 99', 'diproses', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `penjual`
--

CREATE TABLE `penjual` (
  `id_penjual` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjual`
--

INSERT INTO `penjual` (`id_penjual`, `id_user`, `nama`, `alamat`, `no_hp`, `foto`) VALUES
(1, 2, 'Titania Najwa', 'perumahan sukatani permai', '082434234234', NULL),
(2, 8, 'nabila', 'dimana aja', '084234234234', NULL),
(3, 21, 'Titania Najwa', 'Perumahan sukatani permai blok E no 9', '08582323232323', '1778953216_profil_21.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `surat_jalan`
--

CREATE TABLE `surat_jalan` (
  `id_suratjalan` int(11) NOT NULL,
  `id_pengiriman` int(11) DEFAULT NULL,
  `tanggal_cetak` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_jalan`
--

INSERT INTO `surat_jalan` (`id_suratjalan`, `id_pengiriman`, `tanggal_cetak`) VALUES
(1, 1, '2026-05-16 23:24:44'),
(2, 2, '2026-05-16 23:36:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('admin','pembeli','penjual','kurir') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`) VALUES
(2, 'titalys', '$2y$10$EHELAy94aUvyhbTuX0zQ2udmTDPwbiOap.1ytdcZ1OODBf6X.Hc46', 'penjual'),
(4, 'mikhayla', '$2y$10$Kx2nfm1.v9hzSgJgiqYMQ.ZBFu.SowKipqJpnm.OkHBC3n2kkK.F.', 'admin'),
(6, 'lita', '$2y$10$YpPlpGOX70BdfHzglK2RieJtBJBNBTMam7S.KDkYrnyMZobCpVuau', 'pembeli'),
(7, 'reva', '$2y$10$DjHEyww7Y/pcySFpJLdKTuPHImyrHXztG8SpgNMhbZt/IZ0mcUzky', 'pembeli'),
(8, 'nabila', '$2y$10$U65n1JE/YPUVF2OjJHNKee5hsHVlJEhZulQix0EZVj7rm1DNm5n6a', 'penjual'),
(9, 'hanisut', '$2y$10$Typl6Zm6UYNsTHwSIKYZxuIUhzZbNbeQhm9kOrUwJ6w3ckFn1EbtO', 'pembeli'),
(10, 'lee jehoon gf', '$2y$10$qamzo9YsL09KLcvdeQt3R.fejkZjeI6tPOtSyaDueBkH4zH6JcEKS', 'pembeli'),
(11, 'customer', '$2y$10$QRJIhW.DTgXZUp3HXf31e.7XIOCVdYlBAQoMqyEao1aKTly9gh6kG', 'pembeli'),
(13, 'pembeli', '123', 'pembeli'),
(14, 'imahesz', '123', 'pembeli'),
(15, 'gendut', '123', 'pembeli'),
(17, 'penjual', '123', 'penjual'),
(18, 'admin123', '$2y$10$qSbxyfHQ3UpWqp9pXyYwvOGP6btKaP.WDkIdI2gVwF1RsfjrEzm0i', 'admin'),
(19, 'pembeli123', '$2y$10$kmQLqo5Eb17Pad/xN6fPwO3dfs03s5yhBQty2qEQlaiGc.R70.zUm', 'pembeli'),
(20, 'kurir123', '$2y$10$KdjjhVoqFfoW2CBAAM7zbOxFwVizbjEJjUhX/QuRXtfF01XSPY6de', 'kurir'),
(21, 'penjual123', '$2y$10$rinTb5y67a6DXPXHfVd7lOiKXWjz0jHJfUOTocMxC3ZGSqwyo/gyO', 'penjual');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `administrasi_kendaraan`
--
ALTER TABLE `administrasi_kendaraan`
  ADD PRIMARY KEY (`id_administrasi`);

--
-- Indexes for table `kurir`
--
ALTER TABLE `kurir`
  ADD PRIMARY KEY (`id_kurir`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `kwitansi`
--
ALTER TABLE `kwitansi`
  ADD PRIMARY KEY (`id_kwitansi`),
  ADD KEY `id_pembayaran` (`id_pembayaran`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`);

--
-- Indexes for table `mobil`
--
ALTER TABLE `mobil`
  ADD PRIMARY KEY (`id_mobil`),
  ADD KEY `id_penjual` (`id_penjual`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pemesanan` (`id_pemesanan`);

--
-- Indexes for table `pembeli`
--
ALTER TABLE `pembeli`
  ADD PRIMARY KEY (`id_pembeli`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD KEY `id_pembeli` (`id_pembeli`),
  ADD KEY `id_mobil` (`id_mobil`);

--
-- Indexes for table `penawaran`
--
ALTER TABLE `penawaran`
  ADD PRIMARY KEY (`id_penawaran`),
  ADD KEY `id_penjual` (`id_penjual`),
  ADD KEY `id_mobil` (`id_mobil`);

--
-- Indexes for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`id_pengiriman`),
  ADD KEY `id_pemesanan` (`id_pemesanan`),
  ADD KEY `id_kurir` (`id_kurir`);

--
-- Indexes for table `penjual`
--
ALTER TABLE `penjual`
  ADD PRIMARY KEY (`id_penjual`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  ADD PRIMARY KEY (`id_suratjalan`),
  ADD KEY `id_pengiriman` (`id_pengiriman`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `administrasi_kendaraan`
--
ALTER TABLE `administrasi_kendaraan`
  MODIFY `id_administrasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kurir`
--
ALTER TABLE `kurir`
  MODIFY `id_kurir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kwitansi`
--
ALTER TABLE `kwitansi`
  MODIFY `id_kwitansi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mobil`
--
ALTER TABLE `mobil`
  MODIFY `id_mobil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pembeli`
--
ALTER TABLE `pembeli`
  MODIFY `id_pembeli` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id_pemesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `penawaran`
--
ALTER TABLE `penawaran`
  MODIFY `id_penawaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengiriman`
--
ALTER TABLE `pengiriman`
  MODIFY `id_pengiriman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penjual`
--
ALTER TABLE `penjual`
  MODIFY `id_penjual` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  MODIFY `id_suratjalan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `kurir`
--
ALTER TABLE `kurir`
  ADD CONSTRAINT `kurir_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `kwitansi`
--
ALTER TABLE `kwitansi`
  ADD CONSTRAINT `kwitansi_ibfk_1` FOREIGN KEY (`id_pembayaran`) REFERENCES `pembayaran` (`id_pembayaran`);

--
-- Constraints for table `mobil`
--
ALTER TABLE `mobil`
  ADD CONSTRAINT `mobil_ibfk_1` FOREIGN KEY (`id_penjual`) REFERENCES `penjual` (`id_penjual`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`);

--
-- Constraints for table `pembeli`
--
ALTER TABLE `pembeli`
  ADD CONSTRAINT `pembeli_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `fk_pembeli` FOREIGN KEY (`id_pembeli`) REFERENCES `pembeli` (`id_pembeli`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`id_pembeli`) REFERENCES `pembeli` (`id_pembeli`),
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`id_mobil`) REFERENCES `mobil` (`id_mobil`);

--
-- Constraints for table `penawaran`
--
ALTER TABLE `penawaran`
  ADD CONSTRAINT `penawaran_ibfk_1` FOREIGN KEY (`id_penjual`) REFERENCES `penjual` (`id_penjual`),
  ADD CONSTRAINT `penawaran_ibfk_2` FOREIGN KEY (`id_mobil`) REFERENCES `mobil` (`id_mobil`);

--
-- Constraints for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD CONSTRAINT `pengiriman_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`),
  ADD CONSTRAINT `pengiriman_ibfk_2` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`);

--
-- Constraints for table `penjual`
--
ALTER TABLE `penjual`
  ADD CONSTRAINT `penjual_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  ADD CONSTRAINT `surat_jalan_ibfk_1` FOREIGN KEY (`id_pengiriman`) REFERENCES `pengiriman` (`id_pengiriman`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
