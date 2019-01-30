#!/usr/bin/env node

/*
 *
 *  Copyright (C) 2016 Libelium Comunicaciones Distribuidas S.L.
 *  http://www.libelium.com
 *
 *  This program is distributed WITHOUT ANY WARRANTY; without
 *  even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 *  PARTICULAR PURPOSE.
 *
 *  By using it you accept the Libelium Terms & Conditions.
 *  You can find them at: http://libelium.com/legal
 *
 *
 *  Version:           2.3
 *  Design:            David Gascon
 *  Implementation:    Diego Becerrica
 */


//npm install -g node-gyp
//npm install websocket
//npm install log4js
//npm install ajax-request
//npm install iniparser


/* Ini */
var ini = require('iniparser');
var cfg = ini.parseSync('../../data/services.ini');

var log_level = cfg.loriot.log_level.replace(/["']/g, "");
var log_file = cfg.loriot.log_file.replace(/["']/g, "");
var websocket_url = cfg.loriot.websocket_url.replace(/["']/g, "");
var service_url = cfg.loriot.service_url.replace(/["']/g, "");

/* Log */
var log4js = require('log4js');
log4js.configure({
  appenders: [
    { type: 'file', filename: log_file, category: 'loriot' }
  ]
});

var logger = log4js.getLogger('loriot');
logger.setLevel(log_level);

/* Websocket */
var WebSocketClient = require('websocket').client;
var client = new WebSocketClient();

client.on('connectFailed', function(error) {
    logger.error('Connect Error: ' + error.toString());
});

client.on('connect', function(connection) {
    logger.info('WebSocket Client Connected');

    connection.on('error', function(error) {
        logger.error("Connection Error: " + error.toString());
    });

    connection.on('close', function() {
        logger.info('echo-protocol Connection Closed');
    });

    connection.on('message', function(message) {
        try {
            logger.info('Message received.');
            logger.info(message);
            if (message.type === 'utf8') {
                var info = JSON.parse(message.utf8Data);
                if (info.cmd == "rx"){
                    var request = require('ajax-request');
                    var device = info.EUI;
                    var data = info.data;
                    var port = info.port;
                    logger.info("Getting response from " + service_url+'websocket_response.php?device='+device+'&data='+data);
                    request(service_url+'websocket_response.php?device='+device+'&data='+data, function(err, res, body) {
                        if(body===false)
                            body="";

                        body = body.toString().trim();

                        if(body!="") {
                            var response  = {
                                cmd   : "tx",
                                EUI   : device,
                                port  : port,
                                data :  body
                            };
                            connection.sendUTF(JSON.stringify(response));
                            logger.info('Send response');
                            logger.info(response);
                        }
                        else{
                            logger.info('Empty response.');
                        }

                    });
                }
            }
        } catch (err) {
          logger.error("On message error: " + err.toString());
        }
      });
});
client.connect(websocket_url, 'echo-protocol');
