# -*- mode: ruby -*-

# vi: set ft=ruby :

boxes = [
    {
        :name => "haproxy",
        :hostname => "haproxy.local.ovrride.com",
        :eth1 => "192.168.50.4",
        :mem => "512",
        :cpu => "1"
    },
    {
        :name => "web1",
        :hostname => "web1.local.ovrride.com",
        :eth1 => "192.168.50.5",
        :mem => "512",
        :cpu => "1"
    },
    {
        :name => "web2",
        :hostname => "web2.local.ovrride.com",
        :eth1 => "192.168.50.6",
        :mem => "512",
        :cpu => "1"
    },
    {
        :name => "lists",
        :hostname => "lists.local.ovrride.com",
        :eth1 => "192.168.50.8",
        :mem => "512",
        :cpu => "1"
    },
    {
        :name => "mysql",
        :hostname => "mysql.local.ovrride.com",
        :eth1 => "192.168.50.7",
        :mem => "512",
        :cpu => "1"
    }
]

Vagrant.configure(2) do |config|

  # One box to rule them all (works on Fusion and VirtualBox)
  config.vm.box = "phusion/ubuntu-14.04-amd64"

  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.omnibus.chef_version = "12.3.0"
  boxes.each do |opts|
    config.vm.define opts[:name] do |config|
      
      config.hostmanager.aliases
      # Take care of that pesky stdin error message on provisioning
      config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"

      # Automatically Add hostnames to HOST hosts file
      config.hostmanager.enabled = true
      config.hostmanager.manage_host = true
      config.vm.network "public_network"
      config.vm.network :private_network, ip: opts[:eth1]

      if opts[:name] == "haproxy"
        config.vm.hostname = "local.ovrride.com"
      else
        config.vm.hostname = opts[:hostname]
      end


      config.vm.provider "vmware_fusion" do |v|
        v.vmx["memsize"] = opts[:mem]
        v.vmx["numvcpus"] = opts[:cpu]
      end

      config.vm.provider "virtualbox" do |v|
        v.customize ["modifyvm", :id, "--memory", opts[:mem]]
        v.customize ["modifyvm", :id, "--cpus", opts[:cpu]]
      end

      config.vm.provision "chef_solo" do |chef|
        # Resolves Chef SSL Error on provisioning
        chef.custom_config_path = "Vagrantfile.chef"

        chef.cookbooks_path = ["cookbooks", "site-cookbooks"]
        chef.roles_path = "roles"
        chef.data_bags_path = "data_bags"
        if opts[:name] == "web1" || opts[:name] == "web2"
          chef.add_role "web"
        else
          chef.add_role opts[:name]
        end
      end
    end
  end
end
