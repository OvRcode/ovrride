include_recipe "php::default"

package "php5-mysql" do
  action :install
end

package "php5-gd" do
  action :install
end

package "php5-curl" do
  action :install
end

package "php5-mcrypt" do
  action :install
end

package "php5-memcached" do
  action :install
end
