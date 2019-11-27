import flask
from flask import request, jsonify
import os
import platform
import subprocess

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
	script = request.form['script']
	options = request.form['options'].replace('"','')
	command = "python3 " + script + " " + options
	try:
		pid = subprocess.Popen(command.split(), shell=False).pid
		return str(pid)
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