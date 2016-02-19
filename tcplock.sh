#!/bin/bash
#Author:xuweihua

# Source function library
. /etc/init.d/functions

RETVAL=0
  
logfile="/data/logs/tcplock/tcplock.log"
pidfile="/usr/local/tcplock/run.pid"

mkdir -p `dirname ${logfile}`
touch ${pidfile}

start () {
    /usr/local/tcplock/tcplock -port 7002 >>${logfile} 2>>${logfile} &
    echo $! > ${pidfile}

    echo "Open tcplock processes. OK"
}

stop () {
    TMP=$(cat ${pidfile})

    if  [ -z ${TMP} ]; then
        echo "tcplock process not runing."
        exit 1
    fi

    killproc -p ${pidfile} tcplock
    RETVAL=$?

    if [ $RETVAL -ne 0 ]; then
        echo "tcplock process stop error."
        exit 1
    fi

    echo "" > ${pidfile}
    echo "Close tcplock processes. OK"
}

reset () {
    echo "" > ${pidfile}
    echo "Reset tcplock pidfile. OK"
}

restart () {
        stop
        start
}
  
case "$1" in
  start)
        start
        ;;
  stop)
        stop
        ;;
  reset)
        reset
        ;;
  restart)
        restart
        ;;
  *)
        echo $"Usage: $0 {start|stop|restart|reset}"
        RETVAL=2
        ;;
esac

exit $RETVAL
