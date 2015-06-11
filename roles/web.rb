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
  "version" => "2.4",
  "default_site_enabled" => "true"
}
)
override_attributes(
:apt => {
  :compile_time_update => 'true'
}
)

run_list(
"recipe[build-essential]",
"recipe[apt]",
"recipe[apache2::default]",
"recipe[apache2::mpm_worker]"
)