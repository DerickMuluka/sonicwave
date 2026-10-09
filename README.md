<div align="center">

# 🎵 SonicWave

**A modern, self-hosted music and media streaming platform**

Import from 9 platforms · AI-powered DJ · 10-band equalizer · Continuous playback · Full admin panel

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES2022-F7DF1E?logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![License](https://img.shields.io/badge/License-MIT-22c55e.svg)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-7c5cff.svg)](CONTRIBUTING.md)

</div>

---

## 📖 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Screenshots](#-screenshots)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [API Reference](#-api-reference)
- [Security](#-security)
- [Deployment](#-deployment)
- [Roadmap](#-roadmap)
- [Contributing](#-contributing)
- [License](#-license)
- [Acknowledgements](#-acknowledgements)

---

## 🌊 Overview

**SonicWave** is a modern, self-hosted streaming platform that brings together music, video, and podcasts from **9 different sources** into one beautifully unified player.

Unlike commercial streaming services, SonicWave runs entirely on your own server. You own your library, your data, and your privacy — while still being able to import tracks from YouTube, Spotify, SoundCloud, Vimeo, Apple Music, Deezer, Bandcamp, direct audio/video URLs, and your own uploads.

Built from scratch with **vanilla PHP, MySQL, and JavaScript** — no frameworks, no build steps, no dependencies to install. Drop it on any PHP host and you're running in under 5 minutes.

### Why SonicWave?

- **🎧 Universal import** — one place for all your music, no matter the source
- **🤖 AI DJ** — mood-aware queue generation that adapts to your taste
- **🎛️ Studio-grade equalizer** — 10-band EQ with 8 presets and bass/mid/treble control
- **♾️ Continuous playback** — music keeps playing as you navigate between pages
- **🔐 Secure by design** — CSRF protection, prepared statements, bcrypt hashing, audit logging
- **📱 Fully responsive** — one codebase, every screen size
- **🚀 Zero dependencies** — no npm, no composer, no build tools

---

## ✨ Features

### 🎵 Music & Media
- **Multi-platform import** — YouTube, Spotify, SoundCloud, Vimeo, Apple Music, Deezer, Bandcamp, direct URLs
- **File upload** — MP3, WAV, OGG, M4A, FLAC, AAC, WebM, Opus (up to 40 MB)
- **Audio + video playback** — native `<audio>`, `<video>`, and iframe embeds
- **Download support** — MP3, MP4, and WebM formats with quality selection
- **Media Hub** — dedicated viewer with resolution controls and fullscreen mode

### 🤖 AI Features
- **AI DJ** — six mood presets (Chill, Focus, Workout, Happy, Romantic, Late Night)
- **Smart scoring engine** — genre affinity, keyword matching, popularity, and user history
- **Personalised recommendations** — learns from your play history
- **Auto mood tagging** — every upload is analysed and tagged automatically
- **Generated descriptions** — every imported track gets an AI-written description

### 🎛️ Audio Controls
- **10-band equalizer** — 32 Hz to 16 kHz with live Web Audio API processing
- **Bass / Mid / Treble** — global tone shaping
- **8 presets** — Flat, Bass, Treble, Vocal, Rock, Jazz, EDM, Podcast
- **Persistent settings** — your EQ follows you across sessions

### 📚 Library Management
- **Personal library** — every user manages their own uploaded and imported tracks
- **Playlists** — create, edit, delete, add/remove tracks, public or private
- **Liked tracks** — one-tap favourite system
- **Listening history** — last 50 plays per user
- **Advanced search** — searches title, artist, album, and description
- **Genre filters** — Pop, Rock, Hip-Hop, Electronic, Jazz, and more

### 🎨 User Experience
- **Continuous playback** — music never stops when you navigate
- **Animated vinyl disc** — spins when playing, pauses when stopped
- **Ambient aurora background** — animated gradient orbs
- **Rotating hero images** — 5 curated backgrounds change every 60 seconds
- **Bento grid dashboard** — dense, modern layout
- **Horizontal navigation** — compact top nav bar with mobile scroll
- **Toast notifications** — non-blocking feedback for every action
- **Dark theme** — easy on the eyes, gradient accents

### 🔐 Security
- **CSRF protection** — tokens on every POST request
- **SQL injection prevention** — 100% prepared statements via PDO
- **XSS protection** — output escaping on every user-facing string
- **Bcrypt password hashing** — industry-standard cost factor 10
- **Session hardening** — regeneration on login, secure cookie flags
- **Rate limiting** — login attempt throttling
- **Ownership enforcement** — users can only modify their own tracks/playlists
- **Audit logging** — every sensitive action logged with actor, IP, and timestamp
- **File upload validation** — extension whitelist, MIME sniffing, size limits
- **Apache hardening** — `.htaccess` blocks direct access to sensitive folders

### 🛡️ Admin Panel
- **Dashboard metrics** — total users, tracks, plays, active today
- **User management** — view, promote, ban, unban
- **Content moderation** — review, approve, delete any track
- **Audit trail** — full history of administrative actions
- **Broadcast system** — announce maintenance or policy updates

### 🎨 UI Features
- **Fully responsive** — mobile, tablet, desktop
- **Fixed user chip** — always visible top-right
- **Password visibility toggles** — SVG eye icons, not emojis
- **Password change** — self-service via profile page
- **Rotating backgrounds** — curated, music-themed imagery
- **Respects `prefers-reduced-motion`** — animations disabled for users who prefer

---

## 📸 Screenshots

> Add screenshots to `/docs/screenshots/` and reference them here.

| Dashboard | Player | Equalizer |
|---|---|---|
| ![Dashboard](docs/screenshots/dashboard.png) | ![Player](docs/screenshots/player.png) | ![EQ](docs/screenshots/equalizer.png) |

| Library | Playlist | Admin |
|---|---|---|
| ![Library](docs/screenshots/library.png) | ![Playlist](docs/screenshots/playlist.png) | ![Admin](docs/screenshots/admin.png) |

| Login | Landing | Mobile |
|---|---|---|
| ![Login](docs/screenshots/login.png) | ![Landing](docs/screenshots/landing.png) | ![Mobile](docs/screenshots/mobile.png) |

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.2+ (vanilla, no framework) |
| **Database** | MySQL 8.0+ / MariaDB 10.5+ |
| **Frontend** | Vanilla JavaScript (ES2022), HTML5, CSS3 |
| **Audio** | Web Audio API (10-band equalizer) |
| **Fonts** | Inter (Google Fonts) |
| **Icons** | Inline SVG (no icon library) |
| **Auth** | PHP sessions + bcrypt |
| **Security** | CSRF tokens, PDO prepared statements, output escaping |

### Optional Binaries (for download/conversion features)

| Binary | Purpose | Required? |
|---|---|---|
| [`yt-dlp`](https://github.com/yt-dlp/yt-dlp) | Download from YouTube, Vimeo, SoundCloud, Bandcamp | Optional |
| [`ffmpeg`](https://ffmpeg.org/) | Audio extraction, format conversion | Optional |

**Note:** These binaries require shell execution. They work on VPS/dedicated servers but **not** on shared hosting (InfinityFree, Byet, etc.). The system auto-detects their availability and degrades gracefully.

---

## 📁 Project Structure
sonicwave/
├── .htaccess # Apache hardening
├── index.php # Landing (public) / Dashboard (logged in)
├── login.php # Sign in
├── register.php # Create account
├── logout.php # Session destroy
├── library.php # All tracks / mine / liked / playlists
├── upload.php # Upload + multi-platform import
├── playlist.php # Playlist detail with CRUD
├── search.php # Universal search
├── profile.php # Password + listening history
├── media.php # Media hub with embeds
├── admin.php # Admin panel
├── robots.txt # SEO
│
├── config/
│ ├── constants.php # App constants + feature flags
│ ├── config.php # Session + error handling
│ └── database.php # Environment-aware PDO connection
│
├── includes/
│ ├── init.php # Bootstrap loader
│ ├── auth.php # Authentication + role guards
│ ├── csrf.php # CSRF token helpers
│ ├── sanitize.php # Input cleaning
│ ├── functions.php # Shared utilities
│ ├── ai.php # Mood scoring + recommendations
│ └── media_tools.php # yt-dlp / ffmpeg wrappers
│
├── api/
│ ├── auth/ # login, register, logout
│ ├── tracks/ # list, stream, upload, download, like, play, delete
│ ├── playlists/ # list, create, add, remove, update, delete, tracks
│ ├── media/ # import, download, serve
│ ├── ai/ # recommend, dj
│ ├── user/ # password, history
│ ├── admin/ # stats, users, tracks
│ └── search/ # universal
│
├── templates/
│ ├── header.php # Meta + CSS links
│ ├── footer.php # JS script loading
│ ├── nav.php # Horizontal navigation
│ ├── topbar.php # Fixed user chip
│ └── player.php # Mini-player + EQ panel
│
├── assets/
│ ├── css/ # 10 stylesheets
│ │ ├── main.css
│ │ ├── auth.css
│ │ ├── landing.css
│ │ ├── dashboard.css
│ │ ├── player.css
│ │ ├── nav.css
│ │ ├── media.css
│ │ ├── admin.css
│ │ ├── profile.css
│ │ └── responsive.css
│ └── js/ # 12 JavaScript modules
│ ├── api.js # Fetch wrapper
│ ├── app.js # Helpers + sidebar
│ ├── player.js # Universal player
│ ├── audio-engine.js # Web Audio API
│ ├── equalizer.js # EQ panel
│ ├── auth.js # Login/register
│ ├── upload.js # Upload + import
│ ├── library.js # Library view
│ ├── playlist.js # Playlist CRUD
│ ├── search.js # Universal search
│ ├── profile.js # Profile tabs
│ ├── home.js # AI DJ + recs
│ ├── media.js # Media hub
│ ├── admin.js # Admin panel
│ └── backgrounds.js # Rotating hero
│
├── uploads/
│ ├── audio/ # User uploads
│ ├── covers/ # Album art
│ ├── video/ # Downloaded videos
│ ├── cache/ # Converted files
│ └── .htaccess # PHP engine off
│
├── bin/ # Optional binaries (not committed)
│ ├── yt-dlp.exe
│ ├── ffmpeg.exe
│ └── ffprobe.exe
│
└── database/
└── schema.sql # Full DB schema + admin seed


---

## 🚀 Installation

### Requirements

- **PHP** 8.2 or newer
- **MySQL** 8.0+ or **MariaDB** 10.5+
- **Apache** with `mod_rewrite` and `mod_headers`
- **PDO MySQL** extension enabled
- **Fileinfo** extension enabled (for MIME detection)

### Quick Start (Local with XAMPP)

#### 1. Clone the repository

```bash
cd C:\xampp\htdocs
git clone https://github.com/YOUR_USERNAME/sonicwave.git
cd sonicwave

2. Create the database
Open http://localhost/phpmyadmin

Click New → create database sonicwave with collation utf8mb4_unicode_ci

Select the database → click Import → choose database/schema.sql → Go

3. Configure the app
Edit config/constants.php:
define('BASE_URL', 'http://localhost/sonicwave');
Confirm config/database.php defaults match XAMPP (host 127.0.0.1, user root, empty password, db sonicwave). These are the defaults already.

4. Set folder permissions
Ensure these folders are writable by the web server:
uploads/audio/     → 755
uploads/covers/    → 755
uploads/video/     → 755
uploads/cache/     → 755

5. Create the admin account
Create setup-admin.php at root:
<?php
require_once __DIR__ . '/includes/init.php';
$hash = password_hash('Admin@2024', PASSWORD_BCRYPT);
db()->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'")->execute([$hash]);
echo "Admin password set to: Admin@2024";

Visit http://localhost/sonicwave/setup-admin.php once, then delete the file.

6. Login
URL: http://localhost/sonicwave/login.php

Email: admin@sonicwave.local

Password: Admin@2024

Change the password immediately via Profile → Security.

⚙️ Configuration
Environment-Aware Database
config/database.php auto-detects environment:

Local (localhost, 127.0.0.1, .local, .test): uses XAMPP defaults

Production: uses credentials from the else branch — update these

Feature Flags
config/constants.php exposes optional feature toggles:
// Auto-detected — no config needed if binaries exist
define('YTDLP_ENABLED',   is_file(YTDLP_BIN));
define('FFMPEG_ENABLED',  is_file(FFMPEG_BIN));

// Force disable on shared hosting:
define('YTDLP_ENABLED',   false);
define('FFMPEG_ENABLED',  false);

Optional Binaries
To enable downloads and conversion:

Download yt-dlp: https://github.com/yt-dlp/yt-dlp/releases/latest

Download ffmpeg: https://www.gyan.dev/ffmpeg/builds/

Place them in /bin/:

bin/yt-dlp.exe (Windows) or bin/yt-dlp (Linux/Mac)

bin/ffmpeg.exe / bin/ffmpeg

bin/ffprobe.exe / bin/ffprobe

Ensure they're executable (chmod +x bin/* on Linux)

🎮 Usage
First Steps
Register a new account or login as admin

Visit Upload / Import from the navigation

Either:

Upload an MP3 from your device, OR

Paste a link from YouTube, Spotify, SoundCloud, etc.

Your track appears in Library → My Uploads

Click any track to start playing

Playing Music
Click the play icon on any track card

The mini-player appears at the bottom

Navigate freely — music keeps playing across pages

Use the equalizer (slider icon) to tune the sound

Click the red X to stop playback entirely

Creating Playlists
Go to Library → Playlists

Click + New Playlist

Give it a name and description

Open the playlist → click Add tracks

Select tracks from your library → click + Add

AI DJ
From the dashboard:

Choose a mood: Chill, Focus, Workout, Happy, Romantic, or Late Night

The AI builds a personalised queue based on:

Your listening history

Track genres and moods

Community popularity

Title/artist keyword matching

Each mood produces a distinct queue. The more you listen, the better the recommendations become.

Admin Panel
Admins (role = 'admin') can access /admin.php:

Dashboard — platform metrics and health

Users — promote, ban, or unban accounts

Tracks — review and delete any content

Audit log — every admin action is recorded

🔌 API Reference
All endpoints return JSON. All POST requests require an X-CSRF-Token header.

Authentication
Method	Endpoint	Description
POST	/api/auth/register.php	Create a new account
POST	/api/auth/login.php	Sign in and start session
POST	/api/auth/logout.php	Destroy session
Tracks
Method	Endpoint	Description
GET	/api/tracks/list.php	List tracks (?genre=, ?q=, ?liked=1, ?mine=1)
GET	/api/tracks/stream.php?id=N	Stream audio/video with Range support
POST	/api/tracks/upload.php	Upload an audio file
POST	/api/tracks/like.php	Toggle like on a track
POST	/api/tracks/play.php	Log a play event
POST	/api/tracks/delete.php	Delete own track
Playlists
Method	Endpoint	Description
GET	/api/playlists/list.php	List user's playlists
POST	/api/playlists/create.php	Create new playlist
GET	/api/playlists/tracks.php?playlist_id=N	Get playlist tracks
POST	/api/playlists/add.php	Add track to playlist
POST	/api/playlists/remove.php	Remove track from playlist
POST	/api/playlists/update.php	Update playlist metadata
POST	/api/playlists/delete.php	Delete playlist
Media Import
Method	Endpoint	Description
POST	/api/media/import.php	Import from URL (YouTube, Spotify, etc.)
GET	/api/media/download.php?id=N&format=mp3	Download as MP3/MP4/WebM
AI
Method	Endpoint	Description
POST	/api/ai/dj.php	Build mood-based queue
GET	/api/ai/recommend.php	Get personalised recommendations
User
Method	Endpoint	Description
POST	/api/user/password.php	Change password
GET	/api/user/history.php	Get listening history
Admin
Method	Endpoint	Description
GET	/api/admin/stats.php	Platform metrics
GET/POST	/api/admin/users.php	Manage users
GET/POST	/api/admin/tracks.php	Manage all tracks
Search
Method	Endpoint	Description
GET	/api/search/universal.php?q=...	Search library + detect URLs
🔒 Security
SonicWave takes security seriously. Every layer is hardened:

What's Protected
Threat	Mitigation
CSRF	Per-session tokens on all POST requests, constant-time comparison
SQL Injection	100% PDO prepared statements, never string interpolation
XSS	htmlspecialchars() with ENT_QUOTES | ENT_SUBSTITUTE on all output
Session Fixation	session_regenerate_id(true) on login
Session Hijacking	HttpOnly, SameSite=Lax, Secure cookie flags
Brute Force	Login attempt throttling per session
File Upload Attacks	Extension whitelist + MIME sniff + size limits
Path Traversal	All file paths validated against ROOT_PATH
Unauthorized Access	Role-based guards on every admin endpoint
Ownership Violations	Every mutating endpoint verifies user_id = current_user.id
Recommended Server Config
HTTPS only — force via .htaccess redirect

Strong admin password — change from Admin@2024 immediately

Keep PHP updated — 8.2 or newer

Backup the database — weekly export via phpMyAdmin

Monitor audit log — check audit_log table for unusual activity

Reporting Vulnerabilities
If you discover a security issue, please email security@yourdomain.com rather than opening a public issue.

🌐 Deployment
Shared Hosting (InfinityFree, Byet.host, etc.)
Works: upload, playback, playlists, search, AI DJ, admin panel

Does NOT work: download, conversion (requires binaries + shell access)

Upload all files to htdocs/ via FTP

Create MySQL database in control panel

Import database/schema.sql via phpMyAdmin

Update config/constants.php → BASE_URL

Update config/database.php → production credentials

Set uploads/* folders to 755

Run setup-admin.php once, then delete

⚠️ Known issue: Free subdomains (.great-site.net, .byethost.com, etc.) may trigger Google Safe Browsing warnings due to shared-domain reputation. Solution: register a custom domain (~$10/year) and point it to your host.

VPS / Dedicated Server
Full feature set including downloads and conversion:

Install yt-dlp and ffmpeg to /bin/

Set permissions: chmod +x bin/*

Enable in config/constants.php:

php
define('YTDLP_ENABLED', true);
define('FFMPEG_ENABLED', true);
Docker (Community)
bash
docker run -d \
  -p 80:80 \
  -v $(pwd):/var/www/html \
  -e DB_HOST=db \
  -e DB_NAME=sonicwave \
  -e DB_USER=root \
  -e DB_PASS=secret \
  php:8.2-apache
🗺️ Roadmap
v3.4 (In Progress)
□ Lyrics support with timed highlighting
□ Podcast RSS feed support
□ Collaborative playlists
□ Social sharing (share playlist via link)
v3.5 (Planned)
□ Progressive Web App (PWA) with offline mode
□ Push notifications for new releases
□ User-to-user following
□ Comments on tracks
v4.0 (Long-term)
□ Native mobile apps (React Native)
□ Desktop app (Electron)
□ Chromecast / AirPlay support
□ Real-time collaborative listening rooms
🤝 Contributing
Contributions are welcome! Please follow these guidelines:

Fork the repository

Create a branch — git checkout -b feature/amazing-feature

Follow the code style — vanilla PHP, no frameworks, no build tools

Test on both XAMPP and shared hosting if possible

Commit — git commit -m "feat: add amazing feature"

Push — git push origin feature/amazing-feature

Open a Pull Request

Commit Convention
This project uses Conventional Commits:

feat: — new feature

fix: — bug fix

chore: — maintenance

style: — formatting

refactor: — code restructure

docs: — documentation

test: — test additions

📄 License
This project is licensed under the MIT License — see the LICENSE file for details.

text
MIT License

Copyright (c) 2025 SonicWave Contributors

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
🙏 Acknowledgements
yt-dlp — universal media downloader

FFmpeg — audio and video conversion

Inter font — beautiful typography

Unsplash — curated hero imagery

Shields.io — README badges

The open-source community for decades of inspiration

💬 Contact
Issues: GitHub Issues

Discussions: GitHub Discussions

Email: support@yourdomain.com

<div align="center">
Built with ❤️ for people who love music

⭐ Star this repo if you find it useful ⭐

</div> ```
📝 Git Commit Message for README
Use this exact message when committing the README (and the description/topics don't need a commit — they're set via GitHub UI):

bash
git add README.md
git commit -m "docs: add comprehensive README

- Project overview and rationale
- Feature list organised by category
- Complete tech stack and requirements
- Project structure tree
- Installation guide for XAMPP and shared hosting
- Configuration and feature flags
- Usage walkthrough for users and admins
- Full API reference with all endpoints
- Security documentation and threat model
- Deployment guides (shared hosting, VPS, Docker)
- Roadmap with v3.4, v3.5, v4.0 milestones
- Contributing guidelines and commit convention
- MIT license text
- Acknowledgements and contact info"