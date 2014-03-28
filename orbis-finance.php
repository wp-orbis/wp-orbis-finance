<?php
/*
Plugin Name: Orbis Finance
Plugin URI: http://www.orbiswp.com/
Description: The Orbis Finance plugin extends your Orbis environment with some finance features.

Version: 1.0.1
Requires at least: 3.5

Author: Pronamic
Author URI: http://www.pronamic.eu/

Text Domain: orbis_finance
Domain Path: /languages/

License: Copyright (c) Pronamic

GitHub URI: https://github.com/pronamic/wp-orbis-keychains
*/

function orbis_finance_bootstrap() {
	include 'classes/orbis-finance-plugin.php';

	global $orbis_finance_plugin;
	
	$orbis_finance_plugin = new Orbis_Finance_Plugin( __FILE__ );
}

add_action( 'orbis_bootstrap', 'orbis_finance_bootstrap' );
