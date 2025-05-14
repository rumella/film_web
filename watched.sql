-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 14 May 2025, 06:52:40
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `watched`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `anatable`
--

CREATE TABLE `anatable` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `liked_movies` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `anatable`
--

INSERT INTO `anatable` (`id`, `client_id`, `liked_movies`) VALUES
(6, 2, '[{\"adult\":false,\"backdrop_path\":\"/yY76zq9XSuJ4nWyPDuwkdV7Wt0c.jpg\",\"genre_ids\":[28,53,878],\"id\":577922,\"original_language\":\"en\",\"original_title\":\"Tenet\",\"overview\":\"Armed with only one word - Tenet - and fighting for the survival of the entire world, the Protagonist journeys through a twilight world of international espionage on a mission that will unfold in something beyond real time.\",\"popularity\":10.3368,\"poster_path\":\"/aCIFMriQh8rvhxpN1IWGgvH0Tlg.jpg\",\"release_date\":\"2020-08-22\",\"title\":\"Tenet\",\"video\":false,\"vote_average\":7.181,\"vote_count\":10168},{\"adult\":false,\"backdrop_path\":\"/dbrLfmFNFEJWv8rLnjpgCKlXWSy.jpg\",\"genre_ids\":[12,28,878],\"id\":333339,\"original_language\":\"en\",\"original_title\":\"Ready Player One\",\"overview\":\"When the creator of a popular video game system dies, a virtual contest is created to compete for his fortune.\",\"popularity\":14.5756,\"poster_path\":\"/pU1ULUq8D3iRxl1fdX2lZIzdHuI.jpg\",\"release_date\":\"2018-03-28\",\"title\":\"Ready Player One\",\"video\":false,\"vote_average\":7.589,\"vote_count\":15917},{\"adult\":false,\"backdrop_path\":\"/y9zcjxEilWr44c4vJbEaLTgE0Uw.jpg\",\"genre_ids\":[878,18,10749],\"id\":31011,\"original_language\":\"en\",\"original_title\":\"Mr. Nobody\",\"overview\":\"Nemo Nobody leads an ordinary existence with his wife and 3 children; one day, he wakes up as a mortal centenarian in the year 2092.\",\"popularity\":4.3994,\"poster_path\":\"/qNkIONc4Rgmzo23ph7qWp9QfVnW.jpg\",\"release_date\":\"2009-11-06\",\"title\":\"Mr. Nobody\",\"video\":false,\"vote_average\":7.81,\"vote_count\":5904},{\"adult\":false,\"backdrop_path\":\"/sNx1A3822kEbqeUxvo5A08o4N7o.jpg\",\"genre_ids\":[28,35,53],\"id\":1195506,\"original_language\":\"en\",\"original_title\":\"Novocaine\",\"overview\":\"When the girl of his dreams is kidnapped, everyman Nate turns his inability to feel pain into an unexpected strength in his fight to get her back.\",\"popularity\":29.7101,\"poster_path\":\"/xmMHGz9dVRaMY6rRAlEX4W0Wdhm.jpg\",\"release_date\":\"2025-03-12\",\"title\":\"Novocaine\",\"video\":false,\"vote_average\":7,\"vote_count\":174},{\"adult\":false,\"backdrop_path\":\"/neeNHeXjMF5fXoCJRsOmkNGC7q.jpg\",\"genre_ids\":[18,36],\"id\":872585,\"original_language\":\"en\",\"original_title\":\"Oppenheimer\",\"overview\":\"The story of J. Robert Oppenheimer\'s role in the development of the atomic bomb during World War II.\",\"popularity\":27.7046,\"poster_path\":\"/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg\",\"release_date\":\"2023-07-19\",\"title\":\"Oppenheimer\",\"video\":false,\"vote_average\":8.067,\"vote_count\":10065},{\"adult\":false,\"backdrop_path\":\"/hlfu6g0h0D65SjkVhQBU20zePTl.jpg\",\"genre_ids\":[28,12,14,16],\"id\":1357633,\"original_language\":\"ja\",\"original_title\":\"俺だけレベルアップな件 -ReAwakening-\",\"overview\":\"Over a decade after \'gates\' connecting worlds appeared, awakening \'hunters\' with superpowers, weakest hunter Sung Jinwoo encounters a double dungeon and accepts a mysterious quest, becoming the only one able to level up, changing his fate. A catch-up recap of the first season coupled with an exclusive sneak peek of the first two episodes of the highly anticipated second season in one momentous theatrical fan experience.\",\"popularity\":58.6467,\"poster_path\":\"/dblIFen0bNZAq8icJXHwrjfymDW.jpg\",\"release_date\":\"2024-11-26\",\"title\":\"Solo Leveling -ReAwakening-\",\"video\":false,\"vote_average\":6.949,\"vote_count\":177},{\"adult\":false,\"backdrop_path\":\"/2t8uXcm5CuChOoX5gEKl918yv6I.jpg\",\"genre_ids\":[28,878,18],\"id\":39254,\"original_language\":\"en\",\"original_title\":\"Real Steel\",\"overview\":\"Charlie Kenton is a washed-up fighter who retired from the ring when robots took over the sport. After his robot is trashed, he reluctantly teams up with his estranged son to rebuild and train an unlikely contender.\",\"popularity\":14.1565,\"poster_path\":\"/4GIeI5K5YdDUkR3mNQBoScpSFEf.jpg\",\"release_date\":\"2011-09-28\",\"title\":\"Real Steel\",\"video\":false,\"vote_average\":7.048,\"vote_count\":8530},{\"adult\":false,\"backdrop_path\":\"/gHz4ZQytRs8YGrqFMwB3Vrr8pig.jpg\",\"genre_ids\":[18,10749,878],\"id\":274870,\"original_language\":\"en\",\"original_title\":\"Passengers\",\"overview\":\"A spacecraft traveling to a distant colony planet and transporting thousands of people has a malfunction in its sleep chambers. As a result, two passengers are awakened 90 years early.\",\"popularity\":10.8949,\"poster_path\":\"/oZpdONg32luHu0g8HcysuPgSlIK.jpg\",\"release_date\":\"2016-12-21\",\"title\":\"Passengers\",\"video\":false,\"vote_average\":6.952,\"vote_count\":13475},{\"adult\":false,\"backdrop_path\":\"/zOpe0eHsq0A2NvNyBbtT6sj53qV.jpg\",\"genre_ids\":[28,878,35,10751,12,14],\"id\":939243,\"original_language\":\"en\",\"original_title\":\"Sonic the Hedgehog 3\",\"overview\":\"Sonic, Knuckles, and Tails reunite against a powerful new adversary, Shadow, a mysterious villain with powers unlike anything they have faced before. With their abilities outmatched in every way, Team Sonic must seek out an unlikely alliance in hopes of stopping Shadow and protecting the planet.\",\"popularity\":148.7506,\"poster_path\":\"/d8Ryb8AunYAuycVKDp5HpdWPKgC.jpg\",\"release_date\":\"2024-12-19\",\"title\":\"Sonic the Hedgehog 3\",\"video\":false,\"vote_average\":7.744,\"vote_count\":2364},{\"adult\":false,\"backdrop_path\":\"/hiKmpZMGZsrkA3cdce8a7Dpos1j.jpg\",\"genre_ids\":[35,53,18],\"id\":496243,\"original_language\":\"ko\",\"original_title\":\"기생충\",\"overview\":\"All unemployed, Ki-taek\'s family takes peculiar interest in the wealthy and glamorous Parks for their livelihood until they get entangled in an unexpected incident.\",\"popularity\":33.1208,\"poster_path\":\"/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg\",\"release_date\":\"2019-05-30\",\"title\":\"Parasite\",\"video\":false,\"vote_average\":8.5,\"vote_count\":18928},{\"adult\":false,\"backdrop_path\":\"/isxWigGBWpXtc2xOxybiYWxHzWm.jpg\",\"genre_ids\":[27,53,878],\"id\":838484,\"original_language\":\"en\",\"original_title\":\"Choose or Die\",\"overview\":\"In pursuit of an unclaimed $125,000 prize, a broke college dropout decides to play an obscure, 1980s survival computer game. But the game curses her, and she’s faced with dangerous choices and reality-warping challenges. After a series of unexpectedly terrifying moments, she realizes she’s no longer playing for the money but for her life.\",\"popularity\":4.7799,\"poster_path\":\"/jEYE5BPFd5FuPa1judcjpW6xqKp.jpg\",\"release_date\":\"2022-04-15\",\"title\":\"Choose or Die\",\"video\":false,\"vote_average\":5.274,\"vote_count\":807},{\"adult\":false,\"backdrop_path\":\"/u2J7HxrT9QtzIy37S5q3RdrzxXD.jpg\",\"genre_ids\":[28,53],\"id\":615457,\"original_language\":\"en\",\"original_title\":\"Nobody\",\"overview\":\"Hutch Mansell, a suburban dad, overlooked husband, nothing neighbor — a \\\"nobody.\\\" When two thieves break into his home one night, Hutch\'s unknown long-simmering rage is ignited and propels him on a brutal path that will uncover dark secrets he fought to leave behind.\",\"popularity\":14.9641,\"poster_path\":\"/oBgWY00bEFeZ9N25wWVyuQddbAo.jpg\",\"release_date\":\"2021-03-18\",\"title\":\"Nobody\",\"video\":false,\"vote_average\":7.925,\"vote_count\":7329},{\"adult\":false,\"backdrop_path\":\"/2PCcEJ1lUJgir598yMGXkluJa6y.jpg\",\"genre_ids\":[878,28,12,10751],\"id\":438590,\"original_language\":\"en\",\"original_title\":\"A-X-L\",\"overview\":\"The life of a teenage boy is forever altered by a chance encounter with cutting edge military technology.\",\"popularity\":4.4909,\"poster_path\":\"/9kB56ZdMB6RgY5QtX9Bar45jCeI.jpg\",\"release_date\":\"2018-08-23\",\"title\":\"A-X-L\",\"video\":false,\"vote_average\":6.284,\"vote_count\":1176},{\"adult\":false,\"backdrop_path\":\"/qRik98bxXPn4qCHTe3ffhbGFRAu.jpg\",\"genre_ids\":[27,28,878],\"id\":295830,\"original_language\":\"ja\",\"original_title\":\"進撃の巨人 ATTACK ON TITAN\",\"overview\":\"100 years ago, titans suddenly appeared on Earth. Soon, human civilization veered on collapse due to the titans. Humans then built a giant wall to defend themselves. Within the giant walls, humans lived in peace, but, 100 years later, the giant wall is broken.\",\"popularity\":7.4815,\"poster_path\":\"/uT7qQMmP07FG935JfM1oMlwfvoV.jpg\",\"release_date\":\"2015-08-01\",\"title\":\"Attack on Titan\",\"video\":false,\"vote_average\":6.106,\"vote_count\":769}]'),
(8, 15, '[{\"adult\":false,\"backdrop_path\":\"/isxWigGBWpXtc2xOxybiYWxHzWm.jpg\",\"genre_ids\":[27,53,878],\"id\":838484,\"original_language\":\"en\",\"original_title\":\"Choose or Die\",\"overview\":\"In pursuit of an unclaimed $125,000 prize, a broke college dropout decides to play an obscure, 1980s survival computer game. But the game curses her, and she’s faced with dangerous choices and reality-warping challenges. After a series of unexpectedly terrifying moments, she realizes she’s no longer playing for the money but for her life.\",\"popularity\":4.1617,\"poster_path\":\"/jEYE5BPFd5FuPa1judcjpW6xqKp.jpg\",\"release_date\":\"2022-04-15\",\"title\":\"Choose or Die\",\"video\":false,\"vote_average\":5.274,\"vote_count\":810},{\"adult\":false,\"backdrop_path\":\"/yY76zq9XSuJ4nWyPDuwkdV7Wt0c.jpg\",\"genre_ids\":[28,53,878],\"id\":577922,\"original_language\":\"en\",\"original_title\":\"Tenet\",\"overview\":\"Armed with only one word - Tenet - and fighting for the survival of the entire world, the Protagonist journeys through a twilight world of international espionage on a mission that will unfold in something beyond real time.\",\"popularity\":21.4751,\"poster_path\":\"/aCIFMriQh8rvhxpN1IWGgvH0Tlg.jpg\",\"release_date\":\"2020-08-22\",\"title\":\"Tenet\",\"video\":false,\"vote_average\":7.181,\"vote_count\":10222}]'),
(9, 16, '[{\"adult\":false,\"backdrop_path\":\"/sd4xN5xi8tKRPrJOWwNiZEile7f.jpg\",\"genre_ids\":[16,12,35,10751],\"id\":920,\"original_language\":\"en\",\"original_title\":\"Cars\",\"overview\":\"Lightning McQueen, a hotshot rookie race car driven to succeed, discovers that life is about the journey, not the finish line, when he finds himself unexpectedly detoured in the sleepy Route 66 town of Radiator Springs. On route across the country to the big Piston Cup Championship in California to compete against two seasoned pros, McQueen gets to know the town\'s offbeat characters.\",\"popularity\":9.7527,\"poster_path\":\"/2Touk3m5gzsqr1VsvxypdyHY5ci.jpg\",\"release_date\":\"2006-06-08\",\"title\":\"Cars\",\"video\":false,\"vote_average\":6.997,\"vote_count\":14301}]');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `authority`
--

