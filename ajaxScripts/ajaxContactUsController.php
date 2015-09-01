<?php
//header("Content-type: text/xml");

require_once    '../include/common.inc.php';
require_once    '../include/PHPMailer_5.2.4/class.phpmailer.php';



// --------------------------------------------------------------------------------------------------------------
// sendEmailMessage
// --------------------------------------------------------------------------------------------------------------
function sendEmailMessage(  $name,
                            $email,
                            $message )
{
    assert( isset( $name  ) );
    assert( isset( $email ) );
    assert( isset( $message ) );
    
    $isSuccess = false;
        
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
            
    $mail->AddAddress( CONTACT_US_EMAIL, "ContactUs - Barbara Jones Incorporated" );
            
            
    //$mail->AddCC("info@clintonfong.com", "Clinton Fong");

    $mail->Subject  = "Contact Us Message from Barbara Jones Incorporated Website";
    $msg            = "Name: {$name} <br />";
	$msg           .= "Email: {$email}<br />";
	$msg           .= "Message: {$message}<br>";

    $mail->Body    = $msg;

	// Mail it
    if( $mail->Send() ) { $isSuccess = true; }
    else                { /* $strResult .= " Mailer error: {$mail->ErrorInfo}"; */ }

    return $isSuccess;

} // sendEmailMessage


// --------------------------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------------------------
// decide what action to take depending on the client request

$strResponseStatus  = "Request Undefined";
$strResponseMessage = "";
$strResponseData    = "";

$action     =  ( isset( $_REQUEST['action'] ) )?    $_REQUEST['action']     : "";
$name       =  ( isset( $_REQUEST['name'] ) )?      $_REQUEST['name']       : "";
$email      =  ( isset( $_REQUEST['email'] ) )?     $_REQUEST['email']      : "";
$message    =  ( isset( $_REQUEST['message'] ) )?   $_REQUEST['message']    : "";
    
switch ( $action )
{
	case "send-message" :	// handles the forgot password request
        if( sendEmailMessage( $name, $email, $message ) )
        {
            $strResponseStatus  = "Success";
            $strResponseMessage = "Message Sent Successfully";
        }
        else
        {
            $strResponseStatus  = "Failure";
            $strResponseMessage = "Sending Message Unsuccessful";
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