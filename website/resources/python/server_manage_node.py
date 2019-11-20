import flask
from flask import request, jsonify
import os
import platform

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

	return {
		"threads": len(cpu_list), 
		"model": cpu_list[0][1]
	}

# Get RAM Info
def getRAMinfo():
	with open("/proc/meminfo", "r") as f:
		lines = f.readlines()

	return {
		"total": (int(lines[0].strip().split(":")[1].split(" ")[-2])*1024),
		"free": (int(lines[1].strip().split(":")[1].split(" ")[-2])*1024)
	}

# Get RAM Info
def getGPUinfo():

	gpu_info = os.popen("lspci | grep VGA").read().strip().split(":")[2]
	return {
		"model": gpu_info,
	}


# ROUTE GET HARDWARE INFO
@app.route('/getHardwareInfo', methods=['GET'])
def home():
	hwinfo = {}

	hwinfo["status"] = 1
	hwinfo["cpu"] = getCPUinfo()
	hwinfo["ram"] = getRAMinfo()
	hwinfo["gpu"] = getGPUinfo()

	return jsonify(hwinfo)



if __name__ == '__main__':
	os.system("sudo iptables -I INPUT -p tcp --dport 5050 -j ACCEPT")
	app.run(host = '0.0.0.0', port=5050)

# allow port 5000 for external request
#> iptables -I INPUT -p tcp --dport 5050 -j ACCEPT

# block port 5000 from external request
#> iptables -I INPUT -p tcp --dport 5050 -j REJECT	