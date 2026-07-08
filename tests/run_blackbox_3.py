import asyncio
import os
import re
import json
from playwright.async_api import async_playwright

BASE_URL = "http://127.0.0.1:8001"
OUTPUT_DIR = os.path.join(os.path.dirname(__file__), "hasil_evaluasi")
os.makedirs(OUTPUT_DIR, exist_ok=True)

async def run():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(viewport={'width': 1280, 'height': 800})
        page = await context.new_page()

        print("====================================")
        print("Skenario 3: Server NLP Timeout/Mati")
        print("====================================")

        await page.goto(f"{BASE_URL}/")
        await page.wait_for_load_state("networkidle")
        
        await page.fill('input[id="data.nik"]', '10001')
        await page.keyboard.press("Tab")
        await page.wait_for_timeout(2000)
        
        await page.locator('.choices__inner').click()
        await page.wait_for_timeout(500)
        await page.locator('.choices__item--choice').nth(1).click()
        
        await page.fill('input[id="data.deskripsi_umum_masalah"]', 'Test saat server mati')
        
        await page.locator('trix-editor').click()
        await page.keyboard.type('Ini adalah pengujian Skenario 3 saat API NLP dimatikan.')
        
        print("Men-submit form... tunggu response timeout (30s) atau fallback error...")
        await page.click('button[type="submit"]')
        
        # Wait for the page to show an error or a fallback success
        try:
            await page.wait_for_url(re.compile(r".*/laporan-sukses/.*"), timeout=45000)
            current_url3 = page.url
            uuid_3 = current_url3.split('/laporan-sukses/')[1]
            print(f"  -> Berhasil membuat tiket Skenario 3 (Fallback API mati). UUID: {uuid_3}")
            await page.goto(f"{BASE_URL}/laporan/cek?uuid={uuid_3}")
            await page.wait_for_load_state("networkidle")
        except:
            print("  -> Timeout/Error terdeteksi sesuai ekspektasi (API mati).")
        
        await page.wait_for_timeout(2000)
        screenshot_7 = os.path.join(OUTPUT_DIR, "7_bukti_tiket_skenario_3_server_mati.png")
        await page.screenshot(path=screenshot_7, full_page=True)
        print(f"  -> Disimpan: {screenshot_7}")
        
        # Load existing UUIDs and append Skenario 3 if success
        if 'uuid_3' in locals():
            uuids_file = os.path.join(OUTPUT_DIR, "uuids.json")
            if os.path.exists(uuids_file):
                with open(uuids_file, 'r') as f:
                    uuids = json.load(f)
            else:
                uuids = {}
                
            uuids["skenario_3_uuid"] = uuid_3
            with open(uuids_file, 'w') as f:
                json.dump(uuids, f)

        await browser.close()
        print("Selesai Skenario 3.")

if __name__ == "__main__":
    asyncio.run(run())
