function submitEmail() {
    $('#step1').addClass('animated fadeOutUp');
    
    $.ajax( "{{ path('desport_signup') }}", {type:'post', data:$( "#step1").serialize()} )
    .done(function( data ) { 
        if(data.error){
            displayError(data.error);
            $('#step1').removeClass('animated fadeOutUp');
        } else {
            $('#client_code').val(data.client_code);
            loadStep2();
        }
    });
}

function loadStep2() {
    $('#step1').css('display','none');
    $('#step2').css('display','block').addClass('animated fadeInUp');
}

function submitStep2() {
    $('#step2').addClass('animated fadeOutUp');
    
    $.ajax( "{{ path('desport_sitecreate') }}", {type:'post', data:$( "#step2").serialize()} )
    .done(function( data ) { 
        if(data.error){
            displayError(data.error);
            $('#step2').removeClass('animated fadeOutUp');
        } else {
            process1();
        }
    });
}

function process1() {
    $('#step2').css('display','none');
    $('#step3').css('display','block').addClass('animated fadeInUp');
    
    $.ajax( "{{ path('desport_sitecreate_stage',{stage_id:0}) }}", {} )
    .done(function( data ) { 
        if(data.error){
            displayError(data.error);
            $('#processBar').removeClass('active');
        } else {
            process2();
        }
    });
}
function process2() {
    $('#processBar').width('10%');
    
    $.ajax( "{{ path('desport_sitecreate_stage',{stage_id:1}) }}", {} )
    .done(function( data ) { 
        if(data.error){
            displayError(data.error);
            $('#processBar').removeClass('active');
        } else {
            process3();
        }
    });
}
function process3() {
    $('#processBar').width('20%');
    
    $.ajax( "{{ path('desport_sitecreate_stage',{stage_id:2}) }}", {} )
    .done(function( data ) { 
        if(data.error){
            displayError(data.error);
            $('#processBar').removeClass('active');
        } else {
            process4();
        }
    });
}
function process4() {
    $('#processBar').width('70%');
    
    $.ajax( "{{ path('desport_sitecreate_stage',{stage_id:3}) }}", {} )
    .done(function( data ) { 
        if(data.error){
            displayError(data.error);
            $('#processBar').removeClass('active');
        } else {
            processend();
        }
    });
}
function processend() {
    $('#processBar').width('100%');
    
    $('#step3').css('display','none');
    $('#step4').css('display','block');
}

function displayError(error_code) {

    switch(error_code) {
        case 'empty_email': alert('Debes introducir un email'); break;
        case 'empty_fields': alert('Debes rellenar todos los campos'); break;
        case 'name_not_available': alert('Esa dirección web no está disponible. Prueba con otra.'); break;
        case 'no_client': alert('Error. No has sido registrado correctamente.'); break;
        case 'bad_request': alert('Error interno. Por favor vuelve a empezar o ponte en contacto con nosotros.'); break;
        default: alert(error_code);
    }
}