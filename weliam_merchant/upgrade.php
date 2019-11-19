<?php
//升级数据表
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_activity_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `name` varchar(125) NOT NULL COMMENT '名称',
  `logo` varchar(255) NOT NULL COMMENT '图标',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '状态',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_activity_category','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_category')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_activity_category','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_category')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_activity_category','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_category')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_activity_category','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_category')." ADD   `name` varchar(125) NOT NULL COMMENT '名称'");}
if(!pdo_fieldexists('ims_wlmerchant_activity_category','logo')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_category')." ADD   `logo` varchar(255) NOT NULL COMMENT '图标'");}
if(!pdo_fieldexists('ims_wlmerchant_activity_category','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_category')." ADD   `sort` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_wlmerchant_activity_category','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_category')." ADD   `status` int(11) NOT NULL COMMENT '状态'");}
if(!pdo_fieldexists('ims_wlmerchant_activity_category','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_category')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_activity_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '状态 1待使用 2已核销',
  `sid` int(11) NOT NULL COMMENT '商户id',
  `activityid` int(11) NOT NULL COMMENT '活动id',
  `mid` int(11) NOT NULL COMMENT '用户id',
  `checkcode` varchar(145) NOT NULL COMMENT '验证码',
  `usetimes` int(11) NOT NULL COMMENT '剩余使用次数',
  `usedtime` text NOT NULL COMMENT '核销记录',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `orderid` int(11) DEFAULT NULL COMMENT '订单id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_activity_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_record')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_activity_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_record')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_activity_record','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_record')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_activity_record','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_record')." ADD   `status` int(11) NOT NULL COMMENT '状态 1待使用 2已核销'");}
if(!pdo_fieldexists('ims_wlmerchant_activity_record','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_record')." ADD   `sid` int(11) NOT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('ims_wlmerchant_activity_record','activityid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_record')." ADD   `activityid` int(11) NOT NULL COMMENT '活动id'");}
if(!pdo_fieldexists('ims_wlmerchant_activity_record','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_record')." ADD   `mid` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_wlmerchant_activity_record','checkcode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_record')." ADD   `checkcode` varchar(145) NOT NULL COMMENT '验证码'");}
if(!pdo_fieldexists('ims_wlmerchant_activity_record','usetimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_record')." ADD   `usetimes` int(11) NOT NULL COMMENT '剩余使用次数'");}
if(!pdo_fieldexists('ims_wlmerchant_activity_record','usedtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_record')." ADD   `usedtime` text NOT NULL COMMENT '核销记录'");}
if(!pdo_fieldexists('ims_wlmerchant_activity_record','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_record')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_activity_record','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activity_record')." ADD   `orderid` int(11) DEFAULT NULL COMMENT '订单id'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_activitylist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `sid` int(11) NOT NULL COMMENT '商户id',
  `cateid` int(11) NOT NULL COMMENT '分类id',
  `status` int(11) NOT NULL COMMENT '状态 0待审核 1未开始报名 2报名中  3已结束 4被驳回',
  `title` varchar(145) NOT NULL COMMENT '活动标题',
  `thumb` varchar(145) NOT NULL COMMENT '图片',
  `activestarttime` int(11) NOT NULL COMMENT '活动开始时间',
  `activeendtime` int(11) NOT NULL COMMENT '活动结束时间',
  `address` varchar(225) NOT NULL COMMENT '活动地址',
  `pv` int(11) NOT NULL COMMENT '活动浏览量',
  `sort` int(11) NOT NULL COMMENT '活动排序',
  `enrollstarttime` int(11) NOT NULL COMMENT '报名开始时间',
  `enrollendtime` int(11) NOT NULL COMMENT '报名结束时间',
  `maxpeoplenum` int(11) NOT NULL COMMENT '活动最大人数',
  `minpeoplenum` int(11) NOT NULL COMMENT '活动最小人数',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '报名费',
  `vipstatus` int(11) NOT NULL COMMENT 'vip设置 0无 1特价 2特供',
  `vipprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'vip报名费',
  `settlementmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '结算金额',
  `vipsettlementmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'vip结算价',
  `isdistri` int(11) NOT NULL COMMENT '是否参与分销 1参与 0不参与',
  `onedismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '一级分销',
  `twodismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '二级分销',
  `threedismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '三级分销',
  `threeurl` varchar(225) NOT NULL COMMENT '三方链接',
  `bgmusic` varchar(255) NOT NULL COMMENT '背景音乐',
  `enrolldetail` text NOT NULL COMMENT '报名须知',
  `detail` text NOT NULL COMMENT '活动详情',
  `share_title` varchar(32) NOT NULL COMMENT '分享标题',
  `share_desc` varchar(32) NOT NULL COMMENT '分享详情',
  `share_image` varchar(255) NOT NULL COMMENT '分享图片',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `enrollnum` int(11) NOT NULL COMMENT '已经报名人数',
  `viponedismoney` decimal(10,2) NOT NULL,
  `viptwodismoney` decimal(10,2) NOT NULL,
  `vipthreedismoney` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_activitylist','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `sid` int(11) NOT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','cateid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `cateid` int(11) NOT NULL COMMENT '分类id'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `status` int(11) NOT NULL COMMENT '状态 0待审核 1未开始报名 2报名中  3已结束 4被驳回'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `title` varchar(145) NOT NULL COMMENT '活动标题'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `thumb` varchar(145) NOT NULL COMMENT '图片'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','activestarttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `activestarttime` int(11) NOT NULL COMMENT '活动开始时间'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','activeendtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `activeendtime` int(11) NOT NULL COMMENT '活动结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','address')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `address` varchar(225) NOT NULL COMMENT '活动地址'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','pv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `pv` int(11) NOT NULL COMMENT '活动浏览量'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `sort` int(11) NOT NULL COMMENT '活动排序'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','enrollstarttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `enrollstarttime` int(11) NOT NULL COMMENT '报名开始时间'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','enrollendtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `enrollendtime` int(11) NOT NULL COMMENT '报名结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','maxpeoplenum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `maxpeoplenum` int(11) NOT NULL COMMENT '活动最大人数'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','minpeoplenum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `minpeoplenum` int(11) NOT NULL COMMENT '活动最小人数'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '报名费'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','vipstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `vipstatus` int(11) NOT NULL COMMENT 'vip设置 0无 1特价 2特供'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','vipprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `vipprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'vip报名费'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','settlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `settlementmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '结算金额'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','vipsettlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `vipsettlementmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'vip结算价'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','isdistri')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `isdistri` int(11) NOT NULL COMMENT '是否参与分销 1参与 0不参与'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','onedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `onedismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '一级分销'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','twodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `twodismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '二级分销'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','threedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `threedismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '三级分销'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','threeurl')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `threeurl` varchar(225) NOT NULL COMMENT '三方链接'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','bgmusic')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `bgmusic` varchar(255) NOT NULL COMMENT '背景音乐'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','enrolldetail')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `enrolldetail` text NOT NULL COMMENT '报名须知'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','detail')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `detail` text NOT NULL COMMENT '活动详情'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','share_title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `share_title` varchar(32) NOT NULL COMMENT '分享标题'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','share_desc')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `share_desc` varchar(32) NOT NULL COMMENT '分享详情'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','share_image')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `share_image` varchar(255) NOT NULL COMMENT '分享图片'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','enrollnum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `enrollnum` int(11) NOT NULL COMMENT '已经报名人数'");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','viponedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `viponedismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','viptwodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `viptwodismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_activitylist','vipthreedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_activitylist')." ADD   `vipthreedismoney` decimal(10,2) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '状态 1默认',
  `name` varchar(125) DEFAULT NULL COMMENT '收货人姓名',
  `tel` varchar(125) DEFAULT NULL COMMENT '收货人电话',
  `province` varchar(45) DEFAULT NULL COMMENT '省',
  `city` varchar(45) DEFAULT NULL COMMENT '市',
  `county` varchar(45) DEFAULT NULL COMMENT '县区',
  `detailed_address` text COMMENT '创建时间',
  `addtime` varchar(125) DEFAULT NULL COMMENT '最后更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_address','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_address')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_address','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_address')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_address','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_address')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_address','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_address')." ADD   `mid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_address','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_address')." ADD   `status` int(11) DEFAULT NULL COMMENT '状态 1默认'");}
if(!pdo_fieldexists('ims_wlmerchant_address','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_address')." ADD   `name` varchar(125) DEFAULT NULL COMMENT '收货人姓名'");}
if(!pdo_fieldexists('ims_wlmerchant_address','tel')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_address')." ADD   `tel` varchar(125) DEFAULT NULL COMMENT '收货人电话'");}
if(!pdo_fieldexists('ims_wlmerchant_address','province')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_address')." ADD   `province` varchar(45) DEFAULT NULL COMMENT '省'");}
if(!pdo_fieldexists('ims_wlmerchant_address','city')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_address')." ADD   `city` varchar(45) DEFAULT NULL COMMENT '市'");}
if(!pdo_fieldexists('ims_wlmerchant_address','county')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_address')." ADD   `county` varchar(45) DEFAULT NULL COMMENT '县区'");}
if(!pdo_fieldexists('ims_wlmerchant_address','detailed_address')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_address')." ADD   `detailed_address` text COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_address','addtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_address')." ADD   `addtime` varchar(125) DEFAULT NULL COMMENT '最后更新时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_adv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `advname` varchar(50) DEFAULT '' COMMENT '幻灯片名称',
  `link` varchar(255) DEFAULT '' COMMENT '幻灯片链接',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '幻灯片图片',
  `displayorder` int(11) DEFAULT '0' COMMENT '排序',
  `enabled` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1显示',
  `type` int(11) DEFAULT '0',
  `cateid` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=165 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_adv','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_adv')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_adv','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_adv')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_adv','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_adv')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_adv','advname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_adv')." ADD   `advname` varchar(50) DEFAULT '' COMMENT '幻灯片名称'");}
if(!pdo_fieldexists('ims_wlmerchant_adv','link')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_adv')." ADD   `link` varchar(255) DEFAULT '' COMMENT '幻灯片链接'");}
if(!pdo_fieldexists('ims_wlmerchant_adv','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_adv')." ADD   `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '幻灯片图片'");}
if(!pdo_fieldexists('ims_wlmerchant_adv','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_adv')." ADD   `displayorder` int(11) DEFAULT '0' COMMENT '排序'");}
if(!pdo_fieldexists('ims_wlmerchant_adv','enabled')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_adv')." ADD   `enabled` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1显示'");}
if(!pdo_fieldexists('ims_wlmerchant_adv','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_adv')." ADD   `type` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_adv','cateid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_adv')." ADD   `cateid` int(10) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_agentadmin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `aid` int(11) NOT NULL COMMENT '代理id',
  `openid` varchar(100) NOT NULL,
  `mid` int(11) NOT NULL COMMENT '用户表id',
  `notice` int(11) NOT NULL COMMENT '通知权限 0无 1有',
  `manage` int(11) NOT NULL COMMENT '管理权限 0无 1有',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `account` varchar(20) DEFAULT NULL COMMENT '代理商员工登录账号',
  `password` varchar(32) DEFAULT NULL COMMENT '代理商员工登录密码',
  `jurisdiction` text COMMENT '代理商员工的操作权限',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_aid` (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_agentadmin','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_agentadmin','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_agentadmin','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD   `aid` int(11) NOT NULL COMMENT '代理id'");}
if(!pdo_fieldexists('ims_wlmerchant_agentadmin','openid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD   `openid` varchar(100) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentadmin','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD   `mid` int(11) NOT NULL COMMENT '用户表id'");}
if(!pdo_fieldexists('ims_wlmerchant_agentadmin','notice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD   `notice` int(11) NOT NULL COMMENT '通知权限 0无 1有'");}
if(!pdo_fieldexists('ims_wlmerchant_agentadmin','manage')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD   `manage` int(11) NOT NULL COMMENT '管理权限 0无 1有'");}
if(!pdo_fieldexists('ims_wlmerchant_agentadmin','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_agentadmin','account')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD   `account` varchar(20) DEFAULT NULL COMMENT '代理商员工登录账号'");}
if(!pdo_fieldexists('ims_wlmerchant_agentadmin','password')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD   `password` varchar(32) DEFAULT NULL COMMENT '代理商员工登录密码'");}
if(!pdo_fieldexists('ims_wlmerchant_agentadmin','jurisdiction')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD   `jurisdiction` text COMMENT '代理商员工的操作权限'");}
if(!pdo_fieldexists('ims_wlmerchant_agentadmin','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_agentadmin','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentadmin')." ADD   KEY `idx_uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_agentsetting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=198 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_agentsetting','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentsetting')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_agentsetting','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentsetting')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentsetting','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentsetting')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentsetting','key')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentsetting')." ADD   `key` varchar(64) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentsetting','value')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentsetting')." ADD   `value` longtext NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_agentusers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `groupid` int(10) unsigned NOT NULL,
  `agentname` varchar(64) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `salt` varchar(10) NOT NULL,
  `realname` varchar(32) NOT NULL,
  `mobile` varchar(32) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `joindate` int(10) unsigned NOT NULL,
  `joinip` varchar(15) NOT NULL,
  `lastvisit` int(10) unsigned NOT NULL,
  `lastip` varchar(15) NOT NULL,
  `remark` varchar(500) NOT NULL,
  `starttime` int(10) unsigned NOT NULL,
  `endtime` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `percent` varchar(200) DEFAULT NULL,
  `cashopenid` varchar(200) DEFAULT NULL,
  `allmoney` decimal(10,2) DEFAULT '0.00',
  `nowmoney` decimal(10,2) DEFAULT '0.00',
  `payment_type` tinyint(1) DEFAULT NULL COMMENT '代理商收款方式(1=支付宝，2=微信，3=银行卡,4=余额[仅分销商有余额打款])',
  `bank_name` varchar(50) DEFAULT NULL COMMENT '代理商银行卡开户行信息',
  `card_number` varchar(20) DEFAULT NULL COMMENT '代理商银行卡账号信息',
  `alipay` varchar(20) DEFAULT NULL COMMENT '代理商支付宝账号信息',
  `bank_username` varchar(20) DEFAULT NULL COMMENT '代理商银行卡开户人的姓名',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_agentusers','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `uniacid` int(10) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','groupid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `groupid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','agentname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `agentname` varchar(64) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','username')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `username` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','password')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `password` varchar(64) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','salt')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `salt` varchar(10) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','realname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `realname` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','mobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `mobile` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `status` tinyint(4) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','joindate')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `joindate` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','joinip')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `joinip` varchar(15) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','lastvisit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `lastvisit` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','lastip')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `lastip` varchar(15) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `remark` varchar(500) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','starttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `starttime` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `endtime` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `type` tinyint(3) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','percent')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `percent` varchar(200) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','cashopenid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `cashopenid` varchar(200) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','allmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `allmoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','nowmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `nowmoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','payment_type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `payment_type` tinyint(1) DEFAULT NULL COMMENT '代理商收款方式(1=支付宝，2=微信，3=银行卡,4=余额[仅分销商有余额打款])'");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','bank_name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `bank_name` varchar(50) DEFAULT NULL COMMENT '代理商银行卡开户行信息'");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','card_number')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `card_number` varchar(20) DEFAULT NULL COMMENT '代理商银行卡账号信息'");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','alipay')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `alipay` varchar(20) DEFAULT NULL COMMENT '代理商支付宝账号信息'");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','bank_username')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   `bank_username` varchar(20) DEFAULT NULL COMMENT '代理商银行卡开户人的姓名'");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers','username')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers')." ADD   KEY `username` (`username`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_agentusers_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `package` varchar(5000) NOT NULL,
  `isdefault` int(2) unsigned NOT NULL,
  `enabled` int(2) unsigned NOT NULL,
  `createtime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_agentusers_group','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers_group')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers_group','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers_group')." ADD   `uniacid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers_group','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers_group')." ADD   `name` varchar(50) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers_group','package')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers_group')." ADD   `package` varchar(5000) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers_group','isdefault')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers_group')." ADD   `isdefault` int(2) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers_group','enabled')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers_group')." ADD   `enabled` int(2) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_agentusers_group','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_agentusers_group')." ADD   `createtime` int(11) unsigned NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_apirecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `sendmid` int(11) NOT NULL,
  `sendmobile` varchar(15) DEFAULT NULL,
  `takemid` int(11) NOT NULL,
  `takemobile` varchar(15) DEFAULT NULL,
  `type` smallint(2) NOT NULL,
  `remark` varchar(32) DEFAULT NULL,
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=211 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_apirecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_apirecord')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_apirecord','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_apirecord')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_apirecord','sendmid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_apirecord')." ADD   `sendmid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_apirecord','sendmobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_apirecord')." ADD   `sendmobile` varchar(15) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_apirecord','takemid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_apirecord')." ADD   `takemid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_apirecord','takemobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_apirecord')." ADD   `takemobile` varchar(15) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_apirecord','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_apirecord')." ADD   `type` smallint(2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_apirecord','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_apirecord')." ADD   `remark` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_apirecord','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_apirecord')." ADD   `createtime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_apirecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_apirecord')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_applydistributor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL,
  `realname` text,
  `mobile` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `createtime` varchar(145) DEFAULT NULL,
  `reason` text,
  `rank` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_applydistributor','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_applydistributor')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_applydistributor','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_applydistributor')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_applydistributor','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_applydistributor')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_applydistributor','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_applydistributor')." ADD   `mid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_applydistributor','realname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_applydistributor')." ADD   `realname` text");}
if(!pdo_fieldexists('ims_wlmerchant_applydistributor','mobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_applydistributor')." ADD   `mobile` varchar(100) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_applydistributor','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_applydistributor')." ADD   `status` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_applydistributor','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_applydistributor')." ADD   `createtime` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_applydistributor','reason')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_applydistributor')." ADD   `reason` text");}
if(!pdo_fieldexists('ims_wlmerchant_applydistributor','rank')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_applydistributor')." ADD   `rank` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL,
  `name` varchar(500) NOT NULL,
  `visible` tinyint(4) unsigned NOT NULL,
  `displayorder` int(11) NOT NULL,
  `level` tinyint(3) unsigned NOT NULL,
  `lat` varchar(16) NOT NULL,
  `lng` varchar(16) NOT NULL,
  `pinyin` varchar(32) NOT NULL,
  `initial` varchar(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `isShow` (`visible`),
  KEY `parentId` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=990105 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_area','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_area')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_area','pid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_area')." ADD   `pid` int(11) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_area','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_area')." ADD   `name` varchar(500) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_area','visible')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_area')." ADD   `visible` tinyint(4) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_area','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_area')." ADD   `displayorder` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_area','level')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_area')." ADD   `level` tinyint(3) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_area','lat')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_area')." ADD   `lat` varchar(16) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_area','lng')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_area')." ADD   `lng` varchar(16) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_area','pinyin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_area')." ADD   `pinyin` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_area','initial')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_area')." ADD   `initial` varchar(2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_area','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_area')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_area','isShow')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_area')." ADD   KEY `isShow` (`visible`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_areagroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_areagroup','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_areagroup')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_areagroup','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_areagroup')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_areagroup','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_areagroup')." ADD   `name` varchar(50) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_areagroup','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_areagroup')." ADD   `sort` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_autosettlement_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `aid` int(11) NOT NULL COMMENT '代理id',
  `type` int(11) NOT NULL COMMENT '类型 1抢购 2拼团 3卡券 4一卡通订单 5掌上信息 6付费入驻  7提现或驳回',
  `merchantid` int(11) NOT NULL COMMENT '商户id',
  `orderid` int(11) NOT NULL COMMENT '订单id',
  `orderno` varchar(145) NOT NULL COMMENT '订单编号',
  `goodsid` int(11) NOT NULL COMMENT '商品id',
  `orderprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单实际金额',
  `agentmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '代理收入',
  `merchantmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商户结算收入',
  `distrimoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '给分销商的金额',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `merchantnowmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改后商户现有金额',
  `agentnowmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改后代理现有金额',
  `specialstatus` int(11) NOT NULL,
  `sharemoney` decimal(10,2) NOT NULL,
  `checkcode` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_aid` (`aid`),
  KEY `idx_merchantid` (`merchantid`),
  KEY `idx_type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=166 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `aid` int(11) NOT NULL COMMENT '代理id'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `type` int(11) NOT NULL COMMENT '类型 1抢购 2拼团 3卡券 4一卡通订单 5掌上信息 6付费入驻  7提现或驳回'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','merchantid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `merchantid` int(11) NOT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `orderid` int(11) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','orderno')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `orderno` varchar(145) NOT NULL COMMENT '订单编号'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','goodsid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `goodsid` int(11) NOT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','orderprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `orderprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单实际金额'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','agentmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `agentmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '代理收入'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','merchantmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `merchantmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商户结算收入'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','distrimoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `distrimoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '给分销商的金额'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','merchantnowmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `merchantnowmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改后商户现有金额'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','agentnowmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `agentnowmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改后代理现有金额'");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','specialstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `specialstatus` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','sharemoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `sharemoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','checkcode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   `checkcode` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   KEY `idx_uniacid` (`uniacid`)");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','idx_aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   KEY `idx_aid` (`aid`)");}
if(!pdo_fieldexists('ims_wlmerchant_autosettlement_record','idx_merchantid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_autosettlement_record')." ADD   KEY `idx_merchantid` (`merchantid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '唯一标识',
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `link` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `displayorder` int(11) NOT NULL,
  `enabled` int(11) NOT NULL,
  `visible_level` varchar(145) NOT NULL COMMENT '1强制推广',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_banner','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_banner')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '唯一标识'");}
if(!pdo_fieldexists('ims_wlmerchant_banner','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_banner')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_banner','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_banner')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_banner','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_banner')." ADD   `name` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_banner','link')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_banner')." ADD   `link` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_banner','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_banner')." ADD   `thumb` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_banner','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_banner')." ADD   `displayorder` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_banner','enabled')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_banner')." ADD   `enabled` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_banner','visible_level')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_banner')." ADD   `visible_level` varchar(145) NOT NULL COMMENT '1强制推广'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_bargain_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '商品名称',
  `status` int(11) NOT NULL COMMENT '活动状态 0下架中 1未开始 2进行中 3已结束 5待审核 6未通过',
  `cateid` int(11) NOT NULL COMMENT '分类id',
  `sid` int(11) NOT NULL COMMENT '商户id',
  `oldprice` decimal(10,2) NOT NULL COMMENT '商品原价',
  `price` decimal(10,2) NOT NULL COMMENT '商品底价',
  `vipprice` decimal(10,2) NOT NULL COMMENT '会员底价',
  `submitmoneylimit` decimal(10,2) NOT NULL COMMENT '允许提交订单的金额',
  `starttime` int(11) NOT NULL COMMENT '活动开始时间',
  `endtime` int(11) NOT NULL COMMENT '活动结束时间',
  `helplimit` int(11) NOT NULL COMMENT '好友帮砍限制数量',
  `dayhelpcount` int(11) NOT NULL COMMENT '每天帮砍好友人数限制',
  `joinlimit` int(11) NOT NULL COMMENT '参加人数限制',
  `falsejoinnum` int(11) NOT NULL COMMENT '虚拟参与人数',
  `falselooknum` int(11) NOT NULL COMMENT '虚拟浏览量',
  `falsesharenum` int(11) NOT NULL COMMENT '虚拟分享次数',
  `code` varchar(50) NOT NULL COMMENT '商品编号',
  `thumb` varchar(255) NOT NULL COMMENT '海报图片',
  `thumbs` text NOT NULL COMMENT '幻灯片',
  `unit` varchar(45) NOT NULL COMMENT '商品单位',
  `bgmusic` varchar(255) NOT NULL COMMENT '背景音乐',
  `detail` text NOT NULL COMMENT '商品详情',
  `rules` text NOT NULL COMMENT '砍价规则',
  `vipstatus` int(11) NOT NULL COMMENT '会员设置',
  `share_image` varchar(255) NOT NULL COMMENT '分享图片',
  `share_title` varchar(1000) NOT NULL COMMENT '分享标题',
  `share_desc` varchar(1000) NOT NULL COMMENT '分享描述',
  `settlementmoney` decimal(10,2) NOT NULL COMMENT '一般结算价格',
  `vipsettlementmoney` decimal(10,2) NOT NULL COMMENT '会员结算价格',
  `isdistri` int(11) NOT NULL COMMENT '是否参与分销',
  `onedismoney` decimal(10,2) NOT NULL COMMENT '普通一级分销金额',
  `twodismoney` decimal(10,2) NOT NULL COMMENT '普通二级分销金额',
  `viponedismoney` decimal(10,2) NOT NULL COMMENT '会员一级分销金额',
  `viptwodismoney` decimal(10,2) NOT NULL COMMENT '会员二级分销金额',
  `userlabel` text NOT NULL COMMENT '用户标签',
  `stock` int(11) NOT NULL COMMENT '商品库存',
  `sort` int(11) NOT NULL COMMENT '商品排序',
  `pv` int(11) NOT NULL COMMENT '真实浏览量',
  `sharenum` int(11) NOT NULL COMMENT '真实分享数',
  `usestatus` int(11) NOT NULL COMMENT '消费方式',
  `expressid` int(11) NOT NULL COMMENT '运费模板id',
  `independent` int(11) NOT NULL COMMENT '独立结算金额',
  `createtime` int(11) NOT NULL,
  `allowapplyre` int(11) NOT NULL,
  `level` text NOT NULL COMMENT '适用会员等级',
  `onlytimes` int(11) NOT NULL COMMENT '单个用户可以砍价次数',
  `dissettime` int(11) NOT NULL COMMENT '结算时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='砍价商品表';

");

if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `name` varchar(50) NOT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `status` int(11) NOT NULL COMMENT '活动状态 0下架中 1未开始 2进行中 3已结束 5待审核 6未通过'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','cateid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `cateid` int(11) NOT NULL COMMENT '分类id'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `sid` int(11) NOT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','oldprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `oldprice` decimal(10,2) NOT NULL COMMENT '商品原价'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `price` decimal(10,2) NOT NULL COMMENT '商品底价'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','vipprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `vipprice` decimal(10,2) NOT NULL COMMENT '会员底价'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','submitmoneylimit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `submitmoneylimit` decimal(10,2) NOT NULL COMMENT '允许提交订单的金额'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','starttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `starttime` int(11) NOT NULL COMMENT '活动开始时间'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `endtime` int(11) NOT NULL COMMENT '活动结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','helplimit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `helplimit` int(11) NOT NULL COMMENT '好友帮砍限制数量'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','dayhelpcount')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `dayhelpcount` int(11) NOT NULL COMMENT '每天帮砍好友人数限制'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','joinlimit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `joinlimit` int(11) NOT NULL COMMENT '参加人数限制'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','falsejoinnum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `falsejoinnum` int(11) NOT NULL COMMENT '虚拟参与人数'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','falselooknum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `falselooknum` int(11) NOT NULL COMMENT '虚拟浏览量'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','falsesharenum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `falsesharenum` int(11) NOT NULL COMMENT '虚拟分享次数'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','code')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `code` varchar(50) NOT NULL COMMENT '商品编号'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `thumb` varchar(255) NOT NULL COMMENT '海报图片'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','thumbs')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `thumbs` text NOT NULL COMMENT '幻灯片'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','unit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `unit` varchar(45) NOT NULL COMMENT '商品单位'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','bgmusic')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `bgmusic` varchar(255) NOT NULL COMMENT '背景音乐'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','detail')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `detail` text NOT NULL COMMENT '商品详情'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','rules')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `rules` text NOT NULL COMMENT '砍价规则'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','vipstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `vipstatus` int(11) NOT NULL COMMENT '会员设置'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','share_image')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `share_image` varchar(255) NOT NULL COMMENT '分享图片'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','share_title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `share_title` varchar(1000) NOT NULL COMMENT '分享标题'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','share_desc')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `share_desc` varchar(1000) NOT NULL COMMENT '分享描述'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','settlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `settlementmoney` decimal(10,2) NOT NULL COMMENT '一般结算价格'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','vipsettlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `vipsettlementmoney` decimal(10,2) NOT NULL COMMENT '会员结算价格'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','isdistri')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `isdistri` int(11) NOT NULL COMMENT '是否参与分销'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','onedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `onedismoney` decimal(10,2) NOT NULL COMMENT '普通一级分销金额'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','twodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `twodismoney` decimal(10,2) NOT NULL COMMENT '普通二级分销金额'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','viponedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `viponedismoney` decimal(10,2) NOT NULL COMMENT '会员一级分销金额'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','viptwodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `viptwodismoney` decimal(10,2) NOT NULL COMMENT '会员二级分销金额'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','userlabel')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `userlabel` text NOT NULL COMMENT '用户标签'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','stock')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `stock` int(11) NOT NULL COMMENT '商品库存'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `sort` int(11) NOT NULL COMMENT '商品排序'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','pv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `pv` int(11) NOT NULL COMMENT '真实浏览量'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','sharenum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `sharenum` int(11) NOT NULL COMMENT '真实分享数'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','usestatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `usestatus` int(11) NOT NULL COMMENT '消费方式'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','expressid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `expressid` int(11) NOT NULL COMMENT '运费模板id'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','independent')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `independent` int(11) NOT NULL COMMENT '独立结算金额'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `createtime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','allowapplyre')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `allowapplyre` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','level')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `level` text NOT NULL COMMENT '适用会员等级'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','onlytimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `onlytimes` int(11) NOT NULL COMMENT '单个用户可以砍价次数'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_activity','dissettime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_activity')." ADD   `dissettime` int(11) NOT NULL COMMENT '结算时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_bargain_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `aid` int(11) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='砍价分类表';

");

if(!pdo_fieldexists('ims_wlmerchant_bargain_category','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_category')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_category','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_category')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_category','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_category')." ADD   `name` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_category','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_category')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_category','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_category')." ADD   `thumb` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_category','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_category')." ADD   `sort` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_bargain_helprecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `activityid` int(11) NOT NULL COMMENT '活动id',
  `userid` int(11) NOT NULL COMMENT '用户砍价单id',
  `authorid` int(11) NOT NULL COMMENT '发起人id',
  `mid` int(11) NOT NULL COMMENT '用户id',
  `bargainprice` decimal(10,2) NOT NULL COMMENT '砍价价格',
  `afterprice` decimal(10,2) NOT NULL COMMENT '砍后价格',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='砍价记录表';

");

