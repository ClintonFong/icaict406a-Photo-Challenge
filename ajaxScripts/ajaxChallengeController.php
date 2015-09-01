<?php
session_start();

require_once    '../include/class.basicDB.inc.php';



//---------------------------------------------------------------------------------------------
class c_challengeStruct
{
    public $idChallenge     = -1;
    public $challengeType   = -1;
    public $year            = ''; 
    public $month           = ''; 
    public $status          = '';
    public $comments        = '';

    function __displayAttributes()
    {
        echo "idChallenge = {$this->idChallenge}<br>";
        echo "challengeType = {$this->challengeType}<br>";
        echo "year = {$this->year}<br>";
        echo "month = {$this->month}<br>";
        echo "status = {$this->status}<br>";        
        echo "comments = {$this->comments}<br>";

    } // __displayAttributes

} // c_userStruct

//---------------------------------------------------------------------------------------------
class c_ajaxChallengeController extends c_basicDB
{
    public $challenges      = array();
    public $isChangesMade   = false;

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
    // registerNewChallenge
    //
    // Description: registers new challenge
	//---------------------------------------------------------------------------------------------
	function registerNewChallenge( $objChallengeStructInput )
	{
        assert( isset( $this->dbConnection) );
        
        $bRegisterSuccessful = FALSE;

        if( !$this->dbConnection->connect_errno )
        {
            $year           = $this->scrubInput( $objChallengeStructInput->year );
            $month          = $this->scrubInput( $objChallengeStructInput->month );
            $challengeType  = $this->scrubInput( $objChallengeStructInput->challengeType );
            
		    $stmtQuery      = "SELECT count(*) as num_challenges FROM icaict406a_challenges";
            $stmtQuery     .= " WHERE year='{$year}' AND month='{$month}' AND challengeType='{$challengeType}'";

     	    if( $resultQuery = $this->dbConnection->query( $stmtQuery ) )
            {
		        $row = $resultQuery->fetch_array( MYSQL_ASSOC );
                if( $row['num_challenges'] == 0 )
                {
		            $stmtQuery   = "INSERT INTO icaict406a_challenges (challengeType, year, month, status, comments) VALUES ";
                    $stmtQuery  .= " (?, ?, ?, ?, ? )";

                    if( $stmt = $this->dbConnection->prepare( $stmtQuery ) )
                    {
                        $status         = $this->scrubInput( $objChallengeStructInput->status );
                        $comments       = $this->scrubInput( $objChallengeStructInput->comments );

                        $stmt->bind_param("iiiis",  $challengeType,
                                                    $year,
                                                    $month,
                                                    $status,
                                                    $comments );


		                $bRegisterSuccessful    = ( $stmt->execute() && ($stmt->affected_rows > 0)  );
                        $this->isChangesMade    = $stmt->affected_rows;

                        if( $bRegisterSuccessful )
                        {
                            // store attributes
                            //
                            $challenge                  = new c_challengeStruct();

                            $challenge->idUser          = $this->dbConnection->insert_id;
                            $challenge->challengeType   = $challengeType;
                            $challenge->month           = $month;
                            $challenge->year            = $year;
                            $challenge->status          = $status;
                            $challenge->comments        = $comments;

                            $this->challenges[]         = $challenge;
                        }
                    }
                }              
                $resultQuery->close();
            }
        }    

        return $bRegisterSuccessful;

	} // registerNewChallenge

    //---------------------------------------------------------------------------------------------
    // updateChallengeDetails
    //
    // Description: update challenge details
	//---------------------------------------------------------------------------------------------
	function updateChallengeDetails( $objChallengeStructInput )
    {
        //echo "updateUserPersonalDetails";
        assert( isset( $this->dbConnection) );

        $bSuccess    = FALSE;
        
        if( !$this->dbConnection->connect_errno )
        {
            $idChallenge    = $this->scrubInput( $objChallengeStructInput->idChallenge );
            $year           = $this->scrubInput( $objChallengeStructInput->year );
            $month          = $this->scrubInput( $objChallengeStructInput->month );
            $challengeType  = $this->scrubInput( $objChallengeStructInput->challengeType );
            
		    $stmtQuery      = "SELECT count(*) as num_challenges FROM icaict406a_challenges";
            $stmtQuery     .= " WHERE idChallenge <> '{$idChallenge}' AND year='{$year}' AND month='{$month}' AND challengeType='{$challengeType}'";

     	    if( $resultQuery = $this->dbConnection->query( $stmtQuery ) )
            {
		        $row = $resultQuery->fetch_array( MYSQL_ASSOC );
                if( $row['num_challenges'] == 0 )
                {
                    $stmtQuery   = "UPDATE icaict406a_challenges set challengeType=?, year=?, month=?, status=?, comments=?";
		            $stmtQuery  .= " WHERE idChallenge=?";

                    if( $stmt = $this->dbConnection->prepare( $stmtQuery ) )
                    {
                        $status         = $this->scrubInput( $objChallengeStructInput->status );
                        $comments       = $this->scrubInput( $objChallengeStructInput->comments );
            
                        $stmt->bind_param("iiiisi", $challengeType,
                                                    $year,
                                                    $month,
                                                    $status,
                                                    $comments,
                                                    $idChallenge );

                        $bSuccess               = $stmt->execute();
                        $this->isChangesMade    = $stmt->affected_rows;
   	                    $stmt->close(); 	// Free resultset 
                    }
                }
            }
        }
		return $bSuccess;


    } // updateChallengeDetails

