<?php
    session_start();

    require_once 'include/common.inc.php';
    
    $title = "Photo Challenge - Home";
    if( $_SERVER["HTTP_X_PJAX"] ): 
	    echo "<title>{$title}</title>";

        include "include/index-pjax.php"; 

    else: 
?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width">
            <title>Photo Challenge - Home</title>

            <link rel='stylesheet' type='text/css' href='js/jquery-ui-1.11.2.custom/jquery-ui.min.css' />
	        <link rel="stylesheet" type='text/css' href="css/lightbox.css">
            <link rel='stylesheet' type='text/css' href='css/main.css' />
            <link rel='stylesheet' type='text/css' href='css/popupForgotPassword.css' />

        </head>
        <body>
            <div id="cntBackgroundImages"></div>

            <div class="ajaxLoader"></div>

	        <?php 
                include "include/header.php";	
                include "include/navMenu.php";	
            ?>

            <section id='pjaxCntMainContent'>
                <?php include "include/index-pjax.php";	?>
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

            <script src="js/basictools.js"></script>
            <script src="js/login.js"></script>
            <script src="js/main.js"></script>
            <script src="js/index.js"></script>
            <script src="js/admin.js"></script>
            <script src="js/popupForgotPassword.js"></script>

        </body>
        </html>

<?php 
    endif; 
?>
