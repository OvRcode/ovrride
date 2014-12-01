#!/bin/bash
sudo apt-get -y install gzip
if [ -f /vagrant/ovrride.sql ]; then
  echo 'Removing old copy of db'
  rm /vagrant/ovrride.sql
fi
echo 'Identify Latest DB Snapshot'
# s3cmd lists files, egrep searches for correct snapshot, cut drops extra data from s3cmd
DBFILE=$(s3cmd -c /vagrant/chef/s3cfg ls S3://ovrdatabase/latest/ | egrep '(ovrride.{19}(Mon|Tues|Wednes|Thurs|Fri|Sat|Sun)day.*)' | cut -c54-92)
echo 'Downloading compressed DB'
s3cmd -c /vagrant/chef/s3cfg get S3://ovrdatabase/latest/$DBFILE /vagrant/ovrride.sql.gz
#s3cmd -c /vagrant/chef/s3cfg get S3://ovrdatabase/latest/ovrride* /vagrant/ovrride.sql.gz 
echo 'Decompressing archive'
gzip -d /vagrant/ovrride.sql.gz
echo 'DB is ready to import'
