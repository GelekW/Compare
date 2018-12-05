<?php 
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    require_once("database.php");

    if(isset($_SESSION["userName"])){
        $username = $_SESSION["userName"];
    } else {
        header("Location: signin.php");
        exit();
    } 

    $database = Database::instance();

    $title = $_GET['title'];

    $article = $database->storyByTitle($title);

    $commentText = isset ($_POST['commentText']) ? $_POST['commentText'] : null;

    $status = null;
    if ($commentText != null) {
        $status = $database->createUserComment($_SESSION['userName'], $title, $commentText);
    }

    $publisherName = $database->canEndorse($_SESSION['userName']);
    $isEndorsed = $database->isEndorsed($publisherName, $title);

    $endorsement = $database->getEndorsement($title);
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="view-article.css">
        <title><?php echo $title; ?></title>
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
                $('#deleteBtn').click(function(){
                    var ajaxurl = 'database.php';
                    var title = "<?php echo $title; ?>";
                    var data =  {'action': 'delete-article', 'article': title};
                    $.post(ajaxurl, data, function (response) {
                        alert("Deleted " + title + " Successfully!");
                        window.location.href = "home.php";
                    });
                });
                $('#endorseBtn').click(function(){
                    var ajaxurl = 'database.php';
                    var title = "<?php echo $title; ?>";
                    var publisherName = "<?php echo $publisherName; ?>";
                    var data = {'action': 'endorse', 'publisherName': publisherName, 'article': title};
                    $.post(ajaxurl, data, function (response) {
                        alert("Endorsed " + title + " Successfully!");
                        window.location.href = `view-article.php?title=${title}`;
                    });
                });
                $('#unendorseBtn').click(function(){
                    var ajaxurl = 'database.php';
                    var title = "<?php echo $title; ?>";
                    var publisherName = "<?php echo $publisherName; ?>";
                    var data = {'action': 'unendorse', 'publisherName': publisherName, 'article': title};
                    $.post(ajaxurl, data, function (response) {
                        alert("Un-Endorsed " + title + " Successfully!");
                        window.location.href = `view-article.php?title=${title}`;
                    });
                });
            });

            function deleteComment(commentId) {
                var title = "<?php echo $title; ?>";
                var ajaxurl = 'database.php';
                var data = {'action': 'delete-comment', 'commentId': commentId};
                $.post(ajaxurl, data, function (response) {
                    alert("Deleted Successfully!");
                    alert(response);
                    window.location.href = `view-article.php?title=${title}`;
                    
                });
            }
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
            <h1 id="title">
                <?php echo $article["title"]; ?>
                <?php 
                if ($article["userName"] == $_SESSION["userName"]) {
                    //Current user
                    echo "<button id='deleteBtn' class='deleteBtn' name='submit'>Delete</button>";
                }

                if ($publisherName != null) {
                    if (!$isEndorsed && $endorsement == null) {
                        echo "<button id='endorseBtn' name='submit'>Endorse</button>";
                    } else {
                        echo "<button id='unendorseBtn' name='submit'>Un-Endorse</button>";
                    }
                }
                ?>
            </h1>
            <hr>
            <h4>
                By <?php echo $article["fName"] . " " . $article["lName"] . "<br>"; ?>
                <?php echo $article["publishDate"]; ?>
                <?php if ($endorsement != null) {
                    echo "<br>Endorsed by " . $endorsement["publicationName"];
                }?>
            </h4>
            <div id="content">
                <p>
                    <?php echo $article["mainText"]; ?>
                </p>
            </div>
            <hr>
            <form method="post" <?php echo "action='view-article.php?title=$title'";?> id="commentform">
                <div id="write-user-comments">
                    <textarea name="commentText" form="commentform"></textarea>
                    <input type="submit" id="button" value="Post Comment">
                </div>
            </form>

            <hr>

            <div id="user-comments">
                <?php 
                    $comments = $database->getUserCommentsByStory($_GET["title"]);

                    if ($comments != null) {
                        foreach ($comments as $comment) {
                            echo "<div class='user-comment'>";
                            echo "<strong class='user-comment-name'>".$comment["userName"]."</strong>";
                            if ($_SESSION["userName"] == $comment["userName"]) {
                                echo "<button class=delete-comment-btn onclick='deleteComment(".$comment['id'].")'>Delete</button>";
                            }        
                                    echo "<div class='user-comment-post-date'>".$comment["postDate"]."</div>";
                                    
                                    echo "<p>".$comment["comment"]."</p>";
                                    
                            echo "</div>";
                        }
                    } else {
                        echo "No comments yet!";
                    }
                ?>
            </div>
        </div>
    </body>
</html>