//---------------------------------------------------------------------------------------------
// JQuery functions for forgot password popup
//---------------------------------------------------------------------------------------------

$(document).ready( function() 
{
    //---------------------------------------------------------------------------------------------
	$('#aForgotPassword').click(function() 
    {
		//alert("aForgotPassword");

		// Getting the variable's value from a link 
		var forgotPasswordBox = $(this).attr('href');

		//Fade in the Popup and add close button
		$(forgotPasswordBox).fadeIn(300);
		
		//Set the center alignment padding + border
		var popMargTop = ($(forgotPasswordBox).height() + 24) / 2; 
		var popMargLeft = ($(forgotPasswordBox).width() + 24) / 2; 
		
		$(forgotPasswordBox).css(
        { 
			'margin-top' : -popMargTop,
			'margin-left' : -popMargLeft
		});
		
		// Add the mask to body
		$('body').append("<div id='mask'></div>");
		$('#mask').fadeIn(300);
		
        // reset the ajaxMessageArea
        $('#ajaxForgotPasswordMessageResponse').html('');

		return false;
	});
	
    //---------------------------------------------------------------------------------------------
    $('a.close').click( function()   // When clicking on the button close (the cross at the top right corner)
    { 
	    $('.forgotPasswordPopup').fadeOut(300 , function() 
        {
		    $('#mask').remove();  
	    }); 
	    return false;
    });

    //---------------------------------------------------------------------------------------------
    $('#btnSend').click( function()   
    { 
        if( $('#forgotPasswordSigninEmail').val() == '' ) 
        { 
            alert('Please enter your Sign-in Email for a new password to be emailed to you');
            $('#forgotPasswordSigninEmail').css('background-color', '#FFF8E2');
        }
        else
        {
            doAjaxForgotPassword();
        }
    });
});

//---------------------------------------------------------------------------------------------
// AJAX function calls
//---------------------------------------------------------------------------------------------

$.ajaxSetup(
{
    cache: false
});


//---------------------------------------------------------------------------------------------
function doAjaxForgotPassword()
{
//	alert('doAjaxForgotPassword');
	
	document.getElementById('ajaxForgotPasswordMessageResponse').innerHTML = 'Resetting password...please wait';
    
	var dataSend = 	'action=forgot-password' +
					'&email='                + document.forms['frmForgotPassword'].signinEmail.value; 
    
    //alert(dataSend );
	$.ajax({
			'type'		:	'POST',
			'url'		: 	'ajaxScripts/ajaxForgotPassword.php',
			'data'		:	dataSend,
			'success'	:	function(data) 
                            {
                                //alert('successful');
                                //alert(data);
                                //$('#ajaxForgotPasswordMessageResponse').html( data );
                                
                                if( stripStatusFromAjaxData( data ) == 'Success' )
                                {
					                $('#ajaxForgotPasswordMessageResponse').html( stripMessageFromAjaxData(data) );
                                }
                                else
                                {
                                   // do need to display each unsuccessful step.  $('#ajaxForgotPasswordMessageResponse').html( stripDataFromAjaxData(data) );
					                $('#ajaxForgotPasswordMessageResponse').html( 'Problems encountered resetting your password. <br>Please contact us to resolve this issue.' );
                                }                                
				            }
		   });

} // doAjaxForgotPassword