if(!pdo_fieldexists('ims_wlmerchant_bargain_helprecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_helprecord')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_helprecord','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_helprecord')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_helprecord','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_helprecord')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_helprecord','activityid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_helprecord')." ADD   `activityid` int(11) NOT NULL COMMENT '活动id'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_helprecord','userid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_helprecord')." ADD   `userid` int(11) NOT NULL COMMENT '用户砍价单id'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_helprecord','authorid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_helprecord')." ADD   `authorid` int(11) NOT NULL COMMENT '发起人id'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_helprecord','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_helprecord')." ADD   `mid` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_helprecord','bargainprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_helprecord')." ADD   `bargainprice` decimal(10,2) NOT NULL COMMENT '砍价价格'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_helprecord','afterprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_helprecord')." ADD   `afterprice` decimal(10,2) NOT NULL COMMENT '砍后价格'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_helprecord','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_helprecord')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_bargain_userlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `activityid` int(11) NOT NULL COMMENT '活动id',
  `merchantid` int(11) NOT NULL COMMENT '商户id',
  `mid` int(11) NOT NULL COMMENT '用户id',
  `status` int(11) NOT NULL COMMENT '状态 1进行中 2支付 3已失败',
  `price` decimal(10,2) NOT NULL COMMENT '当前价格',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `updatetime` int(11) NOT NULL COMMENT '修改时间',
  `orderid` int(11) NOT NULL COMMENT '订单表中的订单id',
  `qrcode` int(11) NOT NULL COMMENT '验证码',
  `usetimes` int(11) NOT NULL COMMENT '剩余使用次数',
  `usedtime` text NOT NULL,
  `expressid` int(11) NOT NULL COMMENT '快递id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='砍价活动表';

");

if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','activityid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `activityid` int(11) NOT NULL COMMENT '活动id'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','merchantid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `merchantid` int(11) NOT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `mid` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `status` int(11) NOT NULL COMMENT '状态 1进行中 2支付 3已失败'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `price` decimal(10,2) NOT NULL COMMENT '当前价格'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','updatetime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `updatetime` int(11) NOT NULL COMMENT '修改时间'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `orderid` int(11) NOT NULL COMMENT '订单表中的订单id'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','qrcode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `qrcode` int(11) NOT NULL COMMENT '验证码'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','usetimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `usetimes` int(11) NOT NULL COMMENT '剩余使用次数'");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','usedtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `usedtime` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_bargain_userlist','expressid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_bargain_userlist')." ADD   `expressid` int(11) NOT NULL COMMENT '快递id'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_call` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL COMMENT '公众号id',
  `aid` int(11) unsigned NOT NULL COMMENT '代理商id',
  `title` varchar(25) NOT NULL COMMENT '集call活动名称',
  `number` smallint(3) unsigned NOT NULL COMMENT '集call的数量',
  `state` tinyint(1) unsigned NOT NULL COMMENT '活动状态(1=开启,2=关闭)',
  `prize_id` int(10) unsigned NOT NULL COMMENT '活动奖品的id',
  `explain` text COMMENT '活动的说明',
  `limit` text COMMENT '活动的限制',
  `receive_time` varchar(11) NOT NULL COMMENT '奖品领取期限',
  `use_time` varchar(11) NOT NULL COMMENT '奖品使用期限',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='集call活动列表';

");

if(!pdo_fieldexists('ims_wlmerchant_call','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_call','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call')." ADD   `uniacid` int(11) unsigned NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_call','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call')." ADD   `aid` int(11) unsigned NOT NULL COMMENT '代理商id'");}
if(!pdo_fieldexists('ims_wlmerchant_call','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call')." ADD   `title` varchar(25) NOT NULL COMMENT '集call活动名称'");}
if(!pdo_fieldexists('ims_wlmerchant_call','number')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call')." ADD   `number` smallint(3) unsigned NOT NULL COMMENT '集call的数量'");}
if(!pdo_fieldexists('ims_wlmerchant_call','state')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call')." ADD   `state` tinyint(1) unsigned NOT NULL COMMENT '活动状态(1=开启,2=关闭)'");}
if(!pdo_fieldexists('ims_wlmerchant_call','prize_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call')." ADD   `prize_id` int(10) unsigned NOT NULL COMMENT '活动奖品的id'");}
if(!pdo_fieldexists('ims_wlmerchant_call','explain')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call')." ADD   `explain` text COMMENT '活动的说明'");}
if(!pdo_fieldexists('ims_wlmerchant_call','limit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call')." ADD   `limit` text COMMENT '活动的限制'");}
if(!pdo_fieldexists('ims_wlmerchant_call','receive_time')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call')." ADD   `receive_time` varchar(11) NOT NULL COMMENT '奖品领取期限'");}
if(!pdo_fieldexists('ims_wlmerchant_call','use_time')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call')." ADD   `use_time` varchar(11) NOT NULL COMMENT '奖品使用期限'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_call_hit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL COMMENT '公众号id',
  `aid` int(11) unsigned NOT NULL COMMENT '代理商id',
  `mid` int(11) unsigned NOT NULL COMMENT '打call用户的id',
  `list_id` int(11) unsigned NOT NULL COMMENT '已发起的集call活动的id',
  `hit_time` varchar(11) NOT NULL COMMENT '打call的时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='打call信息列表';

");

if(!pdo_fieldexists('ims_wlmerchant_call_hit','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_hit')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_call_hit','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_hit')." ADD   `uniacid` int(11) unsigned NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_call_hit','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_hit')." ADD   `aid` int(11) unsigned NOT NULL COMMENT '代理商id'");}
if(!pdo_fieldexists('ims_wlmerchant_call_hit','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_hit')." ADD   `mid` int(11) unsigned NOT NULL COMMENT '打call用户的id'");}
if(!pdo_fieldexists('ims_wlmerchant_call_hit','list_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_hit')." ADD   `list_id` int(11) unsigned NOT NULL COMMENT '已发起的集call活动的id'");}
if(!pdo_fieldexists('ims_wlmerchant_call_hit','hit_time')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_hit')." ADD   `hit_time` varchar(11) NOT NULL COMMENT '打call的时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_call_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL COMMENT '公众号id',
  `aid` int(11) unsigned NOT NULL COMMENT '代理商id',
  `mid` int(11) unsigned NOT NULL COMMENT '发起用户的id',
  `call_id` int(11) unsigned NOT NULL COMMENT '集call活动的id',
  `start_time` int(11) unsigned NOT NULL COMMENT '发起的时间',
  `grant` tinyint(1) DEFAULT '1' COMMENT '奖品是否发放(1=未发放，2=已发放)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='已发起集call活动的信息列表';

");

if(!pdo_fieldexists('ims_wlmerchant_call_list','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_list')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_call_list','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_list')." ADD   `uniacid` int(11) unsigned NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_call_list','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_list')." ADD   `aid` int(11) unsigned NOT NULL COMMENT '代理商id'");}
if(!pdo_fieldexists('ims_wlmerchant_call_list','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_list')." ADD   `mid` int(11) unsigned NOT NULL COMMENT '发起用户的id'");}
if(!pdo_fieldexists('ims_wlmerchant_call_list','call_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_list')." ADD   `call_id` int(11) unsigned NOT NULL COMMENT '集call活动的id'");}
if(!pdo_fieldexists('ims_wlmerchant_call_list','start_time')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_list')." ADD   `start_time` int(11) unsigned NOT NULL COMMENT '发起的时间'");}
if(!pdo_fieldexists('ims_wlmerchant_call_list','grant')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_call_list')." ADD   `grant` tinyint(1) DEFAULT '1' COMMENT '奖品是否发放(1=未发放，2=已发放)'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_category_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `aid` int(10) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `thumb` varchar(255) NOT NULL COMMENT '分类图片',
  `parentid` int(10) unsigned DEFAULT '0' COMMENT '上级分类ID,0为第一级',
  `isrecommand` int(10) DEFAULT '0',
  `description` varchar(500) DEFAULT NULL COMMENT '分类介绍',
  `displayorder` tinyint(3) unsigned DEFAULT '0' COMMENT '排序',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启',
  `visible_level` int(11) DEFAULT NULL COMMENT '1为首页顶部展示',
  `abroad` varchar(500) NOT NULL COMMENT '定时参数',
  `state` int(11) NOT NULL COMMENT '不在商户详情页显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=838 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_category_store','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_category_store','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD   `uniacid` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_category_store','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD   `aid` int(10) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_category_store','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD   `name` varchar(50) NOT NULL COMMENT '分类名称'");}
if(!pdo_fieldexists('ims_wlmerchant_category_store','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD   `thumb` varchar(255) NOT NULL COMMENT '分类图片'");}
if(!pdo_fieldexists('ims_wlmerchant_category_store','parentid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD   `parentid` int(10) unsigned DEFAULT '0' COMMENT '上级分类ID,0为第一级'");}
if(!pdo_fieldexists('ims_wlmerchant_category_store','isrecommand')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD   `isrecommand` int(10) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_category_store','description')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD   `description` varchar(500) DEFAULT NULL COMMENT '分类介绍'");}
if(!pdo_fieldexists('ims_wlmerchant_category_store','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD   `displayorder` tinyint(3) unsigned DEFAULT '0' COMMENT '排序'");}
if(!pdo_fieldexists('ims_wlmerchant_category_store','enabled')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD   `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启'");}
if(!pdo_fieldexists('ims_wlmerchant_category_store','visible_level')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD   `visible_level` int(11) DEFAULT NULL COMMENT '1为首页顶部展示'");}
if(!pdo_fieldexists('ims_wlmerchant_category_store','abroad')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD   `abroad` varchar(500) NOT NULL COMMENT '定时参数'");}
if(!pdo_fieldexists('ims_wlmerchant_category_store','state')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_category_store')." ADD   `state` int(11) NOT NULL COMMENT '不在商户详情页显示'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_chargelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `name` varchar(225) NOT NULL COMMENT '名称',
  `days` int(11) NOT NULL COMMENT '天数',
  `status` int(11) NOT NULL COMMENT '状态 1启用0禁用',
  `price` decimal(10,2) NOT NULL COMMENT '价格',
  `audits` int(11) NOT NULL COMMENT '免审核',
  `onedismoney` decimal(10,2) DEFAULT '0.00',
  `twodismoney` decimal(10,2) DEFAULT '0.00',
  `threedismoney` decimal(10,2) DEFAULT '0.00',
  `isdistri` int(11) DEFAULT '0',
  `sort` int(11) NOT NULL,
  `renewstatus` int(11) NOT NULL,
  `aid` int(11) NOT NULL COMMENT '地区id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_chargelist','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `name` varchar(225) NOT NULL COMMENT '名称'");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','days')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `days` int(11) NOT NULL COMMENT '天数'");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `status` int(11) NOT NULL COMMENT '状态 1启用0禁用'");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `price` decimal(10,2) NOT NULL COMMENT '价格'");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','audits')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `audits` int(11) NOT NULL COMMENT '免审核'");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','onedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `onedismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','twodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `twodismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','threedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `threedismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','isdistri')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `isdistri` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `sort` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','renewstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `renewstatus` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_chargelist','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_chargelist')." ADD   `aid` int(11) NOT NULL COMMENT '地区id'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL,
  `storeid` int(11) DEFAULT NULL,
  `createtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_collect','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_collect')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_collect','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_collect')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_collect','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_collect')." ADD   `mid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_collect','storeid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_collect')." ADD   `storeid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_collect','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_collect')." ADD   `createtime` int(11) DEFAULT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `gid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '对应的商品id',
  `mid` int(11) DEFAULT NULL COMMENT '用户ID',
  `sid` int(11) DEFAULT NULL COMMENT '商家ID',
  `parentid` int(11) DEFAULT NULL COMMENT '回复上级ID',
  `pic` varchar(1000) DEFAULT NULL COMMENT '图片',
  `idoforder` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '对应的order的id',
  `text` varchar(800) DEFAULT NULL COMMENT '评价文字',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示 0显示 1不显示',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '评价等级 1好评 2中评 3差评',
  `createtime` varchar(145) NOT NULL DEFAULT '0' COMMENT '评价时间',
  `headimg` varchar(255) DEFAULT NULL COMMENT '评价人头像',
  `nickname` varchar(32) DEFAULT NULL COMMENT '评价人昵称',
  `plugin` varchar(32) DEFAULT NULL COMMENT '插件名称',
  `star` int(11) DEFAULT '0',
  `replyone` int(11) DEFAULT '1',
  `checkone` int(11) DEFAULT '1',
  `true` int(11) DEFAULT '1',
  `replytextone` varchar(1000) DEFAULT NULL,
  `replypicone` varchar(500) DEFAULT NULL,
  `ispic` int(11) DEFAULT '0',
  `aid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index` (`idoforder`,`gid`,`status`,`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='价';

");

if(!pdo_fieldexists('ims_wlmerchant_comment','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_comment','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `uniacid` int(11) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','gid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `gid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '对应的商品id'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `mid` int(11) DEFAULT NULL COMMENT '用户ID'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `sid` int(11) DEFAULT NULL COMMENT '商家ID'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','parentid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `parentid` int(11) DEFAULT NULL COMMENT '回复上级ID'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','pic')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `pic` varchar(1000) DEFAULT NULL COMMENT '图片'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','idoforder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `idoforder` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '对应的order的id'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','text')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `text` varchar(800) DEFAULT NULL COMMENT '评价文字'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示 0显示 1不显示'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','level')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `level` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '评价等级 1好评 2中评 3差评'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `createtime` varchar(145) NOT NULL DEFAULT '0' COMMENT '评价时间'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','headimg')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `headimg` varchar(255) DEFAULT NULL COMMENT '评价人头像'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','nickname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `nickname` varchar(32) DEFAULT NULL COMMENT '评价人昵称'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','plugin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `plugin` varchar(32) DEFAULT NULL COMMENT '插件名称'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','star')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `star` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','replyone')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `replyone` int(11) DEFAULT '1'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','checkone')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `checkone` int(11) DEFAULT '1'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','true')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `true` int(11) DEFAULT '1'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','replytextone')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `replytextone` varchar(1000) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_comment','replypicone')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `replypicone` varchar(500) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_comment','ispic')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `ispic` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_comment','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_comment','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_comment')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_consumption` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL COMMENT '用户uid',
  `status` int(11) DEFAULT NULL COMMENT '状态 0未通知 1已通知',
  `credits` int(11) DEFAULT NULL COMMENT '扣除积分',
  `time` varchar(145) DEFAULT NULL COMMENT '兑换时间',
  `itemCode` int(11) DEFAULT NULL COMMENT '商品编号',
  `actualPrice` decimal(11,2) DEFAULT NULL COMMENT '商品实际价格',
  `description` text COMMENT '详细描述',
  `orderNum` text COMMENT '兑吧订单号',
  `yue` int(11) DEFAULT NULL COMMENT '本次操作后的余额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_consumption','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_consumption','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_consumption','uid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption')." ADD   `uid` int(11) DEFAULT NULL COMMENT '用户uid'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption')." ADD   `status` int(11) DEFAULT NULL COMMENT '状态 0未通知 1已通知'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption','credits')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption')." ADD   `credits` int(11) DEFAULT NULL COMMENT '扣除积分'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption','time')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption')." ADD   `time` varchar(145) DEFAULT NULL COMMENT '兑换时间'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption','itemCode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption')." ADD   `itemCode` int(11) DEFAULT NULL COMMENT '商品编号'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption','actualPrice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption')." ADD   `actualPrice` decimal(11,2) DEFAULT NULL COMMENT '商品实际价格'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption','description')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption')." ADD   `description` text COMMENT '详细描述'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption','orderNum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption')." ADD   `orderNum` text COMMENT '兑吧订单号'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption','yue')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption')." ADD   `yue` int(11) DEFAULT NULL COMMENT '本次操作后的余额'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_consumption_adv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `wxapp_link` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(10) DEFAULT '0',
  `status` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `status` (`status`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='积分商城幻灯片表';

");

if(!pdo_fieldexists('ims_wlmerchant_consumption_adv','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_adv')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_adv','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_adv')." ADD   `uniacid` int(10) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_adv','advname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_adv')." ADD   `advname` varchar(50) DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_adv','link')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_adv')." ADD   `link` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_adv','wxapp_link')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_adv')." ADD   `wxapp_link` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_adv','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_adv')." ADD   `thumb` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_adv','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_adv')." ADD   `displayorder` int(10) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_adv','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_adv')." ADD   `status` int(10) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_adv','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_adv')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_adv','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_adv')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_adv','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_adv')." ADD   KEY `status` (`status`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_consumption_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `displayorder` tinyint(3) unsigned DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  `advimg` varchar(255) DEFAULT '',
  `advurl` varchar(500) DEFAULT '',
  `isrecommand` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `displayorder` (`displayorder`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='积分商城分类表';

");

if(!pdo_fieldexists('ims_wlmerchant_consumption_category','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_category')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_category','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_category')." ADD   `uniacid` int(10) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_category','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_category')." ADD   `name` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_category','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_category')." ADD   `thumb` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_category','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_category')." ADD   `displayorder` tinyint(3) unsigned DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_category','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_category')." ADD   `status` tinyint(3) DEFAULT '1'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_category','advimg')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_category')." ADD   `advimg` varchar(255) DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_category','advurl')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_category')." ADD   `advurl` varchar(500) DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_category','isrecommand')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_category')." ADD   `isrecommand` tinyint(3) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_category','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_category')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_category','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_category')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_category','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_category')." ADD   KEY `displayorder` (`displayorder`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_consumption_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `category_id` int(10) NOT NULL COMMENT '分类id',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '商品类型',
  `thumb` varchar(255) NOT NULL COMMENT '商品图片',
  `old_price` varchar(10) NOT NULL COMMENT '商品原价',
  `chance` tinyint(3) unsigned NOT NULL COMMENT '每人共计兑换次数',
  `totalday` tinyint(3) unsigned NOT NULL COMMENT '每天提供份数',
  `use_credit1` varchar(10) NOT NULL DEFAULT '0' COMMENT '需要支付的积分',
  `use_credit2` varchar(10) NOT NULL DEFAULT '0' COMMENT '需要支付的金额',
  `description` text NOT NULL COMMENT '商品详情',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1开启 0关闭',
  `credit2` varchar(10) NOT NULL COMMENT '设置的赠送余额',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `redpacket` text NOT NULL COMMENT '红包设置',
  `pv` int(11) NOT NULL COMMENT '浏览量',
  `expressid` int(11) NOT NULL,
  `isdistri` int(11) NOT NULL,
  `onedismoney` decimal(10,2) NOT NULL,
  `twodismoney` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `vipstatus` int(11) NOT NULL,
  `vipcredit1` int(11) NOT NULL,
  `vipcredit2` decimal(10,2) NOT NULL,
  `dissettime` int(11) NOT NULL COMMENT '分销佣金结算时间',
  `halfcardid` int(11) NOT NULL COMMENT '一卡通类型的id',
  `advs` text NOT NULL COMMENT '幻灯片',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='积分商城商品表';

");

if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `uniacid` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `title` varchar(50) NOT NULL COMMENT '标题'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','category_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `category_id` int(10) NOT NULL COMMENT '分类id'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `type` varchar(20) NOT NULL DEFAULT '' COMMENT '商品类型'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `thumb` varchar(255) NOT NULL COMMENT '商品图片'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','old_price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `old_price` varchar(10) NOT NULL COMMENT '商品原价'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','chance')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `chance` tinyint(3) unsigned NOT NULL COMMENT '每人共计兑换次数'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','totalday')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `totalday` tinyint(3) unsigned NOT NULL COMMENT '每天提供份数'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','use_credit1')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `use_credit1` varchar(10) NOT NULL DEFAULT '0' COMMENT '需要支付的积分'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','use_credit2')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `use_credit2` varchar(10) NOT NULL DEFAULT '0' COMMENT '需要支付的金额'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','description')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `description` text NOT NULL COMMENT '商品详情'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1开启 0关闭'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','credit2')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `credit2` varchar(10) NOT NULL COMMENT '设置的赠送余额'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','redpacket')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `redpacket` text NOT NULL COMMENT '红包设置'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','pv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `pv` int(11) NOT NULL COMMENT '浏览量'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','expressid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `expressid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','isdistri')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `isdistri` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','onedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `onedismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','twodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `twodismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','stock')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `stock` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','vipstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `vipstatus` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','vipcredit1')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `vipcredit1` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','vipcredit2')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `vipcredit2` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','dissettime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `dissettime` int(11) NOT NULL COMMENT '分销佣金结算时间'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','halfcardid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `halfcardid` int(11) NOT NULL COMMENT '一卡通类型的id'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','advs')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   `advs` text NOT NULL COMMENT '幻灯片'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_goods')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_consumption_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `mid` int(11) NOT NULL COMMENT '用户id',
  `goodsid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `orderid` int(10) NOT NULL COMMENT '订单id',
  `expressid` int(11) NOT NULL COMMENT '快递id',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间书生',
  `status` int(11) NOT NULL COMMENT '状态 1待发货 2待收货 3已完成 4已退货',
  `integral` int(11) NOT NULL COMMENT '消耗积分',
  `money` decimal(10,2) NOT NULL COMMENT '金额',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='兑换记录表';

");

if(!pdo_fieldexists('ims_wlmerchant_consumption_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_record')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_record')." ADD   `uniacid` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_record','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_record')." ADD   `mid` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_record','goodsid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_record')." ADD   `goodsid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品id'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_record','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_record')." ADD   `orderid` int(10) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_record','expressid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_record')." ADD   `expressid` int(11) NOT NULL COMMENT '快递id'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_record','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_record')." ADD   `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间书生'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_record','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_record')." ADD   `status` int(11) NOT NULL COMMENT '状态 1待发货 2待收货 3已完成 4已退货'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_record','integral')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_record')." ADD   `integral` int(11) NOT NULL COMMENT '消耗积分'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_record','money')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_record')." ADD   `money` decimal(10,2) NOT NULL COMMENT '金额'");}
if(!pdo_fieldexists('ims_wlmerchant_consumption_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_consumption_record')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_couponlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) DEFAULT NULL COMMENT '代理id',
  `status` int(11) NOT NULL COMMENT '优惠券状态 1启用 0禁用2已失效',
  `type` int(11) NOT NULL COMMENT '优惠券类型 1 折扣券 2代金券 3礼品券 4 团购券 5优惠券',
  `is_charge` int(11) NOT NULL COMMENT '是否收费 1收费 0免费',
  `logo` varchar(100) NOT NULL COMMENT '优惠券logo',
  `indeximg` varchar(100) DEFAULT NULL COMMENT '优惠券详情顶部图片',
  `merchantid` int(11) NOT NULL COMMENT '商户id',
  `color` varchar(100) NOT NULL COMMENT '优惠券颜色',
  `title` varchar(145) NOT NULL COMMENT '优惠券标题',
  `sub_title` varchar(145) DEFAULT NULL COMMENT '优惠券小标题',
  `goodsdetail` text COMMENT '商品详情',
  `time_type` int(11) NOT NULL COMMENT '时间类型 1.规定时间段 2 领取后限制',
  `starttime` varchar(255) DEFAULT NULL COMMENT '开始时间',
  `endtime` varchar(255) DEFAULT NULL COMMENT '结束时间',
  `deadline` int(11) DEFAULT NULL COMMENT '持续天数',
  `quantity` int(11) NOT NULL COMMENT '库存',
  `surplus` int(11) NOT NULL COMMENT '剩余数量',
  `get_limit` int(11) NOT NULL COMMENT '限量',
  `description` text NOT NULL COMMENT '卡券使用须知',
  `usetimes` int(11) NOT NULL COMMENT '使用次数',
  `createtime` varchar(255) NOT NULL COMMENT '创建时间',
  `price` decimal(10,2) DEFAULT NULL COMMENT '收费金额',
  `is_show` int(11) NOT NULL COMMENT '是否列表显示 0显示 1隐藏',
  `vipstatus` int(11) DEFAULT '0',
  `vipprice` decimal(10,2) DEFAULT '0.00',
  `is_indexshow` int(11) NOT NULL DEFAULT '1',
  `indexorder` int(11) NOT NULL,
  `dk` int(11) NOT NULL,
  `pv` int(11) DEFAULT '0',
  `settlementmoney` decimal(10,2) DEFAULT '0.00',
  `onedismoney` decimal(10,2) DEFAULT '0.00',
  `twodismoney` decimal(10,2) DEFAULT '0.00',
  `threedismoney` decimal(10,2) DEFAULT '0.00',
  `isdistri` int(11) DEFAULT '0',
  `vipsettlementmoney` decimal(10,2) NOT NULL,
  `viponedismoney` decimal(10,2) NOT NULL,
  `viptwodismoney` decimal(10,2) NOT NULL,
  `vipthreedismoney` decimal(10,2) NOT NULL,
  `userlabel` text NOT NULL,
  `independent` int(11) NOT NULL,
  `allowapplyre` int(11) NOT NULL,
  `overrefund` tinyint(1) NOT NULL COMMENT '1=开启过期退款',
  `level` text NOT NULL COMMENT '适用会员等级',
  `dissettime` int(11) NOT NULL COMMENT '分销佣金结算时间',
  `nostoreshow` int(11) NOT NULL COMMENT '不在商户详情页显示',
  `extflag` int(11) NOT NULL COMMENT '一般卡券0 外链卡券1',
  `extlink` varchar(500) NOT NULL COMMENT '外部链接',
  `extinfo` text NOT NULL COMMENT '外部信息',
  `salesmid` int(11) NOT NULL COMMENT '业务员mid',
  `name` varchar(145) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_couponlist','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `aid` int(11) DEFAULT NULL COMMENT '代理id'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `status` int(11) NOT NULL COMMENT '优惠券状态 1启用 0禁用2已失效'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `type` int(11) NOT NULL COMMENT '优惠券类型 1 折扣券 2代金券 3礼品券 4 团购券 5优惠券'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','is_charge')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `is_charge` int(11) NOT NULL COMMENT '是否收费 1收费 0免费'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','logo')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `logo` varchar(100) NOT NULL COMMENT '优惠券logo'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','indeximg')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `indeximg` varchar(100) DEFAULT NULL COMMENT '优惠券详情顶部图片'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','merchantid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `merchantid` int(11) NOT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','color')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `color` varchar(100) NOT NULL COMMENT '优惠券颜色'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `title` varchar(145) NOT NULL COMMENT '优惠券标题'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','sub_title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `sub_title` varchar(145) DEFAULT NULL COMMENT '优惠券小标题'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','goodsdetail')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `goodsdetail` text COMMENT '商品详情'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','time_type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `time_type` int(11) NOT NULL COMMENT '时间类型 1.规定时间段 2 领取后限制'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','starttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `starttime` varchar(255) DEFAULT NULL COMMENT '开始时间'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `endtime` varchar(255) DEFAULT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','deadline')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `deadline` int(11) DEFAULT NULL COMMENT '持续天数'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','quantity')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `quantity` int(11) NOT NULL COMMENT '库存'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','surplus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `surplus` int(11) NOT NULL COMMENT '剩余数量'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','get_limit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `get_limit` int(11) NOT NULL COMMENT '限量'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','description')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `description` text NOT NULL COMMENT '卡券使用须知'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','usetimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `usetimes` int(11) NOT NULL COMMENT '使用次数'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `createtime` varchar(255) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `price` decimal(10,2) DEFAULT NULL COMMENT '收费金额'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','is_show')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `is_show` int(11) NOT NULL COMMENT '是否列表显示 0显示 1隐藏'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','vipstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `vipstatus` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','vipprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `vipprice` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','is_indexshow')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `is_indexshow` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','indexorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `indexorder` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','dk')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `dk` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','pv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `pv` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','settlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `settlementmoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','onedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `onedismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','twodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `twodismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','threedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `threedismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','isdistri')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `isdistri` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','vipsettlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `vipsettlementmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','viponedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `viponedismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','viptwodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `viptwodismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','vipthreedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `vipthreedismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','userlabel')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `userlabel` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','independent')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `independent` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','allowapplyre')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `allowapplyre` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','overrefund')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `overrefund` tinyint(1) NOT NULL COMMENT '1=开启过期退款'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','level')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `level` text NOT NULL COMMENT '适用会员等级'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','dissettime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `dissettime` int(11) NOT NULL COMMENT '分销佣金结算时间'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','nostoreshow')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `nostoreshow` int(11) NOT NULL COMMENT '不在商户详情页显示'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','extflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `extflag` int(11) NOT NULL COMMENT '一般卡券0 外链卡券1'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','extlink')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `extlink` varchar(500) NOT NULL COMMENT '外部链接'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','extinfo')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `extinfo` text NOT NULL COMMENT '外部信息'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','salesmid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `salesmid` int(11) NOT NULL COMMENT '业务员mid'");}
