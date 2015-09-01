<?php

session_start();

require_once    '../include/class.basicDB.inc.php';

//---------------------------------------------------------------------------------------------
class c_submissionStruct
{
    public $idChallengePhoto    = '';

    public $firstname           = '';
    public $lastname            = '';
    public $email               = '';
    public $phone               = '';
    
    public $challengeType       = '';
    
    public $year                = '';
    public $month               = '';
    public $challengeNo         = ''; // also referred to as day

    public $photoFilename1      = '';
    public $photoFilename2      = '';

    public $photoThumbnail1     = '';
    public $photoThumbnail2     = '';
    
}

//---------------------------------------------------------------------------------------------
class c_ajaxSubmissionsController extends c_basicDB
{
    public $submissions = array();
    
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
    // getSubmissionsToViewAndDelete
    //
    // Description: returns submissions with given search parameters for viewing and possibly deleting
	//---------------------------------------------------------------------------------------------
	function getSubmissionsToViewAndDelete( $objQuerySubmissionStruct )
	{
        assert( isset( $this->dbConnection ) );

        $isSuccess   = false;

        $bindTypes  = "";
        $params     = array();

        $stmtQuery   = "SELECT idChallengePhoto, firstname, lastname, email, phone, challengeType, year, month, challengeNo, photoFilename1, photoFilename2, photoThumbnail1, photoThumbnail2";
        $stmtQuery  .= " FROM icaict406a_challenge_photos, icaict406a_challenges, icaict406a_users";
        $stmtQuery  .= " WHERE icaict406a_users.idUser = icaict406a_challenge_photos.userID";
        $stmtQuery  .= " AND icaict406a_challenges.idChallenge  = icaict406a_challenge_photos.challengeID";

        $stmtQuery .= $this->setQueryParam( $objQuerySubmissionStruct->challengeType, " AND challengeType= ?",   FALSE, "i", $bindTypes, $params );
        $stmtQuery .= $this->setQueryParam( $objQuerySubmissionStruct->month,         " AND month= ?",           FALSE, "i", $bindTypes, $params );
        $stmtQuery .= $this->setQueryParam( $objQuerySubmissionStruct->year,          " AND year= ?",            FALSE, "i", $bindTypes, $params );
        $stmtQuery .= $this->setQueryParam( $objQuerySubmissionStruct->firstname,     " AND firstname like ?",   TRUE,  "s", $bindTypes, $params );
        $stmtQuery .= $this->setQueryParam( $objQuerySubmissionStruct->lastname,      " AND lastname like ?",    TRUE,  "s", $bindTypes, $params );

        $stmtQuery  .= " ORDER BY year DESC, month DESC, challengeNo DESC";

        if( $stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            if( $bindTypes != "" )
            {
                $bindNames[] = $bindTypes;
                foreach ($params as $key => $value ) 
                {
                    $bindName       = "bind" . $key;
                    $$bindName      = $value;
                    $bindNames[]    = &$$bindName;
                }
                call_user_func_array( array($stmt, "bind_param"), $bindNames );
            }
            
		    if( $bSuccess = $stmt->execute())
            {
                $stmt->bind_result( $db_idChallengePhoto, $db_firstname, $db_lastname, $db_email, $db_phone, $db_challengeType, $db_year, $db_month, $db_challengeNo, $db_photoFilename1, $db_photoFilename2, $db_photoThumbnail1, $db_photoThumbnail2 );
		        
                while( $stmt->fetch() ) 
		        {
                    $objSubmission                      = new c_submissionStruct();
                    $objSubmission->idChallengePhoto    = $db_idChallengePhoto;
                    $objSubmission->firstname           = $db_firstname;
                    $objSubmission->lastname            = $db_lastname;
                    $objSubmission->email               = $db_email;
                    $objSubmission->phone               = $db_phone;
                    $objSubmission->challengeType       = $db_challengeType;
                    $objSubmission->month               = $db_month;
                    $objSubmission->year                = $db_year;
                    $objSubmission->challengeNo         = $db_challengeNo;
                    $objSubmission->photoFilename1      = ( $db_photoFilename1  != '' )? PHOTO_DIR . $db_photoFilename1 : ''; 
                    $objSubmission->photoFilename2      = ( $db_photoFilename2  != '' )? PHOTO_DIR . $db_photoFilename2 : ''; 
                    $objSubmission->photoThumbnail1     = ( $db_photoThumbnail1 != '' )? THUMBNAIL_PHOTO_DIR . $db_photoThumbnail1 : ''; 
                    $objSubmission->photoThumbnail2     = ( $db_photoThumbnail2 != '' )? THUMBNAIL_PHOTO_DIR . $db_photoThumbnail2 : ''; 

                    $this->submissions[]                = $objSubmission;
		        } 
            }
	        $stmt->close(); 	// Free resultset 
        }
    	return $bSuccess;

	} // getSubmissions
  
