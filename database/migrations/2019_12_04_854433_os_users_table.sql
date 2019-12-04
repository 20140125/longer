-- ----------------------------
-- 2019-12-04 10:02:54 backup table start
-- ----------------------------
-- ----------------------------
-- Table structure for os_users
-- ----------------------------
DROP TABLE IF EXISTS `os_users`;
CREATE TABLE `os_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '用户名',
  `email` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '用户邮箱',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色Id',
  `ip_address` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ip地址',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '用户状态',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `password` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户密码',
  `salt` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '密码盐值',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '登陆标识',
  `phone_number` char(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '手机号',
  PRIMARY KEY (`id`),
  KEY `os_admin_users_username_email_index` (`username`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员';
-- ;
-- ----------------------------
-- Records of os_users
-- ----------------------------
INSERT INTO os_users VALUES ('1','admin','admin@qq.com','1','127.0.0.1','1','1559272310','1575267011','33e152072244d418213c89529080edd7','G3S4nrG1','63062514a14fbe5323d1d9da3dd2ba0b','110');
-- ----------------------------
-- 2019-12-04 10:02:54 backup table end
-- ----------------------------
