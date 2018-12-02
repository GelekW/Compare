<?php
    class Database {
        private $_connection = null;
    
        public function __construct($host, $userName, $password) {
            $this->_connection = new mysqli($host, $userName, $password);
            $this->_connection->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'Compare'");
            $this->_connection->query("CREATE DATABASE IF NOT EXISTS Compare;");
            $this->_connection->query("USE Compare;");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Users (userName varchar NOT NULL, password varchar NOT NULL, fName varchar NOT NULL, lName varchar NOT NULL, PRIMARY KEY (userName));");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS UserComments (id int NOT NULL AUTO_INCREMENT, comment varchar NOT NULL, numOfLikes int, PRIMARY KEY (id));");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Stories (title varchar NOT NULL, category varchar NOT NULL, publishDate DATE NOT NULL, numOfLikes int);");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Publisher (name varchar NOT NULL, bio varchar NOT NULL, location varchar NOT NULL);");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Edit (userName varchar NOT NULL, publisherName varchar NOT NULL, FOREIGN KEY (userName) REFERENCES Users(userName), FOREIGN KEY (publisherName) REFERENCES Publisher(name));");
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Write (userName varchar NOT NULL, title varchar NOT NULL, FOREIGN KEY (userName) REFERENCES Users(userName), FOREIGN KEY (title) REFERENCES Stories(title));");
            echo "<br>" . $this->_connection->error;
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Comment (userName varchar NOT NULL, commentId int NOT NULL, storyTitle varchar NOT NULL, FOREIGN KEY (userName) REFERENCES Users(userName), FOREIGN KEY (commentId) REFERENCES UserComments(id), FOREIGN KEY (storyTitle) REFERENCES Stories(title));");
            echo "<br>" . $this->_connection->error;
            $this->_connection->query("CREATE TABLE IF NOT EXISTS Publish (publicationName varchar NOT NULL, storyTitle varchar NOT NULL, FOREIGN KEY (publicationName) REFERENCES Publisher(name), FOREIGN KEY (storyTitle) REFERENCES Stories(title));");
            echo "<br>" . $this->_connection->error;
        }
    
        public function createUser($userName, $password, $firstName, $lastName) {
            $sql = "INSERT INTO Users (UserName, Password, F_Name, L_Name) VALUES ('$userName', '$password', '$firstName', '$lastName')";

            if ($this->_connection->query($sql) === TRUE) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $this->_connection->error;
            }
        }
    
        public function __destruct() {
            $this->_connection->close();
        }
    }
?>