-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2024 at 08:23 AM
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
(1, 'Voice of Ancestors', 'fantasy', 'Getpremades.com', '978-3-16-148410-0', 129.00, 35, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728309990/b3_w75zis.jpg', 'In \"Voice of Ancestors,\" a young mage discovers a hidden realm where the whispers of ancient spirits guide her destiny. As she navigates a world of mythical creatures and powerful magic, she must unravel the secrets of her lineage to protect her homeland from an impending darkness. With the help of her ancestors\' voices, she embarks on a quest that will challenge her courage, test her loyalty, and ultimately reveal her true potential.', 'Fantasy, Magic, Ancestors, Quest, Young Mage, Mythical Creatures, Ancient Spirits, Destiny, Adventure, Dark Forces, Wolf', '2024-10-30', 0, 0.00, 0.00),
(2, 'Night Spinner', 'fantasy', 'Addie Thorley', '978-2-34-567890-1', 350.75, 12, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728309990/b1_vgu4hi.jpg', 'In \"Night Spinner,\" a gifted weaver of dreams discovers her ability to manipulate the threads of fate. When an ancient evil threatens to unravel the fabric of her world, she must harness her unique powers to protect her people. Guided by the enigmatic spirits of the night, she embarks on a perilous journey filled with enchantment and danger, learning that the strength of her heart is as vital as the magic she wields.', 'Fantasy, Dreams, Magic, Fate, Weaver, Adventure, Ancient Evil, Spirits, Journey, Enchantment, Girl, Bird', '2024-01-15', 0, 0.00, 0.00),
(3, 'Storm Tide', 'fantasy', 'Den Patrick', '978-3-45-678901-2', 900.00, 78, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728309989/b2_if3uu4.jpg', 'In \"Storm Tide,\" a coastal kingdom faces an unprecedented threat as ancient sea spirits awaken from their slumber. With storms raging and waters rising, a brave sailor must unite a band of unlikely heroes to confront the tempest and uncover the secrets of the deep. As they navigate treacherous waters and uncover hidden truths, they learn that the power of friendship and courage can change the tide of fate.', 'Fantasy, Adventure, Sea Spirits, Storms, Coastal Kingdom, Heroes, Tempest, Secrets, Friendship, Courage, Dolphin, Sea', '2023-05-20', 10, 0.00, 150.00),
(4, 'Chronicles of Narnia', 'fantasy', 'Getpremades.com', '978-4-56-789012-3', 450.25, 50, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728309989/b4_p43b9p.jpg', 'In \"Chronicles of Narnia,\" four siblings stumble upon a magical wardrobe that leads them to the enchanting land of Narnia, where talking animals and mythical creatures thrive. As they uncover Narnia’s secrets, they are drawn into an epic battle between good and evil, guided by the noble lion Aslan. Their journey through this captivating realm tests their bravery, friendship, and faith, revealing the true power of love and sacrifice.', 'Fantasy, Magic, Narnia, Adventure, Talking Animals, Aslan, Good vs. Evil, Siblings, Epic Battle, Friendship, Hero, Lion', '2005-07-29', 20, 0.00, 250.00),
(5, 'Penpal', 'horror', 'Dathan Auerbach', '978-5-67-890123-4', 750.99, 23, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728381588/Penpal_uab1ip.jpg', 'In \"Penpal,\" a series of unsettling letters begins to arrive in the mail, written by a mysterious stranger who seems to know far too much about the protagonist\'s life. As the correspondence unfolds, secrets from the past resurface, revealing a dark connection that threatens to unravel everything they hold dear. With each chilling message, the line between friend and foe blurs, leading to a terrifying climax that forces the protagonist to confront their deepest fears.', 'Horror, Mystery, Letters, Suspense, Dark Secrets, Psychological, Fear, Intrigue, Isolation, Twists, Yellow, Man, Woods, Trees', '2022-09-10', 5, 0.00, 100.00),
(6, 'Set in Stone', 'horror', 'Mari Mancusi', '978-7-89-012345-6', 1100.80, 45, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728381587/Set_in_Stone_Disney__A_Twisted_Tale_15_s088x2.jpg', 'In \"Set in Stone,\" a small town is haunted by an ancient curse that traps the souls of the restless dead in its graveyard. When a group of friends uncovers a hidden tombstone with eerie inscriptions, they unwittingly awaken the malevolent spirits bound to it. As sinister events begin to unfold, they must unravel the dark history of the town and confront the terror that has been lying in wait. Time is running out, and the line between the living and the dead blurs as they fight to escape their grim fate.', 'Horror, Curse, Graveyard, Spirits, Ancient Tombstone, Small Town, Supernatural, Mystery, Friendship, Survival, Boy, Sword, Bird, Castle, Trees', '2023-02-15', 3, 0.00, 80.00),
(7, 'A Thousand Letters', 'romance', 'Staci Hart', '978-6-54-321098-7', 500.00, 90, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728381586/A_Thousand_Letters_The_Austens_Series_by_Staci_Hart_jxykp2.jpg', 'In \"A Thousand Letters,\" a young woman discovers a collection of heartfelt letters hidden away in her late grandmother’s attic. As she reads through the poignant words, she learns about a long-lost love story that transcends time and distance. Inspired by her grandmother’s experiences, she embarks on a journey of self-discovery and unexpected romance, finding courage to pursue her own heart\'s desires. Through the power of written words, she uncovers the magic of love that can bridge generations.', 'Romance, Letters, Love Story, Self-Discovery, Family, Heartfelt, Journey, Generations, Inspiration, Courage, Man, Woman, Green, Light, Street light, Snow', '2023-08-10', 2, 0.00, 30.00),
(8, 'You Deserve Each Other', 'romance', 'Sarah Hogle', '978-0-12-345678-9', 320.40, 17, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728381586/20_Best_Romance_Chick_Lit_Books_of_2020_-_The_Bibliofile_urxfbt.jpg', 'In \"You Deserve Each Other,\" a couple on the brink of their wedding realizes that their relationship might not be as perfect as it seems. With both partners secretly wanting to call off the engagement, they engage in a playful yet poignant battle of wills, testing their love and compatibility in unexpected ways. As they navigate misunderstandings and heartfelt revelations, they discover what it truly means to deserve one another, leading to a deeper connection than they ever imagined.', 'Romance, Engagement, Relationship, Humor, Love, Self-Discovery, Misunderstanding, Conflict, Heartfelt, Connection, Man, Woman, Pink, Flower, Window', '2020-11-12', 0, 0.00, 10.00),
(9, 'Shadow Warrior', 'action', 'Getpremades.com', '978-8-76-543210-9', 1800.90, 66, 'https://res.cloudinary.com/dvr0evn7t/image/upload/v1728381587/Designed_by_TrifBookDesign_com_lhjsjo.jpg', 'In \"Shadow Warrior,\" a skilled assassin grapples with his dark past while navigating the treacherous underworld of a futuristic city. Tasked with eliminating a powerful crime lord, he must use his unparalleled combat skills and stealth to survive. As he delves deeper into the shadows, he uncovers a conspiracy that threatens not only his life but the lives of countless innocents. With every decision weighing heavily on his conscience, he must choose between vengeance and redemption in a high-stakes battle for justice.', 'Action, Assassin, Combat, Futuristic, Crime Lord, Underworld, Stealth, Conspiracy, Vengeance, Redemption, Hero, Sword, Dragon, Wasteland, Monster', '2021-06-05', 15, 0.00, 200.00),
(10, 'Whistle in the Dark', 'mystery', 'Emma Healey', '978-9-87-654321-0', 410.50, 30, 'https://res.cloudinary.com/izynegallardo/image/upload/v1730443580/pandieno_bookstore/cover_images/book_10_1730443577.jpg', 'In \"Whistle in the Dark,\" a woman returns to her hometown to confront the shadows of her past, only to find herself embroiled in a mysterious disappearance that has haunted the community for years. As she pieces together clues from her own childhood and interviews the townspeople, she uncovers hidden truths that challenge her understanding of loyalty and betrayal. In a gripping narrative filled with suspense, she must decide how far she’s willing to go to uncover the truth and protect those she loves.', 'Mystery, Suspense, Disappearance, Hometown, Childhood, Clues, Investigation, Loyalty, Betrayal, Truth, Woman, Forest, Night, Lantern', '2023-09-25', 8, 0.00, 75.00),
(21, 'Laughs on the Run', 'comedy', 'Jamie Baxter', '978-1-23456-789-0', 230.00, 100, 'https://res.cloudinary.com/izynegallardo/image/upload/v1730441002/pandieno_bookstore/cover_images/vrbfdwypdrjlxvv2yryq.jpg', 'In a world where the mundane meets the absurd, \"Laughs on the Run\" takes readers on a rollicking adventure with Charlie, an average guy whose life is turned upside down when he accidentally becomes a viral sensation. Join Charlie as he navigates a series of hilarious misadventures, from accidentally crashing a celebrity wedding to finding himself in a high-stakes chase involving a runaway llama. With witty dialogue and an unforgettable cast of quirky characters, this comedy will have you in stitches from start to finish.', 'Comedy, Humor, Adventure, Viral Sensation, Quirky Characters', '2004-05-15', 0, NULL, 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(30) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
