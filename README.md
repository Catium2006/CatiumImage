# CatiumImage
一个简易图床

## 数据库搭建
用`createdb.sql`.  
数据库建好之后, 在mgmt表中插入一行, 然后记着替换成你自己的密码MD5还有网页访问网址.  
```sql
INSERT INTO `img_upload`.`mgmt`
INSERT INTO `img_upload`.`mgmt`
(`id`,
`password_md5`,
`background_img`,
`grid_img`,
`visit_count`,
`file_count`,
`notice`,
`domain`,
`adminmail`)
VALUES
(0,
'<{password_md5: }>',
'',
'',
0,
0,
'图床开业大吉',
'https://img.example.com/',
'admin@example.com');

```


## 网页搭建
把`www`目录下的东西放nginx或者什么服务器就行(要开启php扩展).  
记得修改`www/db.php`里面登录数据库的口令(默认是`web/web`).  

如果出现白屏, 500错误, Access Denied, 之类的报错请检查www下文件权限.  
`www/upload/`权限也要检查一下, 上传的图片都保存在这里.  


## 邮件服务
需要配合postfix之类的邮件服务器, 并自行处理外域邮箱收发相关问题.  