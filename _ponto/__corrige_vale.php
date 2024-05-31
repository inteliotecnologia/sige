<?
require_once("conexao_ponto.php");

$result= mysql_query("select * from rh_ponto order by id_horario desc");

if (mysql_num_rows($result)==0) echo "nada encontrado";
/*
while ($rs= mysql_fetch_object($result)) {
	
	if ($rs->vale_dia=="802008") {
		$valedia= "2008-04-08";
		$result2= mysql_query("update rh_ponto set vale_dia = '". $valedia ."' where id_horario = '". $rs->id_horario ."' ");
	}
	if ($rs->vale_dia=="403208") {
		$valedia= "2008-03-04";
		$result2= mysql_query("update rh_ponto set vale_dia = '". $valedia ."' where id_horario = '". $rs->id_horario ."' ");
	}
	
	if (strlen($rs->vale_dia)==7) {
		$valedia_ano= substr($rs->vale_dia, 3, 4);
		$valedia_mes= substr($rs->vale_dia, 1, 2);
		$valedia_dia= substr($rs->vale_dia, 0, 1);
		
		$valedia= $valedia_ano .'-'. $valedia_mes .'-0'. $valedia_dia;
		
		echo $valedia ."<br />";
		$result2= mysql_query("update rh_ponto set vale_dia = '". $valedia ."' where id_horario = '". $rs->id_horario ."' ");
		
	}
	else {
		if (strlen($rs->vale_dia)==5) {
			$valedia_ano= substr($rs->vale_dia, 1, 4);
			$valedia_mes= substr($rs->vale_dia, 0, 1);
			$valedia_dia= substr($rs->vale_dia, 0, 1);
			
			$valedia= $valedia_ano .'-0'. $valedia_mes .'-01';//. $valedia_dia;
			
			echo $valedia ."<br />";
			$result2= mysql_query("update rh_ponto set vale_dia = '". $valedia ."' where id_horario = '". $rs->id_horario ."' ");
		}
		if (strlen($rs->vale_dia)==8) {
			$valedia_ano= substr($rs->vale_dia, 4, 4);
			$valedia_mes= substr($rs->vale_dia, 2, 2);
			$valedia_dia= substr($rs->vale_dia, 0, 2);
			
			$valedia= $valedia_ano .'-'. $valedia_mes .'-'. $valedia_dia;
			
			echo $valedia ."<br />";
			$result2= mysql_query("update rh_ponto set vale_dia = '". $valedia ."' where id_horario = '". $rs->id_horario ."' ");
		}
	}
	
	
	
	//
	
}
*/
?>