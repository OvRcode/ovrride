#!/bin/bash
LOCALFILE=$(ls /vagrant | egrep 'ovrride.*\.sql\.gz')
REMOTEFILE=$(s3cmd ls S3://ovrdatabase/latest/ | egrep '(ovrride.{19}(Mon|Tues|Wednes|Thurs|Fri|Sat|Sun)day.*)' | cut -c54-95)

if [ ! -f /vagrant/ovrride.*.gz ] || [ $LOCALFILE != $REMOTEFILE ];then
  if [ -f /vagrant/$LOCALFILE ];then
    rm /vagrant/$LOCALFILE
  fi
  if [ -f /vagrant/ovrride.sql ];then
    rm /vagrant/ovrride.sql
  fi
  echo 'Downloading new compressed database from server'
  s3cmd get S3://ovrdatabase/latest/$REMOTEFILE /vagrant/
  echo 'Decompressing database'
  zcat /vagrant/$REMOTEFILE > /vagrant/ovrride.sql
else
  echo 'File is the same'
fi
echo 'Database ready to import!'