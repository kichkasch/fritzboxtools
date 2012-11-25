-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `calllog`
--

CREATE TABLE IF NOT EXISTS `calllog` (
  `typ` int(11) DEFAULT NULL,
  `Datum` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Name` varchar(50) DEFAULT NULL,
  `Rufnummer` varchar(50) NOT NULL DEFAULT '',
  `EigeneRufnummer` varchar(50) DEFAULT NULL,
  `DauerMinuten` int(11) DEFAULT NULL,
  PRIMARY KEY (`Datum`,`Rufnummer`),
  KEY `typ` (`typ`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `calltyp`
--

CREATE TABLE IF NOT EXISTS `calltyp` (
  `typid` int(11) DEFAULT NULL,
  `typname` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `calltyp`
--

INSERT INTO `calltyp` (`typid`, `typname`) VALUES
(1, 'incoming call'),
(2, 'missed call'),
(3, 'outgoing call');
