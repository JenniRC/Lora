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

        $ID = $_POST['device'];

        $list = explode(",", $ID);
        foreach($list as $val){
            $config->removeSection(trim($val));
        }

        $config->save();
        $res = "Configuration deleted.";
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