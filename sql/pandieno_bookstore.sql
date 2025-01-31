-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2024 at 08:52 AM
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
-- Database: `pandieno_bookstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `house_no` varchar(100) NOT NULL,
  `street` varchar(100) NOT NULL,
  `province` enum('Bulacan') NOT NULL,
  `city` enum('Pandi') NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `barangay`, `house_no`, `street`, `province`, `city`, `user_id`) VALUES
(1, 'Bunsuran 1', '425', 'Tanguile St.', 'Bulacan', 'Pandi', 0),
(2, 'Python', '7000', 'ChatGPT', 'Bulacan', 'Pandi', 0),
(3, 'Bunsuran 1', '425', 'Tanguile St.', 'Bulacan', 'Pandi', 0);

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', 'admin', '2024-11-01 06:56:16');

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `author_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(30) NOT NULL,
  `title` varchar(100) NOT NULL,
  `category` enum('comedy','horror','drama','action','romance','sci-fi','fantasy','mystery','thriller','historical','biography','self-help','children','young adult','non-fiction','poetry','graphic novel') NOT NULL,
  `author` varchar(100) NOT NULL,
  `isbn` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cover_image` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `sales_count` int(11) DEFAULT 0,
  `avg_rating` decimal(3,2) DEFAULT NULL,
  `published_sales` decimal(10,2) DEFAULT 0.00
) ;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `category`, `author`, `isbn`, `price`, `quantity`, `cover_image`, `description`, `keywords`, `publish_date`, `sales_count`, `avg_rating`, `published_sales`) VALUES
(1, 'Voice of Ancestors', 'fantasy', 'Getpremades.com', '978-3-16-148410-0', 129.00, 30, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728309990/b3_w75zis.jpg', 'In \"Voice of Ancestors,\" a young mage discovers a hidden realm where the whispers of ancient spirits guide her destiny. As she navigates a world of mythical creatures and powerful magic, she must unravel the secrets of her lineage to protect her homeland from an impending darkness. With the help of her ancestors\' voices, she embarks on a quest that will challenge her courage, test her loyalty, and ultimately reveal her true potential.', 'Fantasy, Magic, Ancestors, Quest, Young Mage, Mythical Creatures, Ancient Spirits, Destiny, Adventure, Dark Forces, Wolf', '2024-10-30', 0, 0.00, 0.00),
(2, 'Night Spinner', 'fantasy', 'Addie Thorley', '978-2-34-567890-1', 350.75, 0, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728309990/b1_vgu4hi.jpg', 'In \"Night Spinner,\" a gifted weaver of dreams discovers her ability to manipulate the threads of fate. When an ancient evil threatens to unravel the fabric of her world, she must harness her unique powers to protect her people. Guided by the enigmatic spirits of the night, she embarks on a perilous journey filled with enchantment and danger, learning that the strength of her heart is as vital as the magic she wields.', 'Fantasy, Dreams, Magic, Fate, Weaver, Adventure, Ancient Evil, Spirits, Journey, Enchantment, Girl, Bird', '2024-01-15', 0, 0.00, 0.00),
(3, 'Storm Tide', 'fantasy', 'Den Patrick', '978-3-45-678901-2', 900.00, 76, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728309989/b2_if3uu4.jpg', 'In \"Storm Tide,\" a coastal kingdom faces an unprecedented threat as ancient sea spirits awaken from their slumber. With storms raging and waters rising, a brave sailor must unite a band of unlikely heroes to confront the tempest and uncover the secrets of the deep. As they navigate treacherous waters and uncover hidden truths, they learn that the power of friendship and courage can change the tide of fate.', 'Fantasy, Adventure, Sea Spirits, Storms, Coastal Kingdom, Heroes, Tempest, Secrets, Friendship, Courage, Dolphin, Sea', '2023-05-20', 10, 0.00, 150.00),
(4, 'Chronicles of Narnia', 'fantasy', 'Getpremades.com', '978-4-56-789012-3', 450.25, 49, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728309989/b4_p43b9p.jpg', 'In \"Chronicles of Narnia,\" four siblings stumble upon a magical wardrobe that leads them to the enchanting land of Narnia, where talking animals and mythical creatures thrive. As they uncover Narnia’s secrets, they are drawn into an epic battle between good and evil, guided by the noble lion Aslan. Their journey through this captivating realm tests their bravery, friendship, and faith, revealing the true power of love and sacrifice.', 'Fantasy, Magic, Narnia, Adventure, Talking Animals, Aslan, Good vs. Evil, Siblings, Epic Battle, Friendship, Hero, Lion', '2005-07-29', 20, 0.00, 250.00),
(5, 'Penpal', 'horror', 'Dathan Auerbach', '978-5-67-890123-4', 750.99, 11, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728381588/Penpal_uab1ip.jpg', 'In \"Penpal,\" a series of unsettling letters begins to arrive in the mail, written by a mysterious stranger who seems to know far too much about the protagonist\'s life. As the correspondence unfolds, secrets from the past resurface, revealing a dark connection that threatens to unravel everything they hold dear. With each chilling message, the line between friend and foe blurs, leading to a terrifying climax that forces the protagonist to confront their deepest fears.', 'Horror, Mystery, Letters, Suspense, Dark Secrets, Psychological, Fear, Intrigue, Isolation, Twists, Yellow, Man, Woods, Trees', '2022-09-10', 5, 0.00, 100.00),
(6, 'Set in Stone', 'horror', 'Mari Mancusi', '978-7-89-012345-6', 1100.80, 42, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728381587/Set_in_Stone_Disney__A_Twisted_Tale_15_s088x2.jpg', 'In \"Set in Stone,\" a small town is haunted by an ancient curse that traps the souls of the restless dead in its graveyard. When a group of friends uncovers a hidden tombstone with eerie inscriptions, they unwittingly awaken the malevolent spirits bound to it. As sinister events begin to unfold, they must unravel the dark history of the town and confront the terror that has been lying in wait. Time is running out, and the line between the living and the dead blurs as they fight to escape their grim fate.', 'Horror, Curse, Graveyard, Spirits, Ancient Tombstone, Small Town, Supernatural, Mystery, Friendship, Survival, Boy, Sword, Bird, Castle, Trees', '2023-02-15', 3, 0.00, 80.00),
(7, 'A Thousand Letters', 'romance', 'Staci Hart', '978-6-54-321098-7', 500.00, 89, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728381586/A_Thousand_Letters_The_Austens_Series_by_Staci_Hart_jxykp2.jpg', 'In \"A Thousand Letters,\" a young woman discovers a collection of heartfelt letters hidden away in her late grandmother’s attic. As she reads through the poignant words, she learns about a long-lost love story that transcends time and distance. Inspired by her grandmother’s experiences, she embarks on a journey of self-discovery and unexpected romance, finding courage to pursue her own heart\'s desires. Through the power of written words, she uncovers the magic of love that can bridge generations.', 'Romance, Letters, Love Story, Self-Discovery, Family, Heartfelt, Journey, Generations, Inspiration, Courage, Man, Woman, Green, Light, Street light, Snow', '2023-08-10', 2, 0.00, 30.00),
(8, 'You Deserve Each Other', 'romance', 'Sarah Hogle', '978-0-12-345678-9', 320.40, 17, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728381586/20_Best_Romance_Chick_Lit_Books_of_2020_-_The_Bibliofile_urxfbt.jpg', 'In \"You Deserve Each Other,\" a couple on the brink of their wedding realizes that their relationship might not be as perfect as it seems. With both partners secretly wanting to call off the engagement, they engage in a playful yet poignant battle of wills, testing their love and compatibility in unexpected ways. As they navigate misunderstandings and heartfelt revelations, they discover what it truly means to deserve one another, leading to a deeper connection than they ever imagined.', 'Romance, Engagement, Relationship, Humor, Love, Self-Discovery, Misunderstanding, Conflict, Heartfelt, Connection, Man, Woman, Pink, Flower, Window', '2020-11-12', 0, 0.00, 10.00),
(9, 'Shadow Warrior', 'action', 'Getpremades.com', '978-8-76-543210-9', 1800.90, 66, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728381587/Designed_by_TrifBookDesign_com_lhjsjo.jpg', 'In \"Shadow Warrior,\" a skilled assassin grapples with his dark past while navigating the treacherous underworld of a futuristic city. Tasked with eliminating a powerful crime lord, he must use his unparalleled combat skills and stealth to survive. As he delves deeper into the shadows, he uncovers a conspiracy that threatens not only his life but the lives of countless innocents. With every decision weighing heavily on his conscience, he must choose between vengeance and redemption in a high-stakes battle for justice.', 'Action, Assassin, Combat, Futuristic, Crime Lord, Underworld, Stealth, Conspiracy, Vengeance, Redemption, Hero, Sword, Dragon, Wasteland, Monster', '2021-06-05', 15, 0.00, 200.00),
(10, 'Whistle in the Dark', 'mystery', 'Emma Healey', '978-9-87-654321-0', 410.50, 30, 'https://res.cloudinary.com/izynegallardo/image/upload/v1730443580/pandieno_bookstore/cover_images/book_10_1730443577.jpg', 'In \"Whistle in the Dark,\" a woman returns to her hometown to confront the shadows of her past, only to find herself embroiled in a mysterious disappearance that has haunted the community for years. As she pieces together clues from her own childhood and interviews the townspeople, she uncovers hidden truths that challenge her understanding of loyalty and betrayal. In a gripping narrative filled with suspense, she must decide how far she’s willing to go to uncover the truth and protect those she loves.', 'Mystery, Suspense, Disappearance, Hometown, Childhood, Clues, Investigation, Loyalty, Betrayal, Truth, Woman, Forest, Night, Lantern', '2023-09-25', 8, 0.00, 75.00),
(21, 'Laughs on the Run', 'comedy', 'Jamie Baxter', '978-1-23456-789-0', 230.00, 101, 'https://res.cloudinary.com/izynegallardo/image/upload/v1730441002/pandieno_bookstore/cover_images/vrbfdwypdrjlxvv2yryq.jpg', 'In a world where the mundane meets the absurd, \"Laughs on the Run\" takes readers on a rollicking adventure with Charlie, an average guy whose life is turned upside down when he accidentally becomes a viral sensation. Join Charlie as he navigates a series of hilarious misadventures, from accidentally crashing a celebrity wedding to finding himself in a high-stakes chase involving a runaway llama. With witty dialogue and an unforgettable cast of quirky characters, this comedy will have you in stitches from start to finish.', 'Comedy, Humor, Adventure, Viral Sensation, Quirky Characters', '2004-05-15', 0, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `book_authors`
--

CREATE TABLE `book_authors` (
  `book_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-11-02 10:14:23', '2024-11-02 10:14:23'),
