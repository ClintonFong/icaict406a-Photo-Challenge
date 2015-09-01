//---------------------------------------------------------------------------------------------
// Javascript library for login.php
//---------------------------------------------------------------------------------------------

var nFldsetSignInHeight;
var nFldsetRegisterHeight;

//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
// JQuery functions for login.php
//---------------------------------------------------------------------------------------------


jQuery( function($)
{
    nFldsetSignInHeight = parseInt($('#fldsetSignin').css('height'), 10);
    nFldsetRegisterHeight = parseInt($('#fldsetRegister').css('height'), 10);

    // check if timed out
    if( ( typeof( timedout ) != 'undefined' ) && timedout )
    {
        alert("Either your Administration Session has timed out, or you're an Unauthorised Administrator.\nPlease sign in to continue.");
    }


    //---------------------------------------------------------------------------------------------
    $('#signinPassword').keyup( function( event )
    {
        var code = event.keyCode || event.which;
        if(code == 13) { signInModule.doSignIn(); }  
    });

    //---------------------------------------------------------------------------------------------
    $('#btnSignin').click(function (event)
    {
        signInModule.doSignIn();

    }); // $('#btnSignin').click

    //---------------------------------------------------------------------------------------------
    $('#registerConfirmPassword').keyup( function( event )
    {
        var code = event.keyCode || event.which;
        if(code == 13) { signInModule.doRegister(); }  
    });

    //---------------------------------------------------------------------------------------------
    $('#btnRegister').click(function (event)
    {
        signInModule.doRegister();

    }); // $('#btnRegister').click


}); // $(document).ready(function()

// end JQuery functions
//---------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
// signInModule
//---------------------------------------------------------------------------------------------
    
var signInModule = (function()
{
    var errorHighlightColor = '#FFF8E2';
    var resetHighlightColor = '#ffffff';

    function signIn()
    {
        var nLines = 0;
        var strErrorMessage = '';

        $('#signinEmail').css('background-color', resetHighlightColor);
        $('#signinPassword').css('background-color', resetHighlightColor);

        if (document.forms['frmSignin'].email.value == '')
        {
            strErrorMessage = "*Sign-in Email is required";
            $('#signinEmail').css('background-color', errorHighlightColor);
            nLines++;
        }
        if (document.forms['frmSignin'].password.value == '')
        {
            strErrorMessage += (nLines >= 1) ? "<br>*Password is required" : "*Password is required";
            $('#signinPassword').css('background-color', errorHighlightColor);
            nLines++;
        }

        // decide what to do
        //
        if( nLines ) { writeSignInError( nLines, strErrorMessage ); }
        else         { signInServerModule.doAjaxSignIn();           }

    } // signIn

    //---------------------------------------------------------------------------------------------
    function register()
    {
        var nLines = 0;
        var strErrorMessage = '';

        // reset all the background colors
        $('#registerFirstname').css('background-color', resetHighlightColor);
        $('#registerLastname').css('background-color', resetHighlightColor);
        $('#registerEmail').css('background-color', resetHighlightColor);
        $('#registerMobilePhone').css('background-color', resetHighlightColor);
        $('#registerPassword').css('background-color', resetHighlightColor);
        $('#registerConfirmPassword').css('background-color', resetHighlightColor);


        // check values and mark the ones that needs fixing
        //
        if (document.forms['frmRegister'].firstname.value == '')
        {
            strErrorMessage = "*First name is required";
            $('#registerFirstname').css('background-color', errorHighlightColor);
            nLines++;
        }

        if (document.forms['frmRegister'].lastname.value == '')
        {
            strErrorMessage += (nLines >= 1) ? "<br>" : "";
            strErrorMessage += "*Last name is required";
            $('#registerLastname').css('background-color', errorHighlightColor);
            nLines++;
        }

        if (document.forms['frmRegister'].email.value == '')
        {
            strErrorMessage += (nLines >= 1) ? "<br>" : "";
            strErrorMessage += "*Signin Email is required";
            $('#registerSigninEmail').css('background-color', errorHighlightColor);
            nLines++;
        }

        /* - phone not mandatory
                
                if (document.forms['frmRegister'].primaryPhone.value == '')
                {
                    strErrorMessage += (nLines >= 1) ? "<br>" : "";
                    strErrorMessage += "*Primary Phone number is required";
                    $('#registerPrimaryPhone').css('background-color', errorHighlightColor);
                    nLines++;
                }
        */
        if (document.forms['frmRegister'].password.value == '')
        {
            strErrorMessage += (nLines >= 1) ? "<br>" : "";
            strErrorMessage += "*Password is required";
            $('#registerPassword').css('background-color', errorHighlightColor);
            nLines++;
        }

        if (document.forms['frmRegister'].password.value != document.forms['frmRegister'].confirmPassword.value)
        {
            strErrorMessage += (nLines >= 1) ? "<br>" : "";
            strErrorMessage += '*Passwords do not match';
            $('#registerConfirmPassword').css('background-color', errorHighlightColor);
            nLines++;
        }

        // decide what to do
        //
        if( nLines )    { writeRegisterError( nLines, strErrorMessage );    }
        else            { signInServerModule.doAjaxRegister();              }
    }

    //---------------------------------------------------------------------------------------------
    return {
        doSignIn    :   signIn,
        doRegister  :   register
    }


}()) // signInModule


