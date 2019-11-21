# Network Shared Filesystem (NFS) Client/Server Initialization (Debian-Ubuntu Systems)

### Server Only
1. Intall _NFS_ package: <br>
`#> apt-get install -y nfs-kernel-server`

2. **OPTIONAL.** Uncomment and change "Domain" name (line 6) to `webserver.neuralnetworkbuilder`: <br>
`#> gedit /etc/idmapd.conf`

3. Add the folder to share and which hosts can access the directory. <br>
Add the following line to the end of `/etc/exports` <br>
`#> echo "/PATH/TO/REPO/NeuralNetworkBuilder/website/storage/app 192.168.1.0/24(rw,no_root_squash)" >> /etc/exports`

4. Restart the server to read the new settings: <br>
`#> systemctl restart nfs-server`


### Client Only
1. Install _NFS_ package: <br>
`#> apt-get -y install nfs-common`

2. **OPTIONAL.** Uncomment and change "Domain" name (line 6) to `node.neuralnetworkbuilder`: <br>
`#> gedit /etc/idmapd.conf`

2.1 Test if shared folder is shown on the server <br>
`#> showmount -e _SERVER_IP_ADDRESS_`

3. Mount the NFS: <br>
`#> mount -t nfs _SERVER_IP_ADDRESS_:/PATH/TO/REPO/NeuralNetworkBuilder/website/storage/app /PATH/TO/PYTHON/SCRIPTS/python/saves`

4. Add the following line to the end of '/etc/fstab': <br>
`#> echo "_SERVER_IP_ADDRESS_:/PATH/TO/REPO/NeuralNetworkBuilder/website/storage/app /PATH/TO/PYTHON/SCRIPTS/python/saves nfs defaults 0 0" >> /etc/fstab`