    //---------------------------------------------------------------------------------------------
    // deleteChallenge
    //
    // Description: deletes a challenge for a given idChallenge
	//---------------------------------------------------------------------------------------------
	function deleteChallenge( $idChallenge )
    {
        assert( isset( $this->dbConnection) );

        $this->dbConnection->autocommit( false );    // begin transaction
        
        $isSuccess = $this->deleteChallengeDB( $idChallenge );
        $isSuccess = ( $isSuccess )? $this->deleteChallengePhotos( $idChallenge ) : false;
        
        if( $isSuccess )    { $this->dbConnection->commit();    }
        else                { $this->dbConnection->rollback();  }
        
        return $isSuccess;
        
    } // deleteChallenge
    
    //---------------------------------------------------------------------------------------------
    // deleteChallengeDB
    //
    // Description: deletes a challenge for a given idChallenge from the database
	//---------------------------------------------------------------------------------------------
	function deleteChallengeDB( $idChallenge )
    {
        assert( isset( $this->dbConnection) );

        $bSuccess    = FALSE;
        
        if( !$this->dbConnection->connect_errno )
        {
            $stmtQuery   = "DELETE FROM icaict406a_challenges WHERE idChallenge=?";

            if( $stmt = $this->dbConnection->prepare( $stmtQuery ) )
            {
                $idChallenge    = $this->scrubInput( $idChallenge );

                $stmt->bind_param("i", $idChallenge );
                $bSuccess = ($stmt->execute() && ( $stmt->affected_rows > 0 ) );
   	            $stmt->close(); 	// Free resultset 
            }
        }
		return $bSuccess;

    } // deleteChallengeDB

    //---------------------------------------------------------------------------------------------
    // deleteChallengePhotos
    //
    // Description: deletes all challenge photo files from the uploaded directory and 
    //              then makes a call to remove all entries from the database
	//---------------------------------------------------------------------------------------------
	function deleteChallengePhotos( $idChallenge )
	{
        $bSuccess = $this->deleteChallengePhotoFiles( $idChallenge );
        $bSuccess = ( $bSuccess )? $this->deleteChallengePhotosDB( $idChallenge ) : false;
        
        return $bSuccess;
            
    } // deleteChallengePhotos
    
    //---------------------------------------------------------------------------------------------
    // deleteChallengePhotoFiles
    //
    // Description: deletes all challenge photo files from the uploaded directory and 
    //              then makes a call to remove all entries from the database
	//---------------------------------------------------------------------------------------------
	function deleteChallengePhotoFiles( $idChallenge )
	{
        assert( isset( $this->dbConnection ) );
        assert( isset( $this->objChallengeStruct ) && ( $this->objChallengeStruct != "" ) ); 

        $bSuccess = false;

        $stmtQuery   = "SELECT idChallengePhoto, photoFilename1, photoFilename2 FROM icaict406a_challenge_photos";
        $stmtQuery  .= " WHERE challengeID=?";
        
        if ($stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            $idChallenge    = $this->scrubInput( $idChallenge );
            
            $stmt->bind_param( "i", $idChallenge );

		    if( $bSuccess = $stmt->execute())
            {
                $stmt->bind_result( $db_idChallengePhoto, $db_photoFilename1, $db_photoFilename2 );

                $bSuccess = true;
		        while( $bSuccess && $stmt->fetch() ) 
		        {
                    if( isset($db_photoFilename1) && ( $db_photoFilename1 != "" ) )
                    {
                        $photoFilename1    = "../" . PHOTO_DIR . $db_photoFilename1; 
                        $bSuccess =  ( file_exists( $photoFilename1 ) )? unlink( $photoFilename1 ) : true;  
                    }
                    
                    if( $bSuccess && isset($db_photoFilename2) && ( $db_photoFilename2 != "" ) )
                    {
                        $photoFilename2    = "../" . PHOTO_DIR . $db_photoFilename2; 
                        $bSuccess =  ( file_exists( $photoFilename2 ) )? unlink( $photoFilename2 ) : true; 
                    }

		        } 
            }
	        $stmt->close(); 	// Free resultset 
        }
    	return $bSuccess;
       
        
    } // deleteChallengePhotoFiles   
    
