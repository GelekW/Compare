<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'signout') {
            $_SESSION["userName"] = null;
            $_SESSION = array();
            session_destroy();
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
                    $results[$i] = array();
                    $results[$i]["title"] = $row["title"];
                    $results[$i]["category"] = $row["category"];
                    $results[$i]["publishDate"] = $row["publishDate"];
                    $results[$i]["numOfLikes"] = $row["numOfLikes"];
                    $results[$i]["mainText"] = $row["mainText"]; 
                    $i++;
                }
                return $results;
            } else {
                return null;
            }
        }

        public function storyByTitle($title) {
            $sql = "SELECT * FROM Stories WHERE title='$title';";

            $result = $this->_connection->query($sql);

            if ($result->num_rows == 1) {
                return $result->fetch_assoc();
            } else {
                return null;
            }
        }

        public function storyByDate() {
            $sql = "SELECT * FROM Stories ORDER BY publishDate;"

            $result = $this->_connection->query($sql);

            if ($result->num_rows > 0) {
                $results = array();
                $i = 0;
                while($row = $result->fetch_assoc()) {
                    $results[$i] = array();
                    $results[$i]["title"] = $row["title"];
                    $results[$i]["category"] = $row["category"];
                    $results[$i]["publishDate"] = $row["publishDate"];
                    $results[$i]["numOfLikes"] = $row["numOfLikes"];
                    $results[$i]["mainText"] = $row["mainText"]; 
                    $i++;
                }
                return $results;
            } else {
                return null;
            }
        }

        public function createUserComment($username, $storyTitle, $comment) {
            $sql = "INSERT INTO UserComments (comment, numOfLikes) VALUES ('$comment', 0);";

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
    
        public function __destruct() {
            $this->_connection->close();
        }
    }
?>