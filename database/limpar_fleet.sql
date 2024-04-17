##### SCRIPT PARA LIMPAR DADOS DE OUTRAS AGENCIAS DO PROGRAMA #####

SET FOREIGN_KEY_CHECKS=0;

truncate fleet_accessories;
truncate fleet_checklists;
truncate fleet_checklists_answers;

delete from fleet_costs where vehicle_id not in (select id from fleet_vehicles where source = "velozrotina");
SET @num := 0;
UPDATE fleet_costs SET id = @num := (@num+1);
ALTER TABLE fleet_costs AUTO_INCREMENT =1;

delete from fleet_expenses where vehicle_id not in (select id from fleet_vehicles where source = "velozrotina");
ALTER TABLE fleet_expenses AUTO_INCREMENT =1;

delete from fleet_fixed_costs where vehicle_id not in (select id from fleet_vehicles where source = "velozrotina");
ALTER TABLE fleet_fixed_costs AUTO_INCREMENT =1;

delete from fleet_fuel_log where vehicle_id not in (select id from fleet_vehicles where source = "velozrotina");
ALTER TABLE fleet_fuel_log AUTO_INCREMENT =1;

delete from fleet_incidences where vehicle_id not in (select id from fleet_vehicles where source = "velozrotina");
ALTER TABLE fleet_incidences AUTO_INCREMENT =1;

delete from fleet_maintenance_assigned_parts where maintenance_id not in (select id from fleet_maintenances where id not in (select id from fleet_vehicles where source = "velozrotina"));
ALTER TABLE fleet_maintenance_assigned_parts AUTO_INCREMENT =1;

delete from fleet_maintenances where vehicle_id not in (select id from fleet_vehicles where source = "velozrotina");
ALTER TABLE fleet_maintenances AUTO_INCREMENT =1;

delete from fleet_incidences where vehicle_id not in (select id from fleet_vehicles where source = "velozrotina");
ALTER TABLE fleet_incidences AUTO_INCREMENT =1;

delete from fleet_reminders where vehicle_id not in (select id from fleet_vehicles where source = "velozrotina");
ALTER TABLE fleet_reminders AUTO_INCREMENT =1;

delete from fleet_tolls_log where vehicle_id not in (select id from fleet_vehicles where source = "velozrotina");
ALTER TABLE fleet_tolls_log AUTO_INCREMENT =1;

delete from fleet_services where source <> "velozrotina";
ALTER TABLE fleet_services AUTO_INCREMENT =1;

delete from fleet_usage_log where vehicle_id not in (select id from fleet_vehicles where source = "velozrotina");
ALTER TABLE fleet_usage_log AUTO_INCREMENT =1;

delete from fleet_vehicle_attachments where vehicle_id not in (select id from fleet_vehicles where source = "velozrotina");
ALTER TABLE fleet_vehicle_attachments AUTO_INCREMENT =1;

delete from fleet_vehicle_history where vehicle_id not in (select id from fleet_vehicles where source = "velozrotina");
ALTER TABLE fleet_vehicle_history AUTO_INCREMENT =1;

delete from fleet_vehicles where source <> "velozrotina";
ALTER TABLE fleet_vehicles AUTO_INCREMENT =1;

delete from fleet_parts where source_owner <> "velozrotina";

drop table fleet_providers;