    //---------------------------------------------------------------------------------------------
    // deleteChallengePhotosDB
    //
    // Description: deletes all challenge photos from the database for a given idChallenge
	//---------------------------------------------------------------------------------------------
	function deleteChallengePhotosDB( $idChallenge )
	{
        assert( isset( $this->dbConnection) );

        $bSuccess   = FALSE;
		$stmtQuery  = "DELETE FROM icaict406a_challenge_photos WHERE challengeID=?";

        if( $stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            $idChallengePhoto = $this->scrubInput( $idChallenge );
            $stmt->bind_param( "i", $idChallenge );
            $bSuccess = $stmt->execute();
   	        $stmt->close(); 	// Free resultset 
        }
		return $bSuccess;
    
	} // deleteChallengePhotosDB
    
    
} // c_ajaxChallengeController


//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
// Link to the outside world - the view/controller that called this ajax controller
//-------------------------------------------------------------------------------------

$strResponseStatus  = "";
$strResponseMessage = "Request Undefined";
$strResponseData    = "";


$objAjaxChallengeController = new c_ajaxChallengeController();

// get the post variables
//
$action                                  = (isset($_POST['action']))? $_POST['action']               : '';

$objChallengeStructInput                 = new c_challengeStruct();

$objChallengeStructInput->idChallenge    = (isset($_POST['idChallenge']))? $_POST['idChallenge']     : '';
$objChallengeStructInput->challengeType  = (isset($_POST['challengeType']))? $_POST['challengeType'] : '';
$objChallengeStructInput->year           = (isset($_POST['year']))? $_POST['year']                   : '';
$objChallengeStructInput->month          = (isset($_POST['month']))? $_POST['month']                 : '';
$objChallengeStructInput->status         = (isset($_POST['status']))? $_POST['status']               : '';
$objChallengeStructInput->comments       = (isset($_POST['comments']))? $_POST['comments']           : '';


switch ( $action )
{
	case "register-challenge" :	// handles the register request
        if( $objAjaxChallengeController->registerNewChallenge( $objChallengeStructInput ) )
        {
            $strResponseData    = $objAjaxChallengeController->challenges[0]->idChallenge;
            $strResponseStatus  = "Success";
            $strResponseMessage = "Successfully Registered";
        }
        else
        {
            $strResponseStatus  = "Failure";
            $strResponseMessage = "Register Unsuccessful:: Month/Year/Challenge Type combination already registered";
        }
        break;

    case "update-challenge"   :   // updates user personal details
    	if( $objAjaxChallengeController->updateChallengeDetails( $objChallengeStructInput ) )
        {
            $strResponseStatus  = "Success";
            $strResponseMessage = ( $objAjaxChallengeController->isChangesMade > 0 )? "Challenge Details Updated Successfully" : "Nothing changed...";
        }
        else
        {
            $strResponseStatus  = "Failure";
            $strResponseMessage = "Update Unsuccessful :: Month/Year/Challenge Type combination already registered";
        }
        break;

    case "delete-challenge"   :   // deletes user
    	if( $objAjaxChallengeController->deleteChallenge( $objChallengeStructInput->idChallenge ) )
        {
            $strResponseStatus  = "Success";
            $strResponseMessage = "Challenge deleted Successfully";
        }
        else
        {
            $strResponseStatus  = "Failure";
            $strResponseMessage = "Delete Unsuccessful";
        }
        break;


} // switch

$strResponse  = "<status>{$strResponseStatus}</status>";
$strResponse .= "<message>{$strResponseMessage}</message>";
$strResponse .= "<data><![CDATA[{$strResponseData}]]></data>";
$strPackage   = "<package>{$strResponse}</package>";
echo $strPackage;

?>

