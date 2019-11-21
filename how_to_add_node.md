# How to add a new node to the main cluster (root permissions required).
1. Start the machine you want to add to the cluster;
2. Install all python dependeces as in `README.md`;
3. Copy the _python_ folder (`website/resources/python/`) into the local machine you want to add to the cluster;
4. Go into the copied python directory and run `#> sudo python3 server_manage_node.py`;
5. Run `#> ip a` on the shell and get the local IP address (remember it);
6. Go to the web app and log in with an Admin user;
7. Open the Admin panel and go to "Create new Node";
8. Insert here the node IP Address you got at step 5, then press "Add Node" button.
