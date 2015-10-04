-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Авг 29 2015 г., 17:52
-- Версия сервера: 5.6.25
-- Версия PHP: 5.6.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `auth`
--

-- --------------------------------------------------------

--
-- Структура таблицы `token`
--

CREATE TABLE IF NOT EXISTS `token` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `token` varchar(36) COLLATE utf8_bin NOT NULL,
  `time_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_ip` varchar(36) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `token`
--

INSERT INTO `token` (`id`, `id_user`, `token`, `time_add`, `user_ip`) VALUES
(53, 1, '7a69fec0954b1e5bdbff724b1fdfead2', '2015-08-29 18:34:57', '::1');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL,
  `login` varchar(36) COLLATE utf8_bin NOT NULL,
  `pwd` varchar(36) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `login`, `pwd`) VALUES
(1, 'c3284d0f94606de1fd2af172aba15bf3', 'c3284d0f94606de1fd2af172aba15bf3');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `token`
--
ALTER TABLE `token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=54;
--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
