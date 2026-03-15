# DVWA Security Assessment (Demonstration-Only Remediation)

Target: https://github.com/westonbrown/DVWA  
Date: 2026-03-15

> This repository is intentionally vulnerable for training. Findings below are true positive security defects with exploitability details and secure remediation patterns.

## HIGH-01: OS Command Injection in command execution module
- **CWE**: CWE-78 (OS Command Injection)
- **File:Line**: `vulnerabilities/exec/source/low.php:5,10,14`
- **Why exploitable**: User-controlled `$_REQUEST['ip']` is concatenated into `shell_exec('ping ... ' . $target)` without strict validation.
- **PoC scenario**: Attacker submits `127.0.0.1; id` and executes arbitrary shell commands as the web server user.
- **Impact**: Remote code execution, host compromise, lateral movement.
- **Remediation path**:
  1. Accept only valid IP/domain values (`filter_var`, strict regex).
  2. Avoid shell invocation; use safe network libraries.
  3. If shell is unavoidable, use parameterized process APIs and `escapeshellarg` as defense-in-depth.

## HIGH-02: SQL Injection in SQLi module
- **CWE**: CWE-89 (SQL Injection)
- **File:Line**: `vulnerabilities/sqli/source/low.php:5,10,31`
- **Why exploitable**: `$_REQUEST['id']` is interpolated directly into SQL query strings.
- **PoC scenario**: Input `1' OR '1'='1` manipulates query logic and leaks arbitrary rows.
- **Impact**: Authentication bypass, sensitive data disclosure, potential data modification/deletion.
- **Remediation path**:
  1. Use prepared statements with bound parameters (`mysqli_prepare`/PDO).
  2. Enforce numeric typing for IDs before DB access.
  3. Remove raw DB error output to users.

## HIGH-03: Unrestricted File Upload leading to RCE
- **CWE**: CWE-434 (Unrestricted Upload of File with Dangerous Type)
- **File:Line**: `vulnerabilities/upload/source/low.php:6,9`
- **Why exploitable**: Uploaded filename is accepted and moved to web-accessible directory without MIME/extension/content validation.
- **PoC scenario**: Attacker uploads `shell.php` and executes it from `/hackable/uploads/shell.php`.
- **Impact**: Remote code execution, data theft, full application compromise.
- **Remediation path**:
  1. Allowlist file types and validate with server-side MIME/content checks.
  2. Store uploads outside web root and serve through controlled download handlers.
  3. Randomize filenames and strip executable permissions.

## HIGH-04: Weak Password Hashing (MD5) in authentication path
- **CWE**: CWE-327 (Broken/Risky Cryptographic Algorithm)
- **File:Line**: `login.php:27,39`
- **Why exploitable**: Passwords are hashed with MD5 and compared directly in SQL authentication logic.
- **PoC scenario**: Attacker with DB dump cracks MD5 hashes quickly via rainbow tables/GPU cracking.
- **Impact**: Account takeover and credential reuse compromise.
- **Remediation path**:
  1. Replace MD5 with `password_hash(..., PASSWORD_DEFAULT)` and `password_verify`.
  2. Migrate existing hashes with rehash-on-login strategy.
  3. Add login throttling and MFA where feasible.

## Fix Routing Decision
- No **CRITICAL** findings were confirmed during this pass.
- Per mission policy, critical safe-fix patching is therefore **not applicable** in this cycle.
- This assessment provides dedicated remediation paths for confirmed HIGH findings.
