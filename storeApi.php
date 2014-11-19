<?php
	/* 
		This is an example class script proceeding secured API
		To use this class you should keep same as query string and function name
		Ex: If the query string value rquest=delete_user Access modifiers doesn't matter but function should be
		     function delete_user(){
				 You code goes here
			 }
		Class will execute the function dynamically;
		
		usage :
		
		    $object->response(output_data, status_code);
			$object->_request	- to get santinized input 	
			
			output_data : JSON (I am using)
			status_code : Send status message for headers
			
		Add This extension for localhost checking :
			Chrome Extension : Advanced REST client Application
			URL : https://chrome.google.com/webstore/detail/hgmloofddffdnphfgcellkdfbfbjeloo
		
		I used the below table for demo purpose.
		
		CREATE TABLE IF NOT EXISTS `users` (
		  `user_id` int(11) NOT NULL AUTO_INCREMENT,
		  `user_fullname` varchar(25) NOT NULL,
		  `user_email` varchar(50) NOT NULL,
		  `user_password` varchar(50) NOT NULL,
		  `user_status` tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
 	*/
	
	require_once("Rest.inc.php");
	
	class API extends REST {
	
		public $data = "";
		
		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "root";
		const DB = "middleware";
		
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
			echo "Important func call ".$func;
                        echo "<br>";
                        if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
		}
		

		//called by the delivery system
		private function checkOrderIDAndPin($pin, $orderID){
			//check the database if the entry corresponding to orderID and pin is available
			return 1;
		}

        //delivery pick order 
        private function pickOrder($time, $orderID, $pin){
        	$ok=1;
        	if(checkOrderIDAndPin($pin, $orderID)){
        		//means that the pin is valid for the orderID
        		return $ok;
        	}
        	//prepare order before given time
        	return 0;//there is some problem the pin does not match the order id, INVALID request
        }

        private function checkAvailability($name, $qty){
        	echo "checking";
            //return a number of the availability or -1 if the item is not availble in the store
            return array(1, 10);
        }

        private function genPin(){
        	return 44;//return unique pin code for confirmation
        }

        private function genOrderID(){
        	return 100;
        }

        private function getPrice($name){
        	return 10;
        }

        private function checkQuan($name, $quan){
        	//return 1 if given quantity available
        	return 1;
        	//return 0 if given quantity not available
        }

        private function placeOrder(){

        	//confirms and places the order and redirects to the payment portal

			$num_items = $this->_request['num_items'];	
            $num_items = intval($num_items);

            $price=array();
            $status=array();
            $avl_qty=array();
            $total=0;
			if(!empty($num_items)){
	            $item_name=$this->_request['item_name'];
	            $qty=$this->_request['qty'];
	            for($i=0; $i<$num_items; $i++){
	            	$qty[$i]=intval($qty[$i]);
	                echo "printing";
	                echo $item_name[$i]." ".$qty[$i];
	                if($this->checkQuan($item_name[$i], $qty[$i])){
		                $temp=$this->getPrice($item_name[$i]);
		            	$price[$i]=$qty[$i]*$temp;
		            	$total=$total+$price[$i];
	            	}
	            }

	            $result=array();
	            $result["item_name"]=$item_name;
	            $result["qty"]=$qty;
	            $result["price"]=$price;
	            $result["total"]=$total;
	            $result["orderID"]=$this->genOrderID();
	            $result["pin"]=$this->genPin();
	            print_r($result);
				$this->response($this->json($result), 200);

			}
	        else{
	            echo "error expected params missing aaya";
	            // If invalid inputs "Bad Request" status message and reason
	            $error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
	            $this->response($this->json($error), 400);
	        }



        	//if the payment portal sends successful work done send receipt

        	//else return payment failed :/
        }
                
		private function getPriceListAndAvailability(){
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			
			$num_items = $this->_request['num_items'];	
            $num_items = intval($num_items);
                        echo $num_items;

                        // Input validations

            $price=array();
            $status=array();
            $avl_qty=array();
			if(!empty($num_items)){
	            $item_name=$this->_request['item_name'];
	            $qty=$this->_request['qty'];
	            for($i=0; $i<$num_items; $i++){
	            	$qty[$i]=intval($qty[$i]);
	                echo "printing";
	                echo $item_name[$i]." ".$qty[$i];
	                $temp=$this->checkAvailability($item_name[$i], $qty[$i]);
	            	$price[$i]=$temp[1];
	            	$avl_qty[$i]=$temp[0];
	            }

	            $result=array();
	            $result["item_name"]=$item_name;
	            $result["qty"]=$qty;
	            $result["price"]=$price;
	            $result["avl_qty"]=$avl_qty;
	            print_r($result);
				$this->response($this->json($result), 200);

			}
	        else{
	            echo "error expected params missing aaya";
	            // If invalid inputs "Bad Request" status message and reason
	            $error = array('status' => "Failed", "msg" => "Invalid Input");
	            $this->response($this->json($error), 400);
	        }
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