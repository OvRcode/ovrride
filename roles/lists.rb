name "lists"
# Lists using php-fpm due to HHVM incompatibility with Flight PHP
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
"recipe[s3cmd::add_keys]",
"recipe[memcached]",
"recipe[php::modules]",
"recipe[php-fpm::install]",
"recipe[php-fpm::lists]",
"recipe[apache2::listsconfig]"
)
