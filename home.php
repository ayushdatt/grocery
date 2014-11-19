<html>
<head>
<title>Database</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="bootstrap.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
function altRows(id){
	if(document.getElementsByTagName){  

		var i;
		var table = document.getElementById(id);  
		var rows = table.getElementsByTagName("tr"); 
		 
		for(i = 0; i < rows.length; i++){          
			if(i % 2 == 0){
				rows[i].className = "evenrowcolor";
			}else{
				rows[i].className = "oddrowcolor";
			}      
		}
	}
}

window.onload=function(){
	altRows('alternatecolor');
        altRows('alternatecolor2');
}
</script>
</head>
<?php
session_start();
$con=mysql_connect("localhost","root","root");
$user_ssn = $_SESSION['name'];

if(!$con)
{
  die('Could not connect' . mysql_error());
}
mysql_select_db("home",$con);
$query=mysql_query("SELECT name, quantity, threshold FROM item") or die("Invalid Query: " .mysql_query());
if(mysql_num_rows($query) != 0)
{
	echo "<table border=\"1\" class=\"altrowstable\" id=\"alternatecolor\" width=\"100%\">
<tr>
<td><b>Name</b></td>
<td><b>Quantity</b></td>
<td><b>Threshold</b></td>
</tr>";
	while(list($name,$quantity,$threshold)= mysql_fetch_row($query))
	{
		echo "<tr>
		<td>$name</td><td>$quantity</td><td>$threshold</td>";

 	} 
	echo "</table>";
}
else {
	echo 'No one has added any items so far.';
}
?>
    <a href="home_edit.php">Edit Home Items</a>
    
 <?php

if(isset($_SESSION['obj']))
{
    //echo $_SESSION['obj'];
    $var= json_decode($_SESSION['obj']);
    echo "<br \>";
    echo "<br \>";
    echo "<br \>";
    //print_r(unserialize($var->{'item_name'}));
    
    //echo $var->{'num_items'};
        
    echo "<table border=\"1\" class=\"altrowstable\" id=\"alternatecolor2\" width=\"100%\">
    <tr>
    <td><b>Name</b></td>
    <td><b>Available Quantity</b></td>
    <td><b>Price</b></td>
    </tr>";
        for($j=0; $j < $var->{'num_items'}; $j++)
        {
            echo "<tr>
		<td>".unserialize($var->{'item_name'})[$j]."</td><td>".$var->{'avl_qty'}[$j]."</td> <td>".$var->{'price'}[$j]."</td> </tr>";
        }
         
	echo "</table>";
        echo "<br \>";
    ?>
    
        <form action="call.php" method="POST">
                <center><input type="submit" name="make_payment" value="Make Payment"></center>
        </form>
    </body>
    
    <?php
}
 ?>

</html>