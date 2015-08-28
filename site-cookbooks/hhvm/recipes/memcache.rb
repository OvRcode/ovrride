include_recipe "hhvm::default"

template "/etc/hhvm/php.ini" do
  source "php.ini.erb"
  mode 00644
  owner "root"
  group "root"
  variables({:save_path => 'tcp://192.168.50.6:11211,tcp://192.168.50.6:11211'})
end

# Couldn't get it working through notifies on template for some reason
execute "restart HHVM" do
  command "service hhvm restart"
end