//---------------------------------------------------------------------------------------------
// JQuery functions for entire site
//---------------------------------------------------------------------------------------------

var dateToday   = new Date();
var thisYear    = dateToday.getFullYear();

var months = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
var weeksOfMonth = [ "1", "2", "3", "4" ];


//jQuery(function($){
jQuery(document).ready(function ($)
{
    if( $('#cntSlides').length ) { setupSlides(); } 

    //loadMenuModule.loadAll();   // loading the menus - has been moved over to php to construct...

    // --------------------------------------------------------------------------
    $('#photoADayChallenge').on('click', 'li.days-in-month ul li', function( event ) 
	{
        var day         = $(this).html().replace(/\D/g,'');
        var month       = $(this).parent().parent().attr('id').replace(/\D/g, ''); 
        var year        = $('#photoADayChallenge ul li.year').html();
        var idChallenge = $(this).parent().parent().attr('idChallenge'); 
	    
        uploadPhotosModule.getChallengePhotos( CT_DAILY, idChallenge, day, month, year );
	});

    // --------------------------------------------------------------------------
    $('#photoAWeekendChallenge').on('click', 'li.weeks-in-month ul li', function( event ) 
	{
        var day         = $(this).html().replace(/\D/g,'');
        var month       = $(this).parent().parent().attr('id').replace(/\D/g, ''); 
        var year        = $('#photoAWeekendChallenge ul li.year').html();
        var idChallenge = $(this).parent().parent().attr('idChallenge'); 
	    
        uploadPhotosModule.getChallengePhotos( CT_WEEKLY, idChallenge, day, month, year );
	});

    // --------------------------------------------------------------------------
    $('#photoAMonthChallenge').on('click', 'li.month-selection', function( event ) 
	{
        var day         = 1;
        var month       = $(this).attr('id').replace(/\D/g, ''); 
        var year        = $('#photoAMonthChallenge ul li.year').html();
        var idChallenge = $(this).attr('idChallenge');
	    
        uploadPhotosModule.getChallengePhotos( CT_MONTHLY, idChallenge, day, month, year );
	});

    //-----------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------
	$('#btnMyAccountUpdateInfo').click( function ()
	{
        var nLines = 0;
        var errorMsg = "";
        if( $('#myAccountFirstname').val() == "" )       { errorMsg  = "Firstname required<br>"; nLines++;  }
        if( $('#myAccountLastname').val() == "" )        { errorMsg += "Lastname required<br>"; nLines++;   }
        if( !isValidEmail( $('#myAccountEmail').val()))  { errorMsg += "Invalid Email<br>"; nLines++;       }

        if( errorMsg == "" )    { myAccountServerModule.doAjaxUpdateAccountPersonalInfo();  }
        else                    { writeUpdateAccountInfoError( nLines, errorMsg );          }
	});

    //-----------------------------------------------------------------------------------------
	$('#btnMyAccountUpdatePassword').click( function ()
	{
        var nLines = 0;
        var errorMsg = "";
        if( $('#oldPassword').val() == "" )                             { errorMsg  = "Old Password required<br>"; nLines++; }
        if( $('#newPassword').val() == "" )                             { errorMsg += "New Password required<br>"; nLines++; }
        if( $('#newPassword').val() != $('#confirmPassword').val() )    { errorMsg += "New & Confirm Password DO NOT MATCH<br>"; nLines++; }

        if( errorMsg == "") { myAccountServerModule.doAjaxUpdateAccountPassword(); }
        else                { writeUpdateAccountPasswordError( nLines, errorMsg ); }
	});


    //-----------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------
	$('#btnContactUsSend').click( function ()
	{

        var nLines = 0;
        var errorMsg = "";
        if( $('#contactUsName').val() == "" )               { errorMsg  = "Name required<br>"; nLines++;        }
        if( !isValidEmail( $('#contactUsEmail').val() ) )   { errorMsg += "Valid Email required<br>"; nLines++; }
        if( $('#contactUsMessage').val() == "" )            { errorMsg += "Nothing in Message<br>"; nLines++;   }

        if( errorMsg == "" )    { contactUsServerModule.doAjaxSendMessage();  }
        else                    { writeContactUsError( nLines, errorMsg );          }

	}); // $('#btnContactUsSend').click( function ()


}); // jQuery(document).ready(function ($)


// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
// setupSlides
// --------------------------------------------------------------------------
function setupSlides()
{
    var options = {

        $AutoPlay: true,                                    //[Optional] Whether to auto play, to enable slideshow, this option must be set to true, default value is false
        $AutoPlayInterval: 4000,                            //[Optional] Interval (in milliseconds) to go for next slide since the previous stopped if the slider is auto playing, default value is 3000
        $SlideDuration: 500,                                //[Optional] Specifies default duration (swipe) for slide in milliseconds, default value is 500
        $DragOrientation: 3,                                //[Optional] Orientation to drag slide, 0 no drag, 1 horizental, 2 vertical, 3 either, default value is 1 (Note that the $DragOrientation should be the same as $PlayOrientation when $DisplayPieces is greater than 1, or parking position is not 0)
        $UISearchMode: 0,                                   //[Optional] The way (0 parellel, 1 recursive, default value is 1) to search UI components (slides container, loading screen, navigator container, arrow navigator container, thumbnail navigator container etc).

        $ArrowKeyNavigation: true,
        $ArrowNavigatorOptions: {
            $Class: $JssorArrowNavigator$,              //[Requried] Class to create arrow navigator instance
            $ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
            $AutoCenter: 0,                                  //[Optional] Auto center navigator in parent container, 0 None, 1 Horizontal, 2 Vertical, 3 Both, default value is 0
            $Steps: 1                                       //[Optional] Steps to go for each navigation request, default value is 1
        },

        $ThumbnailNavigatorOptions: {
            $Class: $JssorThumbnailNavigator$,              //[Required] Class to create thumbnail navigator instance
            $ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
            $Loop: 1,                                       //[Optional] Enable loop(circular) of carousel or not, 0: stop, 1: loop, 2 rewind, default value is 1
            $SpacingX: 3,                                   //[Optional] Horizontal space between each thumbnail in pixel, default value is 0
            $SpacingY: 3,                                   //[Optional] Vertical space between each thumbnail in pixel, default value is 0
            $DisplayPieces: 3,                              //[Optional] Number of pieces to display, default value is 1
            $ParkingPosition: 204                           //[Optional] The offset position to park thumbnail,
        }

    }; // var options

    var jssor_slider1 = new $JssorSlider$("cntSlides", options);
    //responsive code begin
    //you can remove responsive code if you don't want the slider scales while window resizes

    function ScaleSlider() {
        var parentWidth = jssor_slider1.$Elmt.parentNode.clientWidth;
        if (parentWidth) { jssor_slider1.$ScaleWidth(Math.min(parentWidth, 800)); }
        else { window.setTimeout(ScaleSlider, 30); }

    } // ScaleSlider

    ScaleSlider();

    if (!navigator.userAgent.match(/(iPhone|iPod|iPad|BlackBerry|IEMobile)/)) { $(window).bind('resize', ScaleSlider); }


} // setupSlides


