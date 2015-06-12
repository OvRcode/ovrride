# Cookbook Name:: apache2
# Recipe:: ovrconfig
#

include_recipe "apache2"
include_recipe "apache2::mod_actions"
include_recipe "apache2::mod_rewrite"
include_recipe "apache2::mod_fastcgi"
include_recipe "apache2::mpm_worker"
include_recipe "apache2::mod_ssl"

execute "copy php-fpm config" do
  command "cp /vagrant/chef/fastcgi.conf /etc/apache2/conf-available/"
end
execute "enable php-fpm config" do
  command "a2enconf fastcgi"
end
execute "check SSL key/cert" do
  command "/vagrant/chef/certCheck.sh"
end

execute "remove /var/www" do
  command "rm -r /var/www "
end

execute "link vagrant to www" do
  command "ln -s /vagrant /var/www"
end
execute "copy site" do
  command "cp /vagrant/chef/ovr.conf /etc/apache2/sites-available/"
end
execute "enable site" do
  command "a2ensite ovr"
end

execute "check for ssl cert" do
  command "/vagrant/chef/certCheck.sh"
end


execute "reboot apache" do
  command "sudo service apache2 restart"
end

execute "setup wp cron" do
  command 'sudo crontab -r;echo "*/1 * * * * wget -q -O - http://local.ovrride.com/wp-cron.php?doing_wp_cron=1 >/dev/null 2>&1" | sudo crontab -'
end