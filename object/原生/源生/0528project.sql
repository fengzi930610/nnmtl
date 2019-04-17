# Host: localhost  (Version: 5.5.53)
# Date: 2018-08-09 16:43:19
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "about"
#

DROP TABLE IF EXISTS `about`;
CREATE TABLE `about` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `cId` tinyint(3) DEFAULT NULL COMMENT '分类Id',
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `jianjie` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#
# Data for table "about"
#

INSERT INTO `about` VALUES (1,17,'公司介绍','<p><img src=\"/ueditor/php/upload/image/20180801/1533103985666734.jpg\" title=\"1533103985666734.jpg\" alt=\"1532422767116706.jpg\" style=\"float: left;\"/></p><p style=\"text-indent: 2em;\">润泰环保设备有限公司，是一家提供服装品牌开发各环节全方位服务的综合性服装设计工作室工作室专注于中高档时尚女装的服装设计和研发，专为服装品牌公司、品牌买手企业、服装网购企业、服装零售商提供从品牌企划方案到单品设计、从纸作室专注于中高档时尚女装的服装设计和研发，专为服装品牌公司、品牌买手企业、服装网购企业、服装零售商提供从品牌企划方案到单品设计、从纸零售商提供从品牌企划方案到单品设计、从纸服装样到样衣制作.</p><p style=\"text-indent: 2em;\">深圳市翔隆服饰有限公司是一家集开发、生产、销售为一体的大型服装企业，我们专业设计生产：工厂制服、公司制服、工程制服、广告衫，POLO衫，棉衣，夹克，封压领衬衫，职业装等等中高档服装\r\n 公司从原材料采购到生产出库，严格把关，涵盖电子、化工、工业制造、建筑、冶金、消防等多类行业。</p><p style=\"text-indent: 2em;\">在为企业提供优质产品的同时，为企业的劳动安全提供最佳的现场解决方案以及完善的产品服务成为专业，诚信，优秀的产品供应商一直是我们的企业目标。</p><p style=\"text-indent: 2em;\">公司自成立以来，严格按现代企业制度进行建设和管理，以灵活的市场机制不断为客户提供最优质的服务。在为企业提供优质产品的同时，为企业的劳动安全提供最佳的现场解决方案以及完善的产品服务成为专业，诚信，优秀的产品供应商一直是我们的企业目标。</p><p style=\"text-indent: 2em;\">以最合理的价格 &nbsp;最完善的服务，提供最优质的产品一致是我们的服务宗旨。 用服务与真诚来换取您的信任与支持，我们期待与您互惠互利共创双赢。</p>','杭州市翔隆服饰有限公司是一家专业设计、生产、销售为一体的大型销售网络及产、供、销一体化的发展体系和完善的销售体系。公司专业生产执法制服、保安服饰、迷彩服饰 、职业服饰，工作服饰、等产品 目前我公司已覆盖政府、公安、房产、物业、商场、酒店、学校、制造等企业服装企业，企业始终坚持以诚信、务实、共赢为经营发展理念，以市场需求为核心，不断提升品牌形象，勇于开拓诚信经营，并逐步建立全国性的销售网络及产公司专业生....');

#
# Structure for table "banner"
#

DROP TABLE IF EXISTS `banner`;
CREATE TABLE `banner` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `imgsrc` varchar(100) NOT NULL DEFAULT '',
  `group` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

#
# Data for table "banner"
#

INSERT INTO `banner` VALUES (3,'upload/2018-06-22/20180622153403vKi2.jpg',1),(4,'upload/2018-06-22/20180622153410YwB3.jpg',1),(5,'upload/2018-06-22/20180622153416dwjQ.jpg',2),(10,'upload/2018-06-24/20180624152053xdbT.jpg',3),(11,'upload/2018-06-26/20180626154126QTrj.jpg',4),(12,'upload/2018-06-26/20180626161120Q7sN.jpg',5),(13,'upload/2018-07-30/banner/20180730120239rRvU.jpg',1);

#
# Structure for table "category"
#

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `fId` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

#
# Data for table "category"
#

