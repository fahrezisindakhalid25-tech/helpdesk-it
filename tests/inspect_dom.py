import asyncio
from playwright.async_api import async_playwright

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        page = await browser.new_page()
        await page.goto("http://127.0.0.1:8001/lapor")
        await page.wait_for_load_state("networkidle")
        
        # Get all inputs
        inputs = await page.locator('input').element_handles()
        for i in inputs:
            print("INPUT ID:", await i.get_attribute("id"), "TYPE:", await i.get_attribute("type"))
        
        await browser.close()

if __name__ == "__main__":
    asyncio.run(run())
