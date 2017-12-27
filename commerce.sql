drop table if exists `shop_admin`;
create table if not exists `shop_admin`(
`adminid` int unsigned not null auto_increment comment '主键ID',
`adminuser` varchar(32) not null default '' comment '管理员账号',
`adminpassword` char(32) not null default '' comment '管理员密码',
`adminemail` varchar(50) not null default '' comment '管理员电子邮箱',
`logintime` int unsigned not null default '0' comment '登录时间',
`loginip` bigint not null default '0' comment '登录IP',
`createtime` int unsigned not null default '0' comment '创建时间',
primary key(`adminid`),
unique shop_admin_adminuser_adminpassword(`adminuser`,`adminpassword`),
unique shop_admin_adminuser_adminemail(`adminuser`,`adminemail`)
)engine=InnoDB default charset=utf8;

insert into `shop_admin`(adminuser,adminpassword,adminemail,createtime) values('admin',md5('123456'),'1026251951@qq.com',unix_timestamp());

drop table if exists `shop_user`;
create table if not exists `shop_user`(
`userid` bigint unsigned not null auto_increment,
`username` varchar(30) not null default '',
`userpassword` varchar(30) not null default '',
`useremail` varchar(50) not null default '',
`createtime` int unsigned not null default '0',
primary key(`userid`),
unique shop_username_userpassword(`username`,`userpassword`),
unique shop_useremail_userpassword(`userpassword`,`useremail`)
)engine=InnoDB default charset = utf8;

drop table if exists `shop_profile`;
create table if not exists `shop_profile`(
`id` bigint unsigned not null auto_increment,
`truename` varchar(10) not null default '',
`age` tinyint unsigned not null default '0',
`sex` ENUM('0','1','2') NOT NULL DEFAULT '0',
`birthday` DATE NOT NULL DEFAULT '2017-01-02',
`nickname` VARCHAR(32) NOT NULL DEFAULT '',
`company` VARCHAR(32) NOT NULL DEFAULT '',
`userid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
`createtime` INT UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY(`id`),
UNIQUE KEY shop_userid(`userid`)
)engine = InnoDB DEFAULT charset = utf8;

drop table if exists `shop_category`;
create table if not exists `shop_category`(
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
`title` VARCHAR(32) NOT NULL DEFAULT '',
`parentid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
`createtime` INT UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY(`id`),
KEY shop_parentid(`parentid`)
)ENGINE = InnoDB DEFAULT charset = utf8;

drop table if exists `shop_product`;
create table if not exists `shop_product`(
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
`cateid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
`title` VARCHAR(200) NOT NULL DEFAULT '',
`describe` TEXT,
`num` BIGINT UNSIGNED NOT NULL DEFAULT '0',
`price` DECIMAL(10,2) NOT NULL DEFAULT '00000000.00',
`cover` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '封面图片',
`pics` TEXT COMMENT '图床所有图片',
`issale` ENUM('0', '1') NOT NULL DEFAULT '0',
`ishot` ENUM('0', '1') NOT NULL DEFAULT '0',
`saleprice` DECIMAL(10,2) NOT NULL DEFAULT '00000000.00',
`createtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY(`id`),
KEY shop_product_cateid(`cateid`),
KEY shop_product_ison(`ison`)
)ENGINE = InnoDB DEFAULT charset = utf8;

drop table if exists `shop_cart`;
create table if not exists `shop_cart`(
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`productid` bigint unsigned NOT NULL DEFAULT '0',
`productnum` int unsigned NOT NULL DEFAULT '0',
`price` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
`userid` bigint unsigned NOT NULL DEFAULT '0',
`createtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY(`id`),
KEY shop_cart_productid(`productid`),
KEY shop_cart_userid(`userid`)
)ENGINE = InnoDB DEFAULT charset = utf8;


drop table if exists `shop_order`;
create table if not exists `shop_order`(
    `orderid` bigint unsigned NOT NULL AUTO_INCREMENT,
    `userid` bigint unsigned NOT NULL DEFAULT '0',
    `addressid` bigint unsigned NOT NULL DEFAULT '0',
    `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
    `status` tinyint unsigned NOT NULL DEFAULT '0',
    `expressid` int unsigned NOT NULL DEFAULT '0' COMMENT '快递id',
    `expressno` varchar(50) NOT NULL DEFAULT '' COMMENT '快递单号',
    `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatetime` timestamp NOT NULL DEFAULT '1970-01-02 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(`orderid`),
    KEY shop_order_userid(`userid`),
    KEY shop_order_addressid(`addressid`),
    KEY shop_order_expressid(`expressid`)
)ENGINE = InnoDB DEFAULT charset = utf8;


drop table if exists `shop_order_detail`;
create table if not exists `shop_order_detail`(
    `detailid` bigint unsigned NOT NULL AUTO_INCREMENT,
    `productid` bigint unsigned NOT NULL DEFAULT '0',
    `price` decimal(10,2) NOT NULL DEFAULT '0.00',
    `productnum` int unsigned NOT NULL DEFAULT '0',
    `orderid` bigint unsigned NOT NULL DEFAULT '0',
    `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`detailid`),
    KEY shop_order_detail_productid(`productid`),
    KEY shop_order_detail_orderid(`orderid`)
)ENGINE = InnoDB DEFAULT charset = utf8;


drop table if exists `shop_order_address`;
create table if not exists `shop_address`(
    `addressid` bigint unsigned NOT NULL AUTO_INCREMENT,
    `firstname` varchar(32) NOT NULL DEFAULT '',
    `lastname` varchar(32) NOT NULL DEFAULT '',
    `company` varchar(100) NOT NULL DEFAULT '',
    `address` TEXT,
    `postcode` char(6) NOT NULL DEFAULT '' COMMENT '邮政编码',
    `email` varchar(100) NOT NULL DEFAULT '',
    `telephone` varchar(20) NOT NULL DEFAULT '',
    `userid` bigint unsigned NOT NULL DEFAULT '0', 
    `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`addressid`),
    KEY shop_address_userid(`userid`)
)ENGINE = InnoDB DEFAULT charset = utf8;

