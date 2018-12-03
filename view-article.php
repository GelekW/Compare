<?php 
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    require_once("database.php");

    $database = Database::instance();

    $article = $database->storyByTitle($_GET["title"]);
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="view-article.css">
        <title>Home</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script>
            $(document).ready(function(){
                $('.signout').click(function(){
                    var ajaxurl = 'database.php',
                    data =  {'action': 'signout'};
                    $.post(ajaxurl, data, function (response) {
                        window.location.href = "signin.php";
                    });
                });
            });
        </script>
    </head>
    <body>
        <span id="top">
            <a href="home.php"><img src=logosmall.png alt="logo" id="logosmall"></a>
            <span id="account">            
                <p> Hello, <?php 
                echo $_SESSION['fName'] . " "; 
                ?>  <a id="signout" href="write-article.php">Create New Article</a> <button type="button" class="signout" id="signout" name="submit">Sign Out</button></p>
            </span>
        </span>
        <div id="container">

            
            <div id="title">
                <?php echo $article["title"]; ?>
            </div>
            <div>
                <?php echo $article["userName"]; ?>
            </div>
            <div id="date">
                <?php echo $article["publishDate"]; ?>
            </div>
            <div id="content">
                <p>
                    <?php echo $article["mainText"]; ?>
                </p>
            </div>
        </div>
    </body>
</html>