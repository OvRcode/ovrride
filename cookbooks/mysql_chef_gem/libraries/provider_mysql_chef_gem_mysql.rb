class Chef
  class Provider
    class MysqlChefGem
      class Mysql < Chef::Provider::LWRPBase
        include Chef::DSL::IncludeRecipe
        use_inline_resources if defined?(use_inline_resources)

        def whyrun_supported?
          true
        end

        action :install do
          include_recipe 'build-essential::default'

          # As a resource: can pass version from calling recipe
          mysql_client 'default' do
            version new_resource.client_version
            action :create
          end

          gem_package 'mysql' do
            gem_binary RbConfig::CONFIG['bindir'] + '/gem'
            version new_resource.gem_version
            action :install
          end
        end

        action :remove do
          gem_package 'mysql' do
            gem_binary RbConfig::CONFIG['bindir'] + '/gem'
            action :remove
          end
        end
      end
    end
  end
end
