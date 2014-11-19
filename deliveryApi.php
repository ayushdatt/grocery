<?php
	
	require_once("Rest.inc.php");
	
	class API extends REST {
	
		public $data = "";
		
		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "root";
		const DB = "delivery";
		
		private $db = NULL;
	
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->dbConnect();					// Initiate Database connection
		}
		
		/*
		 *  Database connection 
		*/
		private function dbConnect(){
			$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
			if($this->db)
				mysql_select_db(self::DB,$this->db);
		}
		
		/*
		 * Public method for access api.
		 * This method dynmically call the method based on the query string
		 *
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
                        
                        if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
		}
		

		private function checkLocation($coordinates){
			//If can deliver to coordinate then return 1 else 0
			return 1;
		}

		private function canDeliver(){
                    $source=1;
                    $destination=1;
                    $result=-1;
                    
			if( $this->checkLocation($source) ){
				if($this->checkLocation($destination)){
                                    
                                    $result=1;
				}
				else{
					$result=0;
				}
			}
                        echo $result;
                        echo '<form action="./deliveryApi.php" method="POST">
            <input type="text" name="rquest" value="confirmDelivery" hidden>
            Source<input type="number" name="source" value="2"><br>
            Destination<input type="number" name="destination" value="3"><br>
            OrderID<input type="number" name="orderID" value="2"><br>
            PIN<input type="number" name="PIN" value="3"><br>
            <input type="submit" name="submit">
        </form>';
                        //$this->response($this->json($result), 200);
		}

		private function confirmDelivery(){
                    //$source, $destination, $orderID, $pin
			//confirm with store the orderID and Pin, does it exists in the database of the store.
                    echo "delivery confirmed from store";
                    return 1;
		}

        //delivery pick order 
        private function pickOrder(){
        	//pick the order from the store
        	//send store (orderID, pin and time);
        	$status=1;
        	return $status;
        }

		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
                
	}
	
	// Initiiate Library
	
	$api = new API;
	$api->processApi();
?>