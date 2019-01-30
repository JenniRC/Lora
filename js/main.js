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

$('document').ready(function(){
    var API_SERVICE = "";

    $.getJSON('includes/get_api.php', function (resp) {
        if (resp.status == "OK") {
            API_SERVICE = resp.data;
        }
        else {
            process_alert(resp.data, 'error');
        }
    });

    //Form
    //Turn form checkboxes into toggle buttons
    $(".my_form [type='checkbox']").bootstrapSwitch({onColor: 'success'});

    //Disabled 0 in sensor threshold
    $("select[name='THRESHOLD'] option:eq(0)").attr("disabled", "disabled");


    //Load devices
    fill_devices();

    //Night mode logic
    $("#nm_enable").on('switchChange.bootstrapSwitch', function(event, state) {
        if(state == false){
            $('#nm_period').prop( "disabled", true);
            $('#nm_start').prop( "disabled", true);
            $('#nm_sleep_time').prop( "disabled", true);
            $('#nm_keep_alive').prop( "disabled", true);
        }else{
            $('#nm_period').prop( "disabled", false);
            $('#nm_start').prop( "disabled", false);
            $('#nm_sleep_time').prop( "disabled", false);
            $('#nm_keep_alive').prop( "disabled", false);
        }
    });

    //Radio mode logic
    $('#radio_mode').change(function(){
        if($('#radio_mode').val() == 0){
            $('#lorawan_mode').prop("disabled", true);
        }else{
            $('#lorawan_mode').prop("disabled", false);
        }
    });
    $('#radio_mode').change();

    $('#devices').on('click', 'a', function(){
        var id = $(this).data('val');
        try{
            $.ajax({
                url: "includes/get_configuration.php",
                data: "id=" + id,
                type: 'POST',
                beforeSend: function(){
                    modal_message("Loading configuration...");
                },
                success: function (resp) {
                    if (resp.status == "OK") {
                        fill_form(resp.data);
                    }
                    else {
                        process_alert(resp.data, 'error');
                    }
                    $('#DEVICE_ID').val(id);
                },
                error:  function (jqXHR, textStatus, error)  {
                }
            }).done(function() {
                fill_devices();
            });
        }
        catch(err) {
            process_alert(err, 'error');
        }
        finally{
            setTimeout(function() {
                modal_message_close();
            }, 500);
        }
    });

    function fill_devices(){
        $.ajax({
            url: "includes/get_devices.php",
            data: "",
            type: 'POST',
            success: function (resp) {
                if (resp.status == "OK") {
                    $('#devices').empty();
                    $.each(resp.data, function(key, value){
                        if(value.default=="1")
                            $('#devices').append('<li><a class="" href="#" data-val="'+key+'"><span class="label label-default">Default</span> &nbsp; ' + key + " - " + value.name + '</a></li>');
                        else
                            $('#devices').append('<li><a href="#" data-val="'+key+'">' + key + " - " + value.name + '</a></li>');
                    });
                }
                else {
                    process_alert(resp.data, 'error');
                }
            },
            error:  function (jqXHR, textStatus, error)  {
                process_alert(textStatus, 'error');
            }
        });
    }

    function fill_form(info){
        $.each(info, function(key, value){
            if($('#'+key).is('select')) {
                $('#' + key + ' option[value="' + value + '"]').prop('selected', true);
            }
            else if($('#'+key).is(":text")){
                $('#' + key).val(value);
            }
            else if($('#'+key).is(":checkbox")) {
                if(value==0) {
                    $('#' + key).attr('checked', false);
                    $('#' + key).bootstrapSwitch('state', false);
                }
                else {
                    $('#' + key).attr('checked', true);
                    $('#' + key).bootstrapSwitch('state', true);
                }
            }
        });
    }

    function reset_form(){
        $('#DEVICE_ID').val('');
        $('#DEVICE_NAME').val('');
    }

    function process_alert(message, type){
        var title = "";

        switch (type){
            case 'warning':
                type = BootstrapDialog.TYPE_WARNING;
                title = "Warning";
                break;
            case 'error':
                type = BootstrapDialog.TYPE_DANGER;
                title = "Error";
                break;
            case 'info':
                type = BootstrapDialog.TYPE_INFO;
                title = "Info";
                break;
            case 'success':
                type = BootstrapDialog.TYPE_SUCCESS;
                title = "OK";
                break;
            default:
                type = BootstrapDialog.TYPE_DEFAULT;
                title = "Message";
                break;
        }

        BootstrapDialog.show({
            type: type,
            title: title,
            message: message,
            buttons: [{
                label: 'OK',
                action: function(dialogRef){
                    dialogRef.close();
                }
            }]
        });
    }

    function modal_message(message){
        $('#myPleaseWait h4').html(message);
        $('#myPleaseWait').modal('show');
    }
    function modal_message_close(){
        $('#myPleaseWait').modal('hide');
    }

    //Change values when Night Mode is changed
    $('#NM_STATUS').on('switchChange.bootstrapSwitch', function(event, state) {
        if($('#NM_STATUS').is(':checked')){
            $('select[name=NM_PERIOD]').prop('disabled', false);
            $('select[name=NM_START]').prop('disabled', false);
            $('select[name=NM_SLEEP_TIME]').prop('disabled', false);
            $('select[name=NM_KEEP_ALIVE]').prop('disabled', false);

            $('input[name=NM_PERIOD]').prop('disabled', 'disabled');
            $('input[name=NM_START]').prop('disabled', 'disabled');
            $('input[name=NM_SLEEP_TIME]').prop('disabled', 'disabled');
            $('input[name=NM_KEEP_ALIVE]').prop('disabled', 'disabled');
        }
        else{
            $('input[name=NM_START]').val($('select[name=NM_START]').val());
            $('input[name=NM_SLEEP_TIME]').val($('select[name=NM_SLEEP_TIME]').val());
            $('input[name=NM_KEEP_ALIVE]').val($('select[name=NM_KEEP_ALIVE]').val());

            $('select[name=NM_PERIOD]').prop('disabled', 'disabled');
            $('select[name=NM_START]').prop('disabled', 'disabled');
            $('select[name=NM_SLEEP_TIME]').prop('disabled', 'disabled');
            $('select[name=NM_KEEP_ALIVE]').prop('disabled', 'disabled');

            $('input[name=NM_PERIOD]').prop('disabled', false);
            $('input[name=NM_START]').prop('disabled', false);
            $('input[name=NM_SLEEP_TIME]').prop('disabled', false);
            $('input[name=NM_KEEP_ALIVE]').prop('disabled', false);
        }
    });

    //Save Form
    $("#save").click(function(){
        var device = $('#DEVICE_ID').val();
        var status_1 = false;
        var status_2 = false;
        var status_3 = false;
        var response = "";
        var config = $('.my_form').serialize();

        try {
            $.ajax({
                url: 'includes/get_libelium_config.php',
                data: "OP_MODE=CONF&NODE_TYPE=PARKING&" + $('.my_form').serialize(),
                type: 'POST',
                beforeSend: function (xhr){
                    modal_message("Saving configuration...");
                },
                success: function (resp) {
                    if (resp.status == "OK") {
                        status_1 = true
                        response = resp.data;
                    }
                    else {
                        process_alert(resp.data, 'error');
                    }
                },
                error:  function (jqXHR, textStatus, error)  {
                }
            })
                .done(function() {
                    if(status_1){
                        $.ajax({
                            url: "includes/save_response.php",
                            data: "device="+device+"&response="+response,
                            type: 'POST',
                            success: function (resp){
                                if (resp.status == "OK")
                                    status_2 = true;
                                else
                                    process_alert(resp.data,'error');
                            },
                            error:  function (data, textStatus, jqXHR)  {
                                process_alert(textStatus, 'error');
                            }
                        })
                        .done(function() {
                            if(status_2){
                                $.ajax({
                                    url: "includes/save_configuration.php",
                                    data: config,
                                    type: 'POST',
                                    success: function (resp){
                                        if(resp.status=="OK")
                                            status_3 = true;
                                        else
                                            process_alert(resp.data, 'error');
                                    },
                                    error:  function (data, textStatus, jqXHR)  {
                                        process_alert(textStatus, 'error');
                                    }
                                })
                                    .done(function() {
                                        fill_devices();
                                    });
                            }
                        });
                    }
                });
        }
        catch(err) {
            process_alert(err, 'error');
        }
        finally{
            setTimeout(function() {
                modal_message_close();
            }, 500);
        }
    });


    $("#delete").click(function(){
        var device = $('#DEVICE_ID').val();
        try {
            $.ajax({
                url: "includes/delete_configuration.php",
                data: "device="+device,
                type: 'POST',
                beforeSend: function(){
                    modal_message("Deleting configuration...");
                },
                success: function (resp) {
                    if (resp.status == "OK") {
                        response = resp.data;
                    }
                    else {
                        process_alert(resp.data, 'error');
                    }
                },
                error:  function (jqXHR, textStatus, error)  {
                }
            })
            .done(function() {
                fill_devices();
                reset_form();
            });
        }
        catch(err) {
            process_alert(err, 'error');
        }
        finally{
            setTimeout(function() {
                modal_message_close();
            }, 500);
        }
    });


    $("#default").click(function(){
        try {
            if ($('#DEVICE_ID').val().indexOf(',') !== -1) {
                process_alert('You can only set one device as default.', 'warning');
            }
            else {
                var device = $('#DEVICE_ID').val();
                $.ajax({
                    url: "includes/set_default.php",
                    data: "device=" + device,
                    type: 'POST',
                    beforeSend: function () {
                        modal_message("Setting as default...");
                    },
                    success: function (resp) {
                        if (resp.status == "OK") {
                            response = resp.data;
                        }
                        else {
                            process_alert(resp.data, 'error');
                        }
                    },
                    error: function (jqXHR, textStatus, error) {
                    }
                })
                    .done(function () {
                        fill_devices();
                    });
            }
        }
        catch(err) {
            process_alert(err, 'error');
        }
        finally{
            setTimeout(function() {
                modal_message_close();
            }, 500);
        }
    });

});
