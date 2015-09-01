<?php
    session_start();

    require_once 'include/common.inc.php';

    $idUser = ( isset( $_SESSION['icaict406a-idUser'] ) )? $_SESSION['icaict406a-idUser'] : "";
    
    // check if user is an administrator and logged in
    if( ( $idUser == "" ) || !isset( $_COOKIE["icaict406a_userType"] ) || ( $_COOKIE["icaict406a_userType"] != UT_ADMIN ) )  { header( "location: login.php?timedout=1" ); }
    
   
    $title = "Photo Challenge - Home";
    if( $_SERVER["HTTP_X_PJAX"] ): 
	    echo "<title>{$title}</title>";

        include "include/admin-pjax.php"; 

    else: 
?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width">
            <title>Photo Challenge - Admin</title>

            
            <link rel='stylesheet' type='text/css' href='css/font-awesome-4.2.0/css/font-awesome.min.css' />
            <link rel='stylesheet' type='text/css' href='js/jquery-ui-1.11.2.custom/jquery-ui.min.css' />
	        <link rel="stylesheet" type='text/css' href="css/lightbox.css">
	        <link rel="stylesheet" type='text/css' href="css/jqpagination.css">
            <link rel='stylesheet' type='text/css' href='css/main.css' />
            <link rel='stylesheet' type='text/css' href='css/popupForgotPassword.css' />

        </head>
        <body>

            <input id="idUser" type="hidden" value="<?php echo $idUser; ?>" />

            <div class="ajaxLoader"></div>

	        <?php include "include/header.php";	?>

            <section id='pjaxCntMainContent'>
                <?php include "include/admin-pjax.php";	?>
            </section>
            <div id='dialogChallengePhoto' class='dynamicDialog'></div>

	
	        <?php include "include/footer.php";	?>



            <script src="js/jquery-1.11.0.min.js"></script>
            <script src="js/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script>
            <script src="js/jquery.pjax.js"></script>
            <script src="js/jquery.form.js"></script>
            <script src="js/jssor.js"></script>
            <script src="js/jssor.slider.js"></script>
            <script src="js/lightbox.min.js"></script>
            <script src="js/jquery.jqpagination.min.js"></script>


            <script src="js/basictools.js"></script>
            <script src="js/login.js"></script>
            <script src="js/main.js"></script>
            <script src="js/index.js"></script>
            <script src="js/admin.js"></script>


        </body>
        </html>

<?php 
    endif; 
?>
