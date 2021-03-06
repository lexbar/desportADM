function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
};

function submitEmail() {
    if(!isValidEmailAddress($('#signup_email').val())) {
        displayError('Introduce un email válido');
    } else {
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
}

function loadStep2() {
    $('#signup_url').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
    
        e.preventDefault();
        return false;
    });
    
    $('#step1').css('display','none');
    $('#step2').css('display','block').addClass('animated fadeInUp');
}

function fillSignupUrl(el) {
    $('#signup_url').val( normalize($(el).val()).replace(/[^a-zA-Z]/g,'').replace(/[^a-zA-Z]/g,'').toLowerCase() );
}

var normalize = (function() {
  var from = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç", 
      to   = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc",
      mapping = {};
 
  for(var i = 0, j = from.length; i < j; i++ )
      mapping[ from.charAt( i ) ] = to.charAt( i );
 
  return function( str ) {
      var ret = [];
      for( var i = 0, j = str.length; i < j; i++ ) {
          var c = str.charAt( i );
          if( mapping.hasOwnProperty( str.charAt( i ) ) )
              ret.push( mapping[ c ] );
          else
              ret.push( c );
      }      
      return ret.join( '' );
  }
 
})();

function submitStep2() {
    $('#step2').addClass('animated fadeOutUp');
    
    $.ajax( "/sitecreate/", {type:'post', data:$( "#step2").serialize()} )
    .done(function( data ) { 
        if(data.error){
            displayError(data.error);
            $('#step2').removeClass('animated fadeOutUp');
        } else {
            $('#step2').css('display','none');
            $('#step4').css('display','block').addClass('animated fadeInUp');
            
            //process1();
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
    $('#processBar').width('50%');
    
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
    $('#processBar').width('80%');
    
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