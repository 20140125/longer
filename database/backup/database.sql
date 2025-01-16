-- os_api_category 表
CREATE TABLE `os_api_category` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL COMMENT '分类名称',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1-启用,0-禁用',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API分类表';

-- os_api_doc 表
CREATE TABLE `os_api_doc` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(100) NOT NULL COMMENT '文档标题',
`content` text NOT NULL COMMENT '文档内容',
`category_id` int(11) NOT NULL COMMENT '分类ID',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1-启用,0-禁用',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
KEY `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API文档表';

-- os_api_lists 表
CREATE TABLE `os_api_lists` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(100) NOT NULL COMMENT 'API名称',
`url` varchar(255) NOT NULL COMMENT 'API地址',
`method` varchar(10) NOT NULL COMMENT '请求方法',
`category_id` int(11) NOT NULL COMMENT '分类ID',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1-启用,0-禁用',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
KEY `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API列表';

-- os_api_log 表
CREATE TABLE `os_api_log` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`api_id` int(11) NOT NULL COMMENT 'API ID',
`user_id` int(11) NOT NULL COMMENT '用户ID',
`request_data` text COMMENT '请求数据',
`response_data` text COMMENT '响应数据',
`ip_address` varchar(50) NOT NULL COMMENT 'IP地址',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
KEY `idx_api` (`api_id`),
KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API调用日志';

-- os_china_area 表
CREATE TABLE `os_china_area` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(50) NOT NULL COMMENT '地区名称',
 `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID',
 `level` tinyint(1) NOT NULL COMMENT '层级:1-省,2-市,3-区县',
 `created_at` int(11) NOT NULL COMMENT '创建时间',
 `updated_at` int(11) NOT NULL COMMENT '更新时间',
 PRIMARY KEY (`id`),
 KEY `idx_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='中国地区表';

-- os_auth 表
CREATE TABLE `os_auth` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL COMMENT '权限名称',
`path` varchar(100) NOT NULL COMMENT '权限路径',
`parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1-启用,0-禁用',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
KEY `idx_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限表';

-- os_emotion 表
CREATE TABLE `os_emotion` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`type` varchar(20) NOT NULL COMMENT '表情类型',
`url` varchar(255) NOT NULL COMMENT '表情图片地址',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='表情包表';

