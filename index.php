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
            <form name="create-account" action="/signup_action.php" onsubmit="return validateForm()" method="post">
                <div id="container">
                    <h1>Register</h1>
                    <p>Please fill in this form to create an account.</p>
                
                    <label class="signin-label" for="fname"><b>First Name</b></label>
                    <br>
                    <input class="signin-input" type="text" placeholder="Enter First Name" name="fname" required>
                    <br><br>
                    <label for="lname"><b>Last Name</b></label>
                    <input class="signin-input" type="text" placeholder="Enter Last Name" name="lname" required>
                    
                    <label for="username"><b>Username</b></label>
                    <input class="signin-input" type="text" placeholder="Enter Username" name="username" required>
                    
                    <label for="psw"><b>Password</b></label>
                    <input class="signin-input" type="password" placeholder="Enter Password" name="password" required> 
                    
                    <br><br>
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