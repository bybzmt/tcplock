#!/usr/bin/env php
<?php
namespace bybzmt\Locker;

require_once __DIR__ . "/SocketLock.php";
require_once __DIR__ . "/FileLock.php";

//网络锁 (需要启动锁服务)
//超时是指锁的最长加锁时间，超过这个时间锁会自动解除。
//服务器端也有一个同样的超时时间，服务器端的时间短于它时以服务器端的为准。
$lock = new SocketLock("lock_key", '127.0.0.1', 2000, 10);

//加锁
$lock->lock();

//这里是需要上锁的业务代码。
//这里是需要上锁的业务代码。
//这里是需要上锁的业务代码。

//解锁，另外$lock变量删除时、连接断开或超时间都会自动解锁。
$lock->unlock();


//文件锁, 方便本地开发, 不需要启服务
//文件锁不能多台服间加锁! 只能单机使用
$lock = new FileLock("lock_key");
$lock->lock();

//这里是需要上锁的业务代码。

$lock->unlock();
