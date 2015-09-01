<?php
session_start();

require_once    '../include/class.basicDB.inc.php';

//---------------------------------------------------------------------------------------------
class c_userStruct
{
    public $idUser          = -1;
    public $userType        = -1;
    public $firstname       = ''; 
    public $lastname        = ''; 
    public $email           = '';
    public $phone           = '';

} // c_userStruct

//---------------------------------------------------------------------------------------------
class c_ajaxLoginController extends c_basicDB
{
    public $users           = array();


	//---------------------------------------------------------------------------------------------
	// constructors 
	//---------------------------------------------------------------------------------------------
	function __construct()
	{
		parent::__construct();
		
		
	} // __construct

	//---------------------------------------------------------------------------------------------
	// destructors
	//---------------------------------------------------------------------------------------------
	function __destruct()
	{
		parent::__destruct();	
		
	} // __destruct

    
	//---------------------------------------------------------------------------------------------
    // isLoginValid
    //
    // Description: Validates login details
	//---------------------------------------------------------------------------------------------
	function isLoginValid( $email,
                           $password )
	{
        assert( isset( $this->dbConnection ) );

        $bLoginSuccessful = FALSE;

        $whereField = (is_numeric($email))? 'idUser' : 'email';

		// $stmtQuery  = "SELECT member_id, firstname, password FROM members WHERE email='{$loginEmail}'";
        // usage of prepare and bind is more secure rather than straight query
        
        $stmtQuery  = "SELECT idUser, userType, firstname, lastname, password, email, phone";
        $stmtQuery .= " FROM icaict406a_users";
        $stmtQuery .= " WHERE {$whereField}=?";

        if ($stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {

            $bindParamType = (is_numeric($email))? 'i' : 's';
            $email = $this->scrubInput( $email );
            $stmt->bind_param( $bindParamType, $email );

		    if( $bSuccess = $stmt->execute())
            {
                $stmt->bind_result( $db_idUser, $db_userType, $db_firstname, $db_lastname, $db_password, $db_email, $db_phone );

		        if( $stmt->fetch() ) 
		        {
//                    echo $db_member_id;
//                    echo "password={$db_password}: db_password={$db_password}";
                    $sha256Password =  hash('sha256', $password);

			        if( $db_password == $sha256Password )
                    {
                        $bLoginSuccessful = TRUE;

                        $user               = new c_userStruct();

                        $user->idUser       = $db_idUser;
                        $user->userType     = $db_userType;
                        $user->firstname    = $db_firstname;
                        $user->lastname     = $db_lastname;
                        $user->email        = $db_email;
                        $user->phone        = $db_phone;

                        $this->users[]      = $user;
                    }
		        } 
            }
	        $stmt->close(); 	// Free resultset 
        }

    	return $bLoginSuccessful;

	} // isLoginValid


    //---------------------------------------------------------------------------------------------
    // flagLoggedIn
    //
    // Description: Flags the database that this user has successfully logged in 
	//---------------------------------------------------------------------------------------------
	function flagLoggedIn( $idUser )
	{
        //echo "In flagLoggedIn";
        assert( isset( $this->dbConnection) );

        $bSuccess   = FALSE;
		$stmtQuery  = "UPDATE icaict504a_users SET isLoggedIn='" . LOGGED_IN . "' WHERE idUser=?";

        if( $stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            $userID = $this->scrubInput( $idUser );
            $stmt->bind_param("i", $idUser );
            $bSuccess = $stmt->execute();
   	        $stmt->close(); 	// Free resultset 
        }
		return $bSuccess;
    
	} // flagLoggedIn

    //---------------------------------------------------------------------------------------------
    // flagLoggedOut
    //
    // Description: Flags the database that this user has successfully logged out
	//---------------------------------------------------------------------------------------------
	function flagLoggedOut( $idUser )
	{
        //echo "In flagLoggedOut";
        assert( isset( $this->dbConnection) );

        $bSuccess   = FALSE;
		$stmtQuery  = "UPDATE icaict406a_users SET isLoggedIn='" . LOGGED_OUT . "' WHERE idUser=?";

        if( $stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            $idUser = $this->scrubInput( $idUser );
            $stmt->bind_param("i", $idUser );
            $bSuccess = $stmt->execute();
	        $stmt->close(); 	// Free resultset 
        }
		return $bSuccess;

	} // flagLoggedOut

   	//---------------------------------------------------------------------------------------------
    // isUserLoggedIn
    //
    // Description: returns true if the member is logged in, otherwise false
	//---------------------------------------------------------------------------------------------
	function isUserLoggedIn( $idUser )
	{
        assert( isset( $this->dbConnection ) );

        $bUserLoggedIn = FALSE;

		$stmtQuery  = "SELECT idUser, userType, firstname, lastname, isLoggedIn";
        $stmtQuery .= " FROM icaict504a_users WHERE idUser=?";

        if( $stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            $idUser = $this->scrubInput( $idUser );
            $stmt->bind_param("i", $idUser );

		    if( $bSuccess = $stmt->execute())
            {
                $stmt->bind_result( $db_idUser, $db_userType, $db_firstname, $db_lastname, $db_isLoggedIn );

		        if ( $stmt->fetch() ) 
		        {
			        if ( $db_isLoggedIn == LOGGED_IN )
                    {
                        $bUserLoggedIn      = TRUE;

                        $user               = new c_userStruct();

                        $user->idUser       = $db_idUser;
                        $user->userType     = $db_userType;
                        $user->firstname    = $db_firstname;
                        $user->lastname     = $db_lastname;

                        $this->users[]      = $user;
                    }
		        } 
            }
	        $stmt->close(); 	// Free resultset 
        }
    	return $bUserLoggedIn;

	} // isUserLoggedIn


	//---------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
    // debugging tools
	//---------------------------------------------------------------------------------------------
    function __displayAttributes()
    {
        echo "<br>
            idUser = {$this->idUser}<br>
            userType = {$this->userType}<br>
            firstname = {$this->firstname}<br>
            lastname = {$this->lastname}<br>
            email = {$this->email}<br>
            phone = {$this->phone}<br>
            <br>
            ";

    } // __displayAttributes
    
} // class c_ajaxLoginController

//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
// Link to the outside world - the view/controller that called this ajax controller
//-------------------------------------------------------------------------------------

$strResponseStatus  = "";
$strResponseMessage = "Request Undefined";
$strResponseData    = "";


$objAjaxLoginController = new c_ajaxLoginController();


// get the post variables
//
$action     = (isset($_POST['action']))? $_POST['action']           : '';
$email      = (isset($_POST['email']))? $_POST['email']             : '';
$password   = (isset($_POST['password']))? $_POST['password']       : '';

$idUser     = (isset($_POST['idUser']))? $_POST['idUser']           : '';

/*
$firstname  = (isset($_POST['firstname']))? $_POST['firstname']     : '';
$lastname   = (isset($_POST['lastname']))? $_POST['lastname']       : '';
$phone      = (isset($_POST['phone']))? $_POST['phone']             : '';

$userType   = (isset($_POST['userType']))? $_POST['userType']       : '';
*/

// decide what action to take depending on the client request
//

switch ( $action )
{
	case "sign-in" :	// handles the sign-in request
        if( $objAjaxLoginController->isLoginValid( $email, $password ) )
        {
            $objAjaxLoginController->flagLoggedIn( $objAjaxLoginController->users[0]->idUser );

            // set session and cookie variables
            //
            $_SESSION['icaict406a-idUser']      = $objAjaxLoginController->users[0]->idUser; 
            $usersName = $objAjaxLoginController->users[0]->firstname . " " . $objAjaxLoginController->users[0]->firstname;
            
            // cookies will be set by the client
            //
            //setcookie( "icaict406a_usersName", $usersName ); 
            //setcookie( "icaict406a_userType",  $objAjaxLoginController->users[0]->userType ); 

            //$strJSON = "{ \"idUser\" : \"{$objAjaxLoginController->idUser}\", \"userType\" : \"{$objAjaxLoginController->userType}\", \"firstname\" : \"{$objAjaxLoginController->firstname}\", \"lastname\" : \"{$objAjaxLoginController->lastname}\", \"email\" : \"{$objAjaxLoginController->email}\", \"phone\" : \"{$objAjaxLoginController->phone}\" }";
            $strResponseData    = json_encode( $objAjaxLoginController->users[0] );
            $strResponseStatus  = "Success";
            $strResponseMessage = "Successfully Signed-In";
        }
        else
        {
            $strResponseStatus  = "Failure";
            $strResponseMessage = "Sign-In failed";
        }
        break;


	case "is-signed-in" :	// handles the is-signed-in request
        if( $idUser == '' ) { $idUser = (isset($_SESSION['icaict406a-idUser']))?  $_SESSION['icaict406a-idUser'] : ''; }

        if( !$objAjaxLoginController->isUserLoggedIn( $idUser ) )   
        { 
            $strResponseStatus  = "Failure"; 
            $strResponseMessage = "User {$objAjaxLoginController->users[0]->firstname} {$objAjaxLoginController->users[0]->lastname} is NOT Signed-In";
        }
        else
        {
            //$strJSON = "{ \"idUser\" : \"{$objAjaxLoginController->idUser}\", \"userType\" : \"{$objAjaxLoginController->userType}\", \"firstname\" : \"{$objAjaxLoginController->firstname}\", \"lastname\" : \"{$objAjaxLoginController->lastname}\" }";
            $strResponseData    = json_encode( $objAjaxLoginController->users[0] );
            $strResponseStatus  = "Success";
            $strResponseMessage = "User {$objAjaxLoginController->firstname} {$objAjaxLoginController->lastname} is Signed-In";
        }
        break;

	case "signed-out" :	// handles the signed-out request
        if( $objAjaxLoginController->flagLoggedOut( $idUser ) )
        {
            // clear all session variables
            //
            session_unset();
            session_destroy();
            session_write_close();
            setcookie(session_name(),'',0,'/');
            session_regenerate_id(true);

            $strResponseStatus  = "Success";
            $strResponseMessage = "Log out successful";
        }
        break;


	default:
		$strResponseMessage = "Unknown request";		
		
} // switch


$strResponse  = "<status>{$strResponseStatus}</status>";
$strResponse .= "<message>{$strResponseMessage}</message>";
$strResponse .= "<data><![CDATA[{$strResponseData}]]></data>";
$strPackage   = "<package>{$strResponse}</package>";
echo $strPackage;


?>
