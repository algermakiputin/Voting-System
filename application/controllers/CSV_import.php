<?php

class CSV_import extends CI_Controller {
	
	public function upload_file(){

    	$file = explode('.', $_FILES['csv_file']['name']); 
    	$extension = $file[1];  
    	$tmpName = $_FILES['csv_file']['tmp_name'];
		$csvAsArray = array_map('str_getcsv', file($tmpName));	

		
		if ($extension == "csv") {

			if (count($csvAsArray) > 1500) {
				echo "1";
				return;
			}

			$datasets = [];


			foreach ($csvAsArray as $key => $row) {

				if ($key == 0)
					continue;


				$username = explode(' ', $row[0]);
				$username = $this->generate_username($username[0]);

			 
				

				$datasets[] = [
						'name' => $row[0],
						'section'	=> $row[1],
						'grade'	=> $row[2],
						'password'	=> $this->generate_password(),
						'username'	=> $username
					];

			}

 			$this->db->insert_batch('users', $datasets);
 			 

		}

 
    	
	}	

	private function randomPassword() {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}

	private function randomUsername() {
    $alphabet = '1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

    for ($i = 0; $i < 5; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
	 
	 return implode($pass); //turn the array into a string
	}

	public function generate_username($name) {

		$username = "";

		do {

			$username = $this->randomUsername(); 

		}	while ($this->db->where('username', $username)->get('users')->num_rows());

		return $name . $username;
	}

	public function generate_password() {

		$password = "";

		do {

			$password = $this->randomPassword();

		}	while ($this->db->where('password', $password)->get('users')->num_rows());

		return $password;

	}

	function csv_to_array( $fileName ) {
		$row = 0;
		$dataHolder = [];
		$cvData = [];
		if (($handle = fopen(base_url() . "uploads/csv/". $fileName, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 153000, ",")) !== FALSE) {
		        $num = count($data);
		 		
		        for ($c=0; $c < $num; $c++) {

		       		array_push($dataHolder, $data[$c]);
		       		
		       		if ($row == 4) {
		       			array_push($cvData, array(
		       				'name' => utf8_encode($dataHolder[0]),
		       				'username' => strtolower($dataHolder[1]),
		       				'password' => strtolower($dataHolder[2]),
		       				'grade' => $dataHolder[3],
		       				'section' => $dataHolder[4],
		       				'voted' => 0
		       			));
		       			$dataHolder = [];
		       			$row = 0;
		       			continue;
		       		}

		       		$row++;
		       		
		        }

		    }
		    fclose($handle);

		}

		return $cvData;

	

	}
}

?>