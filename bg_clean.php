<html>
<body>

<form action="bg_clean.php" method="post"
enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file"><br>
<input type="submit" name="submit" value="Submit">
</form>

</body>
</html>

<?php
if ($_FILES["file"]["error"] > 0)
  {
  echo "Error: " . $_FILES["file"]["error"] . "<br>";
  }
else
  {
  	var_dump($_FILES["file"]);

  	move_uploaded_file($_FILES["file"]["tmp_name"], __DIR__.'/im/'.$_FILES["file"]["name"]);
  echo "Upload: " . $_FILES["file"]["name"] . "<br>";
  echo "Type: " . $_FILES["file"]["type"] . "<br>";
  echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
  echo "Stored in: " . $_FILES["file"]["tmp_name"]."<br>";
  
$image = new Imagick(__DIR__."/im/".$_FILES["file"]["name"]); 
$x = 1; 
$y = 1; 
$pixel = $image->getImagePixelColor($x, $y); 
$colorsTopLeft = $pixel->getColor();
echo "<pre>top left corner color";
print_r($colorsTopLeft); // produces Array([r]=>255,[g]=>255,[b]=>255,[a]=>1);
unset($colorsTopLeft["a"]);
$dimension = $image->getImageGeometry();
var_dump($dimension);
echo "<pre>top right corner color";
$pixel = $image->getImagePixelColor($dimension["width"], $y); 
$colorsTopRight = $pixel->getColor();

print_r($colorsTopRight); // produces Array([r]=>255,[g]=>255,[b]=>255,[a]=>1);
unset($colorsTopRight["a"]);
if ($_FILES["file"]["type"] != 'image/png') {
	exec("convert im/".$_FILES["file"]["name"]." im/".$_FILES["file"]["name"].".png");
	$_FILES["file"]["name"] .= ".png"; 
}
$command1 = ("convert im/".$_FILES["file"]["name"].
	" -fuzz 15% -transparent 'rgb(".implode(",", $colorsTopRight).")' im/edited_".$_FILES["file"]["name"]."");
//echo $command;
exec($command1);
if (implode("", $colorsTopLeft) != implode("", $colorsTopRight)) {
	$command2 = ("convert im/edited_".$_FILES["file"]["name"].
		" -fuzz 15% -transparent 'rgb(".implode(",", $colorsTopLeft).")' im/edited_".$_FILES["file"]["name"]."");
	exec($command2);
}
echo '<img src="/im/'.$_FILES["file"]["name"].'" width=300>';
echo '<img src="/im/edited_'.$_FILES["file"]["name"].'" width=300>';
  }
?>


