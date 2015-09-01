
<div id='cntNavMenu'>
	<ul>
		<li id='home'       class="js-pjax">Home</li>
		<li id='contactUs'  class="js-pjax">Contact Us</li>
	</ul>
</div>

<div id='cntLogin'>
    
    <div id='cntSigninBox'>

        <form name='frmSignin' action='login.php' target='_self' method ='post'>

            <input type='hidden' name='actionTaken' value='validate-member-login' />
            <input type='hidden' name='signinAttempt' value='' id='signinAttempt' />

            <div id='cntRegisterHref'>
                <a id='aRegister' class='toggleSignInDisplay'>*Sign-Up (Join)</a>
            </div>

            <fieldset id='fldsetSignin'>
                <div id='cntSigninTitle'>
                    <h1>Sign In</h1>
                </div>
                <div id='cntSigninDetails'>
                    <label class="required">Sign-in Email / User ID:</label>                        
                    <input name='email' id='signinEmail' type ='text' value="<?php echo $email; ?>" required /><br />
                    <label class="required">Password:</label>                        
                    <input name='password' id='signinPassword' type ='password' value="" required /><br />
                    <a id='aForgotPassword' href='#forgotPasswordBox'>Forgot your Password?</a> 
                    <input name='btnSignin' id='btnSignin' class='btn btnSignin' type ='button' value='Sign In &#9658;'  />

                    <div id='cntSignInMessage'>
                        <label class='important errorMessage'></label>
                        <label class='successMessage'></label>
                    </div>
                </div>
            </fieldset>
        </form>

    </div>

    <!-- Member Register -->
    <div id='cntRegisterBox'>
        <form name='frmRegister' action='login.php' target='_self' method ='post'>

            <input type='hidden' name='actionTaken' value='register' />
            <input type='hidden' name='registerAttempt' value='' id='registerAttempt' />

            <div id='cntSigninHref'>
                <a id='aSignin' class='toggleSignInDisplay'>*Sign-In</a>
            </div>

            <fieldset id='fldsetRegister'>
                <div id='cntRegisterTitle'>
                    <h1>Sign Up</h1>
                </div>
                <div id='cntRegisterDetails'>
                    <label class='required'>First Name:</label>                        
                    <input name='firstname' id='registerFirstname' type ='text' value="<?php echo $firstname; ?>" required /><br />
                    <label class='required'>Last Name:</label>                        
                    <input name='lastname' id='registerLastname' type ='text' value="<?php echo $lastname; ?>" required /><br />
                    <label class='required' >Sign-in Email:</label>                        
                    <input name='email' id='registerEmail' type ='email' value="<?php echo $email; ?>" required /><br />
                    <label>Primary Phone:</label>                        
                    <input name='primaryPhone' id='registerPrimaryPhone' type ='text' value="<?php echo $phone ?>" onkeypress="return isPhoneNumberKey(event)" required /><br />
                    <label class='required' >Password:</label>                        
                    <input name='password' id='registerPassword' type ='password' value="" required /><br />
                    <label class='required'>Confirm Password:</label>                        
                    <input name='confirmPassword' id='registerConfirmPassword' type ='password' value="" required /><br />
                    <input name='btnRegister' id='btnRegister' class='btn btnRegister' type ='button' value='Sign Up &#9658;' />

                    <div id='cntRegisterMessage'>
                        <label class='important errorMessage'></label>
                        <label class='successMessage'></label>
                    </div>

                </div>

            </fieldset>

        </form>
    </div>


    <!-- Forgot Email -->
    <div id='forgotPasswordBox' class='forgotPasswordPopup'>
        <a href='#' class='close'><img src='images/close_pop.png' class='btnClose' title='Close Window' alt='Close' /></a>
        <form name='frmForgotPassword' action='#' method='post' class='signin'>

            <fieldset class='textbox'>
                <label>
                    <span>Please enter your Sign-in Email address<br> and your new password will be emailed to you.</span>
                    <input id='forgotPasswordSigninEmail' name='signinEmail' value='' type='text' autocomplete='on' placeholder='Sign-in Email'>
                </label>

                <button id='btnSend' class='button' type='button'>Send</button><br>
                <div id='ajaxForgotPasswordMessageResponse'></div>
            </fieldset>

        </form>
    </div>

</div>

<?php

    if( $timedout )
    {
        echo "<script> var timedout = true;</script>"; 
    }
?>

