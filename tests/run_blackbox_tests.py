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
        print("Memulai Black Box Testing & Screenshot")
        print("====================================")

        # 1. Halaman formulir kosong
        print("[1/5] Skenario: Screenshot Formulir Kosong")
        await page.goto(f"{BASE_URL}/")
        await page.wait_for_load_state("networkidle")
        
        screenshot_1 = os.path.join(OUTPUT_DIR, "1_halaman_formulir_kosong.png")
        await page.screenshot(path=screenshot_1, full_page=True)
        print(f"  -> Disimpan: {screenshot_1}")

        # --- Skenario 1: Keluhan IT Normal/Jelas ---
        print("\n[2/5] Skenario 1: Keluhan IT Normal")
        await page.fill('input[id="data.nik"]', '10001')
        await page.keyboard.press("Tab") # Trigger onBlur
        await page.wait_for_timeout(2000) # Wait for auto-fill
        
        await page.locator('.choices__inner').click()
        await page.wait_for_timeout(500)
        await page.locator('.choices__item--choice').nth(1).click()
        
        await page.fill('input[id="data.deskripsi_umum_masalah"]', 'Wifi di ruangan saya tidak bisa terkoneksi')
        
        await page.locator('trix-editor').click()
        await page.keyboard.type('Sejak pagi wifi putus nyambung dan tidak bisa buat buka internet.')
        
        await page.click('button[type="submit"]')
        
        try:
            await page.wait_for_url(re.compile(r".*/laporan-sukses/.*"), timeout=45000)
        except Exception as e:
            await page.screenshot(path=os.path.join(OUTPUT_DIR, "error_skenario_1.png"), full_page=True)
            html = await page.content()
            with open(os.path.join(OUTPUT_DIR, "error_skenario_1.html"), "w", encoding="utf-8") as f:
                f.write(html)
            raise e
            
        current_url = page.url
        uuid_1 = current_url.split('/laporan-sukses/')[1]
        print(f"  -> Berhasil membuat tiket Skenario 1 (Normal). UUID: {uuid_1}")
        
        await page.goto(f"{BASE_URL}/laporan/cek?uuid={uuid_1}")
        await page.wait_for_load_state("networkidle")
        await page.wait_for_timeout(2000)
        screenshot_5 = os.path.join(OUTPUT_DIR, "5_bukti_tiket_skenario_1.png")
        await page.screenshot(path=screenshot_5, full_page=True)
        print(f"  -> Disimpan: {screenshot_5}")


        # --- Skenario 2: Keluhan IT Ambigu (Fallback) ---
        print("\n[3/5] Skenario 2: Keluhan IT Ambigu (Fallback Confidence Rendah)")
        await page.goto(f"{BASE_URL}/")
        await page.wait_for_load_state("networkidle")
        
        await page.fill('input[id="data.nik"]', '10001')
        await page.keyboard.press("Tab")
        await page.wait_for_timeout(2000)
        
        await page.locator('.choices__inner').click()
        await page.wait_for_timeout(500)
        await page.locator('.choices__item--choice').nth(1).click()
        
        await page.fill('input[id="data.deskripsi_umum_masalah"]', 'Tolong perbaiki secepatnya')
        
        await page.locator('trix-editor').click()
        await page.keyboard.type('Alat ini rusak, sudah lama tidak bisa dipakai. Mohon segera dicek karena butuh.')
        
        await page.click('button[type="submit"]')
        await page.wait_for_url(re.compile(r".*/laporan-sukses/.*"), timeout=45000)
        
        current_url2 = page.url
        uuid_2 = current_url2.split('/laporan-sukses/')[1]
        print(f"  -> Berhasil membuat tiket Skenario 2 (Ambigu). UUID: {uuid_2}")
        
        await page.goto(f"{BASE_URL}/laporan/cek?uuid={uuid_2}")
        await page.wait_for_load_state("networkidle")
        await page.wait_for_timeout(2000)
        screenshot_6 = os.path.join(OUTPUT_DIR, "6_bukti_tiket_skenario_2.png")
        await page.screenshot(path=screenshot_6, full_page=True)
        print(f"  -> Disimpan: {screenshot_6}")


        # --- Admin Dashboard & Detail ---
        print("\n[4/5] Skenario: Screenshot Dashboard & Admin Detail")
        await page.goto(f"{BASE_URL}/admin/login")
        await page.fill('input[id="data.email"]', 'admin@ptpn.com') 
        await page.fill('input[id="data.password"]', 'password')
        await page.click('button[type="submit"]')
        
        try:
            await page.wait_for_url(re.compile(r".*/admin.*"), timeout=5000)
            await page.wait_for_load_state("networkidle")
            screenshot_3 = os.path.join(OUTPUT_DIR, "3_dashboard_admin.png")
            await page.screenshot(path=screenshot_3, full_page=True)
            print(f"  -> Disimpan: {screenshot_3}")
            
            await page.goto(f"{BASE_URL}/admin/tickets")
            await page.wait_for_load_state("networkidle")
            
            try:
                await page.locator('table tr td a').first.click(timeout=3000)
                await page.wait_for_load_state("networkidle")
                screenshot_4 = os.path.join(OUTPUT_DIR, "4_detail_tiket_admin.png")
                await page.screenshot(path=screenshot_4, full_page=True)
                print(f"  -> Disimpan: {screenshot_4}")
            except Exception as e:
                 print("Gagal buka detail tiket di admin:", e)
        except Exception as e:
            print("Gagal login admin. Beralih ke halaman utama.")
        

        # --- Skenario 4: Pelacakan Status Tiket (Halaman /laporan/cek Umum) ---
        print("\n[5/5] Skenario 4: Pelacakan Tiket (General View)")
        await page.goto(f"{BASE_URL}/laporan/cek?uuid={uuid_1}")
        await page.wait_for_load_state("networkidle")
        screenshot_8 = os.path.join(OUTPUT_DIR, "8_halaman_pelacakan_skenario_4.png")
        await page.screenshot(path=screenshot_8, full_page=True)
        print(f"  -> Disimpan: {screenshot_8}")
        
        uuids = {
            "skenario_1_uuid": uuid_1,
            "skenario_2_uuid": uuid_2
        }
        with open(os.path.join(OUTPUT_DIR, "uuids.json"), 'w') as f:
            json.dump(uuids, f)

        await browser.close()
        print("Selesai.")

if __name__ == "__main__":
    asyncio.run(run())
