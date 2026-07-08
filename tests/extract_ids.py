import re
html = open('page_content.html', encoding='utf-8').read()
inputs = re.findall(r'<input[^>]+>', html)
for i in inputs:
    match = re.search(r'id=\"([^\"]+)\"', i)
    if match:
        print("ID:", match.group(1))
    else:
        print("NO ID:", i)
