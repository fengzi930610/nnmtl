# Host: localhost  (Version: 5.5.53)
# Date: 2018-08-13 23:22:15
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "tp_adress"
#

DROP TABLE IF EXISTS `tp_adress`;
CREATE TABLE `tp_adress` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `menberId` int(11) NOT NULL DEFAULT '0',
  `prvince` varchar(20) DEFAULT '',
  `city` varchar(10) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `linkman` varchar(255) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "tp_adress"
#


#
# Structure for table "tp_brand"
#

DROP TABLE IF EXISTS `tp_brand`;
CREATE TABLE `tp_brand` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(18) NOT NULL DEFAULT '',
  `logo` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

#
# Data for table "tp_brand"
#

INSERT INTO `tp_brand` VALUES (1,'Nike','timg.jpg','<p>耐克<br/></p>'),(2,'水电费','./public/static/admin/upload/image/brand/2018-08-01/5b61ae65c8614.jpg','<p>水电费<br/></p>'),(3,'水电费','./public/static/admin/upload/image/brand/2018-08-02/5b6286f8ad8fc.jpg','<p>水电费<br/></p>');

#
# Structure for table "tp_cart"
#

DROP TABLE IF EXISTS `tp_cart`;
CREATE TABLE `tp_cart` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `memberId` int(11) NOT NULL DEFAULT '0' COMMENT '会员Id',
  `productId` int(11) DEFAULT NULL COMMENT '商品Id',
  `num` int(11) DEFAULT NULL COMMENT '商品数量',
  `createtime` int(11) DEFAULT NULL COMMENT '添加购物车时间',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#
# Data for table "tp_cart"
#

INSERT INTO `tp_cart` VALUES (1,1,34,2,1532752863,1),(2,1,33,2,1532857809,1);

#
# Structure for table "tp_category"
#

DROP TABLE IF EXISTS `tp_category`;
CREATE TABLE `tp_category` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `fId` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;

#
# Data for table "tp_category"
#

INSERT INTO `tp_category` VALUES (1,'连体衣',0),(2,'裤子',0),(3,'上衣',0),(4,'连衣裙',0),(29,'婴幼儿服装',1),(30,'男童服装',1),(31,'女童服装',1),(32,'儿童服饰',1),(33,'婴儿家居',1),(34,'儿童家居',1),(35,'其他',1),(36,'男童服装',2),(37,'女童服装',2),(38,'儿童服饰',2),(39,'婴儿家居',2),(40,'儿童家居',2),(41,'其他',2),(42,'婴幼儿服装',3),(44,'男童服装',3),(45,'女童服装',3),(46,'儿童服饰',3),(47,'婴儿家居',3),(48,'儿童家居',3),(49,'其他',3),(52,'男童服装',4),(53,'女童服装',4),(54,'儿童服饰',4),(55,'婴儿家居',4),(56,'儿童家居',4),(57,'其他',4);

#
# Structure for table "tp_level"
#

DROP TABLE IF EXISTS `tp_level`;
CREATE TABLE `tp_level` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `fId` int(11) NOT NULL DEFAULT '0',
  `model` varchar(15) NOT NULL DEFAULT '',
  `controller` varchar(15) NOT NULL DEFAULT '',
  `action` varchar(15) NOT NULL DEFAULT '',
  `state` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

#
# Data for table "tp_level"
#

INSERT INTO `tp_level` VALUES (1,'系统管理',0,'admin','System','',1),(2,'系统设置',1,'admin','System','index',1),(3,'分类管理',0,'admin','Category','',1),(4,'分类列表',3,'admin','Category','categoryList',1),(5,'分类添加',3,'admin','Category','categoryAdd',1),(6,'新闻管理',0,'admin','News','',1),(7,'新闻列表',6,'admin','News','newsList',1),(8,'新闻添加',6,'admin','News','newsAdd',1),(9,'管理员管理',0,'admin','Manager','',1),(10,'管理员列表',9,'admin','Manager','managerList',1),(11,'管理员添加',9,'admin','Manager','managerAdd',1),(12,'分类编辑',3,'admin','Category','categoryUpdate',0),(13,'分类删除',3,'admin','Category','categoryDelete',0),(14,'新闻编辑',6,'admin','News','newsUpdate',0),(15,'新闻删除',6,'admin','News','newsDelete',0),(16,'管理员编辑',9,'admin','Manager','managerUpdate',0),(17,'管理员删除',9,'admin','Manager','managerDelete',0),(18,'菜单管理',0,'admin','Level','',1),(19,'菜单列表',18,'admin','Level','levelList',1),(20,'菜单添加',18,'admin','Level','levelAdd',1),(21,'菜单编辑',18,'admin','Level','levelUpdate',0),(22,'菜单删除',18,'admin','Level','levelDelete',0),(23,'权限管理',0,'admin','Rote','',1),(24,'角色添加',23,'admin','Rote','roteAdd',1),(25,'角色列表',23,'admin','Rote','roteList',1),(26,'角色编辑',23,'admin','Rote','roteUpdate',0),(27,'角色删除',23,'admin','Rote','roteDelete',0),(28,'banner管理',0,'admin','Banner','',1),(29,'banner列表',28,'admin','Banner','bannerList',1),(30,'banner添加',28,'admin','Banner','bannerAdd',1),(31,'banner更新',28,'admin','Banner','bannerUpdate',0),(32,'banner删除',28,'admin','Banner','bannerDelete',0),(33,'产品管理',0,'admin','Product','',1),(34,'产品列表',33,'admin','Product','productList',1),(35,'产品添加',33,'admin','Product','productAdd',1),(36,'产品更新',33,'admin','Product','productUpdate',0),(37,'产品删除',33,'admin','Product','productDelete',0);

