name "haproxy"
default_attributes(
"haproxy" => {
  "hostname" => "local.ovrride.com",
  "ipaddress" => "192.168.50.4",
  "port" => "80",
  "ssl_port" => "443"
}
)
run_list(
"recipe[haproxy::default]"
)