<?php

require_once dirname(__FILE__).'/include/config.inc.php';
require_once dirname(__FILE__).'/include/forms.inc.php';

$page['title'] = _('Maintenance one clic');
$page['file'] = 'maintenance_oneclic.php';
$page['type'] = detect_page_type(PAGE_TYPE_HTML);
$page['hist_arg'] = array('groupid');
$page['scripts'] = array('multiselect.js');

require_once dirname(__FILE__).'/include/page_header.php';
$hostsWidget = new CWidget();
$hostsWidget->addPageHeader(_('Maintenance One Clic'));
$hostsWidget->show();

if (!empty($_POST["host"])){
	$name=$_POST["host"];
	$period=$_POST["time"];
	$hosts = API::Host()->get(array(
		'filter' => array('host' => $name),
		'output' => array('hostid'),
		'limit' => 1
	));
	$now=time();
	$tomorrow=time() + $period; ;
	$hostid=$hosts[0]['hostid'];
	$maintenance=array(
		'name' => "Maintenance One Clic on $name for ".$_POST["time"]." seconds (".date('Y-m-d H:i:s',$now).")",
		'active_since' => "$now",
		'active_till' => "$tomorrow",
		'maintenance_type' => 0,
		'hostids' => array("$hostid"),
		'groupids' => array(),
		'timeperiods' => array(array(
			'timeperiod_type' =>"0",
			'start_time' =>"$now",
			'period' =>"$period",
			'every' => "1",
                	'dayofweek' => "64"
		))
		
	);
	$result = API::Maintenance()->create($maintenance);
	if (!empty($result['maintenanceids'][0])){
		echo "Maintenance succefully added : <a href='http://zabbix24/zabbix/maintenance.php?form
=update&maintenanceid=".$result['maintenanceids'][0]."'>Maintenance ".$result['maintenanceids'][0]." </a>
";
	}else
	{
		echo "Error when adding maintenance !";
	}
	

}	

?>
<br/>
<form method="post">
<center>
<label for="host">Host : </label>
<input list="host" type="text" name="host">
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
<label for="time">Time : </label>
<select id="time" name="time">
	<option value="3600">1h</option>
	<option value="7200">2h</option>
	<option value="14400">4h</option>
	<option value="86400">24h</option>
</select>
<input type="submit"/>
</center>
</form>
<?php
require_once dirname(__FILE__).'/include/page_footer.php';

