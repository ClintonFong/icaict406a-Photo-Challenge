<?php
    define( "MAX_PAGE_ITEMS", "20" );

    include './lib/loadUsersController.inc.php';
    $loadUsersController = new c_loadUsersController();
    
    $nUsers    = count( $loadUsersController->users );
    $nPages    = ceil( $nUsers / MAX_PAGE_ITEMS );
    
    $userTypes = array( "0" => "Challenger", "9" => "Administrator" );
?>                        

<div id="loadUsers">
    <fieldset id="fldsetViewUsers">
        <legend>View &amp; Edit Users</legend>

        <!-- Error/Success Message -->
        <div id='cntAdminMessage' class="hide">
            <label class='important errorMessage'></label>
            <label class='successMessage'></label>
        </div>

        <!-- Input Wrapper & Update buttons -->
        <div class="inputWrapper hide">

            <div class="cntViewValues">
                <input id="userViewIdUser" type="hidden" value="-1" />

                <label>Firstname:</label>
                <input id='userViewFirstname' type='text' value='' disabled><br>

                <label>Lastname:</label>
                <input id='userViewLastname' type='text' value='' disabled><br>

                <label>Email:</label>
                <input id='userViewEmail' type='text' value='' disabled><br>
               
                <label>Phone:</label>
                <input id='userViewPhone' type='text' value='' disabled><br>

                <label>User Type:</label>
                <select id='userViewUserType'>
                    <option value="0"><?php echo $userTypes["0"]; ?></option>
                    <option value="9"><?php echo $userTypes["9"]; ?></option>
                </select><br>

            </div>

            <div class="updateButtons">
                <input id="btnUpdate"   class="btn btnUpdate"   type="button" value="Update" /><br />
                <input id="btnDelete"   class="btn btnDelete"   type="button" value="Delete" /><br />
                <input id="btnClose"    class="btn btnClose"    type="button" value="Close" /><br />
            </div>

        </div>

        <!-- listing -->
        <div class="listingUsers">
            <table id="tblListingUsers" class="tableListing">
                <thead>
                    <tr>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    for( $iUser = 0;  $iUser < $nUsers; $iUser++ )
                    {
                        $user       = $loadUsersController->users[ $iUser ];
                        $hideClass  = ( $iUser >= MAX_PAGE_ITEMS )? "hide" : "";
                        
                        echo "<tr id='trUser{$user->idUser}' class='hoverHighlight {$hideClass}'>";
                        echo "    <td>{$user->firstname}</td>";
                        echo "    <td>{$user->lastname}</td>";
                        echo "    <td>{$user->email}</td>";
                        echo "    <td>{$user->phone}</td>";
                        echo "</tr>";
                    }
                ?>
                </tbody>
            </table>

            <div id="usersPagination" class="pagination <?php if( $nPages <= 1 ) { echo 'hide'; } ?>">
                <a href="#" class="first" data-action="first">&laquo;</a>
                <a href="#" class="previous" data-action="previous">&lsaquo;</a>
                <input type="text" readonly="readonly" data-max-page="<?php echo $nPages; ?>" />
                <a href="#" class="next" data-action="next">&rsaquo;</a>
                <a href="#" class="last" data-action="last">&raquo;</a>
            </div>
        </div>

    </fieldset>
</div>

<?php
    echo "<script>";
    echo "  usersListing = " . json_encode( $loadUsersController->users );
    echo "</script>";
?>
