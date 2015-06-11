#!/bin/bash
function generateKey {
  openssl genrsa -out /vagrant/local.ovrride.com.key 2048
  generateCertificate
}
function generateCertificate {
  echo "Generating Certificate file"
  openssl req -new -x509 -key /vagrant/local.ovrride.com.key -out /vagrant/local.ovrride.com.cert -days 3650 -subj /CN=local.ovrride.com
}

if [ ! -f /vagrant/local.ovrride.com.key ]; then
  echo "Local key not found, generating key"
  generateKey
  exit 0
else
  echo "Local key found, moving on"
fi

if [ ! -f /vagrant/local.ovrride.com.cert ]; then
  echo "Certificate file not found"
  generateCertificate
  exit 0
fi