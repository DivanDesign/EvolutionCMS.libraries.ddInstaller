<?php
namespace DDInstaller\Installer;

abstract class Installer extends \DDTools\Base\Base {
	use \DDTools\Base\AncestorTrait;
	
	/**
	 * @property $distrData {stdClass}
	 * @property $distrData->provider {'github'|'gitlab'} — Repository host provider.
	 * @property $distrData->fullName {string} — Resource full name (e. g. `EvolutionCMS.libraries.ddTools`).
	 * @property $distrData->shortName {string} — Resource short name (e. g. `ddTools`).
	 * @property $distrData->type {'library'|'snippet'|'plugin'} — Resource type.
	 * @property $distrData->namespace {string} — Repository namespace (e. g. `DivanDesign` for GitHub).
	 */
	protected $distrData = [
		'provider' => '',
		'fullName' => '',
		'shortName' => '',
		'type' => '',
		'namespace' => '',
	];
	
	/**
	 * @property $paths {stdClass}
	 * @property $paths->assetsDir {string} — Full path of `assets` (e. g. `/var/www/someuser/data/www/somesite.com/assets/`).
	 * @property $paths->destinationDir {string} — Resource destination full path (e. g. `/var/www/someuser/data/www/somesite.com/assets/libs/ddTools/`).
	 * @property $paths->cacheDir {string} — Full path of `assets/cache/ddInstaller` (e. g. `/var/www/someuser/data/www/somesite.com/assets/cache/ddInstaller/`).
	 * @property $paths->cacheFile {string} — Full path name of cache file (e. g. `/var/www/someuser/data/www/somesite.com/assets/cache/ddInstaller/EvolutionCMS.libraries.ddTools.zip`).
	 */
	protected $paths = [
		'assetsDir' => '',
		'destinationDir' => '',
		'cacheDir' => '',
		'cacheFile' => '',
	];
	
	/**
	 * @property $dbSettings {stdClass}
	 * @property $dbSettings->tableName {string}
	 * @property $dbSettings->contentField {string}
	 */
	protected $dbSettings = [
		'tableName' => null,
		'contentField' => null,
	];
	
	/**
	 * __construct
	 * @version 1.0.4 (2026-05-28)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted} — @required
	 * @param $params->url {stringUrl} — Resource GitHub or GitLab URL (e. g. `https://github.com/DivanDesign/EvolutionCMS.libraries.ddTools`). @required
	 */
	public function __construct($params = []){
		// Prepare params
		$params = \DDTools\ObjectTools::convertType([
			'object' => $params,
			'type' => 'objectStdClass',
		]);
		
		// Prepare field types
		$this->paths = (object) $this->paths;
		$this->distrData = (object) $this->distrData;
		$this->dbSettings = (object) $this->dbSettings;
		
		// Prepare DB settings
		if (!empty($this->dbSettings->tableName)){
			$this->dbSettings->tableName = \ddTools::$tables[$this->dbSettings->tableName];
		}
		
		// Fill distr data from URL
		$this->fillDistrDataFromUrl($params->url);
		
		// Fill distr resource type
		$this->distrData->type =
			// E. g. `snippet`
			strtolower(
				// E. g. `Snippet`
				static::getClassName()->namespaceShort
			)
		;
		
		// Fill paths
		$this->fillPaths();
		
		// Create cache dir if needed
		\DDTools\FilesTools::createDir([
			'path' => $this->paths->cacheDir,
		]);
	}
	
	/**
	 * fillDistrDataFromUrl
	 * @version 1.1 (2026-05-28)
	 * 
	 * @desc Parses GitHub or GitLab repository URL and fills $this->distrData.
	 * 
	 * @return {void}
	 */
	protected final function fillDistrDataFromUrl($distrUrl){
		$parsedUrl = parse_url($distrUrl);
		
		$this->distrData->provider =
			$parsedUrl['host'] == 'gitlab.com'
			? 'gitlab'
			: 'github'
		;
		
		$urlSegments = explode(
			'/',
			trim(
				$parsedUrl['path'],
				'/'
			)
		);
		
		$this->distrData->fullName = array_pop($urlSegments);
		$this->distrData->namespace = implode(
			'/',
			$urlSegments
		);
		
		// E. g. `['EvolutionCMS', 'libraries', 'ddTools']`
		$this->distrData->shortName = explode(
			'.',
			// E. g. `EvolutionCMS.libraries.ddTools`
			$this->distrData->fullName
		);
		// E. g. `ddTools`
		$this->distrData->shortName = array_pop($this->distrData->shortName);
	}
	
