name "web"
# Role shared by all OvR Web Server VMs
default_attributes(
'build-essential' => {
  'compile_time' => true
},
"apt" => {
  "compile_time_update" => true
},
"apache" => {
  "version" => "2.4"
}

)
override_attributes(
"apt" => {
  "compile_time_update" => 'true'
},
"php-fpm" => {
  "pools" => {
    "default" => {
      "enable" => 'true'
    }
  }
}
)

run_list(
"recipe[build-essential]",
"recipe[apt]",
"recipe[memcached::default]",
"recipe[hhvm::memcache]",
"recipe[php::modules]",
"recipe[php-fpm::install]",
"recipe[php-fpm::web]",
"recipe[nginx::webconfig]"
)
