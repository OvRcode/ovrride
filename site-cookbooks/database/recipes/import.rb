#
# Cookbook Name:: database
# Recipe:: import
#
include_recipe "database::mysql"

mysql_connection_info = {
  :host => '0.0.0.0',
  :username => 'root',
  :password => node['mysql']['server_root_password']
}

mysql_database 'ovrride' do
  connection mysql_connection_info
  action :drop
  action :create
end

=begin Probably going to move this to a script outside of repo
execute 'download backup' do
  command "/vagrant/chef/getDB.sh"
=end

execute 'import backup' do
  command "mysql -f -u root -p\"#{node['mysql']['server_root_password']}\" ovrride < /vagrant/ovrride.sql"
end

execute 'update' do
  command "mysql -f -u root -p\"#{node['mysql']['server_root_password']}\" < /vagrant/dev.sql"
end