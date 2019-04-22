-- MySQL dump 10.13  Distrib 5.5.50, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: jhwm
-- ------------------------------------------------------
-- Server version	5.5.50-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `jh_activity`
--

DROP TABLE IF EXISTS `jh_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_activity` (
  `active_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `banner1` varchar(150) DEFAULT '',
  `banner2` varchar(150) DEFAULT '',
  `banner3` varchar(150) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT '',
  `title` varchar(100) DEFAULT '',
  `intro` varchar(255) DEFAULT '',
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `cate_id` int(10) DEFAULT '0',
  PRIMARY KEY (`active_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_activity`
--

LOCK TABLES `jh_activity` WRITE;
/*!40000 ALTER TABLE `jh_activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_activity_items`
--

DROP TABLE IF EXISTS `jh_activity_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_activity_items` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active_id` int(10) DEFAULT '0',
  `can_id` int(10) DEFAULT '0',
  `type` int(10) DEFAULT '0',
  `order_by` int(10) DEFAULT '50',
  `dateline` int(10) DEFAULT '0',
  `title` varchar(100) DEFAULT '',
  `photo` varchar(100) DEFAULT '',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_activity_items`
--

LOCK TABLES `jh_activity_items` WRITE;
/*!40000 ALTER TABLE `jh_activity_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_activity_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_admin`
--

DROP TABLE IF EXISTS `jh_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_admin` (
  `admin_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(15) DEFAULT '',
  `passwd` char(32) DEFAULT '',
  `role_id` smallint(6) DEFAULT '0',
  `last_login` int(10) DEFAULT '0',
  `last_ip` varchar(15) DEFAULT '0.0.0.0',
  `closed` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_admin`
--

LOCK TABLES `jh_admin` WRITE;
/*!40000 ALTER TABLE `jh_admin` DISABLE KEYS */;
INSERT INTO `jh_admin` VALUES (1,'admin','241138d948f213ba25d0c9f0d0980eb1',1,1544089385,'113.16.65.147',0,1542875812),(2,'qinghong','982b524cd644724fe8216a5c6b0f46e3',1,1543808154,'222.252.37.109',0,1543306411),(3,'lixunhua','13df0a3cb813a96d7f229fa01af6cd82',1,0,'0.0.0.0',0,1543490760);
/*!40000 ALTER TABLE `jh_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_admin_role`
--

DROP TABLE IF EXISTS `jh_admin_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_admin_role` (
  `role_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(20) DEFAULT '',
  `role` enum('editor','admin','system','fenzhan') DEFAULT NULL,
  `priv` mediumtext,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_admin_role`
--

LOCK TABLES `jh_admin_role` WRITE;
/*!40000 ALTER TABLE `jh_admin_role` DISABLE KEYS */;
INSERT INTO `jh_admin_role` VALUES (1,'系统管理员','system',''),(2,'客服','editor',NULL);
/*!40000 ALTER TABLE `jh_admin_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_adv`
--

DROP TABLE IF EXISTS `jh_adv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_adv` (
  `adv_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `theme` varchar(20) DEFAULT 'default',
  `theme_id` smallint(6) DEFAULT '0',
  `page` varchar(50) DEFAULT '',
  `title` varchar(50) DEFAULT '',
  `from` enum('text','photo','product','script','lunzhuan') DEFAULT 'photo',
  `limit` smallint(6) DEFAULT '10',
  `config` mediumtext,
  `desc` varchar(255) DEFAULT '',
  `tmpl` mediumtext,
  `orderby` smallint(6) unsigned DEFAULT '0',
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) unsigned DEFAULT '0',
  `dateline` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`adv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_adv`
--

