import { test, expect } from '@playwright/test';

async function loginAsAdmin(page: any) {
  await page.goto('/wp-login.php');
  await page.getByLabel('Username or Email Address').fill('admin');
  await page.locator('#user_pass').fill('password');
  await page.getByRole('button', { name: 'Log In' }).click();
  await expect(page).toHaveURL(/\/wp-admin\//);
}

test('renders the WikiPress admin dashboard', async ({ page }) => {
  await loginAsAdmin(page);

  await page.goto('/wp-admin/admin.php?page=wikipress');

  await expect(page.getByRole('heading', { name: 'Quick actions', exact: true })).toBeVisible();
  await expect(page.locator('a.wikipress-summary-card')).toHaveCount(6);
  await expect(page.getByRole('link', { name: /User groups/i })).toBeVisible();
  await expect(page.getByRole('link', { name: /Access settings/i })).toBeVisible();
});

test('loads the wiki manager page and shows the wiki list screen', async ({ page }) => {
  await loginAsAdmin(page);

  await page.goto('/wp-admin/admin.php?page=wikipress&group=wiki-manager&tab=wikis');

  await expect(page.getByRole('heading', { name: 'Wiki Manager', exact: true })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Wikis', exact: true })).toBeVisible();
  await expect(page.getByRole('button', { name: 'Create New Wiki', exact: true }).first()).toBeVisible();
});
