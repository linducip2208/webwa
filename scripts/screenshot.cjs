// Desktop screenshot capture for WebWA marketing + docs.
// Usage: node scripts/screenshot.cjs   (dev server must run on :8787)
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = 'http://127.0.0.1:8787';
const OUT = path.join(__dirname, '..', 'public', 'marketing', 'screens');
fs.mkdirSync(OUT, { recursive: true });

async function settle(page) {
  // Reveal scroll-animated elements + let Tailwind CDN JIT + fonts settle.
  await page.waitForTimeout(1200);
  await page.evaluate(() => {
    document.querySelectorAll('.reveal').forEach(e => e.classList.add('visible'));
    window.scrollTo(0, 0);
  });
  await page.waitForTimeout(600);
}

async function login(context, email) {
  const page = await context.newPage();
  await page.goto(BASE + '/login', { waitUntil: 'networkidle' });
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', 'password');
  await Promise.all([
    page.waitForURL('**/dashboard', { timeout: 20000 }).catch(() => {}),
    page.click('button:has-text("Masuk")'),
  ]);
  await page.waitForTimeout(800);
  await page.close();
}

async function shot(context, route, file, fullPage = false) {
  const page = await context.newPage();
  try {
    await page.goto(BASE + route, { waitUntil: 'networkidle', timeout: 30000 });
    await settle(page);
    await page.screenshot({ path: path.join(OUT, file), fullPage });
    console.log('  OK  ' + file + '  <- ' + route);
  } catch (e) {
    console.log('  FAIL ' + file + '  (' + e.message.split('\n')[0] + ')');
  }
  await page.close();
}

(async () => {
  const browser = await chromium.launch();

  // --- Public (no auth) ---
  const pub = await browser.newContext({ viewport: { width: 1440, height: 900 }, deviceScaleFactor: 2 });
  await shot(pub, '/', 'landing.png', true);
  await shot(pub, '/docs', 'docs.png', true);
  await shot(pub, '/harga', 'pricing.png', true);
  await shot(pub, '/blog', 'blog.png', false);
  await pub.close();

  // --- Demo user (has devices + logs + api key) ---
  const demo = await browser.newContext({ viewport: { width: 1440, height: 900 }, deviceScaleFactor: 2 });
  await login(demo, 'demo@webwa.test');
  await shot(demo, '/dashboard', 'dashboard.png');
  await shot(demo, '/devices', 'devices.png');
  await shot(demo, '/devices/1', 'device-detail.png');
  await shot(demo, '/send', 'send.png');
  await shot(demo, '/api-keys', 'api-keys.png');
  await shot(demo, '/logs', 'logs.png');
  await demo.close();

  // --- Admin ---
  const admin = await browser.newContext({ viewport: { width: 1440, height: 900 }, deviceScaleFactor: 2 });
  await login(admin, 'admin@webwa.test');
  await shot(admin, '/admin', 'admin.png');
  await shot(admin, '/admin/users', 'admin-users.png');
  await admin.close();

  await browser.close();
  console.log('Done. -> ' + OUT);
})();
