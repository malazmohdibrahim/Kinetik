-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: kinetik_db
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `order_details`
--

DROP TABLE IF EXISTS `order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `vehicle_id` int DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price_at_purchase` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `vehicle_id` (`vehicle_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_details`
--

LOCK TABLES `order_details` WRITE;
/*!40000 ALTER TABLE `order_details` DISABLE KEYS */;
INSERT INTO `order_details` VALUES (1,1,2,1,185000.00),(3,2,2,1,280000.00),(4,2,3,1,310000.00),(5,3,1,1,260000.00),(6,4,1,1,260000.00),(7,5,2,1,280000.00),(8,5,1,1,260000.00),(9,6,4,1,230000.00),(10,7,2,1,280000.00);
/*!40000 ALTER TABLE `order_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` enum('Pending','Approved','In Transit','Delivered','Cancelled') DEFAULT 'Pending',
  `payment_method` enum('Bank Transfer','Credit Card','Mobile Money','Crypto') DEFAULT 'Bank Transfer',
  `shipping_address` text NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,1,185000.00,'In Transit','Bank Transfer','Nyarutarama Estate, Villa 14, Kigali, Rwanda','2026-06-10 15:59:25'),(2,1,590000.00,'Approved','Credit Card','sfougheiughaeorui | Phone: 123456','2026-06-10 18:45:14'),(3,1,260000.00,'Approved','Bank Transfer','sonatubes maradona | Phone: 790456743','2026-06-10 19:45:02'),(4,1,260000.00,'Approved','Bank Transfer','cifjhzbsdifusb | Phone: 24346356','2026-06-10 20:40:14'),(5,1,540000.00,'Approved','Bank Transfer','ghcfjxfhjdghm | Phone: 24346356','2026-06-10 20:40:54'),(6,1,230000.00,'Approved','Bank Transfer','rfve7rg8e7yrgi | Phone: 123345','2026-06-12 17:30:46'),(7,1,0.00,'Pending','Bank Transfer','Staging Collection - Kigali Hub','2026-06-15 11:01:25');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_drives`
--

DROP TABLE IF EXISTS `test_drives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_drives` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `vehicle_id` int NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `status` enum('Requested','Confirmed','Completed','Cancelled') DEFAULT 'Requested',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `vehicle_id` (`vehicle_id`),
  CONSTRAINT `test_drives_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `test_drives_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_drives`
--

LOCK TABLES `test_drives` WRITE;
/*!40000 ALTER TABLE `test_drives` DISABLE KEYS */;
INSERT INTO `test_drives` VALUES (1,1,1,'2026-06-15','14:00:00','Confirmed','2026-06-10 15:59:25');
/*!40000 ALTER TABLE `test_drives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `verification_tier` varchar(50) DEFAULT 'Standard Buyer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Malaz Ibrahim','malaz@kinetik.rw','$2y$10$KinetikSecureHashExampleForCustomerPass','+250 791 591 773','customer','Certified Luxury Buyer','2026-06-10 15:59:25'),(2,'System Administrator','admin@kinetik.rw','$2y$10$KinetikSecureHashExampleForAdminDashboard','+250 790 000 000','admin','Fleet Commander','2026-06-10 15:59:25'),(3,'maha','maha@gmail.com','$2y$10$UVyEUZFTIlZYz6PpK1R6h.cVhF2Ef7msUPcQ9lmXinwwhXi3E6kEC','12345678','customer','Standard Buyer','2026-06-10 19:56:20'),(5,'reem','reem@gmail.com','$2y$10$iTazKNb0MK82stNsg1iA9ujqYXwDaVl9X9VpU7iCK7gQZ3qEf6tGC','1234567890','customer','Standard Buyer','2026-06-10 19:57:27');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_images`
--

DROP TABLE IF EXISTS `vehicle_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`),
  CONSTRAINT `vehicle_images_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_images`
--

LOCK TABLES `vehicle_images` WRITE;
/*!40000 ALTER TABLE `vehicle_images` DISABLE KEYS */;
INSERT INTO `vehicle_images` VALUES (5,1,'assets/images/cars/huracan.png','01_Main Baseline View'),(6,1,'assets/images/cars/huracan_front_right.png','02_Front Right Perspective'),(7,1,'assets/images/cars/huracan_side.png','03_Right Flank Profile View'),(8,1,'assets/images/cars/huracan_back_right.png','04_Back Right Perspective'),(9,1,'assets/images/cars/huracan_rear_back_left.png','05_Rear Back Left Quarter'),(10,1,'assets/images/cars/huracan_left_side.png','06_Left Flank Profile View'),(11,1,'assets/images/cars/huracan_front_left.png','07_Front Left Perspective'),(19,2,'assets/images/cars/ferrari-488.png','01_Main Baseline View'),(20,2,'assets/images/cars/488_front_right.png','02_Front Right Perspective'),(21,2,'assets/images/cars/488_side.png','03_Right Flank Profile View'),(22,2,'assets/images/cars/488_back_right.png','04_Back Right Perspective'),(23,2,'assets/images/cars/488_rear_back_left.png','05_Rear Back Left Quarter'),(24,2,'assets/images/cars/488_left_side.png','06_Left Flank Profile View'),(25,2,'assets/images/cars/488_front_left.png','07_Front Left Perspective'),(26,4,'assets/images/cars/911-turbo-s.png','01_Main Baseline View'),(27,4,'assets/images/cars/911_front_right.png','02_Front Right Perspective'),(28,4,'assets/images/cars/911_side.png','03_Right Flank Profile View'),(29,4,'assets/images/cars/911_back_right.png','04_Back Right Perspective'),(30,4,'assets/images/cars/911_rear_back_left.png','05_Rear Back Left Quarter'),(31,4,'assets/images/cars/911_left_side.png','06_Left Flank Profile View'),(32,4,'assets/images/cars/911_front_left.png','07_Front Left Perspective'),(40,3,'assets/images/cars/mclaren_720s.png','01_Main Baseline View'),(41,3,'assets/images/cars/720s_front_right.png','02_Front Right Perspective'),(42,3,'assets/images/cars/720s_side.png','03_Right Flank Profile View'),(43,3,'assets/images/cars/720s_back_right.png','04_Back Right Perspective'),(44,3,'assets/images/cars/720s_rear_back_left.png','05_Rear Back Left Quarter'),(45,3,'assets/images/cars/720s_left_side.png','06_Left Flank Profile View'),(46,3,'assets/images/cars/720s_front_left.png','07_Front Left Perspective'),(47,5,'assets/images/cars/mustang.jpg','01_Main Baseline View'),(48,5,'assets/images/cars/mustang_front_right.jpg','02_Front Right Perspective'),(49,5,'assets/images/cars/mustang_side.jpg','03_Right Flank Profile View'),(50,5,'assets/images/cars/mustang_back_right.jpg','04_Back Right Perspective'),(51,5,'assets/images/cars/mustang_rear_back_left.jpg','05_Rear Back Left Quarter'),(52,5,'assets/images/cars/mustang_left_side.jpg','06_Left Flank Profile View'),(53,5,'assets/images/cars/mustang_front_left.jpg','07_Front Left Perspective');
/*!40000 ALTER TABLE `vehicle_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `brand` varchar(50) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `category` enum('sports','Super','muscle') NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `description` text,
  `horsepower` int DEFAULT NULL,
  `top_speed_kmh` int DEFAULT NULL,
  `main_image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (1,'Lamborghini','HuracÃ¡n','Super',260000.00,'A mid-engine masterwork blending an aggressive, sharp luxury design aesthetic with a high-revving, naturally aspirated 5.2-liter V10 engine.',640,325,'assets/images/cars/huracan.png','2026-06-10 15:59:25'),(2,'Ferrari','488 GTB','sports',280000.00,'An Italian icon combining an aerodynamic, low-profile stance with a ferocious twin-turbocharged V8 engine engineered for track-level responsiveness.',670,330,'assets/images/cars/ferrari-488.png','2026-06-10 15:59:25'),(3,'McLaren','720S','Super',310000.00,'A futuristic performance hypercar engineered around a revolutionary carbon fiber Monocage chassis and an advanced proactive chassis control suspension.',720,341,'assets/images/cars/mclaren-720s.png','2026-06-10 15:59:25'),(4,'Porsche','911 Turbo S','sports',230000.00,'The definitive high-performance everyday supercar, delivering unparalleled dual-clutch acceleration, precision all-wheel drive, and twin-turbo tuning.',650,330,'assets/images/cars/911-turbo-s.png','2026-06-10 15:59:25'),(5,'Ford','Mustang GT','muscle',55000.00,'A classic American muscle car with a powerful V8 engine.',450,250,'assets/images/cars/mustang.jpg','2026-06-11 19:57:55');
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-15 17:00:41
