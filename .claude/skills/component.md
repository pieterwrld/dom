---
name: component
description: Generate a new WordPress template part following the dom-theme conventions
---

# Create Template Part

When creating a new template part for dom-theme, follow this structure:

## File location
`dom-theme/template-parts/<component-name>.php`

## Template

```php
<?php
/**
 * Template part: <Component Name>
 *
 * @package DomTheme
 */

defined('ABSPATH') || exit;

// Get ACF fields if needed
// $field = get_field('field_name', 'option');
?>

<section class="dom-<component-name>">
  <div class="dom-container">
    <!-- Component HTML here -->
    <!-- Use Tailwind utility classes for layout -->
    <!-- Use BEM for component-specific styling: .dom-<component>__element -->
  </div>
</section>
```

## Rules

1. **Naming:** File name matches component: `hero-video.php` → `dom-hero-video` section class
2. **Container:** Always wrap content in `.dom-container` for consistent max-width and padding
3. **Tailwind:** Use utility classes for spacing, flex, grid, responsive breakpoints
4. **Custom CSS:** Only add custom CSS in `assets/css/input.css` under `@layer components` if Tailwind can't handle it (animations, complex hovers)
5. **ACF fields:** Reference fields from CLAUDE.md. Use `get_field()` for page fields, `get_field('name', 'option')` for global options
6. **Images:** Always use `wp_get_attachment_image()` with appropriate size. Add `loading="lazy"` for below-fold images.
7. **Responsive:** Mobile-first. Start with mobile layout, add `md:` and `lg:` breakpoints
8. **Accessibility:** Semantic HTML, aria-labels on interactive elements, visible focus states
9. **JavaScript:** If the component needs JS, add it as a small module in `assets/js/` and enqueue conditionally

## Including in templates

```php
get_template_part('template-parts/component-name');
```

## Passing data

```php
get_template_part('template-parts/component-name', null, [
    'title' => 'Custom Title',
    'items' => $items,
]);
// Access in template part: $args['title']
```
