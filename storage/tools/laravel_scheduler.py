import subprocess
import time
import os
import sys
import json
import traceback
from datetime import datetime, timezone

# ⚙️ KONFIGURASI PATH (Sesuaikan jika struktur folder berbeda)
LARAVEL_ROOT = r"D:\laragon\www\rhinedottir"
PHP_BIN      = r"D:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe"

# Path otomatis ke storage Laravel
STORAGE_DIR  = os.path.join(LARAVEL_ROOT, "storage")
HEARTBEAT    = os.path.join(STORAGE_DIR, "scheduler_heartbeat.json")
LOCK_FILE    = os.path.join(STORAGE_DIR, "scheduler.lock")
LOG_FILE     = os.path.join(STORAGE_DIR, "logs", "scheduler.log")

# Pastikan direktori log ada
os.makedirs(os.path.dirname(LOG_FILE), exist_ok=True)

# Mapping interval Laravel Scheduler ke detik
INTERVAL_MAP = {
    'everyMinute': 60,
    'everyFiveMinutes': 300,
    'everyTenMinutes': 600,
    'everyThirtyMinutes': 1800,
    'hourly': 3600,
    'daily': 86400,
}

def log(message, level="INFO"):
    """Logging murni ke file (aman untuk mode --noconsole)"""
    ts = datetime.now(timezone.utc).isoformat()
    line = f"[{ts}] [{level}] {message}"
    try:
        with open(LOG_FILE, "a", encoding="utf-8") as f:
            f.write(line + "\n")
    except Exception:
        pass

def load_env_vars(env_path):
    """Parser sederhana untuk membaca file .env Laravel"""
    env_vars = {}
    try:
        with open(env_path, 'r', encoding='utf-8') as f:
            for line in f:
                line = line.strip()
                if line and not line.startswith('#') and '=' in line:
                    key, value = line.split('=', 1)
                    env_vars[key.strip()] = value.strip().strip("'\"")
    except Exception:
        pass
    return env_vars

def get_db_interval(db_config):
    """Mengambil push_interval dari database secara dinamis"""
    try:
        import pymysql
        conn = pymysql.connect(
            host=db_config['host'],
            user=db_config['user'],
            password=db_config['password'],
            database=db_config['database'],
            port=db_config['port'],
            connect_timeout=5,
            cursorclass=pymysql.cursors.DictCursor
        )
        with conn.cursor() as cursor:
            cursor.execute("SELECT push_interval FROM api_settings LIMIT 1")
            row = cursor.fetchone()
        conn.close()
        
        if row and row.get('push_interval'):
            interval_str = row['push_interval']
            return INTERVAL_MAP.get(interval_str, 60)
        return 60  # Default fallback
    except Exception as e:
        log(f"Failed to read DB interval: {e}", "WARN")
        return 60  # Fallback aman agar script tidak crash

def update_heartbeat():
    """Update file heartbeat agar UI Laravel tahu scheduler masih hidup"""
    data = {"status": "running", "last_run": datetime.now(timezone.utc).isoformat(), "pid": os.getpid()}
    try:
        with open(HEARTBEAT, "w", encoding="utf-8") as f:
            json.dump(data, f)
    except Exception:
        pass

def check_lock():
    """Cegah duplikasi instance dengan validasi PID"""
    if os.path.exists(LOCK_FILE):
        try:
            with open(LOCK_FILE, "r") as f:
                old_pid = int(f.read().strip())
            os.kill(old_pid, 0)  # Cek apakah proses masih hidup
            log(f"Another instance running (PID: {old_pid}). Exiting.", "WARN")
            sys.exit(0)
        except (ValueError, OSError, ProcessLookupError):
            log("Stale lock found. Cleaning up...", "WARN")
            try: os.remove(LOCK_FILE)
            except: pass

def create_lock():
    """Buat lock file dengan PID saat ini"""
    os.makedirs(os.path.dirname(LOCK_FILE), exist_ok=True)
    with open(LOCK_FILE, "w") as f:
        f.write(str(os.getpid()))

def cleanup():
    """Hapus lock & heartbeat saat berhenti"""
    for f in [LOCK_FILE, HEARTBEAT]:
        try: os.remove(f)
        except: pass
    log("Scheduler stopped. Lock & heartbeat removed.", "INFO")

def run_schedule():
    """Jalankan php artisan schedule:run tanpa console window"""
    try:
        result = subprocess.run(
            [PHP_BIN, "artisan", "schedule:run"],
            cwd=LARAVEL_ROOT,
            capture_output=True,
            text=True,
            check=False,
            creationflags=subprocess.CREATE_NO_WINDOW  # Windows: prevent CMD popup
        )
        out = (result.stdout + "\n" + result.stderr).strip()
        if out and "No scheduled commands are ready to run" not in out:
            log(f"Schedule Output: {out}", "OUTPUT")
        else:
            log("No push tasks due at this time.", "INFO")
        update_heartbeat()
    except Exception as e:
        log(f"Schedule execution failed: {str(e)}", "ERROR")
        log(traceback.format_exc(), "DEBUG")

def main():
    log("Laravel Scheduler Runner started.", "INFO")
    check_lock()
    create_lock()
    import atexit
    atexit.register(cleanup)

    # Load konfigurasi DB otomatis dari .env Laravel
    env = load_env_vars(os.path.join(LARAVEL_ROOT, ".env"))
    db_config = {
        'host': env.get('DB_HOST', '127.0.0.1'),
        'user': env.get('DB_USERNAME', 'root'),
        'password': env.get('DB_PASSWORD', ''),
        'database': env.get('DB_DATABASE', 'rhinedottir'),
        'port': int(env.get('DB_PORT', 3306))
    }

    errors = 0
    try:
        while True:
            try:
                # ✅ Ambil interval terbaru dari database setiap siklus
                current_interval = get_db_interval(db_config)
                
                run_schedule()
                errors = 0
                log(f"Sleeping for {current_interval}s until next run.", "INFO")
                time.sleep(current_interval)
            except Exception as e:
                errors += 1
                log(f"Loop error ({errors}): {e}", "ERROR")
                if errors >= 5:
                    log("Too many errors. Waiting 30s before retry...", "CRITICAL")
                    time.sleep(30)
                    errors = 0
    except KeyboardInterrupt:
        log("Stopped by user.", "INFO")
    except Exception as e:
        log(f"Fatal crash: {e}", "CRITICAL")
        time.sleep(5)
        log("Auto-restarting...", "INFO")
        # Restart process secara aman
        os.execv(sys.executable, [sys.executable] + sys.argv)

if __name__ == "__main__":
    main()