# 短代码开发规则

## 规则 1

`functions.php` 只负责做引入，不负责写业务逻辑，不负责写大段 HTML，不负责写完整短代码结构。

文件位置：

```text
themes/astra/functions.php
```

## 规则 2

所有短代码注册统一放在这个文件夹：

```text
themes/astra/inc/shortcodes/
```

如果是新的独立模块，可以继续新增新的短代码注册文件，不要所有短代码都永久堆在同一个文件里。

## 规则 3

所有 PHP 渲染文件统一放在这个文件夹：

```text
themes/astra/inc/render/
```

每一个独立模块，都要有自己单独的 render PHP 文件。

例如：

- 一个 header 一个 PHP
- 一个 trustpilot 条一个 PHP
- 一个公告条一个 PHP
- 一个 promo 区块一个 PHP

不要把所有模块都写进一个 PHP 文件。

## 规则 4

所有样式文件统一放在这个文件夹：

```text
themes/astra/assets/css/
```

每一个独立模块，都要有自己单独的样式文件。

例如：

- `header.php` 对应 `header.css`
- `trustpilot-bar.php` 对应 `trustpilot-bar.css`
- `promo-strip.php` 对应 `promo-strip.css`

不要把所有模块的样式长期混在一个 CSS 文件里。

## 规则 5

所有交互脚本统一放在这个文件夹：

```text
themes/astra/assets/js/
```

如果某个模块需要 JS，就给这个模块单独建立 JS 文件。

例如：

- `header.js`
- `promo-strip.js`

## 规则 6

如果主人要求“用短代码做”，默认按下面的结构新增：

1. 在 `inc/render/` 新建这个模块自己的 PHP
2. 在 `inc/shortcodes/` 新建或补充这个模块自己的短代码注册文件
3. 在 `assets/css/` 新建这个模块自己的 CSS
4. 如果有交互，在 `assets/js/` 新建这个模块自己的 JS
5. `functions.php` 只负责把短代码入口文件引进来

## 规则 7

如果主人要求“用 PHP 渲染”，那就表示：

- 结构写到 `inc/render/`
- 不要直接把结构写进 Elementor
- 不要直接把结构写进 `functions.php`

## 规则 8

如果主人要求“Elementor 只负责调用”，那就表示：

- Elementor 里只放短代码
- 真正结构由 PHP 输出
- 样式由模块自己的 CSS 负责

## 规则 9

每一个事件、每一个模块、每一个功能块，都要尽量独立：

- 独立 PHP
- 独立 CSS
- 有需要时独立 JS

不要为了省事把多个模块硬塞到同一个 PHP 或同一个 CSS 里。

## 规则 10

命名保持统一，继续使用项目当前前缀：

```text
muukal_
```

用于：

- 短代码名
- PHP 函数名
- 模块命名

## 规则 11

后续新增模块时，优先参考这个目录规则：

```text
themes/astra/
  functions.php
  inc/
    shortcodes/
    render/
  assets/
    css/
    js/
```

不要脱离这个结构另起一套新的组织方式。
