<?php
	function upgrade_module_2_0($module) {
		$module->installDB();
		return true; // Return true if success.
	}