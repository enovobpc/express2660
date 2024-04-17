##### SCRIPT PARA LIMPAR DADOS DE OUTRAS AGENCIAS DO PROGRAMA #####

SET FOREIGN_KEY_CHECKS=0;


truncate agencies_billing;

delete from agencies_zip_codes where source <> 'transportescm' or deleted_at is not null;
SET @num := 0;
UPDATE agencies_zip_codes SET id = @num := (@num+1);
ALTER TABLE agencies_zip_codes AUTO_INCREMENT =1;

truncate backup_auth_tokens;
truncate backups;

delete from billing_products where source <> 'transportescm';
SET @num := 0;
UPDATE billing_products SET id = @num := (@num+1);
ALTER TABLE billing_products AUTO_INCREMENT =1;

delete from billing_zones where source <> 'transportescm' and source is not null or deleted_at is not null;
SET @num := 0;
UPDATE billing_zones SET id = @num := (@num+1);
ALTER TABLE billing_zones AUTO_INCREMENT =1;

delete from calendar_events_participants where calendar_event_id not in (select id from calendar_events where deleted_at is null and created_by not in (select id from users where source='transportescm'));
SET @num := 0; UPDATE calendar_events_participants SET id = @num := (@num+1);
ALTER TABLE calendar_events_participants AUTO_INCREMENT =1;
delete from calendar_events where created_by not in (select id from users where source = 'transportescm') or deleted_at is not null;
ALTER TABLE calendar_events AUTO_INCREMENT =1;

truncate cashier_movements;
delete from customers_assigned_messages where customer_id not in (select id from customers where source='transportescm');
SET @num := 0; UPDATE customers_assigned_messages SET id = @num := (@num+1);
ALTER TABLE customers_assigned_messages AUTO_INCREMENT =1;
delete from customers_messages where source <> 'transportescm';

delete from customers_ranking where customer_id not in (select id from customers where source='transportescm');
SET @num := 0;
UPDATE customers_ranking SET id = @num := (@num+1);
ALTER TABLE customers_ranking AUTO_INCREMENT =1;

delete from customers_webservices where customer_id not in (select id from customers where source='transportescm') or deleted_at is not null;
SET @num := 0;
UPDATE customers_webservices SET id = @num := (@num+1);
ALTER TABLE customers_webservices AUTO_INCREMENT =1;

delete from customers_attachments where customer_id not in (select id from customers where source = 'transportescm') or deleted_at is not null;
SET @num := 0; UPDATE customers_attachments SET id = @num := (@num+1);
ALTER TABLE customers_attachments AUTO_INCREMENT =1;

delete from customers_balance where customer_id not in (select id from customers where source = 'transportescm') or deleted_at is not null;
SET @num := 0; UPDATE customers_balance SET id = @num := (@num+1);
ALTER TABLE customers_balance AUTO_INCREMENT =1;

delete from customers_billing where customer_id not in (select id from customers where source = 'transportescm') or deleted_at is not null;
SET @num := 0; UPDATE customers_billing SET id = @num := (@num+1);
ALTER TABLE customers_billing AUTO_INCREMENT =1;

delete from customers_business_history where customer_id not in (select id from customers where source = 'transportescm') or deleted_at is not null;
SET @num := 0; UPDATE customers_business_history SET id = @num := (@num+1);
ALTER TABLE customers_business_history AUTO_INCREMENT =1;

delete from customers_contacts where customer_id not in (select id from customers where source = 'transportescm') or deleted_at is not null;
SET @num := 0; UPDATE customers_contacts SET id = @num := (@num+1);
ALTER TABLE customers_contacts AUTO_INCREMENT =1;

delete from customers_covenants where customer_id not in (select id from customers where source = 'transportescm') or deleted_at is not null;
ALTER TABLE customers_covenants AUTO_INCREMENT =1;


truncate express_services;
drop table import_methods;

delete from importer_models where source is not null and source <> 'transportescm';
SET @num := 0; UPDATE importer_models SET id = @num := (@num+1);
ALTER TABLE importer_models AUTO_INCREMENT =1;


delete from invoices_lines where id not in (select id from invoices where source = 'transportescm');
ALTER TABLE invoices_lines AUTO_INCREMENT =1;
delete from invoices where source <> 'transportescm';
ALTER TABLE invoices AUTO_INCREMENT =1;

truncate licenses_payments;
truncate licenses;
truncate ltm_translations;
delete from meetings where customer_id not in (select id from customers where source='transportescm') or deleted_at is not null;
SET @num := 0;
UPDATE meetings SET id = @num := (@num+1);
ALTER TABLE meetings AUTO_INCREMENT =1;

drop table news;
truncate notices_assigned_users;
truncate notices;
truncate notifications;

truncate oauth_access_tokens;
truncate oauth_auth_codes;
truncate oauth_refresh_tokens;
truncate operators_tasks;
truncate password_resets;

delete from prices_tables where source <> 'transportescm' or source is null or deleted_at is not null;
ALTER TABLE prices_tables AUTO_INCREMENT =1;


delete from customers_assigned_services where customer_id not in (select id from customers where source = 'transportescm') and price_table_id is null;
delete from customers_assigned_services where customer_id is null and price_table_id not in (select id from prices_tables where source = 'transportescm');
ALTER TABLE customers_assigned_services AUTO_INCREMENT =1;

