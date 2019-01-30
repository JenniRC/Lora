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
    $data = isset($_GET['data']) ? $_GET['data'] : "";
    $device = isset($_GET['device']) ? $_GET['device'] : "";

    $config = new Config_Lite('../../data/services.ini');
    $log = new Logger($config->get('loriot', 'log_file', ''), "Loriot", $config->get('loriot', 'log_level', 'OFF'));

    if ($data != "") {
        $frame = new Frame($device);
        $data_frame = $frame->get_info($data);
        if ($frame->waiting_response()) {
            echo $frame->get_response();
        }
    }

    //To-Do: get the information received: $frame->get_data();

} catch (Exception $e) {
    $log->addError($e->getMessage());
}

?>
