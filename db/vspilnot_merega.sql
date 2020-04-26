-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Час створення: Лис 14 2017 р., 12:42
-- Версія сервера: 5.5.53-MariaDB
-- Версія PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `vspilnot_merega`
--

-- --------------------------------------------------------

--
-- Структура таблиці `members_users`
--

DROP TABLE IF EXISTS `members_users`;
CREATE TABLE `members_users` (
  `net_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `list_name` varchar(50) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `dislike` bit(1) DEFAULT b'0',
  `voice` bit(1) DEFAULT b'0'
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `nets`
--

DROP TABLE IF EXISTS `nets`;
CREATE TABLE `nets` (
  `net_id` int(11) NOT NULL,
  `net_level` int(11) DEFAULT NULL,
  `net_address` int(11) DEFAULT NULL,
  `parent_net_id` int(11) DEFAULT NULL,
  `first_net_id` int(11) DEFAULT NULL,
  `full_net_address` int(11) DEFAULT NULL,
  `count_of_nets` int(11) DEFAULT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `nets`
--

INSERT INTO `nets` (`net_id`, `net_level`, `net_address`, `parent_net_id`, `first_net_id`, `full_net_address`, `count_of_nets`) VALUES
(1, 1, 1, NULL, 1, 10000, 1),
(224, 1, 1, NULL, 224, 10000, 1),
(237, 1, 1, NULL, 237, 10000, 1);

-- --------------------------------------------------------

--
-- Структура таблиці `nets_data`
--

DROP TABLE IF EXISTS `nets_data`;
CREATE TABLE `nets_data` (
  `net_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `goal` text,
  `resource_name_1` varchar(50) DEFAULT NULL,
  `resource_link_1` varchar(255) DEFAULT NULL,
  `resource_name_2` varchar(50) DEFAULT NULL,
  `resource_link_2` varchar(255) DEFAULT NULL,
  `resource_name_3` varchar(50) DEFAULT NULL,
  `resource_link_3` varchar(255) DEFAULT NULL,
  `resource_name_4` varchar(50) DEFAULT NULL,
  `resource_link_4` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `nets_data`
--

INSERT INTO `nets_data` (`net_id`, `name`, `goal`, `resource_name_1`, `resource_link_1`, `resource_name_2`, `resource_link_2`, `resource_name_3`, `resource_link_3`, `resource_name_4`, `resource_link_4`) VALUES
(1, 'СПІЛЬНОТА ПРОЕКТУ "Ти і Світ"', 'Мета. Надати людині IT-інструмент, який надає їй можливість реалізувати свій ресурс для досягнення особистої мети (цілі).\r\n\r\nПовний текст:\r\nhttps://drive.google.com/file/d/0B_ERsyuQYfojemFGdWFvQ3hubzA/view?usp=sharing', 'ФБ група', 'https://www.facebook.com/groups/uandw/', 'Вибір домену', 'https://docs.google.com/spreadsheets/d/1KzwHb-zw9kaG8BNHtk7WPOw5EZ_X58JtRstSUL15RbM/edit?usp=sharing', 'Задача мінімум', 'https://drive.google.com/file/d/0B_ERsyuQYfojY3B2REtqTHFocjA/view?usp=sharing', 'Пропозиції', 'https://docs.google.com/spreadsheets/d/1ci_LLEOcClO5GZKTaR1KsjzPOA-yF-7gRtB_OWF0CUE/edit?usp=sharing'),
(224, 'УНІВЕРСАЛЬНИЙ ОФІС', 'МЕТА. СТВОРИТИ спільний простір для спілкування, праці, творчості та відпочинку.\r\n\r\nЗАВДАННЯ. Зібрати однодумців, скинутись ресурсами за можливостями та бажанням кожного з учасників, знайти приміщення 30-50м2 і облаштувати його відповідно до МЕТИ.\r\n\r\nПЛАН. Зібрати 30-50 учасників. Скласти план реалізації ЗАВДАННЯ і виконати його.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(237, 'КАЗКОВИЙ КРАЙ', 'Розвивати УЯВУ в дітей та дорослих.\r\n\r\nЗАВДАННЯ. Створити сайт, на майданчику якого щодня буде викладатись аудіозапис казки. Забезпечити відвідуваність сайту 1000 користувачів в день.', 'ПЛАН', 'https://docs.google.com/document/d/1NPG3_EDv3h6cumuGhjwJzRLiSTxKV-dIicBx2C0MAbU/edit?usp=sharing', 'РОЛІ', 'https://docs.google.com/spreadsheets/d/1UCn5zXZUJeoR71vm3J8TzKp1Z_-tabye1vX41PXhkQ8/edit?usp=sharing', 'САЙТ ТЗ', 'https://docs.google.com/document/d/1PKTGwLm5TsuCVYC4ZEY8MF2DoPEUtLAOC8kOKiRBCMk/edit?usp=sharing', '', '');

-- --------------------------------------------------------

--
-- Структура таблиці `nets_events`
--

DROP TABLE IF EXISTS `nets_events`;
CREATE TABLE `nets_events` (
  `event_id` int(11) NOT NULL,
  `net_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_node_id` int(11) DEFAULT NULL,
  `notification_tpl_id` int(11) DEFAULT NULL,
  `event_code` int(4) DEFAULT NULL,
  `notification_text` varchar(255) DEFAULT NULL,
  `new` bit(1) DEFAULT b'1',
  `shown` bit(1) DEFAULT b'0'
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `nets_users_data`
--

DROP TABLE IF EXISTS `nets_users_data`;
CREATE TABLE `nets_users_data` (
  `net_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email_show` bit(1) DEFAULT b'0',
  `name_show` bit(1) DEFAULT b'0',
  `mobile_show` bit(1) DEFAULT b'0'
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `nets_users_data`
--

INSERT INTO `nets_users_data` (`net_id`, `user_id`, `email_show`, `name_show`, `mobile_show`) VALUES
(224, 63, b'0', b'0', b'0'),
(237, 63, b'0', b'0', b'0'),
(269, 1, b'0', b'0', b'0');

-- --------------------------------------------------------

--
-- Структура таблиці `nodes`
--

DROP TABLE IF EXISTS `nodes`;
CREATE TABLE `nodes` (
  `node_id` int(11) NOT NULL,
  `node_level` int(11) DEFAULT NULL,
  `node_address` int(11) DEFAULT NULL,
  `parent_node_id` int(11) DEFAULT NULL,
  `first_node_id` int(11) DEFAULT NULL,
  `full_node_address` int(11) DEFAULT NULL,
  `count_of_members` int(11) DEFAULT NULL,
  `node_date` datetime DEFAULT NULL,
  `blocked` bit(1) DEFAULT NULL,
  `changes` bit(1) DEFAULT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `nodes`
--

INSERT INTO `nodes` (`node_id`, `node_level`, `node_address`, `parent_node_id`, `first_node_id`, `full_node_address`, `count_of_members`, `node_date`, `blocked`, `changes`) VALUES
(1, 1, 1, NULL, 1, 1000000, 1, '2017-09-27 23:24:16', b'0', b'0'),
(2, 2, 1, 1, 1, 1100000, 0, '2017-02-18 13:31:21', b'0', b'0'),
(3, 2, 2, 1, 1, 1200000, 0, '2017-02-03 10:49:36', b'0', b'0'),
(4, 2, 3, 1, 1, 1300000, 0, '2017-09-28 08:59:00', b'0', b'0'),
(5, 2, 4, 1, 1, 1400000, 0, '2017-09-28 09:11:52', b'0', b'0'),
(6, 2, 5, 1, 1, 1500000, 0, '2017-09-27 22:26:02', b'0', b'0'),
(38, 2, 6, 1, 1, 1600000, 0, '2017-09-18 09:29:47', b'0', b'0'),
(224, 1, 1, NULL, 224, 1000000, 1, '2017-02-19 14:12:28', b'0', b'0'),
(225, 2, 1, 224, 224, 1100000, 0, '2017-02-19 14:12:28', b'0', NULL),
(226, 2, 2, 224, 224, 1200000, 0, '2017-02-19 14:12:28', NULL, NULL),
(227, 2, 3, 224, 224, 1300000, 0, '2017-02-19 14:12:28', NULL, NULL),
(228, 2, 4, 224, 224, 1400000, 0, '2017-02-19 14:12:28', NULL, NULL),
(229, 2, 5, 224, 224, 1500000, 0, '2017-02-19 14:12:28', NULL, NULL),
(230, 2, 6, 224, 224, 1600000, 0, '2017-02-19 14:12:28', NULL, NULL),
(237, 1, 1, NULL, 237, 1000000, 1, '2017-03-09 12:15:56', b'0', b'0'),
(238, 2, 1, 237, 237, 1100000, 0, '2017-09-23 13:31:27', b'0', b'0'),
(239, 2, 2, 237, 237, 1200000, 0, '2017-09-27 22:26:21', b'0', b'0'),
(240, 2, 3, 237, 237, 1300000, 0, '2017-09-16 21:49:11', b'0', b'0'),
(241, 2, 4, 237, 237, 1400000, 0, '2017-03-09 12:15:56', b'0', NULL),
(242, 2, 5, 237, 237, 1500000, 0, '2017-03-09 12:15:56', b'0', NULL),
(243, 2, 6, 237, 237, 1600000, 0, '2017-03-09 12:15:56', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `nodes_tmp`
--

DROP TABLE IF EXISTS `nodes_tmp`;
CREATE TABLE `nodes_tmp` (
  `node_id` int(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `list_name` varchar(50) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `nodes_users`
--

DROP TABLE IF EXISTS `nodes_users`;
CREATE TABLE `nodes_users` (
  `node_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `invite` varchar(255) DEFAULT NULL,
  `old_list_name` varchar(50) DEFAULT NULL,
  `old_list_note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `nodes_users`
--

INSERT INTO `nodes_users` (`node_id`, `user_id`, `invite`, `old_list_name`, `old_list_note`) VALUES
(1, 24, NULL, NULL, NULL),
(224, 63, NULL, NULL, NULL),
(237, 63, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `notifications_tpl`
--

DROP TABLE IF EXISTS `notifications_tpl`;
CREATE TABLE `notifications_tpl` (
  `notification_tpl_id` int(11) NOT NULL,
  `event_code` int(2) DEFAULT NULL,
  `notification_code` int(1) DEFAULT NULL,
  `notification_text` varchar(255) DEFAULT NULL,
  `notification_action` varchar(255) DEFAULT NULL,
  `notification_close` bit(1) DEFAULT b'0'
) ENGINE=InnoDB AVG_ROW_LENGTH=2730 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `notifications_tpl`
--

INSERT INTO `notifications_tpl` (`notification_tpl_id`, `event_code`, `notification_code`, `notification_text`, `notification_action`, `notification_close`) VALUES
(1, 31, 1, 'Нажаль, людина, яка Вас запросила, відмовила Вам у долученні до спільноти!', NULL, b'1'),
(2, 32, 3, 'Людина, яка відповіла на Ваше запрошення, відмовилась від ідентифікації. Ви можете сформувати новий лінк і запросити іншу людину!', NULL, b'1'),
(3, 33, 0, 'Ви від\'єднались від спільноти \'[net_name]\'! При необхідності долучитись до цієї спільноти знову - Вам необхідно отримати нове запрошення від її учасника!', NULL, b'1'),
(4, 34, 1, 'Нажаль, людина, яка Вас запросила, від\'єдналась від спільноти. Для долучитись до цієї спільноти Вам необхідно отримати інше запрошення від її учасника!', NULL, b'1'),
(5, 33, 3, 'Учасник вашого дерева [name] від\'єднався від спільноти. Якщо його дерево пусте - Ви можете запросити в спільноту іншу людину!', NULL, b'1'),
(6, 33, 4, 'Учасник вашого кола [name] від\'єднався від спільноти.', NULL, b'1'),
(7, 33, 5, 'Ваш координатор [name] від\'єднався від спільноти. Вам необхідно вибрати нового координатора серед учасників Вашого кола!', NULL, b'0'),
(8, 11, 3, 'Людина, яку Ви запросили, відповіла на Ваше запрошення. Якщо Ви впевнені, що це саме та людина, яку Ви запрошували - ідентифікуйте її!', NULL, b'0'),
(9, 11, 6, 'Людина, від якої Ви отримали запрошення, має Вас ідентифікувати. Як тільки це відбудеться - Ви зможете використовувати повний функціонал спільноти!', NULL, b'0'),
(10, 12, 4, 'В вашому колі новий учасник [name]. Він запрошений координатором!', NULL, b'1'),
(11, 12, 6, 'Ви долучились до спільноти [net_name]. Людина, яка Вас запросила, виконує функції координатора!', NULL, b'1'),
(12, 12, 0, 'Ідентифікація пройшла успішно!', NULL, b'1'),
(22, 31, 0, 'Ідентифікацію успішно скасовано!', NULL, b'1'),
(30, 32, 0, 'Ви успішно відмовились від долучення до спільноти!', NULL, b'1'),
(32, 40, 3, 'У вашому дереві новий учасник [\'name\']. Він підключений внаслідок реорганізації спільноти!', NULL, b'1'),
(33, 40, 4, 'У вашому колі новий учасник [\'name\']. Він підключений внаслідок реорганізації спільноти!', NULL, b'1'),
(34, 40, 6, 'Вас підключено до іншого кола внаслідок реорганізації спільноти! Ваш новий координатор - [\'name\'].', NULL, b'1'),
(35, 35, 0, 'Запрошення успішно скасовано!', NULL, b'1'),
(36, 13, 0, 'Запрошення успішно сформовано!', NULL, b'1'),
(37, 36, 1, 'Вас від\'єднано від спільноти за рішенням учасників вашого кола!', NULL, b'1'),
(38, 36, 3, 'Учасника [name] від\'єднано від спільноти за рішенням учасників вашого дерева!', NULL, b'1'),
(39, 36, 4, 'Учасника [name] від\'єднано від спільноти за рішенням вашого кола!', NULL, b'1'),
(40, 36, 5, 'Вашого координатора [name] від\'єднано від спільноти за рішенням учасників його кола!', NULL, b'1'),
(41, 14, 2, 'Вас обрано координатором!', NULL, b'1'),
(42, 14, 3, 'У вашому дереві новий учасник [\'name\']. Його обрано координатором.', NULL, b'1'),
(43, 14, 4, 'У вашому колі новий учасник [\'name\']. Його обрано координатором.', NULL, b'1'),
(44, 14, 5, 'Учасника вашого кола [\'name\'] обрано координатором.', NULL, b'1'),
(45, 14, 6, 'Ви в новому колі. [\'name\'] - ваш координатор.', NULL, b'1'),
(46, 15, 5, '[\'name\'] - ваш новий координатор. Його переобрано в його колі.', NULL, b'1'),
(47, 15, 6, 'Учасника вашого дерева [\'name\'] обрано координатором. Ви зараз в його колишній комірці.', NULL, b'1'),
(48, 15, 2, 'Вас переобрано. Тепер Ви відповідальні за нове дерево.', NULL, b'1'),
(49, 15, 4, 'Ваш колишній координатор [\'name\'] тепер в цій комірці.', NULL, b'1'),
(50, 38, 5, 'Вашого координатора [\'name\'] обрано координатором в його колі.', NULL, b'1'),
(51, 39, 3, 'Вашого учасника [\'name\'] переобрано координатором в його дереві.', NULL, b'1'),
(52, 39, 4, 'Учасника вашого кола [\'name\'] переобрано координатором в його дереві.', NULL, b'1'),
(53, 37, 1, 'Вас від\'єднано від спільноти за рішенням учасників вашого дерева!', NULL, b'1'),
(54, 37, 3, 'Учасника [name] від\'єднано від спільноти за рішенням учасників його дерева!', NULL, b'1'),
(55, 37, 4, 'Учасника [name] від\'єднано від спільноти за рішенням його дерева!', NULL, b'1'),
(56, 37, 5, 'Вашого координатора [name] від\'єднано від спільноти за рішенням учасників вашого кола!', NULL, b'1'),
(57, 16, 0, 'Спільноту успішно створено!', NULL, b'1');

-- --------------------------------------------------------

--
-- Структура таблиці `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `invite` varchar(255) DEFAULT NULL,
  `restore` varchar(255) DEFAULT NULL,
  `net_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=5461 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `users`
--

INSERT INTO `users` (`user_id`, `email`, `name`, `mobile`, `password`, `link`, `invite`, `restore`, `net_name`) VALUES
(1, 'email', 'Міша', '', 'pass', '', '', NULL, NULL),
(16, 'email_1', 'Гарний Хлопець', '', 'pass', '', NULL, NULL, NULL),
(24, 'email_2', 'Міша Васківнюк', '', 'pass', '', NULL, NULL, NULL),
(27, 'email_3', 'Міша 3', '', 'pass', '', '', NULL, NULL),
(36, 'maksym.khlivnenko@gmail.com', 'Максим', '0978886188ааа', 'Maksym', '', '', NULL, NULL),
(42, 'm.vaskivnyuk@ae.vn.ua', 'mike mike', '050-446-40-89-00', 'pass', '', '', '', NULL),
(43, 'kerlaeda82@gmail.com', 'Aleks', '0971600126', 'sanja1982', '', '', NULL, NULL),
(44, 'a_apostoliuk@ukr.net', 'Andrii Apostoliuk', '+380503881554', '87654321ha', '', '', NULL, NULL),
(46, 'slovo2005@ukr.net', 'Kuzma', '', 'qwertyuiop', '', '', NULL, NULL),
(47, 'm@khlivnenko.com', 'Test', '', '12345678', '', '', NULL, NULL),
(48, 'tor_1990@mail.ru', 'Костя', '', 'lthgfhjkm', '', '', NULL, NULL),
(51, 'email_4', 'МІША 4', '', 'pass', '', NULL, NULL, NULL),
(52, 'fotolibre@ukr.net', 'Just do it!', '', '87654321ha', NULL, NULL, NULL, NULL),
(53, 'vognesmih@bigmir.net', 'VikToR', '', 'jn6dd512b', '', '', NULL, NULL),
(54, 'brigadiroleg77@gmail.com', 'Олег', '', '15sent1977', '', NULL, NULL, NULL),
(56, 'konvert22300@gmail.com', 'Олександр', '', 'qazwsx', '', NULL, NULL, NULL),
(57, 'viktor163@ukr.net', 'Віктор', '', 'sourip1532', '', NULL, NULL, NULL),
(63, 'm.vaskivnyuk@gmail.com', 'Міша gmail', '', 'pass', NULL, NULL, NULL, NULL),
(64, 'olexandr.kots@gmail.com', 'Oleksandr', '0971600126', 'sovsek82', NULL, NULL, NULL, NULL),
(65, 'alex@grey.kiev.ua', 'Grey', '0633445966', 'gfhjkm', NULL, NULL, NULL, NULL),
(66, 'ju.klymenko@donnu.edu.ua', 'Юрій', '0975390892', 'YuNiK9591', NULL, NULL, NULL, NULL),
(67, 'vsenchuk@ukr.net', 'Вадим', '0673827141', 'dctyxer', NULL, NULL, NULL, NULL),
(68, 'djamal_69@ukr.net', 'Юрій', '0965587787', 'vfcfhfri1122', NULL, NULL, '59c7fe6e160bf', NULL),
(70, 'Kyforuk.a@mail.ru', 'Олексій', '', '', NULL, NULL, NULL, NULL),
(71, 'petr.fedchenko@gmail.com', 'Петро Федченко', '', '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `users_notifications`
--

DROP TABLE IF EXISTS `users_notifications`;
CREATE TABLE `users_notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `code` int(4) DEFAULT NULL,
  `notification` varchar(511) DEFAULT NULL,
  `new` bit(1) DEFAULT b'1',
  `shown` bit(1) DEFAULT b'0',
  `close` bit(1) DEFAULT b'0'
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `members_users`
--
ALTER TABLE `members_users`
  ADD UNIQUE KEY `uniq_net_user_member` (`net_id`,`user_id`,`member_id`);

--
-- Індекси таблиці `nets`
--
ALTER TABLE `nets`
  ADD PRIMARY KEY (`net_id`);

--
-- Індекси таблиці `nets_data`
--
ALTER TABLE `nets_data`
  ADD PRIMARY KEY (`net_id`),
  ADD UNIQUE KEY `uniq_net` (`net_id`);

--
-- Індекси таблиці `nets_events`
--
ALTER TABLE `nets_events`
  ADD PRIMARY KEY (`event_id`);

--
-- Індекси таблиці `nets_users_data`
--
ALTER TABLE `nets_users_data`
  ADD UNIQUE KEY `uniq_net_user` (`net_id`,`user_id`);

--
-- Індекси таблиці `nodes`
--
ALTER TABLE `nodes`
  ADD PRIMARY KEY (`node_id`);

--
-- Індекси таблиці `nodes_tmp`
--
ALTER TABLE `nodes_tmp`
  ADD UNIQUE KEY `node_id` (`node_id`);

--
-- Індекси таблиці `nodes_users`
--
ALTER TABLE `nodes_users`
  ADD PRIMARY KEY (`node_id`);

--
-- Індекси таблиці `notifications_tpl`
--
ALTER TABLE `notifications_tpl`
  ADD PRIMARY KEY (`notification_tpl_id`);

--
-- Індекси таблиці `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Індекси таблиці `users_notifications`
--
ALTER TABLE `users_notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `nets_events`
--
ALTER TABLE `nets_events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблиці `nodes`
--
ALTER TABLE `nodes`
  MODIFY `node_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=244;
--
-- AUTO_INCREMENT для таблиці `notifications_tpl`
--
ALTER TABLE `notifications_tpl`
  MODIFY `notification_tpl_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
--
-- AUTO_INCREMENT для таблиці `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;
--
-- AUTO_INCREMENT для таблиці `users_notifications`
--
ALTER TABLE `users_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
