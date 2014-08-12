<?php
class VooLevels {
	private $username = null;
	private $password = null;
	private $host     = '192.168.100.1';
	
	private $tables  = array(
		0 => 'downstream',
		1 => 'upstream'
	);
	
	private $columns = array(
		0 => array(
			0 => 'status',
			1 => 'modulation',
			2 => 'channel',
			3 => 'symrate',
			4 => 'frequency',
			5 => 'rx',
			6 => 'snr',
			7 => 'docsis'
		),
		1 => array(
			0 => 'status',
			1 => 'modulation',
			2 => 'channel',
			3 => 'symrate',
			4 => 'frequency',
			5 => 'tx'
		)
	);
	
	private $data = array();
	
	function __construct($username, $password) {
		$this->username = $username;
		$this->password = $password;
		
		$this->download();
	}
	
	function download() {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'http://'.$this->host.'/VooInstallationLevels.asp');
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_USERPWD, $this->username.':'.$this->password);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		$xml = curl_exec($curl);
		
		curl_close($curl);
		
		$this->parse($xml);
	}
	
	function parse($xml) {
		$dom = new DOMDocument();
		@$dom->loadHTML($xml);
		
		$index = 0;
		foreach($dom->getElementsByTagName('table') as $table) {
			$this->data[$this->tables[$index]] = $this->table($table, $index);
			$index++;
		}
	}
	
	function table($table, $id) {
		$items = array();
		$index = 0;
		
		foreach($table->getElementsByTagName('tr') as $tr) {
			if($index++ <= 1)
				continue;
			
			$items[] = $this->line($tr, $id);
		}
		
		return $items;
	}
	
	function line($tr, $id) {
		$items = array();
		$index = 0;
		
		foreach($tr->getElementsByTagName('td') as $td)
			$items[$this->columns[$id][$index++]] = trim($td->nodeValue);
		
		return $items;
	}
	
	function getArray() {
		return $this->data;
	}
}
?>