CREATE TABLE `authority` (
  `id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `authority`
--

INSERT INTO `authority` (`id`, `role`) VALUES
(1, 'admin'),
(2, 'mod'),
(3, 'client');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `birth_date` date NOT NULL,
  `gender` enum('Erkek','Kadın','Diğer') NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `authority_id` int(11) DEFAULT 3,
  `profile_photo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `clients`
--

INSERT INTO `clients` (`id`, `name`, `surname`, `birth_date`, `gender`, `email`, `password`, `authority_id`, `profile_photo`) VALUES
(2, 'parzival', 'parzivaloğlu', '2001-01-01', 'Erkek', 'noway@goaway.com', '$2y$10$m2Ig5Uc7k4Iex81R4YfQxOXgV7Jbm3COR7c30wRNxB7G8KPtnr4Ue', 1, '6819f328608b0_cry_anime_girl_thumb.png'),
(15, 'Robber', 'TheBank', '2025-06-07', 'Erkek', 'rob@thebank.com', '$2y$10$bsMfqXRQ3d7fJqH6iwL1aeXBZFGONN/F12ixQ05JyGOkKgqj7iR6.', 3, NULL),
(16, 'edi', 'büdü', '2025-12-27', 'Kadın', 'edi@budu.com', '$2y$10$/68Tgisum8k4axEZrlAjg.mKOfy8mYLp2PecMvBaqXU4K26/Cgmq6', 3, '6819fd8cb32b8_eren_face.jpg');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `comment` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `comments`
--

INSERT INTO `comments` (`id`, `client_id`, `movie_id`, `comment`, `created_at`) VALUES
(5, 2, 295830, 'Acar dizi yaw.', '2025-05-06 03:51:06'),
(11, 2, 838484, 'Çok iyi yaw, adamın ağzından film şeridi fln çıkıyo vs. baya iyi.', '2025-05-06 12:01:40'),
(14, 2, 872585, 'Patlatman guzum.', '2025-05-14 04:51:38');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `replies`
--

CREATE TABLE `replies` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `reply` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `anatable`
--
ALTER TABLE `anatable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Tablo için indeksler `authority`
--
ALTER TABLE `authority`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_clients_authority_id` (`authority_id`);

--
-- Tablo için indeksler `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Tablo için indeksler `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `anatable`
--
ALTER TABLE `anatable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Tablo için AUTO_INCREMENT değeri `authority`
--
ALTER TABLE `authority`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Tablo için AUTO_INCREMENT değeri `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Tablo için AUTO_INCREMENT değeri `replies`
--
ALTER TABLE `replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `anatable`
--
ALTER TABLE `anatable`
  ADD CONSTRAINT `anatable_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `fk_clients_authority_id` FOREIGN KEY (`authority_id`) REFERENCES `authority` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
