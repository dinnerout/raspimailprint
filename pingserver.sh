# get own ip
IP=$(/sbin/ifconfig | /bin/grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | /bin/grep -Eo '([0-9]*\.){3}[0-9]*' | /bin/grep -v '127.0.0.1')
# send data
URL="http://dirtybits.nonstatics.com/service/?action=Report&name=EMailPrinter&address="
/usr/bin/curl "$URL$IP" > /dev/null 2>&1