# black-php-inotify

A simple OO wrapper for PHP inotify module

This is a fork of [php-inotify](https://github.com/davidjmemmett/php-inotify "php-inotify").

## Requirements

* The [inotify module for PHP](http://www.php.net/manual/en/inotify.install.php "inotify module for PHP").

## Example

```php
<?php
use Black\OS\Inotify\Monitor;

$inotify = new Monitor();
$inotify->addWatch(__DIR__, Monitor::MODIFY);
while (true) {
  foreach ($inotify->read() as $result) {
    echo 'Something happened to \''. __DIR__. '/'. $result->getName(). "'.\n";
  }
}
```