#
# Structure for table "tp_manager"
#

DROP TABLE IF EXISTS `tp_manager`;
CREATE TABLE `tp_manager` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL DEFAULT '',
  `password` varchar(18) NOT NULL DEFAULT '',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#
# Data for table "tp_manager"
#

INSERT INTO `tp_manager` VALUES (1,'admin','admin',1);

#
# Structure for table "tp_member"
#

DROP TABLE IF EXISTS `tp_member`;
CREATE TABLE `tp_member` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `password` varchar(18) NOT NULL DEFAULT '',
  `phone` varchar(11) NOT NULL DEFAULT '',
  `email` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#
# Data for table "tp_member"
#

INSERT INTO `tp_member` VALUES (1,'admin','admin','13111111111','1057780049@qq.com');

#
# Structure for table "tp_nav"
#

DROP TABLE IF EXISTS `tp_nav`;
CREATE TABLE `tp_nav` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='后台首页导航';

#
# Data for table "tp_nav"
#

INSERT INTO `tp_nav` VALUES (1,'特惠购'),(2,'闪购'),(3,'新品抢购'),(4,'全球购');

#
# Structure for table "tp_orders"
#

DROP TABLE IF EXISTS `tp_orders`;
CREATE TABLE `tp_orders` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `num` int(11) NOT NULL DEFAULT '0',
  `menberId` int(11) NOT NULL DEFAULT '0',
  `productId` int(11) NOT NULL DEFAULT '0',
  `orderNumber` varchar(35) NOT NULL DEFAULT '' COMMENT '订单编号',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "tp_orders"
#


#
# Structure for table "tp_product"
#

DROP TABLE IF EXISTS `tp_product`;
CREATE TABLE `tp_product` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `introduction` varchar(255) DEFAULT NULL COMMENT '产品简介',
  `brandId` int(5) DEFAULT NULL COMMENT '品牌Id',
  `categoryId` int(5) DEFAULT NULL COMMENT '分类Id',
  `number` varchar(255) NOT NULL DEFAULT '' COMMENT '商品货号',
  `market` decimal(5,2) DEFAULT NULL COMMENT '市场价格',
  `price` decimal(5,2) DEFAULT NULL COMMENT '销售价格',
  `num` int(5) DEFAULT NULL COMMENT '商品库存',
  `thumb` varchar(255) DEFAULT NULL COMMENT '主图缩略图',
  `picture` varchar(255) NOT NULL DEFAULT '' COMMENT '主图图片路径',
  `content` text COMMENT '产品详情',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  `utime` int(11) DEFAULT NULL COMMENT '更新时间',
  `newArrival` tinyint(3) NOT NULL DEFAULT '0' COMMENT '新品',
  `burstingProducts` tinyint(3) NOT NULL DEFAULT '0' COMMENT '爆款产品',
  `hotProduct` tinyint(3) NOT NULL DEFAULT '0' COMMENT '热门产品',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

#
# Data for table "tp_product"
#

