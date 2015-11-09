<?php
class Profiles{
	/*
	* Success reports are stored into $succeded as an array
	*/
	public $succeded = array();
	
	/*
	* Error reports are stored into $failed as an array
	*/
	public $failed = array();
	
	/*
	* 
	*/
	public $total = array();
	
	/*
	* 
	*/
	public $returnCSV = array();
	
	private $profile;
	private $profileUrlId;
	private $returnInfo;
	private $urlID;
	
	/*
	* Construct function to switch between ark and doi
	*/
	public function __construct($profile){
		if($profile == 'ark'){
			$this->profile = $profile;
			$this->profileUrlId = 'ARK_profile_ID';
			$this->returnInfo = 'ARK_BASE_URL'.$this->profileUrlId;
		
		}elseif($profile == 'doi'){
			$this->profile = $profile;
			$this->profileUrlId = 'DOI_profile_ID';
			$this->returnInfo = 'DOI_BASE_URL';
		
		}
	}
	
	public function ezid($inputList){
	
	// time stamp
	$t=time();
	
	$fileName = $this->profile.date("n-j-y-g_i:A",$t).'.csv';
	
	// count the array
	$arrCount = count($inputList);
	
	// open file
	$fp = fopen('/PATH_THE_FILE_IS_IN/'.$fileName, 'x+');
		
	// loop through each instance
	for ($x = 0; $x < $arrCount; $x++){
		$ch = curl_init();
		
		// create id with count of array, date, and time (e.g., 109_24_15_15_51)
		$id = $x.date("m_d_y_H_i",$t);
		
		curl_setopt($ch, CURLOPT_URL, 'https://ezid.cdlib.org/id/'.$this->profileUrlId. $id);
		curl_setopt($ch, CURLOPT_USERPWD, 'username:password');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_HTTPHEADER,
		array('Content-Type: text/plain; charset=UTF-8',
			  'Content-Length: ' . strlen($inputList[$x])));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $inputList[$x]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
		$output = curl_exec($ch); 
		$report = curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
		
	if($report == 400){
		echo '<div class="fail">line: '.($x + 1).' failed to load</div>'; 
	}elseif($report == 201) {
		array_push($this->succeded,$output.$report);
			
		fputs($fp, $inputList[$x]."\n".$id."\n".$this->returnInfo.$id."\n");
	}
	array_push($this->total, $x);
	curl_close($ch);
	}
	//close file
	fclose($fp);

	// Count Success
	if(count($this->succeded) > 1){
		echo '<div class="success">'.count($this->succeded). ' of '. count($this->total).' profiles uploaded.</div>';
		echo '<a href="/download.php?download_file='.$fileName.'">Download file</a><br>'; 
	}
	
	}
}
