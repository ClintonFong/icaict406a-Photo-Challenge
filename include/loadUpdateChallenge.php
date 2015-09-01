<?php

// loadUpdateChallenge.inc.php
// 
// by Clinton - Jan, 2015
//

define( "MAX_PAGE_ITEMS", "20" );

include './lib/loadChallengesController.inc.php';
$loadChallengesController = new c_loadChallengesController();
    
$nChallenges    = count( $loadChallengesController->challenges );
$nPages         = ceil( $nChallenges  / MAX_PAGE_ITEMS );

?>                        

<div id="loadUpdateChallenge">
    <fieldset id="fldsetUpdateChallenge">
        <legend>Update Challenge</legend>

        <!-- Error/Success Message -->
        <div id='cntAdminMessage' class="hide">
            <label class='important errorMessage'></label>
            <label class='successMessage'></label>
        </div>

        <!-- Input Wrapper & Update buttons -->
        <div class="inputWrapper">

            <div class="cntEntryValues">
                <input id="idChallenge" type="hidden" value="-1" />

                <label>Challenge Type:</label>
                <select id="loadUpdateChallenge-challengeType">
                    <option value="1">Once a Day</option>
                    <option value="2">Once a Weekend</option>
                    <option value="3">Once a Month</option>
                </select><br />

                <label>Year:</label>
                <select id="loadUpdateChallenge-year"></select><br />

                <label>Month:</label>
                <select id="loadUpdateChallenge-month">
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select><br />

                <label>Status:</label>
                <select id="loadUpdateChallenge-status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select><br />

                <label>Comments:</label>
                <textarea id="loadUpdateChallenge-comments"></textarea>

            </div>

            <div class="updateButtons">
                <input id="btnUpdate"   class="btn btnUpdate"   type="button" value="Update" /><br />
                <input id="btnDelete"   class="btn btnDelete"   type="button" value="Delete" /><br />
                <input id="btnClose"    class="btn btnClose"    type="button" value="Close" /><br />
            </div>

        </div>

        <!-- listing -->
        <div class="listingChallenges">
            <table id="tblListingChallenges" class="tableListing">
                <thead>
                    <tr>
                        <th colspan="3">Date &amp; Type</th>
                        <th rowspan="2">Status</th>
                        <th rowspan="2">Comments</th>
                    </tr>
                    <tr>
                        <th>Challenge Type</th>
                        <th>Year</th>
                        <th>Month</th>
                    </tr>

                </thead>
                <tbody>
                <?php
                    for( $iChallenge = 0;  $iChallenge < $nChallenges; $iChallenge++ )
                    {
                        $challenge      = $loadChallengesController->challenges[ $iChallenge ];
                        
                        $challengeType  = $loadChallengesController->getChallengeType( $challenge );
                        $monthName      = $loadChallengesController->getMonth( $challenge );
                        $status         = $loadChallengesController->getStatus( $challenge );
                    
                        $hideClass      = ( $iChallenge >= MAX_PAGE_ITEMS )? "hide" : "";
                        
                        echo "<tr id='trChallenge{$challenge->idChallenge}' class='hoverHighlight {$hideClass}'>";
                        echo "    <td value='{$challenge->challengeType}'>{$challengeType}</td>";
                        echo "    <td>{$challenge->year}</td>";
                        echo "    <td value={$challenge->month}>{$monthName}</td>";
                        echo "    <td value='{$challenge->status}'>{$status}</td>";
                        echo "    <td><div>{$challenge->comments}</div></td>";
                        echo "</tr>";
                    }
                ?>
                </tbody>
            </table>

            <div id="challengesPagination" class="pagination <?php if( $nPages <= 1 ) { echo 'hide'; } ?>">
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
    echo "  challengesListing = " . json_encode( $loadChallengesController->challenges );
    echo "</script>";
?>
