#!/usr/bin/env python3
"""
Discord Bot Backup Daemon
Runs every 6 hours, backs up bot files to website
"""
import json
import subprocess
import urllib.request
import urllib.parse
import http.cookiejar
import ssl
import os
import time
from datetime import datetime

BACKUP_FILE = "/data/workspace/fitness_v3/discord_bot_backup.tar.gz"
BOT_DIR = "/data/workspace/fitness_v3"
CPCPANEL_URL = "https://2450773184.cloudylink.com:2083"
CPCPANEL_USER = "h409204"
CPCPANEL_PASS = "Pocox6artinak060/"
UPLOAD_DIR = "/public_html/fitness_pro/backups"
INTERVAL = 6 * 60 * 60  # 6 hours

def backup():
    try:
        # Create backup
        subprocess.run(
            ["tar", "czf", BACKUP_FILE, "discord_bot.py", "punishments.db"],
            cwd=BOT_DIR, check=True
        )
        
        # Get cPanel token
        ctx = ssl.create_default_context()
        ctx.check_hostname = False
        ctx.verify_mode = ssl.CERT_NONE
        
        cj = http.cookiejar.CookieJar()
        opener = urllib.request.build_opener(
            urllib.request.HTTPCookieProcessor(cj),
            urllib.request.HTTPSHandler(context=ctx)
        )
        
        # Login
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
        
        # Upload
        with open(BACKUP_FILE, 'rb') as fh:
            d = fh.read()
        
        b = '----BAK'
        body = (
            f'--{b}\r\n'
            f'Content-Disposition: form-data; name="dir"\r\n\r\n{UPLOAD_DIR}\r\n'
            f'--{b}\r\n'
            f'Content-Disposition: form-data; name="overwrite"\r\n\r\n1\r\n'
            f'--{b}\r\n'
            f'Content-Disposition: form-data; name="file-1"; filename="discord_bot_backup.tar.gz"\r\n'
            f'Content-Type: application/gzip\r\n\r\n'
        ).encode() + d + f'\r\n--{b}--\r\n'.encode()
        
        rq = urllib.request.Request(
            f"{CPCPANEL_URL}{token}/execute/Fileman/upload_files",
            data=body, method='POST'
        )
        rq.add_header('User-Agent', 'Mozilla/5.0')
        rq.add_header('Content-Type', f'multipart/form-data; boundary={b}')
        opener.open(rq, timeout=30).read()
        
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Backup done")
        return True
        
    except Exception as e:
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Backup failed: {e}")
        return False

if __name__ == "__main__":
    print("Backup daemon started")
    while True:
        backup()
        time.sleep(INTERVAL)