<?php

class Orbis_Finance_Plugin extends Orbis_Plugin {
	public function __construct( $file ) {
		parent::__construct( $file );

		$this->set_name( 'orbis_finance' );
		$this->set_db_version( '1.0.2' );

		$this->plugin_include( 'includes/project.php' );
	}

	public function loaded() {
		$this->load_textdomain( 'orbis_finance', '/languages/' );
	}

	public function install() {
		

		parent::install();
	}
}
