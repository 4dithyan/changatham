-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2025 at 06:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `changatham`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `name`, `description`, `image`, `created_at`) VALUES
(1, 'Yoga & Meditation', 'Experience peaceful yoga and meditation sessions in serene natural surroundings.', 'yoga.jpg', '2025-10-01 06:10:34'),
(2, 'Campfire', 'Enjoy evening campfire with music and storytelling under the starry sky.', 'campfire.jpg', '2025-10-01 06:10:34'),
(3, 'Story Telling', 'Listen to fascinating stories about local culture and legends.', 'storytelling.jpg', '2025-10-01 06:10:34'),
(4, 'Trekking', 'Explore scenic trails and enjoy breathtaking views of the landscape.', 'trekking.jpg', '2025-10-01 06:10:34'),
(5, 'Boating', 'Enjoy peaceful boating in the pristine backwaters.', 'boating.jpg', '2025-10-01 06:10:34'),
(6, 'Cultural Dance', 'Experience traditional Kerala cultural dance performances with local artists.', 'dance.jpg', '2025-10-01 07:07:34'),
(7, 'Spa & Wellness', 'Relax and rejuvenate with traditional Ayurvedic spa treatments and wellness therapies.', 'spa.jpg', '2025-10-01 07:07:34'),
(8, 'Bird Watching', 'Explore the rich avian diversity of Kerala with expert bird watchers in natural habitats.', 'birdwatching.jpg', '2025-10-01 07:07:34'),
(9, 'Cooking Class', 'Learn to prepare authentic Kerala cuisine with hands-on cooking classes.', 'cooking.jpg', '2025-10-01 07:07:34'),
(10, 'Photography Tour', 'Capture the beauty of Kerala with guided photography tours to scenic locations.', 'photography.jpg', '2025-10-01 07:07:34');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `age` int(11) NOT NULL,
  `aadhaar_no` varchar(12) NOT NULL,
  `booking_date` date NOT NULL,
  `payment_type` enum('advance','full') NOT NULL,
  `advance_amount` decimal(10,2) NOT NULL,
  `remaining_amount` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Cancelled','Scheduled','Delayed') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `bus_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`bus_id`, `name`, `type`, `capacity`, `description`, `image`, `created_at`) VALUES
