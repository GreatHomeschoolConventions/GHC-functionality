# Launch Dev
- [ ] Area Hotels
- [ ] Speakers
- [ ] Special Events
- [ ] Exhibitors
- [ ] Special Tracks

# Launch Content
- [ ] Workshops
- [ ] Speakers
- [ ] Special Events
- [ ] Sponsors
- [ ] Exhibitors
- [ ] Hotels
- [ ] Testimonials

# Launch checklist
- [ ] Resave permalinks
- [ ] Sync all ACF groups
- [ ] Run `convention CTAs.sql`
- [ ] Update each location’s map and features
	- [ ] Remove OpenGraph video from each
- [ ] Add 2019 Robly lists and update all products and forms

# Dev
- [ ] Add lightbox to speaker carousel (all carousels?)
- [ ] Finish `[register]` per Josh’s design
	+ [ ] Add Select2 to `.select2` on frontend
- [ ] Add auto cache-clearing when CTA or pricing schedule rolls over
	+ On `save_post_location`, add wp-cron job to clear locations transient and WP Super Cache on each future date?
- [ ] Update `load_conventions_info`:
	+ Run on `save_post_location` and `save_post_product`
	+ Include main registration product ID + title
	+ Include special events product ID + title
	+ Remove `wc_get_products` from `register` shortcode and get from transient instead
	+ Drop products out once past the sale date
- [ ] Add expiration date to product CPT? Restrict somehow based on associated convention dates?

# Forms
- [ ] Replace hardcoded convention locations with `[convention_form_list]` or `[convention_form_list format "short"]`
- [ ] Remove robly-lists.min.js from [this form](https://greathomeschoolconventions.com/wp-admin/admin.php?page=wpcf7&post=57398&action=edit)

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
