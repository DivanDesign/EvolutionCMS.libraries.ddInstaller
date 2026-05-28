<?php
/**
 * ddInstaller
 * @version 0.4 (2026-05-28)
 * 
 * @see README.md
 * 
 * @copyright 2021–2026 https://Ronef.me
 */

// Simple API
class DDInstaller {
	/**
	 * install
	 * @version 1.3 (2026-05-28)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted} — @required
	 * @param $params->url {stringUrl} — Resource GitHub URL (e. g. `https://github.com/DivanDesign/EvolutionCMS.libraries.ddTools`). @required
	 * @param [$params->revision='master'] {string} — The branch name, tag name, or commit hash to retrieve.
	 * @param [$params->type] {'Snippet'|'Plugin'|'Library'} — Resource type.
	 * @param [$params->token] {string} — Access token for private repositories on GitHub.com or GitLab.com.
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
				'token' => $params->token ?? null,
			],
		]);
		
		return $installerObject->install($params->revision ?? null);
	}
}
?>