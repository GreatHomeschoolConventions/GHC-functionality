# Introduction

A plugin to add custom post types and categories for use on [Great Homeschool Conventions’ website](https://www.greathomeschoolconventions.com/), developed by ![AndrewRMinion Design](https://andrewrminion.com/).

See ![GHC documentation](https://bitbucket.org/greathomeschoolconventions/ghc-documentation/src/master/) for more info.

# Usage

- Install the plugin
- Use shortcodes as necessary
- When posting blog articles, create a new user (if necessary) with the “Contributor” role
    - Add the square photo from the speaker CPT
    - Add a few sentences of bio in the biography field
    - Select their name from the “Select a speaker to match this author” dropdown (this will automatically pull in their conventions info)

# Other Notes

Available shortcodes; see documentation in `inc/class-ghc-shortcodes.php` for up-to-date usage information:

- `[carousel]`
- `[container]`
- `[convention_address]`
- `[convention_cta]`
- `[convention_icon]`
- `[convention_features]`
- `[convention_pricing]`
- `[exhibitor_list]`
- `[exhibit_hall_hours]`
- `[hotel_grid]`
- `[locations_map]`
- `[price_sheet]`
- `[product_price]`
- `[register]`
- `[speaker_archive]`
- `[speaker_grid]`
- `[speaker_info]`
- `[speaker_list]`
- `[speaker_tags]`
- `[special_event_grid]`
- `[special_event_list]`
- `[special_track_speakers]`
- `[sponsors]`
- `[workshop_list]`


# Changelog

- 4.1.5
	+ Add support for intro content in `[carousel]` shortcode
	+ Add program guide to `[register]` shortcode
	+ Add header to `[locations]` shortcode
	+ Add support for free shopping pass

- 4.1.4
	+ Fix some add-to-cart issues
	+ Update hotel display
	+ Add WooCommerce email content to site options ACF group

- 4.1.3
	+ Fix price sheet text colors
	+ Fix post save behavior

- 4.1.2
	+ Update `[price_sheet]` shortcode to get live data from location ACF fields
	+ Add generic header fallback image

- 4.1.1
	+ Fix map click action

- 4.1.0
	+ Major overhaul of `[register]` shortcode
	+ Style tweaks

- 4.0.1
	+ Bugfixes pre-launch

- 4.0.0
	+ Major rewrite for 2019 season

- 3.2.7
    - Include convention icons as `<img>`s rather than SVG `<use>` for better performance

- 3.2.6
    - Modify responsive images `sizes` attribute so only the max size needed is downloaded

- 3.2.5
    - Add `rel="noopener noreferrer"` to all outbound links
    - Add support for location feature icons
    - Tweak product name meta

- 3.2.4
    - Add workaround `onkeyup` min/max ticket fix for touchscreen devices

- 3.2.3
    - Handle FlowXO bot-provided family member data

- 3.2.2
    - Set max ticket quantity to 6
    - Visually disable special event tickets (for logged-out users) until registration is in cart

- 3.2.1
    - Add support for GET-style parameters in URI fragment

- 3.2.0
    - Enforce special event ticket quantity on server-side based on family members in cart

- 3.1.3
    - Add visual indicators when adding products to cart

- 3.1.1
    - Hide teen track tickets when individual ticket type is selected
    - Add related sponsor logos on special track archives
    - Add `show_content` attribute to `[hotel_grid]` shortcode
    - Use color related sponsor logos

- 3.1.0
    - Fix a bunch of bugs
    - Add related speakers to workshops
    - Update `[speaker_info]` handle multiple speakers
    - Save workshops the related speaker’s post_meta to cut down on meta_queries for each page load
    - Show speakers’ conventions on their workshops
    - Add 2018 price sheets
    - Sort special track taxonomies to show speakers first
    - Add support for showing sponsor’s content along with their logo
    - Update family member quantities for WooCommerce v3.2.x
    - Add related sponsors to special track tax archives

- 3.0.6
    - Handle family member quantities

- 3.0.5
    - Set PopupMaker cookies when adding a product to the cart

- 3.0.4
    - Improve registration page display especially on mobile devices

- 3.0.3
    - Improve registration page behavior (scroll to attendee type, style attendee types similar to convention choice)

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
