<?php

session_start();

require_once    '../include/class.basicDB.inc.php';
require_once    './lib/class.createThumbnail.inc.php';

//---------------------------------------------------------------------------------------------
class c_challengeStruct
{
    public $idChallenge     = '';
    public $idUser          = '';
    public $challengeType   = '';
    public $year            = '';
    public $month           = '';
    public $challengeNo     = '';
}

//---------------------------------------------------------------------------------------------
class c_photoStruct
{
    public $idChallengePhoto = '';
    public $photoFilename1   = '';
    public $photoFilename2   = '';
    public $photoThumbnail1  = '';
    public $photoThumbnail2  = '';
}

//---------------------------------------------------------------------------------------------
class c_ajaxChallengePhotosController extends c_basicDB
{
    private $challengeTypes      = array("daily", "weekend", "monthly"); // to be used for filename
        
    public  $objChallengeStruct  = "";
    public  $photos              = array();
    public  $photoThumbs         = array();
  

	//---------------------------------------------------------------------------------------------
	// constructors 
	//---------------------------------------------------------------------------------------------
	function __construct( $objChallengeStruct = "" )
	{
		parent::__construct();
        
        $this->objChallengeStruct   = $objChallengeStruct;
        
        $path = "../" . PHOTO_DIR ;
		$this->createPath( $path );

        $thumbpath = "../" . THUMBNAIL_PHOTO_DIR ;
		$this->createPath( $thumbpath );
        
	} // __construct

	//---------------------------------------------------------------------------------------------
	// destructors
	//---------------------------------------------------------------------------------------------
	function __destruct()
	{
		parent::__destruct();	
		
	} // __destruct


	//---------------------------------------------------------------------------------------------
    // getChallengePhotos
    //
    // Description: gets car photos for a given id
	//---------------------------------------------------------------------------------------------
	function getChallengePhotos()
	{
        assert( isset( $this->dbConnection ) );
        assert( isset( $this->objChallengeStruct ) && ( $this->objChallengeStruct != "" ) ); 

        $bSuccess               = FALSE;

        $stmtQuery   = "SELECT idChallengePhoto, photoFilename1, photoFilename2, photoThumbnail1, photoThumbnail2";
        $stmtQuery  .= " FROM icaict406a_challenge_photos";
        $stmtQuery  .= " WHERE userID=?";
        $stmtQuery  .= " AND challengeID=?";
        $stmtQuery  .= " AND challengeNo=?";

        if ($stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            $idUser         = $this->scrubInput( $this->objChallengeStruct->idUser );
            $idChallenge    = $this->scrubInput( $this->objChallengeStruct->idChallenge );
            $challengeNo    = $this->scrubInput( $this->objChallengeStruct->challengeNo );
            
            $stmt->bind_param( "iii", $idUser, $idChallenge, $challengeNo );

		    if( $bSuccess = $stmt->execute())
            {
                $stmt->bind_result( $db_idChallengePhoto, $db_photoFilename1, $db_photoFilename2, $db_photoThumbnail1, $db_photoThumbnail2 );

		        if( $stmt->fetch() ) 
		        {
                    $objPhoto                    = new c_photoStruct();
                    $objPhoto->idChallengePhoto  = $db_idChallengePhoto;
                    $objPhoto->photoFilename1    = ( $db_photoFilename1  != '' )? PHOTO_DIR . $db_photoFilename1 : ''; 
                    $objPhoto->photoFilename2    = ( $db_photoFilename2  != '' )? PHOTO_DIR . $db_photoFilename2 : ''; 
                    $objPhoto->photoThumbnail1   = ( $db_photoThumbnail1 != '' )? THUMBNAIL_PHOTO_DIR . $db_photoThumbnail1 : ''; 
                    $objPhoto->photoThumbnail2   = ( $db_photoThumbnail2 != '' )? THUMBNAIL_PHOTO_DIR . $db_photoThumbnail2 : ''; 

                    $this->photos[]              = $objPhoto;
		        } 
            }
	        $stmt->close(); 	// Free resultset 
        }
    	return $bSuccess;

	} // getChallengePhotos
  
    
    //------------------------------------------------------------------------------------------------------
    /** 
     * recursively create a long directory path
     * taken and modified from : http://stackoverflow.com/questions/2303372/create-a-folder-if-it-doesnt-already-exist
     */
    function createPath( $path ) 
    {
        if( is_dir($path) ) { return true; }
        $prev_path  = substr( $path, 0, strrpos($path, '/', -2) + 1 );
        $return     = $this->createPath( $prev_path );
        return ( $return && is_writable($prev_path) ) ? mkdir( $path ) : false;
    
    } // createPath

