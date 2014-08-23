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
 
include('../common/voolevels.limits.class.php');

class VooTable extends VooLimits {
	private $data     = null;
	private $template = null;
	
	function __construct($levels) {
		$this->data = $levels;
		$this->template = file_get_contents('template.include.html');
	}
	
	function render() {
		// titles
		$down = $this->downtitle();
		$up   = $this->uptitle();
		
		// contents
		foreach($this->data['downstream'] as $value)
			$down .= $this->downstream($value);
		
		foreach($this->data['upstream'] as $value)
			$up .= $this->upstream($value);
		
		
		$pattern = array('{{DOWNSTREAM}}', '{{UPSTREAM}}');
		$replace = array($down, $up);
		
		return str_replace($pattern, $replace, $this->template);
	}
	
	function tr($data) {
		return '<tr>'.$data.'</tr>';
	}
	
	function td($data, $class = null) {
		return '<td'.(($class) ? ' class="'.$class.'"' : '').'>'.$data.'</td>';
	}
	
	function thead($data) {
		return '<thead>'.$data.'</thead>';
	}
	
	//
	// downstream
	//
	function downtitle() {
		return $this->thead(
			$this->td('Status').
			$this->td('Modulation').
			$this->td('Channel').
			$this->td('Power').
			$this->td('SNR')
		);
	}
	
	function downstream($value) {
		return $this->tr(
			$this->td($value['status'], $this->status($value['status'])).
			$this->td($value['modulation']).
			$this->td($value['channel']).
			$this->td($value['rx'], $this->downlimit($value['rx'])).
			$this->td($value['snr'], $this->downlimitsnr($value['snr'], $value['modulation']))
		);
	}
	
	
	
	//
	// upstream
	//
	function uptitle() {
		return $this->thead(
			$this->td('Status').
			$this->td('Modulation').
			$this->td('Channel').
			$this->td('Power').
			$this->td('SNR')
		);
	}
	
	function upstream($value) {
		return $this->tr(
			$this->td($value['status'], $this->status($value['status'])).
			$this->td($value['modulation']).
			$this->td($value['channel']).
			$this->td($value['tx'], $this->uplimit($value['tx'], $value['modulation'])).
			$this->td('')
		);
	}
}
?>
