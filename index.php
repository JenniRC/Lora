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

require('includes/functions.php');
?>
<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            padding-top: 50px;
            padding-bottom: 20px;
        }
    </style>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="css/bootstrap-switch.min.css">
    <link rel="stylesheet" href="css/bootstrap-dialog.min.css">
    <link rel="stylesheet" href="css/main.css">

    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Libelium Callback Configurator</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
        </div>
        <!--/.navbar-collapse -->
    </div>
</nav>
<br><br>

<div class="container">

    <form class="my_form">
        <div class="row">
            <fieldset>
                <div class="col-md-12">
                    <legend>DEVICE</legend>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="DEVICE_ID">ID</label>
                        <div class="input-group">
                            <input name="DEVICE_ID" type="text" class="form-control" id="DEVICE_ID" placeholder="Device ID">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></button>

                                <ul id="devices" class="dropdown-menu dropdown-menu-right">
                                </ul>
                            </div><!-- /btn-group -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="DEVICE_NAME">Name</label>
                    <input name="DEVICE_NAME" type="text" class="form-control" id="DEVICE_NAME" placeholder="Name">
                </div>
            </fieldset>
        </div>

        <div class="row">
            <div class="col-md-6">
                <fieldset>
                    <legend>TIME SETTINGS</legend>
                    <div class="form-group">
                        <label for="SLEEP_TIME">Sleep time</label>
                        <?php
                        $a_data = array('1m', '2m', '3m', '4m', '5m');
                        echo create_select('SLEEP_TIME', $a_data, 1, 'form-control');
                        ?>
                    </div>

                    <div class="form-group">
                        <label for="KEEP_ALIVE">Keep-Alive</label>
                        <?php
                        $a_data = array('0h', '0.5h', '1h', '2h', '4h');
                        echo create_select('KEEP_ALIVE', $a_data, 9, 'form-control');
                        ?>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>NIGHT MODE</legend>

                    <div class="form-group">
                        <label for="NM_STATUS">Enabled</label><br>
                        <input type="checkbox" id="NM_STATUS" name="NM_STATUS" checked>
                    </div>

                    <div class="form-group">
                        <label for="NM_PERIOD">Duration</label>
                        <?php
                        $a_data = array('0h', '1h', '2h', '3h', '4h', '5h', '6h', '7h', '8h', '9h', '10h', '11h', '12h', '13h', '14h', '15h');
                        echo create_select('NM_PERIOD', $a_data, 8, 'form-control');
                        ?>
                        <input type="hidden" name="NM_PERIOD" value="0" disabled="disabled" />
                    </div>

                    <div class="form-group">
                        <label for="NM_START">Start hour</label>
                        <?php
                        $a_data = array('0h', '1h', '2h', '3h', '4h', '5h', '6h', '7h', '8h', '9h', '10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h');
                        echo create_select('NM_START', $a_data, 22, 'form-control');
                        ?>
                        <input type="hidden" name="NM_START" value="0" disabled="disabled" />
                    </div>

                    <div class="form-group">
                        <label for="NM_SLEEP_TIME">Sleep time</label>
                        <?php
                        $a_data = array('2m', '5m');
                        echo create_select('NM_SLEEP_TIME', $a_data, 2, 'form-control');
                        ?>
                        <input type="hidden" name="NM_SLEEP_TIME" value="0" disabled="disabled" />
                    </div>

                    <div class="form-group">
                        <label for="NM_KEEP_ALIVE">Keep-Alive</label>
                        <?php
                        $a_data = array('0h', '1h', '2h', '3h', '4h', '5h', '6h', '7h', '8h', '9h', '10h', '11h', '12h', '13h', '14h', '15h');
                        echo create_select('NM_KEEP_ALIVE', $a_data, 4, 'form-control');
                        ?>
                        <input type="hidden" name="NM_KEEP_ALIVE" value="0" disabled="disabled" />
                    </div>
                </fieldset>
            </div>
            <div class="col-md-6">
                <fieldset>
                    <legend>EXTRA CONFIGURATION</legend>

                    <div class="form-group">
                        <label for="THRESHOLD">Sensor threshold</label>
                        <?php
                        $a_data = array();
                        for ($x = 0; $x <= 255; $x++) {
                            $a_data[] = $x;
                        }
                        echo create_select('THRESHOLD', $a_data, 75, 'form-control');
                        ?>
                        <label class="form-text text-muted">Recommended
                            value:
                            75</label>
                    </div>

                    <input name="BAT_READING" type="hidden" value="0">

                    <div class="form-group">
                        <label for="ENABLE_DAILY_FRAME">Daily frame</label><br>
                        <input type="checkbox" id="ENABLE_DAILY_FRAME" name="ENABLE_DAILY_FRAME" checked>
                    </div>

                    <div class="form-group">
                        <label for="RESET_REQUEST">Reset request</label><br>
                        <input type="checkbox" id="RESET_REQUEST" name="RESET_REQUEST">
                    </div>

                    <div class="form-group">
                        <label for="CONFIG_ID">Configuration version</label>
                        <?php
                        $a_data = array();
                        for ($x = 1; $x <= 255; $x++) {
                            $a_data[$x] = $x;
                        }
                        echo create_select('CONFIG_ID', $a_data, 100, 'form-control');
                        ?>
                    </div>


                </fieldset>

                <fieldset>
                    <legend>RADIO MODE</legend>

                    <div class="form-group">
                        <label for="RADIO_MODE">Radio mode</label>
                        <?php
                        $a_data = array('Sigfox', 'LoRaWAN', 'Sigfox + LoRaWAN', 'Sigfox &#8594; LoRaWAN', 'LoRaWAN &#8594; Sigfox');
                        echo create_select('RADIO_MODE', $a_data, 0, 'form-control');
                        ?>
                    </div>

                    <div class="form-group">
                        <label for="LORAWAN_MODE">LoRaWAN join mode</label>
                        <?php
                        $a_data = array('ABP', 'OTAA');
                        echo create_select('LORAWAN_MODE', $a_data, 0, 'form-control');
                        ?>
                    </div>

                </fieldset>
            </div>
        </div>