INSERT INTO `category` VALUES (1,'新闻中心',0),(2,'保安系列',9),(3,'新闻动态',1),(4,'新闻资讯',1),(9,'产品中心',0),(10,'热点评论',1),(11,'迷彩系列',9),(13,'职业系列',9),(14,'工作系列',9),(15,'公司介绍',0),(16,'公司文化',15),(17,'公司咨询',15),(18,'公司荣耀',15);

#
# Structure for table "level"
#

DROP TABLE IF EXISTS `level`;
CREATE TABLE `level` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `fId` int(11) NOT NULL DEFAULT '0',
  `module` varchar(15) NOT NULL DEFAULT '',
  `controller` varchar(15) NOT NULL DEFAULT '',
  `action` varchar(15) NOT NULL DEFAULT '',
  `state` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

#
# Data for table "level"
#

INSERT INTO `level` VALUES (1,'系统管理',0,'admin','system','',1),(2,'系统设置',1,'admin','system','index',1),(3,'分类管理',0,'admin','category','',1),(4,'分类列表',3,'admin','category','list',1),(5,'分类添加',3,'admin','category','add',1),(6,'新闻管理',0,'admin','news','',1),(7,'新闻列表',6,'admin','news','list',1),(8,'新闻添加',6,'admin','news','add',1),(9,'管理员管理',0,'admin','manager','',1),(10,'管理员列表',9,'admin','manager','list',1),(11,'管理员添加',9,'admin','manager','add',1),(12,'分类编辑',3,'admin','category','update',0),(13,'分类删除',3,'admin','category','delete',0),(14,'新闻编辑',6,'admin','news','update',0),(15,'新闻删除',6,'admin','news','delete',0),(16,'管理员编辑',9,'admin','manager','update',0),(17,'管理员删除',9,'admin','manager','delete',0),(18,'菜单管理',0,'admin','level','',1),(19,'菜单列表',18,'admin','level','list',1),(20,'菜单添加',18,'admin','level','add',1),(21,'菜单编辑',18,'admin','level','update',0),(22,'菜单删除',18,'admin','level','delete',0),(23,'权限管理',0,'admin','rote','',1),(24,'角色添加',23,'admin','rote','add',1),(25,'角色列表',23,'admin','rote','list',1),(26,'角色编辑',23,'admin','rote','update',0),(27,'角色删除',23,'admin','rote','delete',0),(28,'banner管理',0,'admin','banner','',1),(29,'banner列表',28,'admin','banner','list',1),(30,'banner添加',28,'admin','banner','add',1),(31,'banner更新',28,'admin','banner','update',0),(32,'banner删除',28,'admin','banner','delete',0),(33,'产品管理',0,'admin','product','',1),(34,'产品列表',33,'admin','product','list',1),(35,'产品添加',33,'admin','product','add',1),(36,'产品更新',33,'admin','product','update',0),(37,'产品删除',33,'admin','product','delete',0),(38,'留言管理',0,'admin','suggestion','',1),(39,'留言列表',38,'admin','suggestion','list',1),(40,'留言删除',38,'admin','suggestion','delete',0),(41,'留言编辑',38,'admin','suggestion','update',0),(42,'公司团队管理',0,'admin','team','',1),(43,'团队列表',42,'admin','team','list',1),(44,'团队人员添加',42,'admin','team','add',1),(45,'团队编辑',42,'admin','team','update',0),(46,'团队删除',42,'admin','team','delete',0),(47,'公司管理',0,'admin','about','',1),(48,'公司介绍',47,'admin','about','index',1),(49,'后台管理',0,'admin','index','',1),(50,'后台首页',49,'admin','index','index',1);

#
# Structure for table "manager"
#

DROP TABLE IF EXISTS `manager`;
CREATE TABLE `manager` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(10) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `tname` varchar(10) NOT NULL DEFAULT '',
  `sex` varchar(4) NOT NULL DEFAULT '' COMMENT '0是女，1是男，2是未知',
  `phone` varchar(11) NOT NULL DEFAULT '',
  `Email` varchar(50) NOT NULL DEFAULT '',
  `regtime` int(11) NOT NULL DEFAULT '0',
  `randstr` varchar(5) NOT NULL DEFAULT '',
  `state` int(11) NOT NULL DEFAULT '0',
  `rid` int(11) NOT NULL DEFAULT '0',
  `roteId` int(11) NOT NULL DEFAULT '0' COMMENT '角色Id',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8;

