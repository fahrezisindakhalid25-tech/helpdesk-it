import re
html = open('tests/admin_login.html', encoding='utf-8').read()
emails = re.findall(r'<input[^>]*id="[^"]*email[^"]*"[^>]*>', html)
print("Email inputs by id:", emails)
pwds = re.findall(r'<input[^>]*type="password"[^>]*>', html)
print("Password inputs by type:", pwds)