	//---------------------------------------------------------------------------------------------
    function setQueryParam( $objAttributeSearchParamValue,
                            $stmtQueryConditionStringToAdd,
                            $isLike,
                            $bindType,
                            &$bindTypes,
                            &$params )
    {
        $stmtQueryConditionStringToAddRet = "";
        if( $objAttributeSearchParamValue != "" )   
        { 
            $stmtQueryConditionStringToAddRet   = $stmtQueryConditionStringToAdd;
            $bindTypes                         .= $bindType; 
            $params[]                           = ( $isLike )? $this->setLikeBindParam( $objAttributeSearchParamValue ) : $this->scrubInput( $objAttributeSearchParamValue ); 
        }
        return $stmtQueryConditionStringToAddRet;

    } // setQueryParam
    

    
    
	//---------------------------------------------------------------------------------------------
    // getSubmissionsToViewAndDelete
    //
    // Description: returns submissions with given search parameters for viewing and possibly deleting
	//---------------------------------------------------------------------------------------------
	function getPhotoToDelete( $objQuerySubmissionStruct )
	{
        assert( isset( $this->dbConnection ) );
    } //     
    
} // c_ajaxSubmissionsController

//---------------------------------------------------------------------------------------------

$strResponseStatus  = "Request Undefined";
$strResponseMessage = "";
$strResponseData    = "";

$action             = (isset($_POST['action']))? $_POST['action'] : '';

$objQuerySubmission                 = new c_submissionStruct();
$objQuerySubmission->challengeType  = (isset($_POST['challengeType']))? $_POST['challengeType'] : '';
$objQuerySubmission->firstname      = (isset($_POST['firstname']))?     $_POST['firstname']     : '';
$objQuerySubmission->lastname       = (isset($_POST['lastname']))?      $_POST['lastname']      : '';
$objQuerySubmission->month          = (isset($_POST['month']))?         $_POST['month']         : '';
$objQuerySubmission->year           = (isset($_POST['year']))?          $_POST['year']          : '';
        
switch( $action )
{
    case "retrieve-submissions":
        
        $objAjaxSubmissionsController = new c_ajaxSubmissionsController();
        if( $objAjaxSubmissionsController->getSubmissionsToViewAndDelete( $objQuerySubmission ) )
        {
            $strResponseStatus  = "Success"; 
            $strResponseData    = json_encode( $objAjaxSubmissionsController->submissions );
        }
        else
        {
            $strResponseStatus  = "Failure"; 
            $strResponseMessage = "Retrieving of submissions failed.";
        }
        break;
       

}

$strResponse  = "<status>{$strResponseStatus}</status>";
$strResponse .= "<message>{$strResponseMessage}</message>";
$strResponse .= "<data><![CDATA[{$strResponseData}]]></data>";
$strPackage   = "<package>{$strResponse}</package>";
echo $strPackage;


?>