#!/bin/bash

case "$1" in
start)
   nohup nodejs websocket.js >>../../logs/loriot.log 2>&1 &
   echo $!>loriot.pid
   ;;
stop)
   kill `cat loriot.pid`
   rm loriot.pid
   ;;
restart)
   $0 stop
   $0 start
   ;;
status)
   if [ -e loriot.pid ]; then
      echo websocket.sh is running, pid=`cat loriot.pid`
   else
      echo websocket.sh is NOT running
      exit 1
   fi
   ;;
*)
   echo "Usage: $0 {start|stop|status|restart}"
esac

exit 0
