# Muukal 商品内页模仿规划

## 目标页面

- 目标页: `https://muukal.com/frame/goods/gid/1823/gcid/4/gn/black-cat-eye-chic-mixed-materials-eyeglasses.html`
- 当前本地相关实现:
  - 详情页模板: `themes/astra/inc/render/product-detail-template.php`
  - Try On 插件: `plugins/facepp-virtual-tryon/`
  - Select Lenses 插件: `plugins/muukal-lens-replica/`

## 一、这个页面除了 Header / Footer 还有哪些模块

### 1. 可见主页面模块

1. 面包屑导航
2. 商品主区域
3. 左侧产品图册
4. 缩略图列表
5. `TRY-ON` 入口按钮
6. 商品功能标签行
   - Blue Light Blocking
   - Bifocal
   - Progressive
   - Tinted Lenses
   - Daily
7. 右侧商品信息区
   - 商品标题
   - 副标题
   - 价格
   - Customers Like
8. 颜色切换区
9. 尺寸显示区
   - Size
   - Frame size guide
10. 操作按钮区
   - Add to Wishlist
   - Select Lenses
11. Promotion 区
   - 文案条
   - 促销图片
12. 服务保障条
   - 30-day Return & Exchange
   - 3-month Warranty
   - 100% Money Back Guaranteed
   - Save with FSA or HSA dollars
13. 商品信息说明区
   - Measurements
   - Frame dimensions
   - Frame Info
   - Description
   - Warm Tips
14. 推荐商品区
   - You May Also Like
   - View Similar Frames
15. 评论区
   - Customer Reviews

### 2. 隐藏/弹层/辅助模块

1. Try On 弹窗 `#tryonModal`
2. Warranty 说明弹窗 `#tmWarrantyModal`
3. FSA/HSA 说明弹窗 `#fsahsaModal`
4. Warm Tips 详细说明弹窗 `#PadtimeModal`
5. 尺寸说明弹窗 `#sizeModal`
6. Sunglass Lens 说明弹窗 `#SunglassLensModal`
7. Lens Sale 说明弹窗 `#LensSaleExpModal`
8. Prescription 说明弹窗
9. PD / Prism / Prescription 检查相关弹窗
10. `SELECT LENSES` 大型底部抽屉/弹层
11. Add to cart success 弹窗 `#lensModal_success`
12. Progressive 升级确认弹窗
13. 各种促销说明弹窗

### 3. 其中最复杂的两个系统

1. `TRY-ON`
   - 不是普通按钮，而是一个完整弹层应用
   - 有模特图选择
   - 有颜色切换
   - 有手动微调
   - 有 PD 设置
   - 有眼睛白点拖拽定位
2. `SELECT LENSES`
   - 不是普通弹窗，而是完整的 5 步表单流程
   - 有联动规则
   - 有价格实时变化
   - 有右侧商品摘要
   - 有处方录入
   - 有上传处方
   - 有 prism / near PD / readers / progressive 升级等分支

## 二、当前本地代码现状

## 1. 详情页模板现状

当前 `themes/astra/inc/render/product-detail-template.php` 已经有这些基础模块:

- 面包屑
- 图库
- 标题/价格/颜色/尺寸
- wishlist
- promotion
- 服务保障条
- measurements / frame info / description / warm tips
- 推荐商品
- 评论区

当前两个关键问题:

1. `TRY-ON` 现在只有一个静态按钮，还没有接入你的 `facepp-virtual-tryon`
2. `SELECT LENSES` 已经接了 `[muukal_lens_replica]`，但前端结构和目标站差距还很大

## 2. Try On 插件现状

文件:

- `plugins/facepp-virtual-tryon/facepp-virtual-tryon.php`
- `plugins/facepp-virtual-tryon/assets/app.js`
- `plugins/facepp-virtual-tryon/assets/app.css`

当前优点:

- 已经有独立 shortcode
- 已经有 Face++ 检测接口
- 已经有自动对齐
- 已经有上传图片
- 已经有手动微调
- 已经有 frame / model 基础配置

当前和目标站差异较大的地方:

1. 现在是“通用 try-on 工具 UI”，不是 Muukal 商品页的 try-on UI
2. 现在 frame 来源是插件后台全局配置，不是当前商品/当前颜色
3. 没有 Muukal 那套右侧模型缩略图区
4. 没有 Muukal 那套颜色切换区
5. 没有 Muukal 的 PD 设置位置和交互方式
6. 没有 Muukal 的白色眼睛定位点交互
7. 没有 Muukal 的底部按钮流程
8. 视觉风格差异很大

