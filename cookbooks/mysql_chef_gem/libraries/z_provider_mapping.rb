#########
# mysql_chef_gem
#########
Chef::Platform.set platform: :amazon, resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :centos, version: '< 7.0', resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :centos, version: '>= 7.0', resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :debian, resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :fedora, version: '< 19', resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :fedora, version: '>= 19', resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :omnios, resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :redhat, version: '< 7.0', resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :redhat, version: '>= 7.0', resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :scientific, version: '< 7.0', resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :scientific, version: '>= 7.0', resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :smartos, resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :suse, resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
Chef::Platform.set platform: :ubuntu, resource: :mysql_chef_gem, provider: Chef::Provider::MysqlChefGem::Mysql
