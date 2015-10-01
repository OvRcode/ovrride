#
# Author::  Seth Chisamore (<schisamo@opscode.com>)
# Cookbook Name:: php-fpm
# Recipe:: package
#
# Copyright 2011, Opscode, Inc.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#

template node['php-fpm']['conf_file'] do
  source "php-fpm.conf.erb"
  mode 00644
  owner "root"
  group "root"
  notifies :restart, "service[php-fpm]"
end
template "/etc/php5/fpm/php.ini" do
  only_if "test -d /etc/php5/fpm || mkdir -p /etc/php5/fpm"
  source "php.ini.erb"
  mode 00644
  owner "root"
  group "root"
  variables({:session_handler => "memcached",:session_path => "tcp://192.168.50.6:11211,tcp://192.168.50.6:11211"})
  notifies :restart, "service[php-fpm]"
end
unless node['php-fpm']['pools'].key?('www')
  php_fpm_pool 'www' do
    enable false
  end
end
if node['php-fpm']['pools']
  node['php-fpm']['pools'].each do |pool|
    if pool.is_a?(Array)
      pool_name = pool[0]
      pool = pool[1]
    else
      pool_name = pool[:name]
    end
    php_fpm_pool pool_name do
      pool.each do |k, v|
        self.params[k.to_sym] = v
      end
    end
  end
end
