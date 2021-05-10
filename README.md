# Shopware 6 Plugin - ImportByProductNumber

As of Shopware 6.4.0.0, importing products with a productNumber that already exists in the database yields an error.

This plugin checks during the import of products to Shopware 6 whether a productNumber is already existing.
If so, the entry is updated.
