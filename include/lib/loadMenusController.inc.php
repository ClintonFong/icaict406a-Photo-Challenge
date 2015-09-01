<?php

// loadMenusController.inc.php
//
// Description: Controller for loading Menus dynamically. used from navMenu.php
//
// by Clinton - Jan, 2015
//

require_once    'include/class.basicDB.inc.php';


//---------------------------------------------------------------------------------------------
class c_menuItemStruct
{
    public $idChallenge     = -1;
    public $challengeType   = -1;
    public $year            = ''; 
    public $month           = ''; 
    public $status          = '';

    function __displayAttributes()
    {
        echo "idChallenge = {$this->idChallenge}<br>";
        echo "challengeType = {$this->challengeType}<br>";
        echo "year = {$this->year}<br>";
        echo "month = {$this->month}<br>";
        echo "status = {$this->status}<br>";        

    } // __displayAttributes

} // c_menuItemStruct

//---------------------------------------------------------------------------------------------
class c_loadMenusController extends c_basicDB
{
    public $menuItemsOnceADay       = array();
    public $menuItemsOnceAWeekend   = array();
    public $menuItemsOnceAMonth     = array();

    public $htmlMenuOnceADay        = "";
    public $htmlMenuOnceAWeekend    = "";
    public $htmlMenuOnceAMonth      = "";

   
	//---------------------------------------------------------------------------------------------
	// constructors 
	//---------------------------------------------------------------------------------------------
	function __construct()
	{
		parent::__construct();
		
        $this->buildPhotoChallengeMenu();
		
	} // __construct

	//---------------------------------------------------------------------------------------------
	// destructors
	//---------------------------------------------------------------------------------------------
	function __destruct()
	{
		parent::__destruct();	
		
	} // __destruct

    //---------------------------------------------------------------------------------------------
    // buildPhotoChallengeMenu
    //
    // Description: builds photo a day menu
	//---------------------------------------------------------------------------------------------
	function buildPhotoChallengeMenu()
	{
        $this->buildPhotoADayMenu();
        $this->buildPhotoAWeekendMenu();
        $this->buildPhotoAMonthMenu();
        
    } // buildPhotoChallengeMenu

