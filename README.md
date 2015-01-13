PHP SOAP 实例
====

放在WEB服务的任意位置，访问：http://localhost/[path-to-soap]/test.php


#### 简介 {#summary}

通常我们的应用服务需要在不同的平台进行交互操作的时候，会使用 [WEB服务].

常用的WEB服务有以下三种：

* [SOAP]（简单对象访问协议）: 支持多种协议(http/https/smtp等),W3C专门定义的一些标准
* [XML-RPC]（远程过程调用）: 只支持http协议，没有标准
* [REST]（表征状态转移）: 只支持http协议，是一种针对于资源理解的URI设计风格而没有标准，
加上 [OAuth]（开放授权）会让你的WEB服务(或开放平台)看上去更加简洁和简单，之后的文章会详细介绍。

本篇文章重点：SOAP 简单对象访问协议（Simple Object Access Protocol）。

#### PHP SOAP {#php-soap}

__模式__：

SOAP 分为 WSDL 和 non-WSDL 模式，可以简单理解为：WSDL 模式对外提供 WSDL 定义文件，
而 non-WSDL 模式不对外提供 WSDL 定义文件（会有人给你发一个接口文档的）。

__依赖__：

`php-soap` 扩展，如果不存在此扩展，安装：

    # yum install php-soap

或编译 PHP：`--enable-soap`。

或使用：[nusoap] 包。

__实例__：

本文使用 `php-soap` 扩展，做了一个例子，源码地址为：[https://github.com/ueaner/soap]，目录结构说明：

    $ tar xf soap.tar.bz2

    $ tree -C soap
    soap
    |-- class                   # 提供服务的类目录
        |-- Person.class.php    # 提供服务的类文件
    |-- Client.php              # 客户端类
    |-- non-wsdl                # non-WSDL 模式：提供服务的目录
        |-- PersonService.php   # non-WSDL 模式：提供服务的文件
    |-- readme.txt              # readme
    |-- Service.php             # 服务端类
    |-- test.php                # 测试文件
    |-- wsdl                    # WSDL 模式：提供服务的目录
        |-- PersonService.php   # WSDL 模式：提供服务的文件
    |-- xml                     # WSDL 模式：生成的 WSDL xml 的目录
        |-- Person.wsdl         # WSDL 模式：生成的 WSDL xml 的文件

    4 directories, 8 files

`Client.php` 和 `Service.php` 均实现了 WSDL 和 non-WSDL 两种模式。

WSDL 模式 和 non-WSDL 模式对照表：

                    WSDL 模式            non-WSDL 模式
    SoapServer
        参数1     SomeService.php?wsdl       null
        参数2       uri 可有，可无             uri
    SoapClient
        参数1     SomeService.php?wsdl       null
        参数2       uri 可有，可无         uri + location

这里的 `SomeService.php?wsdl` 类似 `http://127.0.0.1:80/soap/wsdl/PersonService.php?wsdl` 这样的地址(有 `?wsdl`)，
`location` 是类似 `http://127.0.0.1:80/soap/wsdl/PersonService.php` 这样的地址(无 `?wsdl`)。
`uri` 一般为你的根域名，如 `http://localhost`，或与 `location` 参数定义相同都可。

另外 WSDL 模式对外提供 WSDL 定义的 xml 文件，所以在以 GET 方式访问 http://127.0.0.1:80/soap/wsdl/PersonService.php?wsdl
地址时会输出相应的 xml 文件，对接口对象或函数进行说明。

具体实现请见 [https://github.com/ueaner/soap] 源码，如有疑问，欢迎在本页下方进行回复。


[WEB服务]: http://zh.wikipedia.org/wiki/Web服务
[SOAP]: http://zh.wikipedia.org/wiki/SOAP
[XML-RPC]: http://zh.wikipedia.org/wiki/XML-RPC
[REST]: http://zh.wikipedia.org/wiki/REST
[OAuth]: http://zh.wikipedia.org/wiki/OAuth
[nusoap]: http://sourceforge.net/projects/nusoap/

[https://github.com/ueaner/soap]: https://github.com/ueaner/soap "PHP SOAP 实例"
