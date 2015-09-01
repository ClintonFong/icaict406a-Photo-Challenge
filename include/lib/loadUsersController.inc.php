<?php

// loadUsersController.inc.php
// 
// by Clinton - Jan, 2015
//

session_start();

require_once    'common.inc.php';
require_once    'class.basicDB.inc.php';

//---------------------------------------------------------------------------------------------
class c_userStruct
{
    public $idUser      = -1;
    public $firstname   = '';
    public $lastname    = ''; 
    public $email       = ''; 
    public $phone       = '';
    public $userType    = '';

    function __displayAttributes()
    {
        echo "idUser= {$this->idUser}<br>";
        echo "firstname = {$this->firstname}<br>";
        echo "lastname = {$this->lastname}<br>";
        echo "email = {$this->email}<br>";
        echo "phone = {$this->phone}<br>";        

    } // __displayAttributes

} // c_challengeStruct

//---------------------------------------------------------------------------------------------
class c_loadUsersController extends c_basicDB
{
    public $users   = array();


	//---------------------------------------------------------------------------------------------
	// constructors 
	//---------------------------------------------------------------------------------------------
	function __construct()
	{
		parent::__construct();
		
        $this->buildUsersList();
		
	} // __construct

	//---------------------------------------------------------------------------------------------
	// destructors
	//---------------------------------------------------------------------------------------------
	function __destruct()
	{
		parent::__destruct();	
		
	} // __destruct


  	//---------------------------------------------------------------------------------------------
    // buildUsersList
    //
    // Description: retrieves a list of users from the database and stores in member variable
	//---------------------------------------------------------------------------------------------
	function buildUsersList()
	{
        assert( isset( $this->dbConnection ) );

        $bSuccess               = FALSE;

        $stmtQuery  = "SELECT idUser, firstname, lastname, email, phone, userType";
        $stmtQuery .= " FROM icaict406a_users";
        $stmtQuery .= " ORDER BY firstname ASC, lastname ASC, email ASC";

        if ($stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
		    if( $bSuccess = $stmt->execute() )
            {
                $stmt->bind_result( $db_idUser, $db_firstname, $db_lastname, $db_email, $db_phone, $db_userType );

                $bSuccess               = TRUE;

		        while( $stmt->fetch() ) 
		        {
                    $objUserStruct              = new c_userStruct();

                    $objUserStruct->idUser      = $db_idUser;
                    $objUserStruct->firstname   = $db_firstname;
                    $objUserStruct->lastname    = $db_lastname;
                    $objUserStruct->email       = $db_email;
                    $objUserStruct->phone       = $db_phone;
                    $objUserStruct->userType    = $db_userType;

                    $this->users[]              = $objUserStruct;
		        } 
            }
	        $stmt->close(); 	// Free resultset 
        }

    	return $bSuccess;

	} // buildUsersList

    
} // c_loadUsersController 

?>

