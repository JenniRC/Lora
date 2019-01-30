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
        require_once 'Config/Lite.php';
        $config = new Config_Lite('../data/configuration.ini', LOCK_EX);

        $devices = isset($_POST["DEVICE_ID"]) ? $_POST["DEVICE_ID"] : NULL;
        if ($devices == NULL) throw new Exception('device not recognized.');
        unset($_POST['DEVICE_ID']);

        $device = explode(";", $devices);
        foreach($device as $dev) {
            $ID = trim($dev);

            foreach ($_POST as $key => $val) {
                $config->setString($ID, $key, $val);
            }

            //ON-OFF values
            if (!array_key_exists("NM_STATUS", $_POST)) $config->setString($ID, "NM_STATUS", 0); else $config->setString($ID, "NM_STATUS", 1);
            if (!array_key_exists("ENABLE_DAILY_FRAME", $_POST)) $config->setString($ID, "ENABLE_DAILY_FRAME", 0); else $config->setString($ID, "ENABLE_DAILY_FRAME", 1);
            if (!array_key_exists("RESET_REQUEST", $_POST)) $config->setString($ID, "RESET_REQUEST", 0); else $config->setString($ID, "RESET_REQUEST", 1);
        }
        $config->save();
        $res = "Configuration saved.";
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