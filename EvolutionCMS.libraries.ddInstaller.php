<?php
/**
 * ddInstaller
 * @version 0.2 (2024-09-13)
 * 
 * @see README.md
 * 
 * @copyright 2021–2024 Ronef {@link https://Ronef.me }
 */

// Simple API
class DDInstaller {
	/**
	 * install
	 * @version 1.1 (2024-09-13)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted} — @required
	 * @param $params->url {stringUrl} — Resource GitHub URL (e. g. `https://github.com/DivanDesign/EvolutionCMS.libraries.ddTools`). @required
	 * @param [$params->type] {'Snippet'|'Plugin'|'Library'} — Resource type.
	 * 
	 * @return {boolean}
	 */
	public static function install($params){
		// Prepare params
		$params = \DDTools\ObjectTools::convertType([
			'object' => $params,
			'type' => 'objectStdClass',
		]);
		
		if (empty($params->type)){
			$urlLowerCase = strtolower($params->url);
			
			// TODO: Replace `strpos` to `str_contains` (PHP >= 8.0 is required)
			// E. g. 'EvolutionCMS.snippets.ddGetChunk'
			if (strpos($urlLowerCase, 'snippet') !== false){
				$params->type = 'snippet';
			// E. g. 'EvolutionCMS.plugins.ddSendRedirect'
			}elseif (strpos($urlLowerCase, 'plugin') !== false){
				$params->type = 'plugin';
			}elseif (
				// E. g. 'EvolutionCMS.libraries.ddTools', 'EvolutionCMS.library.ddTools'
				strpos($urlLowerCase, 'libraries') !== false
				|| strpos($urlLowerCase, 'library') !== false
			){
				$params->type = 'library';
			}
		}
		
		$installerObject = \DDInstaller\Installer\Installer::createChildInstance([
			'name' => $params->type,
			// Passing parameters into constructor
			'params' => [
				'url' => $params->url,
			],
		]);
		
		return $installerObject->install();
	}
}
?>