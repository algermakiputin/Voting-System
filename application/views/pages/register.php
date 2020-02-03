<!-- <!DOCTYPE html>
<html>
<head>
    <title>Polling System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/index.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    
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
                        <span id="main-heading">Precious</span><br>
                        <span id="sub-heading">International School Of Davao</span>
                    </td>
                </tr>
            </table>
            
        </div>
        <nav class="text-right">
        
                <div class="col-md-10 mx-auto">
                    <ul >
                        <a href="<?php echo base_url() ?>">
                            <li><i class="fa fa-tachometer"></i> Dashboard </li>
                        </a>
                        <?php if ($this->session->userdata('staff')):?>
                            
                            <a id="view_users">
                                <li><i class="fa fa-list"></i> View Users </li>
                            </a>
                            <a href="<?php echo base_url() ?>register">
                                <li><i class="fa fa-user"></i> Register Users </li>
                            </a>
                        <?php endif;?>
                        <a href="<?php echo base_url() ?>lagout">
                            <li><i class="fa fa-sign-out"></i> SIGN OUT </li>
                        </a>
                    </ul>
                </div>
        
            
        </nav>
    </div>
</header>
<body> -->

<div class="poll pad-15">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <h4 class="">Register User</h4>
            
            <?php
                if ($this->session->flashdata('message')) {
                    ?>

                    <div class="alert alert-danger">
                        <a  class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                        <?php echo $this->session->flashdata('message'); ?>

                    </div>
                    <?php
                }

                if ($this->session->flashdata('successMessage')) {

                     ?>

                    <div class="alert alert-success">
                        <?php 
                            echo $this->session->flashdata('successMessage');
                        ?>
                        
                    </div>

                     <?php
                }
            ?>

            <form  class="form" role="form" autocomplete="off" id="registerForm"  novalidate="" method="POST">
                 <div id="messageBox">
                
                 </div>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" class="form-control form-control-md " name="name" id="full_name" required="">
           
                </div>
                <div class="form-group">
                    <div class="row">
                         <div class="col-md-6">
                             <label for="grade">Grade</label>
                                <select type="text" class="form-control form-control-md " name="grade" id="grade" required="">
                                    <option>Select Grade</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                         </div>
                         <div class="col-md-6">
                             <label for="section">Section</label>
                             <select type="text" class="form-control form-control-md " name="section" id="section" required="">
                                <option value="none">Select Section</option>
                                <option value="BETHESDA">BETHESDA</option>
                                <option value="GALILEE">GALILEE</option>
                                <option value="JORDAN">JORDAN</option>
                                <option value="SYCHAR">SYCHAR</option>
                                <option value="MTCARMEL">MT. CARMEL</option>
                                <option value="MTHOREB">MT. HOREB</option>
                                <option value="MTSINAI">MT. SINAI</option>
                                <option value="CEDAR">CEDAR</option>
                                <option value="MYRRH">MYRRH</option>
                                <option value="OLIVE">OLIVE</option>
                                <option value="SYCAMORE">SYCAMORE</option>
                             </select>
                         </div>
                    </div>
                    
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control form-control-md " name="username" id="username" required="">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control form-control-md" name="password" id="password" required="" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="conf_password">Confirm Password</label>
                    <input type="password" class="form-control form-control-md" name="conf_password" id="conf_password" required="" autocomplete="new-password">
                </div>
               
                <input type="submit" value="REGISTER" class="btn btn-primary btn-md  form-control" id="regBtn"></input>
            </form>
           
        </div>
    </div>
</div>
 
<!--  <div class="container page " id="home">
    <div class="row">
    	<div class="col-lg-12 mx-auto" id="login-form"  >
            <div class="main" id="#main">
                <div class="poll pad-15">
                    <div class="row">
                        <div class="col-lg-6 mx-auto">
                    		<h4 class="">Register User</h4>
                            <?php
                                if ($this->session->flashdata('message')) {
                                    ?>

                                    <div class="alert alert-danger">
                                        <a  class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                        <?php echo $this->session->flashdata('message'); ?>

                                    </div>
                                    <?php
                                }

                                if ($this->session->flashdata('successMessage')) {

                                     ?>

                                    <div class="alert alert-success">
                                        <?php 
                                            echo $this->session->flashdata('successMessage');
                                        ?>
                                        
                                    </div>

                                     <?php
                                }
                            ?>
                    		<form action ="<?php echo base_url(); ?>register/save" class="form" role="form" autocomplete="off" id="formLogin" novalidate="" method="POST">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" class="form-control form-control-md " name="name" id="full_name" required="">
                           
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                         <div class="col-md-6">
                                             <label for="grade">Grade</label>
                                                <select type="text" class="form-control form-control-md " name="grade" id="grade" required="">
                                                    <option>Select Grade</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select>
                                         </div>
                                         <div class="col-md-6">
                                             <label for="section">Section</label>
                                             <select type="text" class="form-control form-control-md " name="section" id="section" required="">

                                                <option value="none">Select Section</option>
                                                <option value="BETHESDA">BETHESDA</option>
                                                <option value="GALILEE">GALILEE</option>
                                                <option value="JORDAN">JORDAN</option>
                                                <option value="SYCHAR">SYCHAR</option>
                                                <option value="MTCARMEL">MT. CARMEL</option>
                                                <option value="MTHOREB">MT. HOREB</option>
                                                <option value="MTSINAI">MT. SINAI</option>
                                                <option value="CEDAR">CEDAR</option>
                                                <option value="MYRRH">MYRRH</option>
                                                <option value="OLIVE">OLIVE</option>
                                                <option value="SYCAMORE">SYCAMORE</option>

                                
                                             </select>
                                         </div>
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control form-control-md " name="username" id="username" required="">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control form-control-md" name="password" id="password" required="" autocomplete="new-password">
                                </div>
                                <div class="form-group">
                                    <label for="conf_password">Confirm Password</label>
                                    <input type="password" class="form-control form-control-md" name="conf_password" id="conf_password" required="" autocomplete="new-password">
                                </div>
                               
                                <input type="submit" value="REGISTER" class="btn btn-primary btn-md  form-control" id="btnLogin"></input>
                            </form>
                           
                        </div>
                    </div>
                </div>
            </div>
    	</div>
    </div>
</div> -->



