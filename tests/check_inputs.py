import re
html = open('tests/hasil_evaluasi/error_skenario_1.html', encoding='utf-8').read()

def get_val(id_str):
    match = re.search(r'<input[^>]*id="' + id_str + r'"[^>]*>', html)
    if not match: return "Not found"
    tag = match.group(0)
    val = re.search(r'value="([^"]*)"', tag)
    if val: return val.group(1)
    
    # Check if value is stored in x-model or something, or it doesn't exist
    return "No value attr: " + tag

print("NIK:", get_val("data\.nik"))
print("Ringkasan:", get_val("data\.deskripsi_umum_masalah"))
