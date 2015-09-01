
<div id='cntNavMenu'>
	<ul id="navMenu-Admin">
        <?php
            if( isset( $_COOKIE["icaict406a_usersName"] ) && ( $_COOKIE["icaict406a_userType"] == UT_ADMIN ) ) // display these buttons only if they're a member
            {
		        echo "<li id='adminExit'  class='js-pjax'>Exit Administration Panel</li>";
            }
            else
            {
                // should not not come here - unless user has cleared the cookies, in which case throw them out
                header( "Location: index.php");
            }
        ?>
	</ul>
</div>
    
<div id='cntAdmin'>

    <!-- left Panel -->
    <div id="cntLeftPanel">
        <h1>Administration<br />Panel</h1>
        <div id="cntMenuAccordion">
            <!------------------------->
            <h3>Challenges</h3>
            <div id="leftMenuChallenges" class="leftMenu">
                <ul>
                    <li class="leftMenuRegisterChallenge">Register</li>
                    <li class="leftMenuUpdateChallenge">Update &amp; Delete</li>
                </ul>
            </div>

            <!------------------------->
            <h3>Submissions</h3>
            <div id="leftMenuSubmissions" class="leftMenu">
                <ul>
                    <li class="leftMenuViewSubmissions">View &amp; Edit</li>
                </ul>
            </div>

            <!------------------------->
            <h3>Users</h3>
            <div id="leftMenuUsers" class="leftMenu">
                <ul>
                    <li class="leftMenuViewUsers">View &amp; Edit</li>
                </ul>
            </div>

        </div>
    </div>

    <!-- Right panel -->
    <div id="cntRightPanel">
    </div>

</div>

