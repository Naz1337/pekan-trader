# Search Result Page Design (`search-result.blade.php`)

## 1. Purpose

This document outlines the design and implementation details for the enhanced product search result list items in `resources/views/catalogue/search-result.blade.php`. The objective was to transform the minimalist list items into richer, more informative, and visually appealing entries, drawing inspiration from the `product_card_component_design.md` while maintaining a horizontal list-item layout.

## 2. File Location

The Blade view is located at:
`resources/views/catalogue/search-result.blade.php`

## 3. Key Features & Enhancements

The product search result list items incorporate several features to enhance user experience:

### 3.1. Overall Structure Refinement

*   The main `<a>` tag within each `<li>` was restructured to use a more flexible `flex` layout.
*   Styling `bg-base-200 p-4 rounded-xl shadow-md hover:bg-base-300` was retained for the overall list item appearance.

### 3.2. Enhanced Product Image Section

*   **Image Source:** Updated `img src="{{ Storage::url($product->image_path) }}"` to `img src="{{ $product->main_image_url }}"` for consistency with the product card component's intelligent image selection.
*   **Dimensions & Styling:** Maintained `w-24 h-24 object-cover rounded-lg` for the image, wrapped in a `div` for relative positioning of badges.
*   **"NEW" Badge:** Implemented the "NEW" badge logic and styling (`badge badge-accent badge-sm absolute top-1 right-1 z-10 font-semibold`), displayed if `$product->created_at->gt(now()->subDays(7))`.
*   **Stock Indicators:**
    *   **"Out of Stock":** Added an overlay badge (`badge badge-error badge-outline`) centered at the bottom of the image if `$product->stock_quantity <= 0`.
    *   **"Low Stock":** Added an overlay badge (`badge badge-warning badge-outline badge-sm`) at the bottom-right of the image if `0 < $product->stock_quantity <= 10`.

### 3.3. Main Product Details Section

*   This section contains the primary product information, arranged vertically.
*   **Product Category:** Introduced a new line displaying the product category with an icon:
    ```blade
    @if($product->category)
        <div class="text-xs text-base-content/70 mb-1.5 flex items-center">
            <x-icon.tag class="w-3 h-3 mr-1.5 text-primary"/>
            <span>{{ $product->category->name }}</span>
        </div>
    @endif
    ```
*   **Product Name:** Retained `h3 class="text-xl font-semibold text-base-content"`, with added `leading-snug` and `hover:text-primary` for consistency.
*   **Product Price:** Retained `p class="text-lg text-primary font-medium mt-1">RM {{ number_format($product->price, 2) }}</p>`.

### 3.4. Secondary Details Section

*   This section was added below the main product details.
*   **Short Description:** Added a truncated description of the product:
    ```blade
    <p class="text-sm text-base-content/80 mb-3 h-10 overflow-hidden">
        {{ Str::limit($product->description, 70) }}
    </p>
    ```
*   **Seller Information:** Included the seller's business name with an icon:
    ```blade
    @if($product->seller)
        <div class="mt-auto pt-2 border-t border-base-300">
            <div class="flex gap-2 text-xs items-center text-base-content/70">
                <x-icon.store class="w-3.5 h-3.5 shrink-0 text-base-content/80"/>
                <span class="truncate" title="{{ $product->seller->business_name }}">
                    {{ $product->seller->business_name }}
                </span>
            </div>
        </div>
    @endif
    ```

### 3.5. "Amount Sold" Display

*   The existing `$fulfilledQuantity` calculation was retained.
*   The display was enhanced with the star icon and styling similar to the product card:
    ```blade
    @if($fulfilledQuantity > 0)
        <div class="flex-shrink-0 text-right ml-6 min-w-[60px]">
            <div class="flex flex-col items-end">
                <div class="flex items-center gap-1.5">
                    <x-icon.star class="w-4 h-4 text-yellow-500"/>
                    <span class="font-semibold text-sm text-base-content/90">{{ $fulfilledQuantity }}</span>
                </div>
                <span class="text-xs text-base-content/70">sold</span>
            </div>
        </div>
    @endif
    ```

## 4. Styling

The component is styled using Tailwind CSS utility classes and DaisyUI component classes, adapted for a list item view.

## 5. Iconography (Font Awesome via Blade Components)

*   `<x-icon.tag />`: For product category.
*   `<x-icon.store />`: For seller/business name.
*   `<x-icon.star />`: For "Amount Sold" indicator.
