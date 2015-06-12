include_recipe "php::default"

package "php5-mysql" do
  action :install
end

package "php5-gd" do
  action :install
end