<br>

    <div class="row">
        <div class="col-md-3">
            <button type="button" class="btn btn-primary" id="save">
                <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save configuration
            </button>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-info" id="default">
                <span class="glyphicon glyphicon-flash" aria-hidden="true"></span>&nbsp; Set as default configuration
            </button>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-danger" id="delete">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp; Delete configuration
            </button>
        </div>
    </div>
  </form>

    <hr>

    <footer>
        <p>&copy; Libelium Comunicaciones Distribuidas</p>
    </footer>

    <!-- Modal Start here-->
    <div class="modal fade bs-example-modal-sm" id="myPleaseWait" tabindex="-1"
         role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Saving configuration...</h4>
                </div>
                <div class="modal-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-info
                    progress-bar-striped active"
                             style="width: 100%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal ends Here -->

    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Modal title</h4>
                </div>
                <div class="modal-body">
                    <p>One fine body…</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


</div>
<!-- /container -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

<script src="js/vendor/bootstrap.min.js"></script>
<script src="js/vendor/bootstrap-switch.min.js"></script>
<script src="js/vendor/bootstrap-dialog.min.js"></script>

<script src="js/plugins.js"></script>
<script src="js/main.js"></script>

<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
<script>
    (function (b, o, i, l, e, r) {
        b.GoogleAnalyticsObject = l;
        b[l] || (b[l] =
            function () {
                (b[l].q = b[l].q || []).push(arguments)
            });
        b[l].l = +new Date;
        e = o.createElement(i);
        r = o.getElementsByTagName(i)[0];
        e.src = '//www.google-analytics.com/analytics.js';
        r.parentNode.insertBefore(e, r)
    }(window, document, 'script', 'ga'));
    ga('create', 'UA-XXXXX-X', 'auto');
    ga('send', 'pageview');
</script>
</body>
</html>