if(!pdo_fieldexists('ims_wlmerchant_couponlist','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_couponlist')." ADD   `name` varchar(145) NOT NULL DEFAULT '0'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_creditrecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `openid` varchar(245) NOT NULL,
  `num` varchar(30) NOT NULL,
  `createtime` varchar(145) NOT NULL,
  `transid` varchar(145) NOT NULL,
  `status` int(11) NOT NULL,
  `paytype` int(2) NOT NULL COMMENT '1微信2后台',
  `ordersn` varchar(145) NOT NULL,
  `type` int(2) NOT NULL COMMENT '1积分2余额',
  `remark` varchar(145) NOT NULL,
  `table` tinyint(4) DEFAULT NULL COMMENT '1微擎2tg',
  `uid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=327 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_creditrecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','openid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `openid` varchar(245) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','num')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `num` varchar(30) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `createtime` varchar(145) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','transid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `transid` varchar(145) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `status` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','paytype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `paytype` int(2) NOT NULL COMMENT '1微信2后台'");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','ordersn')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `ordersn` varchar(145) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `type` int(2) NOT NULL COMMENT '1积分2余额'");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `remark` varchar(145) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','table')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `table` tinyint(4) DEFAULT NULL COMMENT '1微擎2tg'");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','uid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `uid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_creditrecord','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_creditrecord')." ADD   `mid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_current` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '明细种类 1商户 2代理 3分销商',
  `type` int(11) NOT NULL COMMENT '订单种类 1抢购 2拼团 3卡券 4一卡通订单 5掌上信息 6付费入驻  7提现或驳回 8分销付费申请 9商户活动 10团购 -1后台修改',
  `objid` int(11) NOT NULL COMMENT '代理 商户 分销商id',
  `fee` decimal(10,2) NOT NULL COMMENT '变更金额',
  `nowmoney` decimal(10,2) NOT NULL COMMENT '变更后金额',
  `createtime` int(11) NOT NULL,
  `orderid` int(11) NOT NULL COMMENT '订单详情',
  `remark` varchar(255) NOT NULL COMMENT '修改备注',
  `aid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8 COMMENT='结算明细表';

");

if(!pdo_fieldexists('ims_wlmerchant_current','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_current')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_current','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_current')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_current','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_current')." ADD   `status` int(11) NOT NULL COMMENT '明细种类 1商户 2代理 3分销商'");}
if(!pdo_fieldexists('ims_wlmerchant_current','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_current')." ADD   `type` int(11) NOT NULL COMMENT '订单种类 1抢购 2拼团 3卡券 4一卡通订单 5掌上信息 6付费入驻  7提现或驳回 8分销付费申请 9商户活动 10团购 -1后台修改'");}
if(!pdo_fieldexists('ims_wlmerchant_current','objid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_current')." ADD   `objid` int(11) NOT NULL COMMENT '代理 商户 分销商id'");}
if(!pdo_fieldexists('ims_wlmerchant_current','fee')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_current')." ADD   `fee` decimal(10,2) NOT NULL COMMENT '变更金额'");}
if(!pdo_fieldexists('ims_wlmerchant_current','nowmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_current')." ADD   `nowmoney` decimal(10,2) NOT NULL COMMENT '变更后金额'");}
if(!pdo_fieldexists('ims_wlmerchant_current','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_current')." ADD   `createtime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_current','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_current')." ADD   `orderid` int(11) NOT NULL COMMENT '订单详情'");}
if(!pdo_fieldexists('ims_wlmerchant_current','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_current')." ADD   `remark` varchar(255) NOT NULL COMMENT '修改备注'");}
if(!pdo_fieldexists('ims_wlmerchant_current','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_current')." ADD   `aid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_disapply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '申请状态 1申请中 2代理审核通过 3平台审核通过 4已结算 5代理驳回 6平台驳回',
  `mid` int(11) DEFAULT NULL COMMENT '申请人MID',
  `disid` int(11) DEFAULT NULL COMMENT '申请人代理商ID',
  `money` decimal(10,2) DEFAULT NULL COMMENT '申请金额',
  `createtime` varchar(145) DEFAULT NULL COMMENT '申请时间',
  `dotime` varchar(145) DEFAULT NULL COMMENT '操作时间',
  `cashstatus` int(11) DEFAULT NULL COMMENT '结算方式 1打款 2手动完成',
  `applymoney` decimal(10,2) DEFAULT '0.00',
  `trade_no` varchar(45) NOT NULL,
  `deletes` tinyint(1) DEFAULT '1' COMMENT '当前数据是否被合并并且删除(1=否2=是)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_disapply','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_disapply','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_disapply','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_disapply','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD   `status` int(11) DEFAULT NULL COMMENT '申请状态 1申请中 2代理审核通过 3平台审核通过 4已结算 5代理驳回 6平台驳回'");}
if(!pdo_fieldexists('ims_wlmerchant_disapply','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD   `mid` int(11) DEFAULT NULL COMMENT '申请人MID'");}
if(!pdo_fieldexists('ims_wlmerchant_disapply','disid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD   `disid` int(11) DEFAULT NULL COMMENT '申请人代理商ID'");}
if(!pdo_fieldexists('ims_wlmerchant_disapply','money')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD   `money` decimal(10,2) DEFAULT NULL COMMENT '申请金额'");}
if(!pdo_fieldexists('ims_wlmerchant_disapply','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD   `createtime` varchar(145) DEFAULT NULL COMMENT '申请时间'");}
if(!pdo_fieldexists('ims_wlmerchant_disapply','dotime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD   `dotime` varchar(145) DEFAULT NULL COMMENT '操作时间'");}
if(!pdo_fieldexists('ims_wlmerchant_disapply','cashstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD   `cashstatus` int(11) DEFAULT NULL COMMENT '结算方式 1打款 2手动完成'");}
if(!pdo_fieldexists('ims_wlmerchant_disapply','applymoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD   `applymoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_disapply','trade_no')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD   `trade_no` varchar(45) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_disapply','deletes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disapply')." ADD   `deletes` tinyint(1) DEFAULT '1' COMMENT '当前数据是否被合并并且删除(1=否2=是)'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_disdetail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `disorderid` int(11) NOT NULL COMMENT '分销订单id',
  `leadid` int(11) NOT NULL COMMENT '分销商mid',
  `buymid` int(11) NOT NULL COMMENT '下级mid',
  `type` int(11) NOT NULL COMMENT '1收入 2支出',
  `price` decimal(10,2) NOT NULL COMMENT '金额',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `plugin` varchar(32) NOT NULL COMMENT '插件类型',
  `rank` int(11) NOT NULL COMMENT '订单层级',
  `reason` varchar(128) NOT NULL COMMENT '原因',
  `nowmoney` decimal(10,2) NOT NULL,
  `checkcode` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_disdetail','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_disdetail','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_disdetail','disorderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD   `disorderid` int(11) NOT NULL COMMENT '分销订单id'");}
if(!pdo_fieldexists('ims_wlmerchant_disdetail','leadid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD   `leadid` int(11) NOT NULL COMMENT '分销商mid'");}
if(!pdo_fieldexists('ims_wlmerchant_disdetail','buymid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD   `buymid` int(11) NOT NULL COMMENT '下级mid'");}
if(!pdo_fieldexists('ims_wlmerchant_disdetail','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD   `type` int(11) NOT NULL COMMENT '1收入 2支出'");}
if(!pdo_fieldexists('ims_wlmerchant_disdetail','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD   `price` decimal(10,2) NOT NULL COMMENT '金额'");}
if(!pdo_fieldexists('ims_wlmerchant_disdetail','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_disdetail','plugin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD   `plugin` varchar(32) NOT NULL COMMENT '插件类型'");}
if(!pdo_fieldexists('ims_wlmerchant_disdetail','rank')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD   `rank` int(11) NOT NULL COMMENT '订单层级'");}
if(!pdo_fieldexists('ims_wlmerchant_disdetail','reason')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD   `reason` varchar(128) NOT NULL COMMENT '原因'");}
if(!pdo_fieldexists('ims_wlmerchant_disdetail','nowmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD   `nowmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_disdetail','checkcode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disdetail')." ADD   `checkcode` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_dislevel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `name` varchar(45) NOT NULL COMMENT '等级名称',
  `onecommission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '一级佣金比例',
  `twocommission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '二级佣金比例',
  `threecommission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '三级佣金比例',
  `upstandard` int(11) NOT NULL COMMENT '升级要求',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `plugin` text NOT NULL COMMENT '适用插件',
  `isdefault` int(11) NOT NULL,
  `ownstatus` int(11) NOT NULL COMMENT '自购返佣',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_dislevel','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_dislevel')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_dislevel','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_dislevel')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_dislevel','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_dislevel')." ADD   `name` varchar(45) NOT NULL COMMENT '等级名称'");}
if(!pdo_fieldexists('ims_wlmerchant_dislevel','onecommission')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_dislevel')." ADD   `onecommission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '一级佣金比例'");}
if(!pdo_fieldexists('ims_wlmerchant_dislevel','twocommission')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_dislevel')." ADD   `twocommission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '二级佣金比例'");}
if(!pdo_fieldexists('ims_wlmerchant_dislevel','threecommission')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_dislevel')." ADD   `threecommission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '三级佣金比例'");}
if(!pdo_fieldexists('ims_wlmerchant_dislevel','upstandard')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_dislevel')." ADD   `upstandard` int(11) NOT NULL COMMENT '升级要求'");}
if(!pdo_fieldexists('ims_wlmerchant_dislevel','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_dislevel')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_dislevel','plugin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_dislevel')." ADD   `plugin` text NOT NULL COMMENT '适用插件'");}
if(!pdo_fieldexists('ims_wlmerchant_dislevel','isdefault')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_dislevel')." ADD   `isdefault` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_dislevel','ownstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_dislevel')." ADD   `ownstatus` int(11) NOT NULL COMMENT '自购返佣'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_disorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '分销结算状态 0不可结算 1可结算 2已结算',
  `plugin` varchar(145) DEFAULT NULL COMMENT '订单所属插件',
  `orderid` int(11) DEFAULT NULL COMMENT '订单id',
  `orderprice` decimal(10,2) DEFAULT NULL COMMENT '订单金额',
  `buymid` int(11) DEFAULT NULL COMMENT '购买人id',
  `oneleadid` int(11) DEFAULT NULL COMMENT '一级分销商id',
  `twoleadid` int(11) DEFAULT NULL COMMENT '二级分销商id',
  `threeleadid` int(11) DEFAULT NULL COMMENT '三级分销商id',
  `leadmoney` text COMMENT '分销提成金额',
  `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间',
  `neworderflag` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_disorder','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `status` int(11) DEFAULT NULL COMMENT '分销结算状态 0不可结算 1可结算 2已结算'");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','plugin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `plugin` varchar(145) DEFAULT NULL COMMENT '订单所属插件'");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `orderid` int(11) DEFAULT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','orderprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `orderprice` decimal(10,2) DEFAULT NULL COMMENT '订单金额'");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','buymid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `buymid` int(11) DEFAULT NULL COMMENT '购买人id'");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','oneleadid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `oneleadid` int(11) DEFAULT NULL COMMENT '一级分销商id'");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','twoleadid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `twoleadid` int(11) DEFAULT NULL COMMENT '二级分销商id'");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','threeleadid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `threeleadid` int(11) DEFAULT NULL COMMENT '三级分销商id'");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','leadmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `leadmoney` text COMMENT '分销提成金额'");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_disorder','neworderflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_disorder')." ADD   `neworderflag` tinyint(1) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_distributor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `mid` int(11) NOT NULL COMMENT '用户表id',
  `disflag` int(11) DEFAULT NULL COMMENT '分销商标识 1分销商 2下线',
  `leadid` int(11) NOT NULL COMMENT '上级id -1为直属',
  `dismoney` decimal(10,2) DEFAULT '0.00' COMMENT '累计佣金',
  `nowmoney` decimal(10,2) DEFAULT '0.00' COMMENT '现有佣金',
  `createtime` varchar(45) DEFAULT NULL COMMENT '创建时间',
  `nickname` varchar(145) DEFAULT NULL COMMENT '昵称',
  `realname` varchar(145) DEFAULT NULL COMMENT '真实姓名',
  `mobile` varchar(100) DEFAULT NULL COMMENT '电话',
  `dislevel` int(11) DEFAULT '0',
  `lockflag` int(11) NOT NULL,
  `expiretime` int(11) NOT NULL,
  `source` int(11) NOT NULL,
  `subnum` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_leadid` (`leadid`),
  KEY `idx_lockflag` (`lockflag`)
) ENGINE=MyISAM AUTO_INCREMENT=174 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_distributor','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `mid` int(11) NOT NULL COMMENT '用户表id'");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','disflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `disflag` int(11) DEFAULT NULL COMMENT '分销商标识 1分销商 2下线'");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','leadid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `leadid` int(11) NOT NULL COMMENT '上级id -1为直属'");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','dismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `dismoney` decimal(10,2) DEFAULT '0.00' COMMENT '累计佣金'");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','nowmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `nowmoney` decimal(10,2) DEFAULT '0.00' COMMENT '现有佣金'");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `createtime` varchar(45) DEFAULT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','nickname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `nickname` varchar(145) DEFAULT NULL COMMENT '昵称'");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','realname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `realname` varchar(145) DEFAULT NULL COMMENT '真实姓名'");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','mobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `mobile` varchar(100) DEFAULT NULL COMMENT '电话'");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','dislevel')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `dislevel` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','lockflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `lockflag` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','expiretime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `expiretime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','source')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `source` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','subnum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   `subnum` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   KEY `idx_uniacid` (`uniacid`)");}
if(!pdo_fieldexists('ims_wlmerchant_distributor','idx_leadid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_distributor')." ADD   KEY `idx_leadid` (`leadid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_diypage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号ID',
  `aid` int(11) NOT NULL DEFAULT '0' COMMENT '所属代理ID',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '页面的类型 1=自定义;2=商城首页;3=会员中心;4=分销中心;5=商品详情页;6=积分商城;7=整点秒杀;8=兑换中心;9=快速购买;99=公用模块',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '页面的名称/页面的标题',
  `data` longtext NOT NULL COMMENT '当前页面的配置数据(base64加密)',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `lastedittime` int(11) NOT NULL DEFAULT '0' COMMENT '最后编辑时间',
  `preview_image` varchar(100) DEFAULT NULL COMMENT '页面预览效果图片',
  `page_class` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=公众号页面   2=小程序页面',
  `diymenu` int(11) NOT NULL DEFAULT '0' COMMENT '使用的菜单id',
  `diyadv` int(11) NOT NULL DEFAULT '0' COMMENT '使用的广告id',
  `is_public` tinyint(1) DEFAULT NULL COMMENT '0=私有页面,1=公共页面',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_type` (`type`),
  KEY `idx_lastedittime` (`lastedittime`),
  KEY `idx_createtime` (`createtime`),
  KEY `idx_aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='页面信息表';

");

if(!pdo_fieldexists('ims_wlmerchant_diypage','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号ID'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   `aid` int(11) NOT NULL DEFAULT '0' COMMENT '所属代理ID'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '页面的类型 1=自定义;2=商城首页;3=会员中心;4=分销中心;5=商品详情页;6=积分商城;7=整点秒杀;8=兑换中心;9=快速购买;99=公用模块'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   `name` varchar(30) NOT NULL DEFAULT '' COMMENT '页面的名称/页面的标题'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','data')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   `data` longtext NOT NULL COMMENT '当前页面的配置数据(base64加密)'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','lastedittime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   `lastedittime` int(11) NOT NULL DEFAULT '0' COMMENT '最后编辑时间'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','preview_image')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   `preview_image` varchar(100) DEFAULT NULL COMMENT '页面预览效果图片'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','page_class')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   `page_class` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=公众号页面   2=小程序页面'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','diymenu')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   `diymenu` int(11) NOT NULL DEFAULT '0' COMMENT '使用的菜单id'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','diyadv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   `diyadv` int(11) NOT NULL DEFAULT '0' COMMENT '使用的广告id'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','is_public')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   `is_public` tinyint(1) DEFAULT NULL COMMENT '0=私有页面,1=公共页面'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   KEY `idx_uniacid` (`uniacid`)");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','idx_type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   KEY `idx_type` (`type`)");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','idx_lastedittime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   KEY `idx_lastedittime` (`lastedittime`)");}
if(!pdo_fieldexists('ims_wlmerchant_diypage','idx_createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage')." ADD   KEY `idx_createtime` (`createtime`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_diypage_adv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号ID',
  `aid` int(11) NOT NULL DEFAULT '0' COMMENT '所属代理ID',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '广告类型',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '广告名称',
  `data` text NOT NULL COMMENT '广告配置信息',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `lastedittime` int(11) NOT NULL DEFAULT '0' COMMENT '最后编辑时间',
  `adv_class` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=公众号页面   2=小程序页面',
  `is_public` tinyint(1) DEFAULT NULL COMMENT '0=私有广告,1=公共广告',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_createtime` (`createtime`),
  KEY `idx_lastedittime` (`lastedittime`),
  KEY `idx_aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='广告信息表';

");

if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号ID'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   `aid` int(11) NOT NULL DEFAULT '0' COMMENT '所属代理ID'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   `type` int(11) NOT NULL DEFAULT '0' COMMENT '广告类型'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   `name` varchar(255) NOT NULL DEFAULT '' COMMENT '广告名称'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','data')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   `data` text NOT NULL COMMENT '广告配置信息'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','lastedittime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   `lastedittime` int(11) NOT NULL DEFAULT '0' COMMENT '最后编辑时间'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','adv_class')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   `adv_class` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=公众号页面   2=小程序页面'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','is_public')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   `is_public` tinyint(1) DEFAULT NULL COMMENT '0=私有广告,1=公共广告'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   KEY `idx_uniacid` (`uniacid`)");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','idx_createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   KEY `idx_createtime` (`createtime`)");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_adv','idx_lastedittime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_adv')." ADD   KEY `idx_lastedittime` (`lastedittime`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_diypage_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号ID',
  `aid` int(11) NOT NULL COMMENT '所属代理ID',
  `name` varchar(125) NOT NULL COMMENT '名称',
  `data` text NOT NULL COMMENT '内容',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `lastedittime` int(11) NOT NULL COMMENT '上次修改时间',
  `menu_class` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=公众号菜单   2=小程序菜单',
  `is_public` tinyint(1) DEFAULT NULL COMMENT '0=私有菜单,1=公共菜单',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='菜单信息表';

");

if(!pdo_fieldexists('ims_wlmerchant_diypage_menu','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_menu')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_menu','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_menu')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号ID'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_menu','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_menu')." ADD   `aid` int(11) NOT NULL COMMENT '所属代理ID'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_menu','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_menu')." ADD   `name` varchar(125) NOT NULL COMMENT '名称'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_menu','data')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_menu')." ADD   `data` text NOT NULL COMMENT '内容'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_menu','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_menu')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_menu','lastedittime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_menu')." ADD   `lastedittime` int(11) NOT NULL COMMENT '上次修改时间'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_menu','menu_class')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_menu')." ADD   `menu_class` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=公众号菜单   2=小程序菜单'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_menu','is_public')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_menu')." ADD   `is_public` tinyint(1) DEFAULT NULL COMMENT '0=私有菜单,1=公共菜单'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_menu','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_menu')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_menu','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_menu')." ADD   KEY `idx_uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_diypage_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号ID',
  `aid` int(11) NOT NULL DEFAULT '0' COMMENT '代理id',
  `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '页面的类型页面的类型 1=自定义;2=商城首页;3=会员中心;4=分销中心;5=商品详情页;6=积分商城;7=整点秒杀;8=兑换中心;9=快速购买;99=公用模块''',
  `name` varchar(25) NOT NULL DEFAULT '' COMMENT '模板名称',
  `data` longtext NOT NULL COMMENT '模板页面配置信息',
  `preview` varchar(100) NOT NULL DEFAULT '' COMMENT '预览图片地址',
  `tplid` int(11) NOT NULL DEFAULT '0',
  `cate` int(11) NOT NULL DEFAULT '0' COMMENT '模板分类id',
  `deleted` tinyint(3) NOT NULL DEFAULT '0',
  `page_class` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=公众号模板   2=小程序模板',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_type` (`type`),
  KEY `idx_cate` (`cate`),
  KEY `idx_aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='模板信息表';

");

if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号ID'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   `aid` int(11) NOT NULL DEFAULT '0' COMMENT '代理id'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '页面的类型页面的类型 1=自定义;2=商城首页;3=会员中心;4=分销中心;5=商品详情页;6=积分商城;7=整点秒杀;8=兑换中心;9=快速购买;99=公用模块'''");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   `name` varchar(25) NOT NULL DEFAULT '' COMMENT '模板名称'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','data')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   `data` longtext NOT NULL COMMENT '模板页面配置信息'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','preview')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   `preview` varchar(100) NOT NULL DEFAULT '' COMMENT '预览图片地址'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','tplid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   `tplid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','cate')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   `cate` int(11) NOT NULL DEFAULT '0' COMMENT '模板分类id'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','deleted')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   `deleted` tinyint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','page_class')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   `page_class` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=公众号模板   2=小程序模板'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   KEY `idx_uniacid` (`uniacid`)");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','idx_type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   KEY `idx_type` (`type`)");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp','idx_cate')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp')." ADD   KEY `idx_cate` (`cate`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_diypage_temp_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `aid` int(11) NOT NULL DEFAULT '0',
  `cate_class` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1=公众号模板分类   2=小程序模板分类',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_aid` (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='模板分类信息表';

");

if(!pdo_fieldexists('ims_wlmerchant_diypage_temp_cate','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp_cate')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp_cate','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp_cate')." ADD   `uniacid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp_cate','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp_cate')." ADD   `name` varchar(255) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp_cate','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp_cate')." ADD   `aid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp_cate','cate_class')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp_cate')." ADD   `cate_class` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1=公众号模板分类   2=小程序模板分类'");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp_cate','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp_cate')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_diypage_temp_cate','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_diypage_temp_cate')." ADD   KEY `idx_uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_express` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL COMMENT '用户id',
  `goodsid` int(11) DEFAULT NULL COMMENT '商品id',
  `merchantid` int(11) DEFAULT NULL COMMENT '商家id',
  `orderid` int(11) DEFAULT NULL COMMENT '订单id',
  `name` varchar(145) DEFAULT NULL COMMENT '收件人姓名',
  `tel` varchar(45) DEFAULT NULL COMMENT '收件人电话',
  `address` text COMMENT '地址',
  `expressprice` decimal(10,2) DEFAULT NULL COMMENT '快递费',
  `expressname` varchar(45) DEFAULT NULL COMMENT '物流名称',
  `expresssn` varchar(45) DEFAULT NULL COMMENT '物流单号',
  `sendtime` varchar(45) DEFAULT NULL COMMENT '发货时间',
  `receivetime` varchar(45) DEFAULT NULL COMMENT '接收时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_express','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_express','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_express','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `mid` int(11) DEFAULT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_wlmerchant_express','goodsid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `goodsid` int(11) DEFAULT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('ims_wlmerchant_express','merchantid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `merchantid` int(11) DEFAULT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('ims_wlmerchant_express','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `orderid` int(11) DEFAULT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('ims_wlmerchant_express','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `name` varchar(145) DEFAULT NULL COMMENT '收件人姓名'");}
if(!pdo_fieldexists('ims_wlmerchant_express','tel')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `tel` varchar(45) DEFAULT NULL COMMENT '收件人电话'");}
if(!pdo_fieldexists('ims_wlmerchant_express','address')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `address` text COMMENT '地址'");}
if(!pdo_fieldexists('ims_wlmerchant_express','expressprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `expressprice` decimal(10,2) DEFAULT NULL COMMENT '快递费'");}
if(!pdo_fieldexists('ims_wlmerchant_express','expressname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `expressname` varchar(45) DEFAULT NULL COMMENT '物流名称'");}
if(!pdo_fieldexists('ims_wlmerchant_express','expresssn')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `expresssn` varchar(45) DEFAULT NULL COMMENT '物流单号'");}
if(!pdo_fieldexists('ims_wlmerchant_express','sendtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `sendtime` varchar(45) DEFAULT NULL COMMENT '发货时间'");}
if(!pdo_fieldexists('ims_wlmerchant_express','receivetime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express')." ADD   `receivetime` varchar(45) DEFAULT NULL COMMENT '接收时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_express_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL COMMENT '模板名称',
  `expressarray` text COMMENT '详细运费',
  `defaultnum` int(11) DEFAULT NULL COMMENT '默认起始件数',
  `defaultmoney` decimal(10,2) DEFAULT NULL COMMENT '默认起始费用',
  `defaultnumex` int(11) DEFAULT NULL COMMENT '默认增加件数',
  `defaultmoneyex` decimal(10,2) DEFAULT NULL COMMENT '默认增加费用',
  `createtime` varchar(45) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_express_template','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express_template')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_express_template','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express_template')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_express_template','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express_template')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_express_template','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express_template')." ADD   `name` varchar(200) DEFAULT NULL COMMENT '模板名称'");}
if(!pdo_fieldexists('ims_wlmerchant_express_template','expressarray')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express_template')." ADD   `expressarray` text COMMENT '详细运费'");}
if(!pdo_fieldexists('ims_wlmerchant_express_template','defaultnum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express_template')." ADD   `defaultnum` int(11) DEFAULT NULL COMMENT '默认起始件数'");}
if(!pdo_fieldexists('ims_wlmerchant_express_template','defaultmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express_template')." ADD   `defaultmoney` decimal(10,2) DEFAULT NULL COMMENT '默认起始费用'");}
if(!pdo_fieldexists('ims_wlmerchant_express_template','defaultnumex')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express_template')." ADD   `defaultnumex` int(11) DEFAULT NULL COMMENT '默认增加件数'");}
if(!pdo_fieldexists('ims_wlmerchant_express_template','defaultmoneyex')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express_template')." ADD   `defaultmoneyex` decimal(10,2) DEFAULT NULL COMMENT '默认增加费用'");}
if(!pdo_fieldexists('ims_wlmerchant_express_template','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_express_template')." ADD   `createtime` varchar(45) DEFAULT NULL COMMENT '创建时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_fabulous` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(10) unsigned NOT NULL COMMENT '点赞用户id',
  `relation_id` int(10) unsigned NOT NULL COMMENT '关联好评表||头条留言表的id',
  `class` tinyint(1) unsigned NOT NULL COMMENT '点赞类别(1=好评点赞,2=头条点赞)',
  `times` varchar(11) DEFAULT NULL COMMENT '点赞时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='头条留言信息表';

");

if(!pdo_fieldexists('ims_wlmerchant_fabulous','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fabulous')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_fabulous','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fabulous')." ADD   `mid` int(10) unsigned NOT NULL COMMENT '点赞用户id'");}
if(!pdo_fieldexists('ims_wlmerchant_fabulous','relation_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fabulous')." ADD   `relation_id` int(10) unsigned NOT NULL COMMENT '关联好评表||头条留言表的id'");}
if(!pdo_fieldexists('ims_wlmerchant_fabulous','class')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fabulous')." ADD   `class` tinyint(1) unsigned NOT NULL COMMENT '点赞类别(1=好评点赞,2=头条点赞)'");}
if(!pdo_fieldexists('ims_wlmerchant_fabulous','times')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fabulous')." ADD   `times` varchar(11) DEFAULT NULL COMMENT '点赞时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_fightgroup_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL COMMENT '分类名称',
  `logo` varchar(255) DEFAULT NULL COMMENT '分类图片',
  `listorder` int(11) DEFAULT '0',
  `is_show` int(11) DEFAULT NULL COMMENT '首页显示 0显示 1隐藏',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=173 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_fightgroup_category','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_category')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_category','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_category')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_category','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_category')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_category','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_category')." ADD   `name` varchar(50) DEFAULT NULL COMMENT '分类名称'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_category','logo')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_category')." ADD   `logo` varchar(255) DEFAULT NULL COMMENT '分类图片'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_category','listorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_category')." ADD   `listorder` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_category','is_show')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_category')." ADD   `is_show` int(11) DEFAULT NULL COMMENT '首页显示 0显示 1隐藏'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_fightgroup_falsemember` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `nickname` varchar(125) DEFAULT NULL,
  `avatar` varchar(445) DEFAULT NULL,
  `createtime` varchar(125) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_fightgroup_falsemember','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_falsemember')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_falsemember','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_falsemember')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_falsemember','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_falsemember')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_falsemember','nickname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_falsemember')." ADD   `nickname` varchar(125) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_falsemember','avatar')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_falsemember')." ADD   `avatar` varchar(445) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_falsemember','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_falsemember')." ADD   `createtime` varchar(125) DEFAULT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_fightgroup_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '状态 0未上架 1上架',
  `merchantid` int(11) DEFAULT NULL COMMENT '商家id',
  `name` varchar(145) DEFAULT NULL COMMENT '商品名称',
  `logo` varchar(145) DEFAULT NULL COMMENT '商品logo图片',
  `detail` text COMMENT '商品详情',
  `price` decimal(10,2) DEFAULT NULL COMMENT '团购价',
  `aloneprice` decimal(10,2) DEFAULT NULL COMMENT '单买价',
  `oldprice` decimal(10,2) DEFAULT NULL COMMENT '市场价',
  `peoplenum` int(11) DEFAULT NULL COMMENT '组团人数',
  `grouptime` decimal(10,2) DEFAULT NULL COMMENT '组团时间（单位小时）',
  `specstatus` int(11) DEFAULT NULL COMMENT '规格类型 0无规格 1同价格规格 2不同价格规格',
  `specdetail` text COMMENT '规格详情',
  `categoryid` int(11) DEFAULT NULL COMMENT '分类id',
  `tag` text COMMENT '商品标签',
  `stock` int(11) DEFAULT NULL COMMENT '库存',
  `realsalenum` int(11) DEFAULT NULL COMMENT '真实销量',
  `falsesalenum` int(11) DEFAULT NULL COMMENT '虚拟销量',
  `listorder` int(11) DEFAULT NULL COMMENT '商品排序',
  `buylimit` int(11) DEFAULT NULL COMMENT '购买限制',
  `unit` varchar(32) DEFAULT NULL COMMENT '单位',
  `adv` text COMMENT '商品幻灯片',
  `share_image` varchar(145) DEFAULT NULL COMMENT '分享图片',
  `share_title` varchar(145) DEFAULT NULL COMMENT '分享标题',
  `share_desc` text COMMENT '分享描述',
  `usestatus` int(11) DEFAULT NULL COMMENT '消费方式 1到店消费 2快递',
  `expressid` int(11) DEFAULT NULL COMMENT '运费模板id',
  `vipdiscount` decimal(10,2) NOT NULL,
  `markid` int(11) DEFAULT '0',
  `islimittime` int(11) DEFAULT '0',
  `limitstarttime` varchar(45) DEFAULT NULL,
  `limitendtime` varchar(45) DEFAULT NULL,
  `settlementmoney` decimal(10,2) DEFAULT '0.00',
  `onedismoney` decimal(10,2) DEFAULT '0.00',
  `twodismoney` decimal(10,2) DEFAULT '0.00',
  `threedismoney` decimal(10,2) DEFAULT '0.00',
  `isdistri` int(11) DEFAULT '0',
  `userlabel` text NOT NULL,
  `independent` int(11) NOT NULL,
  `pv` int(11) NOT NULL,
  `allowapplyre` int(11) NOT NULL,
  `cutoffstatus` int(11) NOT NULL,
  `cutofftime` int(11) NOT NULL,
  `cutoffday` int(11) NOT NULL,
  `overrefund` tinyint(1) NOT NULL COMMENT '1=开启过期退款',
  `dissettime` int(11) NOT NULL COMMENT '分销佣金结算时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `status` int(11) DEFAULT NULL COMMENT '状态 0未上架 1上架'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','merchantid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `merchantid` int(11) DEFAULT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `name` varchar(145) DEFAULT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','logo')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `logo` varchar(145) DEFAULT NULL COMMENT '商品logo图片'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','detail')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `detail` text COMMENT '商品详情'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `price` decimal(10,2) DEFAULT NULL COMMENT '团购价'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','aloneprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `aloneprice` decimal(10,2) DEFAULT NULL COMMENT '单买价'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','oldprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `oldprice` decimal(10,2) DEFAULT NULL COMMENT '市场价'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','peoplenum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `peoplenum` int(11) DEFAULT NULL COMMENT '组团人数'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','grouptime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `grouptime` decimal(10,2) DEFAULT NULL COMMENT '组团时间（单位小时）'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','specstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `specstatus` int(11) DEFAULT NULL COMMENT '规格类型 0无规格 1同价格规格 2不同价格规格'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','specdetail')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `specdetail` text COMMENT '规格详情'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','categoryid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `categoryid` int(11) DEFAULT NULL COMMENT '分类id'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','tag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `tag` text COMMENT '商品标签'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','stock')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `stock` int(11) DEFAULT NULL COMMENT '库存'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','realsalenum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `realsalenum` int(11) DEFAULT NULL COMMENT '真实销量'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','falsesalenum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `falsesalenum` int(11) DEFAULT NULL COMMENT '虚拟销量'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','listorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `listorder` int(11) DEFAULT NULL COMMENT '商品排序'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','buylimit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `buylimit` int(11) DEFAULT NULL COMMENT '购买限制'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','unit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `unit` varchar(32) DEFAULT NULL COMMENT '单位'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','adv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `adv` text COMMENT '商品幻灯片'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','share_image')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `share_image` varchar(145) DEFAULT NULL COMMENT '分享图片'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','share_title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `share_title` varchar(145) DEFAULT NULL COMMENT '分享标题'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','share_desc')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `share_desc` text COMMENT '分享描述'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','usestatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `usestatus` int(11) DEFAULT NULL COMMENT '消费方式 1到店消费 2快递'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','expressid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `expressid` int(11) DEFAULT NULL COMMENT '运费模板id'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','vipdiscount')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `vipdiscount` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','markid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `markid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','islimittime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `islimittime` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','limitstarttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `limitstarttime` varchar(45) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','limitendtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `limitendtime` varchar(45) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','settlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `settlementmoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','onedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `onedismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','twodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `twodismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','threedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `threedismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','isdistri')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `isdistri` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','userlabel')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `userlabel` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','independent')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `independent` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','pv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `pv` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','allowapplyre')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `allowapplyre` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','cutoffstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `cutoffstatus` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','cutofftime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `cutofftime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','cutoffday')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `cutoffday` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','overrefund')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `overrefund` tinyint(1) NOT NULL COMMENT '1=开启过期退款'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_goods','dissettime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_goods')." ADD   `dissettime` int(11) NOT NULL COMMENT '分销佣金结算时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_fightgroup_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '团状态 1组团中 2组团成功 3组团失败',
  `goodsid` int(11) DEFAULT NULL COMMENT '商品id',
  `sid` int(11) DEFAULT NULL COMMENT '商家id',
  `neednum` int(11) DEFAULT NULL COMMENT '需要人数',
  `lacknum` int(11) DEFAULT NULL COMMENT '缺少人数',
  `starttime` varchar(145) DEFAULT NULL COMMENT '开团时间',
  `failtime` varchar(145) DEFAULT NULL COMMENT '时间',
  `successtime` varchar(145) DEFAULT NULL COMMENT '组团成功时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_fightgroup_group','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_group')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_group','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_group')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_group','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_group')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_group','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_group')." ADD   `status` int(11) DEFAULT NULL COMMENT '团状态 1组团中 2组团成功 3组团失败'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_group','goodsid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_group')." ADD   `goodsid` int(11) DEFAULT NULL COMMENT '商品id'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_group','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_group')." ADD   `sid` int(11) DEFAULT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_group','neednum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_group')." ADD   `neednum` int(11) DEFAULT NULL COMMENT '需要人数'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_group','lacknum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_group')." ADD   `lacknum` int(11) DEFAULT NULL COMMENT '缺少人数'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_group','starttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_group')." ADD   `starttime` varchar(145) DEFAULT NULL COMMENT '开团时间'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_group','failtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_group')." ADD   `failtime` varchar(145) DEFAULT NULL COMMENT '时间'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_group','successtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_group')." ADD   `successtime` varchar(145) DEFAULT NULL COMMENT '组团成功时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_fightgroup_userecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `orderid` int(11) DEFAULT NULL,
  `qrcode` varchar(145) DEFAULT NULL COMMENT '核销码',
  `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间',
  `usetimes` int(11) DEFAULT NULL COMMENT '使用次数',
  `usedtime` text COMMENT '核销详情 type 1扫码核销 2后台核销 3商家核销工具核销 ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_fightgroup_userecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_userecord')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_userecord','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_userecord')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_userecord','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_userecord')." ADD   `orderid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_userecord','qrcode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_userecord')." ADD   `qrcode` varchar(145) DEFAULT NULL COMMENT '核销码'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_userecord','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_userecord')." ADD   `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_userecord','usetimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_userecord')." ADD   `usetimes` int(11) DEFAULT NULL COMMENT '使用次数'");}
if(!pdo_fieldexists('ims_wlmerchant_fightgroup_userecord','usedtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_fightgroup_userecord')." ADD   `usedtime` text COMMENT '核销详情 type 1扫码核销 2后台核销 3商家核销工具核销 '");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_goods_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `goodsid` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `thumb` varchar(128) NOT NULL,
  `specs` text NOT NULL,
  `stock` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `vipprice` decimal(10,2) NOT NULL,
  `settlementmoney` decimal(10,2) NOT NULL,
  `vipsettlementmoney` decimal(10,2) NOT NULL,
  `onedismoney` decimal(10,2) NOT NULL,
  `twodismoney` decimal(10,2) NOT NULL,
  `threedismoney` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_goods_option','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `type` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','goodsid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `goodsid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `title` varchar(50) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `thumb` varchar(128) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','specs')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `specs` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','stock')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `stock` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `price` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','vipprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `vipprice` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','settlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `settlementmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','vipsettlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `vipsettlementmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','onedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `onedismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','twodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `twodismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_option','threedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_option')." ADD   `threedismoney` decimal(10,2) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_goods_spec` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `goodsid` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `displaytype` tinyint(3) NOT NULL,
  `content` text NOT NULL,
  `displayorder` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1 抢购商品',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_goods_spec','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec','goodsid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec')." ADD   `goodsid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec')." ADD   `title` varchar(50) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec','description')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec')." ADD   `description` varchar(1000) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec','displaytype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec')." ADD   `displaytype` tinyint(3) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec','content')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec')." ADD   `content` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec')." ADD   `displayorder` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec')." ADD   `type` int(11) NOT NULL COMMENT '1 抢购商品'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_goods_spec_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `specid` int(11) NOT NULL,
  `title` varchar(225) NOT NULL,
  `thumb` varchar(225) NOT NULL,
  `show` int(11) NOT NULL,
  `displayorder` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_goods_spec_item','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec_item')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec_item','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec_item')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec_item','specid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec_item')." ADD   `specid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec_item','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec_item')." ADD   `title` varchar(225) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec_item','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec_item')." ADD   `thumb` varchar(225) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec_item','show')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec_item')." ADD   `show` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goods_spec_item','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goods_spec_item')." ADD   `displayorder` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_goodshouse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL COMMENT '商家id',
  `aid` int(11) DEFAULT NULL COMMENT '代理id',
  `name` varchar(145) DEFAULT NULL COMMENT '活动名称',
  `code` varchar(145) DEFAULT NULL COMMENT '商品编号',
  `describe` varchar(255) DEFAULT NULL COMMENT '描述',
  `detail` text COMMENT '详情',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '抢购价',
  `oldprice` decimal(10,2) DEFAULT '0.00' COMMENT '原价',
  `vipprice` decimal(10,2) DEFAULT '0.00' COMMENT 'vip价格',
  `num` int(11) DEFAULT NULL COMMENT '限量',
  `levelnum` int(11) DEFAULT NULL COMMENT '剩余数量',
  `endtime` varchar(225) DEFAULT NULL COMMENT '活动结束时间',
  `follow` int(11) DEFAULT NULL COMMENT '关注人数',
  `tag` text COMMENT '标签',
  `share_title` varchar(32) DEFAULT NULL,
  `share_image` varchar(250) DEFAULT NULL,
  `share_desc` varchar(32) DEFAULT NULL,
  `unit` varchar(32) DEFAULT NULL COMMENT '单位',
  `thumb` varchar(145) DEFAULT NULL COMMENT '首页图片',
  `thumbs` text COMMENT '图集',
  `salenum` int(11) DEFAULT NULL COMMENT '销量',
  `displayorder` int(11) DEFAULT NULL COMMENT '排序',
  `stock` int(11) DEFAULT NULL COMMENT '库存',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_goodshouse','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `sid` int(11) DEFAULT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `aid` int(11) DEFAULT NULL COMMENT '代理id'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `name` varchar(145) DEFAULT NULL COMMENT '活动名称'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','code')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `code` varchar(145) DEFAULT NULL COMMENT '商品编号'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','describe')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `describe` varchar(255) DEFAULT NULL COMMENT '描述'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','detail')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `detail` text COMMENT '详情'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `price` decimal(10,2) DEFAULT '0.00' COMMENT '抢购价'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','oldprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `oldprice` decimal(10,2) DEFAULT '0.00' COMMENT '原价'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','vipprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `vipprice` decimal(10,2) DEFAULT '0.00' COMMENT 'vip价格'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','num')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `num` int(11) DEFAULT NULL COMMENT '限量'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','levelnum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `levelnum` int(11) DEFAULT NULL COMMENT '剩余数量'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `endtime` varchar(225) DEFAULT NULL COMMENT '活动结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','follow')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `follow` int(11) DEFAULT NULL COMMENT '关注人数'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','tag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `tag` text COMMENT '标签'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','share_title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `share_title` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','share_image')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `share_image` varchar(250) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','share_desc')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `share_desc` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','unit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `unit` varchar(32) DEFAULT NULL COMMENT '单位'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `thumb` varchar(145) DEFAULT NULL COMMENT '首页图片'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','thumbs')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `thumbs` text COMMENT '图集'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','salenum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `salenum` int(11) DEFAULT NULL COMMENT '销量'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `displayorder` int(11) DEFAULT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_wlmerchant_goodshouse','stock')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_goodshouse')." ADD   `stock` int(11) DEFAULT NULL COMMENT '库存'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_groupon_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号id',
  `sid` int(11) DEFAULT NULL COMMENT '商家id',
  `aid` int(11) DEFAULT NULL COMMENT '代理id',
  `name` varchar(145) DEFAULT NULL COMMENT '活动名称【可和仓库的商品名称一致】',
  `code` varchar(145) DEFAULT NULL COMMENT '商品编号',
  `detail` text COMMENT '详情',
  `price` decimal(10,2) DEFAULT NULL COMMENT '抢购价',
  `oldprice` decimal(10,2) DEFAULT NULL COMMENT '原价',
  `vipprice` decimal(10,2) DEFAULT '0.00' COMMENT 'vip价格',
  `num` int(11) DEFAULT NULL COMMENT '限量',
  `levelnum` int(11) DEFAULT NULL COMMENT '剩余数量',
  `status` int(11) DEFAULT '1' COMMENT '1进行中2已结束',
  `starttime` varchar(225) DEFAULT NULL COMMENT '活动开始时间',
  `endtime` varchar(225) DEFAULT NULL COMMENT '活动结束时间',
  `follow` int(11) DEFAULT NULL COMMENT '关注人数',
  `tag` text COMMENT '标签',
  `orderinfo` varchar(255) NOT NULL COMMENT '订单信息',
  `share_title` varchar(32) DEFAULT NULL,
  `share_image` varchar(250) DEFAULT NULL,
  `share_desc` varchar(250) DEFAULT NULL,
  `unit` varchar(32) DEFAULT NULL COMMENT '单位',
  `thumb` varchar(145) DEFAULT NULL COMMENT '首页图片',
  `thumbs` text COMMENT '图集',
  `describe` text,
  `op_one_limit` int(11) DEFAULT NULL COMMENT '单人限购',
  `cutofftime` int(11) NOT NULL,
  `is_indexshow` int(11) NOT NULL DEFAULT '1',
  `allsalenum` int(11) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `cutoffstatus` int(11) NOT NULL COMMENT '截止时间类型',
  `cutoffday` int(11) NOT NULL COMMENT '购买后有效天数',
  `retainage` decimal(10,2) NOT NULL COMMENT '尾款',
  `appointment` int(11) NOT NULL COMMENT '预约小时',
  `integral` int(11) NOT NULL COMMENT '获得积分',
  `pv` int(11) NOT NULL COMMENT '人气',
  `vipstatus` int(11) NOT NULL COMMENT '0无 1会员特价 2会员特供',
  `cateid` int(11) NOT NULL COMMENT '抢购分类ID',
  `specialid` int(11) NOT NULL COMMENT '主题ID',
  `settlementmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '结算金额',
  `onedismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '一级分销',
  `twodismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '二级分销',
  `threedismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '三级分销',
  `isdistri` int(11) NOT NULL COMMENT '是否参与核销 0参与 1不参与',
  `falseorder` text NOT NULL COMMENT '虚拟订单',
  `bgmusic` varchar(255) NOT NULL COMMENT '背景音乐',
  `vipsettlementmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'vip结算金额',
  `viponedismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'vip一级分销',
  `viptwodismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'vip二级分销',
  `vipthreedismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'vip三级分销',
  `optionstatus` int(11) NOT NULL COMMENT '多规格标记',
  `userlabel` text NOT NULL COMMENT '用户标签',
  `listtag` text NOT NULL COMMENT '列表标签',
  `subtitle` varchar(255) NOT NULL,
  `recommend` int(11) NOT NULL,
  `createtime` int(11) NOT NULL,
  `sharemoney` decimal(10,2) NOT NULL,
  `sharestatus` int(11) NOT NULL,
  `independent` int(11) NOT NULL,
  `falsesalenum` int(11) NOT NULL,
  `allowapplyre` int(11) NOT NULL,
  `usestatus` int(11) NOT NULL,
  `expressid` int(11) NOT NULL,
  `fastpay` int(11) NOT NULL,
  `overrefund` tinyint(1) NOT NULL COMMENT '1=开启过期退款',
  `level` text NOT NULL COMMENT '适用会员等级',
  `dissettime` int(11) NOT NULL COMMENT '分销佣金结算时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `uniacid` int(11) DEFAULT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `sid` int(11) DEFAULT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `aid` int(11) DEFAULT NULL COMMENT '代理id'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `name` varchar(145) DEFAULT NULL COMMENT '活动名称【可和仓库的商品名称一致】'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','code')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `code` varchar(145) DEFAULT NULL COMMENT '商品编号'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','detail')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `detail` text COMMENT '详情'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `price` decimal(10,2) DEFAULT NULL COMMENT '抢购价'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','oldprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `oldprice` decimal(10,2) DEFAULT NULL COMMENT '原价'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','vipprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `vipprice` decimal(10,2) DEFAULT '0.00' COMMENT 'vip价格'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','num')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `num` int(11) DEFAULT NULL COMMENT '限量'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','levelnum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `levelnum` int(11) DEFAULT NULL COMMENT '剩余数量'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `status` int(11) DEFAULT '1' COMMENT '1进行中2已结束'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','starttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `starttime` varchar(225) DEFAULT NULL COMMENT '活动开始时间'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `endtime` varchar(225) DEFAULT NULL COMMENT '活动结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','follow')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `follow` int(11) DEFAULT NULL COMMENT '关注人数'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','tag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `tag` text COMMENT '标签'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','orderinfo')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `orderinfo` varchar(255) NOT NULL COMMENT '订单信息'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','share_title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `share_title` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','share_image')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `share_image` varchar(250) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','share_desc')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `share_desc` varchar(250) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','unit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `unit` varchar(32) DEFAULT NULL COMMENT '单位'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `thumb` varchar(145) DEFAULT NULL COMMENT '首页图片'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','thumbs')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `thumbs` text COMMENT '图集'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','describe')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `describe` text");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','op_one_limit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `op_one_limit` int(11) DEFAULT NULL COMMENT '单人限购'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','cutofftime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `cutofftime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','is_indexshow')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `is_indexshow` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','allsalenum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `allsalenum` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `sort` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','cutoffstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `cutoffstatus` int(11) NOT NULL COMMENT '截止时间类型'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','cutoffday')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `cutoffday` int(11) NOT NULL COMMENT '购买后有效天数'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','retainage')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `retainage` decimal(10,2) NOT NULL COMMENT '尾款'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','appointment')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `appointment` int(11) NOT NULL COMMENT '预约小时'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','integral')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `integral` int(11) NOT NULL COMMENT '获得积分'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','pv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `pv` int(11) NOT NULL COMMENT '人气'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','vipstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `vipstatus` int(11) NOT NULL COMMENT '0无 1会员特价 2会员特供'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','cateid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `cateid` int(11) NOT NULL COMMENT '抢购分类ID'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','specialid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `specialid` int(11) NOT NULL COMMENT '主题ID'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','settlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `settlementmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '结算金额'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','onedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `onedismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '一级分销'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','twodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `twodismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '二级分销'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','threedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `threedismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '三级分销'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','isdistri')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `isdistri` int(11) NOT NULL COMMENT '是否参与核销 0参与 1不参与'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','falseorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `falseorder` text NOT NULL COMMENT '虚拟订单'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','bgmusic')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `bgmusic` varchar(255) NOT NULL COMMENT '背景音乐'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','vipsettlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `vipsettlementmoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'vip结算金额'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','viponedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `viponedismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'vip一级分销'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','viptwodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `viptwodismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'vip二级分销'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','vipthreedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `vipthreedismoney` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'vip三级分销'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','optionstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `optionstatus` int(11) NOT NULL COMMENT '多规格标记'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','userlabel')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `userlabel` text NOT NULL COMMENT '用户标签'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','listtag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `listtag` text NOT NULL COMMENT '列表标签'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','subtitle')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `subtitle` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','recommend')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `recommend` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `createtime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','sharemoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `sharemoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','sharestatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `sharestatus` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','independent')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `independent` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','falsesalenum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `falsesalenum` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','allowapplyre')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `allowapplyre` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','usestatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `usestatus` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','expressid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `expressid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','fastpay')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `fastpay` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','overrefund')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `overrefund` tinyint(1) NOT NULL COMMENT '1=开启过期退款'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','level')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `level` text NOT NULL COMMENT '适用会员等级'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_activity','dissettime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_activity')." ADD   `dissettime` int(11) NOT NULL COMMENT '分销佣金结算时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_groupon_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `aid` int(11) NOT NULL DEFAULT '0',
  `thumb` varchar(225) NOT NULL,
  `sort` int(11) NOT NULL,
  `parentid` int(10) NOT NULL,
  `is_show` tinyint(1) DEFAULT '0' COMMENT '首页显示 0显示 1隐藏',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=105 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_groupon_category','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_category')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_category','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_category')." ADD   `uniacid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_category','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_category')." ADD   `name` varchar(255) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_category','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_category')." ADD   `aid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_category','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_category')." ADD   `thumb` varchar(225) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_category','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_category')." ADD   `sort` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_category','parentid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_category')." ADD   `parentid` int(10) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_category','is_show')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_category')." ADD   `is_show` tinyint(1) DEFAULT '0' COMMENT '首页显示 0显示 1隐藏'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_category','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_category')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_category','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_category')." ADD   KEY `idx_uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_groupon_userecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `orderid` int(11) DEFAULT NULL,
  `qrcode` varchar(145) DEFAULT NULL COMMENT '核销码',
  `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间',
  `usetimes` int(11) DEFAULT NULL COMMENT '使用次数',
  `usedtime` text COMMENT '核销详情 type 1扫码核销 2后台核销 3商家核销工具核销 ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_groupon_userecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_userecord')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_userecord','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_userecord')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_userecord','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_userecord')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_userecord','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_userecord')." ADD   `orderid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_userecord','qrcode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_userecord')." ADD   `qrcode` varchar(145) DEFAULT NULL COMMENT '核销码'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_userecord','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_userecord')." ADD   `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_userecord','usetimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_userecord')." ADD   `usetimes` int(11) DEFAULT NULL COMMENT '使用次数'");}
