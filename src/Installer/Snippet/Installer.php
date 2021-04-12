<?php
namespace DDInstaller\Snippet;

class Installer extends \DDInstaller\Installer {
	protected
		/**
		 * @property $dbSettings {stdClass}
		 * @property $dbSettings->tableName {string}
		 * @property $dbSettings->contentField {string}
		 */
		$dbSettings = [
			'tableName' => 'site_snippets',
			'contentField' => 'snippet'
		]
	;
}
?>