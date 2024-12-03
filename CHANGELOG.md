# (MODX)EvolutionCMS.libraries.ddInstaller changelog


## Version 0.3 (2024-12-04)

* \+ `\DDInstaller::install($params)` → Parameters → `$params->revision`: The new optional parameter. Allows to specify the branch name, tag name, or commit hash to retrieve.


## Version 0.2 (2024-09-13)

* \+ `\DDInstaller::install($params)` → Parameters → `$params->type`: The parameter has become optional. The method will detect type automatically from `$params->url` (see README).
* \* Attention! PHP >= 7.4 is required.
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.62 is required.


## Version 0.1.1 (2021-04-16)

* \* `\DDInstaller\Installer::installToDb`: Resource description will also be escaped through `$modx->db->escape`.
* \* `\DDInstaller\Installer::fillDistrDataFromUrl`: Fixed a bug with passing a non-variable parameter to `array_pop`.


## Version 0.1 (2021-04-12)

* \+ The first release.


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
<style>ul{list-style:none;}</style>