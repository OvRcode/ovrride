#
# Cookbook Name:: database
# Recipe:: import
#
include_recipe "database::mysql"

mysql_connection_info = {
  :host => 'localhost',
  :username => 'root',
  :password => node['mysql']['server_root_password']
}

mysql_database 'ovrride' do
  connection mysql_connection_info
  action :drop
  action :create
end

execute 'import' do
  command "mysql -f -u root -p\"#{node['mysql']['server_root_password']}\" ovrride < /vagrant/testDB.sql"
  action :run
end