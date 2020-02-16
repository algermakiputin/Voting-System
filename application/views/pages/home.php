
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        You must fill out all
      </div>

      <div class="modal-footer" >
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
       
      </div>
    </div>
  </div>
</div>
<div id="top"></div>
<div class="container page" id="home">
	<div class="row">
		<div class="col-lg-12 mx-auto">
			<div id="messageBox">
			
			</div>
			<div class="main" id="main">
				<?php
					$counter = 0;
					$today = date("Y-m-d");
					$today_time = strtotime($today);
					$voted = [];
				
					foreach($results as $result) {
						foreach ($result as $res) {
							array_push($voted, $res);
						}
					}
					if ($polls) {
						foreach ($polls as $poll) {
							
							$end_time = date_format(date_create($poll->end_time), 'M d Y');
						 
							
							$end_date = strtotime($end_time);
							$ended = false;
						 
							if ($end_date < $today_time) {
								$ended = true;
							} 
						?>
						
						
						<div class="poll poll-home" id="poll-<?php echo $poll->id; ?>">
							<div class="poll-content">
								<div class="poll-name">
									<?php echo $poll->name; ?>
								</div>
								<div class="poll-desctipion">
									<?php echo $poll->tag_line; ?>
									<div>
										Type: <?php echo $poll->private ? "Private" : "Public" ?>
									</div>
								</div>
								<div class="poll-summary">
									<?php
										$pollSummary = "";
										if (in_array($poll->id, $voted) && $votes[$counter] == 1 ){
											$pollSummary = "You Voted this";
										}if (in_array($poll->id, $voted) && $votes[$counter] > 1 ){
											$total = $votes[$counter] - 1;
											$pollSummary = "You and $total other people voted this";

										}if ($votes[$counter] == 0) {
											$pollSummary = "No votes yet";
										}if (!in_array($poll->id, $voted) && $votes[$counter] == 1) {
											$pollSummary = "$votes[$counter] people voted";
										}if ($this->session->userdata('staff')) {
											$pollSummary  = "$votes[$counter] people voted";
										}

										echo $pollSummary;

									?>
								</div>
							 
							 
								<div class="poll-time">
									<?php 
										if ($ended) {
											echo "Voting end";
										}else {
											?>
											From:
										<?php echo date_format(date_create($poll->start_time),'M, d Y'); ?>
									 To: 
										<?php echo date_format(date_create($poll->end_time), 'M, d Y') ?>
											<?php 
										}
									?>
									
								</div>
								<div class="actions">
									
									<?php
										
										if (!$this->session->userdata('staff')) {
											if (!$ended) {
												if (!in_array($poll->id, $voted)){

													$can_vote = $this->db->where('user_id', $this->session->userdata('id'))
																				->where('poll_id', $poll->id)
																				->get('private_allowed_users')
																				->num_rows();

													?>
												 	<?php if (!$poll->private) : ?>
														<a class="btn btn-info view" data="<?php echo $poll->id ?>">VOTE</a>

													<?php else: ?>
														<?php if ($poll->private && $can_vote): ?>

															<a class="btn btn-info view" data="<?php echo $poll->id ?>">VOTE</a>
														<?php endif; ?>
													<?php endif ?>

													<?php
												}
											}
										}else {
											?>
											
											<?php
											if ($poll->publish) {
												?>
												<a class="btn btn-success" id="publish-<?php echo $poll->id ?>" data="<?php echo $poll->id ?>">PUBLISHED</a>
												<?php
											}else {
											?>

												<a class="btn btn-info edit-publish" id="publish-<?php echo $poll->id ?>" data="<?php echo $poll->id ?>">PUBLISH</a>
												
											<?php
											}
											?>
											 <a class="btn btn-danger delete" id="delete-<?php echo $poll->id ?>" data="<?php echo $poll->id ?>">DELETE</a>
											<?php
										}
										
										?>
										<a class="btn btn-primary status" data="<?php echo $poll->id ?>">VIEW</a>
										<?php
									?>
									
									
								</div>
							</div>
						</div>
						<?php

						$counter++;
						}
					}else {
						?>
						<div class="poll poll-home" >
							<div class="poll-content">
								
									<p>No active election at the moment....</p>	
							
								
							</div>
						</div>
						<?php
					}
				?>
				
			</div>
			<?php if ($this->session->userdata('staff')) : ?>
					<div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h5 class="modal-title" id="exampleModalLabel">New Election</h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					        <form id="new_poll" name="new_poll"  accept-charset="utf-8" method="POST">
					        	<div class="form-group">
				                    <label for="username">Election Name</label>
				                    <input type="text" placeholder="Not more than 50 characters" class="form-control form-control-lg rounded-1" name="poll_name" id="poll_name" required="">
				                </div>
				                <div class="form-group">
				                    <label for="username">Tag Line</label>
				                    <textarea rows="5" class="form-control form-control-lg rounded-1" name="tag_line" id="tag_line" required="" placeholder="Not more than 150 characters" ></textarea>
				                </div>
				                <div class="form-group">
				                	<label>Private</label>
				                	<select class="form-control" name="private" id="private">
				                		<option value="0">No</option>
				                		<option value="1">Yes</option>
				                	</select>
				                </div>
				                <div class="form-group">
				                	<table>
				                		<tr>
				                			<td>
				                				<label> Start Date: </label>		
				                			</td>
				                			<td>
				                				<input type="date" class="form-control"  name="start-time" id="start-time">
				                			</td>
				                		</tr>
				                		<tr>
				                			<td>
				                				<label> End Date: </label>
				                   				
				                			</td>
				                			<td>
				                				<input type="date" class="form-control"  name="end-time" id="end-time">
				                			</td>
				                		</tr>
				                	</table>
				                   	
				                </div>
				                
				                 
				                
				                <div id="groups" class="groups">
				                	
				                </div>
				                <div class="form-group">
				                    <button id="add-group" class='btn btn-primary'>Add Group</button>
				                </div>
				                <div class="form-group">
				                    <input class="btn btn-success btn-lg" id="poll_save" type="Submit" value="Save">
				                </div>
				                


					        </form>
					      </div>
					      <div class="modal-footer" >
					        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					       
					      </div>
					    </div>
					  </div>
					</div>
					<button class="btn btn-success btn-fab" id="add-btn">
			          <i class="fa fa-plus"></i>
			       	</button>


			       	<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					        <form id="edit_form" name="edit_form"  accept-charset="utf-8" method="POST">
					        	<div class="form-group">
					        		<label>Name</label>
					        		<input type="text" name="" class="form-control" id="edit_name">
					        	</div>
				                <div class="form-group">
					        		<label>Username</label>
					        		<input type="text" name="" class="form-control" id="edit_username">
					        	</div>
					        	<div class="form-group">
					        		<label>Password</label>
					        		<input type="text" name="" class="form-control" id="edit_password">
					        	</div>
					        	<div class="form-group">
					        		<label>Grade</label>
					        		<input type="text" name="" class="form-control" id="edit_grade">
					        	</div>
					        	<div class="form-group">
					        		<label>Section</label>
					        		<input type="text" name="" class="form-control" id="edit_section">
					        	</div>
					        	<div class="form-group">
					        		<input type="submit" name="" id="edit_save" value="Save" class="btn btn-success">
					        	</div>


					        </form>
					      </div>
					      <div class="modal-footer" >
					        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					       
					      </div>
					    </div>
					  </div>
					</div>
		       <?php endif; ?>
		</div>
	</div>
</div>
<footer class="site-footer">
	<p>Copyright &copy; Precious International School Of Davao
 <?php echo date('Y') ?> - Developed By: <a target="__blank" href="https://algermakiputin.github.io/portfolio">Alger Makiputin</a></p>
</footer>

