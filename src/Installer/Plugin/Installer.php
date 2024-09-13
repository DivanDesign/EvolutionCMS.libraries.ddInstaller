<?php
namespace DDInstaller\Installer\Plugin;

class Installer extends \DDInstaller\Installer\Installer {
	protected
		/**
		 * @property $dbSettings {stdClass}
		 * @property $dbSettings->tableName {string}
		 * @property $dbSettings->contentField {string}
		 */
		$dbSettings = [
			'tableName' => 'site_plugins',
			'contentField' => 'plugincode',
		]
	;
}
?>