#
# Data for table "manager"
#

INSERT INTO `manager` VALUES (1,'admin','aa0d92e0ba84b75fcb77b88440c0f912','admin','0','13188888888','1057780049@qq.com',0,'9PVrf',0,0,0),(154,'admin80','385028f2929d0e3ae2ee602f1ec931b2','ad','1','13133331111','1057780049@qq.com',0,'wjoMs',0,0,1),(159,'y','77be1977f50564831113f3ec829f12d9','y','1','y','1057780049@qq.com',1528957805,'0oOAX',1,0,3),(160,'a','c72cb467c334e91cbf047f9b4e1acac0','a','1','a','a',1530002370,'Di8d5',0,0,3),(161,'q','7631f53e70322c2508d9d08fa2c727b6','b','1','a','a',1530003276,'qkpDu',0,0,3),(162,'r','b63af2c66a2efc7521ea7f086102d381','v','1','','',1530003288,'OwIOr',0,0,3),(163,'w','96f9f7f0238066ef359d3d6b7ee85d5c','d','1','a','1057780049qq.com',1530003419,'L3Xt5',0,0,3),(164,'f','5c120056d34338dd699e0f9e7be1741a','i','1','a','a',1530003640,'HRone',0,0,2),(165,'u','f780a694d8490769f2292a872dc5495f','p','1','y','a',1530003652,'ALdq4',0,0,2);

#
# Structure for table "nav"
#

DROP TABLE IF EXISTS `nav`;
CREATE TABLE `nav` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `c` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

#
# Data for table "nav"
#

INSERT INTO `nav` VALUES (1,'翔隆首页','index'),(2,'公司介绍','about'),(3,'新闻中心','news'),(4,'产品中心','product'),(5,'联系我们','contact');

#
# Structure for table "news"
#

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL DEFAULT '',
  `content` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `author` varchar(10) NOT NULL DEFAULT '',
  `state` varchar(10) NOT NULL DEFAULT '',
  `rtime` varchar(15) DEFAULT '' COMMENT '发布时间',
  `ctime` varchar(11) DEFAULT '' COMMENT '录入时间',
  `utime` varchar(11) DEFAULT '' COMMENT '修改时间',
  `access` varchar(20) NOT NULL DEFAULT '' COMMENT '访问量',
  `img` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

#
# Data for table "news"
#

