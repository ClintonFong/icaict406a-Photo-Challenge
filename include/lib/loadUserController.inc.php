<?php

// loadUserController.inc.php
// 
// by Clinton - Jan, 2015
//
// this is called from myAccount-pjax.php which is loaded from myAccount.php sitting in the root
// directory, so the path to class.basicDB.inc.php is referenced from the root directory
//

session_start();

require_once    'include/class.basicDB.inc.php';

//---------------------------------------------------------------------------------------------
class c_loadUserController extends c_basicDB
{
    public $idUser      = -1;
    public $firstname   = '';
    public $lastname    = ''; 
    public $email       = ''; 
    public $phone       = '';
    public $userType    = '';
    
    public $isError     = true;



	//---------------------------------------------------------------------------------------------
	// constructors 
	//---------------------------------------------------------------------------------------------
	function __construct( $idUser )
	{
		parent::__construct();
		
        $this->isError = (!$this->getUser( $idUser ));
		
	} // __construct

	//---------------------------------------------------------------------------------------------
	// destructors
	//---------------------------------------------------------------------------------------------
	function __destruct()
	{
		parent::__destruct();	
		
	} // __destruct


  	//---------------------------------------------------------------------------------------------
    // getUser
    //
    // Description: retrieves user details with a given user id
	//---------------------------------------------------------------------------------------------
	function getUser( $idUser )
	{
        assert( isset( $this->dbConnection ) );

        $bSuccess               = FALSE;

        $stmtQuery  = "SELECT idUser, firstname, lastname, email, phone, userType";
        $stmtQuery .= " FROM icaict406a_users";
        $stmtQuery .= " WHERE idUser=?";

        if ($stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
            $idUser = $this->scrubInput( $idUser );
            
            $stmt->bind_param( "i", $idUser );
                
		    if( $bSuccess = $stmt->execute() )
            {
                $stmt->bind_result( $db_idUser, $db_firstname, $db_lastname, $db_email, $db_phone, $db_userType );

                $bSuccess               = TRUE;

		        if( $stmt->fetch() ) 
		        {
                    $this->idUser      = $db_idUser;
                    $this->firstname   = $db_firstname;
                    $this->lastname    = $db_lastname;
                    $this->email       = $db_email;
                    $this->phone       = $db_phone;
                    $this->userType    = $db_userType;
		        } 
            }
	        $stmt->close(); 	// Free resultset 
        }

    	return $bSuccess;

	} // getUser

	//---------------------------------------------------------------------------------------------
    function __displayAttributes()
    {
        echo "idUser= {$this->idUser}<br>";
        echo "firstname = {$this->firstname}<br>";
        echo "lastname = {$this->lastname}<br>";
        echo "email = {$this->email}<br>";
        echo "phone = {$this->phone}<br>";        
        echo "userType = {$this->userType}<br>";        

    } // __displayAttributes
    
    
} // c_loadUserController 

?>

