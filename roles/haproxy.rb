name "haproxy"
default_attributes(
"apt" => {
  "compile_time_update" => true
},
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
  "lists_servers" => {
    "lists1" => "192.168.50.8"
  },
  "map" => {
    "lists.local.ovrride.com" => "lists",
    "local.ovrride.com" => "web"
  },
  "ssl_map" => {
    "local.ovrride.com" => "ssl_web",
    "lists.local.ovrride.com" => "ssl_lists"
  },
  "package" => {
    "version" => "1.5*"
  }
}
)
run_list(
"recipe[apt]",
"recipe[haproxy::ovr]"
)