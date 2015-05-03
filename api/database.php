<?php
    
    class Database
    {
        var $db = null;

    	public static function singleton() {
    		static $singleton = null;
    		if ($singleton == null) {
    			$singleton = new static();
    		}
    		return $singleton;
    	}
        
        private function __construct()
        {
            $this->db = mysqli_connect('localhost', 'root', 'SpinCounter1', 'SPINCOUNTER');
            if ($this->db == null) {
                http_response_code(500);
                die("Connection failed with error: " . mysqli_connect_error() . "\n");
            }
        }

        public function query($sql)
        {
            $result = mysqli_query($this->db, $sql);
            if (mysqli_error($this->db)) {
                http_response_code(500);
                die("Query failed with error" . mysqli_error($this->db) . "\n");
            }
            return $result;
        }
        
        public function __destruct()
        {
            mysqli_close($this->db);
        }
    }
?>
