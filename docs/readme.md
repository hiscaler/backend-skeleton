简介
===

## 目录结构
      assets/             包含资源文件路径定义
      commands/           命令行脚本
      config/             应用程序配置
      controllers/        控制器
      docs/               文档
      docs/api            API 文档
      docs/db-dict        数据词典
      docs/guide          系统开发手册（开发人员）
      docs/manual         用户使用手册（使用人员）
      docs/releases       版本发布文档
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

## 操作手册撰写
操作手册指的是针对客户的使用说明。一个完整的项目中应该包含一份相关操作文档，方便客户使用。

本系统中，您可以将相关的文档撰写在 `docs/manual` 中，每个类别类型的文档应该建立一个子目录，然后将相关文档撰写在您创建的目录中。

系统中，我们均使用 Markdown 格式作为系统的文档文件格式。相关的语法您可以参考 [Markdown 语法介绍](https://coding.net/help/doc/project/markdown.html)

您撰写完毕文档之后，为了更好的展示您的文档（比如文档的先后顺序），您还需要配置 `docs/manual/config.json` 文件，该文件为一个合法的 JSON 格式文件。包含了 `author`, `lastModify`, `directories` 三个项目，示例如下：
```json
{
    "author": "hiscaler",
    "lastModify": "2019年4月22日 14:44:34",
    "directories": {
        "default": {
            "name": "readme",
            "title": "系统介绍",
            "docs": [
                {
                    "name": "readme",
                    "title": "系统介绍"
                }
            ]
        },
        "faq": {
            "title": "常见问题",
            "docs": [
                {
                    "name": "readme",
                    "title": "财务管理"
                }
            ]
        },
        "finance": {
            "title": "财务管理",
            "docs": [
                {
                    "name": "readme",
                    "title": "财务管理"
                }
            ]
        }
    }
}
``` 
其中，`author` 项目为文档的作者，多个作者之间请使用小写的逗号进行分隔，`lastModify` 为文档的最后修改时间，`directories` 则为文档的展示设定，单个项目结构如下：
```json
{
    "faq": {
        "title": "常见问题",
        "docs": [
            {
                "name": "faq",
                "title": "财务管理"
            }
        ]
    }
}
```
其中，`faq` 表示的子目录名称，如果该名称为 `default` 则表示为根目录 `docs/manual`

`title` 为显示的标题，`docs` 则用来维护您撰写的文档结构，`name` 表示您的文档文件名称（无文件后缀），`title` 则表示您的展示名称。

需要注意的是在 `directories` 中的设定顺序将确定着您文档的最终目录展示顺序，所以在设置过程中，您应根据您的需求来设定展示的顺序，以便生成一份完美的文档展示给用户。