LOCK TABLES `jh_adv` WRITE;
/*!40000 ALTER TABLE `jh_adv` DISABLE KEYS */;
INSERT INTO `jh_adv` VALUES (22,'default',1,'','V3外卖首页轮播','photo',10,'a:2:{s:5:\"width\";s:4:\"100%\";s:6:\"height\";s:3:\"200\";}','','[loop]\r\n<div class=\"swiper-slide\"><a href=\"<{$item.link}>\" style=\"background:url(<{$pager.img}>/<{$item.thumb}>) no-repeat center; background-size:cover;\"></a></div>\r\n[/loop]',50,1,0,1482285223),(23,'default',1,'','V3外卖首页推荐','photo',10,'a:2:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}','',NULL,50,1,0,1482285460),(26,'default',1,'','启动页广告','text',10,'a:2:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}','',NULL,50,1,1,1487143769),(27,'default',1,'','V3启动页广告','photo',10,'a:2:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}','',NULL,50,1,0,1487143838),(31,'default',1,'','v3外卖首页分类','photo',10,'a:2:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}','',NULL,50,1,0,1497842545),(32,'default',1,'','积分商城首页推荐位','photo',4,'a:2:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}','',NULL,50,1,0,1499305342),(33,'default',1,'','积分商城首页轮播','photo',10,'a:2:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}','',NULL,50,1,0,1500084444),(34,'default',1,'','跑腿首页轮播','photo',10,'a:2:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}','',NULL,50,1,0,1505119464);
/*!40000 ALTER TABLE `jh_adv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_adv_item`
--

DROP TABLE IF EXISTS `jh_adv_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_adv_item` (
  `item_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `adv_id` smallint(6) unsigned DEFAULT '0',
  `city_id` smallint(6) DEFAULT '0',
  `title` varchar(100) DEFAULT '',
  `link` varchar(150) DEFAULT '',
  `thumb` varchar(150) DEFAULT '',
  `script` mediumtext,
  `clicks` mediumint(8) unsigned DEFAULT '0',
  `stime` int(10) NOT NULL DEFAULT '0',
  `ltime` int(10) NOT NULL DEFAULT '0',
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) unsigned DEFAULT '0',
  `dateline` int(10) unsigned DEFAULT '0',
  `desc` varchar(255) DEFAULT '',
  `target` enum('_self','_blank','_parent','_top') DEFAULT '_blank',
  `orderby` smallint(6) unsigned DEFAULT '50',
  `wxapp_link` varchar(150) DEFAULT '',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_adv_item`
--

LOCK TABLES `jh_adv_item` WRITE;
/*!40000 ALTER TABLE `jh_adv_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_adv_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_adv_themes`
--

DROP TABLE IF EXISTS `jh_adv_themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_adv_themes` (
  `theme_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT '',
  `photo` varchar(150) DEFAULT '',
  `config` mediumtext,
  `default` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_adv_themes`
--

LOCK TABLES `jh_adv_themes` WRITE;
/*!40000 ALTER TABLE `jh_adv_themes` DISABLE KEYS */;
INSERT INTO `jh_adv_themes` VALUES (11,'祖国华诞','photo/201809/20180908_DBAEED20C33ACB6D35CB8DB5645FFD4E.png','a:11:{s:7:\"module0\";a:3:{s:16:\"background_color\";s:0:\"\";s:10:\"background\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201810/20181009_7F36157DEABE9260E4DD96B2D31C401F.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module1\";a:7:{s:5:\"class\";s:1:\"1\";s:9:\"searchBox\";a:3:{s:4:\"open\";s:1:\"0\";s:5:\"color\";s:6:\"FFFFFF\";s:8:\"keywords\";a:3:{i:0;s:9:\"脆皮鸡\";i:1;s:9:\"排骨饭\";i:2;s:6:\"寿司\";}}s:16:\"background_color\";s:0:\"\";s:5:\"color\";s:6:\"FFFFFF\";s:13:\"icon_location\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/icon_location_white@2x.png\";s:9:\"icon_down\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/to_btn_arrowd_white@2x.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module2\";a:6:{s:16:\"background_color\";s:0:\"\";s:5:\"class\";s:1:\"2\";s:7:\"content\";a:1:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act_ban.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:4:\"open\";s:1:\"1\";}s:7:\"module3\";a:4:{s:5:\"class\";s:1:\"1\";s:10:\"background\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_FD49692846D0A2F0C1999D9B4B4AB204.png\";s:7:\"content\";a:10:{i:0;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_C9E704CE2AE482CEF8D716B2401A867D.png\";s:5:\"title\";s:9:\"品牌馆\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";}i:1;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_7755DA2D05AB4B577CD10299FDC577F8.png\";s:5:\"title\";s:12:\"餐饮美食\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-3.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=3\";}i:2;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_678C79684BC8EEDC1FE411EE430FA6D9.png\";s:5:\"title\";s:12:\"水果牛奶\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-4.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=4\";}i:3;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_4EC79B2EE1736FCA1B9CD32153ADA1EC.png\";s:5:\"title\";s:6:\"鲜花\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-5.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=5\";}i:4;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_CC42EB405001E8DF1414CC99CCCBEDB3.png\";s:5:\"title\";s:9:\"面调控\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-6.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=6\";}i:5;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_6B0129C942B96FDFCA1099609E793395.png\";s:5:\"title\";s:6:\"小吃\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-7.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=7\";}i:6;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_630FA56828B6D42145702723A3E60758.png\";s:5:\"title\";s:12:\"蛋糕糕点\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-8.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=8\";}i:7;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_988D2844AEFCEB22D1F1DD756401D825.png\";s:5:\"title\";s:6:\"小吃\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-7.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=7\";}i:8;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_7C4C7BCAD8A6736D3D59312A566AB644.png\";s:5:\"title\";s:6:\"抢购\";s:4:\"link\";s:29:\"http://wmdemo.jhcms.cn/qiang/\";s:6:\"wxlink\";s:0:\"\";}i:9;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_CF05783F8640F65E19978E0608992297.png\";s:5:\"title\";s:6:\"跑腿\";s:4:\"link\";s:30:\"http://wmdemo.jhcms.cn/paotui/\";s:6:\"wxlink\";s:0:\"\";}}s:4:\"open\";s:1:\"1\";}s:7:\"module4\";a:1:{s:4:\"open\";s:1:\"1\";}s:7:\"module8\";a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_FE065F74BE0F97593D6786919BDDE9E8.png\";s:4:\"link\";s:52:\"http://wmdemo.jhcms.cn/waimai/huodong/detail-13.html\";s:6:\"wxlink\";s:32:\"../huodong/huodong?huodong_id=13\";s:4:\"open\";s:1:\"1\";}s:7:\"module5\";a:6:{s:5:\"class\";s:1:\"1\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:4:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_5ECD1ED4E1AD676B0A995CAC6613CE78.png\";s:4:\"link\";s:52:\"http://wmdemo.jhcms.cn/waimai/huodong/detail-13.html\";s:6:\"wxlink\";s:32:\"../huodong/huodong?huodong_id=13\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_18C0916CE2658D352F67DFE1CCA31B06.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/huodong/detail-8.html\";s:6:\"wxlink\";s:31:\"../huodong/huodong?huodong_id=8\";}i:2;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_E8E9C3E46DC1EF0A1D69FFAFBB4D224E.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:3;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_F16F53AC31EF14802D3F4F8BC767E0FD.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-858.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=858\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_17.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module6\";a:7:{s:4:\"open\";s:1:\"0\";s:5:\"class\";i:1;s:5:\"title\";s:15:\"优惠专区二\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module7\";a:6:{s:5:\"class\";s:1:\"2\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:7:\"content\";a:2:{i:1;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:4:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180903_E0B9DE1F142AFE5B77966778C1C4EEA1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180903_67218B7682957ABC3272FD9D8A1466F7.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180903_68B5EB6C47CC39A264C6AC274F661E7A.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180903_0019BB2CDAE4A08FE5F8C717A1787F41.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module9\";a:2:{s:4:\"open\";s:1:\"1\";s:12:\"show_huodong\";s:1:\"1\";}s:8:\"module10\";a:4:{s:15:\"color_nochecked\";s:6:\"999999\";s:13:\"color_checked\";s:6:\"333333\";s:7:\"content\";a:4:{i:0;a:5:{s:5:\"title\";s:6:\"首页\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_BA92F01BB8E9C562C7332E34143C624E.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_4C373C93D0E4DC237AE839C0088ED5F6.png\";}i:1;a:5:{s:5:\"title\";s:6:\"跑腿\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:30:\"http://wmdemo.jhcms.cn/paotui/\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_85645742287FC876C2350ECA7EDDA3E5.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_1C78E22B36763F37FA6571B9D290EC0F.png\";}i:2;a:5:{s:5:\"title\";s:6:\"订单\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_34073563F35BE00185292C27F8503DE3.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_38F003459EAA15A8218DA912E12D0E44.png\";}i:3;a:5:{s:5:\"title\";s:6:\"我的\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_CF9F962E6A79E095A479AD1EFCDBDCB8.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_4C6692AC1147D6197A1387C642985FE7.png\";}}s:4:\"open\";s:1:\"1\";}}',0,1535945372),(12,'默认风格','photo/201809/20180904_948E196703D09A57264B0A2A5BD38DDF.png','a:11:{s:7:\"module0\";a:1:{s:4:\"open\";s:1:\"1\";}s:7:\"module1\";a:8:{s:5:\"class\";s:1:\"3\";s:9:\"searchBox\";a:3:{s:4:\"open\";s:1:\"1\";s:5:\"color\";s:6:\"666666\";s:8:\"keywords\";a:6:{i:0;s:9:\"脆皮鸡\";i:1;s:9:\"排骨饭\";i:2;s:6:\"寿司\";i:3;s:6:\"凉皮\";i:4;s:6:\"米线\";i:5;s:6:\"面条\";}}s:10:\"background\";a:4:{i:1;s:31:\"https://img01.jhcms.com/wmdemo/\";i:2;s:31:\"https://img01.jhcms.com/wmdemo/\";i:3;s:31:\"https://img01.jhcms.com/wmdemo/\";i:4;s:31:\"https://img01.jhcms.com/wmdemo/\";}s:16:\"background_color\";s:6:\"ffffff\";s:5:\"color\";s:6:\"666666\";s:13:\"icon_location\";s:76:\"https://img01.jhcms.com/wmdemo/default/icon/common/icon_location_gray@2x.png\";s:9:\"icon_down\";s:76:\"https://img01.jhcms.com/wmdemo/default/icon/common/to_btn_arrowd_gray@2x.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module2\";a:6:{s:10:\"background\";s:0:\"\";s:5:\"class\";s:1:\"1\";s:7:\"content\";a:2:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act_ban.png\";s:4:\"link\";s:52:\"http://wmdemo.jhcms.cn/waimai/huodong/detail-13.html\";s:6:\"wxlink\";s:32:\"../huodong/huodong?huodong_id=13\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_3D1F584CD6072464AC454466F5BD5E03.png\";s:4:\"link\";s:46:\"http://m.jhcms.com/zt/waimaiupgrade/index.html\";s:6:\"wxlink\";s:0:\"\";}}s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:4:\"open\";s:1:\"1\";}s:7:\"module3\";a:5:{s:5:\"class\";s:1:\"1\";s:5:\"color\";s:6:\"333333\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:10:{i:0;a:4:{s:5:\"photo\";s:58:\"https://img01.jhcms.com/wmdemo/default/image/img_cla_1.png\";s:5:\"title\";s:9:\"品牌馆\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";}i:1;a:4:{s:5:\"photo\";s:58:\"https://img01.jhcms.com/wmdemo/default/image/img_cla_2.png\";s:5:\"title\";s:6:\"美食\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-3.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=3\";}i:2;a:4:{s:5:\"photo\";s:58:\"https://img01.jhcms.com/wmdemo/default/image/img_cla_3.png\";s:5:\"title\";s:6:\"商超\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-4.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=4\";}i:3;a:4:{s:5:\"photo\";s:58:\"https://img01.jhcms.com/wmdemo/default/image/img_cla_4.png\";s:5:\"title\";s:6:\"鲜花\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-5.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=5\";}i:4;a:4:{s:5:\"photo\";s:58:\"https://img01.jhcms.com/wmdemo/default/image/img_cla_5.png\";s:5:\"title\";s:9:\"面条控\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-7.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=7\";}i:5;a:4:{s:5:\"photo\";s:58:\"https://img01.jhcms.com/wmdemo/default/image/img_cla_6.png\";s:5:\"title\";s:6:\"零食\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-7.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=7\";}i:6;a:4:{s:5:\"photo\";s:58:\"https://img01.jhcms.com/wmdemo/default/image/img_cla_7.png\";s:5:\"title\";s:6:\"蛋糕\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-8.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=8\";}i:7;a:4:{s:5:\"photo\";s:58:\"https://img01.jhcms.com/wmdemo/default/image/img_cla_8.png\";s:5:\"title\";s:6:\"果蔬\";s:4:\"link\";s:53:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-20.html\";s:6:\"wxlink\";s:30:\"../shoplist/shoplist?cateid=20\";}i:8;a:4:{s:5:\"photo\";s:58:\"https://img01.jhcms.com/wmdemo/default/image/img_cla_9.png\";s:5:\"title\";s:6:\"饮品\";s:4:\"link\";s:53:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-19.html\";s:6:\"wxlink\";s:30:\"../shoplist/shoplist?cateid=19\";}i:9;a:4:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_cla_10.png\";s:5:\"title\";s:6:\"更多\";s:4:\"link\";s:53:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-18.html\";s:6:\"wxlink\";s:30:\"../shoplist/shoplist?cateid=18\";}}s:4:\"open\";s:1:\"1\";}s:7:\"module4\";a:1:{s:4:\"open\";s:1:\"1\";}s:7:\"module5\";a:6:{s:5:\"class\";s:1:\"4\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180914_86B58EB8959B5C04F676402D87776D2A.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:4:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_32503DE53F7DCE076D92B32A6884B857.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_0CE65D2FAAD8A2BD73342B0B8A681285.png\";s:4:\"link\";s:52:\"http://wmdemo.jhcms.cn/waimai/huodong/detail-13.html\";s:6:\"wxlink\";s:32:\"../huodong/huodong?huodong_id=13\";}i:2;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_8494782699E81F45C4A55B151C6B74BB.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/huodong/detail-8.html\";s:6:\"wxlink\";s:31:\"../huodong/huodong?huodong_id=8\";}i:3;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_6AAC4C632618656D64F5F4B8F6F6B6E7.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-858.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=858\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_17.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module7\";a:6:{s:5:\"class\";s:1:\"2\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_AAAB294F1AB1C6392BF3AC2745E09962.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:7:\"content\";a:2:{i:1;a:6:{i:0;a:4:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_1.png\";s:5:\"title\";s:9:\"必胜客\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:4:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_2.png\";s:5:\"title\";s:12:\"吉祥馄饨\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-415.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=415\";}i:2;a:4:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_3.png\";s:5:\"title\";s:12:\"青年餐厅\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-860.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=860\";}i:3;a:4:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_4.png\";s:5:\"title\";s:9:\"老乡鸡\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-802.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=802\";}i:4;a:4:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_5.png\";s:5:\"title\";s:9:\"台资味\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-421.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=421\";}i:5;a:4:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_6.png\";s:5:\"title\";s:12:\"大脸鸡排\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-437.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=437\";}}i:2;a:4:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180911_26104E3DA28E733C1949D9BFDCBD66F1.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180911_C7A9904990001029B47C6CEE2D8C656F.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-415.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=415\";}i:2;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180911_DEBE0D7F9AFC336F67ED8DFF6BA6FEFC.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-860.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=860\";}i:3;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180911_CE88E11EA2A203717FA30B11D8547420.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-858.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=858\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module6\";a:7:{s:4:\"open\";s:1:\"1\";s:5:\"class\";i:1;s:5:\"title\";s:15:\"优惠专区二\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module8\";a:4:{s:5:\"photo\";s:57:\"https://img01.jhcms.com/wmdemo/default/image/img_act8.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/huodong/detail-8.html\";s:6:\"wxlink\";s:31:\"../huodong/huodong?huodong_id=8\";s:4:\"open\";s:1:\"1\";}s:7:\"module9\";a:2:{s:4:\"open\";s:1:\"1\";s:12:\"show_huodong\";s:1:\"1\";}s:8:\"module10\";a:4:{s:15:\"color_nochecked\";s:6:\"999999\";s:13:\"color_checked\";s:6:\"20AD20\";s:7:\"content\";a:4:{i:0;a:5:{s:5:\"title\";s:6:\"首页\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot1_2.png\";s:14:\"icon_nochecked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot1_1.png\";}i:1;a:5:{s:5:\"title\";s:6:\"跑腿\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:31:\"https://wmdemo.jhcms.cn/paotui/\";s:12:\"icon_checked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot2_2.png\";s:14:\"icon_nochecked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot2_1.png\";}i:2;a:5:{s:5:\"title\";s:6:\"订单\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot3_2.png\";s:14:\"icon_nochecked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot3_1.png\";}i:3;a:5:{s:5:\"title\";s:6:\"我的\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot4_2.png\";s:14:\"icon_nochecked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot4_1.png\";}}s:4:\"open\";s:1:\"1\";}}',0,1535958616),(13,'功夫熊猫','photo/201809/20180908_80AEEDB1F758B302D5C31E0E175CF20D.png','a:11:{s:7:\"module0\";a:3:{s:16:\"background_color\";s:0:\"\";s:10:\"background\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201810/20181009_203AC9B48C78C2B2A9037D1382EC4920.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module1\";a:8:{s:5:\"class\";s:1:\"2\";s:9:\"searchBox\";a:3:{s:4:\"open\";s:1:\"1\";s:5:\"color\";s:6:\"FFFFFF\";s:8:\"keywords\";a:6:{i:0;s:9:\"脆皮鸡\";i:1;s:9:\"排骨饭\";i:2;s:6:\"寿司\";i:3;s:6:\"凉皮\";i:4;s:9:\"皮皮虾\";i:5;s:3:\"面\";}}s:10:\"background\";a:4:{i:1;s:31:\"https://img01.jhcms.com/wmdemo/\";i:2;s:31:\"https://img01.jhcms.com/wmdemo/\";i:3;s:31:\"https://img01.jhcms.com/wmdemo/\";i:4;s:31:\"https://img01.jhcms.com/wmdemo/\";}s:16:\"background_color\";s:6:\"1f0e2e\";s:5:\"color\";s:6:\"FFFFFF\";s:13:\"icon_location\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/icon_location_white@2x.png\";s:9:\"icon_down\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/to_btn_arrowd_white@2x.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module2\";a:6:{s:10:\"background\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_911FFA0DEBB3D34021CEB079F41477BF.png\";s:5:\"class\";s:1:\"2\";s:7:\"content\";a:2:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_79B4F5AF7B0711D7C3B0DA405F47207B.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_B83F1E3C845BFDD5A8EA38F094C7E289.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-3.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=3\";}}s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";s:4:\"open\";s:1:\"1\";}s:7:\"module3\";a:5:{s:5:\"class\";s:1:\"2\";s:5:\"color\";s:6:\"333333\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:8:{i:0;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_BCE598C09CC092959182500F2E670A7D.png\";s:5:\"title\";s:9:\"品牌馆\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";}i:1;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_3E1492A2F43A7DDBB9D1649447382918.png\";s:5:\"title\";s:12:\"餐饮美食\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-3.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=3\";}i:2;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_9117BFE82B6F2D504808B8884B06C366.png\";s:5:\"title\";s:12:\"水果牛奶\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-4.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=4\";}i:3;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_2652C3F9264FA67B3014F71BDF0176B1.png\";s:5:\"title\";s:6:\"鲜花\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-5.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=5\";}i:4;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_3C4D470912031912EB142E71ADDBB03D.png\";s:5:\"title\";s:9:\"面条控\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-6.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=6\";}i:5;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_5258A63C627E11177D12D15FDD2068AB.png\";s:5:\"title\";s:6:\"小吃\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-7.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=7\";}i:6;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_EBB0B82F00ABA5C310E4E0C979193DED.png\";s:5:\"title\";s:12:\"蛋糕甜点\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-8.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=8\";}i:7;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_AC03FCA75CDF8AEA003B65971F35FCF0.png\";s:5:\"title\";s:6:\"跑腿\";s:4:\"link\";s:31:\"https://wmdemo.jhcms.cn/paotui/\";s:6:\"wxlink\";s:0:\"\";}}s:4:\"open\";s:1:\"1\";}s:7:\"module4\";a:1:{s:4:\"open\";s:1:\"1\";}s:7:\"module5\";a:6:{s:5:\"class\";s:1:\"4\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_F830E0BCEA6908271D2A801C517E59CA.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";s:7:\"content\";a:7:{i:1;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/huodong/detail-8.html\";s:6:\"wxlink\";s:31:\"../huodong/huodong?huodong_id=8\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";}i:3;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180903_840E49401B13C7E235DFE0D19CC80721.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-3.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=3\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_EA2DAADE5762A1851D26224C84F192E7.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_625B9C83A052498ADC3B2BE79B40A930.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_41EBD8E65BC70E020EFC1A225BC5E007.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_461E770370D3072FB23F0FC0FB91FD67.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:2;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_979A0277E6802128C65BB0A6FEF58BB0.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-415.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=415\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:2;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_79B0E315173E32B61FB4B16ED1A2A839.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:3;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_71CF5DB4EF1311DBF81BB00DFFB7712E.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_17.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module6\";a:6:{s:5:\"class\";s:1:\"2\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_A88A92FEDE54DF2E5E540800F6148AEA.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:0:\"\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module8\";a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_7E074CB7680FFAF1749D261068546591.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/huodong/detail-8.html\";s:6:\"wxlink\";s:31:\"../huodong/huodong?huodong_id=8\";s:4:\"open\";s:1:\"1\";}s:7:\"module7\";a:6:{s:5:\"class\";s:1:\"1\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_213FA6883E7207C9C98AAA8E45E49078.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/huodong/detail-8.html\";s:6:\"wxlink\";s:31:\"../huodong/huodong?huodong_id=8\";s:7:\"content\";a:2:{i:1;a:6:{i:0;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180905_1BE5FEFD0BD2C889421AD5926C1C0CC1.png\";s:5:\"title\";s:3:\"光\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180905_1B649A10C1A368A559E12262E3AAD960.png\";s:5:\"title\";s:3:\"棍\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:2;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180905_BC968672E1DEC71A07D91AFC744D62A6.png\";s:5:\"title\";s:3:\"节\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:3;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180905_B783ADDB0BCF18D92210C408495C0613.png\";s:5:\"title\";s:3:\"我\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:4;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_78E590DA88509E81150AB848F41E1767.png\";s:5:\"title\";s:3:\"骄\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:5;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180905_E449B13ED284DF92F34E737AFDC29396.png\";s:5:\"title\";s:3:\"傲\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}}i:2;a:4:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_9F940C163EF6DF71E02BF0D1AC4BAF98.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_91B8DC94083B0408CD5862425FC13793.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:2;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_F3A895CB778604DCFD35008702B51F3E.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:3;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180904_894F49D0BF2A853933E9554008672AE3.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module9\";a:2:{s:4:\"open\";s:1:\"1\";s:12:\"show_huodong\";s:1:\"0\";}s:8:\"module10\";a:4:{s:15:\"color_nochecked\";s:6:\"999999\";s:13:\"color_checked\";s:6:\"20ad20\";s:7:\"content\";a:4:{i:0;a:5:{s:5:\"title\";s:6:\"首页\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_AAC779C5C73F3D699D8B737264C18AA3.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_CBB45E67EA59AF8E63107CB46E3397EF.png\";}i:1;a:5:{s:5:\"title\";s:6:\"跑腿\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:31:\"https://wmdemo.jhcms.cn/paotui/\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_1BDE687CEF2947F6268E5DF6440C2911.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_4A5E89AFC1F67A98489FED0B589EE9A5.png\";}i:2;a:5:{s:5:\"title\";s:6:\"订单\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_B0A308BC2D43779D758FDDD39B841F97.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_250B490F45FEB8D0C5D3397F7EC769B4.png\";}i:3;a:5:{s:5:\"title\";s:6:\"我的\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_93244DDBFB644444E6FE1F959F349EA0.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_1EB0A0FC026C0792AEEB61A7D26313D5.png\";}}s:4:\"open\";s:1:\"1\";}}',0,1535961298),(22,'月圆中秋','photo/201809/20180908_BA3B5CDD48A77EB2FCE23004138F6A85.png','a:11:{s:7:\"module0\";a:3:{s:16:\"background_color\";s:0:\"\";s:10:\"background\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201810/20181009_96E695EB52F4A5E5AAFEB73DBB9C9EDD.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module1\";a:7:{s:5:\"class\";s:1:\"2\";s:9:\"searchBox\";a:3:{s:4:\"open\";s:1:\"1\";s:5:\"color\";s:6:\"FFFFFF\";s:8:\"keywords\";a:6:{i:0;s:9:\"脆皮鸡\";i:1;s:9:\"排骨饭\";i:2;s:6:\"寿司\";i:3;s:6:\"凉皮\";i:4;s:9:\"皮皮虾\";i:5;s:3:\"面\";}}s:16:\"background_color\";s:0:\"\";s:5:\"color\";s:6:\"FFFFFF\";s:13:\"icon_location\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/icon_location_white@2x.png\";s:9:\"icon_down\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/to_btn_arrowd_white@2x.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module2\";a:6:{s:16:\"background_color\";s:0:\"\";s:5:\"class\";s:1:\"2\";s:7:\"content\";a:1:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act_ban.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}s:4:\"link\";s:48:\"http://wmv4.weizx.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";s:4:\"open\";s:1:\"1\";}s:7:\"module3\";a:5:{s:5:\"class\";s:1:\"1\";s:5:\"color\";s:6:\"333333\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:10:{i:0;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_0A605D4FCB63AAE255B53B1CDF90745A.png\";s:5:\"title\";s:9:\"品牌馆\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";}i:1;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_43EB68CDD87CB5A1C4DD5755960DFEDD.png\";s:5:\"title\";s:6:\"美食\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-3.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=3\";}i:2;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_9B70D1C53EE91D2FDD2A060604BD5672.png\";s:5:\"title\";s:6:\"鲜花\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-5.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=5\";}i:3;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_309B661D63185E699BD8DCE1429979F1.png\";s:5:\"title\";s:9:\"面条控\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-6.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=6\";}i:4;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_12A843563002B4353595C0C790F0A255.png\";s:5:\"title\";s:6:\"零食\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-7.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=7\";}i:5;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_8B9A251C0169BE2148966D82BD7F9AB8.png\";s:5:\"title\";s:6:\"蛋糕\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-8.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=8\";}i:6;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_FB223EAD3E6146BA8251EB7FF61BBFC6.png\";s:5:\"title\";s:6:\"积分\";s:4:\"link\";s:29:\"http://wmdemo.jhcms.cn/jifen/\";s:6:\"wxlink\";s:0:\"\";}i:7;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_BE0AC2DA32C4C7DC1D4DB824144C700C.png\";s:5:\"title\";s:6:\"抢购\";s:4:\"link\";s:29:\"http://wmdemo.jhcms.cn/qiang/\";s:6:\"wxlink\";s:0:\"\";}i:8;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_722903CE9182FC7D45F72CF8B133B75B.png\";s:5:\"title\";s:6:\"更多\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:9;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_19F66E1A203D10758A24686936DCFDCB.png\";s:5:\"title\";s:6:\"月饼\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}}s:4:\"open\";s:1:\"1\";}s:7:\"module4\";a:1:{s:4:\"open\";s:1:\"1\";}s:7:\"module8\";a:5:{s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_441570B63F94151A5F19D754D8638AF7.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";s:4:\"open\";s:1:\"1\";}s:7:\"module5\";a:7:{s:5:\"class\";s:1:\"4\";s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_B6C1046148D009032B22FE493AA71925.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-419.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=419\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-415.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=415\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-857.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=857\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-858.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=858\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_17.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module7\";a:7:{s:5:\"class\";s:1:\"1\";s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_BEDFEF1715F89D455CEE26994B9AC63C.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";s:7:\"content\";a:2:{i:1;a:6:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180906_5C85A021858956A92952D838BA790FE3.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180906_84D87B0264158C947EB1027FE6C0673C.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:2;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180906_F679D58631E6C9DC307D24BB558858C8.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:3;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180906_FF69D4F915BC515C194DA1DB015A878D.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:4;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180906_35CEE4606FDBFAD883B1F50235786B4E.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:5;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180906_E22168EB1296480DD1C2EEED73ED22F6.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}}i:2;a:4:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act10_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act10_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act10_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act10_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module6\";a:7:{s:5:\"class\";s:1:\"2\";s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_1970563CA4BC9C3DC9C5C4BB9F1EF102.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-415.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=415\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:50:\"http://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:51:\"http://wmdemo.jhcms.cn/waimai/huodong/detail-8.html\";s:6:\"wxlink\";s:31:\"../huodong/huodong?huodong_id=8\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module9\";a:2:{s:4:\"open\";s:1:\"1\";s:12:\"show_huodong\";s:1:\"1\";}s:8:\"module10\";a:4:{s:15:\"color_nochecked\";s:6:\"e6a505\";s:13:\"color_checked\";s:6:\"55479f\";s:7:\"content\";a:4:{i:0;a:5:{s:5:\"title\";s:6:\"首页\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_CF79D72714E5315261D69E193BFF4A6A.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_F237B802FAB3401111BDB7801FC1D829.png\";}i:1;a:5:{s:5:\"title\";s:6:\"跑腿\";s:4:\"open\";s:1:\"0\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_E092011F286127BDDC71202B01FCA7E3.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_C38823CF829B3A8C3D493631563E2C81.png\";}i:2;a:5:{s:5:\"title\";s:6:\"订单\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_F48C731638E0FBAD24D3A5A5EF6A6163.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_758472D21F76667DA144C3B00E5E12C5.png\";}i:3;a:5:{s:5:\"title\";s:6:\"我的\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_85F049615C43C9B65FC141540A78468E.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180908_D487F204DA02CBB32D40111151B29967.png\";}}s:4:\"open\";s:1:\"1\";}}',0,1536410696),(30,'新年快乐','photo/201809/20180930_4CA385B78E1A9C39A5EF5B80D1184D17.png','a:11:{s:7:\"module0\";a:3:{s:16:\"background_color\";s:0:\"\";s:10:\"background\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_35B2273D214C0176466A094B07734801.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module1\";a:7:{s:5:\"class\";s:1:\"2\";s:9:\"searchBox\";a:3:{s:4:\"open\";s:1:\"0\";s:5:\"color\";s:6:\"FFFFFF\";s:8:\"keywords\";a:6:{i:0;s:9:\"脆皮鸡\";i:1;s:9:\"排骨饭\";i:2;s:6:\"寿司\";i:3;s:6:\"凉皮\";i:4;s:9:\"老乡鸡\";i:5;s:6:\"汉堡\";}}s:16:\"background_color\";s:0:\"\";s:5:\"color\";s:6:\"FFFFFF\";s:13:\"icon_location\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/icon_location_white@2x.png\";s:9:\"icon_down\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/to_btn_arrowd_white@2x.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module2\";a:6:{s:16:\"background_color\";s:0:\"\";s:5:\"class\";s:1:\"2\";s:7:\"content\";a:1:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act_ban.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:4:\"open\";s:1:\"1\";}s:7:\"module3\";a:5:{s:5:\"class\";s:1:\"1\";s:5:\"color\";s:6:\"333333\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:10:{i:0;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_CC788B055EC026941DF0A534C2C732C1.png\";s:5:\"title\";s:6:\"美食\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";}i:1;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_CC1F31746B0D53BE6CA5846EB79EE388.png\";s:5:\"title\";s:6:\"便利\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-3.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=3\";}i:2;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_09C3381A572CFFDEC9E61CE25CC442A8.png\";s:5:\"title\";s:6:\"水果\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-415.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=415\";}i:3;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_AAD18F670E760AB06B9A89B12272EF83.png\";s:5:\"title\";s:6:\"急送\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-5.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=5\";}i:4;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_E3E7C8D297DF8275D089A4A221470698.png\";s:5:\"title\";s:6:\"饮品\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-4.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=4\";}i:5;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_54BD1611CAF0334528E9EDD16C06BC84.png\";s:5:\"title\";s:6:\"跑腿\";s:4:\"link\";s:31:\"https://wmdemo.jhcms.cn/paotui/\";s:6:\"wxlink\";s:0:\"\";}i:6;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_B2834EE5A06F7DAD23BB44422B7ABA43.png\";s:5:\"title\";s:6:\"特惠\";s:4:\"link\";s:53:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-19.html\";s:6:\"wxlink\";s:30:\"../shoplist/shoplist?cateid=19\";}i:7;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_056E92F63F5D9B21ADD451F09C9A2A69.png\";s:5:\"title\";s:6:\"小吃\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-4.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=4\";}i:8;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_C3C0B662F5872FEA078250A90512B0CB.png\";s:5:\"title\";s:6:\"河蟹\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-6.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=6\";}i:9;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_9C3522B053FC774384F22F5E42CE1316.png\";s:5:\"title\";s:6:\"特产\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/huodong/detail-8.html\";s:6:\"wxlink\";s:31:\"../huodong/huodong?huodong_id=8\";}}s:4:\"open\";s:1:\"1\";}s:7:\"module4\";a:1:{s:4:\"open\";s:1:\"1\";}s:7:\"module8\";a:5:{s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_671C5DCA599CD3AFA7B56D6EE52FFE27.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:4:\"open\";s:1:\"1\";}s:7:\"module5\";a:7:{s:5:\"class\";s:1:\"1\";s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_622F5FD3E9518913B4CF2B9E3E67CA4E.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"0\";}s:7:\"module6\";a:8:{s:4:\"open\";s:1:\"0\";s:5:\"class\";i:1;s:5:\"title\";s:15:\"优惠专区二\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module7\";a:8:{s:4:\"open\";s:1:\"1\";s:5:\"class\";i:1;s:5:\"title\";s:12:\"大牌甄选\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:2:{i:1;a:6:{i:0;a:4:{s:5:\"title\";s:9:\"必胜客\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:4:{s:5:\"title\";s:12:\"吉祥混沌\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:4:{s:5:\"title\";s:12:\"青年餐厅\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:4:{s:5:\"title\";s:9:\"老乡鸡\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:5:{s:5:\"title\";s:9:\"台资味\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_5.png\";s:4:\"logo\";s:0:\"\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:4:{s:5:\"title\";s:12:\"大脸鸡排\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:4:{i:0;a:4:{s:5:\"title\";s:9:\"必胜客\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop01@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:4:{s:5:\"title\";s:12:\"吉祥混沌\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop02@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:4:{s:5:\"title\";s:12:\"青年餐厅\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop03@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:4:{s:5:\"title\";s:9:\"台资味\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop04@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module9\";a:2:{s:4:\"open\";s:1:\"1\";s:12:\"show_huodong\";s:1:\"1\";}s:8:\"module10\";a:4:{s:15:\"color_nochecked\";s:6:\"999999\";s:13:\"color_checked\";s:6:\"20AD20\";s:7:\"content\";a:4:{i:0;a:5:{s:5:\"title\";s:6:\"首页\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_532690FA7082C97D0D5D83D92432EA8F.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_33ABF96542EEB6616D7A64A3EF8CD81C.png\";}i:1;a:5:{s:5:\"title\";s:9:\"购物车\";s:4:\"open\";s:1:\"0\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot2_2.png\";s:14:\"icon_nochecked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot2_1.png\";}i:2;a:5:{s:5:\"title\";s:6:\"订单\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_156864CCAA6F22F7765E00CEF0AE12E1.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_95FBF56BBD2641D368CE1B5B9A5C8136.png\";}i:3;a:5:{s:5:\"title\";s:6:\"我的\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_B6B8637EAB0D55EDD15E0563BF6B6316.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_E1B1856A55C7530B55A7A9928D00C897.png\";}}s:4:\"open\";s:1:\"1\";}}',0,1538276608),(33,'百度外卖','photo/201809/20180930_D825B2244430802B04EB29B4BEE221ED.png','a:11:{s:7:\"module0\";a:3:{s:4:\"open\";s:1:\"1\";s:10:\"background\";s:0:\"\";s:16:\"background_color\";s:0:\"\";}s:7:\"module1\";a:8:{s:4:\"open\";s:1:\"1\";s:5:\"class\";i:1;s:10:\"background\";a:4:{i:1;s:31:\"https://img01.jhcms.com/wmdemo/\";i:2;s:31:\"https://img01.jhcms.com/wmdemo/\";i:3;s:31:\"https://img01.jhcms.com/wmdemo/\";i:4;s:31:\"https://img01.jhcms.com/wmdemo/\";}s:16:\"background_color\";s:0:\"\";s:5:\"color\";s:6:\"666666\";s:13:\"icon_location\";s:63:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_lat_1.png\";s:9:\"icon_down\";s:63:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_dow_1.png\";s:9:\"searchBox\";a:3:{s:4:\"open\";i:1;s:5:\"color\";s:6:\"666666\";s:8:\"keywords\";a:6:{i:0;s:9:\"脆皮鸡\";i:1;s:9:\"排骨饭\";i:2;s:6:\"寿司\";i:3;s:6:\"凉皮\";i:4;s:9:\"老乡鸡\";i:5;s:6:\"汉堡\";}}}s:7:\"module2\";a:7:{s:4:\"open\";s:1:\"0\";s:5:\"class\";i:1;s:10:\"background\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:7:\"content\";a:1:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act_ban.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:7:\"module3\";a:5:{s:5:\"class\";s:1:\"1\";s:5:\"color\";s:6:\"333333\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:10:{i:0;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_44B3C70E3E085E3C491C3121D287CF66.png\";s:5:\"title\";s:9:\"品牌馆\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_78AF5700317D9C5823C0F669AEA41F7A.png\";s:5:\"title\";s:6:\"美食\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_8B7460FD6BDD5866B9AFE1ED89B44B9F.png\";s:5:\"title\";s:6:\"商超\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_75AE9A4E9EF1E1D8D7AF573AC78B0E69.png\";s:5:\"title\";s:6:\"鲜花\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_0ACD995586F646AB7E8F7923B79AD987.png\";s:5:\"title\";s:6:\"面食\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_86D0177ECA72E9A1EF744C2A7F7910A9.png\";s:5:\"title\";s:6:\"零食\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:6;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_96307706488BE8333DC62827855160F9.png\";s:5:\"title\";s:6:\"蛋糕\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:7;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_F24CBFDDA39CC4DA8D867B4F3FCFA54A.png\";s:5:\"title\";s:6:\"果蔬\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:8;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_06C1EA836775CEF7623219E4DBBEE36C.png\";s:5:\"title\";s:6:\"饮品\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:9;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_FB7E8D183003789D2D94BAB106EAAD47.png\";s:5:\"title\";s:6:\"更多\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}s:4:\"open\";s:1:\"1\";}s:7:\"module4\";a:1:{s:4:\"open\";s:1:\"1\";}s:7:\"module8\";a:5:{s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_A16ADADF72C1493D3E0B33E509D53AFC.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:4:\"open\";s:1:\"1\";}s:7:\"module5\";a:7:{s:5:\"class\";s:1:\"4\";s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_81F37C9CDB5B8C95D5FA3C0EEB396217.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module6\";a:8:{s:4:\"open\";s:1:\"0\";s:5:\"class\";i:1;s:5:\"title\";s:15:\"优惠专区二\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module7\";a:8:{s:4:\"open\";s:1:\"0\";s:5:\"class\";i:1;s:5:\"title\";s:12:\"大牌甄选\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:2:{i:1;a:6:{i:0;a:4:{s:5:\"title\";s:9:\"必胜客\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:4:{s:5:\"title\";s:12:\"吉祥混沌\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:4:{s:5:\"title\";s:12:\"青年餐厅\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:4:{s:5:\"title\";s:9:\"老乡鸡\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:5:{s:5:\"title\";s:9:\"台资味\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_5.png\";s:4:\"logo\";s:0:\"\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:4:{s:5:\"title\";s:12:\"大脸鸡排\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:4:{i:0;a:4:{s:5:\"title\";s:9:\"必胜客\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop01@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:4:{s:5:\"title\";s:12:\"吉祥混沌\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop02@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:4:{s:5:\"title\";s:12:\"青年餐厅\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop03@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:4:{s:5:\"title\";s:9:\"台资味\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop04@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module9\";a:2:{s:4:\"open\";s:1:\"1\";s:12:\"show_huodong\";s:1:\"1\";}s:8:\"module10\";a:4:{s:15:\"color_nochecked\";s:6:\"999999\";s:13:\"color_checked\";s:6:\"20AD20\";s:7:\"content\";a:4:{i:0;a:5:{s:5:\"title\";s:6:\"首页\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_4FF1B767A914C2BB3F40DCF805C33052.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_3D6BDE297CCBF9C6220128746C227352.png\";}i:1;a:5:{s:5:\"title\";s:9:\"购物车\";s:4:\"open\";s:1:\"0\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_97AEE82EF7AA2A4DAD4AEDD3C98DE24C.png\";s:14:\"icon_nochecked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot2_1.png\";}i:2;a:5:{s:5:\"title\";s:6:\"订单\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot3_2.png\";s:14:\"icon_nochecked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot3_1.png\";}i:3;a:5:{s:5:\"title\";s:6:\"我的\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_7EFA506F367FE4F7C03D310D092DA473.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_6CBDC4447A7D180EAD45EE5E16C4E5EE.png\";}}s:4:\"open\";s:1:\"1\";}}',0,1538288418),(34,'高校专营','photo/201809/20180930_830ABB519BB9C4C6D60BD51F9B0CAC6A.png','a:11:{s:7:\"module0\";a:3:{s:16:\"background_color\";s:0:\"\";s:10:\"background\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_78D6D523137CA2A9B74BE4BA1173B91F.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module1\";a:7:{s:5:\"class\";s:1:\"2\";s:9:\"searchBox\";a:3:{s:4:\"open\";s:1:\"0\";s:5:\"color\";s:6:\"666666\";s:8:\"keywords\";a:6:{i:0;s:9:\"脆皮鸡\";i:1;s:9:\"排骨饭\";i:2;s:6:\"寿司\";i:3;s:6:\"凉皮\";i:4;s:9:\"老乡鸡\";i:5;s:6:\"汉堡\";}}s:16:\"background_color\";s:0:\"\";s:5:\"color\";s:6:\"FFFFFF\";s:13:\"icon_location\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/icon_location_white@2x.png\";s:9:\"icon_down\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/to_btn_arrowd_white@2x.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module2\";a:6:{s:16:\"background_color\";s:0:\"\";s:5:\"class\";s:1:\"2\";s:7:\"content\";a:1:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act_ban.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/huodong/detail-8.html\";s:6:\"wxlink\";s:31:\"../huodong/huodong?huodong_id=8\";s:4:\"open\";s:1:\"1\";}s:7:\"module3\";a:5:{s:5:\"class\";s:1:\"1\";s:5:\"color\";s:6:\"333333\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:10:{i:0;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_B8AEADC1AD1E1D954D30D2AEAE3B98EC.png\";s:5:\"title\";s:9:\"品牌馆\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";}i:1;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_26519B4526BD1BAFDA22874920170C69.png\";s:5:\"title\";s:6:\"美食\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-3.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=3\";}i:2;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_D095D594BBC97CF1CA601D7DE0127011.png\";s:5:\"title\";s:6:\"商超\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-4.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=4\";}i:3;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_BA3F66138ABB4BA1C4A4EB7B584E6FDA.png\";s:5:\"title\";s:6:\"鲜花\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-5.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=5\";}i:4;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_7DD57365DC2977E1CA48C9CD0309B8F0.png\";s:5:\"title\";s:9:\"面调控\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-6.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=6\";}i:5;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_CF2638BFDA9501321204589D2393CFD4.png\";s:5:\"title\";s:6:\"零食\";s:4:\"link\";s:50:\"http://wmv4.weizx.cn/waimai/shoplist/index-18.html\";s:6:\"wxlink\";s:30:\"../shoplist/shoplist?cateid=18\";}i:6;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_6DAE3172B4F21BD71B59DDAE4F8BFF08.png\";s:5:\"title\";s:6:\"蛋糕\";s:4:\"link\";s:50:\"http://wmv4.weizx.cn/waimai/shoplist/index-19.html\";s:6:\"wxlink\";s:30:\"../shoplist/shoplist?cateid=19\";}i:7;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_7D3CDA5B2A58641332EE4ADE8F6D9108.png\";s:5:\"title\";s:6:\"果蔬\";s:4:\"link\";s:50:\"http://wmv4.weizx.cn/waimai/shoplist/index-20.html\";s:6:\"wxlink\";s:30:\"../shoplist/shoplist?cateid=20\";}i:8;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_482BADDB81DF68C22601B5FE2EED15BA.png\";s:5:\"title\";s:6:\"饮品\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-8.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=8\";}i:9;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_744E2DA3619093B750F85E92FF3A8959.png\";s:5:\"title\";s:4:\"More\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-7.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=7\";}}s:4:\"open\";s:1:\"1\";}s:7:\"module4\";a:1:{s:4:\"open\";s:1:\"1\";}s:7:\"module8\";a:5:{s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_10220F2A5CB8E186129A401DF288EBA6.png\";s:4:\"link\";s:50:\"http://wmv4.weizx.cn/waimai/huodong/detail-13.html\";s:6:\"wxlink\";s:32:\"../huodong/huodong?huodong_id=13\";s:4:\"open\";s:1:\"1\";}s:7:\"module5\";a:8:{s:4:\"open\";s:1:\"1\";s:5:\"class\";i:1;s:5:\"title\";s:12:\"优惠专区\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module6\";a:8:{s:4:\"open\";s:1:\"0\";s:5:\"class\";i:1;s:5:\"title\";s:15:\"优惠专区二\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module7\";a:8:{s:4:\"open\";s:1:\"1\";s:5:\"class\";i:1;s:5:\"title\";s:12:\"大牌甄选\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:2:{i:1;a:6:{i:0;a:4:{s:5:\"title\";s:9:\"必胜客\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:4:{s:5:\"title\";s:12:\"吉祥混沌\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:4:{s:5:\"title\";s:12:\"青年餐厅\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:4:{s:5:\"title\";s:9:\"老乡鸡\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:5:{s:5:\"title\";s:9:\"台资味\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_5.png\";s:4:\"logo\";s:0:\"\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:4:{s:5:\"title\";s:12:\"大脸鸡排\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:4:{i:0;a:4:{s:5:\"title\";s:9:\"必胜客\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop01@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:4:{s:5:\"title\";s:12:\"吉祥混沌\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop02@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:4:{s:5:\"title\";s:12:\"青年餐厅\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop03@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:4:{s:5:\"title\";s:9:\"台资味\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop04@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module9\";a:2:{s:4:\"open\";s:1:\"1\";s:12:\"show_huodong\";s:1:\"1\";}s:8:\"module10\";a:4:{s:4:\"open\";s:1:\"1\";s:13:\"color_checked\";s:6:\"20AD20\";s:15:\"color_nochecked\";s:6:\"999999\";s:7:\"content\";a:4:{i:0;a:5:{s:4:\"open\";i:1;s:5:\"title\";s:6:\"首页\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot1_2.png\";s:14:\"icon_nochecked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot1_1.png\";}i:1;a:5:{s:4:\"open\";i:0;s:5:\"title\";s:9:\"购物车\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot2_2.png\";s:14:\"icon_nochecked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot2_1.png\";}i:2;a:5:{s:4:\"open\";i:1;s:5:\"title\";s:6:\"订单\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot3_2.png\";s:14:\"icon_nochecked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot3_1.png\";}i:3;a:5:{s:4:\"open\";i:1;s:5:\"title\";s:6:\"我的\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot4_2.png\";s:14:\"icon_nochecked\";s:65:\"https://img01.jhcms.com/wmdemo/default/icon_home/icon_foot4_1.png\";}}}}',0,1538288539),(35,'美团风格','photo/201809/20180930_AC6932E88523E6650C6AB3F5D5212EAE.png','a:11:{s:7:\"module0\";a:3:{s:16:\"background_color\";s:0:\"\";s:10:\"background\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_A0309C571483373B1711B5D3CFF6B573.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module1\";a:7:{s:5:\"class\";s:1:\"2\";s:9:\"searchBox\";a:3:{s:4:\"open\";s:1:\"0\";s:5:\"color\";s:6:\"FFFFFF\";s:8:\"keywords\";a:6:{i:0;s:9:\"脆皮鸡\";i:1;s:9:\"排骨饭\";i:2;s:6:\"寿司\";i:3;s:6:\"凉皮\";i:4;s:9:\"老乡鸡\";i:5;s:6:\"汉堡\";}}s:16:\"background_color\";s:0:\"\";s:5:\"color\";s:6:\"FFFFFF\";s:13:\"icon_location\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/icon_location_white@2x.png\";s:9:\"icon_down\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/to_btn_arrowd_white@2x.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module2\";a:6:{s:16:\"background_color\";s:0:\"\";s:5:\"class\";s:1:\"2\";s:7:\"content\";a:1:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act_ban.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:4:\"open\";s:1:\"1\";}s:7:\"module3\";a:5:{s:5:\"class\";s:1:\"1\";s:5:\"color\";s:6:\"333333\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:8:{i:0;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_FBC3CB51C81731BF80E346D98C597278.png\";s:5:\"title\";s:9:\"品牌馆\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";}i:1;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_2C98B929481BF476EEBD73B2304E9D90.png\";s:5:\"title\";s:6:\"美食\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-5.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=5\";}i:2;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_DF196B15730DB1D0972BCF637FE6C119.png\";s:5:\"title\";s:6:\"鲜花\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-5.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=5\";}i:3;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_1757902D67599919D27BA8C3BCA3AEAE.png\";s:5:\"title\";s:6:\"面食\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-6.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=6\";}i:4;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_EC0FC77542E41E0992AA52C21CCB38DF.png\";s:5:\"title\";s:6:\"小吃\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-7.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=7\";}i:5;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_1B3FB430A9C71363AA2998962755F2C6.png\";s:5:\"title\";s:6:\"零食\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-8.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=8\";}i:6;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_FF8F843981AEADA5939B3B08B836408D.png\";s:5:\"title\";s:6:\"饮料\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-4.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=4\";}i:7;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_C7FF5C54B74B31437601B48FD6683F64.png\";s:5:\"title\";s:6:\"更多\";s:4:\"link\";s:30:\"https://wmdemo.jhcms.cn/jifen/\";s:6:\"wxlink\";s:0:\"\";}}s:4:\"open\";s:1:\"1\";}s:7:\"module4\";a:1:{s:4:\"open\";s:1:\"1\";}s:7:\"module8\";a:5:{s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_E996979ECCCBE194DAB822DAC2D5B145.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:4:\"open\";s:1:\"1\";}s:7:\"module5\";a:7:{s:5:\"class\";s:1:\"1\";s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-861.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=861\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-861.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=861\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-861.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=861\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module6\";a:8:{s:4:\"open\";s:1:\"0\";s:5:\"class\";i:1;s:5:\"title\";s:15:\"优惠专区二\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module7\";a:7:{s:5:\"class\";s:1:\"1\";s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name2.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-872.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=872\";s:7:\"content\";a:2:{i:1;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_3.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-860.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=860\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:4:{i:0;a:3:{s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop01@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop02@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop03@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop04@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module9\";a:2:{s:4:\"open\";s:1:\"1\";s:12:\"show_huodong\";s:1:\"1\";}s:8:\"module10\";a:4:{s:15:\"color_nochecked\";s:6:\"999999\";s:13:\"color_checked\";s:6:\"ffb038\";s:7:\"content\";a:4:{i:0;a:5:{s:5:\"title\";s:6:\"首页\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_D0DFCD76A0F83FF116933DA71C3A1807.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_A6B6C0A77920E8CC05579CC85C3BA984.png\";}i:1;a:5:{s:5:\"title\";s:6:\"跑腿\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:31:\"https://wmdemo.jhcms.cn/paotui/\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_1EC713F0BF46263E058F355B6EAD2385.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_B617FE1B885311761D3B2D99FBEF43E5.png\";}i:2;a:5:{s:5:\"title\";s:6:\"订单\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_7B0D0FDBD15833DCBC3416A0ABA79E36.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_6FF7D5A400109A64C685DDBE2D2A5BB8.png\";}i:3;a:5:{s:5:\"title\";s:6:\"我的\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_3AAA2F64CDF2F9379D8301E0DCC26BC0.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_272E4ABAD2223C559E5E3A616E66FAE1.png\";}}s:4:\"open\";s:1:\"1\";}}',0,1538288575),(36,'猪年大吉','photo/201809/20180930_7AE5C3CA59524A46C600CDED6E1525AB.png','a:11:{s:7:\"module0\";a:3:{s:16:\"background_color\";s:0:\"\";s:10:\"background\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_E9B3F76147FC252E9CD27E8C51FE70BD.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module1\";a:7:{s:5:\"class\";s:1:\"2\";s:9:\"searchBox\";a:3:{s:4:\"open\";s:1:\"0\";s:5:\"color\";s:6:\"666666\";s:8:\"keywords\";a:6:{i:0;s:9:\"脆皮鸡\";i:1;s:9:\"排骨饭\";i:2;s:6:\"寿司\";i:3;s:6:\"凉皮\";i:4;s:9:\"老乡鸡\";i:5;s:6:\"汉堡\";}}s:16:\"background_color\";s:0:\"\";s:5:\"color\";s:6:\"FFFFFF\";s:13:\"icon_location\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/icon_location_white@2x.png\";s:9:\"icon_down\";s:77:\"https://img01.jhcms.com/wmdemo/default/icon/common/to_btn_arrowd_white@2x.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module2\";a:6:{s:16:\"background_color\";s:0:\"\";s:5:\"class\";s:1:\"2\";s:7:\"content\";a:1:{i:0;a:3:{s:5:\"photo\";s:60:\"https://img01.jhcms.com/wmdemo/default/image/img_act_ban.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:4:\"open\";s:1:\"1\";}s:7:\"module3\";a:5:{s:5:\"class\";s:1:\"1\";s:5:\"color\";s:6:\"333333\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:10:{i:0;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_D1AD2EC07C6221BDEC6BD8693C0F1FFE.png\";s:5:\"title\";s:9:\"品牌馆\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";}i:1;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_2216ABECC40FA9ACF70425891178EA05.png\";s:5:\"title\";s:6:\"美食\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-4.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=4\";}i:2;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_25ACF31558FC8F173440B0D4B0E48CE7.png\";s:5:\"title\";s:6:\"商超\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-5.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=5\";}i:3;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_B1F12503E2A43ED1CF6BDFA32EF8956F.png\";s:5:\"title\";s:6:\"鲜花\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-7.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=7\";}i:4;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_44803A13DD17C0B9CC6CF2BC38F98DE2.png\";s:5:\"title\";s:9:\"面调控\";s:4:\"link\";s:50:\"http://wmv4.weizx.cn/waimai/shoplist/index-19.html\";s:6:\"wxlink\";s:30:\"../shoplist/shoplist?cateid=19\";}i:5;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_915B3CACFAB1CFD32D6C03C1278A036E.png\";s:5:\"title\";s:6:\"零食\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-8.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=8\";}i:6;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_702BF084674BD288B02C8B7F4B821A5A.png\";s:5:\"title\";s:6:\"蛋糕\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-4.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=4\";}i:7;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_EFE355DE9838C84B81D55883ABB47B4D.png\";s:5:\"title\";s:6:\"果蔬\";s:4:\"link\";s:50:\"http://wmv4.weizx.cn/waimai/shoplist/index-19.html\";s:6:\"wxlink\";s:30:\"../shoplist/shoplist?cateid=19\";}i:8;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_A6696421F2B80D08C6994CF2720B9A00.png\";s:5:\"title\";s:6:\"饮品\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/shoplist/index-5.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=5\";}i:9;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_A438C49E40C0FCA1FB34AF93284E821B.png\";s:5:\"title\";s:4:\"More\";s:4:\"link\";s:50:\"http://wmv4.weizx.cn/waimai/huodong/detail-12.html\";s:6:\"wxlink\";s:32:\"../huodong/huodong?huodong_id=12\";}}s:4:\"open\";s:1:\"1\";}s:7:\"module4\";a:1:{s:4:\"open\";s:1:\"1\";}s:7:\"module8\";a:5:{s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_6D4EA35B3DEBB89DB871CF2B8FB4CE26.png\";s:4:\"link\";s:49:\"http://wmv4.weizx.cn/waimai/huodong/detail-8.html\";s:6:\"wxlink\";s:31:\"../huodong/huodong?huodong_id=8\";s:4:\"open\";s:1:\"1\";}s:7:\"module5\";a:8:{s:4:\"open\";s:1:\"1\";s:5:\"class\";i:1;s:5:\"title\";s:12:\"优惠专区\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module6\";a:8:{s:4:\"open\";s:1:\"0\";s:5:\"class\";i:1;s:5:\"title\";s:15:\"优惠专区二\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module7\";a:8:{s:4:\"open\";s:1:\"1\";s:5:\"class\";i:1;s:5:\"title\";s:12:\"大牌甄选\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:2:{i:1;a:6:{i:0;a:4:{s:5:\"title\";s:9:\"必胜客\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:4:{s:5:\"title\";s:12:\"吉祥混沌\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:4:{s:5:\"title\";s:12:\"青年餐厅\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:4:{s:5:\"title\";s:9:\"老乡鸡\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:5:{s:5:\"title\";s:9:\"台资味\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_5.png\";s:4:\"logo\";s:0:\"\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:4:{s:5:\"title\";s:12:\"大脸鸡排\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:4:{i:0;a:4:{s:5:\"title\";s:9:\"必胜客\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop01@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:4:{s:5:\"title\";s:12:\"吉祥混沌\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop02@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:4:{s:5:\"title\";s:12:\"青年餐厅\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop03@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:4:{s:5:\"title\";s:9:\"台资味\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop04@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module9\";a:2:{s:4:\"open\";s:1:\"1\";s:12:\"show_huodong\";s:1:\"1\";}s:8:\"module10\";a:4:{s:15:\"color_nochecked\";s:6:\"999999\";s:13:\"color_checked\";s:6:\"ff0000\";s:7:\"content\";a:4:{i:0;a:5:{s:5:\"title\";s:6:\"首页\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_B0A822821EBF75DFD8FE48E07BB6124A.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_61110A7CDBCF6886C4EB89C3F511A3B1.png\";}i:1;a:5:{s:5:\"title\";s:6:\"跑腿\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:28:\"http://wmv4.weizx.cn/paotui/\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_488187FF64D36B8FCE3005D99C999E54.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_C130285B40645452C09C3104E1ACCEA4.png\";}i:2;a:5:{s:5:\"title\";s:6:\"订单\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_4F15CC7740E6B6348B699976CA6CFE80.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_F527AC6E5EC55B9224BC5B9711ECFB40.png\";}i:3;a:5:{s:5:\"title\";s:6:\"我的\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_507A73321ECC74DD73FDC4CA430EE0CF.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_6712AC2F59A5E6CFE965C38D01D79581.png\";}}s:4:\"open\";s:1:\"1\";}}',0,1538290856),(38,'饿了么风格','photo/201809/20180930_81FC7C7F52D1107CFDA27832D028B922.png','a:11:{s:7:\"module0\";a:3:{s:4:\"open\";s:1:\"1\";s:10:\"background\";s:0:\"\";s:16:\"background_color\";s:0:\"\";}s:7:\"module1\";a:7:{s:5:\"class\";s:1:\"2\";s:9:\"searchBox\";a:3:{s:4:\"open\";s:1:\"1\";s:5:\"color\";s:6:\"666666\";s:8:\"keywords\";a:6:{i:0;s:9:\"脆皮鸡\";i:1;s:9:\"排骨饭\";i:2;s:6:\"寿司\";i:3;s:6:\"凉皮\";i:4;s:9:\"老乡鸡\";i:5;s:6:\"汉堡\";}}s:16:\"background_color\";s:0:\"\";s:5:\"color\";s:6:\"666666\";s:13:\"icon_location\";s:76:\"https://img01.jhcms.com/wmdemo/default/icon/common/icon_location_gray@2x.png\";s:9:\"icon_down\";s:76:\"https://img01.jhcms.com/wmdemo/default/icon/common/to_btn_arrowd_gray@2x.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module2\";a:6:{s:16:\"background_color\";s:0:\"\";s:5:\"class\";s:1:\"1\";s:7:\"content\";a:1:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_7ACFAD7B93E02C848F141A1665CC4520.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}}s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:4:\"open\";s:1:\"1\";}s:7:\"module3\";a:5:{s:5:\"class\";s:1:\"1\";s:5:\"color\";s:6:\"333333\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:10:{i:0;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_D0C0B7960D77CFF33150BB3C1EE3CA71.png\";s:5:\"title\";s:6:\"美食\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";}i:1;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_DF939C9EA5C7016D25B889C52755EBBB.png\";s:5:\"title\";s:6:\"便利\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-3.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=3\";}i:2;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_CAF19055D1F469941F9F7201D48740A2.png\";s:5:\"title\";s:6:\"水果\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-4.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=4\";}i:3;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_DB1FA04C598B7A4BFD5B8224F96A20C8.png\";s:5:\"title\";s:6:\"急送\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-5.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=5\";}i:4;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_3C6EA6DBBF0C2AB385F79A826A18092F.png\";s:5:\"title\";s:6:\"饮品\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-6.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=6\";}i:5;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_7FA1AC22D07DABD7EA22D45462B176FA.png\";s:5:\"title\";s:6:\"跑腿\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-7.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=7\";}i:6;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_154EE5710CA233F5D178286C1970F1B3.png\";s:5:\"title\";s:6:\"特惠\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-8.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=8\";}i:7;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_FE23F093810A5484ABBE2DF95565A3D3.png\";s:5:\"title\";s:6:\"小吃\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-7.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=7\";}i:8;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_1D337457D6A6E74CCB01684E54199CA4.png\";s:5:\"title\";s:6:\"河蟹\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";}i:9;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_B0BD37DF98995367EE26B995FA679752.png\";s:5:\"title\";s:9:\"土特产\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-3.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=3\";}}s:4:\"open\";s:1:\"1\";}s:7:\"module4\";a:1:{s:4:\"open\";s:1:\"1\";}s:7:\"module5\";a:7:{s:5:\"class\";s:1:\"4\";s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201810/20181008_4564DE72ED548857FEBE50A05DB8FC8C.png\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_1622C1D1AF560A4DEBD94252F19E2AAB.png\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/huodong/detail-8.html\";s:6:\"wxlink\";s:31:\"../huodong/huodong?huodong_id=8\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_D991524F7D3ACAD67396A581B0CE5C7E.png\";s:4:\"link\";s:53:\"https://wmdemo.jhcms.cn/waimai/huodong/detail-12.html\";s:6:\"wxlink\";s:32:\"../huodong/huodong?huodong_id=12\";}i:2;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_3101CF093573761E2D525D903C762147.png\";s:4:\"link\";s:53:\"https://wmdemo.jhcms.cn/waimai/huodong/detail-13.html\";s:6:\"wxlink\";s:32:\"../huodong/huodong?huodong_id=13\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module6\";a:8:{s:4:\"open\";s:1:\"0\";s:5:\"class\";i:1;s:5:\"title\";s:15:\"优惠专区二\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module7\";a:7:{s:5:\"class\";s:1:\"1\";s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201810/20181008_35030260845C55EF5408A4ADDAFC159E.png\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:29:\"../shoplist/shoplist?cateid=2\";s:7:\"content\";a:2:{i:1;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_1.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_2.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-860.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=860\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_3.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-415.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=415\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_4.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-872.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=872\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_5.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-858.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=858\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_6.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-861.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=861\";}}i:2;a:4:{i:0;a:3:{s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop01@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop02@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop03@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop04@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"1\";}s:7:\"module8\";a:5:{s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:57:\"https://img01.jhcms.com/wmdemo/default/image/img_act8.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:27:\"../shoptail/shoptail?id=855\";s:4:\"open\";s:1:\"1\";}s:7:\"module9\";a:2:{s:4:\"open\";s:1:\"1\";s:12:\"show_huodong\";s:1:\"1\";}s:8:\"module10\";a:4:{s:15:\"color_nochecked\";s:6:\"666666\";s:13:\"color_checked\";s:6:\"2977f4\";s:7:\"content\";a:4:{i:0;a:5:{s:5:\"title\";s:6:\"首页\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_13A8A343DAC89D2CE38516B096496F8F.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_0623D3BB5FBE6E5EF7668F1801B77090.png\";}i:1;a:5:{s:5:\"title\";s:6:\"跑腿\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:31:\"https://wmdemo.jhcms.cn/paotui/\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_573B2CC91B8B76EAF93197F736B15A51.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_28384ADB90A549326018DA63E3DEC8AD.png\";}i:2;a:5:{s:5:\"title\";s:6:\"订单\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_8A077E1787D05898987143595328979C.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_6C2C4872E6843F8CC8DC0BCC3DFB6967.png\";}i:3;a:5:{s:5:\"title\";s:6:\"我的\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_0B6B86136603008E3C97AC562611DB9D.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201809/20180930_E0D80181750EE66AA93E29A2C25CF36E.png\";}}s:4:\"open\";s:1:\"1\";}}',1,1539224896),(45,'官方默认2','https://img01.jhcms.com/wmdemo/photo/201811/20181105_C9C21463D4689502B5109669D2199395.png','a:11:{s:7:\"module0\";a:3:{s:4:\"open\";s:1:\"1\";s:10:\"background\";s:0:\"\";s:16:\"background_color\";s:0:\"\";}s:7:\"module1\";a:7:{s:5:\"class\";s:1:\"3\";s:9:\"searchBox\";a:3:{s:4:\"open\";s:1:\"1\";s:5:\"color\";s:6:\"666666\";s:8:\"keywords\";a:6:{i:0;s:9:\"脆皮鸡\";i:1;s:9:\"排骨饭\";i:2;s:6:\"寿司\";i:3;s:6:\"凉皮\";i:4;s:9:\"老乡鸡\";i:5;s:6:\"汉堡\";}}s:16:\"background_color\";s:0:\"\";s:5:\"color\";s:6:\"666666\";s:13:\"icon_location\";s:45:\"default/icon/common/icon_location_gray@2x.png\";s:9:\"icon_down\";s:45:\"default/icon/common/to_btn_arrowd_gray@2x.png\";s:4:\"open\";s:1:\"1\";}s:7:\"module2\";a:6:{s:16:\"background_color\";s:0:\"\";s:5:\"class\";s:1:\"1\";s:7:\"content\";a:1:{i:0;a:3:{s:5:\"photo\";s:58:\"photo/201811/20181105_75B4D6BDDD356CC21FFA8A579936B141.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-861.html\";s:6:\"wxlink\";s:31:\"/pages/shoptail/shoptail?id=861\";}}s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:4:\"open\";s:1:\"1\";}s:7:\"module3\";a:5:{s:5:\"class\";s:1:\"1\";s:5:\"color\";s:6:\"333333\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:5:{i:0;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_3DAAEBD46B47C26671FB5FF6C3B48B60.png\";s:5:\"title\";s:12:\"营养早餐\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-2.html\";s:6:\"wxlink\";s:33:\"/pages/shoplist/shoplist?cateid=2\";}i:1;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_541A2D77BEBE5D62C4A09FC5E78C7E7F.png\";s:5:\"title\";s:12:\"美味午餐\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-3.html\";s:6:\"wxlink\";s:33:\"/pages/shoplist/shoplist?cateid=3\";}i:2;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_0D17EB7EF9EA718E66DE8417A9E9DD20.png\";s:5:\"title\";s:12:\"诱人晚餐\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-4.html\";s:6:\"wxlink\";s:33:\"/pages/shoplist/shoplist?cateid=4\";}i:3;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_69A3FE3692B953DE8B6992FA9E6805FB.png\";s:5:\"title\";s:12:\"解馋夜宵\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-5.html\";s:6:\"wxlink\";s:33:\"/pages/shoplist/shoplist?cateid=5\";}i:4;a:4:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_58528FD554A0F74DF00768600798A9E1.png\";s:5:\"title\";s:12:\"特色小吃\";s:4:\"link\";s:52:\"https://wmdemo.jhcms.cn/waimai/shoplist/index-6.html\";s:6:\"wxlink\";s:33:\"/pages/shoplist/shoplist?cateid=6\";}}s:4:\"open\";s:1:\"1\";}s:7:\"module4\";a:1:{s:4:\"open\";s:1:\"1\";}s:7:\"module5\";a:7:{s:5:\"class\";s:1:\"1\";s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_B1D6C65FBD19143F7B5C6EF255E6F873.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_3954ACBC7B9D5E81A507E1A412E1639B.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-855.html\";s:6:\"wxlink\";s:31:\"/pages/shoptail/shoptail?id=855\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_F526B1D45ED198D23F368E389FB9D9BB.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-872.html\";s:6:\"wxlink\";s:31:\"/pages/shoptail/shoptail?id=872\";}i:2;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_B81F9BC5C838021A8326B0B3C4997826.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-795.html\";s:6:\"wxlink\";s:31:\"/pages/shoptail/shoptail?id=795\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act5_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"0\";}s:7:\"module8\";a:5:{s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_815E8798C4C0C407507B09F954CE0CED.png\";s:4:\"link\";s:53:\"https://wmdemo.jhcms.cn/waimai/huodong/detail-11.html\";s:6:\"wxlink\";s:36:\"/pages/huodong/huodong?huodong_id=11\";s:4:\"open\";s:1:\"0\";}s:7:\"module6\";a:7:{s:5:\"class\";s:1:\"5\";s:16:\"background_color\";s:0:\"\";s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_DE629B39065CB5A7E26F9A92D09F0226.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:7:\"content\";a:7:{i:1;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act1_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:2:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act2_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:3;a:1:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act3_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:4;a:3:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act4_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:5;a:4:{i:0;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_34166146508DCF43B5956AB2FCE49010.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-868.html\";s:6:\"wxlink\";s:31:\"/pages/shoptail/shoptail?id=868\";}i:1;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_08EFEF50E5A7A52EAD9B8FB39B48551E.png\";s:4:\"link\";s:30:\"https://wmdemo.jhcms.cn/qiang/\";s:6:\"wxlink\";s:30:\"https://wmdemo.jhcms.cn/qiang/\";}i:2;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_EFB55447372E0BE9B4A2707A8AE80CBC.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-415.html\";s:6:\"wxlink\";s:31:\"/pages/shoptail/shoptail?id=415\";}i:3;a:3:{s:5:\"photo\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_9930EAFC305365138BC65C1B2D2EB378.png\";s:4:\"link\";s:51:\"https://wmdemo.jhcms.cn/waimai/shop/detail-878.html\";s:6:\"wxlink\";s:31:\"/pages/shoptail/shoptail?id=878\";}}i:6;a:4:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act6_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:7;a:6:{i:0;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_5.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:3:{s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act7_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}s:4:\"open\";s:1:\"0\";}s:7:\"module7\";a:8:{s:4:\"open\";s:1:\"0\";s:5:\"class\";i:1;s:5:\"title\";s:12:\"大牌甄选\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/img_act_name2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";s:16:\"background_color\";s:0:\"\";s:7:\"content\";a:2:{i:1;a:6:{i:0;a:4:{s:5:\"title\";s:9:\"必胜客\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_1.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:4:{s:5:\"title\";s:12:\"吉祥混沌\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_2.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:4:{s:5:\"title\";s:12:\"青年餐厅\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_3.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:4:{s:5:\"title\";s:9:\"老乡鸡\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_4.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:4;a:5:{s:5:\"title\";s:9:\"台资味\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_5.png\";s:4:\"logo\";s:0:\"\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:5;a:4:{s:5:\"title\";s:12:\"大脸鸡排\";s:5:\"photo\";s:59:\"https://img01.jhcms.com/wmdemo/default/image/img_act9_6.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}i:2;a:4:{i:0;a:4:{s:5:\"title\";s:9:\"必胜客\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop01@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:1;a:4:{s:5:\"title\";s:12:\"吉祥混沌\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop02@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:2;a:4:{s:5:\"title\";s:12:\"青年餐厅\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop03@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}i:3;a:4:{s:5:\"title\";s:9:\"台资味\";s:5:\"photo\";s:62:\"https://img01.jhcms.com/wmdemo/default/image/pic_shop04@2x.png\";s:4:\"link\";s:0:\"\";s:6:\"wxlink\";s:0:\"\";}}}}s:7:\"module9\";a:2:{s:4:\"open\";s:1:\"1\";s:12:\"show_huodong\";s:1:\"1\";}s:8:\"module10\";a:4:{s:15:\"color_nochecked\";s:6:\"999999\";s:13:\"color_checked\";s:6:\"fe6d4c\";s:7:\"content\";a:4:{i:0;a:5:{s:5:\"title\";s:6:\"首页\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_AD95F274E3C63E038CD7B33028A8DF9F.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_6327904A193C8A5BE7430B7EE479B280.png\";}i:1;a:5:{s:5:\"title\";s:6:\"跑腿\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:31:\"https://wmdemo.jhcms.cn/paotui/\";s:12:\"icon_checked\";s:58:\"photo/201811/20181105_874559EC16930033BE47FFC5060486A3.png\";s:14:\"icon_nochecked\";s:58:\"photo/201811/20181105_4DF5B77E41A1FC1ACB51A7F9F0AAD7F0.png\";}i:2;a:5:{s:5:\"title\";s:6:\"订单\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_477E0B20C5C954AFE3BE0E6539422010.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_3027055D891F84E7EBF660633354E2D0.png\";}i:3;a:5:{s:5:\"title\";s:6:\"我的\";s:4:\"open\";s:1:\"1\";s:4:\"link\";s:0:\"\";s:12:\"icon_checked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_B330AF10C1A847E741B240610244103D.png\";s:14:\"icon_nochecked\";s:89:\"https://img01.jhcms.com/wmdemo/photo/201811/20181105_60A41DBF8E62F4E4A3DD7D322F558581.png\";}}s:4:\"open\";s:1:\"1\";}}',0,1541384714);
/*!40000 ALTER TABLE `jh_adv_themes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_article`
--

DROP TABLE IF EXISTS `jh_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_article` (
  `article_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` smallint(6) DEFAULT '0',
  `cat_id` mediumint(8) unsigned DEFAULT '0',
  `from` enum('article','about','help','hongbao','pchelp','page') DEFAULT 'article',
  `page` varchar(15) DEFAULT '',
  `title` varchar(200) DEFAULT '',
  `thumb` varchar(150) DEFAULT '',
  `desc` varchar(255) DEFAULT '',
  `views` mediumint(8) DEFAULT '0',
  `favorites` mediumint(8) DEFAULT '0',
  `allow_comment` tinyint(1) DEFAULT '1',
  `comments` mediumint(8) DEFAULT '0',
  `photos` smallint(6) DEFAULT '0',
  `linkurl` varchar(255) DEFAULT '',
  `ontime` int(10) DEFAULT '0',
  `hidden` tinyint(1) DEFAULT '0',
  `orderby` smallint(6) unsigned DEFAULT '50',
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) unsigned DEFAULT '0',
  `dateline` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`article_id`),
  KEY `cat_id` (`cat_id`,`from`,`audit`,`closed`,`hidden`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_article`
--

LOCK TABLES `jh_article` WRITE;
/*!40000 ALTER TABLE `jh_article` DISABLE KEYS */;
INSERT INTO `jh_article` VALUES (1,0,3,'page','hongbao','红包说明','','1.红包怎么获得？通过邀请好友获得2.红包可以做什么？可以抵扣在线支付时的实际支付金额3.一个红包能拆开多次使用吗？不能，一个红包只能一次性使用，不能分开使用。4.下单的时候使用了红包，但是后来订单取消了，红包还会返回吗？会的，订单无效后红包会自动返还到您的账户里。5.红包兑换码怎样兑换成红包，怎样查看红包？在个人中心-&gt;我的红包-&gt;兑换红包…',1,0,1,0,0,'',0,0,50,1,0,1501234655),(2,0,3,'page','protocolstaff','骑手入住协议','','骑手入住协议',0,0,1,0,0,'',0,0,50,1,0,1501863196),(3,0,3,'page','protocol','注册协议','','注册协议',0,0,1,0,0,'',0,0,50,1,0,1501863261),(4,0,3,'page','protocolbiz','商户入住协议','','商户入住协议',0,0,1,0,0,'',0,0,50,1,0,1501863332),(5,0,2,'about','about','关于我们','','关于我们',0,0,1,0,0,'',0,0,50,1,0,1502435326),(6,0,2,'about','works','加入我们','','加入我们',0,0,1,0,0,'',0,0,50,1,0,1502435409),(7,0,2,'about','downapp','下载APP','','下载APP',0,0,1,0,0,'',0,0,50,1,0,1502435459),(8,0,2,'about','kaidian','我要开店','','我要开店',0,0,1,0,0,'',0,0,50,1,0,1502435536),(9,0,2,'about','peijiameng','配送加盟','','配送加盟',0,0,1,0,0,'',0,0,50,1,0,1502435965),(10,0,2,'about','csdaili','城市代理','','城市代理',0,0,1,0,0,'',0,0,50,1,0,1502436063),(11,0,3,'page','about','关于我们','','测试',0,0,1,0,0,'',0,0,50,1,0,1510220740);
/*!40000 ALTER TABLE `jh_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_article_cate`
--

DROP TABLE IF EXISTS `jh_article_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_article_cate` (
  `cat_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` mediumint(8) unsigned DEFAULT '0',
  `title` varchar(150) DEFAULT '',
  `level` tinyint(1) unsigned DEFAULT '1',
  `from` enum('about','help','page','hongbao','pchelp','article') DEFAULT 'article',
  `seo_title` varchar(255) DEFAULT '',
  `seo_keywords` varchar(255) DEFAULT '',
  `seo_description` varchar(255) DEFAULT '',
  `orderby` smallint(6) unsigned DEFAULT '50',
  `hidden` tinyint(1) DEFAULT '0',
  `dateline` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_article_cate`
--

LOCK TABLES `jh_article_cate` WRITE;
/*!40000 ALTER TABLE `jh_article_cate` DISABLE KEYS */;
INSERT INTO `jh_article_cate` VALUES (1,0,'帮助中心',1,'help','帮助中心','帮助中心','帮助中心',50,1,1501860858),(2,0,'关于我们',1,'about','关于我们','关于我们','关于我们',50,1,1501860872),(3,0,'单页管理',1,'page','','','',50,1,1501860890);
/*!40000 ALTER TABLE `jh_article_cate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_article_content`
--

DROP TABLE IF EXISTS `jh_article_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_article_content` (
  `content_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(10) NOT NULL,
  `seo_title` varchar(150) DEFAULT '',
  `seo_keywords` varchar(255) DEFAULT '',
  `seo_description` varchar(255) DEFAULT '',
  `content` mediumtext,
  `clientip` varchar(15) DEFAULT '0.0.0.0',
  PRIMARY KEY (`content_id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_article_content`
--

LOCK TABLES `jh_article_content` WRITE;
/*!40000 ALTER TABLE `jh_article_content` DISABLE KEYS */;
INSERT INTO `jh_article_content` VALUES (1,1,'','','','<h1 style=\"font-size:16px;font-weight:normal;color:#333333;font-family:\" background-color:#eef2f5;\"=\"\">\r\n	红包说明\r\n	</h1>\r\n<p style=\"font-size:14px;color:#666666;font-family:\" background-color:#eef2f5;\"=\"\"> <span style=\"font-family:NSimSun;font-size:12px;\">1.红包怎么获得？<br />\r\n通过邀请好友获得<br />\r\n2.红包可以做什么？<br />\r\n可以抵扣在线支付时的实际支付金额<br />\r\n3.一个红包能拆开多次使用吗？<br />\r\n不能，一个红包只能一次性使用，不能分开使用。<br />\r\n4.下单的时候使用了红包，但是后来订单取消了，红包还会返回吗？<br />\r\n会的，订单无效后红包会自动返还到您的账户里。<br />\r\n5.红包兑换码怎样兑换成红包，怎样查看红包？<br />\r\n在个人中心-&gt;我的红包-&gt;兑换红包，输入兑换码进行兑换。<br />\r\n6.邀请好友了，为什么没获得红包？<br />\r\n先检查一下您是否在同一设备上进行的邀请？或者被邀请人是否通过您发给对方的链接进</span> \r\n		</p>','112.26.23.195'),(2,2,'','','','<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">请到管理员后台</span></strong>\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&nbsp;&gt;&gt; 运营 &nbsp;</span></strong>\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&nbsp;&gt;&gt; 文章管理 &nbsp;</span></strong>\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&nbsp;&gt;&gt; 单页管理&nbsp;</span></strong>\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&nbsp;&gt;&gt; 骑手入住协议</span></strong>\r\n</p>','183.160.98.73'),(3,3,'','','','<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<p>\r\n		<strong><span style=\"color:#E56600;font-size:16px;\">请到管理员后台</span></strong>\r\n	</p>\r\n	<p>\r\n		<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 运营</span></strong>\r\n	</p>\r\n	<p>\r\n		<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 文章管理</span></strong>\r\n	</p>\r\n	<p>\r\n		<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 单页管理</span></strong>\r\n	</p>\r\n	<p>\r\n		<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 注册协议</span></strong>\r\n	</p>\r\n</p>','183.160.98.73'),(4,4,'','','','<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\"><br />\r\n</span></strong>\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">请到管理员后台</span></strong>\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 运营</span></strong>\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 文章管理</span></strong>\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 单页管理</span></strong>\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 商户入住协议</span></strong>\r\n</p>','183.160.98.73'),(5,5,'','','','<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\"><br />\r\n</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">请到管理员后台</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 运营</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 文章管理</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 关于我们</span></strong>\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt;<strong><span style=\"color:#E56600;font-size:16px;\">关于我们</span></strong></span></strong> \r\n</p>','47.52.3.160'),(6,6,'','','','<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\"><br />\r\n</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">请到管理员后台</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 运营</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 文章管理</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 关于我们</span></strong>\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 加入我们</span></strong>\r\n</p>','47.52.3.160'),(7,7,'','','','<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\"><br />\r\n</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">请到管理员后台</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 运营</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 文章管理</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 关于我们</span></strong>\r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 下载APP</span></strong>\r\n</p>','47.52.3.160'),(8,8,'','','','<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\"><br />\r\n</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">请到管理员后台</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 运营</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 文章管理</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 关于我们</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 我要开店</span></strong> \r\n</p>','47.52.3.160'),(9,9,'','','','<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\"><br />\r\n</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">请到管理员后台</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 运营</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 文章管理</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 关于我们</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 配送加盟</span></strong> \r\n</p>','47.52.3.160'),(10,10,'','','','<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\"><br />\r\n</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">请到管理员后台</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 运营</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 文章管理</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 关于我们</span></strong> \r\n</p>\r\n<p>\r\n	<strong><span style=\"color:#E56600;font-size:16px;\">&gt;&gt; 城市代理</span></strong> \r\n</p>','47.52.3.160'),(11,11,'','','','<p>\r\n	测试\r\n</p>\r\n<p>\r\n	<img src=\"http://img01.jhcms.com/wmdemo/photo/201808/20180815_302B53968A4D96C24A442615D79EDCAC.png?PID50263\" alt=\"\" /> \r\n</p>','192.168.1.128'),(12,12,'','','','e','117.95.5.244');
/*!40000 ALTER TABLE `jh_article_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_article_photo`
--

DROP TABLE IF EXISTS `jh_article_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_article_photo` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(10) DEFAULT '0',
  `title` varchar(100) DEFAULT NULL,
  `photo` varchar(150) DEFAULT '',
  `size` mediumint(8) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_article_photo`
--

LOCK TABLES `jh_article_photo` WRITE;
/*!40000 ALTER TABLE `jh_article_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_article_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_article_zan`
--

DROP TABLE IF EXISTS `jh_article_zan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_article_zan` (
  `zan_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(10) DEFAULT '0',
  `uid` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`zan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_article_zan`
--

LOCK TABLES `jh_article_zan` WRITE;
/*!40000 ALTER TABLE `jh_article_zan` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_article_zan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_block`
--

DROP TABLE IF EXISTS `jh_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_block` (
  `block_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `theme` varchar(30) DEFAULT NULL,
  `page_id` smallint(6) DEFAULT '0',
  `title` varchar(50) DEFAULT '',
  `from` varchar(50) DEFAULT '',
  `type` enum('default','hot','new','only','zhanwei') DEFAULT 'default',
  `config` mediumtext,
  `tmpl` mediumtext,
  `limit` tinyint(3) DEFAULT '10',
  `ttl` mediumint(8) DEFAULT '900',
  `orderby` smallint(50) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_block`
--

LOCK TABLES `jh_block` WRITE;
/*!40000 ALTER TABLE `jh_block` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_block` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_block_item`
--

DROP TABLE IF EXISTS `jh_block_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_block_item` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block_id` mediumint(8) unsigned DEFAULT '0',
  `itemId` int(10) DEFAULT '0',
  `title` varchar(50) DEFAULT '',
  `link` varchar(150) DEFAULT '',
  `thumb` varchar(150) DEFAULT '',
  `city_id` smallint(6) DEFAULT '0',
  `data` mediumtext,
  `expire_time` int(10) DEFAULT '0',
  `orderby` smallint(6) DEFAULT '50',
  `dateline` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `block_id` (`block_id`,`itemId`,`city_id`),
  KEY `orderby` (`orderby`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_block_item`
--

LOCK TABLES `jh_block_item` WRITE;
/*!40000 ALTER TABLE `jh_block_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_block_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_block_page`
--

DROP TABLE IF EXISTS `jh_block_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_block_page` (
  `page_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT '',
  `orderby` smallint(6) DEFAULT '50',
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_block_page`
--

LOCK TABLES `jh_block_page` WRITE;
/*!40000 ALTER TABLE `jh_block_page` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_block_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_book`
--

DROP TABLE IF EXISTS `jh_book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_book` (
  `book_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT '0',
  `nickname` varchar(30) DEFAULT '',
  `content` text,
  `dateline` int(10) unsigned DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_book`
--

LOCK TABLES `jh_book` WRITE;
/*!40000 ALTER TABLE `jh_book` DISABLE KEYS */;
INSERT INTO `jh_book` VALUES (1,47,'12121','231234214234234234234234',1480146860,'127.0.0.1'),(2,0,'游客','234234234234234234234234234',1480146932,'127.0.0.1');
/*!40000 ALTER TABLE `jh_book` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_cash_bills`
--

DROP TABLE IF EXISTS `jh_cash_bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_cash_bills` (
  `bills_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bills_sn` int(10) DEFAULT '0',
  `staff_id` int(10) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `amount` decimal(8,2) DEFAULT '0.00',
  `dateline` int(10) DEFAULT '0',
  `fee` decimal(8,2) DEFAULT '0.00',
  `pei_amount` decimal(8,2) DEFAULT '0.00',
  PRIMARY KEY (`bills_id`),
  UNIQUE KEY `staff_id_d_bills_sn` (`bills_sn`,`staff_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_cash_bills`
--

LOCK TABLES `jh_cash_bills` WRITE;
/*!40000 ALTER TABLE `jh_cash_bills` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_cash_bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_cash_bills_log`
--

DROP TABLE IF EXISTS `jh_cash_bills_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_cash_bills_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bills_id` int(10) DEFAULT '0',
  `bills_sn` int(10) DEFAULT '0',
  `staff_id` int(10) DEFAULT '0',
  `order_id` int(10) DEFAULT '0',
  `amount` decimal(8,2) DEFAULT '0.00',
  `dateline` int(10) DEFAULT '0',
  `fee` decimal(8,2) DEFAULT '0.00',
  `pei_amount` decimal(8,2) DEFAULT '0.00',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_cash_bills_log`
--

LOCK TABLES `jh_cash_bills_log` WRITE;
/*!40000 ALTER TABLE `jh_cash_bills_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_cash_bills_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_chong_bills`
--

DROP TABLE IF EXISTS `jh_chong_bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_chong_bills` (
  `bills_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(8,2) DEFAULT '0.00',
  `bills_sn` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`bills_id`),
  UNIQUE KEY `bills_sn_uid` (`bills_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_chong_bills`
--

LOCK TABLES `jh_chong_bills` WRITE;
/*!40000 ALTER TABLE `jh_chong_bills` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_chong_bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_chong_bills_log`
--

DROP TABLE IF EXISTS `jh_chong_bills_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_chong_bills_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT '0.00',
  `bills_id` int(10) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_chong_bills_log`
--

LOCK TABLES `jh_chong_bills_log` WRITE;
/*!40000 ALTER TABLE `jh_chong_bills_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_chong_bills_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_data_area`
--

DROP TABLE IF EXISTS `jh_data_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_data_area` (
  `area_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `city_id` mediumint(8) DEFAULT NULL,
  `area_name` varchar(50) DEFAULT '',
  `orderby` smallint(6) DEFAULT '50',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`area_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_data_area`
--

LOCK TABLES `jh_data_area` WRITE;
/*!40000 ALTER TABLE `jh_data_area` DISABLE KEYS */;
INSERT INTO `jh_data_area` VALUES (1,1,'蜀山区',50,1500862864);
/*!40000 ALTER TABLE `jh_data_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_data_business`
--

DROP TABLE IF EXISTS `jh_data_business`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_data_business` (
  `business_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `city_id` mediumint(8) DEFAULT '0',
  `area_id` mediumint(8) DEFAULT '0',
  `business_name` varchar(50) DEFAULT NULL,
  `orderby` smallint(6) DEFAULT '50',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`business_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_data_business`
--

LOCK TABLES `jh_data_business` WRITE;
/*!40000 ALTER TABLE `jh_data_business` DISABLE KEYS */;
INSERT INTO `jh_data_business` VALUES (1,1,1,'华润五彩城',50,1500863091);
/*!40000 ALTER TABLE `jh_data_business` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_data_city`
--

DROP TABLE IF EXISTS `jh_data_city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_data_city` (
  `city_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `province_id` smallint(6) DEFAULT '0',
  `city_name` varchar(50) DEFAULT '',
  `pinyin` varchar(50) DEFAULT '',
  `theme_id` smallint(6) DEFAULT '0',
  `logo` varchar(150) DEFAULT '',
  `phone` varchar(30) DEFAULT '',
  `city_code` char(10) DEFAULT '',
  `mobile` varchar(15) DEFAULT '',
  `mail` varchar(30) DEFAULT '',
  `kfqq` varchar(30) DEFAULT '',
  `orderby` smallint(6) DEFAULT '50',
  `audit` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `adcode` int(10) DEFAULT NULL,
  PRIMARY KEY (`city_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_data_city`
--

LOCK TABLES `jh_data_city` WRITE;
/*!40000 ALTER TABLE `jh_data_city` DISABLE KEYS */;
INSERT INTO `jh_data_city` VALUES (1,1,'河内','henei',1,'','','340100','','','',50,1,1500862854,NULL),(2,1,'胡志明市','huzhiming',0,'','','','','','',50,1,1543491629,NULL);
/*!40000 ALTER TABLE `jh_data_city` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_data_district`
--

DROP TABLE IF EXISTS `jh_data_district`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_data_district` (
  `district_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `city_id` mediumint(8) DEFAULT NULL,
  `district_name` varchar(50) DEFAULT '',
  `orderby` smallint(6) DEFAULT '50',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_data_district`
--

LOCK TABLES `jh_data_district` WRITE;
/*!40000 ALTER TABLE `jh_data_district` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_data_district` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_data_province`
--

DROP TABLE IF EXISTS `jh_data_province`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_data_province` (
  `province_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `province_name` varchar(30) DEFAULT '',
  `orderby` smallint(6) DEFAULT '50',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`province_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_data_province`
--

LOCK TABLES `jh_data_province` WRITE;
/*!40000 ALTER TABLE `jh_data_province` DISABLE KEYS */;
INSERT INTO `jh_data_province` VALUES (1,'越南',50,1500862812);
/*!40000 ALTER TABLE `jh_data_province` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_data_region`
--

DROP TABLE IF EXISTS `jh_data_region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_data_region` (
  `region_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `region_name` varchar(50) NOT NULL DEFAULT '',
  `parent_id` mediumint(8) unsigned DEFAULT '0',
  `path_ids` varchar(255) DEFAULT NULL,
  `level` tinyint(3) unsigned DEFAULT '1',
  `lng` int(10) DEFAULT '0',
  `lat` int(10) DEFAULT '0',
  `orderby` smallint(6) unsigned DEFAULT '50',
  `closed` tinyint(1) DEFAULT '0',
  `city_code` varchar(6) DEFAULT '0',
  `adcode` int(10) DEFAULT '0',
  `option_level` enum('district','city','province') DEFAULT NULL,
  PRIMARY KEY (`region_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3267 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_data_region`
--

LOCK TABLES `jh_data_region` WRITE;
/*!40000 ALTER TABLE `jh_data_region` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_data_region` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_ditui`
--

DROP TABLE IF EXISTS `jh_ditui`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_ditui` (
  `ditui_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` smallint(6) DEFAULT '0',
  `mobile` varchar(11) DEFAULT '0',
  `passwd` char(32) DEFAULT '',
  `money` decimal(10,2) DEFAULT '0.00',
  `pmid` char(9) DEFAULT '',
  `reg_count` mediumint(8) DEFAULT '0',
  `order_count` mediumint(8) DEFAULT '0',
  `name` varchar(30) DEFAULT '',
  `id_number` varchar(18) DEFAULT '',
  `id_photo` varchar(150) DEFAULT '',
  `account_type` varchar(30) DEFAULT '',
  `account_name` varchar(30) DEFAULT '',
  `account_number` varchar(30) DEFAULT '',
  `audit` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `total_money` decimal(10,2) DEFAULT '0.00',
  `closed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ditui_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_ditui`
--

LOCK TABLES `jh_ditui` WRITE;
/*!40000 ALTER TABLE `jh_ditui` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_ditui` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_ditui_log`
--

DROP TABLE IF EXISTS `jh_ditui_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_ditui_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ditui_id` mediumint(8) DEFAULT '0',
  `uid` mediumint(8) DEFAULT NULL,
  `money` decimal(8,2) DEFAULT '0.00',
  `intro` varchar(255) DEFAULT '',
  `admin` varchar(30) DEFAULT '',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT NULL,
  `type` enum('tixian','invite') DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_ditui_log`
--

LOCK TABLES `jh_ditui_log` WRITE;
/*!40000 ALTER TABLE `jh_ditui_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_ditui_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_ditui_member`
--

DROP TABLE IF EXISTS `jh_ditui_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_ditui_member` (
  `mid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ditui_id` mediumint(8) DEFAULT '0',
  `uid` mediumint(8) DEFAULT '0',
  `signup_amount` decimal(6,2) DEFAULT '0.00',
  `first_amount` decimal(6,2) DEFAULT '0.00',
  `first_order_id` int(10) DEFAULT '0',
  `first_order_amount` decimal(8,2) DEFAULT '0.00',
  `first_order_time` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `day` int(8) DEFAULT '0',
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_ditui_member`
--

LOCK TABLES `jh_ditui_member` WRITE;
/*!40000 ALTER TABLE `jh_ditui_member` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_ditui_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_ditui_shop`
--

DROP TABLE IF EXISTS `jh_ditui_shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_ditui_shop` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ditui_id` mediumint(8) DEFAULT '0',
  `shop_id` mediumint(8) DEFAULT '0',
  `signup_amount` decimal(6,2) DEFAULT '0.00',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_ditui_shop`
--

LOCK TABLES `jh_ditui_shop` WRITE;
/*!40000 ALTER TABLE `jh_ditui_shop` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_ditui_shop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_ditui_tixian`
--

DROP TABLE IF EXISTS `jh_ditui_tixian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_ditui_tixian` (
  `tixian_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ditui_id` mediumint(8) DEFAULT '0',
  `money` decimal(10,2) DEFAULT '0.00',
  `intro` varchar(255) DEFAULT '',
  `account_info` varchar(512) DEFAULT '',
  `status` tinyint(1) DEFAULT '0',
  `reason` varchar(255) DEFAULT NULL,
  `updatetime` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT NULL,
  `end_money` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`tixian_id`),
  KEY `ditui_id` (`ditui_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_ditui_tixian`
--

LOCK TABLES `jh_ditui_tixian` WRITE;
/*!40000 ALTER TABLE `jh_ditui_tixian` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_ditui_tixian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_hongbao`
--

DROP TABLE IF EXISTS `jh_hongbao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_hongbao` (
  `hongbao_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` enum('waimai','all','paotui') DEFAULT 'waimai',
  `title` varchar(80) NOT NULL,
  `min_amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `type` tinyint(1) DEFAULT '0',
  `uid` int(10) DEFAULT '0',
  `hongbao_sn` char(8) NOT NULL DEFAULT '',
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `order_id` int(10) DEFAULT '0',
  `used_ip` varchar(15) DEFAULT '',
  `used_time` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(11) DEFAULT '0',
  `limit_stime` varchar(20) DEFAULT '',
  `limit_ltime` varchar(20) DEFAULT '',
  `cate_id` int(10) DEFAULT '0',
  PRIMARY KEY (`hongbao_id`),
  KEY `uid` (`uid`,`order_id`),
  KEY `stime` (`stime`,`ltime`),
  KEY `hongbao_sn` (`hongbao_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_hongbao`
--

LOCK TABLES `jh_hongbao` WRITE;
/*!40000 ALTER TABLE `jh_hongbao` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_hongbao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_hongbao_huodong`
--

DROP TABLE IF EXISTS `jh_hongbao_huodong`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_hongbao_huodong` (
  `huodong_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) DEFAULT NULL,
  `intro` varchar(255) DEFAULT NULL,
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `times` text,
  `weeks` varchar(30) DEFAULT NULL,
  `limit` smallint(6) DEFAULT '0',
  `config` text,
  `status` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  `clientip` varchar(15) DEFAULT NULL,
  `background` varchar(150) DEFAULT '' COMMENT '弹框背景图',
  `background_color` varchar(6) DEFAULT NULL COMMENT '背景色',
  PRIMARY KEY (`huodong_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_hongbao_huodong`
--

LOCK TABLES `jh_hongbao_huodong` WRITE;
/*!40000 ALTER TABLE `jh_hongbao_huodong` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_hongbao_huodong` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_hongbao_huodong_log`
--

DROP TABLE IF EXISTS `jh_hongbao_huodong_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_hongbao_huodong_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `huodong_id` int(10) DEFAULT '0',
  `uid` int(10) DEFAULT '0',
  `day` int(10) DEFAULT '0',
  `clientip` varchar(20) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `day` (`day`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_hongbao_huodong_log`
--

LOCK TABLES `jh_hongbao_huodong_log` WRITE;
/*!40000 ALTER TABLE `jh_hongbao_huodong_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_hongbao_huodong_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_hongbao_linqu_log`
--

DROP TABLE IF EXISTS `jh_hongbao_linqu_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_hongbao_linqu_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hongbao_id` int(10) DEFAULT '0',
  `min_amount` decimal(8,2) DEFAULT '0.00',
  `amount` decimal(8,2) DEFAULT '0.00',
  `uid` int(10) DEFAULT '0',
  `day` int(10) DEFAULT '0',
  `clientip` varchar(20) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  `huodong_id` int(10) DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `day` (`day`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_hongbao_linqu_log`
--

LOCK TABLES `jh_hongbao_linqu_log` WRITE;
/*!40000 ALTER TABLE `jh_hongbao_linqu_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_hongbao_linqu_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_hongbao_log`
--

DROP TABLE IF EXISTS `jh_hongbao_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_hongbao_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hongbao_id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_hongbao_log`
--

LOCK TABLES `jh_hongbao_log` WRITE;
/*!40000 ALTER TABLE `jh_hongbao_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_hongbao_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_hongbao_order_log`
--

DROP TABLE IF EXISTS `jh_hongbao_order_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_hongbao_order_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT '0',
  `uid` int(10) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `min_amount` decimal(8,2) DEFAULT '0.00',
  `amount` decimal(8,2) DEFAULT '0.00',
  `day` tinyint(1) DEFAULT '0',
  `face` varchar(1024) DEFAULT '',
  `nickname` varchar(32) DEFAULT '',
  `wx_openid` varchar(255) DEFAULT '',
  `wx_unionid` varchar(255) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_hongbao_order_log`
--

LOCK TABLES `jh_hongbao_order_log` WRITE;
/*!40000 ALTER TABLE `jh_hongbao_order_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_hongbao_order_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_intracity_bills`
--

DROP TABLE IF EXISTS `jh_intracity_bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_intracity_bills` (
  `bills_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bills_sn` int(10) DEFAULT '0',
  `shop_id` int(10) DEFAULT '0',
  `amount` decimal(8,2) DEFAULT '0.00',
  `dateline` int(10) DEFAULT NULL,
  `extend` text,
  `group_id` int(10) DEFAULT '0',
  PRIMARY KEY (`bills_id`),
  UNIQUE KEY `bills_shop_id` (`bills_sn`,`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_intracity_bills`
--

LOCK TABLES `jh_intracity_bills` WRITE;
/*!40000 ALTER TABLE `jh_intracity_bills` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_intracity_bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_intracity_bills_log`
--

DROP TABLE IF EXISTS `jh_intracity_bills_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_intracity_bills_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bills_id` int(10) DEFAULT '0',
  `shop_id` int(10) DEFAULT '0',
  `order_id` int(10) DEFAULT '0',
  `amount` decimal(8,2) DEFAULT '0.00',
  `dateline` int(10) DEFAULT '0',
  `group_id` int(10) DEFAULT '0',
  `staff_id` int(10) DEFAULT '0',
  `extend` text,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_intracity_bills_log`
--

LOCK TABLES `jh_intracity_bills_log` WRITE;
/*!40000 ALTER TABLE `jh_intracity_bills_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_intracity_bills_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_jifen_bills`
--

DROP TABLE IF EXISTS `jh_jifen_bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_jifen_bills` (
  `bills_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(8,2) DEFAULT '0.00',
  `jifen` decimal(8,2) DEFAULT NULL,
  `bills_sn` int(8) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `fee` decimal(10,0) DEFAULT '0',
  PRIMARY KEY (`bills_id`),
  UNIQUE KEY `bills_sn` (`bills_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_jifen_bills`
--

LOCK TABLES `jh_jifen_bills` WRITE;
/*!40000 ALTER TABLE `jh_jifen_bills` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_jifen_bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_jifen_bills_log`
--

DROP TABLE IF EXISTS `jh_jifen_bills_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_jifen_bills_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bills_id` int(10) DEFAULT NULL,
  `fee` decimal(8,2) DEFAULT '0.00',
  `jifen` decimal(8,2) DEFAULT '0.00',
  `uid` int(10) DEFAULT '0',
  `bills_sn` int(8) DEFAULT NULL,
  `dateline` int(10) DEFAULT '0',
  `order_id` int(10) DEFAULT '0',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_jifen_bills_log`
--

LOCK TABLES `jh_jifen_bills_log` WRITE;
/*!40000 ALTER TABLE `jh_jifen_bills_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_jifen_bills_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_jpush_device`
--

DROP TABLE IF EXISTS `jh_jpush_device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_jpush_device` (
  `device_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) DEFAULT '0',
  `staff_id` mediumint(8) DEFAULT '0',
  `shop_id` mediumint(8) DEFAULT '0',
  `from` enum('member','staff','shop') DEFAULT NULL,
  `platform` enum('ios','android') DEFAULT 'android',
  `register_id` varchar(64) DEFAULT '',
  `tag_ids` varchar(150) DEFAULT '',
  `type` enum('alipush','jpush') DEFAULT 'jpush',
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_jpush_device`
--

LOCK TABLES `jh_jpush_device` WRITE;
/*!40000 ALTER TABLE `jh_jpush_device` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_jpush_device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_jpush_log`
--

DROP TABLE IF EXISTS `jh_jpush_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_jpush_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` enum('member','staff','shop') DEFAULT NULL,
  `platform` enum('all','android','ios') DEFAULT 'all',
  `tag` varchar(50) DEFAULT '',
  `alias` varchar(50) DEFAULT '',
  `device_id` int(10) DEFAULT '0',
  `register_id` varchar(64) DEFAULT '',
  `content` varchar(1024) DEFAULT '',
  `status` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT NULL,
  `dateline` int(10) DEFAULT '0',
  `push_time` int(10) DEFAULT '0',
  `link_title` varchar(255) DEFAULT '',
  `link_url` varchar(255) DEFAULT '',
  `type` enum('alipush','jpush') DEFAULT 'jpush',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_jpush_log`
--

LOCK TABLES `jh_jpush_log` WRITE;
/*!40000 ALTER TABLE `jh_jpush_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_jpush_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_jpush_tag`
--

DROP TABLE IF EXISTS `jh_jpush_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_jpush_tag` (
  `tag_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) DEFAULT '',
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_jpush_tag`
--

LOCK TABLES `jh_jpush_tag` WRITE;
/*!40000 ALTER TABLE `jh_jpush_tag` DISABLE KEYS */;
INSERT INTO `jh_jpush_tag` VALUES (1,'default');
/*!40000 ALTER TABLE `jh_jpush_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_links`
--

DROP TABLE IF EXISTS `jh_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_links` (
  `link_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT '',
  `link` varchar(150) DEFAULT '',
  `logo` varchar(150) DEFAULT '',
  `desc` varchar(512) DEFAULT '',
  `city_id` smallint(5) DEFAULT '0',
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_links`
--

LOCK TABLES `jh_links` WRITE;
/*!40000 ALTER TABLE `jh_links` DISABLE KEYS */;
INSERT INTO `jh_links` VALUES (37,'江湖科技','www.ijh.cc','','',0,1,1,1481097516);
/*!40000 ALTER TABLE `jh_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_mall_cate`
--

DROP TABLE IF EXISTS `jh_mall_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_mall_cate` (
  `cate_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(6) DEFAULT '0',
  `title` varchar(30) DEFAULT '',
  `icon` varchar(150) DEFAULT '',
  `orderby` smallint(6) DEFAULT '50',
  PRIMARY KEY (`cate_id`),
  KEY `orderby` (`orderby`,`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_mall_cate`
--

LOCK TABLES `jh_mall_cate` WRITE;
/*!40000 ALTER TABLE `jh_mall_cate` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_mall_cate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_mall_order`
--

DROP TABLE IF EXISTS `jh_mall_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_mall_order` (
  `order_id` int(10) unsigned NOT NULL,
  `product_jifen` mediumint(8) DEFAULT '0',
  `product_price` decimal(10,2) DEFAULT '0.00',
  `product_number` smallint(6) DEFAULT '0',
  `freight` decimal(8,2) DEFAULT '0.00',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_mall_order`
--

LOCK TABLES `jh_mall_order` WRITE;
/*!40000 ALTER TABLE `jh_mall_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_mall_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_mall_order_product`
--

DROP TABLE IF EXISTS `jh_mall_order_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_mall_order_product` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) DEFAULT '0',
  `product_id` mediumint(8) DEFAULT '0',
  `product_name` varchar(80) DEFAULT '',
  `product_jifen` mediumint(8) DEFAULT '0',
  `product_price` decimal(10,2) DEFAULT '0.00',
  `product_number` smallint(6) DEFAULT '0',
  `photo` varchar(255) DEFAULT '',
  PRIMARY KEY (`pid`),
  KEY `Index1` (`order_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_mall_order_product`
--

LOCK TABLES `jh_mall_order_product` WRITE;
/*!40000 ALTER TABLE `jh_mall_order_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_mall_order_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_mall_product`
--

DROP TABLE IF EXISTS `jh_mall_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_mall_product` (
  `product_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` smallint(6) DEFAULT '0',
  `title` varchar(80) DEFAULT '',
  `photo` varchar(150) DEFAULT '',
  `jifen` mediumint(8) DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `freight` decimal(8,2) DEFAULT '0.00',
  `info` mediumtext,
  `views` mediumint(8) DEFAULT '0',
  `sales` mediumint(8) DEFAULT '0',
  `sku` int(10) DEFAULT '0',
  `orderby` smallint(6) DEFAULT '50',
  `closed` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`product_id`),
  KEY `cate_id` (`cate_id`,`orderby`,`closed`),
  KEY `jifen` (`jifen`,`views`,`sales`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_mall_product`
--

LOCK TABLES `jh_mall_product` WRITE;
/*!40000 ALTER TABLE `jh_mall_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_mall_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_member`
--

DROP TABLE IF EXISTS `jh_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_member` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` varchar(15) NOT NULL,
  `passwd` char(32) DEFAULT '',
  `paypasswd` char(32) DEFAULT '',
  `nickname` varchar(30) DEFAULT '',
  `sex` tinyint(1) DEFAULT '0',
  `money` decimal(10,2) DEFAULT '0.00',
  `coin` int(10) DEFAULT '0',
  `total_money` decimal(10,2) DEFAULT '0.00',
  `orders` mediumint(8) DEFAULT '0',
  `jifen` int(10) DEFAULT '0',
  `face` varchar(150) DEFAULT '',
  `wx_openid` varchar(255) DEFAULT '',
  `wx_unionid` varchar(255) DEFAULT '',
  `loginip` varchar(15) DEFAULT '',
  `lastlogin` int(10) DEFAULT '0',
  `pmid` char(9) DEFAULT '',
  `closed` tinyint(1) DEFAULT '0',
  `regip` varchar(15) DEFAULT '',
  `tuisong` tinyint(1) DEFAULT '1',
  `dateline` int(10) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `register_from` enum('web','wxapp','android','ios') DEFAULT 'web',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `mobile` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_member`
--

LOCK TABLES `jh_member` WRITE;
/*!40000 ALTER TABLE `jh_member` DISABLE KEYS */;
INSERT INTO `jh_member` VALUES (1,'18621884811','46f94c8de14fb36680850768ff1b7f2a','','风来西林',0,0.00,0,0.00,0,0,'','oH44_1Wk9xW0tthmSfZy4myO7wKw','','113.16.66.136',1542901015,'',0,'113.16.66.136',1,1542900988,0,'web'),(2,'0123456789','cd2534eda876e14e617ace680a3a4796','cd2534eda876e14e617ace680a3a4796','%E7%80%9A',0,0.00,0,0.00,0,0,'face/C81/C81E728D9D4C2F636F067F89CC14862C.jpg','oH44_1S423M_qVbNU2CmYmwWZZmE','','183.3.255.32',1543485922,'',0,'183.3.255.32',1,1543485922,0,'wxapp'),(3,'3','216577861937ea223e27c8c3ad7150eb','216577861937ea223e27c8c3ad7150eb','%E7%8E%8B%E6%83%9F',0,0.00,0,0.00,0,0,'face/ECC/ECCBC87E4B5CE2FE28308FD9F2A7BAF3.jpg','oH44_1QKBsC1ziDz_y80Oq6FfyBQ','','183.3.226.234',1543490689,'',0,'183.3.226.234',1,1543490689,0,'wxapp'),(4,'4','42f0dd43d052a7c7815a4f14448ebc9e','42f0dd43d052a7c7815a4f14448ebc9e','%E4%B8%AD%E8%B6%8A%E4%B9%8B%E5',0,0.00,0,0.00,0,0,'face/A87/A87FF679A2F3E71D9181A67B7542122C.jpg','oH44_1SqVzTN58R20lKlebqEuTtY','','14.162.174.13',1543565103,'',0,'14.162.174.13',1,1543565103,0,'wxapp'),(5,'0933898884','265b6816d7dd416a2878ed8f89ca4ad8','265b6816d7dd416a2878ed8f89ca4ad8','%E5%BD%A9%E8%99%B9',0,0.00,0,0.00,0,0,'face/E4D/E4DA3B7FBBCE2345D7772B0674A318D5.jpg','oH44_1WWN1GDuTPF4DEkMyTc6ULc','','222.252.37.109',1544157766,'',0,'222.252.37.109',1,1544157766,0,'wxapp');
/*!40000 ALTER TABLE `jh_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_member_addr`
--

DROP TABLE IF EXISTS `jh_member_addr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_member_addr` (
  `addr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) DEFAULT '0',
  `contact` varchar(30) DEFAULT '',
  `mobile` varchar(15) DEFAULT '',
  `addr` varchar(255) DEFAULT '',
  `house` varchar(150) DEFAULT '',
  `lat` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `lng` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  `is_default` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`addr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_member_addr`
--

LOCK TABLES `jh_member_addr` WRITE;
/*!40000 ALTER TABLE `jh_member_addr` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_member_addr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_member_collect`
--

DROP TABLE IF EXISTS `jh_member_collect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_member_collect` (
  `collect_id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) DEFAULT '0',
  `type` enum('staff','weidian','waimai','weidian_product','tuan_product','tuan') DEFAULT 'waimai',
  `can_id` int(11) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`collect_id`),
  UNIQUE KEY `collect_id` (`collect_id`),
  KEY `can_id` (`can_id`,`uid`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_member_collect`
--

LOCK TABLES `jh_member_collect` WRITE;
/*!40000 ALTER TABLE `jh_member_collect` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_member_collect` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_member_coupon`
--

DROP TABLE IF EXISTS `jh_member_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_member_coupon` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` int(10) NOT NULL DEFAULT '0',
  `uid` mediumint(8) NOT NULL DEFAULT '0',
  `use_time` int(10) DEFAULT '0',
  `order_id` int(10) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `order_amount` decimal(10,2) DEFAULT '0.00',
  `coupon_amount` decimal(8,2) DEFAULT '0.00',
  `shop_id` int(10) DEFAULT '0',
  `mendian_id` varchar(32) DEFAULT '0',
  `title` varchar(32) DEFAULT '',
  PRIMARY KEY (`cid`,`coupon_id`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_member_coupon`
--

LOCK TABLES `jh_member_coupon` WRITE;
/*!40000 ALTER TABLE `jh_member_coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_member_coupon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_member_feedback`
--

DROP TABLE IF EXISTS `jh_member_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_member_feedback` (
  `fid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `content` varchar(255) DEFAULT NULL,
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_member_feedback`
--

LOCK TABLES `jh_member_feedback` WRITE;
/*!40000 ALTER TABLE `jh_member_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_member_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_member_help`
--

DROP TABLE IF EXISTS `jh_member_help`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_member_help` (
  `help_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT '',
  `details` text,
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`help_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_member_help`
--

LOCK TABLES `jh_member_help` WRITE;
/*!40000 ALTER TABLE `jh_member_help` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_member_help` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_member_invite`
--

DROP TABLE IF EXISTS `jh_member_invite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_member_invite` (
  `invite_uid` mediumint(8) NOT NULL DEFAULT '0',
  `uid` mediumint(8) DEFAULT '0',
  `mobile` char(11) DEFAULT '',
  `money` decimal(6,2) DEFAULT '0.00',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`invite_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_member_invite`
--

LOCK TABLES `jh_member_invite` WRITE;
/*!40000 ALTER TABLE `jh_member_invite` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_member_invite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_member_log`
--

DROP TABLE IF EXISTS `jh_member_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_member_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) DEFAULT '0',
  `type` enum('money','coin','jifen') DEFAULT NULL,
  `number` float DEFAULT '0',
  `intro` varchar(255) DEFAULT '',
  `admin` varchar(80) DEFAULT '',
  `day` int(8) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`log_id`),
  KEY `uid` (`uid`,`type`),
  KEY `day` (`day`,`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_member_log`
--

LOCK TABLES `jh_member_log` WRITE;
/*!40000 ALTER TABLE `jh_member_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_member_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_member_message`
--

DROP TABLE IF EXISTS `jh_member_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_member_message` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT '0',
  `title` varchar(80) DEFAULT NULL,
  `content` varchar(512) DEFAULT '',
  `type` tinyint(1) DEFAULT '0',
  `order_id` int(10) DEFAULT '0',
  `is_read` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT NULL,
  `can_id` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`message_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_member_message`
--

LOCK TABLES `jh_member_message` WRITE;
/*!40000 ALTER TABLE `jh_member_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_member_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_member_msg`
--

DROP TABLE IF EXISTS `jh_member_msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_member_msg` (
  `msg_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT '0',
  `title` varchar(80) DEFAULT '',
  `content` varchar(512) DEFAULT '',
  `is_read` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_member_msg`
--

LOCK TABLES `jh_member_msg` WRITE;
/*!40000 ALTER TABLE `jh_member_msg` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_member_msg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_member_subscribe`
--

DROP TABLE IF EXISTS `jh_member_subscribe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_member_subscribe` (
  `uid` int(10) NOT NULL,
  `wx_openid` varchar(255) NOT NULL,
  `wx_unionid` varchar(255) DEFAULT NULL,
  `subscribe` tinyint(1) DEFAULT '0',
  `subscribe_time` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_member_subscribe`
--

LOCK TABLES `jh_member_subscribe` WRITE;
/*!40000 ALTER TABLE `jh_member_subscribe` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_member_subscribe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_member_tixian`
--

DROP TABLE IF EXISTS `jh_member_tixian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_member_tixian` (
  `tixian_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT '0',
  `money` decimal(8,2) DEFAULT '0.00',
  `intro` varchar(255) DEFAULT '',
  `status` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`tixian_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_member_tixian`
--

LOCK TABLES `jh_member_tixian` WRITE;
/*!40000 ALTER TABLE `jh_member_tixian` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_member_tixian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_order`
--

DROP TABLE IF EXISTS `jh_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` smallint(6) DEFAULT '0',
  `shop_id` mediumint(8) DEFAULT '0',
  `staff_id` mediumint(8) DEFAULT '0',
  `uid` mediumint(8) DEFAULT '0',
  `from` enum('tuan','waimai','paotui','maidan','weixiu','house','mall','weidian','reward','qiang','other') DEFAULT NULL,
  `order_status` tinyint(1) DEFAULT '0',
  `online_pay` tinyint(1) DEFAULT '1',
  `pay_status` tinyint(1) DEFAULT '0',
  `trade_no` int(10) DEFAULT '0',
  `total_price` decimal(10,2) DEFAULT '0.00',
  `hongbao_id` int(11) DEFAULT '0',
  `hongbao` decimal(8,2) DEFAULT '0.00',
  `order_youhui` decimal(8,2) DEFAULT '0.00',
  `first_youhui` decimal(8,2) DEFAULT '0.00',
  `money` decimal(10,2) DEFAULT '0.00',
  `amount` decimal(10,2) DEFAULT '0.00',
  `o_lng` int(10) DEFAULT '0',
  `o_lat` int(10) DEFAULT '0',
  `contact` varchar(15) DEFAULT '',
  `mobile` char(11) DEFAULT '',
  `addr` varchar(150) DEFAULT '',
  `house` varchar(150) DEFAULT '',
  `lng` int(10) DEFAULT '0',
  `lat` int(10) DEFAULT '0',
  `first_order` tinyint(1) DEFAULT '0',
  `first_shop_order` tinyint(1) DEFAULT '0',
  `day` int(8) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `intro` varchar(255) DEFAULT NULL,
  `order_from` enum('weixin','ios','android','wap','wxapp','www') DEFAULT 'weixin',
  `wx_openid` varchar(64) DEFAULT '',
  `pay_code` varchar(10) DEFAULT '',
  `pay_time` int(10) DEFAULT '0',
  `pei_time` int(10) DEFAULT '0',
  `pei_type` tinyint(1) DEFAULT '0',
  `pei_amount` decimal(8,2) DEFAULT '0.00',
  `dateline` int(10) DEFAULT '0',
  `jd_time` int(10) unsigned DEFAULT '0',
  `comment_status` tinyint(1) unsigned DEFAULT '0',
  `lasttime` int(10) unsigned DEFAULT '0',
  `cui_time` int(10) DEFAULT '0',
  `closed` tinyint(1) unsigned DEFAULT '0',
  `coupon_id` int(10) DEFAULT '0',
  `coupon` decimal(8,2) DEFAULT '0.00',
  `express_name` varchar(32) DEFAULT '',
  `express` varchar(32) DEFAULT '',
  `refund_status` tinyint(1) DEFAULT '0',
  `change_price` decimal(8,2) DEFAULT '0.00',
  `day_num` smallint(6) DEFAULT '1',
  `group_id` int(10) DEFAULT '0',
  `tmp_staff_id` int(10) DEFAULT '0',
  `tmp_ltime` int(10) DEFAULT '0',
  `print_id` int(10) DEFAULT '0',
  `jifen_status` tinyint(1) DEFAULT '0',
  `jifen_cfg` mediumtext,
  `discount_youhui` decimal(8,2) DEFAULT '0.00',
  `formid` varchar(32) DEFAULT NULL,
  `prepayid` varchar(64) DEFAULT NULL,
  `expect_time` int(10) DEFAULT '0',
  `card_id` int(10) DEFAULT '0',
  `card_amount` decimal(8,2) DEFAULT '0.00',
  `peicard_id` int(10) DEFAULT '0',
  `peicard_youhui` decimal(8,2) DEFAULT '0.00',
  `huangou_youhui` decimal(8,2) DEFAULT '0.00',
  `is_baskets` tinyint(1) DEFAULT '0',
  `member_orders` smallint(6) DEFAULT '0' COMMENT '用户下单次数（针对商户）',
  PRIMARY KEY (`order_id`),
  KEY `shop_id` (`shop_id`),
  KEY `dateline` (`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_order`
--

LOCK TABLES `jh_order` WRITE;
/*!40000 ALTER TABLE `jh_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_order_cancellog`
--

DROP TABLE IF EXISTS `jh_order_cancellog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_order_cancellog` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` enum('member','satff') NOT NULL,
  `order_id` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL,
  `staff_id` mediumint(8) NOT NULL DEFAULT '0',
  `shop_id` mediumint(8) DEFAULT NULL,
  `group_id` mediumint(8) DEFAULT NULL,
  `reason` varchar(255) NOT NULL,
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_order_cancellog`
--

LOCK TABLES `jh_order_cancellog` WRITE;
/*!40000 ALTER TABLE `jh_order_cancellog` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_order_cancellog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_order_complaint`
--

DROP TABLE IF EXISTS `jh_order_complaint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_order_complaint` (
  `complaint_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT NULL,
  `uid` mediumint(8) DEFAULT '0',
  `shop_id` mediumint(8) DEFAULT '0',
  `staff_id` mediumint(8) DEFAULT '0',
  `title` varchar(80) DEFAULT '',
  `content` varchar(255) DEFAULT '',
  `reply` varchar(255) DEFAULT '',
  `reply_time` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`complaint_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_order_complaint`
--

LOCK TABLES `jh_order_complaint` WRITE;
/*!40000 ALTER TABLE `jh_order_complaint` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_order_complaint` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_order_cuilog`
--

DROP TABLE IF EXISTS `jh_order_cuilog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_order_cuilog` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) DEFAULT '0',
  `shop_id` mediumint(8) unsigned DEFAULT '0',
  `staff_id` mediumint(8) DEFAULT '0',
  `order_id` int(10) unsigned DEFAULT '0',
  `reply` varchar(255) DEFAULT '',
  `reply_time` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_order_cuilog`
--

LOCK TABLES `jh_order_cuilog` WRITE;
/*!40000 ALTER TABLE `jh_order_cuilog` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_order_cuilog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_order_log`
--

DROP TABLE IF EXISTS `jh_order_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_order_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `log` varchar(255) DEFAULT '',
  `from` enum('shop','admin','staff','member','system','payment') DEFAULT 'member',
  `intro` varchar(255) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_order_log`
--

LOCK TABLES `jh_order_log` WRITE;
/*!40000 ALTER TABLE `jh_order_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_order_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_order_photo`
--

DROP TABLE IF EXISTS `jh_order_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_order_photo` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT '0',
  `photo` varchar(150) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_order_photo`
--

LOCK TABLES `jh_order_photo` WRITE;
/*!40000 ALTER TABLE `jh_order_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_order_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_order_time`
--

DROP TABLE IF EXISTS `jh_order_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_order_time` (
  `order_id` int(10) NOT NULL DEFAULT '0',
  `create_time` int(10) DEFAULT '0',
  `pay_time` int(10) DEFAULT '0',
  `shop_jiedan_time` int(10) DEFAULT '0',
  `staff_jiedan_time` int(10) DEFAULT '0',
  `staff_start_time` int(10) DEFAULT '0',
  `staff_compltet_time` int(10) DEFAULT '0',
  `order_compltet_time` int(10) DEFAULT '0',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_order_time`
--

LOCK TABLES `jh_order_time` WRITE;
/*!40000 ALTER TABLE `jh_order_time` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_order_time` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_order_track`
--

DROP TABLE IF EXISTS `jh_order_track`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_order_track` (
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `diatance` int(10) DEFAULT '0',
  `day` int(10) DEFAULT '0',
  `data` text,
  `dateline` int(10) DEFAULT '0',
  `staff_id` int(10) DEFAULT '0',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_order_track`
--

LOCK TABLES `jh_order_track` WRITE;
/*!40000 ALTER TABLE `jh_order_track` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_order_track` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_order_voice`
--

DROP TABLE IF EXISTS `jh_order_voice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_order_voice` (
  `voice_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT '0',
  `voice` varchar(255) DEFAULT '',
  `voice_time` tinyint(3) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`voice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_order_voice`
--

LOCK TABLES `jh_order_voice` WRITE;
/*!40000 ALTER TABLE `jh_order_voice` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_order_voice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_other_order`
--

DROP TABLE IF EXISTS `jh_other_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_other_order` (
  `order_id` int(10) unsigned NOT NULL,
  `type` enum('own','meituan','ele') DEFAULT NULL,
  `shop_id` int(10) DEFAULT '0',
  `price` decimal(8,2) DEFAULT '0.00',
  `product` text,
  `lng` int(10) DEFAULT '0',
  `lat` int(10) DEFAULT '0',
  `addr` varchar(255) DEFAULT '',
  `contact` varchar(30) DEFAULT '',
  `mobile` varchar(20) DEFAULT '',
  `ext_shop_id` int(10) DEFAULT '0',
  `ext_order_id` varchar(100) DEFAULT '0' COMMENT '鍟嗗煄璁㈠崟id',
  `extend` text,
  `dateline` int(10) DEFAULT '0',
  `p_order_id` int(10) DEFAULT '0',
  PRIMARY KEY (`order_id`),
  KEY `shop_id` (`shop_id`),
  KEY `p_order_id` (`p_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_other_order`
--

LOCK TABLES `jh_other_order` WRITE;
/*!40000 ALTER TABLE `jh_other_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_other_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_paotui_cate`
--

DROP TABLE IF EXISTS `jh_paotui_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_paotui_cate` (
  `cate_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` mediumint(8) DEFAULT '0',
  `from` enum('song','mai') DEFAULT 'mai',
  `title` varchar(100) DEFAULT '',
  `desc` varchar(100) DEFAULT '',
  `config` text,
  `dateline` int(10) DEFAULT '0',
  `photo` varchar(255) DEFAULT '',
  `orderby` int(10) DEFAULT '0',
  PRIMARY KEY (`cate_id`),
  KEY `city_id` (`city_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_paotui_cate`
--

LOCK TABLES `jh_paotui_cate` WRITE;
/*!40000 ALTER TABLE `jh_paotui_cate` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_paotui_cate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_paotui_order`
--

DROP TABLE IF EXISTS `jh_paotui_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_paotui_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` enum('song','mai') DEFAULT 'mai',
  `o_lng` int(10) DEFAULT '0',
  `o_lat` int(10) DEFAULT '0',
  `lng` int(10) DEFAULT '0',
  `lat` int(10) DEFAULT '0',
  `o_addr` varchar(255) DEFAULT '',
  `addr` varchar(255) DEFAULT '',
  `amount` decimal(8,2) DEFAULT '0.00',
  `tip` decimal(8,2) DEFAULT '0.00',
  `product` text,
  `dateline` int(10) DEFAULT '0',
  `contact` varchar(30) DEFAULT '',
  `mobile` varchar(20) DEFAULT '',
  `o_contact` varchar(30) DEFAULT '',
  `o_mobile` varchar(20) DEFAULT '',
  `yuji_price` decimal(8,2) DEFAULT '0.00',
  `price` varchar(50) DEFAULT '',
  `weight` decimal(8,2) DEFAULT '0.00',
  `type` tinyint(1) DEFAULT '0',
  `ext_shop_id` int(10) DEFAULT '0',
  `extend` text,
  `ext_order_id` int(10) DEFAULT '0',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_paotui_order`
--

LOCK TABLES `jh_paotui_order` WRITE;
/*!40000 ALTER TABLE `jh_paotui_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_paotui_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_payment`
--

DROP TABLE IF EXISTS `jh_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_payment` (
  `payment_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `payment` varchar(20) DEFAULT '',
  `title` varchar(100) DEFAULT '',
  `logo` varchar(150) DEFAULT '',
  `config` mediumtext,
  `status` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_payment`
--

LOCK TABLES `jh_payment` WRITE;
/*!40000 ALTER TABLE `jh_payment` DISABLE KEYS */;
INSERT INTO `jh_payment` VALUES (4,'money','余额支付','photo/201511/20151125_8AB5E4BF5DE920E71B08E1E6D3FFEEE9.jpg',NULL,1,1448354756),(5,'alipay','支付宝','photo/201708/20170825_37912AC6AD5A0EFB4FF3100871EDD627.png','a:10:{s:11:\"refund_type\";s:1:\"0\";s:15:\"alipay_rsa_type\";s:4:\"RSA2\";s:14:\"alipay_account\";s:0:\"\";s:14:\"alipay_partner\";s:0:\"\";s:10:\"alipay_key\";s:0:\"\";s:18:\"alipay_rsa_private\";s:0:\"\";s:17:\"alipay_rsa_public\";s:0:\"\";s:12:\"alipay_appid\";s:0:\"\";s:16:\"open_rsa_private\";s:0:\"\";s:15:\"open_rsa_public\";s:0:\"\";}',1,1502189143),(6,'wxpay','微信支付','photo/201708/20170825_03CBC5F28C0C08AF4B83751B8EBDC1F2.png','a:20:{s:11:\"refund_type\";s:1:\"0\";s:10:\"wxpay_mweb\";s:1:\"0\";s:5:\"appid\";s:0:\"\";s:9:\"appsecret\";s:0:\"\";s:6:\"mch_id\";s:0:\"\";s:3:\"key\";s:0:\"\";s:11:\"mp_cert_pem\";s:0:\"\";s:10:\"mp_key_pem\";s:0:\"\";s:9:\"app_appid\";s:0:\"\";s:13:\"app_appsecret\";s:0:\"\";s:10:\"app_mch_id\";s:0:\"\";s:7:\"app_key\";s:0:\"\";s:12:\"app_cert_pem\";s:0:\"\";s:11:\"app_key_pem\";s:0:\"\";s:11:\"wxapp_appid\";s:0:\"\";s:15:\"wxapp_appsecret\";s:0:\"\";s:12:\"wxapp_mch_id\";s:0:\"\";s:9:\"wxapp_key\";s:0:\"\";s:14:\"wxapp_cert_pem\";s:0:\"\";s:13:\"wxapp_key_pem\";s:0:\"\";}',1,1502189153);
/*!40000 ALTER TABLE `jh_payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_payment_log`
--

DROP TABLE IF EXISTS `jh_payment_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_payment_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `staff_id` mediumint(8) DEFAULT '0',
  `uid` mediumint(8) DEFAULT '0',
  `from` enum('money','order','paotui','cloud','coin','yzbill','deliver','peicard','cashier') DEFAULT NULL,
  `type` enum('pay','refund') DEFAULT 'pay',
  `payment` varchar(20) DEFAULT '',
  `trade_no` int(10) DEFAULT '0',
  `order_id` int(10) DEFAULT '0',
  `order_ids` varchar(256) DEFAULT '',
  `amount` decimal(10,2) DEFAULT '0.00',
  `trade_type` varchar(15) DEFAULT NULL,
  `payed` tinyint(1) DEFAULT '0',
  `payedip` varchar(15) DEFAULT '',
  `payedtime` int(10) DEFAULT '0',
  `pay_trade_no` varchar(50) DEFAULT '',
  `extra_pay` varchar(200) DEFAULT '',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `pay_level` tinyint(1) DEFAULT '0',
  `card_id` int(10) DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `trade_no` (`trade_no`),
  KEY `uid` (`uid`),
  KEY `from` (`from`,`payed`),
  KEY `shop_id` (`shop_id`),
  KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_payment_log`
--

LOCK TABLES `jh_payment_log` WRITE;
/*!40000 ALTER TABLE `jh_payment_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_payment_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_pei_bind`
--

DROP TABLE IF EXISTS `jh_pei_bind`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_pei_bind` (
  `bind_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) DEFAULT '0',
  `shop_id` int(10) DEFAULT '0',
  `addr` varchar(255) DEFAULT '0',
  `lng` int(11) DEFAULT '0',
  `lat` int(11) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `contact` varchar(100) DEFAULT '',
  `mobile` varchar(20) DEFAULT '',
  PRIMARY KEY (`bind_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_pei_bind`
--

LOCK TABLES `jh_pei_bind` WRITE;
/*!40000 ALTER TABLE `jh_pei_bind` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_pei_bind` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_pei_group`
--

DROP TABLE IF EXISTS `jh_pei_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_pei_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `province_id` int(10) DEFAULT '0',
  `city_id` int(10) DEFAULT '0',
  `group_name` varchar(100) DEFAULT '',
  `mobile` varchar(20) DEFAULT '',
  `passwd` varchar(32) DEFAULT '',
  `addr` varchar(100) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `overtime` int(10) DEFAULT '5',
  `contact` varchar(100) DEFAULT '',
  `face` varchar(255) DEFAULT '0',
  `lng` varchar(30) DEFAULT '',
  `lat` varchar(30) DEFAULT '',
  `polygon_point` text,
  `min_amount` decimal(8,2) DEFAULT '0.00',
  `min_pei` decimal(8,2) DEFAULT '0.00',
  `voice` tinyint(1) DEFAULT '0',
  `assign` tinyint(1) DEFAULT '0',
  `limit_order` int(10) DEFAULT '10',
  `is_used` tinyint(1) DEFAULT '0',
  `baseconfig` text,
  `timeconfig` text,
  `badweather` text,
  `timeout_config` text,
  `timeout_time` smallint(2) DEFAULT '0',
  `efence` text,
  `autopei_config` text,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_pei_group`
--

LOCK TABLES `jh_pei_group` WRITE;
/*!40000 ALTER TABLE `jh_pei_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_pei_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_peicard`
--

DROP TABLE IF EXISTS `jh_peicard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_peicard` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT '',
  `days` smallint(6) DEFAULT '0',
  `amount` decimal(8,2) DEFAULT '0.00',
  `limits` smallint(6) DEFAULT '0',
  `reduce` decimal(8,2) DEFAULT '0.00',
  `template` tinyint(2) DEFAULT '1',
  `photo` varchar(150) DEFAULT '',
  `orderby` smallint(6) DEFAULT '50',
  `closed` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_peicard`
--

LOCK TABLES `jh_peicard` WRITE;
/*!40000 ALTER TABLE `jh_peicard` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_peicard` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_peicard_log`
--

DROP TABLE IF EXISTS `jh_peicard_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_peicard_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) DEFAULT '0',
  `cid` int(10) DEFAULT '0',
  `order_id` int(10) DEFAULT '0',
  `money` decimal(8,2) DEFAULT '0.00',
  `day` int(8) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `uid` (`uid`),
  KEY `day` (`day`),
  KEY `dateline` (`dateline`),
  KEY `user_card_day` (`uid`,`cid`,`day`),
  KEY `cid` (`cid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_peicard_log`
--

LOCK TABLES `jh_peicard_log` WRITE;
/*!40000 ALTER TABLE `jh_peicard_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_peicard_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_peicard_member`
--

DROP TABLE IF EXISTS `jh_peicard_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_peicard_member` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_id` int(10) DEFAULT '0',
  `uid` int(10) DEFAULT '0',
  `title` varchar(128) DEFAULT '',
  `ltime` int(10) DEFAULT '0',
  `limits` smallint(6) DEFAULT '0',
  `reduce` decimal(8,2) DEFAULT '0.00',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`cid`),
  KEY `uid` (`uid`),
  KEY `ltime` (`ltime`),
  KEY `user_ltime` (`uid`,`ltime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_peicard_member`
--

LOCK TABLES `jh_peicard_member` WRITE;
/*!40000 ALTER TABLE `jh_peicard_member` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_peicard_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_push_log`
--

DROP TABLE IF EXISTS `jh_push_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_push_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('jpush','alipush') DEFAULT 'jpush',
  `title` varchar(256) DEFAULT '' COMMENT '推送标题',
  `content` varchar(1024) DEFAULT '',
  `params` text,
  `extras` text,
  `schedule` text,
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_push_log`
--

LOCK TABLES `jh_push_log` WRITE;
/*!40000 ALTER TABLE `jh_push_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_push_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_qiang`
--

DROP TABLE IF EXISTS `jh_qiang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_qiang` (
  `qiang_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` int(10) DEFAULT '0',
  `area_id` int(10) DEFAULT '0',
  `business_id` int(10) DEFAULT '0',
  `shop_id` mediumint(8) DEFAULT '0',
  `title` varchar(80) DEFAULT '',
  `photo` varchar(150) DEFAULT '',
  `price` decimal(8,2) DEFAULT '0.00',
  `discount_price` decimal(8,2) DEFAULT '0.00',
  `type` enum('goods','ticket') DEFAULT NULL,
  `is_tui` tinyint(1) unsigned DEFAULT '0',
  `pei_type` tinyint(1) unsigned DEFAULT '0',
  `is_yuyue` tinyint(1) unsigned DEFAULT '0',
  `is_limit` tinyint(1) unsigned DEFAULT '0',
  `limit_sku` mediumint(8) DEFAULT '0',
  `freight` decimal(8,2) DEFAULT '0.00',
  `sales` mediumint(8) DEFAULT '0',
  `bl` int(10) DEFAULT '0',
  `sku` mediumint(8) DEFAULT '0',
  `count_sku` mediumint(8) DEFAULT '0',
  `is_onsale` tinyint(1) unsigned DEFAULT '0',
  `qiang_ltime` varchar(20) DEFAULT '',
  `use_ltime` varchar(20) DEFAULT '',
  `info` text,
  `rules` text,
  `orderby` smallint(6) DEFAULT '50',
  `closed` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`qiang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_qiang`
--

LOCK TABLES `jh_qiang` WRITE;
/*!40000 ALTER TABLE `jh_qiang` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_qiang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_qiang_bills`
--

DROP TABLE IF EXISTS `jh_qiang_bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_qiang_bills` (
  `bills_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bills_sn` int(8) NOT NULL DEFAULT '0' COMMENT '1',
  `shop_id` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned DEFAULT '0',
  `amount` decimal(8,2) DEFAULT '0.00',
  `user_amount` decimal(8,2) DEFAULT '0.00',
  `fee` decimal(8,2) DEFAULT '0.00',
  `freight` decimal(8,2) DEFAULT '0.00',
  `dateline` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`bills_id`),
  UNIQUE KEY `shop_bills_sn` (`bills_sn`,`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_qiang_bills`
--

LOCK TABLES `jh_qiang_bills` WRITE;
/*!40000 ALTER TABLE `jh_qiang_bills` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_qiang_bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_qiang_bills_log`
--

DROP TABLE IF EXISTS `jh_qiang_bills_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_qiang_bills_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bills_id` int(10) unsigned NOT NULL,
  `bills_sn` int(8) DEFAULT '0',
  `shop_id` int(10) unsigned DEFAULT NULL,
  `bills_number` varchar(16) DEFAULT '',
  `status` tinyint(1) unsigned DEFAULT '0',
  `amount` decimal(8,2) DEFAULT '0.00',
  `count` int(10) DEFAULT '0',
  `type` varchar(10) DEFAULT '',
  `user_amount` decimal(8,2) DEFAULT '0.00',
  `fee` decimal(8,2) DEFAULT '0.00',
  `bl` decimal(8,2) DEFAULT '0.00',
  `freight` decimal(8,2) NOT NULL DEFAULT '0.00',
  `dateline` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_qiang_bills_log`
--

LOCK TABLES `jh_qiang_bills_log` WRITE;
/*!40000 ALTER TABLE `jh_qiang_bills_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_qiang_bills_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_qiang_comment`
--

DROP TABLE IF EXISTS `jh_qiang_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_qiang_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `qiang_id` mediumint(8) DEFAULT '0',
  `shop_id` mediumint(8) DEFAULT '0',
  `uid` mediumint(8) DEFAULT '0',
  `order_id` int(8) DEFAULT '0',
  `score` tinyint(1) DEFAULT '0',
  `content` varchar(1024) DEFAULT '',
  `have_photo` tinyint(1) DEFAULT '0',
  `reply` varchar(1024) DEFAULT '',
  `reply_ip` varchar(15) DEFAULT '',
  `reply_time` int(10) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `shop_id` (`qiang_id`,`uid`),
  KEY `order_id` (`order_id`,`score`,`reply_time`,`closed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_qiang_comment`
--

LOCK TABLES `jh_qiang_comment` WRITE;
/*!40000 ALTER TABLE `jh_qiang_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_qiang_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_qiang_comment_photo`
--

DROP TABLE IF EXISTS `jh_qiang_comment_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_qiang_comment_photo` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) DEFAULT '0',
  `photo` varchar(150) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`photo_id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_qiang_comment_photo`
--

LOCK TABLES `jh_qiang_comment_photo` WRITE;
/*!40000 ALTER TABLE `jh_qiang_comment_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_qiang_comment_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_qiang_order`
--

DROP TABLE IF EXISTS `jh_qiang_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_qiang_order` (
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `qiang_id` int(10) DEFAULT '0',
  `qiang_title` varchar(80) DEFAULT '',
  `qiang_price` decimal(10,2) DEFAULT '0.00',
  `qiang_discount_price` decimal(10,2) DEFAULT '0.00',
  `qiang_freight` decimal(10,2) DEFAULT '0.00',
  `qiang_number` smallint(6) DEFAULT '0',
  `photo` varchar(150) DEFAULT '',
  `notes` text,
  `is_ticket` tinyint(1) unsigned DEFAULT '0',
  `ticket_status` tinyint(1) DEFAULT '0',
  `number` varchar(15) DEFAULT '0',
  `bl` int(10) DEFAULT '0',
  `use_time` int(10) DEFAULT '0',
  `use_ltime` varchar(20) DEFAULT '',
  `info` text,
  `rules` text,
  `type` enum('goods','ticket') DEFAULT 'ticket',
  PRIMARY KEY (`order_id`),
  KEY `_index` (`number`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抢购订单';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_qiang_order`
--

LOCK TABLES `jh_qiang_order` WRITE;
/*!40000 ALTER TABLE `jh_qiang_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_qiang_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_session`
--

DROP TABLE IF EXISTS `jh_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_session` (
  `SSID` char(35) NOT NULL,
  `uid` mediumint(8) DEFAULT '0',
  `city_id` mediumint(8) DEFAULT '0',
  `ip` char(15) DEFAULT '0.0.0.0',
  `data` varchar(1024) DEFAULT NULL,
  `lastupdate` int(10) DEFAULT '0',
  PRIMARY KEY (`SSID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_session`
--

LOCK TABLES `jh_session` WRITE;
/*!40000 ALTER TABLE `jh_session` DISABLE KEYS */;
INSERT INTO `jh_session` VALUES ('f8dac4159d63fbfc24017c8e82e5d287',0,0,'113.16.66.136','VIMGCODE|s:4:\"6225\";',1542896999);
/*!40000 ALTER TABLE `jh_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop`
--

DROP TABLE IF EXISTS `jh_shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop` (
  `shop_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` smallint(6) DEFAULT '0',
  `city_id` smallint(6) DEFAULT '0',
  `title` varchar(30) DEFAULT '',
  `contact` varchar(15) DEFAULT '',
  `mobile` varchar(11) DEFAULT '',
  `info` varchar(512) DEFAULT '',
  `total_money` decimal(10,2) DEFAULT '0.00',
  `phone` varchar(15) DEFAULT '',
  `passwd` char(32) DEFAULT '',
  `money` decimal(10,2) DEFAULT '0.00',
  `thumb` varchar(255) DEFAULT '',
  `intro` varchar(512) DEFAULT '',
  `tixian_money` decimal(10,2) DEFAULT '0.00',
  `tixian_percent` tinyint(1) DEFAULT '100',
  `have_waimai` tinyint(1) DEFAULT '0',
  `have_tuan` tinyint(1) DEFAULT '0',
  `have_quan` tinyint(1) DEFAULT '0',
  `have_maidan` tinyint(1) DEFAULT '0',
  `have_paidui` tinyint(1) DEFAULT '0',
  `have_dingzuo` tinyint(1) DEFAULT '0',
  `have_diancan` tinyint(1) DEFAULT '0',
  `max_youhui` decimal(10,2) DEFAULT '0.00',
  `lat` int(10) DEFAULT '0',
  `lng` int(10) DEFAULT '0',
  `logo` varchar(150) DEFAULT '',
  `banner` varchar(150) DEFAULT '',
  `score` int(10) DEFAULT '0',
  `comments` mediumint(8) DEFAULT '0',
  `addr` varchar(150) DEFAULT '',
  `avg_amount` decimal(6,2) unsigned DEFAULT '0.00',
  `business_id` smallint(6) DEFAULT '0',
  `area_id` smallint(6) DEFAULT '0',
  `verify_name` tinyint(1) unsigned DEFAULT '0',
  `orderby` tinyint(1) DEFAULT '100',
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `have_weidian` tinyint(1) DEFAULT '0',
  `have_fenxiao` tinyint(1) DEFAULT '0',
  `fenxiao_type` tinyint(1) unsigned DEFAULT '0',
  `is_wifi` tinyint(1) unsigned DEFAULT '0',
  `is_cart` tinyint(1) unsigned DEFAULT '0',
  `refuse` varchar(255) DEFAULT '',
  `tuan_bl` int(10) DEFAULT '0',
  `maidan_bl` int(10) DEFAULT '0',
  `business_time` varchar(64) DEFAULT '',
  `pintuan_bl` int(10) DEFAULT '0',
  PRIMARY KEY (`shop_id`),
  KEY `__INDEX1` (`audit`,`closed`,`lat`,`lng`,`verify_name`,`orderby`),
  KEY `__INDEX2` (`city_id`,`business_id`,`area_id`),
  KEY `__INDEX3` (`have_waimai`,`have_tuan`,`have_quan`,`have_maidan`,`verify_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop`
--

LOCK TABLES `jh_shop` WRITE;
/*!40000 ALTER TABLE `jh_shop` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_account`
--

DROP TABLE IF EXISTS `jh_shop_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_account` (
  `shop_id` mediumint(8) NOT NULL DEFAULT '0',
  `account_type` varchar(80) DEFAULT NULL,
  `account_name` varchar(30) DEFAULT '',
  `account_number` varchar(100) DEFAULT '',
  `account_branch` varchar(100) DEFAULT '',
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_account`
--

LOCK TABLES `jh_shop_account` WRITE;
/*!40000 ALTER TABLE `jh_shop_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_album`
--

DROP TABLE IF EXISTS `jh_shop_album`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_album` (
  `album_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `name` varchar(30) DEFAULT NULL,
  `photo` varchar(150) DEFAULT '',
  `orderby` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`album_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_album`
--

LOCK TABLES `jh_shop_album` WRITE;
/*!40000 ALTER TABLE `jh_shop_album` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_album` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_album_photo`
--

DROP TABLE IF EXISTS `jh_shop_album_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_album_photo` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `title` varchar(64) DEFAULT '',
  `album_id` mediumint(8) DEFAULT '0',
  `photo` varchar(150) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_album_photo`
--

LOCK TABLES `jh_shop_album_photo` WRITE;
/*!40000 ALTER TABLE `jh_shop_album_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_album_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_cate`
--

DROP TABLE IF EXISTS `jh_shop_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_cate` (
  `cate_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(6) DEFAULT '0',
  `title` varchar(30) DEFAULT '',
  `icon` varchar(150) DEFAULT '',
  `photo` varchar(150) DEFAULT '',
  `orderby` smallint(6) DEFAULT '50',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_cate`
--

LOCK TABLES `jh_shop_cate` WRITE;
/*!40000 ALTER TABLE `jh_shop_cate` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_cate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_collect`
--

DROP TABLE IF EXISTS `jh_shop_collect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_collect` (
  `shop_id` mediumint(8) NOT NULL DEFAULT '0',
  `uid` mediumint(8) NOT NULL DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`uid`,`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_collect`
--

LOCK TABLES `jh_shop_collect` WRITE;
/*!40000 ALTER TABLE `jh_shop_collect` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_collect` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_comment`
--

DROP TABLE IF EXISTS `jh_shop_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `uid` mediumint(8) DEFAULT '0',
  `order_id` int(8) DEFAULT '0',
  `score` tinyint(1) DEFAULT '0',
  `score_fuwu` tinyint(1) DEFAULT '0',
  `score_kouwei` tinyint(1) DEFAULT '0',
  `content` varchar(1024) DEFAULT '',
  `pei_time` smallint(6) DEFAULT '30',
  `have_photo` tinyint(1) DEFAULT '0',
  `reply` varchar(1024) DEFAULT '',
  `reply_ip` varchar(15) DEFAULT '',
  `reply_time` int(10) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `u_mobile` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_comment`
--

LOCK TABLES `jh_shop_comment` WRITE;
/*!40000 ALTER TABLE `jh_shop_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_comment_photo`
--

DROP TABLE IF EXISTS `jh_shop_comment_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_comment_photo` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) DEFAULT '0',
  `photo` varchar(150) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_comment_photo`
--

LOCK TABLES `jh_shop_comment_photo` WRITE;
/*!40000 ALTER TABLE `jh_shop_comment_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_comment_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_coupon`
--

DROP TABLE IF EXISTS `jh_shop_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_coupon` (
  `coupon_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `order_amount` decimal(10,2) DEFAULT '0.00',
  `coupon_amount` decimal(8,2) DEFAULT '0.00',
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `use_count` smallint(6) DEFAULT '0',
  `sku` int(10) DEFAULT '0',
  `orderby` smallint(6) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `closed` tinyint(1) unsigned DEFAULT '0',
  `picked` int(10) unsigned DEFAULT '0',
  `type` tinyint(1) unsigned DEFAULT '0',
  `mendian_id` varchar(32) DEFAULT '0',
  `title` varchar(32) DEFAULT '',
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_coupon`
--

LOCK TABLES `jh_shop_coupon` WRITE;
/*!40000 ALTER TABLE `jh_shop_coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_coupon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_log`
--

DROP TABLE IF EXISTS `jh_shop_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) DEFAULT '0',
  `money` decimal(10,2) DEFAULT '0.00',
  `intro` varchar(255) DEFAULT '',
  `admin` varchar(255) DEFAULT '',
  `day` int(8) DEFAULT '0',
  `clientip` varchar(25) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `balance` decimal(10,2) DEFAULT '0.00',
  `extend` text,
  PRIMARY KEY (`log_id`),
  KEY `shop_id` (`shop_id`),
  KEY `day` (`day`,`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_log`
--

LOCK TABLES `jh_shop_log` WRITE;
/*!40000 ALTER TABLE `jh_shop_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_msg`
--

DROP TABLE IF EXISTS `jh_shop_msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_msg` (
  `msg_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) DEFAULT '0',
  `order_id` int(10) DEFAULT '0',
  `title` varchar(80) DEFAULT NULL,
  `content` varchar(512) DEFAULT '',
  `type` tinyint(1) DEFAULT '0',
  `is_read` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`msg_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_msg`
--

LOCK TABLES `jh_shop_msg` WRITE;
/*!40000 ALTER TABLE `jh_shop_msg` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_msg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_print`
--

DROP TABLE IF EXISTS `jh_shop_print`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_print` (
  `plat_id` int(10) NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `title` varchar(50) DEFAULT '',
  `from` enum('ylyun') DEFAULT NULL,
  `partner` mediumint(8) unsigned DEFAULT '0',
  `apikey` varchar(50) DEFAULT '0',
  `machine_code` varchar(30) DEFAULT '0',
  `mkey` varchar(15) DEFAULT '0',
  `num` smallint(6) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `online` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
  PRIMARY KEY (`plat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_print`
--

LOCK TABLES `jh_shop_print` WRITE;
/*!40000 ALTER TABLE `jh_shop_print` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_print` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_tixian`
--

DROP TABLE IF EXISTS `jh_shop_tixian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_tixian` (
  `tixian_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `money` decimal(10,2) DEFAULT '0.00',
  `intro` varchar(255) DEFAULT '',
  `account_info` varchar(512) DEFAULT '',
  `status` tinyint(1) DEFAULT '0',
  `reason` varchar(255) DEFAULT '',
  `updatetime` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT NULL,
  `end_money` decimal(10,2) DEFAULT '0.00',
  `from` enum('admin','shop') DEFAULT 'shop' COMMENT '提现操作者：shop 商户，admin 平台',
  `trade_no` bigint(15) DEFAULT '0',
  `city_id` mediumint(6) DEFAULT '0',
  `payee_account` varchar(100) DEFAULT '',
  `pay_result` varchar(255) DEFAULT '',
  `pay_status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`tixian_id`),
  KEY `shop_id` (`shop_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_tixian`
--

LOCK TABLES `jh_shop_tixian` WRITE;
/*!40000 ALTER TABLE `jh_shop_tixian` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_tixian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_shop_verify`
--

DROP TABLE IF EXISTS `jh_shop_verify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_shop_verify` (
  `shop_id` mediumint(8) unsigned NOT NULL,
  `id_name` varchar(30) DEFAULT '',
  `id_number` varchar(20) DEFAULT '',
  `id_photo` varchar(150) DEFAULT '',
  `shop_photo` varchar(150) DEFAULT '',
  `verify_dianzhu` tinyint(1) DEFAULT '0',
  `company_name` varchar(64) DEFAULT '',
  `yz_number` varchar(30) DEFAULT '',
  `yz_photo` varchar(150) DEFAULT '',
  `verify_yyzz` tinyint(1) DEFAULT '0',
  `refuse` varchar(255) DEFAULT '',
  `verify` tinyint(1) DEFAULT '0',
  `verify_time` int(10) DEFAULT '0',
  `updatetime` int(10) DEFAULT '0',
  `yz_photo_s` varchar(150) DEFAULT '',
  `id_photo_s` varchar(150) DEFAULT '',
  `id_photo_sf` varchar(150) DEFAULT '',
  `id_photo_f` varchar(150) DEFAULT '',
  `yz_addr` varchar(150) DEFAULT '',
  `yz_time` varchar(50) DEFAULT '0',
  `yz_name` varchar(150) DEFAULT '',
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_shop_verify`
--

LOCK TABLES `jh_shop_verify` WRITE;
/*!40000 ALTER TABLE `jh_shop_verify` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_shop_verify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_site_tongji`
--

DROP TABLE IF EXISTS `jh_site_tongji`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_site_tongji` (
  `tongji_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(8,2) DEFAULT '0.00',
  `order_id` int(10) DEFAULT '0',
  `from` enum('paotui','qiang','waimai','peicard') DEFAULT 'waimai',
  `shop_amount` decimal(8,2) DEFAULT '0.00',
  `staff_amount` decimal(8,2) DEFAULT '0.00',
  `shop_id` int(10) DEFAULT '0',
  `uid` int(10) DEFAULT '0',
  `staff_id` int(10) DEFAULT '0',
  `pei_amount` decimal(8,2) DEFAULT '0.00',
  `shop_fee` decimal(8,2) DEFAULT '0.00',
  `staff_fee` decimal(8,2) DEFAULT '0.00',
  `platform_first` decimal(8,2) DEFAULT '0.00',
  `platform_mj` decimal(8,2) DEFAULT '0.00',
  `platform_hongbao` decimal(8,2) DEFAULT '0.00',
  `platform_staff` decimal(8,2) DEFAULT '0.00',
  `shop_first` decimal(8,2) DEFAULT '0.00',
  `shop_mj` decimal(8,2) DEFAULT '0.00',
  `shop_coupon` decimal(8,2) DEFAULT '0.00',
  `shop_discount` decimal(8,2) DEFAULT '0.00',
  `year` int(4) DEFAULT '0',
  `mouth` int(6) DEFAULT '0',
  `day` int(8) DEFAULT '0',
  `hour` int(2) DEFAULT '0',
  `city_id` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `site_fee` decimal(8,2) DEFAULT '0.00',
  `shop_huangou` decimal(8,2) DEFAULT '0.00',
  `platform_peicard` decimal(8,2) DEFAULT '0.00',
  PRIMARY KEY (`tongji_id`),
  KEY `shop_id` (`shop_id`),
  KEY `udi` (`uid`),
  KEY `staff_id` (`staff_id`),
  KEY `year` (`year`),
  KEY `mouth` (`mouth`),
  KEY `day` (`day`),
  KEY `hour` (`hour`),
  KEY `dateline` (`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_site_tongji`
--

LOCK TABLES `jh_site_tongji` WRITE;
/*!40000 ALTER TABLE `jh_site_tongji` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_site_tongji` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_sms_log`
--

DROP TABLE IF EXISTS `jh_sms_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_sms_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` varchar(50) DEFAULT '',
  `content` varchar(255) DEFAULT '',
  `sms` varchar(20) DEFAULT '',
  `status` tinyint(1) DEFAULT '0',
  `clientip` char(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_sms_log`
--

LOCK TABLES `jh_sms_log` WRITE;
/*!40000 ALTER TABLE `jh_sms_log` DISABLE KEYS */;
INSERT INTO `jh_sms_log` VALUES (1,'18621884811','【丝路互通外卖】您的短信验证码是499822，该验证码3分钟有效','56dx',0,'113.16.66.136',1542896999);
/*!40000 ALTER TABLE `jh_sms_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff`
--

DROP TABLE IF EXISTS `jh_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff` (
  `staff_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` smallint(6) DEFAULT '0',
  `from` enum('weixiu','paotui','house') DEFAULT NULL,
  `name` varchar(30) DEFAULT '',
  `mobile` char(11) DEFAULT '',
  `passwd` char(32) DEFAULT '',
  `face` varchar(150) DEFAULT '',
  `money` decimal(10,2) DEFAULT '0.00',
  `total_money` decimal(10,2) DEFAULT '0.00',
  `tixian_percent` tinyint(3) DEFAULT '0',
  `tixian_money` decimal(10,2) DEFAULT '0.00',
  `orders` mediumint(8) DEFAULT '0',
  `score` int(10) DEFAULT '0',
  `comments` mediumint(8) DEFAULT '0',
  `lat` int(10) DEFAULT '0',
  `lng` int(10) DEFAULT '0',
  `attr` varchar(255) DEFAULT '',
  `sex` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `age` tinyint(2) unsigned DEFAULT '0',
  `intro` text,
  `status` tinyint(1) DEFAULT '0',
  `updatetime` int(10) DEFAULT '0',
  `loginip` varchar(15) DEFAULT '',
  `lastlogin` int(10) DEFAULT '0',
  `verify_name` tinyint(1) DEFAULT '3',
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `group_id` int(10) DEFAULT '0',
  `pei_time` int(10) DEFAULT '0',
  `is_used` tinyint(1) DEFAULT '0',
  `limit_order` int(10) DEFAULT '10',
  `level_id` int(10) DEFAULT '0',
  `locked` tinyint(1) DEFAULT '0',
  `outline` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`staff_id`),
  KEY `mobile` (`mobile`,`closed`),
  KEY `city_id` (`city_id`,`audit`,`closed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff`
--

LOCK TABLES `jh_staff` WRITE;
/*!40000 ALTER TABLE `jh_staff` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_account`
--

DROP TABLE IF EXISTS `jh_staff_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_account` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `account` varchar(50) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `dateline` int(11) DEFAULT NULL,
  `title` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `account_id` (`account_id`),
  KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_account`
--

LOCK TABLES `jh_staff_account` WRITE;
/*!40000 ALTER TABLE `jh_staff_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_bills`
--

DROP TABLE IF EXISTS `jh_staff_bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_bills` (
  `bills_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bills_sn` int(10) NOT NULL,
  `staff_id` mediumint(8) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `freight_amount` decimal(8,2) DEFAULT '0.00',
  `amount` decimal(8,2) DEFAULT '0.00',
  `diff_amount` decimal(8,2) DEFAULT '0.00',
  `orders` mediumint(8) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`bills_id`),
  UNIQUE KEY `bills` (`bills_sn`,`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_bills`
--

LOCK TABLES `jh_staff_bills` WRITE;
/*!40000 ALTER TABLE `jh_staff_bills` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_bills_log`
--

DROP TABLE IF EXISTS `jh_staff_bills_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_bills_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bills_id` int(10) DEFAULT '0',
  `bills_sn` int(10) DEFAULT '0',
  `staff_id` mediumint(8) DEFAULT '0',
  `order_id` int(10) DEFAULT '0',
  `freight_amount` decimal(8,2) DEFAULT '0.00',
  `tixian_percent` tinyint(3) DEFAULT '100',
  `amount` decimal(8,2) DEFAULT '0.00',
  `diff_amount` decimal(8,2) DEFAULT '0.00',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `bills_id` (`bills_id`),
  KEY `staff_billls` (`staff_id`,`bills_sn`),
  KEY `dateline` (`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_bills_log`
--

LOCK TABLES `jh_staff_bills_log` WRITE;
/*!40000 ALTER TABLE `jh_staff_bills_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_bills_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_comment`
--

DROP TABLE IF EXISTS `jh_staff_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT '0',
  `staff_id` mediumint(8) DEFAULT '0',
  `uid` mediumint(8) DEFAULT '0',
  `score` tinyint(1) DEFAULT '0',
  `content` varchar(255) DEFAULT '',
  `reply` varchar(255) DEFAULT '',
  `reply_ip` varchar(15) DEFAULT '',
  `reply_time` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `have_photo` tinyint(1) DEFAULT '0',
  `pei_time` int(11) DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT '0' COMMENT '是否匿名评价',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_comment`
--

LOCK TABLES `jh_staff_comment` WRITE;
/*!40000 ALTER TABLE `jh_staff_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_comment_photo`
--

DROP TABLE IF EXISTS `jh_staff_comment_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_comment_photo` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) DEFAULT '0',
  `photo` varchar(255) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_comment_photo`
--

LOCK TABLES `jh_staff_comment_photo` WRITE;
/*!40000 ALTER TABLE `jh_staff_comment_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_comment_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_distance`
--

DROP TABLE IF EXISTS `jh_staff_distance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_distance` (
  `distance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) DEFAULT '0',
  `distance` int(10) DEFAULT '0',
  `day` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`distance_id`),
  UNIQUE KEY `staff_id_day` (`staff_id`,`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_distance`
--

LOCK TABLES `jh_staff_distance` WRITE;
/*!40000 ALTER TABLE `jh_staff_distance` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_distance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_fields`
--

DROP TABLE IF EXISTS `jh_staff_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_fields` (
  `staff_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `id_name` varchar(30) DEFAULT '',
  `id_number` varchar(30) DEFAULT '',
  `id_photo` varchar(150) DEFAULT '',
  `verify_photo` varchar(150) DEFAULT '',
  `account_type` varchar(30) DEFAULT '',
  `account_name` varchar(30) DEFAULT '',
  `account_number` varchar(30) DEFAULT '',
  `info` mediumtext,
  PRIMARY KEY (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_fields`
--

LOCK TABLES `jh_staff_fields` WRITE;
/*!40000 ALTER TABLE `jh_staff_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_forcedlog`
--

DROP TABLE IF EXISTS `jh_staff_forcedlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_forcedlog` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT '0',
  `staff_id` mediumint(8) DEFAULT '0',
  `group_id` mediumint(8) DEFAULT NULL,
  `distance` decimal(8,2) DEFAULT '0.00',
  `lng` int(10) DEFAULT '0',
  `lat` int(10) DEFAULT '0',
  `o_lng` int(10) DEFAULT '0',
  `o_lat` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `staff_id` (`staff_id`),
  KEY `group_id` (`group_id`),
  KEY `dateline` (`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_forcedlog`
--

LOCK TABLES `jh_staff_forcedlog` WRITE;
/*!40000 ALTER TABLE `jh_staff_forcedlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_forcedlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_level`
--

DROP TABLE IF EXISTS `jh_staff_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_level` (
  `level_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '',
  `config_waimai` text,
  `config_paotui` text,
  `dateline` int(10) DEFAULT '0',
  `stime` varchar(100) DEFAULT '',
  `ltime` varchar(100) DEFAULT '',
  `config_waimai_time` text,
  `config_paotui_time` text,
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_level`
--

LOCK TABLES `jh_staff_level` WRITE;
/*!40000 ALTER TABLE `jh_staff_level` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_log`
--

DROP TABLE IF EXISTS `jh_staff_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) DEFAULT NULL,
  `money` decimal(10,2) DEFAULT '0.00',
  `intro` varchar(255) DEFAULT '',
  `admin` varchar(255) DEFAULT '',
  `day` int(8) DEFAULT '0',
  `clientip` varchar(25) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `balance` decimal(10,2) DEFAULT '0.00',
  `extend` text,
  PRIMARY KEY (`log_id`),
  KEY `staff_id` (`staff_id`),
  KEY `day` (`day`,`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_log`
--

LOCK TABLES `jh_staff_log` WRITE;
/*!40000 ALTER TABLE `jh_staff_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_msg`
--

DROP TABLE IF EXISTS `jh_staff_msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_msg` (
  `msg_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) DEFAULT '0',
  `title` varchar(80) DEFAULT NULL,
  `content` varchar(512) DEFAULT '',
  `is_read` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT NULL,
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`msg_id`),
  KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_msg`
--

LOCK TABLES `jh_staff_msg` WRITE;
/*!40000 ALTER TABLE `jh_staff_msg` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_msg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_outlinelog`
--

DROP TABLE IF EXISTS `jh_staff_outlinelog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_outlinelog` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) DEFAULT '0',
  `city_id` int(10) DEFAULT '0',
  `group_id` int(10) DEFAULT '0',
  `stime` int(10) DEFAULT NULL,
  `ltime` int(10) DEFAULT NULL,
  `lng` int(10) DEFAULT NULL,
  `lat` int(10) DEFAULT NULL,
  `o_lng` int(10) DEFAULT NULL,
  `o_lat` int(10) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `staff_id` (`staff_id`),
  KEY `group_id` (`group_id`),
  KEY `dateline` (`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_outlinelog`
--

LOCK TABLES `jh_staff_outlinelog` WRITE;
/*!40000 ALTER TABLE `jh_staff_outlinelog` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_outlinelog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_paidan`
--

DROP TABLE IF EXISTS `jh_staff_paidan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_paidan` (
  `paidan_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) DEFAULT '0',
  `pai` int(10) DEFAULT '0',
  `accept` int(10) DEFAULT '0',
  `refuse` int(10) DEFAULT '0',
  `day` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  `intro` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '',
  PRIMARY KEY (`paidan_id`),
  UNIQUE KEY `day_staff` (`staff_id`,`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_paidan`
--

LOCK TABLES `jh_staff_paidan` WRITE;
/*!40000 ALTER TABLE `jh_staff_paidan` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_paidan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_paidan_log`
--

DROP TABLE IF EXISTS `jh_staff_paidan_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_paidan_log` (
  `log_id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT '0',
  `intro` varchar(255) CHARACTER SET utf8 DEFAULT '',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_paidan_log`
--

LOCK TABLES `jh_staff_paidan_log` WRITE;
/*!40000 ALTER TABLE `jh_staff_paidan_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_paidan_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_timeoutorder`
--

DROP TABLE IF EXISTS `jh_staff_timeoutorder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_timeoutorder` (
  `time_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT '0',
  `staff_id` int(10) DEFAULT '0',
  `jd_time` int(10) DEFAULT '0',
  `complete_time` int(10) DEFAULT '0',
  `timeout` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`time_id`),
  KEY `order_id` (`order_id`),
  KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_timeoutorder`
--

LOCK TABLES `jh_staff_timeoutorder` WRITE;
/*!40000 ALTER TABLE `jh_staff_timeoutorder` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_timeoutorder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_tixian`
--

DROP TABLE IF EXISTS `jh_staff_tixian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_tixian` (
  `tixian_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` mediumint(8) DEFAULT '0',
  `money` decimal(10,2) DEFAULT '0.00',
  `intro` varchar(255) DEFAULT '',
  `account_info` varchar(512) DEFAULT '',
  `status` tinyint(1) DEFAULT '0',
  `reason` varchar(255) DEFAULT NULL,
  `updatetime` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT NULL,
  `end_money` decimal(10,2) DEFAULT '0.00',
  `payee_account` varchar(100) DEFAULT '',
  `pay_result` varchar(255) DEFAULT '',
  `pay_status` tinyint(1) DEFAULT '0',
  `trade_no` bigint(15) DEFAULT '0',
  PRIMARY KEY (`tixian_id`),
  KEY `staff_id` (`staff_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_tixian`
--

LOCK TABLES `jh_staff_tixian` WRITE;
/*!40000 ALTER TABLE `jh_staff_tixian` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_tixian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_staff_verify`
--

DROP TABLE IF EXISTS `jh_staff_verify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_staff_verify` (
  `staff_id` mediumint(8) NOT NULL,
  `id_name` varchar(30) DEFAULT '',
  `id_number` varchar(18) DEFAULT '',
  `id_photo` varchar(150) DEFAULT '',
  `verify` tinyint(1) DEFAULT '0',
  `verify_time` int(10) DEFAULT '0',
  `refuse` varchar(150) DEFAULT '',
  `updatetime` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_staff_verify`
--

LOCK TABLES `jh_staff_verify` WRITE;
/*!40000 ALTER TABLE `jh_staff_verify` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_staff_verify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_stripe_card`
--

DROP TABLE IF EXISTS `jh_stripe_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_stripe_card` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) DEFAULT '0',
  `card_id` varchar(64) CHARACTER SET utf8 DEFAULT '',
  `card_name` varchar(20) CHARACTER SET utf8 DEFAULT '',
  `card_type` varchar(10) CHARACTER SET utf8 DEFAULT '' COMMENT 'Visa,MasterCard,American Express',
  `card_number` varchar(20) CHARACTER SET utf8 DEFAULT '',
  `exp_month` varchar(2) CHARACTER SET utf8 DEFAULT '',
  `exp_year` varchar(4) CHARACTER SET utf8 DEFAULT '',
  `cvc` varchar(5) CHARACTER SET utf8 DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_stripe_card`
--

LOCK TABLES `jh_stripe_card` WRITE;
/*!40000 ALTER TABLE `jh_stripe_card` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_stripe_card` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_stripe_customer`
--

DROP TABLE IF EXISTS `jh_stripe_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_stripe_customer` (
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `customer_id` varchar(64) CHARACTER SET utf8 DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_stripe_customer`
--

LOCK TABLES `jh_stripe_customer` WRITE;
/*!40000 ALTER TABLE `jh_stripe_customer` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_stripe_customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_subsidy_staff`
--

DROP TABLE IF EXISTS `jh_subsidy_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_subsidy_staff` (
  `subsidy_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT '0',
  `staff_id` int(10) DEFAULT NULL,
  `pei_amount` decimal(8,2) DEFAULT NULL,
  `staff_amount` decimal(8,2) DEFAULT NULL,
  `diff_amount` decimal(8,2) DEFAULT NULL,
  `bl` decimal(8,2) DEFAULT NULL,
  `from` enum('paotui','waimai') DEFAULT 'waimai',
  `year` int(4) DEFAULT '0',
  `mouth` int(6) DEFAULT '0',
  `day` int(8) DEFAULT NULL,
  `hour` int(2) DEFAULT NULL,
  `group_id` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  `city_id` int(10) DEFAULT '0',
  PRIMARY KEY (`subsidy_id`),
  KEY `order_id` (`order_id`),
  KEY `staff_id` (`staff_id`),
  KEY `mouth` (`mouth`),
  KEY `year` (`year`),
  KEY `day` (`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_subsidy_staff`
--

LOCK TABLES `jh_subsidy_staff` WRITE;
/*!40000 ALTER TABLE `jh_subsidy_staff` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_subsidy_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_subsidy_waimai`
--

DROP TABLE IF EXISTS `jh_subsidy_waimai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_subsidy_waimai` (
  `subsidy_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '0',
  `order_id` int(10) DEFAULT '0',
  `shop_id` int(10) DEFAULT '0',
  `platform_first` decimal(8,2) DEFAULT '0.00',
  `platform_mj` decimal(8,2) DEFAULT '0.00',
  `platform_hongbao` decimal(8,2) DEFAULT '0.00',
  `shop_first` decimal(8,2) DEFAULT '0.00',
  `shop_mj` decimal(8,2) DEFAULT '0.00',
  `year` int(4) DEFAULT '0',
  `mouth` int(6) DEFAULT '0',
  `day` int(8) DEFAULT '0',
  `hour` int(2) DEFAULT NULL,
  `group_id` int(10) DEFAULT '0',
  `city_id` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `bl` int(3) DEFAULT '0',
  `shop_coupon` decimal(8,2) DEFAULT '0.00',
  `shop_discount` decimal(8,2) DEFAULT '0.00',
  `shop_huangou` decimal(8,2) DEFAULT '0.00',
  `platform_peicard` decimal(8,2) DEFAULT '0.00' COMMENT '平台配送会员卡',
  `uid` int(10) DEFAULT '0',
  PRIMARY KEY (`subsidy_id`),
  KEY `year` (`year`),
  KEY `mouth` (`mouth`),
  KEY `day` (`day`),
  KEY `hour` (`hour`),
  KEY `dateline` (`dateline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_subsidy_waimai`
--

LOCK TABLES `jh_subsidy_waimai` WRITE;
/*!40000 ALTER TABLE `jh_subsidy_waimai` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_subsidy_waimai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_system_config`
--

DROP TABLE IF EXISTS `jh_system_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_system_config` (
  `k` varchar(30) NOT NULL,
  `v` mediumtext,
  `title` varchar(30) DEFAULT '',
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_system_config`
--

LOCK TABLES `jh_system_config` WRITE;
/*!40000 ALTER TABLE `jh_system_config` DISABLE KEYS */;
INSERT INTO `jh_system_config` VALUES ('access','a:5:{s:6:\"denyip\";s:0:\"\";s:12:\"mobile_count\";s:2:\"10\";s:11:\"mobile_time\";s:1:\"0\";s:6:\"closed\";s:1:\"0\";s:13:\"closed_reason\";s:0:\"\";}','访问设置',1505443590),('apppush','a:3:{s:6:\"member\";a:4:{s:6:\"appkey\";s:0:\"\";s:10:\"appkey_ltd\";s:0:\"\";s:6:\"secret\";s:0:\"\";s:10:\"secret_ltd\";s:0:\"\";}s:5:\"staff\";a:4:{s:6:\"appkey\";s:0:\"\";s:10:\"appkey_ltd\";s:0:\"\";s:6:\"secret\";s:0:\"\";s:10:\"secret_ltd\";s:0:\"\";}s:4:\"shop\";a:4:{s:6:\"appkey\";s:0:\"\";s:10:\"appkey_ltd\";s:0:\"\";s:6:\"secret\";s:0:\"\";s:10:\"secret_ltd\";s:0:\"\";}}','推送配置',1536753390),('app_download','a:37:{s:19:\"apk_client_packname\";s:0:\"\";s:18:\"apk_client_version\";s:0:\"\";s:23:\"apk_client_force_update\";s:1:\"0\";s:19:\"apk_client_download\";s:0:\"\";s:16:\"apk_client_intro\";s:0:\"\";s:19:\"ios_client_packname\";s:0:\"\";s:18:\"ios_client_version\";s:0:\"\";s:23:\"ios_client_force_update\";s:1:\"0\";s:19:\"ios_client_download\";s:0:\"\";s:16:\"ios_client_intro\";s:0:\"\";s:14:\"yyb_client_url\";s:0:\"\";s:15:\"client_download\";s:1:\"0\";s:15:\"client_packname\";s:0:\"\";s:11:\"client_logo\";s:58:\"photo/201809/20180910_7D2112488625D917E306E1E365559B2E.png\";s:13:\"client_prompt\";s:0:\"\";s:16:\"apk_biz_packname\";s:0:\"\";s:15:\"apk_biz_version\";s:0:\"\";s:20:\"apk_biz_force_update\";s:1:\"0\";s:16:\"apk_biz_download\";s:0:\"\";s:13:\"apk_biz_intro\";s:0:\"\";s:16:\"ios_biz_packname\";s:0:\"\";s:15:\"ios_biz_version\";s:0:\"\";s:20:\"ios_biz_force_update\";s:1:\"0\";s:16:\"ios_biz_download\";s:0:\"\";s:13:\"ios_biz_intro\";s:0:\"\";s:11:\"yyb_biz_url\";s:0:\"\";s:18:\"apk_staff_packname\";s:0:\"\";s:17:\"apk_staff_version\";s:0:\"\";s:22:\"apk_staff_force_update\";s:1:\"0\";s:18:\"apk_staff_download\";s:0:\"\";s:15:\"apk_staff_intro\";s:0:\"\";s:18:\"ios_staff_packname\";s:0:\"\";s:17:\"ios_staff_version\";s:0:\"\";s:22:\"ios_staff_force_update\";s:1:\"0\";s:18:\"ios_staff_download\";s:0:\"\";s:15:\"ios_staff_intro\";s:0:\"\";s:13:\"yyb_staff_url\";s:0:\"\";}','APP版本',1536753051),('attach','a:4:{s:3:\"url\";s:10:\"./attachs/\";s:10:\"allow_size\";s:4:\"2048\";s:12:\"thumbquality\";s:2:\"90\";s:5:\"thumb\";s:3:\"200\";}','附件设置',1543489748),('automatic','a:4:{s:17:\"unpay_cancel_time\";s:1:\"5\";s:17:\"auto_comfirm_time\";s:2:\"30\";s:20:\"unjiedan_cancel_time\";s:2:\"10\";s:20:\"untreated_agree_time\";s:1:\"5\";}','脚本配置',1534731979),('config','a:2:{s:4:\"hash\";s:8360:\"6956424F5277304B47676F414141414E535568455567414141483041414141744341594141414344446D545341414141475852465748525462325A30643246795A5142425A4739695A53424A6257466E5A564A6C5957523563636C6C5041414141794270564668305745314D4F6D4E76625335685A4739695A53353462584141414141414144772F654842685932746C644342695A576470626A30693737752F496942705A443069567A564E4D4531775132566F61556836636D5654656B355559337072597A6C6B496A382B494478344F6E68746347316C6447456765473173626E4D366544306959575276596D5536626E4D366257563059533869494867366547317764477339496B466B62324A6C4946684E5543424462334A6C494455754D43316A4D445977494459784C6A457A4E4463334E7977674D6A41784D4338774D6938784D6930784E7A6F7A4D6A6F774D4341674943416749434167496A346750484A6B5A6A70535245596765473173626E4D36636D526D50534A6F644852774F693876643364334C6E637A4C6D39795A7938784F546B354C7A41794C7A49794C584A6B5A69317A6557353059586774626E4D6A496A346750484A6B5A6A70455A584E6A636D6C7764476C76626942795A47593659574A76645851394969496765473173626E4D366547317750534A6F644852774F693876626E4D7559575276596D5575593239744C336868634338784C6A41764969423462577875637A70346258424E545430696148523063446F764C32357A4C6D466B62324A6C4C6D4E7662533934595841764D5334774C3231744C79496765473173626E4D36633352535A575939496D6830644841364C793975637935685A4739695A53356A62323076654746774C7A45754D43397A56486C775A5339535A584E7664584A6A5A564A6C5A694D694948687463447044636D566864473979564739766244306951575276596D5567554768766447397A6147397749454E544E5342586157356B6233647A496942346258424E5454704A626E4E305957356A5A556C4550534A346258417561576C6B4F6B55334D3055784E5459354D4449314D7A457852544D344D554933516A46475154597A4D304E474E54597A496942346258424E5454704562324E316257567564456C4550534A34625841755A476C6B4F6B55334D3055784E545A424D4449314D7A457852544D344D554933516A46475154597A4D304E474E54597A496A3467504868746345314E4F6B526C636D6C325A575247636D397449484E30556D566D4F6D6C7563335268626D4E6C53555139496E687463433570615751365254637A525445314E6A63774D6A557A4D5446464D7A6778516A64434D555A424E6A4D7A513059314E6A4D6949484E30556D566D4F6D5276593356745A57353053555139496E68746343356B615751365254637A525445314E6A67774D6A557A4D5446464D7A6778516A64434D555A424E6A4D7A513059314E6A4D694C7A3467504339795A4759365247567A59334A70634852706232342B49447776636D526D4F6C4A45526A3467504339344F6E68746347316C6447452B4944772F654842685932746C6443426C626D5139496E4969507A3637555966634141414973306C455156523432757963435778555652534737394253704341574B6367715167566B4559774C6771675246526663514E77696F6B614A476F4F4345414B4B454E53344C776949714B43345339474943305A6C55304645525648454254634552634343624E307330426E50795879547555376554476435725A33704F386B665A715A33376E74392F7A336E2F4F666357337942514D4234567265736E766349504E493971774F5737665468707930364C704A2F7567744B424B483466364267676D424F457466704C356774714C546D79784E4D45647A482B317A42564D4641727175574A64677347437A34323658662B5148426C594C6476506478543663497471517A6D58324B316964504F6F53336376673850386E37306539316450693870665736767143396F4858456D4F61434269343946795734726541515946754475754C7030634C376E69696637302F794F6E756A6646345A4D66632B687A4737557268757041556972686B7966355450367854705070657645306A524F394D3566576F61797947534A4B4B686369496963564E425A794B692B7A6E64737971745144444D656E34484343714961493073687970414F3477426D6C71755A6B777246725366434A654E686A466F69387346707775755A5137564F6A634A7A6955715855737139494F66304530653664566B536C4B7834414C4262344C56676D50514A4F3944666E394536445245714936355158417933766F59337277586B56784247744D46385A54674C6347744546384D3259634C68677132436B594B31676932735A412B396B69765874744346644F485A2F6763336A684F734A44586C776B32436A62786E666459454630456379464E506251584663564D5153454C5142665348337A6E43464A45415A4867524261514C6F345A6771386850653677373547656E4A306D6D43676F6F3953635473376542664768614B4135654A35674F4A4667752B424651536D457170587A37317242523959312B754C397A374D414E4C5350596E354E4854634B7A684A305A66772F48756E56613430463751526E514C6147315256346279346575524E767635586E6E45756531716A776854565845367350596C734A65587768704A636A695064772F517036437A7572714A4138306C32796475545338594B444254305156354E3570714671705A6D675348437334485842434D4751434E49445553716364594950436576614E376D436E6F55756F70645A57453879626F6B6C4944335371386D576B713956694E30702B453777454747335067547551356A5651316D586F514F614A4341575A3050305145546934344B4C5341586C7A4E553030624C59497A303557774D47556A6239535A342F694964665A676D725279444A494D4B557141666A764D346F7776347977537242766554362B6F542F3639415242703251556E5047733672744A4254336C3552515778427068586A34554A523371655667787A4457746C416E30422F78755771464671687A5031476C6B516E7646655251447437442B4E784D496A3151432B63376E5470374A74366D684A354E6E6A3059676155352F427A4272337A6E65484C384E31616E7271455645587A776B516668756C665267667A7649304963537268767866632B45587A5039373253725970636D597070447531484C746664794A6155583770622B424C50564A73793879476E495648675849685A5338672F6A43695154376C316965426F76486B444B554A562B6C6551337078726170672F4370337743796B6A4951644F42394C56632B36694A4D6C4B5952342F586E35696976657A682F745251716579434A5338682F464B62636B6542346B5863392B6643546F4A6E6F5845504479336B6B553446652F4F73577075726345585543573853636374744F6855775539423155394135622B61536151334A6F54574671753069486D47686F7432335971744D51735164666C34614448452F5955487678584864565A796A5A416D4B4C49453233693067597249735A527432377A775876326D2F66546C4D58362B472F7961355078626F337865616932457A53416838306833787A547474454773395351482B2F432B5A6554666B67546E31507A666D547A666C66546B4939392F79707756796478734F704475783674533356635057436F3478385837302F7738686B35625163546341547A324D334C77736A6A6E624D2B633535746739363965784A792F6D324158376C4772423542527043766877784651716478764A62684E634B5A4C3936596B7A7A4C42625653316E386E6E4A64787244387137515361344933647A48494A4C782B6E57367047385830325673426552714C582B71594A7271434B4757794976593067764A357756755454664D4A666D3055375A45784375432F4A75453978523232434E30584B744E2B57636B6A6B643062637978694A365774414E62353473654463697636757748574343323746643642634D5355513770454E7A7875336A556765344E4D38497646684C726B6D51734D46687758364556796F706568687A5A49773578304C344473624E635242304A66514164477456643968363862706A4A70466547303037613166772B6732384C5A62394948694E31307071573463783355674461724F5A4E35597449712B7261542F2B6C4E6F57336E305A527271325634394149377874346A744A4F34327846535A38357436327755534362656943654777636A5A31792B675731697653444D6F7A30546B544A55506B556A315656552F65306F734B33636336354869526B4E5258656D325559366145393742304F6554775A613244432B2B786254666730544C5659545A462B574977794B74314D6857432B31554E77347738783871786F754C2B3666344761494C304A3559715437557044306A556E6C3771735638704E2B49436B79515453727A6242566D4B6B3653624539326C49656F4377486C7251725632596334384A2F796D5A6274446B70697670326F3638586E4248464D476F4175547A4E4D3370577932437569547776577A6746423143352B50316F455337424B71696248697339332B51376D4F46366731664B486A424250764E655648477A7A644A37424456456C744F616455306766705939396631464B73656F476A7638504F6C704137645A446B687A6A6E314C487768356542464E5647797153635070337A52666439474E42303649647761782F6975486A61596C51626B617175314F7753764E2B476A567039547170316E676D31645863437234346836326A50586B374F5448635973354764484D3361523566314F316F5A783274545263335172616F4A304C544F754D73486563694B6D44334253476E693552693039665871704352353348686E7859505641596A385450707173326D56646C416734326F6F494C31716177445956682F634C586A48424C567074757478676E413948364C314E685042396647392B545A44754E346B664D7452636549734A62694C556468764751382B69524E4E652B306F54507257716E71363938686D51744A685157326A436634335346636359514370645243694F5A7270686F7A747A453069522B6C722F4975596436376F614366516B55562F65617871646D3867766C6831446F6361725A4F4E644942712B376A54526435685376556171696A7A534F70762F6E736C7235694441394C6855475637664159386537544458667367627A2F6859707566766444763564753768627542552F6378454B42733353473855682F4372536A4671324E6B4F79664D67665763566F6A4972536A5045546348704E466557772B63616A6654736575692F4B666B71536A4E704C6C36764278354F516F5346446C4C6F6D6267503850444663524165656D355447542B492B5871623843455131526136632F63323139336E46756D62574E6E466C68646F54567269554B2B57574D304B502B383349745A556B61364B73776E6A5A373479457A356B4744704F3747626E626A6371655A655663774D4F6E62416C654A76757076316F676E766830577744595675397552574C794D647A325779534F39623048564165576C7233716F35545A4649347975337A2F765041756D666566727048756D6365365A356C705030727741444766685A7833374E41374141414141424A52553545726B4A6767673D3D\";s:4:\"host\";s:132:\"687474703A2F2F77777725732E696A682E63632F696E6465782E7068703F63746C3D6C697374656E26686F73743D2573266B65793D25732676657273696F6E3D2573\";}','系统设置',NULL),('connect',NULL,'三方登录',1490000000),('ditui','a:10:{s:6:\"banner\";s:58:\"photo/201802/20180205_1734BFBDB1DC168B9E21072DBA96B5AA.jpg\";s:11:\"share_title\";s:15:\"分享得红包\";s:10:\"share_info\";s:0:\"\";s:12:\"haibao_photo\";s:58:\"photo/201802/20180205_4744D2CC51F348AEDDD3588AD5CC360F.jpg\";s:12:\"haibao_qrtop\";s:4:\"1565\";s:13:\"haibao_qrleft\";s:3:\"400\";s:14:\"haibao_qrwidth\";s:3:\"440\";s:12:\"order_amount\";s:1:\"5\";s:7:\"hongbao\";a:2:{i:0;a:6:{s:10:\"min_amount\";s:2:\"20\";s:6:\"amount\";s:1:\"5\";s:3:\"day\";s:1:\"2\";s:4:\"type\";s:3:\"all\";s:5:\"stime\";s:5:\"00:00\";s:5:\"ltime\";s:5:\"12:00\";}i:1;a:6:{s:10:\"min_amount\";s:2:\"30\";s:6:\"amount\";s:2:\"10\";s:3:\"day\";s:1:\"1\";s:4:\"type\";s:3:\"all\";s:5:\"stime\";s:5:\"20:00\";s:5:\"ltime\";s:12:\"次日 08:00\";}}s:5:\"renwu\";s:110:\"1.推广员任务规则：\r\n2.推广员任务规则：\r\n3.推广员任务规则：\r\n4.推广员任务规则：\";}','推广设置',1517815672),('fenxiao','a:3:{s:5:\"level\";s:1:\"3\";s:6:\"domain\";s:8:\"weizx.cn\";s:5:\"other\";s:1:\"0\";}','分销设置',1478057748),('fright','','运费',1516239948),('frighttime','','',1510219716),('hongbao','a:7:{s:5:\"title\";s:12:\"红包标题\";s:4:\"coin\";s:59:\"photo/201709/20170922_DABD8DC917FDFE92230A96D9173E11E7.jpeg\";s:6:\"banner\";s:58:\"photo/201707/20170727_707CDFB4C73647FE023787400F71F33E.png\";s:5:\"intro\";s:150:\"活动描述：活动描述：活动描述：活动描述：活动描述：活动描述：活动描述：活动描述：活动描述：活动描述：\";s:4:\"desc\";s:210:\"活动规则：活动规则：活动规则：活动规则：活动规则：活动规则：活动规则：活动规则：活动规则：活动规则：活动规则：活动规则：活动规则：活动规则：\";s:5:\"limit\";s:2:\"10\";s:7:\"hongbao\";a:0:{}}','',1536753182),('hotmallproduct','a:1:{s:14:\"hotmallproduct\";s:42:\"火龙果,小龙虾,进口苹果,鸡米饭\";}','商城商品热搜',1495419005),('hotmallshop','a:1:{s:11:\"hotmallshop\";s:48:\"烧烤店,江湖客栈,无敌寂寞,蛇皮四海\";}','商城商家热搜',1495418992),('hottuan','a:1:{s:7:\"hottuan\";s:0:\"\";}','商场团购',1500619883),('hotwaimai','a:1:{s:9:\"hotwaimai\";s:23:\"肯打鸡,黄焖鸡,面\";}','',1534991457),('invite','a:5:{s:18:\"is_inviter_hongbao\";s:1:\"0\";s:18:\"is_invitee_hongbao\";s:1:\"0\";s:11:\"share_photo\";s:58:\"photo/201809/20180910_6D18160662DCEAFAE9E80368D96986F9.png\";s:11:\"share_title\";s:30:\"邀请好友奖励现金红包\";s:5:\"intro\";s:88:\"1、邀请好友奖励现金红包邀请好友奖励现金红包邀请好友奖励现金\";}','',1536753006),('jifen','a:3:{s:12:\"jifen_module\";a:2:{i:0;s:6:\"waimai\";i:1;s:6:\"paotui\";}s:10:\"jifen_type\";s:1:\"2\";s:11:\"jifen_ratio\";s:1:\"1\";}','积分设置',1536573931),('mail','a:4:{s:6:\"sender\";s:15:\"ijhmail@126.com\";s:4:\"mode\";s:4:\"smtp\";s:4:\"smtp\";a:4:{s:4:\"host\";s:12:\"smtp.126.com\";s:4:\"port\";s:2:\"25\";s:5:\"uname\";s:15:\"ijhmail@126.com\";s:6:\"passwd\";s:8:\"ijianghu\";}s:5:\"email\";s:15:\"ijianghu@qq.com\";}','邮件设置',1410881223),('moneypack','a:0:{}','充值配置',1536753138),('paotui','a:5:{s:7:\"mileage\";a:1:{i:0;a:4:{s:3:\"fkm\";s:1:\"1\";s:2:\"fm\";s:1:\"3\";s:3:\"wkg\";s:1:\"1\";s:2:\"wm\";s:3:\"1.5\";}}s:6:\"weight\";a:1:{i:0;a:1:{s:3:\"fkg\";s:1:\"1\";}}s:4:\"time\";a:1:{i:0;a:3:{s:5:\"stime\";s:5:\"00:00\";s:5:\"ltime\";s:5:\"08:00\";s:5:\"ratio\";s:2:\"10\";}}s:3:\"tip\";a:1:{i:0;s:1:\"1\";}s:5:\"price\";a:1:{i:1;s:2:\"10\";}}','跑腿配置',1536753333),('paotuimatic',NULL,'跑腿计划任务',1534731979),('print','a:4:{s:5:\"title\";s:9:\"云打印\";s:4:\"from\";s:0:\"\";s:7:\"partner\";s:0:\"\";s:6:\"apikey\";s:0:\"\";}','打印',1536753147),('push','a:2:{s:5:\"jpush\";a:3:{s:6:\"member\";a:4:{s:6:\"appkey\";s:24:\"23fb5e7e86c3c4b65c389b7f\";s:10:\"appkey_ltd\";s:0:\"\";s:6:\"secret\";s:24:\"4ac248c30c30ef2699dd29c4\";s:10:\"secret_ltd\";s:0:\"\";}s:5:\"staff\";a:4:{s:6:\"appkey\";s:24:\"003dc36472acfec174a268f9\";s:10:\"appkey_ltd\";s:24:\"0f75745336a71e50ec4ba5a4\";s:6:\"secret\";s:24:\"d716820879ba342f01242138\";s:10:\"secret_ltd\";s:24:\"08960dbb23df3fdc8166cf1e\";}s:4:\"shop\";a:4:{s:6:\"appkey\";s:24:\"02271ee1c099fe317edad3c1\";s:10:\"appkey_ltd\";s:24:\"b9a1c95f8530ba4f7472ad30\";s:6:\"secret\";s:24:\"6b8bc717ff9711174886fb0f\";s:10:\"secret_ltd\";s:24:\"4a948f12894cee438b8595b4\";}}s:7:\"alipush\";a:5:{s:11:\"accessKeyId\";s:16:\"LTAIXyuvLmIiRrRS\";s:15:\"accessKeySecret\";s:30:\"tB5h6ejHuG1PjTvD0UX3I033nJRSIZ\";s:6:\"member\";a:2:{s:7:\"android\";a:1:{s:6:\"appkey\";s:8:\"24957670\";}s:3:\"ios\";a:1:{s:6:\"appkey\";s:8:\"24927679\";}}s:5:\"staff\";a:2:{s:7:\"android\";a:1:{s:6:\"appkey\";s:8:\"24965560\";}s:3:\"ios\";a:1:{s:6:\"appkey\";s:8:\"24928854\";}}s:4:\"shop\";a:2:{s:7:\"android\";a:1:{s:6:\"appkey\";s:8:\"24965157\";}s:3:\"ios\";a:1:{s:6:\"appkey\";s:8:\"24928856\";}}}}','推送配置',1531735773),('score','a:3:{s:7:\"company\";a:5:{s:6:\"score1\";s:6:\"服务\";s:6:\"score2\";s:6:\"价格\";s:6:\"score3\";s:6:\"设计\";s:6:\"score4\";s:6:\"施工\";s:6:\"score5\";s:6:\"售后\";}s:8:\"designer\";a:3:{s:6:\"score1\";s:6:\"设计\";s:6:\"score2\";s:6:\"服务\";s:6:\"score3\";s:6:\"贴心\";}s:2:\"gz\";a:3:{s:6:\"score1\";s:6:\"施工\";s:6:\"score2\";s:6:\"服务\";s:6:\"score3\";s:6:\"贴心\";}}','评论配置',1414511280),('site','a:18:{s:5:\"title\";s:18:\"丝路互通外卖\";s:7:\"siteurl\";s:24:\"http://waimai.zyzjgzh.cn\";s:4:\"mail\";s:17:\"waimai@zyzjgzh.cn\";s:4:\"kfqq\";s:0:\"\";s:5:\"phone\";s:0:\"\";s:7:\"address\";s:0:\"\";s:4:\"logo\";s:0:\"\";s:9:\"logo_shop\";s:0:\"\";s:8:\"weixinqr\";s:0:\"\";s:7:\"city_id\";s:1:\"1\";s:6:\"domain\";s:0:\"\";s:9:\"show_shop\";s:1:\"0\";s:9:\"pei_range\";s:0:\"\";s:12:\"wxapp_ststus\";s:1:\"0\";s:7:\"rewrite\";s:1:\"0\";s:5:\"intro\";s:0:\"\";s:6:\"tongji\";s:0:\"\";s:3:\"icp\";s:20:\"桂ICP备14002473号\";}','基本设置',1542876555),('site_config','a:2:{s:4:\"hash\";s:8360:\"6956424F5277304B47676F414141414E535568455567414141483041414141744341594141414344446D545341414141475852465748525462325A30643246795A5142425A4739695A53424A6257466E5A564A6C5957523563636C6C5041414141794270564668305745314D4F6D4E76625335685A4739695A53353462584141414141414144772F654842685932746C644342695A576470626A30693737752F496942705A443069567A564E4D4531775132566F61556836636D5654656B355559337072597A6C6B496A382B494478344F6E68746347316C6447456765473173626E4D366544306959575276596D5536626E4D366257563059533869494867366547317764477339496B466B62324A6C4946684E5543424462334A6C494455754D43316A4D445977494459784C6A457A4E4463334E7977674D6A41784D4338774D6938784D6930784E7A6F7A4D6A6F774D4341674943416749434167496A346750484A6B5A6A70535245596765473173626E4D36636D526D50534A6F644852774F693876643364334C6E637A4C6D39795A7938784F546B354C7A41794C7A49794C584A6B5A69317A6557353059586774626E4D6A496A346750484A6B5A6A70455A584E6A636D6C7764476C76626942795A47593659574A76645851394969496765473173626E4D366547317750534A6F644852774F693876626E4D7559575276596D5575593239744C336868634338784C6A41764969423462577875637A70346258424E545430696148523063446F764C32357A4C6D466B62324A6C4C6D4E7662533934595841764D5334774C3231744C79496765473173626E4D36633352535A575939496D6830644841364C793975637935685A4739695A53356A62323076654746774C7A45754D43397A56486C775A5339535A584E7664584A6A5A564A6C5A694D694948687463447044636D566864473979564739766244306951575276596D5567554768766447397A6147397749454E544E5342586157356B6233647A496942346258424E5454704A626E4E305957356A5A556C4550534A346258417561576C6B4F6B55334D3055784E5459354D4449314D7A457852544D344D554933516A46475154597A4D304E474E54597A496942346258424E5454704562324E316257567564456C4550534A34625841755A476C6B4F6B55334D3055784E545A424D4449314D7A457852544D344D554933516A46475154597A4D304E474E54597A496A3467504868746345314E4F6B526C636D6C325A575247636D397449484E30556D566D4F6D6C7563335268626D4E6C53555139496E687463433570615751365254637A525445314E6A63774D6A557A4D5446464D7A6778516A64434D555A424E6A4D7A513059314E6A4D6949484E30556D566D4F6D5276593356745A57353053555139496E68746343356B615751365254637A525445314E6A67774D6A557A4D5446464D7A6778516A64434D555A424E6A4D7A513059314E6A4D694C7A3467504339795A4759365247567A59334A70634852706232342B49447776636D526D4F6C4A45526A3467504339344F6E68746347316C6447452B4944772F654842685932746C6443426C626D5139496E4969507A3637555966634141414973306C455156523432757963435778555652534737394253704341574B6367715167566B4559774C6771675246526663514E77696F6B614A476F4F4345414B4B454E53344C776949714B43345339474943305A6C55304645525648454254634552634343624E307330426E50795879547555376554476435725A33704F386B665A715A33376E74392F7A336E2F4F666357337942514D4234567265736E766349504E493971774F5737665468707930364C704A2F7567744B424B483466364267676D424F457466704C356774714C546D79784E4D45647A482B317A42564D4641727175574A64677347437A34323658662B5148426C594C6476506478543663497471517A6D58324B316964504F6F53336376673850386E37306539316450693870665736767143396F4858456D4F61434269343946795734726541515946754475754C7030634C376E69696637302F794F6E756A6646345A4D66632B687A4737557268757041556972686B7966355450367854705070657645306A524F394D3566576F61797947534A4B4B686369496963564E425A794B692B7A6E64737971745144444D656E34484343714961493073687970414F3477426D6C71755A6B777246725366434A654E686A466F69387346707775755A5137564F6A634A7A6955715855737139494F66304530653664566B536C4B7834414C4262344C56676D50514A4F3944666E394536445245714936355158417933766F59337277586B56784247744D46385A54674C6347744546384D3259634C68677132436B594B31676932735A412B396B69765874744346644F485A2F6763336A684F734A44586C776B32436A62786E666459454630456379464E506251584663564D5153454C5142665348337A6E43464A45415A4867524261514C6F345A6771386850653677373547656E4A306D6D43676F6F3953635473376542664768614B4135654A35674F4A4667752B424651536D457170587A37317242523959312B754C397A374D414E4C5350596E354E4854634B7A684A305A66772F48756E56613430463751526E514C6147315256346279346575524E767635586E6E45756531716A776854565845367350596C734A65587768704A636A695064772F517036437A7572714A4138306C32796475545338594B444254305156354E3570714671705A6D675348437334485842434D4751434E49445553716364594950436576614E376D436E6F55756F70645A57453879626F6B6C4944335371386D576B713956694E30702B453777454747335067547551356A5651316D586F514F614A4341575A3050305145546934344B4C5341586C7A4E553030624C59497A303557774D47556A6239535A342F694964665A676D725279444A494D4B557141666A764D346F7776347977537242766554362B6F542F3639415242703251556E5047733672744A4254336C3552515778427068586A34554A523371655667787A4457746C416E30422F78755771464671687A5031476C6B516E7646655251447437442B4E784D496A3151432B63376E5470374A74366D684A354E6E6A3059676155352F427A4272337A6E65484C384E31616E7271455645587A776B516668756C665267667A7649304963537268767866632B45587A5039373253725970636D597070447531484C746664794A6155583770622B424C50564A73793879476E495648675849685A5338672F6A43695154376C316965426F76486B444B554A562B6C6551337078726170672F4370337743796B6A4951644F42394C56632B36694A4D6C4B5952342F586E35696976657A682F745251716579434A5338682F464B62636B6542346B5863392B6643546F4A6E6F5845504479336B6B553446652F4F73577075726345585543573853636374744F6855775539423155394135622B61536151334A6F54574671753069486D47686F7432335971744D51735164666C34614448452F5955487678584864565A796A5A416D4B4C49453233693067597249735A527432377A775876326D2F66546C4D58362B472F7961355078626F337865616932457A53416838306833787A547474454773395351482B2F432B5A6554666B67546E31507A666D547A666C66546B4939392F79707756796478734F704475783674533356635057436F3478385837302F7738686B35625163546341547A324D334C77736A6A6E624D2B633535746739363965784A792F6D324158376C4772423542527043766877784651716478764A62684E634B5A4C3936596B7A7A4C42625653316E386E6E4A64787244387137515361344933647A48494A4C782B6E57367047385830325673426552714C582B71594A7271434B4757794976593067764A357756755454664D4A666D3055375A45784375432F4A75453978523232434E30584B744E2B57636B6A6B643062637978694A365774414E62353473654463697636757748574343323746643642634D5355513770454E7A7875336A556765344E4D38497646684C726B6D51734D46687758364556796F706568687A5A49773578304C344473624E635242304A66514164477456643968363862706A4A70466547303037613166772B6732384C5A62394948694E31307071573463783355674461724F5A4E35597449712B7261542F2B6C4E6F57336E305A527271325634394149377874346A744A4F34327846535A38357436327755534362656943654777636A5A31792B675731697653444D6F7A30546B544A55506B556A315656552F65306F734B33636336354869526B4E5258656D325559366145393742304F6554775A613244432B2B786254666730544C5659545A462B574977794B74314D6857432B31554E77347738783871786F754C2B3666344761494C304A3559715437557044306A556E6C3771735638704E2B49436B79515453727A6242566D4B6B3653624539326C49656F4377486C7251725632596334384A2F796D5A6274446B70697670326F3638586E4248464D476F4175547A4E4D3370577932437569547776577A6746423143352B50316F455337424B71696248697339332B51376D4F46366731664B486A424250764E655648477A7A644A37424456456C744F616455306766705939396631464B73656F476A7638504F6C704137645A446B687A6A6E314C487768356542464E5647797153635070337A52666439474E42303649647761782F6975486A61596C51626B617175314F7753764E2B476A567039547170316E676D31645863437234346836326A50586B374F5448635973354764484D3361523566314F316F5A783274545263335172616F4A304C544F754D73486563694B6D44334253476E693552693039665871704352353348686E7859505641596A385450707173326D56646C416734326F6F494C31716177445956682F634C586A48424C567074757478676E413948364C314E685042396647392B545A44754E346B664D7452636549734A62694C556468764751382B69524E4E652B306F54507257716E71363938686D51744A685157326A436634335346636359514370645243694F5A7270686F7A747A453069522B6C722F4975596436376F614366516B55562F65617871646D3867766C6831446F6361725A4F4E644942712B376A54526435685376556171696A7A534F70762F6E736C7235694441394C6855475637664159386537544458667367627A2F6859707566766444763564753768627542552F6378454B42733353473855682F4372536A4671324E6B4F79664D67665763566F6A4972536A5045546348704E466557772B63616A6654736575692F4B666B71536A4E704C6C36764278354F516F5346446C4C6F6D6267503850444663524165656D355447542B492B5871623843455131526136632F63323139336E46756D62574E6E466C68646F54567269554B2B57574D304B502B383349745A556B61364B73776E6A5A373479457A356B4744704F3747626E626A6371655A655663774D4F6E62416C654A76757076316F676E766830577744595675397552574C794D647A325779534F39623048564165576C7233716F35545A4649347975337A2F765041756D666566727048756D6365365A356C705030727741444766685A7833374E41374141414141424A52553545726B4A6767673D3D\";s:4:\"host\";s:114:\"687474703A2F2F7777772E696A682E63632F696E6465782E7068703F63746C3D6D6F6E69746F72696E6726686F73743D2573266B65793D2573\";}','配置设置',1389324222),('sms','a:10:{s:10:\"show_error\";s:1:\"1\";s:11:\"remind_open\";s:1:\"1\";s:5:\"comid\";s:0:\"\";s:9:\"smsnumber\";s:0:\"\";s:5:\"uname\";s:0:\"\";s:6:\"passwd\";s:0:\"\";s:6:\"mobile\";s:0:\"\";s:7:\"sms_num\";s:1:\"0\";s:11:\"verify_open\";s:1:\"1\";s:5:\"title\";s:0:\"\";}','短信设置',1536753364),('tixian','a:3:{s:3:\"day\";s:1:\"0\";s:5:\"limit\";s:3:\"100\";s:4:\"shop\";s:1:\"1\";}','提现',1536307753),('tjhongbao','a:4:{s:5:\"title\";s:12:\"天降红包\";s:5:\"intro\";s:41:\"天上掉红包,明天可能还会有哦!\";s:6:\"status\";s:1:\"1\";s:7:\"hongbao\";a:2:{i:0;a:7:{s:10:\"min_amount\";s:2:\"40\";s:6:\"amount\";s:1:\"9\";s:3:\"day\";s:1:\"1\";s:5:\"title\";s:12:\"深夜食堂\";s:4:\"type\";s:3:\"all\";s:5:\"stime\";s:5:\"22:30\";s:5:\"ltime\";s:12:\"次日 02:00\";}i:1;a:7:{s:10:\"min_amount\";s:2:\"20\";s:6:\"amount\";s:1:\"3\";s:3:\"day\";s:1:\"1\";s:5:\"title\";s:9:\"下午茶\";s:4:\"type\";s:3:\"all\";s:5:\"stime\";s:5:\"11:00\";s:5:\"ltime\";s:5:\"12:00\";}}}','',1513135495),('unit','a:1:{s:4:\"unit\";a:15:{i:0;s:3:\"份\";i:1;s:3:\"个\";i:2;s:3:\"件\";i:3;s:3:\"次\";i:4;s:6:\"平米\";i:5;s:6:\"小时\";i:6;s:3:\"斤\";i:7;s:3:\"两\";i:8;s:6:\"公斤\";i:9;s:3:\"台\";i:10;s:3:\"套\";i:11;s:3:\"条\";i:12;s:3:\"双\";i:13;s:3:\"座\";i:14;s:3:\"张\";}}','',1517972698),('waimaihuodongconfig','a:19:{s:7:\"hongbao\";s:1:\"0\";s:5:\"first\";s:1:\"0\";s:7:\"manjian\";s:1:\"0\";s:6:\"youhui\";s:1:\"0\";s:6:\"manfan\";s:1:\"0\";s:9:\"closesite\";s:1:\"4\";s:9:\"opendaofu\";s:1:\"1\";s:8:\"neworder\";s:1:\"1\";s:8:\"cuiorder\";s:1:\"1\";s:8:\"tuiorder\";s:1:\"1\";s:2:\"go\";s:1:\"1\";s:4:\"show\";s:1:\"0\";s:11:\"user_tixian\";s:1:\"1\";s:7:\"autopei\";s:1:\"1\";s:12:\"autopei_time\";s:2:\"30\";s:9:\"autobills\";s:1:\"1\";s:15:\"autobills_staff\";s:1:\"0\";s:12:\"shop_orderby\";s:4:\"juli\";s:14:\"staff_distance\";s:3:\"300\";}','',1536548361),('wechat','a:14:{s:5:\"appid\";s:18:\"wx7473ee670e5ffcad\";s:9:\"appsecret\";s:32:\"e4ffc9d293e1423f0e3816e432e24f6c\";s:12:\"wechat_token\";s:32:\"52b70f8461bdfd22ce8e69a88333a267\";s:13:\"wechat_aeskey\";s:43:\"q9d2kHDok4jo5IRrU8WQ3oI4TGSp1VlU74aZq1hfzhM\";s:9:\"app_appid\";s:0:\"\";s:13:\"app_appsecret\";s:0:\"\";s:13:\"open_mp_appid\";s:0:\"\";s:17:\"open_mp_appsecret\";s:0:\"\";s:13:\"open_mp_token\";s:0:\"\";s:14:\"open_mp_aeskey\";s:0:\"\";s:14:\"subscribe_open\";s:1:\"1\";s:12:\"subscribe_qr\";s:58:\"photo/201711/20171116_195820EF1A4DE84920AD4BB8FFFA08AC.jpg\";s:6:\"wxtmpl\";s:1:\"0\";s:9:\"wx_alipay\";s:1:\"0\";}','微信配置',1542878359),('weidian','a:1:{s:6:\"domain\";s:8:\"jhcms.cn\";}','微店设置',1476275332),('wxtmpl','a:2:{s:4:\"wxmp\";a:1:{s:5:\"order\";s:43:\"EjTzhEwrMJx8ynnSK65i6iOER2N8oW2vmOA-QgDILSo\";}s:5:\"wxapp\";a:1:{s:5:\"order\";s:43:\"5IACzxMLCXfx0CXPR9WuYgYgsh_qR4rkIMf416JaaGE\";}}','模板消息',1506675185),('wx_config','a:4:{s:12:\"order_number\";s:7:\"TM00017\";s:8:\"order_id\";s:43:\"KKIfDKtqdsIZa0Swe_ZX82Kj8D0M4TEUkEGlvTm5H0E\";s:12:\"money_number\";s:15:\"OPENTM205454780\";s:8:\"money_id\";s:43:\"zn3sAuL36ea6d_VHyJPDsz4PqUy-fXSMTZEGEaucnsw\";}','微信模版消息',1453098824);
/*!40000 ALTER TABLE `jh_system_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_system_logs`
--

DROP TABLE IF EXISTS `jh_system_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_system_logs` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin` varchar(30) DEFAULT '',
  `action` varchar(50) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `content` mediumtext,
  `dateline` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_system_logs`
--

LOCK TABLES `jh_system_logs` WRITE;
/*!40000 ALTER TABLE `jh_system_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_system_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_system_module`
--

DROP TABLE IF EXISTS `jh_system_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_system_module` (
  `mod_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `module` enum('top','menu','module') DEFAULT 'module',
  `level` tinyint(1) DEFAULT '3',
  `ctl` varchar(32) DEFAULT '',
  `act` varchar(20) DEFAULT '',
  `title` varchar(20) DEFAULT '',
  `visible` tinyint(1) DEFAULT '1',
  `parent_id` smallint(6) DEFAULT '0',
  `orderby` smallint(6) DEFAULT '50',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`mod_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2763 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_system_module`
--

LOCK TABLES `jh_system_module` WRITE;
/*!40000 ALTER TABLE `jh_system_module` DISABLE KEYS */;
INSERT INTO `jh_system_module` VALUES (1,'top',1,'','','设置',1,0,11,1356434427),(5,'top',1,'','','运营',1,0,18,1356434427),(6,'menu',2,'','','权限管理',1,601,1,1356434427),(7,'menu',2,'','','模块管理',1,601,2,1356434427),(8,'module',3,'module/menu','index','导航菜单管理',1,7,1,1356434427),(9,'module',3,'module/ctl','index','控制模型管理',1,7,11,1356434427),(26,'module',3,'module/menu','create','添加导航菜单',0,7,2,1356434427),(27,'module',3,'module/menu','save','保存导航菜单',0,7,6,1356434427),(28,'module',3,'module/menu','edit','编辑导航菜单',0,7,3,1356434427),(31,'module',3,'module/ctl','batch','指量添加控制模块',0,7,13,1356434427),(32,'module',3,'module/ctl','save','保存控制模块',0,7,14,1356434427),(33,'module',3,'module/ctl','detail','管理控制模型',0,7,12,1356434427),(35,'module',3,'module/menu','update','更新导航菜单',0,7,4,1356434427),(37,'module',3,'module/ctl','remove','删除控制模块',0,7,15,1356434427),(44,'module',3,'module/menu','remove','删除导航菜单',0,7,5,1356437401),(48,'module',3,'admin/role','index','角色管理',1,6,1,1356437591),(49,'module',3,'admin/admin','index','管理员管理',1,6,2,1356437975),(50,'module',3,'admin/role','create','创建角色',0,6,50,1356437975),(51,'module',3,'admin/role','detail','管理角色',0,6,50,1356437975),(52,'module',3,'admin/role','save','保存角色',0,6,50,1356437975),(53,'module',3,'admin/role','delete','删除角色',0,6,50,1356437975),(54,'module',3,'admin/admin','create','创建管理员',0,6,50,1356437975),(55,'module',3,'admin/admin','edit','修改管理员',0,6,50,1356437975),(56,'module',3,'admin/admin','save','保存管理员',0,6,50,1356437975),(57,'module',3,'admin/admin','delete','删除管理员',0,6,50,1356437975),(68,'menu',2,'','','页面设置',1,5,3,1356513698),(71,'top',1,'','','用户',1,0,15,1356513738),(72,'menu',2,'','','会员管理',1,71,1,1356513776),(113,'module',3,'adv/adv','index','广告位管理',0,68,50,1357460157),(114,'module',3,'adv/adv','detail','管理广告位',0,68,50,1357460260),(115,'module',3,'adv/adv','edit','编辑广告位',0,68,50,1357460260),(116,'module',3,'adv/adv','create','创建广告位',0,68,50,1357460260),(117,'module',3,'adv/adv','delete','删除广告位',0,68,50,1357460260),(119,'module',3,'adv/item','create','添加广告',0,68,50,1357460574),(120,'module',3,'adv/item','edit','修改广告',0,68,50,1357460574),(122,'module',3,'adv/item','delete','删除广告',0,68,50,1357460574),(123,'module',3,'adv/adv','update','更新广告位',0,68,50,1357462189),(124,'module',3,'adv/item','update','更新广告内容',0,68,50,1357463273),(127,'top',1,'','','工具',1,0,80,1357609135),(142,'module',3,'member/member','index','会员列表',1,72,10,1357714750),(144,'module',3,'member/member','ulock','锁定会员',0,72,14,1357714750),(146,'module',3,'member/member','recycle','会员回收站',1,72,15,1357714750),(188,'module',3,'member/member','delete','删除会员',0,72,16,1358501680),(189,'module',3,'member/member','regain','恢复会员',0,72,17,1358501680),(244,'menu',2,'','','站长工具',1,127,52,1366388132),(245,'module',3,'tools/cache','clean','清空缓存',1,244,50,1366388194),(269,'menu',2,'','','网站设置',1,1,1,1370085075),(279,'menu',2,'','','数据库管理',1,127,50,1371537222),(383,'module',3,'member/member','create','添加会员',0,72,11,1372437934),(384,'module',3,'member/member','edit','修改会员',0,72,12,1372437934),(385,'module',3,'member/member','so','搜索会员',0,72,13,1372438141),(386,'module',3,'system/config','site','基本设置',1,269,1,1372869314),(419,'module',3,'member/log','index','余额日志',1,72,50,1373683486),(420,'module',3,'member/log','so','日志搜索',0,72,50,1373683486),(430,'module',3,'member/member','money','充值余额',0,72,18,1373792200),(470,'module',3,'system/config','attach','附件设置',1,269,2,1374459620),(506,'module',3,'system/config','sms','短信设置',1,951,50,1376155472),(515,'module',3,'adv/adv','so','搜索广告位',0,68,50,1376479539),(516,'module',3,'magic/upload','editor','编辑器上传图片',0,269,50,1376590326),(551,'menu',2,'','','支付配置',1,1,3,1380438926),(553,'module',3,'payment/payment','index','支付接口',1,551,50,1380440527),(554,'module',3,'tools/database','index','数据库管理',1,279,50,1380561710),(562,'module',3,'member/member','ucard','会员卡片层',0,72,19,1381942505),(570,'module',3,'member/member','face','更新头像',0,72,50,1383112630),(601,'top',1,'','','系统',1,0,10,1383820332),(602,'module',3,'member/member','dialog','选择用户',0,72,50,1383980953),(670,'menu',2,'','','模板设置',1,1,4,1384760168),(671,'module',3,'system/theme','index','模板管理',1,670,50,1384760203),(891,'module',3,'payment/payment','config','配置接口',0,551,50,1388027577),(892,'module',3,'payment/payment','install','安装接口',0,551,50,1388027577),(893,'module',3,'payment/payment','uninstall','卸载接口',0,551,50,1388027577),(897,'module',3,'payment/log','index','支付流水',1,551,50,1388048090),(899,'module',3,'payment/log','so','修改流水',0,551,50,1388048090),(901,'module',3,'payment/log','status','更改状态',0,551,50,1388048090),(919,'module',3,'member/member','manager','管理会员',0,72,50,1388485506),(922,'module',3,'system/theme','detail','管理模板',0,670,50,1389258144),(923,'module',3,'system/theme','install','安装模板',0,670,50,1389258144),(924,'module',3,'system/theme','uninstall','卸载模板',0,670,50,1389258144),(925,'module',3,'system/theme','setdefault','设置默认模板',0,670,50,1389258144),(951,'menu',2,'','','通知设置',1,71,2,1403261297),(993,'module',3,'adv/item','doaudit','审核广告',0,68,50,1250176349),(1011,'module',3,'tools/database','backup','备份数据库',0,279,50,1413193393),(1012,'module',3,'tools/database','backlist','备份列表',0,279,50,1413193393),(1013,'module',3,'tools/database','backdel','删除备份',0,279,50,1413193393),(1014,'module',3,'tools/database','restore','还原数据库',0,279,50,1413193393),(1015,'module',3,'tools/database','optimize','优化数据库',0,279,50,1413193393),(1016,'module',3,'adv/adv','config','广告位设置',0,68,50,1413384837),(1017,'module',3,'adv/adv','code','调用代码',0,68,50,1413384837),(1018,'module',3,'system/theme','tmpledit','编辑模板',0,670,50,1413429248),(1019,'module',3,'system/theme','tmplbak','查看备份',0,670,50,1413429248),(1020,'module',3,'system/theme','tmplrestore','还原模板',0,670,50,1413429248),(1021,'module',3,'system/theme','tmpldelbak','删除备份',0,670,50,1413429794),(1022,'module',3,'system/theme','tmplsave','修改入库',0,670,50,1413447673),(1025,'module',3,'hongbao/hongbao','index','红包管理',1,72,50,1413947742),(1026,'module',3,'hongbao/hongbao','create','创建红包',0,72,50,1413947742),(1027,'module',3,'hongbao/hongbao','add','添加红包',0,72,50,1413947742),(1028,'module',3,'hongbao/hongbao','edit','修改红包',0,72,50,1413947742),(1029,'module',3,'hongbao/hongbao','delete','删除红包',0,72,50,1413947742),(1096,'module',3,'system/config','access','访问设置',1,269,50,1419059027),(1130,'module',3,'system/config','wechat','微信配置',1,269,5,1420386712),(1183,'module',3,'adv/item','index','内容管理',0,68,50,1431152244),(1184,'module',3,'adv/item','so','搜索内容',0,68,50,1431167794),(1196,'module',3,'sms/log','index','短信日志',1,951,50,1435192593),(1275,'module',3,'member/member','detail','会员资料',0,72,50,1442051185),(1284,'module',3,'member/message','index','消息列表',1,951,50,1442546094),(1285,'module',3,'member/message','create','添加消息',0,951,50,1442546094),(1286,'module',3,'member/message','edit','修改消息',0,951,50,1442546094),(1287,'module',3,'member/message','delete','删除消息',0,951,50,1442546094),(1294,'module',3,'system/config','wxtmpl','模版消息',1,269,50,1445569927),(1329,'module',3,'staff/complaint','replay','回复投诉',0,951,50,1446284079),(1341,'module',3,'hongbao/hongbao','so','红包搜索',0,72,50,1448333318),(1345,'module',3,'member/addr','index','收货地址',1,72,50,1448339823),(1346,'module',3,'member/addr','create','添加收货地址',0,72,50,1448341927),(1347,'module',3,'member/addr','edit','修改收货地址',0,72,50,1448342007),(1348,'module',3,'member/addr','delete','删除收货地址',0,72,50,1448342090),(1349,'module',3,'member/addr','detail','收货地址详情',0,72,50,1448342090),(1350,'module',3,'member/addr','so','搜索收货地址',0,72,50,1448342367),(1351,'module',3,'hongbao/hongbao','detail','查看红包',0,72,50,1448343749),(1373,'module',3,'mall/cate','index','商品分类',1,2490,50,1448349756),(1374,'module',3,'mall/product','index','商品列表',1,2490,50,1448350190),(1375,'module',3,'mall/cate','create','添加分类',0,2490,50,1448350795),(1376,'menu',2,'','','数据设置',1,1,2,1448351132),(1377,'module',3,'data/province','index','省份管理',1,1376,50,1448351276),(1378,'module',3,'data/province','create','添加省份',0,1376,50,1448351276),(1379,'module',3,'data/province','edit','修改省份',0,1376,50,1448351276),(1380,'module',3,'data/province','delete','删除省份',0,1376,50,1448351276),(1381,'module',3,'data/city','index','城市管理',1,1376,50,1448351276),(1382,'module',3,'data/city','create','添加城市',0,1376,50,1448351276),(1383,'module',3,'data/city','edit','修改城市',0,1376,50,1448351276),(1384,'module',3,'data/city','delete','删除城市',0,1376,50,1448351276),(1385,'module',3,'data/city','so','搜索城市',0,1376,50,1448351276),(1386,'module',3,'mall/cate','edit','修改分类',0,2490,50,1448351599),(1387,'module',3,'mall/cate','delete','删除分类',0,2490,50,1448351599),(1388,'module',3,'mall/cate','so','搜索分类',0,2490,50,1448351599),(1389,'module',3,'mall/product','create','添加商品',0,2490,50,1448353142),(1390,'module',3,'mall/product','edit','修改分类',0,2490,50,1448353142),(1391,'module',3,'mall/product','delete','删除分类',0,2490,50,1448353142),(1392,'module',3,'mall/product','so','搜索分类',0,2490,50,1448353142),(1393,'module',3,'mall/cate','detail','查看分类',0,2490,50,1448353208),(1394,'module',3,'mall/product','detail','查看商品',0,2490,50,1448353208),(1407,'module',3,'mall/order','index','订单管理',1,2490,50,1448354982),(1408,'module',3,'mall/order','so','搜索订单',0,2490,50,1448355182),(1429,'module',3,'system/config','invite','好友邀请',1,269,50,1449481112),(1430,'menu',2,'','','文章管理',1,5,2,1450143779),(1431,'module',3,'article/cate','index','文章分类',0,1430,1,1450143874),(1432,'module',3,'article/cate','create','添加分类',0,1430,50,1450143874),(1433,'module',3,'article/cate','edit','编辑分类',0,1430,50,1450143874),(1434,'module',3,'article/cate','delete','删除分类',0,1430,50,1450143874),(1435,'module',3,'article/article','index','文章列表',0,1430,2,1450144179),(1436,'module',3,'article/article','create','添加文章',0,1430,50,1450160838),(1437,'module',3,'article/article','edit','编辑文章',0,1430,50,1450160838),(1438,'module',3,'article/article','delete','删除文章',0,1430,50,1450160838),(1441,'module',3,'article/help','index','帮助中心',1,1430,3,1450161189),(1442,'module',3,'article/help','create','添加帮助',0,1430,50,1450161189),(1443,'module',3,'article/help','edit','编辑帮助',0,1430,50,1450161189),(1444,'module',3,'article/help','delete','删除帮助',0,1430,50,1450161189),(1445,'module',3,'article/help','so','搜索帮助',0,1430,50,1450161189),(1446,'module',3,'article/about','index','关于我们',1,1430,4,1450161332),(1447,'module',3,'article/about','create','添加内容',0,1430,50,1450161332),(1448,'module',3,'article/about','edit','编辑内容',0,1430,50,1450161332),(1449,'module',3,'article/about','delete','删除内容',0,1430,50,1450161332),(1450,'module',3,'article/about','so','搜索内容',0,1430,50,1450161332),(1451,'module',3,'article/cate','update','更新分类',0,1430,50,1450162157),(1452,'module',3,'system/config','jifen','积分设置',1,269,50,1451275113),(1465,'module',3,'member/message','detail','查看消息',0,951,50,1451480119),(1472,'module',3,'mall/order','delivery','订单发货',0,2490,50,1451552755),(1473,'module',3,'mall/order','delete','删除订单',0,2490,50,1451552755),(1474,'module',3,'mall/order','detail','查看订单',0,2490,50,1451552755),(1482,'module',3,'member/invite','index','会员返利',1,72,50,1452823418),(1493,'module',3,'system/config','app_download','APP版本',1,269,50,1453277651),(1523,'top',1,'','','财务',1,0,17,1456279863),(1524,'menu',2,'','','订单对账',1,1523,1,1456293593),(1531,'menu',2,'','','商家提现',1,1523,2,1456300314),(1571,'module',3,'data/area','index','区县管理',1,1376,50,1458871224),(1572,'module',3,'data/area','create','添加区县',0,1376,50,1458871224),(1573,'module',3,'data/area','edit','修改区县',0,1376,50,1458871224),(1574,'module',3,'data/area','delete','删除区县',0,1376,50,1458871224),(1575,'module',3,'data/area','so','搜索区县',0,1376,50,1458871873),(1576,'module',3,'data/business','index','商圈管理',0,1376,50,1458877744),(1577,'module',3,'data/business','create','添加商圈',0,1376,50,1458877744),(1578,'module',3,'data/business','edit','修改商圈',0,1376,50,1458877744),(1579,'module',3,'data/business','delete','删除商圈',0,1376,50,1458877744),(1580,'module',3,'data/business','so','搜索商圈',0,1376,50,1458877744),(1583,'module',3,'system/config','apppush','推送配置(旧)',0,1584,50,1460959945),(1584,'menu',2,'','','推送管理',1,5,5,1460960991),(1678,'module',3,'jpush/device','index','安装设备',1,1584,50,1465382925),(1679,'module',3,'jpush/device','edit','修改设备',0,1584,50,1465382925),(1680,'module',3,'jpush/device','push','推送信息',1,1584,50,1465382925),(1681,'module',3,'jpush/log','index','推送日志',1,1584,50,1465382925),(1682,'module',3,'jpush/tag','index','标签管理',0,1584,50,1465382925),(1683,'module',3,'jpush/tag','create','创建标签',0,1584,50,1465382925),(1684,'module',3,'jpush/tag','edit','修改标签',0,1584,50,1465382925),(1685,'module',3,'jpush/tag','delete','删除标签',0,1584,50,1465382925),(1701,'module',3,'data/area','options','区县数据',0,1376,50,1466079002),(1702,'module',3,'data/business','options','商圈数据',0,1376,50,1466079002),(1843,'module',3,'mall/order','cancel','取消订单',0,2490,50,1472890486),(1844,'module',3,'mall/order','fahuo','订单发货',0,2490,50,1472892664),(1922,'module',3,'system/config','moneypack','充值配置',1,269,50,1480732946),(1923,'menu',2,'','','友情链接',1,5,7,1481096339),(1924,'module',3,'links/links','index','链接列表',1,1923,1,1481096370),(1925,'module',3,'links/links','create','添加',0,1923,50,1481096370),(1926,'module',3,'links/links','edit','编辑',0,1923,50,1481096370),(1927,'module',3,'links/links','delete','删除',0,1923,50,1481096370),(1962,'top',1,'','','外卖',1,0,12,1489629188),(1966,'menu',2,'','','商家管理',1,1962,1,1489643309),(1968,'module',3,'waimai/waimai','index','商家列表',1,1966,1,1492678611),(1969,'module',3,'waimai/cate','index','商家分类',1,1966,2,1492678999),(1970,'module',3,'waimai/comment','index','商家评价',1,1966,3,1492737521),(1971,'module',3,'waimai/log','index','商家余额日志',1,1524,51,1492738331),(1972,'module',3,'waimai/recycle','index','商家回收站',1,1966,5,1492738331),(1973,'module',3,'waimai/waimai','detail','商家详情',0,1966,50,1492738331),(1974,'module',3,'waimai/waimai','create','添加外卖商家',0,1966,50,1492738331),(1975,'module',3,'waimai/waimai','edit','商家编辑',0,1966,50,1492738331),(1976,'module',3,'waimai/waimai','delete','商家删除',0,1966,50,1492738331),(1977,'module',3,'waimai/waimai','so','商家搜索',0,1966,50,1492738331),(1978,'module',3,'waimai/cate','create','添加分类',0,1966,50,1492738331),(1979,'module',3,'waimai/cate','edit','编辑分类',0,1966,50,1492738331),(1980,'module',3,'waimai/cate','delete','删除分类',0,1966,50,1492738331),(1981,'module',3,'waimai/cate','update','更新分类',0,1966,50,1492738331),(1982,'module',3,'waimai/cate','detail','查看分类',0,1966,50,1492738331),(1983,'module',3,'waimai/comment','detail','查看评价',0,1966,50,1492738331),(1984,'module',3,'waimai/comment','so','搜索评价',0,1966,50,1492738331),(1985,'module',3,'waimai/comment','delete','删除评价',0,1966,50,1492738331),(1986,'module',3,'waimai/comment','recovery','恢复评价',0,1966,50,1492738331),(1987,'menu',2,'','','订单管理',1,1962,2,1492738649),(1988,'module',3,'waimai/order','index','订单列表',1,1987,1,1492738990),(1989,'module',3,'waimai/order','rights','维权订单',1,1987,2,1492738990),(1990,'module',3,'waimai/order','detail','查看订单',0,1987,50,1492738990),(1991,'module',3,'waimai/order','cancel','取消订单',0,1987,50,1492738990),(1992,'module',3,'waimai/order','complete','完成订单',0,1987,50,1492738990),(1993,'module',3,'waimai/order','so','搜索订单',0,1987,50,1492738990),(1994,'module',3,'waimai/order','jiedan','接单',0,1987,50,1492738990),(1995,'menu',2,'','','商家审核',1,1962,3,1492739362),(1996,'module',3,'waimai/apply','index','入驻审核',1,1995,1,1492739449),(1997,'module',3,'waimai/huodong','index','活动审核',1,1995,2,1492743499),(1998,'menu',2,'','','外卖管理',1,1962,4,1492743552),(2000,'module',3,'waimai/order','dopaidan','派单',0,1998,50,1492743768),(2002,'module',3,'waimai/order','paidan','派单管理',1,1998,2,1492743893),(2017,'module',3,'waimai/waimai','manage','外卖管理',0,1966,50,1492755077),(2042,'module',3,'waimai/recycle','delete','彻底删除',0,1966,50,1492769068),(2043,'module',3,'waimai/recycle','recovery','恢复商家',0,1966,50,1492769068),(2049,'module',3,'waimai/shop','one','设置店铺信息',0,1966,50,1492770396),(2050,'module',3,'waimai/shop','two','设置资质信息',0,1966,50,1492770396),(2051,'module',3,'waimai/shop','three','设置配送信息',0,1966,50,1492770396),(2052,'module',3,'waimai/shop','four','设置帐户信息',0,1966,50,1492770396),(2114,'module',3,'staff/staff','doaudit','审核骑手',0,2417,12,1492841822),(2122,'module',3,'staff/verify','index','骑手认证',1,2417,20,1492841981),(2183,'module',3,'waimai/shop','uploadimg','异步上传图片',0,1966,50,1492844833),(2188,'module',3,'waimai/recycle','so','回收商家搜索',0,1966,50,1493006510),(2189,'module',3,'waimai/apply','detail','商家详情',0,1995,50,1493023452),(2190,'module',3,'waimai/apply','delete','商家删除',0,1995,50,1493023452),(2191,'module',3,'waimai/apply','audit','商家审核',0,1995,50,1493023452),(2192,'module',3,'waimai/apply','so','入驻商家搜索',0,1995,50,1493023452),(2193,'module',3,'waimai/huodong','so','搜索活动',0,1995,50,1493090476),(2194,'module',3,'waimai/huodong','shop','商家活动详情',0,1995,50,1493090476),(2195,'module',3,'waimai/huodong','detail','活动详情',0,1995,50,1493090476),(2196,'module',3,'waimai/huodong','create','添加活动',0,1995,50,1493090476),(2197,'module',3,'waimai/huodong','edit','编辑活动',0,1995,50,1493090476),(2198,'module',3,'waimai/huodong','delete','撤销活动',0,1995,50,1493090476),(2199,'module',3,'waimai/huodong','audit','审核活动',0,1995,50,1493090476),(2210,'module',3,'waimai/order','agree','同意退款',0,1987,50,1493362702),(2211,'module',3,'waimai/order','refuse','拒绝退款',0,1987,50,1493362702),(2232,'module',3,'finance/waimai','bills','外卖商户对账',1,1524,1,1493807224),(2236,'module',3,'finance/waimai','detail','外卖对账详情',0,1524,50,1493861982),(2240,'module',3,'finance/waimai','so','外卖帐单搜索',0,1524,50,1493861982),(2244,'module',3,'finance/waimai','shop','外卖对账商家',0,1524,50,1493879359),(2248,'module',3,'finance/account','index','商家提现',1,1531,1,1493951313),(2249,'module',3,'finance/account','agree','同意提现',0,1531,50,1493951313),(2250,'module',3,'finance/account','so','提现搜索',0,1531,50,1493951313),(2251,'module',3,'finance/account','loan','提现打款',0,1531,50,1493951313),(2252,'module',3,'finance/account','complete','提现完成',0,1531,50,1493951313),(2253,'module',3,'finance/account','wait','待转账列表',1,1531,2,1493951932),(2254,'module',3,'finance/account','logs','转账记录',1,1531,3,1493951932),(2255,'module',3,'finance/account','refund','拒绝提现',0,1531,50,1493965425),(2256,'menu',2,'','','数据统计',1,1523,3,1493971391),(2257,'module',3,'tongji/tongji','index','营业统计',1,2256,1,1493971569),(2258,'module',3,'tongji/money','index','资金入账',1,2256,2,1493971569),(2259,'module',3,'tongji/tongji','so','统计搜索',0,2256,50,1493971569),(2260,'module',3,'tongji/money','get_data','账单数据',0,2256,50,1493971569),(2261,'module',3,'waimai/log','so','日志搜索',0,1966,50,1494227619),(2262,'module',3,'waimai/log','detail','日志详情',0,1966,50,1494227619),(2265,'module',3,'tongji/tongji','get_chart','统计数据',0,2256,50,1494292714),(2266,'module',3,'finance/waimai','index','外卖商户账单',0,1524,50,1494295819),(2270,'module',3,'finance/account','export','导出账单',0,1531,50,1494311236),(2271,'module',3,'tongji/tongji','get_chart_order','订单量统计',0,2256,50,1494318222),(2272,'menu',2,'','','外卖活动',1,1962,5,1494812333),(2273,'module',3,'waimai/activity','index','活动列表',1,2272,1,1494812483),(2274,'module',3,'waimai/activity','create','添加活动',0,2272,50,1494812483),(2275,'module',3,'waimai/activity','edit','编辑活动',0,2272,50,1494812483),(2276,'module',3,'waimai/activity','delete','删除活动',0,2272,50,1494812483),(2277,'module',3,'waimai/activity','so','活动搜索',0,2272,50,1494817486),(2278,'module',3,'waimai/activity','shop','活动商家',0,2272,50,1494817486),(2279,'module',3,'waimai/activity','product','活动商品',0,2272,50,1494817486),(2280,'module',3,'waimai/activity','dialog','添加项目',0,2272,50,1494832798),(2281,'module',3,'waimai/activity','itemdel','删除项目',0,2272,50,1494832798),(2282,'module',3,'waimai/activity','itemso','搜索项目',0,2272,50,1494834696),(2283,'module',3,'system/config','print','云打印设置',1,269,50,1494834943),(2284,'module',3,'waimai/shop','set_print','外卖商家打印机',0,1966,50,1494838559),(2285,'module',3,'waimai/activity','add','添加',0,2272,50,1494838728),(2286,'module',3,'system/config','hongbao','下单分享红包设置',1,269,50,1495504680),(2287,'module',3,'tongji/tongji','get_columnar_chart','柱状图统计数据',0,2256,50,1495622189),(2288,'module',3,'staff/bills','index','骑手配送对账',1,1524,11,1495855856),(2294,'module',3,'finance/waimai','comfirm_bills','外卖商户入账',0,1524,50,1496999305),(2298,'module',3,'system/config','automatic','计划任务',1,269,50,1497064745),(2409,'module',3,'system/config','tjhongbao','红包设置',0,1998,3,1497407703),(2410,'module',3,'system/config','tixian','提现设置',1,269,50,1497427037),(2412,'module',3,'system/config','hotwaimai','外卖热搜',1,1998,4,1497952773),(2413,'module',3,'finance/jifen','bills','积分商城对账',0,1524,50,1498459597),(2414,'module',3,'finance/jifen','index','积分商城详情',0,1524,50,1498460557),(2415,'module',3,'system/config','ditui','推广设置',1,2423,1,1498613909),(2417,'menu',2,'','','配送中心',1,1962,50,1499323856),(2418,'module',3,'group/group','items','配送站管理',1,2417,1,1499323912),(2419,'module',3,'group/group','create','配送站添加',0,2417,50,1499323950),(2421,'module',3,'group/group','manage','管理配送站',0,2417,50,1499395469),(2423,'menu',2,'','','地推管理',1,1962,50,1499763159),(2424,'module',3,'ditui/ditui','index','地推管理',1,2423,2,1499764005),(2425,'module',3,'ditui/ditui','detail','查看地推',0,2423,50,1499764005),(2426,'module',3,'ditui/ditui','edit','修改地推',0,2423,50,1499764005),(2427,'module',3,'ditui/ditui','doaudit','审核地推',0,2423,50,1499764005),(2428,'module',3,'ditui/ditui','delete','删除地推',0,2423,50,1499764005),(2429,'module',3,'ditui/ditui','create','添加地推',0,2423,50,1499764005),(2430,'module',3,'ditui/ditui','so','搜索地推',0,2423,50,1499764005),(2431,'module',3,'ditui/log','index','日志管理',1,2423,50,1499764005),(2432,'module',3,'ditui/log','detail','日志查看',0,2423,50,1499764005),(2433,'module',3,'ditui/log','so','日志搜索',0,2423,50,1499764005),(2434,'module',3,'ditui/tixian','index','提现管理',1,2423,50,1499764005),(2435,'module',3,'ditui/tixian','detail','提现查看',0,2423,50,1499764005),(2436,'module',3,'ditui/tixian','delete','提现删除',0,2423,50,1499764005),(2437,'module',3,'ditui/tixian','doaudit','提现同意',0,2423,50,1499764005),(2438,'module',3,'ditui/tixian','refuse','提现拒绝',0,2423,50,1499764005),(2439,'module',3,'ditui/tixian','remittance','提现汇款',0,2423,50,1499764005),(2440,'module',3,'ditui/tixian','so','提现搜索',0,2423,50,1499764005),(2441,'module',3,'ditui/member','index','会员管理',1,2423,50,1499764005),(2442,'module',3,'ditui/member','detail','查看会员',0,2423,50,1499764005),(2444,'module',3,'ditui/member','so','会员搜索',0,2423,50,1499764005),(2445,'module',3,'ditui/ditui','dialog','地推选择',0,2423,50,1499764005),(2455,'module',3,'group/staff','index','骑手管理',1,2417,2,1499768582),(2456,'module',3,'group/staff','edit','修改服务人员',0,2417,50,1499768582),(2457,'module',3,'group/staff','doaudit','审核服务人员',0,2417,50,1499768582),(2458,'module',3,'group/staff','delete','删除服务人员',0,2417,50,1499768582),(2459,'module',3,'group/staff','create','添加服务人员',0,2417,50,1499768582),(2460,'module',3,'group/staff','so','搜索骑手',0,2417,50,1499768582),(2461,'module',3,'group/staff','dialog','选择骑手',0,2417,50,1499768582),(2462,'module',3,'group/staff','recycle','骑手回收站',1,2417,4,1499768582),(2463,'module',3,'group/staff','regain','恢复骑手',0,2417,50,1499768582),(2464,'module',3,'group/staff','paiorder','选择派单',0,2417,50,1499768582),(2465,'module',3,'group/staff','weiaudit','待审骑手',0,2417,3,1499837852),(2466,'module',3,'ditui/ditui','audit','待审地推',1,2423,2,1499847488),(2467,'module',3,'ditui/tixian','waititems','待转账管理',1,2423,50,1500000414),(2468,'module',3,'ditui/tixian','logs','转账记录',1,2423,50,1500000414),(2469,'module',3,'ditui/tixian','export','待转账导出',0,2423,50,1500000414),(2470,'module',3,'group/tixian','index','骑手提现',1,2417,50,1500023628),(2471,'module',3,'group/tixian','doaudit','通过审核',0,2417,50,1500026836),(2472,'module',3,'group/tixian','detail','提现详情',0,2417,50,1500028094),(2473,'module',3,'group/tixian','zhuanzhang','打款',0,2417,50,1500028140),(2474,'module',3,'group/tixian','so','搜索提现',0,2417,50,1500028702),(2475,'module',3,'group/tixian','reason','退回提现',0,2417,50,1500028909),(2476,'module',3,'ditui/member','delete','会员删除',0,2423,50,1500457143),(2477,'module',3,'finance/chong','index','后台充值对账',1,1524,50,1500522098),(2478,'module',3,'finance/chong','detail','后台充值详情',0,1524,50,1500523121),(2479,'module',3,'finance/chong','so','充值账单搜索',0,1524,50,1500532587),(2480,'module',3,'staff/bills','detail','骑手对账详情',0,1524,50,1500623427),(2481,'module',3,'staff/bills','comfirm_bills','骑手配送入账',0,1524,50,1500623504),(2482,'module',3,'staff/bills','bills','骑手对账详情',0,1524,50,1500623639),(2483,'module',3,'staff/bills','so','骑手对账搜索',0,1524,50,1500623671),(2484,'module',3,'staff/staff','dialog','选择骑手',0,1524,50,1500623715),(2485,'module',3,'staff/verify','detail','认证详情',0,2417,50,1500625385),(2486,'module',3,'staff/verify','edit','认证编辑',0,2417,50,1500625468),(2487,'module',3,'staff/verify','so','认证搜索',0,2417,50,1500625468),(2488,'module',3,'staff/verify','audit','骑手审核',0,2417,50,1500625561),(2489,'module',3,'staff/verify','delete','删除骑手',0,2417,50,1500625561),(2490,'menu',2,'','','积分商城',1,5,1,1500711432),(2491,'module',3,'group/group','delete','关闭配送站',0,2417,50,1500716233),(2492,'module',3,'group/group','edit','配送站编辑',0,2417,50,1500716233),(2494,'module',3,'article/page','index','单页管理',1,1430,50,1501860638),(2495,'module',3,'article/page','create','添加单页',0,1430,50,1501860638),(2496,'module',3,'article/page','edit','修改单页',0,1430,50,1501860638),(2497,'module',3,'article/page','delete','删除单页',0,1430,50,1501860638),(2498,'module',3,'staff/bills','staff','骑手配送账单',0,1524,50,1501922799),(2499,'module',3,'staff/staff','paiorder','选择配送员',0,1998,50,1502183028),(2500,'module',3,'group/group','setarea','配送站区域',1,2417,50,1502345841),(2501,'module',3,'group/group','get_groups','获取配送站',0,2417,50,1502345936),(2502,'menu',2,'','','跑腿管理',1,1962,50,1502347030),(2503,'module',3,'system/config','paotui','跑腿配置',0,2502,50,1502347317),(2504,'module',3,'paotui/cate','index','跑腿分类',0,2502,50,1502353048),(2505,'module',3,'paotui/cate','so','跑腿分类搜索',0,2502,50,1502353135),(2506,'module',3,'paotui/cate','detail','跑腿分类详情',0,2502,50,1502353135),(2507,'module',3,'paotui/cate','create','跑腿分类创建',0,2502,50,1502353135),(2508,'module',3,'paotui/cate','edit','跑腿分类编辑',0,2502,50,1502353135),(2509,'module',3,'paotui/cate','delete','跑腿分类删除',0,2502,50,1502353135),(2510,'module',3,'group/group','save_polygon','保存配送站配送区域',0,2417,50,1502423911),(2511,'module',3,'system/config','paotuimatic','跑腿计划任务',1,269,50,1502866686),(2512,'module',3,'waimai/shop','import','导入数据',0,1966,50,1503717990),(2513,'module',3,'paotui/order','index','订单管理',1,2502,50,1504084790),(2514,'module',3,'paotui/order','so','订单搜索',0,2502,50,1504084790),(2515,'module',3,'paotui/order','detail','订单查看',0,2502,50,1504084790),(2516,'module',3,'paotui/order','delete','订单删除',0,2502,50,1504084790),(2517,'module',3,'fenzhan/admin','index','分站管理员',0,6,50,1504086954),(2518,'module',3,'fenzhan/admin','create','创建分站管理员',0,6,50,1504086954),(2519,'module',3,'fenzhan/admin','edit','修改分站管理员',0,6,50,1504086954),(2520,'module',3,'fenzhan/admin','save','保存分站管理员',0,6,50,1504086954),(2521,'module',3,'fenzhan/admin','delete','删除分站管理员',0,6,50,1504086954),(2522,'module',3,'fenzhan/admin','so','搜索分站管理员',0,6,50,1504086954),(2523,'module',3,'fenzhan/role','index','分站角色管理',0,6,50,1504086954),(2524,'module',3,'fenzhan/role','create','创建分站角色',0,6,50,1504086954),(2525,'module',3,'fenzhan/role','detail','管理分站角色',0,6,50,1504086954),(2526,'module',3,'fenzhan/role','save','保存分站角色',0,6,50,1504086954),(2527,'module',3,'fenzhan/role','delete','删除分站角色',0,6,50,1504086954),(2528,'module',3,'paotui/order','cancel','订单取消',0,2502,50,1504159755),(2529,'module',3,'paotui/order','confirm','订单完成',0,2502,50,1504159755),(2530,'module',3,'shop/shop','dialog','选择商家',0,1966,50,1504332737),(2531,'module',3,'shop/shop','so','搜索商家',0,1966,50,1504333958),(2543,'module',3,'group/staff','wso','未审核搜索',0,2417,50,1505113680),(2544,'module',3,'waimai/shop','separate','单独配置',0,1966,50,1508982040),(2545,'module',3,'waimai/order','export','导出订单',0,1987,50,1508982347),(2546,'module',3,'finance/export','waimai','外卖对账单导出',0,1524,50,1508982472),(2547,'module',3,'finance/export','staff','配送员对账单导出',0,1524,50,1508982582),(2548,'module',3,'staff/staff','so','配送员搜索',0,2417,50,1508982625),(2549,'module',3,'finance/cash','index','代收款对账',1,1524,50,1509351478),(2550,'module',3,'finance/cash','bills','代收款对账详情',0,1524,50,1509353875),(2552,'module',3,'finance/cash','comfirm','确认上缴',0,1524,50,1509419730),(2553,'module',3,'system/config','frighttime','分时段配置',0,2417,50,1509428765),(2554,'module',3,'system/config','waimaihuodongconfig','外卖配置',1,1998,50,1509506086),(2555,'module',3,'finance/cash','so','搜索收款对账',0,1524,50,1510038536),(2556,'module',3,'waimai/complaint','index','商户投诉',1,1966,50,1510041817),(2557,'module',3,'waimai/complaint','delete','删除商户投诉',0,1966,50,1510044245),(2558,'module',3,'waimai/complaint','detail','商户投诉详情',0,1966,50,1510048918),(2559,'module',3,'group/staff','complaint','配送员投诉',1,2417,50,1510051285),(2560,'module',3,'waimai/complaint','so','商户投诉搜索',0,1966,50,1510051467),(2561,'module',3,'group/group','so','配送站搜索',0,2417,50,1510051825),(2562,'module',3,'group/staff','complaint_detail','配送员投诉详情',0,2417,50,1510051992),(2563,'module',3,'group/staff','complaint_so','配送员投诉搜索',0,2417,50,1510052461),(2564,'module',3,'group/timeout','index','配送员超时订单',1,2417,50,1510119428),(2565,'module',3,'group/timeout','so','配送员超时搜索',0,2417,50,1510120847),(2566,'module',3,'group/timeout','delete','配送员超时删除',0,2417,50,1510121055),(2567,'module',3,'group/refund','index','骑手异常订单',1,2417,50,1510128287),(2568,'module',3,'group/refund','so','骑手异常订单搜索',0,2417,50,1510129326),(2569,'module',3,'finance/export','cash','代收款对账导出',0,1524,50,1510142569),(2570,'module',3,'group/staff','dialog_so','搜索配送员',0,2417,50,1510280695),(2571,'module',3,'waimai/order','get_nctorder','订单提醒',0,1987,50,1510623512),(2572,'module',3,'sms/log','admin','发送测试短信',0,951,50,1510713687),(2573,'module',3,'waimai/print','index','打印机管理',1,1998,50,1510735321),(2574,'module',3,'waimai/print','so','打印机搜索',0,1998,50,1510735321),(2575,'module',3,'waimai/print','delete','删除打印机',0,1998,50,1510735321),(2576,'module',3,'waimai/print','edit','编辑打印机',0,1998,50,1510735321),(2577,'module',3,'waimai/print','create','新增打印机',0,1998,50,1510735321),(2578,'module',3,'finance/cash','detail','代收款对账单详情',0,1524,50,1510883876),(2584,'module',3,'adv/paotui','index','跑腿广告位',0,68,50,1511256509),(2585,'module',3,'adv/adv','uploadimg','广告位上传图片',0,68,50,1511264895),(2586,'module',3,'adv/paotui','save','跑腿广告位保存',0,68,50,1511340765),(2587,'module',3,'adv/start','index','页面设置',1,68,50,1511507657),(2588,'module',3,'adv/start','save','V3启动页广告保存',0,68,50,1511508657),(2589,'module',3,'adv/waimai','index','外卖广告位',0,68,50,1511510588),(2590,'module',3,'adv/waimai','save','外卖广告位保存',0,68,50,1511514312),(2591,'module',3,'adv/jifen','index','积分广告位',0,68,50,1511574386),(2592,'module',3,'adv/jifen','save','积分商城广告位保存',0,68,50,1511574386),(2593,'module',3,'group/assessment','index','配送员考核',1,2417,50,1511581943),(2594,'module',3,'waimai/shop','importInit','导入数据初始化',0,1966,50,1511590812),(2595,'module',3,'waimai/shop','importDo','导入数据开始',0,1966,50,1511590812),(2596,'module',3,'group/assessment','so','配送员考核搜索',0,2417,50,1511594192),(2597,'module',3,'adv/waimai','dialog','外卖广告位链接选择',0,68,50,1511603852),(2598,'module',3,'adv/waimai','dialogso','外卖广告位链接搜索',0,68,50,1511752767),(2599,'module',3,'adv/jifen','dialog','积分商城广告位链接选择',0,68,50,1511770779),(2600,'module',3,'adv/jifen','dialogso','积分商城广告位链接选择',0,68,50,1511770779),(2601,'module',3,'waimai/shop','importcate','导入分类',0,1966,50,1512185012),(2602,'module',3,'waimai/shop','importproduct','导入商品',0,1966,50,1512185012),(2603,'module',3,'finance/cash','billso','代收款对账详情搜索',0,1524,50,1512352366),(2604,'module',3,'waimai/bills','index','商家对账',0,1524,50,1512354202),(2605,'module',3,'waimai/bills','so','商家对账搜索',0,1524,50,1512356570),(2607,'module',3,'finance/staffbills','index','按人员对账',0,1524,50,1512367255),(2608,'module',3,'finance/staffbills','so','按人员对账搜索',0,1524,50,1512368171),(2609,'module',3,'finance/staffbills','wso','人员对账详情搜索',0,1524,50,1512369990),(2611,'module',3,'finance/cash','unpay_bills','人员对账详情未上缴',0,1524,50,1512381068),(2612,'module',3,'finance/cash','unpay_bills_so','人员对账详情未上缴搜索',0,1524,50,1512382639),(2613,'module',3,'finance/account','money','商户余额',1,1531,50,1512718970),(2614,'module',3,'finance/account','tixian','商户提现',0,1531,50,1512720172),(2615,'module',3,'finance/account','tixians','商户批量提现',0,1531,50,1512791518),(2616,'module',3,'finance/account','shopso','商户搜索',0,1531,50,1512799383),(2617,'module',3,'group/group','dialog','配送站搜索',0,2417,50,1512960965),(2618,'module',3,'finance/tongcheng','index','同城配送对账',0,1524,50,1512972317),(2619,'module',3,'finance/tongcheng','detail','同城配送对账详情',0,1524,50,1512980331),(2620,'module',3,'finance/tongcheng','so','同城配送对账搜索',0,1524,50,1512981226),(2621,'module',3,'hongbao/hongbao','getTime','红包次日时间',0,72,50,1512984436),(2622,'module',3,'group/tc','index','同城配送商家',0,2417,50,1513067427),(2623,'module',3,'group/tc','so','同城配送商家搜索',0,2417,50,1513068715),(2624,'module',3,'group/tc','unbind','同城配送商家解除绑定',0,2417,50,1513068715),(2625,'module',3,'hongbao/hongbao','multipy_send','批量发红包',0,72,50,1514536029),(2626,'module',3,'waimai/print','cancelall','清除打印队列',0,1998,50,1514973764),(2627,'module',3,'group/stafflevel','items','配送员等级',1,2417,50,1515813828),(2628,'module',3,'group/stafflevel','create','配送员等级新增',0,2417,50,1515813868),(2629,'module',3,'system/config','badweather','恶劣天气设置',0,269,50,1515823094),(2630,'module',3,'group/stafflevel','edit','配送员等级编辑',0,2417,50,1515981109),(2631,'module',3,'group/stafflevel','so','配送员等级搜索',0,2417,50,1515981109),(2632,'module',3,'group/stafflevel','delete','配送员等级删除',0,2417,50,1515981109),(2633,'module',3,'waimai/cate','show_time_and_week','设置分类置顶时间',0,1966,50,1516068944),(2634,'module',3,'waimai/shop','setbusiness','设置营业信息',0,1966,50,1516081311),(2635,'module',3,'waimai/shop','comment','商家评论',0,1966,50,1516099590),(2640,'module',3,'group/group','set_map','配送区域单独设置',0,2417,50,1516176175),(2641,'module',3,'group/group','baseconfig','配送费基础设置',0,2417,50,1516181737),(2642,'module',3,'group/group','timeconfig','配送费时间设置',0,2417,50,1516183925),(2643,'module',3,'waimai/shop','comment_delete','删除评论',0,1966,50,1516184790),(2644,'module',3,'group/group','badweather','伪劣天气配置',0,2417,50,1516245199),(2645,'module',3,'system/config','unit','单位设置',1,1998,50,1516328244),(2646,'module',3,'member/tixian','items','会员提现',1,72,50,1516610858),(2647,'module',3,'member/tixian','apply','会员提现通过',0,72,50,1516613399),(2648,'module',3,'member/tixian','so','会员提现搜索',0,72,50,1516613759),(2649,'module',3,'member/tixian','refuse','会员提现拒绝',0,72,50,1516617175),(2650,'module',3,'group/group','timeoutconfig','超时规则设置',0,2417,50,1516687836),(2651,'module',3,'subsidy/staff','items','补贴统计',1,2256,50,1516872875),(2652,'module',3,'subsidy/staff','load_table','骑手补贴加载',0,2256,50,1517191779),(2653,'module',3,'subsidy/staff','detail','骑手补贴详情',0,2256,50,1517197455),(2655,'module',3,'subsidy/waimai','items','商家补贴统计',0,2256,50,1517291857),(2656,'module',3,'subsidy/waimai','load_table','商家补贴加载',0,2256,50,1517292949),(2657,'module',3,'subsidy/waimai','detail','商家补贴详情',0,2256,50,1517298030),(2658,'module',3,'subsidy/member','items','用户补贴统计',0,2256,50,1517304973),(2659,'module',3,'subsidy/member','load_table','用户补贴加载',0,2256,50,1517305013),(2660,'module',3,'subsidy/member','detail','用户补贴详情',0,2256,50,1517305013),(2661,'module',3,'tongji/tongji','get_data','获取统计数据',0,2256,50,1517474611),(2662,'module',3,'group/staff','cancellog','骑手弃单记录',1,2417,50,1517794516),(2663,'module',3,'group/staff','cancellog_so','骑手弃单搜索',0,2417,50,1517796519),(2664,'module',3,'payment/log','highchat','支付流水统计',0,551,50,1517990852),(2665,'module',3,'payment/log','get_payment_log_data','获取支付流水数据',0,551,50,1517995478),(2666,'menu',2,'','','天降红包',1,1962,50,1520844528),(2667,'module',3,'hongbao/huodong','index','天降红包',1,2666,50,1520844805),(2668,'module',3,'hongbao/huodong','create','活动添加',0,2666,50,1520844805),(2669,'module',3,'hongbao/huodong','edit','活动修改',0,2666,50,1520844805),(2670,'module',3,'hongbao/huodong','delete','活动删除',0,2666,50,1520844805),(2671,'module',3,'hongbao/huodong','history','历史活动',0,2666,50,1520844805),(2672,'module',3,'hongbao/huodong','detail','活动详情',0,2666,50,1520844921),(2673,'module',3,'hongbao/huodong','so','活动搜索',0,2666,50,1520845036),(2674,'menu',2,'','','三方管理',1,1962,50,1523584491),(2675,'module',3,'other/order','index','订单列表',1,2674,50,1523584670),(2676,'module',3,'other/order','detail','订单详情',0,2674,50,1523584670),(2677,'module',3,'other/order','so','订单搜索',0,2674,50,1523584670),(2678,'module',3,'other/order','jiedan','接单',0,2674,50,1523585155),(2679,'module',3,'other/order','cancel','取消订单',0,2674,50,1523585155),(2680,'module',3,'other/order','setconfirm','确认送达',0,2674,50,1523585155),(2681,'module',3,'other/order','setpei','申请配送',0,2674,50,1523585155),(2682,'module',3,'other/order','cancelpei','取消配送',0,2674,50,1523585155),(2683,'module',3,'other/order','agree','同意退款',0,2674,50,1523585155),(2684,'module',3,'other/order','refuse','拒绝退款',0,2674,50,1523585155),(2685,'module',3,'other/order','get_minpei','获取基础配送费',0,2674,50,1523585155),(2686,'module',3,'other/order','addtip','追加小费',0,2674,50,1523585155),(2687,'menu',2,'','','抢购管理',1,5,50,1524217537),(2688,'module',3,'qiang/qiang','index','抢购列表',1,2687,50,1524218134),(2689,'module',3,'qiang/qiang','create','添加抢购',0,2687,50,1524218134),(2690,'module',3,'qiang/qiang','detail','查看抢购',0,2687,50,1524218134),(2691,'module',3,'qiang/qiang','edit','修改抢购',0,2687,50,1524218134),(2692,'module',3,'qiang/qiang','so','搜索抢购',0,2687,50,1524218134),(2693,'module',3,'qiang/qiang','delete','删除抢购',0,2687,50,1524218134),(2694,'module',3,'qiang/order','index','订单管理',1,2687,50,1524218134),(2695,'module',3,'qiang/order','so','搜索订单',0,2687,50,1524218134),(2696,'module',3,'qiang/order','delivery','订单发货',0,2687,50,1524218134),(2697,'module',3,'qiang/order','delete','删除订单',0,2687,50,1524218134),(2698,'module',3,'qiang/order','detail','查看订单',0,2687,50,1524218134),(2699,'module',3,'qiang/order','cancel','取消订单',0,2687,50,1524218134),(2700,'module',3,'qiang/qiang','doaudit','上下架',0,2687,50,1524713870),(2701,'module',3,'qiang/comment','index','抢购评论',1,2687,50,1525328896),(2702,'module',3,'qiang/comment','detail','评论详情',0,2687,50,1525328896),(2703,'module',3,'qiang/comment','so','评论搜索',0,2687,50,1525328896),(2704,'module',3,'qiang/comment','delete','评论删除',0,2687,50,1525329022),(2705,'module',3,'qiang/comment','recovery','评论回复',0,2687,50,1525413405),(2706,'module',3,'finance/waimai','comfirm','抢购商户上缴',0,1524,50,1525421806),(2707,'module',3,'finance/qiang','index','抢购商户账单',0,1524,50,1525421820),(2708,'module',3,'finance/qiang','bills','抢购商户入账',0,1524,50,1525421820),(2709,'module',3,'finance/qiang','comfirm_bills','抢购商户上缴',0,1524,50,1525421820),(2710,'module',3,'finance/qiang','so','抢购商户搜索',0,1524,50,1525421820),(2711,'module',3,'finance/qiang','shop','对账商家',0,1524,50,1525422467),(2712,'module',3,'finance/qiang','detail','抢购商户账单详情',0,1524,50,1525422611),(2713,'module',3,'system/config','apppush','推送配置',1,1584,1,1531452182),(2714,'module',3,'tongji/export','yy_tongji','营业统计导出',0,2256,50,1531994638),(2715,'module',3,'tongji/export','subsidy','补贴统计导出',0,2256,50,1532065628),(2716,'module',3,'finance/jifen','so','积分商城对账搜索',0,1524,50,1532509082),(2717,'module',3,'finance/account','transfer','支付宝一键转账',0,1531,50,1532514828),(2718,'module',3,'qiang/order','payback','取消并退款订单',0,2687,50,1526524801),(2719,'module',3,'jpush/device','so','安装设备搜索',0,1584,50,1532572110),(2720,'menu',2,'','','配送会员卡',1,1962,50,1532742625),(2721,'module',3,'peicard/card','index','会员卡管理',1,2720,50,1532744969),(2722,'module',3,'peicard/card','create','会员卡添加',0,2720,50,1532744969),(2723,'module',3,'peicard/card','edit','会员卡修改',0,2720,50,1532744969),(2724,'module',3,'peicard/card','so','会员卡搜索',0,2720,50,1532744969),(2725,'module',3,'peicard/card','delete','会员卡删除',0,2720,50,1532744969),(2726,'module',3,'peicard/card','detail','会员卡查看',0,2720,50,1532744969),(2727,'module',3,'peicard/member','index','会员卡用户',1,2720,50,1532749940),(2728,'module',3,'peicard/member','detail','会员卡用户查看',0,2720,50,1533289781),(2729,'module',3,'peicard/member','so','会员卡用户搜索',0,2720,50,1533289781),(2730,'module',3,'peicard/log','index','使用记录',1,2720,50,1533290327),(2731,'module',3,'peicard/log','so','记录搜索',0,2720,50,1533290432),(2732,'module',3,'adv/themes','index','首页风格',1,68,50,1534299350),(2733,'module',3,'adv/themes','preview','首页预览',0,68,50,1534299350),(2734,'module',3,'adv/themes','module','首页模块',0,68,50,1534299350),(2735,'module',3,'adv/themes','editModule','首页单个模块修改',0,68,50,1534299350),(2736,'module',3,'adv/themes','save','首页模块保存',0,68,50,1534299350),(2737,'module',3,'adv/themes','edit','首页风格修改',0,68,50,1534299350),(2738,'module',3,'adv/themes','setDefault','设置默认风格',0,68,50,1534299350),(2739,'module',3,'adv/themes','dialog','选择链接',0,68,50,1534312105),(2740,'module',3,'adv/themes','upload','上传图片',0,68,50,1534319340),(2741,'module',3,'adv/themes','delete','风格删除',0,68,50,1534326867),(2742,'module',3,'adv/themes','copy','风格复制',0,68,50,1534326867),(2743,'module',3,'adv/themes','photoGallery','选择图片',0,68,50,1534489763),(2744,'module',3,'adv/themes','upload_by_data','上传图片',0,68,50,1534734214),(2745,'menu',2,'','','我的图片',1,5,4,1535163750),(2746,'module',3,'upload/photo','index','图片管理',1,2745,50,1535164352),(2747,'module',3,'upload/photo','setcate','图片分组',0,2745,50,1535164352),(2748,'module',3,'upload/photo','delete','图片删除',0,2745,50,1535164352),(2749,'module',3,'upload/photo','upload','图片上传',0,2745,50,1535164352),(2750,'module',3,'upload/photo','upload_by_data','图片上传2',0,2745,50,1535164352),(2751,'module',3,'upload/photo','photoGallery','图片库',0,2745,50,1535164352),(2752,'module',3,'upload/cate','index','分组管理',0,2745,50,1535164352),(2753,'module',3,'upload/cate','create','分组创建',0,2745,50,1535164352),(2754,'module',3,'upload/cate','edit','分组修改',0,2745,50,1535164352),(2755,'module',3,'upload/cate','delete','分组删除',0,2745,50,1535164352),(2756,'module',3,'adv/themes','iconGallery','图标库',0,68,50,1535616079),(2757,'module',3,'group/staff','forcedlog','强制送达记录',1,2417,50,1536304692),(2758,'module',3,'group/staff','forcedlog_so','强制送达记录搜索',0,2417,50,1536305063),(2759,'module',3,'adv/export','index','模板库',0,68,50,1536470879),(2760,'module',3,'adv/export','export','模板导入',0,68,50,1536470879),(2761,'module',3,'adv/export','preview','模板库预览',0,68,50,1536470879),(2762,'module',3,'group/tixian','transfer','支付宝一键转账',0,2417,50,1539311525);
/*!40000 ALTER TABLE `jh_system_module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_themes`
--

DROP TABLE IF EXISTS `jh_themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_themes` (
  `theme_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `theme` varchar(50) DEFAULT '',
  `title` varchar(50) DEFAULT '',
  `thumb` varchar(150) DEFAULT '',
  `config` mediumtext,
  `default` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`theme_id`),
  KEY `theme` (`theme`,`default`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_themes`
--

LOCK TABLES `jh_themes` WRITE;
/*!40000 ALTER TABLE `jh_themes` DISABLE KEYS */;
INSERT INTO `jh_themes` VALUES (1,'v3','V3','thumb.jpg',NULL,0,1500862838);
/*!40000 ALTER TABLE `jh_themes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_upload_cate`
--

DROP TABLE IF EXISTS `jh_upload_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_upload_cate` (
  `cate_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '分组ID',
  `title` varchar(30) DEFAULT '',
  `orderby` smallint(6) DEFAULT '50',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_upload_cate`
--

LOCK TABLES `jh_upload_cate` WRITE;
/*!40000 ALTER TABLE `jh_upload_cate` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_upload_cate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_upload_photo`
--

DROP TABLE IF EXISTS `jh_upload_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_upload_photo` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT '0',
  `from` varchar(30) DEFAULT '',
  `hash` char(32) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `photo` varchar(150) DEFAULT '',
  `size` smallint(6) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `cate_id` smallint(6) DEFAULT '0',
  PRIMARY KEY (`photo_id`),
  KEY `hash` (`hash`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_upload_photo`
--

LOCK TABLES `jh_upload_photo` WRITE;
/*!40000 ALTER TABLE `jh_upload_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_upload_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai`
--

DROP TABLE IF EXISTS `jh_waimai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai` (
  `shop_id` mediumint(8) unsigned NOT NULL,
  `city_id` smallint(6) DEFAULT '0',
  `area_id` int(10) DEFAULT '0',
  `business_id` int(10) DEFAULT '0',
  `cate_id` smallint(6) DEFAULT '0',
  `contact` varchar(16) DEFAULT '',
  `title` varchar(80) DEFAULT '',
  `banner` varchar(150) DEFAULT '',
  `logo` varchar(150) DEFAULT '',
  `addr` varchar(150) DEFAULT '',
  `views` mediumint(8) DEFAULT '0',
  `orders` mediumint(8) DEFAULT '0',
  `comments` mediumint(8) DEFAULT '0',
  `praise_num` mediumint(8) DEFAULT '0',
  `score` int(10) DEFAULT '0',
  `score_peisong` int(10) DEFAULT '0',
  `first_amount` decimal(6,2) DEFAULT '0.00',
  `min_amount` decimal(6,2) DEFAULT '0.00',
  `freight` decimal(6,2) DEFAULT '0.00',
  `freight_stage` text,
  `pei_amount` decimal(6,2) DEFAULT '0.00',
  `pei_distance` decimal(6,0) DEFAULT '0',
  `pei_type` tinyint(1) DEFAULT '5',
  `pei_time` smallint(6) DEFAULT '30',
  `yy_status` tinyint(1) DEFAULT '0',
  `yy_stime` char(5) DEFAULT '9:00',
  `yy_ltime` char(5) DEFAULT '20:00',
  `yy_xiuxi` varchar(512) DEFAULT '',
  `is_new` tinyint(1) DEFAULT '0',
  `online_pay` tinyint(1) DEFAULT '0',
  `youhui` varchar(1024) DEFAULT NULL,
  `info` varchar(1024) DEFAULT '',
  `delcare` varchar(255) DEFAULT NULL,
  `pmid` char(9) DEFAULT '',
  `last_time` int(10) DEFAULT '0',
  `verify_name` tinyint(1) DEFAULT '0',
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `tmpl_type` enum('market','waimai') DEFAULT NULL,
  `phone` varchar(15) DEFAULT '',
  `is_daofu` tinyint(1) unsigned DEFAULT '0',
  `is_ziti` tinyint(1) unsigned DEFAULT '0',
  `lat` int(10) DEFAULT NULL,
  `lng` int(10) DEFAULT NULL,
  `orderby` smallint(6) DEFAULT '50',
  `yuyue_day` tinyint(1) DEFAULT '1',
  `yy_peitime` varchar(2014) DEFAULT '',
  `area_polygon` text,
  `waimai_bl` int(10) DEFAULT '0',
  `hot` varchar(1024) DEFAULT '',
  `hd_first_ltime` int(10) DEFAULT '0',
  `hd_coupon_ltime` int(10) DEFAULT '0',
  `hd_mf_ltime` int(10) DEFAULT '0',
  `hd_mj_ltime` int(10) DEFAULT '0',
  `group_id` int(10) DEFAULT '0',
  `print_type` tinyint(1) DEFAULT '0',
  `cate_ids` varchar(150) DEFAULT '',
  `config` text,
  `is_separate` tinyint(1) DEFAULT '0',
  `ps_time` text,
  `refund_order` int(10) DEFAULT '0',
  `yy_weeks` varchar(16) DEFAULT '0,1,2,3,4,5,6',
  `pstime_type` tinyint(1) DEFAULT '1',
  `jiesuan_type` tinyint(1) DEFAULT '0',
  `is_ztsp` tinyint(1) DEFAULT '0',
  `zt_bl` int(10) DEFAULT '0',
  `hd_discount_ltime` int(10) DEFAULT '0',
  `zero_ziti` tinyint(1) DEFAULT '0',
  `deliver` decimal(8,2) DEFAULT '0.00',
  `warn_sku` smallint(6) DEFAULT '0',
  `auto_print` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`shop_id`),
  KEY `city_id` (`city_id`,`cate_id`),
  KEY `views` (`views`,`orders`,`comments`,`score`,`audit`,`closed`),
  KEY `pei_time` (`pei_time`,`pei_type`,`yy_stime`,`yy_ltime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai`
--

LOCK TABLES `jh_waimai` WRITE;
/*!40000 ALTER TABLE `jh_waimai` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_accesstoken`
--

DROP TABLE IF EXISTS `jh_waimai_accesstoken`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_accesstoken` (
  `shop_id` int(10) NOT NULL DEFAULT '0',
  `access_token` varchar(255) DEFAULT '',
  `expires_in` int(10) DEFAULT '0',
  `refresh_token` varchar(255) DEFAULT '',
  `ext_shop_id` int(10) DEFAULT '0',
  `ext_title` varchar(255) DEFAULT '',
  `meituan_token` varchar(255) DEFAULT '',
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_accesstoken`
--

LOCK TABLES `jh_waimai_accesstoken` WRITE;
/*!40000 ALTER TABLE `jh_waimai_accesstoken` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_accesstoken` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_adv`
--

DROP TABLE IF EXISTS `jh_waimai_adv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_adv` (
  `adv_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `title` varchar(128) DEFAULT '',
  `link` varchar(128) DEFAULT '',
  `photo` varchar(255) DEFAULT '',
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `orderby` smallint(6) DEFAULT NULL,
  `closed` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`adv_id`),
  KEY `shop_id` (`shop_id`),
  KEY `items` (`shop_id`,`stime`,`ltime`,`closed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_adv`
--

LOCK TABLES `jh_waimai_adv` WRITE;
/*!40000 ALTER TABLE `jh_waimai_adv` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_adv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_bills`
--

DROP TABLE IF EXISTS `jh_waimai_bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_bills` (
  `bills_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bills_sn` int(8) NOT NULL,
  `shop_id` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned DEFAULT '0',
  `amount` decimal(8,2) DEFAULT '0.00',
  `fee` decimal(8,2) DEFAULT '0.00',
  `dateline` int(10) unsigned DEFAULT NULL,
  `shop_amount` decimal(8,2) DEFAULT '0.00',
  `roof_amount` decimal(8,2) DEFAULT '0.00',
  `user_amount` decimal(8,2) DEFAULT '0.00',
  `freight` decimal(8,2) DEFAULT '0.00',
  PRIMARY KEY (`bills_id`),
  UNIQUE KEY `bills` (`bills_sn`,`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_bills`
--

LOCK TABLES `jh_waimai_bills` WRITE;
/*!40000 ALTER TABLE `jh_waimai_bills` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_bills_log`
--

DROP TABLE IF EXISTS `jh_waimai_bills_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_bills_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bills_id` int(10) unsigned NOT NULL,
  `bills_sn` int(8) DEFAULT '0',
  `shop_id` int(10) unsigned DEFAULT NULL,
  `bills_number` int(10) DEFAULT '0',
  `status` tinyint(1) unsigned DEFAULT '0',
  `online_pay` tinyint(1) unsigned DEFAULT '1',
  `amount` decimal(8,2) DEFAULT '0.00',
  `fee` decimal(8,2) DEFAULT '0.00',
  `dateline` int(10) unsigned DEFAULT NULL,
  `shop_amount` decimal(8,2) DEFAULT '0.00',
  `roof_amount` decimal(8,2) DEFAULT '0.00',
  `user_amount` decimal(8,2) DEFAULT '0.00',
  `freight` decimal(8,2) DEFAULT '0.00',
  `bl` decimal(8,2) DEFAULT '0.00',
  PRIMARY KEY (`log_id`),
  KEY `bills_id` (`bills_id`),
  KEY `shop_id` (`shop_id`),
  KEY `bills_number` (`bills_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_bills_log`
--

LOCK TABLES `jh_waimai_bills_log` WRITE;
/*!40000 ALTER TABLE `jh_waimai_bills_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_bills_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_cate`
--

DROP TABLE IF EXISTS `jh_waimai_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_cate` (
  `cate_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(6) DEFAULT '0',
  `title` varchar(30) DEFAULT '',
  `icon` varchar(150) DEFAULT '',
  `photo` varchar(150) DEFAULT '',
  `orderby` smallint(6) DEFAULT '50',
  `dateline` int(10) DEFAULT '0',
  `show_time` varchar(1024) DEFAULT '',
  `yy_weeks` varchar(255) DEFAULT '0,1,2,3,4,5,6',
  PRIMARY KEY (`cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_cate`
--

LOCK TABLES `jh_waimai_cate` WRITE;
/*!40000 ALTER TABLE `jh_waimai_cate` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_cate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_comment`
--

DROP TABLE IF EXISTS `jh_waimai_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `uid` mediumint(8) DEFAULT '0',
  `order_id` int(8) DEFAULT '0',
  `score` tinyint(1) DEFAULT '0',
  `score_peisong` tinyint(1) DEFAULT '0',
  `score_avg` decimal(10,1) DEFAULT '0.0',
  `content` varchar(1024) DEFAULT '',
  `pei_time` smallint(6) DEFAULT '30',
  `have_photo` tinyint(1) DEFAULT '0',
  `reply` varchar(1024) DEFAULT '',
  `reply_ip` varchar(15) DEFAULT '',
  `reply_time` int(10) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `extend` varchar(1024) DEFAULT '',
  `is_anonymous` tinyint(1) DEFAULT '0' COMMENT '是否匿名评价',
  PRIMARY KEY (`comment_id`),
  KEY `shop_id` (`shop_id`,`uid`),
  KEY `order_id` (`order_id`,`score`,`reply_time`,`closed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_comment`
--

LOCK TABLES `jh_waimai_comment` WRITE;
/*!40000 ALTER TABLE `jh_waimai_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_comment_photo`
--

DROP TABLE IF EXISTS `jh_waimai_comment_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_comment_photo` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) DEFAULT '0',
  `photo` varchar(150) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`photo_id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_comment_photo`
--

LOCK TABLES `jh_waimai_comment_photo` WRITE;
/*!40000 ALTER TABLE `jh_waimai_comment_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_comment_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_complaint_photo`
--

DROP TABLE IF EXISTS `jh_waimai_complaint_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_complaint_photo` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `complaint_id` int(10) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_complaint_photo`
--

LOCK TABLES `jh_waimai_complaint_photo` WRITE;
/*!40000 ALTER TABLE `jh_waimai_complaint_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_complaint_photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_coupon`
--

DROP TABLE IF EXISTS `jh_waimai_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_coupon` (
  `coupon_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `order_id` int(10) DEFAULT '0',
  `huodong_id` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  `uid` int(10) DEFAULT '0',
  `order_amount` decimal(10,2) DEFAULT '0.00',
  `coupon_amount` decimal(8,2) DEFAULT '0.00',
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `orderby` smallint(6) DEFAULT '0',
  `use_time` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `title` varchar(50) DEFAULT '',
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_coupon`
--

LOCK TABLES `jh_waimai_coupon` WRITE;
/*!40000 ALTER TABLE `jh_waimai_coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_coupon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_discount_product`
--

DROP TABLE IF EXISTS `jh_waimai_discount_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_discount_product` (
  `product_id` int(10) NOT NULL DEFAULT '0',
  `huodong_id` int(10) DEFAULT '0',
  `discount_value` int(10) DEFAULT '0',
  `sale_sku` int(10) DEFAULT '0',
  `sale_count` int(10) DEFAULT '0',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_discount_product`
--

LOCK TABLES `jh_waimai_discount_product` WRITE;
/*!40000 ALTER TABLE `jh_waimai_discount_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_discount_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_env`
--

DROP TABLE IF EXISTS `jh_waimai_env`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_env` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) unsigned DEFAULT '0',
  `photo` varchar(255) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_env`
--

LOCK TABLES `jh_waimai_env` WRITE;
/*!40000 ALTER TABLE `jh_waimai_env` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_env` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_huangou_product`
--

DROP TABLE IF EXISTS `jh_waimai_huangou_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_huangou_product` (
  `product_id` int(10) NOT NULL DEFAULT '0',
  `huodong_id` int(10) DEFAULT '0',
  `discount_value` int(10) DEFAULT '0',
  `sale_sku` int(10) DEFAULT '0',
  `sale_count` int(10) DEFAULT '0',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_huangou_product`
--

LOCK TABLES `jh_waimai_huangou_product` WRITE;
/*!40000 ALTER TABLE `jh_waimai_huangou_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_huangou_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_huodong`
--

DROP TABLE IF EXISTS `jh_waimai_huodong`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_huodong` (
  `huodong_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '',
  `banner` varchar(128) DEFAULT '',
  `tmpl` varchar(128) DEFAULT '',
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  PRIMARY KEY (`huodong_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_huodong`
--

LOCK TABLES `jh_waimai_huodong` WRITE;
/*!40000 ALTER TABLE `jh_waimai_huodong` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_huodong` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_huodong_coupon`
--

DROP TABLE IF EXISTS `jh_waimai_huodong_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_huodong_coupon` (
  `huodong_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) DEFAULT '0',
  `config` text,
  `num` int(10) DEFAULT '0',
  `limit` mediumint(5) DEFAULT NULL,
  `group` tinyint(1) DEFAULT '0',
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  PRIMARY KEY (`huodong_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_huodong_coupon`
--

LOCK TABLES `jh_waimai_huodong_coupon` WRITE;
/*!40000 ALTER TABLE `jh_waimai_huodong_coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_huodong_coupon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_huodong_discount`
--

DROP TABLE IF EXISTS `jh_waimai_huodong_discount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_huodong_discount` (
  `huodong_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) DEFAULT '0',
  `title` varchar(30) DEFAULT NULL,
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `period_type` tinyint(1) DEFAULT '0',
  `period_times` text,
  `period_weeks` varchar(30) DEFAULT NULL,
  `quota` smallint(6) DEFAULT '0',
  `discount_type` tinyint(1) DEFAULT '0',
  `products` text,
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  `clientip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`huodong_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_huodong_discount`
--

LOCK TABLES `jh_waimai_huodong_discount` WRITE;
/*!40000 ALTER TABLE `jh_waimai_huodong_discount` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_huodong_discount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_huodong_first`
--

DROP TABLE IF EXISTS `jh_waimai_huodong_first`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_huodong_first` (
  `huodong_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) DEFAULT '0',
  `config` text,
  `type` tinyint(1) DEFAULT '0',
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  PRIMARY KEY (`huodong_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_huodong_first`
--

LOCK TABLES `jh_waimai_huodong_first` WRITE;
/*!40000 ALTER TABLE `jh_waimai_huodong_first` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_huodong_first` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_huodong_huangou`
--

DROP TABLE IF EXISTS `jh_waimai_huodong_huangou`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_huodong_huangou` (
  `huodong_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) DEFAULT '0',
  `title` varchar(30) DEFAULT NULL,
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `order_amount` decimal(8,0) DEFAULT '0',
  `period_times` text,
  `period_weeks` varchar(30) DEFAULT NULL,
  `quota` smallint(6) DEFAULT '0',
  `products` text,
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  `clientip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`huodong_id`),
  KEY `shop_id` (`shop_id`),
  KEY `find` (`shop_id`,`stime`,`ltime`,`audit`,`closed`,`order_amount`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_huodong_huangou`
--

LOCK TABLES `jh_waimai_huodong_huangou` WRITE;
/*!40000 ALTER TABLE `jh_waimai_huodong_huangou` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_huodong_huangou` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_huodong_items`
--

DROP TABLE IF EXISTS `jh_waimai_huodong_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_huodong_items` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `huodong_id` int(10) DEFAULT '0',
  `can_id` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  `orderby` tinyint(2) DEFAULT '50',
  `title` varchar(128) DEFAULT '',
  `photo` varchar(128) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_huodong_items`
--

LOCK TABLES `jh_waimai_huodong_items` WRITE;
/*!40000 ALTER TABLE `jh_waimai_huodong_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_huodong_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_huodong_mf`
--

DROP TABLE IF EXISTS `jh_waimai_huodong_mf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_huodong_mf` (
  `huodong_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) DEFAULT '0',
  `config` text,
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  PRIMARY KEY (`huodong_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_huodong_mf`
--

LOCK TABLES `jh_waimai_huodong_mf` WRITE;
/*!40000 ALTER TABLE `jh_waimai_huodong_mf` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_huodong_mf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_huodong_mj`
--

DROP TABLE IF EXISTS `jh_waimai_huodong_mj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_huodong_mj` (
  `huodong_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) DEFAULT '0',
  `config` text,
  `stime` int(10) DEFAULT '0',
  `ltime` int(10) DEFAULT '0',
  `audit` tinyint(1) DEFAULT '0',
  `closed` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  PRIMARY KEY (`huodong_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_huodong_mj`
--

LOCK TABLES `jh_waimai_huodong_mj` WRITE;
/*!40000 ALTER TABLE `jh_waimai_huodong_mj` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_huodong_mj` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_order`
--

DROP TABLE IF EXISTS `jh_waimai_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_order` (
  `order_id` int(10) unsigned NOT NULL,
  `product_number` mediumint(8) DEFAULT '0',
  `product_price` decimal(10,2) DEFAULT '0.00',
  `package_price` decimal(8,2) DEFAULT '0.00',
  `freight` decimal(6,2) DEFAULT '0.00',
  `spend_number` varchar(16) DEFAULT '',
  `spend_status` tinyint(1) DEFAULT '0',
  `shop_amount` decimal(8,2) DEFAULT '0.00',
  `roof_amount` decimal(8,2) DEFAULT '0.00',
  `first_shop` decimal(8,2) DEFAULT '0.00',
  `first_roof` decimal(8,2) DEFAULT '0.00',
  PRIMARY KEY (`order_id`),
  KEY `spend_number` (`spend_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_order`
--

LOCK TABLES `jh_waimai_order` WRITE;
/*!40000 ALTER TABLE `jh_waimai_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_order_complaint`
--

DROP TABLE IF EXISTS `jh_waimai_order_complaint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_order_complaint` (
  `complaint_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT NULL,
  `uid` mediumint(8) DEFAULT '0',
  `shop_id` mediumint(8) DEFAULT '0',
  `staff_id` mediumint(8) DEFAULT '0',
  `title` varchar(80) DEFAULT '',
  `content` varchar(255) DEFAULT '',
  `clientip` varchar(15) DEFAULT '',
  `reply` varchar(255) DEFAULT '',
  `reply_time` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  `have_photo` mediumint(10) DEFAULT '0',
  PRIMARY KEY (`complaint_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_order_complaint`
--

LOCK TABLES `jh_waimai_order_complaint` WRITE;
/*!40000 ALTER TABLE `jh_waimai_order_complaint` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_order_complaint` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_order_log`
--

DROP TABLE IF EXISTS `jh_waimai_order_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_order_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT '0',
  `from` enum('shop','admin','staff','member','system','payment') DEFAULT 'member',
  `log` varchar(255) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_order_log`
--

LOCK TABLES `jh_waimai_order_log` WRITE;
/*!40000 ALTER TABLE `jh_waimai_order_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_order_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_order_product`
--

DROP TABLE IF EXISTS `jh_waimai_order_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_order_product` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT '0',
  `product_id` int(10) DEFAULT '0',
  `product_name` varchar(150) DEFAULT '',
  `product_price` decimal(8,2) DEFAULT NULL,
  `package_price` decimal(6,2) DEFAULT '0.00',
  `product_number` smallint(6) DEFAULT '0',
  `amount` decimal(8,2) DEFAULT '0.00',
  `spec_id` int(10) DEFAULT NULL,
  `unit` varchar(20) DEFAULT '',
  `specification` text,
  `huodong_id` int(10) DEFAULT '0',
  `product_prices` decimal(8,2) DEFAULT '0.00',
  `product_oldprice` decimal(8,2) DEFAULT '0.00',
  `product_oldprices` decimal(8,2) DEFAULT '0.00',
  `huodong_title` varchar(150) DEFAULT '',
  `basket_title` varchar(150) DEFAULT '',
  `huangou_id` int(10) DEFAULT '0',
  `product_photo` varchar(150) DEFAULT '',
  PRIMARY KEY (`pid`),
  KEY `order_id` (`order_id`,`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6704 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_order_product`
--

LOCK TABLES `jh_waimai_order_product` WRITE;
/*!40000 ALTER TABLE `jh_waimai_order_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_order_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_order_refund`
--

DROP TABLE IF EXISTS `jh_waimai_order_refund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_order_refund` (
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `from` enum('shop','member','admin') DEFAULT 'member',
  `uid` mediumint(8) DEFAULT '0',
  `shop_id` mediumint(8) DEFAULT '0',
  `reflect` varchar(255) DEFAULT '',
  `reply` varchar(255) DEFAULT '',
  `reply_time` int(10) DEFAULT '0',
  `refund_price` decimal(10,2) DEFAULT '0.00',
  `status` tinyint(1) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`order_id`),
  KEY `uid` (`uid`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_order_refund`
--

LOCK TABLES `jh_waimai_order_refund` WRITE;
/*!40000 ALTER TABLE `jh_waimai_order_refund` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_order_refund` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_product`
--

DROP TABLE IF EXISTS `jh_waimai_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_product` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `area_id` int(10) DEFAULT '0',
  `business_id` int(10) DEFAULT '0',
  `cate_id` int(10) DEFAULT '0',
  `cat_id` int(10) DEFAULT '0',
  `title` varchar(80) DEFAULT '',
  `photo` varchar(150) DEFAULT '',
  `price` decimal(8,2) DEFAULT '0.00',
  `package_price` decimal(6,2) DEFAULT '0.00',
  `sales` mediumint(8) DEFAULT '0',
  `sale_type` tinyint(1) DEFAULT '1',
  `sale_sku` mediumint(8) DEFAULT '0',
  `sale_count` mediumint(8) DEFAULT '0',
  `intro` varchar(1024) DEFAULT '',
  `orderby` smallint(6) DEFAULT '50',
  `closed` tinyint(1) DEFAULT '0',
  `clientip` varchar(15) DEFAULT '',
  `dateline` int(10) DEFAULT '0',
  `is_hot` tinyint(1) DEFAULT '0',
  `is_spec` tinyint(1) DEFAULT '0',
  `spec` varchar(1024) DEFAULT '',
  `is_onsale` tinyint(1) unsigned DEFAULT '0',
  `good` int(10) DEFAULT '0',
  `bad` int(10) DEFAULT '0',
  `unit` varchar(20) DEFAULT '',
  `specification` text,
  `cate_ids` varchar(30) DEFAULT NULL,
  `is_must` tinyint(1) DEFAULT '0',
  `is_tuijian` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`product_id`),
  KEY `shop_id` (`shop_id`,`cate_id`,`closed`),
  KEY `orderby` (`orderby`,`closed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_product`
--

LOCK TABLES `jh_waimai_product` WRITE;
/*!40000 ALTER TABLE `jh_waimai_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_product_cate`
--

DROP TABLE IF EXISTS `jh_waimai_product_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_product_cate` (
  `cate_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `shop_id` mediumint(8) DEFAULT '0',
  `title` varchar(30) DEFAULT '',
  `icon` varchar(150) DEFAULT '',
  `orderby` smallint(6) DEFAULT '50',
  `type` enum('market','waimai') DEFAULT NULL,
  `dateline` int(10) DEFAULT NULL,
  `settime` varchar(500) DEFAULT '',
  `show_type` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`cate_id`),
  KEY `shop_id` (`shop_id`),
  KEY `orderby` (`orderby`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_product_cate`
--

LOCK TABLES `jh_waimai_product_cate` WRITE;
/*!40000 ALTER TABLE `jh_waimai_product_cate` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_product_cate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_product_spec`
--

DROP TABLE IF EXISTS `jh_waimai_product_spec`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_product_spec` (
  `spec_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `package_price` decimal(10,2) DEFAULT '0.00',
  `spec_name` varchar(50) DEFAULT '',
  `spec_photo` varchar(150) DEFAULT '',
  `sale_sku` mediumint(8) DEFAULT '0',
  `sale_count` mediumint(8) DEFAULT '0',
  `sale_type` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`spec_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_product_spec`
--

LOCK TABLES `jh_waimai_product_spec` WRITE;
/*!40000 ALTER TABLE `jh_waimai_product_spec` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_product_spec` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_verify`
--

DROP TABLE IF EXISTS `jh_waimai_verify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_verify` (
  `shop_id` mediumint(8) unsigned NOT NULL,
  `id_name` varchar(30) DEFAULT '',
  `id_number` varchar(20) DEFAULT '',
  `id_photo` varchar(150) DEFAULT '',
  `shop_photo` varchar(150) DEFAULT '',
  `verify_dianzhu` tinyint(1) DEFAULT '0',
  `company_name` varchar(64) DEFAULT '',
  `yz_number` varchar(30) DEFAULT '',
  `yz_photo` varchar(150) DEFAULT '',
  `verify_yyzz` tinyint(1) DEFAULT '0',
  `cy_number` varchar(30) NOT NULL DEFAULT '',
  `cy_photo` varchar(150) DEFAULT '',
  `verify_cy` tinyint(1) DEFAULT '0',
  `refuse` varchar(255) DEFAULT '',
  `verify` tinyint(1) DEFAULT '0',
  `verify_time` int(10) DEFAULT '0',
  `updatetime` int(10) DEFAULT '0',
  `yz_photo_s` varchar(150) DEFAULT '',
  `id_photo_s` varchar(150) DEFAULT '',
  `id_photo_sf` varchar(150) DEFAULT '',
  `id_photo_f` varchar(150) DEFAULT '',
  `yz_addr` varchar(150) DEFAULT '',
  `cy_addr` varchar(150) DEFAULT '',
  `yz_time` varchar(50) DEFAULT '0',
  `cy_time` varchar(50) DEFAULT '0',
  `yz_name` varchar(150) DEFAULT '',
  `cy_name` varchar(150) DEFAULT '',
  PRIMARY KEY (`shop_id`,`cy_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_verify`
--

LOCK TABLES `jh_waimai_verify` WRITE;
/*!40000 ALTER TABLE `jh_waimai_verify` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_verify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_views`
--

DROP TABLE IF EXISTS `jh_waimai_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_views` (
  `view_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `uid` int(10) DEFAULT '0',
  `views` int(10) DEFAULT '0',
  `updatetime` int(10) DEFAULT '0',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`view_id`),
  KEY `shop_user` (`shop_id`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_views`
--

LOCK TABLES `jh_waimai_views` WRITE;
/*!40000 ALTER TABLE `jh_waimai_views` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_views` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jh_waimai_youhui`
--

DROP TABLE IF EXISTS `jh_waimai_youhui`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jh_waimai_youhui` (
  `youhui_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` mediumint(8) DEFAULT '0',
  `order_amount` decimal(10,2) DEFAULT '0.00',
  `youhui_amount` decimal(8,2) DEFAULT '0.00',
  `use_count` smallint(6) DEFAULT '0',
  `orderby` smallint(6) DEFAULT '0',
  `dateline` int(10) DEFAULT NULL,
  PRIMARY KEY (`youhui_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jh_waimai_youhui`
--

LOCK TABLES `jh_waimai_youhui` WRITE;
/*!40000 ALTER TABLE `jh_waimai_youhui` DISABLE KEYS */;
/*!40000 ALTER TABLE `jh_waimai_youhui` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-12-16  2:02:37
