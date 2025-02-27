# 从模型生成表

[toc]

根据模型中 DDL 注解定义，生成表

> 注意，本工具是删除重建表，会丢失数据，生产环境慎重使用！

> imi v1.2.3 版本支持

> PostgreSQL 暂不支持从模型生成表

可选参数：
`--namespace` 模型所在命名空间，支持半角逗号分隔传多个，默认不传则为全部
`--database` 数据库名，不传则取连接池默认配置的库名
`--poolName` 连接池名称，不传则取默认连接池
`--include` 要包含的表名，以半角逗号分隔
`--exclude` 要排除的表名，以半角逗号分隔
`--override` 是否覆盖已存在的表，请慎重！`true`-全覆盖;`false`-不覆盖;默认缺省状态为`false`

示例：

```shell
vendor/bin/imi-xxx generate/table
```

> (`xxx` 根据运行模式不同而不同)

## 事件监听

### IMI.GENERATE_MODEL.BEFORE

生成表工具的前置操作

### IMI.GENERATE_MODEL.AFTER

生成表工具的后置操作
