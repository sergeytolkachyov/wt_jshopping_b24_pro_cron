# WT JShopping Bitrix 24 PRO CRON
Joomla 4.1+ task scheduler plugin for updating prices and quantities in JoomShopping products form Bitrix 24
## v.1.1.1 Bitrix 24 API requests limit
Bitrix24 assumes a limit of 2 requests per second when accessing the API. If the limit is exceeded, the request is not executed, the API returns an error about exceeding the limit. As a result, some goods did not receive the updated value of prices and quantities. 
Now the script "sleeps" 0.5 seconds after each executed request. It takes 2 requests to update the price and quantity. Each attribute also requires 2 API requests. Accordingly, updating the product data now takes at least 1 second, as well as another 1 second for each attribute.
###  Approximate time of updating of goods depending on their quantity and complexity
| Product type                                     | Number of products | API Requests | Task complete expected time (seconds) |
|--------------------------------------------------|--------------------|--------------|--------------------------------------|
| Simple product without variations                | 1                  | 2            | 1s                                   |
|                                                  | 100                | 2            | 100s (~1.7min)                       |
|                                                  | 3000               | 2            | 3000s (~50min)                       |
| Simple product **with** 1 (main) variation       | 1                  | 2            | 1s                                   |
|                                                  | 100                | 2            | 100s (~1.7min)                       |
|                                                  | 3000               | 2            | 3000s (~50min)                       |
| Product with 1 (main) variation and 1 attribute  | 1                  | 4            | 2s                                   |
|                                                  | 100                | 4            | 200s (~3.4min)                       |
|                                                  | 3000               | 4            | 6000s (~100 min = 1h 40min)         |
| Product with 1 (main) variation and 2 attributes | 1                  | 6            | 3s                                   |
|                                                  | 100                | 6            | 300s (5 min)                         |
|                                                  | 3000               | 6            | 9000s (~150 min = 2h 30min)          |

## v.1.1.0 Variations of Bitrix 24 products 
**Need a [WT JoomShopping Bitrix 24 PRO plugin](https://github.com/sergeytolkachyov/wt_jshopping_b24_pro) minimal version 3.1.0 for work**
Added an update of prices and balances of JoomShopping products with dependent attributes from variations of Bitrix 24 products. For a correct update, you need to configure the mapping of JoomShopping attributes with variations of Bitrix 24 products. If you have a product without dependent attributes, and a product in Bitrix24 with a variation, specify the main variation for the product in JoomShopping.
The update time of one product depends on the number of attributes, since for each attribute there are 2 separate requests to the Bitrix 24 API, which increases the synchronization time as a whole.
It takes from 1.5 to 2.2 seconds to update a product with the specified main variation of the product and 3 attributes.
## v.1.0.1
- Joomla 4 plugin structure
- need a [WT JoomShopping Bitrix 24 PRO plugin](https://github.com/sergeytolkachyov/wt_jshopping_b24_pro) for work
- Processing time for 1 product is near 0.6 sec.
- usage from CLI (and CRON): php /path/to/site/public_html/cli/joomla.php scheduler:run