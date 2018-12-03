<?php
if(isset($_SESSION)){
    $username=$_SESSION("userName");
    
}
else{
   header("Location: signin.php");
   exit();
} 
?>

<!DOCTYPE html>
<html>

<?php
 require_once('database.php');
 $articleName = isset ($_POST['articleName']) ? $_POST['articleName'] : null;
 $articleText = isset ($_POST['articleText']) ? $_POST['articleText'] : null;
 $articleCategory = isset ($_POST['articleText']) ? $_POST['articleText'] : null;
 
 $database = new Database("localhost","root","","");

 $status = null;

 if($articleName != NULL && $articleText != NULL && $articleCategory != NULL){
    
    $status=$database->publishStory($articleName,$articleCategory,$articleText);
    
    if($status == 1){
        echo "Article Title is not unique, please choose new Title";
    }
    else if($status == 2){
        echo "Article Success";
        sleep(0.5);
        header("Location: feed.php");
        exit();
    }
 }

 ?>

<head>
<meta charset= "UTF-8">
<title>Something Dumb</title>
<link rel="stylesheet" type="text/css" href="write-article.css">
</head>

<body>
    <div id="container">
        
        <form method="POST" action="/write-article.php" id="writingform">
        <p> Article Name: </p> <input type="text" name="articleName">
        <p> Article Category: </p> <input type="text" name="articleCategory">
        <input type="submit">
        </form>

        <p>Start Writing:</p>
        <br>
        <textarea name="articleText" form="writingform"></textarea>

    </div>

    <p>Feeling Trapped? <a href="/index.php">Return to Home Page</a></p>
</body>

</html>