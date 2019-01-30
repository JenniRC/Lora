<?php
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

require_once '../../includes/class.Frame.php';
require_once '../../includes/class.Logger.php';

try {
    $config = new Config_Lite('../../data/services.ini');

    $log = new Logger($config->get('loriot_http', 'log_file', ''), "Loriot_HTTP", $config->get('loriot_http', 'log_level', 'OFF'));

    $data = json_decode(file_get_contents('php://input'), true);

    $log->addInfo("Received info: " . print_r($data, true));

    if ($data["cmd"] == "rx") {
        $frame = new Frame($data["EUI"], abs($data['rssi']));
        $data_frame = $frame->get_info($data["data"]);
        if ($frame->waiting_response()) {
            /*$ch = curl_init("https://" . $config->get('loriot_http', 'server_url', '') . "/1/rest");
            $authorization = "Authorization: Bearer " . $config->get('loriot_http', 'token', '');
            $data_json = array("appid" => $config->get('loriot_http', 'appid', ''), "cmd" => "tx", "EUI" => $data["EUI"], "port" => $data["port"], "confirmed" => false, "data" => $frame->get_response());
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_json));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            $log->addInfo("Sent info: " . print_r($result, true));*/

            $log->addInfo("Response: " . $frame->get_response());
        }

        //To-Do: get the information received: $frame->get_data();
    }
} catch (Exception $e) {
    $log->addError($e->getMessage());
}

?>
