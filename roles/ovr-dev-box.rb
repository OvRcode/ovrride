name "ovr-dev-box"
override_attributes(
  "mysql" => {
    "server_root_password" => 'iloverandompasswordsbutthiswilldo',
    "server_repl_password" => 'iloverandompasswordsbutthiswilldo',
    "server_debian_password" => 'iloverandompasswordsbutthiswilldo'
  }
)
run_list(
  "recipe[apt]",
  "recipe[build-essential]",
  "recipe[openssl]",
  "recipe[apache2]",
  "recipe[apache2::mod_php5]",
  "recipe[mysql::server]",
  "recipe[php]",
  "recipe[php::module_mysql]",
  "recipe[apache2::vhosts]",
  "recipe[mysql-chef_gem::default]",
  "recipe[database::mysql]",
  "recipe[database::import]"
)