# Introduction

A plugin to add custom post types and categories for use on Great Homeschool Conventions’ website.

# Usage

- Install the plugin
- Use shortcodes as necessary

# Other Notes

Available shortcodes (see `inc/shortcodes.php` for up-to-date information):

## `[convention_cta]`

- displays the CTA text for the specified convention; accepts the argument `convention` (two-letter abbreviation)

## `[convention_icon]`

- displays the icon(s) specified; accepts the argument `convention` (two-letter abbreviation or comma-separated string)

## `[discretionary_registration]`

- shows a box with link to registration, optionally for the specified convention; intended for use in blog posts; accepts these arguments:
    - `convention` (two-letter abbreviation)
    - `year` (four-digit year)
    - `intro_text` (string of text to precede the button/link)

## `[exhibitor_list]`

- displays all exhibitors, optionally for the specified convention; accepts these arguments
    - `convention` (two-letter abbreviation)
    - `style` (allowed values “large” and “small”)

## `[exhibit_hall_hours]`

- displays exhibit hall hours defined in ACF options

## `[hotel_grid]`

- displays a grid of hotels for the specified convention; accepts the argument `convention` (two-letter abbreviation)

## `[price_sheet]`

- displays price sheet of specified convention, expecting `price-sheets/price-sheet-XX.html` in the plugin folder where `XX` is the lowercase abbreviation; accepts the argument `convention` (two-letter abbreviation)

## `[speaker_archive]`

- displays a grid of all speakers; intended for the archive page

## `[speaker_grid]`

- displays a grid of speakers for the specified convention; intended for the individual locations pages; accepts these arguments:
    - `convention` (two-letter abbreviation)
    - `posts_per_page` (number of speakers to show; defaults to “-1”, showing all posts)
    - `offset` (number of speakers to skip before showing the first; most useful in conjunction with a prior shortcode using “posts_per_page” to set a max number to show in that location)
    - `show` (comma-separated list of elements to show; allowed values include any combination of the following: image, conventions, name, bio, excerpt)
    - `image_size` (named image size or string of comma-separated integers creating an image size array)

## `[speaker_info]`

- displays speaker photo, name, conventions; accepts these arguments:
    - `post_id`
    - `pagename` (slug)
    - `align` (left, right, etc.)
    - `no_conventions` (true)
    - `photo_only` (true)
    - `extra_classes` (string of classes to add)

## `[speaker_list]`

- displays list of speaker names, optionally filtered by convention; accepts these arguments:
    - `convention` (two-letter abbreviation)
    - `posts_per_page` (number of speakers to show; defaults to “-1”, showing all posts)
    - `offset` (number of speakers to skip before showing the first; most useful in conjunction with a prior shortcode using “posts_per_page” to set a max number to show in that location)
    - `ul_class` (string of text to use as classes on `ul.speaker-list`)
    - `li_class` (string of text to use as classes on each `li`)
    - `a_class` (string of text to use as classes on each `a`)

## `[special_event_grid]`

- displays a grid of special events for the specified convention; intended for the individual locations pages; accepts these arguments:
    - `convention` (two-letter abbreviation)
    - `posts_per_page` (number of special events to show; defaults to “-1”, showing all posts)
    - `offset` (number of special events to skip before showing the first; most useful in conjunction with a prior shortcode using “posts_per_page” to set a max number to show in that location)
    - `show` (comma-separated list of elements to show; allowed values include any combination of the following: image, conventions, name, bio, excerpt)
    - `image_size` (named image size or string of comma-separated integers creating an image size array; defaults to )

## `[special_track_speakers]`

- displays speakers’ photos, names, and conventions for specified special track category; accepts the argument `track` with a special track slug

## `[sponsors]`

- displays all sponsors; accepts these arguments:
    - `gray` (specifies the grayscale logo should be used)
    - `width` (max width of image)

## `[workshops_schedule]`

- displays a workshop schedule; accepts the argument `convention` (two-letter abbreviation)

# Changelog

- 3.0.2
    - Remove workshop names from speaker archive
    - Add workshop full descriptions to speaker bio pages
    - Remove current workshop from related workshops list

- 3.0.1
    - Fix WooCommerce cart issues
        - Auto-complete orders
        - Restrict max quantities to family-members on registration product

- 3.0.0
    - Rebuild for 2018 convention season

- 2.6.3
    - Add special tracks filter to `workshops_schedule` shortcode

- 2.6.2
    - Add wrapper to `exhibitor_list`

- 2.6.1
    - Add `exhibitor_list` shortcode to display per-convention exhibitors

- 2.6
    - Add `workshops_schedule` shortcode to display detailed schedule
    - Rename `workshops_list` to `sessions_list` to maintain continuity with CPTs

- 2.5
    - Add `workshops_list` shortcode
    - Fix coupon display for admin and customer emails
    - Fix force sells for 2017 Jeff Foxworthy promo

- 2.4.1
    - Fix a bug in woocommerce.js if max was not specified
    - Handle cart quantities for 2017 Jeff Foxworthy promo

- 2.4
    - Add Workshop CPT for handling individual dates/times/locations, with ACF relating to speaker and GoodLayer’s sessions CPT for the full description
    - Add ACF JSON save point to sync ACF fields

- 2.3.4
    - Add hotel info to single views

- 2.3.3
    - Add content to hotels archive
    - Change hotels archive from `/hotel/` to `/hotels/`
    - Prepare for future hotel directions maps and filtering

- 2.3.2
    - Remove `[related_sponsor]` shortcode and append content to any singular post that has related sponsors specified

- 2.3.1
    - Add `[sponsors]` shortcode

- 2.3
    - Add price sheets

- 2.2.5
    - Update `[related_sponsor]` to output all sponsors if none are defined

- 2.2.4
    - Add `ul_class`, `li_class`, and `a_class` attributes to `[speaker_list]` shortcode for styling

- 2.2.3
    - Add `posts_per_page` and `offset` attributes to `[speaker_list]` shortcode

- 2.2.2
    - Add `related_sponsor` shortcode

- 2.2.1
    - Miscellaneous minor fixes

- 2.2
    - Add WooCommerce restrictions

- 2.1.1
    - Add video metadata

- 2.1
    - Add video OpenGraph data

- 2.0
    - Major updates for website redesign

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
