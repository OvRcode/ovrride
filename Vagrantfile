# -*- mode: ruby -*-

# vi: set ft=ruby :
require 'yaml'
settings = YAML.load_file 'vagrant.yml'

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
        :name => "mysql",
        :hostname => "mysql.local.ovrride.com",
        :eth1 => "192.168.50.7",
        :mem => "512",
        :cpu => "1"
    }
]

Vagrant.configure(2) do |config|

  config.vm.box = "ubuntu/trusty64;"

  config.vm.provider "vmware_fusion" do |v, override|
    override.vm.box = "https://oss-binaries.phusionpassenger.com/vagrant/boxes/latest/ubuntu-14.04-amd64-vmwarefusion.box"
  end
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  boxes.each do |opts|
    config.vm.define opts[:name] do |config|
      
      # Take care of that pesky stdin error message on provisioning
      config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"
      
      # Automatically Add hostnames to HOST hosts file
      config.hostmanager.enabled = true
      config.hostmanager.manage_host = true
      config.vm.network "public_network"
      config.vm.network :private_network, ip: opts[:eth1]

      if opts[:name] == "haproxy"
        config.vm.hostname = "local.ovrride.com"
        config.vm.synced_folder ".", "/vagrant", id: "vagrant-root", disabled: true
      else
        config.vm.hostname = opts[:hostname]
        config.vm.synced_folder ".", "/var/www/"
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
        chef.custom_config_path = "Vagrantfile.chef"
        chef.cookbooks_path = ["cookbooks", "site-cookbooks"]
        chef.roles_path = "roles"
        chef.data_bags_path = "data_bags"
        chef.add_role opts[:name]
      end
    end
  end
end