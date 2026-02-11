# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Baptist Bookroom (BLS) is an inventory and sales management system for a multi-branch bookstore. Built with **Laravel 10** and **Filament 3** admin panel. PHP 8.2+, MySQL, Vite + Tailwind CSS.

The entire UI is the Filament admin panel served at the root path (`/`). There are no separate frontend SPA views.

## Common Commands

```bash
# Serve locally (Laravel Herd is used for local dev)
php artisan serve

# Frontend assets
npm run dev          # Vite dev server
npm run build        # Production build

# Database
php artisan migrate
php artisan migrate:rollback

# Linting
./vendor/bin/pint    # Laravel Pint (PSR-12 style fixer)

# Tests
php artisan test
./vendor/bin/phpunit
./vendor/bin/phpunit --filter=TestName        # Single test
./vendor/bin/phpunit tests/Unit               # Unit suite only
./vendor/bin/phpunit tests/Feature            # Feature suite only

# Filament
php artisan filament:make-resource ModelName  # New resource
php artisan make:filament-page PageName       # New page

# Cache clearing (useful after config/route changes)
php artisan optimize:clear
```

## Architecture

### Data Flow: Stock → Sales

Items flow through a pipeline: **Item** → **MainStock** (central warehouse) → **StockDistribute** → **BranchStock** (per-branch inventory) → **Sale**.

- `Item` holds product metadata (name, category, subcategory)
- `MainStock` holds cost_price, mrp, batch, quantity, barcode for each item
- `StockDistribute` transfers quantity from MainStock to a Branch
- `BranchStock` tracks available quantity per branch
- `Sale` records individual line-item sales from BranchStock with GST calculation

### Key Model Relationships

Models use `staudenmeir/belongs-to-through` and `staudenmeir/eloquent-has-many-deep` for deep relationship traversal:
- `Sale` reaches `Item` through `BranchStock → MainStock → Item`
- `SalesCartItem` follows the same chain
- `BranchStock` reaches `Item` through `MainStock`

### Private Books Module

Separate workflow for books received from donors: `PrivateBook` → `PrivateBookAccount` (payments to donors) and `PrivateBookReturn` (returns). Each private book links to an Item and MainStock entry.

### Filament Admin Panel Structure

Panel configured in `app/Providers/Filament/AdminPanelProvider.php`. Uses top navigation with these groups:
- **Sales** — SaleResource (read-only, grouped by memo/invoice), SalesCart page
- **Stocks** — MainStockResource, BranchStockResource, StockDistributeResource, StockDistributeCarts page, AllBranchStock page
- **Private Books** — PrivateBookResource
- **Manage Items** — ItemResource, CategoryResource, SubCategoryResource
- **Assets** — AssetResource
- **Settings** — UserResource, RoleResource, PermissionResource, BranchResource, CustomerResource, SupplierResource, CreditTransactionResource

### Cart Pages (Livewire)

`SalesCart` and `StockDistributeCarts` are custom Filament Pages (not standard CRUD resources). They implement shopping-cart patterns with:
- Database transaction locking for concurrent updates
- Deadlock retry with exponential backoff (StockDistributeCarts)
- Real-time quantity validation against available stock

### Invoice/Receipt Generation

PDF generation via `barryvdh/laravel-dompdf` and `laraveldaily/laravel-invoices`. Controllers in `app/Http/Controllers/` handle receipt downloads. Routes are defined in `routes/web.php` — all are GET endpoints for PDF downloads.

Invoice number format: `BLS/YY-YY/BRANCH_CODE/MEMO_NO` (financial year based). Config tracked in `config/invoicenumber.php` and `config/saleinvoicenumbergenerator.php`.

### Authorization

Role-based access control with `Role`, `Permission` models and pivot tables (`role_user`, `permission_role`). Users are assigned to a Branch. Filament resources use permission checks for visibility.

### Exports

Excel exports via `pxlrbt/filament-excel`. Exporter classes in `app/Filament/Exports/` for Sales, Items, MainStock, BranchStock, PrivateBook, StockDistribute.

### Custom Table Columns

`app/Tables/Columns/TotalBookAmount.php` and `TotalBookSale.php` — custom Filament table columns used in PrivateBookResource.

### Enums

`app/Enums/Type.php` — used for payment type casting in SupplierFinancials.

## Database Notes

- Prices use `decimal(10,2)` — cost_price, mrp in MainStock, BranchStock, sales tables
- GST fields on Sale: `gst_rate`, `gst_amount`, `total_amount_with_gst`
- Sale payment modes: cash, UPI, bank transfer, cheque
- Branch has contact details (address, phone, email, contact_person)
