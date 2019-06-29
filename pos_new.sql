-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 28, 2019 at 01:18 PM
-- Server version: 5.7.25
-- PHP Version: 7.1.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos_new`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `inventory_stock`
-- (See below for the actual view)
--
CREATE TABLE `inventory_stock` (
`id_produk` int(11)
,`nama_produk` varchar(255)
,`id_variant` int(11)
,`nama_variant` varchar(255)
,`barcode` varchar(32)
,`lokasi_online` longtext
,`lokasi_butik` longtext
,`stock_online` decimal(32,0)
,`stock_butik` decimal(32,0)
,`stock_bazar_a` decimal(32,0)
,`stock_bazar_b` decimal(32,0)
,`stock_bazar_c` decimal(32,0)
,`total` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `kas`
--

CREATE TABLE `kas` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `saldo_awal` decimal(20,2) NOT NULL,
  `saldo_now` decimal(20,2) NOT NULL,
  `act` int(11) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `users` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kas`
--

INSERT INTO `kas` (`id`, `nama`, `saldo_awal`, `saldo_now`, `act`, `keterangan`, `users`) VALUES
(1, 'Penjualan Online', '0.00', '0.00', 0, '', '[1]'),
(2, 'Penjualan Butik', '0.00', '0.00', 0, '', '[1, 2]'),
(3, 'Penjualan Bazar A', '0.00', '0.00', 0, '', '[1]'),
(4, 'Penjualan Bazar B', '0.00', '0.00', 0, '', '[1]'),
(5, 'Penjualan Bazar C', '0.00', '0.00', 0, '', '[1]'),
(10, 'Semesta', '0.00', '0.00', 0, 'For anyone', '[\"*\"]'),
(11, 'Mandiri', '12300000.00', '0.00', 0, '-', '[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]'),
(12, 'Secret Foe', '200000.00', '0.00', 0, 'Strict dummy', '[1, 12]');

-- --------------------------------------------------------

--
-- Table structure for table `kas_trans`
--

