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

execute 'import' do
  command "mysql -f -u root -p\"#{node['mysql']['server_root_password']}\" ovrride < /vagrant/ovrride.sql"
  action :run
end

mysql_database 'update' do
  connection mysql_connection_info
  sql { ::File.open('dev.sql').read }
  action :query
end