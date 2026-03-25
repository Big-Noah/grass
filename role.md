
如果要求“用短代码做”，默认按下面的结构新增：

1. 在 `inc/render/` 新建这个模块自己的 PHP
2. 在 `inc/shortcodes/` 新建或补充这个模块自己的短代码注册文件
3. 在 `assets/css/` 新建这个模块自己的 CSS
4. 如果有交互，在 `assets/js/` 新建这个模块自己的 JS
5. `functions.php` 只负责把短代码入口文件引进来
6、项目修改完后  C:\Users\61668\code\wp-content>  git add .  git commit , git push  
7、 
6、我如果让你用MCP 那要求的网址是 https://muukal.com/






为了让docker 同步， git 完成后 还需要C:\Users\61668\code\wp-content>
 powershell -ExecutionPolicy Bypass -File .\deploy-github-to-container.ps1

 执行完上诉内容我才能通过mcp 看到网页端效果