    //------------------------------------------------------------------------------------------------------
    function checkUploadPhotoForErrors(  $file, 
                                        &$strResponseMessage )
    {
	    $bIsOk = TRUE;
	
	    if (!isset( $file ))
	    {
		    $bIsOk = FALSE;
		    $strResponseMessage .= "The uploaded file error...please try again";
	    }
	    elseif( $file['error'] == UPLOAD_ERR_INI_SIZE )
	    {
		    $bIsOk= FALSE;
		    $strResponseMessage .= "The uploaded file  exceeds the upload_max_filesize directive in php.ini";
	    }
	    elseif( $file['error'] == UPLOAD_ERR_FORM_SIZE )
	    {
		    $bIsOk= FALSE;
		    $strResponseMessage .= "The uploaded file exceeds the maximum suggested file size of 1 megabyte.";
	    }
	    elseif( $file['error'] == UPLOAD_ERR_PARTIAL )
	    {
		    $bIsOk= FALSE;
		    $strResponseMessage .= "The uploaded file was partially uploaded";
	    }
	    elseif( $file['error'] == UPLOAD_ERR_NO_FILE )
	    {
		    $bIsOk= FALSE;
		    $strResponseMessage .= "No File was uploaded";
	    }
	    elseif( $file['error'] == UPLOAD_ERR_NO_TMP_DIR )
	    {
		    $bIsOk= FALSE;
		    $strResponseMessage .= "Missing temporary folder.";
	    }
	
	    return $bIsOk;
			
    } // checkUploadPhotoForErrors

  
    //------------------------------------------------------------------------------------------------------
    function moveUploadPhotoFile( $tagName,
                                  $file, 
                                 &$strResponseMessage )
    {
        assert( isset( $this->objChallengeStruct ) && ( $this->objChallengeStruct != "" ) ); 
        
        $bSuccess   = false;
        
        $fileName   = $file['name'];
        $fileSize   = $file['size'];
        $fileTmp    = $file['tmp_name'];
        $fileType   = $file['type'];   

        $formats    = array( "jpg", "png", "gif", "bmp", "jpeg" );
        $fileExt    = strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) );

        if( in_array( $fileExt, $formats ) )
        {
            // create file name   
            $targetFileName     = $this->objChallengeStruct->idUser . "_" . $this->challengeTypes[ $this->objChallengeStruct->challengeType - 1 ] . $this->objChallengeStruct->challengeNo . "_" . $tagName . "_" . time() . "." . $fileExt; 
            $fileDestination    = "../" . PHOTO_DIR . $targetFileName;

            // move the file and change permissions
            if( move_uploaded_file( $fileTmp, $fileDestination ) )
            {
                chmod( $fileDestination, 0755 );
            }
            
            // create the photo object to pass to insert/update database
            if( count( $this->photos ) == 0 ) 
            { 
                $objPhoto = new c_photoStruct();
                $this->photos[] = $objPhoto;
            }
            
            // save the new filename
            if( $tagName == 'photo1')   { $this->photos[0]->photoFilename1 = $targetFileName; }
            else                        { $this->photos[0]->photoFilename2 = $targetFileName; }
            
            $bSuccess               = true;
        }
        else
        {
            $strResponseMessage .= "File {$fileName} is not an image file";
        }
 
        return $bSuccess;
        
    } // moveUploadPhotoFile


    //------------------------------------------------------------------------------------------------------
    function createPhotoFileThumbnail(  $tagName, 
                                        $objThumbnailCreator, 
                                        $strResponseMessage )
    {
        assert( isset( $objThumbnailCreator ) );
        
        $isSuccess          = false;
        $originalFilename   = "../" . PHOTO_DIR;
        $targetPath         = "../" . THUMBNAIL_PHOTO_DIR;
        
        if( $tagName == 'photo1')   { $originalFilename .= $this->photos[0]->photoFilename1; }
        else                        { $originalFilename .= $this->photos[0]->photoFilename2; }

        $isSuccess = $objThumbnailCreator->createThumbnail( $originalFilename, $targetPath );
        if( !$isSuccess ) { $strResponseMessage = $objThumbnailCreator->strErrorMessage; }

        // save the new filename
        if( $tagName == 'photo1')   { $this->photos[0]->photoThumbnail1 = $objThumbnailCreator->strThumbname; }
        else                        { $this->photos[0]->photoThumbnail2 = $objThumbnailCreator->strThumbname; }
        
        return $isSuccess;
        
    } // createPhotoFileThumbnail
    
    
    //---------------------------------------------------------------------------------------------
    // insertChallengePhoto
    //
    // Description: insert a photo into the database
    //         pre: a call to checkUploadPhotoForErrors & moveUploadPhotoFile with array 
    //              $this->photoFilenames[] populated with filenames of the uploaded files
	//---------------------------------------------------------------------------------------------
	function insertChallengePhoto(  $idUser,
                                    $idChallenge,
                                    $challengeNo,
                                    &$idChallengePhoto )
	{
        assert( isset( $this->dbConnection) );

        $bSuccess   = FALSE;
		$stmtQuery  = "INSERT INTO icaict406a_challenge_photos ( userID, challengeID, challengeNo, photoFilename1, photoFilename2, photoThumbnail1, photoThumbnail2 )";
        $stmtQuery .= " VALUES (?, ?, ?, ?, ?, ?, ?)";

        if( $stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            $idUser         = $this->scrubInput( $idUser );
            $idChallenge    = $this->scrubInput( $idChallenge );
            $challengeNo    = $this->scrubInput( $challengeNo );
            $filename1      = ( isset( $this->photos[0]->photoFilename1) )?  $this->scrubInput( $this->photos[0]->photoFilename1 )  : "";
            $filename2      = ( isset( $this->photos[0]->photoFilename2) )?  $this->scrubInput( $this->photos[0]->photoFilename2 )  : "";
            $thumbnail1     = ( isset( $this->photos[0]->photoThumbnail1) )? $this->scrubInput( $this->photos[0]->photoThumbnail1 ) : "";
            $thumbnail2     = ( isset( $this->photos[0]->photoThumbnail2) )? $this->scrubInput( $this->photos[0]->photoThumbnail2 ) : "";

            $stmt->bind_param("iiissss",    $idUser, 
                                            $idChallenge, 
                                            $challengeNo,
                                            $filename1,
                                            $filename2,
                                            $thumbnail1,
                                            $thumbnail2 );
            $bSuccess           = ( $stmt->execute() && ( $stmt->affected_rows == 1 ) );
            
            $idChallengePhoto   = $this->dbConnection->insert_id;
   	        $stmt->close(); 	// Free resultset 
        }
		return $bSuccess;
    
	} // insertChallengePhoto

    //---------------------------------------------------------------------------------------------
    // updateChallengePhoto
    //
    // Description: updates a challenge photo from the database
    //         pre: a call to checkUploadPhotoForErrors & moveUploadPhotoFile with array 
    //              $this->photoFilenames[] populated with filenames of the uploaded files
	//---------------------------------------------------------------------------------------------
	function updateChallengePhoto( $idChallengePhoto )
	{
        assert( isset( $this->dbConnection) );

        $bSuccess   = FALSE;
        
        $filename1      = ( isset( $this->photos[0]->photoFilename1) )?  $this->scrubInput( $this->photos[0]->photoFilename1 )  : "";
        $filename2      = ( isset( $this->photos[0]->photoFilename2) )?  $this->scrubInput( $this->photos[0]->photoFilename2 )  : "";
        $thumbnail1     = ( isset( $this->photos[0]->photoThumbnail1) )? $this->scrubInput( $this->photos[0]->photoThumbnail1 ) : "";
        $thumbnail2     = ( isset( $this->photos[0]->photoThumbnail2) )? $this->scrubInput( $this->photos[0]->photoThumbnail2 ) : "";

        $stmtQueryFile  = ( $filename1 != "")? "photoFilename1=?, photoThumbnail1=?" : "";
        if( $filename2 != "" )      { $stmtQueryFile .= ($stmtQueryFile == "")? "photoFilename2=?, photoThumbnail2=?" : ", photoFilename2=?, photoThumbnail2=?";  }
        if( $stmtQueryFile == "")   { return false; } // nothing to update
        
        $stmtQuery  = "UPDATE icaict406a_challenge_photos SET ";
        $stmtQuery .= $stmtQueryFile;
        $stmtQuery .= " WHERE idChallengePhoto=?";

        if( $stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            $idChallengePhoto      = $this->scrubInput( $idChallengePhoto );

            if( ( $filename1 != "" ) && ( $filename2 != "" ) )  { $stmt->bind_param( "ssssi", $filename1, $thumbnail1, $filename2, $thumbnail2, $idChallengePhoto ); }
            elseif( $filename1 != "" )                          { $stmt->bind_param( "ssi",   $filename1, $thumbnail1, $idChallengePhoto ); }
            else                                                { $stmt->bind_param( "ssi",   $filename2, $thumbnail2, $idChallengePhoto ); }
            
            $bSuccess = $stmt->execute();
   	        $stmt->close(); 	// Free resultset 
        }
 
		return $bSuccess;

	} // deletePhoto


    //---------------------------------------------------------------------------------------------
    // deleteChallengePhoto
    //
    // Description: unlinks a photo from the file system and deletes it from the database
	//---------------------------------------------------------------------------------------------
	function deleteChallengePhoto( $idChallengePhoto, 
                                   $whichPhoto )
	{
        // if whichPhoto = 0 or empty - delete all photos
        
        $isSuccess = false;
        
        if( $photo = $this->getChallengePhoto( $idChallengePhoto ) )
        {
            // delete the photo file from the filesystem
            if( ( $whichPhoto == 0 ) || ( $whichPhoto == 1 ) )
            {
                $filePath = "../" . PHOTO_DIR . $photo->photoFilename1; 
                $isSuccess =  unlink( $filePath ); 

                $filePath = "../" . THUMBNAIL_PHOTO_DIR . $photo->photoThumbnail1; 
                $isSuccess =  unlink( $filePath ) || $isSuccess; 
            }
            
            if( ( $whichPhoto == 0 ) || ( $whichPhoto == 2 ) )
            {
                $filePath = "../" . PHOTO_DIR . $photo->photoFilename2; 
                $isSuccess =  unlink( $filePath ) || $isSuccess; 
              
                $filePath = "../" . THUMBNAIL_PHOTO_DIR . $photo->photoThumbnail2; 
                $isSuccess =  unlink( $filePath ) || $isSuccess; 
            }
            
            // delete the photo from the database
            if( $isSuccess ) 
            { 
                //if( $whichPhoto == 0 )  { $isSuccess = $this->deleteChallengePhotoDB( $idChallengePhoto );              }
                //else                    { $isSuccess = $this->removeChallengePhotoDB( $idChallengePhoto, $whichPhoto ); }
                $isSuccess = $this->removeChallengePhotoDB( $idChallengePhoto, $whichPhoto ); 
                $isSuccess = $this->deleteChallengePhotoDB( $idChallengePhoto );
            }
        }
        
        return $isSuccess;
        
    } // deleteChallengePhoto
    
    //---------------------------------------------------------------------------------------------
    // getChallengePhoto
    //
    // Description: gets the photo details for a submitted challenge 
	//---------------------------------------------------------------------------------------------
	function getChallengePhoto( $idChallengePhoto )
	{
        assert( isset( $this->dbConnection ) );

        $objPhoto    = false;

        $stmtQuery   = "SELECT idChallengePhoto, photoFilename1, photoFilename2, photoThumbnail1, photoThumbnail2";
        $stmtQuery  .= " FROM icaict406a_challenge_photos";
        $stmtQuery  .= " WHERE idChallengePhoto=?";

        if ($stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            $idChallengePhoto   = $this->scrubInput( $idChallengePhoto );
            
            $stmt->bind_param( "i", $idChallengePhoto );

		    if( $bSuccess = $stmt->execute())
            {
                $stmt->bind_result( $db_idChallengePhoto, $db_photoFilename1, $db_photoFilename2, $db_photoThumbnail1, $db_photoThumbnail2 );

		        if( $stmt->fetch() ) 
		        {
                    $objPhoto                    = new c_photoStruct();
                    $objPhoto->idChallengePhoto  = $db_idChallengePhoto;
                    $objPhoto->photoFilename1    = $db_photoFilename1; 
                    $objPhoto->photoFilename2    = $db_photoFilename2; 
                    $objPhoto->photoThumbnail1   = $db_photoThumbnail1; 
                    $objPhoto->photoThumbnail2   = $db_photoThumbnail2; 
		        } 
            }
	        $stmt->close(); 	// Free resultset 
        }
    	return $objPhoto;
        
    } // getChallengePhoto
    
    //---------------------------------------------------------------------------------------------
    // deleteChallengePhotoDB
    //
    // Description: deletes a photo from the database
	//---------------------------------------------------------------------------------------------
	function deleteChallengePhotoDB( $idChallengePhoto )
	{
        assert( isset( $this->dbConnection) );

        $bSuccess   = FALSE;
		$stmtQuery  = "DELETE FROM icaict406a_challenge_photos";
        $stmtQuery .= " WHERE idChallengePhoto=?";
		$stmtQuery .= " AND ( photoFilename1 IS NULL OR photoFilename1='' )";
        $stmtQuery .= " AND ( photoFilename2 IS NULL OR photoFilename2='' )";
            
        if( $stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            $idChallengePhoto = $this->scrubInput( $idChallengePhoto );
            $stmt->bind_param( "i", $idChallengePhoto );
            $bSuccess = $stmt->execute();
   	        $stmt->close(); 	// Free resultset 
        }
		return $bSuccess;
    
	} // deleteChallengePhotoDB

    //---------------------------------------------------------------------------------------------
    // removeChallengePhotoDB
    //
    // Description: removes a photo from the database yet keeping the record
	//---------------------------------------------------------------------------------------------
	function removeChallengePhotoDB( $idChallengePhoto,
                                     $whichPhoto )
	{
        assert( isset( $this->dbConnection) );

        $bSuccess   = FALSE;
		$stmtQuery  = "UPDATE icaict406a_challenge_photos";
        
        if( $whichPhoto == 1 )  { $stmtQuery .= " SET photoFilename1='', photoThumbnail1=''"; }
        else                    { $stmtQuery .= " SET photoFilename2='', photoThumbnail2=''"; }
        
        $stmtQuery .= " WHERE idChallengePhoto=?";

        if( $stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            $idChallengePhoto = $this->scrubInput( $idChallengePhoto );
            $stmt->bind_param( "i", $idChallengePhoto );
            $bSuccess = $stmt->execute();
   	        $stmt->close(); 	// Free resultset 
        }
		return $bSuccess;
    
	} // removeChallengePhotoDB
    

    
    
} // class c_ajaxChallengePhotosController extends c_basicDB

