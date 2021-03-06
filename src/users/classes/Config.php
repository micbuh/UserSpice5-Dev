<?php
/*
UserSpice 4
An Open Source PHP User Management System
by the UserSpice Team at http://UserSpice.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class Config {
	private $_site_settings = null;
	public function __construct() {
		$this->setSiteSettings();
	}
	private function setSiteSettings() {
		$db = DB::getInstance();
		$this->_site_settings = $db->query("SELECT * FROM settings")->first();
	}
	public function get($path=null, $default=null) {
		#echo "DEBUG: Config::get($path, $default): Entering<br />\n";
		if (!isset($this->_site_settings)) {
			$this->setSiteSettings();
		}
		// Settings can be stored in the settings table - if so, this takes priority
		if (isset($this->_site_settings->$path)) {
			return $this->_site_settings->$path;
		}
		// Not in settings table? Look in $GLOBALS['config'] array
		return $this->simpleGet($path, $default);
	}
	// Particularly during initialization (i.e., before the DB is ready for use)
	// we have to look up some config variables. Thus this separation to allow looking
	// up config values *only* from the array-based system(s).
	public static function simpleGet($path=null, $default=null) {
		#echo "DEBUG: Config::simpleGet($path, $default): Entering<br />\n";
		if ($path) {
			$config = $GLOBALS['config'];
			$path = explode('/', $path);

			foreach ($path as $bit) {
				if (isset($config[$bit])) {
					$config = $config[$bit];
				} else {
					return $default;
				}
			}
			return $config;
		}
		// $path not found - return $default (null if not passed in)
		return $default;
	}
}

if (!function_exists('configGet')) {
	//Get value using Config::get()
    // this is just a shortcut to avoid having "global $cfg" at the top of 
    // every function. It assumes that $cfg has already been set.
	function configGet($path, $default=null) {
		global $cfg;
        return $cfg->get($path, $default);
	}
}
