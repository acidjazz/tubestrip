
<p align="center">
  <img src="https://toppng.com/uploads/preview/youtube-social-media-icon-social-media-icon-png-icone-do-youtube-11562958792oqqewxr6w9.png" width="240px"/>
</p>

> Simple youtube scraper

[![Version](https://poser.pugx.org/acidjazz/tubestrip/version)](//packagist.org/packages/acidjazz/tubestrip)
[![Total Downloads](https://poser.pugx.org/acidjazz/tubestrip/downloads)](//packagist.org/packages/acidjazz/tubestrip)
[![License](https://poser.pugx.org/acidjazz/tubestrip/license)](//packagist.org/packages/acidjazz/tubestrip)
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
use acidjazz\tubestrip\TubeStrip;

$ts = new TubeStrip();
$results = $ts->search('GETV ANSI Show');
dump($results);
```

```
array:20 [▼
  0 => {#3768 ▼
    +"id": "r_cYOi3pnhA"
    +"title": "GETV: ANSI Art for the Masses"
  }
...
```

