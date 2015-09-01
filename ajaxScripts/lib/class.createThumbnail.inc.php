<?php

require_once '../include/common.inc.php';


//------------------------------------------------------------------------------------------------------
// Class c_createThumbnailImage
//
// Description: Gets image, crops it and saves it.
//
//------------------------------------------------------------------------------------------------------
class c_createThumbnailImage
{
    public $nOriginalImageWidth     = 0;
    public $nOriginalImageHeight    = 0;
	
	public $strSourceFullFilename   = "";     // the filename and path of original big file
	public $strTargetPath           = "";     // the path of the target file to be saved
    public $strThumbname            = "";
    
	public $typeImage;
	public $imageSource;
    	
	public $bCompletedSuccessfully  = FALSE;
    public $strErrorMessage         = "";

	//------------------------------------------------------------------------------------------------------
	// constructor 
	//------------------------------------------------------------------------------------------------------
	function __construct()
	{	
	} // __construct	

	
	//------------------------------------------------------------------------------------------------------
	// destructor
	//------------------------------------------------------------------------------------------------------
	function __destruct()
	{
        if( $this->imageSource ) { imagedestroy($this->imageSource); }
        
	} //__destruct

	//------------------------------------------------------------------------------------------------------
    
	//------------------------------------------------------------------------------------------------------
    function createThumbnail(   $strSourceFullFilename, 
                                $strTargetPath )
    {
        $isSuccess = false;
        
		$this->strSourceFullFilename	= $strSourceFullFilename;
		$this->strTargetPath		    = $strTargetPath;
        $this->strThumbname             = "";

		$this->imageSource              = $this->getSourceImage( $strSourceFullFilename );
		
		if( $this->imageSource )
		{
    	    $isSuccess = $this->createThumbnailImage();
		}
        
        return $isSuccess;
        
    } // createThumbnail
    
	//------------------------------------------------------------------------------------------------------
	// getSourceImage 
    //
    // Description: Gets the source image
	//------------------------------------------------------------------------------------------------------
	function getSourceImage( $strImageFile )
    {
		list($this->nOriginalImageWidth, $this->nOriginalImageHeight, $this->typeImage) = getimagesize($strImageFile);	// begin by getting the details of the image

/*      - for debugging
        echo "-->strImageFile={$strImageFile}";
        echo "-->nOriginalImageWidth={$nOriginalImageWidth}";
        echo "-->nOriginalImageHeight={$nOriginalImageWidth}";
        echo "-->typeImage={$this->typeImage}";
*/
		// create an image resource for the original
	    $imageSource = NULL;

		switch($this->typeImage)
		{
			case 1:
				$imageSource = @ imagecreatefromgif( $strImageFile );
				if (!$imageSource)  { $this->strErrorMessage = 'Cannot process GIF files.';  }
				break;
			case 2:
				$imageSource = imagecreatefromjpeg( $strImageFile );
				if (!$imageSource)  { $this->strErrorMessage = 'Cannot process JPEG files.';  }
				break;
			case 3:
				$imageSource = imagecreatefrompng( $strImageFile );
				if (!$imageSource)  { $this->strErrorMessage = 'Cannot process PNG files.';  }
				break;
			default:
				$this->strErrorMessage = 'Cannot identify file type.';
				
		} // switch
	
		return $imageSource;

    } // getSourceImage

	//------------------------------------------------------------------------------------------------------
	// create thumbnail image
    // 
    // Description: Creates a thumbnail image and stores in member variable. 
    //              Returns true if successful. 
    //              Error message is stored in $this->strErrorMessage
	//------------------------------------------------------------------------------------------------------
	function createThumbnailImage()
	{

        $bSuccess = FALSE;
		
		
		// calculate the scaling ratio
		if ( ($this->nOriginalImageWidth  <= MAX_THUMBNAIL_WIDTH) && ($this->nOriginalImageHeight <= MAX_THUMBNAIL_HEIGHT) )    { $ratio = 1; }
		elseif ($this->nOriginalImageWidth > $this->nOriginalImageHeight)   													{ $ratio = MAX_THUMBNAIL_WIDTH/$this->nOriginalImageWidth; }
		else    																					                            { $ratio = MAX_THUMBNAIL_HEIGHT/$this->nOriginalImageHeight; }
		
		
		// make sure the image resource is OK
		if (!$this->imageSource)
		{
			$strResult = 'Problem copying original';
		}
		else
		{
			// calculate the dimensions of the thumbnail
			$nThumbWidth    = round($this->nOriginalImageWidth * $ratio);
			$nThumbHeight   = round($this->nOriginalImageHeight * $ratio);
			
			$imageThumb = imagecreatetruecolor( $nThumbWidth, $nThumbHeight );	  // create the resized copy image resource for the thumbnail
			imagecopyresampled($imageThumb, $this->imageSource, 0, 0, 0, 0, $nThumbWidth, $nThumbHeight, $this->nOriginalImageWidth, $this->nOriginalImageHeight);
			
			// strip the extension off the image filename
			$typesImage 	= array('/\.gif$/', '/\.jpg$/', '/\.jpeg$/', '/\.png$/');
			$strName 		= preg_replace($typesImage, '', basename( $this->strSourceFullFilename ));
            
			// save the resized copy
			switch($this->typeImage)
			{
				case 1:
					if (function_exists('imagegif'))
					{
						$bSuccess           = imagegif( $imageThumb, $this->strTargetPath . $strName .'_thb.gif' );
						$this->strThumbname = $strName .'_thb.gif';
					}
					else
					{
						$bSuccess           = imagejpeg($imageThumb, $this->strTargetPath . $strName .'_thb.jpg' );
						$this->strThumbname = $strName .'_thb.jpg';
					}
					break;
				case 2:
					$bSuccess           = imagejpeg($imageThumb, $this->strTargetPath . $strName .'_thb.jpg' );
					$this->strThumbname = $strName .'_thb.jpg';
					break;
				case 3:
					$bSuccess           = imagepng($imageThumb, $this->strTargetPath . $strName . '_thb.png');
					$this->strThumbname = $strName .'_thb.png';
			}
			
              
            
			if (!$bSuccess) { $this->strErrorMessage = "Problem creating thumbnail"; }
            
			// remove the image resources from memory
			imagedestroy($imageThumb);
		}
	
        return $bSuccess;

	} // createThumbnailImage

	
	
	//---------------------------------------------------------------------------------------------
	// Debugging functions
	//---------------------------------------------------------------------------------------------
	function __toString()
	{
        echo "In __toString";
		return "<br />
				nOriginalImageWidth   	= " . $this->nWidth . "<br />
				nOriginalImageHeight  	= " . $this->nHeight . "<br />
				strOriginalFileAndPath	= " . $this->strSourceFullFilename . "<br />
				strTargetFilename		= " . $this->strTargetFilename . "<br />
			";
	
	} // __toString

} // class c_createThumbnailImage



?>

