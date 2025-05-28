# Product Card Component Design (`item.blade.php`)

## 1. Purpose

This document outlines the design and implementation details for the enhanced product card component used to display individual products in catalogue listings (e.g., homepage, search results). The goal is to provide a richer, more informative, and visually appealing presentation of products.

## 2. File Location

The Blade component is located at:
`resources/views/components/catalogue/item.blade.php`

## 3. Key Features & Enhancements

The product card incorporates several features to enhance user experience:

### 3.1. Card Layout
- **Size:** The card width has been increased to `w-72` (18rem) to accommodate more information comfortably.
- **Structure:** Uses DaisyUI `card` component with `bg-base-200`.
- **Interactivity:** Includes `hover:shadow-xl` and `active:shadow-md` for visual feedback. A `group` class is used for parent-level hover effects on child elements.

### 3.2. Product Image
- **Display:** Occupies the top portion of the card with a fixed height of `h-48` (12rem) and `object-cover` to ensure consistent sizing and aspect ratio. A `bg-base-300` is used as a placeholder background.
- **Source:** Uses the `$product->main_image_url` accessor from the `Product` model, which intelligently selects the best available image (thumbnail or primary image).
- **Hover Effect:** The image scales slightly (`group-hover:scale-105`) on card hover.

### 3.3. "NEW" Badge
- **Logic:** Displayed if the product's `created_at` timestamp is within the last 7 days (`$product->created_at->gt(now()->subDays(7))`).
- **Styling:** A `badge badge-accent badge-sm` positioned at the top-right of the image.

### 3.4. Stock Indicators
- **"Out of Stock":**
    - **Logic:** Displayed if `$product->stock_quantity <= 0`.
    - **Styling:** A `badge badge-error badge-outline` centered at the bottom of the image.
- **"Low Stock":**
    - **Logic:** Displayed if `0 < $product->stock_quantity <= 10`.
    - **Styling:** A `badge badge-warning badge-outline badge-sm` at the bottom-right of the image.

### 3.5. Product Category
- **Display:** Shows the category name (`$product->category->name`).
- **Icon:** Uses `<x-icon.tag />` (Font Awesome `tag` icon).
- **Styling:** `text-xs text-base-content/70`, icon colored with `text-primary`.

### 3.6. Product Name
- **Display:** Shows `$product->name`.
- **Styling:** `card-title text-md font-semibold leading-snug`, with a `hover:text-primary` effect.

### 3.7. Product Price
- **Display:** Shows `RM {{ number_format($product->price, 2) }}`.
- **Styling:** `text-primary font-bold text-lg`.

### 3.8. "Amount Sold" Display
- **Logic:** Calculates the sum of `quantity` from `orderItems` where the associated `order` has `status = 'completed'`.
    ```php
    $fulfilledQuantity = $product->orderItems()
        ->whereHas('order', function ($query) {
            $query->where('status', 'completed');
        })
        ->sum('quantity');
    ```
- **Display:** Shown if `$fulfilledQuantity > 0`. Consists of the count and the word "sold".
- **Icon:** Uses `<x-icon.star />` (Font Awesome `star` icon).
- **Styling:** `font-semibold text-sm` for the count, `text-xs` for "sold", star icon `text-yellow-500`.
- **Performance Note:** This calculation is currently done per-product in the Blade component. For pages with many products, this might lead to N+1 query issues and should be considered for future optimization (e.g., eager loading counts in the controller).

### 3.9. Short Description
- **Display:** Shows a truncated version of `$product->description` using `Str::limit($product->description, 70)`.
- **Styling:** `text-sm text-base-content/80`, with a fixed height `h-10` to maintain layout consistency.

### 3.10. Seller Information
- **Display:** Shows the seller's business name (`$product->seller->business_name`), truncated if too long.
- **Icon:** Uses `<x-icon.store />` (Font Awesome `store` icon).
- **Styling:** `text-xs text-base-content/70`, separated by a `border-t`.

