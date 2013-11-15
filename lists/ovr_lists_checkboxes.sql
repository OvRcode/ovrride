CREATE TABLE IF NOT EXISTS `ovr_lists_checkboxes` (
  `ID` varchar(32) NOT NULL,
  `AM` tinyint(1) DEFAULT NULL,
  `PM` tinyint(1) DEFAULT NULL,
  `Waiver` tinyint(1) DEFAULT NULL,
  `Product` tinyint(1) DEFAULT NULL,
  `Bus` tinyint(1) DEFAULT NULL,
  `All_Area` tinyint(1) DEFAULT NULL,
  `Beg` tinyint(1) DEFAULT NULL,
  `BRD` tinyint(1) DEFAULT NULL,
  `SKI` tinyint(1) DEFAULT NULL,
  `LTS` tinyint(1) DEFAULT NULL,
  `LTR` tinyint(1) DEFAULT NULL,
  `Prog_Lesson` tinyint(1) DEFAULT NULL,
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;