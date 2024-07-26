-- ----------------------------
-- 2021-07-15 10:41:32 backup table start
-- ----------------------------
-- ----------------------------
-- Table structure for os_api_category
-- ----------------------------
DROP TABLE IF EXISTS `os_api_category`;
CREATE TABLE `os_api_category`
(
    `id`    int(10) unsigned                                             NOT NULL AUTO_INCREMENT,
    `name`  varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '权限名称',
    `pid`   int(10) unsigned                                             NOT NULL DEFAULT '0' COMMENT '上级Id',
    `path`  varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '路径',
    `level` tinyint(3) unsigned                                          NOT NULL DEFAULT '0' COMMENT '等级',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 31
  DEFAULT CHARSET = utf8mb4
  ROW_FORMAT = COMPACT COMMENT ='接口分类';
-- ;
-- ----------------------------
-- Records of os_api_category
-- ----------------------------
INSERT INTO os_api_category
VALUES ('1', '系统后台', '0', '1', '0');
INSERT INTO os_api_category
VALUES ('2', '权限管理', '1', '1-2', '1');
INSERT INTO os_api_category
VALUES ('3', '文件管理', '1', '1-3', '1');
INSERT INTO os_api_category
VALUES ('4', '系统管理', '1', '1-4', '1');
INSERT INTO os_api_category
VALUES ('5', '项目管理', '1', '1-5', '1');
INSERT INTO os_api_category
VALUES ('6', '编辑器管理', '1', '1-6', '1');
INSERT INTO os_api_category
VALUES ('7', '权限列表', '2', '1-2-7', '2');
INSERT INTO os_api_category
VALUES ('8', '更新权限', '7', '1-2-7-8', '3');
INSERT INTO os_api_category
VALUES ('9', '保存权限', '7', '1-2-7-9', '3');
INSERT INTO os_api_category
VALUES ('10', '删除权限', '7', '1-2-7-10', '3');
INSERT INTO os_api_category
VALUES ('11', '管理员列表', '2', '1-2-11', '2');
INSERT INTO os_api_category
VALUES ('12', '角色列表', '2', '1-2-12', '2');
INSERT INTO os_api_category
VALUES ('13', '授权用户列表', '2', '1-2-13', '2');
INSERT INTO os_api_category
VALUES ('14', '申请授权列表', '2', '1-2-14', '2');
INSERT INTO os_api_category
VALUES ('15', '文件列表', '3', '1-3-15', '2');
INSERT INTO os_api_category
VALUES ('16', '文件权限', '15', '1-3-15-16', '3');
INSERT INTO os_api_category
VALUES ('17', '文件上传', '15', '1-3-15-17', '3');
INSERT INTO os_api_category
VALUES ('18', '数据表列表', '4', '1-4-18', '2');
INSERT INTO os_api_category
VALUES ('19', '日志列表', '4', '1-4-19', '2');
INSERT INTO os_api_category
VALUES ('20', '基础配置', '4', '1-4-20', '2');
INSERT INTO os_api_category
VALUES ('21', '城市列表', '4', '1-4-21', '2');
INSERT INTO os_api_category
VALUES ('22', '站内通知', '4', '1-4-22', '2');
INSERT INTO os_api_category
VALUES ('23', '个人中心', '4', '1-4-23', '2');
INSERT INTO os_api_category
VALUES ('24', '系统日志', '4', '1-4-24', '2');
INSERT INTO os_api_category
VALUES ('25', '时间线', '4', '1-4-25', '2');
INSERT INTO os_api_category
VALUES ('26', '项目列表', '5', '1-5-26', '2');
INSERT INTO os_api_category
VALUES ('27', 'APIDoc', '5', '1-5-27', '2');
INSERT INTO os_api_category
VALUES ('28', 'MarkDown', '6', '1-6-28', '2');
INSERT INTO os_api_category
VALUES ('29', 'Quill', '6', '1-6-29', '2');
INSERT INTO os_api_category
VALUES ('30', '微信小程序接口', '0', '30', '0');
-- ----------------------------
-- 2021-07-15 10:41:32 backup table end
-- ----------------------------
