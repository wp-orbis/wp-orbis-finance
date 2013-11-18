<?php
/*
Plugin Name: Orbis Finance
Plugin URI: http://orbiswp.com/
Description: 

Version: 0.1
Requires at least: 3.5

Author: Pronamic
Author URI: http://pronamic.eu/

Text Domain: orbis
Domain Path: /languages/

License: GPL

GitHub URI: https://github.com/pronamic/wp-orbis-keychains
*/

function orbis_finance_bootstrap() {
	include 'classes/orbis-finance-plugin.php';

	global $orbis_finance_plugin;
	
	$orbis_finance_plugin = new Orbis_Finance_Plugin( __FILE__ );
}

add_action( 'orbis_bootstrap', 'orbis_finance_bootstrap' );
