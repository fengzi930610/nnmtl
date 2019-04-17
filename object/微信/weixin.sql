# Host: localhost  (Version: 5.5.53)
# Date: 2018-08-13 12:49:57
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "weixin_event"
#

DROP TABLE IF EXISTS `weixin_event`;
CREATE TABLE `weixin_event` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `openId` varchar(50) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Data for table "weixin_event"
#

/*!40000 ALTER TABLE `weixin_event` DISABLE KEYS */;
/*!40000 ALTER TABLE `weixin_event` ENABLE KEYS */;

#
# Structure for table "weixin_log"
#

DROP TABLE IF EXISTS `weixin_log`;
CREATE TABLE `weixin_log` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `errcode` int(11) NOT NULL DEFAULT '0',
  `errmsg` varchar(100) NOT NULL DEFAULT '',
  `cretatime` varchar(11) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "weixin_log"
#


#
# Structure for table "weixin_token"
#

DROP TABLE IF EXISTS `weixin_token`;
CREATE TABLE `weixin_token` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `access_token` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "weixin_token"
#


#
# Structure for table "weixin_xml"
#

DROP TABLE IF EXISTS `weixin_xml`;
CREATE TABLE `weixin_xml` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `xml` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "weixin_xml"
#

