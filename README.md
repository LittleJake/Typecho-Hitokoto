## Typecho挂件一言插件
[![Apache2](https://camo.githubusercontent.com/64d506383be67decddf8968e3b0072c3e9ba4a84/68747470733a2f2f696d672e736869656c64732e696f2f686578706d2f6c2f706c75672e737667)](LICENSE)
[![HitCount](http://hits.dwyl.io/LittleJake/Typecho-hitokoto.svg)](http://hits.dwyl.io/LittleJake/Typecho-hitokoto)
> 无论在哪里遇到你，我都会喜欢上你。

调用一言接口 `https://v1.hitokoto.cn/?c=a` 返回json

具体接口调用选项参考 [这里](https://hitokoto.cn/api)

api 可自行替换 `Plugin.php` 变量 `$api`

> <del>就是喜欢动漫怎么着</del>

json：
````json
// https://v1.hitokoto.cn/

{
  "id": 542,
  "hitokoto": "由变态化身成为变态，也就是真·变态，也就是说，完全变态呢。",
  "type": "a",
  "from": "一拳超人",
  "creator": "没了",
  "created_at": "1472551518"
}

````

    

## 安装方法

1. `git clone`或zip下载，将 Hitokoto 文件夹放入 usr/plugins 文件夹内，文件夹权限0755，插件文件0644。

2. 打开Typecho后台激活插件

3. 在挂件位置插入
```php
<?php echo Hitokoto_Plugin::getHitokoto() ?>
```

    

### 注意
1. PHP 需启用 curl

## 鸣谢
特别感谢一言提供接口。

[点此](https://afdian.net/@hitokoto) 打赏一言作者~