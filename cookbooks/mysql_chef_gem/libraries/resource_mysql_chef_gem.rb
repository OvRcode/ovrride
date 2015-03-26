require 'chef/resource/lwrp_base'

class Chef
  class Resource
    class MysqlChefGem < Chef::Resource::LWRPBase
      self.resource_name = :mysql_chef_gem
      actions :install, :remove
      default_action :install

      attribute :mysql_chef_gem_name, kind_of: String, name_attribute: true, required: true
      attribute :gem_version, kind_of: String, default: '2.9.1'
      attribute :client_version, kind_of: String, default: nil
    end
  end
end