if(!pdo_fieldexists('ims_wlmerchant_groupon_userecord','usedtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_groupon_userecord')." ADD   `usedtime` text COMMENT '核销详情 type 1扫码核销 2后台核销 3商家核销工具核销 '");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_halfcard_qrscan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `cardid` int(11) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `scantime` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uos` (`uniacid`,`openid`,`scantime`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_halfcard_qrscan','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_qrscan')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_qrscan','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_qrscan')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_qrscan','cardid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_qrscan')." ADD   `cardid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_qrscan','openid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_qrscan')." ADD   `openid` varchar(50) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_qrscan','scantime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_qrscan')." ADD   `scantime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_qrscan','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_qrscan')." ADD   `type` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_qrscan','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_qrscan')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_halfcard_realcard` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `cardid` int(11) NOT NULL COMMENT '购卡ID',
  `days` int(11) NOT NULL COMMENT '包含时长',
  `cardsn` varchar(64) NOT NULL,
  `salt` varchar(32) NOT NULL COMMENT '加密盐',
  `status` tinyint(1) unsigned NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  `bindtime` int(10) NOT NULL COMMENT '绑定时间',
  `remark` varchar(50) NOT NULL COMMENT '场景备注',
  `url` varchar(255) NOT NULL,
  `levelid` int(11) NOT NULL,
  `icestatus` int(11) NOT NULL COMMENT '冻结开关',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`) USING BTREE,
  KEY `idx_mid` (`cardid`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=5021 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   `uniacid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','cardid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   `cardid` int(11) NOT NULL COMMENT '购卡ID'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','days')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   `days` int(11) NOT NULL COMMENT '包含时长'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','cardsn')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   `cardsn` varchar(64) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','salt')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   `salt` varchar(32) NOT NULL COMMENT '加密盐'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   `status` tinyint(1) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   `createtime` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','bindtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   `bindtime` int(10) NOT NULL COMMENT '绑定时间'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   `remark` varchar(50) NOT NULL COMMENT '场景备注'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','url')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   `url` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','levelid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   `levelid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','icestatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   `icestatus` int(11) NOT NULL COMMENT '冻结开关'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   KEY `idx_uniacid` (`uniacid`) USING BTREE");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_realcard','idx_mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_realcard')." ADD   KEY `idx_mid` (`cardid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_halfcard_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL COMMENT '充值金额',
  `howlong` varchar(145) DEFAULT NULL COMMENT '充值五折卡月数',
  `paytime` varchar(145) DEFAULT NULL COMMENT '充值时间',
  `orderno` varchar(145) DEFAULT NULL COMMENT '充值单号',
  `limittime` varchar(145) DEFAULT NULL COMMENT '下次到期时期',
  `status` int(11) DEFAULT NULL COMMENT '0未支付 1已经支付',
  `paytype` int(11) DEFAULT NULL,
  `transid` varchar(145) DEFAULT NULL,
  `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间',
  `issettlement` int(11) DEFAULT '0',
  `typeid` int(11) DEFAULT '0',
  `is_vip` int(11) DEFAULT '0',
  `disorderid` int(11) DEFAULT '0',
  `todistributor` int(11) DEFAULT '0',
  `cardid` int(11) DEFAULT '0',
  `username` varchar(32) DEFAULT NULL,
  `paidprid` int(11) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `mototype` varchar(32) NOT NULL COMMENT '车型',
  `platenumber` varchar(32) NOT NULL COMMENT '车牌号',
  PRIMARY KEY (`id`),
  KEY `adx_uniacid` (`uniacid`),
  KEY `adx_aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `mid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `price` decimal(10,2) DEFAULT NULL COMMENT '充值金额'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','howlong')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `howlong` varchar(145) DEFAULT NULL COMMENT '充值五折卡月数'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','paytime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `paytime` varchar(145) DEFAULT NULL COMMENT '充值时间'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','orderno')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `orderno` varchar(145) DEFAULT NULL COMMENT '充值单号'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','limittime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `limittime` varchar(145) DEFAULT NULL COMMENT '下次到期时期'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `status` int(11) DEFAULT NULL COMMENT '0未支付 1已经支付'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','paytype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `paytype` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','transid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `transid` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','issettlement')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `issettlement` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','typeid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `typeid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','is_vip')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `is_vip` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','disorderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `disorderid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','todistributor')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `todistributor` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','cardid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `cardid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','username')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `username` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','paidprid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `paidprid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','mobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `mobile` varchar(20) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','mototype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `mototype` varchar(32) NOT NULL COMMENT '车型'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','platenumber')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   `platenumber` varchar(32) NOT NULL COMMENT '车牌号'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_record','adx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_record')." ADD   KEY `adx_uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_halfcard_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `logo` varchar(145) DEFAULT NULL,
  `name` varchar(145) DEFAULT NULL,
  `days` int(11) DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `status` int(11) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT '0',
  `is_vip` int(11) DEFAULT '0',
  `todistributor` int(11) DEFAULT '0',
  `is_hot` int(11) DEFAULT '0',
  `onedismoney` decimal(10,2) DEFAULT '0.00',
  `twodismoney` decimal(10,2) DEFAULT '0.00',
  `threedismoney` decimal(10,2) DEFAULT '0.00',
  `isdistri` int(11) DEFAULT '0',
  `sort` int(11) NOT NULL,
  `levelid` int(11) NOT NULL,
  `give_price` decimal(10,0) DEFAULT NULL COMMENT '开通一卡通时赠送的金额',
  `detail` text COMMENT '一卡通的详细信息',
  `qrshow` tinyint(1) NOT NULL,
  `renew` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','logo')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `logo` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `name` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','days')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `days` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `price` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `status` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','num')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `num` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `aid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','is_vip')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `is_vip` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','todistributor')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `todistributor` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','is_hot')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `is_hot` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','onedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `onedismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','twodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `twodismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','threedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `threedismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','isdistri')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `isdistri` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `sort` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','levelid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `levelid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','give_price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `give_price` decimal(10,0) DEFAULT NULL COMMENT '开通一卡通时赠送的金额'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','detail')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `detail` text COMMENT '一卡通的详细信息'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','qrshow')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `qrshow` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcard_type','renew')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcard_type')." ADD   `renew` tinyint(1) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_halfcardlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `aid` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL COMMENT '状态 1启用 0禁用',
  `merchantid` int(11) NOT NULL COMMENT '商户id',
  `title` varchar(145) NOT NULL COMMENT '商品标题',
  `datestatus` int(11) NOT NULL COMMENT '时间格式 1 星期 2日期',
  `week` text COMMENT '五折时间 星期',
  `day` text COMMENT '五折时间 天数',
  `adv` text NOT NULL COMMENT '幻灯片',
  `limit` text COMMENT '限制说明',
  `detail` text COMMENT '商品详细说明',
  `describe` text COMMENT '半价卡使用说明',
  `createtime` varchar(100) NOT NULL COMMENT '创建时间',
  `pv` int(11) DEFAULT NULL COMMENT '浏览次数',
  `discount` decimal(10,1) DEFAULT '0.0',
  `daily` int(11) DEFAULT '0',
  `timeslimit` int(11) NOT NULL,
  `usetimes` int(11) DEFAULT '0',
  `activediscount` decimal(10,1) DEFAULT '0.0',
  `sort` int(11) DEFAULT '0',
  `level` text NOT NULL,
  `type` int(11) NOT NULL COMMENT '一般礼包0 外链礼包1',
  `extlink` varchar(500) NOT NULL COMMENT '外部链接',
  `extinfo` text NOT NULL COMMENT '外部信息',
  `starttime` varchar(100) NOT NULL COMMENT '开始时间',
  `endtime` varchar(100) NOT NULL COMMENT '结束时间',
  `timingstatus` int(11) NOT NULL COMMENT '定时参数',
  PRIMARY KEY (`id`),
  KEY `adx_uniacid` (`uniacid`),
  KEY `adx_aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `status` int(11) NOT NULL COMMENT '状态 1启用 0禁用'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','merchantid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `merchantid` int(11) NOT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `title` varchar(145) NOT NULL COMMENT '商品标题'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','datestatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `datestatus` int(11) NOT NULL COMMENT '时间格式 1 星期 2日期'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','week')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `week` text COMMENT '五折时间 星期'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','day')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `day` text COMMENT '五折时间 天数'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','adv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `adv` text NOT NULL COMMENT '幻灯片'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','limit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `limit` text COMMENT '限制说明'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','detail')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `detail` text COMMENT '商品详细说明'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','describe')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `describe` text COMMENT '半价卡使用说明'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `createtime` varchar(100) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','pv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `pv` int(11) DEFAULT NULL COMMENT '浏览次数'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','discount')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `discount` decimal(10,1) DEFAULT '0.0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','daily')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `daily` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','timeslimit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `timeslimit` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','usetimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `usetimes` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','activediscount')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `activediscount` decimal(10,1) DEFAULT '0.0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `sort` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','level')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `level` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `type` int(11) NOT NULL COMMENT '一般礼包0 外链礼包1'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','extlink')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `extlink` varchar(500) NOT NULL COMMENT '外部链接'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','extinfo')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `extinfo` text NOT NULL COMMENT '外部信息'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','starttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `starttime` varchar(100) NOT NULL COMMENT '开始时间'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `endtime` varchar(100) NOT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','timingstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   `timingstatus` int(11) NOT NULL COMMENT '定时参数'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardlist','adx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardlist')." ADD   KEY `adx_uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_halfcardmember` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) DEFAULT NULL COMMENT '代理id',
  `mid` int(11) DEFAULT NULL COMMENT '用户id',
  `expiretime` int(11) DEFAULT NULL COMMENT '五折卡结束时间',
  `createtime` int(11) DEFAULT NULL COMMENT '记录创建时间',
  `disable` int(11) DEFAULT '0',
  `username` varchar(32) DEFAULT NULL,
  `levelid` int(11) NOT NULL,
  `mototype` varchar(32) NOT NULL COMMENT '车型',
  `platenumber` varchar(32) NOT NULL COMMENT '车牌号',
  `from` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `adx_uniacid` (`uniacid`),
  KEY `adx_aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   `aid` int(11) DEFAULT NULL COMMENT '代理id'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   `mid` int(11) DEFAULT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','expiretime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   `expiretime` int(11) DEFAULT NULL COMMENT '五折卡结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   `createtime` int(11) DEFAULT NULL COMMENT '记录创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','disable')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   `disable` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','username')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   `username` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','levelid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   `levelid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','mototype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   `mototype` varchar(32) NOT NULL COMMENT '车型'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','platenumber')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   `platenumber` varchar(32) NOT NULL COMMENT '车牌号'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','from')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   `from` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardmember','adx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardmember')." ADD   KEY `adx_uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_halfcardrecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '状态 1未使用 2已经使用',
  `activeid` int(11) NOT NULL COMMENT '五折活动ID',
  `merchantid` int(11) NOT NULL COMMENT '五折店铺ID',
  `date` varchar(145) NOT NULL COMMENT '优惠日期',
  `qrcode` varchar(145) NOT NULL COMMENT '核销号码',
  `hexiaotime` varchar(45) NOT NULL COMMENT '核销时间',
  `verfmid` int(11) NOT NULL COMMENT '核销人',
  `createtime` varchar(45) NOT NULL COMMENT '创建时间',
  `is_half` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `adx_uniacid` (`uniacid`),
  KEY `adx_aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   `mid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   `status` int(11) NOT NULL COMMENT '状态 1未使用 2已经使用'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','activeid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   `activeid` int(11) NOT NULL COMMENT '五折活动ID'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','merchantid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   `merchantid` int(11) NOT NULL COMMENT '五折店铺ID'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','date')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   `date` varchar(145) NOT NULL COMMENT '优惠日期'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','qrcode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   `qrcode` varchar(145) NOT NULL COMMENT '核销号码'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','hexiaotime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   `hexiaotime` varchar(45) NOT NULL COMMENT '核销时间'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','verfmid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   `verfmid` int(11) NOT NULL COMMENT '核销人'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   `createtime` varchar(45) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','is_half')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   `is_half` int(11) DEFAULT '1'");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_halfcardrecord','adx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halfcardrecord')." ADD   KEY `adx_uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_halflevel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `name` varchar(45) NOT NULL COMMENT '名称',
  `status` int(11) NOT NULL COMMENT '状态',
  `sort` int(11) NOT NULL COMMENT '排序',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `cardimg` varchar(255) NOT NULL,
  `creditmoney` decimal(10,2) NOT NULL,
  `dkcredit` int(11) NOT NULL,
  `dkmoney` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_halflevel','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halflevel')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_halflevel','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halflevel')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halflevel','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halflevel')." ADD   `name` varchar(45) NOT NULL COMMENT '名称'");}
if(!pdo_fieldexists('ims_wlmerchant_halflevel','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halflevel')." ADD   `status` int(11) NOT NULL COMMENT '状态'");}
if(!pdo_fieldexists('ims_wlmerchant_halflevel','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halflevel')." ADD   `sort` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_wlmerchant_halflevel','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halflevel')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_halflevel','cardimg')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halflevel')." ADD   `cardimg` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halflevel','creditmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halflevel')." ADD   `creditmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halflevel','dkcredit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halflevel')." ADD   `dkcredit` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halflevel','dkmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halflevel')." ADD   `dkmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_halflevel','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_halflevel')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_headline_class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `head_id` int(10) unsigned DEFAULT '0' COMMENT '上级分类id，为0时是一级分类',
  `name` varchar(20) NOT NULL COMMENT '分类名称',
  `sort` tinyint(2) unsigned NOT NULL COMMENT '分类排序',
  `state` tinyint(1) DEFAULT '1' COMMENT '分类状态(0=禁用，1=开启)',
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `aid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='头条分类表';

");

if(!pdo_fieldexists('ims_wlmerchant_headline_class','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_class')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_headline_class','head_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_class')." ADD   `head_id` int(10) unsigned DEFAULT '0' COMMENT '上级分类id，为0时是一级分类'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_class','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_class')." ADD   `name` varchar(20) NOT NULL COMMENT '分类名称'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_class','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_class')." ADD   `sort` tinyint(2) unsigned NOT NULL COMMENT '分类排序'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_class','state')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_class')." ADD   `state` tinyint(1) DEFAULT '1' COMMENT '分类状态(0=禁用，1=开启)'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_class','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_class')." ADD   `uniacid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_class','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_class')." ADD   `aid` int(11) DEFAULT '0'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_headline_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hid` int(10) unsigned NOT NULL COMMENT '关联头条信息的id',
  `mid` int(10) unsigned NOT NULL COMMENT '关联用户的id',
  `times` varchar(11) DEFAULT NULL COMMENT '留言时间',
  `text` text NOT NULL COMMENT '留言内容',
  `reply` text NOT NULL COMMENT '作者回复内容',
  `reply_time` varchar(11) NOT NULL COMMENT '作者回复时间',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否被查看（0=未被查看  1=已被查看）',
  `selected` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为精选留言（0=不是精选留言  1=是精选留言）',
  `set_top` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为置顶留言，每条头条只能有一条留言置顶（0=不是置顶留言  1=是置顶留言）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='头条留言信息表';

