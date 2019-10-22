/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100138
 Source Host           : localhost:3306
 Source Schema         : longer

 Target Server Type    : MySQL
 Target Server Version : 100138
 File Encoding         : 65001

 Date: 22/10/2019 15:26:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for os_role
-- ----------------------------
DROP TABLE IF EXISTS `os_role`;
CREATE TABLE `os_role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '角色名称',
  `auth_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限ID',
  `auth_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限Url',
  `status` tinyint(3) NOT NULL DEFAULT 1 COMMENT '角色状态',
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `os_role_role_name_index`(`role_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '角色列表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of os_role
-- ----------------------------
INSERT INTO `os_role` VALUES (1, '超级管理员', '[3,4,9,10,15,16,35,36,37,45,1,14,17,18,19,2,5,6,7,46,50,51,52,58,59,60,61,8,11,12,13,20,21,22,23,24,25,26,27,28,34,47,49,29,30,32,31,33,53,54,55,56,57,38,39,40,41,42,43,44,48,62,63,64,65,66,67,68,69,70,71,72]', '[\"/admin/auth/default\",\"/admin/auth/index\",\"/admin/auth/save\",\"/admin/auth/update\",\"/admin/auth/delete\",\"/admin/user/index\",\"/admin/user/save\",\"/admin/user/update\",\"/admin/user/delete\",\"/admin/role/index\",\"/admin/role/save\",\"/admin/role/update\",\"/admin/role/delete\",\"/admin/file/default\",\"/admin/file/index\",\"/admin/file/chmod\",\"/admin/file/read\",\"/admin/file/gzip\",\"/admin/file/unzip\",\"/admin/file/save\",\"/admin/file/delete\",\"/admin/file/download\",\"/admin/system/default\",\"/admin/database/index\",\"/admin/log/index\",\"/admin/database/backup\",\"/admin/log/delete\",\"/admin/file/upload\",\"/admin/interface/default\",\"/admin/category/index\",\"/admin/category/save\",\"/admin/category/update\",\"/admin/api/save\",\"/admin/api/update\",\"/admin/category/delete\",\"/admin/oauth/index\",\"/admin/file/rename\",\"/admin/api/index\",\"/admin/file/update\",\"/admin/oauth/save\",\"/admin/oauth/update\",\"/admin/oauth/delete\",\"/admin/config/index\",\"/admin/config/save\",\"/admin/config/update\",\"/admin/config/delete\",\"/admin/config/updateval\",\"/admin/req-rule/index\",\"/admin/req-rule/save\",\"/admin/req-rule/update\",\"/admin/req-rule/delete\",\"/admin/database/repair\",\"/admin/database/optimize\",\"/admin/area/index\",\"/admin/push/index\",\"/admin/push/save\",\"/admin/push/update\",\"/admin/push/delete\",\"/admin/push/see\",\"/admin/user/center\",\"/admin/center/save\",\"/admin/system/log\"]', 1, 1559563238, 1571302594);
INSERT INTO `os_role` VALUES (2, '授权管理员', '[35,36,37,45,1,2,8,14,46,51,58,59,60,61,20,21,23,29,30,62,63,65,66,67,68,38,42,43,48,39,40,41,31,33,69,70]', '[\"/admin/auth/default\",\"/admin/auth/index\",\"/admin/user/index\",\"/admin/role/index\",\"/admin/file/default\",\"/admin/file/index\",\"/admin/file/read\",\"/admin/system/default\",\"/admin/database/index\",\"/admin/log/index\",\"/admin/log/delete\",\"/admin/interface/default\",\"/admin/category/index\",\"/admin/category/save\",\"/admin/category/update\",\"/admin/api/save\",\"/admin/api/update\",\"/admin/oauth/index\",\"/admin/api/index\",\"/admin/oauth/update\",\"/admin/req-rule/index\",\"/admin/req-rule/save\",\"/admin/req-rule/update\",\"/admin/req-rule/delete\",\"/admin/database/repair\",\"/admin/database/optimize\",\"/admin/push/index\",\"/admin/push/save\",\"/admin/push/update\",\"/admin/push/delete\",\"/admin/push/see\",\"/admin/user/center\"]', 1, 1563517517, 1569206868);

