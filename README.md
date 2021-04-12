# (MODX)EvolutionCMS.libraries.ddInstaller

The library for installing and updating snippets, plugins, and libraries from GitHub repositories.


## How it works


### Terms

* “Site” — your site.
* “Resource” — snippet, plugin or library that you want to install or update.


### Algorithm

1. First, the library downloads the repository archive of Resource from GitHub using API and temporary saves it in `assets/cache/ddInstaller/`.
2. Then it decides whether to install / update Resource or not.  
	To do this it looks at the `composer.json` file from the archive and compares with `composer.json` of Resource on your Site:
	* Resource will be installed or updated if:
		1. `composer.json` in the archive:
			* Is exist.
			* And not empty.
			* And contains the valid `version` field.
		1. `composer.json` on Site:
			* Is not exist.
			* Or empty.
			* Or doesn't contain the `version` field.
			* Or the `version` field is invalid.
		1. `version` in the archive > `version` on Site.
	* Otherwise, Resource will not be installed.
3. To avoid accumulation of trash files, the library removes the existing Resource folder before installation and creates it again (e. g. `assets/snippets/ddGetDate/`).
4. If Resource is a snippet or plugin, the library tries to find its DB file (e. g. `ddGetDate_snippet.php`) and installs / upates it to DB.
5. Finally, the library copies remaining files and subfolders to the Resource folder.


## Requires

* PHP >= 5.6
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.48.2
* [(MODX)EvolutionCMS.snippets.ddMakeHttpRequest](https://code.divandesign.biz/modx/ddmakehttprequest) >= 2.3


## Documentation


### Installation

Elements → Manage Files:

1. Create a new folder `assets/libs/ddInstaller/`.
2. Extract the archive to the folder.


### Parameters description


#### `\DDInstaller::install($params)`

Installs or updates needed snippet, plugin, or library.

* `$params`
	* Desctription: Parameters, the pass-by-name style is used.
	* Valid values:
		* `arrayAssociative`
		* `object`
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
	* **Required**
	
* `$params->url`
	* Desctription: Resource GitHub URL.  
		E. g. `'https://github.com/DivanDesign/EvolutionCMS.snippets.ddGetDate'`
	* Valid values: `stringUrl`
	* **Required**
	
* `$params->type`
	* Desctription: Resource type.
	* Valid values:
		* `snippet`
		* `plugin`
		* `library`
	* **Required**


##### Returns

* `$result`
	* Desctription: Installation status.
	* Valid values:
		* `true` — if the resource is installed or updated successfully
		* `false` — if something went wrong or the resource on Site is already up to date


### Examples


#### Install or update the `ddGetDate` snippet

Just run the following PHP code in your sources or [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
//Include (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddInstaller/require.php'
);

//Install (MODX)EvolutionCMS.snippets.ddGetDate
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.snippets.ddGetDate',
	'type' => 'snippet'
]);
```

* If `ddGetDate` is not exist on your Site, the library will just install it.
* If `ddGetDate` is already exist on your Site, the library will check it version and update it if needed.


## Links

* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddinstaller)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />