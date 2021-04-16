<?php
namespace DDInstaller;

abstract class Installer extends \DDTools\BaseClass {
	protected
		/**
		 * @property $distrData {stdClass}
		 * @property $distrData->fullName {string} — Resource full name (e. g. `EvolutionCMS.libraries.ddTools`).
		 * @property $distrData->shortName {string} — Resource short name (e. g. `ddTools`).
		 * @property $distrData->type {'library'|'snippet'|'plugin'} — Resource type.
		 * @property $distrData->owner {string} — Resource GitHub owner (e. g. `DivanDesign`).
		 */
		$distrData = [
			'fullName' => '',
			'shortName' => '',
			'type' => '',
			'owner' => ''
		],
		
		/**
		 * @property $paths {stdClass}
		 * @property $paths->assetsDir {string} — Full path of `assets` (e. g. `/var/www/someuser/data/www/somesite.com/assets/`).
		 * @property $paths->destinationDir {string} — Resource destination full path (e. g. `/var/www/someuser/data/www/somesite.com/assets/libs/ddTools/`).
		 * @property $paths->cacheDir {string} — Full path of `assets/cache/ddInstaller` (e. g. `/var/www/someuser/data/www/somesite.com/assets/cache/ddInstaller/`).
		 * @property $paths->cacheFile {string} — Full path name of cache file (e. g. `/var/www/someuser/data/www/somesite.com/assets/cache/ddInstaller/EvolutionCMS.libraries.ddTools.zip`).
		 */
		$paths = [
			'assetsDir' => '',
			'destinationDir' => '',
			'cacheDir' => '',
			'cacheFile' => ''
		],
		
		/**
		 * @property $dbSettings {stdClass}
		 * @property $dbSettings->tableName {string}
		 * @property $dbSettings->contentField {string}
		 */
		 $dbSettings = [
			'tableName' => null,
			'contentField' => null
		]
	;
	
	/**
	 * __construct
	 * @version 1.0 (2021-04-08)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted} — @required
	 * @param $params->url {stringUrl} — Resource GitHub URL (e. g. `https://github.com/DivanDesign/EvolutionCMS.libraries.ddTools`). @required
	 */
	public function __construct($params = []){
		//Prepare params
		$params = \DDTools\ObjectTools::convertType([
			'object' => $params,
			'type' => 'objectStdClass'
		]);
		
		//Prepare field types
		$this->paths = (object) $this->paths;
		$this->distrData = (object) $this->distrData;
		$this->dbSettings = (object) $this->dbSettings;
		
		//Prepare DB settings
		if (!empty($this->dbSettings->tableName)){
			$this->dbSettings->tableName = \ddTools::$tables[$this->dbSettings->tableName];
		}
		
		//Fill distr data from URL
		$this->fillDistrDataFromUrl($params->url);
		
		//Fill distr resource type
		$this->distrData->type =
			//E. g. `snippet`
			strtolower(
				//E. g. [`Snippet`]
				array_slice(
					//E. g. `['DDInstaller', 'Snippet', 'Installer']`
					explode(
						'\\',
						//E. g. `DDInstaller\\Snippet\\Installer`
						get_called_class()
					),
					-2,
					1
				)
				//E. g. `Snippet`
				[0]
			)
		;
		
		//Fill paths
		$this->fillPaths();
		
		//Create cache dir if needed
		\DDTools\FilesTools::createDir([
			'path' => $this->paths->cacheDir
		]);
	}
	
	/**
	 * fillDistrDataFromUrl
	 * @version 1.0 (2021-04-03)
	 * 
	 * @desc Parses GitHub URL and fill resource data fields.
	 * 
	 * @return {void}
	 */
	protected final function fillDistrDataFromUrl($distrUrl){
		$ownerAndRepo =
			//E. g. `['DivanDesign', 'EvolutionCMS.libraries.ddTools']`
			array_slice(
				//E. g. `['https:', '', 'github.com', 'DivanDesign', 'EvolutionCMS.libraries.ddTools']`
				explode(
					'/',
					//E. g. 'https://github.com/DivanDesign/EvolutionCMS.libraries.ddTools'
					$distrUrl
				),
				-2,
				2
			)
		;
		
		$this->distrData->owner = $ownerAndRepo[0];
		$this->distrData->fullName = $ownerAndRepo[1];
		
		$this->distrData->shortName =
			//E. g. `ddTools`
			array_pop(
				//E. g. `['EvolutionCMS', 'libraries', 'ddTools']`
				explode(
					'.',
					//E. g. `EvolutionCMS.libraries.ddTools`
					$this->distrData->fullName
				)
			)
		;
	}
	
