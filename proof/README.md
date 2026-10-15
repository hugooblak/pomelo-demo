# Test proof — Pomelo Coffee Roasters

Run on 2026-10-15 against a fresh WordPress install with the demo content. Local test server, no compression.

| Check | Result | Details |
|---|---|---|
| 1. Markup lint | ✅ Pass | clean |
| 2. Pages load | ✅ Pass | 6 pages |
| 3. Link crawl | ✅ Pass | 150 URLs checked |
| 4. Editor: no invalid blocks | ❌ Fail | templates archive-product: missing block woocommerce/breadcrumbs, templates archive-product: missing block woocommerce/product-results-count, templates archive-product: missing block woocommerce/catalog-sorting, templates taxonomy-product_attribute: missing block woocommerce/breadcrumbs, templates taxonomy-product_attribute: missing block woocommerce/product-results-count, templates taxonomy-product_attribute: missing block woocommerce/catalog-sorting, templates product-search-results: missing block woocommerce/breadcrumbs, templates product-search-results: missing block woocommerce/product-results-count, templates product-search-results: missing block woocommerce/catalog-sorting, templates order-confirmation: missing block woocommerce/order-confirmation-status, templates order-confirmation: missing block woocommerce/order-confirmation-summary, templates order-confirmation: missing block woocommerce/order-confirmation-totals-wrapper, templates order-confirmation: missing block woocommerce/order-confirmation-totals, templates order-confirmation: missing block woocommerce/order-confirmation-downloads-wrapper, templates order-confirmation: missing block woocommerce/order-confirmation-downloads, templates order-confirmation: missing block woocommerce/order-confirmation-shipping-wrapper, templates order-confirmation: missing block woocommerce/order-confirmation-shipping-address, templates order-confirmation: missing block woocommerce/order-confirmation-billing-wrapper, templates order-confirmation: missing block woocommerce/order-confirmation-billing-address, templates order-confirmation: missing block woocommerce/order-confirmation-additional-fields-wrapper, templates order-confirmation: missing block woocommerce/order-confirmation-additional-fields, templates order-confirmation: missing block woocommerce/order-confirmation-additional-information, templates single-product: missing block woocommerce/breadcrumbs, templates single-product: missing block woocommerce/product-image-gallery |
| 5. Editor font sizes = live site | ✅ Pass | 24 text elements compared |
| 6. Accessibility (axe, WCAG 2.2 AA) | ✅ Pass | 6 pages × 2 widths, 0 violations |
| 7. Keyboard | ✅ Pass | skip link, focus outline, FAQ, mobile menu |
| 8. Forms | ✅ Pass | 9 forms: every field labelled (CF7: errors shown) |
| 9. Layout 320–1440px | ✅ Pass | no overflow, header fits |
| 10. Lighthouse (mobile) | ❌ Fail | / {"performance":68,"accessibility":100,"best-practices":100,"seo":92}, /shop/ {"performance":61,"accessibility":100,"best-practices":100,"seo":92}, /product/brazil-mantiqueira/ {"performance":58,"accessibility":100,"best-practices":100,"seo":92}, /cart/ {"performance":35,"accessibility":100,"best-practices":96,"seo":54}, /my-account/ {"performance":54,"accessibility":100,"best-practices":100,"seo":50} |
| 11. Screenshots | ✅ Pass | 15 saved to proof/screenshots |

## Lighthouse (mobile)

| Page | Speed | Accessibility | Best practices | SEO | LCP | CLS |
|---|---|---|---|---|---|---|
| / | 68 | 100 | 100 | 92 | 5.0 s | 0 |
| /shop/ | 61 | 100 | 100 | 92 | 6.3 s | 0 |
| /product/brazil-mantiqueira/ | 58 | 100 | 100 | 92 | 7.4 s | 0 |
| /cart/ | 35 | 100 | 96 | 54 | 18.7 s | 0 |
| /my-account/ | 54 | 100 | 100 | 50 | 6.3 s | 0 |

Automated tests cannot catch every accessibility or design problem. A real project adds manual screen reader testing.

## Notes on the two failing checks (written by hand, 2026-10-15)

**4. Editor: no invalid blocks.** Every flagged item is one of WooCommerce's own templates (archive-product,
single-product, order-confirmation and so on). WooCommerce only loads breadcrumbs, catalog sorting, product
results count, the image gallery and the order-confirmation blocks in the *site* editor, and this check reads
templates from the *post* editor, where they show as "missing". The same templates checked in the site
editor (`wp-admin/site-editor.php`): 14 templates and parts, 0 invalid or missing blocks. The theme's own
templates, parts, patterns and pages pass.

**10. Lighthouse (mobile).** Accessibility 100 and best practices 96 to 100 on every page. Speed is held down
by the local test server, not the page: WordPress Playground on this PC answers each page in 3 to 5 seconds
once WooCommerce is installed, and sends nothing compressed (Lighthouse counts both). The home page drops
WooCommerce's classic shop stylesheets it does not use and serves a 900 px hero to phones. SEO is 92 on the
shop pages only because Lighthouse rejects the local server's robots.txt (its content is WooCommerce's
standard file). Cart and account pages score 50 to 54 on SEO because WooCommerce marks them `noindex` on
purpose: search engines should not index a cart.

**Tested by hand on a fresh install:** add to cart from a home page card (badge updates, cart drawer opens
with the coffee: `flow-cart-drawer.png`), checkout with the demo payment, order-received page
(`flow-order-received.png`), and the order in WooCommerce > Orders.
