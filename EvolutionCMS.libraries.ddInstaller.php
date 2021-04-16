<?php
/**
 * ddInstaller
 * @version 0.1.1 (2021-04-16)
 * 
 * @see README.md
 * 
 * @copyright 2021 DD Group {@link https://DivanDesign.biz }
 */

//Simple API
class DDInstaller {
	/**
	 * install
	 * @version 1.0 (2021-04-08)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted} — @required
	 * @param $params->url {stringUrl} — Resource GitHub URL (e. g. `https://github.com/DivanDesign/EvolutionCMS.libraries.ddTools`). @required
	 * @param $params->type {'Snippet'|'Plugin'|'Library'} — Resource type. @required
	 * 
	 * @return {boolean}
	 */
	public static function install($params){
		//Prepare params
		$params = \DDTools\ObjectTools::convertType([
			'object' => $params,
			'type' => 'objectStdClass'
		]);
		
		$installerObject = \DDInstaller\Installer::createChildInstance([
			'name' => $params->type,
			'parentDir' =>
				__DIR__ .
				DIRECTORY_SEPARATOR .
				'src' .
				DIRECTORY_SEPARATOR .
				'Installer'
			,
			//Passing parameters into constructor
			'params' => [
				'url' => $params->url
			]
		]);
		
		return $installerObject->install();
	}
}
?>