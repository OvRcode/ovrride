#!/bin/bash
rm -rfv /var/log/apache/error.log
wait 1;
service apache2 reload
wait 2;
watch tail -n 20 /var/log/apache2/error.log