//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
// signInServerModule
//---------------------------------------------------------------------------------------------
var signInServerModule = (function()
{
    //---------------------------------------------------------------------------------------------
    // AJAX function calls
    //---------------------------------------------------------------------------------------------

    $.ajaxSetup(
    {
        cache: false
    });


    //---------------------------------------------------------------------------------------------
    function doAjaxSignIn()
    {

        $(".ajaxLoader").css("display", "block");

        var url = "ajaxScripts/ajaxLoginController.php";
        var sendData = 'action=sign-in' +
                        "&email="       + $('#signinEmail').val()   +
                        "&password="    + $('#signinPassword').val();

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnWriteSignInError(data);     }
                                else                                                { ajaxReturnWriteSignInSuccess(data);   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxSignIn

    //---------------------------------------------------------------------------------------------
    function doAjaxRegister()
    {

        $(".ajaxLoader").css("display", "block");

        var url = "ajaxScripts/ajaxUserController.php";
        var sendData = "action=register"        +
                                "&firstname="   + $('#registerFirstname').val()     +
                                "&lastname="    + $('#registerLastname').val()      +
                                "&email="       + $('#registerEmail').val()         +
                                "&phone="       + $('#registerPrimaryPhone').val()  +
                                "&userType="    + UT_CHALLENGER                     +
                                "&password="    + $('#registerPassword').val();

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data )
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnWriteRegisterError(data);   }
                                else                                                { ajaxReturnWriteRegisterSuccess(data); }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxRegister

    //---------------------------------------------------------------------------------------------
    function doAjaxSignOut()
    {
        //alert("doAjaxSignOut");

        $(".ajaxLoader").css("display", "block");

        var url = "ajaxScripts/ajaxLoginController.php";
        var sendData = 'action=signed-out'  +
                        "&idUser="          + $('#idUser').val();

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnWriteSignOutError(data);     }
                                else                                                { ajaxReturnWriteSignOutSuccess(data);   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });


    } // doAjaxSignOut


    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers
    //---------------------------------------------------------------------------------------------

    function ajaxReturnWriteSignInError(data)
    {
        writeSignInError( 1, stripMessageFromAjaxData(data) );

    } // ajaxReturnWriteSignInError

    //---------------------------------------------------------------------------------------------
    function ajaxReturnWriteSignInSuccess(data)
    {
        writeSignInSuccess( 1, stripMessageFromAjaxData(data) );
        writeSignedInUser( JSON.parse( stripDataFromAjaxData( data ) ) );

    } // ajaxReturnWriteSignInError

    //---------------------------------------------------------------------------------------------
    //---------------------------------------------------------------------------------------------
    function ajaxReturnWriteRegisterError(data)
    {
        writeRegisterError( 1, stripMessageFromAjaxData(data) );
        
    } // ajaxReturnWriteSignInError

    //---------------------------------------------------------------------------------------------
    function ajaxReturnWriteRegisterSuccess(data)
    {
        writeRegisterSuccess( 1, stripMessageFromAjaxData(data) );
        writeSignedInUser( JSON.parse( stripDataFromAjaxData( data ) ) );

    } // ajaxReturnWriteRegisterSuccess

    //---------------------------------------------------------------------------------------------
    //---------------------------------------------------------------------------------------------
    function ajaxReturnWriteSignOutError(data)
    {
        alert("Error Signing out");

    } // ajaxReturnWriteSignInError

    //---------------------------------------------------------------------------------------------
    function ajaxReturnWriteSignOutSuccess(data)
    {
        eraseCookie( "icaict406a_idUser" );
        eraseCookie( "icaict406a_userType"  );
        eraseCookie( "icaict406a_usersName" );
	    location.href = "index.php";

    } // ajaxReturnWriteSignInError

    //---------------------------------------------------------------------------------------------
    return {
        doAjaxSignIn    :   doAjaxSignIn,
        doAjaxRegister  :   doAjaxRegister,
        doAjaxSignOut   :   doAjaxSignOut
    }

}()); // signInServerModule

