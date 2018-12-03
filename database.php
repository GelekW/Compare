<?php
    class Database {
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
                session_start();
                $_SESSION['userName'] = $username;
                return 1;
            } else {
                return 0;
            }
        }
    
        public function __destruct() {
            $this->_connection->close();
        }
    }
?>