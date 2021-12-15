Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/focal64"
  config.vm.provider "virtualbox" do |vb|
     vb.gui = true
     vb.memory = "2048"
  end

  config.vm.provision "shell", inline: <<-SHELL
    apt-get update
    apt-get install -y apache2
  SHELL

  config.vm.provision "shell", inline: <<-SHELL
     mkdir -p /home/vagrant/.ssh
     mkdir -p /home/vagrant/.ansible
     mkdir -p /home/vagrant/.config
     mkdir -p /home/vagrant/.config/openstack
  SHELL


  config.vm.provision "shell", inline: <<-SHELL
     yes | sudo apt-get install python3-pip
     python3 -m pip install --upgrade pip
     python3 -m pip install --upgrade setuptools
     python3 -m pip install --upgrade openstacksdk
     pip3 install openstacksdk
     python3 -m pip install software-properties-common
     python3 -m pip install ansible
     python3 -m pip install kafka-python
     sudo ansible-galaxy collection install openstack.cloud
     python3 -m pip install --upgrade openstacksdk
     sudo chown -R vagrant /home/vagrant/.ansible
  SHELL
  
  config.vm.provision "file", source: "./assets/db-server.properties", destination: "~/assets/db-server.properties"
  config.vm.provision "file", source: "./assets/zk-server.properties", destination: "~/assets/zk-server.properties"
  config.vm.provision "file", source: "./assets/zookeeper.properties", destination: "~/assets/zookeeper.properties"
  config.vm.provision "file", source: "./assets/producer.py", destination: "~/assets/producer.py"
  config.vm.provision "file", source: "./assets/consumer.py", destination: "~/assets/consumer.py"
  config.vm.provision "file", source: "./assets/data.csv", destination: "~/assets/data.csv"
  config.vm.provision "file", source: "./assets/cloud-computing.pem", destination: "~/.ssh/"
  config.vm.provision "file", source: "./assets/ansible.cfg", destination: "/home/vagrant/.ansible.cfg"
  config.vm.provision "file", source: "./assets/mysqld.cnf", destination: "~/assets/mysqld.cnf"
  config.vm.provision "file", source: "./assets/cache-query-results.py", destination: "~/assets/cache-query-results.py"
  config.vm.provision "file", source: "./src", destination: "~/src"


  config.vm.provision "file",
    source: "./MyInventory",
    destination: "/tmp/MyInventory"
  config.vm.provision "shell",
    inline: "mv /tmp/MyInventory /home/vagrant/.ansible/MyInventory"
  config.vm.provision "file",
    source: "./assets/clouds.yaml",
    destination: "/tmp/clouds.yaml"
  config.vm.provision "shell",
    inline: "mv /tmp/clouds.yaml /home/vagrant/.config/openstack/clouds.yaml"




  config.vm.provision "ansible_local" do |ansible|
    ansible.playbook = "playbook_master.yml"
    ansible.verbose = true
    ansible.install = true 
    ansible.limit = "all"
    ansible.inventory_path = "MyInventory"  
    # ansible.start_at_task = "Update Web App Pages" # used to run only some tasks
  end
end