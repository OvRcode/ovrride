name "haproxy"
default_attributes(
"haproxy" => {
  "bind_address" => "192.168.50.4",
  "hostname" => "local.ovrride.com",
  "http_port" => "80",
  "https_port" => "443",
  "web_servers" => {
    "web1" => "192.168.50.5",
    "web2" => "192.168.50.6",
  },
  "ssl_web_servers" => {
    "web1ssl" => "192.168.50.5",
    "web2ssl" => "192.168.50.6",
  },
  "package" => {
    "version" => "1.5.3-1~ubuntu14.04.1"
  }
}
)
run_list(
"recipe[apt]",
"recipe[haproxy::ovr]"
)