import flask
from flask import request, jsonify
import os
import sys
import signal
import platform
import subprocess
import psutil

app = flask.Flask(__name__)
app.config["DEBUG"] = True

# Get CPU Info
def getCPUinfo():
	with open("/proc/cpuinfo", "r")  as f:
		info = f.readlines()

	cpu_info = [x.strip().split(":")[1] for x in info if "model name"  in x]
	cpu_list = []
	for index, item in enumerate(cpu_info):
		cpu_list.append([index, item])

	command = "cat <(grep 'cpu ' /proc/stat) <(sleep 1 && grep 'cpu ' /proc/stat) | awk -v RS='' '{printf ($13-$2+$15-$4)*100/($13-$2+$15-$4+$16-$5)}'"
	cpu_usage = str(subprocess.check_output(['bash','-c', command]).strip()).replace("'","").replace("b","")

	return {
		"threads": len(cpu_list),
		"model": cpu_list[0][1],
		"usage": round(float(cpu_usage), 2),
	}

# Get RAM Info
def getRAMinfo():
	with open("/proc/meminfo", "r") as f:
		lines = f.readlines()
	total = (int(lines[0].strip().split(":")[1].split(" ")[-2])*1024)
	free = (int(lines[1].strip().split(":")[1].split(" ")[-2])*1024)
	usage = (1-(free/total))*100

	return {
		"total": total,
		"free": free,
		"usage": usage,
	}

# Get RAM Info
def getGPUinfo():
	gpu_info = os.popen("lspci | grep VGA").read().strip().split(":")[2]
	return {
		"model": gpu_info,
	}


# ROUTE GET HARDWARE INFO
@app.route('/getHardwareInfo', methods=['GET'], )
def get_hw_info():
	hwinfo = {}

	hwinfo["status"] = 1
	hwinfo["cpu"] = getCPUinfo()
	hwinfo["ram"] = getRAMinfo()
	hwinfo["gpu"] = getGPUinfo()

	return jsonify(hwinfo)


# ROUTE START TRAINING PROCESS
@app.route('/start', methods=['POST'])
def startTraining():
	training_id = request.form['training_id']
	script = request.form['script']
	options = request.form['options'].replace('"','')
	command = "python3 " + script + " " + options
	try:
		pid = subprocess.Popen(command.split(), shell=False).pid
		setstop[training_id] = False
		setpause[training_id] = False
		return str(pid)
	except Exception as err:
		print(str(err))
		return ("ERROR - " + str(err))

setstop = {}
# ROUTE STOP PROCESS
@app.route('/stop', methods=['POST'])
def stopProcess():
	pid = request.form['pid']
	training_id = request.form['training_id']
	try:
		#os.kill(pid, 0) 	# raise an OSError exception if the pid is not running
		os.kill(int(pid), signal.SIGKILL)
		setstop[training_id] = True
		setpause[training_id] = False
		return "OK"
	except Exception as err:
		print(str(err))
		return ("ERROR - " + str(err))

setpause = {}
# ROUTE PAUSE PROCESS
@app.route('/pause', methods=['POST'])
def pauseProcess():
	pid = request.form['pid']
	training_id = request.form['training_id']
	try:
		#os.kill(pid, 0) 	# raise an OSError exception if the pid is not running
		os.kill(int(pid), signal.SIGTERM)
		setpause[training_id] = True
		setstop[training_id] = False
		return "OK"
	except Exception as err:
		print(str(err))
		return ("ERROR - " + str(err))


# ROUTE CHECK PROCESS STATUS
@app.route('/check',  methods=['GET'], )
def check():
	try:
		pid = int(request.args['process_pid'])
		if(psutil.pid_exists(pid)):
			proc = psutil.Process(pid)
			proc_status = proc.status()
			if ((proc_status != psutil.STATUS_RUNNING) and
				(proc_status != psutil.STATUS_WAKING) and
				(proc_status != psutil.STATUS_SLEEPING) and
				(proc_status != psutil.STATUS_DISK_SLEEP)):
				print(proc_status + " - KILL")
				return "KILL"
			print("OK")
			return "OK"
		else:	# process does not exists
			print("STOP")
			return "STOP"
	except Exception as err:
		print(str(err))
		return ("ERROR - " + str(err))


# ROUTE CHECK PROCESS STATUS
@app.route('/checkStatus',  methods=['GET'], )
def checkProcessStatus():
	try:
		pid = int(request.args['process_pid'])
		training_id = request.args['training_id']
		if(psutil.pid_exists(pid)):
			proc = psutil.Process(pid)
			proc_status = proc.status()
			if ((proc_status != psutil.STATUS_RUNNING) and
				(proc_status != psutil.STATUS_WAKING) and
				(proc_status != psutil.STATUS_SLEEPING) and
				(proc_status != psutil.STATUS_DISK_SLEEP)):
				if(setpause[training_id]):	# zombie
					print("PAUSE")
					return "PAUSE"
				if(setstop[training_id]):	# zombie
					print("STOP")
					return "STOP"
				if(not setstop[training_id] and not setstop[training_id]):	# END?
					print(proc_status + " - EXIT")
					return "EXIT"
			print(proc_status + " - OK")
			return "OK"
		else:	# process does not exists
			print(proc_status + " - EXIT")
			return "EXIT"
	except Exception as err:
		print(str(err))
		return ("ERROR - " + str(err))

if __name__ == '__main__':
	os.system("sudo iptables -I INPUT -p tcp --dport 5050 -j ACCEPT")
	app.run(host = '0.0.0.0', port=5050)


# allow port 5050 for external request
#> iptables -I INPUT -p tcp --dport 5050 -j ACCEPT

# block port 5050 from external request
#> iptables -I INPUT -p tcp --dport 5050 -j REJECT
