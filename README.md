# Pomelo Coffee Roasters: specialty coffee shop demo (WordPress + WooCommerce)

A WordPress twin of the Pomelo Coffee Roasters concept site, built as a real WooCommerce shop.
**Pomelo Coffee Roasters is a fictional company.** The name, coffees, prices and photos are sample content.
Orders are saved, but no payment is taken and nothing ships.

**Live demo:** https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/hugooblak/pomelo-demo/main/blueprint.json

It runs in WordPress Playground: a whole WordPress site in the browser tab, nothing installed.
The first load takes a little longer, because WooCommerce is installed while it starts. Login is `admin` / `password`.

## What it shows

- **A real shop**: four coffees with photos, prices and tasting notes; shop page, product pages, cart drawer,
  cart, checkout and account. Checkout works end to end with a demo payment that takes no money, and every
  order lands in WooCommerce > Orders.
- **One colour per origin**: each product has a "Card colour" setting. The home page cards pour in in that colour.
- **Coffee cards that sell**: add to cart works in the background, a small bag arcs into the header cart and
  WooCommerce's own cart drawer opens.
- **Hero**: three bags with tasting-note tags that drop onto each bag on drawn lines.
- **Tasting-notes ticker** that drifts sideways and stops under the pointer.
- **Subscription band**: a line-drawn bean walks a loop and drops a bag each lap; the 2 or 4 week buttons change its pace.
- **Brew-method tiles**: each brewer fills with coffee as it comes into view.
- Reduced motion shows the finished states. Without JavaScript every block shows its finished state.

## Deliberately not here

- **Subscriptions** are marketing only. A real store adds a subscriptions plugin for WooCommerce, which is not
  installed here, so "Start a subscription" goes to the shop.
- A real payment provider, taxes, real shipping zones, reviews, a blog.

## How it is built

- Block theme, `theme.json`, native blocks and patterns. No page builder, no Custom HTML blocks.
- WooCommerce from wordpress.org; `content/setup.php` sets it up on a brand-new install (no setup wizard,
  US dollars, one flat shipping rate, the demo payment, the four products).
- Plugin **Pomelo Coffee Roasters Blocks** holds the six parts normal blocks cannot do: hero tags, ticker, coffee
  cards, subscription loop, brew tiles, newsletter field. One stylesheet and one script, one animation loop.
- Self-hosted fonts (Fraunces, DM Sans). No third-party scripts.
- Checks: see `proof/`.