//---------------------------------------------------------------------------------------------

$strResponseStatus  = "Request Undefined";
$strResponseMessage = "";
$strResponseData    = "";

$action             = (isset($_POST['action']))?            $_POST['action']            : '';
$idChallengePhoto   = (isset($_POST['idChallengePhoto']))?  $_POST['idChallengePhoto']  : '';

$objChallengeStruct                   = new c_challengeStruct();
$objChallengeStruct->idChallenge      = $idChallenge        = (isset($_POST['idChallenge']))?   $_POST['idChallenge']   : '';
$objChallengeStruct->idUser           = $idUser             = (isset($_POST['idUser']))?        $_POST['idUser']        : '';
$objChallengeStruct->challengeNo      = $day                = (isset($_POST['day']))?           $_POST['day']           : '';
$objChallengeStruct->month            = $month              = (isset($_POST['month']))?         $_POST['month']         : '';
$objChallengeStruct->year             = $year               = (isset($_POST['year']))?          $_POST['year']          : '';
$objChallengeStruct->challengeType    = $challengeType      = (isset($_POST['challengeType']))? $_POST['challengeType'] : '';



switch( $action )
{
    case "get-challenge-photos":
        $strResponseStatus   = "Failure"; 
        $objChallengePhotosController  = new c_ajaxChallengePhotosController( $objChallengeStruct );
        if( $objChallengePhotosController->getChallengePhotos() )
        {
            $strResponseStatus   = "Success"; 
            $strResponseData = ( count($objChallengePhotosController->photos) > 0 )? json_encode( $objChallengePhotosController->photos[0] ) : "";   
        }
        break;

    case "upload-photos": 
        $strResponseStatus  = "Success"; 
        
        if( count( $_FILES ) > 0 )
        {        
            $objThumbnailCreator            = new c_createThumbnailImage();
            $objChallengePhotosController   = new c_ajaxChallengePhotosController( $objChallengeStruct );

            // process the files
            //
            foreach( $_FILES as $tagName => $file )
            {   
                if( !$objChallengePhotosController->checkUploadPhotoForErrors( $file, $strResponseMessage ) ) 
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "<br>";
                }
                else if( !$objChallengePhotosController->moveUploadPhotoFile( $tagName, $file, $strResponseMessage ) )
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "<br>";
                }
                else if( !$objChallengePhotosController->createPhotoFileThumbnail( $tagName, $objThumbnailCreator, $strResponseMessage ) )
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "<br>";
                }
                
            } // foreach

            // update the database
            //
            if( $strResponseStatus == "Success"  )
            {
                if( !$objChallengePhotosController->insertChallengePhoto( $idUser, $idChallenge, $day, $idChallengePhoto ) )
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "Database insert failed.";
                }
            }
        }
        else
        {
            $strResponseStatus  = "Failure"; 
            $strResponseMessage = "No File";
        }
        break; // case upload-photos

    case "update-photos": 
        $strResponseStatus  = "Success"; 
        
        if( count( $_FILES ) > 0 )
        {        
            $objThumbnailCreator           = new c_createThumbnailImage();
            $objChallengePhotosController  = new c_ajaxChallengePhotosController( $objChallengeStruct );

            // process the files
            //
            foreach( $_FILES as $tagName => $file )
            {   
                if( !$objChallengePhotosController->checkUploadPhotoForErrors( $file, $strResponseMessage ) ) 
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "<br>";
                }
                else if( !$objChallengePhotosController->moveUploadPhotoFile( $tagName, $file, $strResponseMessage ) )
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "<br>";
                }
                else if( !$objChallengePhotosController->createPhotoFileThumbnail( $tagName, $objThumbnailCreator, $strResponseMessage ) )
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "<br>";
                }
                
            } // foreach

            // update the database
            //
            if( $strResponseStatus == "Success"  )
            {
                if( !$objChallengePhotosController->updateChallengePhoto( $idChallengePhoto ))
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "Database update failed.";
                }
            }
        }
        else
        {
            $strResponseStatus  = "Failure"; 
            $strResponseMessage = "No File";
        }
        break; // case update-photos
        
    case "delete-photo":
        
        $idChallengePhoto   = (isset($_POST['idChallengePhoto']))? $_POST['idChallengePhoto']   : '';
        $whichPhoto         = (isset($_POST['whichPhoto']))? $_POST['whichPhoto']               : '';
       
        $objChallengePhotosController = new c_ajaxChallengePhotosController();

        if( $objChallengePhotosController->deleteChallengePhoto( $idChallengePhoto, $whichPhoto ) )
        {
            $strResponseStatus  = "Success"; 
            $strResponseData    = $idChallengePhoto;
        }
        else
        {
            $strResponseStatus  = "Failure"; 
            $strResponseMessage = "Deletion of Photo failed";
        }
        
        break;

} // switch



$strResponse  = "<status>{$strResponseStatus}</status>";
$strResponse .= "<message>{$strResponseMessage}</message>";
$strResponse .= "<data><![CDATA[{$strResponseData}]]></data>";
$strPackage   = "<package>{$strResponse}</package>";
echo $strPackage;

?>
