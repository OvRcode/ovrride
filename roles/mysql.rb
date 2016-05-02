name "mysql"
default_attributes(
  :mysqld => {
    :root_password => "iloverandompasswordsbutthiswilldo",
    "my.cnf" => {
      :mysqld => {
        :bind_address => "0.0.0.0",
        :max_allowed_packet => "64M"
      }
    }
  }
)
run_list(
"recipe[gzip::default]",
"recipe[s3cmd::add_keys]",
"recipe[mysqld::default]",
"recipe[mysqld::configure]",
"recipe[mysqld::import]",
"recipe[ovr::get_images]"
)
