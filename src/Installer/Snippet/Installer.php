<?php
namespace DDInstaller\Installer\Snippet;

class Installer extends \DDInstaller\Installer\Installer {

	/**
	 * @property $dbSettings {stdClass}
	 * @property $dbSettings->tableName {string}
	 * @property $dbSettings->contentField {string}
	 */
	protected $dbSettings = [
		'tableName' => 'site_snippets',
		'contentField' => 'snippet',
	];
}
?>