// --------------------------------------------------------------------------
// uploadPhotosModule Class
// --------------------------------------------------------------------------
var uploadPhotosModule = ( function()
{
    var thisObj = { challengeType   : "",
                    idChallenge     : "",
                    challengeNo     : "",
                    month           : "",
                    year            : "" };

    // --------------------------------------------------------------------------
    function getChallengePhotos(    challengeType,
                                    idChallenge,
                                    challengeNo,
                                    month,
                                    year  )
    {
        thisObj.challengeType   = challengeType;
        thisObj.idChallenge     = idChallenge;
        thisObj.challengeNo     = challengeNo;
        thisObj.month           = month;
        thisObj.year            = year;

        doAjaxGetChallengePhotos( idChallenge, challengeNo );

/*
        switch( challengeType )
        {
            case CT_DAILY:
                doAjaxGetChallengePhotos( idChallenge, challengeNo );
                break;
        }
*/
    } // getChallengePhotos


    // --------------------------------------------------------------------------
    function openPhotoDialog( title, objPhotoData )
    {
        // check and set variables for first upload or an update
        //
        var action = "upload-photos";
        var photo1 = "";
        var photo2 = "";
        var idChallengePhoto = "";

        if( objPhotoData != "" )
        {
            action              = "update-photos";
            idChallengePhoto    = objPhotoData.idChallengePhoto;
            photo1              = objPhotoData.photoFilename1;
            photo2              = objPhotoData.photoFilename2;
        }

        // build the dialog dynamically
        //
        // placed in html page to hard anchor tag id in to be referenced since with DOM/JQuery otherwise multiple copies of
        // a purely dynamic form would be created with subsequent calls.
        //
        //var dialogHTML  = "<div id='dialogChallengePhoto' class='dynamicDialog'>"; 
        //dialogHTML += formHTML;
        //dialogHTML += "</div>";


        var formHTML  = "<form id='frmDialogPhoto' method='POST'>";

        formHTML     += "<input type='hidden' id='dialogAction'           name='action'           value='" + action                   + "'>";
        formHTML     += "<input type='hidden' id='dialogIdChallengePhoto' name='idChallengePhoto' value='" + idChallengePhoto         + "'>";
        formHTML     += "<input type='hidden' id='dialogIdChallenge'      name='idChallenge'      value='" + thisObj.idChallenge      + "'>";
        formHTML     += "<input type='hidden' id='dialogDay'              name='day'              value='" + thisObj.challengeNo      + "'>";
        formHTML     += "<input type='hidden' id='dialogMonth'            name='month'            value='" + thisObj.month            + "'>";
        formHTML     += "<input type='hidden' id='dialogYear'             name='year'             value='" + thisObj.year             + "'>";
        formHTML     += "<input type='hidden' id='dialogChallengeType'    name='challengeType'    value='" + thisObj.challengeType    + "'>";
        formHTML     += "<input type='hidden' id='dialogIdUser'           name='idUser'           value='" + idUser                   + "'>";

        formHTML += "<label>Photo 1</label>";
        if( photo1 )    { formHTML += "<a id='viewPhoto1' data-lightbox='challengeImage' href='" + photo1 + "'>Photo Already Uploaded:  View</a><br />"; }
        else            { formHTML += "<input type='file' id='photo1' name='photo1' /><br />"; }

        formHTML += "<label>Photo 2 (optional)</label>";
        if( photo2 )    { formHTML += "<a id='viewPhoto2' data-lightbox='challengeImage' href='" + photo2 + "'>Photo Already Uploaded:  View</a><br />"; }
        else            { formHTML += "<input type='file' id='photo2' name='photo2' /><br />"; }
        
        formHTML += "</form>";

        $('#dialogChallengePhoto').html( formHTML );


        // create and display the dialog
        //
        //var dynamicDialogPhoto = $(dialogHTML);
            
        // hide the save button if both photos has been uploaded
        var buttons;
        if( photo1 && photo2 ) 
        {
            buttons = [ { text: "Close", click:  function(){ $(this).dialog("close"); } } ];
        }
        else
        {
            buttons = [ { text: "Submit", click:  function() { validateAndProcessDynamicDialogFields( $(this) ); } },
                        { text: "Close", click:  function(){ $(this).dialog("close"); } } ];
        }

        $('#dialogChallengePhoto').dialog(  {   title           : title,
                                                modal           : true,
                                                width           : "560px",
                                                height          : "auto",
                                                buttons         : buttons 
                                            });
                                      
    } //openPhotoDialog


    // --------------------------------------------------------------------------
    function validateAndProcessDynamicDialogFields( dynamicDialogPhoto )
    {
        var imageFormats = ["jpg", "gif", "png"];
        var filename1 = $( '#photo1 ').val(); 
        var filename2 = $( '#photo2 ').val(); 

        if( filename1 == "" )
        {
            alert( "No Photo Selected for Photo 1" );
        }
        else if( ( ( typeof(filename1) == 'undefined' ) && ( ( typeof(filename2) == 'undefined' ) || ( filename2 == "" ) ) ) ||
                 ( ( typeof(filename2) == 'undefined' ) && ( ( typeof(filename1) == 'undefined' ) || ( filename1 == "" ) ) ) )
        {
            alert( "No Photo Files to Upload" );
        }
        else
        {
            var bOk             = true;
            var errorMessage    = "";

            if( typeof(filename1) != 'undefined' ) 
            { 
                var fileExt1 = filename1.substring( filename1.lastIndexOf('.') + 1).toLowerCase(); 
                if( jQuery.inArray( fileExt1, imageFormats ) == -1 )
                {   
                    bOk = false;
                    errorMessage = "Photo 1 is NOT an Image\n"; 
                }
            }

            if( ( typeof(filename2) != 'undefined' ) && ( filename2 != '' ) )
            {            
                var fileExt2 = filename2.substring( filename2.lastIndexOf('.') + 1).toLowerCase(); 
                if( jQuery.inArray( fileExt2, imageFormats ) == -1 )
                {   
                    bOk = false;
                    errorMessage += "Photo 2 is NOT an Image\n"; 
                }
            }

            if( bOk )   { doAjaxUploadChallengePhotos( dynamicDialogPhoto );   }
            else        { alert( errorMessage );                               }

        }
    } // validateDynamicDialogFields                                                                                       

    // --------------------------------------------------------------------------
    // uploadPhotos AJAX methods
    // --------------------------------------------------------------------------
    $.ajaxSetup(
    {
        cache: false
    });

    // --------------------------------------------------------------------------
    function doAjaxGetChallengePhotos(  idChallenge,
                                        challengeNo )
    {
        $(".ajaxLoader").css("display", "block");

        var url = "ajaxScripts/ajaxPhotosController.php";
        var sendData = 'action=get-challenge-photos'        +
                        "&idUser="          + idUser        +
                        "&idChallenge="     + idChallenge   +
                        "&day="             + challengeNo   ;

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnGetChallengePhotosError(data);     }
                                else                                                { ajaxReturnGetChallengePhotosSuccess(data);   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxCheckUserPhotos

    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers
    //---------------------------------------------------------------------------------------------

    function ajaxReturnGetChallengePhotosError( data )
    {
    } // ajaxReturnGetChallengePhotosError

    //---------------------------------------------------------------------------------------------
    function ajaxReturnGetChallengePhotosSuccess( data )
    {
        var jsonDataString  = stripDataFromAjaxData( data );
        var objPhotoData    = ( jsonDataString == "" )? "" : JSON.parse( jsonDataString );

        switch( thisObj.challengeType )
        {
            case CT_DAILY:
                var title = "Photo A Day Challenge for " + thisObj.challengeNo + " " + months[ thisObj.month-1 ] + ", " + thisObj.year;
                openPhotoDialog( title, objPhotoData );
                break;

            case CT_WEEKLY:
                var title = "Photo A Weekend Challenge for Week" + thisObj.challengeNo + " " + months[ thisObj.month-1 ] + ", " + thisObj.year;
                openPhotoDialog( title, objPhotoData );
                break;

            case CT_MONTHLY:
                var title = "Photo A Month Challenge for " + months[ thisObj.month-1 ] + ", " + thisObj.year;
                openPhotoDialog( title, objPhotoData );
                break;

        }
    } // ajaxReturnGetChallengePhotosSuccess


    // --------------------------------------------------------------------------
    // --------------------------------------------------------------------------
    function doAjaxUploadChallengePhotos( dynamicDialogPhoto )
    {
        $(".ajaxLoader").css("display", "block");

        var url = "ajaxScripts/ajaxPhotosController.php";

        //alert(dataSend );
        $('#dialogChallengePhoto #frmDialogPhoto').ajaxSubmit({
            url         :   url,
            success     :   function( data, status ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnUploadChallengePhotosError(data, dynamicDialogPhoto);     }
                                else                                                { ajaxReturnUploadChallengePhotosSuccess(data, dynamicDialogPhoto);   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxUploadChallengePhotos

    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers
    //---------------------------------------------------------------------------------------------

    function ajaxReturnUploadChallengePhotosError(  data,
                                                    dynamicDialogPhoto )
    {
    } // ajaxReturnUploadChallengePhotosError

    function ajaxReturnUploadChallengePhotosSuccess(    data,
                                                        dynamicDialogPhoto )
    {
        dynamicDialogPhoto.dialog("close");

    } // ajaxReturnUploadChallengePhotosSuccess



    // --------------------------------------------------------------------------
    return {
        getChallengePhotos          : getChallengePhotos
    }

}()); // uploadPhotosModule


// --------------------------------------------------------------------------
// loadMenuModule - has been moved to php to create dynamically with data from database
// --------------------------------------------------------------------------
/*
var loadMenuModule = (function()
{

    // --------------------------------------------------------------------------
    // load All challenges
    // --------------------------------------------------------------------------
    function loadAll()
    {
        loadPhotoADayChallengeMenu();
        loadPhotoAWeekendChallengeMenu();
        loadPhotoAMonthChallengeMenu();

    } // loadAll

    // --------------------------------------------------------------------------
    // load Photo a Day Challenge Menu 
    // --------------------------------------------------------------------------
    //
    function loadPhotoADayChallengeMenu() 
    {
        var html    = "<ul><li class='menuItem-how-to-play'>How to Play</li>";
        html       += "<li class='year'>" + thisYear + "</li>";
        for (iMonth in months) { html += "<li id='menuItem-photoADay-month" + iMonth + "' class='menuItem-photoADay'>" + months[iMonth] + "</li>"; }
        html += "</ul>";
        $('#photoADayChallenge').append(html);

    } // loadPhotoADayChallengeMenu

    // --------------------------------------------------------------------------
    // load Photo a Weekend Challenge Menu 
    // --------------------------------------------------------------------------
    //
    function loadPhotoAWeekendChallengeMenu() 
    {
        var html = "<ul><li class='menuItem-how-to-play'>How to Play</li>";
        html += "<li class='year'>" + thisYear + "</li>";
        for (iMonth in months) {
            html += "<li class='weeks-in-month'>" + months[iMonth];
            html += "<ul>";
            for (iWeek in weeksOfMonth) { html += "<li>" + weeksOfMonth[iWeek] + "</li>"; }
            html += "</ul>";
            html += "</li>";
        }
        html += "</ul>";
        $('#photoAWeekendChallenge').append(html);

    } // loadPhotoAWeekendChallengeMenu

    // --------------------------------------------------------------------------
    // load Photo a Month Challenge Menu 
    // --------------------------------------------------------------------------
    //
    function loadPhotoAMonthChallengeMenu() 
    {
        var html = "<ul><li class='menuItem-how-to-play'>How to Play</li>";
        html += "<li class='year'>" + thisYear + "</li>";
        for (iMonth in months) { html += "<li>" + months[iMonth] + "</li>"; }
        html += "</ul>";
        $('#photoAMonthChallenge').append(html);

    } // loadPhotoAMonthChallengeMenu

    // --------------------------------------------------------------------------
    return {
        loadAll                         : loadAll,
        loadPhotoADayChallengeMenu      : loadPhotoADayChallengeMenu,
        loadPhotoAWeekendChallengeMenu  : loadPhotoAWeekendChallengeMenu,
        loadPhotoAMonthChallengeMenu    : loadPhotoAMonthChallengeMenu
    }
}());
*/

//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
// myAccountServerModule - AJAX function calls
//---------------------------------------------------------------------------------------------
var myAccountServerModule = ( function()
{
    $.ajaxSetup(
    {
        cache: false
    });
    
    //---------------------------------------------------------------------------------------------
    function doAjaxUpdateAccountPersonalInfo()
    {
        //console.log("doAjaxRegisterChallenge");

        $(".ajaxLoader").css("display", "block");

        var url      = "ajaxScripts/ajaxUserController.php";
        var sendData = 'action=update-user-personal-details'              +
                        "&idUser="       + $('#myAccountIdUser').val()       +
                        "&firstname="    + $('#myAccountFirstname').val()    +   
                        "&lastname="     + $('#myAccountLastname').val()     +
                        "&email="        + $('#myAccountEmail').val()        +
                        "&phone="        + $('#myAccountPhone').val()        ;                  

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnUpdateAccountPersonalInfoError(data);     }
                                else                                                { ajaxReturnUpdateAccountPersonalInfoSuccess(data);   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxUpdateAccountPersonalInfo


    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers for ajaxReturnUpdateAccountPersonalInfo
    //---------------------------------------------------------------------------------------------

    function ajaxReturnUpdateAccountPersonalInfoError( data )
    {
        writeUpdateAccountInfoError( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnUpdateAccountPersonalInfoError

    function ajaxReturnUpdateAccountPersonalInfoSuccess( data )
    {
        writeUpdateAccountInfoSuccess( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnUpdateAccountPersonalInfoSuccess

    //---------------------------------------------------------------------------------------------
    //---------------------------------------------------------------------------------------------
    function doAjaxUpdateAccountPassword()
    {
        //console.log("doAjaxRegisterChallenge");

        $(".ajaxLoader").css("display", "block");

        var url      = "ajaxScripts/ajaxUserController.php";
        var sendData = 'action=update-user-password'                    +
                        "&idUser="       + $('#myAccountIdUser').val()  +
                        "&oldPassword="  + $('#oldPassword').val()      +   
                        "&newPassword="  + $('#newPassword').val()      ;

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnUpdateAccountPasswordError(data);     }
                                else                                                { ajaxReturnUpdateAccountPasswordSuccess(data);   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxUpdateAccountPassword


    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers for doAjaxUpdateAccountPassword
    //---------------------------------------------------------------------------------------------

    function ajaxReturnUpdateAccountPasswordError( data )
    {
        writeUpdateAccountPasswordError( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnUpdateAccountPasswordError

    function ajaxReturnUpdateAccountPasswordSuccess( data )
    {
        writeUpdateAccountPasswordSuccess( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnUpdateAccountPasswordSuccess


    //---------------------------------------------------------------------------------------------
    return {
        doAjaxUpdateAccountPersonalInfo : doAjaxUpdateAccountPersonalInfo,
        doAjaxUpdateAccountPassword     : doAjaxUpdateAccountPassword
    };

}()); // myAccountServerModule

//---------------------------------------------------------------------------------------------
// writeUpdateAccountInfoError
//---------------------------------------------------------------------------------------------
function writeUpdateAccountInfoError(   nLines, 
                                        msg )
{
    // set height of message area
    var nHeight = parseInt( $('#cntUpdateAccountInfoMessage label.errorMessage').css('line-height'), 10) * nLines;
    $('#cntUpdateAccountInfoMessage').css("height", nHeight + "px");

    // adjust height of fieldset
//    var nMargin = parseInt($('#cntAdminMessage').css('margin-top'), 10);
//    var nFldsetHeight = nFldsetSignInHeight + nMargin + nHeight;
//    $('#fldsetSignin').css("height", nFldsetHeight + "px");

    // displayRegisterError
    //
    $('#cntUpdateAccountInfoMessage').removeClass("messageBackgroundSuccess");
    $('#cntUpdateAccountInfoMessage').addClass("messageBackgroundError");
    $('#cntUpdateAccountInfoMessage label.errorMessage').html("");
    $('#cntUpdateAccountInfoMessage label.successMessage').html( msg );
    $('#cntUpdateAccountInfoMessage').removeClass( "hide" );

} // writeUpdateAccountInfoError

//---------------------------------------------------------------------------------------------
function writeUpdateAccountInfoSuccess( nLines, 
                                        msg )
{
    // set height of message area
    var nHeight = parseInt($('#cntUpdateAccountInfoMessage label.successMessage').css('line-height'), 10) * nLines;
    $('#cntUpdateAccountInfoMessage').css("height", nHeight + "px");

    // displayRegisterError
    //
    $('#cntUpdateAccountInfoMessage').removeClass("messageBackgroundError");
    $('#cntUpdateAccountInfoMessage').addClass("messageBackgroundSuccess");
    $('#cntUpdateAccountInfoMessage label.errorMessage').html("");
    $('#cntUpdateAccountInfoMessage label.successMessage').html( msg );
    $('#cntUpdateAccountInfoMessage').removeClass( "hide" );

} // writeUpdateAccountInfoSuccess

//---------------------------------------------------------------------------------------------
// writeUpdateAccountPassword
//---------------------------------------------------------------------------------------------
function writeUpdateAccountPasswordError(   nLines, 
                                            msg )
{
    // set height of message area
    var nHeight = parseInt( $('#cntUpdateAccountPasswordMessage label.errorMessage').css('line-height'), 10) * nLines;
    $('#cntUpdateAccountPasswordMessage').css("height", nHeight + "px");

    // adjust height of fieldset
//    var nMargin = parseInt($('#cntAdminMessage').css('margin-top'), 10);
//    var nFldsetHeight = nFldsetSignInHeight + nMargin + nHeight;
//    $('#fldsetSignin').css("height", nFldsetHeight + "px");

    // displayRegisterError
    //
    $('#cntUpdateAccountPasswordMessage').removeClass("messageBackgroundSuccess");
    $('#cntUpdateAccountPasswordMessage').addClass("messageBackgroundError");
    $('#cntUpdateAccountPasswordMessage label.errorMessage').html("");
    $('#cntUpdateAccountPasswordMessage label.successMessage').html( msg );
    $('#cntUpdateAccountPasswordMessage').removeClass( "hide" );

} // writeUpdateAccountPasswordError

//---------------------------------------------------------------------------------------------
function writeUpdateAccountPasswordSuccess( nLines, 
                                            msg )
{
    // set height of message area
    var nHeight = parseInt($('#cntUpdateAccountPasswordMessage label.successMessage').css('line-height'), 10) * nLines;
    $('#cntUpdateAccountPasswordMessage').css("height", nHeight + "px");

    // displayRegisterError
    //
    $('#cntUpdateAccountPasswordMessage').removeClass("messageBackgroundError");
    $('#cntUpdateAccountPasswordMessage').addClass("messageBackgroundSuccess");
    $('#cntUpdateAccountPasswordMessage label.errorMessage').html("");
    $('#cntUpdateAccountPasswordMessage label.successMessage').html( msg );
    $('#cntUpdateAccountPasswordMessage').removeClass( "hide" );

} // writeUpdateAccountPasswordSuccess


//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
// contactUsServerModule - AJAX function calls
//---------------------------------------------------------------------------------------------
var contactUsServerModule = ( function()
{
    $.ajaxSetup(
    {
        cache: false
    });
    
    //---------------------------------------------------------------------------------------------
    function doAjaxSendMessage()
    {
        //console.log("doAjaxRegisterChallenge");

        $(".ajaxLoader").css("display", "block");

        var url      = "ajaxScripts/ajaxContactUsController.php";
        var sendData = 'action=send-message'                        +
                        "&name="    + $('#contactUsName').val()     +
                        "&email="   + $('#contactUsEmail').val()    +   
                        "&message=" + $('#contactUsMessage').val()  ;

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnSendMessageError(data);     }
                                else                                                { ajaxReturnSendMessageSuccess(data);   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxSendMessage


    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers for ajaxReturnUpdateAccountPersonalInfo
    //---------------------------------------------------------------------------------------------

    function ajaxReturnSendMessageError( data )
    {
        writeContactUsError( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnSendMessageError

    function ajaxReturnSendMessageSuccess( data )
    {
        writeContactUsSuccess( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnSendMessageSuccess


    //---------------------------------------------------------------------------------------------
    return {
        doAjaxSendMessage : doAjaxSendMessage
    };

}()); // contactUsServerModule




//---------------------------------------------------------------------------------------------
// writeContactUs
//---------------------------------------------------------------------------------------------
function writeContactUsError(   nLines, 
                                msg )
{
    // set height of message area
    var nHeight = parseInt( $('#cntContactUsMessage label.errorMessage').css('line-height'), 10) * nLines;
    $('#cntContactUsMessage').css("height", nHeight + "px");

    // adjust height of fieldset
//    var nMargin = parseInt($('#cntAdminMessage').css('margin-top'), 10);
//    var nFldsetHeight = nFldsetSignInHeight + nMargin + nHeight;
//    $('#fldsetSignin').css("height", nFldsetHeight + "px");

    // displayRegisterError
    //
    $('#cntContactUsMessage').removeClass("messageBackgroundSuccess");
    $('#cntContactUsMessage').addClass("messageBackgroundError");
    $('#cntContactUsMessage label.errorMessage').html("");
    $('#cntContactUsMessage label.successMessage').html( msg );
    $('#cntContactUsMessage').removeClass( "hide" );

} // writeContactUsError

//---------------------------------------------------------------------------------------------
function writeContactUsSuccess( nLines, 
                                msg )
{
    // set height of message area
    var nHeight = parseInt($('#cntContactUsMessage label.successMessage').css('line-height'), 10) * nLines;
    $('#cntContactUsMessage').css("height", nHeight + "px");

    // displayRegisterError
    //
    $('#cntContactUsMessage').removeClass("messageBackgroundError");
    $('#cntContactUsMessage').addClass("messageBackgroundSuccess");
    $('#cntContactUsMessage label.errorMessage').html("");
    $('#cntContactUsMessage label.successMessage').html( msg );
    $('#cntContactUsMessage').removeClass( "hide" );

} // writeContactUsSuccess
