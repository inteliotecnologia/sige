<?
require_once("conexao.php");
if (pode("pl", $_SESSION["permissao"])) {
	
	$result_pesagens= mysql_query("select * from op_limpa_pesagem
								  
								  ");
	
	while ($rs_pesagens= mysql_fetch_object($result_pesagens)) {
		/*
		$result_insere= mysql_query("insert into op_limpa_pesagem_pecas
										(id_empresa, id_pesagem, id_tipo_roupa, num_pacotes, qtde_pacote, qtde_pecas_sobra, id_usuario)
										values
										('". $rs_pesagens->id_empresa ."', '". $rs_pesagens->id_pesagem ."',
										
										'". $rs_pesagens->id_tipo_roupa ."', '". $rs_pesagens->num_pacotes ."',
										'". $rs_pesagens->qtde_pacote ."', '". $rs_pesagens->qtde_pecas_sobra ."',
										
										'". $rs_pesagens->id_usuario ."'
										
										)
										
										") or die(mysql_error());
		
		$id_grupo= pega_id_grupo_da_peca($rs_pesagens->id_tipo_roupa);
		
		$result_atualiza= mysql_query("update op_limpa_pesagem
									  	set id_grupo = '$id_grupo'
										where id_pesagem = '". $rs_pesagens->id_pesagem ."'
										");
		
		echo "Migrando pesagem nº <strong>". $rs_pesagens->id_pesagem ."</strong>, grupo <strong>". $id_grupo ."</strong><br />";
		*/
		
		$result_atualiza= mysql_query("update op_limpa_pesagem
									  	set data_hora_pesagem = '". $rs_pesagens->data_pesagem ." ". $rs_pesagens->hora_pesagem ."'
										where id_pesagem = '". $rs_pesagens->id_pesagem ."'
										");
		
		//echo "Criando timestamp da pesagem nº <strong>". $rs_pesagens->id_pesagem ."</strong>, pesagem data/hora <strong>". $rs_pesagens->data_pesagem ." ". $rs_pesagens->hora_pesagem ."</strong><br />";
		
	}
	
}
?>