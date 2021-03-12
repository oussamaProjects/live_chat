var socket = require('socket.io');
var express = require('express');
var http = require('http');
var https = require('https');
var mysql = require('mysql');
var cron = require('node-cron');
var request = require("request");

var envlnk = "prod";

// cron.schedule('* * * * *', () => {
// 	console.log('running a task every minute');
// 	request.get("http://livepromo.test/modules/lnk_livepromo/cron.php?secure_key=e235f7020e0bdfdb35199360b416c416", function (error, response, body) {
// 		// console.log(body);
// 	});
// });

if(envlnk == "dev"){
	var db = mysql.createConnection({
		host: 'localhost',
		user: 'root',
		password: '',
		database: 'grossiste'
	});
}else{
	var db = mysql.createConnection({
		host: 'localhost',
		user: 'grossiste-en-lig',
		password: 'QnVbteCfP9hh',
		database: 'grossiste_en_ligne'
	});
}

// Log any errors connected to the db
db.connect(function (err) {
	if (err) console.log(err)
});

// Logger config
const { createLogger, format, transports } = require('winston');
const fs = require('fs');
const path = require('path');

const env = process.env.NODE_ENV || 'development';
const logDir = 'logs';

// Create the log directory if it does not exist
if (!fs.existsSync(logDir)) {
	fs.mkdirSync(logDir);
}

const filename = path.join(logDir, 'sockets.log');

const logger = createLogger({
	// change level if in dev environment versus production
	level: env === 'development' ? 'debug' : 'info',
	format: format.combine(
		format.timestamp({
			format: 'YYYY-MM-DD HH:mm:ss'
		}),
		format.printf(info => `${info.timestamp} ${info.level}: ${info.message}`)
	),
	transports: [
		new transports.Console({
			level: 'info',
			format: format.combine(
				format.colorize(),
				format.printf(
					info => `${info.timestamp} ${info.level}: ${info.message}`
				)
			)
		}),
		new transports.File({ filename })
	]
});


// This line is from the Node.js HTTPS documentation.
if(envlnk == "dev"){
	var options = {
		key: fs.readFileSync('f:/laragon/etc/ssl/laragon.key').toString(),
		cert: fs.readFileSync('f:/laragon/etc/ssl/laragon.crt').toString()
	};
}else{
	var options = {
		key: fs.readFileSync('/home/grossiste-en-ligne/ssl/star.grossiste-en-ligne.com.key').toString(),
		cert: fs.readFileSync('/home/grossiste-en-ligne/ssl/star.grossiste-en-ligne.com.crt').toString()
	};
}


var app = express();
//var server = http.createServer( app );
var server = https.createServer(options, app);

var io = socket.listen(server);

var users = [];
var currentuser = "";

io.sockets.on('connection', function (socket) {

	socket.on('login', function (user) {
		socket.emit('logged');
		var nbr = 0;
		var old = 0;
		var ex = 1;
		var seconds = new Date().getTime() / 1000;

		var me = {
			iddiv: seconds.toFixed(0) + user.userID,
			id: user.userID,
			customer_name: user.customer_name,
			socketId: socket.id
		}
		currentuser = user.userID;
		for (var i = users.length - 1; i >= 0; i--) {
			if (user.userID == users[i].id) {
				ex = 0;
			}
		}
		users.push(me);
		//io.sockets.emit('newusr', user);
		logger.info(`connected client id ${socket.id}`);
		for (var i = users.length - 1; i >= 0; i--) {
			if (users[i].id !== old) {
				nbr += 1;
				old = users[i].id;
			}
		}
		sql = "SELECT * FROM `ps_configuration` WHERE `name` = ?";
		db.query(sql, ["LP_nbr_customer_connected"], function (err, result) {
			if (err) throw err;
			if (result.length > 0) {
				db.query('UPDATE ps_configuration SET value = ? WHERE name = ?', [nbr, 'LP_nbr_customer_connected'], function (error, results, fields) {
					if (error) throw error;
				});
			}
		});
		if (ex) {
			for (var i = users.length - 1; i >= 0; i--) {
				socket.to(users[i].socketId).emit('userlogged', me);
			}
		}

	});

	socket.on('actionvalidateorder', function (data) {
		for (var i = users.length - 1; i >= 0; i--) {
			socket.to(users[i].socketId).emit('actionvalidateorder', data);
		}
	});

	socket.on('actioncartsavenew', function (data) {
		console.log('actioncartsave');
		console.log(users.length);
		for (var i = users.length - 1; i >= 0; i--) {
			socket.to(users[i].socketId).emit('actioncartsavenew', data);
		}
	});

	socket.on('actionauthentication', function (data) {
		// console.log(data);
		for (var i = users.length - 1; i >= 0; i--) {
			socket.to(users[i].socketId).emit('actionauthentication', data);
		}
	});

	socket.on('actioncustomeraccountadd', function (data) {
		// console.log(data);
		for (var i = users.length - 1; i >= 0; i--) {
			socket.to(users[i].socketId).emit('actioncustomeraccountadd', data);
		}
	});

	socket.on('actiongetpromonotification', function (data) {
		// socket.emit('getpromonotification', data);
		for (var i = users.length - 1; i >= 0; i--) {
			socket.to(users[i].socketId).emit('getpromonotification', data);
		}
	});


	socket.on('actiongetscheduled_product', function (data) {
		// socket.emit('getscheduled_product', data);
		for (var i = users.length - 1; i >= 0; i--) {
			socket.to(users[i].socketId).emit('getscheduled_product', data);
		}
	});

	socket.on('actionafterdeleteproductincart', function (data) {
		// socket.emit('actionafterdeleteproductincart', data );
		for (var i = users.length - 1; i >= 0; i--) {
			socket.to(users[i].socketId).emit('actionafterdeleteproductincart', data);
		}
	});

	socket.on('actionlikedproduct', function (data) {
		// socket.emit('actionlikedproduct', data );
		for (var i = users.length - 1; i >= 0; i--) {
			socket.to(users[i].socketId).emit('actionlikedproduct', data);
		}
	});

	socket.on('disconnect', function () {
		var cuser = 0;
		var nbr = 0;
		var old = 0;
		for (var i = users.length - 1; i >= 0; i--) {
			if (users[i].socketId === socket.id) {
				logger.info("Disconnect client id " + users[i].id + " Socket id " + users[i].socketId);
				cuser = users[i].id;
				users.splice(i, 1);
				break;
			}
		}
		for (var i = users.length - 1; i >= 0; i--) {
			if (users[i].id !== old) {
				nbr += 1;
				old = users[i].id;
			}
		}

		sql = "SELECT * FROM `ps_configuration` WHERE `name` = ?";
		db.query(sql, ["LP_nbr_customer_connected"], function (err, result) {
			if (err) throw err;
			if (result.length > 0) {
				db.query('UPDATE ps_configuration SET value = ? WHERE name = ?', [nbr, 'LP_nbr_customer_connected'], function (error, results, fields) {
					if (error) throw error;
				});
			}
		});

		// for(var i = users.length - 1; i >= 0; i--) {
		// 	io.sockets.emit('userlogged', socket.id );
		// }

	});

});

server.listen(3006, function (err) {
	if (err) {
		console.log(err);
	} else {
		console.log("Listening on port 3006 v1.3");
	}
});
