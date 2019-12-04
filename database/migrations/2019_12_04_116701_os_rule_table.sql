-- ----------------------------
-- 2019-12-04 10:01:59 backup table start
-- ----------------------------
-- ----------------------------
-- Table structure for os_rule
-- ----------------------------
DROP TABLE IF EXISTS `os_rule`;
CREATE TABLE `os_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '权限名称',
  `href` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '权限链接',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级Id',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '路径',
  `level` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '等级',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '权限状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='权限管理';
-- ;
-- ----------------------------
-- Records of os_rule
-- ----------------------------
INSERT INTO os_rule VALUES ('1','权限设置','/admin/auth/default','0','1','0','1');
INSERT INTO os_rule VALUES ('2','权限列表','/admin/auth/index','1','1-2','1','1');
INSERT INTO os_rule VALUES ('3','权限保存','/admin/auth/save','2','1-2-3','2','1');
INSERT INTO os_rule VALUES ('6','权限更新','/admin/auth/update','2','1-2-6','2','2');
INSERT INTO os_rule VALUES ('7','删除权限','/admin/auth/delete','2','1-2-7','2','2');
INSERT INTO os_rule VALUES ('8','管理员列表','/admin/user/index','1','1-8','1','1');
INSERT INTO os_rule VALUES ('11','管理员保存','/admin/user/save','8','1-8-11','2','2');
INSERT INTO os_rule VALUES ('12','管理员更新','/admin/user/update','8','1-8-12','2','2');
INSERT INTO os_rule VALUES ('13','管理员删除','/admin/user/delete','8','1-8-13','2','2');
INSERT INTO os_rule VALUES ('14','角色列表','/admin/role/index','1','1-14','1','1');
INSERT INTO os_rule VALUES ('17','角色保存','/admin/role/save','14','1-14-17','2','2');
INSERT INTO os_rule VALUES ('18','角色更新','/admin/role/update','14','1-14-18','2','2');
INSERT INTO os_rule VALUES ('19','角色删除','/admin/role/delete','14','1-14-19','2','2');
INSERT INTO os_rule VALUES ('20','文件管理','/admin/file/default','0','20','0','1');
INSERT INTO os_rule VALUES ('21','文件列表','/admin/file/index','20','20-21','1','1');
INSERT INTO os_rule VALUES ('22','文件权限','/admin/file/chmod','21','20-21-22','2','2');
INSERT INTO os_rule VALUES ('23','文件读取','/admin/file/read','21','20-21-23','2','2');
INSERT INTO os_rule VALUES ('24','文件压缩','/admin/file/gzip','21','20-21-24','2','2');
INSERT INTO os_rule VALUES ('25','文件解压','/admin/file/unzip','21','20-21-25','2','2');
INSERT INTO os_rule VALUES ('26','文件保存','/admin/file/save','21','20-21-26','2','2');
INSERT INTO os_rule VALUES ('27','文件删除','/admin/file/delete','21','20-21-27','2','2');
INSERT INTO os_rule VALUES ('28','文件下载','/admin/file/download','21','20-21-28','2','2');
INSERT INTO os_rule VALUES ('29','系统设置','/admin/system/default','0','29','0','1');
INSERT INTO os_rule VALUES ('30','数据表列表','/admin/database/index','29','29-30','1','1');
INSERT INTO os_rule VALUES ('31','日志列表','/admin/log/index','29','29-31','1','1');
INSERT INTO os_rule VALUES ('32','数据表备份','/admin/database/backup','30','29-30-32','2','2');
INSERT INTO os_rule VALUES ('33','删除日志','/admin/log/delete','31','29-31-33','2','2');
INSERT INTO os_rule VALUES ('34','文件上传','/admin/file/upload','21','20-21-34','2','2');
INSERT INTO os_rule VALUES ('38','项目管理','/admin/interface/default','0','38','0','1');
INSERT INTO os_rule VALUES ('39','项目列表','/admin/category/index','38','38-39','1','1');
INSERT INTO os_rule VALUES ('40','分类保存','/admin/category/save','39','38-39-40','2','2');
INSERT INTO os_rule VALUES ('41','分类更新','/admin/category/update','39','38-39-41','2','2');
INSERT INTO os_rule VALUES ('42','接口保存','/admin/api/save','39','38-39-42','2','2');
INSERT INTO os_rule VALUES ('43','接口更新','/admin/api/update','39','38-39-43','2','2');
INSERT INTO os_rule VALUES ('44','分类删除','/admin/category/delete','39','38-39-44','2','2');
INSERT INTO os_rule VALUES ('46','授权用户','/admin/oauth/index','1','1-46','1','1');
INSERT INTO os_rule VALUES ('47','文件重命名','/admin/file/rename','21','20-21-47','2','2');
INSERT INTO os_rule VALUES ('48','接口详情','/admin/api/index','39','38-39-48','2','2');
INSERT INTO os_rule VALUES ('49','文件更新','/admin/file/update','21','20-21-49','2','2');
INSERT INTO os_rule VALUES ('50','授权用户保存','/admin/oauth/save','46','1-46-50','2','2');
INSERT INTO os_rule VALUES ('51','授权用户更新','/admin/oauth/update','46','1-46-51','2','2');
INSERT INTO os_rule VALUES ('52','授权用户删除','/admin/oauth/delete','46','1-46-52','2','2');
INSERT INTO os_rule VALUES ('53','基础配置','/admin/config/index','29','29-53','1','1');
INSERT INTO os_rule VALUES ('54','配置保存','/admin/config/save','53','29-53-54','2','2');
INSERT INTO os_rule VALUES ('55','配置更新','/admin/config/update','53','29-53-55','2','2');
INSERT INTO os_rule VALUES ('56','配置删除','/admin/config/delete','53','29-53-56','2','2');
INSERT INTO os_rule VALUES ('57','配置值更新','/admin/config/updateVal','53','29-53-57','2','2');
INSERT INTO os_rule VALUES ('58','申请授权列表','/admin/req-rule/index','1','1-58','1','1');
INSERT INTO os_rule VALUES ('59','申请授权保存','/admin/req-rule/save','58','1-58-59','2','2');
INSERT INTO os_rule VALUES ('60','申请授权更新','/admin/req-rule/update','58','1-58-60','2','2');
INSERT INTO os_rule VALUES ('61','申请授权删除','/admin/req-rule/delete','58','1-58-61','2','2');
INSERT INTO os_rule VALUES ('62','数据表修复','/admin/database/repair','30','29-30-62','2','2');
INSERT INTO os_rule VALUES ('63','数据表优化','/admin/database/optimize','30','29-30-63','2','2');
INSERT INTO os_rule VALUES ('64','城市列表','/admin/area/index','29','29-64','1','1');
INSERT INTO os_rule VALUES ('65','站内通知','/admin/push/index','29','29-65','1','1');
INSERT INTO os_rule VALUES ('66','站内通知保存','/admin/push/save','65','29-65-66','2','2');
INSERT INTO os_rule VALUES ('67','站内通知更新','/admin/push/update','65','29-65-67','2','2');
INSERT INTO os_rule VALUES ('68','站内通知删除','/admin/push/delete','65','29-65-68','2','2');
INSERT INTO os_rule VALUES ('69','站内通知读取','/admin/push/see','65','29-65-69','2','2');
INSERT INTO os_rule VALUES ('70','个人中心','/admin/user/center','29','29-70','1','1');
INSERT INTO os_rule VALUES ('71','保存用户信息','/admin/center/save','70','29-70-71','2','2');
INSERT INTO os_rule VALUES ('72','系统日志','/admin/system/log','29','29-72','1','1');
INSERT INTO os_rule VALUES ('73','数据库修改','/admin/database/comment','30','29-30-73','2','2');
INSERT INTO os_rule VALUES ('74','聊天记录','/admin/chat/index','29','29-74','1','1');
INSERT INTO os_rule VALUES ('75','编辑器管理','/admin/editor/default','0','75','0','1');
INSERT INTO os_rule VALUES ('76','Markdown','/admin/editor/markdown','75','75-76','1','1');
INSERT INTO os_rule VALUES ('77','Quill','/admin/editor/quill','75','75-77','1','1');
INSERT INTO os_rule VALUES ('78','APIDoc','/admin/apidoc/index','38','38-78','1','1');
INSERT INTO os_rule VALUES ('79','APIDoc保存','/admin/apidoc/save','78','38-78-79','2','2');
INSERT INTO os_rule VALUES ('80','APIDoc更新','/admin/apidoc/update','78','38-78-80','2','2');
INSERT INTO os_rule VALUES ('81','APIDoc删除','/admin/apidoc/delete','78','38-78-81','2','2');
INSERT INTO os_rule VALUES ('85','组件管理','/admin/components/default','0','85','0','1');
INSERT INTO os_rule VALUES ('86','表格组件','/admin/example/table','85','85-86','1','1');
INSERT INTO os_rule VALUES ('87','表单组件','/admin/example/form','85','85-87','1','1');
-- ----------------------------
-- 2019-12-04 10:01:59 backup table end
-- ----------------------------
