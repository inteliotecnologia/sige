<?
require_once("conexao.php");
if (pode_algum("rh", $_SESSION["permissao"])) {

	$result= mysql_query("select * from rh_afastamentos");
	
	while ($rs= mysql_fetch_object($result)) {
		
		$result_dia= mysql_query("select * from rh_afastamentos_dias
								 	where id_afastamento = '". $rs->id_afastamento ."'
									order by data asc limit 1
									");
		$rs_dia= mysql_fetch_object($result_dia);
		
		$result_atualiza= mysql_query("update rh_afastamentos
									  	set data_inicial = '". $rs_dia->data ."'
										where id_afastamento = '". $rs->id_afastamento ."'
										") or die(mysql_error());
		
		
	}

}
?>