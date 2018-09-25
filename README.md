Backend Skeleton
=================

Backend Skeleton 旨在提供一个通用的后台管理程序，方便用于基于此基础上做拓展和开发。

## 目录结构
      assets/             包含资源文件路径定义
      commands/           命令行脚本
      config/             应用程序配置
      controllers/        控制器
      mail/               邮件模板视图
      models/             模型类
      modules/            模块
      modules/admin       admin 管理模块
      modules/api         api 模块
      runtime/            运行时生成文件
      tests/              测试文件
      vendor/             第三方包
      views/              视图文件
      web/                包含入库文件以及资源文件

## 环境要求
最低要求 PHP>=5.5.9 版本

## 执行核心数据表迁移操作
```bash
yii migrate --migrationPath=@app/migrations
```

## 创建模块
使用 Gii 创建您的模块，或直接复制 example 模块中的内容后做修改。

## 创建模块数据库表迁移文件
```bash
yii migrate/create create_you-module-name_demo_table --migrationPath=@app/modules/admin/modules/YOU-MODULE-NAME/migrations
```

## 执行模块数据表迁移文件
1. 通过后台的模块安装会同步执行数据表迁移文件的处理
2. 执行如下语句
```
yii migrate --migrationPath=@app/modules/admin/modules/YOU-MODULE-NAME/migrations
```

## 注意
1. 模块名称必须是唯一的，且只能由纯英文小写字母组成；
2. 创建数据表迁移文件请记得带上模块名称最为前缀，比如
```
yii migrate/create create_you-module-name_demo_table --migrationPath=@app/modules/admin/modules/YOU-MODULE-NAME/migrations
```
如果开发的模块中只有一个表，且表名称和模块名同名，则可以不需要使用模块名作为前缀；

## 接口开发
接口代码文件的根目录位于 `/modules/api/modules/你的模块名称/` 下，后台启用模块后，系统将会自动处理 URL 访问规则，您只需要编写接口相关代码即可。编写完毕后，使用 http://www.example.com/api/你的模块名称/控制起名称/动作名称 进行访问。