	/**
	 * fillPaths
	 * @version 1.0.2 (2024-09-13)
	 * 
	 * @return {void}
	 */
	protected final function fillPaths(){
		// Path of `assets`
		$this->paths->assetsDir =
			dirname(
				__DIR__,
				4
			)
			. DIRECTORY_SEPARATOR
		;
		
		// Destination path
		$this->fillPaths_destination();
		
		// Cache dir
		$this->paths->cacheDir =
			$this->paths->assetsDir
			. 'cache'
			. DIRECTORY_SEPARATOR
			. 'ddInstaller'
			. DIRECTORY_SEPARATOR
		;
		
		// Cache file
		$this->paths->cacheFile =
			$this->paths->cacheDir
			. $this->distrData->fullName
			. '.zip'
		;
	}
	
	/**
	 * fillPaths_destination
	 * @version 1.0.1 (2024-09-13)
	 * 
	 * @return {void}
	 */
	protected function fillPaths_destination(){
		$this->paths->destinationDir =
			$this->paths->assetsDir
			. (
				$this->distrData->type
				. 's'
			)
			. DIRECTORY_SEPARATOR
			. $this->distrData->shortName
			. DIRECTORY_SEPARATOR
		;
	}
	
	/**
	 * install
	 * @version 1.1 (2024-12-03)
	 * 
	 * @param [$revision='master'] {string} — The branch name, tag name, or commit hash to retrieve.
	 * 
	 * @return {boolean} — Is resource installed?
	 */
	public function install(?string $revision = null){
		$result = false;
		
		if (empty($revision)){
			$revision = 'master';
		}
		
		if ($this->downloadDistrZip($revision)){
			$distrZipObject = new \ZipArchive;
			$distrZipObject->open($this->paths->cacheFile);
			
			$distrRootDir = $distrZipObject->getNameIndex(0);
			
			$distrComposerJson = $distrZipObject->getFromName(
				$distrRootDir
				. 'composer.json'
			);
			
			if (
				is_string($distrComposerJson)
				&& !empty($distrComposerJson)
			){
				$distrComposerJson = \DDTools\ObjectTools::convertType([
					'object' => $distrComposerJson,
					'type' => 'objectStdClass',
				]);
			}
			
			if (
				$this->isNeedToInstall([
					'distrComposerJson' => $distrComposerJson,
					'distrRevision' => $revision,
				])
			){
				// Just remove exist dir
				\DDTools\FilesTools::removeDir($this->paths->destinationDir);
				// And create again
				\DDTools\FilesTools::createDir([
					'path' => $this->paths->destinationDir,
				]);
				
				// Iterate over all files in the archive
				for (
					// Skip root dir
					$fileIndex = 1;
					$fileIndex < $distrZipObject->numFiles;
					$fileIndex++
				){
					// Various directory separators support
					$filePathname = str_replace(
						'\\',
						'/',
						// Get current file name
						$distrZipObject->getNameIndex($fileIndex)
					);
					
					// Remove root dir from file name
					$filePathname = str_replace(
						$distrRootDir,
						'',
						$filePathname
					);
					
					// If it is dir
					if (
						substr(
							$filePathname,
							-1
						)
						== '/'
					){
						// Create
						\DDTools\FilesTools::createDir([
							'path' =>
								$this->paths->destinationDir
								. $filePathname
							,
						]);
					}else{
						// If the file must be installed to DB
						if (
							$filePathname
							// E. g. `ddMakeHttpRequest_snippet.php`
							== (
								$this->distrData->shortName
								. '_'
								. $this->distrData->type
								. '.php'
							)
						){
							$this->installToDb([
								'version' => $distrComposerJson->version,
								'description' =>
									\DDTools\ObjectTools::isPropExists([
										'object' => $distrComposerJson,
										'propName' => 'description',
									])
									? $distrComposerJson->description
									: ''
								,
								'content' => $distrZipObject->getFromIndex($fileIndex),
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
	 * @version 2.1 (2026-05-28)
	 * 
	 * @param $revision {string} — The branch name, tag name, or commit hash to retrieve.
	 * 
	 * @return {boolean}
	 */
	protected function downloadDistrZip($revision){
		$result = false;
		
		$requestParams = (object) [
			'url' => '',
			'headers' => [],
			'userAgent' => \ddTools::$modx->getConfig('site_url'),
		];
		
		// GitLab
		if ($this->distrData->provider == 'gitlab'){
			$requestParams->url =
				'https://gitlab.com/api/v4/projects/'
				. rawurlencode(
					$this->distrData->namespace
					. '/'
					. $this->distrData->fullName
				)
				. '/repository/archive.zip?sha='
				. rawurlencode($revision)
			;
		// GitHub
		}else{
			$requestParams->url =
				'https://api.github.com/repos/'
				. $this->distrData->namespace
				. '/'
				. $this->distrData->fullName
				. '/zipball/'
				. $revision
			;
			
			$requestParams->headers = [
				'Accept: application/vnd.github.v3+json',
			];
		}
		
		$fileContent = \DDTools\Snippet::runSnippet([
			'name' => 'ddMakeHttpRequest',
			'params' => $requestParams,
		]);
		
		// Clear all or we will get error
		ob_clean();
		
		// If we have dump
		if(
			is_string($fileContent)
			// If non-JSON is gotten
			&& $fileContent[0] != '{'
		){
			// Save cache file
			file_put_contents(
				$this->paths->cacheFile,
				$fileContent
			);
			
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * isNeedToInstall
	 * @version 2.1 (2026-05-28)
	 * 
	 * @param $params {stdClass|arrayAssociative} — The parameters object.
	 * @param $params->distrComposerJson {stdClass}
	 * @param $params->distrRevision {string}
	 * 
	 * @return {boolean}
	 */
	protected function isNeedToInstall($params){
		$params = (object) $params;
		
		// Don't want to install by default
		$result = false;
		
		if (
			// We don't do anything if the repository has no `composer.json`
			is_object($params->distrComposerJson)
			// Version is required
			&& !empty($params->distrComposerJson->version)
		){
			$existComposerJson =
				$this->paths->destinationDir
				. 'composer.json'
			;
			
			if (
				// If destination composer is absent
				!is_file($existComposerJson)
				// Or invalid
				|| empty($existComposerJson = file_get_contents($existComposerJson))
			){
				// Just install
				$result = true;
			}else{
				$existComposerJson = \DDTools\ObjectTools::convertType([
					'object' => $existComposerJson,
					'type' => 'objectStdClass',
				]);
				
				if (
					// If destination version is absent
					empty($existComposerJson->version)
					// If distr version is not production — install independen of composer version
					|| (
						// Not `master`/`main`
						!in_array(
							$params->distrRevision,
							[
								'master',
								'main',
							]
						)
						// And not version tag
						&& substr(
							$params->distrRevision,
							0,
							1
						)
						!== 'v'
					)
					// Or distr version > destination version
					|| version_compare(
						$params->distrComposerJson->version,
						$existComposerJson->version,
						'>'
					)
				){
					// Install
					$result = true;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * installToDb
	 * @version 1.0.3 (2024-09-13)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted} — @required
	 * @param $params->version {string} — @required
	 * @param $params->description {string} — @required
	 * @param $params->content {string} — @required
	 * 
	 * @return {void}
	 */
	protected function installToDb($params){
		// Prepare params
		$params = \DDTools\ObjectTools::convertType([
			'object' => $params,
			'type' => 'objectStdClass',
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
					'<b>'
					. $params->version
					. '</b> '
					. $params->description
				),
				$this->dbSettings->contentField => \ddTools::$modx->db->escape($params->content),
			];
			
			$destinationId = \ddTools::$modx->db->getValue(\ddTools::$modx->db->select(
				// Fields
				'id',
				// From
				$this->dbSettings->tableName,
				// Where
				(
					'`name` = "'
						. \ddTools::$modx->db->escape($this->distrData->shortName)
					. '"'
				)
			));
			
			// If resource already exists
			if (is_numeric($destinationId)){
				$fieldsToUpdate['editedon'] = time();
				
				\ddTools::$modx->db->update(
					// Fields
					$fieldsToUpdate,
					// From
					$this->dbSettings->tableName,
					// Where
					'`id` = ' . $destinationId
				);
			}else{
				$fieldsToUpdate['createdon'] = time();
				
				\ddTools::$modx->db->insert(
					// Fields
					$fieldsToUpdate,
					// From
					$this->dbSettings->tableName
				);
			}
		}
	}
}
?>