delete from products_sales where product_id not in (select id from products where source = 'transportescm') or deleted_at is not null;
SET @num := 0; UPDATE products_sales SET id = @num := (@num+1);
ALTER TABLE products_sales AUTO_INCREMENT =1;
delete from products where source <> 'transportescm' or deleted_at is not null;
ALTER TABLE products AUTO_INCREMENT =1;


delete from providers_assigned_services where provider_id not in (select id from providers where source = 'transportescm');
delete from providers_assigned_expenses where provider_id not in (select id from providers where source = 'transportescm');
delete from providers where source <> 'transportescm' or source is null;

delete from refunds_cod where shipment_id not in (select id from shipments where agency_id in (select id from agencies where source = 'transportescm') and deleted_at is null);
delete from refunds_control where shipment_id not in (select id from shipments where agency_id in (select id from agencies where source = 'transportescm') and deleted_at is null);
ALTER TABLE refunds_control AUTO_INCREMENT =1;


delete from shipments_assigned_expenses where shipment_id not in (select id from shipments where agency_id in (select id from agencies where source = 'transportescm') and deleted_at is null);
SET @num := 0; UPDATE shipments_assigned_expenses SET id = @num := (@num+1);
ALTER TABLE shipments_assigned_expenses AUTO_INCREMENT =1;


truncate shipments_attachments;

truncate shipments_history_notifications;
delete from shipments_history where shipment_id not in (select id from shipments where agency_id in (select id from agencies where source = 'transportescm') and deleted_at is null);
ALTER TABLE shipments_history AUTO_INCREMENT =1;


delete from shipments_incidences_resolutions where shipment_id not in (select id from shipments where agency_id in (select id from agencies where source = 'transportescm') and deleted_at is null);
SET @num := 0; UPDATE shipments_incidences_resolutions SET id = @num := (@num+1);
ALTER TABLE shipments_incidences_resolutions AUTO_INCREMENT =1;

delete from shipments_packs_dimensions where shipment_id not in (select id from shipments where agency_id in (select id from agencies where source = 'transportescm') and deleted_at is null);
SET @num := 0; UPDATE shipments_packs_dimensions SET id = @num := (@num+1);
ALTER TABLE shipments_packs_dimensions AUTO_INCREMENT =1;

delete from shipments_pallets where shipment_id not in (select id from shipments where agency_id in (select id from agencies where source = 'transportescm') and deleted_at is null);
SET @num := 0; UPDATE shipments_pallets SET id = @num := (@num+1);
ALTER TABLE shipments_pallets AUTO_INCREMENT =1;

delete from shipments_traceability where shipment_id not in (select id from shipments where agency_id in (select id from agencies where source = 'transportescm') and deleted_at is null);
SET @num := 0; UPDATE shipments_traceability SET id = @num := (@num+1);
ALTER TABLE shipments_traceability AUTO_INCREMENT =1;


delete from shipments where agency_id not in (select id from agencies where source = 'transportescm') or deleted_at is not null;

truncate shipments_scheduled;
delete from shipments_warnings_ignored where shipment_id not in (select id from shipments where agency_id in (select id from agencies where source = 'transportescm') and deleted_at is null);
ALTER TABLE shipments_warnings_ignored AUTO_INCREMENT =1;

delete from shipping_expenses where source <> 'transportescm';
ALTER TABLE shipping_expenses AUTO_INCREMENT =1;


delete from services where source <> 'transportescm' or source is null or deleted_at is not null;

delete from routes where source <> 'transportescm' or deleted_at is not null;
ALTER TABLE routes AUTO_INCREMENT =1;


delete from customers_recipients where customer_id not in (select id from customers where source='transportescm');
ALTER TABLE customers_recipients AUTO_INCREMENT =1;

delete from customers where source <> 'transportescm' or deleted_at is not null;
delete from customers_types where source <> 'transportescm' or deleted_at is not null;
ALTER TABLE customers_types AUTO_INCREMENT =1;

delete from services_volumetric_factor where service_id not in (select id from services where source = 'transportescm');
SET @num := 0; UPDATE services_volumetric_factor SET id = @num := (@num+1);
ALTER TABLE services_volumetric_factor AUTO_INCREMENT =1;

truncate sms_logs;
truncate sms_packs;
truncate safts;

update shipping_status set agencies = null, sources = '["transportescm"]';
update providers_categories set source = "transportescm";
truncate users_cards;
truncate users_contracts;
truncate users_locations;
update webservice_methods set  sources = '["transportescm"]';
delete from webservices_configs where source <> 'transportescm' or deleted_at is not null;
SET @num := 0; UPDATE webservices_configs SET id = @num := (@num+1);
ALTER TABLE webservices_configs AUTO_INCREMENT =1;


delete from users where id > 1 and source <> 'transportescm' or deleted_at is not null;
delete from role_user where user_id not in (select id from users);
delete from roles where source is not null and source <> 'transportescm';
delete from permission_role where role_id not in (select id from roles);

delete from agencies where source <> 'transportescm';

SET FOREIGN_KEY_CHECKS=1;