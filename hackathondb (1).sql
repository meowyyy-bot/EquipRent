-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2025 at 02:04 AM
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
-- Database: `hackathondb`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `category_icon` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `category_icon`, `description`, `is_active`) VALUES
(1, 'Tools', 'fas fa-tools', 'Hand tools, power tools, and construction equipment', 1),
(2, 'Kitchen Appliances', 'fas fa-utensils', 'Cooking appliances, blenders, mixers, and kitchen tools', 1),
(3, 'Heavy Equipment', 'fas fa-truck', 'Construction machinery, excavators, and heavy vehicles', 1),
(4, 'Electronics', 'fas fa-laptop', 'Computers, cameras, audio equipment, and gadgets', 1),
(5, 'Furniture', 'fas fa-couch', 'Tables, chairs, sofas, and home furnishings', 1),
(6, 'Garden & Outdoor', 'fas fa-seedling', 'Lawn mowers, gardening tools, and outdoor equipment', 1),
(7, 'Sports Equipment', 'fas fa-futbol', 'Exercise machines, sports gear, and fitness equipment', 1),
(8, 'Party & Events', 'fas fa-birthday-cake', 'Tents, chairs, tables, and event equipment', 1),
(9, 'Cleaning Equipment', 'fas fa-broom', 'Vacuum cleaners, pressure washers, and cleaning tools', 1),
(10, 'Medical Equipment', 'fas fa-stethoscope', 'Medical devices, wheelchairs, and health equipment', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `rental_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disputes`
--

CREATE TABLE `disputes` (
  `dispute_id` int(11) NOT NULL,
  `rental_id` int(11) NOT NULL,
  `raised_by` int(11) NOT NULL,
  `issue_description` text DEFAULT NULL,
  `dispute_status` enum('open','in_review','resolved','rejected') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `equipment_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `weekly_rate` decimal(10,2) DEFAULT NULL,
  `monthly_rate` decimal(10,2) DEFAULT NULL,
  `location` varchar(100) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `condition_status` enum('excellent','good','fair','poor') DEFAULT 'good',
  `replacement_cost` decimal(10,2) DEFAULT NULL,
  `deposit_amount` decimal(10,2) DEFAULT NULL,
  `minimum_rental_days` int(11) DEFAULT 1,
  `maximum_rental_days` int(11) DEFAULT 30,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `owner_id`, `category_id`, `name`, `description`, `daily_rate`, `hourly_rate`, `weekly_rate`, `monthly_rate`, `location`, `latitude`, `longitude`, `brand`, `model`, `condition_status`, `replacement_cost`, `deposit_amount`, `minimum_rental_days`, `maximum_rental_days`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Professional Drill Set', 'Complete drill set with multiple attachments, perfect for construction and DIY projects. Includes 20V battery, 15+ drill bits, and carrying case.', 800.00, 100.00, 4500.00, 15000.00, 'Manila, Metro Manila', 14.59950000, 120.98420000, 'DeWalt', 'DCD777C2', 'excellent', 15000.00, 2000.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(2, 1, 1, 'Circular Saw', '7-1/4\" circular saw with carbide blade. Perfect for cutting wood, plywood, and other materials. Includes safety guard and depth adjustment.', 600.00, 75.00, 3200.00, 12000.00, 'Manila, Metro Manila', 14.59950000, 120.98420000, 'Makita', '5007MG', 'good', 12000.00, 1500.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(3, 1, 1, 'Air Compressor', '20-gallon air compressor with 2.5 HP motor. Ideal for powering air tools, painting, and inflating tires. Includes pressure regulator and safety valve.', 1200.00, 150.00, 6500.00, 25000.00, 'Manila, Metro Manila', 14.59950000, 120.98420000, 'Porter Cable', 'C2002', 'good', 25000.00, 3000.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(4, 2, 2, 'Professional Stand Mixer', 'Heavy-duty stand mixer perfect for baking. Includes dough hook, flat beater, and wire whip. 5-quart capacity with 10-speed control.', 500.00, 60.00, 2800.00, 10000.00, 'Quezon City, Metro Manila', 14.67600000, 121.04370000, 'KitchenAid', 'KSM150PS', 'excellent', 10000.00, 1000.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(5, 2, 2, 'Commercial Blender', 'High-powered commercial blender for smoothies, soups, and purees. 64-ounce capacity with 6-speed control and pulse function.', 400.00, 50.00, 2200.00, 8000.00, 'Quezon City, Metro Manila', 14.67600000, 121.04370000, 'Vitamix', '5200', 'excellent', 8000.00, 800.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(6, 2, 2, 'Food Processor', 'Multi-functional food processor with multiple blades and attachments. Perfect for chopping, slicing, shredding, and pureeing ingredients.', 350.00, 45.00, 1900.00, 7000.00, 'Quezon City, Metro Manila', 14.67600000, 121.04370000, 'Cuisinart', 'FP-8SV', 'good', 7000.00, 700.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(7, 1, 3, 'Mini Excavator', 'Compact mini excavator for small construction projects. 1.5-ton capacity with hydraulic thumb and rubber tracks. Perfect for digging, trenching, and landscaping.', 8000.00, 1000.00, 45000.00, 180000.00, 'Makati, Metro Manila', 14.55470000, 121.02440000, 'Kubota', 'KX018-4', 'excellent', 180000.00, 20000.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(8, 1, 3, 'Concrete Mixer', 'Portable concrete mixer with 3.5 cubic feet capacity. Electric motor with 1/3 HP power. Ideal for small concrete projects and DIY work.', 800.00, 100.00, 4500.00, 15000.00, 'Makati, Metro Manila', 14.55470000, 121.02440000, 'Harbor Freight', '31999', 'good', 15000.00, 2000.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(9, 4, 4, 'Professional Camera Kit', 'Complete photography kit including DSLR camera, 18-55mm lens, tripod, flash, and carrying case. Perfect for events, portraits, and professional photography.', 1200.00, 150.00, 6500.00, 25000.00, 'Taguig, Metro Manila', 14.51760000, 121.05090000, 'Canon', 'EOS Rebel T7', 'excellent', 25000.00, 3000.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(10, 4, 4, 'DJ Equipment Set', 'Professional DJ setup with turntables, mixer, speakers, and lighting. Includes 2 Technics turntables, Pioneer mixer, and 1000W sound system.', 2000.00, 250.00, 11000.00, 40000.00, 'Taguig, Metro Manila', 14.51760000, 121.05090000, 'Pioneer', 'DDJ-1000', 'excellent', 40000.00, 5000.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(11, 4, 4, 'Projector System', 'High-definition projector with 1080p resolution and 3500 lumens. Includes screen, cables, and carrying case. Perfect for presentations and home theater.', 800.00, 100.00, 4500.00, 15000.00, 'Taguig, Metro Manila', 14.51760000, 121.05090000, 'Epson', 'PowerLite 1761W', 'good', 15000.00, 2000.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(12, 3, 6, 'Lawn Mower', 'Self-propelled lawn mower with 21-inch cutting deck. Gas-powered with 190cc engine. Includes grass catcher and mulching capability.', 600.00, 75.00, 3200.00, 12000.00, 'Makati, Metro Manila', 14.55470000, 121.02440000, 'Honda', 'HRX217VKA', 'excellent', 12000.00, 1500.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(13, 3, 6, 'Pressure Washer', 'Electric pressure washer with 2000 PSI and 1.2 GPM flow rate. Includes multiple nozzle tips and 25-foot hose. Perfect for cleaning driveways, decks, and vehicles.', 500.00, 60.00, 2800.00, 10000.00, 'Makati, Metro Manila', 14.55470000, 121.02440000, 'Sun Joe', 'SPX3000', 'good', 10000.00, 1000.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(14, 3, 6, 'Garden Tools Set', 'Complete garden tool set including shovel, rake, hoe, pruners, and trowel. All tools have ergonomic handles and are made of durable steel.', 200.00, 25.00, 1100.00, 4000.00, 'Makati, Metro Manila', 14.55470000, 121.02440000, 'Fiskars', 'Garden Set', 'excellent', 4000.00, 500.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(15, 5, 7, 'Treadmill', 'Commercial-grade treadmill with 3.0 HP motor and 20\" x 60\" running surface. Features 12 pre-programmed workouts and heart rate monitoring.', 800.00, 100.00, 4500.00, 15000.00, 'Manila, Metro Manila', 14.59950000, 120.98420000, 'NordicTrack', 'T6.5S', 'excellent', 15000.00, 2000.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(16, 5, 7, 'Basketball Court Equipment', 'Portable basketball hoop with adjustable height (7.5-10 feet) and breakaway rim. Includes basketball, pump, and carrying case.', 400.00, 50.00, 2200.00, 8000.00, 'Manila, Metro Manila', 14.59950000, 120.98420000, 'Spalding', 'Portable Pro', 'good', 8000.00, 800.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(17, 5, 8, 'Party Tent Package', 'Complete party tent package with 20x30 foot canopy, side walls, lighting, tables, and chairs. Perfect for outdoor events and celebrations.', 1500.00, 200.00, 8000.00, 30000.00, 'Manila, Metro Manila', 14.59950000, 120.98420000, 'Coleman', 'Event Tent', 'excellent', 30000.00, 3000.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26'),
(18, 5, 8, 'Sound System', 'Professional sound system with 2 speakers, amplifier, microphone, and mixer. 1000W total power output. Ideal for parties and small events.', 1000.00, 125.00, 5500.00, 20000.00, 'Manila, Metro Manila', 14.59950000, 120.98420000, 'JBL', 'Party System', 'good', 20000.00, 2000.00, 1, 30, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_availability`
--

CREATE TABLE `equipment_availability` (
  `availability_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `rental_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `condition_status` enum('new','good','fair','poor') DEFAULT 'good',
  `price_per_day` decimal(10,2) NOT NULL,
  `price_per_week` decimal(10,2) DEFAULT NULL,
  `price_per_month` decimal(10,2) DEFAULT NULL,
  `replacement_cost` decimal(10,2) DEFAULT NULL,
  `availability_status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_images`
--

CREATE TABLE `item_images` (
  `image_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `rental_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'PHP',
  `payment_reference` varchar(100) NOT NULL,
  `platform_fee` decimal(10,2) NOT NULL,
  `owner_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `promo_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `promo_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `rental_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `renter_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `pickup_location` varchar(100) DEFAULT NULL,
  `return_location` varchar(100) DEFAULT NULL,
  `special_instructions` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `deposit_amount` decimal(10,2) DEFAULT NULL,
  `rental_status` enum('pending','confirmed','active','completed','cancelled','disputed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','confirmed','active','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `rental_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `government_id_path` varchar(255) DEFAULT NULL,
  `address_proof_path` varchar(255) DEFAULT NULL,
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_role` enum('owner','customer','admin') DEFAULT 'customer',
  `verification_status` enum('pending','verified','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `address`, `city`, `province`, `postal_code`, `government_id_path`, `address_proof_path`, `average_rating`, `total_reviews`, `is_verified`, `created_at`, `updated_at`, `user_role`, `verification_status`) VALUES
(1, 'john_contractor', 'john@contractor.com', '$2y$10$BbwQZqnHLSuWOBhfVYdZHOI9nhQXNF/wNL8mlkXjlm01dxF3t39Q.', 'John', 'Santos', '09171234567', '123 Construction St', 'Manila', 'Metro Manila', '1000', NULL, NULL, 0.00, 0, 1, '2025-08-15 22:13:26', '2025-08-15 23:23:37', 'customer', 'pending'),
(2, 'maria_chef', 'maria@kitchen.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria', 'Garcia', '09182345678', '456 Kitchen Ave', 'Quezon City', 'Metro Manila', '1100', NULL, NULL, 0.00, 0, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26', 'customer', 'pending'),
(3, 'carlos_gardener', 'carlos@garden.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Reyes', '09193456789', '789 Garden Blvd', 'Makati', 'Metro Manila', '1200', NULL, NULL, 0.00, 0, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26', 'customer', 'pending'),
(4, 'ana_photographer', 'ana@photo.com', '$2y$10$I3.za0l460CkHaWUDfNpgO2cLtz.I0pU00jP.3JGnYVMYb2ISkWUi', 'Ana', 'Cruz', '09194567890', '321 Camera St', 'Taguig', 'Metro Manila', '1300', NULL, NULL, 0.00, 0, 1, '2025-08-15 22:13:26', '2025-08-15 23:24:06', 'customer', 'pending'),
(5, 'demo_user', 'demo@equiprent.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Demo', 'User', '09195678901', 'Demo Address', 'Manila', 'Metro Manila', '1000', NULL, NULL, 0.00, 0, 1, '2025-08-15 22:13:26', '2025-08-15 22:13:26', 'customer', 'pending'),
(12, 'Juan Dela Cruz', 'juandelacruz@gmail.com', '$2y$10$DRi0wveY7Yyu6jdVpwQTBOCPOdQQw1NSNz7UPmkEiuISTmxRL5T6.', 'Juan', 'Dela Cruz', '099999999', 'Taft Ave.', 'Pasay', 'Manila', '4301', NULL, NULL, 0.00, 0, 1, '2025-08-15 23:32:29', '2025-08-15 23:33:27', 'customer', 'verified');

-- --------------------------------------------------------

--
-- Table structure for table `user_verification`
--

CREATE TABLE `user_verification` (
  `verification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `verification_type` enum('government_id','address_proof') NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `verification_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `rental_id` (`rental_id`);

--
-- Indexes for table `disputes`
--
ALTER TABLE `disputes`
  ADD PRIMARY KEY (`dispute_id`),
  ADD KEY `rental_id` (`rental_id`),
  ADD KEY `raised_by` (`raised_by`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `equipment_availability`
--
ALTER TABLE `equipment_availability`
  ADD PRIMARY KEY (`availability_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `item_images`
--
ALTER TABLE `item_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promo_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `renter_id` (`renter_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `rental_id` (`rental_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_verification`
--
ALTER TABLE `user_verification`
  ADD PRIMARY KEY (`verification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disputes`
--
ALTER TABLE `disputes`
  MODIFY `dispute_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `equipment_availability`
--
ALTER TABLE `equipment_availability`
  MODIFY `availability_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_images`
--
ALTER TABLE `item_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `promo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `rental_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_verification`
--
ALTER TABLE `user_verification`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_3` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`rental_id`) ON DELETE SET NULL;

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipment_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`renter_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`rental_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_verification`
--
ALTER TABLE `user_verification`
  ADD CONSTRAINT `user_verification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
