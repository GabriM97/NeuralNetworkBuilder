# NeuralNetworkBuilder
 { ********************** <br/>
 { Autor: @GabriM97 <br/>
 { Telegram: @GabriM97 <br/>
 { Email: marino.gabri97@gmail.com <br/>
 { ********************* <br/>
 <br/>
 **Neural Network Builder** is a website built using **Laravel** (_PHP Framework_) and **Keras** (_ML Python Library_), that allows you to easily build your *Neural Network Models* and train it on your personal dataset.<br/>
I promise you will not write a single line of code, just Import a Dataset -> Build the Model -> Train your Model -> Download your Trained Model. It's easy.
<br/>
### Requires:
* _root permissions (sudo)_
* _MySQL (create database: neural_network_builder <admin:admin>)_
* _PHP_
* _python3_
* _pip3_

<br/>

### Dependences:
**pip3 install**:
* _tensorflow_
* _keras_
* _pandas_
* _supervisor_

<br/>

### Supervisor:
Into the directory `utils/supervisor/` you can find supervisor configuration files. After installing supervisor via *pip3*, copy the config files (both `laravel-worker.conf` and `supervisord.conf`) into `/etc/supervisor/` directory. <br/>
> **NOTE**: Edit the config file `laravel-worker.conf` with your correct paths and username (if you got errors on supervisor, you probably have to edit the `/path/to/supervisor.sock` path, for supervisor socket, into the `supervisord.conf` file)

<br/>

### To run the app:
* composer install
* npm install
* ./start.sh
* php artisan migrate:fresh

<br/>

> **NOTE**: Edit the database settings (database name and user) into `.env` file.<br/><br/>
> **NOTE**: Modify the `upload_max_filesize` and `post_max_size` on your `php.ini` file if you want to upload a **bigger** dataset. (_Linux:_ `/etc/php.ini`) <br/><br/>
> **NOTE**: Edit the `utils/start.sh` script with your correct paths. <br/><br/>
> **NOTE**: If running the script `start.sh` you got an error (such as `unix:///path/to/supervisor.sock no such file`) on supervisroctl tab then don't worry, it will work the same! You can test if everything it's okay running `status` command on supervisorctl tab. If it shows the laravel-workers are in `RUNNING` or `STARTING` status, then it works. If not, try running `reload` command and then `status` again. If you still got this issue or others, then check your supervisor config files or try to unistall and install again.
