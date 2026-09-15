#!/usr/bin/env python3
"""
AK Fitness Pro Health Check - runs every hour via cron
"""
import urllib.request, urllib.parse, ssl, http.cookiejar, sys

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

cj = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj), urllib.request.HTTPSHandler(context=ctx))

def check_json(name, url, data=None, method='GET'):
    try:
        req = urllib.request.Request(url, data=data, method=method)
        if data:
            req.add_header('Content-Type', 'application/json')
        r = opener.open(req, timeout=30)
        if r.getcode() != 200:
            print(f"❌ {name}: HTTP {r.getcode()}")
            return False
        import json
        resp = json.loads(r.read().decode())
        return resp.get('success', False)
    except Exception as e:
        print(f"❌ {name}: {e}")
        return False

def check_html(name, url):
    try:
        r = opener.open(urllib.request.Request(url), timeout=30)
        if r.getcode() != 200:
            print(f"❌ {name}: HTTP {r.getcode()}")
            return False
        html = r.read().decode()
        # Check for PHP errors
        if 'Warning:' in html or 'Fatal' in html or 'Parse error' in html:
            print(f"❌ {name}: PHP errors in HTML")
            return False
        return True
    except Exception as e:
        print(f"❌ {name}: {e}")
        return False

# Login
fd = urllib.parse.urlencode({'username':'ak_v3','password':'test123'}).encode()
try:
    opener.open(urllib.request.Request("https://artinkashefi.ir/fitness_pro/api/auth.php?action=login", data=fd, method='POST'), timeout=15).read()
    print("✅ Login OK")
except Exception as e:
    print(f"❌ Login failed: {e}")
    sys.exit(1)

# 1. Dashboard API
ok1 = check_json("Dashboard API", "https://artinkashefi.ir/fitness_pro/api/dashboard.php")

# 2. AI Food Search
d = __import__('json').dumps({"message":"برنج","grams":200}).encode()
ok2 = check_json("AI Food Search", "https://artinkashefi.ir/fitness_pro/api/agent.php", data=d, method='POST')

# 3. AI Chat
d2 = __import__('json').dumps({"message":"سلام"}).encode()
ok3 = check_json("AI Chat", "https://artinkashefi.ir/fitness_pro/api/chat.php", data=d2, method='POST')

# 4. Pages (HTML)
ok4 = check_html("Dashboard Page", "https://artinkashefi.ir/fitness_pro/dashboard.php")
ok5 = check_html("Meals Page", "https://artinkashefi.ir/fitness_pro/meals.php")
ok6 = check_html("Groups Page", "https://artinkashefi.ir/fitness_pro/groups.php")

all_ok = all([ok1, ok2, ok3, ok4, ok5, ok6])
if all_ok:
    print("✅ AI سالم")
else:
    print("⚠️ مشکل:")

sys.exit(0 if all_ok else 1)