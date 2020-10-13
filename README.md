## Typecho挂件一言插件
[![Apache2](https://camo.githubusercontent.com/64d506383be67decddf8968e3b0072c3e9ba4a84/68747470733a2f2f696d672e736869656c64732e696f2f686578706d2f6c2f706c75672e737667)](LICENSE)
[![HitCount](http://hits.dwyl.io/LittleJake/Typecho-hitokoto.svg)](http://hits.dwyl.io/LittleJake/Typecho-hitokoto)
> 无论在哪里遇到你，我都会喜欢上你。

调用一言接口 `https://v1.hitokoto.cn/` 返回json数据

现在接口设置为可选：国内、国际

具体接口调用选项参考 [这里](https://hitokoto.cn/api)


json：
````json
// https://v1.hitokoto.cn/
// https://international.v1.hitokoto.cn/

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

1. `git clone`或 [zip下载](https://github.com/LittleJake/Typecho-Hitokoto/releases) ，在 usr/plugins 文件夹内创建文件夹 Hitokoto 并放入文件，文件夹权限0755，插件文件0644。

2. 打开Typecho后台激活插件

3. 在挂件位置插入
```php
<?php echo Hitokoto_Plugin::getHitokoto() ?>
```

4. 根据站点需要，在设置中定制个性化需求、修改自定义模板

    

### 注意
1. PHP 需启用 curl

## 鸣谢
特别感谢一言提供接口。

[点此](https://afdian.net/@hitokoto) 打赏一言作者~