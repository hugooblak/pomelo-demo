/* Pomelo Coffee Roasters Blocks: one animation loop for the whole page (ticker, bean loop) plus small
 * one-off effects (hero tags, card pour, fly-to-cart, brewer fill, newsletter). Reduced motion lands
 * everything in its finished state. Without JavaScript every block already shows its finished state. */
(() => {
'use strict';
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
const $$ = (s, r = document) => [...r.querySelectorAll(s)];

/* ---------- hero: headline rises, then the tags drop onto the bags ---------- */
const hero = document.querySelector('.pm-hero');
if (hero) {
	const go = () => hero.classList.add('on');
	const img = hero.querySelector('.pm-hero-fig img');
	Promise.race([Promise.all([document.fonts ? document.fonts.ready : 0, img && img.decode ? img.decode().catch(() => 0) : 0]), new Promise((r) => setTimeout(r, 2000))]).then(go);
}

/* ---------- in view: cards pour, ticker icons draw, brewers fill ---------- */
const pour = (t) => {
	if (reduce) { t.classList.add('fill'); return; }
	t.classList.remove('on', 'pouring');
	void t.offsetWidth;
	t.classList.add('pouring');
	requestAnimationFrame(() => t.classList.add('on'));
	setTimeout(() => t.classList.remove('pouring'), 2600);
};
const io = new IntersectionObserver((es) => es.forEach((e) => {
	if (!e.isIntersecting) return;
	e.target.classList.add('on');
	if (e.target.classList.contains('btile')) pour(e.target);
	io.unobserve(e.target);
}), { threshold: 0.25 });
$$('.pm-card, .pm-ticker, .btile').forEach((el) => io.observe(el));
$$('.btile').forEach((t) => {
	let last = 0;
	t.addEventListener('mouseenter', () => {
		const n = performance.now();
		if (n - last > 3000 && t.classList.contains('on')) { last = n; pour(t); }
	});
});

/* ---------- ticker: endless, ~40px a second, stops under the pointer ---------- */
const tickers = $$('.pm-ticker').map((tk) => {
	const track = tk.querySelector('.ttrack'), set = tk.querySelector('.tset');
	const copy = set.cloneNode(true);
	copy.setAttribute('aria-hidden', 'true');
	track.appendChild(copy);
	const o = { track, set, x: 0, w: 0, paused: false };
	const measure = () => { o.w = set.getBoundingClientRect().width; };
	measure();
	addEventListener('resize', measure);
	tk.addEventListener('mouseenter', () => { o.paused = true; });
	tk.addEventListener('mouseleave', () => { o.paused = false; });
	return o;
});

/* ---------- subscription loop: the bean walks the dashed loop, a bag drops each lap ---------- */
const loops = $$('.pm-loop').map((lp) => {
	const svg = lp.querySelector('.loopsvg'), path = lp.querySelector('.track');
	const beanG = lp.querySelector('.beang'), bag = lp.querySelector('.dropbag'), next = lp.querySelector('.nextbag');
	const seg = lp.querySelector('.seg');
	const L = path.getTotalLength();
	let front = 0, fy = -1;
	for (let i = 0; i < 100; i++) { const y = path.getPointAtLength(i / 100 * L).y; if (y > fy) { fy = y; front = i / 100; } }
	const o = { lap: 7000, prog: (front + 0.88) % 1, on: false, last: 0, front };
	o.place = () => {
		const len = o.prog * L, p = path.getPointAtLength(len), q = path.getPointAtLength((len + 2) % L);
		const s = 0.82 + 0.3 * ((p.y - 154) / 112), flip = q.x < p.x ? -1 : 1;
		beanG.setAttribute('transform', `translate(${p.x} ${p.y}) scale(${s * flip} ${s}) translate(-78 -170)`);
	};
	o.tick = (dt, now) => {
		if (!o.on || reduce) return;
		o.prog = (o.prog + dt / o.lap) % 1;
		const d = (o.prog - o.front + 1) % 1;
		if (d < 0.02 && now - o.last > o.lap * 0.5) { o.last = now; bag.classList.remove('pop'); void bag.getBBox(); bag.classList.add('pop'); }
		o.place();
	};
	new IntersectionObserver((es) => { o.on = es[0].isIntersecting; svg.classList.toggle('walking', o.on && !reduce); }, { threshold: 0.1 }).observe(svg);
	if (seg) {
		const word = seg.dataset.daysWord || '';
		const btns = $$('button', seg);
		btns.forEach((b, i) => b.addEventListener('click', () => {
			btns.forEach((x) => x.setAttribute('aria-pressed', x === b ? 'true' : 'false'));
			const days = parseInt(b.dataset.days, 10) || 14;
			o.lap = 7000 * days / 14;
			next.textContent = `${days} ${word}`.trim();
		}));
	}
	o.place();
	return o;
});

/* ---------- one loop for everything that moves continuously ---------- */
let prev = performance.now();
const frame = (now) => {
	const dt = Math.min(64, now - prev);
	prev = now;
	if (!reduce) {
		tickers.forEach((o) => {
			if (o.paused || !o.w) return;
			o.x -= dt * 0.04;
			if (-o.x >= o.w) o.x += o.w;
			o.track.style.transform = `translate3d(${o.x.toFixed(2)}px,0,0)`;
		});
	}
	loops.forEach((o) => o.tick(dt, now));
	requestAnimationFrame(frame);
};
requestAnimationFrame(frame);

/* ---------- add to cart through WooCommerce's own cart store (the one the header cart reads),
   then a small bag arcs into the header cart. Without that store the button stays a normal link. ---------- */
let wcCart = null;
const importMap = document.querySelector('script[type="importmap"]');
const mods = importMap ? Object.keys(JSON.parse(importMap.textContent).imports || {}) : [];
if (mods.includes('@wordpress/interactivity') && mods.includes('@woocommerce/stores/woocommerce/cart')) {
	Promise.all([import('@wordpress/interactivity'), import('@woocommerce/stores/woocommerce/cart')])
		.then(([ia]) => { const s = ia.store('woocommerce'); if (s && s.actions && s.actions.addCartItem) wcCart = s; })
		.catch(() => {});
}
const cartTarget = () => document.querySelector('.wc-block-mini-cart__button') || document.querySelector('header.wp-block-template-part');
document.addEventListener('click', async (e) => {
	const b = e.target.closest('.pm-add');
	if (!b || !wcCart) return; // no store: the link adds the coffee by loading the page
	e.preventDefault();
	if (b.classList.contains('loading')) return;
	b.classList.add('loading');
	b.setAttribute('aria-busy', 'true');
	// WooCommerce updates the header cart at once and confirms with the server in the background
	// (if the server refuses, it rolls the cart back and shows its own notice). The bag flies on the
	// same timing, then the cart drawer opens.
	fly(b, () => document.querySelector('.wc-block-mini-cart__button')?.click());
	try {
		// quantityToAdd is "how many more", which is what a card button means.
		await wcCart.actions.addCartItem({ id: parseInt(b.dataset.product_id, 10), quantityToAdd: 1 });
	} catch (err) {
		location.href = b.href; // the plain link tries again the old way
	} finally {
		b.classList.remove('loading');
		b.removeAttribute('aria-busy');
	}
});
function fly(b, done) {
	const c = cartTarget();
	if (reduce || !c) { done(); return; }
	const a = b.getBoundingClientRect(), r = c.getBoundingClientRect();
	const f = document.createElement('div');
	f.className = 'pm-flyer';
	f.style.setProperty('--t', getComputedStyle(b.closest('.pm-card')).getPropertyValue('--t'));
	f.style.left = `${a.left + a.width / 2 - 13}px`;
	f.style.top = `${a.top - 10}px`;
	document.body.appendChild(f);
	const dx = r.left + r.width / 2 - (a.left + a.width / 2), dy = r.top + r.height / 2 - (a.top - 10) - 17;
	f.animate([
		{ transform: 'translate(0,0) scale(1)' },
		{ transform: `translate(${dx * 0.5}px,${(Math.min(dy, 0) - 120) * 0.6}px) scale(1.1) rotate(-8deg)`, offset: 0.45 },
		{ transform: `translate(${dx}px,${dy}px) scale(.4) rotate(6deg)`, opacity: 0.7 },
	], { duration: 800, easing: 'cubic-bezier(.3,.6,.4,1)' }).onfinish = () => {
		f.remove();
		c.classList.remove('pm-bump'); void c.offsetWidth; c.classList.add('pm-bump');
		setTimeout(done, 250);
	};
}

/* ---------- newsletter (demo, sends nothing) ---------- */
$$('.pm-signup').forEach((box) => {
	const form = box.querySelector('form'), input = form.email, err = box.querySelector('.ferr');
	const clear = () => { input.removeAttribute('aria-invalid'); if (err) err.hidden = true; };
	input.addEventListener('input', () => { input.parentNode.classList.toggle('filled', !!input.value); clear(); });
	form.addEventListener('submit', (e) => {
		e.preventDefault();
		if (!input.value || !input.checkValidity()) { input.setAttribute('aria-invalid', 'true'); if (err) err.hidden = false; input.focus(); return; }
		clear();
		form.hidden = true;
		const thanks = box.querySelector('.thanks');
		thanks.hidden = false;
		requestAnimationFrame(() => box.classList.add('on'));
	});
});

window.__ready = true;
})();
