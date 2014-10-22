# Cookbook Name:: apache2
# Recipe:: vhosts
#

include_recipe "apache2"

web_app "ovr" do
  server_name "local.ovrride.com"
  server_aliases ["http://local.ovrride.com"]
  directory_index "index.php"
  docroot "/var/www/"
  allow_override "All"
end