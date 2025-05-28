# Sellers Table Structure and Profile View Modifications

This document details the structure of the `sellers` table and the modifications made to the `resources/views/seller/profile/show.blade.php` view to correctly reference the table's columns.

## Sellers Table Structure

The `sellers` table was created via the `2025_05_03_021120_create_sellers_table.php` migration. Its structure is as follows:

| Column Name        | Data Type | Nullable | Description                               |
| :----------------- | :-------- | :------- | :---------------------------------------- |
| `id`               | `bigint`  | No       | Primary Key (auto-incrementing)           |
| `business_name`    | `string`  | No       | Name of the seller's business/shop        |
| `business_description` | `string`  | No       | Description of the seller's business      |
| `business_address` | `string`  | No       | Physical address of the business          |
| `business_phone`   | `string`  | No       | Contact phone number for the business     |
| `business_email`   | `string`  | No       | Contact email for the business            |
| `logo_url`         | `string`  | No       | URL or path to the business logo          |
| `opening_hour`     | `string`  | No       | Business opening hour                     |
| `closing_hour`     | `string`  | No       | Business closing hour                     |
| `facebook`         | `string`  | Yes      | Facebook profile URL (optional)           |
| `instagram`        | `string`  | Yes      | Instagram profile URL (optional)          |
| `ic_number`        | `string`  | No       | Identity Card number (for verification)   |
| `business_cert_url`| `string`  | No       | URL or path to the business certificate   |
| `bank_name`        | `string`  | No       | Name of the bank                          |
| `bank_account_name`| `string`  | No       | Name on the bank account                  |
| `bank_account_number`| `string`  | No       | Bank account number                       |
| `user_id`          | `bigint`  | No       | Foreign key to the `users` table          |
| `created_at`       | `timestamp`| No       | Timestamp of creation                     |
| `updated_at`       | `timestamp`| No       | Timestamp of last update                  |

## Profile View (`resources/views/seller/profile/show.blade.php`) Modifications

The `resources/views/seller/profile/show.blade.php` file was modified to correctly reference the `business_name` and `business_description` columns from the `sellers` table. Previously, it incorrectly used `shop_name` and `description`.

**Changes Made:**

1.  **Seller/Shop Name Reference:**
    *   **Before:**
        ```blade
        <x-layout.main :title="$seller->shop_name ?? $seller->user->name">
        ...
        <img src="{{ $seller->profile_image_url }}" alt="{{ $seller->shop_name ?? $seller->user->name }} Profile" class="w-32 h-32 rounded-full object-cover border-4 border-primary">
        ...
        <h1 class="text-4xl font-bold text-base-content">{{ $seller->shop_name ?? $seller->user->name }}</h1>
        ...
        @if ($seller->shop_name)
        ...
        <h2 class="text-3xl font-bold text-base-content mb-6">Products by {{ $seller->shop_name ?? $seller->user->name }}</h2>
        ```
    *   **After:**
        ```blade
        <x-layout.main :title="$seller->business_name ?? $seller->user->name">
        ...
        <img src="{{ $seller->profile_image_url }}" alt="{{ $seller->business_name ?? $seller->user->name }} Profile" class="w-32 h-32 rounded-full object-cover border-4 border-primary">
        ...
        <h1 class="text-4xl font-bold text-base-content">{{ $seller->business_name ?? $seller->user->name }}</h1>
        ...
        @if ($seller->business_name)
        ...
        <h2 class="text-3xl font-bold text-base-content mb-6">Products by {{ $seller->business_name ?? $seller->user->name }}</h2>
        ```

2.  **Seller Description Reference:**
    *   **Before:**
        ```blade
        <p class="text-base-content/60 mt-2">{{ $seller->description ?? 'No description provided.' }}</p>
        ```
    *   **After:**
        ```blade
        <p class="text-base-content/60 mt-2">{{ $seller->business_description ?? 'No description provided.' }}</p>
        ```

## Model Accessor (`app/Models/Seller.php`)

The `getProfileImageUrlAttribute()` accessor in `app/Models/Seller.php` correctly uses the `logo_url` column, which aligns with the `sellers` table structure. Therefore, no modifications were required for this accessor.
