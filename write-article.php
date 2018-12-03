<?php
    require_once('database.php');
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION["userName"])){
        $username = $_SESSION["userName"];
    } else {
        header("Location: signin.php");
        exit();
    } 
?>

<!DOCTYPE html>
<html>

<?php

 $articleName = isset ($_POST['articleName']) ? $_POST['articleName'] : null;
 $articleText = isset ($_POST['articleText']) ? $_POST['articleText'] : null;
 $articleCategory = isset ($_POST['articleCategory']) ? $_POST['articleCategory'] : null;
 
 $database = Database::instance();

 $status = null;

 if($articleName != NULL && $articleText != NULL && $articleCategory != NULL){
    
    $status=$database->publishStory($username,$articleName,$articleCategory,$articleText);
    
    if($status == 1){
        echo "Article Title is not unique, please choose new Title";
    }
    else if($status == 2){
        echo "Article Success";
        sleep(0.5);
        header("Location: home.php");
        exit();
    }
 }

 ?>

<head>
<meta charset= "UTF-8">
<title>Something Dumb</title>
<link rel="stylesheet" type="text/css" href="index.css">
<link rel="stylesheet" type="text/css" href="write-article.css">
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
        
        <form method="POST" action="/write-article.php" id="writingform">
            <p> Article Name: </p> <input type="text" name="articleName" maxlength="30">
            <p> Article Category: </p> <input type="text" name="articleCategory" maxlength='15'>
            <p>Start Writing:</p>
            <textarea rows="20" cols="100" name="articleText" form="writingform"></textarea>
            <br><br>
            <input type="submit" id="button" value="Create Story">
        </form>
    </div>

    <p>Feeling Trapped? <a href="/index.php">Return to Home Page</a></p>
</body>

</html>