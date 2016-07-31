tcplock
========
是一个网络锁服务,用于在多台服务器间进行加锁工作。

server
-------
这是服务器端程序，需要使用golang进行编译，没有依赖包
安装go后进入这个目录直接go build就可以了。

启动方法 `./tcplock -port=:7002 -time=20`

port是端口，可以是 `:8080` 或 `127.0.0.1:8080` 这两种形式。
time是超时间间，超过这个时间锁会自动解除。

phpclient
-------
这是php的客户端程序。
可以看demo.php里的代码。

composer安装
composer require "bybzmt/tcplock"