	/**
	 * fillPaths
	 * @version 1.0 (2021-04-03)
	 * 
	 * @return {void}
	 */
	protected final function fillPaths(){
		//Path of `assets`
		$this->paths->assetsDir =
			dirname(
				__DIR__,
				4
			) .
			DIRECTORY_SEPARATOR
		;
		
		//Destination path
		$this->fillPaths_destination();
		
		//Cache dir
		$this->paths->cacheDir =
			$this->paths->assetsDir .
			'cache' .
			DIRECTORY_SEPARATOR .
			'ddInstaller' .
			DIRECTORY_SEPARATOR
		;
		
		//Cache file
		$this->paths->cacheFile =
			$this->paths->cacheDir .
			$this->distrData->fullName .
			'.zip'
		;
	}
	
	/**
	 * fillPaths_destination
	 * @version 1.0 (2021-04-03)
	 * 
	 * @return {void}
	 */
	protected function fillPaths_destination(){
		$this->paths->destinationDir =
			$this->paths->assetsDir .
			(
				$this->distrData->type .
				's'
			) .
			DIRECTORY_SEPARATOR .
			$this->distrData->shortName .
			DIRECTORY_SEPARATOR
		;
	}
	
	/**
	 * install
	 * @version 1.0 (2021-04-07)
	 * 
	 * @return {boolean} — Is resource installed?
	 */
	public function install(){
		$result = false;
		
		if ($this->downloadDistrZip()){
			$distrZipObject = new \ZipArchive;
			$distrZipObject->open($this->paths->cacheFile);
			
			$distrRootDir = $distrZipObject->getNameIndex(0);
			
			$distrComposerJson = $distrZipObject->getFromName(
				$distrRootDir .
				'composer.json'
			);
			
			if (
				is_string($distrComposerJson) &&
				!empty($distrComposerJson)
			){
				$distrComposerJson = \DDTools\ObjectTools::convertType([
					'object' => $distrComposerJson,
					'type' => 'objectStdClass'
				]);
			}
			
			if ($this->isNeedToInstall($distrComposerJson)){
				//Just remove exist dir
				\DDTools\FilesTools::removeDir($this->paths->destinationDir);
				//And create again
				\DDTools\FilesTools::createDir([
					'path' => $this->paths->destinationDir
				]);
				
				//Iterate over all files in the archive
				for (
					//Skip root dir
					$fileIndex = 1;
					$fileIndex < $distrZipObject->numFiles;
					$fileIndex++
				){
					//Various directory separators support
					$filePathname = str_replace(
						'\\',
						'/',
						//Get current file name
						$distrZipObject->getNameIndex($fileIndex)
					);
					
					//Remove root dir from file name
					$filePathname = str_replace(
						$distrRootDir,
						'',
						$filePathname
					);
					
					//If it is dir
					if (
						substr(
							$filePathname,
							-1
						) ==
						'/'
					){
						//Create
						\DDTools\FilesTools::createDir([
							'path' =>
								$this->paths->destinationDir .
								$filePathname
						]);
					}else{
						//If the file must be installed to DB
						if (
							$filePathname ==
							//E. g. `ddMakeHttpRequest_snippet.php`
							(
								$this->distrData->shortName .
								'_' .
								$this->distrData->type .
								'.php'
							)
						){
							$this->installToDb([
								'version' => $distrComposerJson->version,
								'description' =>
									\DDTools\ObjectTools::isPropExists([
										'object' => $distrComposerJson,
										'propName' => 'description'
									]) ?
									$distrComposerJson->description :
									''
								,
								'content' => $distrZipObject->getFromIndex($fileIndex)
							]);
						}else{
							file_put_contents(
								(
									$this->paths->destinationDir .
									$filePathname
								),
								$distrZipObject->getFromIndex($fileIndex)
							);
						}
					}
				}
				
				$result = true;
			}
			
			$distrZipObject->close();
			
			unlink($this->paths->cacheFile);
		}
		
		return $result;
	}
	
