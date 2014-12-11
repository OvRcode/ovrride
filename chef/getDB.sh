#!/bin/bash
sudo apt-get -y install gzip
LOCALFILE=$(ls /vagrant | egrep 'ovrride.*\.sql\.gz')
REMOTEFILE=$(s3cmd -c /vagrant/chef/s3cfg ls S3://ovrdatabase/latest/ | egrep '(ovrride.{19}(Mon|Tues|Wednes|Thurs|Fri|Sat|Sun)day.*)' | cut -c54-95)

if [ $LOCALFILE != $REMOTEFILE ];then
  echo 'Different file on server, delete local copy'
  rm /vagrant/$LOCALFILE
  rm /vagrant/ovrride.sql
  echo 'Download new compressed database from server'
  s3cmd -c /vagrant/chef/s3cfg get S3://ovrdatabase/latest/$REMOTEFILE /vagrant/
  echo 'Decompress database'
  zcat /vagrant/$REMOTEFILE > /vagrant/ovrride.sql
else
  echo 'File is the same'
fi
echo 'Database ready to import!'