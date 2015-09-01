
<div id='cntHeader'>
    <div id='cntHeaderInner'>

        <div id='cntHeaderInnerWrapper'>

            <div id='cntLogo'><img id='logo' src="images/logo.png" /></div>
            <div id='cntHeaderSlogan'>
                <div>Make Life an Adventure,</div>
                <div>Capture the Moment...</div>
            </div>

            <div id='cntHeaderCamera'><img id='imgHeaderCamera' src="images/camera1.png" /></div>
            <div id='cntHeaderInnerLine'>
                <svg id='svgHeaderInnerLine'>
                    <line x1="0"    y1="40" x2="300"    y2="40"  /> 
                    <line x1="300"  y1="40" x2="340"    y2="0"  /> 
                    <line x1="340"  y1="0"  x2="470"    y2="0"  /> 
                </svg>
            </div>
            <div id='cntSignIn' class="js-pjax">
                <img id='sign-in' src="images/signin-icon.png" /> 
                <?php
                    if( isset( $_COOKIE["icaict406a_usersName"] ) ) { echo "<span>SIGN OUT</span>";    }
                    else                                            { echo "<span>SIGN IN/UP</span>";  }
                ?>
            </div>
            <div id='cntWelcomeUser'>
                <?php
                    if( isset( $_COOKIE["icaict406a_usersName"] ) ) { echo $_COOKIE["icaict406a_usersName"];    }
                ?>
            </div>

        </div>
    </div>
</div>
