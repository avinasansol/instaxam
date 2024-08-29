      <!-- Sidebar -->
        <div id="sidebar">
          <div class="inner">
            <!-- Search Box -->
            <section id="srch" class="alt">
              <form method="get" action="https://www.instaxam.in/search-result.php" onsubmit="return searchTest()">
			  <table><tr>
              	<td><input type="text" name="searchTxt" id="searchbox" placeholder="Search..." /></td>
                <td><input type="submit" id="searchsub" value="" /></td>
			  </tr></table>
              </form>
            </section>
			<script type="text/javascript">
			function searchTest(){
				var searchText = $("#searchbox").val();
				if(searchText == "") {
					alert("Please enter some text to search.");
					return false;
				}
				return true;
			};
			</script>
			
			<!-- Menu -->
            <nav id="menu">
              <ul>
				<li><a href="https://www.instaxam.in/index.php">Home</a></li>
                <li>
                  <span class="opener">Browse Tests</span>
                  <ul>
                    <li><a href="https://www.instaxam.in/browse-test.php#most-famous">Most Famous</a></li>
                    <li><a href="https://www.instaxam.in/browse-test.php#recently-added">Recently Added</a></li>
                    <li><a href="https://www.instaxam.in/browse-test.php#test-by-creator">Search by Creator</a></li>
                    <li><a href="https://www.instaxam.in/browse-test.php#test-category">Browse by Category</a></li>
                  </ul>
                </li>
				<?php
					if(isset($_SESSION['LogdUsrDet']))
					{
				?>
				<li><a href="https://www.instaxam.in/test-history.php">Test History</a></li>
                <li>
                  <span class="opener">Test Creation</span>
                  <ul>
                    <li><a href="https://www.instaxam.in/create-test.php">Create A New Test</a></li>
                    <li><a href="https://www.instaxam.in/edit-test-list.php">Edit An Existing Test</a></li>
                    <li><a href="https://www.instaxam.in/approve-reject-access.php">Approve/Reject Access</a></li>
                    <!--
					<li><a href="#">Generate Report</a></li>
                    <li><a href="#">Search A Result</a></li>
					-->
                  </ul>
                </li>
				<?php
					}
				?>
              </ul>
            </nav>
			
          </div>
        </div>
