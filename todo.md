# Launch Dev
- [x] Area Hotels
- [x] Speakers
- [x] Special Events
- [x] Exhibitors
- [x] Special Tracks

# Launch Content
- [ ] Workshops
- [ ] Speakers
- [ ] Special Events
- [ ] Sponsors
- [x] Exhibitors
- [x] Hotels
- [ ] Testimonials

# Launch checklist
- [x] Resave permalinks
- [x] Update SE, MW, FL conventions:
	- [x] Location name, slug, and info
	- [x] Location tax name and slug
	- [x] Product name, slug, info, and image
	- [x] product_cat name, slug, and image
- [ ] Add 301 redirects:
	- [ ] locations/southeast -> locations/south-carolina
	- [ ] locations/midwest -> locations/ohio
- [ ] Regenerate missing media thumbnails (speaker-miniscule)
- [x] Sync all ACF groups
- [x] Run `convention CTAs.sql`?
- [x] Update each location’s map and features
	- [x] Remove OpenGraph video from each
- [x] Add 2019 Robly lists and update all products and forms
- [x] Update price sheet content, title, slug

# Dev
- [x] Pricing table pages using ACF content
- [ ] Add lightbox to speaker carousel (all carousels?)
- [x] Move WC email content to ACF option
- [x] Finish `[register]` per Josh’s design
	- [x] Fix totals calculation
	- [x] Fix min going into negative
- [ ] Add auto cache-clearing when CTA or pricing schedule rolls over
	+ On `save_post_location`, add wp-cron job to clear locations transient and WP Super Cache on each future date?
	+ Or set a cron job to clear and cache at 1AM?
- [ ] Update `load_conventions_info`:
	+ Run on `save_post_location` and `save_post_product`
	+ Include main registration product ID + title
	+ Include special events product ID + title
	+ Remove `wc_get_products` from `register` shortcode and get from transient instead
	+ Drop products out once past the sale date
- [ ] Add expiration date to product CPT? Restrict somehow based on associated convention dates?

# Forms
- [x] Replace hardcoded convention locations with `[convention_form_list]` or `[convention_form_list format "short"]`
- [x] Remove robly-lists.min.js from [this form](https://greathomeschoolconventions.com/wp-admin/admin.php?page=wpcf7&post=57398&action=edit)

# Convention CTA
- [ ] Add popups for all conventions and schedule future publish dates
- [ ] Add `[convention_cta]` to all pricing pages
- [ ] Edit all convention CTAs to include headers and buttons
- [ ] Purge caches

# GDPR
- [ ] Uncheck WooCommerce checkbox by default
- [ ] Add notice about adding customers to our marketing list
- [ ] Add notice to all CF7 forms
- [ ] Figure out what we’re doing with Mautic

# DNS
- [ ] Global SPF/DKIM
- [ ] Robly SPF/DKIM
- [ ] Mailgun SPF/DKIM
- [ ] Disable WP Super Cache CDN offloading

# Eventually
- [ ] Convert shortcodes to Gutenberg blocks