	/**
	 * downloadDistrZip
	 * @version 1.0 (2021-04-08)
	 * 
	 * @return {boolean}
	 */
	protected function downloadDistrZip(){
		$result = false;
		
		$fileContent = \DDTools\Snippet::runSnippet([
			'name' => 'ddMakeHttpRequest',
			'params' => [
				'url' =>
					'https://api.github.com/repos/' .
					$this->distrData->owner .
					'/' .
					$this->distrData->fullName .
					'/zipball/'
				,
				'userAgent' => \ddTools::$modx->getConfig('site_url'),
				'headers' => [
					'Accept: application/vnd.github.v3+json'
				]
			]
		]);
		
		//Clear all or we will get error
		ob_clean();
		
		//If we have dump
		if(
			is_string($fileContent) &&
			//If non-JSON is gotten
			$fileContent[0] != '{'
		){
			//Save cache file
			file_put_contents(
				$this->paths->cacheFile,
				$fileContent
			);
			
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * isInstallNeeded
	 * @version 1.0 (2021-04-07)
	 * 
	 * @param $distrComposerJson {stdClass}
	 * 
	 * @return {boolean}
	 */
	protected function isNeedToInstall($distrComposerJson){
		//Don't want to install by default
		$result = false;
		
		if (
			//We don't do anything if the repository has no `composer.json`
			is_object($distrComposerJson) &&
			//Version is required
			!empty($distrComposerJson->version)
		){
			$existComposerJson =
				$this->paths->destinationDir .
				'composer.json'
			;
			
			if (
				//If destination composer is absent
				!is_file($existComposerJson) ||
				//Or invalid
				empty($existComposerJson = file_get_contents($existComposerJson))
			){
				//Just install
				$result = true;
			}else{
				$existComposerJson = \DDTools\ObjectTools::convertType([
					'object' => $existComposerJson,
					'type' => 'objectStdClass'
				]);
				
				if (
					//If destination version is absent
					empty($existComposerJson->version) ||
					//Or distr version > destination version
					version_compare(
						$distrComposerJson->version,
						$existComposerJson->version,
						'>'
					)
				){
					//Install
					$result = true;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * installToDb
	 * @version 1.0.1 (2021-04-16)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted} — @required
	 * @param $params->version {string} — @required
	 * @param $params->description {string} — @required
	 * @param $params->content {string} — @required
	 * 
	 * @return {void}
	 */
	protected function installToDb($params){
		//Prepare params
		$params = \DDTools\ObjectTools::convertType([
			'object' => $params,
			'type' => 'objectStdClass'
		]);
		
		if (!empty($this->dbSettings->tableName)){
			$params->content = trim($params->content);
			$params->content = ltrim(
				$params->content,
				'<?php'
			);
			$params->content = rtrim(
				$params->content,
				'?>'
			);
			
			$fieldsToUpdate = [
				'description' => \ddTools::$modx->db->escape(
					'<b>' .
					$params->version .
					'</b> ' .
					$params->description
				),
				$this->dbSettings->contentField => \ddTools::$modx->db->escape($params->content)
			];
			
			$destinationId = \ddTools::$modx->db->getValue(\ddTools::$modx->db->select(
				//Fields
				'id',
				//From
				$this->dbSettings->tableName,
				//Where
				(
					'`name` = "' .
					\ddTools::$modx->db->escape($this->distrData->shortName) .
					'"'
				)
			));
			
			//If resource already exists
			if (is_numeric($destinationId)){
				$fieldsToUpdate['editedon'] = time();
				
				\ddTools::$modx->db->update(
					//Fields
					$fieldsToUpdate,
					//From
					$this->dbSettings->tableName,
					//Where
					(
						'`id` = ' .
						$destinationId
					)
				);
			}else{
				$fieldsToUpdate['createdon'] = time();
				
				\ddTools::$modx->db->insert(
					//Fields
					$fieldsToUpdate,
					//From
					$this->dbSettings->tableName
				);
			}
		}
	}
}
?>