<!DOCTYPE HTML>
<?php
/*
 ** Zabbix
 ** Copyright (C) 2001-2017 Zabbix SIA
 **
 ** This program is free software; you can redistribute it and/or modify
 ** it under the terms of the GNU General Public License as published by
 ** the Free Software Foundation; either version 2 of the License, or
 ** (at your option) any later version.
 **
 ** This program is distributed in the hope that it will be useful,
 ** but WITHOUT ANY WARRANTY; without even the implied warranty of
 ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 ** GNU General Public License for more details.
 **
 ** You should have received a copy of the GNU General Public License
 ** along with this program; if not, write to the Free Software
 ** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 ** by mma 20170904 v3.2
 **/


require_once dirname(__FILE__).'/include/config.inc.php';
require_once dirname(__FILE__).'/include/hosts.inc.php';
require_once dirname(__FILE__).'/include/maintenances.inc.php';
require_once dirname(__FILE__).'/include/forms.inc.php';
require_once dirname(__FILE__).'/include/users.inc.php';

$page['title'] = _('Maintenance one clic');
$page['file'] = 'maintenance_oneclic.php';
$page['scripts'] = ['class.calendar.js'];

require_once dirname(__FILE__).'/include/page_header.php';

//$hostsWidget = new CWidget();
//$hostsWidget->show();

error_reporting(E_ALL);
ini_set("display_errors", 1);


if (!empty($_POST["host"])){
	$name=$_POST["host"];
	$period=$_POST["time"];
	$description=$_POST["description"];
	$hosts = API::Host()->get(array(
				'filter' => array('host' => $name,'name' => $name),
				'output' => array('hostid'),
				'searchByAny' => 1,
				'limit' => 1
				));
	$now=time();
	//$tomorrow=time() + 86400;
	$tomorrow=time() + $period;
	$hostid=$hosts[0]['hostid'];
	$user=CWebUser::$data['alias'];
	$maintenance=array(
			'name' => "OneClic - $name by $user for ".gmdate("G",$_POST["time"])."h (".date('Y-m-d H:i:s',$now).")",
			'active_since' => "$now",
			'active_till' => "$tomorrow",
			'maintenance_type' => 0,
			'hostids' => array("$hostid"),
			'groupids' => array(),
			'description' => "$description",
			'timeperiods' => array(array(
					'timeperiod_type' =>"0",
					'start_time' =>"$now",
					'period' =>"$period",
					'every' => "1",
					'dayofweek' => "64"
					))

			);
	//echo "<pre>";var_dump($maintenance);echo "</pre>";
	$result = API::Maintenance()->create($maintenance);
	if (!empty($result['maintenanceids'][0])){
		echo "Maintenance succefully added : <a href='http://zabbix24/zabbix/maintenance.php?form=update&maintenanceid=".$result['maintenanceids'][0]."'>Maintenance ".$result['maintenanceids'][0]." </a>"
			;
	}else
	{
		echo "Error when adding maintenance !";
	}


}	

//form
?>
<div class="header-title table"><div class="cell"><h1>Maintenance one clic</h1></div></div>
<br/>
<form method="post">
<div id="maintenanceTab" aria-labelledby="tab_maintenanceTab" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="true" aria-hidden="false">
<ul class="table-forms" id="maintenanceFormList">
<li><div class="table-forms-td-left">
<label for="host">Host </label>
</div>
<div class="table-forms-td-right">
<input list="host" type="text" name="host" size="50" autocomplete="off">
<datalist id="host">
<?php
$hosts = API::Host()->get(array(
			'output' => array('host','name'),
			));
$arr_hosts=array();
foreach($hosts as $host){
	array_push($arr_hosts,$host['host']);
	array_push($arr_hosts,$host['name']);
}
$arr_hosts=array_unique($arr_hosts);
sort($arr_hosts);
foreach($arr_hosts as $name){
	?><option value="<?php echo $name; ?>" /><?php
}

?>
</datalist>
<li>
	<div class="table-forms-td-left">
		<label for="time">Time </label>
	</div>
	<div class="table-forms-td-right">
		<select id="time" name="time">
			<option value="900">15min</option>
			<option value="1800">30min</option>
			<option value="3600">1h</option>
			<option value="7200">2h</option>
			<option value="14400">4h</option>
			<option value="86400">24h</option>
		</select>
	</div>
</li>
<li>
	<div class="table-forms-td-left">
		<label for="description">Description </label>
	</div>
	<div class="table-forms-td-right">
		<textarea id="description" name="description" rows="7" style="width: 480px;" value="<?php echo CWebUser::$data['alias']; ?>"></textarea>
	</div>
</li>
</ul>
<ul class="table-forms">
	<li>
		<div class="table-forms-td-left"></div>
		<div class="table-forms-td-right tfoot-buttons">
			<button type="submit" value="Add"/>Add</button>
		</div>
	</li>
	</ul>
</div>
</form>
<?php

require_once dirname(__FILE__).'/include/page_footer.php';
