-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2021 at 12:21 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gamespace`
--
CREATE DATABASE IF NOT EXISTS `gamespace` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `gamespace`;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `title`) VALUES
(0, 'None'),
(1, 'Looking to play'),
(2, 'New game'),
(3, 'Achievement'),
(4, 'Random');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
CREATE TABLE `friends` (
  `user_id` int(16) NOT NULL,
  `friend_id` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`user_id`, `friend_id`) VALUES
(4, 12),
(4, 13),
(4, 15),
(4, 16),
(9, 10),
(9, 12),
(9, 13),
(10, 9),
(10, 12),
(10, 13),
(12, 4),
(12, 9),
(12, 10),
(13, 4),
(13, 9),
(13, 10),
(13, 16),
(14, 15),
(15, 4),
(15, 14),
(16, 4),
(16, 13);

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `about` text DEFAULT NULL,
  `pic_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `name`, `about`, `pic_id`) VALUES
(1, 'League of Legends', 'League of Legends is the premiere massive online battle arena (MOBA) competitive experience for all gamers. Choose 1 of over 100 characters to control as you battle across summoners rift with your team. Square up against 5 worthy opponents   and defeat them with teamwork, skill, and knowledge.', 29),
(2, 'Call of Duty', 'The Call of Duty (COD) franchise delivers the best first person shooter experience on the market. COD brings fast paced action, customizable load outs, competitive tournament play, and more to gamers worldwide. We hope that our fans find a community on Gamespace where they can form squads, discuss strategy, and share memes to their hearts content.', 30),
(3, 'Path of Exile', 'Path of Exile is an exciting Action RPG by developers Grinding Gear Games. Come explore the world of Wreaclast, defeat hordes of monsters, find rare and powerful loot, and defeat challenging endgame bosses. Path of Exile is your next adventure waiting to happen.', 31),
(4, 'Legend of Zelda BOTW', 'You know how addicting it is. Don\'t play this game unless you want to lose hours of your life. No really it\'s that good...', 32);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int(16) NOT NULL,
  `user_id` int(16) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `text` varchar (4096) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `pic_id` int(11) DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `date`, `text`, `category_id`, `pic_id`, `game_id`) VALUES
