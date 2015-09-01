

<div id='cntNavMenu'>
	<ul>
        <?php
            require_once './include/lib/loadMenusController.inc.php';
            $loadMenusController = new c_loadMenusController();
          
            if( isset( $_COOKIE["icaict406a_usersName"] ) ) // display these buttons only if they're a member
            {
                echo "<li id='home' class='js-pjax'>Home<br/>&nbsp;</li>";
                
                $loadMenusController->displayChallengesMenu();              

                echo "<li id='myAccount' class='js-pjax'>My<br/>Account</li>";               

                if( isset( $_COOKIE["icaict406a_userType"] ) && ( $_COOKIE["icaict406a_userType"] == UT_ADMIN ) )   { echo "<li id='adminPanel'  class='js-pjax'>Administrator<br/>Panel</li>";  }
                else                                                                                                { echo "<li id='contactUs'   class='js-pjax'>Contact Us<br/>&nbsp;</li>";    }
                
            }
            else
            {
                echo "<li id='home' class='js-pjax'>Home</li>";
                echo "<li id='contactUs' class='js-pjax'>Contact Us</li>";
            }
        ?>
	</ul>
</div>

