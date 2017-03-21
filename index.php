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

//for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

try{
$db = new PDO('sqlite:imgdb.sqlite') or die('couldnt connect to databse');
// Set errormode to exceptions
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//for now, just list all the files
$result = $db->query('SELECT * FROM IMG');

foreach($result as $row) {
	echo "Title:" . $row['TITLE'];
	echo "Image:<img src='img/" . $row['FILE'] . "'></img>";
	echo "<br/>";
}

}catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }
echo uuidv4();

?>
