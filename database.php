<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'signout') {
            $_SESSION["userName"] = null;
            $_SESSION = array();
            session_destroy();
        } else if ($_POST['action'] == 'delete-article' && isset($_POST['article'])) {
            $article = $_POST['article'];
            $db = Database::instance();

            $db->deleteStory($article);
        } else if ($_POST['action'] == 'endorse' && isset($_POST['article']) && isset($_POST['publisherName'])) {
            $publisherName = $_POST['publisherName'];
            $article = $_POST['article'];
            $db = Database::instance();

            $db->endorse($publisherName, $article);
        } else if ($_POST['action'] == 'unendorse' && isset($_POST['article']) && isset($_POST['publisherName'])) {
            $publisherName = $_POST['publisherName'];
            $article = $_POST['article'];
            $db = Database::instance();

            $db->unendorse($article);
        } else if($_POST['action'] == 'delete-comment' && isset($_POST['commentId'])){
            $commentId = $_POST['commentId'];
            $db = Database::instance();

            $db->deleteUserComment($commentId);
        }
    }

    class Database {
        public static function instance() {
            static $db = null;
            if ($db === null) {
                $db = new Database("localhost", "root", "");
            }
            return $db;
        }

        private $_connection = null;
    
        public function __construct($host, $userName, $password) {
            $this->_connection = new mysqli($host, $userName, $password);
            $this->_connection->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'Compare'");
            $this->_connection->query("CREATE DATABASE IF NOT EXISTS Compare;");
            $this->_connection->query("USE Compare;");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Users (
                userName varchar(255) NOT NULL, 
                password TEXT NOT NULL, 
                fName TEXT NOT NULL, 
                lName TEXT NOT NULL, 
                PRIMARY KEY (userName)
                );
            ");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS UserComments (
                id int NOT NULL AUTO_INCREMENT, 
                comment TEXT NOT NULL, 
                numOfLikes int,
                postDate DATETIME NOT NULL, 
                PRIMARY KEY (id)
                );
            ");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Stories (
                title varchar(255) NOT NULL, 
                category TEXT NOT NULL, 
                publishDate DATE NOT NULL, 
                numOfLikes int, 
                mainText TEXT NOT NULL, 
                PRIMARY KEY (title));
            ");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Publisher (
                name varchar(255) NOT NULL, 
                bio TEXT NOT NULL, 
                location TEXT NOT NULL,
                PRIMARY KEY (name));
            ");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Edit (
                userName varchar(255) NOT NULL, 
                publisherName varchar(255) NOT NULL, 
                FOREIGN KEY (userName) REFERENCES Users(userName), 
                FOREIGN KEY (publisherName) REFERENCES Publisher(name));
            ");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Wrote (
                userName varchar(255) NOT NULL, 
                title varchar(255) NOT NULL, 
                FOREIGN KEY (userName) REFERENCES Users(userName), 
                FOREIGN KEY (title) REFERENCES Stories(title));
            ");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Comment (
                userName varchar(255) NOT NULL, 
                commentId int NOT NULL, 
                storyTitle varchar(255) NOT NULL, 
                FOREIGN KEY (userName) REFERENCES Users(userName), 
                FOREIGN KEY (commentId) REFERENCES UserComments(id), 
                FOREIGN KEY (storyTitle) REFERENCES Stories(title));
            ");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Publish (
                publicationName varchar(255) NOT NULL, 
                storyTitle varchar(255) NOT NULL, 
                FOREIGN KEY (publicationName) REFERENCES Publisher(name), 
                FOREIGN KEY (storyTitle) REFERENCES Stories(title));
            ");
        }
    
        public function createUser($userName, $password, $firstName, $lastName) {
            $sql = "INSERT INTO Users (userName, password, fName, lName) VALUES ('$userName', '$password', '$firstName', '$lastName')";

            if ($this->_connection->query($sql) === TRUE) {
                header("Location: signin.php");
                exit();
                return 1;
            } else {
                if (strpos($this->_connection->error, "Duplicate") !== false) {
                    return 2;
                } else {
                    return 3;
                }
            }
        }

        public function signin($username, $password) {
            $sql = "SELECT * FROM Users WHERE userName='$username' AND password='$password';";

            $result = $this->_connection->query($sql);

            if ($result->num_rows == 1) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['userName'] = $username;
                $_SESSION['fName'] = $result->fetch_assoc()["fName"];
                header("Location: home.php");
                exit();
                return 2;
            } else {
                return 1;
            }
        }

        public function storiesByUser($username) {
            $sql = "SELECT * FROM Wrote INNER JOIN stories ON Wrote.title = stories.title WHERE userName='$username';";

            $result = $this->_connection->query($sql);

            if ($result->num_rows > 0) {
                $results = array();
                $i = 0;
                while($row = $result->fetch_assoc()) {
                    $results[$i] = $row;
                    $i++;
                }
                return $results;
            } else {
                return null;
            }
        }

        public function storyByTitle($title) {
            $sql = "SELECT * FROM Wrote INNER JOIN Stories ON Wrote.title = Stories.title INNER JOIN Users ON Wrote.userName = Users.userName WHERE Stories.title='$title';";
            $result = $this->_connection->query($sql);

            if ($result != null && $result->num_rows == 1) {
                return $result->fetch_assoc();
            } else {
                return null;
            }
        }

        public function storyByDate() {
            $sql = "SELECT * FROM Wrote INNER JOIN Stories ON Wrote.title = Stories.title INNER JOIN Users ON Wrote.userName = Users.userName ORDER BY publishDate;";

            $result = $this->_connection->query($sql);

            if ($result->num_rows > 0) {
                $results = array();
                $i = 0;
                while($row = $result->fetch_assoc()) {
                    $results[$i] = $row;
                    $i++;
                }
                return $results;
            } else {
                return null;
            }
        }

        public function deleteStory($storyTitle) {
            $sql = "SELECT * FROM Comment WHERE storyTitle='$storyTitle';";

            $result = $this->_connection->query($sql);

            if ($result != null && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $this->deleteUserComment($row["commentId"]);
                }
            }

            $sql = "SELECT * FROM Publish WHERE storyTitle='$storyTitle';";

            $result = $this->_connection->query($sql);

            if ($result != null && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $this->unendorse($row["storyTitle"]);
                }
            }

            $sql = "DELETE FROM Wrote WHERE title='$storyTitle';";

            if ($this->_connection->query($sql) === TRUE) {
                $sql = "DELETE FROM Stories WHERE title='$storyTitle';";

                if ($this->_connection->query($sql) === TRUE) {
                    return 2;
                } else {
                    return 1;
                }
            } else {
                return 1;
            }
        }

        public function createUserComment($username, $storyTitle, $comment) {
            $sql = "INSERT INTO UserComments (comment, numOfLikes, postDate) VALUES ('$comment', 0, NOW());";

            $result = $this->_connection->query($sql);
            if ($result === TRUE) {
                $commentId = $this->_connection->insert_id;
                $sql = "INSERT INTO Comment (userName, commentId, storyTitle) VALUES('$username', $commentId, '$storyTitle');";
                $result = $this->_connection->query($sql);
                if ($result === TRUE) {
                    return 2;
                } else {
                    return 1;
                }
            } else {
                return 1;
            }
        }

        public function deleteUserComment($commentId) {
            $sql = "DELETE FROM Comment WHERE commentId='$commentId';";

            if ($this->_connection->query($sql) === TRUE) {
                $sql = "DELETE FROM UserComments WHERE id='$commentId';";
                if ($this->_connection->query($sql) === TRUE) {
                    return 2;
                }
            }
            return 1;
        }

        public function getUserCommentsByStory($storyTitle) {
            $sql = "SELECT * FROM Comment INNER JOIN UserComments ON Comment.commentId = UserComments.id WHERE Comment.storyTitle='$storyTitle';";
        
            $result = $this->_connection->query($sql);

            if ($result != null && $result->num_rows > 0) {
                $results = array();
                $i = 0;
                while($row = $result->fetch_assoc()) {
                    $results[$i] = $row;
                    $i++;
                }
                return $results;
            } else {
                return null;
            }
        }

        public function publishStory($username, $title, $category, $mainText) {
            $sql = "INSERT INTO Stories VALUES ('$title', '$category', NOW(), 0, '$mainText');";

            $result = $this->_connection->query($sql);

            if ($result === TRUE) {
                $sql = "INSERT INTO Wrote VALUES('$username', '$title');";
                $result = $this->_connection->query($sql);
                if ($result === TRUE) {
                    return 2;
                } else {
                    return 1;
                }
            } else {
                return 1;
            }
        }

        public function canEndorse($userName) {
            $sql = "SELECT * FROM Edit WHERE userName='$userName';";

            $result = $this->_connection->query($sql);

            if ($result != null && $result->num_rows > 0) {
                return $result->fetch_assoc()["publisherName"];
            }
            return null;
        }

        public function endorse($storyTitle, $publisherName) {
            $sql = "INSERT INTO Publish VALUES('$storyTitle', '$publisherName');";

            $result = $this->_connection->query($sql);

            if ($result === TRUE) {
                return 2;
            } else {
                return 1;
            }
        }

        public function unendorse($storyTitle) {
            $sql = "DELETE FROM Publish WHERE storyTitle='$storyTitle';";

            if ($this->_connection->query($sql) === TRUE) {
                return 2;
            } else {
                return 1;
            }
        }

        public function getEndorsement($title) {
            $sql = "SELECT * FROM Publish WHERE storyTitle='$title'";

            $result = $this->_connection->query($sql);

            if ($result != null && $result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            return null;
        }

        public function isEndorsed($publisherName, $storyTitle) {
            $sql = "SELECT * FROM Publish WHERE publicationName='$publisherName' AND storyTitle='$storyTitle';";

            $result = $this->_connection->query($sql);

            if ($result != null && $result->num_rows > 0) {
                return true;
            }
            return false;
        }
    
        public function __destruct() {
            $this->_connection->close();
        }
    }
?>