<?
if (pode_algum("ps", $_SESSION["permissao"])) {

	$result= mysql_query("select *, DATE_FORMAT(data_inicio_separacao, '%d') as dia_inicio_separacao2,
							DATE_FORMAT(data_inicio_separacao, '%m') as mes_inicio_separacao2,
							DATE_FORMAT(data_inicio_separacao, '%Y') as ano_inicio_separacao2,
							
							DATE_FORMAT(hora_inicio_separacao, '%H') as hora_inicio_separacao2,
							DATE_FORMAT(hora_inicio_separacao, '%i') as minuto_inicio_separacao2,
							DATE_FORMAT(hora_inicio_separacao, '%s') as segundo_inicio_separacao2,
							
							DATE_FORMAT(data_fim_separacao, '%d') as dia_fim_separacao2,
							DATE_FORMAT(data_fim_separacao, '%m') as mes_fim_separacao2,
							DATE_FORMAT(data_fim_separacao, '%Y') as ano_fim_separacao2,
							
							DATE_FORMAT(hora_fim_separacao, '%H') as hora_fim_separacao2,
							DATE_FORMAT(hora_fim_separacao, '%i') as minuto_fim_separacao2,
							DATE_FORMAT(hora_fim_separacao, '%s') as segundo_fim_separacao2
							
							from op_suja_remessas
							where op_suja_remessas.id_empresa = '". $_SESSION["id_empresa"] ."' 
							");
	
	$periodo2= explode("/", $periodo);

	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		
		$id_separacao="";
		
		if (($rs->data_inicio_separacao!="0000-00-00") && ($rs->hora_inicio_separacao!="00:00:00")) {
			$result_insere1= mysql_query("insert into op_suja_remessas_separacoes
											(id_empresa, id_remessa, data_separacao, hora_separacao, tipo_separacao, id_usuario)
											values
											('1', '". $rs->id_remessa ."', '". $rs->data_inicio_separacao ."',
											'". $rs->hora_inicio_separacao ."', '1', '". $rs->id_usuario ."')
											");
			$id_separacao= mysql_insert_id();
		}
		
		if (($rs->data_inicio_separacao!="0000-00-00") && ($rs->hora_inicio_separacao!="00:00:00")) {
			$result_insere1= mysql_query("insert into op_suja_remessas_separacoes
											(id_empresa, id_remessa, data_separacao, hora_separacao, tipo_separacao, id_separacao_fecha, id_usuario)
											values
											('1', '". $rs->id_remessa ."', '". $rs->data_fim_separacao ."',
											'". $rs->hora_fim_separacao ."', '0', '". $id_separacao ."', '". $rs->id_usuario ."')
											");
		}
		
		echo $i. ") Remessa ". $rs->id_remessa ." convertida... <br />";
		
		$i++;
	}
}
?>