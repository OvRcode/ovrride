CREATE TABLE `ovr_lists_manual_orders` (
  `ID` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `First` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `Last` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `Pickup` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NO PICKUP',
  `Transit To Rockaway` text COLLATE utf8_unicode_ci,
  `Transit From Rockaway` text COLLATE utf8_unicode_ci,
  `Phone` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `Package` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `Trip` int(11) NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;