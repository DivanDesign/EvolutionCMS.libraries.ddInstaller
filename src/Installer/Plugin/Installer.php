<?php
namespace DDInstaller\Installer\Plugin;

class Installer extends \DDInstaller\Installer\Installer {
	/**
	 * @property $dbSettings {stdClass}
	 * @property $dbSettings->tableName {string}
	 * @property $dbSettings->contentField {string}
	 */
	protected $dbSettings = [
		'tableName' => 'site_plugins',
		'contentField' => 'plugincode',
	];
}
?>