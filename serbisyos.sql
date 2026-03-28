-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 28, 2026 at 09:12 AM
-- Server version: 11.4.10-MariaDB-cll-lve
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `serbvhad_serbisyos`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_sessions`
--

CREATE TABLE `active_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `device_info` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `login_time` datetime NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `logout_time` datetime DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 0,
  `is_2fa_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `location` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `location_accuracy` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `active_sessions`
--

INSERT INTO `active_sessions` (`id`, `user_id`, `session_id`, `device_info`, `ip_address`, `user_agent`, `login_time`, `last_activity`, `logout_time`, `is_current`, `is_2fa_verified`, `created_at`, `location`, `latitude`, `longitude`, `location_accuracy`) VALUES
(791, 236, 'f34585a98378b3494cbe4ec46ce6530b757dab1ae25c290c8d6bd857c985a7a7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-22 11:16:26', '2025-10-26 14:21:59', NULL, 0, 1, '2025-10-22 03:16:26', NULL, NULL, NULL, NULL),
(793, 240, '7a064b96f1162d10eb13ee2ba4b7344272728e85be2c6bafe5e74af952476bfe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-22 12:44:03', '2025-10-26 11:18:49', NULL, 0, 0, '2025-10-22 04:44:03', NULL, NULL, NULL, NULL),
(815, 248, '915c6ba744131a376111474f5f2997eefe14752cfeeffc5814d2f9210d7e8c21', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.182.36', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-23 21:52:49', '2025-10-27 10:25:11', NULL, 0, 0, '2025-10-24 01:52:49', NULL, NULL, NULL, NULL),
(830, 240, '8b3433bb6b7d0ccc4cff49548aa529d030e5a755ad7de71e59faf1265a0af43b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.147.198.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-26 07:18:49', '2025-10-26 18:39:52', NULL, 0, 0, '2025-10-26 11:18:49', NULL, NULL, NULL, NULL),
(834, 236, '70367a545841fb1a5aac3419ac8bbba854729153555bc3ca80016859c27f8e12', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.147.198.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-26 10:21:59', '2025-10-26 14:43:26', '2025-10-26 10:43:26', 0, 1, '2025-10-26 14:21:59', NULL, NULL, NULL, NULL),
(841, 240, '1e4c713bd5bd349d06e455de378be537b6b4d9b4b08c4a732f61ee32b5ff5c0f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.147.198.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-26 14:39:52', '2025-10-26 18:39:52', NULL, 1, 0, '2025-10-26 18:39:52', NULL, NULL, NULL, NULL),
(845, 254, '3a0dba9837df5d8bd66886f25c998e69928c2677e2f1cdf6f07311412cf3a0bf', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '112.198.234.197', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-27 02:17:41', '2025-10-27 06:26:57', '2025-10-27 02:26:57', 0, 0, '2025-10-27 06:17:41', NULL, NULL, NULL, NULL),
(852, 236, '533f284a03ae25268ff1520ac967518590f12dab33b3795bf0245f114555aff5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.147.198.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-27 03:10:35', '2025-10-29 10:10:49', NULL, 0, 1, '2025-10-27 07:10:35', NULL, NULL, NULL, NULL),
(867, 248, '6d8634ade7731fc6cdb1fd140400b4f37706e4e8336ab5cc5286f8f72e4e1039', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.182.36', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-27 06:25:11', '2025-10-28 03:07:35', NULL, 0, 1, '2025-10-27 10:25:11', NULL, NULL, NULL, NULL),
(872, 260, '34909843c77cf4bdca6b854b3fa8d3f4a1ccf1749f7e17b62351809291504ebf', 'Mozilla/5.0 (Linux; Android 13; Infinix X6815C Build/TP1A.220624.014; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/142.0.7444.48 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.2.109;]', '203.84.189.253', 'Mozilla/5.0 (Linux; Android 13; Infinix X6815C Build/TP1A.220624.014; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/142.0.7444.48 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.2.109;]', '2025-10-27 07:36:36', '2025-10-27 11:36:36', NULL, 1, 0, '2025-10-27 11:36:36', NULL, NULL, NULL, NULL),
(873, 261, 'd5e325b8034d1077c96b87b5c68f9be7c5f3c9f33567d926ac1b0f20817fd93f', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.147.198.204', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-27 07:41:38', '2025-10-27 11:41:38', NULL, 1, 0, '2025-10-27 11:41:38', NULL, NULL, NULL, NULL),
(874, 262, '1b23f9c3328ec63c089054db85edb15fe1666f79964821c974eb873f16a2563f', 'Mozilla/5.0 (Linux; Android 15; 23129RAA4G Build/AQ3A.240829.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.122 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/530.1.0.67.107;]', '221.121.102.243', 'Mozilla/5.0 (Linux; Android 15; 23129RAA4G Build/AQ3A.240829.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.122 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/530.1.0.67.107;]', '2025-10-27 07:42:32', '2025-10-27 11:42:32', NULL, 1, 0, '2025-10-27 11:42:32', NULL, NULL, NULL, NULL),
(879, 248, '01f08575856be38919143fe30460f5449dfa34b5267a6c97d03a85aa9efc8bc1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-27 23:07:35', '2025-10-28 03:32:46', '2025-10-27 23:32:46', 0, 1, '2025-10-28 03:07:35', NULL, NULL, NULL, NULL),
(882, 267, '2cedb49ae91146e6487fde8113202af4a1d8e9e80cf209df8813ad95dfe1e912', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-27 23:33:05', '2025-10-28 04:29:49', NULL, 0, 0, '2025-10-28 03:33:05', NULL, NULL, NULL, NULL),
(883, 248, 'a7a4ef119608a8bd4061902b2230e98ebe56674804edf4d9b654348a338543c6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-27 23:33:57', '2025-10-28 04:29:39', NULL, 0, 1, '2025-10-28 03:33:57', NULL, NULL, NULL, NULL),
(884, 248, '25acf16a039f0bab97ebf77fbcc1f4e3529aed97c7a5d751e3a27f29ec46ae05', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-28 00:29:39', '2025-10-28 04:33:03', '2025-10-28 00:33:03', 0, 1, '2025-10-28 04:29:39', NULL, NULL, NULL, NULL),
(885, 267, 'f4219ece170ad714b81f716033eef82f1544d870c4b0eb25a2f33ba661917c23', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '180.190.238.22', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-28 00:29:49', '2025-10-28 04:29:49', NULL, 1, 1, '2025-10-28 04:29:49', NULL, NULL, NULL, NULL),
(887, 248, 'b6256b9c763a7c6c9c1ab46440ae3473fa90d05999cb520af48f715ffb442b25', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-28 00:46:20', '2025-10-28 04:47:40', '2025-10-28 00:47:40', 0, 1, '2025-10-28 04:46:20', NULL, NULL, NULL, NULL),
(888, 248, '6b1db78051b5fe327198e01b4e058d37819ba2b40497c1657adb6bfe27b32d03', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-28 00:48:03', '2025-10-28 05:10:39', NULL, 0, 0, '2025-10-28 04:48:03', NULL, NULL, NULL, NULL),
(889, 248, '002bf07d25fa11d5c0c64257375759968c1e793b24a4e8438ddb69ea730ef18a', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '180.190.238.22', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-28 01:10:39', '2025-10-28 05:13:33', NULL, 0, 0, '2025-10-28 05:10:39', NULL, NULL, NULL, NULL),
(890, 248, 'ed6e38d1bab8d29f26bd071dacca550254d3a1bf62c5a82d337823d88be36346', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-28 01:13:33', '2025-10-29 01:33:51', NULL, 0, 0, '2025-10-28 05:13:33', NULL, NULL, NULL, NULL),
(892, 269, '65076765715cb967a8a2c4cc4160210d1cdcc9ade68fbf2a3bf69b1922b0f9fa', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '49.145.124.204', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2025-10-28 06:59:57', '2025-10-28 11:01:13', NULL, 0, 0, '2025-10-28 10:59:57', NULL, NULL, NULL, NULL),
(893, 269, 'a3bc9d7f2d050935c37fa5e43765aa181b515177d85b64edad7e644a692c97a0', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '49.145.124.204', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2025-10-28 07:01:13', '2025-10-28 11:01:14', NULL, 0, 1, '2025-10-28 11:01:13', NULL, NULL, NULL, NULL),
(894, 269, '2fbsfosfmrn85iteeksclnjplm', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '49.145.124.204', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2025-10-28 07:01:14', '2025-10-28 11:03:13', NULL, 0, 1, '2025-10-28 11:01:14', NULL, NULL, NULL, NULL),
(895, 269, 'punkkg18t8md6ban7tua8al4f6', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '49.145.124.204', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2025-10-28 07:03:13', '2025-10-28 12:24:27', NULL, 0, 1, '2025-10-28 11:03:13', NULL, NULL, NULL, NULL),
(910, 269, '40t6gpcsfiuaatgv20fa0kdshp', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '49.145.124.204', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2025-10-28 08:24:27', '2025-10-28 12:24:27', NULL, 1, 1, '2025-10-28 12:24:27', NULL, NULL, NULL, NULL),
(927, 248, 'c801ea210d021d4d8c4e2bb66b611acd423a7271ede86ac1b5dbaf7e1f060cf6', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', '175.176.75.6', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', '2025-10-29 09:33:51', '2025-10-29 03:19:01', NULL, 0, 0, '2025-10-29 01:33:51', NULL, NULL, NULL, NULL),
(930, 248, '42f2dc7b4c25e718744ea779b1de9c42fca61b594f86300c50a82c4f10a3bceb', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', '180.190.238.22', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', '2025-10-29 11:19:01', '2025-10-30 02:58:55', NULL, 0, 0, '2025-10-29 03:19:01', NULL, NULL, NULL, NULL),
(936, 236, 'a5efec93ddf878ecaeacbe2ed8d6dcad9307df082982cafafbd47953ee284fa8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-30 01:31:35', '2025-10-30 16:30:40', NULL, 0, 1, '2025-10-29 17:31:35', NULL, NULL, NULL, NULL),
(937, 248, '2dbaa3fbbace4535fa4ad6280c30935bfc65d8f4de8b28678c46fb19b0825388', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.182.36', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-30 10:58:55', '2025-11-13 12:06:48', NULL, 0, 0, '2025-10-30 02:58:55', NULL, NULL, NULL, NULL),
(942, 236, '71629c55a80cdc00322afa578bd1e28968ca548e251f3c19165afb36eedfeb4f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-31 00:30:40', '2025-10-30 16:30:58', '2025-10-31 00:30:58', 0, 1, '2025-10-30 16:30:40', NULL, NULL, NULL, NULL),
(944, 236, '07043c2040baab432fb9d09053c6022405b4cf607cfe0edcbbe722e24e999981', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-31 00:31:18', '2025-10-31 06:44:43', NULL, 0, 1, '2025-10-30 16:31:18', NULL, NULL, NULL, NULL),
(948, 236, 'b5e9595685d57f04a694608c855385986e0b8eed14882639454473da34f747a5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-10-31 14:44:43', '2025-10-31 09:41:01', NULL, 0, 1, '2025-10-31 06:44:43', NULL, NULL, NULL, NULL),
(955, 236, '787796308cb30766fec5a8189088f5eceb61c07dc77cca2d117f2f2067ebc6b3', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-31 17:41:01', '2025-10-31 10:51:42', '2025-10-31 18:51:42', 0, 1, '2025-10-31 09:41:01', NULL, NULL, NULL, NULL),
(957, 236, '2221b7bdfc6ca1da367a81067ec6ecf8ebb3c4f74411e2d34dc8e065c48be069', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-10-31 18:06:27', '2025-10-31 11:04:01', NULL, 0, 1, '2025-10-31 10:06:27', NULL, NULL, NULL, NULL),
(958, 236, '3e9cef018158b4b3cb8084bc5abb786b7cb47b087f0ea54e101bbda1932c2aec', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-31 19:04:01', '2025-10-31 12:46:41', NULL, 0, 1, '2025-10-31 11:04:01', NULL, NULL, NULL, NULL),
(962, 236, '98f032bf761bce647bc5cd2a56e4760fd8cc31a9af17220918353b4fb4e652a0', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-31 20:46:41', '2025-10-31 16:56:59', NULL, 0, 1, '2025-10-31 12:46:41', NULL, NULL, NULL, NULL),
(964, 236, 'ada4db85f1bc0175108f0eaa36c0d9e55b063731e68a0d59d0226b9c8137b202', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-11-01 01:38:04', '2025-10-31 18:04:51', NULL, 0, 1, '2025-10-31 17:38:04', NULL, NULL, NULL, NULL),
(971, 236, 'eb7caa92f52861a6edadc2f483de2dba8ba38ce3c4d3a81031243a9f743b48e2', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-11-01 12:20:52', '2025-11-01 04:20:59', '2025-11-01 12:20:59', 0, 1, '2025-11-01 04:20:52', NULL, NULL, NULL, NULL),
(976, 236, 'e87b5bafc978bb1c32a5567e1343db3df4df35f16e4d14d4ab6cf7441616aa78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-01 19:21:27', '2025-11-01 11:30:58', NULL, 0, 1, '2025-11-01 11:21:27', NULL, NULL, NULL, NULL),
(977, 236, 'a588e89c5f894b98e753057fb51f074071a09b10405270ef5f879525709600e7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-01 19:30:58', '2025-11-01 15:29:49', NULL, 0, 1, '2025-11-01 11:30:58', NULL, NULL, NULL, NULL),
(980, 236, 'c8f23bbd4034e0d7eceabf0f7d8960b334b3fe498c9a2db6284b4fbc1d583407', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-01 23:34:53', '2025-11-01 15:53:10', NULL, 0, 1, '2025-11-01 15:34:53', NULL, NULL, NULL, NULL),
(981, 236, 'e8348821f1f1036615b58283f52f780e214c3930a5d617ba7cf8ee3c09abbbb2', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-11-01 23:53:10', '2025-11-03 09:37:09', NULL, 0, 1, '2025-11-01 15:53:10', NULL, NULL, NULL, NULL),
(983, 236, 'e66b3d8d5e6d60a0e8f46fd644daa4387967ac94cacb3ebc3c2a7b8d6b9f39ac', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 17:37:09', '2025-11-03 10:50:48', NULL, 0, 1, '2025-11-03 09:37:09', NULL, NULL, NULL, NULL),
(986, 236, '5425c615f56e7da36f10b0ded3477dc176b43d0264f01e56972d9f9229cc9510', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 18:50:48', '2025-11-03 12:19:11', NULL, 0, 1, '2025-11-03 10:50:48', NULL, NULL, NULL, NULL),
(989, 236, '7cf5eb0fd7490332213314d6c60584576b98089cb3d7d5e6624f8d4ea94c3292', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 20:19:11', '2025-11-03 12:59:48', NULL, 0, 1, '2025-11-03 12:19:11', NULL, NULL, NULL, NULL),
(991, 236, '29e88a92ae14d603f1d415e1fe9680cde4cd0dcdaef7f096137f0a0aaa01dd7e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 20:59:48', '2025-11-03 13:27:44', NULL, 0, 1, '2025-11-03 12:59:48', NULL, NULL, NULL, NULL),
(992, 236, '0e23d4c9aa1fad5e4f8066a408ac8812f604ba531021743a7e3aabbf05411821', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-11-03 21:27:44', '2025-11-03 13:28:17', '2025-11-03 21:28:17', 0, 1, '2025-11-03 13:27:44', NULL, NULL, NULL, NULL),
(994, 236, '4203f73e2fbfc0f62187bf934272df8fb905d372b9ad0cdf434dfd4e77c36595', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 22:10:40', '2025-11-03 17:55:46', NULL, 0, 1, '2025-11-03 14:10:40', NULL, NULL, NULL, NULL),
(998, 236, '2dfd5f31e7c81edebac615da311fba928a1bdd1fb558fcba5a32cb99242d8f82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 01:55:46', '2025-11-03 18:39:51', '2025-11-04 02:39:51', 0, 1, '2025-11-03 17:55:46', NULL, NULL, NULL, NULL),
(1001, 236, 'bdf166fa049947a38bda79164807dedf489e0214dbc2d3beabc398c1e20e8c0b', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-11-04 02:32:04', '2025-11-03 18:37:16', NULL, 0, 1, '2025-11-03 18:32:04', NULL, NULL, NULL, NULL),
(1002, 236, '75970cd0fa22e87da73690488bf31fb5e0f9a4808d8179286a1516897dffbe95', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-11-04 02:37:16', '2025-11-03 23:33:30', NULL, 0, 1, '2025-11-03 18:37:16', NULL, NULL, NULL, NULL),
(1004, 236, '0d391d3f58a4ebe5f44e2a6d4b870dd1382fc9ff577c62d863bf9bf88646ffb2', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-11-04 07:33:30', '2025-11-04 15:46:11', NULL, 0, 1, '2025-11-03 23:33:30', NULL, NULL, NULL, NULL),
(1005, 236, 'e2bd6fb3101edaaf8b13fad48cfd0c8095441721d092b3a01005a83b2b3e850f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.160', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 23:46:11', '2025-11-04 17:30:08', NULL, 0, 1, '2025-11-04 15:46:11', NULL, NULL, NULL, NULL),
(1006, 236, '1b885943e97e42d4dd1575b9bab0b59bb7fd8f832ae9735cfe21390747bc3891', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.193.160', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-11-05 00:22:45', '2025-11-04 18:00:44', NULL, 0, 1, '2025-11-04 16:22:45', NULL, NULL, NULL, NULL),
(1007, 236, '6d01040b9f0bb40f5ec0f34f1c1eedaa9e52a18e07cabc6b49b10ebc55ebe59d', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.193.160', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-05 02:00:44', '2025-11-04 18:00:49', '2025-11-05 02:00:49', 0, 1, '2025-11-04 18:00:44', NULL, NULL, NULL, NULL),
(1009, 236, 'b4facac2e67a8197ccec4589c7862b78f2c59be6373f9174df25631edcf3bbc4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.160', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 12:52:57', '2025-11-05 06:30:00', NULL, 0, 1, '2025-11-05 04:52:57', NULL, NULL, NULL, NULL),
(1011, 236, 'b743c51f64b6374b1177a8d9dc496373eef4a99115c9cdb8e6935d597758b505', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.160', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 14:30:00', '2025-11-05 16:53:14', NULL, 0, 1, '2025-11-05 06:30:00', NULL, NULL, NULL, NULL),
(1016, 236, '71734b4476fc56ecdfca560065c4e4ec58f7524f6391acbcbbebd22d23112a4b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 00:53:14', '2025-11-06 06:26:07', NULL, 0, 1, '2025-11-05 16:53:14', NULL, NULL, NULL, NULL),
(1020, 236, '47cf9911b70a9af0fd60f3edcad0da14744acb88cce05a6471104ac66f3ee65f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 14:26:07', '2025-11-06 12:58:13', NULL, 0, 1, '2025-11-06 06:26:07', NULL, NULL, NULL, NULL),
(1023, 236, 'aec431e206953b3465b0252a5a46195a146856d822990d61bf9e9400419c009b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 20:58:13', '2025-11-06 18:02:09', NULL, 0, 1, '2025-11-06 12:58:13', NULL, NULL, NULL, NULL),
(1024, 236, '2c9fafaed0de016f3ae7dcc323df8dcd0a021e1d1ac0e070eeb32281d2a482b5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 02:02:09', '2025-11-08 07:49:27', NULL, 0, 1, '2025-11-06 18:02:09', NULL, NULL, NULL, NULL),
(1030, 236, '73925fc15cef9bfc8258439043a18a16b17d8807f4b9db7309661c81cb49cc7a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 15:49:27', '2025-11-08 08:28:48', NULL, 0, 1, '2025-11-08 07:49:27', NULL, NULL, NULL, NULL),
(1033, 236, '88b2bc9cb087957fee6802169e753dd851275c9177782aa3e0a03e7eb55c681a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 16:11:20', '2025-11-09 13:10:29', NULL, 0, 1, '2025-11-08 08:11:20', NULL, NULL, NULL, NULL),
(1035, 236, 'f795de72c5ad96f9d1d9f124b968d2a3ca8a6246e29e13baca54e06dd1046b7c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-09 21:10:29', '2025-11-09 13:10:36', '2025-11-09 21:10:36', 0, 1, '2025-11-09 13:10:29', NULL, NULL, NULL, NULL),
(1036, 236, '106fe0f5303ee70063a23b8754fb1177cd704414c808ec7f65f359ddf90ace50', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-09 21:10:49', '2025-11-11 03:47:28', NULL, 0, 1, '2025-11-09 13:10:49', NULL, NULL, NULL, NULL),
(1037, 236, '2f4cf607aa0feb66aa7c6b848c9073ac0ff1eeb6c843177563adc8e636cdb803', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-11 11:47:28', '2025-11-12 03:22:00', NULL, 0, 1, '2025-11-11 03:47:28', NULL, NULL, NULL, NULL),
(1039, 236, 'fa77163833df46da20b2a5b03ed8db18b0c1fc6c3c724af3ac7a7f6227d5fa67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-12 11:22:00', '2025-11-12 16:05:14', NULL, 0, 1, '2025-11-12 03:22:00', NULL, NULL, NULL, NULL),
(1042, 236, '3ae985ff1adee452e144f5cd478887ab2b889d0c686ccb955bfd63c1f9cbb114', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-13 00:05:14', '2025-11-17 06:03:00', NULL, 0, 1, '2025-11-12 16:05:14', NULL, NULL, NULL, NULL),
(1049, 278, '5f04ac976ec71a4ec54fc61fb96753dddbe8d0b0a2f78185e68e0311a4276210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-13 19:57:16', '2025-11-13 12:05:21', NULL, 0, 0, '2025-11-13 11:57:16', NULL, NULL, NULL, NULL),
(1050, 278, 'daab4978c370499f34ddb7691d7acc3b2138e31194dbc79fbe4c6b92e0b2e969', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-13 20:05:21', '2025-11-13 12:43:48', NULL, 0, 1, '2025-11-13 12:05:21', NULL, NULL, NULL, NULL),
(1051, 248, '586c5f6f78ae66073ad3d81f098012dd7f58b505e4d05e97602938f7a6f6909b', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '124.105.221.209', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-13 20:06:48', '2025-11-13 13:34:01', NULL, 0, 0, '2025-11-13 12:06:48', NULL, NULL, NULL, NULL),
(1053, 278, 'e77a741ef127b9bb70b54c62e01ba31cb03f855ee2782d97a8b2955d8cb1573f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-13 20:43:48', '2025-11-13 12:47:49', '2025-11-13 20:47:49', 0, 1, '2025-11-13 12:43:48', NULL, NULL, NULL, NULL),
(1059, 278, '229dc8726d0982f8f8cf6f8ac3cc5a5608bb8305276ef7c79cb6b27d88527cc8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-13 21:14:02', '2025-11-13 13:15:52', '2025-11-13 21:15:52', 0, 1, '2025-11-13 13:14:02', NULL, NULL, NULL, NULL),
(1061, 278, '8d54b2bbf04edc54d22a4fe5ff273d32d5680853cfac8b14697d4fb7083a5329', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-13 21:29:15', '2025-11-13 14:40:51', '2025-11-13 22:40:51', 0, 1, '2025-11-13 13:29:15', NULL, NULL, NULL, NULL),
(1063, 248, 'a491d7ad4384a550025dfc7436b349a31b8b393dbd2ead001f8d36d124f143ce', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '124.217.19.185', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-13 21:34:01', '2025-12-17 02:07:32', NULL, 0, 0, '2025-11-13 13:34:01', NULL, NULL, NULL, NULL),
(1065, 278, 'a2bd084d7d816b704d3b312cfba93dc371b0f83520d17e8bbe759850bbb6e4d5', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '124.217.19.185', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-13 21:39:53', '2025-11-13 14:29:28', NULL, 0, 1, '2025-11-13 13:39:53', NULL, NULL, NULL, NULL),
(1069, 278, 'f8146a2d21dfee7593046dbec20f0f62425fc35013bf9f41f5c620615d2e17ae', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-13 22:29:28', '2025-11-13 14:44:43', NULL, 0, 1, '2025-11-13 14:29:28', NULL, NULL, NULL, NULL),
(1073, 278, '9837db730b8770593bfaeeca363605f34676db06bee6d5824401de1538b07406', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-13 22:44:43', '2025-11-13 14:53:25', NULL, 0, 1, '2025-11-13 14:44:43', NULL, NULL, NULL, NULL),
(1075, 278, '62f6c66d802bc982eab53f15834d9a1fbfa29e1c66c22078aac2bb017052326e', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '124.217.19.185', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-13 22:53:25', '2025-11-13 15:27:50', NULL, 0, 1, '2025-11-13 14:53:25', NULL, NULL, NULL, NULL),
(1076, 278, '767009239fc70382c8278c7e65766f46a37d3242f3b139f353b560706c052bf7', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-13 23:27:50', '2025-11-13 15:27:57', '2025-11-13 23:27:57', 0, 1, '2025-11-13 15:27:50', NULL, NULL, NULL, NULL),
(1082, 281, 'ca6654def464bb5731a85674707ec603da19c5e210828227c0611f799c1d81bf', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '136.158.240.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-14 08:40:07', '2026-03-11 00:20:59', NULL, 0, 0, '2025-11-14 00:40:07', NULL, NULL, NULL, NULL),
(1083, 278, 'f06851f8e7ecae04c1c642bed28e8a5ff720c8c893f47e5a477a98193353f3ad', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '175.176.75.40', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-14 10:10:12', '2025-11-14 02:30:53', NULL, 0, 1, '2025-11-14 02:10:12', NULL, NULL, NULL, NULL),
(1084, 278, '6a7add11f834276bdf56d50cbc1c0079059dfcb0d6616cd107caafdf44ec19b5', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '136.158.240.130', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-14 10:30:53', '2025-11-14 02:31:05', '2025-11-14 10:31:05', 0, 1, '2025-11-14 02:30:53', NULL, NULL, NULL, NULL),
(1085, 278, 'add2c51488ddd22ef406f61aa717930314a8c0f0e81c143e73299341f629b977', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '136.158.240.130', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-14 10:31:23', '2025-11-14 02:32:01', NULL, 0, 1, '2025-11-14 02:31:23', NULL, NULL, NULL, NULL),
(1086, 278, 'f5bd271dac23390c1533773611641102b1c3f5835ad0a1a397d53bbcdd12a7fd', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '136.158.240.130', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-14 10:32:01', '2025-11-17 05:54:43', NULL, 0, 1, '2025-11-14 02:32:01', NULL, NULL, NULL, NULL),
(1087, 282, '39602e58d36940bb72fd76b65fb3b177df739aaa894caad48a63e76ced16ffa3', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.47.109;]', '49.147.199.6', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.47.109;]', '2025-11-15 11:24:43', '2025-11-15 03:26:15', NULL, 0, 0, '2025-11-15 03:24:43', NULL, NULL, NULL, NULL),
(1088, 282, '47b17bce41ac53f1e6b9f6238bdc3b8ef562937877353e3a1b306de0f66420da', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.47.109;]', '49.147.199.6', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.47.109;]', '2025-11-15 11:26:15', '2025-11-15 03:27:51', '2025-11-15 11:27:51', 0, 0, '2025-11-15 03:26:15', NULL, NULL, NULL, NULL),
(1100, 282, '56f498cfe0c31c8cd0829bcc4c9965a3ddb03ade9a26b9cbc94c13416a2de7cc', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/533.0.0.47.109;]', '110.54.218.182', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/533.0.0.47.109;]', '2025-11-17 07:22:37', '2025-11-16 23:23:33', '2025-11-17 07:23:33', 0, 0, '2025-11-16 23:22:37', NULL, NULL, NULL, NULL),
(1101, 282, 'd05a2e1306f5cf2895b604a74a2fd02b5c961a01b5ee649c3a83f8e957e8d85a', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/533.0.0.47.109;]', '110.54.218.182', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/533.0.0.47.109;]', '2025-11-17 07:53:19', '2025-11-16 23:53:19', NULL, 1, 0, '2025-11-16 23:53:19', NULL, NULL, NULL, NULL),
(1103, 278, 'bc69a8e2375975967a946a9596550a1171d696e0f9becc397ecd523e15e7d09f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '131.226.111.182', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-17 13:54:43', '2025-12-17 02:11:19', NULL, 0, 1, '2025-11-17 05:54:43', NULL, NULL, NULL, NULL),
(1104, 236, '83939630c7eb81a62949bff47d5e8161ee13e81fb3ca8083820a224aa4255678', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-17 14:03:00', '2025-11-17 07:36:17', NULL, 0, 1, '2025-11-17 06:03:00', NULL, NULL, NULL, NULL),
(1105, 236, 'cf3e344d04c542ab7ae4e98bfa90dc0ea3df06b5b72c894d982028c0bf287b43', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-17 15:04:00', '2025-11-17 08:21:50', NULL, 0, 1, '2025-11-17 07:04:00', NULL, NULL, NULL, NULL),
(1106, 236, '439889847ace6538b2e8eca47ea04d5b234208d458042f239588bb79c19b4758', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-17 16:21:50', '2025-11-17 08:25:43', '2025-11-17 16:25:43', 0, 1, '2025-11-17 08:21:50', NULL, NULL, NULL, NULL),
(1107, 236, 'a520be2fc405a7d1534c11c437b4ed11d282cc8c65c636bf21e8a46f576df0c6', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/14.9.0.4', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/14.9.0.4', '2025-11-17 16:26:33', '2025-11-17 10:11:13', NULL, 0, 1, '2025-11-17 08:26:33', NULL, NULL, NULL, NULL),
(1108, 236, '9c26ad8f72896b42787d8c20dbfa3898efd8a9f91c91009bc563ab5a217bc44c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-17 18:11:13', '2025-11-17 10:31:28', NULL, 0, 1, '2025-11-17 10:11:13', NULL, NULL, NULL, NULL),
(1109, 236, '4f07c7eb7e89fcd09a7e1f88487ab9450e315c43d6aebfeea1420c31703df523', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/15.0.0.0', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/15.0.0.0', '2025-11-17 18:17:00', '2025-11-17 11:14:47', NULL, 0, 1, '2025-11-17 10:17:00', NULL, NULL, NULL, NULL),
(1110, 236, 'b3e3318f8ecf142532f2182c6aa76e83187f110db0b424fe1024bc40c079d2e2', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-17 19:14:47', '2025-11-17 16:53:42', NULL, 0, 1, '2025-11-17 11:14:47', NULL, NULL, NULL, NULL),
(1112, 236, '625848e92582cd6ebf8239e23197d0d337acf05207d71aaac9c7014207a935a1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-18 00:53:42', '2025-11-17 17:09:02', NULL, 0, 1, '2025-11-17 16:53:42', NULL, NULL, NULL, NULL),
(1113, 236, '384a7a408a0df01c69de343ab3446973cd27887d126d1228d654bc778a2b4eca', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-18 01:09:02', '2025-11-18 06:45:13', NULL, 0, 1, '2025-11-17 17:09:02', NULL, NULL, NULL, NULL),
(1114, 236, 'af350818bf09a169933417c878178a9add7b0777a4171fac2357bb5f1e9a7976', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-18 14:45:13', '2025-11-18 07:39:17', NULL, 0, 1, '2025-11-18 06:45:13', NULL, NULL, NULL, NULL),
(1115, 236, '5630cd2f2ea1c38afcecbac9e92b60c7af94b350a106008883198ace012c46b6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-18 15:05:29', '2025-11-18 07:48:13', NULL, 0, 1, '2025-11-18 07:05:29', NULL, NULL, NULL, NULL),
(1116, 236, '07d9f350c0563608507e5de8d2a9a50c1ff0f8f7cbb9a51af0d22a730b3ee23c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-18 15:05:56', '2025-11-18 07:15:23', NULL, 0, 1, '2025-11-18 07:05:56', NULL, NULL, NULL, NULL),
(1117, 236, '610378dc87fea0a83acb88baa2489bd5da4a57962800c8bb73c7c54bd4bce437', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/15.0.0.0', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/15.0.0.0', '2025-11-18 15:15:23', '2025-11-18 07:46:37', NULL, 0, 1, '2025-11-18 07:15:23', NULL, NULL, NULL, NULL),
(1118, 236, 'dc0cf4748ef5c9bbca199df07a2170d66a57449c588c2977bfa65c269404d038', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-18 15:39:51', '2025-11-18 09:50:41', NULL, 0, 1, '2025-11-18 07:39:51', NULL, NULL, NULL, NULL),
(1119, 236, '4efcc6b489f1616be49176353f6f26872cb0e2fed6367e696f4b79e52b8ba0e0', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-18 17:50:41', '2025-11-18 11:25:08', NULL, 0, 1, '2025-11-18 09:50:41', NULL, NULL, NULL, NULL),
(1120, 236, '66824b0c9379209ca70e8f7b66b8bdf666429fb8f1c56a7b9d8b49b139b3bba9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-18 19:25:08', '2025-11-21 09:40:20', NULL, 0, 1, '2025-11-18 11:25:08', NULL, NULL, NULL, NULL),
(1123, 236, '22302cb2e3050b863afe72f0cc0927346733f1546360be197ff4e19b41e6d3dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 17:40:20', '2025-12-17 10:15:15', NULL, 0, 1, '2025-11-21 09:40:20', NULL, NULL, NULL, NULL),
(1126, 248, 'a6126681d5a1bbbf2b8d8b28eb0296887cc031525598378ff105b81d3e90c79c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '58.69.2.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-17 10:07:32', '2025-12-29 07:00:11', NULL, 0, 0, '2025-12-17 02:07:32', NULL, NULL, NULL, NULL),
(1127, 278, 'bd6d1deb5c6e67dcb25ae39c27d0d0f67b3144a3d1a5f649da824242fc0c8132', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', '175.176.72.54', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', '2025-12-17 10:11:19', '2026-02-01 05:49:56', NULL, 0, 1, '2025-12-17 02:11:19', NULL, NULL, NULL, NULL);
INSERT INTO `active_sessions` (`id`, `user_id`, `session_id`, `device_info`, `ip_address`, `user_agent`, `login_time`, `last_activity`, `logout_time`, `is_current`, `is_2fa_verified`, `created_at`, `location`, `latitude`, `longitude`, `location_accuracy`) VALUES
(1129, 236, 'ed6b9c4257dfed31fba063674f9df409b63366a6810b257ec8843d4211a81138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '175.176.77.160', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-17 18:15:15', '2025-12-17 10:33:49', NULL, 0, 1, '2025-12-17 10:15:15', NULL, NULL, NULL, NULL),
(1130, 236, '08a3b72f12d012c7022b26e7d5493f561cfdaf3e461f6af10703601b0be03386', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '175.176.77.160', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-17 18:33:49', '2025-12-20 09:25:49', NULL, 0, 1, '2025-12-17 10:33:49', NULL, NULL, NULL, NULL),
(1131, 236, '3da7b322b1d045d10b30cbf83c2e7f9dc1323982983aa33ea95562b6f425b054', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-20 17:25:49', '2025-12-20 09:54:02', NULL, 0, 1, '2025-12-20 09:25:49', NULL, NULL, NULL, NULL),
(1132, 236, '1eda4dd8115d6d76643eb3a0be465408906dfc472ab23c815ca53e3aa3c0be54', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-20 17:37:00', '2025-12-21 01:29:21', NULL, 0, 1, '2025-12-20 09:37:00', NULL, NULL, NULL, NULL),
(1133, 236, '44983ebb93e1102b202fa87a971b2ec40f08826fe5e2d0c5934f0f9b5f150e38', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-21 09:29:21', '2025-12-21 01:37:52', NULL, 0, 1, '2025-12-21 01:29:21', NULL, NULL, NULL, NULL),
(1134, 236, '4e1e19e504205b0138ca22dd95d456796e66242663b36dc12697e6b8224d67bd', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-21 09:37:52', '2025-12-21 01:38:08', '2025-12-21 09:38:08', 0, 1, '2025-12-21 01:37:52', NULL, NULL, NULL, NULL),
(1135, 236, '957de35526ef190d3bf622284fdedfa1be35931c2c84643b15b4c5cbe4896c73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-21 09:44:54', '2025-12-21 01:55:42', '2025-12-21 09:55:42', 0, 1, '2025-12-21 01:44:54', NULL, NULL, NULL, NULL),
(1136, 236, '545e325ebff8c04fa3a115f9adc5813c0d05dbccddc520d64ea1fc12a64535b1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '175.176.76.244', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2025-12-21 15:17:11', '2025-12-26 23:17:17', NULL, 0, 1, '2025-12-21 07:17:11', NULL, NULL, NULL, NULL),
(1137, 285, 'ef3551982924436903454bf3067c66c1d5070382c1e40b96e74952ab11c360e0', 'Mozilla/5.0 (Linux; Android 13; M2103K19PG Build/TP1A.220624.014; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.105 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/542.0.0.46.151;IABMV/1;]', '175.176.79.30', 'Mozilla/5.0 (Linux; Android 13; M2103K19PG Build/TP1A.220624.014; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.105 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/542.0.0.46.151;IABMV/1;]', '2025-12-21 21:14:17', '2025-12-21 13:16:45', NULL, 1, 0, '2025-12-21 13:14:17', NULL, NULL, NULL, NULL),
(1139, 286, 'd7b7e5206b6ec3f96ab62c374324f59c1d86d3170e4870d02155bdabd557b17e', 'Mozilla/5.0 (Linux; Android 15; RMX3710 Build/AP3A.240617.008; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.143 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/543.0.0.55.73;IABMV/1;]', '216.247.22.10', 'Mozilla/5.0 (Linux; Android 15; RMX3710 Build/AP3A.240617.008; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.143 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/543.0.0.55.73;IABMV/1;]', '2025-12-25 12:58:33', '2025-12-25 04:58:55', NULL, 1, 0, '2025-12-25 04:58:33', NULL, NULL, NULL, NULL),
(1140, 236, 'dbc0a845d12860c6fe5fb00b076f60b2f8347c060858978362be9457d8f7bb2e', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '49.147.197.192', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2025-12-27 07:17:17', '2025-12-26 23:33:15', NULL, 1, 1, '2025-12-26 23:17:17', NULL, NULL, NULL, NULL),
(1141, 248, '70bbe6af16efebbb1e347c207de7b6afa271e512f3e0f76d024f8f20298e1f5f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '58.69.2.186', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2025-12-29 15:00:11', '2025-12-29 07:01:02', NULL, 0, 0, '2025-12-29 07:00:11', NULL, NULL, NULL, NULL),
(1142, 248, 'e0105a9fa21c81e09eabf29f521dec97f2bc7db047783c3731974714d9d0c559', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '58.69.2.186', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2025-12-29 15:01:02', '2026-03-11 08:03:57', NULL, 0, 0, '2025-12-29 07:01:02', NULL, NULL, NULL, NULL),
(1146, 278, '0a8e265d71c9f55fe39de7c013063c6870630c7ce9301fd743bff5e4f17899d7', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '49.147.194.136', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-01 13:49:56', '2026-02-01 09:58:40', NULL, 0, 1, '2026-02-01 05:49:56', NULL, NULL, NULL, NULL),
(1147, 278, '27a05e95106e618532a5747212f978a421c97e61e3ce08d20f38b0c473c03700', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '49.147.194.136', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-01 17:58:40', '2026-02-04 01:23:27', NULL, 0, 1, '2026-02-01 09:58:40', NULL, NULL, NULL, NULL),
(1148, 278, 'a4a5a9f4c9a4647ae4b4a7d43ee67ff7a769dd062012e14b7cc501e773bfc8c7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '58.69.2.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '2026-02-04 09:23:27', '2026-02-04 05:10:10', NULL, 0, 1, '2026-02-04 01:23:27', NULL, NULL, NULL, NULL),
(1149, 288, '350a6dd46c8710fac08094c263231cb63cd35cb52616be7fd6fd7c28411c1af6', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '122.53.220.158', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 10:20:46', '2026-02-04 02:21:09', '2026-02-04 10:21:09', 0, 0, '2026-02-04 02:20:46', NULL, NULL, NULL, NULL),
(1150, 289, 'b866e177e148bd0e8163eee7f38340749d384b665de4a0b697b652fedb7ed29b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '198.176.84.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '2026-02-04 11:21:30', '2026-03-11 00:16:21', NULL, 0, 0, '2026-02-04 03:21:30', NULL, NULL, NULL, NULL),
(1151, 288, '362255a0c0790d3612677d36120803eea16640a51d533d012830cd28bfed3a64', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '122.53.220.158', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 12:39:19', '2026-02-04 05:08:57', '2026-02-04 13:08:57', 0, 0, '2026-02-04 04:39:19', NULL, NULL, NULL, NULL),
(1152, 278, '90fdaa18ad5f65b1ac231ee0fc35418c0ed476e04c32b973f27c8c4d3cb3862d', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '122.53.220.158', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 13:10:10', '2026-02-04 05:13:25', '2026-02-04 13:13:25', 0, 1, '2026-02-04 05:10:10', NULL, NULL, NULL, NULL),
(1153, 288, '3e17185132a792967c68b63c2a8a4a0fac98704792209e6db470c7bc19112237', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '49.157.104.61', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 15:48:16', '2026-02-05 07:54:28', '2026-02-05 15:54:28', 0, 0, '2026-02-05 07:48:16', NULL, NULL, NULL, NULL),
(1154, 278, 'fb4bb41cfc8f1f26c65bed80f62562727d2f2e89f95985cd70f38329fd4f6505', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '49.147.199.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 12:32:17', '2026-03-06 03:11:43', NULL, 0, 1, '2026-02-08 04:32:17', NULL, NULL, NULL, NULL),
(1156, 278, 'c45b11f150c5fb831afb642a9829f73046f72db494068509be2b8abf4eb0887c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '49.147.199.221', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-06 11:11:43', '2026-03-06 03:23:51', '2026-03-06 11:23:51', 0, 1, '2026-03-06 03:11:43', NULL, NULL, NULL, NULL),
(1158, 292, '358c6710fa4c157fbd26947f4be1466ded09a8e3813422a70c9a5d8e7e8f59db', 'Mozilla/5.0 (Linux; Android 15; TECNO LJ6 Build/AP3A.240905.015.A2; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', '49.150.51.217', 'Mozilla/5.0 (Linux; Android 15; TECNO LJ6 Build/AP3A.240905.015.A2; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', '2026-03-11 08:15:13', '2026-03-11 00:15:13', NULL, 1, 0, '2026-03-11 00:15:13', NULL, NULL, NULL, NULL),
(1159, 289, '2a2b866b870947980deede4c7912d119e3a4c0e6a9b28e226d1ff14d05ab059f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '58.69.2.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 08:16:21', '2026-03-11 00:32:46', NULL, 1, 0, '2026-03-11 00:16:21', NULL, NULL, NULL, NULL),
(1160, 278, '78dc975273f683a9cc3f065ac255827fa5b7ce94fc742f68a1df717196267622', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '58.69.2.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 08:20:19', '2026-03-11 06:52:04', NULL, 0, 1, '2026-03-11 00:20:19', NULL, NULL, NULL, NULL),
(1161, 281, '6cb8f5c4defb761b93be79502a4f16b07dc88fc18584c33c0796f910acc8f0f7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '143.44.196.232', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 08:20:59', '2026-03-11 00:22:20', '2026-03-11 08:22:20', 0, 0, '2026-03-11 00:20:59', NULL, NULL, NULL, NULL),
(1162, 293, '5627f812e1c331e84ecd355fc3961b704acd71b53f863c61fefd89a28582dd44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '143.44.196.232', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 08:26:51', '2026-03-11 01:19:52', NULL, 0, 0, '2026-03-11 00:26:51', NULL, NULL, NULL, NULL),
(1163, 278, '8ead1ab1bccf96bbd7305f46e80b9b535994799346f333f703c3d73216aff8ae', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '49.147.199.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 09:15:56', '2026-03-11 08:01:18', NULL, 0, 1, '2026-03-11 01:15:56', NULL, NULL, NULL, NULL),
(1164, 293, 'db59468516d9860a3cdb1c2fead3f8fb543cdd060a2a745dc28edf35261f813c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '143.44.196.232', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 09:19:52', '2026-03-11 01:43:54', NULL, 0, 0, '2026-03-11 01:19:52', NULL, NULL, NULL, NULL),
(1165, 294, '44ab78833431b14f308f27dd6386aee5483012099ab784cbc7776cff60686994', 'Mozilla/5.0 (Linux; Android 13; V2038 Build/TP1A.220624.014; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', '110.54.228.144', 'Mozilla/5.0 (Linux; Android 13; V2038 Build/TP1A.220624.014; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', '2026-03-11 09:26:58', '2026-03-11 01:27:40', NULL, 1, 0, '2026-03-11 01:26:58', NULL, NULL, NULL, NULL),
(1166, 295, '8c723edb92e9f7317569084862deba1cb01c04150fbae098d1fbc66bfbc75315', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 [FBAN/FBIOS;FBAV/551.0.0.27.107;FBBV/897058033;FBDV/iPhone17,3;FBMD/iPhone;FBSN/iOS;FBSV/26.3;FBSS/3;FBCR/;FBID/phone;FBLC/en_US;FBOP/80]', '216.247.23.140', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 [FBAN/FBIOS;FBAV/551.0.0.27.107;FBBV/897058033;FBDV/iPhone17,3;FBMD/iPhone;FBSN/iOS;FBSV/26.3;FBSS/3;FBCR/;FBID/phone;FBLC/en_US;FBOP/80]', '2026-03-11 09:40:39', '2026-03-11 01:41:27', NULL, 1, 0, '2026-03-11 01:40:39', NULL, NULL, NULL, NULL),
(1167, 293, 'bff1b8bedde00f59a534ea0fd39bdae7b3ebad8ebf1759952fae45dd77419b26', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '49.147.199.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 09:43:54', '2026-03-11 03:24:41', NULL, 1, 1, '2026-03-11 01:43:54', NULL, NULL, NULL, NULL),
(1168, 296, '531d7fe18e6ddca976b47eee6b629d0f2c7648738f0211448d9927e4324b47e0', 'Mozilla/5.0 (Linux; Android 15; 23129RAA4G Build/AQ3A.240829.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', '112.208.78.217', 'Mozilla/5.0 (Linux; Android 15; 23129RAA4G Build/AQ3A.240829.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', '2026-03-11 12:01:20', '2026-03-11 04:02:25', NULL, 1, 0, '2026-03-11 04:01:20', NULL, NULL, NULL, NULL),
(1169, 297, '93c7cd30968ab82eba249a95fd198ce4d80a5157f9f604d3ee79f8eb7c46c928', 'Mozilla/5.0 (Linux; Android 13; M2103K19G Build/TP1A.220624.014; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', '124.217.27.139', 'Mozilla/5.0 (Linux; Android 13; M2103K19G Build/TP1A.220624.014; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', '2026-03-11 13:21:55', '2026-03-11 05:22:29', NULL, 1, 0, '2026-03-11 05:21:55', NULL, NULL, NULL, NULL),
(1170, 278, 'abfc82bfd972fb4271abdff77d8d74c227862a289fe2bdeeeef4947e7d61d36f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '203.84.189.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-11 16:01:18', '2026-03-11 08:01:32', '2026-03-11 16:01:32', 0, 1, '2026-03-11 08:01:18', NULL, NULL, NULL, NULL),
(1171, 248, 'f42cc82e4bd71c9b49a3837e47274ae22ff8c961fee601b4751619f2cb97fd68', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '203.84.189.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-11 16:03:57', '2026-03-11 08:44:00', NULL, 0, 0, '2026-03-11 08:03:57', NULL, NULL, NULL, NULL),
(1172, 298, '9e306c88ebdc033c2fd36035633730d4a13e93520f072ec438e5b267657ce81d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '112.198.112.136', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 16:07:15', '2026-03-11 08:42:43', '2026-03-11 16:42:43', 0, 0, '2026-03-11 08:07:15', NULL, NULL, NULL, NULL),
(1173, 278, '2324530be45c5649b55550e8ecf251c58978ca6a7230480f4f7fef01da463984', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', '112.198.112.136', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', '2026-03-11 16:41:57', '2026-03-11 08:47:39', NULL, 1, 1, '2026-03-11 08:41:57', NULL, NULL, NULL, NULL),
(1174, 248, '12e7124d208bd285332f53b7c751628bcbab16c026ca968bfd4d9c75dc24a4d0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '112.198.112.136', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 16:44:00', '2026-03-11 08:44:00', NULL, 1, 0, '2026-03-11 08:44:00', NULL, NULL, NULL, NULL),
(1175, 298, 'efe2f4a7271ba3c97a00c06728b0735b26710c2e96a90b984a57f14d6ad2698b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '143.44.196.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 14:27:49', '2026-03-18 06:28:56', NULL, 1, 0, '2026-03-18 06:27:49', NULL, NULL, NULL, NULL),
(1176, 299, 'b58ab30dec7b20cbc1b4b9cba0fa761c1bd9d6359be6574e5ff362a443f81fa4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '180.190.238.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-21 14:27:36', '2026-03-21 06:37:15', NULL, 1, 0, '2026-03-21 06:27:36', NULL, NULL, NULL, NULL),
(1177, 300, 'fee14d7b527a61fbc05816c110768e802b937d4e74391f60d8a9fa714836bd57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '49.147.199.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-27 14:17:16', '2026-03-28 11:12:56', NULL, 0, 0, '2026-03-27 06:17:16', NULL, NULL, NULL, NULL),
(1178, 300, 'd119e8837ecfce1ba937baf9326766860495bbaecfd920c8e7b7ad0319ac69e0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '49.147.193.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 19:12:56', '2026-03-28 11:25:27', NULL, 1, 0, '2026-03-28 11:12:56', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(20) NOT NULL COMMENT 'LOGIN, LOGOUT',
  `device_info` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `attempts_remaining` int(11) DEFAULT NULL,
  `activity_time` datetime NOT NULL DEFAULT current_timestamp(),
  `location` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `activity_type`, `device_info`, `ip_address`, `user_agent`, `attempts_remaining`, `activity_time`, `location`, `latitude`, `longitude`) VALUES
(663, 236, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-16 14:32:02', NULL, NULL, NULL),
(664, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-16 17:27:58', NULL, NULL, NULL),
(665, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-17 16:16:48', NULL, NULL, NULL),
(666, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:49:02', NULL, NULL, NULL),
(667, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:50:36', NULL, NULL, NULL),
(668, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:50:41', NULL, NULL, NULL),
(669, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:50:50', NULL, NULL, NULL),
(670, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:51:51', NULL, NULL, NULL),
(671, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:51:55', NULL, NULL, NULL),
(672, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:52:05', NULL, NULL, NULL),
(673, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:52:42', NULL, NULL, NULL),
(674, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:52:46', NULL, NULL, NULL),
(675, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:52:53', NULL, NULL, NULL),
(676, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:53:35', NULL, NULL, NULL),
(677, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:55:08', NULL, NULL, NULL),
(678, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:55:40', NULL, NULL, NULL),
(679, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:55:44', NULL, NULL, NULL),
(680, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:55:53', NULL, NULL, NULL),
(681, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:56:20', NULL, NULL, NULL),
(682, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:56:24', NULL, NULL, NULL),
(683, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:56:33', NULL, NULL, NULL),
(684, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 10:57:11', NULL, NULL, NULL),
(687, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 11:08:43', NULL, NULL, NULL),
(700, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 11:32:01', NULL, NULL, NULL),
(701, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 11:32:05', NULL, NULL, NULL),
(702, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 11:33:17', NULL, NULL, NULL),
(703, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 11:33:37', NULL, NULL, NULL),
(704, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 11:33:47', NULL, NULL, NULL),
(705, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 11:33:54', NULL, NULL, NULL),
(706, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 11:33:59', NULL, NULL, NULL),
(707, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 11:34:03', NULL, NULL, NULL),
(708, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 11:36:54', NULL, NULL, NULL),
(709, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 11:36:58', NULL, NULL, NULL),
(713, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 13:48:17', NULL, NULL, NULL),
(716, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 16:07:27', NULL, NULL, NULL),
(717, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 16:42:10', NULL, NULL, NULL),
(718, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-18 16:44:56', NULL, NULL, NULL),
(727, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-20 12:55:23', NULL, NULL, NULL),
(728, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-20 15:28:16', NULL, NULL, NULL),
(729, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-20 15:29:46', NULL, NULL, NULL),
(730, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-20 15:29:54', NULL, NULL, NULL),
(731, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-20 15:30:01', NULL, NULL, NULL),
(732, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-20 16:12:56', NULL, NULL, NULL),
(733, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-20 16:13:07', NULL, NULL, NULL),
(734, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-20 16:35:41', NULL, NULL, NULL),
(736, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-20 20:56:58', NULL, NULL, NULL),
(738, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-20 21:05:20', NULL, NULL, NULL),
(739, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-20 21:24:58', NULL, NULL, NULL),
(746, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-21 01:40:57', NULL, NULL, NULL),
(747, 236, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-21 01:41:06', NULL, NULL, NULL),
(748, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-21 01:42:45', NULL, NULL, NULL),
(749, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-21 01:43:16', NULL, NULL, NULL),
(754, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-22 11:16:26', NULL, NULL, NULL),
(757, 240, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-22 12:44:03', NULL, NULL, NULL),
(782, 248, 'SIGNUP', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.182.36', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-23 21:52:49', NULL, NULL, NULL),
(798, 240, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.147.198.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-26 07:18:49', NULL, NULL, NULL),
(802, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.147.198.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-26 10:21:59', NULL, NULL, NULL),
(803, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.147.198.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-26 10:43:26', NULL, NULL, NULL),
(810, 240, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.147.198.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-26 14:39:52', NULL, NULL, NULL),
(815, 254, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '112.198.234.197', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-27 02:17:41', NULL, NULL, NULL),
(816, 254, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '112.198.234.197', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-27 02:26:57', NULL, NULL, NULL),
(825, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.147.198.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-27 03:10:35', NULL, NULL, NULL),
(842, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.182.36', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-27 06:25:11', NULL, NULL, NULL),
(847, 260, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Linux; Android 13; Infinix X6815C Build/TP1A.220624.014; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/142.0.7444.48 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.2.109;]', '203.84.189.253', 'Mozilla/5.0 (Linux; Android 13; Infinix X6815C Build/TP1A.220624.014; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/142.0.7444.48 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.2.109;]', NULL, '2025-10-27 07:36:36', NULL, NULL, NULL),
(848, 261, 'GOOGLE SIGNUP', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.147.198.204', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-27 07:41:38', NULL, NULL, NULL),
(849, 262, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Linux; Android 15; 23129RAA4G Build/AQ3A.240829.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.122 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/530.1.0.67.107;]', '221.121.102.243', 'Mozilla/5.0 (Linux; Android 15; 23129RAA4G Build/AQ3A.240829.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.122 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/530.1.0.67.107;]', NULL, '2025-10-27 07:42:32', NULL, NULL, NULL),
(854, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-27 23:07:35', NULL, NULL, NULL),
(857, 248, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-27 23:32:46', NULL, NULL, NULL),
(858, 267, 'SIGNUP', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-27 23:33:05', NULL, NULL, NULL),
(859, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-27 23:33:57', NULL, NULL, NULL),
(860, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-28 00:29:39', NULL, NULL, NULL),
(861, 267, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '180.190.238.22', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-28 00:29:49', NULL, NULL, NULL),
(862, 248, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', NULL, '2025-10-28 00:33:03', NULL, NULL, NULL),
(864, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-28 00:46:20', NULL, NULL, NULL),
(865, 248, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-28 00:47:40', NULL, NULL, NULL),
(866, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-28 00:48:03', NULL, NULL, NULL),
(867, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '180.190.238.22', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-28 01:10:39', NULL, NULL, NULL),
(868, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-28 01:13:33', NULL, NULL, NULL),
(869, 248, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.132.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-28 01:28:07', NULL, NULL, NULL),
(871, 269, 'SIGNUP', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '49.145.124.204', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', NULL, '2025-10-28 06:59:57', NULL, NULL, NULL),
(872, 269, 'LOGIN SUCCESS', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '49.145.124.204', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', NULL, '2025-10-28 07:01:13', NULL, NULL, NULL),
(904, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', '175.176.75.6', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', NULL, '2025-10-29 09:33:51', NULL, NULL, NULL),
(907, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', '180.190.238.22', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', NULL, '2025-10-29 11:19:01', NULL, NULL, NULL),
(910, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '122.55.157.208', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-29 18:10:49', NULL, NULL, NULL),
(911, 236, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '122.55.157.208', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-29 18:11:28', NULL, NULL, NULL),
(915, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-30 01:31:35', NULL, NULL, NULL),
(916, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.145.182.36', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-30 10:58:55', NULL, NULL, NULL),
(922, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-31 00:30:40', NULL, NULL, NULL),
(923, 236, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-31 00:30:58', NULL, NULL, NULL),
(924, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-31 00:31:06', NULL, NULL, NULL),
(925, 236, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-31 00:31:11', NULL, NULL, NULL),
(926, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-31 00:31:18', NULL, NULL, NULL),
(930, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-10-31 14:44:43', NULL, NULL, NULL),
(939, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-31 17:41:01', NULL, NULL, NULL),
(941, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-10-31 18:06:27', NULL, NULL, NULL),
(942, 236, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-31 18:51:42', NULL, NULL, NULL),
(943, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-31 19:04:01', NULL, NULL, NULL),
(947, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-10-31 20:46:41', NULL, NULL, NULL),
(948, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-01 00:56:59', NULL, NULL, NULL),
(949, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-11-01 01:38:04', NULL, NULL, NULL),
(950, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-11-01 02:04:51', NULL, NULL, NULL),
(955, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-01 12:19:35', NULL, NULL, NULL),
(956, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-11-01 12:20:52', NULL, NULL, NULL),
(957, 236, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-11-01 12:20:59', NULL, NULL, NULL),
(964, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-01 19:21:27', NULL, NULL, NULL),
(965, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-01 19:30:58', NULL, NULL, NULL),
(967, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-01 23:29:49', NULL, NULL, NULL),
(968, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-01 23:34:53', NULL, NULL, NULL),
(969, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.198.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-11-01 23:53:10', NULL, NULL, NULL),
(971, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-03 17:37:09', NULL, NULL, NULL),
(975, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-03 18:50:48', NULL, NULL, NULL),
(978, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-03 20:19:11', NULL, NULL, NULL),
(981, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-03 20:59:48', NULL, NULL, NULL),
(983, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-11-03 21:27:44', NULL, NULL, NULL),
(984, 236, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-11-03 21:28:17', NULL, NULL, NULL),
(986, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-03 22:10:40', NULL, NULL, NULL),
(990, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-04 01:55:46', NULL, NULL, NULL),
(993, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-11-04 02:32:04', NULL, NULL, NULL),
(994, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-11-04 02:37:16', NULL, NULL, NULL),
(995, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-04 02:39:51', NULL, NULL, NULL),
(997, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.196.116', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-11-04 07:33:30', NULL, NULL, NULL),
(998, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.160', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-04 23:46:11', NULL, NULL, NULL),
(999, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '49.147.193.160', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', NULL, '2025-11-05 00:22:45', NULL, NULL, NULL),
(1000, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.193.160', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-05 02:00:44', NULL, NULL, NULL),
(1001, 236, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.193.160', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-05 02:00:49', NULL, NULL, NULL),
(1003, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.160', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-05 12:52:57', NULL, NULL, NULL),
(1015, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-08 15:49:27', NULL, NULL, NULL),
(1018, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-08 16:11:20', NULL, NULL, NULL),
(1021, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-09 21:10:29', NULL, NULL, NULL),
(1022, 236, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-09 21:10:36', NULL, NULL, NULL),
(1023, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.196.201', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-09 21:10:49', NULL, NULL, NULL),
(1028, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 00:05:14', NULL, NULL, NULL),
(1036, 278, 'SIGNUP', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-13 19:57:16', NULL, NULL, NULL),
(1037, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-13 20:05:21', NULL, NULL, NULL),
(1038, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '124.105.221.209', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 20:06:48', NULL, NULL, NULL),
(1040, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 20:43:48', NULL, NULL, NULL),
(1041, 278, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 20:47:49', NULL, NULL, NULL),
(1050, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 21:14:02', NULL, NULL, NULL),
(1051, 278, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 21:15:52', NULL, NULL, NULL),
(1054, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 21:29:15', NULL, NULL, NULL),
(1056, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '124.217.19.185', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 21:34:01', NULL, NULL, NULL),
(1058, 248, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '124.217.19.185', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 21:38:46', NULL, NULL, NULL),
(1059, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '124.217.19.185', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 21:39:53', NULL, NULL, NULL),
(1063, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-13 22:29:28', NULL, NULL, NULL),
(1064, 278, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 22:40:51', NULL, NULL, NULL),
(1069, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 22:44:43', NULL, NULL, NULL),
(1071, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '124.217.19.185', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 22:53:25', NULL, NULL, NULL),
(1072, 278, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-13 23:27:57', NULL, NULL, NULL),
(1079, 281, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '136.158.240.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-14 08:40:07', NULL, NULL, NULL),
(1080, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '175.176.75.40', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-14 10:10:12', NULL, NULL, NULL),
(1081, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '136.158.240.130', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-14 10:30:53', NULL, NULL, NULL),
(1082, 278, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '136.158.240.130', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-14 10:31:05', NULL, NULL, NULL),
(1083, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '136.158.240.130', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-14 10:31:23', NULL, NULL, NULL),
(1084, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '136.158.240.130', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-14 10:32:01', NULL, NULL, NULL),
(1085, 282, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.47.109;]', '49.147.199.6', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.47.109;]', NULL, '2025-11-15 11:24:43', NULL, NULL, NULL);
INSERT INTO `activity_log` (`id`, `user_id`, `activity_type`, `device_info`, `ip_address`, `user_agent`, `attempts_remaining`, `activity_time`, `location`, `latitude`, `longitude`) VALUES
(1086, 282, 'GOOGLE LOGIN', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.47.109;]', '49.147.199.6', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.47.109;]', NULL, '2025-11-15 11:26:15', NULL, NULL, NULL),
(1087, 282, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.47.109;]', '49.147.199.6', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/531.0.0.47.109;]', NULL, '2025-11-15 11:27:51', NULL, NULL, NULL),
(1100, 282, 'GOOGLE LOGIN', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/533.0.0.47.109;]', '110.54.218.182', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/533.0.0.47.109;]', NULL, '2025-11-17 07:22:37', NULL, NULL, NULL),
(1101, 282, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/533.0.0.47.109;]', '110.54.218.182', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/533.0.0.47.109;]', NULL, '2025-11-17 07:23:33', NULL, NULL, NULL),
(1102, 282, 'GOOGLE LOGIN', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/533.0.0.47.109;]', '110.54.218.182', 'Mozilla/5.0 (Linux; Android 12; V2120 Build/SP1A.210812.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/141.0.7390.124 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/533.0.0.47.109;]', NULL, '2025-11-17 07:53:19', NULL, NULL, NULL),
(1104, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '131.226.111.182', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-17 13:54:43', NULL, NULL, NULL),
(1105, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-17 14:03:00', NULL, NULL, NULL),
(1106, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-17 15:04:00', NULL, NULL, NULL),
(1107, 236, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-17 16:25:43', NULL, NULL, NULL),
(1108, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/14.9.0.4', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/14.9.0.4', NULL, '2025-11-17 16:26:33', NULL, NULL, NULL),
(1109, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/15.0.0.0', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/15.0.0.0', NULL, '2025-11-17 18:17:00', NULL, NULL, NULL),
(1111, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-18 00:53:42', NULL, NULL, NULL),
(1112, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', NULL, '2025-11-18 15:05:29', NULL, NULL, NULL),
(1113, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/15.0.0.0', '49.147.197.179', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/15.0.0.0', NULL, '2025-11-18 15:15:23', NULL, NULL, NULL),
(1114, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-18 15:39:51', NULL, NULL, NULL),
(1115, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.197.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-18 19:25:08', NULL, NULL, NULL),
(1120, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '58.69.2.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL, '2025-12-17 10:07:32', NULL, NULL, NULL),
(1121, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', '175.176.72.54', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', NULL, '2025-12-17 10:11:19', NULL, NULL, NULL),
(1123, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '175.176.77.160', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-17 18:15:15', NULL, NULL, NULL),
(1124, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '175.176.77.160', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-17 18:33:49', NULL, NULL, NULL),
(1125, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-20 17:25:49', NULL, NULL, NULL),
(1126, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-21 09:29:21', NULL, NULL, NULL),
(1127, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-21 09:38:08', NULL, NULL, NULL),
(1128, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-21 09:44:54', NULL, NULL, NULL),
(1129, 236, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '49.147.193.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-12-21 09:55:42', NULL, NULL, NULL),
(1130, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '175.176.76.244', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, '2025-12-21 15:17:11', NULL, NULL, NULL),
(1131, 285, 'SIGNUP', 'Mozilla/5.0 (Linux; Android 13; M2103K19PG Build/TP1A.220624.014; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.105 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/542.0.0.46.151;IABMV/1;]', '175.176.79.30', 'Mozilla/5.0 (Linux; Android 13; M2103K19PG Build/TP1A.220624.014; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.105 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/542.0.0.46.151;IABMV/1;]', NULL, '2025-12-21 21:14:17', NULL, NULL, NULL),
(1133, 286, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Linux; Android 15; RMX3710 Build/AP3A.240617.008; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.143 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/543.0.0.55.73;IABMV/1;]', '216.247.22.10', 'Mozilla/5.0 (Linux; Android 15; RMX3710 Build/AP3A.240617.008; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.143 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/543.0.0.55.73;IABMV/1;]', NULL, '2025-12-25 12:58:33', NULL, NULL, NULL),
(1134, 236, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '49.147.197.192', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, '2025-12-27 07:17:17', NULL, NULL, NULL),
(1135, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '58.69.2.186', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, '2025-12-29 15:00:11', NULL, NULL, NULL),
(1136, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '58.69.2.186', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', NULL, '2025-12-29 15:01:02', NULL, NULL, NULL),
(1141, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '49.147.194.136', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', NULL, '2026-02-01 13:49:56', NULL, NULL, NULL),
(1142, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '58.69.2.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', NULL, '2026-02-04 09:23:27', NULL, NULL, NULL),
(1143, 288, 'GOOGLE SIGNUP', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '122.53.220.158', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-04 10:20:46', NULL, NULL, NULL),
(1144, 288, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '122.53.220.158', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-04 10:21:09', NULL, NULL, NULL),
(1145, 289, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '198.176.84.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', NULL, '2026-02-04 11:21:30', NULL, NULL, NULL),
(1146, 288, 'GOOGLE LOGIN', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '122.53.220.158', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-04 12:39:19', NULL, NULL, NULL),
(1147, 288, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '122.53.220.158', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-04 13:08:57', NULL, NULL, NULL),
(1148, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '122.53.220.158', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-04 13:10:10', NULL, NULL, NULL),
(1149, 278, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '122.53.220.158', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-04 13:13:25', NULL, NULL, NULL),
(1150, 288, 'GOOGLE LOGIN', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '49.157.104.61', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-05 15:48:16', NULL, NULL, NULL),
(1151, 288, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '49.157.104.61', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-05 15:54:28', NULL, NULL, NULL),
(1152, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '49.147.199.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-08 12:32:17', NULL, NULL, NULL),
(1154, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '49.147.199.221', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', NULL, '2026-03-06 11:11:43', NULL, NULL, NULL),
(1155, 278, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '49.147.199.221', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', NULL, '2026-03-06 11:23:51', NULL, NULL, NULL),
(1157, 292, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Linux; Android 15; TECNO LJ6 Build/AP3A.240905.015.A2; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', '49.150.51.217', 'Mozilla/5.0 (Linux; Android 15; TECNO LJ6 Build/AP3A.240905.015.A2; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', NULL, '2026-03-11 08:15:13', NULL, NULL, NULL),
(1158, 289, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '58.69.2.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-11 08:16:21', NULL, NULL, NULL),
(1159, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '58.69.2.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-11 08:20:19', NULL, NULL, NULL),
(1160, 281, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '143.44.196.232', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-11 08:20:59', NULL, NULL, NULL),
(1161, 281, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '143.44.196.232', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-11 08:22:20', NULL, NULL, NULL),
(1162, 293, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '143.44.196.232', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-11 08:26:51', NULL, NULL, NULL),
(1163, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '49.147.199.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-11 09:15:56', NULL, NULL, NULL),
(1164, 293, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '143.44.196.232', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-11 09:19:52', NULL, NULL, NULL),
(1165, 294, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Linux; Android 13; V2038 Build/TP1A.220624.014; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', '110.54.228.144', 'Mozilla/5.0 (Linux; Android 13; V2038 Build/TP1A.220624.014; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', NULL, '2026-03-11 09:26:58', NULL, NULL, NULL),
(1166, 295, 'SIGNUP', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 [FBAN/FBIOS;FBAV/551.0.0.27.107;FBBV/897058033;FBDV/iPhone17,3;FBMD/iPhone;FBSN/iOS;FBSV/26.3;FBSS/3;FBCR/;FBID/phone;FBLC/en_US;FBOP/80]', '216.247.23.140', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 [FBAN/FBIOS;FBAV/551.0.0.27.107;FBBV/897058033;FBDV/iPhone17,3;FBMD/iPhone;FBSN/iOS;FBSV/26.3;FBSS/3;FBCR/;FBID/phone;FBLC/en_US;FBOP/80]', NULL, '2026-03-11 09:40:39', NULL, NULL, NULL),
(1167, 293, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '49.147.199.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-11 09:43:54', NULL, NULL, NULL),
(1168, 296, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Linux; Android 15; 23129RAA4G Build/AQ3A.240829.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', '112.208.78.217', 'Mozilla/5.0 (Linux; Android 15; 23129RAA4G Build/AQ3A.240829.003; ) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', NULL, '2026-03-11 12:01:20', NULL, NULL, NULL),
(1169, 297, 'SIGNUP', 'Mozilla/5.0 (Linux; Android 13; M2103K19G Build/TP1A.220624.014; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', '124.217.27.139', 'Mozilla/5.0 (Linux; Android 13; M2103K19G Build/TP1A.220624.014; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.120 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/551.0.0.48.62;]', NULL, '2026-03-11 13:21:55', NULL, NULL, NULL),
(1170, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '203.84.189.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', NULL, '2026-03-11 16:01:18', NULL, NULL, NULL),
(1171, 278, 'LOGOUT', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '203.84.189.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', NULL, '2026-03-11 16:01:32', NULL, NULL, NULL),
(1172, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '203.84.189.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', NULL, '2026-03-11 16:03:57', NULL, NULL, NULL),
(1173, 298, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '112.198.112.136', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-11 16:07:15', NULL, NULL, NULL),
(1174, 278, 'LOGIN SUCCESS', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', '112.198.112.136', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.7 Mobile/15E148 Safari/604.1', NULL, '2026-03-11 16:41:57', NULL, NULL, NULL),
(1175, 298, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '112.198.112.136', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-11 16:42:43', NULL, NULL, NULL),
(1176, 248, 'LOGIN SUCCESS', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '112.198.112.136', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-11 16:44:00', NULL, NULL, NULL),
(1177, 248, 'LOGOUT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '112.198.112.136', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-11 16:48:13', NULL, NULL, NULL),
(1178, 298, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '143.44.196.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '2026-03-18 14:27:49', NULL, NULL, NULL),
(1179, 299, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '180.190.238.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '2026-03-21 14:27:36', NULL, NULL, NULL),
(1180, 300, 'GOOGLE SIGNUP', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '49.147.199.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, '2026-03-27 14:17:16', NULL, NULL, NULL),
(1181, 300, 'GOOGLE LOGIN', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '49.147.193.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '2026-03-28 19:12:56', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `profile_pic` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `profile_pic`, `email`, `phone_number`, `password`, `role`, `created_at`) VALUES
(1, 'Sayrelleeee', 'admin_1_1750415164.png', 'sayjames@gmail.com', '', 'Saysaysay@5858', 'admin', '2025-02-05 10:18:09'),
(2, 'sa', '', 'sa@gmail.com', '', '$2y$10$lTv59sJQrcvjdEzZZdS36uX1s.dAESoqh5E9pc.5PT3bHzEEzSmo.', 'admin', '2025-06-21 03:47:40'),
(3, 'Sayrelle James Tiron', 'admin_3_1751104595.png', 'sayADMIN@serbisyos.com', '', '$2y$10$hIhoDeWr8BwZEJTGXKqaQO9Akl1Dx3DrGpac90AtXH9BJMtMzq3G6', 'admin', '2025-06-21 03:54:36'),
(4, 'Sayrelle James Tiron', '', 'sasa@gmail.com', '', '$2y$10$1SeDsHzI42WPbQ4w/R/ziOHdk9r3zm8Hf6NMhgDth8PPCEQpL3Si6', 'admin', '2025-09-24 09:48:15');

-- --------------------------------------------------------

--
-- Table structure for table `chatbot`
--

CREATE TABLE `chatbot` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT 'New Chat',
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chatbot`
--

INSERT INTO `chatbot` (`id`, `user_id`, `title`, `is_pinned`, `created_at`, `updated_at`) VALUES
(207, 248, 'iVxXHgJxRnyNWechnjeMozRNMDBleUxVb09Vd2ZWbWIwbVo4NjhMRStEVU5oZTVURFhnYitMVS9naWc9', 0, '2025-10-27 10:51:12', '2025-10-27 10:55:40'),
(210, 248, 'pdCGqqkI7cN64MLgcca0DlJEcFF4ejZQeDNmWXBua2hVQzBmeW1TbmExQ0N0SXpqSXQwTG5Ta1Q4ZElzbHJYY2NaMU5nRkZ3QkY5YW5UYUs=', 0, '2025-10-27 11:01:22', '2025-10-27 11:03:15'),
(211, 248, 'HwkYD3DxDGvSJaZjWbElLlFGT1czVGY0M042S0NybUVnRTJweVE9PQ==', 0, '2025-10-27 11:04:16', '2025-10-27 11:09:20'),
(212, 260, 'mkJz2y8o95lEide/Q/YPeCsvaWc5YlptdWczNlVoMzluZ3ZnL0E3RjRhQnpqS2VGcG4yN3BxZHdxVm89', 0, '2025-10-27 11:38:13', '2025-10-27 11:40:16'),
(213, 262, 'FRKr2Tdl2hNJhS7BOFFGp09OeFpMa05jNVdjNVpXUUdGS2VSMlE9PQ==', 0, '2025-10-27 11:43:11', '2025-10-27 11:43:13'),
(215, 248, 'pfnTX+DXFaEkqk3zfzpqalE4QUdvNUxoUytSWS8xZng4Wk9nYndZdGdmVncyMWY4WFArOEFhVmJudFU9', 0, '2025-10-29 01:36:47', '2025-10-29 01:37:54'),
(216, 248, '8mLwbEsfzHYcuBBFch4G9Gp0dDRuUVdpdmQxU3htaUhrU3d0NytvaXJxc3VyakY2OGJqVXFRdVNWUk09', 0, '2025-10-29 03:21:47', '2025-10-29 03:21:54'),
(222, 236, '0dMBU/CuV6kKrd6sprUFfytLTTRKRnQrVFlDcTF2M0krakt1RHc9PQ==', 0, '2025-11-06 18:12:16', '2025-11-06 18:15:05'),
(230, 236, 'bfwfXXOgdzWMRVLO+o4Ok2hPWVZ6Nk5qK2taQ2ZmVDFKc0krVFNYWHdQRFl2YmMrQllhOExCbWlGdVE9', 0, '2025-11-17 17:01:26', '2025-11-17 17:02:47'),
(231, 236, '1Qg2DuuMJKLMRQpJ2Gi3zVc0a0Z5TFpORDk4Z3FjM0tZeGh6Mzd5aGJPbWlnbFgzNmhRQnZZQnVEajg9', 0, '2025-11-18 06:45:27', '2025-11-18 06:45:28'),
(232, 236, 'xujvZm0OHKNK6ll4EoUXai9Ca29kb3VxUGtvRmEyQjdtdm5SUldUcDNjZ2VRWTR3MG9DY2dIQk1lT2M9', 0, '2025-11-18 07:02:10', '2025-11-18 07:02:18'),
(233, 236, '9AoSsbCKbJDTCZprLXtOJi9rS3RWblFveXAydDdJNHZMKzd3N1E9PQ==', 0, '2025-11-18 07:02:34', '2025-11-18 07:02:41'),
(234, 236, 'UdxahRRRjKy5cgwvff5XGTFUaVFiT255cFd4K01ya2lxSUxRbjcrRk5ZSVFsK2Mya3B4ckxuZWZPS0k9', 0, '2025-11-18 07:02:58', '2025-11-18 07:02:59'),
(235, 248, 'GjwuOfr+wpzTmvp704bt6nhZYTdFdG9iSHhjbHdJbWg4elYwTjgwZVF4RFB0VzkvWm8zVnNXWjYxdFpNMGtQd0prQXNZNEYxaUJSR2F2SGo=', 0, '2025-12-17 02:16:37', '2025-12-17 02:16:37'),
(236, 248, 'FEVMEwK9TApXTCGUvb9V8VcvZ3liZGpGUy80TGpIdHY2Qno5Z3c9PQ==', 0, '2025-12-17 02:16:58', '2025-12-17 02:16:58'),
(240, 236, '6ZpgnFHiQ+7TV2pJLoKNK1dqSGpaMDc0ZytsdWNqUDVqNm9YU2c9PQ==', 0, '2025-12-17 10:15:35', '2025-12-17 10:15:47'),
(241, 236, 'qd2/R7vkiT8H9lvJdNMPIXh6Z0Y1V0VndzFlaFoyYm5XbGU3eXc9PQ==', 0, '2025-12-17 10:15:55', '2025-12-17 10:15:55'),
(242, 236, 'x5vFZgJulOIIiDcISrjslXpCRWZId214NEREdGYxVnE0NGxxQ2c9PQ==', 0, '2025-12-17 10:26:14', '2025-12-17 10:29:23'),
(243, 236, 'B4plxxfqVhhyniNnYf8kCE9xbWJ2U2RDNUNxcVFqZm8vUGdNWVE9PQ==', 0, '2025-12-17 10:32:31', '2025-12-17 10:32:31'),
(244, 236, 'pFjQ2V0e2Lh2MRbVka2We2VpTGVEOUFHNVY4MFpFempDRW9SVEE9PQ==', 0, '2025-12-17 10:34:01', '2025-12-17 10:34:01'),
(245, 236, 'b1xEstMZ3HqRiXirmXXr/E9KbE1TV20rMThiUWV0TGpmU08vN2c9PQ==', 0, '2025-12-17 10:39:06', '2025-12-17 10:39:06'),
(246, 236, '0KiPYVtw3bfe0Ux9cSs4y1VPdUhKSllRc052TWszaFloc3lSS1E9PQ==', 0, '2025-12-17 10:40:18', '2025-12-20 09:52:10'),
(247, 236, 'Pkl/LMY7k/ogi+PbMALS42liWVZCNFI1SWpYOTFvL2NMWFBtM2c9PQ==', 0, '2025-12-20 09:26:00', '2025-12-20 09:26:08'),
(248, 236, 'baECXHtDbz2toyRYwO011C9EQ1Uvc2RFVU5CanpJREF3NS9sdVE9PQ==', 0, '2025-12-20 09:37:17', '2025-12-20 09:37:17'),
(249, 236, 'fu/98ZQeFN/aU0W6Cdag/zByNmZMaGJWZUFFNTV4blJNNENlL2c9PQ==', 0, '2025-12-20 09:43:11', '2025-12-20 09:52:13'),
(250, 236, 'XURsCC+i7Tp9h+NbTGaGi1c0V0QyKy9QNjZqSTdGOFRBaEtPblBUTkdFcTlRMlQrRzFrS3QvRml6VWM9', 0, '2025-12-20 09:50:44', '2025-12-20 09:52:03'),
(251, 236, '5Qz+lXNdJZfjSnvwbQYNlUxqNUQ2ZSs5dDNwZm1kaUZMbXFCSVE9PQ==', 0, '2025-12-20 09:51:26', '2025-12-20 09:52:16'),
(253, 236, 'ueSgIN90iFD9qtBAWgH1wHVGck1BY0FPQXJBcXlOU1RKVllWS3c9PQ==', 0, '2025-12-20 09:54:09', '2025-12-20 09:58:15'),
(254, 236, '95gFn+t2YkYcvKGgEWkbnUNKZ09semNWdUdiREtTWHVuMERpZTJORkhLaVZPRVE0ZTlPVWcrWW8zY0E9', 0, '2025-12-21 01:29:37', '2025-12-21 01:45:05'),
(255, 236, 'HBnEdplM3SiArHp7xgM/4kppK1BVTVArK2Z4NHJ1YlUyOTlzbXc9PQ==', 0, '2025-12-21 01:46:47', '2025-12-21 01:49:20'),
(258, 289, 'zEOy64/7U50QAFhcQCrF8DZKWHlHOXJmelhGdG1SaGJqbnkrY2R6TW04OVVUaTM1bkhEckpjdWsvNjQ9', 0, '2026-02-04 03:28:33', '2026-02-04 03:29:54'),
(259, 288, '1cq/aFS0rcxGEfE5L2KzqzRQMnptaXI5OEVmUUpYTEdkL0lZQ1I5bWdrczlxK0RsUDBGU3JQb0NEZDQ9', 0, '2026-02-05 07:51:17', '2026-02-05 07:52:31'),
(263, 300, 'dJveyGt9NfCW5pxNcEwaSjFySUhMenFyU1FlL2hrWUtTbGo3d1I2OUIzSEM2ZEkxRXI5Q0tHQm9RZUE9', 0, '2026-03-28 11:25:32', '2026-03-28 11:25:41');

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_messages`
--

CREATE TABLE `chatbot_messages` (
  `id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `role` enum('user','bot','model') NOT NULL,
  `message` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chatbot_messages`
--

INSERT INTO `chatbot_messages` (`id`, `chat_id`, `role`, `message`, `attachment`, `created_at`) VALUES
(786, 207, 'user', '9l9Eu4YONmh5SeaS6XlxU09VZDg2ZVQ4MFV0MHVLUHIyS0pmS2k3dlNISkQ5VTR4c3MzT3FLdUVjbkk9', NULL, '2025-10-27 10:51:12'),
(787, 207, 'bot', 'VMojZoD9MjA4d5FftyhiFXlnZ0czZVNsalcrRWtDR01ubERNektFQ0dPZjVBRjZVeTBJaVBpRVVxZUxmL0NjRkU2NnYvZ0p4VUNVeGdBMnl6dWUzNVVNOFhia0Y0ZU83VlV5RTZpbUdzMUNDa0Y0bVBZOFVKYkJsZ2FRMEExcGcySlc1UmtEajdIZkZGblFiY09vUWxMTkJLRGkxNzFQVzBjeDY1UENEMHRJMzYwTUlMNkl6eSt5b1ZVcXVGb2I2YnlyNDdpUDYxWU14Y1ExUDdDMjdrWXRIckwxbmZtLzdnMFZVYkxDYUhnVlVlRDh6dngxOGdjOU5MWUxGZXorcXlMMVBaalhSTWoyVWRVRTdvQXcwTmdmSllnUGFoNEFKRk11TkpBPT0=', NULL, '2025-10-27 10:51:14'),
(788, 207, 'user', 'rRlOVxeG/zZ/HrxvApWCLlp1c1pjc0h5UnFBLzdua2RZbktDeGc9PQ==', NULL, '2025-10-27 10:51:27'),
(789, 207, 'bot', 'OtnF9i4f31FhUW+k8RRMgkZzL0JHa0E4OG1qdmx6cEVJSzdoN081Y0NBNEVxTkZWdkRrVWhzZWRNamo3Z3FQNnVFTHlmRW10bllYVkdVYUNWSUwwaFc0WFNqaDk0RXdXMytrS2ZFTmJ4MmRkcCt3RnhSMlRxU2hCanArZHNvdjJ3OE5lc2trQk9meW8xZFprREpSVFk1cDFNa3hMVlFYOWE4TjdGRFpYanNJd3oxd1VLUUZBbVlpcXZyTkNjSUNLZXZ3Y25XUFFXWmVYL0poWXRaVkhoMVZ0bEpnaVNCeDlJb3ZJb1ZBR1NHSXZtN3k4VFhTcXdnNVN0QjJWUUJhNlU2dk54SktFUjR4Q2lsS0dZcWpka0JhM2EyaEhibVc1eHFnOSt3PT0=', NULL, '2025-10-27 10:51:29'),
(790, 207, 'user', 'U2job0UJKWBMzhgyQayWZ2NJU2d0L1c0c05tR2xQREtSVVJPWFR4cFRobDkweC9DUUhWK1NSU3lOMzQ9', NULL, '2025-10-27 10:51:40'),
(791, 207, 'bot', 'J3f7g8SnBWkFi8LJYj4pzmUrcXR1WTVSWnNJaHR4VHk5cXBXQjd4dHJubnFMZ1dsZ1pJM1hyb3RqakNLMXM5M1hINUtzaVVOOEVaN0UzN3JIWTR3WjlmT0lvZUVRMGxweDlGVUY4Yytjd1EwK2lxZ3pPZnJGUHpBMHd2RjlBMWVhZFU3V29GMExjZ3ljU1BQOVI2WDM1NUVpcmNHSmxNVkZQZDhNVnVMTnNlRlRzbUtId045bEhJRjhQd1hFQjZMMVR2RkhweEtuWjZsRXNlVC90T1IvY1dBUWtuazAyQVRWQkFmQTVkQ3U2RTg1MzNzM285RDUxeENlTVVMYTZWeW1yTk8xZUp4QnB0Z05FMm5QRVpCMmlpMjczVis3UkNkRjVVdjY1aGRidHB0akQzRldzN1J2YnAvTC8vd0luQlBYQndBUVEzTXpwTzBURzUzSVAyNThpbHlBcS9RQ3NaZzZZTlVFc3VZRkVXMFd6NVJ0elVOSW92aGVhalVKdEErTUVsWmNGYThjSHk1NHdKYWxhODE0MWdDRUhWM1YrMWtXZlJWU01Va3Mrdk9NNXRIWTRTSW5XcWR5U0JKdldzaU9HK1o4Vld2bE9sby93c2QvUzU5T3ZERHZET0w4cG9sVWlPbDFlT0Q3QnZIZktNRTB3RGtzRW5VTmo4bUordkN6VElFS1ZEOUppY3VGMUtvVlF4VDZXUGJnNGRQQWJaOW4xc0g1cEpWbzhXRzNlZVYxSHNvS0l5T0tRQkhHSWRhdGV6cnlUaUVpYmdDODFUMEVYOEw0OHg2Q2lGWHcxZzFKVGplYnNVNTBjMkkzNVRuZHppZE96OUR5QWJtanVGRkFUMStGRTMvejhKT2F3L2ZxVkhpQ0RwQzNyQis3b3pnMEpVSXJsK1ZGRXhNQjh5Yk04c2VDSnUxTElJUmRGRCtmTU5KN0orME4vQ0czSS9lSytJb01QRHowNGpMQnNaYlhRQjM1eFN3RGltRS9KbmdTbC9hS0t1NSttTUNNTEp6Rm1pczFoejNEWWh0Y1dvdGs2bXpnWnVxaDBaM3VBVytxS3dRUzhhb3lsSXlNOXdnWDhJSGpXRytHNlFudmhReWg1MnJTc1dFMEZub0h4aFEyQTNrN25GSXNwNW5FYUpqTVRKb2p0UHVpbW1MeFdLVGM4TlF5bkJrRkw5SHZBQS9XVDUvdHBXYXBvTVMweTkzcXNrZjV1RFFPWVEyZjNFdkhHUkYyWmE1NHlhWTFISTFQaTRoVVBwTkVQcmtMSDRPMTVlMTF1WnBtbmxVcDJrdTlaQjJhWDZMWU8yWUtKZDc1aEZQL09QNHp0VEcwdnZUUUdBb2R0QURYeXc1Y0UrazBmOWcyRDVJSHBOSlhzY2RwNEcyVmNwNXM2WU5OQkdxbkN3QkxVYUlGTHhLc3JzQURNdi9jeUtRK045Qmx0WG91bWEvRjJmM04rZ1RLUWliU2EzazFGb01zY2JIMzh4UWt4SW4wZz09', NULL, '2025-10-27 10:51:44'),
(792, 207, 'user', 'CVYSC+4EFQoAWmx27Cg29GhiNmdIKzFudGNQcDQvSUZkdDlYK1E9PQ==', NULL, '2025-10-27 10:54:31'),
(793, 207, 'bot', 'zNLmmwUllVlMHEI9IxWz9VQ2Qk5aWEl6RXZTV3VoUkZ0dEJCZGR6NEkxY1BlNHFyVjNWbVRTTkx2MWR5bllOcTJ3bjhoeU1YYlVlM3FSQ2RJaGZsa0dSc1lqVFVDMmEzUVk4eEkzZzhYc3BVWkgvdUorQlByNzA0cTh4UHM1Mk95Yi8zZzNCWTR6YTlwSVNuaVp1MEFMWVAxcjdlalZIYStreWNOMVVLbU00MzBPaUM4aUdyQWJjSjZXUklEZi9qb1ozazFUM2g2R0hKeHZ4bCtTWlFWa2xYNWFuTTcvU1ZCakNmQUlLNTMwdStxd241dlQxNE5KUk5Obm8zTGlYa2NCdjJHYW1PSzdHeTg2WG93RDNvRzZQYXZETjZCWDl4b1dCVUZ3PT0=', NULL, '2025-10-27 10:54:33'),
(794, 207, 'user', '1wAYCLShUJixFS/wPxbe2XM3THZaYjRkM2l5QUx4bFozK3NaKzlKT0xXclFnQW5McHlDdHpRNTlOeTQ9', NULL, '2025-10-27 10:54:41'),
(795, 207, 'bot', 'fZYU6Hk9TrkbFkx2wddAS3RBYkNsS3BkdG9sbk5QRmR6T2hNNVg5V2c4MVdvdEtjSFNjWlhaRTRSSWQ4OVhSaUdMa0RyaVBaNStRVHlkeDIxNVczUmt3WU5CWmo1VHB1cGovcUZGUTFCYzh5UG45eG5vR2NzQldQcmNLUjhJOG5WYkxIMkRIZlZQUm1hOGhyNmVVWEhwUERGQ0luR0pTNk5PakpKVjZPcWV3R3o2aFFZdEM5Mk1vOU9Wa1lxQWNOSmFFTG1KM29PTjQrdVRDU25TckV1SzlXNHlvYzRaSWgxaGNCOTNreXFvVkp2ZE8zRnpoelF6WmZpZ2tJSnEvOGhCYjh3WGVQaFhTRkFQcWQ=', NULL, '2025-10-27 10:54:42'),
(796, 207, 'user', '2D6qygdxSmk6NLzVK/8pa3AzVW92OFY4UUhucWZubDRCUHZQU0E9PQ==', NULL, '2025-10-27 10:54:59'),
(797, 207, 'bot', '7lPj7y2n2z70zmboHwdLpnNtVGFvWXlERE5ScmNNUzlLaEdvOUxlYjZmZitLdDJJbytyNG9FWm1rV0dLb1ExNFkwQW9XbkwvNHNRcjNLcEFUekRib0llNDlHcWNzMVUxSHRocGNETTZ2VitLK04vMFZCc0VNMm9PbmJFTWd0NlhPc25ldGtqL1Z0MEJYVnprbm1nUDE5S2FTZENuQU9NNFIrTUE0WklqU2VZK0xZZ2tybGU4UHhMR3BVUFZiYWhjN1JSNG1BZkF0WXpxQWpOMw==', NULL, '2025-10-27 10:55:00'),
(798, 207, 'user', 'TK/gTazwaLR+eZ0/pYh+GDRZUVJGTEVscHFWYVFrNlY1eEpOK0E9PQ==', NULL, '2025-10-27 10:55:08'),
(799, 207, 'bot', 'sS1S3xjah1Yqi4t4tM8utWt1c0I2ajVtTlJWRnh2TkVBUys5NElOQ3ZZZUhNVi94MFNhT3JINFZtWmZLNVhjZllma3JHbzFGbzhUMW5aVG5GRnFwSHY0azhLcEVtaU5MRkMyNm9ROWdvakExTEdnY1g3cXIzMmI3dlRsSHhtQ2lzNFlGRDJPMjJzVDVoNzVJMDlmVWJNZkkrcEZVS0hWam5pajRnTHI3UHlaT1BOcUxkNFY5KzVOajVqaWZBTnZIOU9KK0JWS3VKSTZtSks2U3ZuVENIdmJ0OFpyc3dtTHljeE9PV29VdkI5V1dIQW1hYTdlYkpiUTd2NW5xUmNpZ050b05ibTdzVExrNFNBeERFWVF2d3Nnd3FzUzJJRzhMMzhKK3dJckFHRnpMejliMk4yWGJibEcvdXFVcmJOd2VGZyt3VWFxb0hFcHN4a3hlTU5rNHpKWHp6aDZJQnF5b09PNVJMOWpHeDA4Vis5MERzbmdUamQyWDRtVm1STXhuWThtQ2NqY2hhRlZLekd3Y3FTVE56NUMvZk40ajlNeFhSY2llZmc4RVJkWXRlcGlndzFuaS9rLzdNZ0w4WjlZNEpOODZqR1NqS0x6RmFISXBSRVlYcXFjNmxrdnUxY2YvZmEzSi9zRks2d0lLdTNvUGxtZkNxSitVc1BQK2svWGdSYzg5a0RsZlpadXlCSk5lRXE0Y3B3cGJVK3ovbDdtVXFUOTFwdz09', NULL, '2025-10-27 10:55:11'),
(800, 207, 'user', 'm5RAIsA5P+YuY6pOMDq4eDh5OVBFSG1pN2Q1T29mTDFwa1hKaVE9PQ==', NULL, '2025-10-27 10:55:29'),
(801, 207, 'bot', 'BO6FX8G5eR4Yf0YwqEx01k5Wa0lGb0tJcXVqejZaeHhsVjBycnlWVzR6aWVDL1h3OTJobVY5S0JzZ2crWjRRUUp2dFd6cXkxRnppY1BDUHhQVEZFNWlDYy9JNzFMbWRxdHF2Q3dOTEFqQmdrYnppcXJpbUtwY29QWlg1MVVwWkZVQWcyWlVEVnhKOUxWbzd6bm04bWMrZW15YSt1M3hiMEQzTHphQWsrYXBXSnJlbmlaV0t1d2phM1QzMGliSTAyQW1UUi9odm9RSFA0NThkYmtFWE0vRXJWT1NLb1ZjREdPeCtyWmc9PQ==', NULL, '2025-10-27 10:55:29'),
(802, 207, 'user', 'rFHOCudhxFl0pMI06dMfGEQrdDZOSmJVamlBaVcrZG5IR1d6RWc9PQ==', NULL, '2025-10-27 10:55:39'),
(803, 207, 'bot', 'XOAz+5jwqspts/2UfTjqi3Vvdi9KTFg2bnlqOVI2WC9iR0ZnR3lETHRUNmFuc0txbHlWM0lJZG1pM0Z0Nkl0cVJnV0I2Qjg0VElIdGJhcWNCQSt0aUVBMjJleURpekovNVlFbG9EYXVWMUN2VlAxL0FodTZJeEt2d2RtQU5GRjJHTHJkeVU0YlpQd0JKM2ZLUnljajZmK0VyS1hJTjVWandLMkpYK0owN3cxd3EvOGMwMy9PMXIwSWRCa2hEcXRSWWhkNE5tOTJ5eVhkUzJ1a1lKendSZWMzcjM5ZWJpcnc3cC9DWUZTM2hPcTBZQ2daTFgwV2RpTmwyWGNmZlJ3K0VTWSsyRFdlSzZKREhNV3RJRldpdmE5TlhyRmtnZ0pVSWtZZkpXdzRLS0YxdVRYeEhWWVVzR0V2bDZIUzNack5HaGp6eDc5RkpOU1FpYjFrbTVndVhxS3lqQ240QmxwQk5HRnloQzMzVTQrc2g0bjE5cmd0NU1BVWZxSncreU05NnVsWmRJM3dlcHl4NzZIbFdsNUM2aFA1bWRBUm54THA3ekxLbkxPeExxUnN4VEk1RW1PckhBSGhEV1A4ZW1FYmhKSzNMVEhnMjFoNUMzWkpNbTc3a1FNcEZYMmwvUjJzUjd1Z2YrRVdkODVHQi9pNEJCQXlvd1l0aktCMkVnbHdzb29LbUJWRmx0NE8zREMveElRUG1tOFQ3ZXdKK0tZenQvcGhBRzNRVHo4eEMzbnFERzRGQjVoYmZwMFpNbTB6L0hUVUl6S1BJeUdWVjRVVVkyVlc1Q3FjbXg5QjlIc3gyY1V2djA1M2ZJY1g2aFJ6ajJ6VzQyMEZOczhSVGg3b3B6eFpDZ053QWxJbzMxOFFaK2NaTzRsQUROSm13STV6NkVNb3pscDNlMGRKSFM3YXg2T3JDaEJBNDY0YzhQTEpvOGw5WkJQYy96cnZRNitsNUNYZlVzcytoUHdMT2FqNktqbmNiQnpVMjAvd0RIeE9EeWF6ZSswWVY5WDJjWmRYY1BFbS9WUTZhdU9YdXkvNzRmbkFsaWlmU1ZEMzlXSk9GT0NEcjBsVmxLYTVFWUN0d0pDY0VUN3JwU3lFdTdUZ3pRSDAyL1dwbHJDaFRneW5CUzh6NUVrL2FZaStuQWNESWNSNUZzaldSSUd1aFc5QUZpcTM5MENDZkV1bFc0ZGxzSDFsMDNwNFBNMTVEMVNaSm85b0tGdy9mWjRjUGpnVWd3Q0NpN3Y4VXcyd2NEZW43WjFWR1UvOGUwMTZFVDBwTmtkQjFScmNIZExvcXUyUHB5cHl0OGMrbTRJaE9Ndy9OTkhsZXVsODExZFdYcEJtanV0MThVZUppdUx5Vkc1MlZnbS9KdmV0SXkxUk9pd3d2UlhOdnhJQnViUFVEbUFPRm9BNGhRN2lCNVo1Y1AvYXMwaUVIU1hheHUyNFFkNE1TdmYzcDBjSGNxWGhha05tNk11a0ZDa0ZRS0NqbVUwclgvVENRQmhIMTRPUEZ1TzBMNmpxY0dkd1FVcmlKbnBQd0M5RFpQWStZVGdoR0Q1TVN2RytqMi9wdllNWjROS3pYcWxPSFN6bFZxYkZ5bFNZS21KdnBHaTF1Qk9EK1JERktTSHJoSS9kVExBTSszemE3MjNkUTloMDRjVDAwekJqZVhwMlFhajV1alFJQXZNZldFNk1qSHpUc3hvZ0l5YVlyeCtIK2xlVUNxTTVoYmZDQjVHcXhITVZaZUxhRG91dVo1UWY5dTVuUjJ0N09CT244YmRkOU5RQkRRTTRpRHRwMmVMRUs5QWNJR3d6cG5QM1RBZ1VUeDFBa1ZPaC8yWGRnSDZSY0J4U203TkJDVy9JSUMzekxCSm84aFFCZ0gvVFFpWDgyYkhTbkR5NEU4L1VrTlJVUUdhbDg3MUlRdFFWMU8vem5RV1c2WEhsVnZvZkJpU0M1K25VNUlxd25FRlpTN2tBeldFbVl6U2R4cXlIanA5dGNqMjFzN25JOHVYT0IzMFdOc0tadEhKQVMvVzhGMHVpTUh4NUUwamdlVWFDY0FKL2xzc3htNzNMcDlIa3kyZkJQd2pjcTUrMDdvbzhVNHJPSlRSSHpXTGVSRGJFNmRZSXdiUVFZWXVVUTZscTRpNHFGVEphRFdwMVdMUDlFSXU3ZnVreVpQeVk1TDVoSzNjbVk4OGk5U01keE12QWZ0UDJZUWptSllKL0ZmbkhONDhjMEhhOW5xZWxvSjAvUEx0Mi9zdm1Rcm41OCtCQk80d1B3WGVydFpVRVFLOERQZi91aURBczladXU4M3ZqRmdBTU9mYlcyT1ZJa2tMN0tXVjJNbmplUFh3Tys1UFlvWDgrSUU0ODlSZy8yUE1ndGs5aHV1SVpJd2xpUGdDRUgweFVlbXJHWGo5SEZlRU45bTN6MXZlYnA4SVhkTmRUa2FHNWpXN2hpemg4RVlPWjhpV2RiWFBvOUhGenRXUjRtb0diR0xiZC9KUzlaOU4yM2NsTCszMlBoZTFCaUFFcjJlUUVTS2JSaVJwS1RsL1FXTHgrUGY1VmlqblZCQ1pXY1FDVmo5YkQwWUYxdG02U1lTdVMzY1lERmRRUXdwM3ZpSXkyaWJyZkRoWUVhdWdXUzJ1dEJaN2Z5YTY4UTYwWEdGWEg2cUpOM05xcFlmTE5oc1M5NlZoMDhPT2dJN1J0VWFwTnlXUGV6VGhGWVdMMlNXQVhMNmttRnNSZ2JpZGtyS2t0ekpZNFcwT3B1b0pzNTZMODNjaTFqaFMxMHlJc2pUVTFkdTNyMjNLbXZqYzh4MjExYzlzZEtQQWY1SXlmWE5hdnhSVlJyMTdMNkR1UVBlaDROcWpNVDRJbGFnQjgxWjBWeTV4SUEyVHdDc0UzSFRJenVnRlh6V3FsaW5MS085TVNMc2ZMeWNwMytQdDdsMlk0Uzg3N05zOXRDTlRyb281Z1ZwTmFWNVlPU2FlWE1UVGZ5NkxKUHUxbU80NGdodE1pTmxyTzNmR25pRS9ERDBZNU41Wk94WEF5Y0pFWkFjd2l5QkZleXQ0dmxEUlhETnBnVGJ3QkUrMnlEYVRzWW5oRzF4ZE1qNlJPK3N1a0c0dDloWUsrb0ltK0xxMWY2dnllY3JqRGRmWU8xT1NNZjdudWVyOTI4ckp0cEhmRUoyeDBRT1N3SlJYYXY5NlNwWjhZSWd1T2pPNjN5ZXNHUjM1UDJtaUlxWGpHZFhnZ1FyYzFkMllsT2xZandueE15Vm02OE14VmtpS2VpaEZLVjlhNVVidXJ1VXJ5S2t6eHZFZm11eTdGdFJqSmJnQjJmOEV2dUE1b3JoZmtObnZEZ1lYK3AvSTVFWHVIYkxoTzNadW53Q3J4NU9CL1NRV2tTN1ZibjhmSlFTck9TMlZiWUdlRHNIbzVSWk5wTTJ5c2RmOUdYTlp3V2FHcTRrSUQxY0M5VjhQaTR2Y0RwN3lybURuMEhxanZjRWxWOFJuNk5qQXVkcFNkSldpeEVBT1ppM2RmaHByTlJYUngrejRURHBNaDFnTktnMk4yQWZZK0ZKYURIWE5wcG11WnMxNlkxMzhqZS9vZ3p3bU9xZFJ3T1hyNmduWldna0xUbVJNclBock1xaUo5V1JoLzVmY2VLVDZuRHFPa1lOT3lOVE0vY2puQnVGUDVmRHRtNEpkOWUrTXJsWCtSaFRVeHphbGMrSHVhOU1uNm9OUDhVQjhyUTZaT09NSjVPbyt5YzhwUHQ2VnJKanBrL0JCYVNMZzZqZFIyWGtDSlRneGdKdlVzUGxCS29LTllYSGVCL3dPRXdGTWJhY1RVL0JmTXJEdXFZNzM4YjdwVDVwUDVja3JoV01tWHM4OE1IZkpSdkpsaXl4SGxBU1hMbng4Z1V3ZVVRYjJ4cWlKeG1FV05wd1hWN0RxMkNYcCtRbFV2NkN5bEJ2ZXFkaVRBbzlpdjljV3AzRnZ6RHFlVlZ3WUdHa1UvZ2h1U3NhbWJlL1daQS9tZzZXa2JUdnBsanh5U1l6aUFwSDZrdkhyaVpEK1hySXd6aisyQXAvbGdLQWhWS3NQT2FpWnZwditxbXVqeFowMGpWSERQbERlYzhSOW5qMVpWUGxPbUVSQ0VaSFpkS0VzZkd1TmxWckh3NnVXZ3RIVDZjdGZ5cW1KN2VoaDZ6KyszWlBQZ1h3bmZqOFF4MTZudG1ZeUJZY1FhUXJuUTFRUzc=', NULL, '2025-10-27 10:55:40'),
(812, 210, 'user', 'Cg8vYg1AMNVNnretjVdrHjJGUHEyZDBuendCRXBHYTM5VUkyTnp5R1ZteG5PdEl4SVllNHNHWU5RZHJZblJXc2NsZk5aRTQwRHBnYTlkK2k=', NULL, '2025-10-27 11:01:22'),
(813, 210, 'bot', 'uwJpdtTC8L/oqxhBn6zsWnZYUmZIMXR1UWQvS3p5NW1uZTl2V25Sakg0YjJheDdkQmJpTUl3bjJrMnRJT0FVWUdSUWJNMSs0ZHJSZ3A4S0t2VHpuTVd6L0VTNWVzcnNqVUZ1L0FhcWtOWXdQaHY2cHRSdWlXSnUvNEJzZXp4UEJ6WUE0aHJvVXVZMUxQSGZOQzRHdGJVSEZyRGJBVGhYei9HNlZ6WWVtR25OaWZ6MGw3UmY4ekpxS0lXUlpLNFJJWnphMmpEUGxPL3g1VHBQQXJyeUtYYWFWN1RwOTN0YUpwY0VYbFFBdGZnTmlCK3hiVEhUSW9FNDh3eU16dkliK2I1TXNKcmhTeW1DNHZIRlBVVHFWN3RFcTUzZFpiNzMwQTZPRFpCSWlyVzFnS0EvNE9vOE05ckh3T2lXRldWV1V3cjZpaGZQanNJeXoySjFhK1hkQU4yNUJka3FFS3Y1NU1hWUFiMG5uZXZaN2FlQ1dGYndYZUI5UmpHU05teHJjUXA1d1lVdUZUM3UyRW1mbDlzKzk2T1QxMkF6NkNzd3dZc3ZORDFkOExNODFMV0htWTRyUU5SYUlKQVA4Rk9pYXBVclNNVFp5YTB1K1ZIK0NqdE5ZOEdYU2dOUTVzZS81a3Zkemo5OEhiTHM1NW5QalNPTjlRbTFydDZFajJEK3dLa0VsLzFFQmhWaXhFUys4SXZGc0JWNlV5KytCMlMxZ1RFczB4VjBoSVAzYmd1L3BhZVkxZUZKUDBIR01veHZZbkcraDYvRFVzMGV1TlplVWxDMWpnVXBpaGlGaVdGcVBGVHZoeHJyckRUR1BsYVVGa0JBdkhWd0ZXTFY3RU40T2RuNll1Zkw2Y3paU2gyek1RVmdCS2xDc01DWTNxNkdPL04vYmpMbStOUGhVN2ozdjFMeTBXM3BxMktPaU9WbXJkbEtLNlJWbWZxcEhRZW13ZVl4bEY2R3htckhMbGg1cUF6N1NIa050RmJWVmh3czgrTGJqNmIrVU50aTRyZEdzaGRDOU1IcXF4NXlaZnlST0JCOHFpTmxVNDNlWW9UWmNsMmVpZk83VTNBT3J3bmg1Y2JGaGJOVzJiTVR4R0Q0YXZzeC83ODFlRkZubGFrQ0dkeHBtSWF2OU5JOFIyajhzRnY2cmFPb2VvOE5BNkNNZU9sMXZJUVNhMi82MDRYblpyZUV5SWYxTittZnlZczMvd3JTRUZFYVJaeXNFNjlXUWw4QjhJVDNnMGRDVWgrN3lxTVpuYllZVVVuUitCRGdlaGkzWS9WMWZPY1hHSnM3c3UzUVhZbFpmblFVbTdvMGVVVTA3c0NZOXFRN2lhUFR4cys0K3JIM3dtMlJKMWp1Tk1SbzdYYUQybS9HWUNSOGtmWDF5c0l1amZnQ3FwSTd3L2lORnhseFg5cUlDN0F5Vkl5VlhWM2JlQTB1d3VDOXJleXNzSWE3aVFjTndNNGFuV0U1d3ZrWjVaNjF1SDZTRk5DNjVVd1dRcXFWbE1CTVM0eEJHYThUMTVoUG8vRWFyYkFwSTNCTXNzeFBlRmlRdTFSSENUc2lvcC83TGdDUlJwR1dNQmlzRXNYQUpHVEhoTFhwWDRPUFNIdGJ0bW5wMUpDdGtSZ1N6VTVLMmt5SS9aSjB3cHJ4ekZrTDd3aEpWKzJVRjV2NTdGajdLMlIxTVkweWlHTlZDek5xSUtQaDhFaFpWTms4K09mSTVZSGhnTUFpVVc3MnY2WWJFTTl4czNpaDZ4NlUrNmMzdVNVUlhZcDJ1em5ZUHAxK2QyK0UvZ0xXRW55cFVOM3pmSlV3S3p4NzVLbHVWOVgyZTlzeUUyT0w5bkFFMTFJRU5pak5XakVWd0VYV3dHZit2OWp6aXMwUXNBbmJHTWdLVVg3ZUYybEN4Vk9vRnFuZDJFb3B2c2QzcmlKVE44ajFHWmptVmFWaVZ4dUZLc3piWDNabW41b2FWQUhZem1MUjFRbGh6dkUvY0RoUFpoWTc0NWdlZDlpQW1icDQ5MS9OZDlBa1RqRFBrbTFpMmYwZ3FnbDV2eWFXR2FOTjQ2UmJrd2Z4MUJaelRGMVVVQ1FiSWdFM1FVb0FjdUZMYzVDRmNPUGl1Q0k1Y1JuWmtDczdXWjRIZlg5RDF1d0trOU5vRktEdWE5cm45OXVMVTNhMWxQdGJpbmFsZnpBZUc3MGxPUlBWRVdZalJ2QS9ER2tsSzk2VkRIQVd1R0lUUjV0c0d4UTRPYTM1Si96R2VBM2oxK0hDd1NNTjREN21YU3RUV1VoalZzZkk0TUpCek1mYit1Q3BFNFRKcWV2YnRZeEF5RUMwNzNRT3dXeUErSG9MU1ZpMzRRbENOanRTR2dzZHdOdnhNdjB1SVdjNDMyZmpVZ0xyTG44STFGMkNuODZBQ0l4akVQMTlOTXh0U25OZXJLc3JRbVlxTUdPRVh4azVWT2tmT3BVWjIrZVlPVW9nMGRTanVOMm5ianNLdVVoL3R1eW1RalFuUUFOMGlFaWR1RnE3Z3JYQkZvT0R6bGxnUHhPQXB4NXhnaytGdmFWNEFOSjU4cWJBTWRHUjltS1U5R21wVkhkNCtjUndubzc4cnBzQmlENElBVXBFU2ZhUnZ6RVlmUWx0OU9ONnBKWXpIK0U4dENEM3FHck5UMnlJendUU21CUVpyekhDc0R6ZkNiWVQ5TlRadXJTbjVVZTlvcGhxNTViQUNWdkZNM3FiY0hJL2dKSlEwdG5CRCsvakN2Y0VhVWVhTVkvUUZwcFp1aEMyMFRTcnZPeEV5dGtiTDRjZjJrcVllK09lNnI2djNiaTlvamQ1NWtzbkhBWGszMitITk95d1pMTEdGb1NJV0dVWjlvL09wUWtOYXVoUVhsUTFudDZOV29KOWZaMWNUM3k4bVJSYk1wQU1hc0J0Z0lFZWxyUkdCMnh6N1dGdHllMzFPU1gzTm4wRVl3OHMvajIwRUwyZGdBWkszdlJxb0Nlb3M1ZlhsTGx4RDVlWGxZQWNTSVVkamtWZDRtenJvT2tSUWd2NzN4SDM2ekRlWWlPM3UrTnFZTEEyaGV2MkhHb3lzaXB1d2pDTTZVRlJyZmtnTlZhR3ZPcXJ5WThxWjVOYmM3TDJMYi94WG51VEpINWs0ZVFDcHBRN3Q2cjl3NTgvYnA4NEEySXpjVlRYbFV2dWRBU200L2NJVDE1UFA5Q1dWalhyVWJNUEdwZGxhemU5c3Q3Uml4RmtSY2N3VXpxZVN1TWNXQmdlanp5b1RYL3BRWncwZnR5N3hQOVNIemJDMlNDcXB1V2JmaVVZb3AxNEJZWWJZVCthN29pT3hCbWxLaFZrYkI1blc3bXVKZVdBNzYxMHR6SkY4b3J5SGF2bkNTWklwQ0M3T1ZuakRieGZMbllvMFBWcEEyZklGWFAvdVpaOHFFYVRvakpBQ3Qrc25oZW8zeEkzR2FNSU9pSVFlWEE2bFd2Z3U3cElpS0pCdFlNUmhiMkVPR2RGdFJ1N0NpRzkvK1lQclZoVlQvNGJjYmQrVHJzZm5RYW8xT2FDRGozcmF4STAvanBlZ1VOK3RWcDBlNEgvcEdGOWZieVB0SVB0REFxU24zaDF6cSs3RlZlNDRRN3FFbkxxZHFzVVZycWh6ZThKV0RYSWxOdk4wdXN2c09YaTBrSCsrT0thc0dUb3l0OGxvY3J1STUzdUZGcmxIME1WWVZGRk9lQUViTzBJNCt6Mmo0QmJicTlPTi9IMnlER3k0cFNxR1NhYU4rREVKZHREclVLeDJJZnR3NjAvZnZwSUt4bXkvLzltZEthbytsTStMQXNFL3VXcDdqUVEwa2s1WHRHVGVweWt3MWFiWnBIMDdFTGtrMThraWppOTI1dXpWLzg4c3NKd2lndG1sb3dDYXRHdjNZWkVEeTczUGdlMk1zMGxuTzVvV0s5VVRnb0Q1UndFWnN0am1yM2V4cDJ0Z0pEZkdsdVZMYmJWdExMU2V3QWFwQ0Yrd2ZFZktwTTBVcS9VYTBWS24ySzNxRUJ5c25SYTZwTExtY3lteUk5T3Q2WmgvMksyemk4YXBMY3FNUnhvMmFXby9ianB6QTdGZmpmcURuZGxIYy9NTnBFczg4bnBDdVBvL0oxZExHUDFjZFAxU2ZsdGtrbU5EL0ZaTllUTjZSeGQxR1VESkErTGtybHZFeGMyOHpmcGhyVVRIQ2c0THRYa2c=', NULL, '2025-10-27 11:01:26'),
(814, 210, 'user', 'AcefQaKOGc4IUwfxCcrFo2tkNlJOajh2MHZmUkdxdlJhdFBPZWtLMGtwb3dWVk5aMVh4R3puWHg3cVk9', NULL, '2025-10-27 11:02:20'),
(815, 210, 'bot', '67L5VsMotn6AdB3zAxDmvTMzOTZaR1J5UjdWSjJVb2RyK1VtbFNzUmJ3YVhQcEh6d3dCNjdiNlJ2REZtMmtEZ1ZuTUxDeWUzbU5BRTF5SFFETVFOOG1QRTc0UE12aW5NMGx2SVFROW1JYzBnQ3BFNXpiQi8rd1VQUHpKMlN0d25wakI0SzZtR3Z1U05xcmpGZjZPbkJEYTlBR3JYWTY0MVpMaFhxN3N4V3BraUlSazhBMjFPTy9LNWhEM2NvOEdoV2ZOZVVnUlVOV2VnNU4xME1KS2I2OGk5Qmh1USs0Wk1GUDJKOVE9PQ==', NULL, '2025-10-27 11:02:22'),
(816, 210, 'user', 'R925zBuqJVVlaaMUUnNHO0M4SGJvTmNHbkJLUWpXUGgxV2dsYnhBdFdqWVNFYm5FeDZ2S1dWcG5OM0IvQlp0Ri83SDJ4clIrM05kUEhKVDk=', NULL, '2025-10-27 11:02:48'),
(817, 210, 'bot', 'OMeTUkZnCQuJu+EflO3OI3YvT3pWTEx6Y01GUU8yK2JWZHhGSjd5dHFFK0prK2RaVUdPTm1TL2RzbFZjbzJURUp6b0o0NFdKTnV4b0lRREZ5Y0pWbEc2djFWQzFPb1ZjS0lnU3BiM0NUZGJxNlhTMHRxdXZoa1ZNNXFHdFhwelhXWUpISkRtTEJDdU9LSE5HK2hFZGhRRGdTVThZcGl0RXJEc2Y5cTV6ekRlUVFDdUFJRjZXS1V4RW5KNlVpcjl1SVowNkFXNVE0eDB0OEFPTw==', NULL, '2025-10-27 11:02:49'),
(818, 210, 'user', '/8C5sKq9ynaq7O5s+QoCxThiQVFPVHlON0diUjBMZ3JobTAwc0pyWHgwYXdHZEtnbFJKNTI0SFFNRk09', NULL, '2025-10-27 11:03:04'),
(819, 210, 'bot', 'HOWoe7tK/fHN8cFNKope/W1HUDBhRjk5UEw3SXJNaEE3Z0VPWWxlZUN3VEY0bkR3NHdVYUI3NU5kZmNUV2h0UGQ0MmF6ZHdLUmVJS3VGM0VMWjhqMFV5T3ZFSHduSk9TVXg5Z29JQWo3M2xzOFJxaitEOW5QRXZKakd2UEpPaUo1eHRORExGUkhRVlplWGNyN0haZFZRZ3g3enlkSEgrYURLWTh4SmZDL2F4YURseXFSZy9HdlRXY2c1SlJsa3JSeDUraFVodFNjTHhDOVBuNUlVekUvSC92ZTRsQUV0c3gvYUtOblV1Qi9jS3FybEozTDFuNG1MN2xwaDNqcjRwZnF5Z3pKbzEwb1F3akJ1UEQ=', NULL, '2025-10-27 11:03:04'),
(820, 210, 'user', 'ua0+7Rk+XII1uzZxzL0AbGd0UklYRWFtMlJkOHFkUVQ5MHRpaHc9PQ==', NULL, '2025-10-27 11:03:15'),
(821, 210, 'bot', 'v2nrqkA1/6wx3FvDMAJwI05tcGNBYno0a1hzbkk4bGhsb0p5K2FGSFN3UFBFOE1vZWRXczM1SHh1WHFvTlR5QW94QUNUWWNrNEVKYU56UjR2WnVLNlpub1RSSkYrQXJvRUhhQno5bVNvM0dHNGRPR2kxSEdHZ29DTVZvNnVDd0tscEZscEpGT0ZJS1Q0NXM1YkI5SWc2TmNSRVRrWjY3ZDRNaEpSMG93Wi94QUJnakxHOFRwZG1IRHhiNXlKK2FnR2QyUTB0TG4wL21DMWZuVw==', NULL, '2025-10-27 11:03:15'),
(822, 211, 'user', 'hMX78T+pnw1dALCp9NyhVENrKzJYRExSWXdqN0R1M2VxWXJwZlE9PQ==', NULL, '2025-10-27 11:04:16'),
(823, 211, 'bot', '+nzMFp5AgVOaD9e8bvZI4UFJaGsrNmpkY3Vld0tybDRSa2RHeFB1S0RkOUVYcTJkL0xuZGtDOWlaSnNhSmcyUDd2RlVIeWdrc2U0ai9YRFVoNFI2RGF4bFRwbXM2ajNQY2Q0N3dVWHR0elUvYVRJRENIS0ZCVDNmWWk5aVk3TXdwTWRaZ0Npa3NaZkQ4K2QxamFzWVlUd1d3Q0pvcFlEeDM2aCs0TjV2WVIxVVo4ei9mNTFHZVFWVnFWMmtBaEovL2lFdEVneXA3aGxwZ2NlNDlaaUNTWTFUY1BVVnJHNlpDQUIwZkE9PQ==', NULL, '2025-10-27 11:04:20'),
(824, 211, 'user', 'skqGSgorgPwkCcebb6TtMFBqQmRldEdlSU96YWlTbjFxbTJhR0xmcGhhMXk0WTR4bmF0azVDWWhCUk09', NULL, '2025-10-27 11:04:31'),
(825, 211, 'bot', '+8Svav3Y1oZdb8V+I4qLdXVpUElmSnZvQ0dDakFzRnZPcXN5MEtLVEdjOVhGQkxtWUFDNzhzcmRoUHZMYXYwdDNUenZtajBwNFI3K2FkaGZ1NUdzbXVxMnNXc3M3Ui9hd2tqdm9EaUovMjN2M1RCV20vekVKdFF6U241NkNUT1VwWk83NU52b3hNREQ3Vnd2K2Y5bm50cFA2TENNVUVKNExCNExZWnFYRFNIQUdQeTlCQVpDYzBZenRNdW1XZkZLTWEvSGdVWFQzaDBWSlZJMHpFK2JtRzRwaVZxMEF6K1FNTVBmQmFvTlNYZGJEL0w4cUQzVkk2WElOWDBHQ01RaUVlTXcydGJtNGlLNWUyMHkrc0FIYmtDSVlZc055bFpmOUZWT2VnPT0=', NULL, '2025-10-27 11:04:32'),
(826, 211, 'user', 'SBnbM1JfC68AbQzmI0tsHkVBNTRmNUJmbDU1ejZvRjZnUG54QzhZZVBwM1JUdDE2NkFwcnUwL2NKdms9', NULL, '2025-10-27 11:04:47'),
(827, 211, 'bot', 'zyq3nzsXZn2XSAyM25IZTno0ZC9RaExyYVJDb3hkMmluZ1lycmgzV3o1akwyS0dOQjdhQ05Ub09iQ0ZveEFXbVlhdFRBMDRENG95cTdobWVDbkxaNGFtOWRsQTRhZHc1Q1M4QXk4eUhxMGpiaUJFVWNFVTQzc2NsU1RUK3pYV0IweW42ZE1iVDVKVUhLMENFZWVnWlQvTlRRQTFuYjFocjFBL1hUMUpOZFdUTFAydG4raXNWaFIvc1lwUT0=', NULL, '2025-10-27 11:04:48'),
(828, 211, 'user', 'yK9fYgjJst2D84nAx+1xx2k1UE1iYzk4Tk5qUFNxNG4wMHF6SWRGOXJQdEx1c2FLaVlPb2liL3V0TEE9', NULL, '2025-10-27 11:05:09'),
(829, 211, 'bot', 'Pju35mEADWJUNXvAJVchcG9BQXZMSWRxRWowYlJ6Z0w1VElYbGdqYW9iTEgxdHFraDJHSFg2RU5RUlVXTXZ3RTVkc241dHdYTHN1UmR6ZGFUNmg1N1R0ZUVDeXRpenl0ekhkcUp1aXlaN3FEbDRUZ1NDajY0Z3hVU0FjUVRmU0NwQzUyMzBYRFRhK0VmY2srN3ZLU3cydXlKelVsZ3NlZmY0VEhubDBXMXF6VDczMHc4YXFRQ0V3Wi96QUF2bFZRekQzNjB6RGJKWE80dVhSL3VHOTU2VXFkSU5EalVhYXBETVZoOWE2WVlUdEhNMzE1dkN3bC9XbkpJR05EbWZ5L0pDOTVBZG1NNDRxUXNiU2MxRmY0aEx0bnFETUhpU3V4QlZLUjA3R2tTb2FvRUF5Qk9RYVlXTHFDZ1gvWHFoK1IyMlA4QnREYXl2MTkvZEhGREJmYUsxY1pEcmQ0b3Z0d3A4bVVtbHhhd283OFpXYURsVEdad0l3cmFHT3V0RTZRdmk5NjB5TTFlVkhqcUxZc0FoczZaR2dCbDhXYkk4dEtJZjdKcWlDdlM0WkFSNFNHNUYxMUdDMGFzR3JMOWpkdGJzM0sxc0Y0dlYyUzJQQlhyN0hrbGs4UENIN2duN25ReW1sS3VVWFpVNjB2UXVYczRiMjFYTUNuYXE3S240TmdFNktiOG5DOHU1S0VlNGVGT0ZEaVByYUpqSlE4Qjk4Q0twZ0tkRTlhSlBGT2RjOExhaVkwbmRRSHlpV1Bja081Q29pV1M5eVlCUVdCVTU2dGhVd0VOTm9pMXc5c2VPaThBbkg1bG9UcDZIRzhqK3E3Y1FHb2V2Q3ZpcHNkeVpsZ0QwWDZmR1cwZFFjdUFXMWVqcm5zK1dYTW9QcEllYzVxMlhZeXBxaFIrUU5adno2aU4zY1RQOFk0U0Z0aTFjRXpmZlBhaDFpYVZWWjA2NHNFZmNSYzUydkswQm5FV25NT0hvb3JtTjlOOU0rMUJkYnNwOHQ0ZlJ1cjI3cmxPS2pjQURLdUNIZkRuc2FwYTkrMk9xV29NZGE3SDd3S1grYkgvVDJ5Z1JNQ3lnZzhjSXhSUVE3NjA1cnVKbUZOQUt0endMcjNNVURkODhoV3dNeERGWi9aZHlCZG43Mm1ycTRheW91VUVyWkk4Z3ZwcUo4c3NneDFFNWFydmYvL2QycmxaTTVUUVpXT3k3czl4QVo1UUZGZEM2RTlxMTRTaG45dyswdlh2VTExemtGMldta2d0WHFWT0R4Y01zWT0=', NULL, '2025-10-27 11:05:14'),
(830, 211, 'user', '8Afbhy1of6Ddi0af2rRMv05lZlJ6dmI4NDhMMWFIZkZDWUlXeXhnNW45SENpN09PZjVycTYrT0JIWEU9', NULL, '2025-10-27 11:05:34'),
(831, 211, 'bot', 'fENdwmuG/p1EfSQP0vyMsnZ3UlNVMlNzakxBbmdhaGY4VnQ4Z1hOQVBxQVh4UzVYODNBbkE3K0lDTUIwdFBmS2svZDNuVGI1S2ZhaVJHZ1RiUC9WZ3VHREQ3eDlsTmxkWnpYeHU2ZXJqekZZTm9OQXZFdDFqU3BObTZkcGFMMjlEam1hUWQwaFNGcFYwRWoybmJKUEh6WGVxTkhrNUk0cUFUYiszMmM1cCs0WUV6L0M1QmtlZnBmWDEwTnVKV29MQUcxTC9pREYxUG1Va2JwNk0zVysvWDA2TVdBNEpkaTFwMHJiTTRLS2dtNnd1TnlZaXBaR1cvVnNWWGlYQW5kV1BUb1hvMFdDeWxHWGhWd2VSQjd5c1k1TXY4b3lYODFOZkhVVkl2Z2gyQkR3N3JjWVVQd0x6ajNJcEIvdnh2RExrdW1BWmNrOWZucGovdXlZODV2ZFdDSDI4VlcreEkzNkw1enZqekI2RW9LRkhPL05QaTRIcjRUN1FjSFIxWGpja3h1SHI5T3pQWFZMNjhBS1BRekNGd051eVBVcytXSndwZi9FcW5heThqYW1sTkxuTWZsT2JEU1BOSUZRb2djVFlZdm5KemVLdXUySWRkOEpsTGpqWDV0NkpZRjNoVUJoTWxZZUYxaWlENEV5RDFnR2huTTNJMkFKWjh4TERHUDdtOUdBVkxzeFRMWjhjSmNod25pYUkva0tLdE9NNy9GTDZYUjlvWVU2QzdoT3hWR1BYbCs0bGJkK3FUUk1aQmw2RzlDTllxRWsrdTh6ekdJVmpSRjI4MWxpS2xjVTdIbVM2Rzk0TmVweU9aQkdtNXVTNWY3NVZPVEhTbGg5UE9lRDBTUUgwTnJBWmpaN21ORDJDc2gwdnNQdE1vVzNXN0pOUUxTUlBXNVdPbTdjQytaZmdzU1B1UkwwNWhwZVBqd0oxWXpkeVBZd0xtcEV1cW5ha3dvWCtyaEE0ZnNEM0tkWm42MG9rV3hnZW5RVnpOSjlyaTN4bEs1TU15MmRaOGZSdm5LRS96dE56MktpUHBXcEhqdUJSWFRFVGdQeUNHb0hoSHZEbFZLdUJ5ankwSWxoN3kzOWd1Z0QvOW84cHpla2d4cWQ3dVpSdVlzUFdPcVJmR1A4ZEFjVExFZW4rTHdJRmVlZitTWGVEb0RhKy96Q095L1FCUUcvcmpkS3pRM3pJU09vVG15T1pGUm1rMllV', NULL, '2025-10-27 11:05:40'),
(832, 211, 'user', '+uKQ7nARznNfNzzS6PNd8mQ0R1k0RjMwQ2pDTkcvbVJxZng1VkppbHZYUU12UVF4RlRoUG0xZEgrVU1mb2lIeWl6cmpNL21xdFNobEdFZEM=', NULL, '2025-10-27 11:05:59'),
(833, 211, 'bot', 'h1wcuA/v5R4AlpAHCwn4ZDRoZ2o2UjBLYm11Tjgxdm5LMk1BcmtXYjhGQi9sWjZpWjVMZFZQZnhnTlprMUY0enNQdEIxV3l0K0xUd01vUFBlaFRFbXNHakV4Tm11YktiUVg1ZXJSNTZsNHR0SHVNeWh3QVdXOEM0Q2c2d3lEcWdtTEEwMDBnbzZIRFZ5NXhjbVRCbjZ6TDlSYUcvSSs5KzZzM1d6ZDJXVFpxSWFrOHF2QlY1NnNTbXo5NGFIV3p5OEdSbjRWNXVCcnhpODNqTXl3YTBOczluZW1aQ0xoSEFWYkxoR2lHVmpUVmNwWEZ1MFMzMlpWWUZKc0VacjREZjlMblIzTXU1QktLU2N5Rnk=', NULL, '2025-10-27 11:06:02'),
(834, 211, 'user', 'W3Me661hhRQgO/plHnbQikZJazRRZVhlVFhjWjlqL3FrdE8raHc9PQ==', NULL, '2025-10-27 11:08:10'),
(835, 211, 'bot', 'gh88WRb5HYS2QLiaiId91W90cnRkTXRNMDBRTk5MM2g1d1NXWXNUN2F3dUdEKzdvRUVibW03aWFLb283bVNPSnl6NTlkMzcvSzlYQVR1Ry9Od1VpL2JYTmtGdUpzWVhmRktPK0NiSGp6NHNqUnB6MitvK2VlZ0pXY0tpMzk1QVpCdDNzRllTSEtEbmpuNkhCdjdpN2F0ai9JejlKWlZkU2VOYjRyTUU2V2Z1blJxZDMrZlkweUdDV29UYytsM1dtNVpjeENpS2VmSlBNdFdXdw==', NULL, '2025-10-27 11:08:11'),
(836, 211, 'user', 'Lpr0VA4DTLKzECikCJ4S4W5Kc0NIMnU2M29vUHhjUU5oSjE3U2c9PQ==', NULL, '2025-10-27 11:08:26'),
(837, 211, 'bot', 'RLqnJSiXjSIoLL09qfjRbFpDemFUYjVnV29hamVsSXRTRWJ6NGQyQm1PaEhWU2ZNTVp5NXZMdnN1bXI2RDNOWDBvQXNWajdOV3ZwUDdvQktKVGxuTVZuR3paMUlxL2VFeGx0L1RWNWZ1b0pMK1dCUVllZ1hUOU14MVZpdFFZSWYzRDlNbGZEcHY4eUtsSXJJL1N5SnpRUS9PVGx5T0xBTFNiYm4ydjVtSEVhaFFTeG16cHhwQTRaeURkL05tL1hORmdURFZoOXNMMFFwMlFXM1R0NUVPVWVDdWNrUlNkenltMjRpTlE9PQ==', NULL, '2025-10-27 11:08:29'),
(838, 211, 'user', 'ccLRSmjjBu1SN2R9rIx7MjdTdEhrZExHTmRmbkF4Z2pGRjBDdkE9PQ==', NULL, '2025-10-27 11:08:39'),
(839, 211, 'bot', 'ItEO4UbW+Jwt+XrGBnWWjzFxSDhNY0NtazBVbUVodmhHSUpRWnFPR1FWaXNPcERkUm1NTS9nR3J6c20zb2FpNkl4dVYwK1JMRStwSzd4aFJtMGNhQ2RYWUw2MGNtQTZnaktlQ3pGTDJxWlhaODIrVmNZNndyQitZdkhDK1I1RE1HOWhaMmlnVDFwUjBZMEdzMkgwcmhRWXBQYjNGZEsvV0J0SVJsdmtUaDI1ZTdIRVpIQTd5emRyQXhjaWxrekNFNkE0YmJTQTFZUk52ZGkzejBnTjl1VXV1TzlDM0srWVN0U21hSENYcEJlUk1MYjI5Q0pVVnFGbUF5ZkxXdTZwdG1IZnhrVGVYUm5yUEdicFo=', NULL, '2025-10-27 11:08:40'),
(840, 211, 'user', '5L1utrfJYE/UyWCt21H1TlJmVnpHVWNqWnpmbVNHM1ZSSmdGS0U1cUNaS0xmZ1c0SGkrbUdzVVBZc2ZKSTkwVG5scVo1QjB1Vkl3ZkwvWmg=', NULL, '2025-10-27 11:08:58'),
(841, 211, 'bot', 'FWY5toj/Vl1UCq/hm7cdM2xtRktHNXA4WnlBZ1hzQW5aZGZZSG5WYk9NeVpURWQ3aUliSEZBUXVZak5Sc0toMEhWbW9xdlRTQnYvYzZxeE5idHI3RWhQTWl3Q0FjYWtycVJsQ2lGblZEQm1IM2k0MHdmRmFZU0pYeGVsQTkzQ0xXQUZRbEtKNmhiU2VGLy90ajVidUtqb3BnQ0Y5T0lNVE1RUzc4TjQ0NC9ESUY5TmlpL1dOM0pHUVlWOGk1NGRYeUdFMmdVUTN6VmNIZVZZUA==', NULL, '2025-10-27 11:08:59'),
(842, 211, 'user', '90Vq+n/e2BWKhDdSeljGckJPSTFkZ3E2OGpFZUNrWlAvYmp6VUxjeC9rRDNGZUkxOHFjZGQzN0VGTEk9', NULL, '2025-10-27 11:09:15'),
(843, 211, 'bot', 'xZnOxdoVCAFIn0bG4YhoOEh4Qk9hL2k1TVEvSkgvUHVmbTRUT09yaENVNzN4bnBsUGMzazhNcERETm5HRjhYeGt1RExSOFd3TmlNQVhMM1h3V3h3MVpSeWpHN0RKd2Fpc0JNS3ZLdnI0b1c4WXVNYmpsUHgrVk83Skt5RkJMUXZrcWxGVXRYMDFzY0FBSjFIRU85bWNmc2FjVjFCbUNqcEd5NGhxMDhrakhPbVB3aVNnYWJSc1lPZDlhKzd2dG9JcTFTZlNLTitMcm50aklFOWtPR3VHcmlPTnZoaVYzQzduRjFYNlU4Mk02RjM2MVVlSTRoU3YwUnArNi9ScFd4RVV1WXpYQm12RlQrR3IySTdyQnd2ZDNsMWxMdVYxcnUrRlhVN3ZWMlJxdTViakM2OEUrbFN4NVZBY0hXR0pxTmlMTjZpMSt6TFZtTnZzVDRaMEozbkNTUlRTVlRHMnROVk92elZwMko5dng5K01xUEhscUhPNVcyOG5yL2JPYnk5dUpwVGNQa21JOFFEN0doRjJYcTV3c3BIZlFKSmdhWGdtVDJ1c2QrMDN2ZFBaUWNjN0tDd2pwazQzbHgrWXNaeTlWNHBZdW9YL09RTHBhZmtNdmNrWHRUOUgxVFFNUk02MXVXYmxURUhBQ2VzaVYxSjZ6ZGlveDZpdVB2dzVuaEpwK3k0NmduMFl5VUVtYWVyUjBidW5HTjNmRVdlK3lFVWhqOEtrMTI5eUdKT0xXZEpIc29nRCswUXYwaGRnMFhpS2xDc1luUjROVDFrQXVLdE9mL0tzbEVNVXVIWFhPM1A4Rkpxby8xTEUxNWRZdU9qSlltREQ3U0Vyd2tBTkNnTEVhUkgzdXFGQk15RXI4TGR5cHlQTG1NcjMwMktGVVBiM2o4eEp0ZVNDdXpZaHFNYmtLcFZCdHYzcHBwTkxtTTRzZ3F4OUJXSHdtMmJ3bUF5L2tqMjBneUxhd09lMnUzdmgwcTNkb3pzRjNraWJTZ3E4YjlRcDZROU5DanppRWVPMGRHbWNZUXEwOWQ0Z0d3M00wUXFYT3NWUG5NTWFyOVVqbHluMUtmM2R3c05LcXcrcFJ5VHVMTEdBeUovMnY1ZVlUZmd6WnpGTDZ5WnV0TkVxeVJLd20vQ09VOEhRemRJYm9nU0NtNHhkVUl2T2pJS2RZVTB5aVJmSk1DWmwvOFcxdWRSR2c2YlY3NWEzMlJvZFl5eVEwblprVUEyWGhRNFFrQ1cvOGw0UytXa1FRWTdTQzRnejRocU9uU3FpbjhpMERWUnZBbGVDbFp6bDNVSnV3OC9NVjhYVVpqeEhsR2NRQmlpZXFkSTVKMWp1ZHhPRXR5UThKZ21wZmhmc2dzREV2WnR5QmxzV21VcXJ4NURQK1RDS0dWZkZUVXhkaGFyMXhYa1FCbGdzVkpHSEF6cUpjNDk0RDJuWmF2K1dwVVZNTzNZZ3hnbytZWlRBc1hidVAzd1NXL1hLbjEwbUZiS0RrNTVhQ0VYbkxJMkJEMzZlMElzdnNGWjZBaz0=', NULL, '2025-10-27 11:09:20'),
(844, 212, 'user', 'xM4r0604aXKrk7hFfqrFbjUyWW5kc1JmdWpjQVY2UktqTk5QWTdDeVlXRmtSd1BOemdXRGh5TTl5OVk9', NULL, '2025-10-27 11:38:13'),
(845, 212, 'bot', 'RPV5q+vRzDntdjA9djY0pTZyaEIxaTJRUHJpS1lKK1ZKK2pKRXBZZ0daTTF4Y1lkMXFjTFovVytaQy9mQkdKbTh0a0o5K01vUzhPNkxlLzRTSjRoVEFBc3I4WlpRL3FiWFNTTGN4U3RDcmNZRkFRTHRLTUNFejgvMnIwODh4dkNNdlJHa1ZXVDZVQ2h3OXU2eUpneDNxcTZ1RUU4MzJ6YzU5TVVHYzFBMXI1VmgwTWdxdnRLTG9GY3M5bVIxdjJZdjNyaUY5TGM3d0trdkZXYg==', NULL, '2025-10-27 11:38:15'),
(846, 212, 'user', '+TNzCDfdryGuDpmirrOUMkZRaCtKQjErL2g1aU5jTzVkSWlEZmc9PQ==', NULL, '2025-10-27 11:38:34'),
(847, 212, 'bot', '9KaAipZTqmPO16Fxt9j81UJSUmNnNGxpL2FGelUxUlJGcHZTRWFZM0J1YjRLeU1tREpOVVJXcU9hWjRwK0YyTkYrN2toZU9iUU5PNC9iVU92RGNCd0NGbGFEbWdQRExGVFZaZWw1amlVY0dJUmFDZjhDdE8wbXN0UEZveGNYQlBRRFpTWkpmSDFKc21sTUZoZ05VZWJOVkxkMmo2dzF3Q2lJNmIreTh0MjdQd0JDQ0dhTkpDbFlhQlVRb3NGUDhOSGwzWEszcW1zU0s5OEliTHpkZXF0bjFRWFkwNXlHVE5rNnpYSTErMlJkLzgxcVFZbFMvNWF1RWtZUTN2MU5EM0kza1o2WDR5bmUvRnBoVm5haHBia0FOMWV4WXBkUDJIWDNnM2ZkbWlIS3laSW5mTnJPcS85NExDQU4zOXpPZjJMY1E2ZXQ3OVdaUFU5QUpWTnNzN0RieWdrV09VWW9qR3hzOWVSQ1NpVFdZRkRBd3pYcnRTdEp6RFJvTmFHZlBqMzBzYWZ6TUkwRVd4NmNVVkx5WTlkZEJiSEN6aVFuK3ZUYklScEtpZnhhek1GQy9GcVBIMGtjOE5qK2xzd0hvdDZ3TnYrUExOOUdySzNjc2h3K0JybFdreFNkcFhsa3JUNkhJc2ZXUUQzZ2U3K2hXODRTWUU4WEZHSFgwdHRiVWliUytSdkpjNkVVN3JtSXlJd0syVmNDR043M2QwdURJc1YxaWVsdkxLZmg1ang1K3AxdWJnKzNFam5vV3ZncE1PbHFLSmZVWkQwQ3dMdDI2RVhyL2V3czdjQm5sMUpaSHQ5ZHREcjlHeHE5WkZUbHQvSE41QmpLQUdMaWZCTGgyR3lkUHJiRWdJeDBMUklMeStYWm9ZMjZMKzR1Z3RNdE04bjcrZnUwQkF4VW93RHozWHFGdVoyUGIrWWhsSmF1MjBBc0hJKzVxWnVaZFZPUUF6bmNWUk1TRlFMY3I4WDZPTGVvem9VVEhtT01rUVRtTHhxcGcwQkxCd2dUUDZhcURYT3BEWXh2V0xYYUpIYXg3WkMxWm50MTlHakJTWjI1MHhEdzBHbmY3alpjM0hXZURncm9Td1Rid3RJbmluWUpBMW5FbUZLUUVIWkxnQmx3RkJxWTdwTVZxOVNsdTArUzNTdGxzelBWVnFqdjhSTk85Yk5vQ0pJRVJiL2lvOERrVXNFYlY4ZEg2OFJWeVg2YktCTmFxK3dqVjJCOHlxaFR1ZWM1KzIvMkhQbGRjaEx0R1puMHlaMk1paS9DMlVnSmV3RTl0QjVZRFJGbUZvT0RjalBDd1dieDhVQWJ3ZXpxZHhrdEwrMW9sRkc4aUtBVVE2aVpFK1U3SXozSkt3WUVGTklIaThzd0Iray9PaHRLYlJSTkFWZUtpL2lHZk5LdW9oR0pOOVdrVCtGWHZnRHpQRXFyWGVUaFFuN2hDUUlKVVBlZTZWUWhrR0d4c245eHFFM25aSVJtZDk4ZVp1cElQUHJpU2JmNTVzbjVHYmd2NmZHb2FTSC91N0h6dlBUcGszWWhhV3IvRGtsUytxbnFyaWU0SUhsc1FqNlBpZWt2cUlHamI2a1l6alY5MWNLdWw3V2dmbEV3b0x2NG5BUEJlQ0YrVURpODl4NmJKdXNySmtaYXY0TkNtNWF3QXhuemVEeDkvdVV3V3ZMQUNsYnl1eURhSTNQc0VKWktoQzFyVXNSaXlTQTU3aElZRThPZ2ZiQXZRSWI0dE4ycmo2NmRzeXhvQndvS1RJbW9BNkJCZFhaVzgyN2pxeDhIaVc1R1Znd3duNjZzMjh5ZTB1SVA5N2l2d3lGTU9hby80QzVsNmhnQWVqQXhITVpwb3lBSW9VMTZjNkRBQllOcjlOWFNpL2hyc3pQMkNmYkhYMU0wRnp6L2lSTVlQRHFMSDFINmZNVW9tU1VIWWFlbkpIMzFQTGpFVGJxUDYyRGxyU2txOWg0Slg5OEs3aEZjQmtaZmhWVlVSeWJNOVdMWXZOc0ozeXYraGRKQnlvN1g4TGV0R3ZFQVlKellsdXpGUVVNRS9FK3dJL3luUjlZNE5hRk5lai9WR1h1K2V0dzU3R1VhNUE3b2NKdUN2N3hOeGhFZG9OdUIyak1KSW9qWmR3NDlGa1dyeTE1T2NzbEtKc2EzeFIzUEpGYjVBSW9MNzVPMTFSVXJqL0xUNm5lZG9hYlVIcHdNYnhFYmxEMzJjTUZXSWZwSTZ6QUFqVkZlRFlaRmJ4WTQ5ZzhhWXBDc3RZbTF1SkE2UzNHdz09', NULL, '2025-10-27 11:38:38'),
(848, 212, 'user', 'EWEFNn6Lh4IOWyFDx56CZkpzNDVWaWU1MGp4UmJQdllON3hmVHphczJsRWQvcmE0YWdJSW1zcDBvUGc9', NULL, '2025-10-27 11:40:01'),
(849, 212, 'bot', 'y4yRr7h5xbErWvPhRJa1tldDa0E5ZlNoWlcvZHhHUHVueW4zUXdzMzgzNlgreSszVVF4TVpXajFQY2ZnUUdha1R1V29Ec0hPSlRLVXpFVWIyR1VxVzZsWVpibExNUlZIYlE1My9XT29xQlNSNXo5OEk4Y0dSYUZOS3FDVUUveFNoY0dIVGVDdEZZM1d1emphY2VBaERHdnE1OXZEN2x2c3FDTVBuenZlbm02ZVNzRWI4RUNmSmEra2E4cDRRZU96R1BuOThIMFhCQS9VVCtiN2p6NHNGRm44MHV2SVE3elU4WHBhRXF0Q1RndTFwL0ZmYWx3ZTNSZHpVVXBvblFCTU5GaDA3VlF3cXlUMC9ydFM=', NULL, '2025-10-27 11:40:01'),
(850, 212, 'user', 'NW5LVoWpApkm489X1A3Ro2dCMzdOZ2RkNUViaFpyalhuUndSU1E9PQ==', NULL, '2025-10-27 11:40:15'),
(851, 212, 'bot', 'n0Pwx463rT9SlKUyjGyDekVaMHBEUUVISVlkSGZkNk5zeGxEN1E5MjJ4WVAvOXI4TUs3SEgyTG9nWUl0b2NrWFRSRTBhS3hQRHdYamFSaVdWZHA0blhtZVhGNWk5cENubExjaDZDM3JFZDFZMEtCUjRzODBnaDF0R3BXQmdBcHpUeU90VGc1WmRWRTNnNVZDU0s1dkFTNSs2eFJCQXNWTWlnQXZWMVFBa2JERDJzSnZkbkQ4c2liaWdzaFgxcllBeSt4a3M4cUtZMWd1bDBCRnhvcjNoY1hHak4ydDRNMnladWc4Q2Y1K3JQL1Z4ZEthWFJYeFUvRkRZOGc4aW51UkNVVC9QV2RMc2t4VklFTEsySE9jTzBHZ0FPTCtPSnFJR3FsL1hvdndDWlQ1K3N2UTM3NUhJSXdBaTZuVzVsZUt1akpQOGVLWlo4UU9QOXVWWlM2akllS2NSSzYxOVNwQTU1REhxN1RiS2hteVFrOVNpdjFQdHNNZVNTc0I1NUI1ekY2Ujhpc3RaMXBHYnZUbHNpNlVUc1BXeU5uelIrcnZpQUt6bTBoOWdSUHBLUDlBcVV4b0RHMzlHQUEvSTFNbi9FYmxkd0g2SU13dWt5SXpiMzFTMmRPVHNwbysrblJOdzhWRStTYm5RckM5RnZRZmFWRElFUTZqaXgyVTBqZHc5dG83bXpqR2VPZ0owTzNWQ0szT2l0NjU1eDZ1MFVDejF5ekhvWlRydlh2bUtZOHZaTzFHNzNtNWhSQlFsR1ZNRUdUVzBIWlVwcVFuVXROVFp1N0tmWHRrQnI2Wm43R1gvR0xDQkN6djVaaWxJaCt2V3JvWm9jWXZpdWVNNjNTaS8yTVRZSGtRZlpJQUdaVnJ2SnFrMmx6Q0NkZTM0ZkVCNG42OU5vaERoMWtqUFhQb3lxc2pBTTQ4OWdWaTZKa2hGOVdkV1R5U2drdW5vM0kvSk80TmcxSU5wakgvOFJSVGROZXRlZkJCTmg1TEtsU29FQUVQa3NaMWozdkhzbUh4cHVCanhZdTMvT2xWaVdOVXd3a3dJODZlRFRubS9uRm81Vm82QlNtSzIydFBZUWdqSm1zd3U1ZFpCMWxmVTFYcDRzMU16akxBaVNzbHBoMWdDSk90Z2p4b3Y2dm5xdlh4QS85TVlkMGhLMjBsNE5tQ0xkL2V0bWRZN2crS2lOQnJkRXFPcUdneGZKUVJJYVdxRVdHanlsYXpNWW5PSEpTVFF1UG5taU1xZTIrNnhWZ2RXa1ZiazJOMWlTbTdKcGNxN0tTOVNuTWk5UExMa1ZHMzFzVkZENms0b1RNWWR1RTVPMFRJdUlEWmxjcE9EMy9wMzFZcDI2a2p2THRuUWNZdlpWWW9XbkhQQXlFMEF3NUdXZHRVeHIxZWQyODVPYVZmZ1pHRW1TU01YbzBwSm0wbEY1RDArMEw5cTM4dzdjTU5ldEVTd1BsR25tcm1kQWVHZHNCVWJkamlwU1B3eUFpa3ZRTGZ4UkRxbVpOU3FFeDA1YXY3SnFocnA2bUJWajdEUE9RU0ZOM09QaldUd2lvV2pEaWhtSzVVbkIzdHF6MkZ3V3I5VjZIZ3B1SW5GSHErd3R2MXFGTzlIWkg4ekdVZDdtYm5RenY4MGJKNTByeExWL2IvWFNNSFNtYWNDalFTODFRbWVYZmMycUtoTk9la2x0VDh5cS9MVVc3a09lekFlL04vU2RQR0tXaEovaGZVTzNXWFlkL3lra0xjZXJXS0Z6eWZnVDdGcGl1VTRDYm1VdkVOaXNvWnlrWFRiYk9neHN3cjdHSXVJdlIwWFVpNHVJYVpzb3VidzAvbVh1WTcwWXB4eForWnFrcThLNnZPb05EbWdlblg5SzIrOGtoWVVYNktncG1WUXBLcDRlallzWml4VmNoa0JNejhoN0NlaUNFVlZyUmRZcFFNQ2NDWUk2azh5TUNqTDZpOWlvZGxERXY4RjVjT1FHcWx4MGhuYzNucld2bGU0dDZ5RkJXcHhONVFUYW1kdWhJd0JRYXdiaFVvMjRKVFZWVFlkanBtZ0lGKzdhTUxPTlJpRkdWaFkwTTcrNytvZW4wTzlUL1B1Q0h6eVpsL0F4Q3lORjlJeXJ0b01NWWNLZHo1UXBSL1M1QzJnY0dnT21HQ2RKUmgyUHEvSlA0cGlhcndIcENOYks5V25pSzlLM3Y5OFlZdVlIZURGUkVvWnFIV1dWNlZ3VkFpc1drNEZyWUpITldGSGdWQzdIK05WZ1A5dGl2S2JQVUQyUVVLVzdwMG55cnk2Qi9DVEdLcDBiOXU3cGJmcTNQUERqQ0VSaHVXazNUU0t0Tk5QM3ZlOHlQOVB1NGx6amlIT28yV1hJY0VZM0R6Nkh2YjZCNUxTaU5HY0hMblRhM3plYWFtYlNzajhIZTZuT0hYck0yYWVRdWxLWElUVnhId0I2bmJIR05MK01wOWwzQWlEVE9xRjBNTmU2SkJhdFZKeW80am41anpFSzNvZG5FS2tNZEtNdGd5L0FWaGFyaDZwaSt4N0VMZ3ZPUTl4Rml3Vmo5QUJzTXlUMVFTRndjVmtFUGs4WXAxWHRObVFKN1ZkUVZCcXBQTGplaU02NTV6c0Flbk1wR282OC9xYldLTWo5U3ZrS09hU3lQS2VwdXZId0hJTERMUGdHcTBERVowamxHejh4ZWU2VmFlUlM2bGdaN1lNSFJBeTRJK3d5Qy9NZUlOM2dCbnNsYk1Rc1FLQStVZVBiYVorR0FlQ3puQ3YvajF0Znl4aThrWnpYaG5jL0p2NWdMZ1V4MVZ0M3ZQZ3hBNmlmL3NlRlRGZGgrejd5bUFmZ0lFeWdsWkEzekRmSHB1T29zK0k0OWZEa0tFZ29ZMWsrVjZFcWNibDhoa2tnSDMzK2JwZVE5N3gwakRrNlF0SHBRVEZUTTEwOC9SS3BQL2FtSHR4SmZPTXVrVmtubFZLUndzbm84bHJGaEVicmNoc3pFWGdjbEdoTDV3V1lSN0RXZjI1VVVKdExGYTdFWFVuK0o1REx0RTZXLytubnlzQnJYVnREOHpLblRiVlNpYVdOaVpIakdxRDFTWWQyVGZmSWNkcndTVmdsb0ZTQVhTbHAxMnkyOVFvTERqcnF0dGo2SDNLcC9WT3llSjNvYVZlYWNhRW4vNzFzYXZuRlJyZS9vS216TEdRMkxmSlhGODRGMXh4TURtUnluTFlZV0dDYXhZVUdHTG5rUVJKVmNlZjNqWU1jeWliVk9mNXZhTDlzYU4vMTlDQTR6YXQ5ZDFrOWlvMDFjaTA1RkFjK3lMVjdLV1lHZC85cTFldUVJd055MzNMaE5vUTJWVk5adVNTVjh0VlNQajk2cERCZHVlUHcxYkJOek9TUGx0VGQvTGZYdFhEcVI4MytST1IwckFyUW41NkhWSnUvMmVnVllpWGJWRWUvdDBnKzFOdFlmdFFRdjA4SHh3ZFJMc1RqcksydnFqd2NnOGdiTXRnY1lDMmJ4VkJ1Q2ZQWjJpV3lnZGxJb01RNUVUOHJKWmt5Sk5ZTm5TSGhGOVNZdHE5bEt0ZlRWQnZ0aWVCVWR3N2VKN0hPcUZqTlRhc0EzOGx4MHo0NUp1WHdBWTcwNHBRQTIwVzNSQjdiSkUrK05obUJMMjlpUXVoc0dxT0oxaHh2Y3pUTWNsL2kvVk1JZlZBdHlXN3g3aW1YQVkxSGNhdzROWld6aWZOVlorb0tPWFVUdExOTGV1cDk0ek8wVWZNeHdLUUk0RTlKMEpBM1p1TWxCaitMb2VVYXdITTRHREUrakczMHR1dEU4dmwrV0d1eEFtS0hYZWw5WXVEdmUvUko0cDBUUEQrZjJmUk5VbkZVZmZOUGZIRjVDWW1mUUVweG01TWJ2VDdQVUxOaWl0QVc5YmM0MnJoc3p5S3VCZXRBOGFlSnVFVXZQUVlxQUdwVlcxV3RhWmxYKzhqNEdTcEdsVEQwd3BvdXNkOGZCUkEvdnJxT20rZ3JHdy90NytwSEtCNE5VSThjZkZJR3E1M2tDS000cndVYkI3TW9DT09JWHo4ekNFcXVsUVJiOUE2ZXd4b0xkZ3Y5YTFrYXJubDgzMDVLYXA1T25OM2NXd2IvQW5kUkxwRE15QSsyUkttV3QxMDFsQU15K1RDZ0RwMjJZNnp0ekpFVmlYZ0tSOTFrckJKYXZRNFM2MDhzL2pqMkVKaUZjMHRCcm1XZ3RhZXhLYkFBckNaOTVBcCtnbTZ3PT0=', NULL, '2025-10-27 11:40:16'),
(852, 213, 'user', '3Y+VKspXD9/Blg8xzwTgrmxEdUkrY1ExcTYvYThpTzBhNzNFUGc9PQ==', NULL, '2025-10-27 11:43:11'),
(853, 213, 'bot', 'JnQ5+JTvGr/TrqYRpZPSN0NybXNRU204ZjVHaVEvMUJNWjVtTkRXTXlWQjc4UG5PUENEZThnMUtMUnM2VTJVZ1A3dGlnTC9UdUdVQVBOd1dOSjAxTWdGZEFxaWpXZ0tKZ3lNY3RvenhyZllFWEloTTFaSnIrWWpkZlNxOE9vZXpMY0FLVmd3UlZ5YlE0QlRLZGM0d01vY2VWZkhkY2tCSmJWOXgyaitXc3k4T29FNjJvb05CSUsvMCtuZXFEN281ZDlQV0EwRGVMVDBkQ2liM3ZVdzExQWxIWnRXQmNoZlkxY3VUVW9IYjh6cytHNU9udXE3ckVNNFg1bHA3NGpyMit6ckdhUExtcFNuNC9pQ0c2aGJQQ3NabDdFbldrQ3VneTZER0NxaDF1YzlUWFAxNDlUVGpsOHZaZzg3Q2VxRGVORmxqRHE2RmhIbWRpdEdBNFc1TnY2MHhNUUg2a3JxbFYyeVQ1SU0ybWRnbHdGNHNTZlJWa3RhaklGelJ3UGJQamN1UnoraG03TS9Na1RqdjJLaFl6Yk1PYW5DTmtZTXc2ZUw5NGNFZFFZTmFmSGJnZS9NUUxlSCt4Q0lQVXFTMEpyWXFJQ2NZK2ZiUVVnckZlK1RsUjF6amthZEEwSkxjV29mcnNQWFRWQlRBNlBoRWoxWStSd1pSdWtPWFJ4VGFRYlgwcmZWT1BZQlFVb1hq', NULL, '2025-10-27 11:43:13'),
(856, 215, 'user', 'mM6u36svc+VIpS//4KE2l3QrZkZwZVU3VWNuZXFhejRvMlhzYXlOR2NtUkhVQ0pWbUVOdmxjcFJJYUk9', NULL, '2025-10-29 01:36:47'),
(857, 215, 'bot', 'ohocWQopf/1gkzj6VPH2xEZlUzNQOWdCTWFVbWFUaFcvTFZUdDVYemxocFZLMmw4OEJyVFBTbzJIM1RmMGcrc2RpNFdUQU5sazJwZnpZdGI5UkxtWGM4NHVpYWtjUVcrcEpWWTg0ZVRyVmw1bWZ6RXpySExnSlY2K0lDcTkyMElnN2tEeWZ2a1h4ZWVzQ2ZvaWt3cXpjSkplM2NRbi9MTUhtcWg0c1lYanZTbWFhcUhnbW5aaVZ1MnplMlAyaUpQWFBmS1FNV3N5bWxyOFFwTXppemptd09zZXhDeFVLMlR2ZmlnSjZBdTdOMmR1bWxTMC9WZUJocTgzSUNnK0FYUmh2TWt0YTZaSlY2YnNKc0o4aGRIZ003bUlmOFNiYzhKZWRKaDRyTVgycXplR0RiVWcwSEVDUjhjdjBiY0JwckJYZjhsMjlSdDJqK3RWZ214RUtnbWE4eDRnY1hEclBKUU1tQjJZRUVZQ1VPQm51eGljQVJ3OUgra2lsNTBtdEhmQ3orQUg4Y04xMmN3OTdjM0RZTXFxOFA2ZlR3MlZOMkRUSUcwUGVKa1Y4WVNIMWhjdU5RZlVsWHJ2Z2luUG1FSk1mR3h6bW5EUlQwUU8veUd2SmZhbU5RNk81dUl6RitkWjJtZzhNcnZrZjB1bndmWHpPci9jbmp1RE5TaUdHZlc5dDFKVUdhK0JxdU52Tlp3cnhjWHlnRFpIYVBvSnRTUlVzRW93b2h3dEo0UjVYaDFHMHBkRXBQR0NVZFUxdVEwd3BnV3NqRFpzTmVSTlF0UHNuS0p5Yk5BRElyK1dHbVhWY2kzV1ZuT09oQzlYM0VvaEhLRFNPdWpkbkQ4SFlKYWtyaE4rVFZXUWZZQ09MMVZMbzlNWEZXTnkxbG1KdHhvY3NRZmhoN2tiNG5sNGlyY2RmQWY3b21RVU43RzVvM3ZNWGlEY0t0bGdoTUZ0TnRkaWl2OU41TUx5TkFXb3RkTFMzRHd2ZDJsZXNGeTY1SVUxUmd5L0EvU0dHWVpaYkVNUmxCcnd6cEFyK1h6YU5RbkFsME9NR0dEVGRBVkNuenM2bHMrM1QvdEdJUlZTbXhMNkNYUncvTlBGRjE5cFp3UGYyTFdhN3FaaG5uc2hFKzVTTXV6YTZ6QTB2Njk2blV0T1RGS3BuRXJJWnFxUnp5TE1yM2REbitPNmRKYnJDRjBlU21jU2RkdVA4ZnZjYUdtRC9NMTRQTkl0YUdoVEhCeU9wY3pLZlVrbU0xY3NCdzYwb3lQVDUwTVdub1JaZlQ5MUFyOHlOQWdCVlQ5U2g3MTJ1MHBYZTUzakpzcENsWUlSMVRrZ3BIZmM2UVM0ZVBpeE0xZ3VseEFkR2J0eVNMOTVjSStyM0I1am5Mb1pnZ1hnWUZjcGJMSzE1dXJ3amZWZzYybWYwem40RWsvTzBYbnNsR3ZZU2k4SGtZYnhEQ0gybG1oejRsK3RxZXlUbVltUUd3QkJiZVZkNjFkeTllTTQvTGpQWGtNTXE4KzNWaUpoMXZqdU11OThyejFtNTdYWmw5SWJMUU83UXBLa3hxTE9CYmw0Q2tHUDREMmkzS015T3J2dWthbHYwZ2tZeW9DODhnUnVRMEZiU2FJUGhaS0tIZTFKbzhRcE43WmkzVVpDOTc5bnF3S0cwMkRwYUtIeXd1a3VaT2VjazdWUGtDVm5rWmplbnc2T3B2a2NLSmJQT01PQlk0aUVLTmt6MGszVWN4QTdLTk5qb2ZPaCt4NXNEaVJYdWpWR0hNaEVobitnNnZ4aFl1TldTRU5MVVB0ZGYxS2dJa0RpbDZWTFFwRmxvckYvdW9mb0ZoS3FTbzE2Qkh6OWRDK2NZSXh2YkNXZitheVRaempoTjcrdXk5YWF6dUZQamNxM0pCVUpwdlE=', NULL, '2025-10-29 01:36:51'),
(858, 215, 'user', 'T+DEOSrq5cCgJpMdo5TjZEhscThraUsydS9iTFRLNnJZeHVuT3c9PQ==', NULL, '2025-10-29 01:37:21'),
(859, 215, 'bot', 'lhaSqoeZl6toW9+JT5CSRzYwYzF3eEJmV3FFUGZRVFlOaW96STk0SFNrc3dvS05iS050RndJVUJkWFZGMGdENnVXbmo2bFJ5ZGZpYUd1eG42Z2JrZmR6NnN4QlRabkk5ekYyWVR2MmNCcVVzL0RiY095aUFUY1RqamlWS2RPbmxPK3BITndRQVB5dE1JU1RRRVdIZForWGtiUENhQXR1eDRBNDF4R2JMbFlBNmx1OXoxb0JxRFcrVU9waTUyS2VDaTJ3KzZjRGhyQThRYWNaVnF1aS9pZTZmYUkxcSszU01pOFdQUVJJZUJYRy9HSm4xSmhnTXd2UkhUWjJFTUFCTm1TTG1iVDgxYmdRZUM5MHZnT0lzQ1lQOHVMTCt4WVFKZEN0WS8zVU9tSk52R083UEJSaXZDQWJNMHFRPQ==', NULL, '2025-10-29 01:37:22'),
(860, 215, 'user', 'r7JasgxQjV9swEhXopb06DBPNndwcUtkNHVBeDU5NEptSm1qWXBtZnQxdXZ4ZnF0VFJKcE9QTVpWR29mVzBuSzZ4c2w2RWthNUpJMWNESE0=', NULL, '2025-10-29 01:37:51'),
(861, 215, 'bot', '/O2tJFhis+gUp3keWosBMGRPanVjZ25scjk0TkQ0Nk0wWnBBZHRKNC9jRGtOVVpWS1VIeVRwZE1wMWlHS2w2b0crZ2hwMlplSStINW5SZ3RSOFFWUHBZQkdoWDBKYnc5TXU3dHg3TUQyZjNtQzlDR0ovd2lXanZiUm54TUZPTzVVWk5XckxvZmFRdk00dXNVWEhYQk4vQmY1NHhxSlhNdDRmUG1QVTNLaTN5ZW83YWdXWGp0RkpzdkFtQWJLT0dnVGZ0c2dXbTVuK1RYcXpUVHpVRHpCRW5VZ1loSklER29XeUdUbnNtUUUrdnNpZG9UZmlHcy9YcWppZER0RSs1L3hhYmNnMVpicDNRbkc1VmprQkdDcHQ5QkcyZUtYbE4vUmlhcnlKbW9uT3BlUDFwbUNNL0VxWktqcm1ROGhRaFhlVmtMN3VzSFVzSDQ5VS9FNnA3QjBMTmoxRGhMMVlPakhiOW1YWlFGYUlXVWQrRkJvQUZ5ZDJlb1ZPU1JiQTVVWkdTVHg1Qk5RakRSejIxVHBLd2drRW9VMEg0dVhLanJpcytPUEp0bkp5L0ttZkh2cjE3MTlFVm1Td2VSWmMrcGNWS05TK3ljMkZ3cE5iYjlMU0k2Um5FeXg3NVk0bEY1b084STZLMCs2cmxuQUFmdVZYZkhuQlRhK2hSbjB2ejlRb2NFNDJKUGZ4NzdhMDJXdUJOa3FhNENFREZmVnVjTlhNVGlTUkFOa2tQY2xuZXp3eTR5M1lodlBrREhBdm8rc3FUN0d3L0JKdFFDRlFHQ21OUTFXbDZ3d3VtU3EwRHZxQ1BrN0NsNnZkTDNvNGVVT29adklsVUZ4WDRJSEtGSEZPK1VxU1E5ZUNtR3owekVwN3R3ekJVcVdWazF0NFVVSUZ1R2NUNmk2NklZLzdhTFU5L2pFTytLcHoxOVRBZVlqTmpCRmlYckl4UWM5SVRqSkVYWWRZVDFOdUQ5eU8rWHF1Q0phdzdDZE9RWFlRZDFlRVdrNmJIMEhqZmhZdlg4VmNrUUwyQUFPQ0txaG41WllZa3AwZzUrWTZwc0xndGp0cTNieEVHaGxKK1liUStaaTVUS2xSd2F4QzdNQ1hpM3VhLzVhRVV1S2h0cW55aHcxbzZoL21nVzAxQUVZVlREdnR4OEx2TzhaWjRvZmJva1ZzRExNdXlGOVhZTWNIbDhYNVNjYXlWM3Q2RmJUSUpLTy9kY3dYK3h3S0t0alNWQUUra29FWlNhSzdHREE5VGNwYWQvUFJLS3UwZ2thNHBaN3FuYWt2aGwvcHhYZkI3VnZXSyt1RUVsTG1PSkRJLzVCbEsrNFZIQWtVMzEwT0tGSU9DaVNQL3hoTGpEZVBCTWRmMjNZdWlqeFNyRnRHZXNwbjhkb3pWL0t6VDArTUVyczg4NmpRZm01eGEweVZyQ05EeFR6OURGREJxM0NpdWk5cG9GbnhOdmFXNUpLdDFrZW55Qi9wdGJzaThLOS84ZGltOWZoNWdvc0pWOTJHNnBwbjNqaFBtN1NBQjVDaFBDdmd2NVZvajJqMTJQelJkc1BsVkFYUmI5alg2ZGszMFo4b01vaGdOR3o3Z3hkaVRmOTh4MU9hVSs1V05xMGNNN3ZZY3dzM1dVNTdRQ3FsSmdGQUwreWtEYUFoVFZZeWNQQmtnbnFiQW9NdW50RldIbW1kdGx6TGtiUm1mSXNrWk4rUkNCTmpmZHJDZlNvNGNZZlRieTdqTGdTaEZjVmZvS002WjVDVE1uOVppd2VPWEE2OGJiL3M3RVV1OWdqempjM2g5bGRYK2hsTGlmd0toUTIydE90a29VNDhRc2gvS0kyRXBsZnBwTTV3ZHJ3QThaV1JMS3U0ZzhTSFgyT0FuaGdnb0JKOS9Eb0FtdHBoQ2VvYk1vbHF6VGJqNVZDNmt2cnBQNm44KzlSNXlYVGlkaW9jRUVvYXVnOTFTcUxrWmhKMWNpa2xpdSt3YVpRdmpzR1g3c0hrNHRubGdVVCsxSXkweFNqaEZ4NUZnVU10eTZYRFVLQ0ZkVnlkZU9oZXFLL2pONHMzbE8vR0RaNGt6bg==', NULL, '2025-10-29 01:37:54'),
(862, 216, 'user', '2qqdYzztxSAXF6m69xZvWnJpL3BPNDF4RXJRSG5xbTVZSk1pOXpVQVdVVWVpcTFNUk0zNytnc0thNWc9', NULL, '2025-10-29 03:21:47'),
(863, 216, 'bot', 'f3rM+apqMOecocyJGnJmjEYxeVc0WlQvZ3RzWFJ3aWJPbU5iNllCSTFFMWo3N1JEYXFUVGZ4eTJuR01oZjdnWC9pMFFsblNRL0dqQUtXY0dSY2Y1WFhwbXhzaTBBNGpzS091TDJROUl3dnRhNkxRRkI4RE1LR3lnT09tZDFSbkRsRGl6NDRkVlZnaGhCMk51QXlER1hCM0t0S1VGQVFQbDJ3ZWRDbFl1TVdwakd1c1RWRXd3dUhIQ3dLRnFIQ2w1WllpL1I1bS9LRmlVV1JPWm5lNlVtbFJDaEFqWmUrbzBxV1Jtd0tFRytlcTBGM2NoNmU3Q2xZZlF5NmpxUjN4NWxlK1h5K2wvV0x5TlR0aTE=', NULL, '2025-10-29 03:21:48'),
(864, 216, 'user', 'NO19kf1JWb5n4ETFcW7aXVpWdGx3UHBRakFxeWo1eFFSZ3RveHc9PQ==', NULL, '2025-10-29 03:21:53'),
(865, 216, 'bot', 'Spg5RsE566KfVManVJa430l2OUhEZVdtdFdmdytXYW10UzVhWXJ2R3RuSzF4VlBKUjRhV0hKZjRTMU9WbUdtaUxsVUZLS2p5OW5RaVdndFVMYW9tc0ZXN1RJT2NwUy9qWGdxTmVPdnM3QXY2aUpNRjVtakd2NlY2T2VoQ0RWZnVSL3NuZW9NWlZwZWdlZzd1SkZHcCtyKzdCV01VaXZKMkNGWlNyUXVDK0FZeSsxTEZ5aFA5TWRNSlMzZUdSeGZWWTJNYVRyRmlqMlJKa3h6QS9RVFRWT2FhaFdaaVg3cEMrK0trWWpHN2JrZm1mUFlHRWFSRVN4ZDNWK0NZaGpuUm1VOUtYQ0V5NExGRmhrRVJWWE0zTEE4N25HY29EVlV2dkduRWJWME1CMWxNNXZqdXh2dlRwWnZ5cnp2c0xDa2xMTHNLVmd0dmk4RWxCRjJ0eFdyaGVCVkZ4ZU8ydm1PSkNsdWUyMHpWSFR4YUFKUGNDd1UrUVB5ZUVQS1R2VnY0QVJRNWJNUTJuRmk1cnpiNG42Q2R0NXlOVGtTY0VoYkV1eGtTVzRUT0EyQzlkOFZTQkdNZUsxMThvZlFiL3MwYnY2YU1uQ0VVbGNnTnZ1Mmx6SXZPbWYxQmorTE5DRW9XSG5QQUVXbnF2VXh3bHhySjYzMmt4M3JjUHZKMWNhUkZaVXg1bEowVWU0dGxRaGpSNmt5Zmtja3NCNS90dEVZVUg2ZVhmZEJMZjBuMTNLN3Y4Um9VZ1RtTHBCbmswalQrUzlCbExBWlJKK1R2M21BTFpmRi9FOE9SbjhzcEpIT1c1V3ppQ0d1SmIxdk1hN2FLTXBsb1JLa0ZDeTk0N1VKa2VzWGNUZno3MmR4d3IzUUpKQUxDRjlsWkxxV3JySENSYjVaU1g4MEErTFhFMks5aTlKOGY0dWRXU0JjRzNlMzEwVFVjb1JkUVBFbitlM3NneFg2VWlJZTdVak1MRWd6QXlxNzJkWTJucUl5Y0NUN25ZWW9lU3VhdGM3V1M0TTZoZjVnd1ViODFtd3k0K2lBTXNDTWlpeGVQRWRVc2t2a3BPK0ZCS05OYjAwdTBMcVptUldTTXlzZ3F4VUJBaTBBWVE0aE9OWXArZ1BwSUhlUnZ5dDZkTzExRXl0R3pnWUNtNGZxNFZ1cEl1MGdnOWg3SXRQRGhmSVM5dmFFZWpYSG8yN0RrZXZ3R3hoRWhwelUrcDFWWGwrVVQ4dEJTWWpQNDQ1NGZneXNma2tESWJxVkUwZUJFTkVJV1d0Q0xrZ2RkRTdGdytGYTZzQy9Lc1dPOUxjZ2UwUlNnQVhYSkp2R0Jlanl2ME0ycUx3LzhJSm52WEhmdGduWEZJN3BOZDJZSGxETjUrT0laellUVmgrSnVjQ2N5d3BGcGQ5MC9vRCsxbnFPMWt3b2JhMTh1dXJSKzNveXltdEkzZHlBaldSWTlKc29RYjAweFRocG9sRWN6ODMrV2JaeVhMaUUwYlFQWm9lOEFCY0JUQ1IvbklOaGIvcG1QQ0VSazNlRm9HQTNlM2x2ZlNrakVqeDNhM0pVQ29wQTFOa3hUMExXOHVaWXZDQ3E4MURPWjJZaVlrL1pubGVnR2V6QnFtTlZrSTcwOWRoRjFQY0VCdllLaitwaXovWlp1UkVDS0E3b1NRTFdFL3FFZ0Z3cU1WNXFOSmUyQWxxR24xYXZOaTRIcW83VmZBRFlJd2R5ZkhJM05RUUh1cVhBT0E0TE9XQy9ZazQwZVRtZnhHZ2x3ZllkeUJxeWg5dnhZK2tqVWdHOWlZVGtTTEV0bHh2ZHNlUjJrak5wK25zOVZqQ2hoUUp6YXJ4d1B2TWJ3bWlSZXJqcEZJRUVDQlJodVVDNGNrT3daV0xzajFRQkF5QTc1c3BnaHR6LzlPRjRrM0JlM05RN1RVWFJ0VFJFQ2pPQ3AwVFFqS3k4TmJNUXBqakRJaWpVcFpsLy9PM3YwdllQMFNtcXBwZm9tWTVMV21ZWmN3eFBremRzUk1OUitQN0htVDFYdEdaaHlZZEp6VVd5NVkvT0hzbGVienVTRFlweWZmZlNQQ3VDY3dqcGRTd3ZpVHdNSSswbWJ3cndOTmkwNlpmWWpkdHRjTVlPbHhXWWMvLzdndWtkUis3SlZVSHMyQ3B4NVl5ZlRLbjVueUhsRk1jMGpqZllRWHVyS3lScytwUFEzUmIvbWNIdEYyMzZBaUVhdkJlZG1VQmpSVmc1U3JsbzFmNzV3VWJrNm9GVzFHN1lOa2FNQm9BWEl1c2ZVV2xoZlpxUW40bUw0UkdLeVF1SHVkZ3JvcmlvbkVwMWo1b2MxS2VkZk5mMVRGbU9mbExwR013UW1OcytRRm9oYmY0OFZRaHFnbDFHMzlxTE4yWHhraU9XRjF5aEhwQlB5YzVKNkQ2bTMwRFE3V1dtNUVJS2hGNGFjdElZRldsUFVHNVp0cEhkSHFtZVpscFZTUHB1SmhpbFpqaUZQald6bnBXKzluMzg3T0c4QjNoUnp4dWpjeTliNlU3TGFTdzZLMU45YU1vUmt5SU1nNWh2STduTG1HSnBIZXFZdWRON0RKN3ZVdlRDdUZTOWVTTUN3cGRjOWhENUdsb05xMUNoTU5Qc01BclhybjdjRkNBNU04MGxLSjloTS8xVzNQRFhLa1hJVE94d01aQVJpMzArNDRSWW5NNlV0S1FqNmVZL1YzMFY2czhmWnEzZTZPODBnbnU0Si9wSkFaNnRSbnZkd1U3YTQ4QklNZEp1ZUd3T0Z2SHh4OE5hT2lWMmxFYWZJdGZIYlRNT3Znd3hpMmM3WFhnUldkUEVUNk9UdDI1YU45d3lmZWIvOFVPSzZLQWE1OExnWGN2cVVJTEJIcVNJNGdMTHRxQk9ZSjZNeXM5eDErMkF5MDlrQzVaSkM0NWhTdHJzREdPcGtKbTlEelVaMFFqSzFSYWJiNzdvZVdOai9YQTFoa2NLYkdyWDNWY05Md2pqQ29NeExSeWdNK2xQTjRZOVZ6OWNpNWNHNDNxSGx1SVhNaTZUd2tycXF3V2ZldUJwNzRHczZQV0h2MUUxQ2xadkorWTB0dUFpNldKalRhbk55ZGhqUjhIUzBlbUNXKzdSVWhrZiszUlh0WnEyYks4eUNoUk9WVk1USzZIWFpzTEdPcldWc05PblBaY1g4c0o3azlZZUpsWjVDWUE0eE4yZHY3bEc4ZGt4Tzk3Z3ArbW00KzVxSnVnSktpQ3pwekNFPQ==', NULL, '2025-10-29 03:21:54'),
(880, 222, 'user', '01tiqqN3um1sDu7qlPYAo2ZxeWpkV1BuellPLzJYbEZKTVNURlE9PQ==', NULL, '2025-11-06 18:12:16'),
(881, 222, 'bot', 'LqMZGzrfpnxbhI2SjVbNHVFodFNaazZ3amxFU0tQRXBtZDJYdDMrU25xMWlkZi9jZTYzTEt4RXhTNG82dndwMjhUMksvUFNuVzhUUDlWZ2dmTGNnczZWUVluSXF0V2lrWjN2ZzBZdGhUT3hTYVFyK1FkQktPUEdrd29xWXpaU3VCZUNLQkFobDhBYmkxOTUwSE5ldnhYMG9mcWJiYk1HVStPUXNYemhpdFpIdGFtOVlvVXdvQmxjTGtzcHJSRjF4N3dvWXBsRCtTTlhLdDEvMg==', NULL, '2025-11-06 18:12:26'),
(882, 222, 'user', '964tE7evHyKe0JuhYDBEc0lmQ1VUMFR2bnZXbC81OE5JcGpvUWc9PQ==', NULL, '2025-11-06 18:12:32'),
(883, 222, 'bot', '2/hnRPc16jRmHdA/sDkjekJGL0NLbnM5Y3AxSUx0aUNsK0xJcE1uWjJrU1J2RnA1bFk2eTdZK0ZIUVV4a1NmR08wbXY0TjA4bTVtOFZnaTc4UmdlNjZwZXJ5djlaUGhnWXVkSWdKQkhIU0hXbmZ3emo5d3IvcDhpakJLaXI5SHRhNE1TZkFRMytyeTVDMXdRRThaVlNvQVA4eUdGMS9sU3NhVWxLbkZXV05pYXNhRkV2NWRhUWNSSGErRWRoSFU5V0JGeno0ZVFDSE9FTnJySlFGNGtVVjh1bXR5a3NWZ1QwdE9wdFBsbUh3NE54QllSaUhnTnAxT3FHL289', NULL, '2025-11-06 18:12:40'),
(884, 222, 'user', '1mz3JjAeAOAOnaLrA3S+Y0dDS0NoUStIUTFNYUdkR0RQc1VsVmc9PQ==', 'Screenshot2023-11-19175206_690ce55a2d298.png', '2025-11-06 18:13:46'),
(885, 222, 'bot', 'kB2x12ZuVKEUWVaRtlyrr0tCSGl3ZmxWbjJBNWVldmhyRW9PNzNMbUh2NHd5WXphYkJsMmxFVE4zdURxdkxyVmtUeUduMk5aWnJGNk9SQzhYclAxMjIycmdQNEpBLzZKc2t5THEvaFAwZmZNbEJVU2lvaUg3L0Nka3I4M1loMzUyMm9EM0h0M2piVm9oeE1PWVFGNFVjWnhXSmJXdVEva3pCZGxWejkycVU1VjBlQ2o0cEl1aExOcHkrYW1xVDdEVzZ2NUIwWjFTdW1BTjhlb3BiUmkwQjdjd0kzbXp1YmJyY2ZUaUE9PQ==', NULL, '2025-11-06 18:15:05'),
(941, 230, 'user', '/CuDxSNtJSCMynfQf2z0q3NIYU5mUTZDRFNLZEZOOHVsZlZaQXd4cU9YSWxud04yMm13R1ZzV05IWjA9', NULL, '2025-11-17 17:01:26'),
(942, 230, 'bot', 'huAw5f4W0rzYnZS5VyWtkHQzREw2RXhMZ0tsSy80cU9JVlh5SlJGbStGZE5LVjBieVUxaVdYK1RwQXhrdTA3MWhFKzlJRCtBV09vWDhBZytZSGpWTkRxdVpHdTg0ZGpVRWRXWWhBNHg4eFhjZnozVjk0Z1piOHpGRmpsUUU1WTcxb3VST1ZaVXZTZ1BNcE9DSWIycGxhVUhFSjVpYUM4MU9qMGpmRnlyOGdKays2UmNIVnk4KzBaOXhjSnRpYUcwdDhNSlJ2bTZ3NU1KWXRSOGRRbnphU0w1eXZYN0NjSDI1S0lDakNoSzVTUHNrMGJIeWZsS3lOYTZFYkRDT0syK215Q1krMUFMZXlRTndCVkI=', NULL, '2025-11-17 17:01:26'),
(943, 230, 'user', 'wXxZjrhk5wdGr1RISryKyEw4NTlhUnNUMm1TN3FwUDd3aEhTdnc9PQ==', NULL, '2025-11-17 17:01:33'),
(944, 230, 'bot', 'U0VtBESWCVLlOv79pvim0TFPVlVvTFYrQ1F1K2cwZ0dUamVvdm1PTXNveFRtYlRldHlNQk43M1RmYkNzV3pLWndTMWlOQWxQcFlMenhFQVVrWnJPZFZGWXRGaU5UTVA2bVpENzhRV0t4ejM5eVBMUG1jcTJibmErWWZOY0ZuWng1US9kVldWVVkzMVdmUlZWYmhHaURrMzYxS1JCSm5IZFB4Ym9sZGcwNStsN1JsUXV6TjVuQ2lSK0Fxcml0YWRQUFN5VDlVSDhJV0FxU01RWXgvNENCSFJab3REK0Jwa3FwVGVFNDN0dGErZmUyOW10b1ZRTU5LYm93WTZWZG0rOEJZbXJud3lPc2E4WGRRNVpudmFKdlZrRmE2em5DdUZhQmg0cFFGdHdhVlFMUUtyN29RYjFDUEpOSUxLcVJVT0V4dGFyUTYxalhSSGRkSDR5UFZoMU0zTjRUTjZLYnNCU2ZQRnJWYVhzcWRBZHNaM09hV3QxMDR5SXRaSGNHNHEvajdsUVI5TUREeVJIMEF1TkQwaDJ3R3lWYXBWY1ptZVhQM2dZdVFXSjhqNXZJSnBoL2N4aDdFYld0ejRNVGFQQU5zMWJkeFpoWVRqOGxtV1A4RXg5eHgxcituUFdOY2hUczNMdnRnTk4va0FjcnBPNmpiZmo5L3BaRU1DQm5qQXNpTmVDVUNTKzdZZlpod3lFQVYvbGVSZjFJMVNSN21sMDBvV3FwVURzYTBpc2diRHpzLzhEV2FnL3dCVmRKd0d3bXJ3TUtnWFRXU3hKMUhCbE5yMnlpK00zdklRS2tZR1BBUGkzZUxrWWJIQkkvTEJxVlB0bEhPQTE2ek1wZDhSdmhVM0dlbW00MURLaXpoWjV6Q2FkUmxQMW9tNVorS1crbmRsNVFtcVVqWE1CZ3lyeGpUazRXNWNPSzI3clIvSDFBQkRQcHB4VUx3RmIrdkNaM055TFZQc2kzYisrcW5lYlZNN0hUZG5mL3ZIOWYzL0VDQ2JnYSswWmRHREM4ZUdxUXJJQ3NCRmpGNElTdk00Zi9tWCtsdm1VWjlNWGhlYkJtUDBLWmtCRGt0RWJFRTVwTVNGYXN1VU1SSnZvcXR3NWIyTmI0Nit2aVpreENCSTFXdTNzRnhrYlVwaWc2QUxuTm9VWUlBPT0=', NULL, '2025-11-17 17:01:38'),
(945, 230, 'user', 'KnMW+DnAkVmpLlpL5u1wC0JyZjFFenZsalc1UWozeE1wRFFkQ2c9PQ==', NULL, '2025-11-17 17:02:19'),
(946, 230, 'bot', 'iZ4CVEs1P7ckPqlb2j4zO0lKc2xIZzFXZlpDbVU4ZlkxTkppazRZUXlGOW4wZ08ybVNWUXpzd2tXOC9lZlVFZDYwejd6TTlwQkdueGFUbmtlN0d3R3FZNXg5ZVpYR3RCeFV0Mk9nQlI0Qm9TYUY1UklqVWQyK0NsaXNudFBJaHJWMkVQQldYN1dOUXVaUGE1OG92TkwxU1Rxb2NodTg3WVZ5L0xCTkVwUmxScWVOQ05kMW8wdHdSZEExOHlhcytydGwwdzJXTVNQUkNOb0RJUHZZekNOczlYUkMxWnBKa3BxVnF0bnM5dURqNTlBeWFEdVdGUGxjc1o4eTUyTGMyK2EzSkhjS085ZktWa2o4alg=', NULL, '2025-11-17 17:02:21'),
(947, 230, 'user', 'f3TTLXVKon1NSMXs2m8/BHQ4Ni9FNHpKOCtJY0NHRlZadjJkSUVaeE9yWDBDRzAycEcwZmxBUzAxWUk9', NULL, '2025-11-17 17:02:42'),
(948, 230, 'bot', 'V1IHxqOSMPe6X+KvIG5h+WJLREpIOTVIVmgxcFQ0UnhCeERaUitTR3dsbDNqNW5rZkt5c1ptZzFSRldDNktWbFE0aHdRMXpTRWlGcWt6ZDEvRWVndU9QNnhaSmpYWXlsNVhybXlFOHJUbGJiWCtJSzBKU0tYN3NBOVk5SFo1SzIxN2dBeDY4RVRnbkpSeG1Qb29pNW5UR1hRTVE3SjNSb3IrK3IxWDg1Z1pTWXBMWE5nUlVtUElCRm1RZ0FGOG95T3lRMHJJTk9jTXY2RThKOEFZUGkvYzlIaXRzL0JCa0NHSnlKL0pXRXp1aFRKK2ZOTDRyWkdhZ00vYXVJRWo4Y1gwZGVEdUkyeWNVb3dLMVg3bkM5a0VnU0Jxajg0REhMTWVqamJRN0tsKzlScUpjakhtcFlNOUp3Q3FXUW9jUzZjb1hHVmxSRDBPUnY1bFZNZk42ZlpKcGJSR0FJd25OZ3JlelpWbnhmMW1NZzhjWVI5UWVycnplbi9ndDVFSFNyV3h3bUlzMXQ1N2MvM1JicDY1VUh2SmpSNjcySEdlR1p5NW9IbjVTM2JSWXdzQ0tnV3ZKT2M1bEw3UXlQWUxVYnlDNkp1WVltc0I0SzZ6czV5bi9LWkE1MStlbUZBb1pCdHRlYUlGbWxBdm80UUQ0R0lwNy9mMkN5RW45aVBCaVd6WGxPby9BeGplaEZNazlBTUp4K1d6WjRNTHh3WkxVNEdIdDdGK01iS1FrZXFPTGJkNERZaGxGZ0Jsb1g1R3E5N3NBOWRuS2NiaDB0dmdOVk1DWW1IUnZid1EyOHlBd0d0SmFRMWdhN0IzRHdQaTg3cEVOY1pNOGdXNm42S2lFbW1Ia2lCeWd4bDdWNTlFWXVnSUdUK2hiSlZzOEsyNFZMWWdjZEFZQnRYY1lyUGhaRmdYZWNrWW9jRXF3OTJLczk2R2l0ajhsY2Z1OGtIN2xvbDNFdlF2ckxHZjlXaC95ejJFbGtKSDFMa0ROUm5sSk0xWTJpZ2QzaUtleTNFWDZyc255cWV5Mm9wb05aV0pkRDQxRDI3dHRQNHJLWjc1bVpBRDZHSFV5eEFrT2V2MitIaE8xcGRWTUJuUVNnYkZreHZHWTVvSU55REJBN0s0U1ZxL1ZwU2JjQncyc1JIa20wYnFHTU5GUFZ3MUhCSS9EaTJkb3hobWVwamxQNy92eEwyOFJibHpvcGp6Z21tYmNQcFNJTVJkS3FONFUranhOZVNsU0JUTlE2UzhVVDV6R0Z0eFhVeUx4bEM4L0pZYlJvKzRIQXVNaHdxRmZjNmZvTE5ndk9pdEtlck1NUGo0cW1iZXo1YlQzVzhIUmE3aWp2Zkg4TWZSb2Jqb2lTZ0t0RXJwOVkvVnR2K2ZFbmlTRUk3V1NONHp2aG16NTZCTVdKZnUycHNsVXVtcExSWjIzRGlCTWxqZ0NpbjQ5eGpGYjY5R2ZOMmxEOGdDbmFFRFJUc3cxUVZEY29hZTR0M0E1andKQWNoVGtGdTJ0VC9XUmEzcllGUlBsaWMxZlAyNWp2dzBOT2pXcnN0d1ZPK29vNk1ZalBzM1o5YU1tUjZWVDVpQldRNzlWemMydm8xdGhpakw5c0tMbTZheWNCNE8xdTM2ZjgzQ2lBMERZSHpLTkUyNTEwQ21ka0tUcWNJb3BjY2ZYRnZHdGJxT211U05zQ1ZBSDdna3JKMVovTDJCL210SVplUm9tV2tXVVhXcGM3cW5WemlZQUdXTS9BL2Q0YmM2ejBueFZGRWJVVFIyb3Q1djBtREdFYXdDSTN1WUFqbTQ2RGc3QmRVdGdNOVNGZzZzQ0dkd2tha1pnVlRGWkFpS0oxcFdxM04xOU9yWkxibEpTSDFmZXJFTU94WXpuVHhxZTRHODQyYjFHVU5BWUN5czVmZDdVWWRXVXp4NVFQbFJlNnJVeERtY3ZLeE1VejNFRzZudjhMRVFCWTRhQUROWk5GY0VVdVNtZ2FUZnc0WUZGRDZXQXBhSnBTZlptSlczb24yM3ZWZWI0cDRqNDhxemM5MlRQZWxDaGNwRmx6c0lXMVdyRGZKSUhTYjlmb3NFb1FrRVBCSVAwRTFQWFowc3hkWG9CVC8xZW44V1B4cXd6V0lJUllubTBtNERKUWlza2l2eDNhcFRNRVo3dy9oV3V6cmRzNi93eThEdU56WDNYQWw5eUxXYVZzY1NwbU9JbVlHL1UwaFVPM003RmlYUGh5QW1CUE84RmhURWZWUU9HM3hCRXlwZ003eDVFKzZtaFc2M3pwQ2Y4aUY3NU1UUWplR2daMWxYaENZWkZpZngzVXAxTnBZcjhPc1pYYXdRRHB3N1RZMTdqdEt3RlNOS29ld09yd0RPanN4WVErU3NkNTVoeXJWZUVyelVGY3M2b3N2MTA1dkNqdUlJVWs=', NULL, '2025-11-17 17:02:47'),
(949, 231, 'user', 'NrlUHLXh+UX7Ezcz5Gl5UlZWUVl3Z24wMEpZQ3pIV2tWQjAvZVVDQ0N2TnZXQis3R2gzd3Jlb2VZNGc9', NULL, '2025-11-18 06:45:27'),
(950, 231, 'bot', '2piXtnuDTEdmpWa2WutwrnIzUm5zODBKemJ5V1BhdEEyREhUWEJIY1krN1hvaGJIM21PZ1FYNitCcGxnTzhIMFVpb2hIaldUaVpsOU04ZUtlMG02eDdFdm9BVWRpN3Y4VEJ6U3NoMDhKYVFtKzhNQXF4cnVVV2xiTWtBeGNBQ2RtV3Vzb1JPNFNJaFMyQSsvNnd1L0JLMWRHMjJuajRMZjA1UFRYaHhkMHp5SjRjNjVSQzhOVUpSNGFWNHE4MWdiRU8wc3VGVVNmWXBTcTdHVWhST1dzZklXUUc1bzY0TDBVeXJsaFhUQlZHUis4Nk0zM1hQdFQvTlJJTERZOGdDeFQydHdGWG80NUdXYnNKMk0=', NULL, '2025-11-18 06:45:28'),
(951, 232, 'user', 'JNvoh3uLHCXfNINhmt9O8TZSNU1pNHUxRkxOc0cydDQ4MEhiTHgvNU9MelJwdDNOUUxRazFQV2daWUU9', NULL, '2025-11-18 07:02:10');
INSERT INTO `chatbot_messages` (`id`, `chat_id`, `role`, `message`, `attachment`, `created_at`) VALUES
(952, 232, 'bot', 'biNTp1zABAlMOuhFUkt/Y2pJV2xTZXoySG1MZWdMSU92dGFvZUsxYkIzeE9VdkNLTXkrRG4rUEN0OUpVR1BvUC81YjUvaFhjMTkxRmJvR1FrWllremJ2eU5pZDBSYjZsR2pZQ1dJS0V1eVhvaWowSUJNUnJ5QlMrQlJkNEF5cloxNHFhZUlLQ2EvMVl4RXNwYVNYcGdDTGdOWVFwWEJQdFJubW15OThvMnU0ZjZjZ25sWFc4ZjdBUUpobGI5Q1dxNngrWWpjU1hRUEJwYkJIY0tLRlNZc2Z1bEJzTDIzLzZmUHQ4VXdzazdZZFQxS2tUYWZPMWFUYVFScklHeTlXS0tMQ1M5UUNTWFI1OFVGZUM=', NULL, '2025-11-18 07:02:13'),
(953, 232, 'user', 'yjp7pcxt5ldjpZc7q7RSZHY5d2Y4MUE3OU14TVVmVmdYZ2d5R0RKYStjZ3lmNjZRVFF0aXZFQk1ub2FnckUrSy9hNWNDclBTcXM5UTVYU28=', NULL, '2025-11-18 07:02:16'),
(954, 232, 'bot', 'Z77HtY1c2zV54IYiM6dCjkpsK0ROVTlZbkh1Uk9JeU56bmdnRkhkUXV5TG0rNTRRQ0JzYW5vWm0zTWd5ZUg4UUcwaXN3U1EyVlF5d0hra0xGd1lQaVZZN1dzR1pUUjBwaDVOVGtvOEl1ZEVjWlk1dW9haCtFSVN3UFNTMHM5UGNhcEtEclFHbzBWK094NU01ZFM3ZjhFQkRyMXdqTTR1bzhhNDNtY3czRDRGRHBYNkFTWmtVU2NmRWhYTT0=', NULL, '2025-11-18 07:02:18'),
(955, 233, 'user', 'iEWh/kBPrm8Uncc9SZvYiGM4dUIrNXNTU2xFbklaMzFHVDN1ekE9PQ==', NULL, '2025-11-18 07:02:34'),
(956, 233, 'bot', 'omcxOf+p9RCYsnlUKm+lLmNFVzNVTE9wRVdmTUlXSEM5anVDRVE9PQ==', NULL, '2025-11-18 07:02:35'),
(957, 233, 'user', '4dkE+orAvnRENBKRqrN+jFlFY1JkM05sVkNhR2hqYll0K21oZXZncE5qNHBxVVdmdTdnazVJQ3phVmc9', NULL, '2025-11-18 07:02:39'),
(958, 233, 'bot', 'cxP3czcZMMbq+rs1sP+fYmFmVi9jQjBLUDE3RUg3R3c2R201MWtiQU02YmI3WG00WlNqYWZ2MHpYdzYzbUwxM3ZtSm1OZTB0aEdUMjJCNEFwR1BLeVBrSUdmd3htSktuM0RoNCtqUk92SEhvVHhDYkpyQWtZWjByL2NsRnprSndoK2c5U1pQV1ZTa0c0eE1oODFxR2R5WVJVRStRTkUwRTVCWk11TS81dzByUWk3Q3dPQ0RMaU5qbzdOekJza1RFWTZWV213akFLYThQdUFROEN6OXd6NEdYeEFaREdGc2VZUkpGM2lmbW00ZFYwcjFqZ3kxOGhzbDdBMEtJeDBWSnpXL3l3U1lJaFZ0dytkOFFxK3pIM3U5RVltbkRHUjFhZUhpU2x3PT0=', NULL, '2025-11-18 07:02:41'),
(959, 234, 'user', 'BPo2J+MueXfoMQfIUNxp605sWTlYR2JOL0x5ZStGc051dzFOaGpUNVg1S3hQNyt4VndNcGdvNmJmc3c9', NULL, '2025-11-18 07:02:58'),
(960, 234, 'bot', 'J9qNcrFuR0uUuKQflNth3TFZNFNXSmMxc2ZrZmxKdDhEWUpCSHZocDdNV3R4bGVBSC9YMXQ2V0Uyb2RKS3FkL1hLak1ZeDlvbUdNTkhtR09RbkcwRVpCMTlVZnFZR1dOTGN3SUM5Q01iK3hOWUhNdkMyVkEzc1MxcXYzMDlPRGpVaDlnK05mOUVCbHN4THVUZjh6ZHFNQTBSV0FFR01iRUY1c1FQVG9wbzVKZE1LeU03dmdMekZiVUt1RFN1di9lNEFYek5sbTl3UmlOV012cUpuZ2szalNjVStYYjlrMkZodTdrYkc1ZzIydWZPN09DR0lkTkVRTXlOTEowSXFSR1lWVEdGdkpqNkdUdnBmUHA=', NULL, '2025-11-18 07:02:59'),
(961, 235, 'user', 'R/Mxe39JAdqNkTtmv0gOCjlsRURrYVdJV0g0cjNFYjU4dHVLYjNNU0RnVVYySDJET1lvM25GRnFVNG89', NULL, '2025-12-17 02:16:37'),
(962, 236, 'user', 'EV7bSLkG8dVZU3UQg3UZ80dXRzJrMDRXOWdqVTdHYUs5Q3R5cVE9PQ==', NULL, '2025-12-17 02:16:58'),
(970, 240, 'user', 'Q8D/uNUCHkfDAUvbgheXilVhN1IzODYreUhpanlaVk9acDNXd2c9PQ==', NULL, '2025-12-17 10:15:35'),
(971, 240, 'user', 'wdqBcLmwPp6IRzAAlxCMmUh6NXJUcFU3TFJZWERDYVdCSmVwNmc9PQ==', NULL, '2025-12-17 10:15:47'),
(972, 241, 'user', 'ELGhsPujKO3f9pbP35YTtmh3TStRNlpSbFhYU3k2YzZ3dnZsbWc9PQ==', NULL, '2025-12-17 10:15:55'),
(973, 242, 'user', 'bcnH4lKe6V6bg524jaQQ/0ptVjQzTHBwQ3pxVjJFK0QxbS8ybUE9PQ==', NULL, '2025-12-17 10:26:14'),
(974, 242, 'user', 'S4iBPj7Lst92dLvgG0pGo0V3L0lNc0ZJcVdJWmlRanAySEV2dlE9PQ==', NULL, '2025-12-17 10:29:23'),
(975, 243, 'user', 'sbAGe/vlDBzqturd3AVy22lYS3hkWUtyeExBdTJRS0t2OUVzcEE9PQ==', NULL, '2025-12-17 10:32:31'),
(976, 244, 'user', 'uGiyNBP+aEpYyj/tIHFHtnU0TnNWOTkvblgwU2VMVUFTcWxVSXc9PQ==', NULL, '2025-12-17 10:34:01'),
(977, 245, 'user', 'vKmvC8a1uRgZUJNR7BMhQXEzSWU2Y3VpTnBUSko3OUF0MG84V2c9PQ==', NULL, '2025-12-17 10:39:06'),
(978, 246, 'user', '132ObSR+kDWED22RRC4gmlpTSjJVeWlrT3QrT0JiNjhiZmRmL3c9PQ==', NULL, '2025-12-17 10:40:18'),
(979, 247, 'user', 'tBq8v1F/0vcn+xAPmY9yCE52QW1vSTFGYmJyVzJKTm1POHdvRFE9PQ==', NULL, '2025-12-20 09:26:00'),
(980, 247, 'bot', 'ozlIuNUdE9MNT6o1RRPZ3zBsVGk2Ym11Z3dlUlFYOEc2amJpVnc9PQ==', NULL, '2025-12-20 09:26:00'),
(981, 247, 'user', 'k3exiYZFsLYTuil+oU4UnWQwV2JqcVVPc3J6QTRua3JpWHY3ZXpJbU5ENjVTWjcxM1FtUFNSeGdLZXFOTGZ2Ym1zckpwUk4zSUF0bXJCUmk5UU1TWmMwVkVqSElyYm1OTzZ4NlRBPT0=', NULL, '2025-12-20 09:26:02'),
(982, 247, 'bot', 'y3iT5gQhrRKgu+Dx+bvRyE04UGFXb2s0ZlVPQzdsUVc2UU16d0hidjlXb0N3dW1tdXcvYjdTKzdtd1lCUG9oVXVyMC9kcTFJRHZUNEszYjArb0FXODBTcm1iZWtkQ2F5STFwTjdsdTF5TFZlRUJaVURQQnFWZWVocE1CWGZBOTkvU1YzcFRxanZDM3JyOVJkWTBtSHpybC80UW56U0Z4VERySmdMN0RaK1JWZlpRMzM0Nk42MnhHUUNIVWZwMWpMdUkzcVhLOFN6TmJXWXFjVWJ5UEVYNEkwdHc4ME9lWFU0blhGZzkzamZneUs4ekhsck83amlCMWRsaUgwUG4zdHRFaXZrZ1JmZkdINVFZVis1ZW5MeGpXNlpkakZIbWs0dzJ0MlFJMVNET3Z0NmxWYkJYTHRzRmlDZEh0YStNOGlZZHV6cnFtdmhZanU4VDNxbmp3YnAwc0lwQUJLemtLOXpLckFybDlLbzdWSnZZeGFyRjBuaDNzME8xMUt2cEtWb0Fhd2pKREYrc3JrWUVrc0hUZXM0aFdCWHM4MUd5dkl5bGs1TnZVY3FEbDYzdjl5Z0RFRHFod0tUMU09', NULL, '2025-12-20 09:26:04'),
(983, 247, 'user', 'J7umx2PZdcsMdGKqhqaqH2pEQWdRWERrRFd1aEx0bjl0M0JNR1E9PQ==', NULL, '2025-12-20 09:26:08'),
(984, 248, 'user', '1LJ5AVMNjYwqoMCjzcxKjU1CZHFYMHF3U0ZRemZCNWxwZlZEclE9PQ==', NULL, '2025-12-20 09:37:17'),
(985, 249, 'user', '8SzQNL6o+cBOUFombHS201dvUnNsTjdkQ0kvNW8xT09XaEl3Ync9PQ==', NULL, '2025-12-20 09:43:11'),
(986, 250, 'user', 'ytgttTYmIc5tAgVLFN9Ylll4aEhEeDF4UCtzZERmcjg1elpEd0xHU0NnL2JnV3pFTTVLNVJaV2V3cjQ9', NULL, '2025-12-20 09:50:44'),
(987, 250, 'bot', 'C+euu+F1b+FClCpYCUHzXVJFQnJyWXF0ODdlcHo4c25NUTROcllpZHlZYzkvelZQZGp2MmNZVTc4czdWYmsyUVBrbFVjUGxLd3lsUzhNUTk5YWFEK1BCRzJDR0ZKRWJFVTJJZE1LbUJlLzlJS1JoSS9ycXV3MUVpc2hNR0V3dW0zVnFDWXhhWnhRMFhMb1QrMTl3YjYvakNiZWN0U0o4NnpBMlgrUkdtQStuNFM0dVhGSzhwZC9QZDhPTTRrbzY4ZzUzTi9kMWc5Qzh2b1MzUW1UaDJvM0NkZzd0aHFQVm42a0Qva05lWW9LNzZwTndndXdUc2k5Ui9nc3pTTXhRdVJvM2tsTnh2VEk3RXl0NFg=', NULL, '2025-12-20 09:50:45'),
(988, 251, 'user', '1uCVMB5/1ellKSTy43WayllyNm82a2dYSyttU0ZKOENiWTZvR1E9PQ==', NULL, '2025-12-20 09:51:26'),
(989, 251, 'bot', 'jem4yvQMW9tgfA2vPOZQOWd5amxZUkJDVHhTT3ZSeUxMSlAzanFLNE5tdVg1S2pNd1R0a3ptUkRzWE1BRkVqYStMdkt5Ry82ZnIvdTJZWkdmRXRZRzB5LzZaWG5sNUhnV0lTWGtEZ1pSdDBubS9nZnViOENiRm1BN21oemhNZXNSQnBjblc0RXhpT1NUQzR2', NULL, '2025-12-20 09:51:28'),
(990, 251, 'user', 'Rb9Q2TpbzCb0J3cBJNTkfnVBWmlIOWkvZ1ZqeWJlV0lxdHBDWGc9PQ==', NULL, '2025-12-20 09:51:45'),
(991, 251, 'bot', 'RwHwVtws9ZsI+NSIRKjmzGdvWlFpVTkxakhyWSt3N3lLTFVSNHBvMW9udmltMFZxVWRmdm1xKzUwSC9jcmtxU0t3bWF1VytvSHBCQWhBdUtSaDNvL2MvY2FZRWo3MnFCNDhVU2wyQmR0M0hqMUpKWk1ZZmZRd1daaVlZZllFcTNEdmYwajVkaWdHRmZPNzJTVk9DYXpDZzVoNm1Uc0tVNmVoaXlTdEFsM0lLL2FvR1g3U2tJN0JETzFXclZKTjRnMlA5ZjJWSEo5Q3d2ZkVwMUVTaldOb1Y4ZjVlSFE1Y0RZa1hrSEE9PQ==', NULL, '2025-12-20 09:51:45'),
(998, 253, 'user', 'slzCmR5JIQm9UZy5hCscUGN6a3hQZnowNmlObzA3NEtQRmkyS0E9PQ==', NULL, '2025-12-20 09:54:09'),
(999, 253, 'bot', 'jcYgQLb3gZ2pbprDiYF+NEpvSWhlbFBhUVBKeEVFVFhKY1Baa2c1NHYrZCtIU0VYTnh5Z0pRem90c2dhL3dKeDF3eEwwaXJzeWlGTG8vaUg2U2lPbWEwNFNvYTd2N2lnc1M2VWVCQ1UyYVpOTnJNV05UVm9EVEdUZmJFdXFOMTNOU1E2VTRSNFNhaFRDVkt4SVM0b01EWmtaelZ1KzNUTWV2T2xhTGY3NHNjTFcyR0plSGM1L01Sam1ZST0=', NULL, '2025-12-20 09:54:11'),
(1000, 253, 'user', '8cTCoJGu0ooSAgCNzeidwHFWVmV5YnFHWjVLUFp5ZlpBaFFPc0RIQUR6S0NGRmJ6OXJkSXdpWVkvVUE9', NULL, '2025-12-20 09:54:14'),
(1001, 253, 'bot', '4E3tnakP+RN21N1qYUAwBC9GcVlEUVg2Mm1MVUwyN1NIbDBYckhiK1hSS2NpbjRUb0JzcEFLbUdFU1BnNUN5a05oNGxWWW0vK0p6YWpNSlBZVm9RaDFkL2pRdUtWT0VhOUJYRVlXQTNuMml6TVppOE5PaGNNdEFvOEVpWkdGTklHNDNvNm9CODlRWU9oZGtxZTVLc1kxRVB1bmxERDdLQThFdGtEWWE4UFhyd3hxUzV4eldHZnRKS3V5SjFPRXVkYzNDR1hFY3RvRjlQOHUyZg==', NULL, '2025-12-20 09:54:16'),
(1002, 253, 'user', '0VWPdiWnkp9uX567ducJGm1KOUJJcU1PSkZ0YkVvVGZKNzA1VWc9PQ==', NULL, '2025-12-20 09:54:24'),
(1003, 253, 'bot', 'clCa2B5DCdit1pZP3qydu3dVenJNelZyZjBaU1JzQVFPTHdKVDJFZEtwTTdlUDRyMEpXQVphYXlmR20vcXc3WVNzcVp1NEZ5VTQ0blcyKzhVTm9ESnoxYTdmOW42SmlJNXNpVEdUVXR5UmFwREFNaGJDTXVleUl4dWN5R1Z1MENzM2Q0ajNYRUR2QlVhVnBzWFBXeng5OTArc0JEamorVzBnZ3JlZEI3QU5JMjVzY3NxZ1lxOThZQlRPZDc0Snd2TFlvT0QyUjJhSUg2QXBMak9pVXUzSEk4SFg3aEhZNUt6WFVESHNiaG54d3BjbU5JZUJXbzc5cHRiUDI4bG1hVE9XZ1M4WnVSRmhMWHFDR0tucGdUZEVJTFVISEdZdUt6Y1JsbFlndW9heFgwUWJ4NmRnNkp2K1hXbkpXRys2UDBIb0ZObFJuM2V1M0tDTzJSYlZXSm56dGFUVVczd3llazRQcEVBT0c3TE1pOG1sMTdQTFVRNnlubExzQWJTK3ZsRVZEVjZuQnovZ1EzSHBMOG5rU0J1Z01LNWlUTUlqWkJzVzR6QUVkK2dKVHd5aURnUm9TMDZJbktkdnJXRlhiWkNZNjlVZjAyMjQ0STMvRGZQKzVwV0gzT3ZEbDArUGl2eUZQQUZJMVlva0ROblpRNTgvQXBReDFLQTBDeDU3enJFaW1jRlllZE1lN09LVmQzNHdEd2p2U1RpUVpNV3hIN1p5TEQ3UGZqRzl2eFZ3K1l1ZUpVMWphWC9zTHRiVXcrNVY2TkxZdlNrcU9tVkhLTTMzRFI2QVlsYTBVSWQxcDNwZmtXRXdmaWNRRFphL09SZFExcG44ZkplMmZxOFZRcXZNcDBsZkJOOVFjNmxwUW1GWVRUbG1HR0cxZ2IvUkQ2RTVDVFdxZENKTG5DcUcwN1BXV2M4REtkbHp3S3k4QVdyL2drYnBNdzRiS3JHOTR4OFU1a3l5N1FQTGZEUitVS1BLZTRmbjl4NUg3QTFJOFVUU1JGMjhXbTd6N0pXbWV6eEJFMVhrbEJ6WnhGQUhWVkpuQkdOWnVORlVYdkhXOXQrcStrU3d3cXl6bytONlV1T0lLdzBNdzZhZ1ZscnNYZjdLWjBQcmNVNTB6bHJUWWRXZWpRd1RYUWdrTi9OSnVEYkQ5b3N3QnpnTTVudlUwU2Q0Vk55bDNMbVFxcVdCbHlTY09nWDZuNGhQWWVWTFhSbGFIRVhqT3Eya3RYWkhZM2JMSFRCZ0x4RlBtTHQvcnVWaWRveDdRNDViVDFrU1dFajNDcEV5eW9LK253Z210bnl3aGpKdThPQkhhbTh3azl0ZGJqaDRobWM2b1JGdG1QZkpDT1ZVclNzbzZhb2FrTmR1NDBjWmR3OENVSTJKTkRxTWEweWRIcjh0TjE1WHJ4b1FqK0pTL3AzQ3R4TWZBcEUxRnJkUnB4MEY3NVpSQ3JvREl1UG8xRmQ4NGdFZGJTRUJ0MXpTWWEzQVc2N1VTL3ArZi9hTEs5Z3NlZTZNS2wxK0I3MjI5YzcrRjZJUTlpRUliRjhMSXZkdGtiek5TQ0ZjcTVqWVNVaUFSRjdJWEJjLzM2RnVOb2ljY1RlQXRFRFVaRU8zQ3U5M2JyR1NlQ2syTzZyRFoxczVnZ0VlY0FNOXpDNy9SWVB3eWpSYjBWMDFIeXhmWVBpVDhiQVpVV2xHcnRHdXpFZnh1QitIUEJPamQwR1c4TUpTNmNkOC9rOHhEZllyZ1hBSG5ldFlKUkowM244ei8vQ3hra2FqVkEzeXFxLyt2Vy9iOC9Mam10WUE3ekxwQjZFWVN0WHdxNGdDb0doeXAwclM4YkQvZEZLZ0p6WEpWcU5KQy9ZRi9qbDJTVEl5WUdyazg9', NULL, '2025-12-20 09:54:28'),
(1004, 253, 'user', 'Q63Bk6voF8vcchG0aqvRdGk5c3lHejdOUlNWU21lRUhtUkRCNUJRNnA4S2RNU0NUa0V4UW4rMTNPNTA9', NULL, '2025-12-20 09:54:39'),
(1005, 253, 'bot', 'kfAeZcGWYmyWPTcgzrn2lXRyK2VKTjZsUTZWSHA2RVJQRTl1bFlNK1FBVDAyR0ZUSjVXZVpScjErRjRObmlDSUVGd05BUU51akNTdVBXbnRXdzJIeGFIbnZZUncrNkhsL3ljck5Ib3M2b05KS25yRngvNUUyUDBaamVhSFlOdk5HZjdCL2g1UmRPNUtseE43SVJsSWpSMW5XZ2Y3bmZsN2x5Skc4UVhPKzdYbG5RSmJEdlRuaG5VYkFnYnc0VjZsR1haaVJFUGl2Wkw5bFRSdg==', NULL, '2025-12-20 09:54:41'),
(1006, 253, 'user', 'iLddoKrGIEhTjtVFee+r1CtvMWNqUmVRQ3BjN0hYMjBmTm5YV0QxV0Rqb3p0R0FNL1JVTzE1eWQyVTA9', NULL, '2025-12-20 09:54:43'),
(1007, 253, 'bot', 'hS9mrCAHbtIXiRXNf5vQGDFXVzlzN2VwbmZiOHNQNVhUZ1lwbm9yRE5PRWF4Y2ErSDd3eHcvRG56Rm9ONkNQdG1nQXUyK3g4djd5Z2NXYW03OTdPSEJNS1BvSDhGaEFwRHJidXF0RUdidlhzWTE5SFJHY1RzK25qL2FlWEhGN3puSGt4NGxsRDUxdVM5K3ZSd3dsTmdFMTVNQ0kzUnNuejcxdHV2K0hCZXAwVzlhMnhWRmczdHp6enQvc0lyWVJyYXNYc1U4YU83ZjFxRWJIQ091b3M0SmZqNmpNVndrQXl5di9Tcjg0S3N2L3RFM2EvYnR0ZzF6Ymh3ZnZyRmNhRDBqeG1SRlRlRnoweUVHK05EVmU2YmFJN2NmSk9DUXNCWkRDS3BnPT0=', NULL, '2025-12-20 09:54:45'),
(1008, 254, 'user', 'Up0WPeNfmwKjMp7pKJhzmW9XTEc1cDE0VW5HSW1lVUlLY0xjZTQ3OEhZQnlEWFVOaS9aVEV2T2xDOVE9', NULL, '2025-12-21 01:29:37'),
(1009, 254, 'bot', '2jcLmkvQOyyzgkGZbG6t8zUwcys3ZnhKdS94c2tkZCtRSEJPLzNOYTE4VEhGVDRqUHozek5tbERwU1ViZnVpdWZXdCtOTE1LTXNtQjdJN01BZHZwbzQ5bWdlRE1iQ01qYlRPdXJveXlnd0M4QjM5SlFwUW5SdmlsRGpZb2ltNytkL2Z3eGt1UmhQM1F1Z1dpWEhqbXdvcWtFcTRrbE1YN0JkdGh0b2FGYWF0RlNja1VtRHRNOW5tcWJzeVRJL2tVTUE1Q21yV3RFQW1TM2lYNA==', NULL, '2025-12-21 01:29:39'),
(1010, 254, 'user', 'XCx3+naxzUfB3h/mNEfeXEVwa3VVL1NuQlV0S28wdFRldkdGWWc9PQ==', NULL, '2025-12-21 01:29:45'),
(1011, 254, 'bot', 'Ooka9/v7X5AjcNsonCIUSEpoWXpmUlFmdUNqWlE3dlFrSS9wYU1pcDlDODJQUysxdi9YMVovV2x4OERkanR5UmhJdHVzQm90UUtrbkIwOERyUW5KQm1PckttSkRhVzI1LzdrbWJzSWszOTBTMjRhbk1mRERUeVRWc0VBaUtOSHNQTmtFVUFCVm1SaTVtQkN1QTNXa0M2SjZFS1htYUtEdVYxWXQ4MkxOQ0FkZWN1SXNBZzZlTnNWT29MUVZjMERnTXZXUzhuVE9NSS9VZmtlMHJKK3hSVi85M21SQXZjbmZ1QitrZE5GYmxuUVV4ZmV4QU9GeEx5dkpwWk9td3pReXBlR2xTb1ozbXBlQVhkMkZXc2pua3E4aU40VGIzVHNOakI3VjBTc1ZhNWtrbkR3cFFUc3ZSMkh4R0VKVmd4VHMxNXJScjFXMFY0dkRMeit5dUsvMDR5cG8yN2oyVWVRZlhNUVBrUVZEVTg2d1p1a1h5bXFOeW1uNjZCUzZ1Y1g4WGNob2JlZmV1QXFHTk41dUpPem5SakMwSTBuKzlERXFaWms2R3o0eVZ1NjhvKzB0ay83bTVDSU54d0J2cU5MamQvQ25rYXpadTE5anhjaUtyb29Ib256VEtmajdJTWg4dEl4VFlIWDdCNE9lc3dWcjRSbmtscDBpUlZKUm9Lb1IrVk4zcFJhVU1nSlgwZ2VDRjlEKytQcmxvU3N5bFhVdStiUk1hWURVT3l5T0dhUVVqRW83b1MvNU9pcCtDbW85UFdtQ1FRR0crZS9yUDVtejNLTlVHR0NOZVRVSkZ5Z0NqWUlxOFhFdTdZYWxIaU5JMjkvR1dnTW44STM0Qkw0SnpYYUtIRE9udnhJT0JtTWY3Ynpmbkh4eEZPWVB5Y2NSNTg0TCtXTW5Uc3Z1Ukd4Qi9QQmtpeWFKSXNLeG1lQnZUOEdLNTZTenBNZFdlZENhb2FzNHFZekpHSW9XL1NZQ2J5Lys5NkY2Z2FXR05QN0VEK2tIVkR1R3dnN3hsSlUxZ1plK3g4aU5QU0FzY2tNVWhEY00zOE8vVllJRndvOW5UTDJMUmdUdERESzl5ZDNvTVJ0NG00azUxQ0lSVUxQNnhQcXpDTXRUVThDVkErY2s1TlhqblAyaUhNMjd3Zm42MFFOdDR3WEIyZ3BnUXA3MlhIamxBNWVUWDNKb3FRSUlVNDRZd3piS0RTRzlOM1NHc2JscExHVnZnZndRTm0xR1lZbUNJeWVaUmhrMFV5QjIrajlDR2pHM1o5MytzbDFheGxqYU5QeTgxQzBGSnhOU3RsbjZQYzZxYU1McEhFTnN1cGJyZDRBVUdLUk5XUDdYdzgzZitDZDM3d21CRWdJZlV6alBVTDc0eG1sMVdBQURwNzZVd1hpNXN0TkpXbGE1UEI0TGVGNVIwZFFuV1BhbitvS2lzL3pxOVJFMDE2WHZoRnl3QU8xK1I2TjR3SnNZQXRjSlZ6QktWcWRGaEhnakZpTjVZeGtmSlJlYk5jWmttRi9OdHIwaDNsRFc0Y0wzVWZZTEtHalVRMUJZbjZSQ05QQ01LQUhDMjBOTXl4VnR5RkdFOGRGRTYxZHYwazQvOVhacU9wVUI3NldFa01QcXRsM3pRUHZxZFBOZWJKa1F5TEhSa2E3QW9BKzM1ZHdjOHFUT2t6NjBzS3dtVU5hamlYTm9OZ1Zmck5POTB6dDhxbTYydkI3NHdVUGsvUk9vaG1qVFhUUWE5MnBldlh4c2xZcU0rYUdtUEJ4ZTFvZnRpNTFTZlRicFpJOTNoWXV2QmdjRjR3bjROcmM2U0VxZHUyc3M5WE9ycGtJYUtTSEpUOFA0c2VNYTEzZFRaWlNOVDEwejBsRXlUZGhGS2Rtd285UkpTcnU5cmFaRkwwd0NBQy94Uk81bVkxMjFuV2FjK1VyOVkwdTUwOUhQSXQvYjk5OWZicnZjMVNWcDZCZE5rUDdZRjhlVFpTU2hOR2NuZjNjSVNPYXFSRXBTLzdtM2xuaFFlTTlzRG9qa2JUL2J2YnZvbjdBUFlrRGVjVkhoZGQ4UkVYOWdYLzl5amF3U1hyd0hBWm4wYjNBM3VZMmMveWJaUWpzV0FZOE5UTlFnMXQzOWpKWmVhUFJDVndENVBCdTlYbjlHbHExa250N2M1TGl0SXIyQjh5eEc1c2p2RHUvQ0lLMDRDR2I2N2ZqRHhiYU1LRk9oVi9VPQ==', NULL, '2025-12-21 01:29:50'),
(1012, 254, 'user', 'cxj1MhflWXRjylgr2WlD6FBpZmprSmR4OUowS3ZNUGxZb3dMOVE9PQ==', NULL, '2025-12-21 01:29:57'),
(1013, 254, 'bot', 'VCxSmiEzzXKc2skTN04dljBNeU1sK0hxbmlDNTlyRjh4cTdxbXgwZjBhcHRjMnVwYzZZTmI4elpCeVRYMHZsMzc3KzhmV0FqdjIzNHlLRnNvbjNmWEU5dDUvRVNxK0RrbWZmS3g0bmQzb1ZpZGp3ckFhTXNHNlNFanBEVEJkOHllOUhhdXg5MmFXZnJmcFB4czV2Q3NLMFNRQ0poTEhYZjh3ZmJQTHZjT2hkdnF5dlQzKzJLT3JjUG1OVG8wdENZWnJoYlNTdUJ3aW0rT3NVbjBxZjQ0Zy9sZkVuUjNxaVoxeW1pVWc9PQ==', NULL, '2025-12-21 01:29:57'),
(1014, 254, 'user', 'oLU3bErCzwkRX2dbPi3UkUQ3UmdxZ0dwY0xWVUlpVERUcjh2aHc9PQ==', NULL, '2025-12-21 01:30:00'),
(1015, 254, 'bot', 'zuqbumpsqc13mEulvHTCtmxZNERHTjU5SFVXQUFqNmEzOGQ1VStRVW9rekZFdVcycFNPVjRPS01qbUNkb28rVENuZ2RNODVaZ1gxMUYrTmVwK2R5ZjAvUi85VGJwc1dRY1U4UGduUTBmMWFUVW12Rk5CMmd1ckFPTWNlYnBZSDJmZGtyejU0S2lGTlByakVmUGwyYTMzcU5XdnJIN1FpcVE5ckFnVnphNm9JSXBYa3JZN2d4TjVTdmpZR21pZ2w0a2h2NUM3eGtWbTJkbmFkRXc0RXg2bFRvZDJSV3BKZnlpcGp6Vlk1ZXpGRDFkNHB2VlJWUllLMGZuelNLM3BhTVM3Ky9DTlQxYXNadDFoczdkekJ4TWlNZHVxdTR5OUtvRnF4Mld4WHRnaDE5SGxzR3JBSkFFakJ0QjBsbXhZWkZtL2JpZjZkc2RLT0twRjRDMXZrTjR2YURyWE9mNW9uencvOFY1R3J1d0VEZWsweUt4bmQ5WHZMdTFXa0xBZS8yM3lWR0tYNTNRSTdwNkZMOEVwOXp2VzYvdEJTUUU0QWVkbmRYZ0diNmRiUVlud0ZOa1Mrd0E5cFdZc3hqeXRQK1Bvcjh1TlFjMEJQVUQ5OUdjR1ZGWFd4dStCT2dQN3FwM2tQMW13V2JPbGtzNzY1amFuWnFqQytCSzZ6T3FWeTlMQnZnb3cxOWcwbzFpSlI2YVlLZTJNc1E1SHFaRXBLaDdJUzlTVzdPa3h3d04vcTVzTTE2N1dEeDVoM2FJLzJJQnVzcU5USnA4SGNURmUwK2hFYnFTeGVHT09oczhFVE5KSTZvc0tFQnd1amNCdXB5cDA4Um1MUFBGeHZnU2lyQ25HRTl5emxSd1ZveWRYa1h6cnNjYVI3UjRnWEVSOTh6REFrb2EwVm5yQ3plOW95SUh4V25TY01mMVhYTTFrMVN0bXNoMDNRL0hmdHZxdmdQaWFoNXRXUUxsQVYxOXBEWTErcjRIY0poME9ucUwyQThWQmNwNlNCanNnQS9hY2RvYUFMS0ZKMHk3Q056Y1B5d2l4a2RDczVNRXZUMnBXNHRIYVpLM2ZyNTMyVXpiSVJORExHeUtMTG5zUXRCTHpMUFhmMTkrL29BOHR6eVZvRWNIT0dQdkxaN3RaLzdFQUVUWTRkK3hzN0dvd0d3ajJOdDNoSENHcnhUZW1leWhoQjg2TEpYT3VVb1RYbDdBUnpDU2xIRm4wUUpQYW5lUkVmdTh0Nk0rTzQ0dDFrTmFIbGF3WmxqanNVTHBiRjVKQXg4NDdGV25hVGdwdFdVQldrczJoOG5Qbm5VVllqSVdVcFJuNThZTGxJNXVTaTE0aDBPeUJwRGI0ZGZoWlUxZERwanFiRGIzQ0w5eTFpZ0JRSDBZa2JaNWZyMEtiSE1jcXExcFhaOEU4dnVPS2hVUDdmRy94Yng0RkF3YnZIcnZhRG0zRzlHdzlubDdUS2JqVDdBNm9kVWk0d2ZYcldIczkwZW9sTm94SDJMY0R0aUZoMXBXQU85ZVRoWWhZc2hLSmVOWGsvNHZFaVpzOVlpMTFaU1ZoOEt5UVVBT3QxMzU3aWhPbUdDV1VjbXJNQ0t3WlZiakxGM0dnKzJ5STFDNTlRcldYOXFOZi9MVEViOURHV254Z2xLTC9yQjlUelZVZ3EzdWZMRzRhMnRiOWRCa01VK3ErWDA3bFlMS1JjSUNsZUIvKzE2aGMyYXhCbzMxdGRDcENDT2dVb1cwSGlRcHNzV3VvRy9DVHR0cVh6ZTFIc0x1VXdQK00rQmIrVmRISldKbU0zZjNjNmpEMEJJNk0zL3gvbE1mYTJrM1E0c0NQTnIwYlR2elRBa1R3Y3FYbmlSRk9xTng5Q1doTHdobE8zZUk1OTltejVkSmw1YTc2cnJBVjUvMmR0RGFVcXdBV0g0UTZtMExvY1JaUHlvamR3TXZRWWl5YTBYc2ZoR1NSSkN0Z255SC9CbXB5RjhlOFhPd0h4RE5rcEhQT04xWEM1VEQ4ZTV5SXgzVVh1THhKVk8yYVJKMmVJRml6QWE3MnljSGdLMys5aVc0cHBIQkIyYlhFWDh6bGFsaGN1YUwvWFJRRC9jSi9XNThqak5pMnlFQUtRZUcrNXAyVFdXQlRjWmFhdHJ5MTkraG9OamVJakFFWjBhQUNUOTNmUE1YNUxIb281ZzhDSGNXUXJHZkFTTXFiRkVWY2tYL0xJPQ==', NULL, '2025-12-21 01:30:01'),
(1016, 255, 'user', 'uTPZ21EpTxv8ApNuIPr/SWcxbE5oOXg0WkhBZ1hhWjZhY00vSUE9PQ==', 'chat-image_694751873e3cd.jpg', '2025-12-21 01:46:47'),
(1017, 255, 'bot', 'exa8OufPZ9UVWcmJe96wXitCQXdmSnFiUTdLUGU4dFNBNUc2V3hnRGdlTDdlWStOcm15aXNQVlVmM2MweThoaFQreklSTmRyTDJzTXM2TUVRK0k1a0loVjlOcG4zK1lrK0NRNWxWcVZHUWtwWURXQXJvL0FoRThCSDZ0b29pZUdWUTNDQnBBeTFQQlV3K3FvZlVyU0l0OUJvNGpSNFlPSVFRdXliWjUxVnhDMGZhOXJwaGptb2hkTGhNbEtySmQ5ZkZrVXpGYXFLS0o4L1ZoN295eTVqR2gyMnpvQWtEekl2cmNCV0s4cEE1LzVkc0l3MmpSNmk3QjlvTmZrOXN1dkROMUxyT3JFbVEyQ1Mwb0paNDhEZGRodFhXY3hjMUlKbnlKaFhDOTJScHJsNlpoZEcrODVpdCs3VEo4dmNkUHhkbWVCaktLZFExWWpGdlBJd2hMS21nZjhGdVVWYzkza0VVWVVXOFVMdHhRQVdrbVA5Y2tMekpucDhvcmZreDkwR0tud2pzRmpZUUwzdlRFV1U4cVlSQmpiOTNSZWVIV3pkaXd5bkkzQTVmV1FKaFJwUDVWTGJnajFKRVZpSWhPWURKa0hSMzRoV3NkS2FLQTNINTRCNnB5dVh2K2JyUjhFazNRSi9Hd001N3ZUT1liY21XT0VYaGRuVE8vMGVCVTJJWkY5MmpmZ3ptSjBTcTI1a2dNRlE0ZTFwWUF4MWdTK20wdXRmTVQ1Wm1aUytXYUIvcVdMN1pteDdhMW4wYnZjR3dPMWFCY056ODlhSkk2elF3NTUrYlh1SDVGZm5RanFBY0dwUGpOVUIvejFLUWcvYm9EY1M3WkRwSE9XNEpMNnBSS0EvcEhkellhSmIzc3EvTkRTTHJYOFRzSEhSR0R1YzdOL21HQ1BBWlBDMzNMeUVEUHlXM2FVTVh2MmlzSStJMzZQOXYweHRjcjRkWjJnS2tMNXFmcDFuKzNHbGNzbDQ3MElzUmgwYjBrWENNRG9tb2hveFlodFBmWkZHaGI3N2psaE1PemIzQWwvQ1lUUHBlb1FVcTRoa2xxb2VQL0ZNTmkrcUl2L0RZdmdRL2diOFk0OHdIYnBlTjN4WjBZN0ZLeVk4THB1K3U2WkFiZVRxY1MvQWx2bldHU3RXUXQxZiswcVpTUTBYcjBMK0dxenpPNHprc1RYM0cxTzlXdFRVQjBTVmFIUE8yQ2QrbDE3QTB4Tk02MFdUMHlPUm1Va2s3MzNxdHBBSTZ6RWViR2FLUE50NWpvd0xFRGhtRzBNV1c0VjR4aDZlcE5vKyt6NzI2RkZZLzBSZm4yOS80VVMvQkVkTW9EU1ZvRFF5QTNwcS9ka284ay9kbnFTK0ErTnczbmtDVU8vYWhZTjNDWUpGKzc2WW4zRjlXNzRkcy8vNTNMOVd4UXVEVGFINzBITVdNMTIvaEtLWW9ZRXpFdkVPOS9SVHRFRG9zZ1I4Y2JTU3dXTk1BV0IxcUdiOHZLVUJUVUY1RVk1eDVxc1NhcTlkbVdFVXRTczNmY1RmSXV1R0tzb2Q3RllUZWtyd1ZDeQ==', NULL, '2025-12-21 01:46:56'),
(1032, 258, 'user', 'rcUvVHsFt05lQBgdN7Wi/HFtMVpyTVFyUHdlOUpTRmdyVG5qaG8vYkpWSTErRHNNc1Y1YmtMVUR4MzA9', NULL, '2026-02-04 03:28:33'),
(1033, 258, 'bot', '9zilx3yAmRDBekkLxsiu/3BmRCtpTlBZUWkwR1RUK00yVFd5RStYbHNBUXZ2WHdIMWpPMzcwai9UalpBRmJHc3Q5eW9Nc1B3a1dvM3FnekpjRmZhSFRUSzYraFUvQThSeDNHUkJtSVpMYTliL1Z4bFA0elJ1U3JlUStmdU5UamRtNGR0a2gzS3d2UVlDT0s0QTRBS0Y4MTE3SDNlK0tTNzBHUGRYZzZsRFZTYlhmNTZuQXZQenhCZFIwT3ZlaWJWb2RqZEJmdkxtamJqbjN1Qw==', NULL, '2026-02-04 03:28:35'),
(1034, 258, 'user', 'yj0i3by4/DBr9jyETJA/EEc4bnA0K09LcWdNM296MDNFbkhSdmc9PQ==', NULL, '2026-02-04 03:28:51'),
(1035, 258, 'bot', 'g1H6crpnFfYxNi9dnhPS7m41cmhBazlkblZXWDYyMTVPS21PTktFY0VwTWRJZGQvN2xVY2VvdElEN2xUV3RKcUlZTjZTdzJRYklCUTdFakh4a2hneGxkYzkwTzRGSmhlcjZPQWlBcXptK3BuV201MllXMVJEL2ZnNzNZU2tiREQ4bHNmeGxiWm5JcWVBS0V4UlJ4eHhERkRxU05yeWRjSWhOaEcwZHlSNWVDNStuWFBudTZnSjZmbWFIMCtuNStwR210dWZGWktHMmNaWkdxeE5GNkVrS25INi8vdXdXYlM2cytwY0g2ZnlRMXFwSVpvZ3JBaUVvKzJvYXl2bUZlNGs5anZaQVZkUWlaL3F6OWlaWnlLLyt0RU5RbWNUb2tKRmtjYW41RnUwbnF1ZzA4azY4T2Y2b2JaZEtXQnl1Y0dtZ0x4a0ZhQ0g1eGFYUUpYU01OUndMbWI1dFB0bSs1bTFDdzE1UEFrMFNEcDlEOXd0MzM2NjlWMjBSQjNIMGEwMnlDOGh1cmorMjN0akFuTHY1M3VjVEFLbUJnQTBtUGtRWjV1N2ZTQ2NaRFJzWkxiVS9wcUlUSEc3blVjY3BtelhNbFlmU2FpRHVUZHhzcGllSXcwOEdzTDE3cjZ6ZVkwVWNnVjdmd2pxUmdKbnFyTzY3WXRuU3dleE5TZ2lMeG5qWXE4UmtYZnpvcjRQMTcvcUxUSEJ2OXRIUGt4dGJKaVVDWUFhS0h2ZmxhbjIxUEZWdXQvcEZaTU1LR1lmVWtZRGptMmgwcVArblhvZkZnY3RvdWthbm5JSzVYSlc4anNYQVVKMUVxS3p6am9HT2VhcGt0ZEgyZDF3OVZCMEdtZjkra1RKaFRsNHB6bHhVeUFSYVdTL1hWcm9JVHNkN1NITTF5Q1pUbXoxNHdDazVZdm5KRUd1SnJweEhCREVKS3JqVzV2MUJvbGpiczZuWlBWUXYrM2l5c0thcDh4UVllcWtkajBRVlpJdHFQRXNEbzhaRmxHSWxwVVhUS2NvL3lmZnl1RzB0S2dvRmJQZGphR1h3ODZ0UHJ2a1doRFdOZkJOa1lXRHlkZjN3ZTg3R2RqUWhoblpvTjE0TXNvaDkvRU9nK0twYmhXOVZvK3A5R3NzYUR2U3NRVjloTXRBSzlUYmRyV04vd0IrTWg0VDYrMFlzeUxIandWWnp1ZHhUNmtUZ1dJbnd3MkZQcjk2OTB2NUR0WG9udTlaY0FOcHE4WE9WclhVVUJuOHBmaFQ5d1hYV0hMbjExTzd6ZnFIT2t2b1RZbFprYW1wVytxTFl3Q29RNElpWSs5MHNOU0dIcWJOSEZHcTlibU5jOTVVenZaSExycmpRU0JyNSs5U2VDbjI1VjdqbG4zbW1XNTVydlY0ZUhQWHhETzl2THMvVTY1UHFHYjNwUnVudHFSd1RhbWUweDdVNXZ4ZjMyVTc3RWYrajZQZzFFcWpKb0YwU0dPZHQrQ09mT0trWjU0Y1N3TFlEZ0tzSlFvQ2Z6SWVOYklyK0RYcCsrdEJCVE95SG5mdUNvbm0wbVI2NUJ1ZGxOSXhRcldTdENUbUVXeTYzY1BGSnphd2Jia0VLOHhmS0YvYktMWDV3aUZTRHVVbUtWcVBUSDg1emwvYWZjTVRKZCtBOU9Wc3BkRGpMWmcyR2J3S1QrdlNyY2lsbERJM0ZKbWFESVRiMGxZRHpNcm51RGxQM0J4a09LSlY5c0pJY3YxN0tTT1VSTGZ2eFg2S25XQWpIWUxKNzB2ZWgwL0E3ZU1uaTAyRUdiaE1NaDN1dk9aOEdkSzVQcGV4NnQxMXZZLw==', NULL, '2026-02-04 03:28:57'),
(1036, 258, 'user', 'Z+JhSPeM6pj3RsBewUgx1zNiYXJvb3AxcERRM3hrd2U1dFpLSDBra2Z1elpTcnBjc0NlcEFzL0N3WUk9', NULL, '2026-02-04 03:29:09'),
(1037, 258, 'bot', '3xhYkq5h3SaOQ6wSzhCmzWRJR2hrbG1KN3hPNGdldVBpc2hTVDFWUHc5V1JsMkNiK2YxaDd2U3VNekwrZVNFekhMbVZoMVFXekR1d05ZeWZTSC9icURBNS9icUtKZzhHdWl3eUxYQWJXZFVUZDd1OGJHR1dmdWJ2RXdtcFRVT3VlUmxwYWxSaTB1bmVCU2hvR1M0cHN0aTdoenp1TVVBUjBDUm9CNXN1WXBmNkt2Rk4wVjdDQjc5WFl5Z1pWVFFPT280eWE0bFRnUkt6cnF5QUNTTE9YMmxwOUtoVjFHdVFzRCtKa2F2d2h3a2QrazhzaXVBTDBDUFFSZTdZcDU0S0k5Vm5BTURMRlNRRktPZFA=', NULL, '2026-02-04 03:29:09'),
(1038, 258, 'user', '6HxLlxcOH1ubiVGDtfNiqCtDcWdUd21uem5tZFR6WkR5eUMySEhDM1A4WmNUZ1hMRTBRei9EcExKZkE9', NULL, '2026-02-04 03:29:28'),
(1039, 258, 'bot', 'fQm5RbDUOv3JFz2Cr83Gy3U1VWtMNDVjVXhDYVV1eGs4c1gzUkxuK0hrZ3Q5ZkZNRGZ0Wnh0Vk5VOTZkWlBZZW5BcURoVExGaS9BZ3RubnozSS9iUm51U3JtamhselErZU53eVVPeEMwVEFzUll2SGdTYUtUaUQrYjJLcEtmeTlxTWx5K0xLQldhRytydDlkWXAwL3B2WWxiZTYxclF4S1c2TjBFMG1EaVBJREh3bCtEM3VWSDBXQWtrRT0=', NULL, '2026-02-04 03:29:29'),
(1040, 258, 'user', '2PiRTcUsJCeB151W9dgi6zQ0VW1wc21OeGk3N3g1Q0dSbExLSVk1KzR1SDFvWjF5UTh0SFM1VWZNUGc9', NULL, '2026-02-04 03:29:50'),
(1041, 258, 'bot', 'pvbiHTyxUbNH14xlajzGglQ0cjZjMlc2Uy82K2p3dGxCK1RqQkZIL29NdGg2LzZXSHJiVFpoNnkvQ0o4UE1YZ0RXdm9Wa1l3ZGpzQnY0cktzc1JHMnJhV0NjUnNzYjlha3JCdXpRc2dVSDFqWU9uZUZnTlBkVVUrbmlVSnozSTVJL0ZvRDNicmlCek5kZEJQaiswbkVwbXJXaUErYUlxc2V6bjZCUmVCK1BmVE1XR3c3cHdiYktHL2ZURDZycGVueHExbHV0bXZpRjY1a0RxS3BkTW9KdDF4R2lsS0ZYOTBTVG16OWZyc1oxb0J0OGZvY2FtL2cwN05vSDI2czJYVHpGR2dBeXJNcFZxT3NQQ0NIN2Rzd0Y0RFRJZHl4QmhONk5nWnJiRjhKaG8yR3NHODJYd2g0VTV0b0UwWGk1OHdGdVAxOXFUbHJLeUMwRzh1ODhDaUd1S1JVdUgvNE84UmNpVDNCN3B1VWhyazNWeG1UbDZ5Snc3U2d6ZVBkZTVIMDlLdUZaZGRjTFZrUkR2eEF0V0Y2Y01UaUtvS05CV2RkOXFWanFOeEtXTTRRejFNZlN4N3ZVd3FZcVMyWS9uQ0QrVW1DSys5SzU4cll3akQzSCt5bnhWY1lraWlrMjZIREtnTEtQZ05sRkF4bFVlOWQ5ak5CNkk1QjFqRXNZZVM5bTJMQXNyb0RwVHl0QUxDQ2lZTUQ5dEpTMExuSHh2WmFXbzhaelJ6TGp2WmU3MExiUWI1TWtQQnVOOUhBWFQyb1RLRnZMbmdNUlN5Mm1Pdm5hUGZmd2xwcCtRZVVDcnVteHhZeTFXK2duMGEwTVJtU0Fjbm5nYlJSUnpTZG9yVGpyU09peEJ4ZkRkb1RzakY=', NULL, '2026-02-04 03:29:54'),
(1042, 259, 'user', 'p2DkuyqkB+Y3LbX6jNLpy21jME9Tc29ZanlYMXZYa05aZDFjeWdnNnFFVzFJQ0NYSkF0cGhJSU5NOU09', NULL, '2026-02-05 07:51:17'),
(1043, 259, 'bot', '36CAaY3AKuXxi+PbA4Rb9XlIK1RCdy9ZUWRacmQ4Um5qcklud25vUHJnbmU0eDhUZFpvTmxRalZxR0JKZ0JnNFZKdkpqU3g4MnpRd2NpZ3FYS3MzRFdqR0FKcm16S2lBM3RpeWlaMU1RU2JYc3E3YU9BRk9hYjRvTzk1MkIxYThOSWVBWWVtTzNYNGxGTjFMVk9SUkNUaXJxWlRMRWV0TzBYTGhQa3RBVnJLUkFic2w4Y3VZbElYdmEvRUZacm1uTDhLTUhXTHJZeWw2VnlkNw==', NULL, '2026-02-05 07:51:19'),
(1044, 259, 'user', 'g8Fm+UmtH+ea0FDp8baXSTBidGJTaUlKbVFaZEg3K0lwOG1Canc9PQ==', NULL, '2026-02-05 07:51:29'),
(1045, 259, 'bot', 'kGmS7u9PwwadGuB72OdjmXRFSGpacmthOGJjV1llUWlXRjdmL0pDdHE5UUowM0tsQTdnU1dmZHpzaGhUWHQvQ1JqUWdoSzdCMzV4MkVpWW44MVlyRWxPazUyTnM0eXlvTEV3R2lWNm9SUUMra3BQc0dqMGEzWFQ0RXJsNldtTWl0UmtQZ0xtS2hzZkNYQmFTQjI5MnZkdEpIbDNYY2p0Z2xkYU1BU2N6Y1dBR0lmZThWSnBhZ0tCczl2MGtIczFUcjlyby9kb0hXM2FBbS9vYThobXdUWE5ieGF0ejJJQlpkeFRVeHc9PQ==', NULL, '2026-02-05 07:51:31'),
(1046, 259, 'user', 'BGMq2G5ilQeSDPjRgp/R0ThNaDZiVHE3VlMzTFBJZ0JabXpralE9PQ==', NULL, '2026-02-05 07:51:53'),
(1047, 259, 'bot', 'PURobfUz1dfZsGelMFMWDnhMbVBWVDJ4S2pZNUYvZisxbFlRSHJUL1RyTWJLZW9LSFVuM3VzYnR3VndBcDFxcEhNOGprbHE1STFPUXF5VXJDczUwcnBMUG9zY1dDY3Q3d0sxd3JHakhVN3loczNTQ2NTbXJlekdoT0taWEJCQ3poKzNVdGtjaXEybXN5RnAyZGFWL25EZlNPNEw4MGVKakM5bmR3WU5WUGRlTzBUWTl5L3R6SzNxYU02UHEzTFAxMUNZVTZVYjB6VkhEWi8raE5nWmROcDFtU003ZitPQUZ1YkUzdnZWN2hDWkZqeHAyZzY1UTNVa2EvQ3FwZWk2YTVtTHFEaDE4K1pvVmNzbUtWWHhMNVNiZ1NUMnpnbHliazIyKzdRaC9FNDkvTFdjSGR1TFFMN3F3Sm1TLzZRcnlpZ3Z0cWhHTTlBSUJNWUsrd29kaGpmeDBEdGNHcERTdTlQTDQvbDJ2bGVBVXFGZXpOZEZMZ2hLVDhVUVRTWlhZc3l0dkxGSWRVQm9EbGd6SHh4MXh4OSthSHFUaHNKSlZ5YzBsYTM3Zmt0R1FkcUhzSzNJVUswQUdYSXZkcTFja2ExeWZsOFJkYUI3ejZhQm5mcTBMUWQ1aFkyck85bFBjSXNFZU5OMGI0eXVSMSttd0NNT21TdFZ3ZW52aU9wVUdZRFh6OUxCdkkwUkpyREN6b2I2RGRkTDQ1YjlkYjVaM0ZJWEQzN09RZjJVNUgzOXcxRktTK0ViZVZHVVNQc2JQVWUvSUYzR0N0SE9Ob0hscDdYczlIa2JSMGhEeVVXSjVRUnlFWkkzdkVLa29jTkszS1VlQmpVVWphdzdVUEl5OGNFdUd2M0FXbWsyL2loaTJHU0tyZWZVZVJPVUpwQ0NBa2xOU3gvWlNDZ1BQeU94RTBHZGhQeHorV2IxcFdQTHB4d0RNRHkyOVNuc2lmQ0xZZWVWWWprTU4yam1CcXVYMDFMZkY1LytjcVBGU09tUWJ2OUhTNk5vOTBsKzMzNGRvcVFrdll2STl1RkxhSDZySGpSeTY5Yk1uQUZZSU9ueTJCNUdpR0l3MytWR2NzeEROTHE1cTNvWVhEMmpoemFHUDFhbjdGWkpxaDhsOGNKQzhjaUJEMmdyZ3llV0ZRQ2EyVkZuVCtKM05BNU1lOTB0cC9EZkN2QVVPYTRCc21TeEFwZWhmeWlYUWkxUlBZVk5ncWVhVVpBWTlqc2lOQmswaDAyUVV6MDhCaFREV2ZjMTRhRWllMHN5a1B6bnJuTXkwQmkrM2dnUnErWE80QjI2bTVJZ0FrbWlWWXlTVDgvY0xGdE9zd0lreElTTTF1MFlVR2Fia2g1clhJTjhVRERjTWFTZUlzMDNHN2hncHV0Z2xPNUpZVC9XdkdycCtXcWJ5M1A5amdXWjdQZDM4aW42WEZZNFJnbkJrYXhETlliWXhKZXdDUFpodTVTWmZqQ3JkRHZhZXJOb3g5d1VnRmYvYWpENzVGK052NDEwM21zWE1KbE16bWVrczN3MlA1ZlRvZE9IcTdxSHQ4NGhicG9ZWFEyelRUcTlHSHkzVms3bXlKMTdGbTdiRXZGa09nK2RMRTRSTnJrS0MxZENXRzdleldvTk93b09QNTAwcHdZSkpYeUtSUmxpL0RJQ1o4dlJFWG5CYTZyK1pNK1dGZEZTS3ZMWC9uRnRZUDMzTHdFRVNDUnJyejNFT3Bwdm81dzF6VGZqMnc0UWw4NzlJRGJnS2k1ZitGeDZEK0lPM29aVGUxNmEyeFZocnkzQ1NTdGZmSGQ0TVI5OUFmUWdYc05naVhSOG9POSs4bTRseTBsa1lPbTNNVm1IZGhHN0FYdkg2RlBxVmdBTzZ4VTcxTk9iMVprWFhBZnN6TGNzSlJCREkwZk5IdnVsdUNlYlJDMlBtOWRKVmZBT3Z4SUk2RFVzdXhqR1RUTzJyekpIWHNuVmlEdnYzZkQ5d1JFNEhmK0I5UVNSUzRhVWZZUUNnMTR5cmR1VEdOTDArZUhnVmxvc05LVkswOFdMM1BBam9ZWXNnTXZTVGlLWkVrd20xYkxKaA==', NULL, '2026-02-05 07:51:57'),
(1048, 259, 'user', 'cRkvCp3fNx/HbOLwhOPmS0xxWnd3d3hMMll5dU4rSDgvMEk5aUp1cU1sNDFnaWlTSjhwTmFqSzVldzA9', 'chat-image_69844c3be4969.jpg', '2026-02-05 07:52:27'),
(1049, 259, 'bot', 'IbcOWJHli45WiBcrq1nF1zdwcGdTVE43YkdBOUM0L0pJSTRiVHY3emdWKzMrVEZWRTB3R2taNWZ1SmtDUzN3TTRxRSsrN1Q1Vk12bURKVHNZUllCTE50eVp6TlFVMHpoNENSYUhuT1hGNXgwT05JU212azN4YzBVN1VkYXowVGhyK2h6aGY0b3RrM2JxdlFFdTN2YUJlZEdUckhSR2d2elY0MnhuQ3BEWVBSbnZsRnFyTllTQmlZcTNpUzVXaGYxYnZQY2RtVFh0bm1ldy9CaExhaWlVZDJyOE5Idm92RUlMMFpTUWxKd2xKSDlsWlVYcnpVYUFjcWsrOVNKVDJvRjlURkVzWVdxa3NwSS9ka2gzYlRiRFJWcUpDNmQvak9uQldVSEN1RWdNV0JvczlJZi84NzZlUzcxUERZYTdtU1F0TkxQTzNzYnBMQTUrblAxZjBEYjdsUXdUelpyRlp4TG9ZUFp6dXhBUU1VRE52dkcwSlJacVZMMG9PU0djL0IyTkdPT0FodUlSWllMa280azJCZHVzVzJ6S2hVUnkrRDNFMnQrRHVhbWdydFlWdFJrZExscUgvVGNlbWRxcHlwbitvRCtydkhaWVBBL1N4cDNVeEVvb2lRT3pnbWV4bTJ1YUFaQUJUTmkxcmlxWUtmNjVOU3RWUHE3K3JjQ1c4eGk1L3ZRNGQwbGpnT1krQm5tUHhxdjlQUEx0ZklBS1JIQUF0TFJ3Y1pOS2xhb25kdkZqeUZVWmRwSm5mS1haVmNGVjFnZWVxNm00WnFLRmFwc1p1T3l1SVhYV2NJVHNxQVcwQXM4N2p4cGRYTi9LdVdML25DOW1nNWcvY01YbW9GWEFZQXlQZksyZUhjbS9rNzh5TS9oYjNTa1BPRGcrZmxmajJQRTRIVHVweWJ0RHlBVU1OK3ZHYU1FaGkrZUZMSGM4WGF3dU1Jb0Zoc1JUbURhMFVyV1htQ0hRRXZWczlvdXhOdEplSENNTXhCYmNBcjhtaXlBRUgrbEI2b3BjU0VEaXdHTnAwdXVaeXR3RGdTQklRK1pxT24wOXByYUtuUVNyOWZFNW1mTWJhY3Z5VW1seVB3MXBtYjhDRlpmbngzYzVtSWt3aVlmUzdkTEpjai9sMFZkM1V2T0p3UnBaT0hDRFlGQWtzWGkzUmtxU2pRS05kRGtNeGZibzZzPQ==', NULL, '2026-02-05 07:52:31'),
(1070, 263, 'user', '2+/tRAm92JxF7NT9LQD4my9ERklhdjhIM2VxUTZEdDFoV1dIMkE5WXAzZDRHK3F4MDJlVVJUb0ZPYXc9', NULL, '2026-03-28 11:25:32'),
(1071, 263, 'bot', 'SzNFgWSbGRlFoDT+zAPiUzA5ZG9DeDRaS3lWd0sxOGhVSnNpNzlRRWlneURBNnltL2NlYWJPZHBiNVNvb0VTMmI0ZlNwWXU2cHliajdYUFdjOG5QbWlpcDNZdGQ5aWVSeU1hMFgxY3V0Q3JVWHhIa1htYVNhbUZWd1kyTVU3TG1EaDlUVURITkJ3czFLNy9CTW1mbGZaTktPd3VvUE1LNEVPMlI5Z3Rwb2pzZHU1YkpEY2pqcWdTSS9keFRWcmovSi9QRG1OK25YaXB2R3RQYlgrcDVQUWhBN2hoVG9tbTRDY012bnU0dm4xNVZiR2lhZXJjOEE3NHZwVGNWT1UvVVFFb0xKYkNYQXpHNWNoQ00=', NULL, '2026-03-28 11:25:32'),
(1072, 263, 'user', '4AYoIRnoBTcJLiPRq2bHZTUzdmRTNkRZdFpSZndoYUp2U3Z2aEE9PQ==', NULL, '2026-03-28 11:25:40'),
(1073, 263, 'bot', '7PWhqryQVXDV5sip64RRz2Qwakw5Z0VuQ2ZlbS8xTS9ITVhvd2dLajJJR1pKdDBFN1JYQWk3VG5HQmg1QmR1MGlsRmdyY0JIL2JXemUwbTl1NnNpYXAzb1Z0UmxnNVVEQ1hwbFBBcEdnbUdiQlQ5MUFzU0ZBUHFFQ3p5MjkzRWdISTRWaTBtSU95cWVMZ0JyUnQxcGhseHNJQk5WaUIzTVV5WVFsOVNwdGZwSEs5YXdJelBrUCs0RW5UWmgzbnl3OEs2LzRodkhPQ2d5Nkx0R3VaUGFXWnJoU1NJYng5VEt5NUs0ZkRlZ3FlR2dqYURwaVJ3Vmc0dDFzVE51MGM3NjFrV2Mra1Roai84T2h0S0RyL0ZPWHRaaGlhVGl0MFltcEdISnQ0VkNaYisrM0RRRWdaSFlnYkVQODcyTjEycDFyMHBBTmpNaE01eGdXb3FZcUFMQUdXdDZ2N3RyQVV3WlJ3V3ZzQ0pjT3NxeU9mTWJ6b0taUHZUVGpSckgzVHVEN2Jpb1FkQTVnRWdRM2tQSjVGUkQ1VnhDMElXM3dURVNzK1ZFR1Z3L2tpUUJCZXZCSEhBd0pIdjRjaFpkMXFoSm9ENVJHaklSYWhuUnkxRWNUeVI3c2llQ2R2dG9OR29udWc1VGJhdjNWWVU2N0FxaVRFQlJLSFBlRW43RXViblk3d2JSbXFieENJZ2RIL3lUODd5QlVPa2NOYjkxeWRQYktJMHBBVmxkZDl0b2hLdHRmSjQ3K2pHSStsZGZ4SmYvZW9rdUhJQXZuNmt2RSt3U2NodTlaL3hKRW5KamdWeTlJT0lOSlRaRG15Q282MkJCV1N0Yml0YmpTT3ZwaExMRTR5c2JONlEvSURlN2NSR2FEamJ2bUtoNktGaWdvVnZBeGg3endzRDljdlVwY1NPeXFMaE1xeFYwQmJWMUxqRXhSTm9vMDdpaXZ1QjFyZmNZV2JrSWFZMEpZc0Z0dXdKdXQvcGVwcTV1M3oxa2hKenFFSEtvMHlxSXpRR1l3YTFSSG8xbnlBejFRMUNvQURJN3JTeTZ5ZUNUbmhVZUlWUW1YNFd3cFViQVMxVmFXaHdPYVV5RjJVbW5LditJaGZNek9GN3pScFV2NnRvbG9aMW1RQm1KUGQveFFoT210akg3US95Q1NSb3V5dEFtSDJacUxZdXpad1JKdkQ0ZU9ra3FvNk5nWnR3dUg0eDkzZGw4', NULL, '2026-03-28 11:25:41');

-- --------------------------------------------------------

--
-- Table structure for table `data_download_requests`
--

CREATE TABLE `data_download_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','processing','completed','failed') DEFAULT 'pending',
  `download_url` varchar(255) DEFAULT NULL,
  `request_type` varchar(20) NOT NULL DEFAULT 'personal',
  `data_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_types`)),
  `completion_date` datetime DEFAULT current_timestamp(),
  `format` varchar(10) DEFAULT 'json',
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_download_requests`
--

INSERT INTO `data_download_requests` (`id`, `user_id`, `request_date`, `status`, `download_url`, `request_type`, `data_types`, `completion_date`, `format`, `expires_at`) VALUES
(125, 236, '2025-12-26 23:17:35', '', NULL, 'custom', '[\"personal\"]', '2025-12-26 23:17:35', 'json', '2026-01-02 23:17:35');

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_type` varchar(50) NOT NULL,
  `sent_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_assistance_services`
--

CREATE TABLE `emergency_assistance_services` (
  `id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_assistance_services`
--

INSERT INTO `emergency_assistance_services` (`id`, `subcategory_id`, `name`, `slug`, `value`) VALUES
(1, 1, 'Flatbed Towing', 'flatbed-towing', 'flatbed_towing'),
(2, 1, 'Wheel-Lift Towing', 'wheel-lift-towing', 'wheel_lift_towing'),
(3, 1, 'Accident Recovery', 'accident-recovery', 'accident_recovery_cars'),
(4, 2, 'Motorcycle Towing', 'motorcycle-towing', 'motorcycle_towing'),
(5, 2, 'Tricycle Towing', 'tricycle-towing', 'tricycle_towing'),
(6, 3, 'Van/Light Truck Towing', 'van-light-truck-towing', 'light_truck_towing'),
(7, 3, 'Minor Accident Recovery', 'minor-accident-recovery', 'minor_accident_recovery'),
(8, 4, 'Heavy-Duty Tow', 'heavy-duty-tow', 'heavy_duty_tow'),
(9, 4, 'Crane-Assisted Recovery', 'crane-assisted-recovery', 'crane_assisted_recovery'),
(12, 6, 'Low-Bed Transport', 'low-bed-transport', 'low_bed_transport'),
(13, 6, 'Site Recovery', 'site-recovery', 'site_recovery'),
(14, 7, 'Jumpstart', 'jumpstart', 'jumpstart_cars'),
(15, 7, 'On-Site Battery Replacement', 'on-site-battery-replacement', 'battery_replacement_cars'),
(16, 8, 'Battery Jumpstart', 'battery-jumpstart', 'jumpstart_motorcycle'),
(17, 8, 'Battery Swap', 'battery-swap', 'battery_swap_motorcycle'),
(18, 9, '12V/24V Jumpstart', '12v-24v-jumpstart', 'jumpstart_commercial'),
(19, 9, 'Fleet Battery Replacement', 'fleet-battery-replacement', 'fleet_battery_replacement'),
(20, 10, '24V Jumpstart', '24v-jumpstart', 'jumpstart_heavy'),
(21, 10, 'Multi-Battery Service', 'multi-battery-service', 'multi_battery_service'),
(24, 12, 'Flat Tire Replacement (with spare)', 'flat-tire-replacement-with-spare', 'tire_replacement_spare'),
(25, 12, 'Tire Inflation / Portable Compressor', 'tire-inflation-portable-compressor', 'tire_inflation'),
(26, 13, 'Tire Change', 'tire-change', 'tire_change_motorcycle'),
(27, 13, 'Tube/Valve Fix', 'tube-valve-fix', 'tube_valve_fix'),
(28, 14, 'Roadside Tire Replacement', 'roadside-tire-replacement', 'roadside_tire_replacement_commercial'),
(29, 14, 'Emergency Vulcanizing', 'emergency-vulcanizing', 'vulcanizing_commercial'),
(30, 15, 'On-Site Tire Replacement', 'on-site-tire-replacement', 'tire_replacement_heavy'),
(31, 15, 'Heavy-Duty Vulcanizing', 'heavy-duty-vulcanizing', 'vulcanizing_heavy'),
(34, 17, 'Emergency Fuel Delivery (Gas/Diesel)', 'emergency-fuel-delivery-gas-diesel', 'fuel_delivery_cars'),
(35, 17, 'Coolant / Oil Top-up', 'coolant-oil-top-up', 'fluids_topup_cars'),
(36, 18, 'Fuel Delivery (Gasoline)', 'fuel-delivery-gasoline', 'fuel_delivery_motorcycle'),
(37, 19, 'Fuel Delivery (Gas/Diesel)', 'fuel-delivery-gas-diesel', 'fuel_delivery_commercial'),
(38, 19, 'Brake/Power Steering Fluid Top-up', 'brake-power-steering-fluid-top-up', 'fluids_topup_commercial'),
(39, 20, 'Bulk Diesel Delivery', 'bulk-diesel-delivery', 'bulk_diesel_delivery'),
(40, 20, 'Hydraulic/Brake Fluid Top-up', 'hydraulic-brake-fluid-top-up', 'fluids_topup_heavy'),
(42, 22, 'Door Unlocking', 'door-unlocking', 'door_unlocking_cars'),
(43, 22, 'Key Retrieval (inside vehicle)', 'key-retrieval-inside-vehicle', 'key_retrieval_cars'),
(44, 23, 'Seat Compartment Unlock', 'seat-compartment-unlock', 'seat_unlock_motorcycle'),
(45, 23, 'Broken Key Extraction', 'broken-key-extraction', 'key_extraction_motorcycle'),
(46, 24, 'Van/Light Truck Door Unlocking', 'van-light-truck-door-unlocking', 'door_unlocking_commercial'),
(47, 25, 'Heavy Vehicle Door Unlocking', 'heavy-vehicle-door-unlocking', 'door_unlocking_heavy'),
(48, 26, 'Belt Replacement (fan/alternator)', 'belt-replacement-fan-alternator', 'belt_replacement_cars'),
(49, 26, 'Hose Leak Temporary Fix', 'hose-leak-temporary-fix', 'hose_fix_cars'),
(50, 26, 'Minor Engine Adjustment', 'minor-engine-adjustment', 'engine_adjustment_cars'),
(51, 27, 'Chain Adjustment', 'chain-adjustment', 'chain_adjustment_motorcycle'),
(52, 27, 'Spark Plug Replacement', 'spark-plug-replacement', 'spark_plug_motorcycle'),
(53, 27, 'Carburetor Tuning', 'carburetor-tuning', 'carburetor_tuning'),
(54, 28, 'Belt/Hose Temporary Fix', 'belt-hose-temporary-fix', 'belt_hose_fix_commercial'),
(55, 28, 'Alternator & Starter Quick Repair', 'alternator-starter-quick-repair', 'alternator_starter_repair'),
(56, 29, 'Hose/Line Temporary Fix', 'hose-line-temporary-fix', 'hose_line_fix_heavy'),
(57, 29, 'Alternator Belt Replacement', 'alternator-belt-replacement', 'alternator_belt_heavy'),
(60, 31, 'On-Site Safety Setup', 'on-site-safety-setup', 'on_site_safety'),
(62, 31, 'First Aid Support', 'first-aid-support', 'first_aid_support'),
(63, 31, 'Breakdown Escort Service', 'breakdown-escort-service', 'breakdown_escort');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_categories`
--

CREATE TABLE `emergency_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT 'fas fa-cogs',
  `display_order` int(11) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_categories`
--

INSERT INTO `emergency_categories` (`id`, `name`, `slug`, `icon`, `display_order`) VALUES
(1, 'Towing & Recovery', 'towing-recovery', 'fas fa-cogs', 10),
(2, 'Battery & Electrical Assistance', 'battery-electrical-assistance', 'fas fa-cogs', 10),
(3, 'Tire & Wheel Assistance', 'tire-wheel-assistance', 'fas fa-cogs', 10),
(4, 'Fuel & Fluids Delivery', 'fuel-fluids-delivery', 'fas fa-cogs', 10),
(5, 'Lockout & Key Assistance', 'lockout-key-assistance', 'fas fa-cogs', 10),
(6, 'Minor On-Site Mechanical Repairs', 'minor-on-site-mechanical-repairs', 'fas fa-cogs', 10),
(7, 'Accident & Breakdown Assistance', 'accident-breakdown-assistance', 'fas fa-cogs', 10);

-- --------------------------------------------------------

--
-- Table structure for table `emergency_requests`
--

CREATE TABLE `emergency_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `shop_user_id` int(11) NOT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `vehicle_model` varchar(100) NOT NULL,
  `issue_description` text NOT NULL,
  `full_address` text NOT NULL,
  `location` text DEFAULT NULL,
  `contact_number` text NOT NULL,
  `urgent` tinyint(1) DEFAULT 0,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` enum('pending','accepted','rejected','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `photos` text DEFAULT NULL,
  `video` text DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT 0,
  `delete_notification` tinyint(1) DEFAULT 0,
  `is_read` tinyint(1) DEFAULT 0,
  `seen_emergency_request` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_requests`
--

INSERT INTO `emergency_requests` (`id`, `user_id`, `shop_id`, `shop_user_id`, `vehicle_type`, `vehicle_model`, `issue_description`, `full_address`, `location`, `contact_number`, `urgent`, `latitude`, `longitude`, `status`, `created_at`, `updated_at`, `completed_at`, `photos`, `video`, `hidden`, `delete_notification`, `is_read`, `seen_emergency_request`, `is_deleted`, `deleted_at`) VALUES
(163, 289, 142, 278, 'Motorcycle', 'Honda click 125', 'Flat tires', 'YeQjQtH1afqAjS+YzQQQeVgxT1E3Tk5BVExBc2M5OGU3cVo2Zi9FZlFqcGVxQ09pUERXMUVhdkthSjlTbXo5ZUViWDQrNGpkL0YvQzdRZnVIaTk0elRYNzBHQi9jdVF4TjNLY0lNcEpFYlVZVDFYWmlnZ3BEMHZyL2Y0WmNHMzR5VkRpV0xYUXB6TU1hK0hFNXhnUWdWS3VWNjIyY2NzdHJvTHQweVlDTm12c1FZK0F4aUxFaU5IdllMMD0=', 'Location coordinates encrypted for security', 'GupJ/D746OY9UJFZ0JqSnzFicHFYTlUvYUE0OEV2Q1JKdTBvSnc9PQ==', 0, 0.00000000, 0.00000000, 'completed', '2026-02-19 01:27:03', '2026-03-11 02:33:53', '2026-03-18 08:30:14', NULL, NULL, 0, 0, 1, 1, 0, NULL),
(165, 298, 142, 278, 'Motorcycle', 'honda click', 'flat tire', 'jZTb3OXWelrR+TE+Ctgn3HQwMVU0dEZBL0xBRkJpTkQwaUwwb3FlTS9uWGczc3R3RmNtWlQyUHJFdGZpMFppUTBzS3BGZ1VRcStjbnRNZTk0WE9iWHp6RUY0ZXZOTGxVUkwwbVJJcFVhdzhsdjlxQzJUOVAzV2pmVGRHdjNpVjgraFludUFVN0ZpbzQvd0F5aStqNVphT25JNGpHUGc0TGpQTXY4ZUZ3R1o1dnBBdEhIZzdwSndzTXY0RFBCL21rR1pKclhsOFFGd3N1NmJFU3B4L1NYR3Z1TGdDOFdpd0NuRWRzdUE9PQ==', 'Location coordinates encrypted for security', 'Fmhqaj+urD96Or2NZrljoHdxNjlSNFhTU0gybS9JS1hGaEtSUXc9PQ==', 0, 0.00000000, 0.00000000, 'pending', '2026-03-11 08:42:16', '2026-03-11 08:42:18', NULL, NULL, NULL, 0, 0, 0, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `emergency_subcategories`
--

CREATE TABLE `emergency_subcategories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_subcategories`
--

INSERT INTO `emergency_subcategories` (`id`, `category_id`, `name`, `slug`) VALUES
(1, 1, 'Cars & SUVs', 'cars-suvs'),
(2, 1, 'Motorcycles & Tricycles', 'motorcycles-tricycles'),
(3, 1, 'Light/Medium Commercial Vehicles', 'light-medium-commercial-vehicles'),
(4, 1, 'Heavy Trucks & Buses', 'heavy-trucks-buses'),
(6, 1, 'Heavy Equipment', 'heavy-equipment'),
(7, 2, 'Cars & SUVs', 'cars-suvs-2'),
(8, 2, 'Motorcycles & Tricycles', 'motorcycles-tricycles-2'),
(9, 2, 'Light/Medium Commercial Vehicles', 'light-medium-commercial-vehicles-2'),
(10, 2, 'Heavy Trucks & Buses', 'heavy-trucks-buses-2'),
(12, 3, 'Cars & SUVs', 'cars-suvs-3'),
(13, 3, 'Motorcycles & Tricycles', 'motorcycles-tricycles-3'),
(14, 3, 'Light/Medium Commercial Vehicles', 'light-medium-commercial-vehicles-3'),
(15, 3, 'Heavy Trucks & Buses', 'heavy-trucks-buses-3'),
(17, 4, 'Cars & SUVs', 'cars-suvs-4'),
(18, 4, 'Motorcycles & Tricycles', 'motorcycles-tricycles-4'),
(19, 4, 'Light/Medium Commercial Vehicles', 'light-medium-commercial-vehicles-4'),
(20, 4, 'Heavy Trucks & Buses', 'heavy-trucks-buses-4'),
(22, 5, 'Cars & SUVs', 'cars-suvs-5'),
(23, 5, 'Motorcycles & Tricycles', 'motorcycles-tricycles-5'),
(24, 5, 'Light/Medium Commercial Vehicles', 'light-medium-commercial-vehicles-5'),
(25, 5, 'Heavy Trucks & Buses', 'heavy-trucks-buses-5'),
(26, 6, 'Cars & SUVs', 'cars-suvs-6'),
(27, 6, 'Motorcycles & Tricycles', 'motorcycles-tricycles-6'),
(28, 6, 'Light/Medium Commercial Vehicles', 'light-medium-commercial-vehicles-6'),
(29, 6, 'Heavy Trucks & Buses', 'heavy-trucks-buses-6'),
(31, 7, 'All Vehicle Types', 'all-vehicle-types');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `name`, `email`, `subject`, `message`, `submitted_at`) VALUES
(1, 'Sayrelle James Tiron', 'sayjay399@gmail.com', 'Shop Feedback', 'T55', '2025-11-16 05:47:43');

-- --------------------------------------------------------

--
-- Table structure for table `inactivity_notifications`
--

CREATE TABLE `inactivity_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `days_inactive` int(11) NOT NULL,
  `notification_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `client_message_id` varchar(50) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `is_automated` tinyint(1) NOT NULL DEFAULT 0,
  `related_message_id` int(11) DEFAULT NULL,
  `deleted_by_sender` tinyint(1) DEFAULT 0,
  `deleted_by_receiver` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `created_at`, `is_read`, `client_message_id`, `attachment`, `is_deleted`, `is_automated`, `related_message_id`, `deleted_by_sender`, `deleted_by_receiver`) VALUES
(832, 278, 278, 'QiNsQIUVkP0wrP13Ma62NfLYNwzPhG0yEL+26EMWrjk=', '2025-11-13 14:45:19', 1, 'msg_1763045119318_02aise1s8', '', 0, 0, NULL, 1, 1),
(833, 278, 278, 'aq79BJe/ePiCJjUHpQTZaADS4be0mTZ+V/TpnEOKQh8MZbLdlH7xnBWeBdptUKFaq/fEUw/1xFZyGDYULvX1XoRhS6gEr8cEWOMjBmWo9Qypgcl3s2U4HdlHsRxU6bxIAJth/y8AiGjmBl6MtTlQZA==', '2025-11-13 14:45:20', 1, 'auto_1763045120517_bpae9nfqm', NULL, 0, 1, NULL, 1, 1),
(834, 278, 278, 'V6MsKvZhN/CGNLrgw0MFGQ==', '2025-11-13 14:46:07', 1, 'msg_1763045167023_7qh3lc894', '', 0, 0, NULL, 1, 1),
(835, 278, 278, 'VJ0mxIJ8kZhDswXSsy16+Q==', '2025-11-13 14:46:44', 1, 'msg_1763045204235_kraopiygq', '', 0, 0, NULL, 1, 1),
(836, 278, 278, '2mhoc81Tjj6DWTiJgOrEI0d++iyVXQNWp+0NM5UvW1k+34xSn42YumGnc74SLWv1TKR1M6m3wHoS6WK6FoGM87J4b00ni0Iufc14G851PII=', '2025-11-13 14:46:45', 1, 'auto_1763045205392_g7x98q4gx', NULL, 0, 1, NULL, 1, 1),
(846, 236, 236, 'yiur3SQkBfBpYa5R5fqlyA==', '2025-11-18 07:04:20', 1, 'msg_1763449466266_mdolyp6hm', '', 0, 0, NULL, 1, 1),
(847, 236, 236, 'yiur3SQkBfBpYa5R5fqlyA==', '2025-11-18 07:27:12', 1, 'msg_1763450838073_8nzvtynbf', '', 0, 0, NULL, 1, 1),
(848, 236, 236, 'kC0DTx9LwC3yvuOtHihJXQ==', '2025-11-18 07:27:19', 1, 'img_1763450843396_ts9zj44wx', 'img_691c1fd7ecf82.jpg', 0, 0, NULL, 1, 1),
(849, 236, 236, 'nld1g5JpD2OpGEhEnUrfTw==', '2025-11-18 11:30:59', 1, 'msg_1763465459188_x31eq1dxp', '', 0, 0, NULL, 1, 1),
(850, 236, 236, 'rNuyJAFkHzoMncgt57362A==', '2025-11-18 11:31:00', 1, 'auto_1763465460343_f9highdbh', NULL, 0, 1, NULL, 1, 1),
(851, 236, 236, 'yiur3SQkBfBpYa5R5fqlyA==', '2025-11-18 11:31:03', 1, 'msg_1763465463537_pao9lk6v7', '', 0, 0, NULL, 1, 1),
(865, 278, 289, 'XshvU7/UhJqC2oNLf3jB8VZ2Tb0y1w1dST/VgPiiMSgtYPH3htnhV3EgKP2ZV9bB', '2026-03-11 00:21:27', 1, NULL, '', 0, 0, NULL, 0, 0),
(866, 248, 278, 'QiNsQIUVkP0wrP13Ma62NfLYNwzPhG0yEL+26EMWrjk=', '2026-03-11 08:47:25', 1, 'msg_1773218842944_cevdgo4uo', '', 0, 0, NULL, 0, 0),
(867, 278, 248, 'aq79BJe/ePiCJjUHpQTZaADS4be0mTZ+V/TpnEOKQh8MZbLdlH7xnBWeBdptUKFaq/fEUw/1xFZyGDYULvX1XoRhS6gEr8cEWOMjBmWo9Qypgcl3s2U4HdlHsRxU6bxIAJth/y8AiGjmBl6MtTlQZA==', '2026-03-11 08:47:27', 0, 'auto_1773218844637_ffe113ls0', NULL, 0, 1, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `notification_type` enum('nearby','province','booking_sent','booking_received','booking_cancelled','booking_accepted','booking_rejected','booking_completed','emergency_completed','emergency_rejected','emergency_cancelled','emergency_received','emergency_accepted','verification','verification_verified','verification_rejected','emergency_sent','application','application_approved','application_rejected') NOT NULL,
  `distance` decimal(10,2) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `delete_notification` tinyint(1) DEFAULT 0,
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `shop_id`, `related_id`, `notification_type`, `distance`, `is_read`, `created_at`, `delete_notification`, `status`) VALUES
(70, 236, 0, 37, 'verification', NULL, 1, '2025-10-16 06:40:37', 0, 'pending'),
(71, 236, 0, 37, 'verification_verified', NULL, 1, '2025-10-16 06:41:04', 0, 'verified'),
(74, 236, 129, 143, 'emergency_sent', NULL, 1, '2025-10-18 06:13:56', 1, 'pending'),
(76, 236, 129, 143, 'emergency_accepted', NULL, 1, '2025-10-18 06:16:07', 1, 'accepted'),
(77, 236, 129, 84, 'booking_sent', NULL, 1, '2025-10-18 06:18:50', 1, 'Pending'),
(79, 236, 129, 84, 'booking_rejected', NULL, 1, '2025-10-18 06:26:00', 1, 'Reject'),
(80, 236, 129, 84, 'booking_accepted', NULL, 1, '2025-10-18 06:31:22', 1, 'Accept'),
(81, 236, 129, 84, 'booking_completed', NULL, 1, '2025-10-18 06:31:27', 1, 'Completed'),
(82, 236, 129, 84, 'booking_accepted', NULL, 1, '2025-10-18 07:04:33', 1, 'Accept'),
(83, 236, 129, 85, 'booking_sent', NULL, 1, '2025-10-20 05:04:31', 1, 'Pending'),
(85, 236, 0, 130, 'application', NULL, 1, '2025-10-20 13:55:37', 0, 'pending'),
(86, 236, 0, 130, 'application_approved', NULL, 1, '2025-10-20 14:06:13', 0, 'Approved'),
(87, 236, 0, 130, 'application_approved', NULL, 1, '2025-10-20 14:06:42', 0, 'Approved'),
(91, 236, 130, 144, 'emergency_received', NULL, 1, '2025-10-20 17:06:00', 1, 'pending'),
(94, 236, 130, 86, 'booking_received', NULL, 1, '2025-10-20 17:20:12', 1, 'Pending'),
(97, 240, 129, 87, 'booking_sent', NULL, 0, '2025-10-22 05:25:36', 0, 'Pending'),
(116, 248, 0, 41, 'verification', NULL, 0, '2025-10-27 10:40:20', 0, 'pending'),
(117, 248, 0, 41, 'verification_verified', NULL, 0, '2025-10-27 10:43:41', 0, 'verified'),
(118, 248, 133, 146, 'emergency_sent', NULL, 0, '2025-10-27 10:48:03', 0, 'pending'),
(120, 248, 133, 146, 'emergency_accepted', NULL, 0, '2025-10-27 11:15:29', 0, 'accepted'),
(121, 248, 133, 146, 'emergency_accepted', NULL, 0, '2025-10-27 11:15:55', 0, 'accepted'),
(124, 248, 133, 146, 'emergency_accepted', NULL, 0, '2025-10-27 11:28:27', 0, 'accepted'),
(125, 248, 133, 146, 'emergency_accepted', NULL, 0, '2025-10-27 11:36:54', 0, 'accepted'),
(127, 248, 133, 146, 'emergency_completed', NULL, 0, '2025-10-27 11:37:06', 0, 'completed'),
(129, 260, 0, 42, 'verification', NULL, 0, '2025-10-27 11:43:29', 0, 'pending'),
(130, 260, 0, 42, 'verification_verified', NULL, 0, '2025-10-27 11:48:11', 0, 'verified'),
(131, 267, 0, 43, 'verification', NULL, 0, '2025-10-28 04:32:43', 0, 'pending'),
(132, 248, 131, 147, 'emergency_sent', NULL, 0, '2025-10-28 05:14:42', 0, 'pending'),
(134, 248, 131, 147, 'emergency_accepted', NULL, 0, '2025-10-28 05:21:54', 0, 'accepted'),
(135, 267, 0, 43, 'verification_verified', NULL, 0, '2025-10-28 05:27:26', 0, 'verified'),
(177, 236, 130, 154, 'emergency_received', NULL, 1, '2025-10-29 10:13:44', 0, 'pending'),
(182, 236, 130, 154, 'emergency_cancelled', NULL, 1, '2025-11-01 04:22:22', 0, 'cancelled'),
(184, 236, 130, 155, 'emergency_received', NULL, 1, '2025-11-01 04:23:00', 0, 'pending'),
(185, 236, 130, 155, 'emergency_cancelled', NULL, 1, '2025-11-01 04:27:14', 0, 'cancelled'),
(187, 236, 130, 156, 'emergency_received', NULL, 1, '2025-11-01 04:27:46', 0, 'pending'),
(188, 236, 130, 156, 'emergency_cancelled', NULL, 1, '2025-11-01 04:31:32', 0, 'cancelled'),
(190, 236, 130, 157, 'emergency_received', NULL, 1, '2025-11-01 04:32:01', 0, 'pending'),
(200, 236, 130, 94, 'booking_received', NULL, 1, '2025-11-01 06:04:07', 0, 'Pending'),
(213, 236, 130, 95, 'booking_received', NULL, 1, '2025-11-03 12:21:10', 0, 'Pending'),
(227, 236, 130, 96, 'booking_received', NULL, 1, '2025-11-03 19:16:43', 0, 'Pending'),
(244, 236, 130, 97, 'booking_received', NULL, 1, '2025-11-06 06:34:37', 0, 'Pending'),
(246, 236, 130, 158, 'emergency_received', NULL, 1, '2025-11-06 06:40:11', 0, 'pending'),
(251, 278, 0, 142, 'application', NULL, 1, '2025-11-13 12:14:44', 0, 'pending'),
(252, 278, 0, 142, 'application_approved', NULL, 1, '2025-11-13 12:15:21', 0, 'Approved'),
(271, 289, 0, 51, 'verification', NULL, 1, '2025-12-07 03:26:51', 0, 'pending'),
(272, 289, 0, 51, 'verification_verified', NULL, 1, '2025-12-07 03:28:28', 0, 'verified'),
(273, 288, 142, 161, 'emergency_sent', NULL, 1, '2026-02-04 04:41:40', 0, 'pending'),
(274, 278, 142, 161, 'emergency_received', NULL, 1, '2026-02-04 04:41:40', 0, 'pending'),
(275, 278, 142, 161, 'emergency_cancelled', NULL, 1, '2026-02-04 04:41:46', 1, 'cancelled'),
(279, 278, 142, 99, 'booking_received', NULL, 1, '2026-03-06 03:10:03', 0, 'Pending'),
(283, 278, 142, 162, 'emergency_received', NULL, 1, '2026-03-06 03:15:18', 0, 'pending'),
(285, 278, 142, 99, 'booking_cancelled', NULL, 1, '2026-03-06 03:16:42', 1, 'Cancelled'),
(288, 289, 142, 100, 'booking_sent', NULL, 1, '2025-12-09 01:19:25', 0, 'Pending'),
(289, 278, 142, 100, 'booking_received', NULL, 1, '2026-03-11 00:19:25', 0, 'Pending'),
(290, 289, 142, 100, 'booking_accepted', NULL, 1, '2025-12-09 02:19:25', 0, 'Accept'),
(291, 289, 142, 163, 'emergency_sent', NULL, 1, '2026-03-11 00:27:04', 0, 'pending'),
(292, 278, 142, 163, 'emergency_received', NULL, 1, '2026-03-11 00:27:04', 0, 'pending'),
(293, 289, 142, 163, 'emergency_accepted', NULL, 1, '2026-03-11 00:28:51', 0, 'accepted'),
(294, 289, 142, 163, 'emergency_completed', NULL, 1, '2026-03-11 00:30:15', 0, 'completed'),
(295, 293, 0, 53, 'verification', NULL, 1, '2026-01-10 01:44:49', 0, 'pending'),
(296, 293, 0, 53, 'verification_verified', NULL, 1, '2026-01-10 03:16:49', 0, 'verified'),
(297, 289, 142, 100, 'booking_completed', NULL, 0, '2025-12-10 07:32:00', 0, 'Completed'),
(298, 293, 142, 101, 'booking_sent', NULL, 1, '2026-01-12 01:30:09', 0, 'Pending'),
(299, 278, 142, 101, 'booking_received', NULL, 1, '2026-03-11 01:30:09', 0, 'Pending'),
(300, 293, 142, 101, 'booking_accepted', NULL, 1, '2026-01-12 03:30:09', 0, 'Accept'),
(301, 293, 142, 101, 'booking_completed', NULL, 1, '2026-01-13 07:32:00', 0, 'Completed'),
(315, 298, 142, 165, 'emergency_sent', NULL, 0, '2026-03-11 08:42:17', 0, 'pending'),
(316, 278, 142, 165, 'emergency_received', NULL, 0, '2026-03-11 08:42:17', 0, 'pending'),
(317, 300, 0, 54, 'verification', NULL, 0, '2026-03-28 11:23:32', 0, 'pending'),
(318, 300, 0, 54, 'verification_verified', NULL, 0, '2026-03-28 11:23:50', 0, 'verified');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verified` tinyint(1) DEFAULT 0,
  `purpose` varchar(20) NOT NULL DEFAULT 'verification'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`id`, `user_id`, `email`, `otp`, `created_at`, `expires_at`, `verified`, `purpose`) VALUES
(390, 285, 'toge.arabit.ui@phinmaed.com', '129c147081ee41a6c10865c84d488314bdeb2c502149be143ffffdfc924fe3fb', '2025-12-21 13:14:17', '2025-12-21 13:15:14', 1, 'verification'),
(392, 295, 'renieljohnt@gmail.com', 'a1130a635f34fbf99352ad6fe56d8bacc8dd979830c4d20bac79819978ad7b9c', '2026-03-11 01:40:39', '2026-03-11 01:41:01', 1, 'verification'),
(393, 297, 'pastillosojrhenry@gmail.com', '4f6bcb835f45d50d4be2fd2da4258e5186dad86f326776c264644dd6c0bbe938', '2026-03-11 05:21:55', '2026-03-11 05:22:28', 1, 'verification');

-- --------------------------------------------------------

--
-- Table structure for table `push_subscriptions`
--

CREATE TABLE `push_subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `endpoint` text NOT NULL,
  `p256dh` varchar(255) NOT NULL,
  `auth` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `push_subscriptions`
--

INSERT INTO `push_subscriptions` (`id`, `user_id`, `endpoint`, `p256dh`, `auth`, `created_at`) VALUES
(20, 248, 'https://fcm.googleapis.com/fcm/send/cje9wwbV5YE:APA91bHBM6RddnHHoV2_3aR8IUw49rF_BPfED_q--ZqmX9SsXzerpXVDZvJBMrmwvT2CUDjUz1hHLjthRXJINHKBQIf4WQmjuBQz01eqRJ_zBiPS0vDRC2NcManMC5L2ePTqYxy2IpKi', 'BPS8I3KbCHA9-cTA7w4Fhqt3PWekhsbJJKK03UpKbVXo1bOKa1lsBdGzgodHkUJ1m7I6n-jVUrYpm3iglEM1TJE', '54afWPeAFhNL6nkq5DDuMg', '2025-11-13 13:37:02'),
(23, 236, 'https://fcm.googleapis.com/fcm/send/fcwwx9q-upA:APA91bG-83WJ7f4Hp49x5dyCuYpUFEt1-89VxaBU2AGPE1V3M-grVXXyiOa60nNjwfoH0MzvJK0EaADevkcKbOzREw5E9O0zlzDa8dq9h1JiM8AOSqZmOKDv6JxfnloSKHtnz-AwVyJv', 'BK2YvupHhGbvgxWaHdZ3hyX8IODPr_6SYsDNhXM6nsWA2EFY398_q6ENeOE9UkaOeIhN49vpSNaqEWSUSH0b7w4', 'aFu52y_icFUIq--b8NbKkw', '2025-11-17 16:59:59');

-- --------------------------------------------------------

--
-- Table structure for table `reactions`
--

CREATE TABLE `reactions` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction_type` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reactions`
--

INSERT INTO `reactions` (`id`, `message_id`, `user_id`, `reaction_type`, `created_at`, `updated_at`) VALUES
(77, 846, 236, 'sad', '2025-11-18 15:27:28', '2025-11-18 15:27:28');

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `device_info` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `user_id`, `token`, `expires_at`, `device_info`) VALUES
(32, 269, 'e3baf177a6b1979f219823d41b9976df9eeb81d54740ec0202acc10a232e08ec', '2025-11-27 19:01:13', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1'),
(34, 236, '0e05bf8941cea868b50c5ea6d300a040c8df02907eb53c36325fcfc602f91275', '2025-11-29 01:31:35', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36'),
(35, 236, '0cc41ba4617790fa71b944b2bbc98e0ae07c48be90bc0e22b5c75abcc2fc67e1', '2025-11-30 00:30:40', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36'),
(36, 236, '3926971d985717c4e3e85254c8e916958a447284580e929ef35a1e9a758adc9d', '2025-11-30 00:31:18', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36'),
(38, 236, 'fc324caa37ea4044c7847a0397b3753e48ee436841a0d1da6d786282a6b587d8', '2025-11-30 14:44:43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(43, 236, '3e924feaf5081c63f736cf3be27fcc61c017502413f645181c2c480d9c8adb89', '2025-12-21 17:40:20', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(45, 236, 'c6a1abb315992e006ec8f7be3e450b03edbd6922203b55d5e866551d342e05fd', '2025-12-12 11:22:00', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(46, 236, '73273a7d725f5198dc7cacf81cdb4f39e4796b2a082adcc1be320c381d72ced0', '2025-12-11 11:47:28', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36'),
(48, 278, '5957203c9335fe6031d8855c4e9cb03f333b0815002522c56b7b360ae3781a45', '2025-12-13 20:05:21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(49, 278, 'd2e363f9a35b6b6557b8311751688776ffb5bbbbf8668cc8f759fc5b71b74bc5', '2025-12-13 20:43:48', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36'),
(50, 278, 'b06b4525d03594660230ab6dc87fbd3bf72c01a392f3aaff3b7a1139af6c64ed', '2025-12-13 22:29:28', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(51, 278, 'a63188f1d1c7c9471bc1c3d41931c548b11a9ff7098ffae786b39427d433bfc9', '2025-12-13 23:27:50', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36'),
(53, 278, '29e71b9a3b234ffbb4d907e8d36e0925c478986b484385a21b35cbb784683864', '2025-12-14 10:31:23', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36'),
(54, 278, '8de21cd602843d8c0f3e42320dd14b68c91dada6e8f075750b6bb586fef59013', '2025-12-14 10:32:01', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36'),
(57, 236, 'bf6298b3d09ef32719b08f59bab6123cf373fefcdd934f17bc5d181c7209be82', '2025-12-18 14:45:13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(58, 236, '52dc2f979ce1a75d517f0f2e2ecb80d8ca6a1e88aeff0f3433383346e30d3ee1', '2025-12-17 16:21:50', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36'),
(59, 236, '92dc8195cfc238df85532a3f0a57fa8fe19b7882f5b494260c82485af0e58d41', '2025-12-17 16:26:33', 'Mozilla/5.0 (Linux; Android 13; V2124) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/123.0.6312.118 Mobile Safari/537.36 VivoBrowser/14.9.0.4'),
(60, 236, '590610478ac36f9a70b7d59368734e5c1af1b0af55321dfc34af6e33f4206a50', '2025-12-18 17:50:41', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36'),
(61, 236, '9a5ee3e72667ff08833842c8d76957dfb159bee5229d09e1619eddbc632fd531', '2025-12-18 15:39:51', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(62, 236, 'ab4844588dc05715440bad5c0f98b84b6284286f810aa0f770e3a4614ac2d6e5', '2026-01-20 09:37:52', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(63, 278, '6124756f553270a0dd606e95c7c66ac7b56d97c5fa708f8a432142cfc8ff0732', '2026-03-03 17:58:40', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36'),
(64, 278, 'cea8377011304acbeb5e60ac6d3a9959da6d2dd3e2833735e00d91c79ff5b410', '2026-03-06 13:10:10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36'),
(65, 278, '65b8d057547053fb629b0be55bcd3d9e5c7c31e57f6f68c3245a93dea412c5d3', '2026-04-05 11:11:43', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36'),
(66, 278, 'fff700b381aa606adf4b2141ef279d7426ff84977b0a3e73dfdf90c789d57150', '2026-04-10 09:15:56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','reviewed','resolved') DEFAULT 'pending',
  `response` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `respond_reviews`
--

CREATE TABLE `respond_reviews` (
  `id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `shop_owner_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `response` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_likes`
--

CREATE TABLE `review_likes` (
  `id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `liked_by_user_id` int(11) NOT NULL,
  `review_owner_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `save_shops`
--

CREATE TABLE `save_shops` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `save_shops`
--

INSERT INTO `save_shops` (`id`, `user_id`, `shop_id`, `saved_at`) VALUES
(256, 208, 113, '2025-09-03 06:20:37'),
(262, 211, 114, '2025-09-17 04:48:52');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `query_term` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `subcategory_id`, `name`, `slug`, `query_term`) VALUES
(1, 1, 'Oil Change', 'oil-change', 'Oil Change'),
(12, 1, 'Transmission Fluid Change', 'transmission-fluid-change', 'Transmission Fluid Change'),
(21, 1, 'Brake Fluid Change', 'brake-fluid-change', 'Brake Fluid Change'),
(32, 1, 'Coolant Change', 'coolant-change', 'Coolant Change'),
(43, 1, 'Power Steering Fluid Change', 'power-steering-fluid-change', 'Power Steering Fluid Change'),
(52, 2, 'Air Filter Replacement', 'air-filter-replacement', 'Air Filter Replacement'),
(63, 2, 'Fuel Filter Replacement', 'fuel-filter-replacement', 'Fuel Filter Replacement'),
(74, 2, 'Cabin Filter Replacement', 'cabin-filter-replacement', 'Cabin Filter Replacement'),
(83, 3, 'Spark Plug Replacement', 'spark-plug-replacement', 'Spark Plug Replacement'),
(93, 3, 'Engine Tune-Up', 'engine-tune-up', 'Engine Tune-Up'),
(104, 4, 'Engine Overhaul', 'engine-overhaul', 'Engine Overhaul'),
(114, 4, 'Cylinder Head Repair', 'cylinder-head-repair', 'Cylinder Head Repair'),
(124, 4, 'Timing Belt/Chain Replacement', 'timing-belt-chain-replacement', 'Timing Belt/Chain Replacement'),
(134, 5, 'Brake System Check & Diagnosis', 'brake-system-check-diagnosis', 'Brake System Check & Diagnosis'),
(143, 6, 'Brake Pad Replacement', 'brake-pad-replacement', 'Brake Pad Replacement'),
(152, 6, 'Brake Shoe Replacement', 'brake-shoe-replacement', 'Brake Shoe Replacement'),
(161, 6, 'Brake Rotor/Disc Replacement', 'brake-rotor-disc-replacement', 'Brake Rotor/Disc Replacement'),
(170, 6, 'Brake Drum Replacement', 'brake-drum-replacement', 'Brake Drum Replacement'),
(179, 6, 'Brake Caliper Replacement', 'brake-caliper-replacement', 'Brake Caliper Replacement'),
(188, 7, 'Brake Fluid Flush & Replacement', 'brake-fluid-flush-replacement', 'Brake Fluid Flush & Replacement'),
(197, 7, 'Handbrake / Parking Brake Adjustment', 'handbrake-parking-brake-adjustment', 'Handbrake / Parking Brake Adjustment'),
(206, 7, 'ABS (Anti-lock Braking System) Diagnosis & Repair', 'abs-anti-lock-braking-system-diagnosis-repair', 'ABS Diagnosis & Repair'),
(215, 8, 'Suspension System Check & Diagnosis', 'suspension-system-check-diagnosis', 'Suspension System Check & Diagnosis'),
(224, 9, 'Shock Absorber Replacement', 'shock-absorber-replacement', 'Shock Absorber Replacement'),
(234, 9, 'Strut Mount Replacement', 'strut-mount-replacement', 'Strut Mount Replacement'),
(243, 10, 'Steering Rack Repair', 'steering-rack-repair', 'Steering Rack Repair'),
(252, 10, 'Power Steering Fluid Flush & Replacement', 'power-steering-fluid-flush-replacement', 'Power Steering Fluid Flush & Replacement'),
(261, 10, 'Steering Rack & Pinion Repair/Replacement', 'steering-rack-pinion-repair-replacement', 'Steering Rack & Pinion Repair/Replacement'),
(270, 10, 'Tie Rod End Replacement', 'tie-rod-end-replacement', 'Tie Rod End Replacement'),
(279, 10, 'Ball Joint/Control Arm Replacement', 'ball-joint-control-arm-replacement', 'Ball Joint/Control Arm Replacement'),
(288, 11, 'Wheel Alignment (Computerized)', 'wheel-alignment-computerized', 'Wheel Alignment (Computerized)'),
(297, 11, 'Wheel Balancing', 'wheel-balancing', 'Wheel Balancing'),
(306, 12, 'Clutch Replacement', 'clutch-replacement', 'Clutch Replacement'),
(316, 12, 'Transmission Repair', 'transmission-repair', 'Transmission Repair'),
(326, 12, 'Differential Repair', 'differential-repair', 'Differential Repair'),
(335, 13, 'Transmission Fluid Change', 'transmission-fluid-change-all-vehicle-types-2', 'Transmission Fluid Change'),
(344, 13, 'Transmission Fluid Flush (Automatic/Manual)', 'transmission-fluid-flush-automatic-manual', 'Transmission Fluid Flush'),
(353, 14, 'Clutch Adjustment & Inspection', 'clutch-adjustment-inspection', 'Clutch Adjustment & Inspection'),
(362, 15, 'Manual Transmission Repair', 'manual-transmission-repair', 'Manual Transmission Repair'),
(371, 15, 'Automatic Transmission Repair', 'automatic-transmission-repair', 'Automatic Transmission Repair'),
(380, 15, 'Transmission Overhaul', 'transmission-overhaul', 'Transmission Overhaul'),
(389, 15, 'Torque Converter Repair', 'torque-converter-repair', 'Torque Converter Repair'),
(398, 15, 'Transmission Replacement', 'transmission-replacement', 'Transmission Replacement'),
(407, 16, 'Differential Fluid Change', 'differential-fluid-change', 'Differential Fluid Change'),
(416, 16, 'Differential Repair/Overhaul', 'differential-repair-overhaul', 'Differential Repair/Overhaul'),
(425, 16, 'Driveshaft & CV Joint Repair', 'driveshaft-cv-joint-repair', 'Driveshaft & CV Joint Repair'),
(434, 17, 'AC System Check & Diagnosis', 'ac-system-check-diagnosis', 'AC System Check & Diagnosis'),
(443, 17, 'AC Gas Recharge (Freon Refill)', 'ac-gas-recharge-freon-refill', 'AC Gas Recharge'),
(452, 17, 'AC Leak Test & Repair', 'ac-leak-test-repair', 'AC Leak Test & Repair'),
(461, 17, 'AC Hose & Leak Repair', 'ac-hose-leak-repair', 'AC Hose & Leak Repair'),
(470, 17, 'AC Leak Detection & Seal', 'ac-leak-detection-seal', 'AC Leak Detection & Seal'),
(479, 18, 'AC Compressor Replacement', 'ac-compressor-replacement', 'AC Compressor Replacement'),
(488, 18, 'AC Condenser Replacement', 'ac-condenser-replacement', 'AC Condenser Replacement'),
(497, 18, 'AC Evaporator Replacement', 'ac-evaporator-replacement', 'AC Evaporator Replacement'),
(506, 18, 'AC Expansion Valve Replacement', 'ac-expansion-valve-replacement', 'AC Expansion Valve Replacement'),
(515, 19, 'AC Filter (Cabin Filter) Replacement', 'ac-filter-cabin-filter-replacement', 'AC Filter (Cabin Filter) Replacement'),
(524, 19, 'Heater System Repair', 'heater-system-repair', 'Heater System Repair'),
(533, 19, 'Heater Core Repair/Replacement', 'heater-core-repair-replacement', 'Heater Core Repair/Replacement'),
(542, 19, 'Blower Motor Replacement', 'blower-motor-replacement', 'Blower Motor Replacement'),
(551, 19, 'Climate Control System Calibration', 'climate-control-system-calibration', 'Climate Control System Calibration'),
(560, 19, 'AC System Cleaning (Flushing)', 'ac-system-cleaning-flushing', 'AC System Cleaning (Flushing)'),
(569, 19, 'AC Vent Cleaning & Sanitizing', 'ac-vent-cleaning-sanitizing', 'AC Vent Cleaning & Sanitizing'),
(578, 19, 'A/C System Retrofit (Old → New Refrigerant)', 'a-c-system-retrofit-old-new-refrigerant', 'A/C System Retrofit'),
(587, 20, 'Muffler Repair & Replacement', 'muffler-repair-replacement', 'Muffler Repair Replacement'),
(596, 20, 'Exhaust Pipe Repair', 'exhaust-pipe-repair', 'Exhaust Pipe Repair'),
(605, 20, 'Catalytic Converter Services', 'catalytic-converter-services', 'Catalytic Converter'),
(610, 21, 'Radiator Repair & Replacement', 'radiator-repair-replacement', 'Radiator Repair Replacement'),
(619, 21, 'Water Pump Replacement', 'water-pump-replacement', 'Water Pump Replacement'),
(624, 21, 'Thermostat Replacement', 'thermostat-replacement', 'Thermostat Replacement'),
(628, 22, 'Fuel Pump Replacement', 'fuel-pump-replacement', 'Fuel Pump Replacement'),
(633, 22, 'Fuel Injector Cleaning', 'fuel-injector-cleaning', 'Fuel Injector Cleaning'),
(638, 22, 'Carburetor Cleaning & Tuning', 'carburetor-cleaning-tuning', 'Carburetor Cleaning Tuning'),
(641, 23, 'ECU Remapping / Chiptuning', 'ecu-remapping-chiptuning', 'ECU Remapping Chiptuning'),
(644, 23, 'Performance Exhaust System Installation', 'performance-exhaust-system-installation', 'Performance Exhaust Installation'),
(648, 23, 'Turbo/Supercharger Installation & Repair', 'turbo-supercharger-installation-repair', 'Turbo Supercharger Installation'),
(651, 24, 'Coilover / Lowering Spring Installation', 'coilover-lowering-spring-installation', 'Coilover Lowering Spring'),
(654, 24, 'Big Brake Kit Installation', 'big-brake-kit-installation', 'Big Brake Kit Installation'),
(657, 25, 'Car Stereo & Head Unit Installation', 'car-stereo-head-unit-installation', 'Car Stereo Head Unit Installation'),
(661, 25, 'Car Alarm Installation', 'car-alarm-installation', 'Car Alarm Installation'),
(665, 25, 'GPS Tracker Installation', 'gps-tracker-installation', 'GPS Tracker Installation'),
(669, 26, 'Backup Camera Installation', 'backup-camera-installation', 'Backup Camera Installation'),
(673, 26, 'Parking Sensor Installation', 'parking-sensor-installation', 'Parking Sensor Installation'),
(677, 27, 'Window Tinting', 'window-tinting', 'Window Tinting'),
(681, 27, 'Body Kit & Spoiler Installation', 'body-kit-spoiler-installation', 'Body Kit Spoiler Installation'),
(684, 28, 'Towing Service', 'towing-service', 'Towing'),
(690, 28, 'Flatbed Towing', 'flatbed-towing', 'Flatbed Towing'),
(694, 29, 'On-site Battery Jumpstart', 'on-site-battery-jumpstart', 'Battery Jumpstart'),
(698, 29, 'On-site Flat Tire Assistance', 'on-site-flat-tire-assistance', 'Flat Tire Assistance'),
(702, 29, 'Vehicle Lockout Service', 'vehicle-lockout-service', 'Vehicle Lockout'),
(706, 30, 'Pre-Purchase Vehicle Inspection', 'pre-purchase-vehicle-inspection', 'Pre-Purchase Inspection'),
(710, 30, 'Smoke Emission Testing', 'smoke-emission-testing', 'Smoke Emission Testing'),
(719, 31, 'LTO Registration Renewal Assistance', 'lto-registration-renewal-assistance', 'LTO Registration Assistance'),
(724, 31, 'Transfer of Ownership Assistance', 'transfer-of-ownership-assistance', 'Transfer of Ownership'),
(728, 32, 'Battery Replacement', 'battery-replacement', 'Battery Replacement'),
(738, 32, 'Alternator Repair/Testing/Replacement', 'alternator-repair-testing-replacement', 'Alternator Repair'),
(747, 32, 'Starter Motor Repair/Testing/Replacement', 'starter-motor-repair-testing-replacement', 'Starter Motor Repair'),
(757, 32, 'Battery Terminal & Cable Repair', 'battery-terminal-cable-repair', 'Battery Terminal & Cable Repair'),
(758, 32, 'Voltage Regulator Testing & Repair', 'voltage-regulator-testing-repair', 'Voltage Regulator Testing & Repair'),
(759, 33, 'Headlight & Taillight Replacement', 'headlight-taillight-replacement', 'Headlight & Taillight Replacement'),
(769, 33, 'Wiring Repair & Harness Replacement', 'wiring-repair-harness-replacement', 'Wiring Repair & Harness Replacement'),
(779, 33, 'Fuse & Relay Replacement', 'fuse-relay-replacement', 'Fuse & Relay Replacement'),
(780, 33, 'Interior Lighting Repair', 'interior-lighting-repair', 'Interior Lighting Repair'),
(781, 33, 'LED/HID Headlight Installation', 'led-hid-headlight-installation', 'LED/HID Headlight Installation'),
(782, 33, 'Power Window Repair', 'power-window-repair', 'Power Window Repair'),
(783, 33, 'Car Horn Installation/Repair', 'car-horn-installation-repair', 'Car Horn Installation/Repair'),
(784, 33, 'Dashboard Electronics Repair', 'dashboard-electronics-repair', 'Dashboard Electronics Repair'),
(785, 33, 'Electronic Accessories Installation (Dashcam, GPS, etc.)', 'electronic-accessories-installation-dashcam-gps-etc', 'Electronic Accessories Installation'),
(786, 34, 'ECU/Computer Diagnostics / OBD Scanning', 'ecu-computer-diagnostics-obd-scanning', 'ECU/Computer Diagnostics'),
(796, 34, 'Check Engine Light Diagnostics', 'check-engine-light-diagnostics', 'Check Engine Light Diagnostics'),
(797, 34, 'Electrical System Troubleshooting (Shorts, Fuses, Relays)', 'electrical-system-troubleshooting-shorts-fuses-relays', 'Electrical System Troubleshooting'),
(798, 34, 'Sensor Replacement (O2, ABS, TPS, etc.)', 'sensor-replacement-o2-abs-tps-etc', 'Sensor Replacement'),
(799, 34, 'Diesel Engine Diagnostics', 'diesel-engine-diagnostics', 'Diesel Engine Diagnostics'),
(800, 34, 'Hybrid/Electric Vehicle Diagnostics', 'hybrid-electric-vehicle-diagnostics', 'Hybrid/Electric Vehicle Diagnostics'),
(801, 35, 'ECU/PCM Reprogramming', 'ecu-pcm-reprogramming', 'ECU/PCM Reprogramming'),
(810, 35, 'Transmission Control Module Programming', 'transmission-control-module-programming', 'Transmission Control Module Programming'),
(819, 35, 'ABS Module Programming', 'abs-module-programming', 'ABS Module Programming'),
(828, 36, 'Immobilizer & Key Programming (Transponder/Smart Key)', 'immobilizer-key-programming-transponder-smart-key', 'Immobilizer & Key Programming'),
(837, 36, 'Immobilizer System Reset', 'immobilizer-system-reset', 'Immobilizer System Reset'),
(846, 37, 'Tire Replacement & Installation', 'tire-replacement-installation', 'Tire Replacement & Installation'),
(855, 38, 'Wheel Balancing', 'wheel-balancing-all-vehicle-types-2', 'Wheel Balancing'),
(864, 38, 'Tire Rotation', 'tire-rotation', 'Tire Rotation'),
(873, 39, '2-Wheel Alignment', '2-wheel-alignment', '2-Wheel Alignment'),
(882, 39, '4-Wheel Alignment', '4-wheel-alignment', '4-Wheel Alignment'),
(891, 40, 'Flat Tire Repair (Puncture, Patch)', 'flat-tire-repair-puncture-patch', 'Flat Tire Repair'),
(900, 41, 'Wheel & Rim Replacement', 'wheel-rim-replacement', 'Wheel & Rim Replacement'),
(909, 41, 'Alloy Wheel Repair/Refinishing', 'alloy-wheel-repair-refinishing', 'Alloy Wheel Repair/Refinishing'),
(918, 42, 'Exterior Wash (Basic/Regular)', 'exterior-wash-basic-regular', 'Exterior Wash'),
(927, 42, 'Interior Cleaning (Vacuum/Wipe Down)', 'interior-cleaning-vacuum-wipe-down', 'Interior Cleaning'),
(936, 42, 'Engine Wash & Degreasing', 'engine-wash-degreasing', 'Engine Wash & Degreasing'),
(945, 43, 'Full Detailing (Interior & Exterior)', 'full-detailing-interior-exterior', 'Full Detailing'),
(954, 43, 'Interior Detailing (Deep Cleaning)', 'interior-detailing-deep-cleaning', 'Interior Detailing'),
(963, 43, 'Exterior Detailing (Polish/Wax)', 'exterior-detailing-polish-wax', 'Exterior Detailing'),
(972, 44, 'Paint Polishing & Buffing', 'paint-polishing-buffing', 'Paint Polishing & Buffing'),
(981, 44, 'Ceramic Coating Application', 'ceramic-coating-application', 'Ceramic Coating Application'),
(990, 44, 'Paint Protection Film (PPF) Installation', 'paint-protection-film-ppf-installation', 'Paint Protection Film (PPF) Installation'),
(999, 45, 'Glass Polishing & Watermark Removal', 'glass-polishing-watermark-removal', 'Glass Polishing & Watermark Removal'),
(1008, 45, 'Upholstery Shampoo & Cleaning', 'upholstery-shampoo-cleaning', 'Upholstery Shampoo & Cleaning'),
(1017, 45, 'Leather Seat Conditioning & Protection', 'leather-seat-conditioning-protection', 'Leather Seat Conditioning & Protection'),
(1026, 46, 'Dent Removal (Paintless Dent Repair)', 'dent-removal-paintless-dent-repair', 'Dent Removal'),
(1035, 46, 'Panel Beating & Straightening', 'panel-beating-straightening', 'Panel Beating Straightening'),
(1044, 46, 'Frame Straightening', 'frame-straightening', 'Frame Straightening'),
(1052, 47, 'Full Body Repaint', 'full-body-repaint', 'Full Body Repaint'),
(1061, 47, 'Partial Panel Repainting', 'partial-panel-repainting', 'Partial Panel Repainting'),
(1070, 47, 'Spot Painting & Touch-up', 'spot-painting-touch-up', 'Spot Painting Touch-up'),
(1075, 47, 'Color Matching Services', 'color-matching-services', 'Color Matching'),
(1078, 48, 'Minor Scratch Repair', 'minor-scratch-repair', 'Minor Scratch Repair'),
(1083, 48, 'Major Collision Repair', 'major-collision-repair', 'Major Collision Repair'),
(1091, 48, 'Bumper Repair & Replacement', 'bumper-repair-replacement', 'Bumper Repair Replacement'),
(1096, 49, 'Windshield Replacement & Repair', 'windshield-replacement-repair', 'Windshield Replacement Repair'),
(1104, 49, 'Undercoating / Underbody Protection', 'undercoating-underbody-protection', 'Undercoating'),
(1112, 49, 'Rust Repair & Treatment', 'rust-repair-treatment', 'Rust Repair Treatment'),
(1120, 50, 'Matte Finish Painting', 'matte-finish-painting', 'Matte Finish Painting'),
(1124, 50, 'Pearlescent/Metallic Painting', 'pearlescent-metallic-painting', 'Pearlescent Metallic Painting'),
(1128, 50, 'Custom Graphics & Design', 'custom-graphics-design', 'Custom Graphics Design'),
(1133, 9, 'Rubber Damper Replacement', 'rubber-damper-replacement', 'Rubber Damper Replacement'),
(1134, 9, 'Dust Boot/Cover Replacement', 'dust-boot-cover-replacement', 'Dust Boot/Cover Replacement'),
(1135, 9, 'Suspension Bushing Replacement', 'suspension-bushing-replacement', 'Suspension Bushing Replacement');

-- --------------------------------------------------------

--
-- Table structure for table `services_booking`
--

CREATE TABLE `services_booking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `vehicle_make` varchar(50) NOT NULL,
  `vehicle_model` varchar(50) NOT NULL,
  `vehicle_year` int(11) NOT NULL,
  `transmission_type` varchar(20) NOT NULL,
  `fuel_type` varchar(20) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `vehicle_type` varchar(255) DEFAULT NULL,
  `plate_number` varchar(20) DEFAULT NULL,
  `preferred_datetime` text DEFAULT NULL,
  `vehicle_issues` text DEFAULT NULL,
  `customer_notes` text DEFAULT NULL,
  `booking_status` enum('Pending','Accept','Reject','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `delete_notification` tinyint(1) DEFAULT 0,
  `is_read` tinyint(1) DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `reminder_sent` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services_booking`
--

INSERT INTO `services_booking` (`id`, `user_id`, `shop_id`, `customer_name`, `customer_phone`, `customer_email`, `vehicle_make`, `vehicle_model`, `vehicle_year`, `transmission_type`, `fuel_type`, `service_type`, `vehicle_type`, `plate_number`, `preferred_datetime`, `vehicle_issues`, `customer_notes`, `booking_status`, `created_at`, `updated_at`, `is_deleted`, `completed_at`, `cancelled_at`, `delete_notification`, `is_read`, `deleted_at`, `reminder_sent`) VALUES
(100, 289, 142, 'Jhon Rey Gallofin', '09151861763', 'gallofinjhonrey18@gmail.com', 'Honda', 'Honda Civic', 2025, 'Automatic', 'Diesel', '[\"Brake Fluid Change\",\"Coolant Change\",\"Engine Tun', 'Sedan', 'FNH452', '12/10/2025, 8:00 AM - 10:00 AM', 'For Maintenance', '', 'Completed', '2025-12-09 01:19:25', '2025-12-09 02:29:47', 0, '2025-12-10 11:00:00', NULL, 0, 1, NULL, 0),
(101, 293, 142, 'Ryan A. Macalalag', '09982848621', 'ryanmacalalag@gmail.com', 'Suzuki', 'Raider125 Fi', 2020, 'Manual', 'Gasoline', '[\"Oil Change\"]', 'Motorcycle', 'XYZ 3345', '01/13/2026, 1:00 PM - 3:00 PM', 'Change oil because almost 1 month i dont have change oil', '', 'Completed', '2026-01-12 01:30:09', '2026-03-11 02:59:53', 0, '2026-01-13 05:00:00', NULL, 0, 1, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `service_categories`
--

CREATE TABLE `service_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `icon` varchar(255) NOT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_categories`
--

INSERT INTO `service_categories` (`id`, `name`, `slug`, `icon`, `display_order`) VALUES
(1, 'Preventive Maintenance', 'preventive-maintenance', 'fa-clipboard-check', 10),
(2, 'Mechanical Work', 'mechanical-work', 'fa-cogs', 20),
(3, 'Brake System', 'brake-system', 'fa-car-crash', 30),
(4, 'Suspension & Steering System', 'suspension-steering-system', 'fa-bullseye', 40),
(5, 'Transmission & Clutch Services', 'transmission-clutch-services', 'fa-grip-horizontal', 50),
(6, 'Air Conditioning (AC) & Heating System', 'air-conditioning-ac-heating-system', 'fa-fan', 60),
(7, 'Exhaust, Cooling, & Fuel Systems', 'exhaust-cooling-fuel-systems', 'fa-wind', 70),
(8, 'Performance Upgrades & Modifications', 'performance-upgrades-modifications', 'fa-rocket', 80),
(9, 'Accessories Installation', 'accessories-installation', 'fa-puzzle-piece', 90),
(10, 'Towing & Roadside Assistance', 'towing-roadside-assistance', 'fa-truck-moving', 100),
(11, 'Vehicle Inspection & Registration', 'vehicle-inspection-registration', 'fa-file-signature', 110),
(12, 'Electrical & Electronics Services', 'electrical-electronics-services', 'fa-bolt', 120),
(13, 'Computer Diagnostics & Programming', 'computer-diagnostics-programming', 'fa-laptop-code', 130),
(14, 'Tires & Wheels Services', 'tires-wheels-services', 'fa-dot-circle', 140),
(15, 'Detailing & Car Wash Services', 'detailing-car-wash-services', 'fa-spray-can-sparkles', 150),
(16, 'Body Repair & Painting', 'body-repair-painting', 'fa-paint-roller', 160);

-- --------------------------------------------------------

--
-- Table structure for table `service_subcategories`
--

CREATE TABLE `service_subcategories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_subcategories`
--

INSERT INTO `service_subcategories` (`id`, `category_id`, `name`, `slug`) VALUES
(1, 1, 'Oil & Fluid Services', 'oil-fluid-services'),
(2, 1, 'Filter Services', 'filter-services'),
(3, 1, 'Tune-Up', 'tune-up'),
(4, 2, 'Engine Repair', 'engine-repair'),
(5, 3, 'Brake Inspection & Diagnosis', 'brake-inspection-diagnosis'),
(6, 3, 'Brake Component Replacement', 'brake-component-replacement'),
(7, 3, 'Brake Fluids & System Maintenance', 'brake-fluids-system-maintenance'),
(8, 4, 'Suspension Inspection & Diagnosis', 'suspension-inspection-diagnosis'),
(9, 4, 'Shock Absorbers & Struts', 'shock-absorbers-struts'),
(10, 4, 'Steering System', 'steering-system'),
(11, 4, 'Alignment & Balancing', 'alignment-balancing'),
(12, 5, 'Transmission & Drivetrain', 'transmission-drivetrain'),
(13, 5, 'Transmission Fluid Service', 'transmission-fluid-service'),
(14, 5, 'Clutch Services', 'clutch-services'),
(15, 5, 'Transmission Repair & Overhaul', 'transmission-repair-overhaul'),
(16, 5, 'Differential & Driveshaft', 'differential-driveshaft'),
(17, 6, 'AC System Service & Diagnostics', 'ac-system-service-diagnostics'),
(18, 6, 'AC Component Replacement', 'ac-component-replacement'),
(19, 6, 'Climate Control & Accessories', 'climate-control-accessories'),
(20, 7, 'Exhaust System Services', 'exhaust-system-services'),
(21, 7, 'Cooling System Services', 'cooling-system-services'),
(22, 7, 'Fuel System Services', 'fuel-system-services'),
(23, 8, 'Performance Tuning & Installation', 'performance-tuning-installation'),
(24, 8, 'Suspension & Brake Upgrades', 'suspension-brake-upgrades'),
(25, 9, 'Audio, Video, & Security', 'audio-video-security'),
(26, 9, 'Cameras & Sensors', 'cameras-sensors'),
(27, 9, 'Exterior & Interior Accessories', 'exterior-interior-accessories'),
(28, 10, 'Towing Services', 'towing-services'),
(29, 10, 'Roadside Assistance', 'roadside-assistance'),
(30, 11, 'Inspection & Testing', 'inspection-testing'),
(31, 11, 'LTO & Insurance Assistance', 'lto-insurance-assistance'),
(32, 12, 'Battery & Charging System', 'battery-charging-system'),
(33, 12, 'Lighting, Wiring & Accessories', 'lighting-wiring-accessories'),
(34, 13, 'Diagnostics & Scanning', 'diagnostics-scanning'),
(35, 13, 'Programming & Updates', 'programming-updates'),
(36, 13, 'Key & Security Systems', 'key-security-systems'),
(37, 14, 'Tire Replacement & Installation', 'tire-replacement-installation'),
(38, 14, 'Tire Balancing & Rotation', 'tire-balancing-rotation'),
(39, 14, 'Wheel Alignment', 'wheel-alignment'),
(40, 14, 'Tire Repair', 'tire-repair'),
(41, 14, 'Wheels & Rims', 'wheels-rims'),
(42, 15, 'Car Wash & Cleaning', 'car-wash-cleaning'),
(43, 15, 'Detailing Services', 'detailing-services'),
(44, 15, 'Paint & Protection', 'paint-protection'),
(45, 15, 'Glass & Upholstery Care', 'glass-upholstery-care'),
(46, 16, 'Body Repair & Panel Work', 'body-repair-panel-work'),
(47, 16, 'Painting Services', 'painting-services'),
(48, 16, 'Collision Repair', 'collision-repair'),
(49, 16, 'Glass, Undercarriage & Rustproofing', 'glass-undercarriage-rustproofing'),
(50, 16, 'Specialty Finishes & Customization', 'specialty-finishes-customization');

-- --------------------------------------------------------

--
-- Table structure for table `shop_applications`
--

CREATE TABLE `shop_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_name` varchar(255) NOT NULL,
  `shop_slug` varchar(255) DEFAULT NULL,
  `business_type` varchar(20) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `years_operation` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `shop_logo` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) NOT NULL,
  `business_permit` varchar(255) NOT NULL,
  `business_permit_file` varchar(255) DEFAULT NULL,
  `tax_id` varchar(255) NOT NULL,
  `tax_id_file` varchar(255) NOT NULL,
  `dti_sec_number` text DEFAULT NULL,
  `dti_sec_file` varchar(255) DEFAULT NULL,
  `valid_id_type` varchar(255) NOT NULL,
  `valid_id_front` varchar(255) NOT NULL,
  `valid_id_back` varchar(255) DEFAULT NULL,
  `shop_gallery_images` text DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) NOT NULL,
  `town_city` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `shop_location` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) NOT NULL,
  `services_offered` text DEFAULT NULL,
  `brands_serviced` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `is_read_admin` tinyint(1) NOT NULL DEFAULT 0,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `business_hours` varchar(255) DEFAULT NULL,
  `opening_time_am` time DEFAULT NULL,
  `closing_time_am` time DEFAULT NULL,
  `opening_time_pm` time DEFAULT NULL,
  `closing_time_pm` time DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `open_24_7` tinyint(1) DEFAULT 0,
  `days_open` varchar(255) DEFAULT NULL,
  `show_book_now` tinyint(1) DEFAULT 0,
  `show_emergency` tinyint(1) DEFAULT 0,
  `shop_status` varchar(50) NOT NULL DEFAULT 'open',
  `seen_toggle_onboarding` tinyint(1) DEFAULT 0,
  `delete_notification` tinyint(1) DEFAULT 0,
  `seen_rejected_notification` tinyint(1) DEFAULT 0,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop_applications`
--

INSERT INTO `shop_applications` (`id`, `user_id`, `shop_name`, `shop_slug`, `business_type`, `owner_name`, `years_operation`, `email`, `shop_logo`, `phone`, `website`, `instagram`, `facebook`, `business_permit`, `business_permit_file`, `tax_id`, `tax_id_file`, `dti_sec_number`, `dti_sec_file`, `valid_id_type`, `valid_id_front`, `valid_id_back`, `shop_gallery_images`, `street`, `barangay`, `town_city`, `province`, `country`, `shop_location`, `postal_code`, `services_offered`, `brands_serviced`, `status`, `is_read`, `is_read_admin`, `applied_at`, `date_created`, `description`, `business_hours`, `opening_time_am`, `closing_time_am`, `opening_time_pm`, `closing_time_pm`, `latitude`, `longitude`, `approved_at`, `approved_by`, `updated_at`, `open_24_7`, `days_open`, `show_book_now`, `show_emergency`, `shop_status`, `seen_toggle_onboarding`, `delete_notification`, `seen_rejected_notification`, `opening_time`, `closing_time`) VALUES
(130, 236, 'Well Auto Repair Shop', 'well-auto-repair-shop', '', 'Louie Abello', 34, 'louieabello@yahoo.com', '68a30438ea6a5.jpg', '09177026908', '', '', '', '', '../uploads/business_permit/68f63f590a4212.98664059.jpg', '', '../uploads/tax_id/68f63f590a9f95.03058243.jpg', '', '', 'Philippine National ID', '../uploads/valid_id_front/68f63f590aef96.57887572.jpg', '', '[]', NULL, 'San Juan Molo', 'Iloilo City', 'Iloilo', 'Philippines', 'MGPW+P72, Baluarte - Calumpang - Villa - Oton Blvd, Molo, Iloilo City, 5000 Iloilo', '5000', NULL, 'All Vehicle Types', 'Approved', 1, 1, '2025-10-20 13:55:37', '2025-10-20 13:55:37', '', NULL, '08:00:00', '11:30:00', '13:30:00', '17:00:00', 10.68560980, 122.54488950, '2025-10-20 14:06:15', 3, '2025-10-20 14:06:15', 0, 'monday,tuesday,wednesday,thursday,friday,saturday,sunday', 1, 1, 'temporarily_closed', 1, 0, 0, NULL, NULL),
(142, 278, 'Marcel\'s Tires & Automobile Services', 'marcel-s-tires-automobile-services', '', 'Anthony Sullano', 15, 'marcel192223@gmail.com', 'logo_6915cc0d5c9dc5.12361787.jpg', '09923155697', '', '', 'https://www.facebook.com/Marcel2223', 'akYB5aWo6Vn5jbFMAo5ZYnhPQVZ6VU5KRXE1bkdqeU05ejNHR280V3haemlJeTNZUUFiL2NzaFN6WDg9', '../uploads/business_permit/6915cbb4916d90.30315527.png', 'h0ZDoCQ7D4vPWkKJTEVJw3JpY1VMcldyZG05ZExQSFVoc1FwSEE9PQ==', '../uploads/tax_id/6915cbb49235f4.83982672.png', 'HLHmY54hvrwZCE++cmdDvDNFRmZHZzF6MXpmRFJucHc4dVNldnZ3TTJTbDhwdXplR1VkMUlqVkdwRkk9', '../uploads/dti_sec/6915cbb4929416.84373752.png', 'Driver\'s License', '../uploads/valid_id_front/6915cbb4927713.84619933.webp', '', NULL, NULL, '', 'Iloilo City', 'Iloilo', 'Philippines', 'Q. Abeto St, Mandurriao, Iloilo City, Iloilo, Philippines', '5000', NULL, 'All Vehicle Types', 'Approved', 1, 1, '2025-11-13 12:14:44', '2025-11-13 12:14:44', 'Marcel\'s Offers Quality Service for your Convenience and comfortable experience for your Tyres, Tube, Automobile Maintenance Service, Diagnose and Troubleshooting.', NULL, '06:30:00', '11:59:00', '13:00:00', '23:00:00', 10.71805700, 122.54204340, '2025-11-13 12:15:20', 3, '2025-11-13 12:15:20', 0, 'monday,tuesday,wednesday,thursday,friday,saturday,sunday', 1, 1, 'open', 1, 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shop_auto_messages`
--

CREATE TABLE `shop_auto_messages` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `welcome_message` text DEFAULT NULL,
  `quick_replies` text DEFAULT NULL COMMENT 'JSON array of quick replies',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop_auto_messages`
--

INSERT INTO `shop_auto_messages` (`id`, `shop_id`, `user_id`, `welcome_message`, `quick_replies`, `created_at`, `updated_at`) VALUES
(9, 142, 278, 'Hi! ðŸ‘‹ Welcome to Marcelâ€™s Tires & Automobile Services. How can we assist you today?', '{\"options\":[{\"option_number\":1,\"label\":\"Services Offered\",\"response\":\"We offer tire replacement, wheel alignment, oil change, brake repair, engine check-up, and more!\",\"is_filled\":true},{\"option_number\":2,\"label\":\"Operating Hours\",\"response\":\"We\\u2019re open Monday to Sunday, 6:30 AM to 11:59 AM - 1:00 PM to 11:00 PM .\",\"is_filled\":true},{\"option_number\":3,\"label\":\"\",\"response\":\"\",\"is_filled\":false},{\"option_number\":4,\"label\":\"\",\"response\":\"\",\"is_filled\":false},{\"option_number\":5,\"label\":\"\",\"response\":\"\",\"is_filled\":false}],\"filled_options\":[1,2],\"total_filled\":2}', '2025-11-13 13:35:07', '2025-11-13 13:35:07'),
(10, 130, 236, 'Hello welcome to Well Auto Repair Shop, how can we help you?', '{\"options\":[{\"option_number\":1,\"label\":\"jjjds\",\"response\":\"dsdsds\",\"is_filled\":true},{\"option_number\":2,\"label\":\"\",\"response\":\"\",\"is_filled\":false},{\"option_number\":3,\"label\":\"\",\"response\":\"\",\"is_filled\":false},{\"option_number\":4,\"label\":\"\",\"response\":\"\",\"is_filled\":false},{\"option_number\":5,\"label\":\"\",\"response\":\"\",\"is_filled\":false}],\"filled_options\":[1],\"total_filled\":1}', '2025-11-18 07:19:59', '2025-11-21 09:41:41');

-- --------------------------------------------------------

--
-- Table structure for table `shop_booking_form`
--

CREATE TABLE `shop_booking_form` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `full_name` tinyint(4) NOT NULL DEFAULT 1,
  `email` tinyint(4) NOT NULL DEFAULT 1,
  `phone` tinyint(4) NOT NULL DEFAULT 1,
  `vehicle_details` varchar(255) NOT NULL,
  `vehicle_issues` text DEFAULT '1',
  `customer_notes` text DEFAULT '1',
  `transmission_types` text DEFAULT NULL,
  `fuel_types` text DEFAULT NULL,
  `service_types` text DEFAULT NULL,
  `business_hours` text DEFAULT NULL,
  `original_slot_counts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`original_slot_counts`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `vehicle_types` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop_booking_form`
--

INSERT INTO `shop_booking_form` (`id`, `user_id`, `shop_id`, `full_name`, `email`, `phone`, `vehicle_details`, `vehicle_issues`, `customer_notes`, `transmission_types`, `fuel_types`, `service_types`, `business_hours`, `original_slot_counts`, `created_at`, `vehicle_types`) VALUES
(18, 196, 102, 1, 1, 1, '', '1', '1', '[\"Automatic\"]', '[\"Diesel\"]', '[\"21\",\"32\",\"1\",\"43\",\"12\",\"114\",\"104\",\"124\",\"134\",\"179\",\"170\",\"143\",\"161\",\"152\",\"206\",\"188\",\"197\"]', '[\"Monday, 10:29 - 22:27 (5 slots)\"]', NULL, '2025-08-17 02:27:30', '[\"Truck\"]'),
(21, 203, 111, 1, 1, 1, '', '1', '1', '[\"manual\",\"automatic\"]', '[\"electric\",\"diesel\"]', '[\"21\",\"32\",\"1\",\"43\",\"12\",\"114\",\"104\",\"124\",\"134\",\"179\",\"170\",\"143\",\"161\",\"152\",\"206\",\"188\",\"197\"]', '[\"Monday, 08:00 - 10:30 (1 slots)\"]', NULL, '2025-08-19 00:17:05', '[\"SUV\",\"truck\"]'),
(29, 236, 130, 1, 1, 1, '', '1', '1', '[\"Automatic\",\"Manual\",\"Semi-Automatic\",\"CVT\"]', '[\"Gasoline\",\"Diesel\",\"Electric\"]', '[\"21\",\"32\",\"1\",\"43\",\"12\",\"114\",\"104\",\"124\",\"134\",\"179\",\"170\",\"143\",\"161\",\"152\",\"206\",\"188\",\"197\"]', '[\"10\\/21\\/2025, 08:00 - 17:00 (14 slots)\",\"10\\/23\\/2025, 08:00 - 17:00 (8 slots)\",\"10\\/24\\/2025, 08:00 - 17:00 (10 slots)\",\"10\\/25\\/2025, 08:00 - 17:00 (9 slots)\"]', NULL, '2025-10-20 14:19:11', '[\"SUV\",\"Truck\",\"Jeepney\",\"Sedan\",\"Coupe\"]'),
(32, 278, 142, 1, 1, 1, '', '1', '1', '[\"Automatic\",\"Manual\"]', '[\"Diesel\",\"Gasoline\",\"Hybrid\"]', '[\"21\",\"32\",\"1\",\"43\",\"12\",\"114\",\"104\",\"124\",\"134\",\"179\",\"170\",\"143\",\"161\",\"152\",\"206\",\"188\",\"197\",\"52\",\"74\",\"63\",\"93\",\"83\",\"215\",\"224\",\"234\",\"279\",\"252\",\"261\",\"243\",\"270\",\"288\",\"297\",\"306\",\"326\",\"316\",\"335\",\"344\",\"353\",\"371\",\"362\",\"389\",\"380\",\"398\",\"407\",\"416\",\"425\",\"605\",\"596\",\"587\",\"610\",\"624\",\"619\",\"638\",\"633\",\"628\",\"654\",\"651\",\"690\",\"684\",\"694\",\"698\",\"702\",\"738\",\"728\",\"757\",\"747\",\"758\",\"783\",\"784\",\"785\",\"779\",\"759\",\"780\",\"781\",\"782\",\"769\",\"846\",\"864\",\"855\",\"873\",\"882\",\"891\",\"909\",\"900\",\"999\",\"1017\",\"1008\",\"1026\",\"1044\",\"1035\",\"1075\",\"1052\",\"1061\",\"1070\",\"1112\",\"1104\",\"1096\",\"1128\",\"1120\",\"1124\",\"1134\",\"1133\",\"1135\"]', '[\"03\\/12\\/2026, 08:00 - 10:00 (2 slots)\",\"03\\/13\\/2026, 10:00 - 12:00 (2 slots)\",\"03\\/14\\/2026, 13:00 - 15:00 (3 slots)\"]', NULL, '2025-11-13 12:18:34', '[\"Sedan\",\"SUV\",\"Hatchback\",\"MPV\\/AUV\",\"Jeepney\",\"Van\",\"Truck\",\"Motorcycle\"]');

-- --------------------------------------------------------

--
-- Table structure for table `shop_emergency_config`
--

CREATE TABLE `shop_emergency_config` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `emergency_hours` text DEFAULT NULL COMMENT 'JSON array of emergency hours',
  `offered_services` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop_emergency_config`
--

INSERT INTO `shop_emergency_config` (`id`, `user_id`, `shop_id`, `emergency_hours`, `offered_services`, `created_at`, `updated_at`) VALUES
(3, 187, 91, '[\"Monday, 10:35 - 14:35\",\"Sunday, 02:38 - 14:43\"]', NULL, '2025-07-25 06:34:03', '2025-07-27 02:46:48'),
(4, 190, 98, '[\"Saturday, 10:42 - 22:46\",\"Sunday, 10:41 - 22:43\",\"Wednesday, 01:53 - 17:58\"]', NULL, '2025-07-27 02:41:06', '2025-07-30 05:52:55'),
(5, 189, 100, '[\"Sunday, 06:22 - 18:19\"]', NULL, '2025-08-09 22:19:49', '2025-08-09 22:30:12'),
(6, 196, 102, '[\"Sunday, 10:28 - 22:26\"]', NULL, '2025-08-17 02:27:30', '2025-08-17 02:27:33'),
(7, 197, 103, '[\"Sunday, 02:53 - 22:53\"]', NULL, '2025-08-17 06:53:16', '2025-08-17 11:07:07'),
(8, 198, 104, '[]', NULL, '2025-08-17 07:39:02', '2025-08-17 07:39:02'),
(9, 203, 111, '[\"Tuesday, 06:00 - 22:00\"]', NULL, '2025-08-19 00:17:05', '2025-08-19 00:24:55'),
(17, 236, 130, '[\"Monday, 08:00 - 17:00\",\"Thursday, 05:20 - 18:20\"]', '[\"breakdown_escort\",\"jumpstart_cars\",\"fuel_delivery_cars\",\"flatbed_towing\",\"tire_replacement_spare\",\"tire_inflation\"]', '2025-10-20 14:19:11', '2025-11-06 06:26:31'),
(21, 278, 142, '[\"Wednesday, 06:00 - 23:00\",\"Thursday, 06:00 - 23:00\",\"Friday, 06:00 - 23:00\",\"Monday, 06:00 - 23:00\",\"Tuesday, 06:00 - 23:00\",\"Saturday, 06:00 - 23:00\",\"Sunday, 06:00 - 23:00\"]', '[\"jumpstart_cars\",\"tire_replacement_spare\",\"battery_replacement_cars\",\"jumpstart_commercial\",\"fleet_battery_replacement\",\"fluids_topup_cars\",\"fuel_delivery_cars\",\"fluids_topup_commercial\",\"fuel_delivery_commercial\",\"door_unlocking_cars\",\"key_retrieval_cars\",\"door_unlocking_commercial\",\"belt_replacement_cars\",\"hose_fix_cars\",\"engine_adjustment_cars\",\"alternator_starter_repair\",\"belt_hose_fix_commercial\",\"tire_inflation\",\"vulcanizing_commercial\",\"roadside_tire_replacement_commercial\",\"accident_recovery_cars\",\"flatbed_towing\",\"wheel_lift_towing\",\"minor_accident_recovery\",\"light_truck_towing\"]', '2025-11-13 12:18:34', '2026-02-01 06:17:37');

-- --------------------------------------------------------

--
-- Table structure for table `shop_profile_visits`
--

CREATE TABLE `shop_profile_visits` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `visitor_ip` varchar(45) NOT NULL,
  `visit_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop_profile_visits`
--

INSERT INTO `shop_profile_visits` (`id`, `shop_id`, `user_id`, `visitor_ip`, `visit_timestamp`) VALUES
(36, 130, 239, '::1', '2025-10-20 17:02:07'),
(38, 130, 239, '::1', '2025-10-20 17:15:30'),
(43, 130, 239, '::1', '2025-10-20 18:10:06'),
(44, 130, 237, '::1', '2025-10-22 03:23:05'),
(47, 130, 247, '49.147.193.171', '2025-10-23 17:15:55'),
(49, 130, 247, '49.147.193.171', '2025-10-23 17:31:39'),
(53, 130, 249, '49.147.198.204', '2025-10-25 15:50:18'),
(65, 130, 252, '49.147.198.204', '2025-10-27 07:07:32'),
(83, 130, 259, '49.147.198.204', '2025-10-27 10:59:30'),
(85, 130, 248, '49.145.182.36', '2025-10-27 11:01:33'),
(95, 130, 259, '49.147.198.204', '2025-10-27 11:48:24'),
(126, 130, 258, '49.147.198.53', '2025-10-31 06:47:19'),
(127, 130, 266, '49.147.198.53', '2025-10-31 07:20:43'),
(128, 130, 266, '49.147.198.53', '2025-10-31 07:46:51'),
(132, 130, 266, '49.147.198.53', '2025-10-31 08:34:02'),
(133, 130, 266, '49.147.198.53', '2025-10-31 08:46:43'),
(134, 130, 266, '49.147.198.53', '2025-10-31 08:53:08'),
(135, 130, 266, '49.147.198.53', '2025-10-31 09:03:25'),
(144, 130, 274, '49.147.196.201', '2025-11-06 07:12:17'),
(148, 142, 273, '49.147.197.179', '2025-11-13 12:27:33'),
(150, 142, 274, '49.147.197.179', '2025-11-13 12:53:40'),
(155, 142, 273, '49.147.197.179', '2025-11-13 13:42:48'),
(158, 142, 273, '49.147.197.179', '2025-11-13 14:28:37'),
(162, 142, 276, '136.158.240.130', '2025-11-14 02:34:48'),
(163, 142, 276, '136.158.240.130', '2025-11-14 03:07:14'),
(164, 142, 273, '49.147.197.179', '2025-11-15 04:08:44'),
(166, 142, 273, '49.147.197.179', '2025-11-16 08:14:33'),
(167, 142, 273, '49.147.197.179', '2025-11-17 05:56:53'),
(170, 142, 273, '49.147.197.179', '2025-11-17 11:52:20'),
(172, 130, 273, '49.147.197.179', '2025-11-18 11:32:01'),
(173, 142, 248, '58.69.2.186', '2025-12-17 02:22:37'),
(174, 130, 273, '175.176.77.160', '2025-12-17 04:12:23'),
(175, 142, 236, '49.147.197.192', '2025-12-26 23:22:57'),
(176, 142, 236, '49.147.197.192', '2025-12-26 23:33:15'),
(177, 142, 274, '49.147.194.136', '2026-01-31 21:34:21'),
(178, 142, 274, '49.147.194.136', '2026-01-31 21:40:04'),
(179, 142, 287, '49.147.194.136', '2026-02-01 05:31:08'),
(180, 142, 289, '198.176.84.34', '2026-02-04 03:21:52'),
(181, 142, 288, '49.157.104.61', '2026-02-05 07:48:32'),
(182, 142, 288, '49.157.104.61', '2026-02-05 07:53:59'),
(183, 130, 278, '49.147.199.206', '2026-02-08 04:46:29'),
(184, 130, 290, '49.147.199.221', '2026-03-06 02:58:10'),
(185, 142, 289, '58.69.2.186', '2026-03-11 00:16:57'),
(186, 142, 293, '143.44.196.232', '2026-03-11 00:28:05'),
(187, 142, 293, '143.44.196.232', '2026-03-11 00:45:47'),
(188, 142, 293, '143.44.196.232', '2026-03-11 01:20:20'),
(189, 142, 248, '112.198.112.136', '2026-03-11 08:47:07'),
(190, 142, 300, '49.147.193.16', '2026-03-28 11:25:13');

-- --------------------------------------------------------

--
-- Table structure for table `shop_ratings`
--

CREATE TABLE `shop_ratings` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reviewer_name` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop_ratings`
--

INSERT INTO `shop_ratings` (`id`, `shop_id`, `user_id`, `reviewer_name`, `rating`, `comment`, `created_at`) VALUES
(228, 128, 236, 'Sayrelle James Tiron', 5, 'Good', '2025-10-16 06:45:02');

-- --------------------------------------------------------

--
-- Table structure for table `shop_services`
--

CREATE TABLE `shop_services` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop_services`
--

INSERT INTO `shop_services` (`id`, `application_id`, `service_id`) VALUES
(3, 114, 306),
(4, 114, 314),
(5, 114, 308),
(6, 114, 315),
(7, 114, 313),
(8, 114, 311),
(9, 114, 312),
(10, 114, 307),
(11, 114, 23),
(12, 114, 664),
(13, 114, 663),
(14, 114, 662),
(15, 114, 661),
(17, 114, 54),
(18, 114, 52),
(19, 114, 60),
(20, 114, 27),
(21, 113, 25),
(22, 115, 120),
(23, 116, 485),
(24, 116, 483),
(25, 114, 62),
(26, 117, 57),
(27, 117, 58),
(28, 117, 53),
(29, 117, 55),
(30, 117, 56),
(31, 117, 120),
(32, 117, 104),
(33, 118, 54),
(34, 118, 62),
(35, 118, 61),
(37, 119, 29),
(38, 119, 23),
(39, 119, 31),
(40, 120, 121),
(41, 121, 121),
(42, 122, 103),
(43, 123, 31),
(44, 124, 23),
(45, 124, 31),
(46, 125, 31),
(47, 114, 178),
(48, 126, 21),
(49, 126, 29),
(50, 126, 23),
(51, 126, 24),
(52, 127, 28),
(53, 128, 21),
(54, 128, 114),
(56, 128, 1044),
(57, 128, 1026),
(58, 128, 1035),
(59, 128, 1091),
(60, 128, 1083),
(61, 128, 1078),
(62, 128, 1104),
(63, 128, 1096),
(64, 128, 1112),
(65, 128, 855),
(66, 128, 864),
(67, 128, 891),
(68, 128, 846),
(69, 128, 873),
(70, 128, 882),
(71, 128, 909),
(72, 128, 900),
(73, 128, 179),
(74, 128, 152),
(75, 128, 161),
(76, 128, 143),
(77, 128, 170),
(78, 128, 297),
(79, 128, 288),
(80, 129, 104),
(81, 130, 114),
(82, 130, 104),
(83, 130, 124),
(84, 130, 52),
(85, 130, 74),
(86, 130, 63),
(92, 130, 93),
(93, 130, 83),
(99, 130, 206),
(100, 130, 188),
(101, 130, 197),
(102, 130, 134),
(103, 130, 288),
(104, 130, 297),
(105, 130, 224),
(106, 130, 234),
(107, 130, 279),
(108, 130, 252),
(109, 130, 261),
(110, 130, 243),
(111, 130, 270),
(112, 130, 215),
(113, 130, 353),
(114, 130, 407),
(115, 130, 416),
(116, 130, 425),
(117, 130, 306),
(118, 130, 326),
(119, 130, 316),
(120, 130, 335),
(121, 130, 344),
(122, 130, 371),
(123, 130, 362),
(124, 130, 389),
(125, 130, 380),
(126, 130, 398),
(127, 130, 694),
(128, 130, 698),
(129, 130, 702),
(130, 130, 690),
(131, 130, 684),
(132, 130, 891),
(133, 130, 846),
(134, 130, 873),
(135, 130, 882),
(136, 130, 1026),
(137, 130, 1044),
(138, 130, 1035),
(139, 131, 21),
(140, 131, 32),
(141, 131, 1),
(142, 131, 43),
(143, 131, 12),
(144, 131, 114),
(145, 131, 104),
(146, 131, 124),
(147, 131, 134),
(148, 131, 215),
(149, 131, 224),
(150, 131, 234),
(151, 131, 279),
(152, 131, 252),
(153, 131, 261),
(154, 131, 243),
(155, 131, 270),
(156, 131, 306),
(157, 131, 326),
(158, 131, 316),
(159, 131, 335),
(160, 131, 344),
(161, 131, 353),
(162, 131, 371),
(163, 131, 389),
(164, 131, 380),
(165, 131, 398),
(166, 131, 596),
(167, 131, 587),
(168, 131, 610),
(169, 131, 624),
(170, 131, 619),
(171, 131, 638),
(172, 131, 633),
(173, 131, 628),
(174, 131, 641),
(175, 131, 644),
(176, 131, 648),
(177, 131, 654),
(178, 131, 651),
(179, 131, 738),
(180, 131, 728),
(181, 131, 757),
(182, 131, 747),
(183, 131, 758),
(184, 131, 783),
(185, 131, 784),
(186, 131, 779),
(187, 131, 759),
(188, 131, 780),
(189, 131, 781),
(190, 131, 782),
(191, 131, 769),
(192, 131, 846),
(193, 131, 864),
(194, 131, 855),
(195, 131, 891),
(196, 131, 909),
(197, 131, 900),
(198, 132, 224),
(199, 132, 234),
(200, 133, 443),
(201, 133, 461),
(202, 133, 470),
(203, 133, 452),
(204, 133, 434),
(205, 134, 74),
(206, 131, 93),
(207, 131, 83),
(208, 133, 288),
(209, 133, 297),
(210, 135, 104),
(211, 136, 52),
(212, 136, 74),
(213, 136, 63),
(214, 137, 114),
(215, 137, 104),
(216, 138, 104),
(217, 139, 104),
(218, 140, 104),
(219, 141, 74),
(220, 142, 21),
(221, 142, 32),
(222, 142, 1),
(223, 142, 43),
(224, 142, 12),
(225, 142, 114),
(226, 142, 104),
(227, 142, 124),
(228, 142, 134),
(229, 142, 179),
(230, 142, 170),
(231, 142, 143),
(232, 142, 161),
(233, 142, 152),
(234, 142, 197),
(235, 142, 188),
(236, 142, 206),
(237, 142, 353),
(238, 142, 371),
(239, 142, 362),
(240, 142, 389),
(241, 142, 398),
(242, 142, 380),
(243, 142, 335),
(244, 142, 344),
(245, 142, 52),
(246, 142, 74),
(247, 142, 63),
(248, 142, 83),
(249, 142, 93),
(251, 142, 619),
(253, 142, 891),
(254, 142, 855),
(255, 142, 864),
(257, 142, 873),
(258, 142, 909),
(259, 142, 900),
(260, 142, 1026),
(261, 142, 1044),
(262, 142, 1035),
(263, 142, 1091),
(264, 142, 1083),
(265, 142, 1078),
(266, 142, 1112),
(267, 142, 1104),
(268, 142, 1096),
(269, 142, 1075),
(270, 142, 1052),
(271, 142, 1061),
(272, 142, 1070),
(273, 142, 1128),
(274, 142, 1120),
(275, 142, 1124),
(276, 143, 114),
(277, 143, 104),
(278, 143, 124),
(279, 143, 936),
(280, 143, 918),
(281, 143, 927),
(282, 143, 864),
(283, 143, 855),
(284, 143, 1112),
(285, 143, 1104),
(286, 143, 1096),
(288, 130, 21),
(289, 130, 32),
(290, 130, 1),
(291, 130, 43),
(292, 130, 12),
(302, 130, 999),
(303, 130, 1017),
(304, 130, 1008),
(306, 130, 972),
(307, 130, 990),
(308, 130, 981),
(309, 130, 170),
(310, 130, 161),
(311, 130, 179),
(312, 130, 143),
(313, 130, 152),
(314, 144, 114),
(315, 144, 104),
(316, 144, 124),
(317, 142, 288),
(318, 142, 297),
(319, 142, 234),
(320, 142, 224),
(321, 142, 279),
(322, 142, 252),
(323, 142, 270),
(324, 142, 261),
(325, 142, 243),
(326, 142, 215),
(327, 142, 407),
(328, 142, 416),
(329, 142, 425),
(330, 142, 306),
(331, 142, 326),
(332, 142, 316),
(333, 142, 610),
(334, 142, 624),
(335, 142, 605),
(336, 142, 596),
(337, 142, 587),
(338, 142, 633),
(339, 142, 628),
(340, 142, 638),
(341, 142, 641),
(342, 142, 644),
(343, 142, 648),
(344, 142, 654),
(345, 142, 651),
(346, 142, 846),
(347, 145, 114),
(348, 145, 104),
(349, 145, 124),
(350, 145, 134),
(351, 145, 179),
(352, 145, 170),
(353, 145, 143),
(354, 145, 161),
(355, 145, 152);

-- --------------------------------------------------------

--
-- Table structure for table `typing_status`
--

CREATE TABLE `typing_status` (
  `user_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `is_typing` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_notifications` tinyint(1) NOT NULL DEFAULT 1,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'unverified',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `profile_picture` varchar(255) DEFAULT NULL,
  `profile_type` enum('user','owner') NOT NULL DEFAULT 'user',
  `contact_number` varchar(15) DEFAULT NULL,
  `full_address` varchar(255) NOT NULL,
  `location_updated_at` timestamp NULL DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Philippines',
  `postal_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `last_attempt` datetime DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `auth_provider` enum('manual','google') DEFAULT 'manual',
  `enable_notifications` int(11) NOT NULL DEFAULT 1,
  `push_notif_modal` tinyint(1) NOT NULL DEFAULT 0,
  `data_collection` tinyint(1) NOT NULL DEFAULT 0,
  `marketing_email` tinyint(1) NOT NULL DEFAULT 1,
  `seen_allow_location` tinyint(1) DEFAULT 0,
  `account_state` enum('Active','Inactive') DEFAULT 'Active',
  `last_login` datetime DEFAULT NULL,
  `verification_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `email_notifications`, `password`, `status`, `is_verified`, `profile_picture`, `profile_type`, `contact_number`, `full_address`, `location_updated_at`, `country`, `postal_code`, `created_at`, `failed_attempts`, `last_attempt`, `login_attempts`, `latitude`, `longitude`, `auth_provider`, `enable_notifications`, `push_notif_modal`, `data_collection`, `marketing_email`, `seen_allow_location`, `account_state`, `last_login`, `verification_expiry`) VALUES
(236, 'Louie Abello', 'louieabello@yahoo.com', 1, '$2y$10$313TsHbvVvw1cTISDIkuFOI5JrotwjaSme5IApingn7ye9wqykKDS', 'verified', 1, NULL, 'owner', NULL, 'Hf9xZMcAfh3AHyOcV1pXblpXaXZwMHErbm1sYzJTMjZjbXBOSTJFQVExTFRVcUVTS0tCNkxnRUgrZ09iYmRhelpqdFcvSHRMWk9YaEgxV2dJcTlvRE9LYUxQTnR1dXVqdjVPaCtmQjhXZjlEZXZqanBjOXRZMmEyNTB3PQ==', NULL, 'Philippines', NULL, '2025-10-16 06:32:02', 0, '2025-11-17 18:16:48', 0, 'VhZ9lOtqVfdfJd+tIxqzYjBXSjFqejZWYXM5NzlPSzNEdTFGU1E9PQ==', 'DjLDMcuCXLRSE8PmSKq0R3hicVBzcXpXbGZ0WmNYWXM4UWdQOXc9PQ==', 'manual', 1, 1, 0, 1, 0, 'Active', '2025-12-27 07:17:17', NULL),
(240, 'Lorenzo M Riano', 'lorm.riano.ui@phinmaed.com', 1, '$2y$10$at60clyeXAwhXmbsk8AW5.b4EJB.KRg3SKQTgnGbFV1wXWGYIcHUK', 'verified', 1, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2025-10-22 04:44:03', 0, NULL, 0, NULL, NULL, 'google', 1, 0, 0, 1, 0, 'Active', '2025-10-26 14:39:52', NULL),
(248, 'Detective Conan', 'eennzzooriano13@gmail.com', 1, '$2y$10$0IgWUbaFH5IowCM5WJ8E3.vHaKqESgkSirqoqXfBinJvUNmZnwaIq', 'verified', 1, '68ff4c9be0d7c.jpg', 'user', '09619090442', 'BjCfKNd0+1nBms07ab+Kq01ZbGwrTEV3Nzd5dXdhYXVsSURxN2lmVHluMUVMeEVOMkFKMEU4R3JnemY5bFlsckFFZCtIVjVwUm5zRWpzWlNwTE5zWTRITzBxZlczOUwvWkpJb1NBPT0=', NULL, 'Philippines', NULL, '2025-10-24 01:52:49', 0, '2026-03-11 16:50:12', 1, 'XBAqZ5Umy9Gg3SSZ6dfrM0tIdWNvbFprWDMzblBTWktmZ2VPUGc9PQ==', 'KG2RSEgkR6fxRlAa4MbdKDZkUmMxMXNrVUEvek8vcVpWTmtUdXc9PQ==', 'manual', 1, 1, 0, 1, 0, 'Active', '2026-03-11 16:44:00', '2025-10-25 09:52:49'),
(254, 'Mark A. Sullano', 'markasullano@gmail.com', 1, '$2y$10$guJ71m1nZZSyCiwdh3XS9eJtnYaDpW2JcQc76b/DpKNgpsna3x9yu', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2025-10-27 06:17:41', 0, NULL, 0, NULL, NULL, 'google', 1, 0, 0, 1, 0, 'Active', NULL, NULL),
(260, 'Jay-r Robles', 'jaca.robles.ui@phinmaed.com', 1, '$2y$10$N7GtqqRS7/l0PDGFWUQq/Onu3CV71M3oVZ8Ei0z51h3VoDJssVB.i', 'verified', 1, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2025-10-27 11:36:36', 0, NULL, 0, NULL, NULL, 'google', 1, 0, 0, 1, 0, 'Active', NULL, NULL),
(261, 'Leomar Aguero', 'leomaraguero244@gmail.com', 1, '$2y$10$hoymq2PJNIRfR.fc1I7KXuIZhTh8RYpS/CH4Sktf.QZV10tOca6rm', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2025-10-27 11:41:38', 0, NULL, 0, NULL, NULL, 'google', 1, 0, 0, 1, 0, 'Active', NULL, NULL),
(262, 'ANN JOSHUA ENDERES TRESVALLES', 'anen.tresvalles.ui@phinmaed.com', 1, '$2y$10$R/7O.gq.vwf.kBGw3Tz/ieBfKhZylt/XsfcmRVi9eJwEKbufNhO42', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2025-10-27 11:42:32', 0, NULL, 0, NULL, NULL, 'google', 1, 0, 0, 1, 0, 'Active', NULL, NULL),
(267, 'Jayrrobles', 'jayrrobles457@gmail.com', 1, '$2y$10$pOsa7GTj7Ax1i9TJatI3jOuBz/BLSuePP9T94NabPaArxKSlEZjiK', 'verified', 1, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2025-10-28 03:33:05', 0, NULL, 0, NULL, NULL, 'manual', 1, 0, 0, 1, 0, 'Active', '2025-10-28 00:29:49', '2025-10-29 11:33:05'),
(269, 'Edward Allen Benjamin', 'nicabasalatan688@gmail.com', 1, '$2y$10$6XbwfMK9I555JG8v2JshhO/Ut42GCm0KyP4AhO.TxVvyxyaYFJ7Oa', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2025-10-28 10:59:57', 0, NULL, 0, NULL, NULL, 'manual', 1, 0, 0, 1, 0, 'Active', '2025-10-28 07:01:13', '2025-10-29 18:59:57'),
(278, 'Anthony Sullano', 'marcel192223@gmail.com', 1, '$2y$10$72LviSKhgxn1Hvr4O6Cymu.1iVr78SNR7OPV/DWfMQjIBRMVa46xy', 'verified', 0, NULL, 'owner', NULL, 'WFHnXw0Eg03Whtrvh+5ZxWNJQnY1VzdrSXVEK09WQUtvQXNIemloYm1WaXlGcEZ1akEwMm5rUC9jNzlvVVhOak5DRFkxVFl5R1JTZ2lPS2w=', NULL, 'Philippines', NULL, '2025-11-13 11:57:16', 0, '2026-03-11 09:15:40', 0, 'snjRnJV0Oq7UalJIDGQ87VlHOTZ1T01FZ0pQdmIwWHhMc1pqZ3BmTTAwN3RqVHlISWwwdVgwdU9jblE9', 'Odgm+K20eVgFCYGr4iibLU1uL1N1Q3k4UU9EcWs3MGtNalhwSmtabURuNlRHVk1RQnNjaU9GMGFhL0U9', 'manual', 1, 1, 0, 1, 0, 'Active', '2026-03-11 16:41:57', '2025-11-14 19:57:16'),
(281, 'Brydon Casulatan', 'takzkie27@gmail.com', 1, '$2y$10$e2W8WJk3QRYQ8ulL.Nyre.lC3ibuIhf9ELmzugRtkf/XpV8kLlyGC', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2025-11-14 00:40:07', 0, NULL, 0, NULL, NULL, 'google', 1, 1, 0, 1, 0, 'Active', '2026-03-11 08:20:59', NULL),
(282, 'Reza grace Luma', 'rezagrace18@gmail.com', 1, '$2y$10$MnumhyScxLMSzShscsm6w./EoWHql0XnFBHknIslKp.h80MGe.RlS', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2025-11-15 03:24:43', 0, NULL, 0, NULL, NULL, 'google', 1, 1, 0, 1, 0, 'Active', '2025-11-17 07:53:19', NULL),
(285, 'Toni Ross Arabit', 'toge.arabit.ui@phinmaed.com', 1, '$2y$10$VAB625EBf5tBDVRzGlbHfOUIFJKZbTneqj3ezRr6926pgax7NAeFO', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2025-12-21 13:14:17', 0, NULL, 0, NULL, NULL, 'manual', 1, 1, 0, 1, 0, 'Active', NULL, '2025-12-22 21:14:17'),
(286, 'Cuddle Muffin', 'cuddlemuffin733@gmail.com', 1, '$2y$10$YLS9VtNBeveRF5TBIqjaV.MEFjEBn2BvPYVMF8HlT5FAgIo9aPgdK', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2025-12-25 04:58:33', 0, NULL, 0, NULL, NULL, 'google', 1, 1, 0, 1, 0, 'Active', NULL, NULL),
(288, 'Clave Anthony Lasconia', 'lasconiaclaveanthony1@gmail.com', 1, '$2y$10$Ow7cnXVpJ.dCCrbLdem6FeTdiFfsfhUkrMg1ly3oUtEGpjHwLG/ae', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2026-02-04 02:20:46', 0, NULL, 0, NULL, NULL, 'google', 1, 1, 0, 1, 0, 'Active', '2026-02-05 15:48:16', NULL),
(289, 'Jhon Rey Gallofin', 'gallofinjhonrey18@gmail.com', 1, '$2y$10$PcDxzdq2g3d7ZpA6bg2z.OsosxfcmpBAN5lcXw6PsY8zzaagm7gHO', 'verified', 1, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2026-02-04 03:21:30', 0, NULL, 0, NULL, NULL, 'google', 1, 1, 0, 1, 0, 'Active', '2026-03-11 08:16:21', NULL),
(292, 'BALLESTEROS KYLE ANDREI', 'kyleandrei3008@gmail.com', 1, '$2y$10$CGw3JkoQxgM2RcJAXxj3zea.kkKQK57cETLKFXtCBpl2O0QP4x3Jy', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2026-03-11 00:15:13', 0, NULL, 0, NULL, NULL, 'google', 1, 1, 0, 1, 0, 'Active', NULL, NULL),
(293, 'Ryan A. Macalalag', 'ryanmacalalag@gmail.com', 1, '$2y$12$j10EE94N6rtr2QD.UO2IpeeDyU/wBFh0PxMKHZd0jLNy25vq7oW0K', 'verified', 1, NULL, 'user', '', '', NULL, 'Philippines', NULL, '2026-03-11 00:26:51', 0, '2026-03-11 09:40:58', 0, NULL, NULL, 'manual', 1, 1, 0, 1, 0, 'Active', '2026-03-11 09:43:54', NULL),
(294, 'CRIS JOHN RIOCASA', 'crisjohn.riocasa@students.isatu.edu.ph', 1, '$2y$10$RK6cmgjgIJKuRwJkfdoNre/xsp6T4xs/veBeE7d46mIvqMMa2Vfdy', 'verified', 0, NULL, 'user', '09394769491', '', NULL, 'Philippines', NULL, '2026-03-11 01:26:58', 0, NULL, 0, NULL, NULL, 'google', 1, 1, 0, 1, 0, 'Active', NULL, NULL),
(295, 'Reniel john tabuloc', 'renieljohnt@gmail.com', 1, '$2y$10$VSTUc4EJzyCZy.i5UW0mE.Qw2LvllxEI2XClFXnHoA75iSCB3ls4W', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2026-03-11 01:40:39', 0, NULL, 0, NULL, NULL, 'manual', 1, 1, 0, 1, 0, 'Active', NULL, '2026-03-12 09:40:39'),
(296, 'rItchi laurilla', 'fckmegood01@gmail.com', 1, '$2y$10$P2.rOHs9TaG25ROfdVsYTuZ7/V9Ckuvq8oAwmgopSPIF3.ZqlkYou', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2026-03-11 04:01:20', 0, NULL, 0, NULL, NULL, 'google', 1, 1, 0, 1, 0, 'Active', NULL, NULL),
(297, 'Henry C. Pastilloso Jr.', 'pastillosojrhenry@gmail.com', 1, '$2y$10$qXpmVkwgCrbsvYppQo/koe0kD0jStGXcyqpK32EmEbjFbUZBJiLS6', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2026-03-11 05:21:55', 0, NULL, 0, NULL, NULL, 'manual', 1, 1, 0, 1, 0, 'Active', NULL, '2026-03-12 13:21:55'),
(298, 'BRYDON ANOCHE CASULATAN', 'bran.casulatan.ui@phinmaed.com', 1, '$2y$10$4CWhKjeY121yKSP3LF2QpOYIqVYxY7ywxWgBWF9hpNWdPP0IuunrG', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2026-03-11 08:07:15', 0, NULL, 0, NULL, NULL, 'google', 1, 1, 0, 1, 0, 'Active', '2026-03-18 14:27:49', NULL),
(299, 'Jerecho Earl Jaboneta Balingasa', 'jeja.balingasa.ui@phinmaed.com', 1, '$2y$10$PUYmhm57cLSqay57IgOVgeMdqayOXwa.jTdIlVV6KoT4uJSMRqtQa', 'verified', 0, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2026-03-21 06:27:36', 0, NULL, 0, NULL, NULL, 'google', 1, 1, 0, 1, 0, 'Active', NULL, NULL),
(300, 'Sayrelle James Genon Tiron', 'ayge.tiron.ui@phinmaed.com', 1, '$2y$10$PeqdDW.u8x3KomDvNt12X.fwenLQckSso7kcsktCFvVzZGeW1S5jq', 'verified', 1, NULL, 'user', NULL, '', NULL, 'Philippines', NULL, '2026-03-27 06:17:16', 0, NULL, 0, NULL, NULL, 'google', 1, 1, 0, 1, 0, 'Active', '2026-03-28 19:12:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_2fa`
--

CREATE TABLE `user_2fa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `secret_key` varbinary(255) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `encryption_key` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_2fa`
--

INSERT INTO `user_2fa` (`id`, `user_id`, `secret_key`, `is_enabled`, `created_at`, `updated_at`, `encryption_key`) VALUES
(25, 248, 0x374f4a566a41505641506d6b59515a4b56792f35326e557765585261596d646a55433933636e70705254426a61564255566c55306446704f5556644764454651515664424b315a506557314d656b5539, 1, '2025-10-28 04:46:50', '2025-10-28 04:47:26', '6dce1883d3536a7c97d839303cd934c3b65fc51e534d7d9a07eaad31207a70fe'),
(26, 236, 0x7a61734f432f4b4d3268306654474c6f33484b43446a46514d55564c65476b764e3030316255316155325653626c6c535332395452454e3155564a4b616c467452314e4d4e5530766154497254584d39, 0, '2025-10-31 17:50:29', '2025-10-31 18:36:09', '71a3b147727ffa9fdcca069ee679b86ec8d53404f8ff2689eabccf3922329c8f'),
(31, 293, 0x4d5168594b554373764146704868655a563071336d6c5a3153326869624855724f544a445a7a457a546e52324c33597a52307432525464795532746d63315a614b30314e643146505648597861476339, 0, '2026-03-11 00:27:46', '2026-03-11 00:27:46', '01f0974b801042847fadadfe9c0bc9b0c4764b4182b69fa4ce5011f2690eda9d');

-- --------------------------------------------------------

--
-- Table structure for table `user_2fa_backup_codes`
--

CREATE TABLE `user_2fa_backup_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `backup_code` varbinary(255) NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL,
  `encryption_key` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_2fa_backup_codes`
--

INSERT INTO `user_2fa_backup_codes` (`id`, `user_id`, `backup_code`, `is_used`, `created_at`, `used_at`, `encryption_key`) VALUES
(185, 248, 0x6a65545933747054454f45536c4b564932433376723070724d5749335554464d4d44464e5a44643261475a79636b6733516d633950513d3d, 0, '2025-10-28 04:47:26', NULL, '6ef04693b5e1e76be7db6f4fde0c7026305a626f4669179b073835078f080d40'),
(186, 248, 0x754b6541333373383352334545624a3675352b6945554a596453396f566d745a57553543516b6858615670786145744b5647633950513d3d, 0, '2025-10-28 04:47:26', NULL, '5796aa685bd9e6f306a7894deeb1c10637e6311faa2221927f32b7a7d551b5b8'),
(187, 248, 0x72764c56464f4b6568695776654c30784850357a627a4643526a4a61576c6f785247747259323133565577315231644e556e633950513d3d, 0, '2025-10-28 04:47:26', NULL, 'fee781d3e50ef2df33d3a09590bd90ca02b706a429d89560840415f2f3c63b83'),
(188, 248, 0x2b577a2f2b2b43472b714f79774935356e677647394468454d6b686f61576449566b56425a54524f6346566e615770696548633950513d3d, 0, '2025-10-28 04:47:26', NULL, '1a545257582a26acd1b22dd2ccdd5fd47f1ae7fc6cda6b79d53a91c028f37968'),
(189, 248, 0x6b4b50714e66444c6269537835726e306f426854486c524b646b633053587042576b394b4d557333647a6445545339485931453950513d3d, 0, '2025-10-28 04:47:26', NULL, '2e91922fffc3a47b3e12f8b3b91f52320d65d50ff8e2db388f6b4e835b48b374'),
(190, 248, 0x634a45707655655363334d57454e6e7367385971576e706964573078567973305757733254574e474e6d51795469396d6555453950513d3d, 0, '2025-10-28 04:47:26', NULL, '0c3389cdd448a715002214c7d4d1c463ba7bbb0b66faceb2cf4aa93425e3e545'),
(191, 248, 0x7a386566337572355132795841473258473768373053746a544864355331427855574661636c46686444497a597a56725433633950513d3d, 0, '2025-10-28 04:47:26', NULL, 'e6170e7976fdad490738928383729b49ac8c903ae9ae018bd12d780563446965'),
(192, 248, 0x2f4639574c6178724d662f59387a6e4431484a717a5468724d6a4246636d647a613067314e33565254554a4752326735636c453950513d3d, 0, '2025-10-28 04:47:26', NULL, '070d6870eac001c5c84e526fcba4a87c4931c0d57dae6371ef50f343b870edad');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `plate_number` varchar(20) DEFAULT NULL,
  `vehicle_model` varchar(255) DEFAULT NULL,
  `vehicle_year` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verification_submissions`
--

CREATE TABLE `verification_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_type` varchar(50) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `birthday` varchar(255) DEFAULT NULL,
  `address_barangay` varchar(255) DEFAULT NULL,
  `address_town_city` varchar(255) DEFAULT NULL,
  `address_province` varchar(255) DEFAULT NULL,
  `address_postal_code` varchar(255) DEFAULT NULL,
  `front_image_path` varchar(255) NOT NULL,
  `back_image_path` varchar(255) NOT NULL,
  `selfie_image_path` varchar(255) NOT NULL,
  `status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_by` int(11) DEFAULT NULL,
  `verification_date` datetime DEFAULT NULL,
  `delete_notification` tinyint(1) NOT NULL DEFAULT 0,
  `is_read_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_submissions`
--

INSERT INTO `verification_submissions` (`id`, `user_id`, `id_type`, `full_name`, `gender`, `birthday`, `address_barangay`, `address_town_city`, `address_province`, `address_postal_code`, `front_image_path`, `back_image_path`, `selfie_image_path`, `status`, `notes`, `submission_date`, `verified_by`, `verification_date`, `delete_notification`, `is_read_admin`, `is_read`) VALUES
(37, 236, 'National ID (PhilSys)', 'eCfMIExANjE1Jr6+oAEBEw==:cGVpUEVTMXNxTjhXblE0NlVoa2xmQT09', 'Male', '16Wc2hvwNdshER2d7Kk/Dw==:SVpZZFBQaFkzNTYvTWNuSEhwYU5ZZz09', '7S+bKDCJvs1oauP4ZeKRHg==:c1AxQnVicGdvVzBkQmhQSGRYRzExdz09', 'JMXEBA1jOpLS3jbf40F/Zw==:bXQ5Ym5JMWEvOGcwc1dqZW5RSlZ1Zz09', 'CtvrU4EnHBIEBAZlIcwCfg==:QXdYQVNXT281UU9MYzFmK3NybVJCdz09', 'RCRcCBqt/ukkQyY/TSPP3A==:d2pueGNOVHFsellkSXhXSmNHWU12dz09', 'uploads/v-submissions-data/3a6317bbf29869543aa86f6abb243618.png', 'uploads/v-submissions-data/e376b475f2e5ec5081977bf3cadc868a.png', 'uploads/v-submissions-data/35883ca75949b463f91be5c7c4177581.jpg', 'verified', '', '2025-10-16 06:40:37', 3, '2025-10-16 14:41:04', 0, 1, 1),
(41, 248, 'National ID (PhilSys)', 'krGqIgWIcUF+uTrtP24hwQ==:b2tla3BQNUMwd1ZNcUdRZVZnNU13dz09', 'Male', 'wxp3BuUL5UQTOM7nqa0GRQ==:bVA3MGpRUWZBK0MvanQ5VUhoL2lBZz09', 'DE/KHHvTmtfLG0kd36+qVQ==:QTcxSnB5NTR6L1p3K3NvbFhZNlBad0lyR09USFZPTHFPN1AwZFlqWTFMaz0=', 'aDB8qYOHBEe1QEwg0z8WoQ==:MG1iV3c2SmczUGl4cGczWTg2REIrQT09', '7MLZXUEx6zFGM6que39lGg==:RmRqYU9hY3Fqb0ZrenN6MG1ZSEErZz09', 'ywU1OryVGqBsEU1PcJ5qVg==:TUxuSitOMDRKYVpVdDBFeTlnaVdXUT09', 'uploads/v-submissions-data/c0ff5d0879b0161ef3b5fe9a2946d7b5.jpg', 'uploads/v-submissions-data/20816b911aaab35a97dba1f74e1b3028.jpg', 'uploads/v-submissions-data/81a24fd0dd0276d23f47d8a649c1bf2d.jpg', 'verified', '', '2025-10-27 10:40:20', 3, '2025-10-27 06:43:41', 0, 1, 0),
(42, 260, 'National ID (PhilSys)', '1xZlzwur5CWkFfC+ouBNig==:Nk9vS2F1WmUrRnJYS3pqQ1p5OE8zUT09', 'Male', 'aguwzBYabmbhDuqQqkOvrQ==:WHNaOGRRNVJXdFJmZWQ2SzkzVnFDQT09', 'EuZsSC9a3o6/+9+Qb7tgSQ==:NmRQbGhQTTE5MnBZUFM4dFR5bXFLQT09', 'gTL0JgCnVpJNaB5MWA1r7Q==:TlRPS01ENi8zQ2pmc0lLTE1vY1RaQT09', 'Z9msMTU5sNa0SAs5LSbebw==:RE1NQkhGbUpXTlRtT1BYWjhDUGN3UT09', 'AaatI7CLw83xO79AZyTJhA==:RTVqVytWWWkzbkROODgwN3hlZ0NVUT09', 'uploads/v-submissions-data/ccb77076b0f5f138c066e61df0f06bf8.jpg', 'uploads/v-submissions-data/1d260450099d3b9078af1e9bb281478b.jpg', 'uploads/v-submissions-data/2eda3550198bdb0d76e8709928c0d551.jpg', 'verified', '', '2025-10-27 11:43:29', 3, '2025-10-27 07:48:11', 0, 1, 0),
(43, 267, 'National ID (PhilSys)', 's22LCu8XS9lKqdmxC1gunA==:TDloSGQxUFZnQVFFS1RXbG82RUU3Zz09', 'Male', 'uFXGa424zaqjnh9yU6XP3Q==:SDQ2b1hTaXBBMlhzMHdOV1ZmNlh1Zz09', 'I8DYUJDOmSqD/u6T/ZtwuQ==:ZTRyeExUaG1KcUtLV1lFVngxMFVjQT09', 'DZc0LPwWQHmuxDGGYJCu8A==:dkVsMHJLa3kzZlZTMHlZM0VsM2ZOUT09', 'P9aP633EkUwZWi+hoQyvKw==:RkprT2VZbVlhbW4yY1lCQ0Z5bm1Mdz09', '9gF29LDn2l6mokKA4PSPag==:bnQ1SC9DL2xxTkhud29KNlBmbUJOUT09', 'uploads/v-submissions-data/031cf65a4b44830f1b2dc75c601d5ed5.jpg', 'uploads/v-submissions-data/f97910cd8de0af9c79ea4f557edc4415.jpg', 'uploads/v-submissions-data/02caa457a84eaedcbbd06cf17c3fc4ff.jpg', 'verified', '', '2025-10-28 04:32:43', 3, '2025-10-28 01:27:26', 0, 1, 0),
(51, 289, 'National ID (PhilSys)', '+Rzh1kI/1fJJ3DfRf89wYQ==:SWVOUXBBbGRKTVh0U2NjcFd5dmpnNUtENkU2NmEzekw3VmgrMnVYMWk1OD0=', 'Male', 'uD1QWQuWrtuG9wM7SJanug==:dkJIWmJlalBsRitsTm9wZU16Z1BGdz09', 'LzoOdFyQ3ErrAR3ZNTGuIw==:eVFZS2xNd2sweUVOcVU4eXdWN1BRWTdsVk5pWEJyZWNEaGg3dFJhVmh5VT0=', 'WseipqhqdKscEodV+yHM1A==:L1JBK0dZcDd1Y2RoZERlcXdZSUh6UT09', 'G+m64EIS9inyGF6stERlEg==:VHNReXhZRkt2ZzAvcG1ZWnNwV1Q5QT09', 'iPDW3APfTW7yf433/SQp8Q==:ejhrS3A1ajlNa1g5Tm5xcTVOendJQT09', 'uploads/v-submissions-data/86c476e5b79944cf4b6e2a01272bba9e.jpg', 'uploads/v-submissions-data/393db8ca332c9dc93cb0501c888e4789.jpg', 'uploads/v-submissions-data/b785edf0d55c566f52e50c1c90ddd8d1.jpg', 'verified', '', '2026-02-04 03:26:51', 3, '2026-02-04 11:28:28', 0, 1, 1),
(53, 293, 'Driver\'s License', 'cEhIdOIlANEd20EZsgpjrA==:bEQ2NkppdFExNlBvb21ya0VTVWVOeU1vaWJ3cllsTFNudlhERXJyTmwwUT0=', 'Male', 'xLWeabEATvNQdDw61x3NWQ==:a0gyZCtucnJONkdYemFRd3J4b1Jxdz09', '3NvGjF4peMNzuq5jKlRLJw==:bEhtcjJySTA3RFdZNXJWY0xsVzBiQkwxS081YytrVjRZaWkyem9rVWZpND0=', 'Ut/ePVLTV3lOnvFA7j4hjA==:eDFoeGdyL1VVcWRXYVcxbHI5SERhUT09', 'pOlXG3ztWCQ73mnIz389Aw==:di9obWtYWDVQcHd3ZWJhNlZiUTBCdz09', 'oKuDRCmVCZcXzVR/TNBGNQ==:TzJVa0pwTTh0K3czTXFmZFIxNGhJdz09', 'uploads/v-submissions-data/bf99154c19b9ca3f33aa0ceec242a980.jpg', 'uploads/v-submissions-data/91e7e15b2b8d5e64368b2746714cb92b.jpg', 'uploads/v-submissions-data/ab943c97e939ea64f5e32b807e0a2852.jpg', 'verified', '', '2026-01-10 01:44:49', 3, '2026-01-09 22:16:49', 0, 0, 1),
(54, 300, 'National ID (PhilSys)', 'TuS6MlmQUSn9Aa7FXDIPOg==:ekEyazRSM2V0SExWTURpb2plUWU0MXpXWHRuVEY2Si83dXJSNkxlelVHdz0=', 'Male', '35qWPuI6jqa2n4XHw3jOFg==:NW1PeUlzTFdEVFp6ZCtDRUhWOXg4dz09', '1aOVRNoX6tbnK4MCuxBfcA==:amNvZE9SbHZtUFdUd0VscGROV0V2UT09', 'EovHLv2J5cTt+2s63IVTpQ==:ZzFXR0w2R0U5NWRONmJVZW5IMWZjUT09', 'H10eihwHwyQoEZ+ujiBfTg==:VXA5ZWRyQlJ4czRlRUMwMEZqMmVjZz09', 'gGoKsqphX5kByEV2vbxlnw==:NDc2ZmNDWDBxdEFsVU1jNlpmSmc3QT09', 'uploads/v-submissions-data/3dfc9747b89bb2c90d02d2ca59ad9fa8.jpeg', 'uploads/v-submissions-data/722d4d7e7b11d2721714a7737670e0e0.jpeg', 'uploads/v-submissions-data/37af4d788da82b2efd9f5e007467fce0.jpg', 'verified', '', '2026-03-28 11:23:32', 3, '2026-03-28 19:23:50', 0, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `activity_time` (`activity_time`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `chatbot`
--
ALTER TABLE `chatbot`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_updated_at` (`updated_at`);

--
-- Indexes for table `chatbot_messages`
--
ALTER TABLE `chatbot_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chat_id` (`chat_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_chat_messages_chat_id` (`chat_id`),
  ADD KEY `idx_chat_messages_created_at` (`created_at`);

--
-- Indexes for table `data_download_requests`
--
ALTER TABLE `data_download_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `emergency_assistance_services`
--
ALTER TABLE `emergency_assistance_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `value` (`value`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Indexes for table `emergency_categories`
--
ALTER TABLE `emergency_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `emergency_requests`
--
ALTER TABLE `emergency_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_request` (`user_id`,`shop_id`,`created_at`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `shop_user_id` (`shop_user_id`);

--
-- Indexes for table `emergency_subcategories`
--
ALTER TABLE `emergency_subcategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inactivity_notifications`
--
ALTER TABLE `inactivity_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `idx_client_message_id` (`client_message_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reactions`
--
ALTER TABLE `reactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `respond_reviews`
--
ALTER TABLE `respond_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_id` (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shop_owner_id` (`shop_owner_id`);

--
-- Indexes for table `review_likes`
--
ALTER TABLE `review_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `review_id` (`review_id`,`liked_by_user_id`),
  ADD KEY `liked_by_user_id` (`liked_by_user_id`),
  ADD KEY `review_owner_id` (`review_owner_id`);

--
-- Indexes for table `save_shops`
--
ALTER TABLE `save_shops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Indexes for table `services_booking`
--
ALTER TABLE `services_booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `service_subcategories`
--
ALTER TABLE `service_subcategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `shop_applications`
--
ALTER TABLE `shop_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_shop_slug` (`shop_slug`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_shop_status_approved` (`status`,`approved_at`),
  ADD KEY `idx_shop_location` (`barangay`,`town_city`,`province`),
  ADD KEY `idx_shop_status` (`status`),
  ADD KEY `fk_approved_by` (`approved_by`),
  ADD KEY `idx_shop_applications_valid_id_type` (`valid_id_type`),
  ADD KEY `idx_shop_applications_status` (`status`);
ALTER TABLE `shop_applications` ADD FULLTEXT KEY `shop_name` (`shop_name`,`services_offered`);
ALTER TABLE `shop_applications` ADD FULLTEXT KEY `idx_shop_search` (`services_offered`,`province`,`town_city`,`barangay`);
ALTER TABLE `shop_applications` ADD FULLTEXT KEY `ft_shop_name` (`shop_name`);
ALTER TABLE `shop_applications` ADD FULLTEXT KEY `ft_search_fields` (`services_offered`,`province`,`town_city`,`barangay`);

--
-- Indexes for table `shop_auto_messages`
--
ALTER TABLE `shop_auto_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shop_id` (`shop_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `shop_booking_form`
--
ALTER TABLE `shop_booking_form`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `shop_emergency_config`
--
ALTER TABLE `shop_emergency_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shop_id` (`shop_id`),
  ADD KEY `shop_emergency_config_ibfk_1` (`user_id`);

--
-- Indexes for table `shop_profile_visits`
--
ALTER TABLE `shop_profile_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `shop_ratings`
--
ALTER TABLE `shop_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `shop_services`
--
ALTER TABLE `shop_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `typing_status`
--
ALTER TABLE `typing_status`
  ADD PRIMARY KEY (`user_id`,`receiver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_created` (`created_at`),
  ADD KEY `idx_user_location` (`latitude`,`longitude`);

--
-- Indexes for table `user_2fa`
--
ALTER TABLE `user_2fa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `user_2fa_backup_codes`
--
ALTER TABLE `user_2fa_backup_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_backup_code` (`user_id`,`backup_code`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `verification_submissions`
--
ALTER TABLE `verification_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_verified_by_admin` (`verified_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `active_sessions`
--
ALTER TABLE `active_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1179;

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1182;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chatbot`
--
ALTER TABLE `chatbot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=264;

--
-- AUTO_INCREMENT for table `chatbot_messages`
--
ALTER TABLE `chatbot_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1074;

--
-- AUTO_INCREMENT for table `data_download_requests`
--
ALTER TABLE `data_download_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emergency_assistance_services`
--
ALTER TABLE `emergency_assistance_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `emergency_categories`
--
ALTER TABLE `emergency_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `emergency_requests`
--
ALTER TABLE `emergency_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `emergency_subcategories`
--
ALTER TABLE `emergency_subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inactivity_notifications`
--
ALTER TABLE `inactivity_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=868;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=319;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=394;

--
-- AUTO_INCREMENT for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `reactions`
--
ALTER TABLE `reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `respond_reviews`
--
ALTER TABLE `respond_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `review_likes`
--
ALTER TABLE `review_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `save_shops`
--
ALTER TABLE `save_shops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=295;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1136;

--
-- AUTO_INCREMENT for table `services_booking`
--
ALTER TABLE `services_booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `service_categories`
--
ALTER TABLE `service_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `service_subcategories`
--
ALTER TABLE `service_subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `shop_applications`
--
ALTER TABLE `shop_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `shop_auto_messages`
--
ALTER TABLE `shop_auto_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `shop_booking_form`
--
ALTER TABLE `shop_booking_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `shop_emergency_config`
--
ALTER TABLE `shop_emergency_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `shop_profile_visits`
--
ALTER TABLE `shop_profile_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `shop_ratings`
--
ALTER TABLE `shop_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT for table `shop_services`
--
ALTER TABLE `shop_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=356;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;

--
-- AUTO_INCREMENT for table `user_2fa`
--
ALTER TABLE `user_2fa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `user_2fa_backup_codes`
--
ALTER TABLE `user_2fa_backup_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verification_submissions`
--
ALTER TABLE `verification_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD CONSTRAINT `active_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chatbot_messages`
--
ALTER TABLE `chatbot_messages`
  ADD CONSTRAINT `chatbot_messages_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chatbot` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `data_download_requests`
--
ALTER TABLE `data_download_requests`
  ADD CONSTRAINT `data_download_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD CONSTRAINT `email_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emergency_assistance_services`
--
ALTER TABLE `emergency_assistance_services`
  ADD CONSTRAINT `fk_emergency_subcat` FOREIGN KEY (`subcategory_id`) REFERENCES `emergency_subcategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `emergency_requests`
--
ALTER TABLE `emergency_requests`
  ADD CONSTRAINT `emergency_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `emergency_requests_ibfk_2` FOREIGN KEY (`shop_id`) REFERENCES `shop_applications` (`id`),
  ADD CONSTRAINT `emergency_requests_ibfk_3` FOREIGN KEY (`shop_user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `emergency_subcategories`
--
ALTER TABLE `emergency_subcategories`
  ADD CONSTRAINT `fk_emergency_cat` FOREIGN KEY (`category_id`) REFERENCES `emergency_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inactivity_notifications`
--
ALTER TABLE `inactivity_notifications`
  ADD CONSTRAINT `inactivity_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD CONSTRAINT `otp_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  ADD CONSTRAINT `push_subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`subcategory_id`) REFERENCES `service_subcategories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_subcategories`
--
ALTER TABLE `service_subcategories`
  ADD CONSTRAINT `service_subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shop_profile_visits`
--
ALTER TABLE `shop_profile_visits`
  ADD CONSTRAINT `shop_profile_visits_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shop_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_submissions`
--
ALTER TABLE `verification_submissions`
  ADD CONSTRAINT `fk_verified_by_admin` FOREIGN KEY (`verified_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
