 <?php
header( 'content-type: text/html; charset=iso-8859-1' );
	if(isset($_POST['csv']))
   {
	$input="";
	$input=$_POST["csv"];
	$lines = explode(PHP_EOL, $input);
	$array = array();
	foreach ($lines as $line) {
	    $array[] = str_getcsv($line);
	}
	//echo "<pre>";
	//print_r($array);
	//echo "</pre>";
	
	$str='<?xml version="1.0" encoding="UTF-8"?>'."
<zabbix_export>
    <version>2.0</version>
    <groups>
        <group>
            <name>".$_POST["groupTemplate"]."</name>
        </group>
    </groups>
    <templates>
        <template>
            <template>".$_POST["nameTemplate"]."</template>
            <name>".$_POST["nameTemplate"]."</name>
            <description/>
            <groups>
                <group>
                    <name>".$_POST["groupTemplate"]."</name>
                </group>
            </groups>
            <applications>
                <application>
                    <name>".$_POST["nameApp"]."</name>
                </application>
            </applications>
	    <items>
";

	foreach ($array as $line) {
	
	$strItem="
	<item>
                    <name>State of service ".trim($line[1])."</name>
                    <type>0</type>
                    <snmp_community/>
                    <multiplier>0</multiplier>
                    <snmp_oid/>
                    <key>service_state[".trim($line[0])."]</key>
                    <delay>300</delay>
                    <history>30</history>
                    <trends>90</trends>
                    <status>0</status>
                    <value_type>3</value_type>
                    <allowed_hosts/>
                    <units/>
                    <delta>0</delta>
                    <snmpv3_contextname/>
                    <snmpv3_securityname/>
                    <snmpv3_securitylevel>0</snmpv3_securitylevel>
                    <snmpv3_authprotocol>0</snmpv3_authprotocol>
                    <snmpv3_authpassphrase/>
                    <snmpv3_privprotocol>0</snmpv3_privprotocol>
                    <snmpv3_privpassphrase/>
                    <formula>1</formula>
                    <delay_flex/>
                    <params/>
                    <ipmi_sensor/>
                    <data_type>0</data_type>
                    <authtype>0</authtype>
                    <username/>
                    <password/>
                    <publickey/>
                    <privatekey/>
                    <port/>
                    <description/>
                    <inventory_link>0</inventory_link>
                    <applications>
                        <application>
                            <name>".$_POST["nameApp"]."</name>
                        </application>
                    </applications>
                    <valuemap>
                                <name>Windows service state</name>
                    </valuemap>
                    <logtimefmt/>
                </item>
	";
	$str.=$strItem;
}

	$str.="
    </items>
            <discovery_rules/>
            <macros/>
            <templates/>
            <screens/>
        </template>
    </templates>
       <value_maps>
        <value_map>
            <name>Windows service state</name>
            <mappings>
                <mapping>
                    <value>0</value>
                    <newvalue>Running</newvalue>
                </mapping>
                <mapping>
                    <value>1</value>
                    <newvalue>Paused</newvalue>
                </mapping>
                <mapping>
                    <value>2</value>
                    <newvalue>Start pending</newvalue>
                </mapping>
                <mapping>
                    <value>3</value>
                    <newvalue>Pause pending</newvalue>
                </mapping>
                <mapping>
                    <value>4</value>
                    <newvalue>Continue pending</newvalue>
                </mapping>
                <mapping>
                    <value>5</value>
                    <newvalue>Stop pending</newvalue>
                </mapping>
                <mapping>
                    <value>6</value>
                    <newvalue>Stopped</newvalue>
                </mapping>
                <mapping>
                    <value>7</value>
                    <newvalue>Unknown</newvalue>
                </mapping>
                <mapping>
                    <value>255</value>
                    <newvalue>No such service</newvalue>
                </mapping>
            </mappings>
        </value_map>
    </value_maps>
    <triggers>
	";

    foreach ($array as $line) {

        $strTrigger="
        <trigger>
            <expression>{".$_POST["nameTemplate"].":service_state[".trim($line[0])."].count(#2,0,&quot;ne&quot;)}=2</expression>
            <name>Service ".trim($line[1])." is {ITEM.LASTVALUE}</name>
            <url/>
            <status>0</status>
            <priority>4</priority>
            <description/>
            <type>0</type>
            <dependencies/>
	   <tags>
                 <tag>
                        <tag>NiveauDeSupport</tag>
                	<value>Service</value>
           	</tag>
           </tags>
        </trigger>
        ";
        $str.=$strTrigger;
}

	$str.="    </triggers>
</zabbix_export>";

	header('Content-Disposition: attachment; filename='.$_POST["nameTemplate"].'.xml');
	header('Content-Type: text/plain'); # Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
	header('Content-Length: ' . strlen($str));
	header('Connection: close');


	echo utf8_encode ($str);

	}
else
	{
?>
To obtain services : <i>Get-WmiObject win32_service -Filter "state = 'running' AND displayName LIKE '%Lync%'" | select-object @{Label='Name';Expression={$_.Name}},@{Label='displayname';Expression={","+$_.displayname}}</i><br/><br/>
		<form method="post">
			Nom du template : <input name="nameTemplate" required = "required" size="50"/><br/>
			Nom de l'application : <input name="nameApp" required = "required" size="50"/><br/>
			Nom du groupe : <input name="groupTemplate" required = "required"  size="50"/><br/>
			CSV (avec des ,) : <textarea name="csv" cols="200" rows="30" required = "required"></textarea><br/>
			<input type="submit"/>

		</form>
<?php
	}
?>
