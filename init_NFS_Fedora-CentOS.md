#Network Shared Filesystem (NFS) Client/Server Initialization (Fedora-CentOS)

### Server Only

1. Install _NFS_ package: <br>
`#> yum install nfs-utils` <br>

2. **OPTIONAL.** Uncomment and change the "Domain" name (line 5) to `webserver.neuralnetworkbuilder`: <br>
`#> gedit /etc/idmapd.conf` <br>

3. Add the folder to share and which hosts can access the directory. <br>
Add the following line at the end of `/etc/exports`: <br>
`#> echo "/PATH/TO/REPO/NeuralNetworkBuilder/website/storage/app 192.168.1.0/24(rw,no_root_squash,no_wdelay,async)" >> /etc/exports` <br>

4. Start NFS service and NFS Server: <br>
`#> systemctl start rpcbind nfs-server` <br>
`#> systemctl enable rpcbind nfs-server` <br>

5. Allow NFS service to Firewall: <br>
`#> firewall-cmd --add-service=nfs --permanent` <br>
`#> firewall-cmd --add-service=rpc-bind --permanent` <br>
`#> firewall-cmd --add-service=mountd --permanent` <br>
`#> firewall-cmd --add-port=2049/tcp --permanent` <br>
`#> firewall-cmd --add-port=2049/udp --permanent` <br>
`#> firewall-cmd --reload` <br>


### Client Only

1. Install _NFS_ package: <br>
`#> yum install nfs-utils` <br>

2. **OPTIONAL.** Uncomment and change the "Domain" name (line 5) to `node.neuralnetworkbuilder`: <br>
`#> gedit /etc/idmapd.conf` <br>

3. Start NFS service: <br>
`#> systemctl start rpcbind` <br>
`#> systemctl enable rpcbind` <br>

4. Mount the NSF: <br>
`#> mount -t nfs _SERVER_IP_ADDRESS_:/PATH/TO/REPO/NeuralNetworkBuilder/website/storage/app /PATH/TO/PYTHON/SCRIPTS/python/saves` <br>

5. Configure NFS mounting when the system starts. <br>
Add the following line at the end of `/etc/fstab` <br>
`#> echo "_SERVER_IP_ADDRESS_:/PATH/TO/REPO/NeuralNetworkBuilder/website/storage/app /PATH/TO/PYTHON/SCRIPTS/python/saves nfs defaults 0 0" >> /etc/fstab` <br>
