# (MODX)EvolutionCMS.libraries.ddInstaller

Библиотека для установки и обновления сниппетов, плагинов и библиотек из репозиториев GitHub.


## Как это работает


### Термины

* «Сайт» — ваш сайт.
* «Ресурс» — сниппет, плагин или библиотека, которую вы хотите установить или обновить.


### Алгоритм

1. Первым делом библиотека загружает архив Ресурса с репозитория GitHub при помощи API и временно сохраняет его в `assets/cache/ddInstaller/`.
2. Затем она решает устанавливать / обновлять Ресурс или нет.  
	Для этого она смотрит на файл `composer.json` из архива и сравнивает его с `composer.json` Ресурса на вашем Сайте:
	* Ресурс будет установлен или обновлён если:
		1. `composer.json` в архиве:
			* Существует.
			* И не пуст.
			* И содержит валидное поле `version`.
		1. `composer.json` на Сайте:
			* Не существует.
			* Или пуст.
			* Или не содержит поле `version`.
			* Или поле `version` невалидно.
		1. `version` в архиве > `version` на Сайте.
	* В противном случае Ресурс не будет установлен.
3. Чтобы избежать накопления мусорных файлов, библиотека удаляет существующую папку Ресурса перед установкой и создает её снова (например, `assets/snippets/ddGetDate/`).
4. Если Ресурс является сниппетом или плагином, библиотека пытается найти его файл для БД (например, `ddGetDate_snippet.php`) и устанавливает / обновляет его в БД.
5. Наконец, библиотека копирует оставшиеся файлы и подпапки в папку Ресурса.


## Использует

* PHP >= 5.6
* [(MODX)EvolutionCMS](https://github.com/evolution-cms/evolution) >= 1.1
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.ru/modx/ddtools) >= 0.48.2
* [(MODX)EvolutionCMS.snippets.ddMakeHttpRequest](https://code.divandesign.ru/modx/ddmakehttprequest) >= 2.3


## Документация


### Установка

Элементы → Управление файлами:

1. Создайте новую папку `assets/libs/ddInstaller/`.
2. Извлеките содержимое архива в неё.


### Описание параметров


#### `\DDInstaller::install($params)`

Устанавливает или обновляет необходимый сниппет, плагин или библиотеку.

* `$params`
	* Описание: Параметры, используется стиль именованных параметров.
	* Допустимые значения:
		* `arrayAssociative`
		* `object`
		* `stringJsonObject` — в виде [JSON](https://ru.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — в виде [HJSON](https://hjson.github.io/)
		* `stringQueryFormated` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
	* **Обязателен**
	
* `$params->url`
	* Описание: URL ресурса на GitHub.  
		Например, `'https://github.com/DivanDesign/EvolutionCMS.snippets.ddGetDate'`
	* Допустимые значения: `stringUrl`
	* **Обязателен**
	
* `$params->type`
	* Описание: Тип ресурса.
	* Допустимые значения:
		* `snippet`
		* `plugin`
		* `library`
	* **Обязателен**


##### Возвращает

* `$result`
	* Описание: Статус установки.
	* Допустимые значения:
		* `true` — если ресурс успешно установлен или обновлен
		* `false` — если что-то пошло не так или версия ресурса на Сайте уже актуальна


### Примеры


#### Установить или обновить сниппет `ddGetDate`

Просто вызовите следующий код в своих исходинках или модуле [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
//Подключение (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddInstaller/require.php'
);

//Установка (MODX)EvolutionCMS.snippets.ddGetDate
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.snippets.ddGetDate',
	'type' => 'snippet'
]);
```

* Если `ddGetDate` отсутствует на вашем Сайте, библиотека просто установит его.
* Если `ddGetDate` уже есть на вашем Сайте, библиотека проверит его версию и обновит, если нужно. 


## Ссылки

* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddinstaller)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />