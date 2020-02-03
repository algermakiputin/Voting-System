<!DOCTYPE html>
<html>
<head>
    <title>Login - E-Voting System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>Assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>Assets/css/index.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>Assets/font-awesome-4.7.0/css/font-awesome.min.css">
    <link href = "<?php echo base_url() ?>Assets/images/logo.png" rel="icon" type="image/gif">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>Assets/css/responsive.css">
</head>
<body style="background: #fff;">
 <div class="container"> 
    <div class="row">
    	<div class="col-md-5 mx-auto" id="login-form" style="margin-top: 65px;">
            <table id="logo">
                <tr>
                    <td>
                        <img src="<?php echo base_url(); ?>Assets/images/logo.png">
                    </td>
                    <td>
                        <span id="main-heading">Precious</span><br>
                        <span id="sub-heading">International School Of Davao - Voting System</span>
                    </td>
                </tr>
            </table>
            <h4>Login</h4>
    		<form action="<?php echo base_url(); ?>login/auth" class="form" role="form" autocomplete="off" id="formLogin" novalidate="" method="POST">
               
                <?php

                if ($this->session->flashdata('errorMessage')) {
                    ?>

                    <div class="alert alert-danger">

                        <?php echo $this->session->flashdata('errorMessage'); ?>

                    </div>
                    <?php
                }

                ?>


                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control form-control-lg rounded-1" name="username" id="uname1" required="">
                    <div class="invalid-feedback">Oops, you missed this one.</div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input name="password" type="password" class="form-control form-control-lg rounded-1" id="pwd1" required="" autocomplete="new-password">
                    <div class="invalid-feedback">Enter your password too!</div>
                </div>
              
                <button type="submit" class="btn btn-primary btn-lg  form-control" id="btnLogin">Login</button>
            </form>
    	</div>

    </div>
</div>