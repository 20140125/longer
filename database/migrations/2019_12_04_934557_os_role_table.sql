-- ----------------------------
-- 2019-12-04 10:02:23 backup table start
-- ----------------------------
-- ----------------------------
-- Table structure for os_role
-- ----------------------------
DROP TABLE IF EXISTS `os_role`;
CREATE TABLE `os_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '角色名称',
  `auth_ids` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限ID',
  `auth_url` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限Url',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '角色状态',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `os_role_role_name_index` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色列表';
-- ;
-- ----------------------------
-- Records of os_role
-- ----------------------------
INSERT INTO os_role VALUES ('1','超级管理员','[3,4,9,10,15,16,35,36,37,45,1,14,17,18,19,2,5,6,7,46,50,51,52,58,59,60,61,8,11,12,13,20,21,22,23,24,25,26,27,28,34,47,49,29,30,32,31,33,53,54,55,56,57,38,39,40,41,42,43,44,48,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81]','["/admin/auth/default","/admin/auth/index","/admin/auth/save","/admin/auth/update","/admin/auth/delete","/admin/user/index","/admin/user/save","/admin/user/update","/admin/user/delete","/admin/role/index","/admin/role/save","/admin/role/update","/admin/role/delete","/admin/file/default","/admin/file/index","/admin/file/chmod","/admin/file/read","/admin/file/gzip","/admin/file/unzip","/admin/file/save","/admin/file/delete","/admin/file/download","/admin/system/default","/admin/database/index","/admin/log/index","/admin/database/backup","/admin/log/delete","/admin/file/upload","/admin/interface/default","/admin/category/index","/admin/category/save","/admin/category/update","/admin/api/save","/admin/api/update","/admin/category/delete","/admin/oauth/index","/admin/file/rename","/admin/api/index","/admin/file/update","/admin/oauth/save","/admin/oauth/update","/admin/oauth/delete","/admin/config/index","/admin/config/save","/admin/config/update","/admin/config/delete","/admin/config/updateval","/admin/req-rule/index","/admin/req-rule/save","/admin/req-rule/update","/admin/req-rule/delete","/admin/database/repair","/admin/database/optimize","/admin/area/index","/admin/push/index","/admin/push/save","/admin/push/update","/admin/push/delete","/admin/push/see","/admin/user/center","/admin/center/save","/admin/system/log","/admin/database/comment","/admin/chat/index","/admin/editor/default","/admin/editor/markdown","/admin/editor/quill","/admin/apidoc/index","/admin/apidoc/save","/admin/apidoc/update","/admin/apidoc/delete"]','1','1559563238','1574239636');
INSERT INTO os_role VALUES ('2','授权管理员','[35,36,37,45,1,2,8,14,46,51,58,59,60,61,20,21,23,29,30,62,63,65,68,38,42,43,48,39,40,41,31,33,69,70]','["/admin/auth/default","/admin/auth/index","/admin/user/index","/admin/role/index","/admin/file/default","/admin/file/index","/admin/file/read","/admin/system/default","/admin/database/index","/admin/log/index","/admin/log/delete","/admin/interface/default","/admin/category/index","/admin/category/save","/admin/category/update","/admin/api/save","/admin/api/update","/admin/oauth/index","/admin/api/index","/admin/oauth/update","/admin/req-rule/index","/admin/req-rule/save","/admin/req-rule/update","/admin/req-rule/delete","/admin/database/repair","/admin/database/optimize","/admin/push/index","/admin/push/delete","/admin/push/see","/admin/user/center"]','1','1563517517','1571818432');
INSERT INTO os_role VALUES ('3','龙哥','[35,36,37,45,1,2,8,14,46,51,58,59,60,61,20,21,23,29,30,62,63,65,68,38,42,43,48,39,40,41,31,33,69,70]','["/admin/auth/default","/admin/auth/index","/admin/user/index","/admin/role/index","/admin/file/default","/admin/file/index","/admin/file/read","/admin/system/default","/admin/database/index","/admin/log/index","/admin/log/delete","/admin/interface/default","/admin/category/index","/admin/category/save","/admin/category/update","/admin/api/save","/admin/api/update","/admin/oauth/index","/admin/api/index","/admin/oauth/update","/admin/req-rule/index","/admin/req-rule/save","/admin/req-rule/update","/admin/req-rule/delete","/admin/database/repair","/admin/database/optimize","/admin/push/index","/admin/push/delete","/admin/push/see","/admin/user/center"]','1','1573787562','1573787562');
-- ----------------------------
-- 2019-12-04 10:02:23 backup table end
-- ----------------------------
