<?php
    session_start();

    $timedout = ( isset( $_REQUEST['timedout'] ) )? $_REQUEST['timedout'] : '0';
        
    $title = "Photo Challenge - Login ";
    if( $_SERVER["HTTP_X_PJAX"] ): 
	    echo "<title>{$title}</title>";

        include "include/login-pjax.php"; 
        

    else: 
?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width">
            <title>Photo Challenge - Login</title>

            <link rel='stylesheet' type='text/css' href='js/jquery-ui-1.11.2.custom/jquery-ui.min.css' />
            <link rel='stylesheet' type='text/css' href='css/main.css' />
            <link rel='stylesheet' type='text/css' href='css/popupForgotPassword.css' />

        </head>
        <body>

	        <?php include "include/header.php";	?>

            <section id='pjaxCntMainContent'>
                <?php include "include/login-pjax.php"; ?>
            </section>
	
	        <?php include "include/footer.php";	?>


            <script src="js/jquery-1.11.0.min.js"></script>
            <script src="js/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script>
            <script src="js/jquery.pjax.js"></script>

            <script src="js/basictools.js"></script>
            <script src="js/login.js"></script>
            <script src="js/main.js"></script>
            <script src="js/popupForgotPassword.js"></script>

        </body>
        </html>

<?php 
    endif; 
?>
