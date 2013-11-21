PHP SOAP 实例
====

依赖：PHP SOAP 扩展

放在可以使用浏览器访问的任意位置，访问：http://localhost/[path-to-soap]/test.php

欢迎反馈。

#### 目录结构 ####

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