-- ----------------------------
-- Table structure for os_rule
-- ----------------------------
DROP TABLE IF EXISTS `os_rule`;
CREATE TABLE `os_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '权限名称',
  `href` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '权限链接',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级Id',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '路径',
  `level` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '等级',
  `status` tinyint(4) NULL DEFAULT 1 COMMENT '权限状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 73 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '权限管理' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of os_rule
-- ----------------------------
INSERT INTO `os_rule` VALUES (1, '权限设置', '/admin/auth/default', 0, '1', 0, 1);
INSERT INTO `os_rule` VALUES (2, '权限列表', '/admin/auth/index', 1, '1-2', 1, 1);
INSERT INTO `os_rule` VALUES (3, '权限保存', '/admin/auth/save', 2, '1-2-3', 2, 2);
INSERT INTO `os_rule` VALUES (6, '权限更新', '/admin/auth/update', 2, '1-2-6', 2, 2);
INSERT INTO `os_rule` VALUES (7, '删除权限', '/admin/auth/delete', 2, '1-2-7', 2, 2);
INSERT INTO `os_rule` VALUES (8, '管理员列表', '/admin/user/index', 1, '1-8', 1, 1);
INSERT INTO `os_rule` VALUES (11, '管理员保存', '/admin/user/save', 8, '1-8-11', 2, 2);
INSERT INTO `os_rule` VALUES (12, '管理员更新', '/admin/user/update', 8, '1-8-12', 2, 2);
INSERT INTO `os_rule` VALUES (13, '管理员删除', '/admin/user/delete', 8, '1-8-13', 2, 2);
INSERT INTO `os_rule` VALUES (14, '角色列表', '/admin/role/index', 1, '1-14', 1, 1);
INSERT INTO `os_rule` VALUES (17, '角色保存', '/admin/role/save', 14, '1-14-17', 2, 2);
INSERT INTO `os_rule` VALUES (18, '角色更新', '/admin/role/update', 14, '1-14-18', 2, 2);
INSERT INTO `os_rule` VALUES (19, '角色删除', '/admin/role/delete', 14, '1-14-19', 2, 2);
INSERT INTO `os_rule` VALUES (20, '文件管理', '/admin/file/default', 0, '20', 0, 1);
INSERT INTO `os_rule` VALUES (21, '文件列表', '/admin/file/index', 20, '20-21', 1, 1);
INSERT INTO `os_rule` VALUES (22, '文件权限', '/admin/file/chmod', 21, '20-21-22', 2, 2);
INSERT INTO `os_rule` VALUES (23, '文件读取', '/admin/file/read', 21, '20-21-23', 2, 2);
INSERT INTO `os_rule` VALUES (24, '文件压缩', '/admin/file/gzip', 21, '20-21-24', 2, 2);
INSERT INTO `os_rule` VALUES (25, '文件解压', '/admin/file/unzip', 21, '20-21-25', 2, 2);
INSERT INTO `os_rule` VALUES (26, '文件保存', '/admin/file/save', 21, '20-21-26', 2, 2);
INSERT INTO `os_rule` VALUES (27, '文件删除', '/admin/file/delete', 21, '20-21-27', 2, 2);
INSERT INTO `os_rule` VALUES (28, '文件下载', '/admin/file/download', 21, '20-21-28', 2, 2);
INSERT INTO `os_rule` VALUES (29, '系统设置', '/admin/system/default', 0, '29', 0, 1);
INSERT INTO `os_rule` VALUES (30, '数据表列表', '/admin/database/index', 29, '29-30', 1, 1);
INSERT INTO `os_rule` VALUES (31, '日志列表', '/admin/log/index', 29, '29-31', 1, 1);
INSERT INTO `os_rule` VALUES (32, '数据表备份', '/admin/database/backup', 30, '29-30-32', 2, 2);
INSERT INTO `os_rule` VALUES (33, '删除日志', '/admin/log/delete', 31, '29-31-33', 2, 2);
INSERT INTO `os_rule` VALUES (34, '文件上传', '/admin/file/upload', 21, '20-21-34', 2, 2);
INSERT INTO `os_rule` VALUES (38, '接口管理', '/admin/interface/default', 0, '38', 0, 1);
INSERT INTO `os_rule` VALUES (39, '分类列表', '/admin/category/index', 38, '38-39', 1, 1);
INSERT INTO `os_rule` VALUES (40, '分类保存', '/admin/category/save', 39, '38-39-40', 2, 2);
INSERT INTO `os_rule` VALUES (41, '分类更新', '/admin/category/update', 39, '38-39-41', 2, 2);
INSERT INTO `os_rule` VALUES (42, '接口保存', '/admin/api/save', 39, '38-39-42', 2, 2);
INSERT INTO `os_rule` VALUES (43, '接口更新', '/admin/api/update', 39, '38-39-43', 2, 2);
INSERT INTO `os_rule` VALUES (44, '分类删除', '/admin/category/delete', 39, '38-39-44', 2, 2);
INSERT INTO `os_rule` VALUES (46, '授权用户', '/admin/oauth/index', 1, '1-46', 1, 1);
INSERT INTO `os_rule` VALUES (47, '文件重命名', '/admin/file/rename', 21, '20-21-47', 2, 2);
INSERT INTO `os_rule` VALUES (48, '接口详情', '/admin/api/index', 39, '38-39-48', 2, 2);
INSERT INTO `os_rule` VALUES (49, '文件更新', '/admin/file/update', 21, '20-21-49', 2, 2);
INSERT INTO `os_rule` VALUES (50, '授权用户保存', '/admin/oauth/save', 46, '1-46-50', 2, 2);
INSERT INTO `os_rule` VALUES (51, '授权用户更新', '/admin/oauth/update', 46, '1-46-51', 2, 2);
INSERT INTO `os_rule` VALUES (52, '授权用户删除', '/admin/oauth/delete', 46, '1-46-52', 2, 2);
INSERT INTO `os_rule` VALUES (53, '基础配置', '/admin/config/index', 29, '29-53', 1, 1);
INSERT INTO `os_rule` VALUES (54, '配置保存', '/admin/config/save', 53, '29-53-54', 2, 2);
INSERT INTO `os_rule` VALUES (55, '配置更新', '/admin/config/update', 53, '29-53-55', 2, 2);
INSERT INTO `os_rule` VALUES (56, '配置删除', '/admin/config/delete', 53, '29-53-56', 2, 2);
INSERT INTO `os_rule` VALUES (57, '配置值更新', '/admin/config/updateVal', 53, '29-53-57', 2, 2);
INSERT INTO `os_rule` VALUES (58, '申请授权列表', '/admin/req-rule/index', 1, '1-58', 1, 1);
INSERT INTO `os_rule` VALUES (59, '申请授权保存', '/admin/req-rule/save', 58, '1-58-59', 2, 2);
INSERT INTO `os_rule` VALUES (60, '申请授权更新', '/admin/req-rule/update', 58, '1-58-60', 2, 2);
INSERT INTO `os_rule` VALUES (61, '申请授权删除', '/admin/req-rule/delete', 58, '1-58-61', 2, 2);
INSERT INTO `os_rule` VALUES (62, '数据表修复', '/admin/database/repair', 30, '29-30-62', 2, 2);
INSERT INTO `os_rule` VALUES (63, '数据表优化', '/admin/database/optimize', 30, '29-30-63', 2, 2);
INSERT INTO `os_rule` VALUES (64, '城市列表', '/admin/area/index', 29, '29-64', 1, 1);
INSERT INTO `os_rule` VALUES (65, '站内通知', '/admin/push/index', 29, '29-65', 1, 1);
INSERT INTO `os_rule` VALUES (66, '站内通知保存', '/admin/push/save', 65, '29-65-66', 2, 2);
INSERT INTO `os_rule` VALUES (67, '站内通知更新', '/admin/push/update', 65, '29-65-67', 2, 2);
INSERT INTO `os_rule` VALUES (68, '站内通知删除', '/admin/push/delete', 65, '29-65-68', 2, 2);
INSERT INTO `os_rule` VALUES (69, '站内通知读取', '/admin/push/see', 65, '29-65-69', 2, 2);
INSERT INTO `os_rule` VALUES (70, '个人中心', '/admin/user/center', 29, '29-70', 1, 1);
INSERT INTO `os_rule` VALUES (71, '保存用户信息', '/admin/center/save', 70, '29-70-71', 2, 2);
INSERT INTO `os_rule` VALUES (72, '系统日志', '/admin/system/log', 29, '29-72', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
