-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2026 at 12:42 PM
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
-- Database: `trustnet_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_sessions`
--

CREATE TABLE `active_sessions` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `session_start` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `session_end` datetime DEFAULT NULL,
  `total_duration` int(11) DEFAULT 0,
  `page_count` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `active_sessions`
--

INSERT INTO `active_sessions` (`id`, `website_id`, `session_id`, `ip_address`, `session_start`, `last_activity`, `session_end`, `total_duration`, `page_count`) VALUES
(1, 2, '669fb02151edb6e9ef7ac98191718e9c', '::1', '2026-05-09 14:41:53', '2026-05-09 14:41:53', '2026-05-09 14:42:31', 38, 1),
(2, 2, '1dc3bc24bf43fcd2302cdd0bc156a2a2', '::1', '2026-05-09 14:42:31', '2026-05-09 14:42:31', '2026-05-09 14:42:33', 1, 1),
(3, 2, 'dea5064fc779d2252db80b8b3841e48d', '::1', '2026-05-09 14:42:33', '2026-05-09 14:42:33', '2026-05-09 14:42:37', 3, 1),
(4, 2, 'cb7b0bcbe47455cca16e4b3dd3ad90cc', '::1', '2026-05-09 14:42:38', '2026-05-09 14:42:38', '2026-05-09 14:42:42', 3, 1),
(5, 2, '902042fe68aad8772f8de2d998702d01', '::1', '2026-05-09 14:42:42', '2026-05-09 14:42:42', '2026-05-09 14:42:44', 2, 1),
(6, 2, '713ebcf0082ac46651992e89ebda6dad', '::1', '2026-05-09 14:42:46', '2026-05-09 14:42:46', '2026-05-09 14:42:49', 2, 1),
(7, 2, 'fb6a4871270d9780a175091d12881f79', '::1', '2026-05-09 14:42:49', '2026-05-09 14:42:49', '2026-05-09 14:42:52', 3, 1),
(8, 2, '2edad26a5d537a4ece5ce5326c4130f9', '::1', '2026-05-09 14:42:54', '2026-05-09 14:42:54', '2026-05-09 14:42:58', 3, 1),
(9, 2, '40789667e4961b62bf9d66f81a415110', '::1', '2026-05-09 14:42:58', '2026-05-09 14:42:58', '2026-05-09 14:43:01', 3, 1),
(10, 2, '0a95a53210d6f8a00fc29ef2474e5fa8', '::1', '2026-05-09 14:43:02', '2026-05-09 14:43:02', '2026-05-09 14:43:05', 2, 1),
(11, 2, 'bdecdd714d8ac768e6332477f0871a2c', '::1', '2026-05-09 14:43:05', '2026-05-09 14:43:05', '2026-05-09 14:43:07', 1, 1),
(12, 2, '05316e5a460f591752ad8c5c2a11160c', '::1', '2026-05-09 14:43:07', '2026-05-09 14:43:07', '2026-05-09 14:57:12', 845, 1),
(13, 2, '063fefcf8b3ddbf4d749db9212057066', '::1', '2026-05-09 16:09:11', '2026-05-09 16:09:12', '2026-05-09 16:09:18', 5, 2),
(14, 2, '5826eb60f3dd050b2cbf7e9b6c69eef5', '::1', '2026-05-09 16:09:19', '2026-05-09 16:09:19', '2026-05-09 16:09:23', 3, 1),
(15, 2, 'cbcc21f1f90b195d9becae7cdff84928', '::1', '2026-05-09 16:09:23', '2026-05-09 16:09:23', '2026-05-09 16:09:26', 3, 1),
(16, 2, 'c1e5d7d1557dbac4d15448f88728c885', '::1', '2026-05-09 16:09:27', '2026-05-09 16:09:27', '2026-05-09 16:09:30', 2, 1),
(17, 2, '1db41877d8a5ba890dd4e56cb537bf21', '::1', '2026-05-09 16:09:30', '2026-05-09 16:09:30', '2026-05-09 16:09:34', 4, 1),
(18, 2, '8fb561ed8570b7338ec3b993cd8a4f38', '::1', '2026-05-09 16:09:36', '2026-05-09 16:09:36', '2026-05-09 16:09:37', 1, 1),
(19, 2, 'f936650d65fbd0b145cb36eaddb7c381', '::1', '2026-05-09 16:09:38', '2026-05-09 16:09:38', '2026-05-09 16:09:43', 4, 1),
(20, 2, 'b21c191947f3e2185d4933f68dbf059c', '::1', '2026-05-09 16:09:45', '2026-05-09 16:09:45', '2026-05-09 16:09:46', 1, 1),
(21, 2, '8f5201d14411455e400a8484b2fd29f1', '::1', '2026-05-09 16:09:46', '2026-05-09 16:09:46', '2026-05-09 16:09:51', 5, 1),
(22, 2, '5abf17364285bdb2a88db086f74c0a5a', '::1', '2026-05-09 16:09:53', '2026-05-09 16:09:53', '2026-05-09 16:09:57', 4, 1),
(23, 2, 'b715e911e1b7f4b442392cd617ebf93c', '::1', '2026-05-09 16:09:57', '2026-05-09 16:09:57', '2026-05-09 16:09:59', 1, 1),
(24, 2, '3006191e147158f94eed2f6dcad0c9b7', '::1', '2026-05-09 16:09:59', '2026-05-09 16:09:59', NULL, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `activity_type` varchar(100) DEFAULT NULL,
  `activity_details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `website_id`, `activity_type`, `activity_details`, `ip_address`, `timestamp`) VALUES
(1, 2, 'pageview', 'Page viewed: http://localhost/m/home.php | Referrer:  | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:00:30'),
(2, 2, 'click', 'Click on button at http://localhost/m/home.php', '::1', '2026-05-09 14:00:33'),
(3, 2, 'session', 'Session duration: 2 seconds', '::1', '2026-05-09 14:00:33'),
(4, 2, 'pageview', 'Page viewed: http://localhost/m/user-login.php | Referrer: http://localhost/m/home.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:00:33'),
(5, 2, 'click', 'Click on input at http://localhost/m/user-login.php', '::1', '2026-05-09 14:00:36'),
(6, 2, 'click', 'Click on input at http://localhost/m/user-login.php', '::1', '2026-05-09 14:00:59'),
(7, 2, 'click', 'Click on span at http://localhost/m/user-login.php', '::1', '2026-05-09 14:01:01'),
(8, 2, 'click', 'Click on button#submit.login-btn  at http://localhost/m/user-login.php', '::1', '2026-05-09 14:01:04'),
(9, 2, 'session', 'Session duration: 30 seconds', '::1', '2026-05-09 14:01:04'),
(10, 2, 'pageview', 'Page viewed: http://localhost/m/home.php | Referrer: http://localhost/m/login_register.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:01:06'),
(11, 2, 'click', 'Click on button at http://localhost/m/home.php', '::1', '2026-05-09 14:01:13'),
(12, 2, 'session', 'Session duration: 7 seconds', '::1', '2026-05-09 14:01:13'),
(13, 2, 'pageview', 'Page viewed: http://localhost/m/signup.php | Referrer: http://localhost/m/home.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:01:13'),
(14, 2, 'click', 'Click on input at http://localhost/m/signup.php', '::1', '2026-05-09 14:01:16'),
(15, 2, 'click', 'Click on form at http://localhost/m/signup.php', '::1', '2026-05-09 14:01:19'),
(16, 2, 'click', 'Click on input at http://localhost/m/signup.php', '::1', '2026-05-09 14:01:22'),
(17, 2, 'click', 'Click on input at http://localhost/m/signup.php', '::1', '2026-05-09 14:01:23'),
(18, 2, 'click', 'Click on button#submit.register-btn  at http://localhost/m/signup.php', '::1', '2026-05-09 14:01:27'),
(19, 2, 'session', 'Session duration: 13 seconds', '::1', '2026-05-09 14:01:27'),
(20, 2, 'pageview', 'Page viewed: http://localhost/m/home.php | Referrer: http://localhost/m/login_register.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:01:34'),
(21, 2, 'pageview', 'Page viewed: http://localhost/m/home.php | Referrer: http://localhost/m/verify.php?email=amanshrivastav2010@gmail.com&v_code=ce922ee550143c15b5ae47067f40277f5e2960d4 | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:01:52'),
(22, 2, 'click', 'Click on button at http://localhost/m/home.php', '::1', '2026-05-09 14:02:27'),
(23, 2, 'session', 'Session duration: 35 seconds', '::1', '2026-05-09 14:02:27'),
(24, 2, 'pageview', 'Page viewed: http://localhost/m/user-login.php | Referrer: http://localhost/m/home.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:02:27'),
(25, 2, 'click', 'Click on button at http://localhost/m/home.php', '::1', '2026-05-09 14:02:41'),
(26, 2, 'session', 'Session duration: 66 seconds', '::1', '2026-05-09 14:02:41'),
(27, 2, 'pageview', 'Page viewed: http://localhost/m/user-login.php | Referrer: http://localhost/m/home.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:02:41'),
(28, 2, 'click', 'Click on button#submit.login-btn  at http://localhost/m/user-login.php', '::1', '2026-05-09 14:02:43'),
(29, 2, 'session', 'Session duration: 1 seconds', '::1', '2026-05-09 14:02:43'),
(30, 2, 'pageview', 'Page viewed: http://localhost/m/home.php | Referrer: http://localhost/m/user-login.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:02:43'),
(31, 2, 'session', 'Session duration: 41 seconds', '::1', '2026-05-09 14:03:09'),
(32, 2, 'click', 'Click on a at http://localhost/m/home.php', '::1', '2026-05-09 14:04:06'),
(33, 2, 'session', 'Session duration: 83 seconds', '::1', '2026-05-09 14:04:06'),
(34, 2, 'pageview', 'Page viewed: http://localhost/m/medicine.php | Referrer: http://localhost/m/home.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:04:06'),
(35, 2, 'click', 'Click on img at http://localhost/m/medicine.php', '::1', '2026-05-09 14:04:09'),
(36, 2, 'click', 'Click on img at http://localhost/m/medicine.php', '::1', '2026-05-09 14:04:11'),
(37, 2, 'click', 'Click on h3 at http://localhost/m/medicine.php', '::1', '2026-05-09 14:04:12'),
(38, 2, 'click', 'Click on h3 at http://localhost/m/medicine.php', '::1', '2026-05-09 14:04:13'),
(39, 2, 'session', 'Session duration: 7 seconds', '::1', '2026-05-09 14:04:14'),
(40, 2, 'pageview', 'Page viewed: http://localhost/m/home.php | Referrer: http://localhost/m/user-login.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:04:14'),
(41, 2, 'click', 'Click on h3 at http://localhost/m/home.php', '::1', '2026-05-09 14:04:17'),
(42, 2, 'click', 'Click on button.cart at http://localhost/m/home.php', '::1', '2026-05-09 14:04:18'),
(43, 2, 'session', 'Session duration: 4 seconds', '::1', '2026-05-09 14:04:18'),
(44, 2, 'pageview', 'Page viewed: http://localhost/m/store.php | Referrer: http://localhost/m/add-cart.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:04:20'),
(45, 2, 'click', 'Click on img at http://localhost/m/store.php', '::1', '2026-05-09 14:04:22'),
(46, 2, 'click', 'Click on i.fas fa-times at http://localhost/m/store.php', '::1', '2026-05-09 14:04:24'),
(47, 2, 'click', 'Click on a at http://localhost/m/store.php', '::1', '2026-05-09 14:04:25'),
(48, 2, 'session', 'Session duration: 5 seconds', '::1', '2026-05-09 14:04:25'),
(49, 2, 'pageview', 'Page viewed: http://localhost/m/cart.php | Referrer: http://localhost/m/store.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:04:25'),
(50, 2, 'click', 'Click on input#exampleFormControlInput1.form-control at http://localhost/m/cart.php', '::1', '2026-05-09 14:04:27'),
(51, 2, 'click', 'Click on input#exampleFormControlInput1.form-control at http://localhost/m/cart.php', '::1', '2026-05-09 14:04:34'),
(52, 2, 'click', 'Click on button.btn btn-block p-3  at http://localhost/m/cart.php', '::1', '2026-05-09 14:04:41'),
(53, 2, 'session', 'Session duration: 15 seconds', '::1', '2026-05-09 14:04:41'),
(54, 2, 'pageview', 'Page viewed: http://localhost/m/checkout2.php | Referrer: http://localhost/m/cart.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:04:42'),
(55, 2, 'click', 'Click on a at http://localhost/m/checkout2.php', '::1', '2026-05-09 14:06:50'),
(56, 2, 'session', 'Session duration: 127 seconds', '::1', '2026-05-09 14:06:50'),
(57, 2, 'pageview', 'Page viewed: http://localhost/m/home.php | Referrer: http://localhost/m/medicine.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:06:54'),
(58, 2, 'click', 'Click on a at http://localhost/m/home.php', '::1', '2026-05-09 14:06:54'),
(59, 2, 'session', 'Session duration: 0 seconds', '::1', '2026-05-09 14:06:54'),
(60, 2, 'pageview', 'Page viewed: http://localhost/m/home.php | Referrer: http://localhost/m/home.php | UA: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '::1', '2026-05-09 14:06:54'),
(61, 2, 'click', 'Click on h1 at http://localhost/m/home.php', '::1', '2026-05-09 14:06:56'),
(62, 2, 'click', 'Click on button.cart at http://localhost/m/home.php', '::1', '2026-05-09 14:06:58'),
(63, 2, 'session', 'Session duration: 3 seconds', '::1', '2026-05-09 14:06:58'),
(64, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/cart.php\",\"page_title\":\"Cart\",\"referrer\":\"http:\\/\\/localhost\\/m\\/add-cart.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local\"}', '::1', '2026-05-09 14:41:53'),
(65, 2, 'click', '{\"element\":\"nav.navbar1 > div.navdiv1 > ul#myul > button\",\"element_text\":\"LOGOUT\",\"page_url\":\"http:\\/\\/localhost\\/m\\/cart.php\",\"coordinates\":{\"x\":1227,\"y\":28}}', '::1', '2026-05-09 14:42:30'),
(66, 2, 'session_end', '{\"duration\":38,\"page_url\":\"http:\\/\\/localhost\\/m\\/cart.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:42:31'),
(67, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"page_title\":\"Home\",\"referrer\":\"http:\\/\\/localhost\\/m\\/cart.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local\"}', '::1', '2026-05-09 14:42:31'),
(68, 2, 'session_end', '{\"duration\":1,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:42:33'),
(69, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"page_title\":\"Login | By Code Info\",\"referrer\":\"http:\\/\\/localhost\\/m\\/home.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local\"}', '::1', '2026-05-09 14:42:33'),
(70, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":631,\"y\":283}}', '::1', '2026-05-09 14:42:34'),
(71, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 14:42:37'),
(72, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:42:37'),
(73, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":524,\"y\":316}}', '::1', '2026-05-09 14:42:37'),
(74, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"page_title\":\"Home\",\"referrer\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local\"}', '::1', '2026-05-09 14:42:38'),
(75, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:42:42'),
(76, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"page_title\":\"Login | By Code Info\",\"referrer\":\"http:\\/\\/localhost\\/m\\/home.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local\"}', '::1', '2026-05-09 14:42:42'),
(77, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":617,\"y\":290}}', '::1', '2026-05-09 14:42:43'),
(78, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 14:42:44'),
(79, 2, 'session_end', '{\"duration\":2,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:42:44'),
(80, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"page_title\":\"Home\",\"referrer\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local\"}', '::1', '2026-05-09 14:42:46'),
(81, 2, 'session_end', '{\"duration\":2,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:42:49'),
(82, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"page_title\":\"Login | By Code Info\",\"referrer\":\"http:\\/\\/localhost\\/m\\/home.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local\"}', '::1', '2026-05-09 14:42:49'),
(83, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":699,\"y\":295}}', '::1', '2026-05-09 14:42:50'),
(84, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 14:42:52'),
(85, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:42:52'),
(86, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"page_title\":\"Home\",\"referrer\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local\"}', '::1', '2026-05-09 14:42:54'),
(87, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:42:58'),
(88, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"page_title\":\"Login | By Code Info\",\"referrer\":\"http:\\/\\/localhost\\/m\\/home.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local\"}', '::1', '2026-05-09 14:42:58'),
(89, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":682,\"y\":281}}', '::1', '2026-05-09 14:42:59'),
(90, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 14:43:01'),
(91, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:43:01'),
(92, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"page_title\":\"Home\",\"referrer\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local\"}', '::1', '2026-05-09 14:43:02'),
(93, 2, 'session_end', '{\"duration\":2,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:43:05'),
(94, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"page_title\":\"Login | By Code Info\",\"referrer\":\"http:\\/\\/localhost\\/m\\/home.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local\"}', '::1', '2026-05-09 14:43:05'),
(95, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 14:43:07'),
(96, 2, 'session_end', '{\"duration\":1,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:43:07'),
(97, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":549,\"y\":308}}', '::1', '2026-05-09 14:43:07'),
(98, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"page_title\":\"Home\",\"referrer\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local\"}', '::1', '2026-05-09 14:43:07'),
(99, 2, 'session_end', '{\"duration\":845,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:12'),
(100, 2, 'click', '{\"element\":\"nav.navbar1 > div.navdiv1 > ul#myul > button > a\",\"element_text\":\"LOGOUT\",\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"coordinates\":{\"x\":1159,\"y\":30}}', '::1', '2026-05-09 14:57:12'),
(101, 2, 'session_end', '{\"duration\":1,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:14'),
(102, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":608,\"y\":291}}', '::1', '2026-05-09 14:57:16'),
(103, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 14:57:18'),
(104, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:18'),
(105, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":546,\"y\":322}}', '::1', '2026-05-09 14:57:18'),
(106, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:22'),
(107, 2, 'click', '{\"element\":\"nav.navbar1 > div.navdiv1 > ul#myul > a > button\",\"element_text\":\"SignIn\",\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"coordinates\":{\"x\":1082,\"y\":32}}', '::1', '2026-05-09 14:57:22'),
(108, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":623,\"y\":293}}', '::1', '2026-05-09 14:57:24'),
(109, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 14:57:26'),
(110, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:26'),
(111, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":541,\"y\":326}}', '::1', '2026-05-09 14:57:26'),
(112, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:31'),
(113, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":669,\"y\":283}}', '::1', '2026-05-09 14:57:32'),
(114, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 14:57:34'),
(115, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:35'),
(116, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":522,\"y\":313}}', '::1', '2026-05-09 14:57:35'),
(117, 2, 'session_end', '{\"duration\":2,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:39'),
(118, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":628,\"y\":286}}', '::1', '2026-05-09 14:57:40'),
(119, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 14:57:42'),
(120, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:42'),
(121, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":504,\"y\":332}}', '::1', '2026-05-09 14:57:42'),
(122, 2, 'session_end', '{\"duration\":2,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:48'),
(123, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 14:57:49'),
(124, 2, 'session_end', '{\"duration\":1,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:49'),
(125, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":557,\"y\":311}}', '::1', '2026-05-09 14:57:49'),
(126, 2, 'session_end', '{\"duration\":2,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:52'),
(127, 2, 'session_end', '{\"duration\":2,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:55'),
(128, 2, 'click', '{\"element\":\"nav.navbar1 > div.navdiv1 > ul#myul > button > a\",\"element_text\":\"LOGOUT\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":1177,\"y\":37}}', '::1', '2026-05-09 14:57:55'),
(129, 2, 'session_end', '{\"duration\":1,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:57:57'),
(130, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":617,\"y\":298}}', '::1', '2026-05-09 14:57:58'),
(131, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 14:58:01'),
(132, 2, 'session_end', '{\"duration\":4,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:58:01'),
(133, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":524,\"y\":333}}', '::1', '2026-05-09 14:58:01'),
(134, 2, 'session_end', '{\"duration\":4,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:58:08'),
(135, 2, 'click', '{\"element\":\"nav.navbar1 > div.navdiv1 > ul#myul > a > button\",\"element_text\":\"SignIn\",\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"coordinates\":{\"x\":1073,\"y\":24}}', '::1', '2026-05-09 14:58:08'),
(136, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":603,\"y\":279}}', '::1', '2026-05-09 14:58:11'),
(137, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 14:58:13'),
(138, 2, 'session_end', '{\"duration\":4,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 14:58:13'),
(139, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":514,\"y\":324}}', '::1', '2026-05-09 14:58:13'),
(140, 2, 'session_end', '{\"duration\":2769,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 15:44:25'),
(141, 2, 'session_end', '{\"duration\":2,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 15:44:27'),
(142, 2, 'click', '{\"element\":\"span > div.login-box > form\",\"element_text\":\"Email\\nPassword\\n Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":658,\"y\":227}}', '::1', '2026-05-09 15:44:37'),
(143, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":669,\"y\":224}}', '::1', '2026-05-09 15:44:38'),
(144, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":702,\"y\":292}}', '::1', '2026-05-09 15:44:42'),
(145, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 15:44:44'),
(146, 2, 'session_end', '{\"duration\":9,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 15:44:44'),
(147, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":531,\"y\":314}}', '::1', '2026-05-09 15:44:44'),
(148, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 15:44:51'),
(149, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":695,\"y\":210}}', '::1', '2026-05-09 15:44:53'),
(150, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":706,\"y\":282}}', '::1', '2026-05-09 15:44:56'),
(151, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 15:44:58'),
(152, 2, 'session_end', '{\"duration\":6,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 15:44:58'),
(153, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":550,\"y\":305}}', '::1', '2026-05-09 15:44:58'),
(154, 2, 'session_end', '{\"duration\":1,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 15:45:02'),
(155, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":692,\"y\":216}}', '::1', '2026-05-09 15:45:03'),
(156, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":664,\"y\":296}}', '::1', '2026-05-09 15:45:08'),
(157, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 15:45:10'),
(158, 2, 'session_end', '{\"duration\":7,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 15:45:10'),
(159, 2, 'session_end', '{\"duration\":1,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 15:45:13'),
(160, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":672,\"y\":215}}', '::1', '2026-05-09 15:45:14'),
(161, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":701,\"y\":273}}', '::1', '2026-05-09 15:45:17'),
(162, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 15:45:19'),
(163, 2, 'session_end', '{\"duration\":6,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 15:45:19'),
(164, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":550,\"y\":316}}', '::1', '2026-05-09 15:45:19'),
(165, 2, 'click', '{\"element\":\"div.banner-container > div.banner-slides > div.banner-slide > img\",\"element_text\":\"\",\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"coordinates\":{\"x\":802,\"y\":105}}', '::1', '2026-05-09 15:45:22'),
(166, 2, 'session_end', '{\"duration\":1,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 15:45:23'),
(167, 2, 'click', '{\"element\":\"span\",\"element_text\":\"MediGuru\\nMedicine Health Care Health Blog Store Cart() SignIn SignUp\\nLogin\\nEmail\\nPassword\\n Login\\n\\nNo\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":860,\"y\":30}}', '::1', '2026-05-09 15:45:25'),
(168, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":651,\"y\":281}}', '::1', '2026-05-09 15:45:27'),
(169, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 15:45:29'),
(170, 2, 'session_end', '{\"duration\":6,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 15:45:29'),
(171, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":512,\"y\":326}}', '::1', '2026-05-09 15:45:29'),
(172, 2, 'session_end', '{\"duration\":1,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 15:45:33'),
(173, 2, 'session_end', '{\"duration\":1483,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:11'),
(174, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"page_title\":\"Login | By Code Info\",\"referrer\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:11'),
(175, 2, 'session_end', '{\"duration\":0,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:12'),
(176, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"page_title\":\"Login | By Code Info\",\"referrer\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:12'),
(177, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"amanshrivastav2010@gmail.com\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":706,\"y\":213}}', '::1', '2026-05-09 16:09:14'),
(178, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":606,\"y\":276}}', '::1', '2026-05-09 16:09:16'),
(179, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 16:09:18'),
(180, 2, 'session_end', '{\"duration\":5,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:18'),
(181, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":544,\"y\":315}}', '::1', '2026-05-09 16:09:18'),
(182, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"page_title\":\"Home\",\"referrer\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:19'),
(183, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:23'),
(184, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"page_title\":\"Login | By Code Info\",\"referrer\":\"http:\\/\\/localhost\\/m\\/home.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:23'),
(185, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":648,\"y\":276}}', '::1', '2026-05-09 16:09:24'),
(186, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 16:09:26'),
(187, 2, 'session_end', '{\"duration\":3,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:26'),
(188, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":560,\"y\":312}}', '::1', '2026-05-09 16:09:26'),
(189, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"page_title\":\"Home\",\"referrer\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:27'),
(190, 2, 'session_end', '{\"duration\":2,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:30'),
(191, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"page_title\":\"Login | By Code Info\",\"referrer\":\"http:\\/\\/localhost\\/m\\/home.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:30'),
(192, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":714,\"y\":280}}', '::1', '2026-05-09 16:09:32'),
(193, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 16:09:34'),
(194, 2, 'session_end', '{\"duration\":4,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:34'),
(195, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":537,\"y\":319}}', '::1', '2026-05-09 16:09:34'),
(196, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"page_title\":\"Home\",\"referrer\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:36'),
(197, 2, 'session_end', '{\"duration\":1,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:37'),
(198, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"page_title\":\"Login | By Code Info\",\"referrer\":\"http:\\/\\/localhost\\/m\\/home.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:37'),
(199, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":718,\"y\":297}}', '::1', '2026-05-09 16:09:40'),
(200, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 16:09:42'),
(201, 2, 'session_end', '{\"duration\":4,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:43'),
(202, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":512,\"y\":325}}', '::1', '2026-05-09 16:09:43'),
(203, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"page_title\":\"Home\",\"referrer\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:45'),
(204, 2, 'session_end', '{\"duration\":1,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:46'),
(205, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"page_title\":\"Login | By Code Info\",\"referrer\":\"http:\\/\\/localhost\\/m\\/home.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:46'),
(206, 2, 'click', '{\"element\":\"span > div.login-box > form > input\",\"element_text\":\"123\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":727,\"y\":285}}', '::1', '2026-05-09 16:09:49'),
(207, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 16:09:51'),
(208, 2, 'session_end', '{\"duration\":5,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:51'),
(209, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"page_title\":\"Home\",\"referrer\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:53'),
(210, 2, 'session_end', '{\"duration\":4,\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:57'),
(211, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"page_title\":\"Login | By Code Info\",\"referrer\":\"http:\\/\\/localhost\\/m\\/home.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:57'),
(212, 2, 'form_submit', '{\"form_id\":\"unnamed\",\"form_action\":\"http:\\/\\/localhost\\/m\\/login_register.php\",\"fields\":[]}', '::1', '2026-05-09 16:09:59'),
(213, 2, 'session_end', '{\"duration\":1,\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"pages_visited\":2}', '::1', '2026-05-09 16:09:59'),
(214, 2, 'click', '{\"element\":\"span > div.login-box > form > button#submit.login-btn\",\"element_text\":\"Login\",\"page_url\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"coordinates\":{\"x\":532,\"y\":330}}', '::1', '2026-05-09 16:09:59'),
(215, 2, 'pageview', '{\"page_url\":\"http:\\/\\/localhost\\/m\\/home.php\",\"page_title\":\"Home\",\"referrer\":\"http:\\/\\/localhost\\/m\\/user-login.php\",\"viewport\":\"1249x577\",\"location\":\"Local, , Local Network\"}', '::1', '2026-05-09 16:09:59');

-- --------------------------------------------------------

--
-- Table structure for table `api_requests`
--

CREATE TABLE `api_requests` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `api_key` varchar(100) DEFAULT NULL,
  `endpoint` varchar(255) DEFAULT NULL,
  `request_ip` varchar(45) DEFAULT NULL,
  `response_time` int(11) DEFAULT NULL,
  `status_code` int(11) DEFAULT NULL,
  `request_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_user_blocks`
--

CREATE TABLE `api_user_blocks` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `is_blocked` tinyint(1) DEFAULT 0,
  `blocked_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `block_reason` varchar(255) DEFAULT NULL,
  `unblocked_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `api_user_blocks`
--

INSERT INTO `api_user_blocks` (`id`, `website_id`, `user_email`, `ip_address`, `failed_attempts`, `is_blocked`, `blocked_until`, `created_at`, `updated_at`, `block_reason`, `unblocked_at`) VALUES
(2, 1, 'demo@example.com', '::1', 4, 1, '2026-05-09 20:57:55', '2026-05-09 10:27:55', '2026-05-09 10:27:55', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `custom_events`
--

CREATE TABLE `custom_events` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `event_name` varchar(100) DEFAULT NULL,
  `event_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_data`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_attempts`
--

CREATE TABLE `failed_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `attempt_count` int(11) DEFAULT 1,
  `blocked_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_login_attempts`
--

CREATE TABLE `failed_login_attempts` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `user_identifier` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `attempt_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `failed_login_attempts`
--

INSERT INTO `failed_login_attempts` (`id`, `website_id`, `user_identifier`, `ip_address`, `location`, `user_agent`, `attempt_time`) VALUES
(6, 1, 'demo@example.com', '::1', NULL, NULL, '2026-05-09 15:57:52'),
(7, 1, 'demo@example.com', '::1', NULL, NULL, '2026-05-09 15:57:53'),
(8, 1, 'demo@example.com', '::1', NULL, NULL, '2026-05-09 15:57:54'),
(9, 1, 'demo@example.com', '::1', NULL, NULL, '2026-05-09 15:57:55');

-- --------------------------------------------------------

--
-- Table structure for table `ip_blacklist`
--

CREATE TABLE `ip_blacklist` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `reason` text DEFAULT NULL,
  `blocked_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip_location_cache`
--

CREATE TABLE `ip_location_cache` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `country_code` varchar(5) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `lat` decimal(10,6) DEFAULT NULL,
  `lon` decimal(10,6) DEFAULT NULL,
  `isp` varchar(255) DEFAULT NULL,
  `is_proxy` tinyint(1) DEFAULT 0,
  `is_vpn` tinyint(1) DEFAULT 0,
  `risk_score` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `user_identifier` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `device` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_time_tracking`
--

CREATE TABLE `page_time_tracking` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `page_url` text DEFAULT NULL,
  `total_time` int(11) DEFAULT 0,
  `last_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_time_tracking`
--

INSERT INTO `page_time_tracking` (`id`, `website_id`, `ip_address`, `page_url`, `total_time`, `last_update`) VALUES
(1, 2, '::1', 'http://localhost/m/cart.php', 40, '2026-05-09 14:42:23'),
(3, 2, '::1', 'http://localhost/m/home.php', 2820, '2026-05-09 16:11:59'),
(17, 2, '::1', 'http://localhost/m/user-login.php', 800, '2026-05-09 15:49:28');

-- --------------------------------------------------------

--
-- Table structure for table `realtime_visitors`
--

CREATE TABLE `realtime_visitors` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `current_page` varchar(500) DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `first_seen` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_events`
--

CREATE TABLE `security_events` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `user_identifier` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `event_details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `email_verified` tinyint(1) DEFAULT 0,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `email_verified`, `otp_code`, `otp_expires`, `created_at`) VALUES
(12, 'Administrator', 'admin@trustnet.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL, NULL, '2026-05-09 07:31:45'),
(13, 'Aman', 'amanshrivastav8388@gmail.com', '$2y$10$HtAVhC6LHZaknqPRHaZGgeJK7XY/MpL4qd8FjA65ivUf4x5btoxHe', 'admin', 1, NULL, NULL, '2026-05-09 07:31:55'),
(16, 'Aman Shrivastav', 'amanshrivastav2010@gmail.com', '$2y$12$5JFyCOKv2VHskFrXn0CoIuPn8jcjAEjjwkXeVQgpD4eF9K9DFagzW', 'user', 1, NULL, NULL, '2026-05-09 07:43:11');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity`
--

CREATE TABLE `user_activity` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_ip` varchar(45) DEFAULT NULL,
  `first_visit` datetime DEFAULT NULL,
  `last_visit` datetime DEFAULT NULL,
  `total_visits` int(11) DEFAULT 1,
  `total_time_spent` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `websites`
--

CREATE TABLE `websites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `website_name` varchar(255) NOT NULL,
  `website_url` varchar(255) NOT NULL,
  `api_key` varchar(100) NOT NULL,
  `secret_key` varchar(100) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_visits` int(11) DEFAULT 0,
  `total_pageviews` int(11) DEFAULT 0,
  `last_activity` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `websites`
--

INSERT INTO `websites` (`id`, `user_id`, `website_name`, `website_url`, `api_key`, `secret_key`, `status`, `created_at`, `total_visits`, `total_pageviews`, `last_activity`) VALUES
(1, 16, 'TechTrang', 'https://mcadypimed.in/', 'TRN_3bb3bcffe3ed5e7c8294bd6297e834cd', 'SK_f841626eef789e161b13f4fbb40f10fee0cf6a5d61ea02c7', 'active', '2026-05-09 07:48:08', 0, 0, NULL),
(2, 16, 'm', 'http://localhost/m/', 'TRN_397b8d85340a1c040f105c9bfd8dda49', 'SK_6917abe768039c6b129b6e6623862aaefad980b1f3ceea6d', 'active', '2026-05-09 08:28:11', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `website_stats`
--

CREATE TABLE `website_stats` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `visits` int(11) DEFAULT 0,
  `pageviews` int(11) DEFAULT 0,
  `unique_visitors` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `website_users`
--

CREATE TABLE `website_users` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('active','blocked') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `website_users`
--

INSERT INTO `website_users` (`id`, `website_id`, `email`, `password`, `name`, `status`, `created_at`, `last_login`) VALUES
(1, 1, 'test@example.com', '$2y$10$Rr66LkgRwdNGdXRyALhWhefzTHJHz7.b2LGGB.u5ZuZ1WzOR1VIIi', 'Test User', 'active', '2026-05-09 10:09:12', NULL),
(2, 1, 'demo@example.com', '$2y$10$SxyPWWVTSz.ZwbpAD1MbvegT5OA.HtOruUjJxBa9dmkt8ZnhQPQZq', 'Demo User', 'active', '2026-05-09 10:09:12', NULL),
(3, 1, 'user@test.com', '$2y$10$2CzFoMAp2cvbQ8Nu4pC4Ne.EHKLA6jPXj3Z24PYUlU4KjXEM/NvPu', 'Regular User', 'active', '2026-05-09 10:09:13', NULL),
(4, 2, 'test@example.com', '$2y$10$P1nG1KGnQ5VJeM43gVP4GuYAsMeGlJyl.DPEUQUSM39IOAZqTLJgS', 'Test User', 'active', '2026-05-09 10:09:13', NULL),
(5, 2, 'demo@example.com', '$2y$10$kfujHshu13GgYcA6ofq7ku4zMrgXdvLb122INIvIaNmY7zAowsYqK', 'Demo User', 'active', '2026-05-09 10:09:13', NULL),
(6, 2, 'user@test.com', '$2y$10$M/gnHHRz94.lUlZJA3q..OK9bS6z0wALN3GIbtOxOY1RFLGT2tdWa', 'Regular User', 'active', '2026-05-09 10:09:13', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `website_id` (`website_id`),
  ADD KEY `idx_session_id` (`session_id`),
  ADD KEY `idx_last_activity` (`last_activity`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_website` (`website_id`,`timestamp`);

--
-- Indexes for table `api_requests`
--
ALTER TABLE `api_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `website_id` (`website_id`);

--
-- Indexes for table `api_user_blocks`
--
ALTER TABLE `api_user_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `website_id` (`website_id`),
  ADD KEY `idx_user_email` (`user_email`),
  ADD KEY `idx_blocked_until` (`blocked_until`);

--
-- Indexes for table `custom_events`
--
ALTER TABLE `custom_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `website_id` (`website_id`),
  ADD KEY `idx_event_name` (`event_name`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `failed_attempts`
--
ALTER TABLE `failed_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_login_attempts`
--
ALTER TABLE `failed_login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_website_user` (`website_id`,`user_identifier`),
  ADD KEY `idx_ip` (`ip_address`),
  ADD KEY `idx_time` (`attempt_time`);

--
-- Indexes for table `ip_blacklist`
--
ALTER TABLE `ip_blacklist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`),
  ADD KEY `blocked_by` (`blocked_by`);

--
-- Indexes for table `ip_location_cache`
--
ALTER TABLE `ip_location_cache`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`),
  ADD KEY `idx_ip` (`ip_address`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_login_website` (`website_id`,`login_time`);

--
-- Indexes for table `page_time_tracking`
--
ALTER TABLE `page_time_tracking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_page_ip` (`website_id`,`ip_address`,`page_url`(255)),
  ADD KEY `idx_website_ip` (`website_id`,`ip_address`);

--
-- Indexes for table `realtime_visitors`
--
ALTER TABLE `realtime_visitors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_last_activity` (`last_activity`),
  ADD KEY `idx_website` (`website_id`);

--
-- Indexes for table `security_events`
--
ALTER TABLE `security_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_website` (`website_id`),
  ADD KEY `idx_ip` (`ip_address`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_email` (`user_email`),
  ADD KEY `idx_website_user` (`website_id`,`user_email`);

--
-- Indexes for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `websites`
--
ALTER TABLE `websites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_key` (`api_key`),
  ADD UNIQUE KEY `secret_key` (`secret_key`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_website_api` (`api_key`);

--
-- Indexes for table `website_stats`
--
ALTER TABLE `website_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_daily_stats` (`website_id`,`date`),
  ADD KEY `idx_date` (`date`);

--
-- Indexes for table `website_users`
--
ALTER TABLE `website_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_website_email` (`website_id`,`email`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `active_sessions`
--
ALTER TABLE `active_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT for table `api_requests`
--
ALTER TABLE `api_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_user_blocks`
--
ALTER TABLE `api_user_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `custom_events`
--
ALTER TABLE `custom_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_attempts`
--
ALTER TABLE `failed_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_login_attempts`
--
ALTER TABLE `failed_login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ip_blacklist`
--
ALTER TABLE `ip_blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip_location_cache`
--
ALTER TABLE `ip_location_cache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_time_tracking`
--
ALTER TABLE `page_time_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `realtime_visitors`
--
ALTER TABLE `realtime_visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_events`
--
ALTER TABLE `security_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `websites`
--
ALTER TABLE `websites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `website_stats`
--
ALTER TABLE `website_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `website_users`
--
ALTER TABLE `website_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD CONSTRAINT `active_sessions_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `api_requests`
--
ALTER TABLE `api_requests`
  ADD CONSTRAINT `api_requests_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `api_user_blocks`
--
ALTER TABLE `api_user_blocks`
  ADD CONSTRAINT `api_user_blocks_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `custom_events`
--
ALTER TABLE `custom_events`
  ADD CONSTRAINT `custom_events_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ip_blacklist`
--
ALTER TABLE `ip_blacklist`
  ADD CONSTRAINT `ip_blacklist_ibfk_1` FOREIGN KEY (`blocked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `realtime_visitors`
--
ALTER TABLE `realtime_visitors`
  ADD CONSTRAINT `realtime_visitors_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `security_events`
--
ALTER TABLE `security_events`
  ADD CONSTRAINT `security_events_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD CONSTRAINT `user_activity_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `websites`
--
ALTER TABLE `websites`
  ADD CONSTRAINT `websites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `website_stats`
--
ALTER TABLE `website_stats`
  ADD CONSTRAINT `website_stats_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `website_users`
--
ALTER TABLE `website_users`
  ADD CONSTRAINT `website_users_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `websites` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
