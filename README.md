tcplock
========
是一个网络锁服务,用于在多台服务器间进行加锁工作。

tcplock.go
-------
是程序的源代码，需要使用golang进行编译，没有依赖包
安装go后直接go build tcplock.go就可以了。

启动方法 `./tcplock -port=:7002 -time=20`

port是端口，可以是 `:8080` 或 `127.0.0.1:8080` 这两种形式。

tcplock.sh
-------
centos上的服务脚本(示例)

配置:
	/data/logs/tcplock/tcplock.log
	/usr/local/tcplock/run.pid
	/usr/local/tcplock/tcplock

SocketLock.php
-------
这是一个php的使用示例。
可以看demo.php里的代码。