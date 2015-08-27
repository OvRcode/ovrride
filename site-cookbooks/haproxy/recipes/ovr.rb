include_recipe "haproxy::install_package"


cookbook_file "/etc/default/haproxy" do
  source "haproxy-default"
  owner "root"
  group "root"
  mode 00644
  notifies :restart, "service[haproxy]"
end

template "/etc/haproxy/map.map" do
  source "map.map.erb"
  owner "root"
  group "root"
  mode 00644
  variables(:map => node['haproxy']['map'])
end
template "/etc/haproxy/ssl_map.map" do
  source "map.map.erb"
  owner "root"
  group "root"
  mode 00644
  variables(:map => node['haproxy']['ssl_map'])
end
template "#{node['haproxy']['conf_dir']}/haproxy.cfg" do
  source "haproxy.cfg.erb"
  owner "root"
  group "root"
  mode 00644
  notifies :restart, "service[haproxy]", :immediately
end

service "haproxy" do
  supports :restart => true, :status => true, :reload => true
  action [:enable, :start]
end