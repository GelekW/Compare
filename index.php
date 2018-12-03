<?php
    require_once('database.php');
    
    $fname = isset ($_POST['fname']) ? $_POST['fname'] : null;
    $lname = isset ($_POST['lname']) ? $_POST['lname'] : null;
    $username = isset ($_POST['username']) ? $_POST['username'] : null;
    $password = isset ($_POST['password']) ? $_POST['password'] : null;

    $database = new Database("localhost", "root", "", "");

    $status = null;

    if ($fname != null && $lname != null && $username != null && $password != null) {
        $status = $database->createUser($username, $password, $fname, $lname);
    }
?>
<!DOCTYPE html>
<html>
    <head> 
        <title>Compare</title> 
        <link rel="stylesheet" type="text/css" href="index.css">
        <script src="/create_account.js"></script>
    </head>
    <body>
        <div>
            <img src=comparelogo.png alt="logo" id="logo">
        </div>
            <form name="create-account" action="/index.php" onsubmit="return validateForm()" method="post">
                <div id="container">
                    <h1>Register</h1>
                    <p><?php 
                    if ($status == null) {
                        echo "Please fill in this form to create an account."; 
                    } else if ($status == 1) {
                        echo "Account created successfully! <br> Please go to signin page.";
                    } else if ($status == 2) {
                        echo "Username already taken! <br> Please choose a different username!";
                    } else {
                        echo "Unknown Error!";
                    }
                    ?></p>
                
                    <label class="signin-label" for="fname"><b>First Name</b></label>
                    <input class="signin-input" type="text" placeholder="Enter First Name" name="fname" <?php if ($status == 2) echo "value='$fname'"?> required>
                    
                    <label for="lname"><b>Last Name</b></label>
                    <input class="signin-input" type="text" placeholder="Enter Last Name" name="lname" <?php if ($status == 2) echo "value='$lname'"?> required>
                    
                    <label for="username"><b>Username</b></label>
                    <input class="signin-input" type="text" placeholder="Enter Username" name="username"  <?php if ($status == 2) echo "value='$username'"?> required>
                    
                    <label for="psw"><b>Password</b></label>
                    <input class="signin-input" type="password" placeholder="Enter Password" name="password" required> 
                    
                    <label for="psw2"><b>Confirm Password</b></label>
                    <input class="signin-input" type="password" placeholder="Confirm Password" name="password2" required>

                    <button type="submit" class="registerbtn" id="button" name="submit">Register</button>
                </div>
  
                <div id="signin">
                    <p>Already have an account? <a href="signin.php">Sign in</a></p>
                    
                </div>
            </form>
    </body>
</html>