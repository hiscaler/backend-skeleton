本扩展基于 **Yii2** 队列管理，使用前请使用
```shell
php composer.phar require --prefer-dist yiisoft/yii2-queue
```
直接安装队列扩展，或者在项目的 composer.json 文件中添加
```shell
"yiisoft/yii2-queue": "~2.0"
```
并运行
```shell
php composer update -vvv
```
安装队列扩展

## 相关资料
- [数据表创建](https://github.com/yiisoft/yii2-queue/blob/master/docs/guide/driver-db.md)

注意：暂只针对 **\\yii\\queue\\db\\Queue** 进行处理。