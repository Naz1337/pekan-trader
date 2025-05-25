# Possible Order Payment Status Values

Based on the application's logic, including controllers and view files, the possible values for an order's `payment_status` are:

*   `unpaid`: The order has been placed but no payment has been initiated or received.
*   `in_payment`: A payment receipt has been uploaded by the customer and is awaiting review by the seller.
*   `reupload_required`: A previously uploaded payment receipt was rejected, and the customer needs to upload a new one.
*   `paid`: The payment for the order has been successfully received and confirmed.