## 3. Select Lenses 插件现状

文件:

- `plugins/muukal-lens-replica/muukal-lens-replica.php`
- `plugins/muukal-lens-replica/includes/config.php`
- `plugins/muukal-lens-replica/assets/muukal-lens-replica.js`
- `plugins/muukal-lens-replica/assets/muukal-lens-replica.css`

当前优点:

- 已经做成了完整 5 步流程
- 已经有依赖规则和价格表
- 已经有读数镜 readers 分支
- 已经有 PD / near PD / prism
- 已经有实时汇总
- 已经有 payload 生成逻辑

当前和目标站差异较大的地方:

1. 前端 DOM 结构还不是目标站原始结构
2. 样式比较粗，离目标站差距大
3. 右侧摘要卡不够像
4. 当前保留了调试性质的 `Payload Preview`
5. 处方上传区还没真正做成目标站样式和流程
6. 处方选择器和“Add new”交互还没对齐
7. 提示 icon / popover / 推荐标签 / 热门标签还不完整
8. 局部图片和图标仍是替代方案，不是完全按目标站爬取
9. 还没有完全对接真实 WooCommerce 加购流程

## 三、插件负责，还是详情页模板负责

## 结论

### 1. `TRY-ON` 前端应以插件为主，模板为辅

推荐职责划分:

- 插件负责:
  - 弹层 DOM
  - Face++ 调用
  - 眼位计算
  - 拖拽/缩放/旋转逻辑
  - PD 逻辑
  - 颜色镜框切换逻辑
  - 模特图选择逻辑
  - try-on 专属 CSS / JS
- 模板负责:
  - 在商品图区域输出入口按钮
  - 把当前商品信息传给插件
  - 决定按钮放在哪里
  - 决定默认打开时使用哪个商品颜色

原因:

`TRY-ON` 是一个完整的交互应用，不是普通页面片段。它有自己的状态机、网络请求、拖拽、定位、缩放、模特/镜框切换，这些都应该由插件统一管理，不应该散落在详情页模板里。

### 2. `SELECT LENSES` 前端也应以插件为主，模板为辅

推荐职责划分:

- 插件负责:
  - 5 步流程 DOM
  - 所有步骤联动规则
  - 价格计算
  - 处方表单逻辑
  - right summary 更新
  - add-to-cart 前数据装配
  - 弹层开关
  - Select Lenses 专属 CSS / JS
- 模板负责:
  - 在商品购买区放入口按钮
  - 提供当前商品上下文
    - 商品 ID
    - SKU
    - 价格
    - 当前颜色
    - 当前标题
    - 当前图片
    - 尺寸信息
  - 控制页面上按钮位置和外层布局

原因:

`SELECT LENSES` 本质上也是一个独立应用。它不只是“一个模板块”，而是完整的业务流程。真正应该沉淀在插件里，这样以后换产品、换详情页布局、甚至换主题，流程层都还能复用。

## 四、推荐的总体架构

## 1. 详情页模板负责“页面骨架”

详情页模板负责这些:

- 图库
- 标题/价格/颜色/尺寸
- promotion
- 服务条
- measurements / frame info / description / reviews / recommend
- try on 按钮位置
- select lenses 按钮位置

## 2. 两个插件负责“交互应用”

### Try On 插件负责

- 渲染 Muukal 风格 try-on modal
- 读取当前商品的 try-on frame 资源
- 读取当前商品颜色变体资源
- 统一管理 upload / model / PD / drag / align

### Lens 插件负责

- 渲染 Muukal 风格 lens drawer
- 读取当前商品价格和颜色信息
- 执行步骤联动
- 输出真实购物车 payload

## 3. 模板不要硬写插件内部 UI

模板里不建议直接手写:

- try-on modal 具体结构
- lens drawer 具体结构
- 处方表单明细
- try-on 的手动微调控件
- lens 的步骤卡片逻辑

否则后面维护会非常痛苦:

- 模板改一点，插件 JS 失效
- 插件业务升级，模板 DOM 不匹配
- 重复逻辑越来越多

## 五、Try On 具体规划

## 目标

保留 `facepp-virtual-tryon` 的 Face++ 能力，但前端尽量贴近目标站。

## 推荐做法

### 阶段 1: 插件从“通用工具”改成“商品页专用可配置组件”

需要把插件从当前这套:

- 固定全局 frame 列表
- 固定测试 model 列表
- 固定通用 UI

改成:

- 支持接收当前商品 try-on 数据
- 支持接收当前商品颜色 frame PNG 列表
- 支持 Muukal 风格 modal DOM
- 支持 Muukal 风格控制区