## 4. Data Sources (from `Product` Model)
- `name`
- `description`
- `price`
- `stock_quantity`
- `created_at`
- `main_image_url` (accessor)
- `category` (relationship to `ProductCategory`)
- `seller` (relationship to `Seller`)
- `orderItems` (relationship used for "Amount Sold" calculation)

## 5. Iconography (Font Awesome via Blade Components)
- `<x-icon.tag />`: For product category.
- `<x-icon.store />`: For seller/business name.
- `<x-icon.star />`: For "Amount Sold" indicator.

## 6. Styling
The component is styled using Tailwind CSS utility classes and DaisyUI component classes.

## 7. Blade Component Code

```blade
@use('Illuminate\Support\Str')
@use('Illuminate\Support\Facades\Storage')
@props([
    'product'
])

<a class="card bg-base-200 w-72 hover:shadow-xl active:shadow-md transition-all duration-300 group"
   href="{{ route('catalogue.show', compact('product')) }}">
    <figure class="relative h-48 bg-base-300">
        {{-- "New" Badge --}}
        @if($product->created_at->gt(now()->subDays(7)))
            <div class="badge badge-accent badge-sm absolute top-2 right-2 z-10 font-semibold">NEW</div>
        @endif

        <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-in-out">

        {{-- Stock Indicator (Overlay on image or below) --}}
        @if($product->stock_quantity <= 0)
            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 z-10">
                <span class="badge badge-error badge-outline font-medium">Out of Stock</span>
            </div>
        @elseif($product->stock_quantity > 0 && $product->stock_quantity <= 10)
            <div class="absolute bottom-2 right-2 z-10">
                <span class="badge badge-warning badge-outline badge-sm font-medium">Low Stock</span>
            </div>
        @endif
    </figure>

    <div class="card-body p-4 flex flex-col justify-between">
        <div>
            {{-- Category --}}
            @if($product->category)
                <div class="text-xs text-base-content/70 mb-1.5 flex items-center">
                    <x-icon.tag class="w-3 h-3 mr-1.5 text-primary"/>
                    <span>{{ $product->category->name }}</span>
                </div>
            @endif

            <h2 class="card-title text-md font-semibold mb-1 leading-snug hover:text-primary transition-colors">
                {{ $product->name }}
            </h2>

            <div class="text-primary font-bold text-lg mb-2">
                RM {{ number_format($product->price, 2) }}
            </div>

            {{-- Fulfilled Orders Count - NEW SECTION --}}
            @php
                $fulfilledQuantity = 0;
                // Ensure the product model has the orderItems relationship defined
                // and that orderItems has an 'order' relationship.
                if (method_exists($product, 'orderItems')) {
                    $fulfilledQuantity = $product->orderItems()
                        ->whereHas('order', function ($query) {
                            $query->where('status', 'completed'); // Assuming 'completed' is the status for fulfilled orders
                        })
                        ->sum('quantity');
                }
            @endphp

            @if($fulfilledQuantity > 0)
            <div class="mb-3 flex items-center gap-1.5">
                <x-icon.star class="w-4 h-4 text-yellow-500"/>
                <span class="font-semibold text-sm text-base-content/90">{{ $fulfilledQuantity }}</span>
                <span class="text-xs text-base-content/70">sold</span>
            </div>
            @endif
            {{-- END NEW SECTION --}}

            {{-- Short Description --}}
            <p class="text-sm text-base-content/80 mb-3 h-10 overflow-hidden">
                {{ Str::limit($product->description, 70) }}
            </p>
        </div>

        <div class="mt-auto pt-2 border-t border-base-300">
            <div class="flex gap-2 text-xs items-center text-base-content/70">
                <x-icon.store class="w-3.5 h-3.5 shrink-0 text-base-content/80"/>
                <span class="truncate" title="{{ $product->seller->business_name }}">
                    {{ $product->seller->business_name }}
                </span>
            </div>
        </div>
    </div>
</a>
