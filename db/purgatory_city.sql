-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2025 at 09:21 AM
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
-- Database: `purgatory_city`
--

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE `actions` (
  `action_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `actions`
--

INSERT INTO `actions` (`action_id`, `name`, `type`) VALUES
(1, 'Chilling', 'Any'),
(2, 'Fighting', 'Any'),
(3, 'Jogging', 'Any'),
(4, 'Eating', 'Any'),
(5, 'Drinking', 'Any'),
(6, 'Walking', 'Any'),
(7, 'Waiting', 'Any'),
(8, 'Working', 'Any'),
(9, 'Sleeping', 'Any'),
(10, 'Thinking', 'Any'),
(11, 'Nothing', 'Any'),
(12, 'Planning', 'Any'),
(13, 'Coffee', 'Any');

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `denizen_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `denizen_id`, `district_id`, `location_id`, `action_id`) VALUES
(1, 1, 1, 14, 7),
(2, 2, 5, 51, 10),
(3, 6, 2, 18, 5),
(4, 7, 4, 35, 5),
(5, 8, 2, 21, 10),
(6, 9, 5, 43, 5),
(7, 10, 1, 15, 11),
(8, 11, 1, 10, 13),
(9, 12, 3, 24, 8),
(10, 13, 1, 15, 7),
(11, 14, 5, 50, 6),
(12, 15, 4, 41, 5),
(13, 16, 2, 16, 10),
(14, 17, 5, 49, 7);

-- --------------------------------------------------------

--
-- Table structure for table `affiliation_list`
--

CREATE TABLE `affiliation_list` (
  `affiliation_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `denizen_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `denizen`
--

CREATE TABLE `denizen` (
  `denizen_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `alignment` varchar(20) NOT NULL,
  `profile_pic` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `denizen`
--

INSERT INTO `denizen` (`denizen_id`, `name`, `alignment`, `profile_pic`) VALUES
(1, 'Shadowplayer11.8', 'Horror', ''),
(2, 'John Stone', 'Violence', ''),
(6, 'Max Rogers', 'Suffering', ''),
(7, 'Zack Verrati', 'Violence', ''),
(8, 'Angelo Kaine', 'Serenity', ''),
(9, 'Cody', 'Truth', ''),
(10, 'Ryan Gosling', 'Serenity', ''),
(11, 'Canoe Scarcliff', 'Horror', ''),
(12, 'Elizabeth Mayfross', 'Truth', ''),
(13, 'Vylet Cavendish', 'Serenity', ''),
(14, 'Samantha Bridges', 'Obsession', ''),
(15, 'Riza Hawke', 'Violence', ''),
(16, 'Anne Ravenharth', 'Suffering', ''),
(17, 'Nichole Tailswith', 'Serenity', '');

-- --------------------------------------------------------

--
-- Table structure for table `district`
--

CREATE TABLE `district` (
  `district_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `district`
--

INSERT INTO `district` (`district_id`, `name`, `type`) VALUES
(1, 'City Central', 'Lawful'),
(2, 'Angler Pines', 'Secluded'),
(3, 'Pine Wood District', 'Sleepy'),
(4, 'Yellow District', 'Dangerous'),
(5, 'Effrey', 'Sophisticated'),
(6, 'Reapers Sandbox', 'Secluded');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `district_id`, `name`, `type`) VALUES
(1, 1, 'Mama G\'s Cafe', 'Chill'),
(2, 1, 'Organization Building', 'Professional'),
(3, 4, 'Yellow Harbor', 'Deadly'),
(4, 4, 'The Tower', 'Chill'),
(5, 4, 'Apartment Complex #8', 'Chill'),
(6, 5, 'Art Measeum', 'Social'),
(7, 1, 'St. Martha General Hospital', 'medical'),
(8, 1, 'Angus Arcade', 'Social'),
(9, 1, 'Shopping Mall', 'Social'),
(10, 1, 'Midnight Mart', 'Shop'),
(11, 1, 'Toreno Apartment Complex', 'Home'),
(12, 1, 'Luigis Autobody Shop', 'Repair'),
(13, 1, 'Chinese Pizza', 'Food'),
(14, 1, 'La Beoff Bistro', 'Food'),
(15, 1, 'Richards Tower', 'Business'),
(16, 2, 'Argus Lake', 'Social'),
(17, 2, 'Soulsnatch Cliff', 'Social'),
(18, 2, 'Bush Clearing', 'Social'),
(19, 2, 'Midas Stream', 'Social'),
(20, 2, 'Lumber Yard', 'Secluded'),
(21, 2, 'Old Cabin', 'Secluded'),
(22, 2, 'Sacrament Cave', 'Secluded'),
(23, 2, 'Raven Perch', 'Secluded'),
(24, 3, 'Little Bells Doll Shop', 'Shop'),
(25, 3, 'Magic Shop', 'Business'),
(26, 3, 'Gypsy Den', 'Business'),
(27, 3, 'Abandoned Saw mill', 'Social'),
(28, 3, 'New Pines Saw mill', 'Business'),
(29, 3, 'Abandoned Bowling Alley', 'Social'),
(30, 3, 'Abandoned Amusement Park', 'Social'),
(31, 3, 'Hollow Street', 'Social'),
(32, 3, 'Abandoned Hospital', 'Social'),
(33, 3, 'Flu Clinic', 'Medical'),
(34, 4, 'Yellow Harbor', 'Residence'),
(35, 4, 'Fish Market', 'Shop'),
(36, 4, 'Bonnie Shipyard', 'Business'),
(37, 4, 'Navigators Club', 'Organization'),
(38, 4, 'Sea Patrol Office', 'Organization'),
(39, 4, 'The Lighthouse', 'Building'),
(40, 4, 'Fishermans Club', 'Organization'),
(41, 4, 'Denise Fishguts', 'Shop'),
(42, 5, 'Yellow Harbor', 'Residence'),
(43, 5, 'Yellow Harbor', 'Residence'),
(44, 5, 'Mayfross Estate', 'Residence'),
(45, 5, 'Deimara Estate', 'Residence'),
(46, 5, 'Arsenault Estate', 'Residence'),
(47, 5, 'St. Gertrudes All Girls College', 'School'),
(48, 5, 'St. Bernard Gold Course', 'Social'),
(49, 5, 'Stallion Peaks', 'Social'),
(50, 5, 'Verdun Private Park', 'Social'),
(51, 5, 'Dormatory Complex #9', 'Residence');

-- --------------------------------------------------------

--
-- Table structure for table `occupation`
--

CREATE TABLE `occupation` (
  `occupation_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `denizen_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actions`
--
ALTER TABLE `actions`
  ADD PRIMARY KEY (`action_id`);

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `affiliation_list`
--
ALTER TABLE `affiliation_list`
  ADD PRIMARY KEY (`affiliation_id`);

--
-- Indexes for table `denizen`
--
ALTER TABLE `denizen`
  ADD PRIMARY KEY (`denizen_id`);

--
-- Indexes for table `district`
--
ALTER TABLE `district`
  ADD PRIMARY KEY (`district_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actions`
--
ALTER TABLE `actions`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `affiliation_list`
--
ALTER TABLE `affiliation_list`
  MODIFY `affiliation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `denizen`
--
ALTER TABLE `denizen`
  MODIFY `denizen_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `district`
--
ALTER TABLE `district`
  MODIFY `district_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
