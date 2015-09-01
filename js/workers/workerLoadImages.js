// Worker thread
/*
var loadImages = ( function()
{
    var imgCameraObjs       = new Array();
    var imgBackgroundObjs   = new Array();

    //--------------------------------------------
    function loadCameraImages()
    {
        var cameraImageFiles = ["images/camera1.png",
                                "images/camera2.png",
                                "images/camera3.png",
                                "images/camera4.png" ];

        for( i=0; i <= cameraImageFiles.length; i++ )
        {
            imgCameraObjs[i]        = new Image();
            imgCameraObjs[i].src    = cameraImageFiles[i]; 
        }

    } // loadBackgroundImages


    //--------------------------------------------
    function loadBackgroundImages()
    {
        var backgroundImageFiles = ["images/background1.jpg",
                                    "images/background2.jpg",
                                    "images/background3.jpg",
                                    "images/background4.jpg",
                                    "images/background5.jpg",
                                    "images/background6.jpg",
                                    "images/background7.jpg",
                                    "images/background8.jpg" ];

        for( i=0; i <= backgroundImageFiles.length; i++ )
        {
            imgBackgroundObjs[i]        = new Image();
            imgBackgroundObjs[i].src    = backgroundImageFiles[i]; 
        }

    } // loadBackgroundImages

}());
*/
// wait for loadImage


function messageHandler( event ) 
{
    var messageSent = event.data;
    var messageReturned = "Hello " + messageSent + " from a separate thread!";

/*
    loadImages.loadCameraImages();
    loadImages.loadBackgroundImages();

    self.postMessage (  {   "imgCameraObjs"        : loadImages.imgCameraObjs,
                            "imgBackgroundObjs"    : loadImages.imgBackgroundObjs 
                        } );
*/
    // Posting back the message to the main page
    this.postMessage( messageReturned );

} // messageHandler

this.addEventListener('message', messageHandler, false);

