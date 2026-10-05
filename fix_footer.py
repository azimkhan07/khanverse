import re, os
path = r"D:\Amtech\skillnest\resources\panels\src\data\footer.js"
with open(path, 'r', encoding='utf-8', errors='ignore') as f:
    s = f.read()
pattern = r'\s*\{\s*id:\s*4\s*,\s*title:\s*"Account"[\s\S]*?\},?\s*\r?\n'
s2 = re.sub(pattern, '\n', s, count=1)
with open(path, 'w', encoding='utf-8') as f:
    f.write(s2)
print('done')
