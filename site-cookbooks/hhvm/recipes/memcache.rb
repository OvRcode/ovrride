include_recipe "hhvm::default"
# Set memcached save ip's in php.ini
template "/etc/hhvm/php.ini" do
  source "php.ini.erb"
  mode 00644
  owner "root"
  group "root"
  variables({:save_path => 'tcp://192.168.50.6:11211,tcp://192.168.50.6:11211'})
end
# Setup server on unix socket
template "/etc/hhvm/server.ini" do
  source "server.ini.erb"
  mode 0644
  owner "root"
  group "root"
  variables({:unix_socket => '/var/run/hhvm/hhvm.sock' })
end


execute "add hhvm to defaults" do
  command "update-rc.d hhvm defaults"
end

# Couldn't get it working through notifies on template for some reason
execute "restart HHVM" do
  command "service hhvm restart"
end