CREATE TABLE `kas_trans` (
  `id` int(11) NOT NULL,
  `id_kas` int(11) NOT NULL,
  `id_sales` int(11) NOT NULL,
  `act` set('IN','OUT') NOT NULL,
  `uang` decimal(20,2) NOT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kas_trans`
--

INSERT INTO `kas_trans` (`id`, `id_kas`, `id_sales`, `act`, `uang`, `keterangan`) VALUES
(24, 1, 7, 'IN', '2874500.00', 'Penjualan Item'),
(25, 1, 7, 'OUT', '178900.00', 'Diskon Item'),
(26, 1, 7, 'OUT', '10500.00', 'Potongan Grosir'),
(27, 1, 7, 'IN', '53702.00', 'Biaya tambahan'),
(28, 1, 7, 'OUT', '40000.00', 'Diskon Voucher'),
(29, 2, 8, 'IN', '2119000.00', 'Penjualan Item'),
(30, 2, 8, 'OUT', '25500.00', 'Potongan Grosir'),
(31, 2, 8, 'OUT', '100000.00', 'Diskon Voucher'),
(32, 1, 9, 'IN', '315000.00', 'Penjualan Item'),
(33, 1, 9, 'OUT', '2500.00', 'Potongan Grosir'),
(34, 1, 9, 'OUT', '12500.00', 'Diskon Voucher'),
(35, 1, 10, 'IN', '1080000.00', 'Penjualan Item'),
(36, 1, 10, 'OUT', '5000.00', 'Potongan Grosir'),
(37, 1, 11, 'IN', '2000000.00', 'Penjualan Item'),
(38, 1, 11, 'OUT', '132000.00', 'Diskon Item'),
(39, 1, 11, 'OUT', '25500.00', 'Potongan Grosir'),
(40, 1, 11, 'IN', '36850.00', 'Biaya tambahan'),
(41, 1, 12, 'IN', '1005000.00', 'Penjualan Item'),
(42, 1, 12, 'OUT', '50000.00', 'Diskon Item'),
(43, 1, 12, 'OUT', '5000.00', 'Potongan Grosir'),
(44, 1, 12, 'OUT', '10000.00', 'Diskon Voucher'),
(45, 1, 13, 'IN', '627500.00', 'Penjualan Item'),
(46, 1, 13, 'OUT', '2500.00', 'Potongan Grosir'),
(47, 1, 13, 'OUT', '25000.00', 'Diskon Voucher'),
(48, 1, 14, 'IN', '1080000.00', 'Penjualan Item'),
(49, 1, 14, 'OUT', '5000.00', 'Potongan Grosir'),
(50, 1, 14, 'IN', '21500.00', 'Biaya tambahan'),
(51, 1, 15, 'IN', '260000.00', 'Penjualan Item');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `id_master` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `id_group` bigint(20) NOT NULL DEFAULT '0',
  `nama_produk` varchar(255) NOT NULL,
  `nama_struk` varchar(255) NOT NULL,
  `harga_pp` decimal(20,2) NOT NULL,
  `harga_ecer` decimal(20,2) NOT NULL,
  `harga_grosir` json NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `foto` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `id_group`, `nama_produk`, `nama_struk`, `harga_pp`, `harga_ecer`, `harga_grosir`, `keterangan`, `foto`) VALUES
(12, 9, 'Test TCG HS', 'TCG HS', '9900.00', '10000.00', '[[10, 1000, 9950]]', 'Test TCG Card', '[0]'),
(13, 0, 'Polo Shirt Quicksilver  30', 'PSO QUIK 30', '80000.00', '240000.00', '[[\"2\", \"3\", \"225000.00\"], [\"4\", \"10\", \"210000.00\"], [\"11\", \"1000\", \"195000.00\"]]', 'Polo shirt Quiksilver original', '[0, 1]'),
(14, 1, 'Polo Shirt Oakley 138', 'PSO OAKLEY 138', '120000.00', '240000.00', '[[3, 5, 230000], [6, 1000, 220000]]', '', '[0]'),
(15, 1, 'Polo Shirt Oakley 146', 'PSO OAKLEY 146', '110000.00', '220000.00', '[[5, 10, 215000], [11, 1000, 209500]]', '', '[0, 1]'),
(16, 0, 'Charlie Shirt', 'CHARLIE BATIK SHIRT', '48500.00', '65000.00', '[[5, 10, 62500], [11, 20, 59500], [21, 1000, 56000]]', '', '[0, 1, 2]'),
(17, 0, 'Kenzo Hoodie Tunic', 'KENZO HOODIE LONG SLEEVE', '49500.50', '70000.00', '[]', '', '[0, 1]');

-- --------------------------------------------------------

--
-- Table structure for table `produk_group`
--

CREATE TABLE `produk_group` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `produk_group`
--

INSERT INTO `produk_group` (`id`, `nama`) VALUES
(1, 'Polo Shirt Oakley'),
(9, 'YGO Card');

-- --------------------------------------------------------

--
-- Table structure for table `produk_inventory`
--

CREATE TABLE `produk_inventory` (
  `id` int(11) NOT NULL,
  `id_variant` int(11) NOT NULL,
  `id_toko` int(11) NOT NULL,
  `act` set('IN','OUT') NOT NULL,
  `qty` int(11) NOT NULL,
  `dt` bigint(20) NOT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `produk_inventory`
--

INSERT INTO `produk_inventory` (`id`, `id_variant`, `id_toko`, `act`, `qty`, `dt`, `keterangan`) VALUES
(90, 32, 2, 'IN', 5, 1561005383, 'Stock Awal'),
(91, 33, 2, 'IN', 4, 1561005441, 'Stock Awal'),
(92, 32, 1, 'OUT', 4, 1559692020, 'Mulai lagi'),
(93, 32, 1, 'IN', 5, 1560988680, ''),
(94, 33, 1, 'IN', 2, 1560964320, ''),
(95, 32, 2, 'OUT', 4, 1560964320, ''),
(96, 33, 2, 'OUT', 3, 1560964320, ''),
(97, 32, 2, 'OUT', 1, 1560990900, 'First Transfer'),
(98, 32, 1, 'IN', 1, 1560990900, 'First Transfer'),
(99, 32, 2, 'OUT', 1, 1560990900, 'First Transfer'),
(100, 32, 1, 'IN', 1, 1560990900, 'First Transfer'),
(101, 33, 2, 'OUT', 1, 1560990900, 'First Transfer'),
(102, 33, 1, 'IN', 1, 1560990900, 'First Transfer'),
(103, 32, 1, 'OUT', 1, 1560997920, 'Tambal'),
(104, 32, 2, 'IN', 1, 1560997920, 'Tambal'),
(105, 32, 3, 'IN', 10, 1560999480, 'Event'),
(106, 33, 3, 'IN', 10, 1560999480, 'Event'),
(107, 33, 2, 'IN', 5, 1561085400, 'Tambal 2'),
(108, 32, 2, 'IN', 5, 1561085400, 'Tambal 2'),
(109, 32, 2, 'OUT', 3, 1561069500, ''),
(110, 33, 2, 'OUT', 2, 1561069500, ''),
(111, 32, 3, 'OUT', 10, 1561071060, 'Rampung Event'),
(112, 32, 1, 'IN', 10, 1561071060, 'Rampung Event'),
(113, 33, 3, 'OUT', 10, 1561071060, 'Rampung Event'),
(114, 33, 1, 'IN', 10, 1561071060, 'Rampung Event'),
(115, 32, 4, 'IN', 5, 1561403520, 'Event maning'),
(116, 33, 4, 'IN', 5, 1561403520, 'Event maning'),
(117, 33, 4, 'OUT', 2, 1561349520, 'Event gilir'),
(118, 33, 5, 'IN', 2, 1561349520, 'Event gilir'),
(119, 32, 4, 'OUT', 2, 1561349520, 'Event gilir'),
(120, 32, 5, 'IN', 2, 1561349520, 'Event gilir'),
(121, 34, 1, 'IN', 5, 1561431477, 'Stock Awal'),
(122, 34, 2, 'IN', 5, 1561431477, 'Stock Awal'),
(123, 35, 1, 'IN', 10, 1561435874, 'Stock Awal'),
(124, 35, 2, 'IN', 10, 1561435874, 'Stock Awal'),
(125, 32, 1, 'OUT', 12, 1561662600, 'Penjualan'),
(126, 35, 1, 'OUT', 10, 1561662600, 'Penjualan'),
(127, 33, 2, 'OUT', 13, 1559936040, 'Penjualan'),
(128, 34, 2, 'OUT', 6, 1559936040, 'Penjualan'),
(129, 32, 1, 'OUT', 5, 1561674960, 'Penjualan'),
(130, 35, 1, 'OUT', 5, 1561685040, 'Penjualan'),
(131, 34, 1, 'OUT', 6, 1563721200, 'Penjualan'),
(132, 33, 1, 'OUT', 11, 1563721200, 'Penjualan'),
(133, 33, 1, 'OUT', 8, 1561698480, 'Penjualan'),
(134, 32, 1, 'OUT', 8, 1561698480, 'Penjualan'),
(135, 32, 1, 'OUT', 10, 1561713300, 'Penjualan'),
(136, 35, 1, 'OUT', 5, 1561734600, 'Penjualan'),
(137, 32, 1, 'IN', 25, 1561693500, 'Miss Update'),
(138, 33, 1, 'IN', 10, 1561693500, 'Miss Update'),
(139, 34, 1, 'IN', 5, 1561693500, 'Miss Update'),
(140, 35, 1, 'IN', 10, 1561693500, 'Miss Update'),
(141, 32, 1, 'OUT', 2, 1560964800, 'Penjualan'),
(142, 33, 1, 'OUT', 2, 1560964800, 'Penjualan');

-- --------------------------------------------------------

--
-- Table structure for table `produk_variant`
--

CREATE TABLE `produk_variant` (
  `id` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `barcode` varchar(32) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `stock` json NOT NULL,
  `lokasi` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `produk_variant`
--

INSERT INTO `produk_variant` (`id`, `id_produk`, `barcode`, `nama`, `stock`, `lokasi`) VALUES
(32, 16, '000032', 'Brown', '{\"1\": 0, \"2\": 2, \"3\": 0, \"4\": 3, \"5\": 2}', '{\"1\": \"\", \"2\": \"A1\"}'),
(33, 16, '000033', 'Red', '{\"1\": 2, \"2\": -10, \"3\": 0, \"4\": 3, \"5\": 2}', '{\"1\": \"\", \"2\": \"A1\"}'),
(34, 14, '000034', 'Brown', '{\"1\": 4, \"2\": -1, \"3\": 0, \"4\": 0, \"5\": 0}', '{\"1\": \"\", \"2\": \"B1\"}'),
(35, 15, '000035', 'Blue', '{\"1\": 0, \"2\": 10, \"3\": 0, \"4\": 0, \"5\": 0}', '{\"1\": \"\", \"2\": \"B1\"}');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `id_toko` int(11) NOT NULL,
  `id_spg` int(11) NOT NULL,
  `id_member` int(11) NOT NULL,
  `items` json NOT NULL,
  `dt` bigint(20) NOT NULL,
  `nama_konsumen` varchar(255) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `diskon` decimal(20,2) NOT NULL,
  `charge_cc` int(3) NOT NULL COMMENT 'Percent of CC Charged',
  `pay` decimal(20,2) NOT NULL,
  `payment` varchar(255) NOT NULL,
  `total_sale` decimal(20,2) NOT NULL,
  `total_transaksi` decimal(20,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `id_toko`, `id_spg`, `id_member`, `items`, `dt`, `nama_konsumen`, `keterangan`, `diskon`, `charge_cc`, `pay`, `payment`, `total_sale`, `total_transaksi`) VALUES
(7, 1, 3, 0, '[[32, 12, 10], [35, 10, 5]]', 1561662600, 'Albert', 'First Sale!!!', '40000.00', 2, '2700000.00', 'Cash', '2685100.00', '2698802.00'),
(8, 2, 3, 0, '[[33, 13, 0], [34, 6, 0]]', 1559936040, 'Hussein', 'Second Sale', '100000.00', 2, '2000000.00', 'Debit', '2093500.00', '1993500.00'),
(9, 1, 0, 0, '[[32, 5, 0]]', 1561674960, 'P', 'Third Sale', '12500.00', 2, '300000.00', 'Cash', '312500.00', '300000.00'),
(10, 1, 0, 0, '[[35, 5, 0]]', 1561685040, 'P', 'Fourth Sale', '0.00', 2, '1100000.00', 'Cash', '1075000.00', '1075000.00'),
(11, 1, 0, 0, '[[34, 6, 10], [33, 11, 0]]', 1563721200, 'P', 'Fifth Sale', '0.00', 2, '2000000.00', 'Cash', '1842500.00', '1879350.00'),
(12, 1, 0, 0, '[[33, 8, 0], [32, 8, 10]]', 1561698480, 'Deco', 'Sixth Sale', '10000.00', 2, '1000000.00', 'Debit', '950000.00', '940000.00'),
(13, 1, 3, 0, '[[32, 10, 0]]', 1561713300, 'Hadi', 'Seventh Sale', '25000.00', 2, '1000000.00', 'Debit', '625000.00', '600000.00'),
(14, 1, 0, 0, '[[35, 5, 0]]', 1561734600, 'Hadi', 'Eighth Sale', '0.00', 2, '1100000.00', 'Cash', '1075000.00', '1096500.00'),
(15, 1, 0, 0, '[[32, 2, 0], [33, 2, 0]]', 1560964800, 'Hadi', '', '0.00', 2, '300000.00', 'Cash', '260000.00', '260000.00');

-- --------------------------------------------------------

--
-- Table structure for table `shelving`
--

CREATE TABLE `shelving` (
  `id` int(11) NOT NULL,
  `nama_lokasi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `spg`
--

CREATE TABLE `spg` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `spg`
--

INSERT INTO `spg` (`id`, `nama`, `no_hp`, `keterangan`) VALUES
(1, 'Dolores', '0888888888', '-'),
(3, 'Lorem', '123', '-'),
(4, 'Ipsum', '17', 'Primarily SPG');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `pic` int(11) NOT NULL,
  `access` set('Administrator','Moderator') NOT NULL,
  `access_kas` json NOT NULL,
  `access_sales` json NOT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `pass`, `pic`, `access`, `access_kas`, `access_sales`, `keterangan`) VALUES
