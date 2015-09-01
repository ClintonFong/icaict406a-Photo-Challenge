<?php

// loadChallengesController.inc.php
//
// Description: Controller for loading Challenges for loadUpdateChallenge.php
// 
// by Clinton - Jan, 2015
//


session_start();

require_once    'common.inc.php';
require_once    'class.basicDB.inc.php';



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

} // c_challengeStruct

//---------------------------------------------------------------------------------------------
class c_loadChallengesController extends c_basicDB
{
    public $challenges   = array();


	//---------------------------------------------------------------------------------------------
	// constructors 
	//---------------------------------------------------------------------------------------------
	function __construct()
	{
		parent::__construct();
		
        $this->buildChallengesList();
		
	} // __construct

	//---------------------------------------------------------------------------------------------
	// destructors
	//---------------------------------------------------------------------------------------------
	function __destruct()
	{
		parent::__destruct();	
		
	} // __destruct


  	//---------------------------------------------------------------------------------------------
    // buildChallengesList
    //
    // Description: retrieves the challenges list from database and stores in member variable
	//---------------------------------------------------------------------------------------------
	function buildChallengesList()
	{
        assert( isset( $this->dbConnection ) );

        $bSuccess               = FALSE;

        $stmtQuery  = "SELECT idChallenge, challengeType, year, month, status, comments";
        $stmtQuery .= " FROM icaict406a_challenges";
        $stmtQuery .= " ORDER BY year DESC, month DESC";

        if ($stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
		    if( $bSuccess = $stmt->execute() )
            {
                $stmt->bind_result( $db_idChallenge, $db_challengeType, $db_year, $db_month, $db_status, $db_comments );

                $bSuccess               = TRUE;

		        while( $stmt->fetch() ) 
		        {
                    $objChallengeStruct                 = new c_challengeStruct();

                    $objChallengeStruct->idChallenge    = $db_idChallenge;
                    $objChallengeStruct->challengeType  = $db_challengeType;
                    $objChallengeStruct->year           = $db_year;
                    $objChallengeStruct->month          = $db_month;
                    $objChallengeStruct->status         = $db_status;
                    $objChallengeStruct->comments       = $db_comments;

                    $this->challenges[]                 = $objChallengeStruct;
		        } 
            }
	        $stmt->close(); 	// Free resultset 
        }

    	return $bSuccess;

	} // buildChallengesList

	//---------------------------------------------------------------------------------------------
    function getStatus( $challenge )
    {
        return ($challenge->status == 0)? "Inactive" : "Active";

    } // getStatus
    
	//---------------------------------------------------------------------------------------------
    function getMonth( $challenge )
    {
        $dateObj   = DateTime::createFromFormat('!m', $challenge->month );  
        return $dateObj->format('F'); 
                        
    } // getMonth

	//---------------------------------------------------------------------------------------------
    function getChallengeType( $challenge )
    {
        switch( $challenge->challengeType )    
        {
            case CT_DAILY: 
                return "Once a Day"; 
                break;
                
            case CT_WEEKLY: 
                return "Once a Weekend"; 
                break;
                
            case CT_MONTHLY: 
                return "Once a Month"; 
                break;
        }
        return "unknown";
        
    } // getChallengeType
    
} // c_loadChallengesController 

?>

