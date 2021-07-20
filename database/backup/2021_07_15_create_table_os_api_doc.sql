-- ----------------------------
-- 2021-07-15 10:47:05 backup table start
-- ----------------------------
-- ----------------------------
-- Table structure for os_api_doc
-- ----------------------------
DROP TABLE IF EXISTS `os_api_doc`;
CREATE TABLE `os_api_doc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(10) NOT NULL,
  `html` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `markdown` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT COMMENT='接口详情列表';
 -- ;
-- ----------------------------
-- Records of os_api_doc
-- ----------------------------
INSERT INTO os_api_doc VALUES ('1','1','<ul>
<li>
<h6><a id="__httpswwwfanglongercomhttpswwwfanglongercom_0"></a>服务器域名：  <a href="https://www.fanglonger.com" target="_blank">https://www.fanglonger.com</a></h6>
</li>
<li>
<h6><a id="Authorization_1"></a>需要设置：Authorization</h6>
</li>
</ul>
<h4><a id="_3"></a>权限管理</h4>
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" checked="" disabled="" type="checkbox"> 1. 权限列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" checked="" disabled="" type="checkbox"> 1. 更新权限</li>
<li class="task-list-item"><input class="task-list-item-checkbox" checked="" disabled="" type="checkbox"> 2. 保存权限</li>
<li class="task-list-item"><input class="task-list-item-checkbox" checked="" disabled="" type="checkbox"> 3. 删除权限</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 管理员列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 更新管理员列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 保存管理员列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 删除管理员列表</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 角色列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 更新角色列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 保存角色列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 删除角色列表</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 授权用户列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 更新授权用户列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 保存授权用户列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 删除授权用户列表</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 5. 申请授权列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 更新申请授权列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 保存申请授权列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 删除申请授权列表</li>
</ul>
</li>
</ul>
<h4><a id="_24"></a>文件管理</h4>
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 文件列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 文件权限</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 文件读取</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 文件压缩</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 文件解压</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 5. 文件上传</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 6. 文件下载</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 7. 文件更新</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 8. 文件重命名</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 9. 文件删除</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 10. 文件保存</li>
</ul>
</li>
</ul>
<h4><a id="_36"></a>系统管理</h4>
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 数据表列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 数据表备份</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 数据表优化</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 数据表修复</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 数据表修改</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 日志列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1.日志删除</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 基础配置
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 基础配置保存</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 基础配置更新</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 基础配置删除</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 基础配置值更新</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 城市列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 5. 站内通知
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 站内通知保存</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 站内通知更新</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 站内通知删除</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 站内通知读取</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 6. 个人中心
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 更新用户信息</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 7. 系统日志</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 8. 聊天记录</li>
</ul>
<h4><a id="_59"></a>项目管理</h4>
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 项目列表 （JSON）
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 分类保存</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 分类更新</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 分类删除</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 接口保存</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 5. 接口更新</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 6. 接口详情</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. APiDoc （MarkDown）
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. APiDoc保存</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. APiDoc更新</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. APiDoc删除</li>
</ul>
</li>
</ul>
<h4><a id="_71"></a>编辑器管理</h4>
<ul class="contains-task-list">
<li class="task-list-item">
<p><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. MarkDown</p>
</li>
<li class="task-list-item">
<p><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. Quill (富文本)</p>
</li>
<li>
<h6><a id="_75"></a>错误码说明</h6>
</li>
</ul>
<table>
<thead>
<tr>
<th>错误码</th>
<th>说明</th>
</tr>
</thead>
<tbody>
<tr>
<td>200</td>
<td>请求成功 （SUCCESS）</td>
</tr>
<tr>
<td>201</td>
<td>请求失败 （ERROR）</td>
</tr>
<tr>
<td>405</td>
<td>方法不允许 （METHOD ERROR）</td>
</tr>
<tr>
<td>401</td>
<td>没有权限 （Unauthorized）</td>
</tr>
<tr>
<td>403</td>
<td>拒绝访问 （Forbidden）</td>
</tr>
<tr>
<td>404</td>
<td>页面不存在 （NOT FOUND）</td>
</tr>
</tbody>
</table>','- ###### 服务器域名：  [https://www.fanglonger.com](https://www.fanglonger.com)
- ###### 需要设置：Authorization

#### 权限管理
   - [x] 1. 权限列表  
      - [x] 1. 更新权限
      - [x] 2. 保存权限
      - [x] 3. 删除权限
  - [ ] 2. 管理员列表
      - [ ] 1. 更新管理员列表
      - [ ] 2. 保存管理员列表
      - [ ] 3. 删除管理员列表
   - [ ] 3. 角色列表
      - [ ] 1. 更新角色列表
      - [ ] 2. 保存角色列表
      - [ ] 3. 删除角色列表 
   - [ ] 4. 授权用户列表
      - [ ] 1. 更新授权用户列表
      - [ ] 2. 保存授权用户列表
      - [ ] 3. 删除授权用户列表 
   - [ ] 5. 申请授权列表
      - [ ] 1. 更新申请授权列表
      - [ ] 2. 保存申请授权列表
      - [ ] 3. 删除申请授权列表
#### 文件管理
- [ ] 1. 文件列表
    - [ ] 1. 文件权限
    - [ ] 2. 文件读取
    - [ ] 3. 文件压缩
    - [ ] 4. 文件解压
    - [ ] 5. 文件上传
    - [ ] 6. 文件下载
    - [ ] 7. 文件更新
    - [ ] 8. 文件重命名
    - [ ] 9. 文件删除
    - [ ] 10. 文件保存
#### 系统管理
- [ ] 1. 数据表列表
    - [ ] 1. 数据表备份
    - [ ] 2. 数据表优化
    - [ ] 3. 数据表修复
    - [ ] 4. 数据表修改
- [ ] 2. 日志列表
    - [ ] 1.日志删除 
- [ ] 3. 基础配置
    - [ ] 1. 基础配置保存
    - [ ] 2. 基础配置更新
    - [ ] 3. 基础配置删除
    - [ ] 4. 基础配置值更新
- [ ] 4. 城市列表
- [ ] 5. 站内通知
    - [ ] 1. 站内通知保存
    - [ ] 2. 站内通知更新
    - [ ] 3. 站内通知删除
    - [ ] 4. 站内通知读取
- [ ] 6. 个人中心
    - [ ] 1. 更新用户信息
- [ ] 7. 系统日志
- [ ] 8. 聊天记录
#### 项目管理
- [ ] 1. 项目列表 （JSON）
   - [ ] 1. 分类保存
   - [ ] 2. 分类更新
   - [ ] 3. 分类删除
   - [ ] 4. 接口保存
   - [ ] 5. 接口更新
   - [ ] 6. 接口详情
- [ ] 2. APiDoc （MarkDown）
   - [ ] 1. APiDoc保存
   - [ ] 2. APiDoc更新
   - [ ] 3. APiDoc删除
#### 编辑器管理
- [ ] 1. MarkDown
- [ ] 2. Quill (富文本)

- ###### 错误码说明
  

|错误码|说明|
|---|---|
|200|请求成功 （SUCCESS）|
|201|请求失败 （ERROR）|
|405|方法不允许 （METHOD ERROR）|
|401|没有权限 （Unauthorized）|
|403|拒绝访问 （Forbidden）|
|404|页面不存在 （NOT FOUND）|');
INSERT INTO os_api_doc VALUES ('2','7','<h5><a id="_0"></a>简要描述</h5>
<ul>
<li>
<h6><a id="_2"></a>获取权限列表</h6>
</li>
</ul>
<h5><a id="_4"></a>请求地址</h5>
<ul>
<li><a href="https://www.fanglonger.com/api/v1/auth/index" target="_blank">https://www.fanglonger.com/api/v1/auth/index</a></li>
</ul>
<h5><a id="_7"></a>请求方式</h5>
<ul>
<li>
<h6><a id="POST_9"></a>POST</h6>
</li>
</ul>
<h5><a id="_11"></a>参数</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>必选</th>
<th>类型</th>
<th>说明</th>
<th>默认值</th>
</tr>
</thead>
<tbody>
<tr>
<td>token</td>
<td>是</td>
<td>string</td>
<td>登录态认证</td>
<td>无</td>
</tr>
</tbody>
</table>
<h5><a id="_17"></a>返回成功实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">200</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"successfully"</span>,
    <span class="hljs-attr">"item"</span>: {
        <span class="hljs-attr">"authLists"</span>: [
            {
                <span class="hljs-attr">"id"</span>: <span class="hljs-number">1</span>,
                <span class="hljs-attr">"name"</span>: <span class="hljs-string">"权限设置"</span>,
                <span class="hljs-attr">"href"</span>: <span class="hljs-string">"/admin/auth/default"</span>,
                <span class="hljs-attr">"pid"</span>: <span class="hljs-number">0</span>,
                <span class="hljs-attr">"path"</span>: <span class="hljs-string">"1"</span>,
                <span class="hljs-attr">"level"</span>: <span class="hljs-number">0</span>,
                <span class="hljs-attr">"status"</span>: <span class="hljs-number">1</span>
            }
        ],
        <span class="hljs-attr">"selectAuth"</span>: [
            {
                <span class="hljs-attr">"id"</span>: <span class="hljs-number">1</span>,
                <span class="hljs-attr">"name"</span>: <span class="hljs-string">"权限设置"</span>,
                <span class="hljs-attr">"href"</span>: <span class="hljs-string">"/admin/auth/default"</span>,
                <span class="hljs-attr">"pid"</span>: <span class="hljs-number">0</span>,
                <span class="hljs-attr">"path"</span>: <span class="hljs-string">"1"</span>,
                <span class="hljs-attr">"level"</span>: <span class="hljs-number">0</span>,
                <span class="hljs-attr">"status"</span>: <span class="hljs-number">1</span>
            }
        ]
    }
}
</code></div></pre>
<h5><a id="_49"></a>返回参数说明</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>类型</th>
<th>说明</th>
</tr>
</thead>
<tbody>
<tr>
<td>code</td>
<td>integer</td>
<td>200 成功</td>
</tr>
</tbody>
</table>
<h5><a id="_55"></a>失败实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">405</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"Method Not Allowed"</span>
}
</code></div></pre>','##### 简要描述

- ###### 获取权限列表

##### 请求地址
- [https://www.fanglonger.com/api/v1/auth/index](https://www.fanglonger.com/api/v1/auth/index)
 
##### 请求方式

- ###### POST

##### 参数

|参数名|必选|类型|说明|默认值|
|---|---|---|---|---|
|token|是|string|登录态认证|无|

##### 返回成功实例

```JSON
{
    "code": 200,
    "msg": "successfully",
    "item": {
        "authLists": [
            {
                "id": 1,
                "name": "权限设置",
                "href": "/admin/auth/default",
                "pid": 0,
                "path": "1",
                "level": 0,
                "status": 1
            }
        ],
        "selectAuth": [
            {
                "id": 1,
                "name": "权限设置",
                "href": "/admin/auth/default",
                "pid": 0,
                "path": "1",
                "level": 0,
                "status": 1
            }
        ]
    }
}
```
##### 返回参数说明

|参数名|类型|说明|
|---|---|---|
|code|integer|200 成功|

##### 失败实例

```JSON
{
    "code": 405,
    "msg": "Method Not Allowed"
}
```');
INSERT INTO os_api_doc VALUES ('3','2','<h4><a id="_0"></a>权限管理</h4>
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" checked="" disabled="" type="checkbox"> 1. 权限列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" checked="" disabled="" type="checkbox"> 1. 更新权限</li>
<li class="task-list-item"><input class="task-list-item-checkbox" checked="" disabled="" type="checkbox"> 2. 保存权限</li>
<li class="task-list-item"><input class="task-list-item-checkbox" checked="" disabled="" type="checkbox"> 3. 删除权限</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 管理员列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 更新管理员列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 保存管理员列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 删除管理员列表</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 角色列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 更新角色列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 保存角色列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 删除角色列表</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 授权用户列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 更新授权用户列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 保存授权用户列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 删除授权用户列表</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 5. 申请授权列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 更新申请授权列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 保存申请授权列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 删除申请授权列表</li>
</ul>
</li>
</ul>','#### 权限管理
   - [x] 1. 权限列表  
      - [x] 1. 更新权限
      - [x] 2. 保存权限
      - [x] 3. 删除权限
  - [ ] 2. 管理员列表
      - [ ] 1. 更新管理员列表
      - [ ] 2. 保存管理员列表
      - [ ] 3. 删除管理员列表
   - [ ] 3. 角色列表
      - [ ] 1. 更新角色列表
      - [ ] 2. 保存角色列表
      - [ ] 3. 删除角色列表 
   - [ ] 4. 授权用户列表
      - [ ] 1. 更新授权用户列表
      - [ ] 2. 保存授权用户列表
      - [ ] 3. 删除授权用户列表 
   - [ ] 5. 申请授权列表
      - [ ] 1. 更新申请授权列表
      - [ ] 2. 保存申请授权列表
      - [ ] 3. 删除申请授权列表');
INSERT INTO os_api_doc VALUES ('4','3','<h4><a id="_0"></a>文件管理</h4>
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" checked="" disabled="" type="checkbox"> 1. 文件列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 文件权限</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 文件读取</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 文件压缩</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 文件解压</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 5. 文件上传</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 6. 文件下载</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 7. 文件更新</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 8. 文件重命名</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 9. 文件删除</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 10. 文件保存</li>
</ul>
</li>
</ul>','#### 文件管理
- [x] 1. 文件列表
    - [ ] 1. 文件权限
    - [ ] 2. 文件读取
    - [ ] 3. 文件压缩
    - [ ] 4. 文件解压
    - [ ] 5. 文件上传
    - [ ] 6. 文件下载
    - [ ] 7. 文件更新
    - [ ] 8. 文件重命名
    - [ ] 9. 文件删除
    - [ ] 10. 文件保存');
INSERT INTO os_api_doc VALUES ('5','4','<h4><a id="_0"></a>系统管理</h4>
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 数据表列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 数据表备份</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 数据表优化</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 数据表修复</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 数据表修改</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 日志列表
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1.日志删除</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 基础配置
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 基础配置保存</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 基础配置更新</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 基础配置删除</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 基础配置值更新</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 城市列表</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 5. 站内通知
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 站内通知保存</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 站内通知更新</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 站内通知删除</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 站内通知读取</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 6. 个人中心
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 更新用户信息</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 7. 系统日志</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 8. 聊天记录</li>
</ul>','#### 系统管理
- [ ] 1. 数据表列表
    - [ ] 1. 数据表备份
    - [ ] 2. 数据表优化
    - [ ] 3. 数据表修复
    - [ ] 4. 数据表修改
- [ ] 2. 日志列表
    - [ ] 1.日志删除 
- [ ] 3. 基础配置
    - [ ] 1. 基础配置保存
    - [ ] 2. 基础配置更新
    - [ ] 3. 基础配置删除
    - [ ] 4. 基础配置值更新
- [ ] 4. 城市列表
- [ ] 5. 站内通知
    - [ ] 1. 站内通知保存
    - [ ] 2. 站内通知更新
    - [ ] 3. 站内通知删除
    - [ ] 4. 站内通知读取
- [ ] 6. 个人中心
    - [ ] 1. 更新用户信息
- [ ] 7. 系统日志
- [ ] 8. 聊天记录');
INSERT INTO os_api_doc VALUES ('6','5','<h4><a id="_0"></a>项目管理</h4>
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 项目列表 （JSON）
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. 分类保存</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. 分类更新</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. 分类删除</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 4. 接口保存</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 5. 接口更新</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 6. 接口详情</li>
</ul>
</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. APiDoc （MarkDown）
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. APiDoc保存</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. APiDoc更新</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 3. APiDoc删除</li>
</ul>
</li>
</ul>','#### 项目管理
- [ ] 1. 项目列表 （JSON）
   - [ ] 1. 分类保存
   - [ ] 2. 分类更新
   - [ ] 3. 分类删除
   - [ ] 4. 接口保存
   - [ ] 5. 接口更新
   - [ ] 6. 接口详情
- [ ] 2. APiDoc （MarkDown）
   - [ ] 1. APiDoc保存
   - [ ] 2. APiDoc更新
   - [ ] 3. APiDoc删除');
INSERT INTO os_api_doc VALUES ('7','6','<h4><a id="_0"></a>编辑器管理</h4>
<ul class="contains-task-list">
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 1. MarkDown</li>
<li class="task-list-item"><input class="task-list-item-checkbox" disabled="" type="checkbox"> 2. Quill (富文本)</li>
</ul>','#### 编辑器管理
- [ ] 1. MarkDown
- [ ] 2. Quill (富文本)');
INSERT INTO os_api_doc VALUES ('8','10','<h5><a id="_0"></a>简要描述</h5>
<ul>
<li>
<h6><a id="_2"></a>删除权限列表</h6>
</li>
</ul>
<h5><a id="_4"></a>请求地址</h5>
<ul>
<li><a href="https://www.fanglonger.com/api/v1/auth/delete" target="_blank">https://www.fanglonger.com/api/v1/auth/delete</a></li>
</ul>
<h5><a id="_7"></a>请求方式</h5>
<ul>
<li>
<h6><a id="POST_9"></a>POST</h6>
</li>
</ul>
<h5><a id="_11"></a>参数</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>必选</th>
<th>类型</th>
<th>说明</th>
<th>默认值</th>
</tr>
</thead>
<tbody>
<tr>
<td>token</td>
<td>是</td>
<td>string</td>
<td>登录态认证</td>
<td>无</td>
</tr>
<tr>
<td>id</td>
<td>是</td>
<td>intger</td>
<td>权限ID</td>
<td>无</td>
</tr>
<tr>
<td>level</td>
<td>是</td>
<td>intger</td>
<td>权限层级</td>
<td>无</td>
</tr>
</tbody>
</table>
<h5><a id="_19"></a>返回成功实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">200</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"successfully"</span>,
}
</code></div></pre>
<h5><a id="_27"></a>返回参数说明</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>类型</th>
<th>说明</th>
</tr>
</thead>
<tbody>
<tr>
<td>code</td>
<td>integer</td>
<td>200 成功</td>
</tr>
</tbody>
</table>
<h5><a id="_33"></a>失败实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">405</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"Method Not Allowed"</span>
}
</code></div></pre>','##### 简要描述

- ###### 删除权限列表

##### 请求地址
- [https://www.fanglonger.com/api/v1/auth/delete](https://www.fanglonger.com/api/v1/auth/delete)
 
##### 请求方式

- ###### POST

##### 参数

|参数名|必选|类型|说明|默认值|
|---|---|---|---|---|
|token|是|string|登录态认证|无|
|id|是|intger|权限ID|无|
|level|是|intger|权限层级|无|

##### 返回成功实例

```JSON
{
    "code": 200,
    "msg": "successfully",
}
```
##### 返回参数说明

|参数名|类型|说明|
|---|---|---|
|code|integer|200 成功|

##### 失败实例

```JSON
{
    "code": 405,
    "msg": "Method Not Allowed"
}
```');
INSERT INTO os_api_doc VALUES ('9','8','<h5><a id="_0"></a>简要描述</h5>
<ul>
<li>
<h6><a id="_2"></a>更新权限列表</h6>
</li>
</ul>
<h5><a id="_4"></a>请求地址</h5>
<ul>
<li><a href="https://www.fanglonger.com/api/v1/auth/update" target="_blank">https://www.fanglonger.com/api/v1/auth/update</a></li>
</ul>
<h5><a id="_7"></a>请求方式</h5>
<ul>
<li>
<h6><a id="POST_9"></a>POST</h6>
</li>
</ul>
<h5><a id="_11"></a>参数</h5>
<div class="hljs-center">
<table>
<thead>
<tr>
<th>参数名</th>
<th>必选</th>
<th>类型</th>
<th>说明</th>
<th>默认值</th>
</tr>
</thead>
<tbody>
<tr>
<td>token</td>
<td>是</td>
<td>string</td>
<td>登录态认证</td>
<td>无</td>
</tr>
<tr>
<td>act</td>
<td>是</td>
<td>string</td>
<td>用户行为(0:更新权限,1:更新状态)</td>
<td>0</td>
</tr>
<tr>
<td>name</td>
<td>是</td>
<td>string</td>
<td>权限名称</td>
<td>无</td>
</tr>
<tr>
<td>href</td>
<td>是</td>
<td>string</td>
<td>权限地址</td>
<td>无</td>
</tr>
<tr>
<td>pid</td>
<td>是</td>
<td>integer</td>
<td>权限上级</td>
<td>0</td>
</tr>
<tr>
<td>id</td>
<td>是</td>
<td>integer</td>
<td>权限ID</td>
<td>0</td>
</tr>
<tr>
<td>path</td>
<td>是</td>
<td>string</td>
<td>权限路径</td>
<td>0</td>
</tr>
<tr>
<td>status</td>
<td>是</td>
<td>integer</td>
<td>显示状态（1 是 2 否）</td>
<td>1</td>
</tr>
</tbody>
</table>
</div>
<h5><a id="_26"></a>返回成功实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">200</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"update rule successfully"</span>,
}
</code></div></pre>
<h5><a id="_34"></a>返回参数说明</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>类型</th>
<th>说明</th>
</tr>
</thead>
<tbody>
<tr>
<td>code</td>
<td>integer</td>
<td>200 成功</td>
</tr>
</tbody>
</table>
<h5><a id="_40"></a>失败实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">201</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"update rule error"</span>
}
</code></div></pre>','##### 简要描述

- ###### 更新权限列表

##### 请求地址
- [https://www.fanglonger.com/api/v1/auth/update](https://www.fanglonger.com/api/v1/auth/update)
 
##### 请求方式

- ###### POST

##### 参数
::: hljs-center
|参数名|必选|类型|说明|默认值|
|---|---|---|---|---|
|token|是|string|登录态认证|无|
|act|是|string|用户行为(0:更新权限,1:更新状态)|0|
|name|是|string|权限名称|无|
|href|是|string|权限地址|无|
|pid|是|integer|权限上级|0|
|id|是|integer|权限ID|0|
|path|是|string|权限路径|0|
|status|是|integer|显示状态（1 是 2 否）|1|
:::


##### 返回成功实例

```JSON
{
    "code": 200,
    "msg": "update rule successfully",
}
```
##### 返回参数说明

|参数名|类型|说明|
|---|---|---|
|code|integer|200 成功|

##### 失败实例

```JSON
{
    "code": 201,
    "msg": "update rule error"
}
```');
INSERT INTO os_api_doc VALUES ('10','9','<h5><a id="_0"></a>简要描述</h5>
<ul>
<li>
<h6><a id="_2"></a>保存权限列表</h6>
</li>
</ul>
<h5><a id="_4"></a>请求地址</h5>
<ul>
<li><a href="https://www.fanglonger.com/api/v1/auth/save" target="_blank">https://www.fanglonger.com/api/v1/auth/save</a></li>
</ul>
<h5><a id="_7"></a>请求方式</h5>
<ul>
<li>
<h6><a id="POST_9"></a>POST</h6>
</li>
</ul>
<h5><a id="_11"></a>参数</h5>
<div class="hljs-center">
<table>
<thead>
<tr>
<th>参数名</th>
<th>必选</th>
<th>类型</th>
<th>说明</th>
<th>默认值</th>
</tr>
</thead>
<tbody>
<tr>
<td>token</td>
<td>是</td>
<td>string</td>
<td>登录态认证</td>
<td>无</td>
</tr>
<tr>
<td>name</td>
<td>是</td>
<td>string</td>
<td>权限名称</td>
<td>无</td>
</tr>
<tr>
<td>href</td>
<td>是</td>
<td>string</td>
<td>权限地址</td>
<td>无</td>
</tr>
<tr>
<td>pid</td>
<td>是</td>
<td>integer</td>
<td>权限上级</td>
<td>0</td>
</tr>
<tr>
<td>id</td>
<td>是</td>
<td>integer</td>
<td>权限ID</td>
<td>0</td>
</tr>
<tr>
<td>path</td>
<td>是</td>
<td>string</td>
<td>权限路径</td>
<td>0</td>
</tr>
<tr>
<td>status</td>
<td>是</td>
<td>integer</td>
<td>显示状态（1 是 2 否）</td>
<td>1</td>
</tr>
</tbody>
</table>
</div>
<h5><a id="_25"></a>返回成功实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">200</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"save rule successfully"</span>,
}
</code></div></pre>
<h5><a id="_33"></a>返回参数说明</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>类型</th>
<th>说明</th>
</tr>
</thead>
<tbody>
<tr>
<td>code</td>
<td>integer</td>
<td>200 成功</td>
</tr>
</tbody>
</table>
<h5><a id="_39"></a>失败实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">201</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"save rule error"</span>
}
</code></div></pre>','##### 简要描述

- ###### 保存权限列表

##### 请求地址
- [https://www.fanglonger.com/api/v1/auth/save](https://www.fanglonger.com/api/v1/auth/save)
 
##### 请求方式

- ###### POST

##### 参数
::: hljs-center
|参数名|必选|类型|说明|默认值|
|---|---|---|---|---|
|token|是|string|登录态认证|无|
|name|是|string|权限名称|无|
|href|是|string|权限地址|无|
|pid|是|integer|权限上级|0|
|id|是|integer|权限ID|0|
|path|是|string|权限路径|0|
|status|是|integer|显示状态（1 是 2 否）|1|
:::


##### 返回成功实例

```JSON
{
    "code": 200,
    "msg": "save rule successfully",
}
```
##### 返回参数说明

|参数名|类型|说明|
|---|---|---|
|code|integer|200 成功|

##### 失败实例

```JSON
{
    "code": 201,
    "msg": "save rule error"
}
```');
INSERT INTO os_api_doc VALUES ('11','15','<h5><a id="_0"></a>简要描述</h5>
<ul>
<li>
<h6><a id="_2"></a>获取文件列表</h6>
</li>
</ul>
<h5><a id="_4"></a>请求地址</h5>
<ul>
<li><a href="https://www.fanglonger.com/api/v1/file/index" target="_blank">https://www.fanglonger.com/api/v1/file/index</a></li>
</ul>
<h5><a id="_7"></a>请求方式</h5>
<ul>
<li>
<h6><a id="POST_9"></a>POST</h6>
</li>
</ul>
<h5><a id="_11"></a>参数</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>必选</th>
<th>类型</th>
<th>说明</th>
<th>默认值</th>
</tr>
</thead>
<tbody>
<tr>
<td>token</td>
<td>是</td>
<td>string</td>
<td>登录态认证</td>
<td>无</td>
</tr>
<tr>
<td>path</td>
<td>是</td>
<td>string</td>
<td>文件路径（base_path,storage_path,public_path）</td>
<td>无</td>
</tr>
<tr>
<td>basename</td>
<td>是</td>
<td>string</td>
<td>文件名</td>
<td>无</td>
</tr>
</tbody>
</table>
<h5><a id="_19"></a>返回成功实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">200</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"successfully"</span>,
    <span class="hljs-attr">"item"</span>: [
        {
            <span class="hljs-attr">"label"</span>: <span class="hljs-string">"routes"</span>,
            <span class="hljs-attr">"fileType"</span>: <span class="hljs-string">"dir"</span>,
            <span class="hljs-attr">"children"</span>: [
                {
                    <span class="hljs-attr">"label"</span>: <span class="hljs-string">"channels.php"</span>,
                    <span class="hljs-attr">"fileType"</span>: <span class="hljs-string">"file"</span>,
                    <span class="hljs-attr">"children"</span>: [],
                    <span class="hljs-attr">"path"</span>: <span class="hljs-string">"D:\\xampp\\htdocs\\www\\longer\\routes/channels.php"</span>,
                    <span class="hljs-attr">"size"</span>: <span class="hljs-string">"bb8f9da38cd96bf25a4e8d8b762dac3a"</span>,
                    <span class="hljs-attr">"auth"</span>: <span class="hljs-string">"666"</span>
                },
                {
                    <span class="hljs-attr">"label"</span>: <span class="hljs-string">"console.php"</span>,
                    <span class="hljs-attr">"fileType"</span>: <span class="hljs-string">"file"</span>,
                    <span class="hljs-attr">"children"</span>: [],
                    <span class="hljs-attr">"path"</span>: <span class="hljs-string">"D:\\xampp\\htdocs\\www\\longer\\routes/console.php"</span>,
                    <span class="hljs-attr">"size"</span>: <span class="hljs-string">"e0b7caa17a7666f1fb8d131d075ca5c4"</span>,
                    <span class="hljs-attr">"auth"</span>: <span class="hljs-string">"666"</span>
                },
                {
                    <span class="hljs-attr">"label"</span>: <span class="hljs-string">"web.php"</span>,
                    <span class="hljs-attr">"fileType"</span>: <span class="hljs-string">"file"</span>,
                    <span class="hljs-attr">"children"</span>: [],
                    <span class="hljs-attr">"path"</span>: <span class="hljs-string">"D:\\xampp\\htdocs\\www\\longer\\routes/web.php"</span>,
                    <span class="hljs-attr">"size"</span>: <span class="hljs-string">"e14209990efaeb3f341c63e212bd1a98"</span>,
                    <span class="hljs-attr">"auth"</span>: <span class="hljs-string">"666"</span>
                },
                {
                    <span class="hljs-attr">"label"</span>: <span class="hljs-string">"api.php"</span>,
                    <span class="hljs-attr">"fileType"</span>: <span class="hljs-string">"file"</span>,
                    <span class="hljs-attr">"children"</span>: [],
                    <span class="hljs-attr">"path"</span>: <span class="hljs-string">"D:\\xampp\\htdocs\\www\\longer\\routes/api.php"</span>,
                    <span class="hljs-attr">"size"</span>: <span class="hljs-string">"b87d825b5e257af999650a24574970ba"</span>,
                    <span class="hljs-attr">"auth"</span>: <span class="hljs-string">"666"</span>
                }
            ],
            <span class="hljs-attr">"path"</span>: <span class="hljs-string">"D:\\xampp\\htdocs\\www\\longer\\routes/"</span>,
            <span class="hljs-attr">"size"</span>: <span class="hljs-string">"118f026f280150638c6c9d4fb9aa4907"</span>,
            <span class="hljs-attr">"auth"</span>: <span class="hljs-string">"777"</span>
        }
    ]
}
</code></div></pre>
<h5><a id="_70"></a>返回参数说明</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>类型</th>
<th>说明</th>
</tr>
</thead>
<tbody>
<tr>
<td>code</td>
<td>integer</td>
<td>200 成功</td>
</tr>
</tbody>
</table>
<h5><a id="_76"></a>失败实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">405</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"Method Not Allowed"</span>
}
</code></div></pre>','##### 简要描述

- ###### 获取文件列表

##### 请求地址
- [https://www.fanglonger.com/api/v1/file/index](https://www.fanglonger.com/api/v1/file/index)
 
##### 请求方式

- ###### POST

##### 参数

|参数名|必选|类型|说明|默认值|
|---|---|---|---|---|
|token|是|string|登录态认证|无|
|path|是|string|文件路径（base_path,storage_path,public_path）|无|
|basename|是|string|文件名|无|

##### 返回成功实例

```JSON
{
    "code": 200,
    "msg": "successfully",
    "item": [
        {
            "label": "routes",
            "fileType": "dir",
            "children": [
                {
                    "label": "channels.php",
                    "fileType": "file",
                    "children": [],
                    "path": "D:\\xampp\\htdocs\\www\\longer\\routes/channels.php",
                    "size": "bb8f9da38cd96bf25a4e8d8b762dac3a",
                    "auth": "666"
                },
                {
                    "label": "console.php",
                    "fileType": "file",
                    "children": [],
                    "path": "D:\\xampp\\htdocs\\www\\longer\\routes/console.php",
                    "size": "e0b7caa17a7666f1fb8d131d075ca5c4",
                    "auth": "666"
                },
                {
                    "label": "web.php",
                    "fileType": "file",
                    "children": [],
                    "path": "D:\\xampp\\htdocs\\www\\longer\\routes/web.php",
                    "size": "e14209990efaeb3f341c63e212bd1a98",
                    "auth": "666"
                },
                {
                    "label": "api.php",
                    "fileType": "file",
                    "children": [],
                    "path": "D:\\xampp\\htdocs\\www\\longer\\routes/api.php",
                    "size": "b87d825b5e257af999650a24574970ba",
                    "auth": "666"
                }
            ],
            "path": "D:\\xampp\\htdocs\\www\\longer\\routes/",
            "size": "118f026f280150638c6c9d4fb9aa4907",
            "auth": "777"
        }
    ]
}
```
##### 返回参数说明

|参数名|类型|说明|
|---|---|---|
|code|integer|200 成功|

##### 失败实例

```JSON
{
    "code": 405,
    "msg": "Method Not Allowed"
}
```');
INSERT INTO os_api_doc VALUES ('12','16','<h5><a id="_0"></a>简要描述</h5>
<ul>
<li>
<h6><a id="_2"></a>设置文件权限</h6>
</li>
</ul>
<h5><a id="_4"></a>请求地址</h5>
<ul>
<li><a href="https://www.fanglonger.com/api/v1/file/chmod" target="_blank">https://www.fanglonger.com/api/v1/file/chmod</a></li>
</ul>
<h5><a id="_7"></a>请求方式</h5>
<ul>
<li>
<h6><a id="POST_9"></a>POST</h6>
</li>
</ul>
<h5><a id="_11"></a>参数</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>必选</th>
<th>类型</th>
<th>说明</th>
<th>默认值</th>
</tr>
</thead>
<tbody>
<tr>
<td>token</td>
<td>是</td>
<td>string</td>
<td>登录态认证</td>
<td>无</td>
</tr>
<tr>
<td>auth</td>
<td>是</td>
<td>integer</td>
<td>权限值</td>
<td>无</td>
</tr>
<tr>
<td>path</td>
<td>是</td>
<td>string</td>
<td>文件路径</td>
<td>无</td>
</tr>
</tbody>
</table>
<h5><a id="_19"></a>返回成功实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">200</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"successfully"</span>,
}
</code></div></pre>
<h5><a id="_27"></a>返回参数说明</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>类型</th>
<th>说明</th>
</tr>
</thead>
<tbody>
<tr>
<td>code</td>
<td>integer</td>
<td>200 成功</td>
</tr>
</tbody>
</table>
<h5><a id="_33"></a>失败实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">405</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"Method Not Allowed"</span>
}
</code></div></pre>','##### 简要描述

- ###### 设置文件权限

##### 请求地址
- [https://www.fanglonger.com/api/v1/file/chmod](https://www.fanglonger.com/api/v1/file/chmod)
 
##### 请求方式

- ###### POST

##### 参数

|参数名|必选|类型|说明|默认值|
|---|---|---|---|---|
|token|是|string|登录态认证|无|
|auth|是|integer|权限值|无|
|path|是|string|文件路径|无|

##### 返回成功实例

```JSON
{
    "code": 200,
    "msg": "successfully",
}
```
##### 返回参数说明

|参数名|类型|说明|
|---|---|---|
|code|integer|200 成功|

##### 失败实例

```JSON
{
    "code": 405,
    "msg": "Method Not Allowed"
}
```');
INSERT INTO os_api_doc VALUES ('13','16','<h5><a id="_0"></a>简要描述</h5>
<ul>
<li>
<h6><a id="_2"></a>设置文件权限</h6>
</li>
</ul>
<h5><a id="_4"></a>请求地址</h5>
<ul>
<li><a href="https://www.fanglonger.com/api/v1/file/chmod" target="_blank">https://www.fanglonger.com/api/v1/file/chmod</a></li>
</ul>
<h5><a id="_7"></a>请求方式</h5>
<ul>
<li>
<h6><a id="POST_9"></a>POST</h6>
</li>
</ul>
<h5><a id="_11"></a>参数</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>必选</th>
<th>类型</th>
<th>说明</th>
<th>默认值</th>
</tr>
</thead>
<tbody>
<tr>
<td>token</td>
<td>是</td>
<td>string</td>
<td>登录态认证</td>
<td>无</td>
</tr>
<tr>
<td>auth</td>
<td>是</td>
<td>integer</td>
<td>权限值</td>
<td>无</td>
</tr>
<tr>
<td>path</td>
<td>是</td>
<td>string</td>
<td>文件路径</td>
<td>无</td>
</tr>
</tbody>
</table>
<h5><a id="_19"></a>返回成功实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">200</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"successfully"</span>,
}
</code></div></pre>
<h5><a id="_27"></a>返回参数说明</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>类型</th>
<th>说明</th>
</tr>
</thead>
<tbody>
<tr>
<td>code</td>
<td>integer</td>
<td>200 成功</td>
</tr>
</tbody>
</table>
<h5><a id="_33"></a>失败实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">405</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"Method Not Allowed"</span>
}
</code></div></pre>','##### 简要描述

- ###### 设置文件权限

##### 请求地址
- [https://www.fanglonger.com/api/v1/file/chmod](https://www.fanglonger.com/api/v1/file/chmod)
 
##### 请求方式

- ###### POST

##### 参数

|参数名|必选|类型|说明|默认值|
|---|---|---|---|---|
|token|是|string|登录态认证|无|
|auth|是|integer|权限值|无|
|path|是|string|文件路径|无|

##### 返回成功实例

```JSON
{
    "code": 200,
    "msg": "successfully",
}
```
##### 返回参数说明

|参数名|类型|说明|
|---|---|---|
|code|integer|200 成功|

##### 失败实例

```JSON
{
    "code": 405,
    "msg": "Method Not Allowed"
}
```');
INSERT INTO os_api_doc VALUES ('14','16','<h5><a id="_0"></a>简要描述</h5>
<ul>
<li>
<h6><a id="_2"></a>设置文件权限</h6>
</li>
</ul>
<h5><a id="_4"></a>请求地址</h5>
<ul>
<li><a href="https://www.fanglonger.com/api/v1/file/chmod" target="_blank">https://www.fanglonger.com/api/v1/file/chmod</a></li>
</ul>
<h5><a id="_7"></a>请求方式</h5>
<ul>
<li>
<h6><a id="POST_9"></a>POST</h6>
</li>
</ul>
<h5><a id="_11"></a>参数</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>必选</th>
<th>类型</th>
<th>说明</th>
<th>默认值</th>
</tr>
</thead>
<tbody>
<tr>
<td>token</td>
<td>是</td>
<td>string</td>
<td>登录态认证</td>
<td>无</td>
</tr>
<tr>
<td>auth</td>
<td>是</td>
<td>integer</td>
<td>权限值</td>
<td>无</td>
</tr>
<tr>
<td>path</td>
<td>是</td>
<td>string</td>
<td>文件路径</td>
<td>无</td>
</tr>
</tbody>
</table>
<h5><a id="_19"></a>返回成功实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">200</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"successfully"</span>
}
</code></div></pre>
<h5><a id="_27"></a>返回参数说明</h5>
<table>
<thead>
<tr>
<th>参数名</th>
<th>类型</th>
<th>说明</th>
</tr>
</thead>
<tbody>
<tr>
<td>code</td>
<td>integer</td>
<td>200 成功</td>
</tr>
</tbody>
</table>
<h5><a id="_33"></a>失败实例</h5>
<pre><div class="hljs"><code class="lang-JSON">{
    <span class="hljs-attr">"code"</span>: <span class="hljs-number">405</span>,
    <span class="hljs-attr">"msg"</span>: <span class="hljs-string">"Method Not Allowed"</span>
}
</code></div></pre>','##### 简要描述

- ###### 设置文件权限

##### 请求地址
- [https://www.fanglonger.com/api/v1/file/chmod](https://www.fanglonger.com/api/v1/file/chmod)
 
##### 请求方式

- ###### POST

##### 参数

|参数名|必选|类型|说明|默认值|
|---|---|---|---|---|
|token|是|string|登录态认证|无|
|auth|是|integer|权限值|无|
|path|是|string|文件路径|无|

##### 返回成功实例

```JSON
{
    "code": 200,
    "msg": "successfully"
}
```
##### 返回参数说明

|参数名|类型|说明|
|---|---|---|
|code|integer|200 成功|

##### 失败实例

```JSON
{
    "code": 405,
    "msg": "Method Not Allowed"
}
```');
-- ----------------------------
-- 2021-07-15 10:47:05 backup table end
-- ----------------------------
