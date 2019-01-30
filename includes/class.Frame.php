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


require_once 'Config/Lite.php';


class Frame
{
    private $data = "";
    private $type = "";
    private $return = "";
    private $device_id = "";

    private $rssi = 0;

    private $bytes = array();
    private $bits = array();
    private $info = array();

    private $version= "";
    private $data_length=0;

    public function __construct($_deviceID, $_rssi=0)
    {
        $this->device_id = $_deviceID;
        $this->rssi = floor(abs($_rssi));
    }

    private function median($values){
        $size = count($values);
        $middle_index = floor($size / 2);
        sort($values, SORT_NUMERIC);

        $median = $values[$middle_index]; // assume an odd # of items

        // Handle the even case by averaging the middle 2 items
        if ($size % 2 == 0) {
            $median = ($median + $values[$middle_index - 1]) / 2;
        }
        return floor($median);
    }

    private function reset()
    {
        $this->type = "";
        $this->return = "";

        unset($this->info);
        unset($this->bytes);
        unset($this->bits);

        $this->bytes = array();
        $this->bits = array();
        $this->info = array();
    }

    public function get_info($_data)
    {
        $this->reset();
        $this->data_length = strlen($_data);
        switch ($this->data_length) {
            case 22:
                $this->version=2;
                break;
            case 24:
                $this->version=1;
                break;
            default:
                $this->version=0;
                throw new Exception('Bad data length.');
                break;
        }

        $this->data = $_data;
        for ($i = 0; $i < $this->data_length; $i += 2) {
            $this->bytes[] = substr($this->data, $i, 2);
        }

        foreach ($this->bytes as $byte) {
            $this->bits[] = str_pad(base_convert($byte, 16, 2), 8, "0", STR_PAD_LEFT);
        }

        $this->type = base_convert(substr($this->bits[0], -4), 2, 10);


        $this->info['device_id'] = $this->device_id;
        $this->info['battery'] = substr($this->bits[0], 1, 1);
        $this->info['parking'] = substr($this->bits[0], 0, 1);
        $this->info["frame_counter"] = base_convert($this->bits[1], 2, 10);
        $this->info['frame_type'] = $this->type;

        switch ($this->type) {
            case 0: //INFO
                if($this->version==1) {
                    $this->info["temperature_msb"] = base_convert($this->bits[2], 2, 10);
                    $this->info["temperature_lsb"] = base_convert($this->bits[3], 2, 10);
                    $this->info["x_msb"] = base_convert($this->bits[4], 2, 10);
                    $this->info["x_lsb"] = base_convert($this->bits[5], 2, 10);
                    $this->info["y_msb"] = base_convert($this->bits[6], 2, 10);
                    $this->info["y_lsb"] = base_convert($this->bits[7], 2, 10);
                    $this->info["z_msb"] = base_convert($this->bits[8], 2, 10);
                    $this->info["z_lsb"] = base_convert($this->bits[9], 2, 10);
                }
                elseif($this->version==2) {
                    $this->info["temperature"] = base_convert($this->bits[2], 2, 10);
                    $this->info["x_msb"] = base_convert($this->bits[3], 2, 10);
                    $this->info["x_lsb"] = base_convert($this->bits[4], 2, 10);
                    $this->info["y_msb"] = base_convert($this->bits[5], 2, 10);
                    $this->info["y_lsb"] = base_convert($this->bits[6], 2, 10);
                    $this->info["z_msb"] = base_convert($this->bits[7], 2, 10);
                    $this->info["z_lsb"] = base_convert($this->bits[8], 2, 10);
                }
                break;

            case 1: //KEEP-ALIVE
                break;

            case 2: //DAILY UPDATE
                $this->info["sensor_msb"] = base_convert($this->bits[2], 2, 10);
                $this->info["sensor_lsb"] = base_convert($this->bits[3], 2, 10);
                $this->info["sigfox_msb"] = base_convert($this->bits[4], 2, 10);
                $this->info["sigfox_lsb"] = base_convert($this->bits[5], 2, 10);
                $this->info["lorawan_msb"] = base_convert($this->bits[6], 2, 10);
                $this->info["lorawann_lsb"] = base_convert($this->bits[7], 2, 10);
                $this->info["resets_today"] = base_convert($this->bits[8], 2, 10);
                $this->info["CONFIG_ID"] = base_convert($this->bits[9], 2, 10);

                $config = new Config_Lite('../../data/configuration.ini');
                $daily = $config->get($this->device_id, 'ENABLE_DAILY_FRAME', 'null');
                if ($daily == 'null') {
                    throw new Exception("ENABLE_DAILY_FRAME does not exists in section " . $this->device_id . " - file: configuration.ini");
                }
                $reset = $config->get($this->device_id, 'RESET_REQUEST', 'null');
                if ($reset == 'null') {
                    throw new Exception("RESET_REQUEST does not exists in section " . $this->device_id . " - file: configuration.ini");
                }

                $config = new Config_Lite('../../data/responses.ini', LOCK_EX);
                $response = $config->get("devices", $this->device_id, 'null');
                if ($response == 'null')
                    throw new Exception("Device ID " . $this->device_id . " does not exist in section devices - file: responses.ini");
                else {
                    $daily = ($daily == 0) ? "0" : "1";
                    $reset = ($reset == 0) ? "0" : "1";

                    $this->return = $response;
                    $this->return .= str_pad(base_convert(date('H'), 10, 16), 2, "0", STR_PAD_LEFT);
                    $this->return .= str_pad(dechex(bindec($daily . $reset . str_pad(decbin(date('i')), 6, "0", STR_PAD_LEFT))), 2, "0", STR_PAD_LEFT);
                }
                break;

            case 3: //ERROR
                $this->info["error_z"] = base_convert(substr($this->bits[2], 7, 1), 2, 10);
                $this->info["error_y"] = base_convert(substr($this->bits[2], 6, 1), 2, 10);
                $this->info["error_x"] = base_convert(substr($this->bits[2], 5, 1), 2, 10);
                $this->info["error_rtc"] = base_convert(substr($this->bits[2], 4, 1), 2, 10);
                $this->info["error_lorawan"] = base_convert(substr($this->bits[2], 3, 1), 2, 10);
                $this->info["error_sigfox"] = base_convert(substr($this->bits[2], 2, 1), 2, 10);
                if($this->version==1) {
                    $this->info["temperature_msb"] = base_convert($this->bits[3], 2, 10);
                    $this->info["temperature_lsb"] = base_convert($this->bits[4], 2, 10);
                    $this->info["x_msb"] = base_convert($this->bits[5], 2, 10);
                    $this->info["x_lsb"] = base_convert($this->bits[6], 2, 10);
                    $this->info["y_msb"] = base_convert($this->bits[7], 2, 10);
                    $this->info["y_lsb"] = base_convert($this->bits[8], 2, 10);
                    $this->info["z_msb"] = base_convert($this->bits[9], 2, 10);
                    $this->info["z_lsb"] = base_convert($this->bits[10], 2, 10);
                    $this->info["battery_level"] = base_convert($this->bits[11], 2, 10);
                }
                elseif($this->version==2) {
                    $this->info["temperature"] = base_convert($this->bits[3], 2, 10);
                    $this->info["x_msb"] = base_convert($this->bits[4], 2, 10);
                    $this->info["x_lsb"] = base_convert($this->bits[5], 2, 10);
                    $this->info["y_msb"] = base_convert($this->bits[6], 2, 10);
                    $this->info["y_lsb"] = base_convert($this->bits[7], 2, 10);
                    $this->info["z_msb"] = base_convert($this->bits[8], 2, 10);
                    $this->info["z_lsb"] = base_convert($this->bits[9], 2, 10);
                    $this->info["battery_level"] = base_convert($this->bits[10], 2, 10);
                }
                break;

            case 4: //START FRAME 1
                $config = new Config_Lite('../../data/configuration.ini');
                $config->setString($this->info['device_id'], "SF1R", $this->rssi);
                $config->setString($this->info['device_id'], "SF1T", date('U'));
                $config->save();
                unset ($config);

                if($this->version==1) {
                    $this->info["temperature_msb"] = base_convert($this->bits[2], 2, 10);
                    $this->info["temperature_lsb"] = base_convert($this->bits[3], 2, 10);
                    $this->info["x_calibration_msb"] = base_convert($this->bits[4], 2, 10);
                    $this->info["x_calibration_lsb"] = base_convert($this->bits[5], 2, 10);
                    $this->info["y_calibration_msb"] = base_convert($this->bits[6], 2, 10);
                    $this->info["y_calibration_lsb"] = base_convert($this->bits[7], 2, 10);
                    $this->info["z_calibration_msb"] = base_convert($this->bits[8], 2, 10);
                    $this->info["z_calibration_lsb"] = base_convert($this->bits[9], 2, 10);
                    $this->info["battery_level_msb"] = base_convert($this->bits[10], 2, 10);
                    $this->info["battery_level_lsb"] = base_convert($this->bits[11], 2, 10);
                }
                elseif($this->version==2) {
                    $this->info["temperature"] = base_convert($this->bits[2], 2, 10);
                    $this->info["x_calibration_msb"] = base_convert($this->bits[3], 2, 10);
                    $this->info["x_calibration_lsb"] = base_convert($this->bits[4], 2, 10);
                    $this->info["y_calibration_msb"] = base_convert($this->bits[5], 2, 10);
                    $this->info["y_calibration_lsb"] = base_convert($this->bits[6], 2, 10);
                    $this->info["z_calibration_msb"] = base_convert($this->bits[7], 2, 10);
                    $this->info["z_calibration_lsb"] = base_convert($this->bits[8], 2, 10);
                    $this->info["battery_level_msb"] = base_convert($this->bits[9], 2, 10);
                    $this->info["battery_level_lsb"] = base_convert($this->bits[10], 2, 10);
                }

                $this->return .= str_pad(base_convert(date('y'), 10, 16), 2, "0", STR_PAD_LEFT);
                $this->return .= str_pad(base_convert(date('m'), 10, 16), 2, "0", STR_PAD_LEFT);
                $this->return .= str_pad(base_convert(date('d'), 10, 16), 2, "0", STR_PAD_LEFT);
                $this->return .= str_pad(base_convert(date('w'), 10, 16), 2, "0", STR_PAD_LEFT);
                $this->return .= str_pad(base_convert(date('H'), 10, 16), 2, "0", STR_PAD_LEFT);
                $this->return .= str_pad(base_convert(date('i'), 10, 16), 2, "0", STR_PAD_LEFT);
                $this->return .= "0000";
                break;

            case 5: //START FRAME 2
                $config = new Config_Lite('../../data/configuration.ini');
                $config->setString($this->info['device_id'], "SF2R", $this->rssi);
                $config->setString($this->info['device_id'], "SF2T", date('U'));
                $config->save();
                unset ($config);

                $this->info["CODE_ID"] = base_convert($this->bits[2], 2, 10);
                $this->info["NM_START"] = base_convert($this->bits[3], 2, 10);
                $this->info["NM_PERIOD"] = base_convert($this->bits[4], 2, 10);
                $this->info["NM_SLEEP_TIME"] = base_convert($this->bits[5], 2, 10);
                $this->info["NM_KEEP_ALIVE"] = base_convert($this->bits[6], 2, 10);
                $this->info["RADIO_MODE"] = base_convert($this->bits[7], 2, 10);
                $this->info["SLEEP_TIME"] = base_convert($this->bits[8], 2, 10);
                $this->info["KEEP_ALIVE"] = base_convert($this->bits[9], 2, 10);
                $this->info["THRESHOLD"] = base_convert($this->bits[10], 2, 10);
                break;

            case 6: //SERVICE FRAME
                $config = new Config_Lite('../../data/configuration.ini');

                $t = array();
                $r = array();
                for($i=1;$i<=10;$i++){
                    if($config->get($this->info['device_id'], "RF".$i."T", 0) != 0) {
                        $r[] = $config->get($this->info['device_id'], "RF" . $i . "R", 0);
                        $t[] = $config->get($this->info['device_id'], "RF" . $i . "T", 0);
                    }
                }
                for($i=1;$i<=2;$i++){
                    if($config->get($this->info['device_id'], "SF".$i."T", 0) != 0) {
                        $r[] = $config->get($this->info['device_id'], "SF" . $i . "R", 0);
                        $t[] = $config->get($this->info['device_id'], "SF" . $i . "T", 0);
                    }
                }

                $n =  date('U');
                $intime = true;
                if(count($t) == 0){
                    $intime = false;
                }
                else {
                    foreach ($t as $item) {
                        if ($item == 0 || $n - $item > 120) {
                            $intime = false;
                        }
                    }
                }

                if($intime){
                    $r[] = $this->rssi;
                    $this->rssi = $this->median($r);
                }

                for($i=1;$i<=10;$i++){
                    $config->remove($this->info['device_id'], "RF".$i."R");
                    $config->remove($this->info['device_id'], "RF".$i."T");
                }
                for($i=1;$i<=2;$i++){
                    $config->remove($this->info['device_id'], "SF".$i."R");
                    $config->remove($this->info['device_id'], "SF".$i."T");
                }
                $config->save();
                unset ($config);

                $this->return .= str_pad(base_convert($this->rssi, 10, 16), 2, "0", STR_PAD_LEFT);
                $this->return .= "00000000000000";
                break;

            case 7: //CONFIRM DOWNLINK - nothing to do.
                break;

            case 8: //RSSI FRAME
                $config = new Config_Lite('../../data/configuration.ini');
                for($i=1;$i<=10;$i++){
                    if($config->get($this->info['device_id'], "RF".$i."T", 0) == 0){
                        $index = $i;
                        break;
                    }
                }
                if($index<=10 && $index>0) {
                    $config->setString($this->info['device_id'], "RF" . $index . "R", $this->rssi);
                    $config->setString($this->info['device_id'], "RF" . $index . "T", date('U'));
                }
                $config->save();
                unset ($config);
                break;

            default:
                break;
        }
    }

    public function waiting_response()
    {
        if (strlen($this->return) > 0)
            return (true);
        else
            return (false);
    }

    public function get_response()
    {
        return (strtoupper($this->return));
    }

    public function get_data()
    {
        return ($this->info);
    }
}

?>
