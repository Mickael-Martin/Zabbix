diff -u /usr/share/zabbix/jsrpc.php.bak /usr/share/zabbix/jsrpc.php
--- /usr/share/zabbix/jsrpc.php.bak	2018-04-16 14:24:12.365708789 +0200
+++ /usr/share/zabbix/jsrpc.php	2018-04-16 14:25:10.946153900 +0200
@@ -121,6 +121,9 @@
 					}
 
 					$url_tr_status = 'tr_status.php?hostid='.$host['hostid'];
+						
+					if ($host['maintenance_status'] == 1) { $sound='no_sound.wav';}
+								
 					$url_events = (new CUrl('zabbix.php'))
 						->setArgument('action', 'problem.view')
 						->setArgument('filter_triggerids[]', $event['objectid'])
