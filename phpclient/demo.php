#!/usr/bin/env php
<?php
namespace bybzmt\SocketLock;

require __DIR__ . "/Locker.php";

//配置信息，这里只是演示。
Locker::$server = "127.0.0.1";
Locker::$port = 7002;
//超时是指锁的最长加锁时间，超过这个时间锁会自动解除。
//服务器端也有一个同样的超时时间，服务器端的时间短于它时以服务器端的为准。
Locker::$timeout = 10;

//实例化锁
$lock = new Locker("lock_key");

//加锁
$lock->lock();

//这里是需要上锁的业务代码。
//这里是需要上锁的业务代码。
//这里是需要上锁的业务代码。

//解锁，另外$lock会删除时、连接断开或超时间都会自动解锁。
$lock->unlock();
