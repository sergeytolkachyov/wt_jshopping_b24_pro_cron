# WT JShopping Bitrix 24 PRO CRON
Joomla 4.1+ task scheduler plugin for updating prices and quantities in JoomShopping products form Bitrix 24
## v.1.1.0 Variations of Bitrix 24 products 
Added an update of prices and balances of JoomShopping products with dependent attributes from variations of Bitrix 24 products. For a correct update, you need to configure the mapping of JoomShopping attributes with variations of Bitrix 24 products. If you have a product without dependent attributes, and a product in Bitrix24 with a variation, specify the main variation for the product in JoomShopping.
The update time of one product depends on the number of attributes, since for each attribute there are 2 separate requests to the Bitrix 24 API, which increases the synchronization time as a whole.
It takes from 1.5 to 2.2 seconds to update a product with the specified main variation of the product and 3 attributes.
## v.1.0.1
- Joomla 4 plugin structure
- need a [WT JoomShopping Bitrix 24 PRO plugin](https://github.com/sergeytolkachyov/wt_jshopping_b24_pro) for work
- Processing time for 1 product is near 0.6 sec.
- usage from CLI (and CRON): php /path/to/site/public_html/cli/joomla.php scheduler:run