(2, 2, '2024-11-20 05:52:44', '2024-11-20 05:52:44'),
(3, 3, '2024-11-20 07:17:48', '2024-11-20 07:17:48');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `book_id`, `quantity`) VALUES
(51, 1, 1, 1),
(52, 1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_text` varchar(500) DEFAULT NULL,
  `rating` decimal(3,2) NOT NULL CHECK (`rating` >= 0 and `rating` <= 5),
  `review_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `birthday` date DEFAULT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'inactive',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `address_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `first_name`, `middle_name`, `last_name`, `gender`, `birthday`, `cart_id`, `status`, `date_created`, `address_id`) VALUES
(1, 'Izyne123', '$2y$10$kxJRSdk60d6JDgHSa9dP/OAKD1KBPpplHb6ZGjPAN9IwrAvwC9xtu', 'izynehowiegallardo@gmail.com', 'Izyne', 'Sernicula', 'Gallardo', 'male', '2003-06-10', 1, 'inactive', '2024-11-02 10:14:23', 1),
(2, 'User', '$2y$10$Jg.64wwrfW9Sz2Vf1I6AdumEhVyepss4KgrWVxGcqlMY1Che002PO', 'user@test.com', 'First', 'Middle', 'Last', 'other', '2024-11-20', 2, 'inactive', '2024-11-20 05:52:44', 2),
(3, 'Test123', '$2y$10$FK.9pWkinLcdDSfP7eu3X.lswi2EiypDCNqVjsjW/BQAHOtU3tm3u', 'test@test.com', 'Test', 'MIddle', 'Last', 'male', '2024-11-07', 3, 'inactive', '2024-11-20 07:17:48', 3);

-- --------------------------------------------------------

--
-- Table structure for table `user_orders`
--

CREATE TABLE `user_orders` (
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` enum('cash_on_delivery') NOT NULL,
  `order_status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `address_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_orders`
--

INSERT INTO `user_orders` (`order_id`, `book_id`, `user_id`, `price`, `quantity`, `date`, `payment_method`, `order_status`, `address_id`) VALUES
(14, 5, 1, 750.99, 1, '2024-11-17 08:08:14', 'cash_on_delivery', 'pending', 1),
(15, 5, 1, 750.99, 1, '2024-11-17 08:11:47', 'cash_on_delivery', 'pending', 1),
(16, 5, 1, 750.99, 1, '2024-11-17 08:13:58', 'cash_on_delivery', 'pending', 1),
(17, 5, 1, 750.99, 1, '2024-11-17 08:16:08', 'cash_on_delivery', 'pending', 1),
(18, 5, 1, 750.99, 1, '2024-11-17 08:18:43', 'cash_on_delivery', 'pending', 1),
(19, 5, 1, 750.99, 1, '2024-11-17 08:20:58', 'cash_on_delivery', 'pending', 1),
(20, 5, 1, 750.99, 1, '2024-11-17 08:22:56', 'cash_on_delivery', 'pending', 1),
(21, 5, 1, 750.99, 1, '2024-11-17 08:24:06', 'cash_on_delivery', 'pending', 1),
(22, 5, 1, 750.99, 1, '2024-11-17 08:26:48', 'cash_on_delivery', 'pending', 1),
(32, 1, 3, 129.00, 1, '2024-11-20 07:24:05', 'cash_on_delivery', 'pending', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indexes for table `book_authors`
--
ALTER TABLE `book_authors`
  ADD PRIMARY KEY (`book_id`,`author_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `carts_ibfk_1` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_cart_id` (`cart_id`),
  ADD KEY `fk_address_id` (`address_id`);

--
-- Indexes for table `user_orders`
--
ALTER TABLE `user_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_orders_ibfk_1` (`user_id`),
  ADD KEY `user_orders_ibfk_2` (`book_id`),
  ADD KEY `fk_order_address_id` (`address_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_orders`
--
ALTER TABLE `user_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book_authors`
--
ALTER TABLE `book_authors`
  ADD CONSTRAINT `book_authors_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `book_authors_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `authors` (`author_id`);

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_address_id` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`),
  ADD CONSTRAINT `fk_cart_id` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`) ON DELETE SET NULL;

--
-- Constraints for table `user_orders`
--
ALTER TABLE `user_orders`
  ADD CONSTRAINT `fk_order_address_id` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`),
  ADD CONSTRAINT `fk_order_book_id` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `fk_order_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
