import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()
        await page.goto('http://127.0.0.1:8001/admin/login')
        html = await page.content()
        with open('tests/admin_login.html', 'w', encoding='utf-8') as f:
            f.write(html)
        await browser.close()

asyncio.run(run())