");

if(!pdo_fieldexists('ims_wlmerchant_headline_comment','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_comment')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_headline_comment','hid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_comment')." ADD   `hid` int(10) unsigned NOT NULL COMMENT '关联头条信息的id'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_comment','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_comment')." ADD   `mid` int(10) unsigned NOT NULL COMMENT '关联用户的id'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_comment','times')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_comment')." ADD   `times` varchar(11) DEFAULT NULL COMMENT '留言时间'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_comment','text')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_comment')." ADD   `text` text NOT NULL COMMENT '留言内容'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_comment','reply')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_comment')." ADD   `reply` text NOT NULL COMMENT '作者回复内容'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_comment','reply_time')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_comment')." ADD   `reply_time` varchar(11) NOT NULL COMMENT '作者回复时间'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_comment','state')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_comment')." ADD   `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否被查看（0=未被查看  1=已被查看）'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_comment','selected')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_comment')." ADD   `selected` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为精选留言（0=不是精选留言  1=是精选留言）'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_comment','set_top')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_comment')." ADD   `set_top` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为置顶留言，每条头条只能有一条留言置顶（0=不是置顶留言  1=是置顶留言）'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_headline_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) unsigned NOT NULL COMMENT '关联店铺id',
  `one_id` int(10) unsigned NOT NULL COMMENT '关联一级分类的id',
  `two_id` int(10) unsigned DEFAULT NULL COMMENT '关联二级分类的id',
  `author` varchar(30) NOT NULL,
  `author_img` varchar(150) NOT NULL COMMENT '作者头像',
  `title` varchar(60) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `browse` int(10) NOT NULL COMMENT '浏览量',
  `display_img` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `release_time` varchar(11) NOT NULL COMMENT '发布时间',
  `call_id` int(11) DEFAULT NULL COMMENT '关联集call活动的id，没有时为不开启集call活动',
  `labels` varchar(100) DEFAULT NULL COMMENT '标签信息',
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号的id',
  `goods_id` int(10) DEFAULT NULL COMMENT '关联商品id',
  `goods_name` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `goods_plugin` varchar(32) DEFAULT NULL COMMENT '商品类型(rush-抢购商品；groupon-团购商品；wlfightgroup-拼团商品；coupon-卡卷商品；bargain-砍价商品)',
  `sid` int(11) DEFAULT NULL COMMENT '商户id',
  `aid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='头条内容表';

");

if(!pdo_fieldexists('ims_wlmerchant_headline_content','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','shop_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `shop_id` int(10) unsigned NOT NULL COMMENT '关联店铺id'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','one_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `one_id` int(10) unsigned NOT NULL COMMENT '关联一级分类的id'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','two_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `two_id` int(10) unsigned DEFAULT NULL COMMENT '关联二级分类的id'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','author')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `author` varchar(30) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','author_img')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `author_img` varchar(150) NOT NULL COMMENT '作者头像'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `title` varchar(60) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','summary')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `summary` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','browse')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `browse` int(10) NOT NULL COMMENT '浏览量'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','display_img')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `display_img` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','content')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `content` longtext NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','release_time')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `release_time` varchar(11) NOT NULL COMMENT '发布时间'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','call_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `call_id` int(11) DEFAULT NULL COMMENT '关联集call活动的id，没有时为不开启集call活动'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','labels')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `labels` varchar(100) DEFAULT NULL COMMENT '标签信息'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `uniacid` int(11) DEFAULT NULL COMMENT '公众号的id'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','goods_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `goods_id` int(10) DEFAULT NULL COMMENT '关联商品id'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','goods_name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `goods_name` varchar(255) DEFAULT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','goods_plugin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `goods_plugin` varchar(32) DEFAULT NULL COMMENT '商品类型(rush-抢购商品；groupon-团购商品；wlfightgroup-拼团商品；coupon-卡卷商品；bargain-砍价商品)'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `sid` int(11) DEFAULT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('ims_wlmerchant_headline_content','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_headline_content')." ADD   `aid` int(11) DEFAULT '0'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_helper_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(145) DEFAULT NULL COMMENT '问题标题',
  `content` text COMMENT '内容',
  `type` int(11) DEFAULT NULL COMMENT '分类',
  `status` int(1) DEFAULT NULL COMMENT '是否显示',
  `recommend` int(1) DEFAULT NULL COMMENT '是否推荐',
  `sort` int(1) DEFAULT NULL,
  `keyword` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_helper_question','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_question')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID'");}
if(!pdo_fieldexists('ims_wlmerchant_helper_question','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_question')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_helper_question','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_question')." ADD   `title` varchar(145) DEFAULT NULL COMMENT '问题标题'");}
if(!pdo_fieldexists('ims_wlmerchant_helper_question','content')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_question')." ADD   `content` text COMMENT '内容'");}
if(!pdo_fieldexists('ims_wlmerchant_helper_question','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_question')." ADD   `type` int(11) DEFAULT NULL COMMENT '分类'");}
if(!pdo_fieldexists('ims_wlmerchant_helper_question','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_question')." ADD   `status` int(1) DEFAULT NULL COMMENT '是否显示'");}
if(!pdo_fieldexists('ims_wlmerchant_helper_question','recommend')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_question')." ADD   `recommend` int(1) DEFAULT NULL COMMENT '是否推荐'");}
if(!pdo_fieldexists('ims_wlmerchant_helper_question','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_question')." ADD   `sort` int(1) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_helper_question','keyword')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_question')." ADD   `keyword` varchar(32) DEFAULT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_helper_slide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `img` varchar(300) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `url` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_helper_slide','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_slide')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_helper_slide','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_slide')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_helper_slide','img')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_slide')." ADD   `img` varchar(300) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_helper_slide','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_slide')." ADD   `title` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_helper_slide','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_slide')." ADD   `status` int(1) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_helper_slide','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_slide')." ADD   `sort` int(11) DEFAULT '0' COMMENT '排序'");}
if(!pdo_fieldexists('ims_wlmerchant_helper_slide','url')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_slide')." ADD   `url` varchar(300) DEFAULT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_helper_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `name` varchar(145) DEFAULT NULL,
  `recommend` int(1) DEFAULT '0',
  `status` int(1) DEFAULT '0',
  `sort` int(11) DEFAULT '0',
  `img` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_helper_type','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_type')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_helper_type','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_type')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_helper_type','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_type')." ADD   `name` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_helper_type','recommend')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_type')." ADD   `recommend` int(1) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_helper_type','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_type')." ADD   `status` int(1) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_helper_type','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_type')." ADD   `sort` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_helper_type','img')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_helper_type')." ADD   `img` varchar(300) DEFAULT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_indexset` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='主页设置：排版；魔方';

");

if(!pdo_fieldexists('ims_wlmerchant_indexset','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_indexset')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_indexset','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_indexset')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_indexset','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_indexset')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_indexset','key')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_indexset')." ADD   `key` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_indexset','value')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_indexset')." ADD   `value` text NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_marking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `getcredit` int(11) DEFAULT NULL COMMENT '获得积分',
  `creditmoney` decimal(10,2) DEFAULT NULL COMMENT '积分抵扣比例',
  `deduct` decimal(10,2) DEFAULT NULL COMMENT '最多抵扣金额',
  `manydeduct` int(11) DEFAULT NULL COMMENT '允许多件抵扣',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_marking','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_marking')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_marking','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_marking')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_marking','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_marking')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_marking','getcredit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_marking')." ADD   `getcredit` int(11) DEFAULT NULL COMMENT '获得积分'");}
if(!pdo_fieldexists('ims_wlmerchant_marking','creditmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_marking')." ADD   `creditmoney` decimal(10,2) DEFAULT NULL COMMENT '积分抵扣比例'");}
if(!pdo_fieldexists('ims_wlmerchant_marking','deduct')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_marking')." ADD   `deduct` decimal(10,2) DEFAULT NULL COMMENT '最多抵扣金额'");}
if(!pdo_fieldexists('ims_wlmerchant_marking','manydeduct')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_marking')." ADD   `manydeduct` int(11) DEFAULT NULL COMMENT '允许多件抵扣'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '会员ID',
  `uid` int(11) NOT NULL COMMENT '微擎会员id',
  `invid` int(11) NOT NULL COMMENT '邀请人id',
  `uniacid` int(11) NOT NULL COMMENT '公众号ID',
  `openid` varchar(100) NOT NULL,
  `unionid` varchar(100) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `realname` varchar(50) NOT NULL,
  `credit1` decimal(10,2) NOT NULL COMMENT '积分',
  `credit2` decimal(10,2) NOT NULL COMMENT '余额',
  `gender` int(11) NOT NULL,
  `isvip` int(11) NOT NULL DEFAULT '1' COMMENT '会员类型1普通2VIP',
  `vipendtime` int(11) NOT NULL COMMENT '会员到期时间',
  `avatar` varchar(445) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `areaid` int(11) DEFAULT NULL COMMENT '地区ID',
  `aid` int(11) DEFAULT NULL COMMENT '所属代理ID',
  `level` int(11) DEFAULT '0' COMMENT '1：VIP1',
  `dealnum` int(11) DEFAULT '0' COMMENT '成交量',
  `dealmoney` decimal(10,2) DEFAULT '0.00' COMMENT '成交额',
  `vipstatus` int(11) DEFAULT NULL COMMENT 'VIP状态',
  `lastviptime` varchar(145) DEFAULT '0' COMMENT '上次VIP应该结束时间',
  `vipleveldays` int(11) DEFAULT '0' COMMENT '会员持续天数，每天更新',
  `distributorid` int(11) DEFAULT '0',
  `salt` varchar(32) DEFAULT '0',
  `registerflag` int(11) DEFAULT '0',
  `password` varchar(32) DEFAULT '0',
  `dotime` int(11) NOT NULL,
  `sharemoney` decimal(10,2) NOT NULL,
  `sharenowmoney` decimal(10,2) NOT NULL,
  `wechat_openid` varchar(100) NOT NULL COMMENT '储存用户小程序的openid',
  `webapp_openid` varchar(100) DEFAULT NULL COMMENT '储存用户的webappopenid',
  `bank_name` varchar(50) DEFAULT NULL COMMENT '用户银行卡开户行',
  `card_number` varchar(20) DEFAULT NULL COMMENT '用户的银行卡账号',
  `alipay` varchar(20) DEFAULT NULL COMMENT '用户的支付宝账号',
  `bank_username` varchar(20) DEFAULT NULL COMMENT '代理商银行卡开户人的姓名',
  `blackflag` tinyint(1) NOT NULL COMMENT '1=被加入黑名单',
  `tokey` varchar(32) DEFAULT '0' COMMENT '用户token',
  `wechat_number` varchar(50) DEFAULT NULL COMMENT '用户微信号',
  `wechat_qrcode` varchar(255) DEFAULT NULL COMMENT '用户微信号二维码图片地址',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_aid` (`aid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_unionid` (`unionid`),
  KEY `idx_createtime` (`createtime`)
) ENGINE=MyISAM AUTO_INCREMENT=5923 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_member','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '会员ID'");}
if(!pdo_fieldexists('ims_wlmerchant_member','uid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `uid` int(11) NOT NULL COMMENT '微擎会员id'");}
if(!pdo_fieldexists('ims_wlmerchant_member','invid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `invid` int(11) NOT NULL COMMENT '邀请人id'");}
if(!pdo_fieldexists('ims_wlmerchant_member','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号ID'");}
if(!pdo_fieldexists('ims_wlmerchant_member','openid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `openid` varchar(100) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member','unionid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `unionid` varchar(100) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member','nickname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `nickname` varchar(50) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member','realname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `realname` varchar(50) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member','credit1')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `credit1` decimal(10,2) NOT NULL COMMENT '积分'");}
if(!pdo_fieldexists('ims_wlmerchant_member','credit2')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `credit2` decimal(10,2) NOT NULL COMMENT '余额'");}
if(!pdo_fieldexists('ims_wlmerchant_member','gender')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `gender` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member','isvip')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `isvip` int(11) NOT NULL DEFAULT '1' COMMENT '会员类型1普通2VIP'");}
if(!pdo_fieldexists('ims_wlmerchant_member','vipendtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `vipendtime` int(11) NOT NULL COMMENT '会员到期时间'");}
if(!pdo_fieldexists('ims_wlmerchant_member','avatar')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `avatar` varchar(445) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member','mobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `mobile` varchar(20) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_member','areaid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `areaid` int(11) DEFAULT NULL COMMENT '地区ID'");}
if(!pdo_fieldexists('ims_wlmerchant_member','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `aid` int(11) DEFAULT NULL COMMENT '所属代理ID'");}
if(!pdo_fieldexists('ims_wlmerchant_member','level')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `level` int(11) DEFAULT '0' COMMENT '1：VIP1'");}
if(!pdo_fieldexists('ims_wlmerchant_member','dealnum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `dealnum` int(11) DEFAULT '0' COMMENT '成交量'");}
if(!pdo_fieldexists('ims_wlmerchant_member','dealmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `dealmoney` decimal(10,2) DEFAULT '0.00' COMMENT '成交额'");}
if(!pdo_fieldexists('ims_wlmerchant_member','vipstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `vipstatus` int(11) DEFAULT NULL COMMENT 'VIP状态'");}
if(!pdo_fieldexists('ims_wlmerchant_member','lastviptime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `lastviptime` varchar(145) DEFAULT '0' COMMENT '上次VIP应该结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_member','vipleveldays')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `vipleveldays` int(11) DEFAULT '0' COMMENT '会员持续天数，每天更新'");}
if(!pdo_fieldexists('ims_wlmerchant_member','distributorid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `distributorid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_member','salt')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `salt` varchar(32) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_member','registerflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `registerflag` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_member','password')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `password` varchar(32) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_member','dotime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `dotime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member','sharemoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `sharemoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member','sharenowmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `sharenowmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member','wechat_openid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `wechat_openid` varchar(100) NOT NULL COMMENT '储存用户小程序的openid'");}
if(!pdo_fieldexists('ims_wlmerchant_member','webapp_openid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `webapp_openid` varchar(100) DEFAULT NULL COMMENT '储存用户的webappopenid'");}
if(!pdo_fieldexists('ims_wlmerchant_member','bank_name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `bank_name` varchar(50) DEFAULT NULL COMMENT '用户银行卡开户行'");}
if(!pdo_fieldexists('ims_wlmerchant_member','card_number')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `card_number` varchar(20) DEFAULT NULL COMMENT '用户的银行卡账号'");}
if(!pdo_fieldexists('ims_wlmerchant_member','alipay')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `alipay` varchar(20) DEFAULT NULL COMMENT '用户的支付宝账号'");}
if(!pdo_fieldexists('ims_wlmerchant_member','bank_username')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `bank_username` varchar(20) DEFAULT NULL COMMENT '代理商银行卡开户人的姓名'");}
if(!pdo_fieldexists('ims_wlmerchant_member','blackflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `blackflag` tinyint(1) NOT NULL COMMENT '1=被加入黑名单'");}
if(!pdo_fieldexists('ims_wlmerchant_member','tokey')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `tokey` varchar(32) DEFAULT '0' COMMENT '用户token'");}
if(!pdo_fieldexists('ims_wlmerchant_member','wechat_number')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `wechat_number` varchar(50) DEFAULT NULL COMMENT '用户微信号'");}
if(!pdo_fieldexists('ims_wlmerchant_member','wechat_qrcode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   `wechat_qrcode` varchar(255) DEFAULT NULL COMMENT '用户微信号二维码图片地址'");}
if(!pdo_fieldexists('ims_wlmerchant_member','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_member','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   KEY `idx_uniacid` (`uniacid`)");}
if(!pdo_fieldexists('ims_wlmerchant_member','idx_aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   KEY `idx_aid` (`aid`)");}
if(!pdo_fieldexists('ims_wlmerchant_member','idx_openid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   KEY `idx_openid` (`openid`)");}
if(!pdo_fieldexists('ims_wlmerchant_member','idx_unionid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member')." ADD   KEY `idx_unionid` (`unionid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_member_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `mid` int(11) NOT NULL COMMENT '用户id',
  `aid` int(11) DEFAULT NULL COMMENT '代理ID',
  `parentid` int(11) NOT NULL COMMENT '父类优惠券id',
  `status` int(11) NOT NULL COMMENT '卡券状态 1未使用 2已使用 5未支付',
  `type` int(11) NOT NULL COMMENT '优惠券类型 1 折扣券 2代金券 3礼品券 4 团购券 5优惠券',
  `title` varchar(145) DEFAULT NULL COMMENT '优惠券标题',
  `sub_title` varchar(145) DEFAULT NULL COMMENT '优惠券副标题',
  `content` text NOT NULL COMMENT '优惠券内容',
  `description` text NOT NULL COMMENT '使用须知',
  `color` varchar(32) DEFAULT NULL COMMENT '颜色',
  `usetimes` int(11) DEFAULT NULL COMMENT '剩余使用次数',
  `starttime` int(11) NOT NULL COMMENT '开始时间',
  `endtime` int(11) NOT NULL COMMENT '结束时间',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `usedtime` text COMMENT '使用时间',
  `orderno` varchar(145) DEFAULT NULL COMMENT '订单号',
  `price` decimal(10,2) DEFAULT NULL COMMENT '支付金额',
  `concode` varchar(32) DEFAULT NULL COMMENT '消费码',
  `cutoffnotice` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=204 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_member_coupons','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `mid` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `aid` int(11) DEFAULT NULL COMMENT '代理ID'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','parentid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `parentid` int(11) NOT NULL COMMENT '父类优惠券id'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `status` int(11) NOT NULL COMMENT '卡券状态 1未使用 2已使用 5未支付'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `type` int(11) NOT NULL COMMENT '优惠券类型 1 折扣券 2代金券 3礼品券 4 团购券 5优惠券'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `title` varchar(145) DEFAULT NULL COMMENT '优惠券标题'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','sub_title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `sub_title` varchar(145) DEFAULT NULL COMMENT '优惠券副标题'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','content')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `content` text NOT NULL COMMENT '优惠券内容'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','description')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `description` text NOT NULL COMMENT '使用须知'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','color')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `color` varchar(32) DEFAULT NULL COMMENT '颜色'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','usetimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `usetimes` int(11) DEFAULT NULL COMMENT '剩余使用次数'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','starttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `starttime` int(11) NOT NULL COMMENT '开始时间'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `endtime` int(11) NOT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','usedtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `usedtime` text COMMENT '使用时间'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','orderno')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `orderno` varchar(145) DEFAULT NULL COMMENT '订单号'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `price` decimal(10,2) DEFAULT NULL COMMENT '支付金额'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','concode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `concode` varchar(32) DEFAULT NULL COMMENT '消费码'");}
if(!pdo_fieldexists('ims_wlmerchant_member_coupons','cutoffnotice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_coupons')." ADD   `cutoffnotice` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_member_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logo` varchar(145) DEFAULT NULL,
  `name` varchar(145) DEFAULT NULL,
  `days` int(11) DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `uniacid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '1显示',
  `num` int(11) DEFAULT NULL COMMENT '可开通次数',
  `is_half` int(11) DEFAULT '0',
  `todistributor` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_member_type','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_type')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_member_type','logo')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_type')." ADD   `logo` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member_type','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_type')." ADD   `name` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member_type','days')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_type')." ADD   `days` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_member_type','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_type')." ADD   `price` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_member_type','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_type')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_member_type','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_type')." ADD   `status` int(11) DEFAULT NULL COMMENT '1显示'");}
if(!pdo_fieldexists('ims_wlmerchant_member_type','num')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_type')." ADD   `num` int(11) DEFAULT NULL COMMENT '可开通次数'");}
if(!pdo_fieldexists('ims_wlmerchant_member_type','is_half')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_type')." ADD   `is_half` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_member_type','todistributor')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_member_type')." ADD   `todistributor` int(11) DEFAULT '0'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_merchant_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `sid` int(11) NOT NULL COMMENT '商家ID',
  `uid` int(11) NOT NULL COMMENT '操作员id',
  `amount` decimal(10,2) NOT NULL COMMENT '交易总金额',
  `updatetime` varchar(45) NOT NULL COMMENT '上次结算时间',
  `no_money` decimal(10,2) NOT NULL COMMENT '目前未结算金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_merchant_account','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_account')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_account','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_account')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_account','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_account')." ADD   `sid` int(11) NOT NULL COMMENT '商家ID'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_account','uid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_account')." ADD   `uid` int(11) NOT NULL COMMENT '操作员id'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_account','amount')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_account')." ADD   `amount` decimal(10,2) NOT NULL COMMENT '交易总金额'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_account','updatetime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_account')." ADD   `updatetime` varchar(45) NOT NULL COMMENT '上次结算时间'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_account','no_money')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_account')." ADD   `no_money` decimal(10,2) NOT NULL COMMENT '目前未结算金额'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_merchant_money_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL COMMENT '商家ID',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '变动金额',
  `createtime` varchar(145) DEFAULT NULL COMMENT '变动时间',
  `orderid` int(11) DEFAULT NULL COMMENT '订单ID',
  `type` int(11) DEFAULT NULL COMMENT '1支付成功2发货成功成为可结算金额3取消发货4商家结算5退款',
  `detail` text COMMENT '详情',
  `plugin` varchar(32) DEFAULT NULL COMMENT '插件名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COMMENT='商家金额记录';

");

if(!pdo_fieldexists('ims_wlmerchant_merchant_money_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_money_record')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_money_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_money_record')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_money_record','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_money_record')." ADD   `sid` int(11) DEFAULT NULL COMMENT '商家ID'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_money_record','money')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_money_record')." ADD   `money` decimal(10,2) DEFAULT '0.00' COMMENT '变动金额'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_money_record','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_money_record')." ADD   `createtime` varchar(145) DEFAULT NULL COMMENT '变动时间'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_money_record','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_money_record')." ADD   `orderid` int(11) DEFAULT NULL COMMENT '订单ID'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_money_record','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_money_record')." ADD   `type` int(11) DEFAULT NULL COMMENT '1支付成功2发货成功成为可结算金额3取消发货4商家结算5退款'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_money_record','detail')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_money_record')." ADD   `detail` text COMMENT '详情'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_money_record','plugin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_money_record')." ADD   `plugin` varchar(32) DEFAULT NULL COMMENT '插件名'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_merchant_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `sid` int(11) NOT NULL COMMENT '商家id',
  `percent` varchar(32) NOT NULL COMMENT '佣金百分比',
  `commission` varchar(32) NOT NULL COMMENT '佣金',
  `money` varchar(45) NOT NULL COMMENT '本次结算金额',
  `get_money` varchar(32) DEFAULT NULL COMMENT '本次商家得到金额',
  `uid` int(11) NOT NULL COMMENT '操作员id',
  `createtime` varchar(45) NOT NULL COMMENT '结算时间',
  `orderno` varchar(145) NOT NULL COMMENT '订单号',
  `plugin` varchar(32) DEFAULT NULL COMMENT '插件名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_merchant_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_record')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_record')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_record','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_record')." ADD   `sid` int(11) NOT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_record','percent')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_record')." ADD   `percent` varchar(32) NOT NULL COMMENT '佣金百分比'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_record','commission')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_record')." ADD   `commission` varchar(32) NOT NULL COMMENT '佣金'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_record','money')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_record')." ADD   `money` varchar(45) NOT NULL COMMENT '本次结算金额'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_record','get_money')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_record')." ADD   `get_money` varchar(32) DEFAULT NULL COMMENT '本次商家得到金额'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_record','uid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_record')." ADD   `uid` int(11) NOT NULL COMMENT '操作员id'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_record','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_record')." ADD   `createtime` varchar(45) NOT NULL COMMENT '结算时间'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_record','orderno')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_record')." ADD   `orderno` varchar(145) NOT NULL COMMENT '订单号'");}
if(!pdo_fieldexists('ims_wlmerchant_merchant_record','plugin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchant_record')." ADD   `plugin` varchar(32) DEFAULT NULL COMMENT '插件名'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_merchantdata` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) DEFAULT NULL COMMENT '代理id',
  `provinceid` int(11) DEFAULT NULL COMMENT '省ID',
  `areaid` int(11) NOT NULL COMMENT '地区id',
  `distid` int(11) DEFAULT NULL COMMENT '区县id',
  `storename` varchar(64) NOT NULL COMMENT '店铺名称',
  `mobile` varchar(32) DEFAULT NULL COMMENT '联系电话',
  `onelevel` int(11) NOT NULL COMMENT '一级分类',
  `twolevel` int(11) NOT NULL COMMENT '二级分类',
  `logo` varchar(128) DEFAULT NULL COMMENT '店铺logo',
  `introduction` text COMMENT '店铺简介',
  `address` varchar(100) DEFAULT NULL COMMENT '店铺地址',
  `location` varchar(128) NOT NULL,
  `realname` varchar(32) DEFAULT NULL COMMENT '联系人',
  `tel` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `enabled` int(2) DEFAULT NULL COMMENT '商户状态',
  `status` int(2) DEFAULT NULL COMMENT '是否审核通过',
  `groupid` int(11) DEFAULT NULL COMMENT '所属组别',
  `storehours` varchar(100) DEFAULT NULL COMMENT '营业时间',
  `endtime` int(11) DEFAULT NULL COMMENT '结束时间',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `remark` text COMMENT '备注',
  `percent` decimal(10,2) DEFAULT '0.00',
  `cardsn` varchar(50) DEFAULT NULL,
  `adv` text,
  `score` int(11) DEFAULT '5',
  `bankrid` int(11) DEFAULT '0',
  `listorder` int(11) DEFAULT '0',
  `verkey` varchar(45) DEFAULT '0',
  `autocash` int(11) DEFAULT '0',
  `audits` int(11) DEFAULT '0',
  `pv` int(11) DEFAULT '0',
  `tag` varchar(145) DEFAULT '0',
  `allmoney` decimal(10,2) DEFAULT '0.00',
  `nowmoney` decimal(10,2) DEFAULT '0.00',
  `panorama` varchar(255) NOT NULL,
  `videourl` varchar(255) NOT NULL,
  `merqrimg` varchar(128) NOT NULL,
  `wxappswitch` int(11) DEFAULT '0',
  `album` text NOT NULL,
  `bgmusic` varchar(255) NOT NULL,
  `iscommon` tinyint(1) NOT NULL,
  `settlementrate` decimal(10,2) NOT NULL,
  `vipsettlementrate` decimal(10,2) NOT NULL,
  `settlementtext` text NOT NULL,
  `payonline` int(11) NOT NULL,
  `qrcode` varchar(128) NOT NULL,
  `mp4thumb` varchar(128) NOT NULL,
  `cloudspeaker` text COMMENT '云喇叭设置信息',
  `externallink` varchar(255) NOT NULL,
  `listshow` tinyint(1) DEFAULT NULL COMMENT '该店铺是否在店铺列表显示0=显示1=隐藏',
  `store_quhao` varchar(20) NOT NULL COMMENT '店铺电话区号',
  `note_quhao` varchar(20) NOT NULL COMMENT '店长电话区号',
  `salesmid` int(11) NOT NULL COMMENT '业务员mid',
  `examineimg` text NOT NULL COMMENT '审核材料',
  `reservestatus` int(11) NOT NULL COMMENT '预留金额类型',
  `reservemoney` decimal(10,2) NOT NULL COMMENT '商户预留金额',
  `autostoreqr` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key_uniacid` (`uniacid`),
  KEY `key_areaid` (`areaid`),
  KEY `key_location` (`location`)
) ENGINE=MyISAM AUTO_INCREMENT=94 DEFAULT CHARSET=utf8 COMMENT='商户资料表';

");

if(!pdo_fieldexists('ims_wlmerchant_merchantdata','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `aid` int(11) DEFAULT NULL COMMENT '代理id'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','provinceid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `provinceid` int(11) DEFAULT NULL COMMENT '省ID'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','areaid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `areaid` int(11) NOT NULL COMMENT '地区id'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','distid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `distid` int(11) DEFAULT NULL COMMENT '区县id'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','storename')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `storename` varchar(64) NOT NULL COMMENT '店铺名称'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','mobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `mobile` varchar(32) DEFAULT NULL COMMENT '联系电话'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','onelevel')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `onelevel` int(11) NOT NULL COMMENT '一级分类'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','twolevel')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `twolevel` int(11) NOT NULL COMMENT '二级分类'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','logo')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `logo` varchar(128) DEFAULT NULL COMMENT '店铺logo'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','introduction')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `introduction` text COMMENT '店铺简介'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','address')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `address` varchar(100) DEFAULT NULL COMMENT '店铺地址'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','location')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `location` varchar(128) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','realname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `realname` varchar(32) DEFAULT NULL COMMENT '联系人'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','tel')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `tel` varchar(20) DEFAULT NULL COMMENT '联系电话'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','enabled')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `enabled` int(2) DEFAULT NULL COMMENT '商户状态'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `status` int(2) DEFAULT NULL COMMENT '是否审核通过'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','groupid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `groupid` int(11) DEFAULT NULL COMMENT '所属组别'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','storehours')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `storehours` varchar(100) DEFAULT NULL COMMENT '营业时间'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `endtime` int(11) DEFAULT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `remark` text COMMENT '备注'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','percent')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `percent` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','cardsn')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `cardsn` varchar(50) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','adv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `adv` text");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','score')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `score` int(11) DEFAULT '5'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','bankrid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `bankrid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','listorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `listorder` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','verkey')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `verkey` varchar(45) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','autocash')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `autocash` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','audits')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `audits` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','pv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `pv` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','tag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `tag` varchar(145) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','allmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `allmoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','nowmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `nowmoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','panorama')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `panorama` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','videourl')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `videourl` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','merqrimg')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `merqrimg` varchar(128) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','wxappswitch')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `wxappswitch` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','album')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `album` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','bgmusic')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `bgmusic` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','iscommon')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `iscommon` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','settlementrate')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `settlementrate` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','vipsettlementrate')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `vipsettlementrate` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','settlementtext')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `settlementtext` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','payonline')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `payonline` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','qrcode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `qrcode` varchar(128) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','mp4thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `mp4thumb` varchar(128) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','cloudspeaker')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `cloudspeaker` text COMMENT '云喇叭设置信息'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','externallink')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `externallink` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','listshow')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `listshow` tinyint(1) DEFAULT NULL COMMENT '该店铺是否在店铺列表显示0=显示1=隐藏'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','store_quhao')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `store_quhao` varchar(20) NOT NULL COMMENT '店铺电话区号'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','note_quhao')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `note_quhao` varchar(20) NOT NULL COMMENT '店长电话区号'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','salesmid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `salesmid` int(11) NOT NULL COMMENT '业务员mid'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','examineimg')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `examineimg` text NOT NULL COMMENT '审核材料'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','reservestatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `reservestatus` int(11) NOT NULL COMMENT '预留金额类型'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','reservemoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `reservemoney` decimal(10,2) NOT NULL COMMENT '商户预留金额'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','autostoreqr')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   `autostoreqr` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','key_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   KEY `key_uniacid` (`uniacid`)");}
if(!pdo_fieldexists('ims_wlmerchant_merchantdata','key_areaid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantdata')." ADD   KEY `key_areaid` (`areaid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_merchantuser` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `mid` int(11) NOT NULL COMMENT '绑定微信id',
  `storeid` int(11) NOT NULL COMMENT '商户id',
  `name` varchar(64) NOT NULL COMMENT '姓名',
  `mobile` varchar(32) NOT NULL COMMENT '电话',
  `account` varchar(32) DEFAULT NULL COMMENT '账号',
  `salt` varchar(16) DEFAULT NULL COMMENT '加密盐',
  `password` varchar(64) DEFAULT NULL COMMENT '密码',
  `groupid` int(11) DEFAULT NULL COMMENT '所属组别',
  `areaid` varchar(16) NOT NULL COMMENT '区域id',
  `endtime` varchar(32) DEFAULT NULL COMMENT '到期时间',
  `createtime` varchar(32) NOT NULL COMMENT '创建时间',
  `limit` text NOT NULL COMMENT '拥有权限',
  `reject` varchar(300) DEFAULT NULL COMMENT '驳回原因',
  `status` int(2) NOT NULL COMMENT '是否通过审核',
  `enabled` int(2) NOT NULL COMMENT '是否启用',
  `ismain` int(2) DEFAULT NULL COMMENT '1超级管理员2核销员',
  `aid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COMMENT='代理、商户表';

");

if(!pdo_fieldexists('ims_wlmerchant_merchantuser','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `mid` int(11) NOT NULL COMMENT '绑定微信id'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','storeid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `storeid` int(11) NOT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `name` varchar(64) NOT NULL COMMENT '姓名'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','mobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `mobile` varchar(32) NOT NULL COMMENT '电话'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','account')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `account` varchar(32) DEFAULT NULL COMMENT '账号'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','salt')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `salt` varchar(16) DEFAULT NULL COMMENT '加密盐'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','password')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `password` varchar(64) DEFAULT NULL COMMENT '密码'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','groupid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `groupid` int(11) DEFAULT NULL COMMENT '所属组别'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','areaid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `areaid` varchar(16) NOT NULL COMMENT '区域id'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `endtime` varchar(32) DEFAULT NULL COMMENT '到期时间'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `createtime` varchar(32) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','limit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `limit` text NOT NULL COMMENT '拥有权限'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','reject')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `reject` varchar(300) DEFAULT NULL COMMENT '驳回原因'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `status` int(2) NOT NULL COMMENT '是否通过审核'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','enabled')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `enabled` int(2) NOT NULL COMMENT '是否启用'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','ismain')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `ismain` int(2) DEFAULT NULL COMMENT '1超级管理员2核销员'");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser')." ADD   `aid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_merchantuser_qrlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `memberid` int(11) NOT NULL,
  `codes` int(11) DEFAULT NULL,
  `status` int(1) NOT NULL,
  `createtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_merchantuser_qrlog','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser_qrlog')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser_qrlog','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser_qrlog')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser_qrlog','memberid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser_qrlog')." ADD   `memberid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser_qrlog','codes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser_qrlog')." ADD   `codes` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser_qrlog','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser_qrlog')." ADD   `status` int(1) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_merchantuser_qrlog','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_merchantuser_qrlog')." ADD   `createtime` int(11) DEFAULT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '唯一标识',
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `link` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `displayorder` int(11) NOT NULL,
  `enabled` int(11) NOT NULL,
  `color` varchar(32) DEFAULT NULL,
  `merchantid` int(11) DEFAULT '0',
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=364 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_nav','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_nav')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '唯一标识'");}
if(!pdo_fieldexists('ims_wlmerchant_nav','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_nav')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_nav','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_nav')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_nav','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_nav')." ADD   `name` varchar(50) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_nav','link')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_nav')." ADD   `link` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_nav','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_nav')." ADD   `thumb` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_nav','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_nav')." ADD   `displayorder` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_nav','enabled')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_nav')." ADD   `enabled` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_nav','color')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_nav')." ADD   `color` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_nav','merchantid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_nav')." ADD   `merchantid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_nav','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_nav')." ADD   `type` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '唯一标识',
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  `enabled` int(11) NOT NULL,
  `createtime` varchar(32) NOT NULL,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_notice','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_notice')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '唯一标识'");}
if(!pdo_fieldexists('ims_wlmerchant_notice','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_notice')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_notice','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_notice')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_notice','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_notice')." ADD   `title` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_notice','content')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_notice')." ADD   `content` text");}
if(!pdo_fieldexists('ims_wlmerchant_notice','enabled')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_notice')." ADD   `enabled` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_notice','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_notice')." ADD   `createtime` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_notice','link')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_notice')." ADD   `link` varchar(255) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_oparea` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) NOT NULL,
  `areaid` int(11) NOT NULL,
  `aid` int(10) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '0禁用1启用',
  `ishot` int(11) NOT NULL COMMENT '0非热门1热门城市',
  `level` int(11) NOT NULL DEFAULT '2',
  `gid` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_oparea','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oparea')." ADD 
  `id` int(10) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_oparea','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oparea')." ADD   `uniacid` int(10) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_oparea','areaid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oparea')." ADD   `areaid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_oparea','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oparea')." ADD   `aid` int(10) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_oparea','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oparea')." ADD   `status` int(11) NOT NULL DEFAULT '1' COMMENT '0禁用1启用'");}
