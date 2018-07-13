# Mount WEB-APP dir
$projectFolderPath = 'code'

# Mount temp share
#$shareFolderPath = '../Temp'

Vagrant.configure("2") do |config|
#	config.vm.synced_folder $projectFolderPath, "/var/www/stock", create: true, :nfs => {:mount_options => ["dmode=777", "fmode=777","nfsvers=4"]}, type: "nfs"
#	config.vm.synced_folder $shareFolderPath,   "/tmp/share",      create: true, :nfs => {:mount_options => ["dmode=777", "fmode=777","nfsvers=4"]}, type: "nfs"
	config.vm.synced_folder $projectFolderPath, "/var/www/stock", create: true, :mount_options => ["dmode=777", "fmode=777"]
#	config.vm.synced_folder $shareFolderPath,   "/tmp/share",      create: true, :mount_options => ["dmode=777", "fmode=777"]
	
	config.vm.box = "debian/contrib-jessie64"
	config.vm.provider "virtualbox" do |v|
    	    v.memory = 1024*2
    	    v.cpus = 1
			v.name = "stock"
			v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
			v.customize ["modifyvm", :id, "--cableconnected1", "on"]
	end

	config.vbguest.no_remote   = true
    config.vbguest.auto_update = false
	config.vm.box_check_update = false
	config.vm.network :private_network, ip: "192.168.10.20"
#	config.vm.network "public_network", ip: "192.168.20.30"
#	config.vm.network "forwarded_port", guest: 22, host: 2222, host_ip: "127.0.0.1", id: 'ssh'
#	config.vm.network "forwarded_port", guest: 80, host: 80, host_ip: "", id: 'World'
#New options starts
#    config.winnfsd.uid = 1
#    config.winnfsd.gid = 1
#New options ends
	config.vm.hostname = "stock"

	config.vm.provision "file", source: "source-file", destination: "/tmp/source-file"
	config.vm.provision "provision", type: :shell, path: "scripts/configure.sh"
	config.vm.provision :shell, path: "scripts/bootstrap.sh", run: 'always'

#	config.trigger.after :destroy do
#		info "Destroy winnfsd process"
#		run "Taskkill /IM winnfsd.exe /F"
#	end
end
