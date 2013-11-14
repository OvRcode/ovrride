CREATE TABLE IF NOT EXISTS `ovr_lists_manual_orders` (
  `ID` varchar(32) NOT NULL,
  `First` varchar(32) NOT NULL,
  `Last` varchar(32) NOT NULL,
  `Pickup` varchar(32) NOT NULL,
  `Phone` varchar(32) NOT NULL,
  `Package` varchar(32) NOT NULL,
  `Trip` int(11) NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;