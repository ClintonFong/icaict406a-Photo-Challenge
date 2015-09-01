// --------------------------------------
// Constants
//
// user types
// ----------
var UT_CHALLENGER   = 0;
var UT_ADMIN        = 9;

// challenge types
// ----------
var CT_DAILY    = 1;
var CT_WEEKLY   = 2;
var CT_MONTHLY  = 3;


//---------------------------------------------------------------------------------------------
// JQuery functions for entire site
//---------------------------------------------------------------------------------------------

//var usersName   = readCookie( "icaict406a_usersName" );
var idUser      = readCookie( "icaict406a_idUser" );

// variables for images - data returned from worker threads
var cameraImageFiles = ["images/camera1.png",
                        "images/camera2.png",
                        "images/camera3.png",
                        "images/camera4.png" ];


var backgroundImageFiles = ["images/background1.jpg",
                            "images/background2.jpg",
                            "images/background3.jpg",
                            "images/background4.jpg",
                            "images/background5.jpg",
                            "images/background6.jpg",
                            "images/background7.jpg",
                            "images/background8.jpg" ];

//var imgCameraObjs       = new Array();
//var imgBackgroundObjs   = new Array();

var nCameraImages                   = cameraImageFiles.length;
var thisCamera                      = 0;

var nBackgroundImages               = backgroundImageFiles.length;
var thisBackground                  = 0;
var isAfterFirstBackgroundImageCyle = false;




//var workerLoadImages = new Worker( 'js/workers/workerLoadImages.js' );

var toggleIsSignIn = true;


//jQuery(function($)
jQuery(document).ready( function($)
{
    // check if user is signed in
/*
    if( usersName )
    {
        $('#cntWelcomeUser').html( usersName );
        $('#cntSignIn span').html( "SIGN OUT" );
    }
*/

    $(".ajaxLoader").css( "display", "none" );
	
    $('.toggleSignInDisplay').click( function()
    {
        toggleIsSignIn = (!toggleIsSignIn);

        if( toggleIsSignIn )
        {
            $('#cntRegisterHref').css('display', 'block');
            $('#fldsetSignin').css('display', 'block');
            $('#cntSigninHref').css('display', 'none');
            $('#fldsetRegister').css('display', 'none' );
        }
        else
        {
            $('#cntSigninHref').css('display', 'block');
            $('#fldsetRegister').css('display', 'block');
            $('#cntRegisterHref').css('display', 'none');
            $('#fldsetSignin').css('display', 'none' );
        }

    });


    //-----------------------------------------------------------------------------------------
    // load background images - re-visit later - playing with javascript and css position: relative/absolute to only load the first image and others later...
    //
/*
    var tag = "<div id='cntBackgroundImages'>";
    for( iImage in backgroundImages ) // images defined in main.js
    {
        var nCount = parseInt( iImage, 10 ) + 1;
        tag +=  "<div id='backgroundImage" + nCount + "'>";
        tag +=  "<img src='images/background" + nCount + ".jpg'>";
        tag += "</div>";
    }
    tag += "</div>";
    $('body').append( tag );
*/

    //-----------------------------------------------------------------------------------------
//    loadCameraImages();
//    loadBackgroundImages();

	var cameraIntervalHandle = setInterval( function ()
	{
	    nextCameraImage();

	}, 3000); // setInterval


	var backgroundIntervalHandle = setInterval( function ()
	{
	    nextBackgroundImage();

	}, 10000); // setInterval




    //-----------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------
    // PJAX - changing pages
    //-----------------------------------------------------------------------------------------
    //
	$(document).pjax('.js-pjax',     '#pjaxCntMainContent');
	$(document).pjax('.how-to-play', '#pjaxCntMainContent');
	$(document).on('pjax:start', function ()    { $(this).addClass('loading');	    });
	$(document).on('pjax:end', function ()      { $(this).removeClass('loading');   });

	$('#cntSignIn.js-pjax').click(function ()
	{
        // check if signed in
        if( !idUser )  { location.href = "login.php";          }    // sign-in
        else           { signInServerModule.doAjaxSignOut();   }
	});

	$('#home.js-pjax').click(function()
	{
	    location.href = "index.php";
	});

    $('#myAccount.js-pjax').click( function() 
	{
	    location.href = "myAccount.php";
	});


	$('#contactUs.js-pjax').click(function()
	{
	    location.href = "contactUs.php";
	});

    //-----------------------------------------------------------------------------------------
	$('#adminPanel.js-pjax').click(function ()
	{
	    location.href = "admin.php";
	});

	$('#adminExit.js-pjax').click(function ()
	{
	    location.href = "index.php";
	});


    //-----------------------------------------------------------------------------------------
    // How to play menu items
	
    $('#photoADayChallenge').on('click', '.menuItem-how-to-play', function( event ) 
	{
	    location.href = "howToPlay_photoADay.php";
	});

	$('#photoAWeekendChallenge').on('click', '.menuItem-how-to-play', function( event ) 
	{
	    location.href = "howToPlay_photoAWeekend.php";
	});

	$('#photoAMonthChallenge').on('click', '.menuItem-how-to-play', function( event ) 
	{
	    location.href = "howToPlay_photoAMonth.php";

	});

	

    //-----------------------------------------------------------------------------------------
    // Tabs
    //
    $("#cntHowToPlay .tabs").tabs();


}); // jQuery

//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------

//--------------------------------------------
/*
function loadCameraImages()
{
    for( i=0; i <= cameraImageFiles.length; i++ )
    {
        imgCameraObjs[i]        = new Image();
        imgCameraObjs[i].src    = cameraImageFiles[i]; 
    }

} // loadBackgroundImages


//--------------------------------------------
function loadBackgroundImages()
{
    for( i=0; i <= backgroundImageFiles.length; i++ )
    {
        imgBackgroundObjs[i]        = new Image();
        imgBackgroundObjs[i].src    = backgroundImageFiles[i]; 
    }

} // loadBackgroundImages
*/

// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
// nextCameraImage
// --------------------------------------------------------------------------
function nextCameraImage()
{
    thisCamera++;
    if (thisCamera == nCameraImages) { thisCamera = 0; }
    $('#imgHeaderCamera').attr("src", cameraImageFiles[thisCamera] );

} // nextCameraImage


// --------------------------------------------------------------------------
// nextBackgroundImage
// --------------------------------------------------------------------------

function nextBackgroundImage()
{
    var lastBackground = thisBackground;
    thisBackground++;
    if (thisBackground == nBackgroundImages ) { thisBackground = 0; 
                                                isAfterFirstBackgroundImageCyle = true; } 
    $('body').css('background-image', 'url(' + backgroundImageFiles[ thisBackground ] + ')');

    // play camera click sound
    if( !isAfterFirstBackgroundImageCyle )
    {
        var cameraClickSound = document.getElementById( "cameraClick" );
		cameraClickSound.volume = 0.1;
        cameraClickSound.play();
    }
        
    //$('#backgroundImage' + thisBackground + ' img').addClass( 'background-image-active' );
    //$('#backgroundImage' + lastBackground + ' img').removeClass( 'background-image-active' );


} // nextBackgroundImage

