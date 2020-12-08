# WP View Controller

![CI](https://github.com/inc2734/wp-view-controller/workflows/CI/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/inc2734/wp-view-controller/v/stable)](https://packagist.org/packages/inc2734/wp-view-controller)
[![License](https://poser.pugx.org/inc2734/wp-view-controller/license)](https://packagist.org/packages/inc2734/wp-view-controller)

## Install
```
$ composer require inc2734/wp-view-controller
```

## How to use
```
<?php
// in functions.php
new Inc2734\WP_View_Controller\Bootstrap();
```

```
<?php
// in page template
use Inc2734\WP_View_Controller\Bootstrap;

Bootstrap::layout( 'right-sidebar' );
Bootstrap::render( 'front-page' );
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
use Inc2734\WP_View_Controller\Bootstrap;

Bootstrap::layout( 'right-sidebar' );
Bootstrap::render( 'content', 'news' );
```

## Template tags

### \\Inc2734\\WP_View_Controller\\Helper::get_template_part()

This method is an extension to `get_template_part()`.

## Filter hooks

### inc2734_wp_view_controller_config_path
```
/**
 * Change config path
 *
 * @param string $path
 * @return string $path
 */
add_filter(
	'inc2734_wp_view_controller_config_path',
	function( $path ) {
		return $path;
	}
);
```

### inc2734_wp_view_controller_pre_template_part_render
```
/**
 * Define and return the content before loading the template.
 * If a string is returned, the template will not load.
 *
 * @param null $html
 * @param string $slug
 * @param string $name
 * @param array $vars
 * @return string
 */
add_filter(
	'inc2734_wp_view_controller_pre_template_part_render',
	function( $html, $slug, $name, $vars ) {
		return $html;
	},
	10,
	4
);
```

### inc2734_wp_view_controller_template_part_render
```
/**
 * Customize the rendered HTML
 *
 * @param string $html
 * @param string $slug
 * @param string $name
 * @param array $vars
 * @return string
 */
add_filter(
	'inc2734_wp_view_controller_template_part_render',
	function( $html, $slug, $name, $vars ) {
		return $html;
	},
	10,
	4
);
```

### inc2734_wp_view_controller_layout
```
/**
 * Change layout file
 *
 * @param string $layout The layout file template slug
 * @return string
 */
add_filter(
	'inc2734_wp_view_controller_layout',
	function( $layout ) {
		return $layout;
	}
);
```

### inc2734_wp_view_controller_view
```
/**
* Change view file
*
* @param string $view The view file template slug
* @return string
*/
add_filter(
	'inc2734_wp_view_controller_view',
	function( $view ) {
		return $view;
	}
);
```

### inc2734_wp_view_controller_config
```
/**
 * Customize config values
 *
 * @param array $values
 * @return array
*/
add_filter(
	'inc2734_wp_view_controller_config',
	function( $values ) {
		return $values;
	}
);
```

### inc2734_wp_view_controller_controller
```
/**
 * Change controller
 *
 * @param string $template
 * @return string
 */
add_filter(
	'inc2734_wp_view_controller_controller',
	function( $template ) {
		return $template;
	}
);
```

### inc2734_wp_view_controller_get_template_part_args
```
/**
 * Customize template part args
 *
 * @param array $args
 *   @var string $slug
 *   @var string $name
 *   @var array $vars
 * @return array
 */
add_filter(
	'inc2734_wp_view_controller_get_template_part_args',
	function( $args ) {
		return $args;
	}
);
```

### inc2734_wp_view_controller_template_part_root_hierarchy
```
/**
 * Customize root hierarchy
 *
 * @param array $hierarchy
 * @param string $slug
 * @param string $name
 * @param array $vars
 * @return array
 */
add_filter(
	'inc2734_wp_view_controller_template_part_root_hierarchy',
	function( $hierarchy, $slug, $name, $vars ) {
		return $hierarchy;
	},
	10,
	4
);
```

### inc2734_wp_view_controller_located_template_slug_fallback
```
/**
 * You can specify a template for fallback
 *
 * @param string|null $fallback_slug
 * @param array $relative_dir_paths
 * @param string $slug
 * @param string $name
 * @return string|null
 */
add_filter(
	'inc2734_wp_view_controller_located_template_slug_fallback',
	function( $fallback_slug, $relative_dir_paths, $slug, $name ) {
		return $fallback_slug;
	},
	10,
	4
);
```

### inc2734_wp_view_controller_render_type
```
/**
 * Change rendered type.
 *
 * loop ... Loading in the WordPress loop
 * direct ... Simply loading
 *
 * @param string $render_type loop or direct
 * @return string
 */
add_filter(
	'inc2734_wp_view_controller_render_type',
	function( $render_type ) {
		return $render_type;
	}
);
```

## Action hooks

### inc2734_wp_view_controller_get_template_part_&lt;slug&gt;-&lt;name&gt;
```
/**
 * Define template
 *
 * @deprecated
 *
 * @param array $vars
 * @return void
 */
add_action(
	'inc2734_wp_view_controller_get_template_part_<slug>-<name>',
	function( $vars ) {
		?>
		HTML
		<?php
	}
);
```

### inc2734_wp_view_controller_get_template_part_&lt;slug&gt;
```
/**
 * Define template
 *
 * @deprecated
 *
 * @param string $name
 * @param array $vars
 * @return void
 */
add_action(
	'inc2734_wp_view_controller_get_template_part_<slug>',
	function( $name, $vars ) {
		?>
		HTML
		<?php
	},
	10,
	2
);
```

### inc2734_wp_view_controller_get_template_part_pre_render
```
/**
 * Fire before template rendering
 *
 * @param array $args
 */
add_action(
	'inc2734_wp_view_controller_get_template_part_pre_render',
	function( $args ) {
		// Do something
	}
);
```

### inc2734_wp_view_controller_get_template_part_post_render
```
/**
 * Fire after template rendering
 *
 * @param array $args
 */
add_action(
	'inc2734_wp_view_controller_get_template_part_post_render',
	function( $args ) {
		// Do something
	}
);
```