(1, 'Volvo AC Multi-Axle', 'AC', 40, 'Premium AC bus with comfortable seating and entertainment system.', 'volvo_ac.jpg', '2025-10-01 00:40:34'),
(2, 'Scania AC Semi-Sleeper', 'AC', 35, 'Comfortable AC semi-sleeper bus with adjustable seats.', 'wallhaven-7p53ve_1920x1080.png', '2025-10-01 00:40:34'),
(3, 'Mercedes Benz Non-AC', 'Non-AC', 45, 'Spacious non-AC bus with comfortable seating.', '2325386-2800x1866-desktop-hd-porsche-911-gt3-rs-wallpaper-photo.jpg', '2025-10-01 00:40:34');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `driver_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`driver_id`, `name`, `phone`, `created_at`) VALUES
(1, 'John Smith', '9876543210', '2025-10-01 06:10:34'),
(2, 'Robert Johnson', '9876543211', '2025-10-01 06:10:34'),
(3, 'Michael Brown', '9876543212', '2025-10-01 06:10:34'),
(4, 'sai', '1234567890', '2025-10-01 08:28:50');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `image_path`, `description`, `created_at`) VALUES
(1, 'Backwaters of Alleppey', 'images/backwaters.jpg', 'Experience the serene beauty of Kerala\'s backwaters on a traditional houseboat.', '2025-10-01 06:35:17'),
(2, 'Munnar Hills', 'images/munnar.jpg', 'Explore the lush tea gardens and misty hills of Munnar, a hill station in Kerala.', '2025-10-01 06:35:17'),
(3, 'Athirappally Waterfalls', 'images/athirappally.jpg', 'Visit the magnificent Athirappally Waterfalls, known as the \"Niagara of India\".', '2025-10-01 06:35:17'),
(4, 'Wayanad Landscape', 'images/wayanad.jpg', 'Discover the scenic beauty and wildlife of Wayanad district in Kerala.', '2025-10-01 06:35:17'),
(5, 'Kerala Beach', 'images/beach.jpg', 'Relax on the pristine beaches of Kerala with golden sand and clear blue waters.', '2025-10-01 06:35:17'),
(6, 'Traditional Kerala House', 'images/staymain.jpg', 'Experience authentic Kerala architecture and hospitality in traditional homes.', '2025-10-01 06:35:17');

-- --------------------------------------------------------

--
-- Table structure for table `guides`
--

CREATE TABLE `guides` (
  `guide_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guides`
--

INSERT INTO `guides` (`guide_id`, `name`, `phone`, `created_at`) VALUES
(1, 'David Wilson', '9876543213', '2025-10-01 06:10:34'),
(2, 'James Davis', '9876543214', '2025-10-01 06:10:34'),
(3, 'William Miller', '9876543215', '2025-10-01 06:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `package_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','danger','success') NOT NULL DEFAULT 'info',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `package_id`, `title`, `message`, `type`, `created_at`) VALUES
(11, 14, 'Booking Confirmed', 'Your booking for package \'Kovalam Experience Tour 14\' has been confirmed.', 'info', '2025-10-01 12:19:07'),
(12, 14, 'Booking Confirmed', 'Your booking for package \'Kovalam Experience Tour 14\' has been confirmed.', 'info', '2025-10-01 12:33:43'),
(13, 15, 'Booking Confirmed', 'Your booking for package \'Thrissur Experience Tour 15\' has been confirmed.', 'info', '2025-11-08 09:09:16'),
(14, 14, 'Booking Confirmed', 'Your booking for package \'Kovalam Experience Tour 14\' has been confirmed.', 'info', '2025-11-08 09:14:23'),
(15, 14, 'Payment Completed', 'Your payment for booking #23 for \'Kovalam Experience Tour 14\' has been completed successfully. Your booking is now fully paid.', 'success', '2025-11-08 09:14:46');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `food_type` varchar(100) NOT NULL,
  `accommodation` varchar(100) NOT NULL,
  `available_slots` int(11) NOT NULL,
  `pickup_point` varchar(255) NOT NULL,
  `dropoff_point` varchar(255) NOT NULL,
  `transportation` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `day1_schedule` text DEFAULT NULL,
  `day2_schedule` text DEFAULT NULL,
  `day3_schedule` text DEFAULT NULL,
  `day4_schedule` text DEFAULT NULL,
  `day5_schedule` text DEFAULT NULL,
  `day6_schedule` text DEFAULT NULL,
  `day7_schedule` text DEFAULT NULL,
  `day8_schedule` text DEFAULT NULL,
  `day9_schedule` text DEFAULT NULL,
  `day10_schedule` text DEFAULT NULL,
  `additional_pickups` text DEFAULT NULL,
  `food_menu` text DEFAULT NULL,
  `stay_details` text DEFAULT NULL,
  `transportation_details` text DEFAULT NULL,
  `activities` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `breakfast` text DEFAULT NULL,
  `lunch` text DEFAULT NULL,
  `dinner` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `daily_start_time` time DEFAULT NULL,
  `daily_end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`package_id`, `destination`, `name`, `price`, `duration`, `description`, `food_type`, `accommodation`, `available_slots`, `pickup_point`, `dropoff_point`, `transportation`, `image`, `day1_schedule`, `day2_schedule`, `day3_schedule`, `day4_schedule`, `day5_schedule`, `day6_schedule`, `day7_schedule`, `day8_schedule`, `day9_schedule`, `day10_schedule`, `additional_pickups`, `food_menu`, `stay_details`, `transportation_details`, `activities`, `created_at`, `breakfast`, `lunch`, `dinner`, `start_date`, `end_date`, `daily_start_time`, `daily_end_time`) VALUES
(5, 'Trivandrum', 'Trivandrum Adventure Tour 5', 11118.00, '4 Days 3 Nights', 'Experience the beauty of Trivandrum with this exciting tour package that includes sightseeing, cultural experiences, and adventure activities.', 'Continental', '4 Star Hotel', 19, 'Munnar', 'Palakkad', 'Innova', 'ellappatty.jpg', 'Arrival at Trivandrum and check-in at accommodation. Evening leisure activities.', 'Full day sightseeing tour of Trivandrum including major attractions and cultural sites.', 'Adventure activities and local experiences in Trivandrum.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Day 1: Welcome dinner with local cuisine\nDay 2: Breakfast, Lunch, Dinner - Continental\nDay 3: Breakfast, Lunch - Local specialities', 'Day 1-3: 4 Star Hotel in Trivandrum (3 Star)', 'Innova with experienced driver. All transfers and sightseeing by private vehicle.', '10, 5, 10', '2025-10-01 11:55:27', 'Light breakfast with fresh fruit platter and yogurt', 'Vegetarian thali with seasonal vegetables and regional specialties', 'Fusion cuisine combining local and international flavors', '2025-10-25', '2025-10-30', '07:00:00', '19:00:00'),
(8, 'Kovalam', 'Kovalam Experience Tour 8', 7833.00, '4 Days 3 Nights', 'Experience the beauty of Kovalam with this exciting tour package that includes sightseeing, cultural experiences, and adventure activities.', 'Vegetarian', 'Resort', 18, 'Palakkad', 'Thrissur', 'AC Bus', 'wayanad.jpg', 'Arrival at Kovalam and check-in at accommodation. Evening leisure activities.', 'Full day sightseeing tour of Kovalam including major attractions and cultural sites.', 'Adventure activities and local experiences in Kovalam.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Day 1: Welcome dinner with local cuisine\nDay 2: Breakfast, Lunch, Dinner - Vegetarian\nDay 3: Breakfast, Lunch - Local specialities', 'Day 1-3: Resort in Kovalam (3 Star)', 'AC Bus with experienced driver. All transfers and sightseeing by private vehicle.', '5, 10, 3', '2025-10-01 12:02:25', 'Kerala breakfast with puttu, kadala curry, and parotta', 'Spice garden lunch with freshly picked herbs and spices', 'Backwater dinner cruise with traditional Kerala meals', NULL, NULL, NULL, NULL),
(9, 'Alleppey', 'Alleppey Experience Tour 9', 21580.00, '4 Days 3 Nights', 'Experience the beauty of Alleppey with this exciting tour package that includes sightseeing, cultural experiences, and adventure activities.', 'Non-Vegetarian', 'Homestay', 11, 'Thekkady', 'Varkala', 'Innova', 'pak_vattavada.webp', 'Arrival at Alleppey and check-in at accommodation. Evening leisure activities.', 'Full day sightseeing tour of Alleppey including major attractions and cultural sites.', 'Adventure activities and local experiences in Alleppey.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Day 1: Welcome dinner with local cuisine\nDay 2: Breakfast, Lunch, Dinner - Non-Vegetarian\nDay 3: Breakfast, Lunch - Local specialities', 'Day 1-3: Homestay in Alleppey (3 Star)', 'Innova with experienced driver. All transfers and sightseeing by private vehicle.', '3, 3, 3', '2025-10-01 12:02:25', 'Houseboat breakfast with tea/coffee and local snacks', 'Coconut-based cuisine with fresh seafood and vegetables', 'Romantic candlelight dinner on the houseboat deck', NULL, NULL, NULL, NULL),
(10, 'Munnar', 'Munnar Experience Tour 10', 9069.00, '8 Days 7 Nights', 'Experience the beauty of Munnar with this exciting tour package that includes sightseeing, cultural experiences, and adventure activities.', 'Multi-Cuisine', 'Cottage', 8, 'Munnar', 'Varkala', 'AC SUV', 'munnar.jpg', 'Arrival at Munnar and check-in at accommodation. Evening leisure activities.', 'Full day sightseeing tour of Munnar including major attractions and cultural sites.', 'Adventure activities and local experiences in Munnar.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Day 1: Welcome dinner with local cuisine\nDay 2: Breakfast, Lunch, Dinner - Multi-Cuisine\nDay 3: Breakfast, Lunch - Local specialities', 'Day 1-3: Cottage in Munnar (3 Star)', 'AC SUV with experienced driver. All transfers and sightseeing by private vehicle.', '10, 8, 9', '2025-10-01 12:02:25', 'Hill station breakfast with homemade bread and jams', 'Tea plantation lunch with organic vegetables and herbs', 'Campfire dinner with grilled meats and local breads', NULL, NULL, NULL, NULL),
(13, 'Trivandrum', 'Trivandrum Experience Tour 13', 3623.00, '2 Days 1 Nights', 'Experience the beauty of Trivandrum with this exciting tour package that includes sightseeing, cultural experiences, and adventure activities.', 'Kerala Specialities', 'Houseboat', 13, 'Trivandrum', 'Alleppey', 'Innova', 'yoga.jpg', 'Arrival at Trivandrum and check-in at accommodation. Evening leisure activities.', 'Full day sightseeing tour of Trivandrum including major attractions and cultural sites.', 'Adventure activities and local experiences in Trivandrum.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Day 1: Welcome dinner with local cuisine\nDay 2: Breakfast, Lunch, Dinner - Kerala Specialities\nDay 3: Breakfast, Lunch - Local specialities', 'Day 1-3: Houseboat in Trivandrum (3 Star)', 'Innova with experienced driver. All transfers and sightseeing by private vehicle.', '1, 1, 5', '2025-10-01 12:02:25', 'Trivandrum breakfast with local delicacies and temple food', 'Palace cuisine with royal recipes and traditional methods', 'Beachside dinner with fresh seafood and coconut-based dishes', NULL, NULL, NULL, NULL),
(14, 'Kovalam', 'Kovalam Experience Tour 14', 10050.00, '8 Days 7 Nights', 'Experience the beauty of Kovalam with this exciting tour package that includes sightseeing, cultural experiences, and adventure activities.', 'Vegetarian', '3 Star Hotel', 9, 'Munnar', 'Wayanad', 'Private Taxi', 'pak_ellappetty.jpg', 'Arrival at Kovalam and check-in at accommodation. Evening leisure activities.', 'Full day sightseeing tour of Kovalam including major attractions and cultural sites.', 'Adventure activities and local experiences in Kovalam.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Day 1: Welcome dinner with local cuisine\nDay 2: Breakfast, Lunch, Dinner - Vegetarian\nDay 3: Breakfast, Lunch - Local specialities', 'Day 1-3: 3 Star Hotel in Kovalam (3 Star)', 'Private Taxi with experienced driver. All transfers and sightseeing by private vehicle.', '6, 8, 3', '2025-10-01 12:02:25', 'Kerala-Muslim breakfast with pathiri and beef curry', 'Biriyani and grilled meats with traditional accompaniments', 'Iftar-style dinner with Middle Eastern and Kerala fusion', NULL, NULL, NULL, NULL),
(15, 'Thrissur', 'Thrissur Experience Tour 15', 3673.00, '8 Days 7 Nights', 'Experience the beauty of Thrissur with this exciting tour package that includes sightseeing, cultural experiences, and adventure activities.', 'South Indian', 'Boutique Hotel', 15, 'Wayanad', 'Varkala', 'Innova', 'pak_ellappetty1.jpg', 'Arrival at Thrissur and check-in at accommodation. Evening leisure activities.', 'Full day sightseeing tour of Thrissur including major attractions and cultural sites.', 'Adventure activities and local experiences in Thrissur.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Day 1: Welcome dinner with local cuisine\nDay 2: Breakfast, Lunch, Dinner - South Indian\nDay 3: Breakfast, Lunch - Local specialities', 'Day 1-3: Boutique Hotel in Thrissur (3 Star)', 'Innova with experienced driver. All transfers and sightseeing by private vehicle.', '1, 1, 3', '2025-10-01 12:02:25', 'Thrissur breakfast with traditional sweets and snacks', 'Pooram feast with elaborate vegetarian preparations', 'Cultural dinner with Theyyam performance and traditional meals', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `package_activities`
--

CREATE TABLE `package_activities` (
  `package_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_activities`
--

INSERT INTO `package_activities` (`package_id`, `activity_id`) VALUES
(5, 1),
(5, 4),
(5, 10),
(8, 1),
(8, 3),
(8, 9),
(9, 5),
(9, 6),
(9, 10),
(10, 5),
(10, 9),
(10, 10),
(13, 5),
(13, 6),
(13, 8),
(14, 4),
(14, 5),
(14, 8),
(15, 3),
(15, 9),
(15, 10);

-- --------------------------------------------------------

--
-- Table structure for table `package_buses`
--

CREATE TABLE `package_buses` (
  `package_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_buses`
--

INSERT INTO `package_buses` (`package_id`, `bus_id`) VALUES
(8, 3),
(9, 1),
(10, 3),
(13, 1),
(14, 2),
(15, 1);

-- --------------------------------------------------------

--
-- Table structure for table `package_drivers`
--

CREATE TABLE `package_drivers` (
  `package_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_drivers`
--

INSERT INTO `package_drivers` (`package_id`, `driver_id`) VALUES
(8, 4),
(9, 4),
(10, 4),
(13, 2),
(14, 3),
(15, 3);

-- --------------------------------------------------------

--
-- Table structure for table `package_guides`
--

CREATE TABLE `package_guides` (
  `package_id` int(11) NOT NULL,
  `guide_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_guides`
--

INSERT INTO `package_guides` (`package_id`, `guide_id`) VALUES
(8, 2),
(9, 3),
(10, 1),
(13, 3),
(14, 1),
(15, 2);

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_ph` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ROLE` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`user_id`, `name`, `email`, `user_ph`, `password`, `ROLE`, `created_at`) VALUES
(1, 'Admin User', 'admin@changatham.com', '9876543210', '$2y$10$UbnoJsd1TLD03a5fusoQFuMs9Ey1WJ.pA0qjK9s/h6/FtOB6U8HSW', 'admin', '2025-10-01 06:10:34'),
(20, 'Adithyan M', 'mailforadithyan@gmail.com', '9778238064', '$2y$10$gxn46BhdG17gYMjk.VLLoOXGXRo08BohuU/6fWhMBlR7e/ndTV2uO', 'user', '2025-11-21 17:35:54');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `package_id`, `rating`, `review_text`, `created_at`) VALUES
(4, 20, 9, 5, 'Test Reviewwwwww...!!', '2025-11-21 17:36:48');

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`bus_id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`driver_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guides`
--
ALTER TABLE `guides`
  ADD PRIMARY KEY (`guide_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `package_activities`
--
ALTER TABLE `package_activities`
  ADD PRIMARY KEY (`package_id`,`activity_id`),
  ADD KEY `activity_id` (`activity_id`);

--
-- Indexes for table `package_buses`
--
ALTER TABLE `package_buses`
  ADD PRIMARY KEY (`package_id`,`bus_id`),
  ADD KEY `bus_id` (`bus_id`);

--
-- Indexes for table `package_drivers`
--
ALTER TABLE `package_drivers`
  ADD PRIMARY KEY (`package_id`,`driver_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `package_guides`
--
ALTER TABLE `package_guides`
  ADD PRIMARY KEY (`package_id`,`guide_id`),
  ADD KEY `guide_id` (`guide_id`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `notification_id` (`notification_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `bus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `guide_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `register` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE;

--
-- Constraints for table `package_activities`
--
ALTER TABLE `package_activities`
  ADD CONSTRAINT `package_activities_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_activities_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE;

--
-- Constraints for table `package_buses`
--
ALTER TABLE `package_buses`
  ADD CONSTRAINT `package_buses_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_buses_ibfk_2` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`bus_id`) ON DELETE CASCADE;

--
-- Constraints for table `package_drivers`
--
ALTER TABLE `package_drivers`
  ADD CONSTRAINT `package_drivers_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_drivers_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`) ON DELETE CASCADE;

--
-- Constraints for table `package_guides`
--
ALTER TABLE `package_guides`
  ADD CONSTRAINT `package_guides_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_guides_ibfk_2` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`guide_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `register` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `user_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `register` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_notifications_ibfk_2` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
