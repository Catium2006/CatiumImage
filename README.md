# CatiumImage
一个简易图床

## 数据库搭建
用`createdb.sql`.  


## 网页搭建
把`www`目录下的东西放nginx或者什么服务器就行(要开启php扩展).  
记得修改`www/db.php`里面登录数据库的口令(默认是`web/web`).  

如果出现白屏, 500错误, Access Denied, 之类的报错请检查www下文件权限.  