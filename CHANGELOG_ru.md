# (MODX)EvolutionCMS.libraries.ddInstaller changelog


## Версия 0.3 (2024-12-04)

* \+ `\DDInstaller::install($params)` → Параметры → `$params->revision`: Новый необязательный параметр. Позволяет задать имя ветки, тега или хэш кэммита для получения.


## Версия 0.2 (2024-09-13)

* \+ `\DDInstaller::install($params)` → Параметры → `$params->type`: Параметр стал необязательным. Метод определит тип автоматически из `$params->url` (см. README).
* \* Внимание! Требуется PHP >= 7.4.
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.62.


## Версия 0.1.1 (2021-04-16)

* \* `\DDInstaller\Installer::installToDb`: Описание ресурса также будет экранировано через `$modx->db->escape`.
* \* `\DDInstaller\Installer::fillDistrDataFromUrl`: Исправлена ошибка с передачей параметра не переменной в `array_pop`.


## Версия 0.1 (2021-04-12)

* \+ Первый релиз.


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
<style>ul{list-style:none;}</style>