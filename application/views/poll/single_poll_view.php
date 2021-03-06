 

<div class='poll' id='status'>  
	<div class='poll' id='results-view'>
		<div class='prev'>
			<a class='previous btn btn'>&laquo; Back to homepage</a>
		</div>
		<div class='poll-content'>
			<div class='poll-box'>
				<div class='poll-name'> <?php echo $election->name ?>  </div>

				<div class='poll-description'> <?php echo $election->tag_line ?> </div>
				<div class='votes'><span class=''>Status: <?php echo $election_status ?></span></div> 
				<div class='votes'><span class=''><?php echo $votes ?> people voted</span><br></div>

				<div class='poll-time'>
					<span class=''>
						From: <?php echo $election->start_time ?>
					</span> To <span class=''>
						<?php echo $election->end_time ?>
					</span>
				</div>


			</div>
		</div>
	</div> 

 
		<?php foreach ($positions as $position) : ?>
			<div class=' choice-title'><h3 class='mx-auto'> <?php echo $position->name ?> </h3></div>
			<div class='choices'>
			 

				<?php foreach ($position->candidates as $candidate): ?>
					<div class="row choice-row">

						<div class="col-md-5 vertical-align "> 
							<?php if (!$candidate->avatar): ?>
							<div class="img-wrapper  vertical-align"> 
								<img id="avatar<?php echo $candidate->id ?>" src="<?php echo base_url('Assets/images/default.png') ?>"></div>
								<span class="choice-name">
									&nbsp; <?php echo $candidate->name; ?>

								</span>  

							</div> 
							<?php else: ?>
								<div class="img-wrapper  vertical-align"> 
								<img id="avatar<?php echo $candidate->id ?>" src="<?php echo base_url('uploads/' . $candidate->avatar) ?>"></div>
									<span class="choice-name">
										&nbsp; <?php echo $candidate->name; ?>

									</span>  

								</div>
							<?php endif; ?>
							<div class="col-md-5 vertical-align"></div>
							<div class="col-md-2 vertical-align ">
								<?php if ($this->session->userdata('staff')): ?>
								<p class="mx-auto"><?php echo $candidate->votes ?> Vote(s)</p>
								<?php else: ?>
									TBA
								<?php endif; ?>
							</div>

							<?php if ($this->session->userdata('staff') && !$election->publish ): ?>
							<div class='col-md-12'>
								<button class='btn btn-info upload-avatar border-0' data-toggle='collapse' href='#collapse<?php echo $candidate->id ?>' aria-expanded='false' aria-controls='collapseExample'>
							    	Upload Avatar
							  	</button>
							</div>
							<div class='collapse' id='collapse<?php echo $candidate->id ?>'>
								<div class='col-md-12'>
									<form enctype='multipart/form-data' id='<?php echo $candidate->id ?>' name='upload_image' class='upload_form'>
										<div >
											<input type='file' class='image_file' name='image_file' id='<?php echo $candidate->id ?>'></input>
											<input type='submit' value='Upload' ></input>
										</div>
										
									</form>
								</div>
							</div>
							<?php endif; ?>
					</div>
						
					<?php endforeach; ?>
				</div>

			<?php endforeach; ?>
 
	</div>

<?php if ( $election->private && $this->session->userdata('staff') ): ?>
	</br>
	<h5>Users Allowed to Vote <button style="float: right;" class="btn btn-primary" id="user-selection">Add</button></h5>
</br>
<table id="allowed_users" class="table table-borderd table-striped">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Username</th> 
		</tr>
	</thead>
	<tbody> 
		<?php foreach ($allowed_users as $row): ?>
			<tr>
				<td><?php echo $row->user_id ?></td>
				<td><?php echo $row->name ?></td>
				<td><?php echo $row->username ?></td> 
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<div class="modal" tabindex="-1" role="dialog" id="add-private-user-modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Select User</h5>
				<button type="button" class="close" style="float: right;" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table table-striped table-borderd table-hover">
					<thead>
						<th>ID</th>
						<th>Name</th>
						<th>Username</th>
						<th>Action</th>
					</thead>
					<tbody>
						<?php foreach ($users as $user): ?>
							<tr>
								<td><?php echo $user->id ?></td>
								<td><?php echo $user->name ?></td>
								<td><?php echo $user->username ?></td>
								<td><button class="btn btn-success add-private-user-to-election" data-poll_id="<?php echo $election->id ?>" data-id="<?php echo $user->id ?>">Add</button></td>
							</tr>

						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<div class="modal-footer"> 
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<?php endif; ?>
