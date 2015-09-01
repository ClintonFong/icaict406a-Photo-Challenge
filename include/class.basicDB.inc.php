<?php

require_once 'common.inc.php';

class c_basicDB
{
	protected static $dbConnection;		// db connection
    public $errorMessage = '';          // errorMessage if any would be saved here

	//---------------------------------------------------------------------------------------------
	// constructors 
	//---------------------------------------------------------------------------------------------
	function __construct( $dbConnection = '' )
	{
        if(isset($dbConnection) && ($dbConnection != '' ))  { $this->dbConnection = $dbConnection;  }
        else                                                { $this->connectDB();                   }
			
	} // __construct
	
	
	//---------------------------------------------------------------------------------------------
	
	//---------------------------------------------------------------------------------------------
	function __destruct()
	{

		$this->closeDB();
		
	} // __destruct

	//---------------------------------------------------------------------------------------------
	// connectDB
	//---------------------------------------------------------------------------------------------
	function connectDB()
	{
//      echo "In connectDB()";
        if( !isset($this->dbConnection) || ($this->dbConnection == ''))
        {
		    $this->dbConnection = new mysqli (DB_SERVER, USER_NAME, PASSWORD, DATABASE);
		
            if( $this->dbConnection->connect_errno )
            {
                $this->errorMessage = "Connection to database failed " . $this->dbConnection->connect_errno;
                trigger_error( $this->errorMessage );
            }
        }
		return $this->dbConnection;
				
	} // connectDB

	//---------------------------------------------------------------------------------------------
	// closeDB
	//---------------------------------------------------------------------------------------------
	function closeDB()
	{
		if ( isset($this->dbConnection)  )
		{ 
            $this->dbConnection->close();
		}
	
	} // closeDB


	//---------------------------------------------------------------------------------------------
	// getDBConnection
	//---------------------------------------------------------------------------------------------
	function getDBConnection()
	{
	    return $this->dbConnection;

	} // getDBConnection

	//---------------------------------------------------------------------------------------------
    // srubInput 
    //
    // Description: scrubs down input value elimaate possible sql injection
	//---------------------------------------------------------------------------------------------
    function scrubInput($value)
    {
        
        //if( get_magic_quotes_gpc() )    { $value = stripslashes($value); }                                           // Stripslashes


        $value = $this->dbConnection->real_escape_string( $value );

        //if (!is_numeric($value)) { $value = "'" . $value . "'";  } // Quote if not a number

        return $value;

    } // scrubInput

   	//---------------------------------------------------------------------------------------------
    // setLikeBindParam
    //
    //  Description: puts one % if $paramInput is blanks and %{$paramInput}% if not
   	//---------------------------------------------------------------------------------------------
    function setLikeBindParam( $paramInput )
    {
        $paramOutput = $this->scrubInput( $paramInput );
        return ($paramOutput == "")? "%" : "%" . $paramOutput. "%";

    } // setLikeBindParam


} // class c_BasicDB

?>