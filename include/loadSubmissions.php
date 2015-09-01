<?php

    //include './lib/loadSubmissionsController.inc.php';
    //$loadSubmissionsController = new c_loadSubmissionsController();
    
?>   

<div id="loadSubmissions">

    <fieldset id='fldsetViewSubmissions' class="fldsetViewSubmissions">
        <legend>Retrieve Submissions to View</legend>

        <!-- Error/Success Message -->
        <div id='cntAdminMessage' class="hide">
            <label class='important errorMessage'></label>
            <label class='successMessage'></label>
        </div>

        <div id='cntViewSubmissions-getSubmissions' class="inputWrapper">

            <!-- -------------- Enter Values ----------------->  
            <div id='cntViewSubmissions-getSubmissions-searchParams'>

                <!-- challenge type -->
                <div class='cnt1Item'>
                    <label>Challenge Type:</label>                        
                    <select id="loadSubmissions-challengeType" class="inputField">
                        <option value="">All</option>
                        <option value="1">Once A Day</option>
                        <option value="2">Once A Weekend</option>
                        <option value="3">Once A Month</option>
                    </select> 
                </div>

                <!-- month -->
                <div class="cnt1Item">
                    <label>Month:</label>                        
                    <select id="loadSubmissions-month" class="inputField">
                        <option value="">All</option>
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
                    </select>
                </div>

                <!-- Year -->
                <div class="cnt1Item">
                    <label>Year:</label>
                    <select id="loadSubmissions-year" class="inputField">
                        <option value="">All</option>
                        <?php
                            $year       = date("Y");
                            $startYear  = $year - 10;
                            $endYear    = $year;
                                
                            for( $iYear = $startYear; $iYear <= $endYear; $iYear++ )
                            {
                                echo "<option value='{$iYear}'>{$iYear}</option>"; 
                            }
                        ?>
                    </select>
                </div>

                <!-- First Name -->
                <div class='cnt1Item'>
                    <label>First Name:</label>                        
                    <input id="loadSubmissions-firstname" type='text' class="inputField" value=""  />
                </div>

                <!-- Last Name -->
                <div class='cnt1Item'>
                    <label>Last Name:</label>                        
                    <input id="loadSubmissions-lastname" type='text' class="inputField" value=""  />
                </div>

            </div>

            <!-- -------------- Retrieve Customer button ----------------->  
            <div id='cntViewSubmissions-getSubmissions-buttons' class="searchButtons">
                <input  id="btnRetrieve" type="button" class="btn btnRetrieve" value="Retrieve"  /><br>
                <input  id="btnClear"    type="button" class="btn btnClear"    value="Clear Fields"  />
            </div>
        </div>

        <!-------------------------------------------------------------------------------->
        <!-------------------------------------------------------------------------------->
        <!-- listing --------------------------------------------------------------------->
        <!-------------------------------------------------------------------------------->
        <div class="listingSubmissions hide" >
            <div id="cntBackToRetrieveSubmissions"><a class="backToSearch">&#x25B2; Back to Retrieve Submissions to View</a></div>
            <table id="tblListingSubmissions" class="tableListing">
                <thead>
                    <tr>
                        <th colspan="4">Date &amp; Type</th>
                        <th rowspan="2">Photo1</th>
                        <th rowspan="2">Photo2</th>
                    </tr>
                    <tr>
                        <th>Challenge Type</th>
                        <th>Year</th>
                        <th>Month</th>
                        <th>No.(day)</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <div id="submissionsPagination" class="pagination">
                <a href="#" class="first" data-action="first">&laquo;</a>
                <a href="#" class="previous" data-action="previous">&lsaquo;</a>
                <input type="text" readonly="readonly" data-max-page="1" />
                <a href="#" class="next" data-action="next">&rsaquo;</a>
                <a href="#" class="last" data-action="last">&raquo;</a>
            </div>

        </div>

    </fieldset>


</div>