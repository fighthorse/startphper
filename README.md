# startphper
从零开始，自己动手搭建php框架，重新认识MVC ORM 

MVC模式（Model-View-Controller）是软件工程中的一种软件架构模式，把软件系统分为三个基本部分：模型（Model）、视图（View）和控制器（Controller）。

PHP中MVC模式也称Web MVC，从上世纪70年代进化而来。MVC的目的是实现一种动态的程序设计，便于后续对程序的修改和扩展简化，并且使程序某一部分的重复利用成为可能。除此之外，此模式通过对复杂度的简化，使程序结构更加直观。

MVC各部分的职能：

模型Model – 管理大部分的业务逻辑和所有的数据库逻辑。模型提供了连接和操作数据库的抽象层。
控制器Controller - 负责响应用户请求、准备数据，以及决定如何展示数据。
视图View – 负责渲染数据，通过HTML方式呈现给用户。


对象关系映射（英语：(Object Relational Mapping，简称ORM，或O/RM，或O/R mapping），是一种程序技术，用于实现面向对象编程语言里不同类型系统的数据之间的转换[1]  。从效果上说，它其实是创建了一个可在编程语言里使用的--“虚拟对象数据库”。


Framework Interoperability Group（框架可互用性小组），简称 FIG，成立于 2009 年。提出了 PSR-0 到 PSR-4 五套 PHP 非官方规范

1. PSR-0 (Autoloading Standard) 自动加载标准

2. PSR-1 (Basic Coding Standard) 基础编码标准

3. PSR-2 (Coding Style Guide) 编码风格向导

4. PSR-3 (Logger Interface) 日志接口

5. PSR-4 (Improved Autoloading) 自动加载优化标准

Composer 类似著名的 npm 和 RubyGems，给海量 PHP 包提供了一个异常方便的协作通道，Composer Hub 地址：https://packagist.org/。Composer 中文网站：http://www.phpcomposer.com/。

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

php composer.phar update

利用 Composer 一步一步构建自己的 PHP 框架

第一步：空的 Composer 项目
在文件夹下新建文件 composer.json：
composer update


第二步：构建路由
推荐 https://github.com/NoahBuscher/Macaw，对应的 Composer 包为 noahbuscher/macaw 。
更改 composer.json：
"noahbuscher/macaw": "dev-master"

新建 public 文件夹，这个文件夹将是用户唯一可见的部分。在文件夹下新建 index.php 文件：

新建 config 文件夹，在里面新建 routs.php 文件，

Macaw 的文档位于 https://github.com/NoahBuscher/Macaw，请按照你的 HTTP 服务软件类型自行设置伪静态，其实跟绝大多数框架一样：“将所有非静态文件全部指向 index.php”。

将某一个端口用 Apache 或 Nginx 分配给 public 目录，这一步十分建议用 Apache 或者 Nginx 做。

Macaw 只有一个文件，简略分析一下：

1. 每次 URL 驱动 MFFC/public/index.php 之后 Composer 自动加载在会在内存中维护一个全量命名空间类名到文件名的数组，这样当代码中使用某个类的时候，将自动载入该类所在的文件。

2. 我们在路由文件中载入了 Macaw 类：“use NoahBuscher\Macaw\Macaw;”，接着调用了两次静态方法 ::get()，这个方法是不存在的，将由 MFFC/vendor/codingbean/macaw/Macaw.php 中的 __callstatic() 接管。

3. 这个函数接受两个参数，$method 和 $params，前者是具体的 function 名称，在这里就是 get，后者是这次调用传递的参数，即 Macaw::get('fuck',function(){...}) 中的两个参数。第一个参数是我们想要监听的 URL 值，第二个参数是一个 PHP 闭包，作为回调，代表 URL 匹配成功后我们想要做的事情。

4. __callstatic() 做的事情也很简单，分别将目标URL（即 /fuck）、HTTP方法（即 GET）和回调代码压入 $routes、$methods 和 $callbacks 三个 Macaw 类的静态成员变量（数组）中。

5. 路由文件最后一行的 Macaw::dispatch(); 方法才是真正处理当前 URL 的地方。能直接匹配到的会直接调用回调，不能直接匹配到的将利用正则进行匹配。


第三步：设计 MVC

extension=php_igbinary.dll
extension=php_redis.dll

规划文件夹
新建 app 文件夹，在 app 中创建 controllers、models、views 三个文件夹，开始正式开始踏上 MVC 的征程。

自动加载，修改 composer.json 为：

{
  "require": {
    "codingbean/macaw": "dev-master"
  },
  "autoload": {
    "classmap": [
      "app/controllers",
      "app/models"
    ]
  }
}
运行 composer dump-autoload


几乎所有人都是通过学习某个框架来了解 MVC 的，这样可能框架用的很熟，一旦离了框架一个简单的页面都写不了，更不要说自己设计 MVC 架构了，其实这里面也没有那么多门道，原理非常清晰：

1. PHP 框架再牛逼，他也是 PHP，也要遵循 PHP 的运行原理和基本哲学。抓住这一点我们就能很容易地理解很多事情。

2. PHP 做的网站从逻辑上说，跟 php test.php 没有任何区别，都只是一段字符串作为参数传递给 PHP 解释器而已。无非就是复杂的网站会根据 URL 来调用需要运行的文件和代码，然后返回相应的结果。

3. 无论我们看到的是 CodeIgniter 这样 180 个文件组成的“小框架”，还是 Laravel 这样加上 vendor 一共 3700 多个文件的 “大框架”，他们都会在每一个 URL 的驱动下，组装一段可以运行的字符串，传给 PHP 解释器，再把从 PHP 解释器返回的字符串传给访客的浏览器。

4. MVC 是一种逻辑架构，本质上是为了让人脑这样的超低 RAM 的计算机能够制造出远超人脑 RAM 的大型软件，其实 MVC 架构在 GUI 软件出现以前就已经成形，命令行输出也是视图嘛。

5. 一个 URL 驱动框架做的事情基本是这样的：入口文件 require 控制器，控制器 require 模型，模型和数据库交互得到数据返回给控制器，控制器再 require 视图，把数据填充进视图，返回给访客，流程结束。

