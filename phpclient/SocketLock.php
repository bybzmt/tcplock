<?php
namespace bybzmt\Locker;

/**
* SocketLock 锁
*/
class SocketLock
{
	//服务器相当配置
	static public $server = '127.0.0.1';
	static public $port = 2000;
	static public $timeout = 20;

	private $_server;
	private $_port;
	private $_timeout;

	//锁的key名称
	private $_key;
	//文件句柄
	private $_fp;

	/**
	* 构造函数
	* @param string $key 锁的名字
	*/
	public function __construct($key, $server=null, $port=null, $timeout=null)
	{
		$this->_key = $key;
		$this->_server = $server ? $server : self::$server;
		$this->_port = $port ? $port : self::$port;
		$this->_timeout = $timeout ? $timeout : self::$timeout;
	}

	/**
	* 加锁
	*/
	public function lock()
	{
		$fp = fsockopen($this->_server, $this->_port, $error, $errstr, $this->_timeout);

		if (!$fp) {
			throw new Exception("SocketLock Errno: {$error} Error: {$errstr}");
		}

		$len = pack("N", strlen($this->_key));
		fwrite($fp, $len.$this->_key);
		fflush($fp);

		$msg = fread($fp, 2);

		if ($msg != 'ok') {
			fclose($fp);
			return false;
		}

		$this->_fp = $fp;

		return true;
	}

	/**
	* 解锁
	*/
	public function unlock()
	{
		if ($this->_fp)
		{
			fclose($this->_fp);
			$this->_fp = false;
		}
	}

	public function __destruct()
	{
		$this->unlock();
	}

}
