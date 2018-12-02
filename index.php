<!DOCTYPE html>
<html>
    <head> 
        <title>Compare</title> 
        <link rel="stylesheet" type="text/css" href="index.css">
    </head>
    <body>
        <div>
            <img src=comparelogo.png alt="logo" id="logo">
        </div>
            <form action="/action_page.php">
                <div id="container">
                    <h1>Register</h1>
                        <p>Please fill in this form to create an account.</p>
                        <hr>
                    
                    <label for="fname"><b>First Name</b></label>
                    <input type="text" placeholder="Enter First Name" name="fname" required>
                    <br><br>
                    <label for="lname"><b>Last Name</b></label>
                    <input type="text" placeholder="Enter Last Name" name="lname" required>
                    <hr>
                    <label for="username"><b>Username</b></label>
                    <input type="text" placeholder="Enter Username" name="username" required>
                    
                    <hr>
                    <label for="psw"><b>Password</b></label>
                    <input type="password" placeholder="Enter Password" name="password" required> 
                    
                    <br><br>
                    <label for="psw2"><b>Confirm Password</b></label>
                    <input type="password2" placeholder="Confirm Password" name="password2" required>


                    <hr>

                    <button type="submit" class="registerbtn" id="button">Register</button>
                </div>
  
                <div id="signin">
                    <p>Already have an account? <a href="signin.php">Sign in</a></p>
                    
                </div>
            </form>
    </body>
</html>