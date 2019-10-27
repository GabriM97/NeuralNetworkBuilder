# NeuralNetworkBuilder
 This is a **Neural Network Builder** website build using **Laravel** and **Keras**

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
Into the directory `utils/supervisor/` you can find supervisor configuration files. After installing supervisor via *pip3*, copy the config files (both `**laravel-worker.conf**` and `**supervisord.conf**`) into `/etc/supervisor/` directory. <br/>
> **NOTE**: Edit the config file `laravel-worker.conf` with your correct paths and username (if you got errors on supervisor, you probably have to edit the supervisor.sock path, for supervisor socket, into the `supervisord.conf` file)

### To run the Laravel app:
> **NOTE**: modify the `upload_max_filesize` and `post_max_size` on your `php.ini` file if you want to upload a **bigger** dataset. (_Linux: /etc/php.ini_) <br/>
> **NOTE**: Edit the `utils/start.sh` script with your correct paths. <br/>

* composer install
* npm install
* ./start.sh
* php artisan migrate:fresh

<br/>
> **NOTE**: If running the script you got an error (such as '_unix:///path/to/supervisor.sock no such file_') on "supervisroctl" tab then don't worry, it will work the same! You can test if everything it's okay running `status` command on supervisorctl tab. If it shows the laravel-workers are in `RUNNING` or `STARTING` status, then it works. <br/>
> If not, try running `reload` command and then `status` again. If you still got this issue or others, then check your supervisor config files or try to unistall and install again.
