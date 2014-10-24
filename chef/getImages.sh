#!/bin/bash
s3cmd -c /vagrant/chef/s3cfg sync S3://ovrride/ /vagrant/wp-content/uploads/
