	<!DOCTYPE html>
<html>
<head>
	<title>E-Voting System</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>Assets/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>Assets/css/index.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>Assets/css/responsive.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>Assets/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>Assets/css/basictable.css">
	<link href = "<?php echo base_url() ?>Assets/images/logo.png" rel="icon" type="image/gif">
</head>
<header>
	<div id="site-header" >
		<div class="container">
			 <table id="logo">
                <tr>
                    <td>
                        <img src="<?php echo base_url(); ?>Assets/images/logo.png">
                    </td>
                    <td>
                        <span id="main-heading">E-Voting</span><br>
                        <span id="sub-heading">School Election Voting System</span>
                    </td>
                </tr>
            </table>
			
		</div>
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
		  <div class="container">
		  	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>

		  <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
		    <div class="ml-auto text-right ">
					<ul id="main-nav" class="">
						<a id="dashboard">
							<li class="active"><i class="fa fa-tachometer"></i> Dashboard </li>
						</a>
						<?php if ($this->session->userdata('staff')):?>
							<a id="import-csv">
								<li><i class="fa fa-file"></i> Import CSV </li>
							</a>
							<a id="view_users">
								<li><i class="fa fa-list"></i> View Users </li>
							</a>
							<a id="register_user">
								<li><i class="fa fa-user"></i> Register Users </li>
							</a>
						<?php endif;?>
						<a href="<?php echo base_url() ?>lagout">
							<li><i class="fa fa-sign-out"></i> Logout </li>
						</a>
					</ul>
				</div>
		  </div>
		  </div>
		</nav>
 
	</div>
</header>
<body>

