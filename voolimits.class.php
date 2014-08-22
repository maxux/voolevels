<?php
/* voolimits.class.php - check limits value for colored support
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

class VooLimits {
	//
	// ranges are based on values from: http://www.dslreports.com/faq/16085
	//
	
	var $limits = array(
		'rx' => array(
			array(0, 7, 'success'),
			array(7, 10, 'warning'),
			array(10, 15, 'danger'),
			array(15, 1000, 'info')
		),
		'snr' => array(
			'QAM256' => array(30, 33),
			'QAM64'  => array(24, 27),
			'QAM16'  => array(18, 21),
			'QPSK'   => array(12, 15),
		),
		'tx' => array(35, 49, 'success'),
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
	
	function downlimit($power) {
		$value = (double) $power;
		
		foreach($this->limits['rx'] as $limit) {
			if($value >= $limit[0] && $value <= $limit[1])
				return $limit[2];
			
			if($value <= $limit[0] && $value >= -$limit[1])
				return $limit[2];
		}
		
		return 'unknown';
	}
	
	function downlimitsnr($snr, $modulation) {
		$value = (double) $snr;
		
		// unknown modulation
		if(!isset($this->limits['snr'][$modulation]))
			return 'unknown';
		
		// better than recommanded
		if($value > $this->limits['snr'][$modulation][1])
			return 'success';
		
		// better than minimum
		if($value > $this->limits['snr'][$modulation][0])
			return 'warning';
		
		// bad value		
		return 'danger';
	}
	
	function uplimit($power, $modulation) {
		$value = (double) $power;
		
		// okay
		if($value > $this->limits['tx'][0] && $value < $this->limits['tx'][1])
			return $this->limits['tx'][2];
		
		// not best value, checking limit with modulation
		// if unknown modulation
		if(!isset($this->limits['tx2'][$modulation]))
			return 'unknown';
		
		// checking modulation limit
		if($value > $this->limits['tx2'][$modulation])
			return 'danger';
		
		return 'warning';
	}
	
	function status($status) {
		return ($status == 'Locked') ? 'success' : 'danger';
	}
}
?>