(1, 'Yanuarizal Kurnia', 'me@yanuarizal.tk', '8f6bf4d6b9ba34fce456bdf1239a825dfe24bd7c9f8efcecb08caa84', 1, 'Administrator', '[\"*\"]', '[\"*\"]', 'Akun ku'),
(2, 'Dummy One', 'dummy1@mail.com', '6674f22c6190b358346a9149d2dd3ba593d0621332856288a9b23873', 1, 'Moderator', '[3, 4, 5, 10]', '[]', 'dummy account\r\nprimary dummy'),
(3, 'dummy2', 'dummy2@mail.com', 'dummy2', 1, 'Moderator', '[]', '[]', 'dummy account'),
(4, 'dummy3', 'dummy3@mail.com', 'dummy3', 1, 'Moderator', '[]', '[]', 'dummy account'),
(5, 'dummy4', 'dummy4@mail.com', 'dummy4', 1, 'Moderator', '[]', '[]', 'dummy account'),
(6, 'dummy5', 'dummy5@mail.com', 'dummy5', 1, 'Moderator', '[]', '[]', 'dummy account'),
(7, 'dummy6', 'dummy6@mail.com', 'dummy6', 1, 'Moderator', '[]', '[]', 'dummy account'),
(8, 'dummy7', 'dummy7@mail.com', 'dummy7', 1, 'Moderator', '[]', '[]', 'dummy account'),
(9, 'dummy8', 'dummy8@mail.com', 'dummy8', 1, 'Moderator', '[]', '[]', 'dummy account'),
(10, 'dummy9', 'dummy9@mail.com', 'dummy9', 1, 'Moderator', '[]', '[]', 'dummy account'),
(11, 'dummy10', 'dummy10@mail.com', 'dummy10', 1, 'Moderator', '[]', '[]', 'dummy account'),
(12, 'John Doe', 'johndoe@mail.com', 'ad0412f62f2506cb75498f63e10c726242c311d3ce7208eb832be200', 1, 'Administrator', '[3, 4, 5, 10]', '[]', 'This is John Doe user\r\nOnly John\r\nNo more');

