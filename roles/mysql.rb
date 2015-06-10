name "mysql"
override_attributes(
  :mysql => {
    :bind_address => "0.0.0.0",
    :server_root_password => 'iloverandompasswordsbutthiswilldo',
    :server_repl_password => 'iloverandompasswordsbutthiswilldo',
    :server_debian_password => 'iloverandompasswordsbutthiswilldo',
    :allow_remote_root => true
  }
)

run_list(
"recipe[mysql::server]"
)