var MAX_USER_PAGE_ITEMS         = 20;
var MAX_CHALLENGE_PAGE_ITEMS    = 20;
var MAX_SUBMISSION_PAGE_ITEMS   = 10;

//---------------------------------------------------------------------------------------------
// JQuery functions for admin site
//---------------------------------------------------------------------------------------------

var challengeTypes = [ "Once a Day", "Once a Weekend", "Once a Month" ];
var userTypes = { 0 : "Challenger", 9 : "Administrator" };


var tableSortDirections = { challengeType   : -1,   // for table listing sort directions. 
                            dateAndType     : -1,   // 
                            year            : -1,   //  1 = ascending
                            month           : -1,   // -1 = descending
                            day             : -1,
                            status          : -1,
                            firstname       : -1,
                            lastname        : -1,
                            email           : -1 };
                        

//jQuery(function($){
jQuery(document).ready( function($)
{
    // check if user is signed in

	
    //-----------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------
    // PJAX - changing pages
    //-----------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------

	$('#adminExit.js-pjax').click(function ()
	{
	    location.href = "index.php";
	});

    //-----------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------
    // JQuery load pages
    //-----------------------------------------------------------------------------------------
    // Register Challenge

    $("#cntMenuAccordion #leftMenuChallenges.leftMenu li.leftMenuRegisterChallenge").click( function()
    {
        $(".ajaxLoader").css("display", "block");

        $('#cntRightPanel').load( "include/loadRegisterChallenge.htm", function( response, status, xhr ) 
        {
            if( status == "success" )
            {
                var todaysDate  = new Date();
                var thisYear    = todaysDate.getFullYear();
                var startYear   = thisYear - 1;     // list from last year
                var endYear     = thisYear + 1;     // list to next year
                var thisMonth   = todaysDate.getMonth() + 1; // since month from Date starts from 0 and we want Jan to be 1

                for( year = startYear; year <= endYear; year++ ) { $('#loadRegisterChallenge-year').append( $('<option />').val( year ).html( year ) ); }
                $('#loadRegisterChallenge-year :nth-child(2)').prop('selected', true); // set this year to be the one displayed
                $('#loadRegisterChallenge-month').val( thisMonth ); // set this month to be the one displayed
                //$('#loadRegisterChallenge-month option:eq(' + thisMonth + ')').prop('selected', true); // set this month to be the one displayed

                $('#cntAdmin').css('height', '640px');
                $(".ajaxLoader").css("display", "none");
            }
        });
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadRegisterChallenge .updateButtons input#btnRegister', function( event )
    {
        $('#cntAdminMessage').addClass("hide");
        challengeServerModule.doAjaxRegisterChallenge();
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadRegisterChallenge .updateButtons input#btnClose', function( event )
    {
        $('#cntAdminMessage').addClass("hide");
        $('#cntRightPanel').html("");
    });

    //-----------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------
    // Update / Delete Challenge
    //
    $("#cntMenuAccordion #leftMenuChallenges.leftMenu li.leftMenuUpdateChallenge").click( function()
    {
        $(".ajaxLoader").css("display", "block");

        $('#cntRightPanel').load( "include/loadUpdateChallenge.php", function( response, status, xhr ) 
        {
            if( status == "success" )
            {
                resetTableSortDirections();

                var todaysDate  = new Date();
                var thisYear    = todaysDate.getFullYear();
                var startYear   = thisYear - 5;     // list from last 5 year
                var endYear     = thisYear + 5;     // list to next 5 year
                var thisMonth   = todaysDate.getMonth() + 1; // since Date starts month from 0 and we start from 1

                for( year = startYear; year <= endYear; year++ ) { $('#loadUpdateChallenge-year').append( $('<option />').val( year ).html( year ) ); }

                $('#fldsetUpdateChallenge .inputWrapper').addClass( 'hide' );
                $('#cntAdmin').css('height', '840px');
                $('#challengesPagination.pagination').jqPagination( { paged : function(page) { updateChallengesPagination( page ); } } );
                $(".ajaxLoader").css("display", "none");
            }
        });
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('dblclick', '#loadUpdateChallenge table#tblListingChallenges.tableListing tr', function( event )
    {
        if( this.id )
        {
            var idChallenge     = this.id.replace(/\D/g, '');
            var challengeType   = $(this).children('td:nth-child(1)').attr('value');
            var year            = $(this).children('td:nth-child(2)').html();
            var month           = $(this).children('td:nth-child(3)').attr('value');
            var status          = $(this).children('td:nth-child(4)').attr('value');
            var comments        = $(this).children('td:nth-child(5)').find('div').html();

            $('#idChallenge').val( idChallenge ); 
            $('#loadUpdateChallenge-challengeType').val( challengeType );
            $('#loadUpdateChallenge-year').val( year );
            $('#loadUpdateChallenge-month').val( month );
            $('#loadUpdateChallenge-status').val( status );
            $('#loadUpdateChallenge-comments').val( comments );

            $('#cntAdminMessage').addClass( 'hide' );
            $('#fldsetUpdateChallenge .inputWrapper').removeClass('hide');
            $('#fldsetUpdateChallenge .listingChallenges').addClass('hide');
        }
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUpdateChallenge .updateButtons input#btnUpdate', function( event )
    {
        $('#cntAdminMessage').addClass( 'hide' );
        challengeServerModule.doAjaxUpdateChallenge( $('#idChallenge').val() );
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUpdateChallenge .updateButtons input#btnDelete', function( event )
    {
        $('#cntAdminMessage').addClass( 'hide' );

        var idChallenge = $('#idChallenge').val();
        var challenge = challengesListing.filter( function( challenge ) { return challenge.idChallenge == idChallenge; } );
        confirmChallengeDeletionDialog( challenge[0] ); 
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUpdateChallenge .updateButtons input#btnClose', function( event )
    {
        // make this visible for user to see
        //
        $('#cntAdminMessage').addClass('hide');
        $('#fldsetUpdateChallenge .inputWrapper').addClass( 'hide' );
        $('#fldsetUpdateChallenge .listingChallenges').removeClass( 'hide' );
    });

    //-----------------------------------------------------------------------------------------
    // Sorting for Challenges

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUpdateChallenge table#tblListingChallenges.tableListing thead tr:nth-child(2) th:nth-child(1)', function( event )
    {
        tableSortDirections.challengeType *= -1; // change the direction
        challengesListing.sort( function( a, b ) { return ( parseInt( a.challengeType, 10 ) - parseInt( b.challengeType, 10 ) ) * tableSortDirections.challengeType; });
        rebuildChallengesListingTable();
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUpdateChallenge table#tblListingChallenges.tableListing thead tr:nth-child(1) th:nth-child(1)', function( event )
    {
        tableSortDirections.dateAndType *= -1; // change the direction
        challengesListing.sort( function( a, b ) 
                                { 
                                    if( ( a.year == b.year ) && ( a.month == b.month ) )    { return ( parseInt( a.challengeType, 10 ) - parseInt( b.challengeType, 10 ) ); }
                                    else if( a.year == b.year )                             { return ( parseInt( a.month, 10 ) - parseInt( b.month, 10 ) ) * tableSortDirections.dateAndType; }
                                    else                                                    { return ( parseInt( a.year, 10 ) - parseInt( b.year, 10 ) ) * tableSortDirections.dateAndType; }
                                });
        rebuildChallengesListingTable();
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUpdateChallenge table#tblListingChallenges.tableListing thead tr:nth-child(2) th:nth-child(2)', function( event )
    {
        tableSortDirections.year *= -1; // change the direction
        challengesListing.sort( function( a, b ) { return ( parseInt( a.year, 10 ) - parseInt( b.year, 10 ) ) * tableSortDirections.year; });
        rebuildChallengesListingTable();
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUpdateChallenge table#tblListingChallenges.tableListing thead tr:nth-child(2)  th:nth-child(3)', function( event )
    {
        tableSortDirections.month *= -1; // change the direction
        challengesListing.sort( function( a, b ) { return ( parseInt( a.month, 10 ) - parseInt( b.month, 10 ) ) * tableSortDirections.month; });
        rebuildChallengesListingTable();
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUpdateChallenge table#tblListingChallenges.tableListing thead tr:nth-child(1) th:nth-child(2)', function( event )
    {
        tableSortDirections.status *= -1;
        challengesListing.sort( function( a, b ) { return ( parseInt( a.status, 10 ) - parseInt( b.status, 10 ) ) * tableSortDirections.status; });
        rebuildChallengesListingTable();
    });


    //-----------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------
    // Load View Submissions
    //
    $("#cntMenuAccordion #leftMenuSubmissions.leftMenu li.leftMenuViewSubmissions").click( function()
    {
        $(".ajaxLoader").css("display", "block");

        $('#cntRightPanel').load( "include/loadSubmissions.php", function( response, status, xhr ) 
        {
            if( status == "success" )
            {
                resetTableSortDirections();

                $('#cntAdmin').css('height', '640px');
                $('#submissionsPagination.pagination').jqPagination( { paged : function(page) { updateSubmissionsPagination( page ); } } );
                $(".ajaxLoader").css("display", "none");
            }
        });

    }); // $("#cntMenuAccordion #leftMenuSubmissions.leftMenu li.leftMenuViewSubmissions").click( function()


    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('dblclick', '#loadSubmissions table#tblListingSubmissions.tableListing tr', function( event )
    {
        if( this.id )
        {
            var idChallengePhoto    = this.id.replace(/\D/g, '');
            var submissions = submissionsListing.filter( function( submission ) { return submission.idChallengePhoto == idChallengePhoto; } );
            submissionsEditDialog( submissions[0] );
        }

    }); // $('#cntRightPanel').on('dblclick', '#loadSubmissions table#tblListingSubmissions.tableListing tr', function( event )


    //-----------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadSubmissions .listingSubmissions a.backToSearch', function( event )
    {
        $('#cntAdmin').css('height', '640px');
        $('#fldsetViewSubmissions legend').html("Retrieve Submissions to View");
        $('#fldsetViewSubmissions .listingSubmissions').addClass( 'hide' );
        $('#fldsetViewSubmissions .inputWrapper').removeClass( 'hide' );
    });

    //-----------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------
    // Submission button clicks
    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadSubmissions .searchButtons input#btnRetrieve', function( event )
    {
        $('#cntAdminMessage').addClass("hide");
        submissionsServerModule.doAjaxRetrieveSubmissions();
    });

    $('#cntRightPanel').on('click', '#loadSubmissions .searchButtons input#btnClear', function( event )
    {
        $('#loadSubmissions-challengeType :nth-child(1)').prop('selected', true);
        $('#loadSubmissions-month :nth-child(1)').prop('selected', true);
        $('#loadSubmissions-year :nth-child(1)').prop('selected', true);
        $('#loadSubmissions-firstname').val("");
        $('#loadSubmissions-lastname').val("");
    });

    //-----------------------------------------------------------------------------------------
    // Sorting for Submissions

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadSubmissions table#tblListingSubmissions.tableListing thead tr:nth-child(1) th:nth-child(1)', function( event )
    {
        tableSortDirections.dateAndType *= -1; // change the direction
        submissionsListing.sort(    function( a, b ) 
                                    { 
                                        if( ( a.year == b.year ) && ( a.month == b.month ) )    { return ( parseInt( a.challengeType, 10 ) - parseInt( b.challengeType, 10 ) ); }
                                        else if( a.year == b.year )                             { return ( parseInt( a.month, 10 ) - parseInt( b.month, 10 ) ) * tableSortDirections.dateAndType; }
                                        else if( a.year == b.year )                             { return ( parseInt( a.year, 10 ) - parseInt( b.year, 10 ) ) * tableSortDirections.dateAndType; }
                                        else                                                    { return ( parseInt( a.day, 10 ) - parseInt( b.day, 10 ) ) * tableSortDirections.dateAndType; }
                                    });
        rebuildSubmissionsListingTable();
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadSubmissions table#tblListingSubmissions.tableListing thead tr:nth-child(2) th:nth-child(1)', function( event )
    {
        tableSortDirections.challengeType *= -1; // change the direction
        submissionsListing.sort( function( a, b ) { return ( parseInt( a.challengeType, 10 ) - parseInt( b.challengeType, 10 ) ) * tableSortDirections.challengeType; });
        rebuildSubmissionsListingTable();
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadSubmissions table#tblListingSubmissions.tableListing thead tr:nth-child(2) th:nth-child(2)', function( event )
    {
        tableSortDirections.year *= -1; // change the direction
        submissionsListing.sort( function( a, b ) { return ( parseInt( a.year, 10 ) - parseInt( b.year, 10 ) ) * tableSortDirections.year; });
        rebuildSubmissionsListingTable();
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadSubmissions table#tblListingSubmissions.tableListing thead tr:nth-child(2)  th:nth-child(3)', function( event )
    {
        tableSortDirections.month *= -1; // change the direction
        submissionsListing.sort( function( a, b ) { return ( parseInt( a.month, 10 ) - parseInt( b.month, 10 ) ) * tableSortDirections.month; });
        rebuildSubmissionsListingTable();
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadSubmissions table#tblListingSubmissions.tableListing thead tr:nth-child(2) th:nth-child(4)', function( event )
    {
        tableSortDirections.day *= -1;
        submissionsListing.sort( function( a, b ) { return ( parseInt( a.challengeNo, 10 ) - parseInt( b.challengeNo, 10 ) ) * tableSortDirections.day; });
        rebuildSubmissionsListingTable();
    });

    //-----------------------------------------------------------------------------------------
    // Submissions dynamic dialog delete photo clicks
    //-----------------------------------------------------------------------------------------
    $('#dialogChallengePhoto').on('click', '#dialogSubmissionsEdit.dynamicDialog input#btnDeleteSubmissionPhoto1', function( event )
    {
        if( confirm( "Please Confirm Deletion/Rejection of Photo #1") )
        {
            if( document.getElementById("btnDeleteSubmissionPhoto1") )  { submissionsServerModule.doAjaxDeleteSubmissionPhoto( 1 ); }
            else                                                        { submissionsServerModule.doAjaxDeleteSubmissionPhoto( 0 );  }   
        }
    });

    $('#dialogChallengePhoto').on('click', '#dialogSubmissionsEdit.dynamicDialog input#btnDeleteSubmissionPhoto2', function( event )
    {
        if( confirm( "Please Confirm Deletion/Rejection of Photo #2") )
        {
            if( document.getElementById("btnDeleteSubmissionPhoto2") )  { submissionsServerModule.doAjaxDeleteSubmissionPhoto( 2 ); }
            else                                                        { submissionsServerModule.doAjaxDeleteSubmissionPhoto( 0 ); }   
        }        
    });


    //-----------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------
    // Load View Users
    //
    $("#cntMenuAccordion #leftMenuUsers.leftMenu li.leftMenuViewUsers").click( function()
    {
        $(".ajaxLoader").css("display", "block");

        $('#cntRightPanel').load( "include/loadUsers.php", function( response, status, xhr ) 
        {
            if( status == "success" )
            {
                $('#cntAdmin').css('height', '640px');
                $('#usersPagination.pagination').jqPagination( { paged : function(page) { viewUsersPagination( page ); } } );
                $(".ajaxLoader").css("display", "none");
            }
        });

    }); // $("#cntMenuAccordion #leftMenuUsers.leftMenu li.leftMenuViewUsers").click( function()


    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('dblclick', '#loadUsers table#tblListingUsers.tableListing tr', function( event )
    {
        if( this.id )
        {
            var idUser      = this.id.replace(/\D/g, '');
            var user        = usersListing.filter( function( user ) { return user.idUser == idUser; } );

            var firstname   = user[0].firstname;
            var lastname    = user[0].lastname;
            var email       = user[0].email;
            var phone       = user[0].phone;
            //var userType    = userTypes[user[0].userType];

            $('#userViewIdUser').val( idUser );
            $('#userViewFirstname').val( firstname );
            $('#userViewLastname').val( lastname );
            $('#userViewEmail').val( email );
            $('#userViewPhone').val( phone );
            //$('#userViewUserType').val( userType );
            $('#userViewUserType').children("[value='"+ user[0].userType +"']").prop('selected', true); // set this year to be the one displayed


            $('#cntAdminMessage').addClass( 'hide' );
            $('#fldsetViewUsers .listingUsers').addClass( 'hide' );
            $('#fldsetViewUsers .inputWrapper').removeClass( 'hide' );
           
        }

    }); // $('#cntRightPanel').on('dblclick', '#loadSubmissions table#tblListingSubmissions.tableListing tr', function( event )


    //-----------------------------------------------------------------------------------------
    // Sorting for Users

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUsers table#tblListingUsers.tableListing thead tr th:nth-child(1)', function( event )
    {
        tableSortDirections.firstname *= -1; // change the direction
        usersListing.sort( function( a, b ) { return ( a.firstname <  b.firstname )? -1 * tableSortDirections.firstname : tableSortDirections.firstname; });
        rebuildUsersListingTable();
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUsers table#tblListingUsers.tableListing thead tr th:nth-child(2)', function( event )
    {
        tableSortDirections.lastname *= -1; // change the direction
        usersListing.sort( function( a, b ) { return ( a.lastname <  b.lastname )? -1 * tableSortDirections.lastname : tableSortDirections.lastname; });
        rebuildUsersListingTable();
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUsers table#tblListingUsers.tableListing thead tr th:nth-child(3)', function( event )
    {
        tableSortDirections.email *= -1; // change the direction
        usersListing.sort( function( a, b ) { return ( a.email <  b.email )? -1 * tableSortDirections.email : tableSortDirections.email; });
        rebuildUsersListingTable();
    });


    //-----------------------------------------------------------------------------------------
    // User Update Buttons
    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUsers .updateButtons input#btnUpdate', function( event )
    {
        $('#cntAdminMessage').addClass("hide");
        var idUser   = $('#userViewIdUser').val();
        userServerModule.doAjaxUpdateUser( idUser );
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUsers .updateButtons input#btnDelete', function( event )
    {
        var name = $('#userViewFirstname').val() + " " + $('#userViewLastname').val();
        if( confirm( "Please Confirm Deletion of User '" + name + "'" ) )
        {
            var idUser   = $('#userViewIdUser').val();
            $('#cntAdminMessage').addClass("hide");
            userServerModule.doAjaxDeleteUser( idUser );
        }
    });

    //-----------------------------------------------------------------------------------------
    $('#cntRightPanel').on('click', '#loadUsers .updateButtons input#btnClose', function( event )
    {
        $('#cntAdminMessage').addClass("hide");
        $('#fldsetViewUsers .listingUsers').removeClass( 'hide' );
        $('#fldsetViewUsers .inputWrapper').addClass( 'hide' );
    });

    //-----------------------------------------------------------------------------------------
    // Accordion
    //
    $("#cntMenuAccordion").accordion();



}); // jQuery(document).ready( function($)


//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
// challengeServerModule - AJAX function calls
//---------------------------------------------------------------------------------------------
var challengeServerModule = ( function()
{
    $.ajaxSetup(
    {
        cache: false
    });
    
    //---------------------------------------------------------------------------------------------
    function doAjaxRegisterChallenge()
    {
        //console.log("doAjaxRegisterChallenge");

        $(".ajaxLoader").css("display", "block");

        var url      = "ajaxScripts/ajaxChallengeController.php";
        var sendData = 'action=register-challenge'                  +
                        "&challengeType="   + $('#loadRegisterChallenge-challengeType').val()   +
                        "&month="           + $('#loadRegisterChallenge-month').val()           +   
                        "&year="            + $('#loadRegisterChallenge-year').val()            +
                        "&status="          + $('#loadRegisterChallenge-status').val()          +
                        "&comments="        + $('#loadRegisterChallenge-comments').val()        ;                  

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnRegisterChallengeError(data);     }
                                else                                                { ajaxReturnRegisterChallengeSuccess(data);   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxRegisterChallenge


    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers for doAjaxRegisterChallenge
    //---------------------------------------------------------------------------------------------

    function ajaxReturnRegisterChallengeError( data )
    {
        writeAdminError( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnRegisterChallengesError

    function ajaxReturnRegisterChallengeSuccess( data )
    {
        writeAdminSuccess( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnRegisterChallengeSuccess

    //---------------------------------------------------------------------------------------------
    //---------------------------------------------------------------------------------------------
    function doAjaxUpdateChallenge( idChallenge )
    {
        //console.log("doAjaxRegisterChallenge");

        $(".ajaxLoader").css("display", "block");

        var url      = "ajaxScripts/ajaxChallengeController.php";
        var sendData = 'action=update-challenge'                                              +
                        "&idChallenge="     + idChallenge                                     +
                        "&challengeType="   + $('#loadUpdateChallenge-challengeType').val()   +
                        "&month="           + $('#loadUpdateChallenge-month').val()           +
                        "&year="            + $('#loadUpdateChallenge-year').val()            +
                        "&status="          + $('#loadUpdateChallenge-status').val()          +
                        "&comments="        + $('#loadUpdateChallenge-comments').val()        ;                  

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnUpdateChallengeError( data, idChallenge );     }
                                else                                                { ajaxReturnUpdateChallengeSuccess( data, idChallenge );   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxUpdateChallenge

    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers for doAjaxUpdateChallenge
    //---------------------------------------------------------------------------------------------

    function ajaxReturnUpdateChallengeError(    data,
                                                idChallenge )
    {
        writeAdminError( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnUpdateChallengeError

    function ajaxReturnUpdateChallengeSuccess(  data,
                                                idChallenge )
    {
        writeAdminSuccess( 1, stripMessageFromAjaxData( data )  );
        updateChallengeInListing( idChallenge );

    } // ajaxReturnUpdateChallengeSuccess

    //---------------------------------------------------------------------------------------------
    //---------------------------------------------------------------------------------------------
    function doAjaxDeleteChallenge( idChallenge )
    {
        $(".ajaxLoader").css("display", "block");

        var url      = "ajaxScripts/ajaxChallengeController.php";
        var sendData = 'action=delete-challenge'            +
                        "&idChallenge="     + idChallenge   ;

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnDeleteChallengeError( data, idChallenge );     }
                                else                                                { ajaxReturnDeleteChallengeSuccess( data, idChallenge );   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxDeleteChallenge

    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers for doAjaxDeleteChallenge
    //---------------------------------------------------------------------------------------------

    function ajaxReturnDeleteChallengeError(    data, 
                                                idChallenge )
    {
        writeAdminError( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnDeleteChallengeError

    function ajaxReturnDeleteChallengeSuccess(  data, 
                                                idChallenge )
    {
        writeAdminSuccess( 1, stripMessageFromAjaxData( data )  );
        deleteChallengeFromListing( idChallenge );

        // go back to listing
        $('#fldsetUpdateChallenge .inputWrapper').addClass( 'hide' );
        $('#fldsetUpdateChallenge .listingChallenges').removeClass( 'hide' );

    } // ajaxReturnDeleteChallengeSuccess


    //---------------------------------------------------------------------------------------------
    return {
        doAjaxRegisterChallenge : doAjaxRegisterChallenge,
        doAjaxUpdateChallenge   : doAjaxUpdateChallenge,
        doAjaxDeleteChallenge   : doAjaxDeleteChallenge
    };

}()); // challengeServerModule

//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
// challengesListing - Tools for manipulating the JSON object structure challengesListing
//---------------------------------------------------------------------------------------------
var challengesListing; // returned json listing of challenges records from jQuery load() call

//---------------------------------------------------------------------------------------------
// updateChallengeInListing
//---------------------------------------------------------------------------------------------
function updateChallengeInListing( idChallenge )
{
    // update the data
    //
    var challenge = challengesListing.filter( function( challenge ) { return challenge.idChallenge == idChallenge; } );
    challenge[0].challengeType  = $('#loadUpdateChallenge-challengeType').val();
    challenge[0].year           = $('#loadUpdateChallenge-year').val();
    challenge[0].month          = $('#loadUpdateChallenge-month').val();
    challenge[0].status         = $('#loadUpdateChallenge-status').val();
    challenge[0].comments       = $('#loadUpdateChallenge-comments').val();
    rebuildChallengesListingTable();

} // updateChallengeInListing

//---------------------------------------------------------------------------------------------
// deleteChallengeFromListing
//---------------------------------------------------------------------------------------------
function deleteChallengeFromListing( idChallenge )
{
    // update the data
    //
    var iChallenge  = 0;
    var isDone      = false;
    for( ; !isDone && (iChallenge < challengesListing.length); iChallenge++ )
    {
        if( challengesListing[ iChallenge ].idChallenge == idChallenge )
        {
            challengesListing.splice( iChallenge, 1 );
            isDone = true;
        }
    }
    rebuildChallengesListingTable();

} // deleteChallengeFromListing


//---------------------------------------------------------------------------------------------
// rebuildChallengesListingTable
//---------------------------------------------------------------------------------------------
function rebuildChallengesListingTable()
{
    var tbodyRows   = "";

    for( iRow in challengesListing )
    {
        var challengeType   = challengeTypes[ challengesListing[iRow].challengeType - 1 ];
        var month           = months[ challengesListing[iRow].month - 1 ];
        var status          = ( challengesListing[iRow].status == "1" )? "Active" : "Inactive";

        var hideClass       = ( iRow >= MAX_CHALLENGE_PAGE_ITEMS )? "hide" : "";

        tbodyRows += "<tr id='trChallenge"  + challengesListing[iRow].idChallenge   + "' class='hoverHighlight " + hideClass + "'>";
        tbodyRows += "    <td value='"      + challengesListing[iRow].challengeType + "'>" + challengeType + "</td>";
        tbodyRows += "    <td>"             + challengesListing[iRow].year          + "</td>";
        tbodyRows += "    <td value="       + challengesListing[iRow].month         + ">" + month + "</td>";
        tbodyRows += "    <td value='"      + challengesListing[iRow].status        + "'>" + status + "</td>";
        tbodyRows += "    <td><div>"        + challengesListing[iRow].comments      + "</div></td>";
        tbodyRows += "</tr>";
    }

    $('table#tblListingChallenges tbody').html( tbodyRows );

    // reset the page on the pagination to 1
    // 
    var nPages = Math.ceil( challengesListing.length  / MAX_CHALLENGE_PAGE_ITEMS );
    $('#challengesPagination.pagination').jqPagination( 'option', 'max_page', nPages );
    $('#challengesPagination.pagination').jqPagination( 'option', 'current_page', 1 );

    if ( nPages <= 1 ) { $('#challengesPagination.pagination').addClass( 'hide' ); }

} // rebuildChallengesListingTable


//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
function writeAdminError(   nLines, 
                            msg )
{
    // set height of message area
    var nHeight = parseInt( $('#cntAdminMessage label.errorMessage').css('line-height'), 10) * nLines;
    $('#cntAdminMessage').css("height", nHeight + "px");

    // adjust height of fieldset
//    var nMargin = parseInt($('#cntAdminMessage').css('margin-top'), 10);
//    var nFldsetHeight = nFldsetSignInHeight + nMargin + nHeight;
//    $('#fldsetSignin').css("height", nFldsetHeight + "px");

    // displayRegisterError
    //
    $('#cntAdminMessage').removeClass("messageBackgroundSuccess");
    $('#cntAdminMessage').addClass("messageBackgroundError");
    $('#cntAdminMessage label.errorMessage').html("");
    $('#cntAdminMessage label.successMessage').html( msg );
    $('#cntAdminMessage').removeClass( "hide" );

} // writeAdminError

//---------------------------------------------------------------------------------------------
function writeAdminSuccess( nLines, 
                            msg )
{
    // set height of message area
    var nHeight = parseInt($('#cntAdminMessage label.successMessage').css('line-height'), 10) * nLines;
    $('#cntAdminMessage').css("height", nHeight + "px");

    // displayRegisterError
    //
    $('#cntAdminMessage').removeClass("messageBackgroundError");
    $('#cntAdminMessage').addClass("messageBackgroundSuccess");
    $('#cntAdminMessagelabel.errorMessage').html("");
    $('#cntAdminMessage label.successMessage').html( msg );
    $('#cntAdminMessage').removeClass( "hide" );

} // writeAdminSuccess


// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
function confirmChallengeDeletionDialog( challenge )
{
    var confirmOk   = false;
    var title       = "Please Confirm Deletion for Challenge";

    var challengeType   = challengeTypes[ challenge.challengeType - 1 ];
    var month           = months[ challenge.month - 1 ];
    var status          = ( challenge.status == "1" )? "Active" : "Inactive";


    var dialogHTML  = "<div id='dialogConfirmDeletion' class='dynamicDialogConfirmDeletion'>";

    dialogHTML += "<label>Challenge Type:</label>";
    dialogHTML += "<input type='text' value='" + challengeType + "' disabled><br>";

    dialogHTML += "<label>Year:</label>";
    dialogHTML += "<input type='text' value='" + challenge.year + "' disabled><br>";

    dialogHTML += "<label>Month:</label>";
    dialogHTML += "<input type='text' value='" + month + "' disabled><br>";
               
    dialogHTML += "<label>Status:</label>";
    dialogHTML += "<input type='text' value='" + status + "' disabled><br>";

    dialogHTML += "<label>Comments:</label>";
    dialogHTML += "<input type='text' value='" + challenge.comments + "' disabled><br>";

    dialogHTML += "<span class='important'>Note: this will also DELETE ALL photo Submissions for this Challenge.</span><br>";

             
    dialogHTML += "</div>";
            
    var dynamicDialog = $(dialogHTML);
    dynamicDialog.dialog({  title           : title,
                            modal           : true,
                            width           : "500px",
                            height          : "auto",
                            dialogClass     : "dynamicDialogConfirmDeletion",
                            buttons         : [ { text: "Cancel", click:    function(){ $(this).dialog("close"); } },
                                                { text: "Confirm", click:    function() 
                                                                            { 
                                                                                challengeServerModule.doAjaxDeleteChallenge( challenge.idChallenge ); 
                                                                                $(this).dialog("close"); 
                                                                            } 
                                                }] 
                            });
                           

} //confirmChallengeDeletionDialog

// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
// Pagination - Challenges
// --------------------------------------------------------------------------
function updateChallengesPagination( page )
{
    // hide all the rows in the table
    $('table#tblListingChallenges tbody').children("tr").addClass( 'hide' );

    // show only the rows in the visible page
    var nStartVisibleRow  = (page - 1) * MAX_CHALLENGE_PAGE_ITEMS + 1;
    var nEndVisibleRow    = Math.min( challengesListing.length, nStartVisibleRow + MAX_CHALLENGE_PAGE_ITEMS - 1 );

    var nthChildString = "tr:nth-child(n+" + nStartVisibleRow + "):nth-child(-n+" + nEndVisibleRow + ")";

    $('table#tblListingChallenges tbody').children( nthChildString ).removeClass( 'hide' );

    window.scrollTo(0, document.querySelector(".listingChallenges").offsetTop);    // scroll back up to the top

} // updateChallengesPagination

// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
// Submissions
// --------------------------------------------------------------------------
var submissionsListing;

//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
// submissionsServerModule - AJAX function calls
//---------------------------------------------------------------------------------------------
var submissionsServerModule = ( function()
{
    $.ajaxSetup(
    {
        cache: false
    });

    //---------------------------------------------------------------------------------------------
    //---------------------------------------------------------------------------------------------
    function doAjaxRetrieveSubmissions()
    {
        //console.log("doAjaxRegisterChallenge");

        $(".ajaxLoader").css("display", "block");

        var url      = "ajaxScripts/ajaxSubmissionsController.php";
        var sendData = 'action=retrieve-submissions'                                      +
                        "&challengeType="   + $('#loadSubmissions-challengeType').val()   +
                        "&month="           + $('#loadSubmissions-month').val()           +
                        "&year="            + $('#loadSubmissions-year').val()            +
                        "&firstname="       + $('#loadSubmissions-firstname').val()       +
                        "&lastname="        + $('#loadSubmissions-lastname').val()        ;                  

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnRetrieveSubmissionError(data);     }
                                else                                                { ajaxReturnRetrieveSubmissionSuccess(data);   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxRetrieveSubmissions

    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers for doAjaxRetrieveSubmissions
    //---------------------------------------------------------------------------------------------

    function ajaxReturnRetrieveSubmissionError( data )
    {
        writeAdminError( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnRetrieveSubmissionError

    function ajaxReturnRetrieveSubmissionSuccess( data )
    {
        //writeAdminSuccess( 1, stripMessageFromAjaxData( data )  );

        var jsonData = stripDataFromAjaxData( data );
        submissionsListing = ( jsonData != "" )? JSON.parse( jsonData ) : "";
        updateSubmissionsListing();

        $('#cntAdmin').css('height', '1140px');
        $('#fldsetViewSubmissions legend').html("Submissions Listing");

        // go to listing
        $('#fldsetViewSubmissions .inputWrapper').addClass( 'hide' );
        $('#fldsetViewSubmissions .listingSubmissions').removeClass( 'hide' );

    } // ajaxReturnRetrieveSubmissionSuccess


    //---------------------------------------------------------------------------------------------
    //---------------------------------------------------------------------------------------------
    function doAjaxDeleteSubmissionPhoto( whichPhoto )
    {
        //console.log("doAjaxRegisterChallenge");

        $(".ajaxLoader").css("display", "block");

        var url      = "ajaxScripts/ajaxPhotosController.php";
        var sendData = 'action=delete-photo'                                                    +
                        "&idChallengePhoto="   + $('#editSubmission_idChallengePhoto').val()    +
                        "&whichPhoto="         + whichPhoto                                     ;

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnDeleteSubmissionPhotoError( data, whichPhoto );     }
                                else                                                { ajaxReturnDeleteSubmissionPhotoSuccess( data, whichPhoto );   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxDeleteSubmissionPhoto

    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers for doAjaxDeleteSubmissionPhoto
    //---------------------------------------------------------------------------------------------

    function ajaxReturnDeleteSubmissionPhotoError(  data,
                                                    whichPhoto )
    {
        //writeAdminError( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnRetrieveSubmissionError

    function ajaxReturnDeleteSubmissionPhotoSuccess(    data,
                                                        whichPhoto )
    {
        var idChallengePhoto = stripDataFromAjaxData( data );

        // get index
        var iSubmission = -1;
        for( iSubmission in submissionsListing ) { if( submissionsListing[ iSubmission ].idChallengePhoto == idChallengePhoto ) { break; } }

        if( iSubmission != -1 )
        {
            
            //
            if( whichPhoto == 0 )       
            { 
                submissionsListing.splice( iSubmission, 1 ); 
                $('#dialogSubmissionsEdit.dynamicDialog #cntDialogSubmissionsEdit_photo1').html("<span>none</span>") // remove from dynamic dialog display
                $('#dialogSubmissionsEdit.dynamicDialog #cntDialogSubmissionsEdit_photo2').html("<span>none</span>") // remove from dynamic dialog display
            }
            else
            {
                if( whichPhoto == 1 )   
                { 
                    submissionsListing[ iSubmission ].photoFilename1 = submissionsListing[ iSubmission ].photoThumbnail1   = ""; // remove from from submissionsListing array
                    $('#dialogSubmissionsEdit.dynamicDialog #cntDialogSubmissionsEdit_photo1').html("<span>none</span>") // remove from dynamic dialog display
                }
                else                    
                { 
                    submissionsListing[ iSubmission ].photoFilename2 = submissionsListing[ iSubmission ].photoThumbnail2   = ""; // remove from from submissionsListing array
                    $('#dialogSubmissionsEdit.dynamicDialog #cntDialogSubmissionsEdit_photo2').html("<span>none</span>") // remove from dynamic dialog display
                }
            } 
            

        }

//    submission[0].photoThumbnail2   = $('#loadUpdateChallenge-photoFilename2').val();



    } // ajaxReturnRetrieveSubmissionSuccess



    //---------------------------------------------------------------------------------------------
    return {
        doAjaxRetrieveSubmissions   : doAjaxRetrieveSubmissions,
        doAjaxDeleteSubmissionPhoto : doAjaxDeleteSubmissionPhoto
    };

}()); // submissionsServerModule

//---------------------------------------------------------------------------------------------
function updateSubmissionsListing()
{
    // update the data
    //
//    var idChallengePhoto            = $('#idChallengePhoto').val();
//    var submission                  = submissionsListing.filter( function( submission ) { return submission.idChallengePhoto == idChallengePhoto; } );
//    submission[0].photoThumbnail1   = $('#loadUpdateChallenge-photoFilename1').val();
//    submission[0].photoThumbnail2   = $('#loadUpdateChallenge-photoFilename2').val();
    rebuildSubmissionsListingTable();

} // updateSubmissionsListing

//---------------------------------------------------------------------------------------------
function rebuildSubmissionsListingTable()
{
    var tbodyRows   = "";

    for( iRow in submissionsListing )
    {
        var challengeType   = challengeTypes[ submissionsListing[iRow].challengeType - 1 ];
        var month           = months[ submissionsListing[iRow].month - 1 ];

        var hideClass       = ( iRow >= MAX_SUBMISSION_PAGE_ITEMS )? "hide" : "";

        var photo1          = ( submissionsListing[iRow].photoFilename1 == "" )? "" : "<a href='"  + submissionsListing[iRow].photoFilename1 + "' data-lightbox='submissionImage" + submissionsListing[iRow].idChallengePhoto + "'><img src='"  + submissionsListing[iRow].photoThumbnail1 + "' /></a>";
        var photo2          = ( submissionsListing[iRow].photoFilename2 == "" )? "" : "<a href='"  + submissionsListing[iRow].photoFilename2 + "' data-lightbox='submissionImage" + submissionsListing[iRow].idChallengePhoto + "'><img src='"  + submissionsListing[iRow].photoThumbnail2 + "' /></a>";

        tbodyRows += "<tr id='trSubmission" + submissionsListing[iRow].idChallengePhoto + "' class='hoverHighlight " + hideClass + "'>";
        tbodyRows += "    <td value='"      + submissionsListing[iRow].challengeType    + "'>" + challengeType + "</td>";
        tbodyRows += "    <td>"             + submissionsListing[iRow].year             + "</td>";
        tbodyRows += "    <td value="       + submissionsListing[iRow].month            + ">" + month + "</td>";
        tbodyRows += "    <td>"             + submissionsListing[iRow].challengeNo      + "</td>";
        tbodyRows += "    <td>"             + photo1                                    + "</td>";
        tbodyRows += "    <td>"             + photo2                                    + "</td>";
        tbodyRows += "</tr>";
    }

    $('table#tblListingSubmissions tbody').html( tbodyRows );

    // reset the page on the pagination to 1
    // 
    var nPages = Math.ceil( submissionsListing.length  / MAX_SUBMISSION_PAGE_ITEMS );
    $('#submissionsPagination.pagination').jqPagination( 'option', 'max_page', nPages );
    $('#submissionsPagination.pagination').jqPagination( 'option', 'current_page', 1 );

    if ( nPages <= 1 ) { $('#submissionsPagination.pagination').addClass( 'hide' ); }


} // rebuildSubmissionsListingTable



// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
function submissionsEditDialog( submission )
{
    var confirmOk   = false;

    var challengeType   = challengeTypes[ submission.challengeType - 1 ];
    var month           = months[ submission.month - 1 ];
    var title       = "Submission for : " + challengeType + " Challenge - " + month + ", " + submission.year;

    var photo1          = ( submission.photoFilename1 == "" )? "<span>none</span>" : "<a href='"  + submission.photoFilename1 + "' data-lightbox='submissionImageDialog" + submission.idChallengePhoto + "'><img src='"  + submission.photoThumbnail1 + "' /></a><input id='btnDeleteSubmissionPhoto1' type='button' class='btn btnDelete' value='Delete/Reject'>";
    var photo2          = ( submission.photoFilename2 == "" )? "<span>none</span>" : "<a href='"  + submission.photoFilename2 + "' data-lightbox='submissionImageDialog" + submission.idChallengePhoto + "'><img src='"  + submission.photoThumbnail2 + "' /></a><input id='btnDeleteSubmissionPhoto2' type='button' class='btn btnDelete' value='Delete / Reject'>";


    var dialogHTML  = "<div id='dialogSubmissionsEdit' class='dynamicDialog'>";

    dialogHTML += "<input id='editSubmission_idChallengePhoto' type='hidden' value='"+ submission.idChallengePhoto +"' />";

    dialogHTML += "<h3>Challenge No. : " + submission.challengeNo + "</h3><br><hr><br>";

    dialogHTML += "<h4>Submitted by</h4><br>";
    dialogHTML += "<label>Firstname:</label>";
    dialogHTML += "<input type='text' value='" + submission.firstname + "' disabled><br>";

    dialogHTML += "<label>Lastname:</label>";
    dialogHTML += "<input type='text' value='" + submission.lastname + "' disabled><br>";

    dialogHTML += "<label>Email:</label>";
    dialogHTML += "<input type='text' value='" + submission.email + "' disabled><br>";
               
    dialogHTML += "<label>Phone:</label>";
    dialogHTML += "<input type='text' value='" + submission.phone + "' disabled><br><br><hr><br>";

    dialogHTML += "<label>Photo1:</label>";
    dialogHTML += "<div id='cntDialogSubmissionsEdit_photo1'>" + photo1 + "</div><br>";

    dialogHTML += "<label>Photo2:</label>";
    dialogHTML += "<div id='cntDialogSubmissionsEdit_photo2'>" + photo2 + "</div><br>";
             
    dialogHTML += "</div>";
            
    $('#dialogChallengePhoto').html( dialogHTML );
    $('#dialogChallengePhoto').dialog(  {   title           : title,
                                            modal           : true,
                                            width           : "600px",
                                            height          : "auto",
                                            dialogClass     : "dynamicDialogSubmissionsEdit",
                                            buttons         : [ { text: "Close", click: function() 
                                                                                        { 
                                                                                            submissionsServerModule.doAjaxRetrieveSubmissions();
                                                                                            $(this).dialog("close"); 
                                                                                        } } ] 
                                        });
                           

} //submissionsEditDialog

// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
// Pagination - Submissions
// --------------------------------------------------------------------------
function updateSubmissionsPagination( page )
{
    // hide all the rows in the table
    $('table#tblListingSubmissions tbody').children("tr").addClass( 'hide' );

    // show only the rows in the visible page
    var nStartVisibleRow  = (page - 1) * MAX_SUBMISSION_PAGE_ITEMS + 1;
    var nEndVisibleRow    = Math.min( submissionsListing.length, nStartVisibleRow + MAX_SUBMISSION_PAGE_ITEMS - 1 );

    var nthChildString = "tr:nth-child(n+" + nStartVisibleRow + "):nth-child(-n+" + nEndVisibleRow + ")";

    $('table#tblListingSubmissions tbody').children( nthChildString ).removeClass( 'hide' );

    window.scrollTo(0, document.querySelector(".listingSubmissions").offsetTop);    // scroll back up to the top

} // updateSubmissionsPagination

// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
// Users
// --------------------------------------------------------------------------
var usersListing;

// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
// userServerModule - AJAX function calls
//---------------------------------------------------------------------------------------------
var userServerModule = ( function() 
{

    //---------------------------------------------------------------------------------------------
    //---------------------------------------------------------------------------------------------
    function doAjaxUpdateUser( idUser )
    {
        //console.log("doAjaxUpdateUser");

        $(".ajaxLoader").css("display", "block");

        var userType = $('#userViewUserType').val();
        var url      = "ajaxScripts/ajaxUserController.php";
        var sendData = 'action=update-usertype' +
                        "&idUser="          + idUser    +
                        "&userType="        + userType  ;

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnUpdateUserError( data, idUser, userType );     }
                                else                                                { ajaxReturnUpdateUserSuccess( data, idUser, userType );   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxUpdateUser

    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers for doAjaxUpdateUser
    //---------------------------------------------------------------------------------------------

    function ajaxReturnUpdateUserError( data, 
                                        idUser,
                                        userType )
    {
        writeAdminError( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnDeleteUserError

    function ajaxReturnUpdateUserSuccess(   data,
                                            idUser,
                                            userType )
    {
        writeAdminSuccess( 1, stripMessageFromAjaxData( data )  );
        updateUserInListing( idUser, userType );


    } // ajaxReturnUpdateUserSuccess



    //---------------------------------------------------------------------------------------------
    //---------------------------------------------------------------------------------------------
    function doAjaxDeleteUser( idUser )
    {
        //console.log("doAjaxRegisterChallenge");

        $(".ajaxLoader").css("display", "block");

        var url      = "ajaxScripts/ajaxUserController.php";
        var sendData = 'action=delete-user' +
                        "&idUser="          + idUser;

        //alert(dataSend );
        $.ajax({
            type        :   'POST',
            url         :   url,
            data        :   sendData,
            crossDomain :   true,
            success     :   function( data ) 
                            {
                                if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnDeleteUserError( data, idUser );     }
                                else                                                { ajaxReturnDeleteUserSuccess( data, idUser );   }
                                $(".ajaxLoader").css("display", "none");
                            }
        });

    } // doAjaxDeleteUser

    //---------------------------------------------------------------------------------------------
    // AJAX Return Helpers for doAjaxDeleteUser
    //---------------------------------------------------------------------------------------------

    function ajaxReturnDeleteUserError( data, 
                                        idUser )
    {
        writeAdminError( 1, stripMessageFromAjaxData( data )  );

    } // ajaxReturnDeleteUserError

    function ajaxReturnDeleteUserSuccess(   data,
                                            idUser )
    {
        writeAdminSuccess( 1, stripMessageFromAjaxData( data )  );

        deleteUserFromListing( idUser );

        $('#fldsetViewUsers .listingUsers').removeClass( 'hide' );
        $('#fldsetViewUsers .inputWrapper').addClass( 'hide' );

    } // ajaxReturnDeleteUserSuccess


    // --------------------------------------------------------------------------
    return {
        doAjaxUpdateUser : doAjaxUpdateUser,
        doAjaxDeleteUser : doAjaxDeleteUser
    }

}()); // userServerModule 

//---------------------------------------------------------------------------------------------
// updateUserInListing
//---------------------------------------------------------------------------------------------
function updateUserInListing(   idUser,
                                userType )
{
    var user = usersListing.filter( function( user ) { return user.idUser == idUser; } );
    user[0].userType = userType;
    // no need since isn't display in table - rebuildUsersListingTable(); 

} // updateUserInListing


//---------------------------------------------------------------------------------------------
// deleteUserFromListing
//---------------------------------------------------------------------------------------------
function deleteUserFromListing( idUser )
{
    // update the data
    //
    var iUser  = 0;
    var isDone      = false;
    for( ; !isDone && (iUser < usersListing.length); iUser++ )
    {
        if( usersListing[ iUser ].idUser == idUser )
        {
            usersListing.splice( iUser, 1 );
            isDone = true;
        }
    }
    rebuildUsersListingTable();

} // deleteUserFromListing

//---------------------------------------------------------------------------------------------
function rebuildUsersListingTable()
{
    var tbodyRows   = "";

    for( iRow in usersListing )
    {
        var hideClass       = ( iRow >= MAX_USER_PAGE_ITEMS )? "hide" : "";

        tbodyRows += "<tr id='trUser" + usersListing[iRow].idUser + "' class='hoverHighlight " + hideClass + "'>";
        tbodyRows += "    <td>"         + usersListing[iRow].firstname  +  "</td>";
        tbodyRows += "    <td>"         + usersListing[iRow].lastname   + "</td>";
        tbodyRows += "    <td>"         + usersListing[iRow].email      + "</td>";
        tbodyRows += "    <td>"         + usersListing[iRow].phone      + "</td>";
        tbodyRows += "</tr>";
    }

    $('table#tblListingUsers tbody').html( tbodyRows );

    // reset the page on the pagination to 1
    // 
    var nPages = Math.ceil( usersListing.length  / MAX_USER_PAGE_ITEMS );
    $('#usersPagination.pagination').jqPagination( 'option', 'max_page', nPages );
    $('#usersPagination.pagination').jqPagination( 'option', 'current_page', 1 );

    if ( nPages <= 1 ) { $('#usersPagination.pagination').addClass( 'hide' ); }


} // rebuildUsersListingTable


// --------------------------------------------------------------------------
// Pagination - Users
// --------------------------------------------------------------------------
function viewUsersPagination( page )
{
    // hide all the rows in the table
    $('table#tblListingUsers tbody').children("tr").addClass( 'hide' );

    // show only the rows in the visible page
    var nStartVisibleRow  = (page - 1) * MAX_USER_PAGE_ITEMS + 1;
    var nEndVisibleRow    = Math.min( usersListing.length, nStartVisibleRow + MAX_USER_PAGE_ITEMS - 1 );

    var nthChildString = "tr:nth-child(n+" + nStartVisibleRow + "):nth-child(-n+" + nEndVisibleRow + ")";

    $('table#tblListingUsers tbody').children( nthChildString ).removeClass( 'hide' );

    window.scrollTo(0, document.querySelector(".listingUsers").offsetTop);    // scroll back up to the top

} // updateUsersPagination


// --------------------------------------------------------------------------
// Reset tableSortDirections
// --------------------------------------------------------------------------
function resetTableSortDirections()
{
    tableSortDirections.challengeType   = -1,   // for table listing sort directions. 
    tableSortDirections.dateAndType     = -1,   // 
    tableSortDirections.year            = -1;   //  1 = ascending
    tableSortDirections.month           = -1;   // -1 = descending
    tableSortDirections.day             = -1;
    tableSortDirections.status          = -1;

    tableSortDirections.firstname       = -1;
    tableSortDirections.lastname        = -1;
    tableSortDirections.email           = -1;
    
} // resetTableSortDirections
