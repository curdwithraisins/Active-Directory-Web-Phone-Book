<?php
$ldapname = $_GET['path'];

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
	$ldapsearch = ldap_search($ldapconn, "ou=Staff,dc=DNG,dc=donbassgaz,dc=ru","(&(objectCategory=person)(objectClass=user)(displayname=$ldapname*))");
	ldap_sort($ldapconn, $ldapsearch, "displayname");
	$ldapinfo = ldap_get_entries($ldapconn, $ldapsearch);
	
	for($i = 0; $i < $ldapinfo["count"]; $i++)
		echo "<li class=\"autocomplete-block\" data-path=\"" .$ldapinfo[$i]["dn"]. "\">" .$ldapinfo[$i]["displayname"][0]. "</li>";
	
	ldap_close($ldapconn);
}
else
{
    echo "No connect";
}

?>