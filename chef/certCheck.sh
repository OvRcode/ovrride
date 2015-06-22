#!/bin/bash
function generate_key {
  openssl genrsa -des3 -passout pass:x -out /vagrant/local.ovrride.com.pass.key 2048
  
  openssl rsa -passin pass:x -in /vagrant/local.ovrride.com.pass.key -out /vagrant/local.ovrride.com.key

  rm /vagrant/local.ovrride.com.pass.key
}

function generate_signing_request {
  openssl req -new -out /vagrant/local.ovrride.com.csr -key /vagrant/local.ovrride.com.key -config /vagrant/chef/openssl.cnf
  #openssl req -new -key /vagrant/local.ovrride.com.key -out /vagrant/local.ovrride.com.csr -subj "/C=US/ST=NY/L=NYC/O=*.local.ovrride.com/OU=IT/CN=OvRride/emailAddress=devops@ovrride.com"
}

function generate_certificate {
  openssl x509 -req -days 3650 -in /vagrant/local.ovrride.com.csr -signkey /vagrant/local.ovrride.com.key -out /vagrant/local.ovrride.com.crt-extensions v3_req -extfile /vagrant/chef/openssl.cnf
  #openssl x509 -req -days 365 -in /vagrant/local.ovrride.com.csr -signkey /vagrant/local.ovrride.com.key -out /vagrant/local.ovrride.com.crt
}

# Verify key,csr,crt are present
if [ ! -f /vagrant/local.ovrride.com.key ]; then
  generate_key
  generate_signing_request
  generate_certificate
elif [ ! -f /vagrant/local.ovrride.com.csr ]; then
  generate_signing_request
  generate_certificate
elif [ ! -f /vagrant/local.ovrride.com.crt ]; then
  generate_certificate
else
  echo "All Files present"
fi

# Verify key and crt match
crt="$(openssl x509 -noout -modulus -in /vagrant/local.ovrride.com.crt | openssl md5)"
key="$(openssl rsa -noout -modulus -in /vagrant/local.ovrride.com.key | openssl md5)"
if [ "$crt" = "$key" ]; then
  echo "Key and Cert match"
else
  echo "Key and cert do not match, regenerating"
  generate_signing_request
  generate_certificate
fi