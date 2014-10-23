# Cookbook Name:: apache2
# Recipe:: ovrconfig
#

include_recipe "apache2"

#web_app "ovr" do
#  server_name "local.ovrride.com"
#  server_aliases ["http://local.ovrride.com"]
#  directory_index "index.php"
#  docroot "/var/www/"
#  allow_override "All"
#end

execute "link config" do
  command <<-EOT
  if [ ! -h /etc/apache2/sites-enabled/ovr.conf ];then
    sudo ln -s /vagrant/chef/ovr.conf /etc/apache2/sites-enabled/ovr.conf
  fi
  EOT
end

execute "reboot apache" do
  command "sudo service apache2 restart"
end

