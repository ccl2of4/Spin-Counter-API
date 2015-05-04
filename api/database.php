<?php
    
    include('credentials.php');

    class Database
    {
        var $db = null;
        
        function __construct()
        {
            global $db_location;
            global $username;
            global $password;
            global $db_name;
            
            $this->db = mysqli_connect('localhost', $username, $password, $db_name);
            if ($this->db == null) {
                http_response_code(500);
                error_log("Connection failed with error: " . mysqli_connect_error() . "\n");
            }
        }
        
        function query($sql)
        {
            $result = mysqli_query($this->db, $sql);
            if (mysqli_error($this->db)) {
                http_response_code(500);
                error_log("Query \"" . $sql . "\" failed with error: " . mysqli_error($this->db) . "\n");
            }
            return $result;
        }
        
        function __destruct()
        {
            mysqli_close($this->db);
        }
    }
?>