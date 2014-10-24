# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  # ubuntu 14.04 64bit base
  config.vm.box = "ubuntu/trusty64"
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.vm.host_name = "local.ovrride.com"
  config.vm.network "private_network", ip: "192.168.50.4"
  config.vm.network "public_network"
  config.vm.synced_folder ".", "/var/www/"

  config.vm.provider "virtualbox" do |vb|
    # Up memory on VM
    vb.customize ["modifyvm", :id, "--memory", "1024"]
  end

  config.vm.provision "chef_solo" do |chef|
    chef.cookbooks_path = ["cookbooks", "site-cookbooks"]
    chef.roles_path = "roles"
    chef.data_bags_path = "data_bags"
    chef.add_role "ovr-dev-box"
  end
end
