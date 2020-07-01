
<p align="center">
  <img src="https://toppng.com/uploads/preview/youtube-social-media-icon-social-media-icon-png-icone-do-youtube-11562958792oqqewxr6w9.png" width="240px"/>
</p>

> Simple youtube scraper

[![Latest Stable Version](https://poser.pugx.org/phpunit/phpunit/v)](//packagist.org/packages/phpunit/phpunit)
[![Total Downloads](https://poser.pugx.org/phpunit/phpunit/downloads)](//packagist.org/packages/phpunit/phpunit)
[![License](https://poser.pugx.org/acidjazz/tubestrip/license)](//packagist.org/packages/acidjazz/tubestrip)
[![Version](https://poser.pugx.org/acidjazz/tubestrip/version)](//packagist.org/packages/acidjazz/tubestrip)
[![codecov](https://codecov.io/gh/acidjazz/tubestrip/branch/master/graph/badge.svg)](https://codecov.io/gh/acidjazz/tubestrip)

> early development

## Features
* Search youtube
* Get a video title, description, view count, and published date


## Installation

Install tubestrip with [composer](https://getcomposer.org/doc/00-intro.md):
```bash
composer require acidjazz/tubestrip
```
## Examples 

```php
<?php

namespace App\Http\Controllers;

use acidjazz\tubestrip\TubeStrip;

$ts = new TubeStrip();
$results = $ts->search('this is a test');
```

