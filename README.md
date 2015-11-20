Loca
====

Created a class for handling translations. I though maybe somebody finds this useful.

Table of contents
-----------------
* [Installation](#installation)
* [Configuration](#configuration)
* [Usages](#usages)
* [Examples](#examples)

Installation
------------

Install the latest version using composer:

```
composer require dees040/loca
```

Make sure you require the autoload.


Configuration
-------------

Use ```Loca::prepare()``` to configure the class.

prepare() takes one argument which is an array.

**Options:**

- locale (string): The country code of the main language to use.
- fallbackLocale (string): The country code of the fallback language to use.
- langDir (string): The full path to the directory which contains the translations.

**Example:**

```
Loca::prepare([
    'locale' => 'fr',
    'fallbackLocale' => 'en',
    'langDir' => '/var/www/Loca/resources/languages',
]);
```

Usages
------

Language strings are stored in files within the specified directory. Within this directory there should be a subdirectory for each language supported by the application:

```
/languageDirectory
    /en
        app.php
    /fr
        app.php
```

All language files simply return an array of keyed strings. For example:

```

<?php

return [
    'welcome' => 'Welcome to our application.'
];

```

At this moment you can call a translation:

```
Loca::translate('app.welcome');
```

Which outputs: Welcome to our application.

Examples
--------

None
