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
 *  Implementation:    Jose Luis Berrocal, Diego Becerrica
 */


function create_select($id_name, $a_data, $selected = '', $msg_class = '')
{
    $msg_out = '<select id="' . $id_name . '" name="' . $id_name . '" class="' . $msg_class . '">';
    foreach ($a_data as $key => $value)
    {
        $msg_selected = '';
        if ($key == $selected)
        {
            $msg_selected = 'selected';
        }
        $msg_out .= '<option value="' . $key . '" ' . $msg_selected . '>' . $value . '</option>';
    }
    $msg_out .= '</select>';
    return $msg_out;
}

function getURL($url, $method="GET", $data= array(), $header= array()) {
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "UTF-8",       // handle all encodings
        CURLOPT_USERAGENT      => "smartparking-customer-server", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 60,      // timeout on connect
        CURLOPT_TIMEOUT        => 60,      // timeout on response
        CURLOPT_MAXREDIRS      => 3,       // stop after 10 redirects
    );

    if(sizeof($header)>0){
        //$options[CURLOPT_HEADER] = true;    // Set header true
        $options[CURLOPT_HTTPHEADER] = $header;    // Header information
    }

    if(strpos("https://", $url)==0){
        $options[CURLOPT_SSL_VERIFYPEER] = false;    // Disabled SSL Cert checks
        $options[CURLOPT_SSL_VERIFYHOST] = false;    // Disable
    }

    $options[CURLOPT_CUSTOMREQUEST] = $method;
    $options[CURLOPT_POSTFIELDS] = http_build_query($data);

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );

    $curl = array(
        "errno"     =>  curl_errno($ch),
        "errmsg"    =>  curl_error($ch),
        "content"   =>  curl_exec($ch),
        "header"    =>  curl_getinfo($ch)
    );
    curl_close($ch);

    if($curl['errno']==0 && $curl['header']['http_code']==200){
        $res = json_decode($curl['content']);
    }
    elseif($curl['header']['http_code']!=200){
        $json = json_decode($curl['content'], true);
        $res = array('status'=>"NOK", "data"=>$json['error']);
    }
    else{
        $res = array('status'=>"NOK", "data"=>$curl['errmsg']);
    }

    return $res;
}

?>