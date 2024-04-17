#### RESET DATABASE ####
#### LIMPA TODA A BASE DE DADOS LOGISTICA ####

SET FOREIGN_KEY_CHECKS=0;

truncate locations;
truncate locations_map;
truncate products;
truncate products_history;
truncate products_images;
truncate products_locations;
truncate reception_orders;
truncate reception_orders_lines;
truncate shipping_orders;
truncate shipping_orders_lines;
truncate warehouses;

SET FOREIGN_KEY_CHECKS=1;