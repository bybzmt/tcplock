<?php
namespace bybzmt\SocketLock;

require "Locker.php";

$lock = new Locker("lock_key");
//配置信息本来应该是类内部从框架配置里读的，这里只是演示。
$lock->server = "127.0.0.1";
$lock->port = 7002;
//超时是指锁的最长加锁时间，超过这个时间锁会自动解除。
//服务器端也有一个同样的超时时间，服务器端的时间短于它时
//以服务器商的为准。
$lock->timeout = 10;

//加锁
$lock->lock();

//这里是需要上锁的业务代码。
//这里是需要上锁的业务代码。
//这里是需要上锁的业务代码。

//解锁，另外$lock会删除时、连接断开或超时间都会自动解锁。
$lock->unlock();
