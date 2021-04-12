<?php
namespace DDInstaller\Library;

class Installer extends \DDInstaller\Installer {
	/**
	 * fillPaths_destination
	 * @version 1.0 (2021-04-03)
	 *
	 * @return {void}
	 */
	protected final function fillPaths_destination(){
		$this->paths->destinationDir =
			$this->paths->assetsDir .
			'libs' .
			DIRECTORY_SEPARATOR .
			$this->distrData->shortName .
			DIRECTORY_SEPARATOR
		;
	}
}
?>