INSERT INTO `tp_product` VALUES (24,'女婴2016秋冬纯棉长袖爬服夹棉连身衣','女婴2016秋冬纯棉长袖爬服夹棉连身衣',1,29,'123456',398.00,398.00,398,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b504fe97d5a9.jpg','./public/static/admin/upload/image/2018-07-19/5b504fe97d5a9.jpg','<p>女婴2016秋冬纯棉长袖爬服夹棉连身衣</p>',1531989993,1531989993,1,1,0),(25,'女婴2016秋冬纯棉长袖爬服夹棉连身衣','女婴2016秋冬纯棉长袖爬服夹棉连身衣',1,30,'398',398.00,398.00,398,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b50583f800dc.jpg','./public/static/admin/upload/image/2018-07-19/5b50583f800dc.jpg','<p>女婴2016秋冬纯棉长袖爬服夹棉连身衣</p>',1531992127,1531992127,1,1,0),(26,'女婴2017秋冬纯棉长袖爬服夹棉连身衣','女婴2017秋冬纯棉长袖爬服夹棉连身衣',1,31,'298',298.00,298.00,298,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b50586c9cc28.jpg','./public/static/admin/upload/image/2018-07-19/5b50586c9cc28.jpg','<p>女婴2016秋冬纯棉长袖爬服夹棉连身衣</p>',1531992172,1531992172,1,0,0),(27,'女婴2018秋冬纯棉长袖爬服夹棉连身衣','女婴2018秋冬纯棉长袖爬服夹棉连身衣',1,32,'298',298.00,298.00,298,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b50588913763.jpg','./public/static/admin/upload/image/2018-07-19/5b50588913763.jpg','<p>女婴2016秋冬纯棉长袖爬服夹棉连身衣</p>',1531992201,1531992201,1,0,0),(28,'连身衣','连身衣',1,33,'123',198.00,98.00,1200,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b505f6691cf7.jpg','./public/static/admin/upload/image/2018-07-19/5b505f6691cf7.jpg','',1531993958,1531993958,1,0,0),(29,'中袖连身衣','中袖连身衣',1,34,'123',128.00,118.00,110,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b505fa07a72f.jpg','./public/static/admin/upload/image/2018-07-19/5b505fa07a72f.jpg','<p>11<br/></p>',1531994016,1531994016,1,0,1),(30,'2018秋冬纯棉长袖爬服夹棉连身衣','2018秋冬纯棉长袖爬服夹棉连身衣',1,35,'11',112.00,112.00,111,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b505ff8d21bd.jpg','./public/static/admin/upload/image/2018-07-19/5b505ff8d21bd.jpg','<p>111<br/></p>',1531994104,1531994104,1,0,0),(31,'女婴2019秋冬纯棉长袖爬服夹棉连身衣','女婴2019秋冬纯棉长袖爬服夹棉连身衣',1,36,'123',123.00,110.00,123,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b506026826c9.jpg','./public/static/admin/upload/image/2018-07-19/5b506026826c9.jpg','<p>按时<br/></p>',1531994150,1531994150,1,0,1),(32,'男婴2020秋冬纯棉长袖爬服夹棉连身衣','男婴2020秋冬纯棉长袖爬服夹棉连身衣',1,37,'111',111.00,111.00,111,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b5060685959e.jpg','./public/static/admin/upload/image/2018-07-19/5b5060685959e.jpg','<p>是<br/></p>',1531994216,1531994216,1,0,0),(33,'男婴2020秋冬纯棉长袖爬服夹棉连身衣','男婴2020秋冬纯棉长袖爬服夹棉连身衣',1,38,'98',98.00,98.00,98,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b506085e0879.jpg','./public/static/admin/upload/image/2018-07-19/5b506085e0879.jpg','<p>是<br/></p>',1531994245,1531994245,1,0,0),(34,'男婴2020秋冬纯棉长袖爬服夹棉连身衣','男婴2020秋冬纯棉长袖爬服夹棉连身衣',1,39,'998',998.00,998.00,998,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b5060a378734.jpg','./public/static/admin/upload/image/2018-07-19/5b5060a378734.jpg','<p>是<br/></p>',1531994275,1531994275,1,0,0),(35,'男婴2020秋冬纯棉长袖爬服夹棉连身衣','男婴2020秋冬纯棉长袖爬服夹棉连身衣',1,40,'888',888.00,888.00,888,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b5061102484b.jpg','./public/static/admin/upload/image/2018-07-19/5b5061102484b.jpg','<p>是<br/></p>',1531994384,1531994384,1,0,0),(36,'男婴儿2016小熊无袖背心爬服','男婴儿2016小熊无袖背心爬服',1,41,'99',999.00,999.00,999,'./public/static/admin/upload/image/2018-07-19/thumb/thumb_5b5081cf4d68d.jpg','./public/static/admin/upload/image/2018-07-19/5b5081cf4d68d.jpg','<p>男婴儿2016小熊无袖背心爬服</p>',1532002767,1532004838,0,0,0),(37,'男婴儿2016小熊无袖背心爬服','男婴儿2016小熊无袖背心爬服',1,36,'123456',998.00,998.00,998,'./public/static/admin/upload/image/2018-08-06/thumb/thumb_5b6846adc6647.jpg','./public/static/admin/upload/image/2018-08-06/5b6846adc6647.jpg','<p>123</p>',1533560493,1533560493,1,0,0);

#
# Structure for table "tp_role"
#

DROP TABLE IF EXISTS `tp_role`;
CREATE TABLE `tp_role` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(18) NOT NULL DEFAULT '',
  `jurisdiction` varchar(50) NOT NULL DEFAULT '' COMMENT '权限',
  `description` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#
# Data for table "tp_role"
#

INSERT INTO `tp_role` VALUES (1,'普通管理员','1,2,3','系统管理相关权限'),(2,'普通管理员','1,2,3','系统管理相关权限');
