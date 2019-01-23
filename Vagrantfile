require "yaml"

vconfig = YAML::load_file("./Vagrantparams.yml")

Vagrant.configure("2") do |config|
    config.vm.box = "bento/ubuntu-16.04"
    config.vm.host_name = vconfig["hostname"]
    config.vm.network "private_network", :ip => vconfig['ip']
    config.vm.synced_folder ".", "/vagrant", nfs: vconfig["nfs"]

    config.vm.provider "virtualbox" do |vb|
        vb.customize [
            "modifyvm", :id,
            "--name", vconfig["hostname"],
            "--natdnsproxy1", "on",
            "--natdnshostresolver1", "on",
            "--memory", vconfig["memory"],
            "--cpus", vconfig['cpu']
        ]
    end

    config.trigger.before :up do |trigger|
        trigger.name = "Install mkcert di host"
        trigger.run = {path: "provisioning/scripts/install_mkcert.sh"}
    end

    config.trigger.after :destroy do |trigger|
        trigger.name = "Uninstall mkcert di host"
        trigger.run = {path: "provisioning/scripts/uninstall_mkcert.sh"}
    end

    config.vm.provision "ansible" do |ansible|
        ansible.become = true
        ansible.become_user = "root"
        ansible.host_key_checking = false
        ansible.playbook = "provisioning/playbook.yml"
        ansible.verbose = "v"
    end
end
