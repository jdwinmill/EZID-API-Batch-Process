<?php
// DOI FILES ONLY
include('/class/profile.php');
$pass_profile = new Profiles('doi');

?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>DOI Uploads</title>
<link rel="stylesheet" type="text/css" href="/ezid.css">
</head>
<body>
<section class="about">
  <h1 hidden>How to:</h1>
  <p>CSV File Only. Must be Pipe delimited and in this order:</p>
  <ol>
    <li>Creater</li>
    <li>Title</li>
    <li>Publisher</li>
    <li>Publication Year</li>
    <li>Resource Type</li>
  </ol>
</section>
<section class="forms">
  <h2 hidden>CSV</h2>
  <ol>
    <li>Choose your file </li>
    <li>Submit</li>
  </ol>
  <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
    <input type="file" name="file" id="file" />
    <input type="submit" name="submit" />
  </form>
</section>
<section class="view">
  <h2 hidden>View</h2>
  <a href="/viewfiles.php">View all files</a> </section>
</body>
</html>
<?php

// set variables
$fileCount = array ();
$inputList = array ();
$filePath = '/path_to_downloads/files/';
$storagename = '';

// Post Form
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
			
            if(isset($_FILES['file'])){
				$storagename = "name_your_file";
                move_uploaded_file($_FILES["file"]["tmp_name"], $filePath . $storagename);
            }
			
	if ($file = fopen($filePath . $storagename, r)) {
		while ($input = fgetcsv($file, 5000, "|")) {
//convert iteration into DOI string and push into array
			if(in_array("", $input)){
				echo '<div class="fail">You have an empty line somewhere</div>';
			}else{
				array_push($inputList, 
				'_target: '.$input[0]."\n" .
				'datacite.creator: '.$input[1]."\n" .
				'datacite.title: '.$input[2]."\n" .
				'datacite.publisher: '.$input[3]."\n" .
				'datacite.publicationyear: '.$input[4]."\n".
				'datacite.resourcetype: '.$input[5]);
			}
		}
	
	
//pass array and count to function
		while($pass_profile->ezid($inputList)){
			echo 'Process Complete!';
			while(unlink ($filePath . $storagename)){
				echo 'File deleted!';	
			}
		}
	}	
}
?>

