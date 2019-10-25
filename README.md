# NeuralNetworkBuilder
 This is a Neural Network Builder website build using Laravel and Keras

### Requires:
* _root permissions (sudo)_
* _python3_
* _pip3_

### Dependences:
**pip3 install**:
* _tensorflow_
* _keras_
* _pandas_
* _supervisor_

### Supervisor:
Into the directory _utils/supervisor/_ you can find supervisor configuration files. After installing supervisor via _pip3_, copy the config files (both **laravel-worker.conf** and **supervisord.conf**) into _/etc/supervisor/_ directory.
**NOTE**: Edit the config file _laravel-worker.conf_ with your correct paths and username (if you got errors on supervisor, you probably have to edit the supervisor.sock path, for supervisor socket, into the _supervisord.conf_ file)

### To run the Laravel app:
**NOTE**: modify the '_upload_max_filesize_' and '_post_max_size_' on your _php.ini_ file if you want to upload a **bigger** dataset. (_Linux: /etc/php.ini_)
**NOTE**: Edit the _utils/start.sh_ script with your correct paths.

* composer install
* npm install
* ./start.sh
* php artisan migrate:fresh
