// Mobile screenshot capture (iPhone 11 Pro Max viewport).
// Usage: node scripts/screenshot-mobile.cjs   (dev server must run on :8787)
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = 'http://127.0.0.1:8787';
const OUT = path.join(__dirname, '..', 'public', 'marketing', 'screens-mobile');
fs.mkdirSync(OUT, { recursive: true });

const MOBILE = { viewport: { width: 414, height: 896 }, deviceScaleFactor: 2, isMobile: true, hasTouch: true };

async function settle(page) {
  await page.waitForTimeout(1200);
  await page.evaluate(() => { document.querySelectorAll('.reveal').forEach(e => e.classList.add('visible')); window.scrollTo(0, 0); });
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
    console.log('  OK  ' + file);
  } catch (e) { console.log('  FAIL ' + file + ' (' + e.message.split('\n')[0] + ')'); }
  await page.close();
}

(async () => {
  const browser = await chromium.launch();
  const pub = await browser.newContext(MOBILE);
  await shot(pub, '/', 'landing-mobile.png', true);
  await pub.close();

  const demo = await browser.newContext(MOBILE);
  await login(demo, 'demo@webwa.test');
  await shot(demo, '/dashboard', 'dashboard-mobile.png');
  await shot(demo, '/devices', 'devices-mobile.png');
  await demo.close();

  await browser.close();
  console.log('Done. -> ' + OUT);
})();
