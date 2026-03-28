# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A CodeIgniter 4 PHP web application hosting a collection of utility tools (QR codes, image conversion/compression, PDF manipulation, geo/map tools, OCR). Uses CodeIgniter Shield for authentication, Twig for templating, and integrates Sentry for error tracking.

## Common Commands

```bash
# Development
make dev                          # Install deps + start dev server (ENVIRONMENT=testing, php spark serve)
make install_dev                  # Install all dependencies including dev

# Testing
make test                         # Run all tests with coverage
make test_fail_fast               # Stop at first failure
make test_group GROUP=GroupName   # Run specific test group

# Code Quality
make fix                          # Auto-fix: runs Rector, then PHP-CS-Fixer, then PHPStan
make lint                         # PHPStan static analysis (level 8) on app/

# Database
make db_migrate                   # Run migrations + seed StatsSeeder

# CI
make ci                           # Full pipeline: install_dev + fix + lint
```

## Architecture

**MVC Structure (CodeIgniter 4):**
- `app/Controllers/` — One controller per tool (e.g., `ToolQrCodes`, `ToolImageCompressor`, `ToolPdfConvertor`, `ToolGeo`)
- `app/Views/tools/` — Corresponding Twig/PHP templates per tool
- `app/Models/` — `KvStore` (key-value), `LwnSubscriber`, `FeedItem`
- `app/Config/Routes.php` — All route definitions; tools live under `/tool/*` and API endpoints under `/qr/v1`, `/barcode/v1`
- `app/Services/EmailService.php` — Email via PHPMailer/Zeptomail SMTP
- `app/Helpers/` — `app_helper.php`, `image_helper.php`, `view_helper.php`, `Logger.php`

**Authentication:** CodeIgniter Shield handles login, register, magic links, and 2FA. Shield views are in `app/Views/Shield/`. Protected routes require authentication via filters.

**File Processing Flow:** Uploaded files go to `writable/uploads/`, processed output to `writable/converted/`, served via `Downloader` controller at `/download/(:any)`.

**Cells:** `app/Cells/DownloadFileCell.php` is a reusable view component for file download buttons.

**Key Libraries:**
- `chillerlan/php-qrcode` — QR codes
- `picqer/php-barcode-generator` — Barcodes
- `dompdf/dompdf` — PDF generation
- `sibyx/phpgpx` — GPX file parsing (geo tool)
- `dg/rss-php` — RSS feed parsing (LWN subscription tool)

## Code Quality Standards

- **PHPStan level 8** — strict; baseline tracked in `phpstan-baseline.neon`
- **PHP-CS-Fixer** — Symfony + PSR-12 style (configured in `.php-cs-fixer.php`)
- **Rector** — auto-refactoring rules (configured in `rector.php`)
- Always run `make fix` before `make lint`; `fix` runs both formatters then PHPStan

## Testing

- Tests live in `tests/` with subdirectories: `unit/`, `session/`, `database/`
- PHPUnit config: `phpunit.xml.dist`; coverage excludes `Views/` and `Routes.php`
- Database tests use a real test database (not mocked)
- Coverage reports output to `build/logs/`

## Environment

- Copy `.env.example` to `.env` for local setup; production secrets are in `.env.gpg`
- PHP 8.3+ required; extensions: `mbstring`, `intl`, `imagick`, `gd`, `zip`, `xdebug`
- Public web root is `public/index.php` (front controller)
- `writable/` must be writable by the web server
