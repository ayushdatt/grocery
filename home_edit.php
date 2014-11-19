<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="style.css" />
<title>Database</title>

<script type="text/javascript">
function altRows(id){
	if(document.getElementsByTagName){  
		
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
}
</script>
</head>
    <form id="uploadform" action="home_edit.php" method="post" enctype="multipart/form-data">

        
<?php
session_start();
$con=mysql_connect("localhost","root","root");
if(!$con)
{
  die('Could not connect' . mysql_error());
}
mysql_select_db("home",$con);


$result = mysql_query("SELECT * FROM item", $con);
$num_rows = mysql_num_rows($result);


function check_threshold()
{
    

    $query=mysql_query("SELECT name,quantity,threshold from item");
    $temp=0;
    
    while(list($name,$quantity,$threshold) = mysql_fetch_row($query))
    {
        //echo "inside loop";
                if($threshold-$quantity >0)
                    $temp=$temp + $threshold-$quantity;
    } 
    //echo $temp;
    if($temp>=5)
    {
        return true;
    }
    return false;
}

function check_order()
{
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
    print_r($namearr);
    $var=$i;
    //echo "in function check order=";
    $ch = curl_init();
    $query="rquest=getPriceListAndAvailability&num_items=".$var."&item_name[]=".serialize($namearr)."&qty[]=".serialize($qtyarr);
    echo $query;
    //exit(0);
    curl_setopt ($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_URL, "localhost/grocery/storeApi.php");
    curl_setopt($ch, CURLOPT_POSTFIELDS,$query);
    curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 (CK) Firefox/3.0.1");
    curl_setopt ($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    print_r($result);
    curl_close($ch);
    $_SESSION['obj']=$result;
    header('Location: /grocery/home.php');   
}

for($j=0; $j<$num_rows; $j++)
{
	$conc = 'Edit' . $j;
	if(isset($_POST[$conc]))
	{
		$name=$_POST['name' . $j]; 
		$quantity=$_POST['quantity' . $j]; 
		$threshold= $_POST['threshold' . $j]; 

		mysql_query("UPDATE item SET quantity=$quantity, threshold = $threshold WHERE name='$name'") or die("Couldnt do it " . mysql_error());
                //echo "running";
                if(check_threshold()==true)
                {   
                    //echo "inside if";
                    check_order();
                }
		break;
	}
}
for($j=0; $j<$num_rows; $j++)
{
	$temp = 'Delete' . $j;
	//echo $temp;
	if(isset($_POST[$temp]))
	{
		$buffer = 'name' . $j;
		$pk = $_POST[$buffer];
		mysql_query("DELETE FROM item WHERE name = '$pk'");
		break;
	}
}

if(isset($_POST['Insert']))
{
	$name=$_POST['name']; 
	$quantity=$_POST['quantity']; 
	$threshold= $_POST['threshold']; 

	mysql_query("INSERT INTO item VALUES ('$name', $quantity, $threshold)") or die("couldnt do it " . mysql_error());
}

$query=mysql_query("SELECT name,quantity,threshold from item")
 or die("Invalid Query: ".mysql_error());
if(mysql_num_rows($query) != 0)
{
	echo "<table border=\"3\" class=\"altrowstable\" id=\"alternatecolor\" width=\"100%\">
<tr>
<td><b>Name</b></td>
<td><b>Quantity</b></td>
<td><b>Threshold</b></td>
</tr>";
$i=0;
	while(list($name,$quantity,$threshold)= mysql_fetch_row($query))
	{
		echo "<tr>
		<td><input name='name$i' type=\"text\" value=\"$name\"></td>
		<td><input name='quantity$i' type=\"text\" value=\"$quantity\"></td>
		<td><input name='threshold$i' type=\"text\" value=\"$threshold\"></td>
				<td><input type = \"submit\" name = \"Delete$i\" value = \"Delete\"><br/>
		<input type = \"submit\" name = \"Edit$i\" value = \"Update\"></td>
		</tr>";
		$i=$i+1;
	} 
	echo "<tr>
		<td><input type=\"text\" name=\"name\"></td>
		<td><input type=\"text\" name=\"quantity\"></td>
		<td><input type=\"text\" name=\"threshold\"></td>
		<td><input type = \"submit\" name = \"Insert\" value = \"Insert\"></td>
		</tr>
		</table>";
}
else {
echo 'No one has added any items so far.';
}
?>
</form>
    <a href="home.php">View Home Items</a>
</html>
