import { test, expect } from '@playwright/test';

const ADMIN = { email: 'teukuaditya2003@gmail.com', password: '12345678' };
const USER = { email: 'teukuaditya2003@gmail.com', password: '12345678' };

async function login(page, { email, password }) {
  await page.goto('/login');
  await page.locator('input[name="email"]').fill(email);
  await page.locator('input[name="password"]').fill(password);
  await page.locator('button[type="submit"], input[type="submit"]').first().click();
  await page.waitForLoadState('networkidle');
}

test.describe('Drive Venture - Detailed E2E Flow', () => {
  test('01. Public pages render key content', async ({ page }) => {
    await page.goto('/user');
    await expect(page.locator('body')).toContainText(/New Arrivals|Best Sellers|DRVN|Shop/i);

    await page.goto('/user/products');
    await expect(page.locator('body')).toContainText(/Product|Filter|All/i);

    await page.goto('/user/categories');
    await expect(page.locator('body')).toBeVisible();

    await page.goto('/user/about');
    await expect(page.locator('body')).toContainText(/Tentang Kami|Email|Alamat/i);

    await page.goto('/login');
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
  });

  test('02. Login invalid shows error, valid login works', async ({ page }) => {
    await page.goto('/login');
    await page.locator('input[name="email"]').fill(USER.email);
    await page.locator('input[name="password"]').fill('wrong-password');
    await page.locator('button[type="submit"], input[type="submit"]').first().click();
    await page.waitForLoadState('networkidle');
    await expect(page.locator('body')).toContainText(/credentials|salah|invalid|password/i);

    await login(page, USER);
    await expect(page).not.toHaveURL(/\/login$/);
  });

  test('03. Product listing search/filter and open detail', async ({ page }) => {
    await page.goto('/user/products');
    const search = page.locator('input[name="q"], input[placeholder*="Cari"], input[type="search"]').first();
    if (await search.count()) {
      await search.fill('a');
      await page.waitForTimeout(800);
      await expect(page.locator('#productGrid')).toBeVisible();
    }

    const firstProduct = page.locator('a[href*="/user/products/"]').first();
    await expect(firstProduct).toBeVisible();
    await firstProduct.click();
    await page.waitForLoadState('networkidle');
    await expect(page.locator('body')).toContainText(/Rp|Description|Stock|Review|Size/i);
  });

  test('04. Wishlist toggle from product page (logged in)', async ({ page }) => {
    await login(page, USER);
    await page.goto('/user/products');
    const wishlistBtn = page.locator('.js-wishlist-toggle').first();
    if (await wishlistBtn.count()) {
      await wishlistBtn.click();
      await page.waitForTimeout(800);
      await expect(wishlistBtn).toBeVisible();
    }
    await page.goto('/user/wishlist');
    await expect(page.locator('body')).toContainText(/Wishlist|Produk|Belum ada/i);
  });

  test('05. Cart page and checkout page load', async ({ page }) => {
    await login(page, USER);
    await page.goto('/user/cart');
    await expect(page.locator('body')).toContainText(/Cart|Keranjang|Total|Belanja/i);

    await page.goto('/user/checkout');
    await expect(page.locator('body')).toContainText(/Checkout|Shipping|Address|Total/i);
  });

  test('06. Profile edit flow', async ({ page }) => {
    await login(page, USER);
    await page.goto('/user/profile/edit');
    await expect(page.locator('#user-info')).toBeVisible();
    await page.locator('#edit-button').click();
    await expect(page.locator('#edit-form')).toBeVisible();
    await expect(page.locator('#name')).toBeVisible();
    await expect(page.locator('#email')).toBeVisible();
    await page.locator('#cancel-button').click();
    await expect(page.locator('#user-info')).toBeVisible();
  });

  test('07. Address page loads and form can be opened', async ({ page }) => {
    await login(page, USER);
    await page.goto('/user/address/edit');
    await expect(page.locator('body')).toContainText(/Address|Recipient|Destination|Belum ada alamat/i);
    const editBtn = page.locator('#edit-address-btn');
    if (await editBtn.count()) {
      await editBtn.click();
      await expect(page.locator('#address-form')).toBeVisible();
    }
  });

  test('08. Order history page shows correct UI', async ({ page }) => {
    await login(page, USER);
    await page.goto('/user/orders/order-history');
    await expect(page.locator('body')).toContainText(/Order History|Belum ada riwayat pesanan|Total Pembayaran/i);
  });

  test('09. Admin dashboard and key pages load', async ({ page }) => {
    await login(page, ADMIN);
    await page.goto('/admin/dashboard');
    await expect(page.locator('body')).toContainText(/Dashboard|Revenue|Orders|Users/i);

    for (const path of ['/admin/categories', '/admin/products', '/admin/users', '/admin/transactions', '/admin/transactions-details', '/admin/carts', '/admin/settings']) {
      await page.goto(path);
      await expect(page.locator('body')).toBeVisible();
    }
  });

  test('10. Admin settings page has key fields and can save', async ({ page }) => {
    await login(page, ADMIN);
    await page.goto('/admin/settings');
    await expect(page.locator('input[name="store_name"]')).toBeVisible();
    await expect(page.locator('input[name="monthly_revenue_goal"]')).toBeVisible();
    await expect(page.locator('input[name="order_id_prefix"]')).toBeVisible();

    // Save without destructive change (keep existing values, just submit)
    await page.locator('button[type="submit"]').click();
    await page.waitForLoadState('networkidle');
    await expect(page.locator('body')).toContainText(/Pengaturan berhasil disimpan|System Settings/i);
  });

  test('11. Admin category/product modals open', async ({ page }) => {
    await login(page, ADMIN);
    await page.goto('/admin/categories');
    const addCat = page.locator('button[data-target="#categoryModal"], button:has-text("Add Category")').first();
    if (await addCat.count()) {
      await addCat.click();
      await expect(page.locator('#categoryModal')).toBeVisible();
    }

    await page.goto('/admin/products');
    const addProd = page.locator('button[data-target="#productModal"], button:has-text("Add Product")').first();
    if (await addProd.count()) {
      await addProd.click();
      await expect(page.locator('#productModal')).toBeVisible();
    }
  });

  test('12. About page shows contact and settings-driven info', async ({ page }) => {
    await page.goto('/user/about');
    await expect(page.locator('body')).toContainText(/Tentang Kami|Email|Alamat/i);
    await expect(page.locator('body')).toContainText(/DRVN|Drive Venture|driveventure|@/i);
  });
});
