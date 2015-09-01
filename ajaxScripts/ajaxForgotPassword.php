<?php
//header("Content-type: text/xml");

require_once    '../include/class.basicDB.inc.php';
require_once    '../include/PHPMailer_5.2.4/class.phpmailer.php';


//---------------------------------------------------------------------------------------------
class c_ajaxForgotPasswordController extends c_basicDB
{
    public $usersName;
    
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

    // --------------------------------------------------------------------------------------------------------------
    // --------------------------------------------------------------------------------------------------------------
    // resetPasswordDB
    // --------------------------------------------------------------------------------------------------------------
    function resetPasswordDB( $email,
                              &$newPassword )
    {
        assert( isset( $email) );
        assert( isset( $this->dbConnection ) );
        
        $isSuccess = false;    
    
        if( !$this->dbConnection->connect_errno )
        {
            
		    $stmtQuery = "UPDATE icaict406a_users SET password=? WHERE email=?";

            if ($stmt = $this->dbConnection->prepare( $stmtQuery ) )
            {
                $email = $this->scrubInput( $email );
                
                $newPassword    = $this->generateTemporaryPassword();
                $sha256Password = hash('sha256', $newPassword);
                
                $stmt->bind_param( "ss", $sha256Password, $email );
		        $isSuccess = ( $stmt->execute() && ( $stmt->affected_rows > 0 ) );
            }
	    }
	
        return $isSuccess;

    } // resetPasswordDB


    // --------------------------------------------------------------------------------------------------------------
    // sendEmailPasswordChanged
    // --------------------------------------------------------------------------------------------------------------
    function sendEmailPasswordChanged( $email,
                                       $newPassword )
    {
        assert( isset( $email) );
        assert( isset( $this->dbConnection ) );
    
        $isSuccess = false;
        
        if( $this->usersName )
        {

            $mail             = new PHPMailer();

            //$mail->IsSMTP();                                            // telling the class to use SMTP
            $mail->SMTPDebug    = 0;                                    // enables SMTP debug information (for testing)
            //$mail->SMTPAuth     = TRUE;                                 // enable SMTP authentication
            //$mail->SMTPSecure   = "ssl";                                // sets the prefix to the server
            //$mail->Host         = "smtp.gmail.com";                     // sets GMAIL as the SMTP server
            //$mail->Port         = 465;                                  // set the SMTP port for the GMAIL server
            $mail->IsHTML(TRUE);

            // gmail account to use to send the email
            //$mail->Username     = "fongclinton.mail.gateway@gmail.com"; 
            //$mail->Password     = "Password001";  

            //$mail->SetFrom("fongclinton.mail.gateway@gmail.com");
            //$mail->AddAddress("Sharon.Carrasco@evocca.com.au", "Sharon Carrasco");
            $mail->AddAddress( $email, $this->usersName );
            //$mail->AddCC("info@clintonfong.com", "Clinton Fong");

            $mail->Subject  = "Photo Challenge - Password reset";
            $msg            = "Dear {$this->usersName},<br><br>";
	        $msg           .= "As requested, your new Temporary Password for Sign-in to Barbara Jones Photo Challenge Incorporated website is <span style='color:#78655F'>{$newPassword}</span><br>";
	        $msg           .= "Please change this password as soon as possible when you next Sign-in.<br><br>";
            $msg           .= "Your friendly support team at Barbara Jones Photo Challenge Incorporated";

            $mail->Body    = $msg;

	        // Mail it
            if( $mail->Send() ) { $isSuccess = true; }
            else                { /* $strResult .= " Mailer error: {$mail->ErrorInfo}"; */ }
        }

        return $isSuccess;

    } // sendEmailPasswordChanged

    // --------------------------------------------------------------------------------------------------------------
    // getMemberNameDB
    // --------------------------------------------------------------------------------------------------------------
    function getMemberNameDB( $email, 
                              &$usersName )
    {
        assert( isset( $email) );
        assert( isset( $this->dbConnection ) );

        $isSuccess      = false;

        if( !$dbConnection->connect_errno )
        {
		    $stmtQuery  = "SELECT firstname, lastname FROM icaict406a_users WHERE email=?";

            if ($stmt = $this->dbConnection->prepare( $stmtQuery ) )
            {
                $email = $this->scrubInput( $email );
                $stmt->bind_param( "s", $email );

		        if( $stmt->execute() )
                {
                    $stmt->bind_result( $db_firstname, $db_lastname );
                    if( $stmt->fetch() ) 
                    { 
                        $isSuccess = true; 
                        $this->usersName = $usersName = $db_firstname . " " . $db_lastname; 
                    }
                }
                $stmt->close();
            }
	    }

        return $isSuccess;

    } // getMemberNameDB

    // --------------------------------------------------------------------------------------------------------------
    // generateTemporaryPassword
    // --------------------------------------------------------------------------------------------------------------
    function generateTemporaryPassword() 
    {
        $alphabet       = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $alphaLength    = strlen( $alphabet ) - 1;
        $newPassword    = "";

        for ($i = 0; $i < TEMPORARY_PASSWORD_LENGTH; $i++) 
        {
            $n = rand(0, $alphaLength);
            $newPassword .= $alphabet[$n];
        }
        return $newPassword;

    } // generateTemporaryPassword

} // class c_ajaxForgotPasswordController extends c_basicDB

// --------------------------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------------------------
// decide what action to take depending on the client request

$strResponseStatus  = "Request Undefined";
$strResponseMessage = "";
$strResponseData    = "";

$action =  ( isset( $_REQUEST['action'] ) )?  $_REQUEST['action'] : "";
$email  =  ( isset( $_REQUEST['email'] ) )?   $_REQUEST['email']  : "";
    
    
switch ( $action )
{
	case "forgot-password" :	// handles the forgot password request
        $strResponseStatus  = "Failure";
        
        if ( $email != "" )
        {
            $objForgotPasswordController = new c_ajaxForgotPasswordController();
            
            if( !$objForgotPasswordController->getMemberNameDB( $email, $usersName ) )
            {
                $strResponseData .= "The email specified is not Registered."; 
            }
            else if( !$objForgotPasswordController->resetPasswordDB( $email, $newPassword ) )
            {
                $strResponseData .= "Trouble resetting password. Please contact us."; 
            }
            else if( !$objForgotPasswordController->sendEmailPasswordChanged( $email, $newPassword ) )
            {
                $strResponseData .= "Error emailing new password. Please contact us."; 
            }
            else           
            {
                $strResponseStatus  = "Success";
                $strResponseMessage = "Email with the new password has been sent to you.";
            }
        }
        else
        {
            $strResponseMessage = "Email NOT specified";
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