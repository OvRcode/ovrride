include_recipe "haproxy::install_package"


cookbook_file "/etc/default/haproxy" do
  source "haproxy-default"
  owner "root"
  group "root"
  mode 00644
  notifies :restart, "service[haproxy]"
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