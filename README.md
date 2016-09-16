#Introduction

A simple plugin to add custom post types and categories for use on Great Homeschool Convention’s website.

#Usage

- Install the plugin
- Customize the `page-speakers.php` theme template to show posts as necessary

#Other Notes

Available shortcodes:

##`[convention_cta]`: displays the CTA text for the specified convention; accepts the argument `convention` (two-letter abbreviation)

##`[convention_icon]`: displays the icon specified; accepts the argument `convention` (two-letter abbreviation)

##`[discretionary_registration]`: shows a box with link to registration, optionally for the specified convention; intended for use in blog posts; accepts these arguments:

- `convention` (two-letter abbreviation)
- `year` (four-digit year)
- `intro_text` (string of text to precede the button/link)

##`[hotel_grid]`: displays a grid of hotels for the specified convention; accepts the argument `convention` (two-letter abbreviation)

##`[speaker_archive]`: displays a grid of all speakers; intended for the archive page

##`[speaker_grid]`: displays a grid of speakers for the specified convention; intended for the individual locations pages; accepts the argument `convention` (two-letter abbreviation)

##`[speaker_info]`: displays speaker photo, name, conventions; accepts these arguments:

- `post_id`
- `pagename` (slug)
- `align` (left, right, etc.)
- `no_conventions` (true)
- `photo_only` (true)
- `extra_classes` (string of classes to add)

#Changelog

- 1.8
    - Use special events instead of speakers, fix convention sort order, and more

- 1.7
    - Add functionality features that were in the theme `functions.php`

- 1.6
    - Add locations, special events, and workshops custom post types
    - Use Advanced Custom Fields instead of hand-coded metaboxes

- 1.5.2
    - Add backend JS to help exhibitor URLs

- 1.5.1
    - Fix some bugs

- 1.5
    - Add “hotels” custom post type

- 1.4
    - Add support for matching WP users with speakers for use in blog posts, etc.

- 1.3
    - Add support for featured/general speakers

- 1.2
    - Add “sponsors” custom post type

- 1.1
    - Add “exhibitors” custom post type

- 1.0.1
    - Add “speaker type” and “convention” taxonomies

- 1.0
    - Add “speaker” custom post type”