### 阶段 2: 接入当前商品上下文

详情页模板应传给插件:

- 当前商品 ID
- 当前商品默认颜色
- 各颜色对应 try-on PNG
- 当前商品主图
- 默认模型列表
- 默认 PD

### 阶段 3: 前端对齐目标站

需要补齐这些 UI:

1. 左侧试戴舞台
2. 右侧模特缩略图
3. 颜色切换区
4. 眼睛定位白点
5. PD 选择器
6. 调整按钮组
7. Continue / Save 这类底部动作区

### 阶段 4: 算法与交互结合

当前插件的自动对齐逻辑可以保留，但展示层要改成目标站风格:

- 自动对齐作为初始定位
- 白点拖拽用于人工修正
- PD 改变时重新缩放镜框
- 当前颜色切换时换 frame PNG，不丢失定位状态或按策略重算

## Try On 的关键结论

Try On 的“功能层”必须在插件里。

Try On 的“入口位置”在模板里。

Try On 的“Muukal 风格前端”也建议写在插件里，而不是散在详情页模板里。

## 六、Select Lenses 具体规划

## 目标

保留 `muukal-lens-replica` 已经做出来的业务逻辑骨架，把前端和流程逐步拉到 Muukal 原站。

## 推荐做法

### 阶段 1: 保留插件做流程引擎

不要把 5 步流程拆回详情页模板。

插件继续负责:

- step 状态
- 校验
- 价格
- payload
- add-to-cart 对接

### 阶段 2: 用爬取结果重构插件 DOM

当前插件需要改的不是“只有 CSS”，而是“DOM + CSS + JS 一起对齐”。

优先级最高的部分:

1. Step 卡片结构
2. Prescription 区结构
3. Step3 色片确认区
4. Step4 推荐标签
5. Step5 热门标签和文案区
6. 右侧商品摘要卡
7. 蓝光升级块
8. 成功弹窗和升级弹窗

### 阶段 3: 去掉测试味道

生产版应移除或隐藏:

- Payload Preview
- Copy payload
- 过于开发调试风格的提示

这些可以保留成 debug 开关，但不应出现在仿站前端里。

### 阶段 4: 补齐目标站缺失交互

需要逐步补:

1. Prescription upload 真实交互
2. 已保存处方选择
3. Add new 入口
4. 各类 popover 帮助提示
5. More-like-original 的步骤禁用/推荐逻辑
6. 真正 WooCommerce 加购

## Select Lenses 的关键结论

`SELECT LENSES` 不应该拆进详情页模板。

它应该继续是插件主导，只是把插件前端从“测试版/近似版”升级为“Muukal 高仿版”。

## 七、建议实施顺序

## 第 1 步: 先把页面骨架做准

先把详情页这些区域对齐:

- 图库
- 标题价格区
- 颜色区
- wishlist / select lenses 区
- promotion
- 服务条
- measurements / frame info / description / warm tips
- recommend
- reviews

原因:

这一步是两个插件挂载的基础。

## 第 2 步: 重构 Try On 插件为商品上下文版

目标:

- 当前商品按钮能打开
- 当前颜色能切换
- 当前商品 try-on PNG 能使用
- 基础 UI 先对齐 70%

## 第 3 步: 重构 Select Lenses 插件前端

目标:

- 把现有逻辑保住
- DOM 结构贴近原站
- UI 贴近原站
- 摘要卡和 5 步流程到位

## 第 4 步: 最后做细节收口

包括:

- 弹窗文案
- tooltips
- 推荐标签
- 图片资源本地化
- 成功弹窗
- 过渡动画

## 八、我建议的落地原则

### 原则 1

页面骨架归模板。

### 原则 2

复杂交互归插件。

### 原则 3

模板只负责“把正确的数据传给插件”和“把按钮放在正确的位置”。

### 原则 4

不要把 try-on 和 lens 的核心交互逻辑散写进产品模板，否则后面一定难维护。

## 九、下一步建议

如果按这个方案继续，建议下一轮先做“只分析不改代码”的更细拆分:

1. 单独出一个 `TRY-ON-PLAN.md`
   - 逐条列出目标站和 `facepp-virtual-tryon` 的 DOM / CSS / JS 差异
2. 单独出一个 `LENS-PLAN.md`
   - 逐条列出目标站和 `muukal-lens-replica` 的 DOM / CSS / JS / 流程差异
3. 最后再开始写代码

这样你会更容易确认:

- 哪些必须保留
- 哪些先做
- 哪些以后再做

