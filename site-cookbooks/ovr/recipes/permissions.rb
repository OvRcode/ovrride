#
# Cookbook Name:: ovr
# Recipe:: permissions
#

execute 'SRP cache permissions' do
  command "sudo chmod -R 755 /vagrant/wp-content/plugins/special-recent-posts/cache"
end

execute 'Theme font permissions' do
  command "sudo chmod -R 755 /vagrant/wp-content/themes/quark/fonts"
end