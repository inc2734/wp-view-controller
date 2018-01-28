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
$Controller = new Inc2734\WP_View_Controller\View_Controller();
```

## Directory structure
```
themes/your-theme-name/
├─ vendor                   # → Composer packages (never edit)
├─ page-template            # → Custom page templates directory
├─ templates                # → Base templates directory
│   ├─ layout
│   │   ├─ wrapper          # → Layout templates
│   │   ├─ header           # → Header templates
│   │   ├─ sidebar          # → Sidebar templates
│   │   └─ footer           # → Footer templates
│   ├─ view                 # → View templates
│   └─ static               # → Static view templates
└─ index.php, page.php ect...
```

## Layout template

The lyout template requires `<?php $_view_controller->view(); ?>`.

## View templates

### In singular page

Loading `/templates/view/content-{post-type}.php` for the view template.
Loading `/templates/view/content.php` when `/templates/view/content-{post-type}.php` isn't exists.

### In archive page

Loading `/templates/view/archive-{post-type}.php` for the view template.
Loading `/templates/view/archive.php` when `/templates/view/archive-{post-type}.php` isn't exists.

### Static view templates

Tries to load the view template according to the URL. For example when URL is http://example.com/foo/bar, tries to laod from `/templates/static/foo/bar.php` or `/templates/static/foo/bar/index.php`.

## Using view controller
```
$controller = new Inc2734_WP_View_Controller();
$controller->layout( 'right-sidebar' );
$controller->render( 'content', 'news' );
```

## Template tags

### wpvc_get_template_part()

This is a function which to pass the variables to WordPress's `get_template_part()`.

```
// The caller
wpvc_get_template_part( 'path/to/template-parts', [
	'_foo' => 'bar',
	'_baz' => 'qux',
] );

// The called template. path/to/template-parts.php
<ul>
	<li><?php echo esc_html( $_foo ); // bar ?></li>
	<li><?php echo esc_html( $_baz ); // qux ?></li>
</ul>
```
