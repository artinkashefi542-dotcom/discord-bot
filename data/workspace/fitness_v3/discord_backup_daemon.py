#!/usr/bin/env python3
"""
Discord Bot Backup - Every 6 hours
Backs up bot code + database + dependencies
Uploads to: artinkashefi.ir/fitness_pro/discord_backup/
"""
import subprocess
import urllib.request
import urllib.parse
import http.cookiejar
import ssl
import json
import os
import time
from datetime import datetime

BOT_DIR = "/data/workspace/fitness_v3"
CPCPANEL_URL = "https://2450773184.cloudylink.com:2083"
CPCPANEL_USER = "h409204"
CPCPANEL_PASS = "Pocox6artinak060/"
UPLOAD_DIR = "/public_html/fitness_pro/discord_backup"
INTERVAL = 6 * 60 * 60  # 6 hours

def backup():
    try:
        # Generate filename with date and time
        now = datetime.now()
        timestamp = now.strftime("%Y-%m-%d_%H-%M")
        filename = f"discord_backup_{timestamp}.tar.gz"
        filepath = f"/tmp/{filename}"
        
        # Create backup with all bot files
        result = subprocess.run(
            ["tar", "czf", filepath, 
             "discord_bot.py", 
             "punishments.db",
             "backup_daemon.py"],
            cwd=BOT_DIR, 
            capture_output=True, text=True
        )
        
        if result.returncode != 0:
            print(f"[{now}] Backup create failed: {result.stderr}")
            return False
        
        # Get file size
        size = os.path.getsize(filepath)
        print(f"[{now}] Backup created: {filename} ({size} bytes)")
        
        # cPanel login
        ctx = ssl.create_default_context()
        ctx.check_hostname = False
        ctx.verify_mode = ssl.CERT_NONE
        
        cj = http.cookiejar.CookieJar()
        opener = urllib.request.build_opener(
            urllib.request.HTTPCookieProcessor(cj),
            urllib.request.HTTPSHandler(context=ctx)
        )
        
        req = urllib.request.Request(CPCPANEL_URL)
        req.add_header('User-Agent', 'Mozilla/5.0')
        opener.open(req, timeout=15).read()
        
        ld = urllib.parse.urlencode({
            'user': CPCPANEL_USER,
            'pass': CPCPANEL_PASS,
            'login_only': '1'
        }).encode()
        
        r2 = urllib.request.Request(
            f"{CPCPANEL_URL}/login/?login_only=1",
            data=ld, method='POST'
        )
        r2.add_header('User-Agent', 'Mozilla/5.0')
        r2.add_header('Content-Type', 'application/x-www-form-urlencoded')
        token = json.loads(opener.open(r2, timeout=15).read())['security_token']
        
        # Upload file
        with open(filepath, 'rb') as fh:
            d = fh.read()
        
        b = '----BAK'
        body = (
            f'--{b}\r\n'
            f'Content-Disposition: form-data; name="dir"\r\n\r\n{UPLOAD_DIR}\r\n'
            f'--{b}\r\n'
            f'Content-Disposition: form-data; name="overwrite"\r\n\r\n1\r\n'
            f'--{b}\r\n'
            f'Content-Disposition: form-data; name="file-1"; filename="{filename}"\r\n'
            f'Content-Type: application/gzip\r\n\r\n'
        ).encode() + d + f'\r\n--{b}--\r\n'.encode()
        
        rq = urllib.request.Request(
            f"{CPCPANEL_URL}{token}/execute/Fileman/upload_files",
            data=body, method='POST'
        )
        rq.add_header('User-Agent', 'Mozilla/5.0')
        rq.add_header('Content-Type', f'multipart/form-data; boundary={b}')
        opener.open(rq, timeout=30).read()
        
        # Clean up temp file
        os.remove(filepath)
        
        print(f"[{now}] ✅ Backup uploaded: {filename}")
        return True
        
    except Exception as e:
        print(f"[{datetime.now()}] ❌ Backup failed: {e}")
        return False

if __name__ == "__main__":
    print("🔄 Discord Backup Daemon started")
    print(f"📁 Backups: https://artinkashefi.ir/fitness_pro/discord_backup/")
    print(f"⏰ Interval: {INTERVAL // 3600} hours")
    
    # Take first backup immediately
    backup()
    
    # Then loop
    while True:
        time.sleep(INTERVAL)
        backup()