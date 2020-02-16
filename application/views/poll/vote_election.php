<div class='poll'>

	<?php if ($this->session->userdata('staff')): ?>
		<div class='prev'>
			<a class='previous btn btn'>&laquo; Back to homepage</a>
		</div>
		<form id='poll-form' method='POST'>
		<div class='poll-content'>
			<div class='poll-box'>
				<div class='poll-name '> <?php echo $election->name ?></div>
				<div class=' poll-description'><?php echo $election->tag_line ?></div>
			</div> 

				<?php else: ?> 
					<h3 class='' style='padding:50px 15px'><?php echo $election->name ?></h3>
				<?php endif; ?>

				<?php foreach ($positions as $position): ?>
					<div class=' choice-title'><h3 ><?php echo $position->name ?> </h3></div>
					<div class='choices'>
						<?php foreach ($position->candidates as $candidate): ?>
						<div class='row choice-row '>
							<div class='col-md-9 vertical-align  '>
								<div class='img-wrapper '>
									<img src='<?php echo base_url("uploads/$candidate->avatar") ?>'>
								</div>
								<span class='choice-name'>
									<?php echo $candidate->name ?>
								</span>
							</div>
							<div class='col-md-3 vertical-align text-center '>
								<div class='options'>
									<label for='<?php echo $candidate->id ?>' class=''> 
										<input required="required" class="candidates" data-group-id="<?php echo $position->id ?>" type='radio' name='<?php echo $position->id ?>' value='<?php echo $candidate->id ?>' id='<?php echo $candidate->id ?>'> 
										<img />
									</label>
								</div>
							</div> 
						</div>
					<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div> 
			<p class='text-center text' id='review'>* Please review your votes *</p>
			<div class='poll-action'> <input type='button' id='submit-poll' class='btn btn-primary form-control btn-lg' value='Submit Vote'> </div>
		</form>
	</div>
</div>


<script type="text/javascript">
	$("#submit-poll").click(function(e) {

		var id = '<?php echo $this->session->userdata('id'); ?>';
		var poll_id = '<?php echo $election->id; ?>'
		var selected = $(".candidates:checked"); 
			
			var positions = [];
			var candidates = []; 


			for (i = 0; i < selected.length; i++) {

				positions.push($(selected[i]).data('group-id'));
				candidates.push($(selected[i]).val());
			}
			 

		$("#submit-poll").prop('disabled', true);

		$.ajax({
			type: "POST",
			url : "<?php echo base_url('vote/save') ?>",
			data : {
				user_id: id,
				group_id: positions,
				choices_id: candidates,
				poll_id: poll_id
				 
			},
			success : function (data) {
				$('#main').innerHTML = "";
				$('#modal').modal('show'); 
				$('.modal-header').removeClass('bg-danger');
				$('.modal-header').addClass('bg-success');
				$('.modal-body').text('Your vote has been submitted');
				$("#submit-poll").prop('disabled', false);
				$("#main").innerHTML = "";
				$.ajax({
					type : "get",
					url : base_url + "reload",
					contentType: "application/json",
					success : function (data) {
						$(".main").innerHTML = ""; 
						$(".main").html(data);
						$(document).scrollTop(0);
						DeletePoll();

					}
				})

			}

		});


	})
</script>