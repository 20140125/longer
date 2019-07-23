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

 Date: 23/07/2019 14:20:50
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED NULL DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2014_10_12_000000_create_admin_users_table', 1);
INSERT INTO `migrations` VALUES (2, '2014_10_12_100000_create_password_resets_table', 1);
INSERT INTO `migrations` VALUES (3, '2018_10_16_102144_create_auth_rules_table', 1);
INSERT INTO `migrations` VALUES (4, '2018_10_16_102952_create_config_table', 1);
INSERT INTO `migrations` VALUES (5, '2018_10_20_021744_create_failed_jobs_table', 1);
INSERT INTO `migrations` VALUES (6, '2018_10_25_074849_create_role_table', 1);
INSERT INTO `migrations` VALUES (7, '2018_10_29_104807_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (8, '2018_11_02_163033_create_system_log_table', 1);
INSERT INTO `migrations` VALUES (9, '2018_12_04_172235_create_posts_table', 1);
INSERT INTO `migrations` VALUES (10, '2018_12_21_112236_create_api_lists_table', 1);
INSERT INTO `migrations` VALUES (11, '2018_12_21_112330_create_api_log_table', 1);
INSERT INTO `migrations` VALUES (12, '2018_12_24_105814_create_api_category_table', 1);
INSERT INTO `migrations` VALUES (13, '2019_01_09_144808_create_users_table', 1);
INSERT INTO `migrations` VALUES (14, '2019_01_28_175506_create_oauth_table', 1);
INSERT INTO `migrations` VALUES (15, '2019_02_01_110140_create_music_table', 1);
INSERT INTO `migrations` VALUES (16, '2019_02_01_113423_create_music_user_search_table', 1);
INSERT INTO `migrations` VALUES (17, '2019_03_05_163150_create_user_music_table', 1);

-- ----------------------------
-- Table structure for os_api_category
-- ----------------------------
DROP TABLE IF EXISTS `os_api_category`;
CREATE TABLE `os_api_category`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '权限名称',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级Id',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '路径',
  `level` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '等级',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 53 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of os_api_category
-- ----------------------------
INSERT INTO `os_api_category` VALUES (1, '权限设置', 0, '1', 0);
INSERT INTO `os_api_category` VALUES (2, '权限列表', 1, '1-2', 1);
INSERT INTO `os_api_category` VALUES (5, '权限保存', 2, '1-2-5', 2);
INSERT INTO `os_api_category` VALUES (6, '权限更新', 2, '1-2-6', 2);
INSERT INTO `os_api_category` VALUES (7, '删除权限', 2, '1-2-7', 2);
INSERT INTO `os_api_category` VALUES (8, '管理员列表', 1, '1-8', 1);
INSERT INTO `os_api_category` VALUES (12, '管理员更新', 8, '1-8-12', 2);
INSERT INTO `os_api_category` VALUES (13, '管理员删除', 8, '1-8-13', 2);
INSERT INTO `os_api_category` VALUES (14, '角色列表', 1, '1-14', 1);
INSERT INTO `os_api_category` VALUES (17, '角色保存', 14, '1-14-17', 2);
INSERT INTO `os_api_category` VALUES (18, '角色更新', 14, '1-14-18', 2);
INSERT INTO `os_api_category` VALUES (19, '角色删除', 14, '1-14-19', 2);
INSERT INTO `os_api_category` VALUES (20, '文件管理', 0, '20', 0);
INSERT INTO `os_api_category` VALUES (21, '文件列表', 20, '20-21', 1);
INSERT INTO `os_api_category` VALUES (22, '文件上传', 21, '20-21-22', 2);
INSERT INTO `os_api_category` VALUES (23, '文件详情', 21, '20-21-23', 2);
INSERT INTO `os_api_category` VALUES (24, '文件打包', 21, '20-21-24', 2);
INSERT INTO `os_api_category` VALUES (25, '文件解压', 21, '20-21-25', 2);
INSERT INTO `os_api_category` VALUES (26, '文件修改', 21, '20-21-26', 2);
INSERT INTO `os_api_category` VALUES (27, '文件删除', 21, '20-21-27', 2);
INSERT INTO `os_api_category` VALUES (28, '文件下载', 21, '20-21-28', 2);
INSERT INTO `os_api_category` VALUES (29, '系统设置', 0, '29', 0);
INSERT INTO `os_api_category` VALUES (30, '数据表列表', 29, '29-30', 1);
INSERT INTO `os_api_category` VALUES (31, '日志列表', 29, '29-31', 1);
INSERT INTO `os_api_category` VALUES (32, '数据表备份', 30, '29-30-32', 2);
INSERT INTO `os_api_category` VALUES (33, '删除日志', 31, '29-31-33', 2);
INSERT INTO `os_api_category` VALUES (34, '文件添加', 21, '20-21-34', 2);
INSERT INTO `os_api_category` VALUES (38, '接口管理', 0, '38', 0);
INSERT INTO `os_api_category` VALUES (39, '接口列表', 38, '38-39', 1);
INSERT INTO `os_api_category` VALUES (41, '接口修改', 39, '38-39-41', 2);
INSERT INTO `os_api_category` VALUES (42, '接口保存', 39, '38-39-42', 2);
INSERT INTO `os_api_category` VALUES (43, '接口更新', 39, '38-39-43', 2);
INSERT INTO `os_api_category` VALUES (44, '接口删除', 39, '38-39-44', 2);
INSERT INTO `os_api_category` VALUES (45, '网易云音乐', 38, '38-45', 1);
INSERT INTO `os_api_category` VALUES (46, '授权用户', 1, '1-46', 1);
INSERT INTO `os_api_category` VALUES (48, '管理员保存', 8, '1-8-48', 2);
INSERT INTO `os_api_category` VALUES (49, '授权用户保存', 46, '1-46-49', 2);
INSERT INTO `os_api_category` VALUES (50, '授权用户更新', 46, '1-46-50', 2);
INSERT INTO `os_api_category` VALUES (51, '授权用户删除', 46, '1-46-51', 2);
INSERT INTO `os_api_category` VALUES (52, '音乐播放', 45, '38-45-52', 2);

-- ----------------------------
-- Table structure for os_api_lists
-- ----------------------------
DROP TABLE IF EXISTS `os_api_lists`;
CREATE TABLE `os_api_lists`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `desc` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '接口描述',
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '分类',
  `href` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '接口地址',
  `method` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '请求方法',
  `request` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '请求字段',
  `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '返回字段',
  `response_string` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '返回参数',
  `remark` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `os_api_lists_type_index`(`type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of os_api_lists
-- ----------------------------
INSERT INTO `os_api_lists` VALUES (2, '获取搜索的权限信息', 2, 'https://www.fangloner.com/admin/auth/index', 'POST', '[{\"name\":\"name\",\"type\":\"string\",\"require\":\"1\",\"desc\":\"权限名称\",\"val\":null},{\"name\":\"pid\",\"type\":\"integer\",\"require\":\"1\",\"desc\":\"权限ID\",\"val\":null},{\"name\":\"page\",\"type\":\"integer\",\"require\":\"1\",\"desc\":\"当前页\",\"val\":null},{\"name\":\"limit\",\"type\":\"integer\",\"require\":\"1\",\"desc\":\"每页显示记录数\",\"val\":null}]', '[{\"name\":\"code\",\"type\":\"integer\",\"desc\":\"200 成功\"},{\"name\":\"authLists\",\"desc\":\"权限列表\",\"type\":\"Object\"}]', '{\n    \"code\": 200,\n    \"msg\": \"success\",\n    \"item\": {\n        \"authLists\": {\n            \"data\": [\n                {\n                    \"id\": 1,\n                    \"name\": \"权限设置\",\n                    \"href\": \"/admin/auth/default\",\n                    \"pid\": 0,\n                    \"path\": \"1\",\n                    \"level\": 0,\n                    \"status\": 1\n                },\n                {\n                    \"id\": 14,\n                    \"name\": \"角色列表\",\n                    \"href\": \"/admin/role/index\",\n                    \"pid\": 1,\n                    \"path\": \"1-14\",\n                    \"level\": 1,\n                    \"status\": 1\n                },\n                {\n                    \"id\": 15,\n                    \"name\": \"角色添加\",\n                    \"href\": \"/admin/role/add\",\n                    \"pid\": 14,\n                    \"path\": \"1-14-15\",\n                    \"level\": 2,\n                    \"status\": 2\n                },\n                {\n                    \"id\": 16,\n                    \"name\": \"角色编辑\",\n                    \"href\": \"/admin/role/edit\",\n                    \"pid\": 14,\n                    \"path\": \"1-14-16\",\n                    \"level\": 2,\n                    \"status\": 2\n                },\n                {\n                    \"id\": 17,\n                    \"name\": \"角色保存\",\n                    \"href\": \"/admin/role/save\",\n                    \"pid\": 14,\n                    \"path\": \"1-14-17\",\n                    \"level\": 2,\n                    \"status\": 2\n                },\n                {\n                    \"id\": 18,\n                    \"name\": \"角色更新\",\n                    \"href\": \"/admin/role/update\",\n                    \"pid\": 14,\n                    \"path\": \"1-14-18\",\n                    \"level\": 2,\n                    \"status\": 2\n                },\n                {\n                    \"id\": 19,\n                    \"name\": \"角色删除\",\n                    \"href\": \"/admin/role/delete\",\n                    \"pid\": 14,\n                    \"path\": \"1-14-19\",\n                    \"level\": 2,\n                    \"status\": 2\n                },\n                {\n                    \"id\": 2,\n                    \"name\": \"权限列表\",\n                    \"href\": \"/admin/auth/index\",\n                    \"pid\": 1,\n                    \"path\": \"1-2\",\n                    \"level\": 1,\n                    \"status\": 1\n                },\n                {\n                    \"id\": 3,\n                    \"name\": \"权限添加\",\n                    \"href\": \"/admin/auth/add\",\n                    \"pid\": 2,\n                    \"path\": \"1-2-3\",\n                    \"level\": 2,\n                    \"status\": 1\n                },\n                {\n                    \"id\": 4,\n                    \"name\": \"权限编辑\",\n                    \"href\": \"/admin/auth/edit\",\n                    \"pid\": 2,\n                    \"path\": \"1-2-4\",\n                    \"level\": 2,\n                    \"status\": 2\n                },\n                {\n                    \"id\": 5,\n                    \"name\": \"权限保存\",\n                    \"href\": \"/admin/auth/save\",\n                    \"pid\": 2,\n                    \"path\": \"1-2-5\",\n                    \"level\": 2,\n                    \"status\": 2\n                },\n                {\n                    \"id\": 6,\n                    \"name\": \"权限更新\",\n                    \"href\": \"/admin/auth/update\",\n                    \"pid\": 2,\n                    \"path\": \"1-2-6\",\n                    \"level\": 2,\n                    \"status\": 2\n                },\n                {\n                    \"id\": 7,\n                    \"name\": \"删除权限\",\n                    \"href\": \"/admin/auth/delete\",\n                    \"pid\": 2,\n                    \"path\": \"1-2-7\",\n                    \"level\": 2,\n                    \"status\": 2\n                },\n                {\n                    \"id\": 46,\n                    \"name\": \"授权用户\",\n                    \"href\": \"/admin/oauth/index\",\n                    \"pid\": 1,\n                    \"path\": \"1-46\",\n                    \"level\": 1,\n                    \"status\": 1\n                },\n                {\n                    \"id\": 8,\n                    \"name\": \"管理员列表\",\n                    \"href\": \"/admin/user/index\",\n                    \"pid\": 1,\n                    \"path\": \"1-8\",\n                    \"level\": 1,\n                    \"status\": 1\n                }\n            ],\n            \"total\": 46\n        },\n        \"selectAuth\": [\n            {\n                \"id\": 1,\n                \"name\": \"权限设置\",\n                \"href\": \"/admin/auth/default\",\n                \"pid\": 0,\n                \"path\": \"1\",\n                \"level\": 0,\n                \"status\": 1\n            },\n            {\n                \"id\": 14,\n                \"name\": \"角色列表\",\n                \"href\": \"/admin/role/index\",\n                \"pid\": 1,\n                \"path\": \"1-14\",\n                \"level\": 1,\n                \"status\": 1\n            },\n            {\n                \"id\": 2,\n                \"name\": \"权限列表\",\n                \"href\": \"/admin/auth/index\",\n                \"pid\": 1,\n                \"path\": \"1-2\",\n                \"level\": 1,\n                \"status\": 1\n            },\n            {\n                \"id\": 46,\n                \"name\": \"授权用户\",\n                \"href\": \"/admin/oauth/index\",\n                \"pid\": 1,\n                \"path\": \"1-46\",\n                \"level\": 1,\n                \"status\": 1\n            },\n            {\n                \"id\": 8,\n                \"name\": \"管理员列表\",\n                \"href\": \"/admin/user/index\",\n                \"pid\": 1,\n                \"path\": \"1-8\",\n                \"level\": 1,\n                \"status\": 1\n            },\n            {\n                \"id\": 20,\n                \"name\": \"文件管理\",\n                \"href\": \"/admin/file/default\",\n                \"pid\": 0,\n                \"path\": \"20\",\n                \"level\": 0,\n                \"status\": 1\n            },\n            {\n                \"id\": 21,\n                \"name\": \"文件列表\",\n                \"href\": \"/admin/file/index\",\n                \"pid\": 20,\n                \"path\": \"20-21\",\n                \"level\": 1,\n                \"status\": 1\n            },\n            {\n                \"id\": 29,\n                \"name\": \"系统设置\",\n                \"href\": \"/admin/system/default\",\n                \"pid\": 0,\n                \"path\": \"29\",\n                \"level\": 0,\n                \"status\": 1\n            },\n            {\n                \"id\": 30,\n                \"name\": \"数据表列表\",\n                \"href\": \"/admin/database/index\",\n                \"pid\": 29,\n                \"path\": \"29-30\",\n                \"level\": 1,\n                \"status\": 1\n            },\n            {\n                \"id\": 31,\n                \"name\": \"日志列表\",\n                \"href\": \"/admin/log/index\",\n                \"pid\": 29,\n                \"path\": \"29-31\",\n                \"level\": 1,\n                \"status\": 1\n            },\n            {\n                \"id\": 35,\n                \"name\": \"地区管理\",\n                \"href\": \"/admin/local/default\",\n                \"pid\": 0,\n                \"path\": \"35\",\n                \"level\": 0,\n                \"status\": 1\n            },\n            {\n                \"id\": 36,\n                \"name\": \"地区列表\",\n                \"href\": \"/admin/local/index\",\n                \"pid\": 35,\n                \"path\": \"35-36\",\n                \"level\": 1,\n                \"status\": 1\n            },\n            {\n                \"id\": 37,\n                \"name\": \"地区联动\",\n                \"href\": \"/admin/tools/index\",\n                \"pid\": 35,\n                \"path\": \"35-37\",\n                \"level\": 1,\n                \"status\": 1\n            },\n            {\n                \"id\": 38,\n                \"name\": \"接口管理\",\n                \"href\": \"/admin/interface\",\n                \"pid\": 0,\n                \"path\": \"38\",\n                \"level\": 0,\n                \"status\": 1\n            },\n            {\n                \"id\": 39,\n                \"name\": \"接口列表\",\n                \"href\": \"/admin/api/index\",\n                \"pid\": 38,\n                \"path\": \"38-39\",\n                \"level\": 1,\n                \"status\": 1\n            },\n            {\n                \"id\": 45,\n                \"name\": \"网易云音乐\",\n                \"href\": \"/admin/music/index\",\n                \"pid\": 38,\n                \"path\": \"38-45\",\n                \"level\": 1,\n                \"status\": 1\n            }\n        ]\n    }\n}', '接口权限列表');

-- ----------------------------
-- Table structure for os_api_log
-- ----------------------------
DROP TABLE IF EXISTS `os_api_log`;
CREATE TABLE `os_api_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `api_id` int(11) NOT NULL DEFAULT 0,
  `desc` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `updated_at` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `os_api_log_username_index`(`username`) USING BTREE,
  INDEX `os_api_log_api_id_index`(`api_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of os_api_log
-- ----------------------------
INSERT INTO `os_api_log` VALUES (1, 'admin', 2, '添加接口权限列表', 2147483647);
INSERT INTO `os_api_log` VALUES (2, 'admin', 2, '修改接口权限列表', 2147483647);
INSERT INTO `os_api_log` VALUES (3, 'admin', 2, '修改接口权限列表', 2147483647);

-- ----------------------------
-- Table structure for os_config
-- ----------------------------
DROP TABLE IF EXISTS `os_config`;
CREATE TABLE `os_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置名称',
  `value` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置值',
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for os_music
-- ----------------------------
DROP TABLE IF EXISTS `os_music`;
CREATE TABLE `os_music`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `search_type` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '搜索类型',
  `music_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '音乐名',
  `music_id` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '音乐ID',
  `singer_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '歌手名',
  `singer_id` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '歌手ID',
  `br` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '歌曲音质',
  `lyric` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '歌曲歌词',
  `picUrl` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '歌曲图片',
  `mv` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'mvid',
  `music_url` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '音乐地址',
  `music_time` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '歌曲时间',
  `s` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '关键词',
  `created_at` int(11) NULL DEFAULT NULL,
  `updated_at` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for os_music_user_search
-- ----------------------------
DROP TABLE IF EXISTS `os_music_user_search`;
CREATE TABLE `os_music_user_search`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `s` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户检索关键词',
  `count` int(11) NOT NULL DEFAULT 0 COMMENT '搜索次数',
  `s_type` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户检索类型',
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作IP',
  `created_at` int(11) NULL DEFAULT NULL,
  `updated_at` int(11) NULL DEFAULT NULL,
  `status` tinyint(4) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of os_music_user_search
-- ----------------------------
INSERT INTO `os_music_user_search` VALUES (1, '粤语', 6, '1', 'vuedemo', '127.0.0.1', 2147483647, 2147483647, 1);

-- ----------------------------
-- Table structure for os_oauth
-- ----------------------------
DROP TABLE IF EXISTS `os_oauth`;
CREATE TABLE `os_oauth`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `openid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'openid',
  `access_token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '授权token',
  `avatar_url` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户头像',
  `url` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '用户地址',
  `refresh_token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '刷新access_token',
  `oauth_type` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限类型',
  `role_id` int(11) NOT NULL DEFAULT 0 COMMENT '角色ID',
  `remember_token` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户token',
  `created_at` int(11) NULL DEFAULT NULL,
  `updated_at` int(11) NULL DEFAULT NULL,
  `expires` int(11) NULL DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of os_oauth
-- ----------------------------
INSERT INTO `os_oauth` VALUES (1, '龙哥', '554BE2A40A690906067BE1ADAB8AD720', '2C14856760CC73BCAFD9CE6A3C787DC9', 'http://thirdqq.qlogo.cn/g?b=oidb&k=18HtqpQWKV2ibhbERicRAZow&s=100', '', '653E948D12CC9F6B97AFFE94660512FB', 'qq', 0, '5f695afd6f8b7449c886abad126fead5', 2147483647, 2147483647, 1568193712);

-- ----------------------------
-- Table structure for os_password_resets
-- ----------------------------
DROP TABLE IF EXISTS `os_password_resets`;
CREATE TABLE `os_password_resets`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户邮箱',
  `token` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'token',
  `created_at` int(11) NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `os_password_resets_token_email_index`(`token`, `email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for os_posts
-- ----------------------------
DROP TABLE IF EXISTS `os_posts`;
CREATE TABLE `os_posts`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文章链接',
  `author` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '文章作者',
  `title` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文章标题',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文章内容',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否删除',
  `is_show` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否显示',
  `thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '文章缩略图',
  `post_date` int(11) NULL DEFAULT NULL,
  `created_at` int(11) NULL DEFAULT NULL,
  `updated_at` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for os_role
-- ----------------------------
DROP TABLE IF EXISTS `os_role`;
CREATE TABLE `os_role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '角色名称',
  `auth_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限ID',
  `auth_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限Url',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '角色状态',
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `os_role_role_name_index`(`role_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of os_role
-- ----------------------------
INSERT INTO `os_role` VALUES (1, '超级管理员', '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\",\"13\",\"14\",\"15\",\"16\",\"17\",\"18\",\"19\",\"46\",\"20\",\"21\",\"22\",\"23\",\"24\",\"25\",\"26\",\"27\",\"28\",\"34\",\"29\",\"30\",\"32\",\"31\",\"33\",\"35\",\"36\",\"37\",\"38\",\"39\",\"40\",\"41\",\"42\",\"43\",\"44\",\"45\"]', '[\"\\/admin\\/auth\\/default\",\"\\/admin\\/auth\\/index\",\"\\/admin\\/auth\\/add\",\"\\/admin\\/auth\\/edit\",\"\\/admin\\/auth\\/save\",\"\\/admin\\/auth\\/update\",\"\\/admin\\/auth\\/delete\",\"\\/admin\\/adminuser\\/index\",\"\\/admin\\/adminuser\\/add\",\"\\/admin\\/adminuser\\/edit\",\"\\/admin\\/adminuser\\/save\",\"\\/admin\\/adminuser\\/update\",\"\\/admin\\/adminuser\\/delete\",\"\\/admin\\/role\\/index\",\"\\/admin\\/role\\/add\",\"\\/admin\\/role\\/edit\",\"\\/admin\\/role\\/save\",\"\\/admin\\/role\\/update\",\"\\/admin\\/role\\/delete\",\"\\/admin\\/file\\/default\",\"\\/admin\\/file\\/index\",\"\\/admin\\/file\\/upload\",\"\\/admin\\/file\\/details\",\"\\/admin\\/file\\/gzip\",\"\\/admin\\/file\\/unzip\",\"\\/admin\\/file\\/save\",\"\\/admin\\/file\\/delete\",\"\\/admin\\/file\\/download\",\"\\/admin\\/system\\/default\",\"\\/admin\\/database\\/index\",\"\\/admin\\/log\\/index\",\"\\/admin\\/database\\/backup\",\"\\/admin\\/log\\/delete\",\"\\/admin\\/file\\/add\",\"\\/admin\\/position\\/default\",\"\\/admin\\/position\\/index\",\"\\/admin\\/tools\\/position\",\"\\/admin\\/interface\",\"\\/admin\\/api\\/index\",\"\\/admin\\/api\\/add\",\"\\/admin\\/api\\/edit\",\"\\/admin\\/api\\/save\",\"\\/admin\\/api\\/update\",\"\\/admin\\/api\\/delete\",\"\\/admin\\/music\\/index\",\"\\/admin\\/oauth\\/index\"]', 1, 1559563238, 1559563238);
INSERT INTO `os_role` VALUES (2, '系统管理员', '[20,21,22,23,24,25,28,34,29,30,32,31,33,35,36,37,38,39,40,41,42,43,44,45]', '[\"\\/admin\\/file\\/default\",\"\\/admin\\/file\\/index\",\"\\/admin\\/file\\/upload\",\"\\/admin\\/file\\/details\",\"\\/admin\\/file\\/gzip\",\"\\/admin\\/file\\/unzip\",\"\\/admin\\/file\\/download\",\"\\/admin\\/system\\/default\",\"\\/admin\\/database\\/index\",\"\\/admin\\/log\\/index\",\"\\/admin\\/database\\/backup\",\"\\/admin\\/log\\/delete\",\"\\/admin\\/file\\/add\",\"\\/admin\\/local\\/default\",\"\\/admin\\/local\\/index\",\"\\/admin\\/tools\\/index\",\"\\/admin\\/interface\",\"\\/admin\\/api\\/index\",\"\\/admin\\/api\\/add\",\"\\/admin\\/api\\/edit\",\"\\/admin\\/api\\/save\",\"\\/admin\\/api\\/update\",\"\\/admin\\/api\\/delete\",\"\\/admin\\/music\\/index\"]', 1, 1563517517, 1563518739);

-- ----------------------------
-- Table structure for os_rule
-- ----------------------------
DROP TABLE IF EXISTS `os_rule`;
CREATE TABLE `os_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '权限名称',
  `href` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '权限链接',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级Id',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '路径',
  `level` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '等级',
  `status` tinyint(4) NULL DEFAULT 1 COMMENT '权限状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 47 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of os_rule
-- ----------------------------
INSERT INTO `os_rule` VALUES (1, '权限设置', '/admin/auth/default', 0, '1', 0, 1);
INSERT INTO `os_rule` VALUES (2, '权限列表', '/admin/auth/index', 1, '1-2', 1, 1);
INSERT INTO `os_rule` VALUES (3, '权限添加', '/admin/auth/add', 2, '1-2-3', 2, 1);
INSERT INTO `os_rule` VALUES (4, '权限编辑', '/admin/auth/edit', 2, '1-2-4', 2, 2);
INSERT INTO `os_rule` VALUES (5, '权限保存', '/admin/auth/save', 2, '1-2-5', 2, 2);
INSERT INTO `os_rule` VALUES (6, '权限更新', '/admin/auth/update', 2, '1-2-6', 2, 2);
INSERT INTO `os_rule` VALUES (7, '删除权限', '/admin/auth/delete', 2, '1-2-7', 2, 2);
INSERT INTO `os_rule` VALUES (8, '管理员列表', '/admin/user/index', 1, '1-8', 1, 1);
INSERT INTO `os_rule` VALUES (9, '管理员添加', '/admin/user/add', 8, '1-8-9', 2, 2);
INSERT INTO `os_rule` VALUES (10, '管理员编辑', '/admin/user/edit', 8, '1-8-10', 2, 2);
INSERT INTO `os_rule` VALUES (11, '管理员保存', '/admin/user/save', 8, '1-8-11', 2, 2);
INSERT INTO `os_rule` VALUES (12, '管理员更新', '/admin/user/update', 8, '1-8-12', 2, 2);
INSERT INTO `os_rule` VALUES (13, '管理员删除', '/admin/user/delete', 8, '1-8-13', 2, 2);
INSERT INTO `os_rule` VALUES (14, '角色列表', '/admin/role/index', 1, '1-14', 1, 1);
INSERT INTO `os_rule` VALUES (15, '角色添加', '/admin/role/add', 14, '1-14-15', 2, 2);
INSERT INTO `os_rule` VALUES (16, '角色编辑', '/admin/role/edit', 14, '1-14-16', 2, 2);
INSERT INTO `os_rule` VALUES (17, '角色保存', '/admin/role/save', 14, '1-14-17', 2, 2);
INSERT INTO `os_rule` VALUES (18, '角色更新', '/admin/role/update', 14, '1-14-18', 2, 2);
INSERT INTO `os_rule` VALUES (19, '角色删除', '/admin/role/delete', 14, '1-14-19', 2, 2);
INSERT INTO `os_rule` VALUES (20, '文件管理', '/admin/file/default', 0, '20', 0, 1);
INSERT INTO `os_rule` VALUES (21, '文件列表', '/admin/file/index', 20, '20-21', 1, 1);
INSERT INTO `os_rule` VALUES (22, '文件上传', '/admin/file/upload', 21, '20-21-22', 2, 2);
INSERT INTO `os_rule` VALUES (23, '文件详情', '/admin/file/details', 21, '20-21-23', 2, 2);
INSERT INTO `os_rule` VALUES (24, '文件打包', '/admin/file/gzip', 21, '20-21-24', 2, 2);
INSERT INTO `os_rule` VALUES (25, '文件解压', '/admin/file/unzip', 21, '20-21-25', 2, 2);
INSERT INTO `os_rule` VALUES (26, '文件修改', '/admin/file/save', 21, '20-21-26', 2, 2);
INSERT INTO `os_rule` VALUES (27, '文件删除', '/admin/file/delete', 21, '20-21-27', 2, 2);
INSERT INTO `os_rule` VALUES (28, '文件下载', '/admin/file/download', 21, '20-21-28', 2, 2);
INSERT INTO `os_rule` VALUES (29, '系统设置', '/admin/system/default', 0, '29', 0, 1);
INSERT INTO `os_rule` VALUES (30, '数据表列表', '/admin/database/index', 29, '29-30', 1, 1);
INSERT INTO `os_rule` VALUES (31, '日志列表', '/admin/log/index', 29, '29-31', 1, 1);
INSERT INTO `os_rule` VALUES (32, '数据表备份', '/admin/database/backup', 30, '29-30-32', 2, 2);
INSERT INTO `os_rule` VALUES (33, '删除日志', '/admin/log/delete', 31, '29-31-33', 2, 2);
INSERT INTO `os_rule` VALUES (34, '文件添加', '/admin/file/add', 21, '20-21-34', 2, 2);
INSERT INTO `os_rule` VALUES (38, '接口管理', '/admin/interface', 0, '38', 0, 1);
INSERT INTO `os_rule` VALUES (39, '接口列表', '/admin/api/index', 38, '38-39', 1, 1);
INSERT INTO `os_rule` VALUES (40, '接口添加', '/admin/api/add', 39, '38-39-40', 2, 2);
INSERT INTO `os_rule` VALUES (41, '接口修改', '/admin/api/edit', 39, '38-39-41', 2, 2);
INSERT INTO `os_rule` VALUES (42, '接口保存', '/admin/api/save', 39, '38-39-42', 2, 2);
INSERT INTO `os_rule` VALUES (43, '接口更新', '/admin/api/update', 39, '38-39-43', 2, 2);
INSERT INTO `os_rule` VALUES (44, '接口删除', '/admin/api/delete', 39, '38-39-44', 2, 2);
INSERT INTO `os_rule` VALUES (45, '网易云音乐', '/admin/music/index', 38, '38-45', 1, 1);
INSERT INTO `os_rule` VALUES (46, '授权用户', '/admin/oauth/index', 1, '1-46', 1, 1);

-- ----------------------------
-- Table structure for os_system_log
-- ----------------------------
DROP TABLE IF EXISTS `os_system_log`;
CREATE TABLE `os_system_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '操作者',
  `url` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'url',
  `ip_address` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT 'ip地址',
  `log` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '记录',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `os_system_log_username_index`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 55 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of os_system_log
-- ----------------------------
INSERT INTO `os_system_log` VALUES (3, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1560321952,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/log\\/delete\\\",\\\"info\\\":\\\"删除记录成功\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1560321952);
INSERT INTO `os_system_log` VALUES (4, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1560321966,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/log\\/delete\\\",\\\"info\\\":\\\"删除记录成功\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1560321966);
INSERT INTO `os_system_log` VALUES (5, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1560917268,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/role\\/update\\\",\\\"info\\\":\\\"保存数据成功\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1560917268);
INSERT INTO `os_system_log` VALUES (6, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1560917368,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/role\\/update\\\",\\\"info\\\":\\\"保存数据成功\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1560917368);
INSERT INTO `os_system_log` VALUES (7, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1560930621,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/auth\\/update\\\",\\\"info\\\":\\\"修改数据成功\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1560930621);
INSERT INTO `os_system_log` VALUES (8, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1560930623,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/auth\\/update\\\",\\\"info\\\":\\\"修改数据成功\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1560930623);
INSERT INTO `os_system_log` VALUES (9, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1560930627,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/auth\\/update\\\",\\\"info\\\":\\\"保存数据成功\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1560930627);
INSERT INTO `os_system_log` VALUES (10, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1560930632,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/auth\\/update\\\",\\\"info\\\":\\\"保存数据成功\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1560930632);
INSERT INTO `os_system_log` VALUES (11, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1560932422,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/auth\\/update\\\",\\\"info\\\":\\\"保存数据成功\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1560932422);
INSERT INTO `os_system_log` VALUES (12, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1560933178,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/auth\\/update\\\",\\\"info\\\":\\\"修改数据成功\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1560933178);
INSERT INTO `os_system_log` VALUES (13, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1560933179,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/auth\\/update\\\",\\\"info\\\":\\\"修改数据成功\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1560933179);
INSERT INTO `os_system_log` VALUES (14, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563259375,\"log\":{\"username\":\"admin\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"info\":\"{\\\"url\\\":\\\"\\/admin\\/music\\/index\\\",\\\"info\\\":\\\"你没有访问权限，请联系管理员【785973567】检验数据的正确性\\\"}\"}}', 1563259375);
INSERT INTO `os_system_log` VALUES (17, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563506676,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/log\\/delete\\\",\\\"info\\\":\\\"delete log success\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1563506676);
INSERT INTO `os_system_log` VALUES (18, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563506687,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/log\\/delete\\\",\\\"info\\\":\\\"delete log success\\\"}\",\"token\":\"a2448444a806f3931649f968adb0bccf\",\"username\":\"admin\"}}', 1563506687);
INSERT INTO `os_system_log` VALUES (22, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563509667,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/log\\/delete\\\",\\\"info\\\":\\\"delete log success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563509667);
INSERT INTO `os_system_log` VALUES (23, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563509688,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/log\\/delete\\\",\\\"info\\\":\\\"delete log success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563509688);
INSERT INTO `os_system_log` VALUES (24, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563517109,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/auth\\/update\\\",\\\"info\\\":\\\"update rule status success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563517109);
INSERT INTO `os_system_log` VALUES (25, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563517112,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/auth\\/update\\\",\\\"info\\\":\\\"update rule status success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563517112);
INSERT INTO `os_system_log` VALUES (26, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563517766,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/role\\/save\\\",\\\"info\\\":\\\"role save success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563517766);
INSERT INTO `os_system_log` VALUES (27, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563518740,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/role\\/update\\\",\\\"info\\\":\\\"role update success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563518740);
INSERT INTO `os_system_log` VALUES (28, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563522353,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/api\\/update\\\",\\\"info\\\":\\\"update api lists success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563522353);
INSERT INTO `os_system_log` VALUES (29, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563522379,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/api\\/update\\\",\\\"info\\\":\\\"update api lists success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563522379);
INSERT INTO `os_system_log` VALUES (30, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563522549,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/save\\\",\\\"info\\\":\\\"save api category success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563522549);
INSERT INTO `os_system_log` VALUES (31, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563523793,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/delete\\\",\\\"info\\\":\\\"remove api and api category success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563523793);
INSERT INTO `os_system_log` VALUES (32, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563523796,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/delete\\\",\\\"info\\\":\\\"remove api and api category success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563523796);
INSERT INTO `os_system_log` VALUES (33, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563523799,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/delete\\\",\\\"info\\\":\\\"remove api and api category success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563523799);
INSERT INTO `os_system_log` VALUES (34, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563523803,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/delete\\\",\\\"info\\\":\\\"remove api and api category success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563523803);
INSERT INTO `os_system_log` VALUES (35, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563523805,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/delete\\\",\\\"info\\\":\\\"remove api and api category success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563523805);
INSERT INTO `os_system_log` VALUES (36, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563523807,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/delete\\\",\\\"info\\\":\\\"remove api and api category success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563523807);
INSERT INTO `os_system_log` VALUES (37, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563523821,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/delete\\\",\\\"info\\\":\\\"remove api and api category success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563523821);
INSERT INTO `os_system_log` VALUES (38, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563523870,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/delete\\\",\\\"info\\\":\\\"remove api and api category success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563523870);
INSERT INTO `os_system_log` VALUES (39, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563523903,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/save\\\",\\\"info\\\":\\\"save api category success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563523903);
INSERT INTO `os_system_log` VALUES (40, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563523915,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/save\\\",\\\"info\\\":\\\"save api category success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563523915);
INSERT INTO `os_system_log` VALUES (41, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563523927,\"log\":{\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/save\\\",\\\"info\\\":\\\"save api category success\\\"}\",\"token\":\"e38eb39b0e2a84cf727a2a05813a7731\",\"username\":\"admin\"}}', 1563523927);
INSERT INTO `os_system_log` VALUES (43, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563763299,\"log\":{\"token\":\"05cfef1274d49e89d041e64350dc9ae8\",\"pid\":null,\"auth\":null,\"page\":\"1\",\"limit\":\"15\",\"username\":\"admin\",\"offset\":\"0\",\"level\":\"1\",\"info\":\"{\\\"url\\\":\\\"\\/v1\\/log\\/delete\\\",\\\"info\\\":\\\"delete log success\\\"}\"}}', 1563763299);
INSERT INTO `os_system_log` VALUES (44, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563767199,\"log\":{\"token\":\"05cfef1274d49e89d041e64350dc9ae8\",\"username\":\"admin\",\"info\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/save\\\",\\\"info\\\":\\\"save api category success\\\"}\"}}', 1563767199);
INSERT INTO `os_system_log` VALUES (45, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563777464,\"log\":{\"token\":\"05cfef1274d49e89d041e64350dc9ae8\",\"path\":\"D:\\\\xampp\\\\htdocs\\\\www\\\\longer\\\\public\\/\\/aaa\",\"basename\":\"\\/\",\"username\":\"admin\",\"info\":\"{\\\"url\\\":\\\"\\/v1\\/file\\/save\\\",\\\"info\\\":\\\"你的新文件名: D:\\\\\\\\xampp\\\\\\\\htdocs\\\\\\\\www\\\\\\\\longer\\\\\\\\public\\/\\/aaa\\\"}\"}}', 1563777464);
INSERT INTO `os_system_log` VALUES (46, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563777699,\"log\":{\"token\":\"05cfef1274d49e89d041e64350dc9ae8\",\"path\":\"base_path\",\"basename\":\"\\/\",\"username\":\"admin\",\"oldFile\":\"D:\\\\xampp\\\\htdocs\\\\www\\\\longer\\\\public\\/aaa\\/\",\"newFile\":\"D:\\\\xampp\\\\htdocs\\\\www\\\\longer\\\\public\\/bbb\\/\",\"info\":\"{\\\"url\\\":\\\"\\/v1\\/file\\/save\\\",\\\"info\\\":\\\"你的新文件名: bbb\\\"}\"}}', 1563777699);
INSERT INTO `os_system_log` VALUES (47, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563777873,\"log\":{\"token\":\"05cfef1274d49e89d041e64350dc9ae8\",\"path\":\"D:\\\\xampp\\\\htdocs\\\\www\\\\longer\\\\public\\/bbb\\/\",\"basename\":\"\\/\",\"username\":\"admin\",\"info\":\"{\\\"url\\\":\\\"\\/v1\\/file\\/delete\\\",\\\"info\\\":\\\"删除文件成功：D:\\\\\\\\xampp\\\\\\\\htdocs\\\\\\\\www\\\\\\\\longer\\\\\\\\public\\/bbb\\/\\\"}\"}}', 1563777873);
INSERT INTO `os_system_log` VALUES (48, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563778333,\"log\":{\"token\":\"05cfef1274d49e89d041e64350dc9ae8\",\"path\":\"D:\\\\xampp\\\\htdocs\\\\www\\\\longer\\\\README.md\",\"basename\":\"\\/\",\"username\":\"admin\",\"info\":\"{\\\"url\\\":\\\"\\/v1\\/file\\/delete\\\",\\\"info\\\":\\\"删除文件成功：D:\\\\\\\\xampp\\\\\\\\htdocs\\\\\\\\www\\\\\\\\longer\\\\\\\\public\\/bbb\\/\\\"}\",\"page\":\"1\",\"limit\":\"15\",\"msg\":\"{\\\"url\\\":\\\"\\/v1\\/file\\/update\\\",\\\"info\\\":\\\"file save success\\\"}\"}}', 1563778333);
INSERT INTO `os_system_log` VALUES (49, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563778560,\"log\":{\"token\":\"05cfef1274d49e89d041e64350dc9ae8\",\"path\":\"D:\\\\xampp\\\\htdocs\\\\www\\\\longer\\\\app\\/Http\\/Controllers\\/Api\\/v1\\/FileController.php\",\"basename\":\"\\/\",\"username\":\"admin\",\"info\":\"{\\\"url\\\":\\\"\\/v1\\/file\\/delete\\\",\\\"info\\\":\\\"删除文件成功：D:\\\\\\\\xampp\\\\\\\\htdocs\\\\\\\\www\\\\\\\\longer\\\\\\\\public\\/bbb\\/\\\"}\",\"page\":\"1\",\"limit\":\"15\",\"msg\":\"{\\\"url\\\":\\\"\\/v1\\/file\\/update\\\",\\\"info\\\":\\\"file save success\\\"}\"}}', 1563778560);
INSERT INTO `os_system_log` VALUES (50, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563778859,\"log\":{\"token\":\"05cfef1274d49e89d041e64350dc9ae8\",\"path\":\"D:\\\\xampp\\\\htdocs\\\\www\\\\longer\\\\storage\\/logs\\/laravel-2019-07-22.log\",\"basename\":\"\\/\",\"username\":\"admin\",\"info\":\"{\\\"url\\\":\\\"\\/v1\\/file\\/delete\\\",\\\"info\\\":\\\"删除文件成功：D:\\\\\\\\xampp\\\\\\\\htdocs\\\\\\\\www\\\\\\\\longer\\\\\\\\public\\/bbb\\/\\\"}\",\"page\":\"1\",\"limit\":\"15\",\"msg\":\"{\\\"url\\\":\\\"\\/v1\\/file\\/update\\\",\\\"info\\\":\\\"file save success\\\"}\"}}', 1563778859);
INSERT INTO `os_system_log` VALUES (51, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563779244,\"log\":{\"token\":\"05cfef1274d49e89d041e64350dc9ae8\",\"path\":\"base_path\",\"basename\":\"\\/\",\"username\":\"admin\",\"id\":\"35\",\"msg\":\"{\\\"url\\\":\\\"\\/v1\\/category\\/delete\\\",\\\"info\\\":\\\"remove api and api category success\\\"}\"}}', 1563779244);
INSERT INTO `os_system_log` VALUES (52, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563779460,\"log\":{\"token\":\"05cfef1274d49e89d041e64350dc9ae8\",\"path\":\"D:\\\\xampp\\\\htdocs\\\\www\\\\longer\\\\storage\\/logs\\/laravel-2019-07-22.log\",\"basename\":\"\\/\",\"username\":\"admin\",\"id\":\"35\",\"msg\":\"{\\\"url\\\":\\\"\\/v1\\/file\\/delete\\\",\\\"info\\\":\\\"删除文件成功：D:\\\\\\\\xampp\\\\\\\\htdocs\\\\\\\\www\\\\\\\\longer\\\\\\\\storage\\/logs\\/laravel-2019-07-22.log\\\"}\",\"page\":\"1\",\"limit\":\"15\",\"search_type\":\"1\",\"s\":null,\"form\":\"mysql\"}}', 1563779460);
INSERT INTO `os_system_log` VALUES (53, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563846951,\"log\":{\"token\":\"05cfef1274d49e89d041e64350dc9ae8\",\"path\":\"base_path\",\"basename\":\"\\/\",\"username\":\"admin\",\"msg\":\"{\\\"url\\\":\\\"\\/v1\\/file\\/chmod\\\",\\\"info\\\":\\\"rename file success\\\"}\"}}', 1563846951);
INSERT INTO `os_system_log` VALUES (54, 'admin', '/api/v1/log/save', '127.0.0.1', '{\"username\":\"admin\",\"url\":\"\\/api\\/v1\\/log\\/save\",\"ip_address\":\"127.0.0.1\",\"created_at\":1563847125,\"log\":{\"token\":\"05cfef1274d49e89d041e64350dc9ae8\",\"path\":\"base_path\",\"basename\":\"\\/\",\"username\":\"admin\",\"msg\":\"{\\\"url\\\":\\\"\\/v1\\/file\\/chmod\\\",\\\"info\\\":\\\"Modify file permissions successfully\\\"}\"}}', 1563847125);

-- ----------------------------
-- Table structure for os_user_music
-- ----------------------------
DROP TABLE IF EXISTS `os_user_music`;
CREATE TABLE `os_user_music`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `music_id` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '音乐ID',
  `music_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '音乐名',
  `singer_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '歌手名',
  `music_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '歌曲链接',
  `pic_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图片地址',
  `openid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户openid',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '是否喜欢 1是 2否',
  `created_at` int(11) NULL DEFAULT NULL,
  `updated_at` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for os_users
-- ----------------------------
DROP TABLE IF EXISTS `os_users`;
CREATE TABLE `os_users`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '用户名',
  `email` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '用户邮箱',
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色Id',
  `ip_address` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ip地址',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '用户状态',
  `created_at` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户密码',
  `salt` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '密码盐值',
  `access_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '0' COMMENT '登陆标识',
  `phone_number` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '手机号',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `os_admin_users_username_email_index`(`username`, `email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of os_users
-- ----------------------------
INSERT INTO `os_users` VALUES (1, 'admin', '785973567@qq.com', 1, '127.0.0.1', 1, 1559272310, 1563761049, 'e74c3fee3feadcbec1c80446c63ce671', 'hKvA8Ywa', '05cfef1274d49e89d041e64350dc9ae8', NULL);

SET FOREIGN_KEY_CHECKS = 1;
