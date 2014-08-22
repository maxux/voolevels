<?php
/* vooconsole.class.php - format voolevels array for unix console
 * Author: Daniel Maxime (root@maxux.net)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 */

class VooConsole {
	private $data = null;
	
	// ranges are based on values from: http://www.dslreports.com/faq/16085
	var $limits = array(
		'rx' => array(
			array(0, 7, 'good'),
			array(7, 10, 'middle'),
			array(10, 15, 'bad'),
			array(15, 1000, 'unusable')
		),
		'snr' => array(
			'QAM256' => array(30, 33),
			'QAM64'  => array(24, 27),
			'QAM16'  => array(18, 21),
			'QPSK'   => array(12, 15),
		),
		'tx' => array(35, 49, 'good'),
		'tx2' => array(
			'ATDMA' => 52,
			'SCDMA' => 53,
			'QAM32' => 54,
			'QAM64' => 54,
			'QAM8'  => 55,
			'QAM16' => 55,
			'QPSK'  => 58,
		),
	);
	
	var $colors = array(
		'good'     => "\e[1;32m",
		'middle'   => "\e[1;33m",
		'bad'      => "\e[1;31m",
		'unusable' => "\e[1;35m",
		'unknown'  => "\e[1;39m",
	);
	
	function __construct($levels) {
		$this->data = $levels;
	}
	
	function render() {
		echo "\e[1;37m============================ Downstream ============================\e[0m\n";
		$this->downtitle();
		
		foreach($this->data['downstream'] as $value) {
			$this->downstream($value);
		}
		
		echo "\n\e[1;37m============================  Upstream  ============================\e[0m\n";
		$this->uptitle();
		
		foreach($this->data['upstream'] as $value) {
			$this->upstream($value);
		}
	}
	
	//
	// downstream
	//
	function downtitle() {
		echo "\e[1;36m";
		
		printf(
			"%-15s %-15s %-10s %-14s %-10s\n",
			'Status',
			'Modulation',
			'Channel',
			'Power',
			'SNR'
		);
		
		echo "\e[0m";
	}
	
	function downstream($value) {
		printf(
			"%s%-15s%s %-15s %-10s %-21s %-16s\n",
			(($value['status'] == 'Locked') ? $this->colors['good'] : $this->colors['bad']),
			$value['status'],
			"\e[0m",
			$value['modulation'],
			$value['channel'],
			$this->downpower($value['rx']),
			$this->downsnr($value['snr'], $value['modulation'])
		);
	}
	
	function downpower($power) {
		$value = (double) $power;
		
		foreach($this->limits['rx'] as $limit) {
			if($value >= $limit[0] && $value <= $limit[1])
				return $this->colors[$limit[2]].$power;
			
			if($value <= $limit[0] && $value >= -$limit[1])
				return $this->colors[$limit[2]].$power;
		}
		
		return $this->colors['unknown'].$power;
	}
	
	function downsnr($snr, $modulation) {
		$value = (double) $snr;
		
		// unknown modulation
		if(!isset($this->limits['snr'][$modulation]))
			return $this->colors['unknown'].$snr;
		
		// better than recommanded
		if($value > $this->limits['snr'][$modulation][1])
			return $this->colors['good'].$snr;
		
		// better than minimum
		if($value > $this->limits['snr'][$modulation][0])
			return $this->colors['middle'].$snr;
		
		// bad value		
		return $this->colors['bad'].$snr;
	}
	
	//
	// upstream
	//
	function uptitle() {
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
	
	function upstream($value) {
		printf(
			"%s%-15s%s %-15s %-10s %-16s\n",
			(($value['status'] == 'Locked') ? $this->colors['good'] : $this->colors['bad']),
			$value['status'],
			"\e[0m",
			$value['modulation'],
			$value['channel'],
			$this->uppower($value['tx'], $value['modulation'])
		);
	}
	
	function uppower($power, $modulation) {
		$value = (double) $power;
		
		// okay
		if($value > $this->limits['tx'][0] && $value < $this->limits['tx'][1])
			return $this->colors[$this->limits['tx'][2]].$power;
		
		// not best value, checking limit with modulation
		// if unknown modulation
		if(!isset($this->limits['tx2'][$modulation]))
			return $this->colors['unknown'].$power;
		
		// checking modulation limit
		if($value > $this->limits['tx2'][$modulation])
			return $this->colors['bad'].$power;
		
		return $this->colors['middle'].$power;
	}
}
?>
