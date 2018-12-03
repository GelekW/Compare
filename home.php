<?php 
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    require_once("database.php");
    $database = Database::instance();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="index.css">
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
    <div class="container">
        <!-- <div class="articlebox">
                <p class="likes">likes</p>
                <a class="articletitle" href="view-article.php">I am an article title blah blah</a> 
                <nobr class="text"> by </nobr> 
                <nobr class="author">I am an author</nobr>
                <nobr class="text">, published on </nobr>
                <nobr class="date">00/00/0000</nobr>
        </div> -->
        <?php
            $articles = $database->storyByDate();
            foreach($articles as $article){
                echo "<div class='articlebox'>
                        <p class='likes'>".$article["category"]."</p>
                        <a class='articletitle' href='view-article.php?title=".$article["title"]."'>".$article["title"]."</a> 
                        <nobr class='text'> by </nobr> 
                        <nobr class='author'>".$article["fName"]." ".$article["lName"]."</nobr>
                        <nobr class='text'>, published on </nobr>
                        <nobr class='date'>".$article["publishDate"]."</nobr>
                    </div>
                    <br>";
            }
        ?>
    </div>

</body>
</html>