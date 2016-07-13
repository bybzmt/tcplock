<?php
namespace bybzmt\SocketLock;

/**
* SocketLock 锁
*/
class Locker
{
	//服务器相当配置
	public $server;
	public $port;
	public $timeout = 30;

	//锁的key名称
	private $name;
	//文件句柄
	private $fp;

	/**
	* 构造函数
	* @param string $name 锁的名字
	*/
	public function __construct($name)
	{
		$this->name = $name;

		//配置信息，应该从框架配置里读取，这里就不写了
		//$this->server = $aa;
		//$this->port = $bb;
		//$this->timeout = $cc;
	}

	/**
	* 加锁
	*/
	public function lock()
	{
		$fp = fsockopen($this->server, $this->port, $error, $errstr, $this->timeout);

		if (!$fp) {
			throw new Exception("SocketLock Errno: {$error} Error: {$errstr}");
		}

		$len = pack("N", strlen($this->name));
		fwrite($fp, $len.$this->name);
		fflush($fp);

		$msg = fread($fp, 2);

		if ($msg != 'ok') {
			fclose($fp);
			return false;
		}

		$this->fp = $fp;

		return true;
	}

	/**
	* 解锁
	*/
	public function unlock()
	{
		if ($this->fp)
		{
			fclose($this->fp);
			$this->fp = false;
		}
	}

	public function __destruct()
	{
		$this->unlock();
	}

}
