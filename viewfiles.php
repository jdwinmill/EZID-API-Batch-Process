<?php
$selectOption = '';

echo "<form method='post'> <select name='file'>\n";
foreach (new DirectoryIterator('path_to_downloads/') as $file) {
   // if the file is not this file, and does not start with a '.' or '..',
   // then store it for later display
   if ( (!$file->isDot()) && ($file->getFilename() != basename($_SERVER['PHP_SELF'])) ) {
      echo "<option>";
      // if the element is a directory add to the file name "(Dir)"
      echo ($file->isDir()) ? "(Dir) ".$file->getFilename() : $file->getFilename();
      echo "</option>\n";
   }
}
echo "</select><input type='submit' value='Choose File'></form>";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$selectOption = $_POST['file'];
}

echo '<a href="download.php?download_file='.$selectOption.'">Download file</a><br>';
echo '<a href="/index.php">Upload CSV</a>';
?>