//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
function writeSignInError( nLines, 
                           msg )
{
    // set height of message area
    var nHeight = parseInt( $('#cntSignInMessage label.errorMessage').css('line-height'), 10) * nLines;
    $('#cntSignInMessage').css("height", nHeight + "px");

    // adjust height of fieldset
    var nMargin = parseInt($('#cntSignInMessage').css('margin-top'), 10);
    var nFldsetHeight = nFldsetSignInHeight + nMargin + nHeight;
    $('#fldsetSignin').css("height", nFldsetHeight + "px");

    // displayRegisterError
    //
    $('#cntSignInMessage').removeClass("messageBackgroundSuccess");
    $('#cntSignInMessage').addClass("messageBackgroundError");
    $('#cntSignInMessage label.errorMessage').html("");
    $('#cntSignInMessage label.successMessage').html( msg );
    $('#cntSignInMessage').css("display", "block");

} // writeSignInError

//---------------------------------------------------------------------------------------------
function writeSignInSuccess( nLines, 
                             msg )
{
    // set height of message area
    var nHeight = parseInt($('#cntSignInMessage label.successMessage').css('line-height'), 10) * nLines;
    $('#cntSignInMessage').css("height", nHeight + "px");

    // adjust height of fieldset
    var nMargin = parseInt($('#cntSignInMessage').css('margin-top'), 10);
    var nFldsetHeight = nFldsetSignInHeight + nMargin + nHeight;
    $('#fldsetSignin').css("height", nFldsetHeight + "px");

    // displayRegisterError
    //
    $('#cntSignInMessage').removeClass("messageBackgroundError");
    $('#cntSignInMessage').addClass("messageBackgroundSuccess");
    $('#cntSignInMessage label.errorMessage').html("");
    $('#cntSignInMessage label.successMessage').html( msg );
    $('#cntSignInMessage').css("display", "block");

} // writeSignInSuccess

//---------------------------------------------------------------------------------------------
function writeRegisterError( nLines, 
                             msg )
{
    // set height of message area
    var nHeight = parseInt($('#cntRegisterMessage label.errorMessage').css('line-height'), 10) * nLines;
    $('#cntRegisterMessage').css("height", nHeight + "px");

    // adjust height of fieldset
    var nMargin = parseInt($('#cntRegisterMessage').css('margin-top'), 10);
    var nFldsetHeight = nFldsetRegisterHeight + nMargin + nHeight;
    $('#fldsetRegister').css("height", nFldsetHeight + "px");

    // displaySignInError
    //
    $('#cntRegisterMessage').removeClass("messageBackgroundSuccess");
    $('#cntRegisterMessage').addClass("messageBackgroundError");
    $('#cntRegisterMessage label.errorMessage').html( msg );
    $('#cntRegisterMessage label.successMessage').html("");
    $('#cntRegisterMessage').css("display", "block");

} // writeRegisterError

//---------------------------------------------------------------------------------------------
function writeRegisterSuccess( nLines, 
                               msg )
{
    // set height of message area
    var nHeight = parseInt($('#cntRegisterMessage label.successMessage').css('line-height'), 10) * nLines;
    $('#cntRegisterMessage').css("height", nHeight + "px");

    // adjust height of fieldset
    var nMargin = parseInt($('#cntRegisterMessage').css('margin-top'), 10);
    var nFldsetHeight = nFldsetRegisterHeight + nMargin + nHeight;
    $('#fldsetRegister').css("height", nFldsetHeight + "px");

    // displayRegisterError
    //
    $('#cntRegisterMessage').removeClass("messageBackgroundError");
    $('#cntRegisterMessage').addClass("messageBackgroundSuccess");
    $('#cntRegisterMessage label.errorMessage').html("");
    $('#cntRegisterMessage label.successMessage').html(msg);
    $('#cntRegisterMessage').css("display", "block");

} // writeRegisterSuccess

//---------------------------------------------------------------------------------------------
function writeSignedInUser( objData )
{
    // change display values
    //
    var usersName = objData.firstname + ' ' + objData.lastname;
    $('#cntWelcomeUser').html( usersName );
    $('#cntSignIn span').html( "SIGN OUT" );

    // set cookie
    //
//    var un = readCookie( "icaict406a_usersName" );
    createCookie( "icaict406a_idUser", objData.idUser );
    createCookie( "icaict406a_userType", objData.userType  );
    createCookie( "icaict406a_usersName", usersName );
    //$.cookie( "name", name );
    //$.cookie( "userType", objData.userType );

    $('#home.js-pjax').click();
    //$.pjax({ url: "index.php", container: "#pjaxCntMainContent" }); // redirect to home page

} // writeWelcomeUser


//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------

function doForgotPassword()
{
    //alert('doForgetPassword');

    var email = prompt('Please enter your Sign-In Email to Reset your Password');

    if (email != null)
    {
        document.forms['frmForgotPassword'].email.value = email;
        document.forms['frmForgotPassword'].submit();
    }

} // doForgotPassword