if(!pdo_fieldexists('ims_wlmerchant_oparea','ishot')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oparea')." ADD   `ishot` int(11) NOT NULL COMMENT '0非热门1热门城市'");}
if(!pdo_fieldexists('ims_wlmerchant_oparea','level')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oparea')." ADD   `level` int(11) NOT NULL DEFAULT '2'");}
if(!pdo_fieldexists('ims_wlmerchant_oparea','gid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oparea')." ADD   `gid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_oparea','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oparea')." ADD   `sort` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_oparea','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oparea')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_oplog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `describe` varchar(225) DEFAULT NULL COMMENT '操作描述',
  `view_url` varchar(225) DEFAULT NULL COMMENT '操作界面url',
  `ip` varchar(32) DEFAULT NULL COMMENT 'IP',
  `data` varchar(1024) DEFAULT NULL COMMENT '操作数据',
  `createtime` varchar(32) DEFAULT NULL COMMENT '操作时间',
  `user` varchar(32) DEFAULT NULL COMMENT '操作员',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_oplog','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oplog')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_oplog','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oplog')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_oplog','describe')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oplog')." ADD   `describe` varchar(225) DEFAULT NULL COMMENT '操作描述'");}
if(!pdo_fieldexists('ims_wlmerchant_oplog','view_url')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oplog')." ADD   `view_url` varchar(225) DEFAULT NULL COMMENT '操作界面url'");}
if(!pdo_fieldexists('ims_wlmerchant_oplog','ip')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oplog')." ADD   `ip` varchar(32) DEFAULT NULL COMMENT 'IP'");}
if(!pdo_fieldexists('ims_wlmerchant_oplog','data')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oplog')." ADD   `data` varchar(1024) DEFAULT NULL COMMENT '操作数据'");}
if(!pdo_fieldexists('ims_wlmerchant_oplog','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oplog')." ADD   `createtime` varchar(32) DEFAULT NULL COMMENT '操作时间'");}
if(!pdo_fieldexists('ims_wlmerchant_oplog','user')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_oplog')." ADD   `user` varchar(32) DEFAULT NULL COMMENT '操作员'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号ID',
  `mid` int(11) DEFAULT NULL COMMENT '用户id',
  `aid` int(11) DEFAULT NULL COMMENT '代理id',
  `sid` int(11) DEFAULT NULL COMMENT '商家id',
  `orderno` varchar(145) DEFAULT NULL COMMENT '订单号',
  `fkid` int(11) DEFAULT NULL COMMENT '商品关联ID',
  `status` int(11) DEFAULT NULL COMMENT '状态 0未支付 1已支付',
  `oprice` decimal(10,2) DEFAULT '0.00' COMMENT '原价',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '实际支付金额',
  `num` int(11) DEFAULT NULL COMMENT '购买数量',
  `paytime` varchar(145) DEFAULT NULL COMMENT '支付时间',
  `paytype` int(11) DEFAULT NULL COMMENT '支付方式 1微信',
  `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间',
  `remark` text COMMENT '卖家备注',
  `issettlement` int(11) DEFAULT '0' COMMENT '1待结算2已结算',
  `plugin` varchar(32) DEFAULT NULL COMMENT '插件',
  `payfor` varchar(32) DEFAULT NULL COMMENT '干什么支付',
  `is_usecard` tinyint(3) DEFAULT NULL COMMENT '1使用优惠',
  `card_type` tinyint(3) DEFAULT NULL COMMENT '优惠类型',
  `card_id` int(3) DEFAULT NULL COMMENT '优惠ID',
  `card_fee` decimal(10,2) DEFAULT '0.00' COMMENT '优惠金额',
  `transid` varchar(145) DEFAULT NULL COMMENT '微信订单号',
  `buyremark` text,
  `spec` text,
  `fightstatus` int(11) NOT NULL,
  `fightgroupid` int(11) NOT NULL,
  `expressid` int(11) NOT NULL,
  `recordid` int(11) NOT NULL,
  `refundtime` varchar(145) NOT NULL,
  `applyrefund` int(11) DEFAULT '0',
  `applytime` varchar(145) DEFAULT NULL,
  `disorderid` int(11) DEFAULT '0',
  `failtimes` int(11) DEFAULT '0',
  `vipbuyflag` int(11) NOT NULL,
  `specid` int(11) NOT NULL,
  `mobile` varchar(32) NOT NULL,
  `name` varchar(125) NOT NULL,
  `address` text NOT NULL,
  `paidprid` int(11) NOT NULL,
  `shareid` int(11) NOT NULL,
  `settlementmoney` decimal(10,2) NOT NULL,
  `goodsprice` decimal(10,2) NOT NULL,
  `overtime` int(11) NOT NULL,
  `changedispatchprice` decimal(10,2) NOT NULL,
  `changeprice` decimal(10,2) NOT NULL,
  `originalprice` decimal(10,2) NOT NULL,
  `estimatetime` int(11) NOT NULL,
  `package` tinyint(2) DEFAULT NULL COMMENT '订单为帖子中的发送红包订单时，这里储存的是红包个数',
  `vip_card_id` int(11) DEFAULT NULL COMMENT '储存用户在购买当前商品时开通的会员卡的id',
  `redisstatus` tinyint(1) NOT NULL,
  `neworderflag` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=288 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_order','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `uniacid` int(11) DEFAULT NULL COMMENT '公众号ID'");}
if(!pdo_fieldexists('ims_wlmerchant_order','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `mid` int(11) DEFAULT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_wlmerchant_order','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `aid` int(11) DEFAULT NULL COMMENT '代理id'");}
if(!pdo_fieldexists('ims_wlmerchant_order','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `sid` int(11) DEFAULT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('ims_wlmerchant_order','orderno')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `orderno` varchar(145) DEFAULT NULL COMMENT '订单号'");}
if(!pdo_fieldexists('ims_wlmerchant_order','fkid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `fkid` int(11) DEFAULT NULL COMMENT '商品关联ID'");}
if(!pdo_fieldexists('ims_wlmerchant_order','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `status` int(11) DEFAULT NULL COMMENT '状态 0未支付 1已支付'");}
if(!pdo_fieldexists('ims_wlmerchant_order','oprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `oprice` decimal(10,2) DEFAULT '0.00' COMMENT '原价'");}
if(!pdo_fieldexists('ims_wlmerchant_order','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `price` decimal(10,2) DEFAULT '0.00' COMMENT '实际支付金额'");}
if(!pdo_fieldexists('ims_wlmerchant_order','num')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `num` int(11) DEFAULT NULL COMMENT '购买数量'");}
if(!pdo_fieldexists('ims_wlmerchant_order','paytime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `paytime` varchar(145) DEFAULT NULL COMMENT '支付时间'");}
if(!pdo_fieldexists('ims_wlmerchant_order','paytype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `paytype` int(11) DEFAULT NULL COMMENT '支付方式 1微信'");}
if(!pdo_fieldexists('ims_wlmerchant_order','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_order','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `remark` text COMMENT '卖家备注'");}
if(!pdo_fieldexists('ims_wlmerchant_order','issettlement')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `issettlement` int(11) DEFAULT '0' COMMENT '1待结算2已结算'");}
if(!pdo_fieldexists('ims_wlmerchant_order','plugin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `plugin` varchar(32) DEFAULT NULL COMMENT '插件'");}
if(!pdo_fieldexists('ims_wlmerchant_order','payfor')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `payfor` varchar(32) DEFAULT NULL COMMENT '干什么支付'");}
if(!pdo_fieldexists('ims_wlmerchant_order','is_usecard')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `is_usecard` tinyint(3) DEFAULT NULL COMMENT '1使用优惠'");}
if(!pdo_fieldexists('ims_wlmerchant_order','card_type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `card_type` tinyint(3) DEFAULT NULL COMMENT '优惠类型'");}
if(!pdo_fieldexists('ims_wlmerchant_order','card_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `card_id` int(3) DEFAULT NULL COMMENT '优惠ID'");}
if(!pdo_fieldexists('ims_wlmerchant_order','card_fee')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `card_fee` decimal(10,2) DEFAULT '0.00' COMMENT '优惠金额'");}
if(!pdo_fieldexists('ims_wlmerchant_order','transid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `transid` varchar(145) DEFAULT NULL COMMENT '微信订单号'");}
if(!pdo_fieldexists('ims_wlmerchant_order','buyremark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `buyremark` text");}
if(!pdo_fieldexists('ims_wlmerchant_order','spec')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `spec` text");}
if(!pdo_fieldexists('ims_wlmerchant_order','fightstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `fightstatus` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','fightgroupid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `fightgroupid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','expressid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `expressid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','recordid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `recordid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','refundtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `refundtime` varchar(145) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','applyrefund')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `applyrefund` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_order','applytime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `applytime` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','disorderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `disorderid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_order','failtimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `failtimes` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_order','vipbuyflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `vipbuyflag` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','specid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `specid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','mobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `mobile` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `name` varchar(125) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','address')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `address` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','paidprid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `paidprid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','shareid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `shareid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','settlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `settlementmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','goodsprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `goodsprice` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','overtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `overtime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','changedispatchprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `changedispatchprice` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','changeprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `changeprice` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','originalprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `originalprice` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','estimatetime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `estimatetime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','package')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `package` tinyint(2) DEFAULT NULL COMMENT '订单为帖子中的发送红包订单时，这里储存的是红包个数'");}
if(!pdo_fieldexists('ims_wlmerchant_order','vip_card_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `vip_card_id` int(11) DEFAULT NULL COMMENT '储存用户在购买当前商品时开通的会员卡的id'");}
if(!pdo_fieldexists('ims_wlmerchant_order','redisstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `redisstatus` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_order','neworderflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_order')." ADD   `neworderflag` tinyint(1) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `merchantid` int(11) NOT NULL COMMENT '商户id',
  `status` int(11) NOT NULL COMMENT '状态',
  `title` varchar(145) NOT NULL COMMENT '礼包标题',
  `price` int(11) DEFAULT NULL COMMENT '礼包价值',
  `datestatus` int(11) NOT NULL COMMENT '循环周期 1无 2每周 3每月 4每年',
  `usetimes` int(11) NOT NULL COMMENT '周期内使用次数',
  `limit` varchar(225) NOT NULL COMMENT '副标题（使用限制）',
  `timeslimit` int(11) NOT NULL COMMENT '单日提供数量',
  `allnum` int(11) NOT NULL COMMENT '总数量',
  `starttime` int(11) NOT NULL COMMENT '活动开始时间',
  `endtime` int(11) NOT NULL COMMENT '活动结束时间',
  `appointment` int(11) NOT NULL COMMENT '提前预约',
  `integral` int(11) NOT NULL COMMENT '赠送积分',
  `sort` int(11) NOT NULL COMMENT '排序',
  `pv` int(11) NOT NULL COMMENT '人气',
  `describe` text NOT NULL COMMENT '说明',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `timestatus` int(11) DEFAULT '0',
  `level` text NOT NULL,
  `packtimestatus` int(11) NOT NULL,
  `datestarttime` int(11) NOT NULL,
  `dateendtime` int(11) NOT NULL,
  `oplimit` int(11) NOT NULL,
  `resetswitch` int(11) NOT NULL,
  `listshow` int(11) NOT NULL,
  `weeklimit` int(11) NOT NULL,
  `monthlimit` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '一般礼包0 外链礼包1',
  `extlink` varchar(255) NOT NULL COMMENT '外部链接',
  `extinfo` text NOT NULL COMMENT '外部信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_package','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_package','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_package','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_package','merchantid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `merchantid` int(11) NOT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('ims_wlmerchant_package','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `status` int(11) NOT NULL COMMENT '状态'");}
if(!pdo_fieldexists('ims_wlmerchant_package','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `title` varchar(145) NOT NULL COMMENT '礼包标题'");}
if(!pdo_fieldexists('ims_wlmerchant_package','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `price` int(11) DEFAULT NULL COMMENT '礼包价值'");}
if(!pdo_fieldexists('ims_wlmerchant_package','datestatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `datestatus` int(11) NOT NULL COMMENT '循环周期 1无 2每周 3每月 4每年'");}
if(!pdo_fieldexists('ims_wlmerchant_package','usetimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `usetimes` int(11) NOT NULL COMMENT '周期内使用次数'");}
if(!pdo_fieldexists('ims_wlmerchant_package','limit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `limit` varchar(225) NOT NULL COMMENT '副标题（使用限制）'");}
if(!pdo_fieldexists('ims_wlmerchant_package','timeslimit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `timeslimit` int(11) NOT NULL COMMENT '单日提供数量'");}
if(!pdo_fieldexists('ims_wlmerchant_package','allnum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `allnum` int(11) NOT NULL COMMENT '总数量'");}
if(!pdo_fieldexists('ims_wlmerchant_package','starttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `starttime` int(11) NOT NULL COMMENT '活动开始时间'");}
if(!pdo_fieldexists('ims_wlmerchant_package','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `endtime` int(11) NOT NULL COMMENT '活动结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_package','appointment')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `appointment` int(11) NOT NULL COMMENT '提前预约'");}
if(!pdo_fieldexists('ims_wlmerchant_package','integral')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `integral` int(11) NOT NULL COMMENT '赠送积分'");}
if(!pdo_fieldexists('ims_wlmerchant_package','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `sort` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_wlmerchant_package','pv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `pv` int(11) NOT NULL COMMENT '人气'");}
if(!pdo_fieldexists('ims_wlmerchant_package','describe')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `describe` text NOT NULL COMMENT '说明'");}
if(!pdo_fieldexists('ims_wlmerchant_package','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_package','timestatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `timestatus` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_package','level')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `level` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_package','packtimestatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `packtimestatus` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_package','datestarttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `datestarttime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_package','dateendtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `dateendtime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_package','oplimit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `oplimit` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_package','resetswitch')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `resetswitch` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_package','listshow')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `listshow` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_package','weeklimit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `weeklimit` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_package','monthlimit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `monthlimit` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_package','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `type` int(11) NOT NULL COMMENT '一般礼包0 外链礼包1'");}
if(!pdo_fieldexists('ims_wlmerchant_package','extlink')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `extlink` varchar(255) NOT NULL COMMENT '外部链接'");}
if(!pdo_fieldexists('ims_wlmerchant_package','extinfo')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_package')." ADD   `extinfo` text NOT NULL COMMENT '外部信息'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_paidrecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `activeid` int(11) NOT NULL COMMENT '活动id',
  `integral` int(11) NOT NULL COMMENT '赠送积分',
  `couponid` varchar(255) DEFAULT NULL COMMENT '赠送的优惠券id',
  `getcouflag` int(11) NOT NULL COMMENT '领取卡券标记',
  `getcoutime` int(11) NOT NULL COMMENT '领取优惠券时间',
  `codeid` int(11) NOT NULL COMMENT '赠送激活码id',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `paytype` int(11) NOT NULL COMMENT '支付方式',
  `price` decimal(10,2) NOT NULL COMMENT '支付价格',
  `img` varchar(255) NOT NULL COMMENT '广告图片',
  `type` int(11) NOT NULL COMMENT '订单类型',
  `orderid` int(11) NOT NULL COMMENT '订单id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='支付有礼记录表';

");

if(!pdo_fieldexists('ims_wlmerchant_paidrecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','activeid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `activeid` int(11) NOT NULL COMMENT '活动id'");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','integral')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `integral` int(11) NOT NULL COMMENT '赠送积分'");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','couponid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `couponid` varchar(255) DEFAULT NULL COMMENT '赠送的优惠券id'");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','getcouflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `getcouflag` int(11) NOT NULL COMMENT '领取卡券标记'");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','getcoutime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `getcoutime` int(11) NOT NULL COMMENT '领取优惠券时间'");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','codeid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `codeid` int(11) NOT NULL COMMENT '赠送激活码id'");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','paytype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `paytype` int(11) NOT NULL COMMENT '支付方式'");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `price` decimal(10,2) NOT NULL COMMENT '支付价格'");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','img')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `img` varchar(255) NOT NULL COMMENT '广告图片'");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `type` int(11) NOT NULL COMMENT '订单类型'");}
if(!pdo_fieldexists('ims_wlmerchant_paidrecord','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paidrecord')." ADD   `orderid` int(11) NOT NULL COMMENT '订单id'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_payactive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `aid` int(11) NOT NULL COMMENT '代理id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `orderprice` decimal(10,2) NOT NULL COMMENT '订单金额',
  `starttime` int(11) NOT NULL COMMENT '开始时间',
  `endtime` int(11) NOT NULL COMMENT '结束时间',
  `userstatus` int(11) NOT NULL COMMENT '用户资格 0全部用户 1一卡通会员',
  `orderstatus` int(11) NOT NULL COMMENT '参与方式 0订单金额 1购买商品',
  `status` int(11) NOT NULL COMMENT '状态',
  `rushflag` int(11) NOT NULL COMMENT '抢购标志',
  `grouponflag` int(11) NOT NULL COMMENT '团购标识',
  `fightgroupflag` int(11) NOT NULL COMMENT '拼团标志',
  `couponflag` int(11) NOT NULL COMMENT '卡券标志',
  `halfcardflag` int(11) NOT NULL COMMENT '一卡通标志',
  `chargeflag` int(11) NOT NULL COMMENT '入驻标志',
  `rushids` text NOT NULL COMMENT '抢购商品id集',
  `grouponids` text NOT NULL COMMENT '团购商品id集',
  `fightgroupids` text NOT NULL COMMENT '拼团商品id集',
  `couponids` text NOT NULL COMMENT '卡券商品id集',
  `halfcardids` text NOT NULL COMMENT '一卡通商品id集',
  `chargeids` text NOT NULL COMMENT '付费入驻商品id集',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `integralrate` decimal(10,2) DEFAULT '0.00',
  `giftstatus` int(11) NOT NULL COMMENT '赠品 0不赠送 1优惠券 2激活码',
  `giftcouponid` varchar(50) DEFAULT NULL COMMENT '赠券id',
  `codereamrk` text NOT NULL COMMENT '激活码备注',
  `img` varchar(255) NOT NULL COMMENT '图片',
  `getstatus` int(11) NOT NULL COMMENT '0手动领取 1自动发放',
  `advurl` varchar(225) NOT NULL,
  `payonlineflag` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付有礼活动表';

");

if(!pdo_fieldexists('ims_wlmerchant_payactive','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `aid` int(11) NOT NULL COMMENT '代理id'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `title` varchar(255) NOT NULL COMMENT '标题'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','orderprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `orderprice` decimal(10,2) NOT NULL COMMENT '订单金额'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','starttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `starttime` int(11) NOT NULL COMMENT '开始时间'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `endtime` int(11) NOT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','userstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `userstatus` int(11) NOT NULL COMMENT '用户资格 0全部用户 1一卡通会员'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','orderstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `orderstatus` int(11) NOT NULL COMMENT '参与方式 0订单金额 1购买商品'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `status` int(11) NOT NULL COMMENT '状态'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','rushflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `rushflag` int(11) NOT NULL COMMENT '抢购标志'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','grouponflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `grouponflag` int(11) NOT NULL COMMENT '团购标识'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','fightgroupflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `fightgroupflag` int(11) NOT NULL COMMENT '拼团标志'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','couponflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `couponflag` int(11) NOT NULL COMMENT '卡券标志'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','halfcardflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `halfcardflag` int(11) NOT NULL COMMENT '一卡通标志'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','chargeflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `chargeflag` int(11) NOT NULL COMMENT '入驻标志'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','rushids')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `rushids` text NOT NULL COMMENT '抢购商品id集'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','grouponids')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `grouponids` text NOT NULL COMMENT '团购商品id集'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','fightgroupids')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `fightgroupids` text NOT NULL COMMENT '拼团商品id集'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','couponids')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `couponids` text NOT NULL COMMENT '卡券商品id集'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','halfcardids')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `halfcardids` text NOT NULL COMMENT '一卡通商品id集'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','chargeids')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `chargeids` text NOT NULL COMMENT '付费入驻商品id集'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','integralrate')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `integralrate` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','giftstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `giftstatus` int(11) NOT NULL COMMENT '赠品 0不赠送 1优惠券 2激活码'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','giftcouponid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `giftcouponid` varchar(50) DEFAULT NULL COMMENT '赠券id'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','codereamrk')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `codereamrk` text NOT NULL COMMENT '激活码备注'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','img')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `img` varchar(255) NOT NULL COMMENT '图片'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','getstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `getstatus` int(11) NOT NULL COMMENT '0手动领取 1自动发放'");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','advurl')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `advurl` varchar(225) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_payactive','payonlineflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_payactive')." ADD   `payonlineflag` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_paylog` (
  `plid` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `acid` int(10) NOT NULL,
  `openid` varchar(40) NOT NULL,
  `uniontid` varchar(64) NOT NULL,
  `tid` varchar(128) NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `module` varchar(50) NOT NULL,
  `tag` varchar(2000) NOT NULL,
  `is_usecard` tinyint(3) unsigned NOT NULL,
  `card_type` tinyint(3) unsigned NOT NULL,
  `card_id` varchar(50) NOT NULL,
  `card_fee` decimal(10,2) unsigned NOT NULL,
  `encrypt_code` varchar(100) NOT NULL,
  `plugin` varchar(50) DEFAULT NULL COMMENT '插件名',
  `payfor` varchar(145) DEFAULT NULL COMMENT '干什么支付',
  PRIMARY KEY (`plid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_tid` (`tid`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `uniontid` (`uniontid`)
) ENGINE=MyISAM AUTO_INCREMENT=529 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_paylog','plid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD 
  `plid` bigint(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `type` varchar(20) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','acid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `acid` int(10) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','openid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `openid` varchar(40) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','uniontid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `uniontid` varchar(64) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','tid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `tid` varchar(128) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','fee')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `fee` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `status` tinyint(4) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','module')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `module` varchar(50) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','tag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `tag` varchar(2000) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','is_usecard')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `is_usecard` tinyint(3) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','card_type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `card_type` tinyint(3) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','card_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `card_id` varchar(50) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','card_fee')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `card_fee` decimal(10,2) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','encrypt_code')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `encrypt_code` varchar(100) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','plugin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `plugin` varchar(50) DEFAULT NULL COMMENT '插件名'");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','payfor')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   `payfor` varchar(145) DEFAULT NULL COMMENT '干什么支付'");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','plid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   PRIMARY KEY (`plid`)");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','idx_openid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   KEY `idx_openid` (`openid`)");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','idx_tid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   KEY `idx_tid` (`tid`)");}
