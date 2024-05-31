<?
require_once("conexao.php");
if (pode_algum("rhv4&", $_SESSION["permissao"])) {
	
	
	$result= mysql_query("select * from rh_ponto
							where data_batida >= '2010-10-31'
							");
	
	while ($rs= mysql_fetch_object($result)) {
		
		$result2= mysql_query(" update rh_ponto
								set data_hora_batida = '". $rs->data_batida ." ". $rs->hora ."'
								where id_horario = '". $rs->id_horario ."'
								");
		echo "Atualizando horário ID ". $rs->id_horario ."<br />";
		
	}
	
	echo "<br /><strong>". mysql_num_rows($result) ."</strong>";	
}
?>