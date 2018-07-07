<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Login Form</title>
      <link rel="stylesheet" href="<?php echo base_url('assets/template/login')?>/css/style.css">  
</head>
<body>
  <div class="login">
  <div class="login-triangle"></div>
  <h2 class="login-header">Log in</h2>
  <form class="login-container" action="<?php echo site_url('login/superadmin/masuk') ?>" method="post">
    <p><input type="text" placeholder="Username" name="username"></p>
    <p><input type="password" placeholder="Password" name="password"></p>
    <center>
        
    <p><select name="status">
            <option value="superadmin">Superadmin</option>
            <option value="gudang">Gudang</option>
            <option value="toko">Toko</option>
        </select></p>
        <p><?php echo $image ?></p>
    </center>
        <p><input type="text" id="security_code" name="security_code" placeholder="Captcha"></p>
    <p><input type="submit" value="Log in"></p>
  </form>
  <?php 
    //mengeluarkan pesan eror
  if(validation_errors()){
      echo '<center>'.validation_errors().'</center>';
  }
  ?>
</div>
  <script src="<?php echo base_url() ?>/assets/grocery_crud/js/jquery-1.11.1.min.js"></script>

</body>
</html>
