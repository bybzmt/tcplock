<?php
namespace bybzmt\Locker;

/**
* 文件锁 (方便开发环境不需要启服务)
*/
class FileLock
{
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
		$this->_key = sys_get_temp_dir() .'/bybzmt_file_lock_'. hash('crc32', $key);
	}

	/**
	* 加锁
	*/
	public function lock()
	{
        $fp = fopen($this->_key, 'w');
		if (!$fp) {
			throw new Exception("FileLock Errno");
		}

		$this->_fp = $fp;
        return flock($fp, LOCK_SH);
	}

	/**
	* 解锁
	*/
	public function unlock()
	{
		if ($this->_fp)
		{
            flock($this->_fp, LOCK_UN);
			fclose($this->_fp);
			$this->_fp = false;
		}
	}

	public function __destruct()
	{
		$this->unlock();
	}

}
