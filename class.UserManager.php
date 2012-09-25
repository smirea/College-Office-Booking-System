<?php

class UserManager{
	
	var $path 		= 'class.UserManager.php';
	var $table		= 'users';
	
	private $RegExps	= array(
		'email'		=> "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@[a-zA-Z0-9_-]+([a-zA-Z0-9\._-]+)*\.[a-zA-Z]+$/",
		'username'	=> "/^[a-zA-Z][a-zA-Z0-9_\-\.]+$/",
		'password'	=> "/.+/"
	);
	private $FieldSize	= array(
		'email'		=> array(7, 100),
		'username'	=> array(5, 30),
		'password'	=> array(5, 100)
	);
	private $errors = array();
	
	
	/**
	 * Logs in an user. Automatically takes the information from $_POST if the $username is set to null
	 * @param $username the username of the account
	 * @param $password the password of the account
	 * @return boolean true/false upon success/failure
	**/
	function logIn( $username = null, $password = null ){
		
		if( !$username ){
			if( !isset($_POST['username']) || !isset($_POST['password']) ) 
				return false;
			
			$username	= $_POST['username'];
			$password	= $_POST['password'];
		}
		
		$password = md5( $password );
		$q = "SELECT id FROM ".$this->table." WHERE username='$username' AND password='$password'";
		
		if( mysql_num_rows( mysql_query( $q ) ) == 0 ){
			$this->err('Username and/or password invalid');
			return false;
		} else {
			$_SESSION['loggedIn'] = true;
			$_SESSION['username'] = $username;
		}
		
		return true;
	}
	
	/**
	 * Sets all UserManager specific session variables to null, but does not closes the session
	 * @return $this
	**/
	function logOut(){
		unset( $_SESSION['loggedIn'] );
		unset( $_SESSION['username'] );
		
		return $this;
	}
	
	/**
	 * Adds a new user to the database. Automatically takes the information from $_POST if the $username is set to null
	 * @param $username the username of the account
	 * @param $mail the email of the user
	 * @param $p1 the password
	 * @param $p2 the retyped password
	 * @return boolean true/false upon success/failure
	**/
	function register( $username = null, $email = null, $p1 = null, $p2 = null ){
		
		if( !$username ){
			if( !isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['password2']) )
				return false;
			
			$username	= $_POST['username'];
			$email		= $_POST['email'];
			$p1			= $_POST['password'];
			$p2			= $_POST['password2'];
		}
		
		$var = array(
			'username'	=> $username,
			'email'		=> $email,
			'password'	=> $p1
		);
		
		/** Begin validating data **/
		
		if( !$var['username'] || !$var['email'] || !$p1 || !$p2 )
			$this->err('All fields are mandatory!');
		
		foreach($var as $k => $v){
			if( $this->RegExps[ $k ] )
				if( !preg_match( $this->RegExps[ $k ], $v ) )
					$this->err("Invalid <b>$k</b>!");
				
			$min = $this->FieldSize[$k][0];
			$max = $this->FieldSize[$k][1];
			if( strlen($v) < $min || strlen($v) > $max )
				$this->err("The <b>$k</b> field must be between <b>$min</b> and <b>$max</b> characters long!");
		}
		
		if( !$p1 || !$p2 || $p1 != $p2 )
			$this->err('Passwords do not match!');
      
		if( mysql_num_rows( mysql_query( "SELECT * FROM ".$this->table." WHERE username='".$var['username']."'" ) ) > 0 )
			$this->err('Username already exists!');
		
		if( mysql_num_rows( mysql_query( "SELECT * FROM ".$this->table." WHERE email='".$var['email']."'" ) ) > 0 )
			$this->err('Email already registered to another account!');
		
		/** Finish validating data **/
		
		if( count($this->errors) > 0 ) return false;
		
		$password = md5( $p1 );
		$q = "INSERT INTO ".$this->table." (username, email, password) VALUES ('".$var['username']."', '".$var['email']."', '$password')";
		
		if( !mysql_query( $q ) ){
			$this->err('Unable to send the information to the database!');
			return false;
		}
		
		return true;
	}
	
	/**
	 * Sets a field in the $this->RegExps
	 * @param $index the name of the field
	 * @param $value the new Regular Expression
	 * @return $this
	**/
	function setRegExp( $index, $value ){
		if( $index )
			$this->RegExps[ $index ] = $value;

		return $this;
	}
	
	/**
	 * Adds an error to the error stack
	 * @param $err the text of the error
	 * @return $this. Alternatively, it returns the $this->RegExps array if $err is null
	**/
	private function err( $err ){
		if( $err ){
			$this->errors[] = $err;
		} else {
			return $this->errors();
		}
		
		return $this;
	}
	
	/**
	 * Prints out all errors in the $this->errors array
	 * @return $this
	**/
	function showErrors(){
		foreach( $this->errors as $v )
			echo "$v<br />";
		
		return $this;
	}
	
	/**
	 * Prints out a registration form
	 * @return $this
	**/
	function registerForm(){
		$h = '
			<form action="'.$this->path.'" method="post" class="userManager registerForm">
				<fieldset>
					<legend>Register a new account</legend>
					<table>
						<tr><td>Username:</td><td><input type="text" name="username" /></td></tr>
						<tr><td>Email:</td><td><input type="text" name="email" /></td></tr>
						<tr><td>Password:</td><td><input type="password" name="password" /></td></tr>
						<tr><td>Password retype:</td><td><input type="password" name="password2" /></td></tr>
						<tr><td colspan="2" align="right"><input type="submit" value="Register" /></td></tr>
					</table>
				</fieldset>
			</form>
		';
		echo $h;
		
		return $this;
	}
	
	/**
	 * Prints out a log-in form
	 * @return $this
	**/
	function logInForm(){
		$h = '
			<form action="'.$this->path.'" method="post" class="userManager loginForm">
				<fieldset>
					<legend>Log in</legend>
					<table>
						<tr><td>Username:</td><td><input type="text" name="username" /></td></tr>
						<tr><td>Password:</td><td><input type="password" name="password" /></td></tr>
						<tr><td colspan="2" align="right"><input type="submit" value="Log In" /></td></tr>
					</table>
				</fieldset>
			</form>
		';
		echo $h;
		
		return $this;
	}
}
?>

<? $UM = new UserManager(); ?>


