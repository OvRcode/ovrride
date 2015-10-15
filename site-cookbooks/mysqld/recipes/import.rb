#
# Cookbook Name:: database
# Recipe:: import
#

execute 'download backup' do
  command "/vagrant/chef/getDB.sh"
end

execute 'import backup' do
  command "mysql -f -u root -p\"#{node['mysqld']['root_password']}\" < /vagrant/ovrride.sql"
end

execute 'update' do
  command "mysql -f -u root -p\"#{node['mysqld']['root_password']}\" < /vagrant/dev.sql"
end