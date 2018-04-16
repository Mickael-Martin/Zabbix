diff -u /usr/share/zabbix/include/events.inc.php.bak /usr/share/zabbix/include/events.inc.php
--- /usr/share/zabbix/include/events.inc.php.bak	2018-04-16 14:22:43.825029683 +0200
+++ /usr/share/zabbix/include/events.inc.php	2018-04-16 14:23:06.877207291 +0200
@@ -784,7 +784,7 @@
 	$triggerOptions = [
 		'filter' => [],
 		'skipDependent' => 1,
-		'selectHosts' => ['hostid', 'name'],
+		'selectHosts' => ['hostid', 'name','maintenance_status'],
 		'output' => API_OUTPUT_EXTEND,
 		'sortfield' => 'lastchange',
 		'sortorder' => ZBX_SORT_DOWN,
