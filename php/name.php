<?php
$ldappath = $_GET['path'];
$id = $_GET['id'];
$ldaphost = "***";
$ldapport = 389;
$ldappassword = "***";
$ldapusername = "***";
$ldapconn = ldap_connect($ldaphost,$ldapport);

if($ldapconn)
{
	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);	
	$ldapbind = ldap_bind($ldapconn, $ldapusername, $ldappassword);
	if ($id == 1)
		$ldapsearch = ldap_search($ldapconn, $ldappath,"(&(objectCategory=person)(objectClass=user))");
	else
		if ($id == 2)
			$ldapsearch = ldap_search($ldapconn, "ou=Staff,dc=DNG,dc=donbassgaz,dc=ru","(&(objectCategory=person)(objectClass=user)(displayname=$ldappath*))");
	ldap_sort($ldapconn, $ldapsearch, "displayname");
	$ldapinfo = ldap_get_entries($ldapconn, $ldapsearch);
	
	for($i = 0; $i < $ldapinfo["count"]; $i++)
	{
		$name = explode(" ", $ldapinfo[$i]["displayname"][0]);
		echo "<tr class=\"person\" data-path=\"" .$ldapinfo[$i]["dn"]. "\">";
		echo "<td></td>";		
		echo "<td>" .$ldapinfo[$i]["displayname"][0]. "</td>";
		echo "<td>" .$ldapinfo[$i]["physicaldeliveryofficename"][0]. "</td>";		
		echo "<td>" .$ldapinfo[$i]["telephonenumber"][0]. "</td>";	
		echo "<td class=\"info\"></td>";
		echo "</tr>";
	}
	
	ldap_close($ldapconn);
}
else
{
    echo "No connect";
}

?>