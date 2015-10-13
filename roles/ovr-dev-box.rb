name "ovr-dev-box"

default_attributes(
    "build-essential" => {
        "compile_time" => true
    },
)

override_attributes(
  "mysql" => {
    "server_root_password" => 'iloverandompasswordsbutthiswilldo',
    "server_repl_password" => 'iloverandompasswordsbutthiswilldo',
    "server_debian_password" => 'iloverandompasswordsbutthiswilldo'
  },
  "apache" => {
    "allow_override" => 'All'
  },
)
run_list(
  "recipe[apt]",
  "recipe[build-essential]",
  "recipe[openssl]",
  "recipe[apache2]",
  "recipe[apache2::mod_php5]",
  "recipe[apache2::mod_env]",
  "recipe[apache2::mod_rewrite]",
  "recipe[apache2::mod_ssl]",
  "recipe[mysql::server]",
  "recipe[php]",
  "recipe[php::module_mysql]",
  "recipe[php::module_curl]",
  "recipe[mysql-chef_gem::default]",
  "recipe[database::mysql]",
  "recipe[s3cmd]",
  "recipe[database::import]",
  "recipe[apache2::ovrconfig]",
  "recipe[ovr::permissions]"
)