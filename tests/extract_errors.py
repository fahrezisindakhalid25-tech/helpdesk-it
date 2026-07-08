import re
html = open('tests/hasil_evaluasi/error_skenario_1.html', encoding='utf-8').read()
errors = re.findall(r'<[^>]*class="[^"]*danger[^"]*"[^>]*>(.*?)</', html, re.IGNORECASE)
print("Danger texts:", errors)
