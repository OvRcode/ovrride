# Cookbook Name:: nginx
# Recipe:: webconfig

include_recipe "nginx::default"

# Remove webroot dir 
execute "remove /var/www" do
  only_if "test -L /var/www"
  command "rm -r /var/www "
end

# Link vagrant share to webroot
execute "link vagrant to www" do
  command "ln -s /vagrant /var/www"
end

# Get enviromental vars from data bags
envvars = data_bag_item("mysql","config")
envvars = envvars.merge(data_bag_item("secret_keys", "keys"))
envvars = envvars.merge(data_bag_item("wordpress", "local_keys"))
envvars.delete("chef_type")
envvars.delete("data_bag")
envvars.delete("id")

template "/etc/nginx/fastcgi_params" do
  source "fastcgi_params.erb"
  variables( :env => envvars)
end

template "/etc/nginx/sites-available/default" do
  source "default.erb"
end

template "/etc/nginx/hhvm.conf" do
  source "hhvm.conf.erb"
end

execute "reboot nginx" do
  command "sudo service nginx restart"
end

execute "setup wp cron" do
  command 'sudo crontab -r;echo "*/1 * * * * wget -q -O - http://local.ovrride.com/wp-cron.php?doing_wp_cron=1 >/dev/null 2>&1" | sudo crontab -'
end