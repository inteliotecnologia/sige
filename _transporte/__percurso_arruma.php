<?
require_once("conexao.php");
if (pode("ey", $_SESSION["permissao"])) {
	
	/*
	$result= mysql_query("select * from tr_percursos_pesagem
						 	where id_empresa = '". $_SESSION["id_empresa"] ."'
							");
	
	while ($rs= mysql_fetch_object($result)) {
		
		$result_insere= mysql_query("insert into tr_percursos_passos
										(id_empresa, id_percurso, passo, id_cliente, id_ad, data_percurso, hora_percurso, 
										 km, peso, pnr, data_registro, hora_registro, id_usuario, migrado)
										values
										('". $rs->id_empresa ."', '". $rs->id_percurso ."', '2', '". $rs->id_cliente ."', '', '". $rs->data_percurso ."', '". hora_percurso ."', 
										 '". $rs->km ."', '". $rs->peso ."', '". $rs->pnr ."', '". $rs->data_registro ."', '". $rs->hora_registro ."', '". $rs->id_usuario ."', '1')
										
										");
		
		echo "Migrando percurso número <strong>". $rs->id_percurso ."</strong><br />" ;
		
	
	
	$result= mysql_query("select * from tr_percursos
								  	where id_empresa = '". $_SESSION["id_empresa"] ."'
									");
	
	while ($rs= mysql_fetch_object($result)) {
		
		$result_teste= mysql_query("select * from tr_percursos_passos
								   	where id_percurso = '". $rs->id_percurso ."'
									order by passo desc limit 1
									");
		$rs_teste= mysql_fetch_object($result_teste);
		
		$result_atualiza= mysql_query("update tr_percursos
										  	set id_situacao_atual= '". $rs_teste->passo ."'
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_percurso = '". $rs->id_percurso ."'
											");
		
		echo "Corrigindo índice de percurso número <strong>". $rs->id_percurso ."</strong> para <strong>". $rs_teste->passo ."</strong><br />" ;
		
	}
	}*/
	
	
	$result_percurso= mysql_query("select * from tr_percursos, tr_percursos_passos
								  where tr_percursos.id_percurso = tr_percursos_passos.id_percurso
								  and   tr_percursos_passos.passo = '1'
								  order by tr_percursos.id_percurso asc
								  ");
	
	while ($rs_percurso= mysql_fetch_object($result_percurso)) {
		
		$data_hora_percurso= $rs_percurso->data_percurso ." ". $rs_percurso->hora_percurso;
		
		echo "Atualizando percurso nº <strong>". $rs_percurso->id_percurso ."</strong>, para a data/hora: <strong>". $data_hora_percurso ."</strong><br />";
		
		$result_atualiza= mysql_query("update tr_percursos
									  	set data_hora_percurso = '". $data_hora_percurso ."'
										where id_percurso = '". $rs_percurso->id_percurso ."'
										");
		
		//echo "Criando timestamp da pesagem nº <strong>". $rs_pesagens->id_pesagem ."</strong>, pesagem data/hora <strong>". $rs_pesagens->data_pesagem ." ". $rs_pesagens->hora_pesagem ."</strong><br />";
		
	}
}
?>