<?php
namespace DDInstaller\Plugin;

class Installer extends \DDInstaller\Installer {
	protected
		/**
		 * @property $dbSettings {stdClass}
		 * @property $dbSettings->tableName {string}
		 * @property $dbSettings->contentField {string}
		 */
		$dbSettings = [
			'tableName' => 'site_plugins',
			'contentField' => 'plugincode'
		]
	;
}
?>