<?php
    
    include('credentials.php');

    class Database
    {
        var $db = null;
        
        function __construct()
        {
            global $db_username;
            global $db_password;
            global $db_name;
            
            $this->db = mysqli_connect('localhost', $db_username, $db_password, $db_name);
            if ($this->db == null) {
                http_response_code(500);
                error_log("Connection failed with error: " . mysqli_connect_error() . "\n");
                exit;
            }
        }
        
        function query($sql)
        {
            $result = mysqli_query($this->db, $sql);
            if (mysqli_error($this->db)) {
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