  	//---------------------------------------------------------------------------------------------
    // buildPhotoADayMenu
    //
    // Description: builds photo a day menu
	//---------------------------------------------------------------------------------------------
	function buildPhotoADayMenu()
	{
        $this->loadPhotoMenu( CT_DAILY );
        
        if( count( $this->menuItemsOnceADay ) > 0 )
        {
		    $this->htmlMenuOnceADay  = "<li id='photoADayChallenge' class='months-in-year'>Photo a Day<br/>Challenge";
            $this->htmlMenuOnceADay .= "<ul><li class='menuItem-how-to-play'>How to Play</li>";
            $this->htmlMenuOnceADay .= "<li class='year'>{$this->menuItemsOnceADay[0]->year}</li>";
            
            foreach ($this->menuItemsOnceADay as $key => $menuItem) 
            { 
                // write the month
                $this->htmlMenuOnceADay .= "<li id='menuItem-photoADay-month{$menuItem->month}' class='days-in-month' idChallenge='{$menuItem->idChallenge}'>" . $this->getMonth( $menuItem->month ); 
                
                // write the days in the month
                $this->htmlMenuOnceADay .= "<ul>";
                $nDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $menuItem->month, $menuItem->year);
                for ( $iDay = 1; $iDay <= $nDaysInMonth; $iDay++) 
                { 
                    $this->htmlMenuOnceADay .= "<li>Day {$iDay}</li>"; 
                }
                $this->htmlMenuOnceADay .= "</ul></li>";
                    
            }
            $this->htmlMenuOnceADay .= "</ul></li>";
        }
       
    } // buildPhotoADayMenu()

  	//---------------------------------------------------------------------------------------------
    // buildPhotoAWeekendMenu
    //
    // Description: builds photo a weekend menu
	//---------------------------------------------------------------------------------------------
	function buildPhotoAWeekendMenu()
	{
        $this->loadPhotoMenu( CT_WEEKLY );
        $weeksOfMonth = [ "1", "2", "3", "4" ];
       
        if( count( $this->menuItemsOnceAWeekend ) > 0 )
        {
            $this->htmlMenuOnceAWeekend  = "<li id='photoAWeekendChallenge' class='months-in-year'>Photo a Weekend<br/>Challenge";
            $this->htmlMenuOnceAWeekend .= "<ul><li class='menuItem-how-to-play'>How to Play</li>";
            $this->htmlMenuOnceAWeekend .= "<li class='year'>{$this->menuItemsOnceAWeekend[0]->year}</li>";

            foreach ($this->menuItemsOnceAWeekend as $key => $menuItem) 
            { 
                // write month
                $this->htmlMenuOnceAWeekend .= "<li id='menuItem-photoAWeekend-month{$menuItem->month}' class='weeks-in-month' idChallenge='{$menuItem->idChallenge}'>"  . $this->getMonth( $menuItem->month );
                
                // write weeks in month
                $this->htmlMenuOnceAWeekend .= " <ul>";
                foreach ($weeksOfMonth as $week ) { $this->htmlMenuOnceAWeekend .= "<li>Week {$week}</li>"; }
                $this->htmlMenuOnceAWeekend .= "</ul></li>";
            }            
            $this->htmlMenuOnceAWeekend .= "</ul></li>";
        }      
        
    } // buildPhotoAWeekendMenu

  	//---------------------------------------------------------------------------------------------
    // buildPhotoAMonthMenu
    //
    // Description: builds photo a month menu
	//---------------------------------------------------------------------------------------------
	function buildPhotoAMonthMenu()
	{
        $this->loadPhotoMenu( CT_MONTHLY );
       
        if( count( $this->menuItemsOnceAMonth ) > 0 )
        {
            $this->htmlMenuOnceAMonth  = "<li id='photoAMonthChallenge'   class='months-in-year'>Photo a Month<br/>Challenge";
            $this->htmlMenuOnceAMonth .= "<ul><li class='menuItem-how-to-play'>How to Play</li>";
            $this->htmlMenuOnceAMonth .= "<li class='year'>{$this->menuItemsOnceAMonth[0]->year}</li>";

            foreach ($this->menuItemsOnceAMonth as $key => $menuItem) 
            { 
                $this->htmlMenuOnceAMonth .= "<li id='menuItem-photoAMonth-month{$menuItem->month}' class='month-selection' idChallenge='{$menuItem->idChallenge}'>"  . $this->getMonth( $menuItem->month ) . " </li>";
            }            
            $this->htmlMenuOnceAMonth .= "</ul></li>";
        }      
        
    } // buildPhotoAMonthMenu
    
    
  	//---------------------------------------------------------------------------------------------
    // loadPhotoMenu
    //
    // Description: loads data from the database for photo given the challenge type
	//---------------------------------------------------------------------------------------------
	function loadPhotoMenu( $challengeType )
	{
        assert( isset( $this->dbConnection ) );

        $bSuccess               = FALSE;

        $stmtQuery  = "SELECT idChallenge, challengeType, year, month, status";
        $stmtQuery .= " FROM icaict406a_challenges";
        $stmtQuery .= " WHERE challengeType={$challengeType}";
        $stmtQuery .= " AND   status=1";
        $stmtQuery .= " AND   year = ( SELECT max(year) FROM icaict406a_challenges";
        $stmtQuery .=  "               WHERE challengeType={$challengeType} AND status=1 )";
        $stmtQuery .= " ORDER BY year DESC, month ASC";

        if ($stmt = $this->dbConnection->prepare( $stmtQuery ) )
        {
		    if( $bSuccess = $stmt->execute() )
            {
                $stmt->bind_result( $db_idChallenge, $db_challengeType, $db_year, $db_month, $db_status );

                $bSuccess               = TRUE;

		        while( $stmt->fetch() ) 
		        {
                    $objMenuItemStruct                  = new c_menuItemStruct();

                    $objMenuItemStruct->idChallenge     = $db_idChallenge;
                    $objMenuItemStruct->challengeType   = $db_challengeType;
                    $objMenuItemStruct->year            = $db_year;
                    $objMenuItemStruct->month           = $db_month;
                    $objMenuItemStruct->status          = $db_status;

                    switch( $challengeType )
                    {
                        case CT_DAILY:      $this->menuItemsOnceADay[]      = $objMenuItemStruct; break;
                        case CT_WEEKLY:     $this->menuItemsOnceAWeekend[]  = $objMenuItemStruct; break;
                        case CT_MONTHLY:    $this->menuItemsOnceAMonth[]    = $objMenuItemStruct; break;
                        default:
                            // should not come here....
                    }
                    
		        } 
            }
	        $stmt->close(); 	// Free resultset 
        }

    	return $bSuccess;

	} // loadPhotoMenu

  	//---------------------------------------------------------------------------------------------
    // displayChallengesMenu
    //
    //---------------------------------------------------------------------------------------------
    function displayChallengesMenu()    
    {
        echo $this->htmlMenuOnceADay;
        echo $this->htmlMenuOnceAWeekend;
        echo $this->htmlMenuOnceAMonth;
        
    } // displayChallengesMenu
    
	//---------------------------------------------------------------------------------------------
    function getStatus( $challenge )
    {
        return ($challenge->status == 0)? "Inactive" : "Active";

    } // getStatus
    
	//---------------------------------------------------------------------------------------------
    function getMonth( $month )
    {
        $dateObj   = DateTime::createFromFormat('!m', $month );  
        return $dateObj->format('F'); 
                        
    } // getMonth

   
} // c_loadMenusController 

?>