-- --------------------------------------------------------

--
-- Structure for view `inventory_stock`
--
DROP TABLE IF EXISTS `inventory_stock`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gravis`@`%` SQL SECURITY DEFINER VIEW `inventory_stock`  AS  select `produk`.`id` AS `id_produk`,`produk`.`nama_produk` AS `nama_produk`,`produk_inventory`.`id_variant` AS `id_variant`,`produk_variant`.`nama` AS `nama_variant`,`produk_variant`.`barcode` AS `barcode`,json_unquote(json_extract(`produk_variant`.`lokasi`,'$."1"')) AS `lokasi_online`,json_unquote(json_extract(`produk_variant`.`lokasi`,'$."2"')) AS `lokasi_butik`,sum(if((`produk_inventory`.`id_toko` = 1),if((`produk_inventory`.`act` = 'IN'),`produk_inventory`.`qty`,-(`produk_inventory`.`qty`)),0)) AS `stock_online`,sum(if((`produk_inventory`.`id_toko` = 2),if((`produk_inventory`.`act` = 'IN'),`produk_inventory`.`qty`,-(`produk_inventory`.`qty`)),0)) AS `stock_butik`,sum(if((`produk_inventory`.`id_toko` = 3),if((`produk_inventory`.`act` = 'IN'),`produk_inventory`.`qty`,-(`produk_inventory`.`qty`)),0)) AS `stock_bazar_a`,sum(if((`produk_inventory`.`id_toko` = 4),if((`produk_inventory`.`act` = 'IN'),`produk_inventory`.`qty`,-(`produk_inventory`.`qty`)),0)) AS `stock_bazar_b`,sum(if((`produk_inventory`.`id_toko` = 5),if((`produk_inventory`.`act` = 'IN'),`produk_inventory`.`qty`,-(`produk_inventory`.`qty`)),0)) AS `stock_bazar_c`,sum(if((`produk_inventory`.`act` = 'IN'),`produk_inventory`.`qty`,-(`produk_inventory`.`qty`))) AS `total` from ((`produk_inventory` join `produk_variant` on((`produk_inventory`.`id_variant` = `produk_variant`.`id`))) join `produk` on((`produk_variant`.`id_produk` = `produk`.`id`))) group by `produk_inventory`.`id_variant` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kas`
--
ALTER TABLE `kas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kas_trans`
--
ALTER TABLE `kas_trans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk_group`
--
ALTER TABLE `produk_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk_inventory`
--
ALTER TABLE `produk_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk_variant`
--
ALTER TABLE `produk_variant`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shelving`
--
ALTER TABLE `shelving`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spg`
--
ALTER TABLE `spg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kas`
--
ALTER TABLE `kas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `kas_trans`
--
ALTER TABLE `kas_trans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `produk_group`
--
ALTER TABLE `produk_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `produk_inventory`
--
ALTER TABLE `produk_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `produk_variant`
--
ALTER TABLE `produk_variant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `shelving`
--
ALTER TABLE `shelving`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spg`
--
ALTER TABLE `spg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
