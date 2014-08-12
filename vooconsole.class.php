<?php
class VooConsole {
	private $data = null;
	
	function __construct($levels) {
		$this->data = $levels;
	}
	
	function render() {
		echo "\e[1;37m==================== Downstream ====================\e[0m\n";
		$this->title();
		
		foreach($this->data['downstream'] as $value) {
			$this->info($value, 'rx');
		}
		
		echo "\n\e[1;37m====================  Upstream  ====================\e[0m\n";
		$this->title();
		
		foreach($this->data['upstream'] as $value) {
			$this->info($value, 'tx');
		}
	}
	
	function title() {
		echo "\e[1;36m";
		
		printf(
			"%-15s %-15s %-10s %-10s\n",
			'Status',
			'Modulation',
			'Channel',
			'Power'
		);
		
		echo "\e[0m";
	}
	
	function info($value, $rtx) {
		printf(
			"%s%-15s%s %-15s %-10s %-10s\n",
			(($value['status'] == 'Locked') ? "\e[1;32m" : "\e[1;31m"),
			$value['status'],
			"\e[0m",
			$value['modulation'],
			$value['channel'],
			$value[$rtx]
		);
	}
}
?>
