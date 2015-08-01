<?php
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
	$ldapsearch = ldap_search($ldapconn, "ou=Staff,dc=DNG,dc=donbassgaz,dc=ru","(ou=Users)");
	ldap_sort($ldapconn, $ldapsearch, "distinguishedname");
	$ldapinfo = ldap_get_entries($ldapconn, $ldapsearch);
	
	//Заполнение массива параметров
	foreach ($ldapinfo as $users)
	{
		$pieces = explode(",", $users["dn"]);
		
		if (count($pieces) == 6)
			$namemass[count($namemass)] = array("count" => 1, 0 => array(0 => $users["dn"],1 => $pieces[1],2 => "",3 => ""));
		else
			if (count($pieces) == 7)
				$namemass[count($namemass)] = array("count" => 2, 0 => array(0 => $users["dn"],1 => $pieces[1],2 => $pieces[2],3 => ""));
			else
				$namemass[count($namemass)] = array("count" => 3, 0 => array(0 => $users["dn"],1 => $pieces[1],2 => $pieces[2],3 => $pieces[3]));
		
		//Массив для сортировки
		$mas_of_sort[count($mas_of_sort)] = $namemass[count($namemass)-1][0][3].$namemass[count($namemass)-1][0][2].$namemass[count($namemass)-1][0][1];
	}
	
	//Сортировка с сохранением ключа
	asort($mas_of_sort);
	
	//Массив нумерации отсортированного
	foreach ($mas_of_sort as $key => $val)
		$mas_of_num[count($mas_of_num)] = $key;
	
	echo "<ul><li class=\"pointer pointer-click check\" data-path=\"home\"><span class=\"home\">Главная</span></li>";
	
	//Вывод
	for($i = 1; $i < count($mas_of_num); $i++)
	{
		//Если уже был рассмотрен (для элементов второй и третей вложенности)
		if($namemass[$mas_of_num[$i]]["count"] == 0)
			continue;
		else
			//Если первая вложенность
			if($namemass[$mas_of_num[$i]]["count"] == 1)
			{
				$name = strtok($namemass[$mas_of_num[$i]][0][1],"OU=");
				echo "<li class=\"pointer pointer-click\" data-path=\"" .$namemass[$mas_of_num[$i]][0][0]. "\"><span>" .$name. "</span></li>";
				$namemass[$mas_of_num[$i]]["count"] = 0;
			}
			else
				//Если вторая вложенность
				if($namemass[$mas_of_num[$i]]["count"] == 2)			
					echo_optgroup($namemass,$mas_of_num[$i]);
				else
				{	
					//Третья вложенность
					$name = strtok($namemass[$mas_of_num[$i]][0][3],"OU=");
					echo "<li class=\"filial\"><div  class=\"ulblock pointer\"><span class=\"close\">" .$name. "</span></div><ul style=\"display: none;\">";
					$j = $i;
					
					while (!strcasecmp($namemass[$mas_of_num[$i]][0][3],$namemass[$mas_of_num[$j]][0][3]))
					{
						$find = echo_optgroup($namemass,$mas_of_num[$j]);
						$j += $find;
					}
					
					echo "</ul></li>";
					//$j-1 из-за i++
					$i = $j - 1;
				}				
	}

	echo "</ul>";
	
	ldap_close($ldapconn);
}
else
{
    echo "No connect";
}

//Функция вывода списка второй вложенности
function echo_optgroup(&$mas, $i)
{
	$name = strtok($mas[$i][0][2],"OU=");
	echo "<li><div  class=\"ulblock pointer\"><span class=\"close\">" .$name. "</span></div><ul class=\"liblock\" style=\"display: none;\">";	
	$kol_find = 0;
	
	for ($j = 0; $j < count($mas); $j++)
	{
		if(!strcasecmp($mas[$i][0][2],$mas[$j][0][2]))
		{
			$name = strtok($mas[$j][0][1],"OU=");
			echo "<li class=\"pointer pointer-click\" data-path=\"" .$mas[$j][0][0]. "\"><span>" .$name. "</span></li>";
			$mas[$j]["count"] = 0;
			//Для отступа при третей вложенности
			$kol_find++;
		}
	}
	
	echo "</ul></li>";	
	
	return $kol_find;
}