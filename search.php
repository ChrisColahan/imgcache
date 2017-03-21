
<!doctype html>
<html>
<body>

 <form action="search.php">
  Search by title:
  <input type="text" name="title"><br>
  <input type="submit" value="Submit">
</form> 

<?php

if(isset($_GET['title'])) {
//for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

try{
$db = new PDO('sqlite:imgdb.sqlite') or die('couldnt connect to databse');
// Set errormode to exceptions
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//for now, just list all the files
$stmt = $db->prepare('SELECT * FROM IMG WHERE TITLE LIKE ?');
$stmt->execute(['%'.$_GET['title'].'%']);
foreach($stmt->fetchAll() as $row) {
	echo $row['TITLE']."<br><img src='img/" . $row['FILE'] . "' style='width:280px;'></img>";
	echo "<br/>";
}

}catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }
}
?>

</body>
</html>
