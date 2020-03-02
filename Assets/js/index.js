$(document).ready(function() {

	var view_btn = $(".view");
	var status_btn = $(".status");
	var choiceCount = 1;
	$('#tag_line').prop("selectionStart");
	var currentChoice = 1;

	function activeMenu() {
		$("#main-nav a li").click(function(){
			$("#site-header ul a").find('.active').removeClass('active');
			$(this).addClass('active');
		});
	}
	activeMenu();

	$("body").on('click', "#user-selection", function() {
		
		$("#add-private-user-modal").modal('toggle');
	});

	$("body").on('click', '.add-private-user-to-election', function() {

		var id = $(this).data('id');
		var poll_id = $(this).data('poll_id');
		var row = $(this).parents("tr");

		$.ajax({
			type: "POST",
			url: base_url + '/PollController/insert_private_user',
			data: {
				id: id,
				poll_id: poll_id
			},
			success: function(data) {
				
				var id = row.find('td').eq(0).text();
				var name = row.find('td').eq(1).text();
				var username = row.find('td').eq(2).text();

				$("#allowed_users tbody").append("<tr>"+
						"<td>"+id+"</td>" +
						"<td>"+name+"</td>" +
						"<td>"+username+"</td>" +
					"</tr>")

				row.remove();

			},
			error: function(data) {
				alert("Opps something went wrong please try again later..");
			}
		})
	})
	
	$("#import-csv").click(function(){
		$('#main').empty();
		$("#main").html("<div class='poll poll-home'><form method='post' enctype='multipart/form-data' id='users-import' name='users-import'><div class='form-group'><label><b>Select CSV File (Formatted User data CSV File)</b><br> <input name='csv_file' id='csv_file' type='file' class='file'></label></div> <div class='form-group'> <input type='submit' value='Import' name='submit-csv' id='submit-csv' class='btn btn-primary btn-lg'></div></form><p id='loading-indicator'></p></div>");
		
		$("#users-import").submit(function(e){
			
			e.preventDefault();
			if ($("#csv_file").val() === "") {
				alert('Selectt CV file to upload');
			}else {
				var form_data = new FormData ($('#users-import')[0]);
				$("#loading-indicator").text('loading....');
				$.ajax({
					url : base_url + 'users/import',
					method: "POST",
				   	data : form_data,
				   	 async : false,
		            cache : false,
		            contentType : false,
		            processData : false,
		            beforeSend : function() {
		            	alert('CLICK OK TO START IMPORTING...');
		            },
				   	success:function(data)
				   	{

				   	if (data == '1') {

				   		alert("Error: Import Limit, 1500 users only per import");
				   		return false;
				   	}
				 
				    	$("#submit-csv").prop('disabled', false);
				    	$("#users-import")[0].reset();
				    	if (data === 'false') {
				    		$("#loading-indicator").html('<p class="alert alert-danger">No data imported, File Not Uploaded <a class="close" data-dismiss="alert" aria-label="close" title="close">×</a></p>');
				    	}else {
				    		$("#loading-indicator").html('<p class="alert alert-success">Data Imported Successfully <a class="close" data-dismiss="alert" aria-label="close" title="close">×</a></p>');
				    	}

				   	},
				   	error : function () {
				   	
				   		$("#submit-csv").prop('disabled', false);
				    	$("#users-import")[0].reset();
				    	$("#loading-indicator").html('<p class="alert alert-danger">Some Error Occur, please try again <a class="close" data-dismiss="alert" aria-label="close" title="close">×</a></p>');
				   	}, 
				   	
				});

			}
			
		})
	})



	function view() {	
		$("#main").on('click', '.view', function () {

			$('#main').innerHTML = "";
			var id = $(this).attr('data')

			$.ajax({
				async: true,
				url : "" + base_url + "view/"+ id +"",
				type : "get",
				contentType: "application/json",
				success : function (data) {
					$(document).scrollTop(0);
					$(".main").html(data);
					
				}
			}
			);

		})

	}




	$("#view_users").click(function() {
		$("#main").empty();
		$.ajax({
			async: true,
			url : "" + base_url + "manage/allusers",
			type : "get",
			contentType: "application/json",
			beforeSend : function() {
				$("#main").html('<p>Loading...</p>');
			},
			success : function (data) {
				
				$("#users-table").tableExport({
					
				});
				$("#main").html(data);

				$("#search_page_pagination li a").css('padding','0 6px');
				
				$("#table-container").css('padding-bottom','20px');
				
				$("#users-table").tableExport({
					bootstrap : true
				   
				});
				
				$(".csv").hide();
						$(".txt").hide();
				$('#users-table').basictable();
				$("#search_page_pagination li.active a").css('color','#000');
				pagination();
				$('#deleteAllUser').confirm({
					text: "Are you sure you want to delete all user?",
					confirm : function() {

						$.ajax({
							type : "get",
							url : base_url + "manage/delete/all_users",
							success : function (data) {
								reloadPage();
								
								alert('All Users Deleted Successfully');
								$(document).scrollTop(0);

							}
						})
					}
				});

				$('.delete').confirm({
					text: "Are you sure you want to delete that users?",
					confirm: function(button) {
			
						id = $(button).attr('id');
						$.ajax({
							type : 'post',
							data : {
								id : id
							},
							url : base_url + "manage/delete/user",
							success: function(data) {
								if (data === "ok") {
									$("#row" + id).remove();
									alert('Deleted Successfully ');
								}
							}

						});
					
				    }
				});

				$('#resetVotes').confirm({
					text: "Are you sure you want to reset all users votes?",
					confirm : function() {
				
						$.ajax({
							type : "get",
							url : base_url + "manage/reset/all_users",
							success : function (data) {
								reloadPage();
								alert('All Users Votes Reseted Successfully');
								$(document).scrollTop(0);

							}
						})
					}
				});

			}
		}
		);
	});


	$("#main").on('click', '.edit',function() {
		id = $(this).attr('id');

		name = $("#row" + id + " td").eq(1).text();
		username = $("#row" + id + " td").eq(2).text();
		password = $("#row" + id + " td").eq(3).text();
		grade = $("#row" + id + " td").eq(4).text();
		section = $("#row" + id + " td").eq(5).text();

		$("#edit_name").val(name);
		$("#edit_username").val(username);
		$("#edit_password").val(password);
		$("#edit_grade").val(grade);
		$("#edit_section").val(section);
		$('#modal-edit').modal('toggle');
	})

	$("#edit_form").submit(function(e) {
			e.preventDefault();
			$("#edit_save").prop('disabled',true);
			$.ajax({
				type : 'post',
				url : base_url + "manage/edit/user",
				data : {
					id : id,
					name : $("#edit_name").val(),
					username : $("#edit_username").val(),
					password : $("#edit_password").val(),
					grade : $("#edit_grade").val(),
					section : $("#edit_section").val()
				},

				success : function (data) {
					$("#row" + id + " td").eq(1).html($("#edit_name").val());
					$("#row" + id + " td").eq(2).html($("#edit_username").val())
					$("#row" + id + " td").eq(3).html($("#edit_password").val())
					$("#row" + id + " td").eq(4).html($("#edit_grade").val())
					$("#row" + id + " td").eq(5).html($("#edit_section").val())
					$("#edit_form")[0].reset();
					$('#modal-edit').modal('toggle');
					alert("Changes Saved");
					$("#edit_save").prop('disabled',false);
				}
			});
	})

	function pagination () {
		$("#main").on('click','ul#search_page_pagination li a',function(e) {
			e.preventDefault();
			var href = $(this).attr('href');
			$.ajax({
				url : href,
				type : 'get',
				success : function (data) {
					$("#table-container").empty();
					$("#table-container").append(data);
					$("#search_page_pagination li a").css('padding','0 6px');
					$("#table-container").css('padding-bottom','20px');
					$('#users-table').basictable();
					$("#search_page_pagination li.active a").css('color','#000');
					$('.delete').confirm({
					text: "Are you sure you want to delete that users?",
					confirm: function(button) {
			
						id = $(button).attr('id');
						$.ajax({
							type : 'post',
							data : {
								id : id
							},
							url : base_url + "manage/delete/user",
							success: function(data) {
								if (data === "ok") {
									$("#row" + id).remove();
									alert('Deleted Successfully ');
								}
							}

						});
					
				    }
				});
				} 
			});

		})
	}

	$("#register_user").click(function(){
		$("#main").empty();
		$.ajax({
			async: true,
			url : "" + base_url + "register",
			type : "get",
			contentType: "application/json",
			success : function (data) {
			
				$("#main").html(data);
			
			}
		}
		);
	})

	$("#main").on('click','#regBtn',function(e){
		e.preventDefault();
		var full_name = $("#full_name").val();
		var grade = $("#grade").val();
		var section = $("#section").val();
		var username = $("#username").val();
		var password = $("#password").val();
		var conf_password = $("#conf_password").val();

		$.ajax({
			async: true,
			url : "" + base_url + "register/save",
			type : "POST",
			data : {
				name : full_name,
				username : username,
				password : password,
				conf_password : conf_password,
				grade : grade,
				section : section,
				voted : 0
			},
			success : function (data) {
				if (data !== 'ok') {
					$("#messageBox").html(data);
				}else {
					$("#registerForm")[0].reset();
					$("#messageBox").html("<div class='alert alert-success'>User Register Successfully <a class='close' data-dismiss='alert' aria-label='close' title='close'>×</a></div>");
				}

				$(document).scrollTop(0);
			}
		}
		);
	});
	$('#site-header ul a').click(function() {

	    $('.navbar-collapse').removeClass('show');
	})
	$("#main").on('click', '#search_btn',function() {
		var query = $("#search").val();
		 
		$.ajax({
			type : 'post',
			data : {
				query : query
			},
			url : base_url + 'manage/search/user',
			success : function(data) {
				$("#table-container").empty();
				$("#table-container").append(data);
				$('#users-table').basictable();
			} 
		});
	        
	})

	function filterUsers() {
		$("#main").on('click', '#filterSection', function(e){
			e.preventDefault();
			var filter = $('#section').val();
			if (filter !== "none") {

				$("#table-container").empty();
				$("#table-container").append('<p class="pad-15">Loading...</p>');
				$.ajax({
					async: true,
					url : "" + base_url + "manage/filterusers",
					type : "POST",
					data : {
						section : filter
					},
					success : function (data) {
						$("#table-container").empty();
						$("#table-container").append(data);
						$("#users-table").tableExport({
							trimWhitespace: false,
					 		position: "top",
					 		bootstrap : true
						});

						$(".csv").hide();
						$(".txt").hide();
						$('#users-table').basictable();
						$('#deleteAllUser').confirm({
							text: "Are you sure you want to delete all users?",
							confirm: function(button) {
					

								$.ajax({
									type : "get",
									url : base_url + "manage/delete/all_users",
									success : function (data) {
									reloadPage();
									alert('All Users Deleted Successfully');
										

									}
								})
						    }
						});
						$('.delete').confirm({
						text: "Are you sure you want to delete that users?",
						confirm: function(button) {
				
							id = $(button).attr('id');
							$.ajax({
								type : 'post',
								data : {
									id : id
								},
								url : base_url + "manage/delete/user",
								success: function(data) {
									if (data === "ok") {
										$("#row" + id).remove();
										alert('Deleted Successfully ');
									}
								}

							});
						
					    }
					});
						$('#resetVotes').confirm({
							text: "Are you sure you want to reset all users votes?",
							confirm : function() {
						
								$.ajax({
									type : "get",
									url : base_url + "manage/reset/all_users",
									success : function (data) {
										reloadPage();
										alert('All Users Votes Reseted Successfully');
										

									}
								})
							}
						});
						
					}
				}
				);
			}else {
				alert('Please Select a section');
			}
		}) 
	}

	filterUsers();

	function prev() {
		$("#main").on('click', '.previous', function(){
			reloadPage();
		})
	} 
	prev();

	$("#dashboard").click(function(){
		reloadPage();
	})
	function statusButton() {

		$("#main").on('click', '.status', function () {
			
			$('#main').innerHTML = "";
			var id = $(this).attr('data')
			
			viewStatus(id);				
		});


	
	}

	function reloadPage() {
		$("#main").innerHTML = "";
		$.ajax({
			type : "get",
			url : base_url + "reload",
			contentType: "application/json",
			success : function (data) {
				$(".main").innerHTML = "";
				
				$(".main").html(data);

				DeletePoll();
				
			}
		})
	}


	function successMessage(message) {
		$("#messageBox").html("<div class='alert alert-success'> " + message +"  <a href='' class='close' data-dismiss='alert' aria-label='close' title='close'>×</a></div>");
	}

	function addGroup() {
		$("#add-group").click(function(e){
			currentChoice = 1;
			e.preventDefault();
			$("#groups").append('<div class="form-group"><input type="text" class="form-control form-control-lg " name="group" name="poll_name" placeholder="Group '+choiceCount+'">  </div>');
			$("#groups").append('<div class="group-choices" id='+ choiceCount +'> <div class="form-group"> <input type="text" class="form-control form-control-lg " name="choice" placeholder="choice '+currentChoice+'" > </div>  </div> <button class="btn btn-info add-choice" id="add-choice">Add Choice</button>');
			currentChoice++;
			$(".add-choice").click(function(e){
				e.preventDefault();
				
				$('#' + (choiceCount - 1)).append('<div class="form-group"> <input type="text" class="form-control form-control-lg " name="choice" placeholder="choice '+currentChoice+'" > </div>');
				currentChoice++;
			});
			choiceCount++;
			
		})
	}

	function savePoll() {

		$("#poll_save").click(function(e){

			e.preventDefault();
			var groupsValue = [];
			var choiceValue = [];
			var choiceHolder = [];
			var poll_name = $("#poll_name").val();
			var tag_line = $("#tag_line").val();
			var start_time = $("#start-time").val();
			var end_time = $("#end-time").val();
			var groups = $('#groups input[name="group"]');
			var private = $("#private").val();
			
			for ( i = 0; i < groups.length; i++) {

				groupsValue.push(groups.eq(i).val());

			}


			for ( i = 0; i < choiceCount; i++) {
				choices = $('#' + i + ' input[name="choice"]');
				image = $('#' + i + ' input[name="avatar"]')
				for (x = 0; x < choices.length; x++) {
					if (choices.eq(x).val() !== "")
						choiceHolder.push(choices.eq(x).val());
					
				}
				choiceValue.push(choiceHolder);
				choiceHolder = [];

			}

			if (poll_name === "" && tag_line === "" ) {
				alert('Fill out all the required field');

			}
			 else {
				
				$.ajax({
					type : "post",
					url : base_url + "manage/save",
					data : {
						'poll_name' : poll_name,
						'tag_line' : tag_line,
						'groups' : groupsValue,
						'choices' : choiceValue,
						'end_time' : end_time,
						'start_time' : start_time,
						'private': private
					},
					beforeSend : function () {

						$("#poll_save").prop('disabled', true);

					},
					success : function (data) {
						
						$("#new_poll")[0].reset();
						successMessage("Poll Added Successfully");
						$('#modal-add').modal('toggle');
						
						$("#poll_save").prop('disabled', false);
						reloadPage();
						
					}
				})
			}

			

		})
	}

	function statusButton() {

		$("#main").on('click', '.status', function () {
			$('#main').innerHTML = "";
			var id = $(this).attr('data')
			$("#messageBox").empty();
			viewStatus(id);
		});
			
	}

	function publishButton() {
 
		$('#main').innerHTML = "";
		$("#main").on('click', '.edit-publish', function () {
			var id = $(this).attr('data')
			publishPost(id);
		});
	
	}

	function DeletePoll() {
		$(".delete").confirm({
		
			text: "Are you sure you want to delete that poll?",
		    confirm: function(button) {
		        var id = $(button).attr('data')

				$.ajax({
						type : "post",
						url : base_url + "manage/deletePoll",
						data : {
							'id' : id
						},
						success : function (data) {
							alert('Poll Deleted Successfully');
							$("#poll-" + id).remove();
							

						}
					})
		    },
		    cancel: function() {
		        // nothing to do
		    }
		}

		);
	}

	DeletePoll();

	function publishPost(id) {
		$.ajax({
			url : base_url + "publish",
			data : {
				id : id
			},
			type : "POST",
			success : function (data) {
				if (data === "ok") {
					$("#publish-" + id).removeClass('btn-info');
					$("#publish-" + id).addClass('btn-success');
					$("#publish-" + id).text('PUBLISHED');
				
				}
			}
		}
		);
	}

	
	function viewStatus(id) {
		$.ajax({
			url : base_url + "status/"+ id,
			type : "get",
			contentType: "application/json",
			success : function (data) {
				$(document).scrollTop(0);
				$(".main").html(data);
				$("#results-table").tableExport({
			 		bootstrap : true,
			 		position : "bottom"					
				});
				$(".xlsx").hide();
				$(".txt").hide();
				$(".csv").text('Export Result To CSV');

				$(".csv").css('background','#17a2b8');
				$(".csv").css('color','#fff');
				$(".csv").css('font-size','15px');
				$(".tableexport-caption").css('bottom' ,'auto');
				$(".tableexport-caption").css('left' ,'initial');
				$(".tableexport-caption").css('top' ,'175px');
				$(".tableexport-caption").css('right' ,'20px');
				$("#results-table tbody").hide();
			}
		}
		);
	}

	$("#export-btn").click(function(){
		alert('test');
	})

	function uploadImage( ) {
		$("#main").on('submit', '.upload_form', function (e) {
			e.preventDefault();
			var id = $(this).attr('id')
			
			var image_file = $("#" + id + " input[name=image_file]");

			if (image_file.val() === "") {
				alert('Select a file to upload');
			}else {
				var formData = new FormData( $("#" + id)[0] );
		        formData.append('id', id);

		        $.ajax({
		            url : base_url + 'image/upload',  // Controller URL
		            type : 'POST',
		            data : formData,
		            async : false,
		            cache : false,
		            contentType : false,
		            processData : false,
		            success : function(data) {
		           	 
		                $("#" + id).append("<p class='text-success'>Image Uploaded Successfully</p>"); 
		                $("#" + id)[0].reset();   
		            
		                var image_url = base_url + '/uploads/' + data;
		            
		                $("#avatar" + id).attr('src', base_url + "/uploads/" + data);      
		            }
		        });
			}
			
		});
	}


	
	uploadImage();
	view();
	statusButton();
	publishButton();
	addGroup();
	savePoll();

	$('#add-btn').click(function(){
		$('#modal-add').modal('show'); 
	})


	
})