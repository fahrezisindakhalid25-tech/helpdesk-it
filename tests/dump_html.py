import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        page = await browser.new_page()
        await page.goto("http://127.0.0.1:8001/")
        await page.wait_for_load_state("networkidle")
        
        content = await page.content()
        with open("page_content.html", "w", encoding="utf-8") as f:
            f.write(content)
        
        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