INSERT INTO `news` VALUES (29,'雅戈尔发力美式休闲装 致力打造“有温度的服装品牌','<p>10月18日 ，宁波国际服装节即将拉开序幕.之际一个美国品牌在中国宁波举行了130周年盛典，Hart Schaffner Marx美国百年传奇的中国坐标，而这场盛宴的主导者就是中国服装巨 头的雅戈尔。宁波市副市长陈仲朝，中国服装协会常务副会长陈大鹏，雅戈尔集团董事长李如成，美国Hart Marx公司首席行政官JEFFREY WOLF等出席此次活动。</p>','3','admin','1','1529823773','',NULL,'0','upload/2018-06-24/20180624150253DG4S.png'),(30,'公益课在香港服装学院火热开讲','<p>10月18日，社会组织新媒体视窗《指尖上的传播》 公益课程在香港服装学院一楼多功能厅火热开讲， 本次课程由深圳市福田区社会组织总会主办，福田区社会组织从业人员以及活动范围在福田区的其他深圳市、区级社会组织从业人员均可免费报名参加。 分享了新媒体的传播推广技巧，现场座无虚席，掌声雷动。</p>','3','admin','1','1529823890','',NULL,'0','upload/2018-06-24/20180624150450rqe5.jpg'),(31,'记者采访本公司副董事长展开重大会议','<p>2017年一到八月，我们行业工业增加值增速是 5.1%，这些年是持续趋缓，分行业领域来判断今年一来服装行业增加值增长水平是 6.3%，高于全行业水平，但也会低于整个工业水平。这些年来 主要是受到国内市场环境趋缓，国际市场环境疲弱，国内原材料市场的等等诸多原因，造成我们在数据表现上呈现趋缓的走势。</p>','3','admin','1','1529823947','',NULL,'0','upload/2018-06-24/20180624150547rydo.png'),(32,'记者采访本公司副董事长采取重要讲话','<p>2014年5月1日，在新闻记者的采访中， &nbsp;五十岁的某公司董事长张建：作为 一个五十岁新闻人物.本人茂名采访了他，采访中宣称将在2017-2020年之间 把公司的服装贸易向国外推广发展。宣称国外合作伙伴已经签下多年合同将多年与本公司合作</p>','3','admin','1','1529824035','',NULL,'0','upload/2018-06-24/20180624150715tF3w.png'),(33,'目前来看，服装的功能早已不拘泥于社交性、社会规则性.....','<p>目前来看，服装的功能早已不拘泥于社交性、社会规则性......</p>','3','admin','1','1529919744','',NULL,'0','upload/2018-06-24/20180624150253DG4S.png'),(34,'9月27日比音勒芬称拟与广东易简投资有限公司共同投资设立..','<p>9月27日比音勒芬称拟与广东易简投资有限公司共同投资设立.....</p>','3','admin','1','1529919818','',NULL,'0','upload/2018-06-24/20180624150253DG4S.png'),(42,'2017中国企业500强发布 服装企业入榜多数为男装','<p style=\"text-indent: 2em;\">除了产品，时尚零售未来将是数据的竞争。近年来强势崛起的宁波太平鸟时尚服饰股份有限公司在上海证交所发布公告称，公司9月20日与阿里巴巴旗下天猫达成\r\n新零售战略合作意向。太平鸟称未来双方拟在品牌建设、大数据运用、消费者运营和线上线下全渠道融合领域以及国际市场开拓等方面开展新零售战略合作。</p><p><br/></p><p style=\"text-indent: 2em;\">公司上下，时尚零售未来将是数据的竞争。近年来强势崛起的宁波太平鸟时尚服饰股份','10','admin','1','1530152300','1530088864','1530152300','0','upload/2018-06-24/20180624150253DG4S.png'),(43,'记者采访本公司副董事长采取重要讲话','<p>2014年5月1日，在新闻记者的采访中， &nbsp;五十岁的某公司董事长张建：作为 一个五十岁新闻人物.本人茂名采访了他，采访中宣称将在2017-2020年之间 把公司的服装贸易向国外推广发展。宣称国外合作伙伴已经签下多年合同将多年与本公司合作</p>','4','admin','1','1533107519','1529824035','1529824035','0','upload/2018-06-24/20180624150715tF3w.png');

#
# Structure for table "product"
#

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `summary` varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  `content` text NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '分类',
  `additions` varchar(35) NOT NULL DEFAULT '' COMMENT '添加人员',
  `imgsrc` varchar(150) DEFAULT '' COMMENT '缩略图',
  `atime` varchar(11) DEFAULT '' COMMENT '添加时间',
  `ctime` varchar(15) DEFAULT '' COMMENT '录入时间',
  `state` tinyint(3) NOT NULL DEFAULT '0' COMMENT '添加状态',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

#
# Data for table "product"
#

