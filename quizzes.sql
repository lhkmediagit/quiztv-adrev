-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 04, 2026 at 04:37 AM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u691843472_quiztv2`
--

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) UNSIGNED NOT NULL,
  `slug` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category_id` int(11) UNSIGNED NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `pass_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_attempts` int(11) NOT NULL DEFAULT 0,
  `duration_minutes` int(11) NOT NULL DEFAULT 5,
  `difficulty` enum('easy','medium','hard') NOT NULL DEFAULT 'medium',
  `stages` text DEFAULT NULL,
  `about_html` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `about_quiz` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `slug`, `title`, `description`, `category_id`, `thumbnail`, `pass_rate`, `total_attempts`, `duration_minutes`, `difficulty`, `stages`, `about_html`, `is_active`, `created_by`, `created_at`, `updated_at`, `about_quiz`) VALUES
(1, 'medicine-test', 'Most Adults Can\'t Score 80% On This Medical Terminology Test', 'Decode hospital shorthand, prescription terms, symptoms, procedures, body-system terms, and emergency-care language.', 1, 'https://therainbowhub.com/images/thumbnails/decode-medical-jargon.jpg', 12.00, 23840, 10, 'hard', '[\"Hospital Shorthand\",\"Prescription Labels\",\"Vitals And Monitoring\",\"Heart And Blood Flow\",\"Emergency Care\",\"Symptoms And Signs\",\"Procedures And Devices\",\"Body System Terms\",\"Diagnoses And Infections\",\"Clinical Language Wildcards\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:13', NULL),
(2, 'medicine', 'Only 3% Pass This Medicine Entrance Exam', 'A fast medicine-style reasoning exam with chart clues, anatomy basics, memory callbacks, and calm pressure decisions.', 2, 'https://therainbowhub.com/images/thumbnails/medicine.jpg', 3.00, 23379, 5, 'hard', '[\"First Clinical Clues\",\"Charts And Memory\",\"Pressure Decisions\",\"Final Entrance Check\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:15', NULL),
(3, 'navy', 'Only 4% Pass This Navy Entrance Test', 'A fast Navy-style mission challenge with navigation, ship logic, signals, callbacks, and pressure decisions.', 3, 'https://therainbowhub.com/images/thumbnails/navy.jpg', 4.00, 15646, 6, 'hard', '[\"Deck Training\",\"Harbour Mission\",\"Signal Pressure\",\"Final Command Check\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:16', NULL),
(4, 'airforce', 'Only 3% Can Pass This Air Force Test', 'A fast Air Force-style mission challenge with flight basics, cockpit logic, callbacks, and pressure decisions.', 4, 'https://therainbowhub.com/images/thumbnails/airforce.jpg', 3.00, 19075, 5, 'hard', '[\"Training Basics\",\"Base Alpha Mission\",\"Final Command Assessment\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:17', NULL),
(5, 'connection', 'Is Your Connection Karmic, Soulmate, Or Twin Flame?', 'Answer quick questions about chemistry, timing, lessons, and emotional pull to reveal your bond profile.', 5, 'https://therainbowhub.com/images/thumbnails/connection.jpg', 50.00, 23610, 5, 'easy', '[\"The First Pull\",\"Lessons And Mirrors\",\"Your Bond Reveal\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:18', NULL),
(6, 'memory', 'Most Adults Can\'t Score 80% On This Memory Test', 'A quick memory test in ten rounds that starts easy and later checks how much you still remember.', 6, 'https://therainbowhub.com/images/thumbnails/memory.jpg', 12.00, 9695, 6, 'medium', '[\"Quick Facts\",\"Detail Capture\",\"Clue Capture\",\"Pattern Memory\",\"Category Recall\",\"Delayed Details\",\"Sequence Memory\",\"Mixed Recall\",\"Long Recall\",\"Final Memory Check\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:19', NULL),
(7, 'iq', 'IQ Challenge', 'An 88-question reasoning challenge built around patterns, logic, number sense, mental grids, focus, and flexible thinking.', 7, 'https://therainbowhub.com/images/thumbnails/iq.jpg', 6.00, 22359, 12, 'hard', '[\"Quick Signal Check\",\"Pattern Starter\",\"Number Moves\",\"Imagine The Grid\",\"Memory And Focus\",\"Logic Links\",\"Rule Switching\",\"Spatial Reasoning\",\"Assumption Check\",\"Layered Patterns\",\"Final Brain Sprint\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:21', NULL),
(8, 'tools', 'Only 1/12 People Can Name These Garage Tools', 'Name common garage and workshop tools from photo clues, from basic hand tools to tougher workshop staples.', 8, 'https://therainbowhub.com/images/thumbnails/tools.jpg', 8.00, 15029, 5, 'medium', '[\"Garage Basics\",\"Grip, Cut, And Measure\",\"Workshop Essentials\",\"Workshop Repair Tools\",\"Heavy-Duty Tools\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:22', NULL),
(9, 'vision', 'Vision Test', 'A visual color-pattern test with hidden numbers, letters, shapes, symbols, and mixed color plates.', 9, 'https://therainbowhub.com/images/thumbnails/vision.jpg', 7.00, 18392, 5, 'medium', '[\"Color Pattern Warm Up\",\"Hidden Number Plates\",\"Shapes And Symbols\",\"Letters And Words\",\"Mixed Color Plates\",\"Harder Number Plates\",\"Pattern Recognition Round\",\"Low Contrast Challenge\",\"Advanced Hidden Plates\",\"Final Vision Test\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:23', NULL),
(10, 'zodiac', 'What’s Your True Zodiac Sign Based On Your Personality?', 'Answer quick personality choices and reveal the zodiac sign that best matches how you think, feel, and connect.', 10, 'https://therainbowhub.com/images/thumbnails/zodiac.jpg', 50.00, 9996, 6, 'easy', '[\"First Instincts\",\"Social Energy\",\"Inner Style\",\"Creative Choices\",\"Values And Drive\",\"Decision Energy\",\"Connection Style\",\"Travel And Change\",\"Life Compass\",\"True Essence Final\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:25', NULL),
(11, 'grammar', 'Everyday Grammar Challenge', 'A practical staged grammar challenge about everyday writing mistakes, clear sentences, word choice, proofreading, and careful reading.', 11, 'https://therainbowhub.com/images/thumbnails/grammar.jpg', 10.00, 15818, 7, 'medium', '[\"Grammar Warm Up\",\"Punctuation Basics\",\"Sentence Sense\",\"Word Choice\",\"Agreement Checks\",\"Pronouns And References\",\"Tense And Time\",\"Proofreading Round\",\"Tricky Writing Mistakes\",\"Final Grammar Challenge\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:26', NULL),
(12, 'history', 'World History Challenge', 'A fun staged history quiz about landmarks, civilizations, inventions, timelines, culture, trade, and careful historical reasoning.', 12, 'https://therainbowhub.com/images/thumbnails/history.jpg', 9.00, 7786, 7, 'hard', '[\"History Warm Up\",\"Ancient Worlds\",\"Landmarks And Civilizations\",\"Ideas And Inventions\",\"Trade And Travel\",\"Turning Points\",\"People And Places\",\"Timeline Challenge\",\"Mixed World History\",\"Final History Challenge\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:27', NULL),
(13, 'quiztv', 'Can You Catch the Trick?', 'Some questions look simple—until you realize they’re not asking what you thought they were. It’s not about what you know, but how carefully you think. Let’s see how many tricks you can catch.', 13, 'https://cloud.appwrite.io/v1/storage/buckets/65969bd3b8e2a0b364e1/files/69e1ece4000ae266b0a3/preview?project=659526d9b73971c0b8b3', 70.00, 15497, 15, 'medium', '[\"First Intentions\",\"Double Takes\",\"Mind Benders\",\"Wordplay Tricks\",\"Visual Illusions\",\"Critical Logic\",\"Unconventional Thinking\",\"The Final Paradox\"]', NULL, 1, 1, '0000-00-00 00:00:00', '2026-07-19 15:39:34', NULL),
(16, 'cjkjcsx', 'cjkjcsx', 'xskj', 12, 'https://static.vecteezy.com/system/resources/thumbnails/054/876/032/small/mirror-image-snow-capped-mountain-peaks-reflected-in-pristine-lake-free-photo.jpg', 50.00, 6, 5, 'easy', NULL, NULL, 1, 1, '2026-07-23 10:28:24', '2026-07-23 10:28:24', NULL),
(18, 'elementary-geography-quiz', 'Elementary Geography Quiz', 'Think you know the basics of geography? Challenge yourself with questions about countries, capitals, continents, oceans, landmarks, and natural features. This fun and educational quiz is perfect for learners of all ages who want to test their world knowledge and discover interesting geography facts along the way.', 16, 'https://images.unsplash.com/photo-1521295121783-8a321d551ad2?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Z2VvZ3JhcGh5fGVufDB8fDB8fHww', 50.00, 7, 5, 'easy', NULL, NULL, 1, 1, '2026-08-01 15:13:16', '2026-08-01 15:13:16', NULL),
(19, 'life-before-the-internet', 'Life Before The Internet', 'Life Before the Internet takes you back to a time when people relied on books, landline phones, newspapers, and face-to-face conversations. Discover how everyday life worked before the digital age changed everything.', 17, 'https://preview.redd.it/life-before-the-internet-what-do-you-recall-v0-pf6be8kulgga1.jpg?width=640&crop=smart&auto=webp&s=5b44281fcb0029a9efa1e8a20bc87ec89977c51b', 50.00, 1, 5, 'medium', NULL, NULL, 1, 1, '2026-08-03 14:36:21', '2026-08-03 14:47:48', NULL),
(20, 'how-well-do-you-remember-these-legendary-cars', 'How Well Do You Remember These Legendary Cars?', 'Test your knowledge of legendary cars that made automotive history.', 18, 'https://docservice.lhkmedia.io/api/file/d5e4c5a605b49f9a06cc432552a2cb69', 50.00, 0, 5, 'medium', NULL, NULL, 1, 1, '2026-08-03 16:26:49', '2026-08-03 16:26:49', NULL),
(21, 'biology-quiz-challenge', 'Biology Quiz Challenge', 'Test your knowledge of the basics of biology with this fun and educational quiz. Explore cells, plants, animals, the human body, and the science of life while challenging yourself with interesting questions.', 19, 'https://greatermanchester.ac.uk//assets/Uploads/What-is-Medical-Biology-University-of-Bolton.jpg', 50.00, 0, 5, 'easy', NULL, NULL, 1, 1, '2026-08-03 16:53:49', '2026-08-03 16:53:49', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