if(!pdo_fieldexists('ims_wlmerchant_paylog','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_paylog')." ADD   KEY `idx_uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_perm_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0',
  `plugins` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_perm_account','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_perm_account')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_perm_account','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_perm_account')." ADD   `uniacid` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_perm_account','plugins')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_perm_account')." ADD   `plugins` text");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_plugin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL,
  `thumb` varchar(255) NOT NULL DEFAULT '',
  `ability` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `displayorder` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_plugin','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_plugin')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_plugin','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_plugin')." ADD   `name` varchar(100) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_plugin','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_plugin')." ADD   `type` varchar(20) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_plugin','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_plugin')." ADD   `title` varchar(100) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_plugin','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_plugin')." ADD   `thumb` varchar(255) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_plugin','ability')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_plugin')." ADD   `ability` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_plugin','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_plugin')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_plugin','displayorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_plugin')." ADD   `displayorder` int(10) unsigned NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_plugin','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_plugin')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_pocket_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL COMMENT '用户id',
  `inid` int(11) DEFAULT NULL COMMENT '帖子ID',
  `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_pocket_blacklist','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_blacklist')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_blacklist','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_blacklist')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_blacklist','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_blacklist')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_blacklist','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_blacklist')." ADD   `mid` int(11) DEFAULT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_blacklist','inid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_blacklist')." ADD   `inid` int(11) DEFAULT NULL COMMENT '帖子ID'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_blacklist','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_blacklist')." ADD   `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_pocket_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `tid` int(11) DEFAULT NULL COMMENT 'infor表id',
  `content` text NOT NULL,
  `mid` int(11) DEFAULT NULL,
  `createtime` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=259 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_pocket_comment','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_comment')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_comment','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_comment')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_comment','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_comment')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_comment','tid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_comment')." ADD   `tid` int(11) DEFAULT NULL COMMENT 'infor表id'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_comment','content')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_comment')." ADD   `content` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_comment','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_comment')." ADD   `mid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_comment','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_comment')." ADD   `createtime` varchar(32) DEFAULT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_pocket_informations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `status` varchar(32) DEFAULT NULL COMMENT '0 显示，1 审核中 2 不显示 3已删除',
  `content` text,
  `img` text,
  `mid` int(11) DEFAULT NULL,
  `top` tinyint(1) unsigned DEFAULT '0' COMMENT '是否置顶 1 置顶 0不置顶',
  `look` int(11) DEFAULT NULL COMMENT '浏览量',
  `likenum` int(11) DEFAULT '0',
  `share` int(11) DEFAULT NULL COMMENT '分享数',
  `endtime` varchar(32) DEFAULT NULL COMMENT '结束时间',
  `onetype` int(11) DEFAULT NULL COMMENT '一级分类',
  `type` int(11) DEFAULT NULL COMMENT '二级分类',
  `nickname` varchar(45) DEFAULT NULL COMMENT '联系人姓名',
  `phone` varchar(32) DEFAULT NULL COMMENT '电话',
  `createtime` varchar(32) DEFAULT NULL COMMENT '创建时间',
  `likeids` text,
  `avatar` varchar(255) DEFAULT '0',
  `share_title` text,
  `keyword` text,
  `reason` varchar(255) NOT NULL,
  `redpack` decimal(10,2) NOT NULL,
  `sredpack` decimal(10,2) NOT NULL,
  `package` tinyint(2) NOT NULL,
  `redpackstatus` int(11) NOT NULL COMMENT '红包状态',
  `location` varchar(255) NOT NULL COMMENT '发帖人定位',
  `address` varchar(255) NOT NULL COMMENT '发帖人地址',
  `locastatus` int(11) NOT NULL COMMENT '发帖人地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=713 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `status` varchar(32) DEFAULT NULL COMMENT '0 显示，1 审核中 2 不显示 3已删除'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','content')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `content` text");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','img')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `img` text");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `mid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','top')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `top` tinyint(1) unsigned DEFAULT '0' COMMENT '是否置顶 1 置顶 0不置顶'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','look')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `look` int(11) DEFAULT NULL COMMENT '浏览量'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','likenum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `likenum` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','share')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `share` int(11) DEFAULT NULL COMMENT '分享数'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `endtime` varchar(32) DEFAULT NULL COMMENT '结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','onetype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `onetype` int(11) DEFAULT NULL COMMENT '一级分类'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `type` int(11) DEFAULT NULL COMMENT '二级分类'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','nickname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `nickname` varchar(45) DEFAULT NULL COMMENT '联系人姓名'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','phone')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `phone` varchar(32) DEFAULT NULL COMMENT '电话'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `createtime` varchar(32) DEFAULT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','likeids')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `likeids` text");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','avatar')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `avatar` varchar(255) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','share_title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `share_title` text");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','keyword')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `keyword` text");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','reason')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `reason` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','redpack')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `redpack` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','sredpack')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `sredpack` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','package')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `package` tinyint(2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','redpackstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `redpackstatus` int(11) NOT NULL COMMENT '红包状态'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','location')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `location` varchar(255) NOT NULL COMMENT '发帖人定位'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','address')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `address` varchar(255) NOT NULL COMMENT '发帖人地址'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_informations','locastatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_informations')." ADD   `locastatus` int(11) NOT NULL COMMENT '发帖人地址'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_pocket_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `cid` int(11) DEFAULT NULL,
  `smid` int(11) DEFAULT NULL,
  `amid` int(11) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `createtime` varchar(32) DEFAULT NULL,
  `tid` int(11) NOT NULL COMMENT '帖子表的id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=189 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_pocket_reply','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_reply')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_reply','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_reply')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_reply','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_reply')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_reply','cid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_reply')." ADD   `cid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_reply','smid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_reply')." ADD   `smid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_reply','amid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_reply')." ADD   `amid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_reply','content')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_reply')." ADD   `content` varchar(255) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_reply','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_reply')." ADD   `createtime` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_reply','tid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_reply')." ADD   `tid` int(11) NOT NULL COMMENT '帖子表的id'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_pocket_slide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `img` varchar(300) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `sort` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(300) DEFAULT NULL,
  `aid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_pocket_slide','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_slide')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_slide','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_slide')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_slide','img')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_slide')." ADD   `img` varchar(300) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_slide','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_slide')." ADD   `title` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_slide','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_slide')." ADD   `status` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_slide','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_slide')." ADD   `sort` tinyint(1) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_slide','url')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_slide')." ADD   `url` varchar(300) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_slide','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_slide')." ADD   `aid` int(11) DEFAULT '0'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_pocket_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '1 启用',
  `sort` tinyint(1) unsigned DEFAULT '0' COMMENT '排序',
  `img` varchar(300) DEFAULT NULL,
  `type` int(11) DEFAULT '0' COMMENT '默认 0 为一级分类，否则为一级分类的ID',
  `price` decimal(10,2) NOT NULL,
  `color` varchar(32) DEFAULT NULL,
  `url` varchar(300) DEFAULT NULL,
  `isnav` int(11) DEFAULT '0',
  `aid` int(11) DEFAULT '0',
  `keyword` text,
  `isdistri` tinyint(1) NOT NULL,
  `onedismoney` decimal(10,2) NOT NULL,
  `twodismoney` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=252 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_pocket_type','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `title` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `status` tinyint(1) unsigned DEFAULT '0' COMMENT '1 启用'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `sort` tinyint(1) unsigned DEFAULT '0' COMMENT '排序'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','img')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `img` varchar(300) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `type` int(11) DEFAULT '0' COMMENT '默认 0 为一级分类，否则为一级分类的ID'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `price` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','color')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `color` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','url')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `url` varchar(300) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','isnav')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `isnav` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `aid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','keyword')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `keyword` text");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','isdistri')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `isdistri` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','onedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `onedismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_pocket_type','twodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_pocket_type')." ADD   `twodismoney` decimal(10,2) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_poster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(3) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `bg` varchar(255) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `createtime` int(11) NOT NULL DEFAULT '0',
  `otherbg` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_type` (`type`),
  KEY `idx_createtime` (`createtime`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_poster','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_poster')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_poster','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_poster')." ADD   `uniacid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_poster','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_poster')." ADD   `type` tinyint(3) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_poster','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_poster')." ADD   `title` varchar(255) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_poster','bg')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_poster')." ADD   `bg` varchar(255) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_poster','data')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_poster')." ADD   `data` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_poster','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_poster')." ADD   `createtime` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_poster','otherbg')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_poster')." ADD   `otherbg` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_poster','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_poster')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_poster','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_poster')." ADD   KEY `idx_uniacid` (`uniacid`)");}
if(!pdo_fieldexists('ims_wlmerchant_poster','idx_type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_poster')." ADD   KEY `idx_type` (`type`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_puv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `areaid` int(11) DEFAULT NULL COMMENT '地区id',
  `uniacid` int(11) NOT NULL,
  `pv` int(11) NOT NULL,
  `uv` int(11) NOT NULL,
  `date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=1601 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_puv','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puv')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_puv','areaid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puv')." ADD   `areaid` int(11) DEFAULT NULL COMMENT '地区id'");}
if(!pdo_fieldexists('ims_wlmerchant_puv','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puv')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_puv','pv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puv')." ADD   `pv` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_puv','uv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puv')." ADD   `uv` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_puv','date')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puv')." ADD   `date` varchar(20) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_puv','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puv')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_puvrecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `pv` int(11) NOT NULL,
  `date` varchar(20) NOT NULL,
  `areaid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `mid` (`mid`)
) ENGINE=MyISAM AUTO_INCREMENT=13549 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_puvrecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puvrecord')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_puvrecord','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puvrecord')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_puvrecord','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puvrecord')." ADD   `mid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_puvrecord','pv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puvrecord')." ADD   `pv` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_puvrecord','date')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puvrecord')." ADD   `date` varchar(20) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_puvrecord','areaid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puvrecord')." ADD   `areaid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_puvrecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puvrecord')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_puvrecord','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_puvrecord')." ADD   KEY `uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_qrcode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(10) DEFAULT NULL,
  `uniacid` int(10) unsigned NOT NULL,
  `sid` int(11) NOT NULL COMMENT '商户ID',
  `qrid` int(10) unsigned NOT NULL,
  `model` tinyint(1) NOT NULL,
  `cardsn` varchar(64) NOT NULL,
  `salt` varchar(32) DEFAULT NULL COMMENT '加密盐',
  `status` tinyint(1) unsigned NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  `remark` varchar(50) NOT NULL COMMENT '场景备注',
  `type` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `aid` (`aid`),
  KEY `qrid` (`qrid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=258 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_qrcode','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   `aid` int(10) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   `uniacid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   `sid` int(11) NOT NULL COMMENT '商户ID'");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','qrid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   `qrid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','model')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   `model` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','cardsn')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   `cardsn` varchar(64) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','salt')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   `salt` varchar(32) DEFAULT NULL COMMENT '加密盐'");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   `status` tinyint(1) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   `createtime` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   `remark` varchar(50) NOT NULL COMMENT '场景备注'");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   `type` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   KEY `uniacid` (`uniacid`)");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode')." ADD   KEY `aid` (`aid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_qrcode_apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `type` int(2) NOT NULL,
  `num` int(11) NOT NULL,
  `pnum` int(11) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_qrcode_apply','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode_apply')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode_apply','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode_apply')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode_apply','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode_apply')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode_apply','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode_apply')." ADD   `status` int(2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode_apply','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode_apply')." ADD   `type` int(2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode_apply','num')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode_apply')." ADD   `num` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode_apply','pnum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode_apply')." ADD   `pnum` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode_apply','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode_apply')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_qrcode_apply','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_qrcode_apply')." ADD   `createtime` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_rank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `name` varchar(30) NOT NULL COMMENT '排行榜名称',
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型0用户1商户',
  `orderby` tinyint(1) unsigned NOT NULL COMMENT '排序方式：0=用户积分  1=用户余额  2=用户消费金额 11=商户人气  12=商户订单数  13=商户营业额',
  `status` tinyint(1) unsigned NOT NULL COMMENT '0=禁用  1=启用',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `number` int(11) NOT NULL DEFAULT '10' COMMENT '排行榜显示数量 默认10',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='排行榜信息表';

");

if(!pdo_fieldexists('ims_wlmerchant_rank','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rank')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_rank','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rank')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rank','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rank')." ADD   `name` varchar(30) NOT NULL COMMENT '排行榜名称'");}
if(!pdo_fieldexists('ims_wlmerchant_rank','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rank')." ADD   `type` tinyint(1) unsigned NOT NULL COMMENT '类型0用户1商户'");}
if(!pdo_fieldexists('ims_wlmerchant_rank','orderby')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rank')." ADD   `orderby` tinyint(1) unsigned NOT NULL COMMENT '排序方式：0=用户积分  1=用户余额  2=用户消费金额 11=商户人气  12=商户订单数  13=商户营业额'");}
if(!pdo_fieldexists('ims_wlmerchant_rank','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rank')." ADD   `status` tinyint(1) unsigned NOT NULL COMMENT '0=禁用  1=启用'");}
if(!pdo_fieldexists('ims_wlmerchant_rank','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rank')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_rank','number')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rank')." ADD   `number` int(11) NOT NULL DEFAULT '10' COMMENT '排行榜显示数量 默认10'");}
if(!pdo_fieldexists('ims_wlmerchant_rank','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rank')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_rank','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rank')." ADD   KEY `idx_uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_red_envelope` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL COMMENT '关联贴子的id（红包的id）',
  `mid` int(11) NOT NULL COMMENT '领取红包的用户的id',
  `gettime` varchar(12) NOT NULL COMMENT '红包领取时间',
  `money` decimal(10,2) NOT NULL COMMENT '领取红包的金额',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `aid` int(11) NOT NULL COMMENT '代理商id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='红包领取记录表';

");

if(!pdo_fieldexists('ims_wlmerchant_red_envelope','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_red_envelope')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_red_envelope','pid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_red_envelope')." ADD   `pid` int(11) unsigned NOT NULL COMMENT '关联贴子的id（红包的id）'");}
if(!pdo_fieldexists('ims_wlmerchant_red_envelope','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_red_envelope')." ADD   `mid` int(11) NOT NULL COMMENT '领取红包的用户的id'");}
if(!pdo_fieldexists('ims_wlmerchant_red_envelope','gettime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_red_envelope')." ADD   `gettime` varchar(12) NOT NULL COMMENT '红包领取时间'");}
if(!pdo_fieldexists('ims_wlmerchant_red_envelope','money')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_red_envelope')." ADD   `money` decimal(10,2) NOT NULL COMMENT '领取红包的金额'");}
if(!pdo_fieldexists('ims_wlmerchant_red_envelope','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_red_envelope')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_red_envelope','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_red_envelope')." ADD   `aid` int(11) NOT NULL COMMENT '代理商id'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_refund_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1手机端2Web端3最后一人退款4部分退款5计划任务退款',
  `payfee` varchar(100) NOT NULL COMMENT '支付金额',
  `paytype` int(3) DEFAULT NULL COMMENT '支付方式1余额2微信',
  `refundfee` varchar(100) NOT NULL COMMENT '退还金额',
  `transid` varchar(115) NOT NULL COMMENT '订单编号',
  `refund_id` varchar(115) NOT NULL COMMENT '微信退款单号',
  `refundername` varchar(100) NOT NULL COMMENT '退款人姓名',
  `refundermobile` varchar(100) NOT NULL COMMENT '退款人电话',
  `goodsname` varchar(100) NOT NULL COMMENT '商品名称',
  `createtime` varchar(45) NOT NULL COMMENT '退款时间',
  `status` int(11) NOT NULL COMMENT '0未成功1成功',
  `orderid` varchar(45) NOT NULL COMMENT '订单id',
  `sid` int(11) NOT NULL COMMENT '商家id',
  `remark` text COMMENT '退款备注',
  `plugin` varchar(32) DEFAULT NULL COMMENT '插件名称',
  `errmsg` varchar(445) DEFAULT '0' COMMENT '退款错误信息',
  `aid` int(11) NOT NULL,
  `orderno` varchar(32) NOT NULL,
  `mid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_refund_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `type` int(11) NOT NULL COMMENT '1手机端2Web端3最后一人退款4部分退款5计划任务退款'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','payfee')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `payfee` varchar(100) NOT NULL COMMENT '支付金额'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','paytype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `paytype` int(3) DEFAULT NULL COMMENT '支付方式1余额2微信'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','refundfee')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `refundfee` varchar(100) NOT NULL COMMENT '退还金额'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','transid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `transid` varchar(115) NOT NULL COMMENT '订单编号'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','refund_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `refund_id` varchar(115) NOT NULL COMMENT '微信退款单号'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','refundername')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `refundername` varchar(100) NOT NULL COMMENT '退款人姓名'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','refundermobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `refundermobile` varchar(100) NOT NULL COMMENT '退款人电话'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','goodsname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `goodsname` varchar(100) NOT NULL COMMENT '商品名称'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `createtime` varchar(45) NOT NULL COMMENT '退款时间'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `status` int(11) NOT NULL COMMENT '0未成功1成功'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `orderid` varchar(45) NOT NULL COMMENT '订单id'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `sid` int(11) NOT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `remark` text COMMENT '退款备注'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','plugin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `plugin` varchar(32) DEFAULT NULL COMMENT '插件名称'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','errmsg')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `errmsg` varchar(445) DEFAULT '0' COMMENT '退款错误信息'");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','orderno')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `orderno` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_refund_record','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_refund_record')." ADD   `mid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `limit` text NOT NULL COMMENT '该角色拥有的权限数组',
  `title` varchar(32) NOT NULL COMMENT '角色title',
  `status` int(2) NOT NULL COMMENT '角色是否显示状态：2显示；0、1不显示',
  `type` int(2) NOT NULL COMMENT '角色类型（备用）',
  `createtime` varchar(32) NOT NULL COMMENT '创建时间',
  `updatetime` varchar(32) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='角色创建表';

");

if(!pdo_fieldexists('ims_wlmerchant_role','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_role')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色id'");}
if(!pdo_fieldexists('ims_wlmerchant_role','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_role')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_role','limit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_role')." ADD   `limit` text NOT NULL COMMENT '该角色拥有的权限数组'");}
if(!pdo_fieldexists('ims_wlmerchant_role','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_role')." ADD   `title` varchar(32) NOT NULL COMMENT '角色title'");}
if(!pdo_fieldexists('ims_wlmerchant_role','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_role')." ADD   `status` int(2) NOT NULL COMMENT '角色是否显示状态：2显示；0、1不显示'");}
if(!pdo_fieldexists('ims_wlmerchant_role','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_role')." ADD   `type` int(2) NOT NULL COMMENT '角色类型（备用）'");}
if(!pdo_fieldexists('ims_wlmerchant_role','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_role')." ADD   `createtime` varchar(32) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_role','updatetime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_role')." ADD   `updatetime` varchar(32) NOT NULL COMMENT '修改时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_rush_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号id',
  `sid` int(11) DEFAULT NULL COMMENT '商家id',
  `aid` int(11) DEFAULT NULL COMMENT '代理id',
  `name` varchar(145) DEFAULT NULL COMMENT '活动名称【可和仓库的商品名称一致】',
  `code` varchar(145) DEFAULT NULL COMMENT '商品编号',
  `detail` longtext NOT NULL COMMENT '详情',
  `price` decimal(10,2) DEFAULT NULL COMMENT '抢购价',
  `oldprice` decimal(10,2) DEFAULT NULL COMMENT '原价',
  `vipprice` decimal(10,2) DEFAULT '0.00' COMMENT 'vip价格',
  `num` int(11) DEFAULT NULL COMMENT '限量',
  `levelnum` int(11) DEFAULT NULL COMMENT '剩余数量',
  `status` int(11) DEFAULT '1' COMMENT '1进行中2已结束',
  `starttime` varchar(225) DEFAULT NULL COMMENT '活动开始时间',
  `endtime` varchar(225) DEFAULT NULL COMMENT '活动结束时间',
  `follow` int(11) DEFAULT NULL COMMENT '关注人数',
  `tag` text COMMENT '标签',
  `share_title` varchar(32) DEFAULT NULL,
  `share_image` varchar(250) DEFAULT NULL,
  `share_desc` varchar(32) DEFAULT NULL,
  `unit` varchar(32) DEFAULT NULL COMMENT '单位',
  `thumb` varchar(145) DEFAULT NULL COMMENT '首页图片',
  `thumbs` text COMMENT '图集',
  `describe` text,
  `op_one_limit` int(11) DEFAULT NULL COMMENT '单人限购',
  `cutofftime` int(11) NOT NULL,
  `is_indexshow` int(11) NOT NULL DEFAULT '1',
  `allsalenum` int(11) NOT NULL,
  `sort` int(11) DEFAULT '0',
  `cutoffstatus` int(11) DEFAULT '0',
  `cutoffday` int(11) DEFAULT '0',
  `retainage` decimal(10,2) DEFAULT '0.00',
  `appointment` int(11) DEFAULT '0',
  `integral` int(11) DEFAULT '0',
  `pv` int(11) DEFAULT '0',
  `vipstatus` int(11) NOT NULL,
  `cateid` int(11) NOT NULL,
  `settlementmoney` decimal(10,2) DEFAULT '0.00',
  `onedismoney` decimal(10,2) DEFAULT '0.00',
  `twodismoney` decimal(10,2) DEFAULT '0.00',
  `threedismoney` decimal(10,2) DEFAULT '0.00',
  `orderinfo` varchar(255) NOT NULL,
  `isdistri` int(11) DEFAULT '0',
  `falseorder` text NOT NULL,
  `specialid` int(11) NOT NULL,
  `bgmusic` varchar(255) NOT NULL,
  `vipsettlementmoney` decimal(10,2) NOT NULL,
  `viponedismoney` decimal(10,2) NOT NULL,
  `viptwodismoney` decimal(10,2) NOT NULL,
  `vipthreedismoney` decimal(10,2) NOT NULL,
  `optionstatus` int(11) NOT NULL,
  `userlabel` text NOT NULL,
  `creditmoney` decimal(10,2) NOT NULL,
  `sharemoney` decimal(10,2) NOT NULL,
  `sharestatus` int(11) NOT NULL,
  `independent` int(11) NOT NULL,
  `allowapplyre` int(11) NOT NULL,
  `usestatus` int(11) NOT NULL,
  `expressid` int(11) NOT NULL,
  `fastpay` int(11) NOT NULL,
  `overrefund` tinyint(1) NOT NULL COMMENT '1=开启过期退款',
  `level` text NOT NULL COMMENT '适用会员等级',
  `dissettime` int(11) NOT NULL COMMENT '分销佣金结算时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_rush_activity','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `uniacid` int(11) DEFAULT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `sid` int(11) DEFAULT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `aid` int(11) DEFAULT NULL COMMENT '代理id'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `name` varchar(145) DEFAULT NULL COMMENT '活动名称【可和仓库的商品名称一致】'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','code')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `code` varchar(145) DEFAULT NULL COMMENT '商品编号'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','detail')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `detail` longtext NOT NULL COMMENT '详情'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `price` decimal(10,2) DEFAULT NULL COMMENT '抢购价'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','oldprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `oldprice` decimal(10,2) DEFAULT NULL COMMENT '原价'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','vipprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `vipprice` decimal(10,2) DEFAULT '0.00' COMMENT 'vip价格'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','num')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `num` int(11) DEFAULT NULL COMMENT '限量'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','levelnum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `levelnum` int(11) DEFAULT NULL COMMENT '剩余数量'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `status` int(11) DEFAULT '1' COMMENT '1进行中2已结束'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','starttime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `starttime` varchar(225) DEFAULT NULL COMMENT '活动开始时间'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','endtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `endtime` varchar(225) DEFAULT NULL COMMENT '活动结束时间'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','follow')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `follow` int(11) DEFAULT NULL COMMENT '关注人数'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','tag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `tag` text COMMENT '标签'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','share_title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `share_title` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','share_image')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `share_image` varchar(250) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','share_desc')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `share_desc` varchar(32) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','unit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `unit` varchar(32) DEFAULT NULL COMMENT '单位'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `thumb` varchar(145) DEFAULT NULL COMMENT '首页图片'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','thumbs')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `thumbs` text COMMENT '图集'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','describe')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `describe` text");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','op_one_limit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `op_one_limit` int(11) DEFAULT NULL COMMENT '单人限购'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','cutofftime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `cutofftime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','is_indexshow')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `is_indexshow` int(11) NOT NULL DEFAULT '1'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','allsalenum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `allsalenum` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `sort` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','cutoffstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `cutoffstatus` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','cutoffday')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `cutoffday` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','retainage')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `retainage` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','appointment')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `appointment` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','integral')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `integral` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','pv')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `pv` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','vipstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `vipstatus` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','cateid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `cateid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','settlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `settlementmoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','onedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `onedismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','twodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `twodismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','threedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `threedismoney` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','orderinfo')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `orderinfo` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','isdistri')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `isdistri` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','falseorder')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `falseorder` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','specialid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `specialid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','bgmusic')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `bgmusic` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','vipsettlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `vipsettlementmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','viponedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `viponedismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','viptwodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `viptwodismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','vipthreedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `vipthreedismoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','optionstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `optionstatus` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','userlabel')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `userlabel` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','creditmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `creditmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','sharemoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `sharemoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','sharestatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `sharestatus` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','independent')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `independent` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','allowapplyre')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `allowapplyre` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','usestatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `usestatus` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','expressid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `expressid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','fastpay')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `fastpay` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','overrefund')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `overrefund` tinyint(1) NOT NULL COMMENT '1=开启过期退款'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','level')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `level` text NOT NULL COMMENT '适用会员等级'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_activity','dissettime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_activity')." ADD   `dissettime` int(11) NOT NULL COMMENT '分销佣金结算时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_rush_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `aid` int(11) NOT NULL DEFAULT '0',
  `sort` int(11) DEFAULT '0' COMMENT '抢购商品分类排序，从大到小',
  `thumb` varchar(255) DEFAULT NULL COMMENT '抢购商品分类图片',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_rush_category','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_category')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_rush_category','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_category')." ADD   `uniacid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_category','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_category')." ADD   `name` varchar(255) NOT NULL DEFAULT ''");}
if(!pdo_fieldexists('ims_wlmerchant_rush_category','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_category')." ADD   `aid` int(11) NOT NULL DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_category','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_category')." ADD   `sort` int(11) DEFAULT '0' COMMENT '抢购商品分类排序，从大到小'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_category','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_category')." ADD   `thumb` varchar(255) DEFAULT NULL COMMENT '抢购商品分类图片'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_category','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_category')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_rush_category','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_category')." ADD   KEY `idx_uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_rush_follows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL,
  `actid` int(11) DEFAULT NULL,
  `sendtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adx_uniacid` (`uniacid`),
  KEY `adx_aid` (`aid`),
  KEY `adx_sendtime` (`sendtime`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_rush_follows','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_follows')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_rush_follows','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_follows')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_follows','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_follows')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_follows','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_follows')." ADD   `mid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_follows','actid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_follows')." ADD   `actid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_follows','sendtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_follows')." ADD   `sendtime` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_follows','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_follows')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_rush_follows','adx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_follows')." ADD   KEY `adx_uniacid` (`uniacid`)");}
if(!pdo_fieldexists('ims_wlmerchant_rush_follows','adx_aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_follows')." ADD   KEY `adx_aid` (`aid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_rush_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `unionid` varchar(145) DEFAULT NULL COMMENT '用户微信id',
  `openid` varchar(225) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL COMMENT '会员ID',
  `aid` int(11) DEFAULT NULL COMMENT '代理id',
  `sid` int(11) DEFAULT NULL COMMENT '商家id',
  `activityid` int(11) DEFAULT NULL COMMENT '活动id',
  `status` int(11) DEFAULT NULL COMMENT '0未支付1已支付2已消费',
  `orderno` varchar(145) DEFAULT NULL COMMENT '订单号',
  `transid` varchar(145) DEFAULT NULL COMMENT '微信支付ID',
  `price` decimal(10,2) DEFAULT NULL COMMENT '实际支付金额',
  `mobile` varchar(145) DEFAULT NULL COMMENT '电话',
  `num` int(11) DEFAULT NULL COMMENT '抢购数量',
  `actualprice` decimal(10,2) DEFAULT NULL COMMENT '实际支付',
  `goodscode` varchar(145) DEFAULT NULL COMMENT '商品编号',
  `paytime` varchar(145) DEFAULT NULL COMMENT '支付时间',
  `paytype` int(2) DEFAULT NULL COMMENT '支付方式 1余额 2微信 3支付宝 4货到付款',
  `checkcode` varchar(145) DEFAULT NULL COMMENT '核销码',
  `createtime` varchar(225) DEFAULT NULL COMMENT '创建时间',
  `adminremark` text COMMENT '卖家备注',
  `verfmid` int(11) NOT NULL,
  `verftime` int(11) NOT NULL,
  `issettlement` int(11) DEFAULT '0',
  `cutoffnotice` int(11) NOT NULL,
  `applytime` varchar(145) DEFAULT NULL,
  `refundtime` varchar(145) DEFAULT NULL,
  `applyrefund` int(11) DEFAULT '0',
  `falsename` varchar(145) DEFAULT NULL,
  `falseavatar` varchar(145) DEFAULT NULL,
  `disorderid` int(11) DEFAULT '0',
  `username` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `usetimes` int(11) NOT NULL,
  `usedtime` text NOT NULL,
  `vipbuyflag` int(11) NOT NULL,
  `optionid` int(11) NOT NULL,
  `dkcredit` int(11) NOT NULL,
  `dkmoney` decimal(10,2) NOT NULL,
  `overtime` int(11) NOT NULL,
  `paidprid` int(11) NOT NULL,
  `shareid` int(11) NOT NULL,
  `settlementmoney` decimal(10,2) NOT NULL,
  `estimatetime` int(11) NOT NULL,
  `changedispatchprice` decimal(10,2) NOT NULL,
  `changeprice` decimal(10,2) NOT NULL,
  `failtimes` int(11) NOT NULL,
  `expressid` int(11) NOT NULL,
  `remark` text NOT NULL,
  `vip_card_id` int(11) DEFAULT NULL COMMENT '储存用户在购买当前商品时开通的会员卡的id',
  `originalprice` decimal(10,2) NOT NULL,
  `redisstatus` tinyint(1) NOT NULL,
  `neworderflag` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_rush_order','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','unionid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `unionid` varchar(145) DEFAULT NULL COMMENT '用户微信id'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','openid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `openid` varchar(225) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `mid` int(11) DEFAULT NULL COMMENT '会员ID'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `aid` int(11) DEFAULT NULL COMMENT '代理id'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `sid` int(11) DEFAULT NULL COMMENT '商家id'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','activityid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `activityid` int(11) DEFAULT NULL COMMENT '活动id'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `status` int(11) DEFAULT NULL COMMENT '0未支付1已支付2已消费'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','orderno')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `orderno` varchar(145) DEFAULT NULL COMMENT '订单号'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','transid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `transid` varchar(145) DEFAULT NULL COMMENT '微信支付ID'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `price` decimal(10,2) DEFAULT NULL COMMENT '实际支付金额'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','mobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `mobile` varchar(145) DEFAULT NULL COMMENT '电话'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','num')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `num` int(11) DEFAULT NULL COMMENT '抢购数量'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','actualprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `actualprice` decimal(10,2) DEFAULT NULL COMMENT '实际支付'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','goodscode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `goodscode` varchar(145) DEFAULT NULL COMMENT '商品编号'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','paytime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `paytime` varchar(145) DEFAULT NULL COMMENT '支付时间'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','paytype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `paytype` int(2) DEFAULT NULL COMMENT '支付方式 1余额 2微信 3支付宝 4货到付款'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','checkcode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `checkcode` varchar(145) DEFAULT NULL COMMENT '核销码'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `createtime` varchar(225) DEFAULT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','adminremark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `adminremark` text COMMENT '卖家备注'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','verfmid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `verfmid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','verftime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `verftime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','issettlement')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `issettlement` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','cutoffnotice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `cutoffnotice` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','applytime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `applytime` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','refundtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `refundtime` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','applyrefund')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `applyrefund` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','falsename')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `falsename` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','falseavatar')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `falseavatar` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','disorderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `disorderid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','username')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `username` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','address')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `address` varchar(255) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','usetimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `usetimes` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','usedtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `usedtime` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','vipbuyflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `vipbuyflag` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','optionid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `optionid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','dkcredit')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `dkcredit` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','dkmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `dkmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','overtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `overtime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','paidprid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `paidprid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','shareid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `shareid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','settlementmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `settlementmoney` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','estimatetime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `estimatetime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','changedispatchprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `changedispatchprice` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','changeprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `changeprice` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','failtimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `failtimes` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','expressid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `expressid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `remark` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','vip_card_id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `vip_card_id` int(11) DEFAULT NULL COMMENT '储存用户在购买当前商品时开通的会员卡的id'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','originalprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `originalprice` decimal(10,2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','redisstatus')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `redisstatus` tinyint(1) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_order','neworderflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_order')." ADD   `neworderflag` tinyint(1) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_rush_special` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `title` varchar(125) NOT NULL COMMENT '题目',
  `thumb` varchar(125) NOT NULL COMMENT '顶部图片',
  `share_title` varchar(255) NOT NULL COMMENT '分享标题',
  `share_desc` varchar(255) NOT NULL COMMENT '分享详情',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `rule` text NOT NULL COMMENT '专题规则',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_rush_special','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_special')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_rush_special','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_special')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_special','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_special')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_rush_special','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_special')." ADD   `title` varchar(125) NOT NULL COMMENT '题目'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_special','thumb')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_special')." ADD   `thumb` varchar(125) NOT NULL COMMENT '顶部图片'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_special','share_title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_special')." ADD   `share_title` varchar(255) NOT NULL COMMENT '分享标题'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_special','share_desc')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_special')." ADD   `share_desc` varchar(255) NOT NULL COMMENT '分享详情'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_special','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_special')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_special','rule')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_special')." ADD   `rule` text NOT NULL COMMENT '专题规则'");}
