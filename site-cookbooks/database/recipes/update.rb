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

mysql_database 'update' do
  connection mysql_connection_info
  sql { ::File.open('/vagrant/dev.sql').read }
  action :query
end