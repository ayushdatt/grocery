<?php

session_start();
$con=mysql_connect("localhost","root","root");
mysql_select_db("home",$con);


$result = mysql_query("SELECT * FROM item", $con);
$num_rows = mysql_num_rows($result);

    $query=mysql_query("SELECT name,quantity,threshold from item");
    $i=0;
    $namearr=array();
    $qtyarr=array();
    while(list($name,$quantity,$threshold)= mysql_fetch_row($query))
    {
                if($threshold> $quantity)
                {
                    $namearr[$i]=$name;
                    $qtyarr[$i]=$threshold - $quantity + 5 ;
                    $i=$i+1;
                }
    }
    //print_r($namearr);
    $var=$i;
    //echo "in function check order=";
    
    $ch = curl_init();
    $query="rquest=placeOrder&num_items=".$var."&item_name[]=".serialize($namearr)."&qty[]=".serialize($qtyarr);
    //echo $query;
    //exit(0);
    curl_setopt ($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_URL, "localhost/grocery/storeApi.php");
    curl_setopt($ch, CURLOPT_POSTFIELDS,$query);
    curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 (CK) Firefox/3.0.1");
    curl_setopt ($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    //echo $result;
    curl_close($ch);
    
    //header('Location: /home/home.php');   
    $var= json_decode($result);
    //$var=$result;
    //print_r($var);
    
    echo "Total=".$var->{'total'};
    echo "<br \>";
    echo "Order_ID=".$var->{'orderID'};
    echo "<br \>";
    echo "PIN=".$var->{'pin'};
    echo "<br \>";
    
    
    /////////////////////////////////
?>    
<form action="./deliveryApi.php" method="POST">
            <input type="text" name="rquest" value="candeliver" hidden>
            Source<input type="number" name="source" value="2"><br>
            Destination<input type="number" name="destination" value="3"><br>
            <input type="submit" name="submit">
        </form>
    

