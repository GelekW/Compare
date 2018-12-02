<!DOCTYPE html>
<html>
    <head> 
        <title>Compare Sign In</title> 
        <link rel="stylesheet" type="text/css" href="index.css">
    </head>
    <body>
        <div>
            <img src=comparelogo.png alt="logo" id="logo">
        </div>
            <form action="/action_page.php">
                <div id="container">
                    <h1>Sign In</h1>
                        <p>Please enter your username and password.</p>

                    <hr>
                    <label for="username"><b>Username</b></label>
                    <input type="text" placeholder="Enter Username" name="username" required>
                    
                    <br><br>
                    <label for="psw"><b>Password</b></label>
                    <input type="password" placeholder="Enter Password" name="password" required> 
 


                    <hr>

                    <button type="submit" class="registerbtn" id="button">Sign In</button>
                </div>
  
                <div id="signin">
                    <p>Don't have an account? <a href="index.php">Sign up</a></p>
                    
                </div>
            </form>
    </body>
</html>