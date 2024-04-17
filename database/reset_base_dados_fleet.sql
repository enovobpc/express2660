##### SCRIPT PARA LIMPAR DADOS DE OUTRAS AGENCIAS DO PROGRAMA #####

SET FOREIGN_KEY_CHECKS=0;

truncate fleet_accessories;
truncate fleet_checklists;
truncate fleet_checklists_answers;
delete from fleet_checklists_items where id >8;
ALTER TABLE fleet_checklists_items AUTO_INCREMENT =1;
truncate fleet_checklists_items;
truncate fleet_costs;
truncate fleet_expenses;
truncate fleet_fixed_costs;
truncate fleet_fuel_log;
truncate fleet_incidences;
truncate fleet_incidences_images;
truncate fleet_maintenance_assigned_parts;
truncate fleet_maintenances;
truncate fleet_parts;
truncate fleet_reminders;
delete from fleet_parts where source_owner is not null;
ALTER TABLE fleet_parts AUTO_INCREMENT =1;
truncate fleet_services;
truncate fleet_tolls_log;
truncate fleet_usage_log;
truncate fleet_vehicle_attachments;
truncate fleet_vehicle_history;
truncate fleet_vehicles;