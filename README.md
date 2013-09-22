

# PHP Uploader

Upload large files to Apache/PHP script without increasing post_max_size and memory_limit for php. Complete solution without any changes to PHP script or client side.

## Requirement 

- Apache
- Apache mod_setenvif
- Apache mod_headers

This modules is common for apache, and exist prebuilded on debian.

## Installation

1. enable apache modules setenvif and headers

	a2enmod setenvif

	a2enmod headers

2. In .htaccess config for upload.php script:

~~~
	<Files upload.php>
	php_value max_execution_time 0
	php_value auto_prepend_file php-uploader.inc.php
	SetEnvIf content-type (multipart/form-data)(.*) NEW_CONTENT_TYPE=multipart/form-data-alternate$2 OLD_CONTENT_TYPE=$1$2
	RequestHeader set content-type %{NEW_CONTENT_TYPE}e env=NEW_CONTENT_TYPE
	</Files>
~~~

And we are ready for testing :)

## Test site

[http://zsteva.info/upload/](http://zsteva.info/upload/)

## Security

	TODO

## Author

	Zeljko Stevanovic <zsteva@gmail.com>



