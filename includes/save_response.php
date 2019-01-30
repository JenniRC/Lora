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

    $error = array();
    $res = "";

    try {
        $device = isset($_POST["device"]) ? $_POST["device"] : NULL;
        $response = isset($_POST["response"]) ? $_POST["response"] : NULL;

        if ($device == NULL) throw new Exception('device not recognized.');
        elseif ($response == NULL) throw new Exception('response not recognized.');
        else {
            require_once 'Config/Lite.php';
            $config = new Config_Lite('../data/responses.ini', LOCK_EX);

            $devices = explode(",", $device);
            foreach ($devices as $dev){
                $config->setString('devices', trim($dev), $response);
            }
            $config->save();
            $res = "Response saved";
        }
    } catch (Exception $e) {
        $error[] = $e->getMessage();
    } finally {
        if(count($error)!=0){
            $data = array ("status"=>"NOK", "data" => implode("\n", $error));
        }
        else{
            $data = array ("status"=>"OK", "data" => $res);
        }

        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($data);
    }