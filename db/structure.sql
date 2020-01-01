-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Дек 30 2019 г., 01:30
-- Версия сервера: 5.6.37
-- Версия PHP: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `ibc`
--

-- --------------------------------------------------------

--
-- Структура таблицы `authors`
--

CREATE TABLE `authors` (
  `id` int(20) UNSIGNED NOT NULL,
  `first_name` varchar(55) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_name` varchar(55) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `middle_name` varchar(55) COLLATE utf8mb4_unicode_520_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `author_relationships`
--

CREATE TABLE `author_relationships` (
  `publication_id` int(20) UNSIGNED NOT NULL,
  `author_id` int(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `copies`
--

CREATE TABLE `copies` (
  `id` int(20) UNSIGNED NOT NULL,
  `publication_id` int(20) UNSIGNED NOT NULL,
  `date_added` datetime NOT NULL,
  `librarian_id` int(20) UNSIGNED NOT NULL,
  `registration_number` varchar(40) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `blog_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `departments`
--

CREATE TABLE `departments` (
  `id` int(20) UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `parent` int(20) UNSIGNED NOT NULL,
  `blog_id` int(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `genres`
--

CREATE TABLE `genres` (
  `id` int(20) UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `parent` int(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `genre_relationships`
--

CREATE TABLE `genre_relationships` (
  `publication_id` int(20) UNSIGNED NOT NULL,
  `genre_id` int(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `issuances`
--

CREATE TABLE `issuances` (
  `id` int(20) UNSIGNED NOT NULL,
  `copy_id` int(20) UNSIGNED NOT NULL,
  `reader_id` int(20) UNSIGNED NOT NULL,
  `librarian_id` int(20) UNSIGNED NOT NULL,
  `clearance_date` datetime NOT NULL,
  `return_date` datetime NOT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `blog_id` int(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `publications`
--

CREATE TABLE `publications` (
  `id` int(20) UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `publishing_house_id` int(20) UNSIGNED NOT NULL,
  `annotation` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `isbn` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `year` varchar(4) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `author_id` int(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `publishing_houses`
--

CREATE TABLE `publishing_houses` (
  `id` int(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `readers`
--

CREATE TABLE `readers` (
  `id` int(20) UNSIGNED NOT NULL,
  `card` varchar(40) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `first_name` varchar(55) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_name` varchar(55) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `sex` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `librarian_id` int(20) UNSIGNED NOT NULL,
  `department_id` int(20) UNSIGNED NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `blog_id` int(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `author_relationships`
--
ALTER TABLE `author_relationships`
  ADD KEY `publication_id` (`publication_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Индексы таблицы `copies`
--
ALTER TABLE `copies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publication_id` (`publication_id`),
  ADD KEY `librarian_id` (`librarian_id`),
  ADD KEY `blog_id` (`blog_id`);

--
-- Индексы таблицы `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent` (`parent`),
  ADD KEY `blog_id` (`blog_id`);

--
-- Индексы таблицы `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent` (`parent`);

--
-- Индексы таблицы `genre_relationships`
--
ALTER TABLE `genre_relationships`
  ADD KEY `publication_id` (`publication_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Индексы таблицы `issuances`
--
ALTER TABLE `issuances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `copy_id` (`copy_id`),
  ADD KEY `reader_id` (`reader_id`),
  ADD KEY `librarian_id` (`librarian_id`),
  ADD KEY `blog_id` (`blog_id`),
  ADD KEY `status` (`status`),
  ADD KEY `clearance_date` (`clearance_date`),
  ADD KEY `return_date` (`return_date`);

--
-- Индексы таблицы `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publishing_house_id` (`publishing_house_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Индексы таблицы `publishing_houses`
--
ALTER TABLE `publishing_houses`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `readers`
--
ALTER TABLE `readers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `copies`
--
ALTER TABLE `copies`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `issuances`
--
ALTER TABLE `issuances`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `publications`
--
ALTER TABLE `publications`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `publishing_houses`
--
ALTER TABLE `publishing_houses`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `readers`
--
ALTER TABLE `readers`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `author_relationships`
--
ALTER TABLE `author_relationships`
  ADD CONSTRAINT `author_relationships_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `author_relationships_ibfk_2` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `copies`
--
ALTER TABLE `copies`
  ADD CONSTRAINT `copies_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `genre_relationships`
--
ALTER TABLE `genre_relationships`
  ADD CONSTRAINT `genre_relationships_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `genre_relationships_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `publications`
--
ALTER TABLE `publications`
  ADD CONSTRAINT `publications_ibfk_1` FOREIGN KEY (`publishing_house_id`) REFERENCES `publishing_houses` (`id`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `readers`
--
ALTER TABLE `readers`
  ADD CONSTRAINT `readers_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
