<?php

    $idUser = ( isset($_COOKIE["icaict406a_idUser"]) )? $_COOKIE["icaict406a_idUser"] : -1;

    include 'include/lib/loadUserController.inc.php';
    $loadUserController = new c_loadUserController( $idUser );
    
?>

<div id="cntMyAccount" class="myAccount">

    <div class="wrapper">

        <h1>My Account </h1>

        <fieldset id='fldsetMyAccountInfo'>
            <legend>My Personal Info</legend>

            <input id="myAccountIdUser" type="hidden" value="<?php echo $idUser; ?>" />

            <!-- Error/Success Message -->
            <div id='cntUpdateAccountInfoMessage' class='hide'>
                <label class='important errorMessage'></label>
                <label class='successMessage'></label>
            </div>

            <!-- Input Wrapper & Update buttons -->
            <div class="inputWrapper">

                <div id='cntMyAccountInfo'>
                    <label class='required'>First Name:</label>                        
                    <input name='firstname' id='myAccountFirstname' type ='text' value="<?php echo $loadUserController->firstname; ?>" required /><br />

                    <label class='required'>Last Name:</label>                        
                    <input name='lastname' id='myAccountLastname' type ='text' value="<?php  echo $loadUserController->lastname; ?>" required /><br />

                    <label class='required' >Sign-in Email:</label>                        
                    <input name='email' id='myAccountEmail' type ='email' value="<?php echo $loadUserController->email; ?>" required /><br />

                    <label>Phone:</label>                        
                    <input name='primaryPhone' id='myAccountPhone' type ='text' value="<?php echo $loadUserController->phone; ?>" onkeypress="return isPhoneNumberKey(event)" required /><br />

                </div>

                <div class="updateButtons">
                    <input id="btnMyAccountUpdateInfo"   class="btn btnUpdate"   type="button" value="Update &#9658;" /><br />
                </div>

            </div>

        </fieldset>

        <fieldset id='fldsetMyAccountPassword'>
            <legend>My Account Password</legend>

            <!-- Error/Success Message -->
            <div id='cntUpdateAccountPasswordMessage' class='hide'>
                <label class='important errorMessage'></label>
                <label class='successMessage'></label>
            </div>

            <!-- Input Wrapper & Update buttons -->
            <div class="inputWrapper">

                <div id='cntMyAccountPassword'>

                    <label class='required' >Old Password:</label>                        
                    <input name='oldPassword' id='oldPassword' type ='password' value="" required /><br />

                    <label class='required' >New Password:</label>                        
                    <input name='newPassword' id='newPassword' type ='password' value="" required /><br />

                    <label class='required'>Confirm Password:</label>                        
                    <input name='confirmPassword' id='confirmPassword' type ='password' value="" required /><br />

                </div>

                <div class="updateButtons">
                    <input id="btnMyAccountUpdatePassword"   class="btn btnUpdate"   type="button" value="Update &#9658;" /><br />
                </div>

            </div>

        </fieldset>

    </div>
</div>