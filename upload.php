<!doctype html>
<html>
<body>

<form action="upload.php" method="post" enctype="multipart/form-data">
    Select image to upload:
    File:<input type="file" name="fileToUpload" id="fileToUpload"><br>
    Title:<input type="text" name="title" id="title"><br>
    <input type="submit" value="Upload Image" name="submit">
</form>

<?php

/**
 * Found on http://stackoverflow.com/questions/31451405/cryptographically-secure-unique-id
 * 
 * Return a UUID (version 4) using random bytes
 * Note that version 4 follows the format:
 *     xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
 * where y is one of: [8, 9, A, B]
 * 
 * We use (random_bytes(1) & 0x0F) | 0x40 to force
 * the first character of hex value to always be 4
 * in the appropriate position.
 * 
 * For 4: http://3v4l.org/q2JN9
 * For Y: http://3v4l.org/EsGSU
 * For the whole shebang: https://3v4l.org/LNgJb
 * 
 * @ref https://stackoverflow.com/a/31460273/2224584
 * @ref https://paragonie.com/b/JvICXzh_jhLyt4y3
 * 
 * @return string
 */
function uuidv4()
{
    return implode('-', [
        bin2hex(random_bytes(4)),
        bin2hex(random_bytes(2)),
        bin2hex(chr((ord(random_bytes(1)) & 0x0F) | 0x40)) . bin2hex(random_bytes(1)),
        bin2hex(chr((ord(random_bytes(1)) & 0x3F) | 0x80)) . bin2hex(random_bytes(1)),
        bin2hex(random_bytes(6))
    ]);
}

$id = uuidv4();
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
	$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	if($check !== false) {
        	echo "File is an image - " . $check["mime"] . ".<br>";
        	$uploadOk = 1;
 	} else {
        	echo "File is not an image.<br>";
        	$uploadOk = 0;
	}

	// Check if file already exists
	if (file_exists($target_file)) {
	    echo "Sorry, file already exists.<br>";
	    $uploadOk = 0;
	}
	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 500000) {
	    echo "Sorry, your file is too large.<br>";
	    $uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	&& $imageFileType != "gif" ) {
	    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
	    $uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	    echo "Sorry, your file was not uploaded.<br>";
	// if everything is ok, try to upload file
	} else {
	    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], 'img/' . $id . $imageFileType)) {
		if(isset($_POST['title'])) {
			//for debugging
			error_reporting(E_ALL);
			ini_set('display_errors', '1');

			try{
				$db = new PDO('sqlite:imgdb.sqlite') or die('couldnt connect to databse');
				// Set errormode to exceptions
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				//for now, just list all the files
				$stmt = $db->prepare('INSERT INTO IMG (ID,FILE,TITLE) VALUES (?,?,?)');
				
				if(TRUE == $stmt->execute(array($id, $id.$imageFileType, $_POST['title']))) {
					echo "The file ". basename($_FILES["fileToUpload"]["name"]). " has been uploaded.";
				}
				else {
					echo 'file not added to database';
				}
			}catch(PDOException $e) {
			    // Print PDOException message
			    echo $e->getMessage();
			}
		}
	} else {
		echo "Sorry, there was an error uploading your file.<br>";
	}
   }
}
?>


</body>
</html>
