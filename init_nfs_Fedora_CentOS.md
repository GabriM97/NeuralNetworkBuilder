#Network Shared Filesystem (NFS) Client/Server Initialization (Fedora-CentOS)

### Server Only

1. Install _NFS_ package:
`#> yum install nfs-utils`

2. **OPTIONAL.** Uncomment and change the "Domain" name (line 5) to `webserver.neuralnetworkbuilder`:
`#> gedit /etc/idmapd.conf`

3. Add the folder to share and which hosts can access the directory.
Add the following line at the end of `/etc/exports`:
`#> echo "/PATH/TO/REPO/NeuralNetworkBuilder/website/storage/app 192.168.1.0/24(rw,no_root_squash)" >> /etc/exports`

4. Start NFS service and NFS Server:
`#> systemctl start rpcbind nfs-server`
`#> systemctl enable rpcbind nfs-server`

5. Allow NFS service to Firewall:
`#> firewall-cmd --add-service=nfs --permanent`
`#> firewall-cmd --add-service=rpc-bind --permanent`
`#> firewall-cmd --add-service=mountd --permanent`
`#> firewall-cmd --add-port=2049/tcp --permanent`
`#> firewall-cmd --add-port=2049/udp --permanent`
`#> firewall-cmd --reload`


### Client Only

1. Install _NFS_ package:
`#> yum install nfs-utils`

2. **OPTIONAL.** Uncomment and change the "Domain" name (line 5) to `node.neuralnetworkbuilder`:
`#> gedit /etc/idmapd.conf`

3. Start NFS service:
`#> systemctl start rpcbind`
`#> systemctl enable rpcbind`

4. Mount the NSF:
`#> mount -t nfs _SERVER_IP_ADDRESS_:/PATH/TO/REPO/NeuralNetworkBuilder/website/storage/app /PATH/TO/PYTHON/SCRIPTS/python/saves`

5. Configure NFS mounting when the system starts.
Add the following line at the end of `/etc/fstab`
`#> echo "_SERVER_IP_ADDRESS_:/PATH/TO/REPO/NeuralNetworkBuilder/website/storage/app /PATH/TO/PYTHON/SCRIPTS/python/saves nfs defaults 0 0" >> /etc/fstab`



