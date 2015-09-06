function submitEmail() {
    $('#step1').addClass('animated fadeOutUp');
    
    $.ajax( "/signup/", {type:'post', data:$( "#step1").serialize()} )
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
    
    $.ajax( "/sitecreate/", {type:'post', data:$( "#step2").serialize()} )
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
    
    $('#processBar').width('4%');
    
    $.ajax( "/sitecreate/0", {} )
    .done(function( data ) { 
        if(data.error){
            displayError(data.error);
            $('#processBar').removeClass('active');
        } else {
            $('#processBar').addClass('active');
            process2();
        }
    })
    .fail(function(){
        if($('#processBar').hasClass('active')) {
            $('#processBar').removeClass('active');
            process1();
        } else {
            displayError('Error durante el proceso.')
        }
    });
}
function process2() {
    $('#processBar').width('15%');
    
    $.ajax( "/sitecreate/1", {} )
    .done(function( data ) { 
        if(data.error){
            displayError(data.error);
            $('#processBar').removeClass('active');
        } else {
            $('#processBar').addClass('active');
            process3();
        }
    })
    .fail(function(){
        if($('#processBar').hasClass('active')) {
            $('#processBar').removeClass('active');
            process2();
        } else {
            displayError('Error durante el proceso.')
        }
    });
}
function process3() {
    $('#processBar').width('30%');
    
    $.ajax( "/sitecreate/2", {} )
    .done(function( data ) { 
        if(data.error){
            displayError(data.error);
            $('#processBar').removeClass('active');
        } else {
            $('#processBar').addClass('active');
            process4();
        }
    })
    .fail(function(){
        if($('#processBar').hasClass('active')) {
            $('#processBar').removeClass('active');
            process3();
        } else {
            displayError('Error durante el proceso.')
        }
    });
}
function process4() {
    $('#processBar').width('70%');
    
    $.ajax( "/sitecreate/3", {} )
    .done(function( data ) { 
        if(data.error){
            displayError(data.error);
            $('#processBar').removeClass('active');
        } else {
            processend();
        }
    })
    .fail(function(){
        if($('#processBar').hasClass('active')) {
            $('#processBar').removeClass('active');
            process4();
        } else {
            displayError('Error durante el proceso.')
        }
    });
}
function processend() {
    $('#processBar').width('100%');
    
    $('#step3').css('display','none');
    $('#step4').css('display','block');
}

function displayError(error_code) {
    $('.fa-spinner.fa-pulse').removeClass('fa-pulse');
    switch(error_code) {
        case 'empty_email': alert('Debes introducir un email'); break;
        case 'empty_fields': alert('Debes rellenar todos los campos'); break;
        case 'name_not_available': alert('Esa dirección web no está disponible. Prueba con otra.'); break;
        case 'no_client': alert('Error. No has sido registrado correctamente.'); break;
        case 'bad_request': alert('Error interno. Por favor vuelve a empezar o ponte en contacto con nosotros.'); break;
        default: alert(error_code);
    }
}