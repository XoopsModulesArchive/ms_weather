# phpMyAdmin SQL Dump
# version 2.5.3
# http://www.phpmyadmin.net
#
# Serveur: localhost
# Généré le : Vendredi 31 Octobre 2003 à 19:10
# Version du serveur: 4.0.15
# Version de PHP: 4.3.3
# 
# Base de données: `xoops`
# 

# --------------------------------------------------------

#
# Structure de la table `msweather`
#

CREATE TABLE `msweather` (
    `id`    INT(11)             NOT NULL AUTO_INCREMENT,
    `type`  TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
    `mswv1` VARCHAR(100)        NOT NULL DEFAULT '',
    `mswv2` VARCHAR(10)         NOT NULL DEFAULT '',
    `mswv3` VARCHAR(10)         NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
)
    ENGINE = ISAM
    AUTO_INCREMENT = 40;

#
# Contenu de la table `msweather`
#

INSERT INTO `msweather` (`id`, `type`, `mswv1`, `mswv2`, `mswv3`)
VALUES (1, 0, 'Maty Scripts', 'MS-Weather', 'v3.0'),
       (2, 1, '0', '1', '1'),
       (3, 2, '1', '1', '1'),
       (4, 3, 'Paris', 'FRXX0076', ''),
       (5, 4, 'Amsterdam', 'NLXX0002', ''),
       (6, 4, 'Atlanta', 'USGA0028', ''),
       (7, 4, 'Bangkok', 'THXX0002', ''),
       (8, 4, 'Barcelona', 'SPXX0015', ''),
       (9, 4, 'Beijing', 'CHXX0008', ''),
       (10, 4, 'Cairo', 'EGXX0004', ''),
       (11, 4, 'Copenhagen', 'DAXX0009', ''),
       (12, 4, 'Dallas', 'USTX0327', ''),
       (14, 4, 'Dortmund', 'GMXX0024', ''),
       (15, 4, 'Eindhoven', 'NLXX0007', ''),
       (16, 4, 'Groningen', 'NLXX0009', ''),
       (17, 4, 'Hong Kong', 'CHXX0049', ''),
       (18, 4, 'Istanbul', 'TUXX0014', ''),
       (19, 4, 'Jakarta', 'IDXX0022', ''),
       (20, 4, 'Johannesburg', 'SFXX0023', ''),
       (21, 4, 'Kuala Lumpur', 'MYXX0008', ''),
       (22, 4, 'London', 'UKXX0085', ''),
       (23, 4, 'Los Angeles', 'USCA0638', ''),
       (24, 4, 'Maastricht', 'NLXX0014', ''),
       (25, 4, 'Madrid', 'SPXX0050', ''),
       (26, 4, 'Montréal', 'CAXX0301', ''),
       (27, 4, 'Moscow', 'RSXX0063', ''),
       (28, 4, 'Munich', 'GMXX0087', ''),
       (29, 4, 'New York', 'USNY0996', ''),
       (30, 4, 'Oslo', 'NOXX0029', ''),
       (31, 4, 'Sydney', 'ASXX0112', ''),
       (32, 4, 'Taipei', 'TWXX0021', ''),
       (33, 4, 'Tel Aviv', 'ISXX0026', ''),
       (34, 4, 'Tokyo', 'JAXX0085', ''),
       (35, 4, 'Paris', 'FRXX0076', ''),
       (36, 4, 'Singapore', 'SNXX0006', ''),
       (37, 4, 'Stockholm', 'SWXX0031', ''),
       (38, 4, 'Zurich', 'SZXX0033', ''),
       (39, 4, 'Bruxelles', 'BEXX0005', '');
