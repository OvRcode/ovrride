#!/bin/bash
sudo apt-get -y install gzip
if [ -f /vagrant/ovrride.sql ]; then
  echo 'Removing old copy of db'
  rm /vagrant/ovrride.sql
fi
echo 'Downloading compressed DB'
s3cmd -c /vagrant/chef/s3cfg get S3://ovrdatabase/latest/ovrride* /vagrant/ovrride.sql.gz 
echo 'Decompressing archive'
gzip -d /vagrant/ovrride.sql.gz
echo 'DB is ready to import'
