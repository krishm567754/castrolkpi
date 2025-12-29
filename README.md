# Castrol KPI Dashboard Prototype

This repository now includes a PHP prototype of the ERP/CRM-style dashboard that ingests Excel (.xlsx) data (converted to JSON)
and enforces role-based access with sales executive scopes.

## Run locally
- Requires PHP 8+.
- Start the built-in server from the repo root:
  - Easiest: `./serve.sh` (binds to 0.0.0.0 with doc root set to the repo).
  - Manual: `php -S localhost:8000` (or `php -S 0.0.0.0:8000 -t .`).
- Visit http://localhost:8000 and log in with credentials from `users.json` (default: admin/admin).

## Remote preview (when `localhost` is not reachable)
- Use `./serve.sh` (or `php -S 0.0.0.0:8000 -t .`) to bind the PHP server on all interfaces.
- Forward the port from your host/IDE (examples):
  - SSH tunnel: `ssh -L 8000:127.0.0.1:8000 <user>@<remote>` and open http://localhost:8000 locally.
  - GitHub Codespaces: start the server, then open the forwarded port labeled 8000 (creates a URL like `https://<codespace>-8000.app.github.dev`).
  - VS Code Remote/Dev Containers: start the server, then use the Ports panel to open/forward port 8000.
- Once forwarded, browse to the forwarded URL and log in with `users.json` credentials (default: admin/admin).

## Shareable preview URL (works on any device)
- Install [cloudflared](https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/install-and-setup/installation/).
- Run `./tunnel.sh` (or `PORT=9000 ./tunnel.sh` for a different port).
  - The script starts the PHP server and opens a Cloudflare Tunnel, printing a public `https://...trycloudflare.com` URL.
  - Send that URL to anyone (mobile/desktop) to view the dashboard without extra forwarding.
- Alternatively, if you already started the server (via `./serve.sh`), you can run `cloudflared tunnel --url http://localhost:8000` to generate a one-off shareable link.

## Data files
Upload processing is still a placeholder, but JSON files drive all views:
- `data/invoices_current.json` – current year invoices (filtered by allowed sales execs).
- `data/invoices_history.json` – historical invoices (for 2-year search).
- `data/open_orders.json` – open orders (last 3 days table).
- `data/stock.json` – stock items with calculated liters.
- `data/customers.json` – customer master data for Dealer 360 cards.

You can edit `users.json` and `sales_execs.json` to manage credentials and scope options. Admins can also add/update users from
`admin_users.php`.

## Excel upload handoff
When ready to wire uploads, map these workbooks to JSON:
- `invoice_current.xlsx` → `invoices_current.json`
- `q1.xlsx` … `q7.xlsx` → `invoices_history.json`
- `open_orders.xlsx` → `open_orders.json`
- `stock.xlsx` → `stock.json`
- `customers.xlsx` → `customers.json`

## Next implementation steps
- Replace `admin_data.php` placeholder with real Excel upload + conversion handlers.
- Add charts (volume by sales exec, weekly sales, brand counts, top customers) to `dashboard.php`.
- Extend Dealer 360 with richer customer fields and invoice history context.
