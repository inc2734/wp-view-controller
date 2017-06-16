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
// $Controller = new Inc2734\WP_View_Controller\View_Controller();

// When not Using composer auto loader
include_once( get_template_directory() . '/vendor/inc2734/wp-view-controller/src/wp-view-controller.php' );
$Controller = new Inc2734_WP_View_Controller();
```

## Directory structure
```
themes/your-theme-name/
├── vendor                   # → Composer packages (never edit)
└── templates
    ├── layout
    │   ├── wrapper          # → Layout templates
    │   ├── header           # → Header templates
    │   ├── sidebar          # → Sidebar templates
    │   └── footer           # → Footer templates
    └── view                 # → View templates
        └── static           # → Static view templates
```

## Layout template

The lyout template requires `<?php $this->view(); ?>`.

## View templates

### In singular page

Mimizuku loading `/templates/view/content-{post-type}.php` for the view template.
Loading `/templates/view/content.php` when `/templates/view/content-{post-type}.php` isn't exists.

### In archive page

Mimizuku loading `/templates/view/archive-{post-type}.php` for the view template.
Loading `/templates/view/archive.php` when `/templates/view/archive-{post-type}.php` isn't exists.

### Static view templates

Mimizuku tries to load the view template according to the URL. For example when URL is http://example.com/foo/bar, tries to laod from `/templates/view/static/foo/bar.php`.

## Using view controller
```
$controller = new Mimizuku_Controller();
$controller->layout( 'right-sidebar' );
$controller->render( 'content', 'news' );
```

## Template tags

### mimizuku_get_template_part()

This is a function which to pass the variables to WordPress's `get_template_part()`.

```
// The caller
mimizuku_get_template_part( 'path/to/template-parts', [
	'_foo' => 'bar',
	'_baz' => 'qux',
] );

// The called template. path/to/template-parts.php
<ul>
	<li><?php echo esc_html( $_foo ); // bar ?></li>
	<li><?php echo esc_html( $_baz ); // qux ?></li>
</ul>
```