(9, 4, '2021-12-03 01:49:43', 'This is a cape I found', 4, 11, 3),
(17, 15, '2021-12-03 04:00:42', 'Does anyone want to play smash?', 1, NULL, NULL),
(18, 4, '2021-12-03 05:45:16', 'This is just a shitpost', 0, 22, 1),
(19, 14, '2021-12-03 06:05:02', 'I\'m addicted to this game!', 2, 23, 4),
(20, 13, '2021-12-03 06:39:01', 'Met my dad the other day. Was a bit rough, but I think he will be good at Beatsaber!', 3, 25, NULL),
(21, 16, '2021-12-04 03:02:02', 'This is not a post', 4, 27, 4),
(22, 4, '2021-12-10 21:10:32', 'I got Gold 4 in Leauge!!!', 3, 28, 1),
(23, 4, '2021-12-10 21:15:14', 'I\'m bad at COD...', 4, NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `post_id` int(16) NOT NULL,
  `user_id` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
CREATE TABLE `uploads` (
  `id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `path`) VALUES
(1, 'uploads/1.png'),
(2, 'uploads/2.png'),
(3, 'uploads/3.png'),
(4, 'uploads/4.png'),
(5, 'uploads/5.png'),
(6, 'uploads/6.jpg'),
(7, 'uploads/7.png'),
(8, 'uploads/8.jpg'),
(9, 'uploads/9.png'),
(10, 'uploads/10.jpg'),
(11, 'uploads/11.png'),
(12, 'uploads/12.png'),
(13, 'uploads/13.jpg'),
(14, 'uploads/14.jpg'),
(15, 'uploads/15.jpg'),
(16, 'uploads/16.png'),
(17, 'uploads/17.jpg'),
(18, 'uploads/18.png'),
(19, 'uploads/19.png'),
(20, 'uploads/20.png'),
(21, 'uploads/21.png'),
(22, 'uploads/22.png'),
(23, 'uploads/23.png'),
(24, 'uploads/24.png'),
(25, 'uploads/25.png'),
(26, 'uploads/26.png'),
(27, 'uploads/27.png'),
(28, 'uploads/28.jpg'),
(29, 'uploads/29.jpg'),
(30, 'uploads/30.jpg'),
(31, 'uploads/31.jpg'),
(32, 'uploads/32.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(16) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pswd` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `pswd`) VALUES
(4, 'carter.d.ellis10@gmail.com', '$2y$10$vHHJbDsCVMQbxydiqHsGDerKoSlbvVkO1kJMO.mOT8en5VdG/u1Na'),
(9, 'foo@gmail.com', '$2y$10$bexbw7Nb9NnscDCvbfuA/O1SU7Y38Yzl7adKY6AHIBMpnsGgZdayW'),
(10, 'bar@gmail.com', '$2y$10$0Qqgz0Ylz.MErurmIXis/u0ATGjzd3HQD74JKWw57lsVaNknVGdG2'),
(11, 'baz@gmail.com', '$2y$10$4a2UIYmHgmzuCzEkOB5vSOfasyOeIZi7ssoM5PWkTkMzpGHOzORzm'),
(12, 'gaz@gmail.com', '$2y$10$E.YpbjT0T2M6Ged/z.yDYuAXIwHp26sJVMmw9jphfO7PYSQL4ffMK'),
(13, 'forcemaster@gmail.com', '$2y$10$KJfFe48S344vT3oOF0WXTOqKyK.bfjY.cwuV0XRxUYfuETPuyU7.O'),
(14, 'vna@gmail.com', '$2y$10$bikK96dEcIKnap1wOKlxjOCE2OczQdn6N9szg3O/ePtySde3rH2Ei'),
(15, 'will@gmail.com', '$2y$10$PxwpfX1LKNoy06ADsaw0QuF9qJAwMaI0pgaPzfU8FRRc5RHJX/Wf6'),
(16, 'redog@gmail.com', '$2y$10$7gIgJs96VCNNYndOm42atuHWlSpvxKJrVKMLEunUTw6NitU.VL67O');

-- --------------------------------------------------------

--
-- Table structure for table `user_games`
--

DROP TABLE IF EXISTS `user_games`;
CREATE TABLE `user_games` (
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_games`
--

INSERT INTO `user_games` (`user_id`, `game_id`) VALUES
(4, 1),
(4, 2),
(4, 3),
(4, 4),
(9, 1),
(9, 2),
(9, 3),
(9, 4),
(10, 1),
(10, 2),
(10, 3),
(10, 4),
(11, 1),
(11, 2),
(11, 3),
(11, 4),
(12, 1),
(12, 2),
(12, 3),
(12, 4),
(13, 1),
(13, 2),
(13, 3),
(13, 4),
(14, 1),
(14, 2),
(14, 3),
(14, 4),
(15, 1),
(15, 2),
(15, 3),
(15, 4),
(16, 1),
(16, 2),
(16, 3),
(16, 4);

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
CREATE TABLE `user_profile` (
  `user_id` int(16) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `bio` varchar(2048) DEFAULT NULL,
  `pic_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`user_id`, `fname`, `lname`, `bio`, `pic_id`) VALUES
(4, 'Carter', 'Ellis', 'Why wont you work!!! Oh wait this is a bio, hi.', 10),
(9, 'Bob', 'Smith', 'People say I have a generic name. I say my cousin John Doe is much worse', NULL),
(10, 'John', 'Doe', 'Doeeeeeeeeee! wuzzup', NULL),
(11, 'Greg', 'Hoof', 'I am a literal horse', NULL),
(12, 'Bob', 'Bobberton', 'bobby bob boberton is a bobber', NULL),
(13, 'Luke', 'Skywalker', 'May the force be with you... &quot;And also with you&quot;... You better say  it or you get the sword', 24),
(14, 'Viano', 'A', 'Video games are coooooool so is PHP (not off topic)', 21),
(15, 'Will', 'W', 'I\'m a savage at Animal Crossing. Don\'t make me show you my powers...', 19),
(16, 'ReAnne', 'Kohlus', 'Im tall', 26);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`user_id`,`friend_id`),
  ADD KEY `FRIENDID` (`friend_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `POSTUSERID` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`post_id`,`user_id`),
  ADD KEY `TAGUSERID` (`user_id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_games`
--
ALTER TABLE `user_games`
  ADD PRIMARY KEY (`user_id`,`game_id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `FRIENDID` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `USERID` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tags`
--
ALTER TABLE `tags`
  ADD CONSTRAINT `POSTID` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `TAGUSERID` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `UID` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