if(!pdo_fieldexists('ims_wlmerchant_rush_special','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_rush_special')." ADD   PRIMARY KEY (`id`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_setting','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_setting')." ADD 
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_setting','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_setting')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_setting','key')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_setting')." ADD   `key` varchar(64) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_setting','value')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_setting')." ADD   `value` longtext NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_settlement_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `sid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0' COMMENT '-1系统审核不通过-2代理审核不通过1代理审核中2系统审核中，3系统审核通过，待结算,4已结算给代理,5已结算到商家',
  `type` int(11) DEFAULT '0' COMMENT '1商家售卖金额提现申请2代理VIP开通金额申请3代理五折卡开通金额申请',
  `sapplymoney` decimal(10,2) DEFAULT '0.00' COMMENT '商家申请结算金额',
  `aapplymoney` decimal(10,2) DEFAULT '0.00' COMMENT '代理申请金额',
  `sgetmoney` decimal(10,2) DEFAULT '0.00' COMMENT '商家实际得到金额',
  `agetmoney` decimal(10,2) DEFAULT '0.00' COMMENT '代理实际得到金额',
  `spercentmoney` decimal(10,2) DEFAULT '0.00' COMMENT '商家缴纳佣金',
  `apercentmoney` decimal(10,2) DEFAULT '0.00' COMMENT '代理缴纳佣金',
  `spercent` decimal(10,4) DEFAULT '0.0000' COMMENT '商家给代理的抽成比例',
  `apercent` decimal(10,4) DEFAULT '0.0000' COMMENT '代理给系统的抽成比例',
  `applytime` varchar(145) DEFAULT NULL COMMENT '申请时间',
  `updatetime` varchar(145) DEFAULT NULL COMMENT '最后操作时间',
  `settletype` int(11) DEFAULT '0' COMMENT '1手动结算2微信钱包',
  `ids` text COMMENT '申请结算的订单id集',
  `ordernum` int(11) DEFAULT NULL COMMENT '结算订单数',
  `sopenid` varchar(145) DEFAULT NULL,
  `aopenid` varchar(145) DEFAULT NULL,
  `type2` int(11) DEFAULT '0',
  `trade_no` varchar(45) NOT NULL,
  `payment_type` tinyint(1) DEFAULT NULL COMMENT '代理商收款方式(1=支付宝，2=微信，3=银行卡,4=余额[仅分销商有余额打款])',
  `mid` int(11) DEFAULT NULL COMMENT '申请提现的分销商的id',
  `disid` int(11) DEFAULT NULL COMMENT '分销提现申请人的代理商ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='商家向代理提出结算申请记录';

");

if(!pdo_fieldexists('ims_wlmerchant_settlement_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `sid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `status` int(11) DEFAULT '0' COMMENT '-1系统审核不通过-2代理审核不通过1代理审核中2系统审核中，3系统审核通过，待结算,4已结算给代理,5已结算到商家'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `type` int(11) DEFAULT '0' COMMENT '1商家售卖金额提现申请2代理VIP开通金额申请3代理五折卡开通金额申请'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','sapplymoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `sapplymoney` decimal(10,2) DEFAULT '0.00' COMMENT '商家申请结算金额'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','aapplymoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `aapplymoney` decimal(10,2) DEFAULT '0.00' COMMENT '代理申请金额'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','sgetmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `sgetmoney` decimal(10,2) DEFAULT '0.00' COMMENT '商家实际得到金额'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','agetmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `agetmoney` decimal(10,2) DEFAULT '0.00' COMMENT '代理实际得到金额'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','spercentmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `spercentmoney` decimal(10,2) DEFAULT '0.00' COMMENT '商家缴纳佣金'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','apercentmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `apercentmoney` decimal(10,2) DEFAULT '0.00' COMMENT '代理缴纳佣金'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','spercent')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `spercent` decimal(10,4) DEFAULT '0.0000' COMMENT '商家给代理的抽成比例'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','apercent')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `apercent` decimal(10,4) DEFAULT '0.0000' COMMENT '代理给系统的抽成比例'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','applytime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `applytime` varchar(145) DEFAULT NULL COMMENT '申请时间'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','updatetime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `updatetime` varchar(145) DEFAULT NULL COMMENT '最后操作时间'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','settletype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `settletype` int(11) DEFAULT '0' COMMENT '1手动结算2微信钱包'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','ids')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `ids` text COMMENT '申请结算的订单id集'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','ordernum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `ordernum` int(11) DEFAULT NULL COMMENT '结算订单数'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','sopenid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `sopenid` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','aopenid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `aopenid` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','type2')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `type2` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','trade_no')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `trade_no` varchar(45) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','payment_type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `payment_type` tinyint(1) DEFAULT NULL COMMENT '代理商收款方式(1=支付宝，2=微信，3=银行卡,4=余额[仅分销商有余额打款])'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `mid` int(11) DEFAULT NULL COMMENT '申请提现的分销商的id'");}
if(!pdo_fieldexists('ims_wlmerchant_settlement_record','disid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_settlement_record')." ADD   `disid` int(11) DEFAULT NULL COMMENT '分销提现申请人的代理商ID'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_signmember` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `mid` int(11) NOT NULL COMMENT '用户id',
  `nickname` varchar(32) DEFAULT NULL COMMENT '用户昵称',
  `avatar` varchar(445) DEFAULT NULL COMMENT '用户头像',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `times` int(11) DEFAULT NULL COMMENT '签到次数',
  `integral` int(11) DEFAULT NULL COMMENT '积分',
  `record` text COMMENT '详细记录',
  `totaltimes` int(11) DEFAULT NULL COMMENT '累计活动次数',
  `total` text COMMENT '累计记录',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=268 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_signmember','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signmember')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_signmember','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signmember')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_signmember','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signmember')." ADD   `mid` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_wlmerchant_signmember','nickname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signmember')." ADD   `nickname` varchar(32) DEFAULT NULL COMMENT '用户昵称'");}
if(!pdo_fieldexists('ims_wlmerchant_signmember','avatar')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signmember')." ADD   `avatar` varchar(445) DEFAULT NULL COMMENT '用户头像'");}
if(!pdo_fieldexists('ims_wlmerchant_signmember','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signmember')." ADD   `createtime` int(11) DEFAULT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_signmember','times')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signmember')." ADD   `times` int(11) DEFAULT NULL COMMENT '签到次数'");}
if(!pdo_fieldexists('ims_wlmerchant_signmember','integral')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signmember')." ADD   `integral` int(11) DEFAULT NULL COMMENT '积分'");}
if(!pdo_fieldexists('ims_wlmerchant_signmember','record')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signmember')." ADD   `record` text COMMENT '详细记录'");}
if(!pdo_fieldexists('ims_wlmerchant_signmember','totaltimes')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signmember')." ADD   `totaltimes` int(11) DEFAULT NULL COMMENT '累计活动次数'");}
if(!pdo_fieldexists('ims_wlmerchant_signmember','total')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signmember')." ADD   `total` text COMMENT '累计记录'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_signreceive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL COMMENT '用户ID',
  `date` int(11) DEFAULT NULL COMMENT '月份',
  `total` int(11) DEFAULT NULL COMMENT '累计天数',
  `reward` int(11) DEFAULT NULL COMMENT '奖励',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_signreceive','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signreceive')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_signreceive','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signreceive')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_signreceive','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signreceive')." ADD   `mid` int(11) DEFAULT NULL COMMENT '用户ID'");}
if(!pdo_fieldexists('ims_wlmerchant_signreceive','date')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signreceive')." ADD   `date` int(11) DEFAULT NULL COMMENT '月份'");}
if(!pdo_fieldexists('ims_wlmerchant_signreceive','total')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signreceive')." ADD   `total` int(11) DEFAULT NULL COMMENT '累计天数'");}
if(!pdo_fieldexists('ims_wlmerchant_signreceive','reward')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signreceive')." ADD   `reward` int(11) DEFAULT NULL COMMENT '奖励'");}
if(!pdo_fieldexists('ims_wlmerchant_signreceive','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signreceive')." ADD   `createtime` int(11) DEFAULT NULL COMMENT '创建时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_signrecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL,
  `date` int(11) DEFAULT NULL COMMENT '签到日期',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `reward` int(11) DEFAULT NULL COMMENT '获取积分',
  `sign_class` varchar(20) NOT NULL DEFAULT '日常签到' COMMENT '签到类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=319 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_signrecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signrecord')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_signrecord','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signrecord')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_signrecord','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signrecord')." ADD   `mid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_signrecord','date')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signrecord')." ADD   `date` int(11) DEFAULT NULL COMMENT '签到日期'");}
if(!pdo_fieldexists('ims_wlmerchant_signrecord','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signrecord')." ADD   `createtime` int(11) DEFAULT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_signrecord','reward')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signrecord')." ADD   `reward` int(11) DEFAULT NULL COMMENT '获取积分'");}
if(!pdo_fieldexists('ims_wlmerchant_signrecord','sign_class')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_signrecord')." ADD   `sign_class` varchar(20) NOT NULL DEFAULT '日常签到' COMMENT '签到类型'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_smallorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `mid` int(11) NOT NULL COMMENT '用户mid',
  `aid` int(11) NOT NULL COMMENT '代理id\n',
  `sid` int(11) NOT NULL COMMENT '商户id',
  `status` int(11) NOT NULL COMMENT '状态 1待使用 2已结算 3已退款',
  `plugin` varchar(50) NOT NULL COMMENT '插件名称',
  `orderid` int(11) NOT NULL COMMENT '母订单id',
  `orderno` varchar(50) NOT NULL COMMENT '母订单编号',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `hexiaotime` int(11) NOT NULL COMMENT '核销时间',
  `hexiaotype` int(11) NOT NULL COMMENT '核销方式',
  `hxuid` int(11) NOT NULL COMMENT '核销员user表id',
  `settletime` int(11) NOT NULL COMMENT '结算时间',
  `checkcode` varchar(50) NOT NULL COMMENT '核销码',
  `orderprice` decimal(10,2) NOT NULL COMMENT '订单支付金额',
  `settlemoney` decimal(10,2) NOT NULL COMMENT '结算金额',
  `disorderid` int(11) NOT NULL COMMENT '分销订单id',
  `oneleadid` int(11) NOT NULL COMMENT '一级分销商id',
  `twoleadid` int(11) NOT NULL COMMENT '二级分销商id',
  `onedismoney` decimal(10,2) NOT NULL COMMENT '一级分销商佣金',
  `twodismoney` decimal(10,2) NOT NULL COMMENT '二级分销商佣金',
  `dissettletime` int(11) NOT NULL COMMENT '分销结算时间',
  `refundtime` int(11) NOT NULL COMMENT '退款时间',
  PRIMARY KEY (`id`),
  KEY `idx_orderid` (`orderid`) USING BTREE,
  KEY `idx_checkcode` (`checkcode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='小订单';

");

if(!pdo_fieldexists('ims_wlmerchant_smallorder','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号id'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `mid` int(11) NOT NULL COMMENT '用户mid'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `aid` int(11) NOT NULL COMMENT '代理id\n'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `sid` int(11) NOT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `status` int(11) NOT NULL COMMENT '状态 1待使用 2已结算 3已退款'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','plugin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `plugin` varchar(50) NOT NULL COMMENT '插件名称'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `orderid` int(11) NOT NULL COMMENT '母订单id'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','orderno')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `orderno` varchar(50) NOT NULL COMMENT '母订单编号'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','hexiaotime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `hexiaotime` int(11) NOT NULL COMMENT '核销时间'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','hexiaotype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `hexiaotype` int(11) NOT NULL COMMENT '核销方式'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','hxuid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `hxuid` int(11) NOT NULL COMMENT '核销员user表id'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','settletime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `settletime` int(11) NOT NULL COMMENT '结算时间'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','checkcode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `checkcode` varchar(50) NOT NULL COMMENT '核销码'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','orderprice')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `orderprice` decimal(10,2) NOT NULL COMMENT '订单支付金额'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','settlemoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `settlemoney` decimal(10,2) NOT NULL COMMENT '结算金额'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','disorderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `disorderid` int(11) NOT NULL COMMENT '分销订单id'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','oneleadid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `oneleadid` int(11) NOT NULL COMMENT '一级分销商id'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','twoleadid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `twoleadid` int(11) NOT NULL COMMENT '二级分销商id'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','onedismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `onedismoney` decimal(10,2) NOT NULL COMMENT '一级分销商佣金'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','twodismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `twodismoney` decimal(10,2) NOT NULL COMMENT '二级分销商佣金'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','dissettletime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `dissettletime` int(11) NOT NULL COMMENT '分销结算时间'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','refundtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   `refundtime` int(11) NOT NULL COMMENT '退款时间'");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_smallorder','idx_orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smallorder')." ADD   KEY `idx_orderid` (`orderid`) USING BTREE");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_smstpl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(32) NOT NULL,
  `smstplid` varchar(32) NOT NULL,
  `data` text NOT NULL,
  `status` smallint(2) NOT NULL,
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_smstpl','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smstpl')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_smstpl','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smstpl')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_smstpl','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smstpl')." ADD   `name` varchar(50) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_smstpl','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smstpl')." ADD   `type` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_smstpl','smstplid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smstpl')." ADD   `smstplid` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_smstpl','data')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smstpl')." ADD   `data` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_smstpl','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smstpl')." ADD   `status` smallint(2) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_smstpl','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_smstpl')." ADD   `createtime` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_store_dynamic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `sid` int(11) NOT NULL COMMENT '商户id',
  `mid` int(11) NOT NULL COMMENT '发布人id',
  `status` int(11) NOT NULL COMMENT '状态 0待审核 1审核通过 2已推送',
  `content` varchar(225) NOT NULL COMMENT '内容',
  `imgs` text NOT NULL COMMENT '图集',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `passtime` int(11) NOT NULL COMMENT '审核时间',
  `sendtime` int(11) NOT NULL COMMENT '发送时间',
  `successnum` int(11) NOT NULL COMMENT '成功人数',
  `supportlist` text NOT NULL COMMENT '点赞列表',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD   `sid` int(11) NOT NULL COMMENT '商户id'");}
if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD   `mid` int(11) NOT NULL COMMENT '发布人id'");}
if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD   `status` int(11) NOT NULL COMMENT '状态 0待审核 1审核通过 2已推送'");}
if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','content')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD   `content` varchar(225) NOT NULL COMMENT '内容'");}
if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','imgs')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD   `imgs` text NOT NULL COMMENT '图集'");}
if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','passtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD   `passtime` int(11) NOT NULL COMMENT '审核时间'");}
if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','sendtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD   `sendtime` int(11) NOT NULL COMMENT '发送时间'");}
if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','successnum')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD   `successnum` int(11) NOT NULL COMMENT '成功人数'");}
if(!pdo_fieldexists('ims_wlmerchant_store_dynamic','supportlist')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_store_dynamic')." ADD   `supportlist` text NOT NULL COMMENT '点赞列表'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_storefans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `source` int(2) NOT NULL COMMENT '1收藏店铺2挪车卡绑定3店铺二维码',
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_sid` (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_storefans','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storefans')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_storefans','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storefans')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_storefans','sid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storefans')." ADD   `sid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_storefans','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storefans')." ADD   `mid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_storefans','source')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storefans')." ADD   `source` int(2) NOT NULL COMMENT '1收藏店铺2挪车卡绑定3店铺二维码'");}
if(!pdo_fieldexists('ims_wlmerchant_storefans','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storefans')." ADD   `createtime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_storefans','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storefans')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_storefans','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storefans')." ADD   KEY `idx_uniacid` (`uniacid`)");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_storeusers_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `package` varchar(5000) NOT NULL,
  `isdefault` int(2) unsigned NOT NULL,
  `enabled` int(2) unsigned NOT NULL,
  `createtime` int(11) unsigned NOT NULL,
  `aid` int(11) NOT NULL,
  `authority` text NOT NULL,
  `chargeid` int(11) DEFAULT '0',
  `defaultrate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_storeusers_group','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storeusers_group')." ADD 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_storeusers_group','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storeusers_group')." ADD   `uniacid` int(10) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_storeusers_group','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storeusers_group')." ADD   `name` varchar(50) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_storeusers_group','package')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storeusers_group')." ADD   `package` varchar(5000) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_storeusers_group','isdefault')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storeusers_group')." ADD   `isdefault` int(2) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_storeusers_group','enabled')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storeusers_group')." ADD   `enabled` int(2) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_storeusers_group','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storeusers_group')." ADD   `createtime` int(11) unsigned NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_storeusers_group','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storeusers_group')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_storeusers_group','authority')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storeusers_group')." ADD   `authority` text NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_storeusers_group','chargeid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storeusers_group')." ADD   `chargeid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_storeusers_group','defaultrate')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_storeusers_group')." ADD   `defaultrate` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `title` varchar(145) NOT NULL COMMENT '标签内容',
  `type` int(11) NOT NULL COMMENT '标签类型',
  `enabled` int(11) NOT NULL COMMENT '是否显示',
  `sort` int(11) NOT NULL COMMENT '排序',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_tags','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_tags')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_tags','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_tags')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_tags','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_tags')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_tags','title')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_tags')." ADD   `title` varchar(145) NOT NULL COMMENT '标签内容'");}
if(!pdo_fieldexists('ims_wlmerchant_tags','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_tags')." ADD   `type` int(11) NOT NULL COMMENT '标签类型'");}
if(!pdo_fieldexists('ims_wlmerchant_tags','enabled')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_tags')." ADD   `enabled` int(11) NOT NULL COMMENT '是否显示'");}
if(!pdo_fieldexists('ims_wlmerchant_tags','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_tags')." ADD   `sort` int(11) NOT NULL COMMENT '排序'");}
if(!pdo_fieldexists('ims_wlmerchant_tags','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_tags')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_timecardrecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `mid` int(11) NOT NULL COMMENT '用户id',
  `cardid` int(11) NOT NULL COMMENT '一卡通id',
  `activeid` int(11) NOT NULL COMMENT '活动id',
  `merchantid` int(11) NOT NULL COMMENT '店铺id',
  `freeflag` tinyint(2) NOT NULL COMMENT '免费标记',
  `ordermoney` decimal(10,2) NOT NULL COMMENT '订单金额',
  `realmoney` decimal(10,2) NOT NULL COMMENT '实际支付金额',
  `verfmid` int(11) NOT NULL COMMENT '操作店员id',
  `usetime` int(11) NOT NULL COMMENT '使用时间',
  `type` int(11) NOT NULL,
  `createtime` int(11) NOT NULL,
  `commentflag` int(11) NOT NULL,
  `discount` decimal(10,1) NOT NULL COMMENT '折扣比例',
  `undismoney` decimal(10,2) NOT NULL COMMENT '不可优惠金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `mid` int(11) NOT NULL COMMENT '用户id'");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','cardid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `cardid` int(11) NOT NULL COMMENT '一卡通id'");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','activeid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `activeid` int(11) NOT NULL COMMENT '活动id'");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','merchantid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `merchantid` int(11) NOT NULL COMMENT '店铺id'");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','freeflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `freeflag` tinyint(2) NOT NULL COMMENT '免费标记'");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','ordermoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `ordermoney` decimal(10,2) NOT NULL COMMENT '订单金额'");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','realmoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `realmoney` decimal(10,2) NOT NULL COMMENT '实际支付金额'");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','verfmid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `verfmid` int(11) NOT NULL COMMENT '操作店员id'");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','usetime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `usetime` int(11) NOT NULL COMMENT '使用时间'");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `type` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `createtime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','commentflag')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `commentflag` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','discount')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `discount` decimal(10,1) NOT NULL COMMENT '折扣比例'");}
if(!pdo_fieldexists('ims_wlmerchant_timecardrecord','undismoney')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_timecardrecord')." ADD   `undismoney` decimal(10,2) NOT NULL COMMENT '不可优惠金额'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(145) DEFAULT '0.00',
  `uniacid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT '0',
  `days` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `type` int(11) DEFAULT NULL COMMENT '可生成类型ID',
  `tokentype` int(11) DEFAULT NULL COMMENT '邀请码类型1VIP2五折',
  `typename` varchar(145) DEFAULT NULL COMMENT '可生成类型名称',
  `status` int(11) DEFAULT '0' COMMENT '1使用中',
  `remark` text,
  `openid` varchar(145) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL,
  `createtime` int(11) DEFAULT '0',
  `levelid` int(11) NOT NULL,
  `give_price` decimal(10,0) DEFAULT NULL COMMENT '使用激活码时赠送的金额',
  `caraid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_token','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_token','number')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `number` varchar(145) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_token','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_token','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `aid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_token','days')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `days` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_token','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `price` decimal(10,2) DEFAULT '0.00'");}
if(!pdo_fieldexists('ims_wlmerchant_token','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `type` int(11) DEFAULT NULL COMMENT '可生成类型ID'");}
if(!pdo_fieldexists('ims_wlmerchant_token','tokentype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `tokentype` int(11) DEFAULT NULL COMMENT '邀请码类型1VIP2五折'");}
if(!pdo_fieldexists('ims_wlmerchant_token','typename')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `typename` varchar(145) DEFAULT NULL COMMENT '可生成类型名称'");}
if(!pdo_fieldexists('ims_wlmerchant_token','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `status` int(11) DEFAULT '0' COMMENT '1使用中'");}
if(!pdo_fieldexists('ims_wlmerchant_token','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `remark` text");}
if(!pdo_fieldexists('ims_wlmerchant_token','openid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `openid` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_token','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `mid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_token','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `createtime` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_token','levelid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `levelid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_token','give_price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `give_price` decimal(10,0) DEFAULT NULL COMMENT '使用激活码时赠送的金额'");}
if(!pdo_fieldexists('ims_wlmerchant_token','caraid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token')." ADD   `caraid` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_token_apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL COMMENT '申请指定类型激活码的id',
  `tokentype` int(11) DEFAULT NULL COMMENT '1VIP2五折',
  `num` int(11) DEFAULT NULL COMMENT '申请生成个数',
  `createtime` varchar(145) DEFAULT NULL COMMENT '申请时间',
  `status` int(11) DEFAULT NULL COMMENT '申请状态',
  `uniacid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_token_apply','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token_apply')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_token_apply','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token_apply')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_token_apply','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token_apply')." ADD   `type` int(11) DEFAULT NULL COMMENT '申请指定类型激活码的id'");}
if(!pdo_fieldexists('ims_wlmerchant_token_apply','tokentype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token_apply')." ADD   `tokentype` int(11) DEFAULT NULL COMMENT '1VIP2五折'");}
if(!pdo_fieldexists('ims_wlmerchant_token_apply','num')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token_apply')." ADD   `num` int(11) DEFAULT NULL COMMENT '申请生成个数'");}
if(!pdo_fieldexists('ims_wlmerchant_token_apply','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token_apply')." ADD   `createtime` varchar(145) DEFAULT NULL COMMENT '申请时间'");}
if(!pdo_fieldexists('ims_wlmerchant_token_apply','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token_apply')." ADD   `status` int(11) DEFAULT NULL COMMENT '申请状态'");}
if(!pdo_fieldexists('ims_wlmerchant_token_apply','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_token_apply')." ADD   `uniacid` int(11) DEFAULT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_userlabel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `name` varchar(124) NOT NULL,
  `sort` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_userlabel','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_userlabel','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_userlabel','name')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel')." ADD   `name` varchar(124) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_userlabel','sort')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel')." ADD   `sort` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_userlabel','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel')." ADD   `status` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_userlabel','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel')." ADD   `createtime` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_userlabel_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `labelid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `times` int(11) NOT NULL,
  `createtime` int(11) NOT NULL,
  `dotime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_userlabel_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel_record')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_userlabel_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel_record')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_userlabel_record','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel_record')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_userlabel_record','labelid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel_record')." ADD   `labelid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_userlabel_record','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel_record')." ADD   `mid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_userlabel_record','times')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel_record')." ADD   `times` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_userlabel_record','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel_record')." ADD   `createtime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_userlabel_record','dotime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_userlabel_record')." ADD   `dotime` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_verifrecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `storeid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `plugin` varchar(32) NOT NULL,
  `orderid` int(11) NOT NULL,
  `verifrcode` varchar(32) NOT NULL,
  `verifmid` int(11) NOT NULL,
  `verifnickname` varchar(100) NOT NULL,
  `verifmobile` varchar(32) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `createtime` int(11) NOT NULL,
  `type` int(11) DEFAULT '0',
  `num` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_verifrecord','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `uniacid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `aid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','storeid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `storeid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `mid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','plugin')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `plugin` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','orderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `orderid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','verifrcode')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `verifrcode` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','verifmid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `verifmid` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','verifnickname')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `verifnickname` varchar(100) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','verifmobile')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `verifmobile` varchar(32) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','remark')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `remark` varchar(100) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `createtime` int(11) NOT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','type')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `type` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_verifrecord','num')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_verifrecord')." ADD   `num` int(11) NOT NULL");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_vip_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `areaid` int(11) DEFAULT NULL,
  `aid` int(11) DEFAULT NULL,
  `openid` varchar(145) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '充值金额',
  `howlong` varchar(145) DEFAULT NULL COMMENT '充值VIP月数',
  `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间',
  `paytime` varchar(145) DEFAULT NULL COMMENT '充值时间',
  `orderno` varchar(145) DEFAULT NULL COMMENT '充值单号',
  `limittime` varchar(145) DEFAULT NULL COMMENT '下次会员到期时间',
  `status` int(11) DEFAULT '0' COMMENT '0未支付1已支付',
  `uniacid` int(11) DEFAULT NULL,
  `unionid` varchar(145) DEFAULT NULL,
  `paytype` int(11) DEFAULT NULL,
  `transid` varchar(145) DEFAULT NULL,
  `issettlement` int(11) DEFAULT '0',
  `typeid` int(11) DEFAULT '0',
  `is_half` int(11) DEFAULT '0',
  `disorderid` int(11) DEFAULT '0',
  `todistributor` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_vip_record','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','mid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `mid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','uid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `uid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','areaid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `areaid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','aid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `aid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','openid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `openid` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','price')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `price` decimal(10,2) DEFAULT '0.00' COMMENT '充值金额'");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','howlong')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `howlong` varchar(145) DEFAULT NULL COMMENT '充值VIP月数'");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `createtime` varchar(145) DEFAULT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','paytime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `paytime` varchar(145) DEFAULT NULL COMMENT '充值时间'");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','orderno')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `orderno` varchar(145) DEFAULT NULL COMMENT '充值单号'");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','limittime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `limittime` varchar(145) DEFAULT NULL COMMENT '下次会员到期时间'");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `status` int(11) DEFAULT '0' COMMENT '0未支付1已支付'");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','unionid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `unionid` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','paytype')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `paytype` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','transid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `transid` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','issettlement')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `issettlement` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','typeid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `typeid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','is_half')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `is_half` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','disorderid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `disorderid` int(11) DEFAULT '0'");}
if(!pdo_fieldexists('ims_wlmerchant_vip_record','todistributor')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_vip_record')." ADD   `todistributor` int(11) DEFAULT '0'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_waittask` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `value` text,
  `key` varchar(145) DEFAULT NULL,
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `dotime` int(11) NOT NULL COMMENT '操作时间',
  `finishtime` int(11) NOT NULL COMMENT '完成时间',
  `status` int(11) NOT NULL COMMENT '状态',
  `important` varchar(145) NOT NULL COMMENT '重要参数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_waittask','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_waittask')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_waittask','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_waittask')." ADD   `uniacid` int(11) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_waittask','value')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_waittask')." ADD   `value` text");}
if(!pdo_fieldexists('ims_wlmerchant_waittask','key')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_waittask')." ADD   `key` varchar(145) DEFAULT NULL");}
if(!pdo_fieldexists('ims_wlmerchant_waittask','createtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_waittask')." ADD   `createtime` int(11) NOT NULL COMMENT '创建时间'");}
if(!pdo_fieldexists('ims_wlmerchant_waittask','dotime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_waittask')." ADD   `dotime` int(11) NOT NULL COMMENT '操作时间'");}
if(!pdo_fieldexists('ims_wlmerchant_waittask','finishtime')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_waittask')." ADD   `finishtime` int(11) NOT NULL COMMENT '完成时间'");}
if(!pdo_fieldexists('ims_wlmerchant_waittask','status')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_waittask')." ADD   `status` int(11) NOT NULL COMMENT '状态'");}
if(!pdo_fieldexists('ims_wlmerchant_waittask','important')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_waittask')." ADD   `important` varchar(145) NOT NULL COMMENT '重要参数'");}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_wlmerchant_wxapp_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '公众号ID',
  `wxapp_uniacid` int(11) NOT NULL COMMENT '小程序ID',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_wxapp_uniacid` (`wxapp_uniacid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

");

if(!pdo_fieldexists('ims_wlmerchant_wxapp_relation','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_wxapp_relation')." ADD 
  `id` int(11) NOT NULL AUTO_INCREMENT");}
if(!pdo_fieldexists('ims_wlmerchant_wxapp_relation','uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_wxapp_relation')." ADD   `uniacid` int(11) NOT NULL COMMENT '公众号ID'");}
if(!pdo_fieldexists('ims_wlmerchant_wxapp_relation','wxapp_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_wxapp_relation')." ADD   `wxapp_uniacid` int(11) NOT NULL COMMENT '小程序ID'");}
if(!pdo_fieldexists('ims_wlmerchant_wxapp_relation','id')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_wxapp_relation')." ADD   PRIMARY KEY (`id`)");}
if(!pdo_fieldexists('ims_wlmerchant_wxapp_relation','idx_uniacid')) {pdo_query("ALTER TABLE ".tablename('ims_wlmerchant_wxapp_relation')." ADD   KEY `idx_uniacid` (`uniacid`)");}
