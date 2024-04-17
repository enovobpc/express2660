#### RESET DATABASE ####
#### LIMPA TODA A BASE DE DADOS ####

SET FOREIGN_KEY_CHECKS=0;

truncate backups;
truncate backup_auth_tokens;
truncate agencies_billing;
truncate agencies_zip_codes;

truncate billing_products;
#truncate billing_zones;
delete from billing_zones where source is not null;
truncate calendar_events_participants;
truncate calendar_events;
truncate cashier_movements;

truncate customers_assigned_messages;
truncate customers_assigned_services;
truncate customers_attachments if exists;
truncate customers_balance;
truncate customers_billing;
truncate customers_business_history;
truncate customers_contacts;
truncate customers_covenants;
drop table if exists customers_future_services;

truncate customers_messages;
truncate customers_ranking;
truncate customers_webservices;
truncate express_services;
truncate users_expenses;

delete from importer_models where source is not null;
SET @num := 0;
UPDATE importer_models SET id = @num := (@num+1);
ALTER TABLE importer_models AUTO_INCREMENT =1;

truncate invoices_series;
truncate invoices_lines;
truncate invoices;
truncate licenses_payments;
truncate licenses;
truncate ltm_translations;
truncate meetings;

truncate notices_assigned_users;
truncate notices;
truncate notifications;

truncate oauth_access_tokens;
truncate oauth_auth_codes;
truncate oauth_personal_access_clients;
truncate oauth_refresh_tokens;
delete from oauth_clients where id > 1;
alter table oauth_clients AUTO_INCREMENT = 1;

truncate operators_tasks;
truncate password_resets;

truncate payments_notifications_history;
truncate payments_notifications;

truncate prices_tables;
truncate products_sales;
truncate products;


truncate providers_assigned_expenses;
truncate providers_assigned_services;
truncate purchase_invoices;
truncate purchase_invoices_lines;
truncate purchase_invoices_types;
truncate purchase_payment_notes;
truncate purchase_payment_note_invoices;
truncate purchase_payment_note_methods;
truncate purchase_payment_note_invoices;



truncate shipments_assigned_expenses;
truncate shipments_history_notifications;
truncate shipments_incidences_resolutions;
truncate shipments_history;

truncate shipments_packs_dimensions;
truncate shipments_pallets;
truncate shipments_scheduled;
truncate shipments_traceability;
truncate shipments_warnings_ignored;
truncate shipments_payments;
truncate shipments_attachments;
truncate shipping_expenses;

truncate refunds_cod;
truncate refunds_control;
truncate refunds_control_agencies;
truncate refunds_control_requests;

truncate shipments;

truncate routes;
truncate safts;
truncate services_volumetric_factor;
truncate services;

truncate sms_logs;
truncate sms_packs;

truncate users_absences;
truncate users_assigned_workgroups;
truncate users_attachments;
truncate users_cards;
truncate users_contracts;
truncate users_workgroups;
truncate vehicles;
truncate webservices_configs;
truncate users_locations;


truncate providers;
truncate customers_recipients;
truncate customers;
truncate customers_types;

truncate payments_notifications_history;
truncate payments_notifications;
truncate gateway_payments;
truncate shipments_payments;

truncate customers_support_messages_attachments;
truncate customers_support_tickets_attachments;
truncate customers_support_messages;
truncate customers_support_tickets;
truncate scheduled_tasks;

truncate agencies;

delete from users where id > 2;
update users set source=null, agencies=null where id = 2;
alter table users AUTO_INCREMENT = 1;
delete from assigned_roles where id > 1;
delete from permission_role where role_id > 7;
delete from role_user where user_id > 2;
delete from roles where source is not null;

update webservice_methods set sources = null;

delete from files_repository where id > 4;
update files_repository set source = 'idealexpresso';
update incidences_types set sources = '["idealexpresso"]';
update webservice_methods set sources = '["idealexpresso"]';
update providers_categories set source = 'idealexpresso';
update pack_types set source = 'idealexpresso';
update cargo_planning_events_types set source = 'idealexpresso';
update payment_conditions set source='idealexpresso'

SET FOREIGN_KEY_CHECKS=1;