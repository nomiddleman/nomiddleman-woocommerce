# Nomiddleman Crypto Payments for Woocommerce [WordPress plugin](https://wordpress.org/plugins/nomiddleman-crypto-payments-for-woocommerce/) 

[Get it in the WordPress plugin repo](https://wordpress.org/plugins/nomiddleman-crypto-payments-for-woocommerce/)

Classic Mode
============
Classic mode includes the basic functionality for a cryptocurrency. It will cycle through your addresses displaying a different one for each order, fetches real time valuation based on selected pricing options, and outputs a QR code to the customer on the thank you page.

Autopay Mode
============
### Autopay Mode builds on Classic Mode with the following additions

- Scans blockchain APIs for transactions that match your orders
- Updates orders to processing once a matching transaction is found
- Emails the customer indicating payment has been received
- Cancels unpaid orders after a set amount of time

Privacy Mode
============
Privacy Mode allows you to utilize your HD Wallet’s Master Public Key to generate a unique address for every order.

### Privacy mode includes all Autopay functionality with these additional features:

- Unique cryptocurrency payment address for every order
- If an order receives a payment too small to process, an email is sent to the customer with the remaining balance
- No chance of payment collisions due to unique address per order