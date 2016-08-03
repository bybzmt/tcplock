package main

import (
	"encoding/binary"
	"flag"
	"io"
	"log"
	"net"
	"runtime"
	"sync"
	"sync/atomic"
	"time"
)

var port = flag.String("port", ":2000", "socket port")
var timeout = flag.Int("time", 20, "lock timeout second")

var read_timeout = 2 * time.Second
var lock_timeout = 10 * time.Second

var groups map[string]*sync.Mutex
var counts map[string]int32
var groups_lock sync.Mutex

var lock_num int64

func main() {
	flag.Parse()

	lock_timeout = time.Duration(*timeout) * time.Second

	groups = make(map[string]*sync.Mutex)
	counts = make(map[string]int32)

	runtime.GOMAXPROCS(runtime.NumCPU())

	log.Println("server start on port:", *port)
	go status()

	ln, err := net.Listen("tcp", *port)
	if err != nil {
		log.Fatal("Listen Error ", err)
	}
	for {
		conn, err := ln.Accept()
		if err != nil {
			log.Println("Accept error", err)
			continue
		}
		go handleConnection(conn)
	}
}

func handleConnection(conn net.Conn) {
	defer func() {
		e := recover()
		if e != nil {
			log.Println(e)
		}
	}()

	//退出关连接
	defer conn.Close()

	var name_len int32

	//读取名字长度
	conn.SetReadDeadline(time.Now().Add(read_timeout))
	err := binary.Read(conn, binary.BigEndian, &name_len)

	if err != nil {
		log.Println("binary.Read failed:", err)
		return
	}

	if name_len < 1 {
		log.Println("name len can't < 1")
		return
	}

	if name_len > 256 {
		log.Println("name len too longer")
		return
	}

	_name := make([]byte, name_len)

	//读取名字
	conn.SetReadDeadline(time.Now().Add(read_timeout))
	n, err := conn.Read(_name)

	if err != nil {
		log.Println("Read failed:", err, _name)
		return
	}

	if int32(n) != name_len {
		log.Println("Name len failed now/need:", n, name_len)
		return
	}

	name := string(_name)
	//log.Println("name:", name)

	atomic.AddInt64(&lock_num, 1)

	//取得锁
	lock := getLock(name)
	//放回锁
	defer CloseLock(name)

	//上锁
	lock.Lock()
	defer lock.Unlock()

	//time.Sleep(3 * time.Second)

	//给客户端响应
	conn.SetWriteDeadline(time.Now().Add(read_timeout))

	msg := "ok"

	_, err = conn.Write([]byte(msg))

	if err != nil {
		log.Println("write failed:", name, err)
		return
	}

	//等待tcp关闭
	_close := make([]byte, 10)
	conn.SetReadDeadline(time.Now().Add(lock_timeout))
	_, err = conn.Read(_close)

	if err != io.EOF {
		log.Println("unlock error:", name, err)
	}
}

//取得锁，没有时初史化
func getLock(name string) *sync.Mutex {
	groups_lock.Lock()
	defer groups_lock.Unlock()

	lock, ok := groups[name]

	if !ok {
		groups[name] = &sync.Mutex{}
		lock = groups[name]
	}

	counts[name]++

	return lock
}

//放回锁，计数器为0时删除锁
func CloseLock(name string) {
	groups_lock.Lock()
	defer groups_lock.Unlock()

	counts[name]--

	if counts[name] == 0 {
		delete(groups, name)
		delete(counts, name)
	}
}

//定时打状态
func status() {
	c := time.Tick(5 * time.Minute)
	for _ = range c {
		groups_lock.Lock()
		num := len(groups)
		groups_lock.Unlock()

		log.Println("Crruent Lcoked:", num, "History Locked:", atomic.LoadInt64(&lock_num))
	}
}
