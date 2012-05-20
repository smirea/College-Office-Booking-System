<form action="login.php" method="post">
  <fieldset class="loginBox">
    <legend>LogIn - CampusNet Credentials</legend>
    <input type="text" name="user" placeholder="username" />
    <input type="password" name="pass" placeholder="password" />
    <div style="text-align:right">
      <span style="color:red; font-weight:bold">
        <?php echo isset($_REQUEST['error']) ? $_REQUEST['error'] : ''; ?>
      </span>
      <input type="submit" value="Log In" />
    </div>
    <div class="disclaimer">
      <div style="color:red">
        * DO NOT try to log in more than 3 times in a row. If it does not work, use the admin user!
        </div>
      <div>
        * Credentials are not saved nor cached in any way
      </div>
    </div>
  </fieldset>
</form> 
