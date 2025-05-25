# Possible Order Status Values

Based on the application's logic, database migrations, and UI, the possible values for an order's status are:

*   `pending`: The initial state of an order after it has been placed.
*   `packing`: The order is being prepared for shipment.
*   `delivering`: The order is currently in transit to the customer.
*   `completed`: The order has been successfully delivered and received by the customer.
*   `canceled`: The order has been canceled.
