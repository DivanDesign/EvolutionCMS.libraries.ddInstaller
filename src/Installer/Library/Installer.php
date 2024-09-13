<?php
namespace DDInstaller\Installer\Library;

class Installer extends \DDInstaller\Installer\Installer {
	/**
	 * fillPaths_destination
	 * @version 1.0.1 (2024-09-13)
	 *
	 * @return {void}
	 */
	protected final function fillPaths_destination(){
		$this->paths->destinationDir =
			$this->paths->assetsDir
			. 'libs'
			. DIRECTORY_SEPARATOR
			. $this->distrData->shortName
			. DIRECTORY_SEPARATOR
		;
	}
}
?>