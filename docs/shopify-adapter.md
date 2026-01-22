Shopify Connector Stub

This directory documents a future Shopify adapter for the channel API.

Planned responsibilities:
- Verify Shopify webhook signatures.
- Map Shopify order GID to external_order_id.
- Map Shopify customer GID to external_customer_id.
- Translate Shopify events into core app channel payloads.

Notes:
- Use external_order_type = "order".
- Use external_customer_type = "customer".
- Validate HMAC via Shopify shared secret.
