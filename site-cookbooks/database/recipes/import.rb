#
# Cookbook Name:: database
# Recipe:: import
#
include_recipe "database::mysql"

mysql_connection_info = {
  :host => '***REMOVED***',
  :username => 'root',
  :password => node['mysql']['server_root_password']
}

mysql_database 'ovrride' do
  connection mysql_connection_info
  action :drop
  action :create
end


execute 'download and import' do
  command "/vagrant/chef/getDB.sh && mysql -f -u root -p\"#{node['mysql']['server_root_password']}\" ovrride < /vagrant/ovrride.sql"
end

execute 'update' do
  command "mysql -f -u root -p\"#{node['mysql']['server_root_password']}\" < /vagrant/dev.sql"
end