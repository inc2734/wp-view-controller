# WP View Controller

[![Build Status](https://travis-ci.org/inc2734/wp-view-controller.svg?branch=master)](https://travis-ci.org/inc2734/wp-view-controller)
[![Latest Stable Version](https://poser.pugx.org/inc2734/wp-view-controller/v/stable)](https://packagist.org/packages/inc2734/wp-view-controller)
[![License](https://poser.pugx.org/inc2734/wp-view-controller/license)](https://packagist.org/packages/inc2734/wp-view-controller)

## Install
```
$ composer require inc2734/wp-view-controller
```

## How to use
```
<?php
// When Using composer auto loader
// $Blog_Card = new Inc2734\WP_View_Controller\View_Controller();

// When not Using composer auto loader
include_once( get_template_directory() . '/vendor/inc2734/wp-view-controller/src/wp-view-controller.php' );
$Blog_Card = new Inc2734_WP_View_Controller();
```