INSERT INTO `product` VALUES (1,'保安服','保安服','<p>保安服</p>','2','admin','upload/2018-07-23/product/20180723201930Q7ci.jpg','1532075054','1532075054',1),(2,'保安服','保安服','<p>保安服</p>','2','admin','upload/2018-07-23/product/20180723201915NX1z.jpg','1532076382','1532076382',1),(3,'保安服','保安服','<p>保安服</p>','2','admin','upload/2018-07-23/product/20180723201857Fj9s.jpg','1532076400','1532076400',1),(4,'保安服','保安服','<p>保安服</p>','2','admin','upload/2018-07-23/product/20180723201838eUKk.jpg','1532076470','1532076470',1),(5,'工作服','工作服','<p>工作服</p>','14','admin','upload/2018-07-23/product/20180723201743a9QW.jpg','1532076671','1532076671',1),(6,'工作服','工作服','<p>工作服</p>','14','admin','upload/2018-07-23/product/20180723201631Xueg.jpg','1532076686','1532076686',1),(7,'工作服','工作服','<p>工作服</p>','14','admin','upload/2018-07-23/product/20180723201610ROoW.jpg','1532076701','1532076701',1),(8,'迷彩','迷彩','<p>迷彩</p>','11','admin','upload/2018-07-23/product/20180723201436Cro9.jpg','1532076774','1532076774',1),(9,'迷彩','迷彩','<p>迷彩</p>','11','admin','upload/2018-07-23/product/20180723201113w99S.jpg','1532076788','1532076788',1),(10,'迷彩','迷彩','<p>迷彩</p>','11','admin','upload/2018-07-23/product/20180723201058s65R.jpg','1532076804','1532076804',1),(11,'职业','职业','<p>职业</p>','13','admin','upload/2018-07-23/product/2018072320072634Hl.jpg','1532076868','1532076868',1),(12,'迷彩','迷彩','迷彩','11','admin','upload/2018-07-23/product/20180723201058s65R.jpg','1532076804','1532076804',1),(13,'迷彩','迷彩','慈爱','11','admin','upload/2018-07-23/product/20180723201857Fj9s.jpg','1532076400','1532076400',1);

#
# Structure for table "rote"
#

DROP TABLE IF EXISTS `rote`;
CREATE TABLE `rote` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL DEFAULT '',
  `rote` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

#
# Data for table "rote"
#

INSERT INTO `rote` VALUES (1,'普通管理员',''),(2,'小编','新闻管理,新闻列表,新闻添加,新闻编辑,新闻删除'),(3,'小k','新闻管理,新闻列表,新闻添加,新闻编辑');

#
# Structure for table "suggestion"
#

DROP TABLE IF EXISTS `suggestion`;
CREATE TABLE `suggestion` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `phone` varchar(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `time` int(11) DEFAULT NULL COMMENT '留言提交时间',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='意见反馈';

#
# Data for table "suggestion"
#

INSERT INTO `suggestion` VALUES (1,'aa','15277073211','a',1532410394),(2,'v','13411111111','v',1532410443),(3,'54964','15277073211','sadhlabsdi',1533540342);

#
# Structure for table "system"
#

DROP TABLE IF EXISTS `system`;
CREATE TABLE `system` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `companyName` varchar(50) NOT NULL DEFAULT '' COMMENT '公司名称',
  `keywords` varchar(150) NOT NULL DEFAULT '',
  `companyTel` char(11) NOT NULL DEFAULT '',
  `linkman` varchar(35) NOT NULL DEFAULT '' COMMENT '联系人',
  `companyAddress` varchar(100) NOT NULL DEFAULT '',
  `companyFax` char(12) NOT NULL DEFAULT '',
  `companyEmail` varchar(20) NOT NULL DEFAULT '',
  `logo` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='系统设置表';

#
# Data for table "system"
#

INSERT INTO `system` VALUES (1,'翔隆服饰有限公司','','0771-888888','马经理','湖南省长沙市芙蓉路某区某路111号','400-200-300','198472252@qq.com','upload/20180809153554dehn.jpg','1');

#
# Structure for table "team"
#

DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `phone` varchar(15) NOT NULL DEFAULT '0',
  `sex` tinyint(3) NOT NULL DEFAULT '0',
  `position` varchar(50) NOT NULL DEFAULT '' COMMENT '职位',
  `imgsrc` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='公司团队';

#
# Data for table "team"
#

INSERT INTO `team` VALUES (1,'陈小聪','2147483647',0,'服装设计师','upload/2018-07-24/team/20180724154101yy6Q.jpg'),(3,'陈小聪','2147483647',0,'服装设计师','upload/2018-07-24/team/20180724160504Ab6F.jpg'),(4,'陈小聪','2147483647',0,'服装设计师','upload/2018-07-24/team/201807241611152ts3.jpg'),(5,'陈小聪','13477777777',0,'服装设计师','upload/2018-07-24/team/20180724161326brDi.jpg');