-- os_oauth 表
-- 更新 os_oauth 表结构
CREATE TABLE `os_oauth` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联的用户ID',
`username` varchar(50) NOT NULL COMMENT '第三方用户名',
`openid` varchar(100) NOT NULL COMMENT '第三方用户唯一标识',
`avatar_url` varchar(255) DEFAULT NULL COMMENT '头像URL',
`access_token` varchar(255) NOT NULL COMMENT '访问令牌',
`refresh_token` varchar(255) DEFAULT NULL COMMENT '刷新令牌',
`oauth_type` varchar(20) NOT NULL COMMENT '授权类型:qq,github,gitee,weibo,baidu,os_china',
`url` varchar(255) DEFAULT NULL COMMENT '第三方用户主页',
`expires` int(11) NOT NULL COMMENT '令牌过期时间',
`remember_token` varchar(100) NOT NULL COMMENT '登录令牌',
`role_id` int(11) NOT NULL DEFAULT '2' COMMENT '角色ID',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1-启用,0-禁用',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
UNIQUE KEY `uk_openid_type` (`openid`,`oauth_type`),
KEY `idx_uid` (`user_id`),
KEY `idx_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='第三方登录授权表';

-- os_permission_apply 表
CREATE TABLE `os_permission_apply` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL COMMENT '用户ID',
`auth_id` int(11) NOT NULL COMMENT '权限ID',
`status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0-待审核,1-通过,2-拒绝',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
KEY `idx_user` (`user_id`),
                                       KEY `idx_auth` (`auth_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限申请表';

-- os_permission_apply_log 表
CREATE TABLE `os_permission_apply_log` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`apply_id` int(11) NOT NULL COMMENT '申请ID',
`operator_id` int(11) NOT NULL COMMENT '操作人ID',
`action` varchar(20) NOT NULL COMMENT '操作:approve-通过,reject-拒绝',
`remark` varchar(255) DEFAULT NULL COMMENT '备注',
`created_at` int(11) NOT NULL COMMENT '创建时间',
PRIMARY KEY (`id`),
KEY `idx_apply` (`apply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限申请日志';

-- os_push 表
CREATE TABLE `os_push` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(100) NOT NULL COMMENT '推送标题',
`content` text NOT NULL COMMENT '推送内容',
`type` varchar(20) NOT NULL COMMENT '推送类型',
`target` text NOT NULL COMMENT '推送目标',
`status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0-未推送,1-已推送',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='消息推送表';

-- os_role 表
CREATE TABLE `os_role` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL COMMENT '角色名称',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1-启用,0-禁用',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

-- os_send_email 表
CREATE TABLE `os_send_email` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `email` varchar(100) NOT NULL COMMENT '邮箱地址',
 `verify_code` varchar(10) NOT NULL COMMENT '验证码',
 `expire_time` int(11) NOT NULL COMMENT '过期时间',
 `created_at` int(11) NOT NULL COMMENT '创建时间',
 `updated_at` int(11) NOT NULL COMMENT '更新时间',
 PRIMARY KEY (`id`),
 KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邮件发送记录表';

-- os_image 表
CREATE TABLE `os_image` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(100) NOT NULL COMMENT '图片标题',
`url` varchar(255) NOT NULL COMMENT '图片地址',
`type_id` int(11) NOT NULL COMMENT '分类ID',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1-启用,0-禁用',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
KEY `idx_type` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Soogif图片表';

-- os_image_type 表
CREATE TABLE `os_image_type` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(50) NOT NULL COMMENT '分类名称',
 `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1-启用,0-禁用',
 `created_at` int(11) NOT NULL COMMENT '创建时间',
 `updated_at` int(11) NOT NULL COMMENT '更新时间',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Image分类表';

-- os_system_config 表
CREATE TABLE `os_system_config` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`key` varchar(50) NOT NULL COMMENT '配置键',
`value` text NOT NULL COMMENT '配置值',
`description` varchar(255) DEFAULT NULL COMMENT '配置描述',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
UNIQUE KEY `uk_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表';

-- os_timeline 表
CREATE TABLE `os_timeline` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`content` text NOT NULL COMMENT '内容',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='时间线表';

-- os_user_center 表
CREATE TABLE `os_user_center` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL COMMENT '用户ID',
`nickname` varchar(50) DEFAULT NULL COMMENT '昵称',
`avatar` varchar(255) DEFAULT NULL COMMENT '头像',
`gender` tinyint(1) DEFAULT '0' COMMENT '性别:0-未知,1-男,2-女',
`birthday` date DEFAULT NULL COMMENT '生日',
`signature` varchar(255) DEFAULT NULL COMMENT '个性签名',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
UNIQUE KEY `uk_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户中心表';

-- os_users 表
CREATE TABLE `os_users` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`username` varchar(50) NOT NULL COMMENT '用户名',
`password` varchar(32) NOT NULL COMMENT '密码',
`salt` varchar(8) NOT NULL COMMENT '密码盐',
`email` varchar(100) NOT NULL COMMENT '邮箱',
`phone_number` varchar(20) DEFAULT NULL COMMENT '手机号',
`avatar_url` varchar(255) DEFAULT NULL COMMENT '头像URL',
`remember_token` varchar(100) DEFAULT NULL COMMENT '记住令牌',
`role_id` int(11) NOT NULL DEFAULT '2' COMMENT '角色ID',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1-启用,0-禁用',
`uuid` varchar(32) DEFAULT NULL COMMENT 'UUID',
`ip_address` varchar(50) DEFAULT NULL COMMENT 'IP地址',
`created_at` int(11) NOT NULL COMMENT '创建时间',
`updated_at` int(11) NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
UNIQUE KEY `uk_email` (`email`),
UNIQUE KEY `uk_phone` (`phone_number`),
KEY `idx_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';
