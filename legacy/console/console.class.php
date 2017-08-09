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
 
include('common/voolevels.limits.class.php');

class VooConsole extends VooLimits {
	private $data = null;
	
	var $termcolors = array(
		'success'  => "\e[1;32m",
		'warning'  => "\e[1;33m",
		'danger'   => "\e[1;31m",
		'info'     => "\e[1;35m",
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
			"%s%-15s%s %-15s %-10s %s%-14s %s%-10s\n",
			$this->termcolors[$this->status($value['status'])],
			$value['status'],
			"\e[0m",
			$value['modulation'],
			$value['channel'],
			$this->termcolors[$this->downlimit($value['rx'])],
			$value['rx'],
			$this->termcolors[$this->downlimitsnr($value['snr'], $value['modulation'])],
			$value['snr']
		);
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
			"%s%-15s%s %-15s %-10s %s%-10s\n",
			$this->termcolors[$this->status($value['status'])],
			$value['status'],
			"\e[0m",
			$value['modulation'],
			$value['channel'],
			$this->termcolors[$this->uplimit($value['tx'], $value['modulation'])],
			$value['tx']
		);
	}
	
	
}
?>
