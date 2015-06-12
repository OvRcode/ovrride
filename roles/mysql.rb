name "mysql"
default_attributes(
  :mysql => {
    :bind_address => "0.0.0.0",
    :server_root_password => 'iloverandompasswordsbutthiswilldo',
    :server_repl_password => 'iloverandompasswordsbutthiswilldo',
    :server_debian_password => 'iloverandompasswordsbutthiswilldo',
    :allow_remote_root => true,
    :version => '5.5'
  }
)
run_list(
"recipe[gzip::default]",
"recipe[s3cmd::add_keys]",
"recipe[mysql::server]",
"recipe[database::import]"
)