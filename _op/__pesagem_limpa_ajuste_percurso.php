<?
if (pode_algum("psl", $_SESSION["permissao"])) {
	
	//$periodo= explode('/', "08/2011");
	
	$marcador_inicial = microtime(1);
	
	for ($q=1; $q<2; $q++) {
		
		$data1_mk= mktime(14, 0, 0, 11, 10, 2011);
		
		$dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, 11, 15, 2011);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
	    $diferenca_tit= date("d", $data2_mk-$data1_mk);
	    
	    for ($w=0; $w<2; $w++) {
	    	
	    	switch($w) {
	    		case 0:
	    		$str= " and   ( tr_percursos.tipo= '1' or tr_percursos.tipo= '4' ) ";
	    		$tit="----------------------<BR> COLETAS";
	    		break;
	    		
	    		case 1:
	    		$str= " and   ( tr_percursos.tipo= '2' or tr_percursos.tipo= '5' ) ";
	    		$tit="----------------------<BR> ENTREGAS";
	    		break;
	    		
	    		case 2:
	    		$str= " and   ( tr_percursos.tipo= '3' ) ";
	    		$tit="----------------------<BR> OUTROS";
	    		break;
	    		
	    	}
	    	
	    	echo $tit ."<br><br>";
	    	
		    //repetir todos os dias do intervalo
		    for ($t=0; $t<$diferenca_tit; $t++) {
				$calculo_data_tit= $data1_mk+(86400*$t);
				
				$dia_tit= date("d", $calculo_data_tit);
				$dia_semana_tit= date("w", $calculo_data_tit);
				$vale_dia= date("Y-m-d", $calculo_data_tit);
				
				echo "<b>". $vale_dia ."</b><br>";
				
				$result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
		                                    where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
		                                    and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
		                                    and   pessoas_tipos.tipo_pessoa = 'c'
											and   pessoas.id_cliente_tipo = '1'
		                                    order by pessoas.nome_rz asc
		                                    ") or die("2: ". mysql_error());
				
				while ($rs_cli= mysql_fetch_object($result_cli)) {
					
					$result= mysql_query("select * from  tr_percursos, tr_percursos_clientes
											where DATE_FORMAT(data_hora_percurso, '%Y-%m-%d') = '". $vale_dia ."'
											and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
											and   tr_percursos_clientes.id_cliente = '". $rs_cli->id_cliente ."'
											$str
											order by tr_percursos.data_hora_percurso asc
											") or die("3: ". mysql_error());
					
					
					$i=1;
					$normal=1;
					$extra=1;
					$outros=1;
					
					$linhas_percursos= mysql_num_rows($result);
					
					if ($linhas_percursos>0) {
					
						echo $rs_cli->apelido_fantasia ."<br><br>";
						
						while ($rs= mysql_fetch_object($result)) {
							echo "Ajustando percurso: ". number_format($rs->id_percurso_cliente, 0, ',', '.') ." ". $rs->data_hora_percurso ." ---> ". $i ."<br>";
							
							if ( ($rs->tipo=="1") || ($rs->tipo=="2") ) {
								$num_percurso_tipo= $normal;
							}
							if ( ($rs->tipo=="4") || ($rs->tipo=="5") ) {
								$num_percurso_tipo= $extra;
							}
							else {
								$num_percurso_tipo= $outros;
							}
							
							$result_atualiza= mysql_query("update tr_percursos_clientes
															set num_percurso = '". $i ."',
															num_percurso_tipo = '". $num_percurso_tipo ."'
															where id_percurso_cliente = '". $rs->id_percurso_cliente ."'
															") or die("4: ". mysql_error());
							/*$result_clientes= mysql_query("select * from tr_percursos_clientes
															where id_percurso = '". $rs->id_percurso ."'
															");
							*/
							
							if ( ($rs->tipo=="1") || ($rs->tipo=="2") ) {
								$normal++;
							}
							if ( ($rs->tipo=="4") || ($rs->tipo=="5") ) {
								$extra++;
							}
							else {
								$outros++;
							}
							
							$i++;
						}
						
						echo "<br><br>";
					}
				}
					
			}//fim for	
		
		}//fim for tipos
	
	}//fim for meses
	
	$marcador_final= microtime(1);
		$tempo_execucao = $marcador_final - $marcador_inicial;
		echo "<br /><br />Tempo para execução: <b>" .sprintf ( "%02.3f", $tempo_execucao ). "</b> segundos. <br>";
	
}
?>