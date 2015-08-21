#!/bin/bash
function generate_key {
  echo "Generating key"
  
  openssl genrsa -des3 -passout pass:x -out local.ovrride.com.pass.key 2048
  
  openssl rsa -passin pass:x -in local.ovrride.com.pass.key -out local.ovrride.com.key

  rm local.ovrride.com.pass.key
}

function generate_signing_request {
  echo "Generating CSR"
  openssl req -new -config chef/openssl.cnf -out local.ovrride.com.csr
}

function generate_certificate {
  echo "Generating CERT"
  openssl x509 -req -days 3650 -in local.ovrride.com.csr -signkey local.ovrride.com.key -out local.ovrride.com.crt -extensions v3_req -extfile chef/openssl.cnf
}

# GET DIR OF THIS SCRIPT
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
# Move up one level
cd $DIR; cd ../

# Verify key,csr,crt are present
if [ ! -f local.ovrride.com.key ]; then
  generate_key
  generate_signing_request
  generate_certificate
elif [ ! -f local.ovrride.com.csr ]; then
  generate_signing_request
  generate_certificate
elif [ ! -f local.ovrride.com.crt ]; then
  generate_certificate
else
  echo "All Files present"
fi

# Verify key and crt match
crt="$(openssl x509 -noout -modulus -in local.ovrride.com.crt | openssl md5)"
key="$(openssl rsa -noout -modulus -in local.ovrride.com.key | openssl md5)"

if [ "$crt" = "$key" ]; then
  echo "Key and Cert match"
else
  echo "Key and cert do not match, regenerating"
  generate_signing_request
  generate_certificate
fi
