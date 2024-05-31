<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("pls", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	$total_operacoes_dia=2;
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	if ( ($_POST["data1"]!="") && ($_POST["data2"]!="") ) {
		$data1= $_POST["data1"];
		$data2= $_POST["data2"];
	}
	else {
		if ( ($_GET["data1"]!="") && ($_GET["data2"]!="") ) {
			$data1= $_GET["data1"];
			$data2= $_GET["data2"];
		}
	}
	
	if ( ($data1!="") && ($data2!="") ) {
		
		$data1f= $data1; $data1= formata_data_hifen($data1);
		$data2f= $data2; $data2= formata_data_hifen($data2);
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
		
		$subtit= $data1f ." à ". $data2f;
	}
	else {
		$periodo2= explode('/', $_POST["periodo"]);
		
		$data1_mk= mktime(22, 0, 0, $periodo2[0], 1, $periodo2[1]);
		$dias_mes= date("t", $data1_mk);
		
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
		$subtit= traduz_mes($periodo2[0]) ."/". $periodo2[1];
	}
	
	if ($_POST["id_cliente"]!="") $str= " and   pessoas.id_pessoa = '". $_POST["id_cliente"] ."'  ";
	
	$result= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
								where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
								and   pessoas_tipos.tipo_pessoa = 'c'
								and   pessoas.status_pessoa = '1'
								and   pessoas.id_cliente_tipo = '1'
								$str
								order by 
								pessoas.apelido_fantasia asc
								") or die(mysql_error());
	
	
	//entrega
	if ($_POST["tipo_relatorio"]=="e") {
		/*require("includes/fpdf/modelo_retrato.php");
		$pdf=new PDF("P", "cm", "A4");
		$pdf->SetMargins(2, 3, 2);
		$pdf->SetAutoPageBreak(true, 3);
		$pdf->SetFillColor(210,210,210);
		$pdf->AddFont('ARIALNARROW');
		$pdf->AddFont('ARIAL_N_NEGRITO');
		$pdf->AddFont('ARIAL_N_ITALICO');
		$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
		$pdf->SetFont('ARIALNARROW');
		
		$i=0;
		
		$pdf->AddPage();
		
		$pdf->SetXY(7,1.75);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "ENTREGAS", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $_POST["data"], 0 , 1, 'R');
	
		$pdf->Ln();$pdf->Ln();
			
			$result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
													where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
													and   pessoas_tipos.tipo_pessoa = 'c'
													and   pessoas.status_pessoa = '1'
													order by 
													pessoas.apelido_fantasia asc
													") or die(mysql_error());
			
			
			$linhas_clientes= mysql_num_rows($result_cli);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
			/*$result_max_coletas= mysql_query("select distinct(tr_percursos_clientes.id_cliente) from tr_percursos_clientes, op_suja_remessas
												 where op_suja_remessas.id_remessa = tr_percursos_clientes.id_remessa
												 and   op_suja_remessas.data_remessa = '". formata_data($_POST["data"]) ."'
												");
			$linhas_max_coletas= mysql_num_rows($result_max_coletas);
			
			
			$pdf->Cell(5, 0.5, "CLIENTE", 1, 0, 1, 1);
			
			for ($e=1; $e<4; $e++) {
				$pdf->Cell(3, 0.5, $e, 1, 0, "C", 1);
			}
			$pdf->Cell(3, 0.5, "TOTAL DIA", 1, 1, "C", 1);
			
			$total_dia= 0;
			$i=0;
			
			while ($rs_cli = mysql_fetch_object($result_cli)) {
				if (($i%2)==0) $fill= 0;
				else $fill= 1;
				
				$total_cliente= 0;
				
				$pdf->SetFillColor(235,235,235);
				$pdf->SetFont('ARIALNARROW', '', 9);
				
				$pdf->Cell(5, 0.5, $rs_cli->sigla, 1, 0, 1, $fill);
				
				for ($e=1; $e<4; $e++) {
					
					$result_entrega= mysql_query("select * from op_pedidos
											where id_empresa = '". $_SESSION["id_empresa"] ."' 
											and   entrega = '$e'
											and   data_pedido = '". formata_data($_POST["data"]) ."'
											and   id_cliente = '". $rs_cli->id_cliente ."'
											");
					$rs_entrega= mysql_fetch_object($result_entrega);
					
					$pdf->Cell(3, 0.5, fnum($rs_entrega->peso_total) ." kg", 1, 0, "C", $fill);
					
					$total_dia+= $rs_entrega->peso_total;
					$total_cliente+= $rs_entrega->peso_total;
				}
				
				$pdf->Cell(3, 0.5, fnum($total_cliente) ." kg", 1, 1, "C", $fill);
				
				$i++;

			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
			$pdf->Cell(5, 0.5, "", 0, 0, "C", $fill);
			$pdf->Cell(3, 0.5, "", 0, 0, "C", $fill);
			$pdf->Cell(3, 0.5, "", 0, 0, "C", $fill);
			$pdf->Cell(3, 0.5, "", 0, 0, "C", $fill);
			$pdf->Cell(3, 0.5, fnum($total_dia) ." kg", 1, 0, "C", $fill);
			
			$pdf->Ln(); $pdf->Ln();
			*/
			
		while ($rs= mysql_fetch_object($result)) {
			
			$total_cliente_periodo_geral= 0;
			$total_empresa_periodo_geral= 0;
			
			$pdf->AddPage();
			
			//echo $rs->apelido_fantasia."<br />";
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, $rs->apelido_fantasia, 0 , 1, 'R');
			
			//$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "ENTREGAS (". $subtit .")", 0 , 1, 'R');
			
			$pdf->Ln();
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
			$pdf->Cell(2, 0.5, "DATA", "LTR", 0, "L", 1);
			$largura= (10/$total_operacoes_dia);
			for ($i=1; $i<=$total_operacoes_dia; $i++) {
				$pdf->Cell($largura, 0.5, $i, "LTR", 0, "C", 1);
			}
			$pdf->Cell(5, 0.5, "TOTAL", "LTR", 1, "C", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 6);
			$pdf->Cell(2, 0.25, "", "LBR", 0, "L", 1);
			$largura_meio= $largura/2;
			for ($i=1; $i<=$total_operacoes_dia; $i++) {
				$pdf->Cell($largura_meio, 0.25, "CLIENTE", "LBR", 0, "C", 1);
				$pdf->Cell($largura_meio, 0.25, "EMPRESA", "LBR", 0, "C", 1);
			}
			$pdf->Cell(2.5, 0.25, "CLIENTE", "LBR", 0, "C", 1);
			$pdf->Cell(2.5, 0.25, "EMPRESA", "LBR", 1, "C", 1);
			
			$diferenca= ceil(($data2_mk-$data1_mk)/86400);
			
			unset($total_cliente_periodo);
			unset($total_empresa_periodo);
			
			for ($d=0; $d<=$diferenca; $d++) {
				$calculo_data= $data1_mk+(86400*$d);
				$data= date("d/m/Y", $calculo_data);
				$data_valida= date("Y-m-d", $calculo_data);
				$id_dia= date("w", $calculo_data);
				
				$amanha= soma_data($data_valida, 1, 0, 0);
				$ontem= soma_data($data_valida, -1, 0, 0);
				$anteontem= soma_data($data_valida, -2, 0, 0);
				
				$total_empresa_dia= 0;
				$total_cliente_dia= 0;
				
				if (($d%2)!=0) $fill=1;
				else $fill= 0;
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->SetFillColor(235,235,235);
				
				$pdf->Cell(2, 0.5, $data, 1, 0, "L", $fill);
				
				$largura_meio= $largura/2;
				for ($i=1; $i<=$total_operacoes_dia; $i++) {
					$j= $i-1;
					
					// -------------------------- cliente
					$result_pesagens_cliente= mysql_query("select * from tr_percursos, tr_percursos_passos
															where tr_percursos_passos.id_cliente = '". $rs->id_cliente ."'
															and   tr_percursos_passos.id_percurso = tr_percursos.id_percurso
															and   tr_percursos_passos.passo = '2'
															and   (tr_percursos.tipo = '2' /* or tr_percursos.tipo = '5' */)
															and   tr_percursos_passos.data_percurso = '". $data_valida ."'
															order by tr_percursos.data_hora_percurso asc
															limit $j, 1
															");
					$rs_pesagens_cliente= mysql_fetch_object($result_pesagens_cliente);
					$linhas_pesagens_cliente= mysql_num_rows($result_pesagens_cliente);
					
					//echo $linhas_pesagens_cliente ."<br />";
					
					if ($linhas_pesagens_cliente>0) {
						$total_cliente_periodo[$i]+= $rs_pesagens_cliente->peso;
						$total_cliente_dia+= $rs_pesagens_cliente->peso;
											
						if ($rs_pesagens_cliente->pnr==1) $peso_cliente_string= "PNR*";
						else $peso_cliente_string= fnum($rs_pesagens_cliente->peso) ."kg";
					}
					else $peso_cliente_string= "-";
					
					
					// --------------------------- empresa
					
					/*
					$result_entrega= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
													where (tr_percursos.tipo = '2' or tr_percursos.tipo = '5')
													and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
													and   tr_percursos_passos.passo = '1'
													and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
													and   tr_percursos_clientes.id_cliente = '". $rs->id_cliente ."'
													and   tr_percursos_passos.data_percurso >= '". $amanha ."'
													order by tr_percursos_passos.data_percurso asc, tr_percursos_passos.hora_percurso asc
													limit $j, 1
													");
					
					$linhas_entrega= mysql_num_rows($result_entrega);
					
					$cont= 1;
					
					//se tem entrega cadastrada
					if ($linhas_entrega>0) {
						while ($rs_entrega= mysql_fetch_object($result_entrega)) {
							
							$data_percurso[$cont]= $rs_entrega->data_percurso;
							$hora_percurso[$cont]= $rs_entrega->hora_percurso;
							
							$cont++;
							
						}
						
						//primeira, pegar as pesagens menores que a data da primeira entrega
						if ($i==1) {
							
							//pegar a ultima entrega do dia ou periodo anterior
							$result_entrega_ultima= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
																where (tr_percursos.tipo = '2' or tr_percursos.tipo = '5')
																and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
																and   tr_percursos_passos.passo = '1'
																and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
																and   tr_percursos_clientes.id_cliente = '". $rs->id_cliente ."'
																and   tr_percursos_passos.data_percurso <= '". $data_valida ."'
																order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
																limit 1
																");
							
							$linhas_entrega_ultima= mysql_num_rows($result_entrega_ultima);
							
							if ($linhas_entrega_ultima>0) {
								$rs_entrega_ultima= mysql_fetch_object($result_entrega_ultima);
								
								$entrega_ultima_data= $rs_entrega_ultima->data_percurso;
								$entrega_ultima_hora= $rs_entrega_ultima->hora_percurso;
							}
							else {
								
								//pegar a primeira COLETA do dia anterior
								$result_coleta_primeira= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
																		where (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
																		and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
																		and   tr_percursos_passos.passo = '1'
																		and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
																		and   tr_percursos_clientes.id_cliente = '". $rs->id_cliente ."'
																		and   tr_percursos_passos.data_percurso < '". $data_valida ."'
																		order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
																		limit 1
																		");
								
								$linhas_coleta_primeira= mysql_num_rows($result_coleta_primeira);
							
								//if ($linhas_coleta_primeira>0) {
									$rs_coleta_primeira= mysql_fetch_object($result_coleta_primeira);
								
									$entrega_ultima_data= $rs_coleta_primeira->data_percurso;
									$entrega_ultima_hora= $rs_coleta_primeira->hora_percurso;
								//}
							}
							
							
							$str_geral = "
										
										and op_limpa_pesagem.data_hora_pesagem < '". $data_percurso[1] ." ". $hora_percurso[1] ."'
										and op_limpa_pesagem.data_hora_pesagem > '". $entrega_ultima_data ." ". $entrega_ultima_hora ."'
										";
										
						}
						//demais, pegar as pesagens com data/hora maiores que a anterior, porém menores que a atual
						else {
							
							//pegar a ultima entrega deste dia sem ser a atual
							$result_entrega_ultima= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
																where (tr_percursos.tipo = '2' or tr_percursos.tipo = '5')
																and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
																and   tr_percursos_passos.passo = '1'
																and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
																and   tr_percursos_clientes.id_cliente = '". $rs->id_cliente ."'
																and   tr_percursos_passos.data_percurso = '". $amanha ."'
																order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
																limit $j, 1
																");
							
							$linhas_entrega_ultima= mysql_num_rows($result_entrega_ultima);
							
							if ($linhas_entrega_ultima>0) {
								$rs_entrega_ultima= mysql_fetch_object($result_entrega_ultima);
								
								$entrega_ultima_data= $rs_entrega_ultima->data_percurso;
								$entrega_ultima_hora= $rs_entrega_ultima->hora_percurso;
								
							}
							
							$str_geral = "
										
										and op_limpa_pesagem.data_hora_pesagem < '". $data_percurso[1] ." ". $hora_percurso[1] ."'
										and op_limpa_pesagem.data_hora_pesagem > '". $entrega_ultima_data ." ". $entrega_ultima_hora ."'	
										";
						}
						
						$result_total= mysql_query("select sum(op_limpa_pesagem.peso) as peso_total from op_limpa_pesagem
													where   op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_limpa_pesagem.id_cliente = '". $rs->id_cliente ."'
													and   extra = '0'
													$str_geral
													");
						
						$rs_total= mysql_fetch_object($result_total);
						
						$total_empresa_periodo[$i]+= $rs_total->peso_total;
						$total_empresa_dia+= $rs_total->peso_total;
						
						$peso_empresa_string= fnum($rs_total->peso_total) ." kg";
						
					}
					else $peso_empresa_string= "-";
					*/
					
					//coleta
					if ($rs->basear_nota_data==1) $data_pedido= $ontem;
					//entrega
					else $data_pedido= $data_valida;
					
					$result_total_empresa= mysql_query("select peso_total from op_pedidos
														where id_cliente= '". $rs->id_cliente ."'
														and   data_pedido = '". $data_pedido ."'
														and   entrega = '". $i ."'
														and   extra = '0'
														and   id_empresa = '". $_SESSION["id_empresa"] ."'
														") or die(mysql_error());
					$rs_total_empresa= mysql_fetch_object($result_total_empresa);
					
					$total_empresa_periodo[$i]+= $rs_total_empresa->peso_total;
					$total_empresa_dia+= $rs_total_empresa->peso_total;
					
					$peso_empresa_string= fnum($rs_total_empresa->peso_total) ." kg";
					
					$pdf->Cell($largura_meio, 0.5, $peso_cliente_string, 1, 0, "C", $fill);
					$pdf->Cell($largura_meio, 0.5, $peso_empresa_string, 1, 0, "C", $fill);
				}
				
				$total_cliente_periodo_geral+= $total_cliente_dia;
				$total_empresa_periodo_geral+= $total_empresa_dia;
				
				$pdf->Cell(2.5, 0.5, fnum($total_cliente_dia) ." kg", 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.5, fnum($total_empresa_dia) ." kg", 1, 1, "C", $fill);
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$pdf->Cell(2, 0.5, "TOTAL", 1, 0, "L", !$fill);
			
			$largura_meio= $largura/2;
			for ($i=1; $i<=$total_operacoes_dia; $i++) {
				$pdf->Cell($largura_meio, 0.5, fnum($total_cliente_periodo[$i]) ." kg", 1, 0, "C", !$fill);
				$pdf->Cell($largura_meio, 0.5, fnum($total_empresa_periodo[$i]) ." kg", 1, 0, "C", !$fill);
			}
			
			$pdf->Cell(2.5, 0.5, fnum($total_cliente_periodo_geral) ." kg", 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.5, fnum($total_empresa_periodo_geral) ." kg", 1, 1, "C", !$fill);
		}
			
			
	}//fim entregas
	
	//coletas
	else {
		
		while ($rs= mysql_fetch_object($result)) {
			
			$total_cliente_periodo_geral= 0;
			$total_empresa_periodo_geral= 0;
			
			$pdf->AddPage();
			
			//echo $rs->apelido_fantasia."<br />";
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, $rs->apelido_fantasia, 0 , 1, 'R');
			
			//$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "COLETAS (". $subtit .")", 0 , 1, 'R');
			
			$pdf->Ln();
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
			$pdf->Cell(2, 0.5, "DATA", "LTR", 0, "L", 1);
			$largura= (10/$total_operacoes_dia);
			for ($i=1; $i<=$total_operacoes_dia; $i++) {
				$pdf->Cell($largura, 0.5, $i, "LTR", 0, "C", 1);
			}
			$pdf->Cell(5, 0.5, "TOTAL", "LTR", 1, "C", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 6);
			$pdf->Cell(2, 0.25, "", "LBR", 0, "L", 1);
			$largura_meio= $largura/2;
			for ($i=1; $i<=$total_operacoes_dia; $i++) {
				$pdf->Cell($largura_meio, 0.25, "CLIENTE", "LBR", 0, "C", 1);
				$pdf->Cell($largura_meio, 0.25, "EMPRESA", "LBR", 0, "C", 1);
			}
			$pdf->Cell(2.5, 0.25, "CLIENTE", "LBR", 0, "C", 1);
			$pdf->Cell(2.5, 0.25, "EMPRESA", "LBR", 1, "C", 1);
			
			$diferenca= ceil(($data2_mk-$data1_mk)/86400);
			
			unset($total_cliente_periodo);
			unset($total_empresa_periodo);
			
			for ($d=0; $d<=$diferenca; $d++) {
				$calculo_data= $data1_mk+(86400*$d);
				$data= date("d/m/Y", $calculo_data);
				$data_valida= date("Y-m-d", $calculo_data);
				$id_dia= date("w", $calculo_data);
				
				$total_empresa_dia= 0;
				$total_cliente_dia= 0;
				
				if (($d%2)!=0) $fill=1;
				else $fill= 0;
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->SetFillColor(235,235,235);
				
				$pdf->Cell(2, 0.5, $data, 1, 0, "L", $fill);
				
				$largura_meio= $largura/2;
				for ($i=1; $i<=$total_operacoes_dia; $i++) {
					$j= $i-1;
					
					// -------------------------- cliente
					$result_pesagens_cliente= mysql_query("select * from tr_percursos, tr_percursos_passos
															where tr_percursos_passos.id_cliente = '". $rs->id_cliente ."'
															and   tr_percursos_passos.id_percurso = tr_percursos.id_percurso
															and   tr_percursos_passos.passo = '2'
															and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
															and   tr_percursos_passos.data_percurso = '". $data_valida ."'
															order by tr_percursos.data_hora_percurso asc
															limit $j, 1
															");
					
					$rs_pesagens_cliente= mysql_fetch_object($result_pesagens_cliente);
					
					$total_cliente_periodo[$i]+= $rs_pesagens_cliente->peso;
					$total_cliente_dia+= $rs_pesagens_cliente->peso;
					
					if ($rs_pesagens_cliente->pnr==1) $peso_cliente_string= "PNR*";
					else $peso_cliente_string= fnum($rs_pesagens_cliente->peso) ."kg";
					
					/*if (($data_valida=="2010-02-05") && ($_SESSION["id_usuario"]==13)) {
						echo "select * from tr_percursos, tr_percursos_clientes, tr_percursos_passos, op_suja_remessas
													where tr_percursos.id_percurso = op_suja_remessas.id_percurso
													and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
													and   tr_percursos_clientes.id_cliente = '". $rs->id_cliente ."'
													and   tr_percursos_passos.id_percurso = tr_percursos.id_percurso
													and   tr_percursos_passos.passo = '1'
													and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
													and   op_suja_remessas.data_remessa = '". $data_valida ."'
													limit $j, 1
													
													<br /><br />
													";
													
													//die();
					}*/
					
					// -------------------------- empresa
					
					$result_coleta= mysql_query("select * from tr_percursos, tr_percursos_clientes, tr_percursos_passos, op_suja_remessas
													where tr_percursos.id_percurso = op_suja_remessas.id_percurso
													and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
													and   tr_percursos_clientes.id_cliente = '". $rs->id_cliente ."'
													and   tr_percursos_passos.id_percurso = tr_percursos.id_percurso
													and   tr_percursos_passos.passo = '1'
													and   (tr_percursos.tipo = '1' /*or tr_percursos.tipo = '4'*/ )
													and   op_suja_remessas.data_remessa = '". $data_valida ."'
													order by tr_percursos.data_hora_percurso asc
													limit $j, 1
													");
					$linhas_coleta= mysql_num_rows($result_coleta);
					$rs_coleta= mysql_fetch_object($result_coleta);
					
					$result_pesagens_empresa= mysql_query("select sum(peso) as peso_total from op_suja_pesagem
															where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
															and   op_suja_pesagem.id_cliente = '". $rs->id_cliente ."'
															and   op_suja_pesagem.id_remessa = '". $rs_coleta->id_remessa ."'
															");
					$rs_pesagens_empresa= mysql_fetch_object($result_pesagens_empresa);
					
					$total_empresa_periodo[$i]+= $rs_pesagens_empresa->peso_total;
					$total_empresa_dia+= $rs_pesagens_empresa->peso_total;
					
					$pdf->Cell($largura_meio, 0.5, $peso_cliente_string, 1, 0, "C", $fill);
					$pdf->Cell($largura_meio, 0.5, fnum($rs_pesagens_empresa->peso_total) ." kg", 1, 0, "C", $fill);
				}
				
				$total_cliente_periodo_geral+= $total_cliente_dia;
				$total_empresa_periodo_geral+= $total_empresa_dia;
				
				if ($total_cliente_dia!=0) $total_cliente_dia_string= fnum($total_cliente_dia) ." kg";
				else $total_cliente_dia_string= "-";
				
				$pdf->Cell(2.5, 0.5, $total_cliente_dia_string, 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.5, fnum($total_empresa_dia) ." kg", 1, 1, "C", $fill);
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$pdf->Cell(2, 0.5, "TOTAL", 1, 0, "L", !$fill);
			
			$largura_meio= $largura/2;
			for ($i=1; $i<=$total_operacoes_dia; $i++) {
				$pdf->Cell($largura_meio, 0.5, fnum($total_cliente_periodo[$i]) ." kg", 1, 0, "C", !$fill);
				$pdf->Cell($largura_meio, 0.5, fnum($total_empresa_periodo[$i]) ." kg", 1, 0, "C", !$fill);
			}
			
			$pdf->Cell(2.5, 0.5, fnum($total_cliente_periodo_geral) ." kg", 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.5, fnum($total_empresa_periodo_geral) ." kg", 1, 1, "C", !$fill);
		}
	}//fim coletas
	
	$pdf->SetFont('ARIALNARROW', '', 8);
	$pdf->Cell(0, 0.8, "* PNR = Peso não registrado", 0, 1, "L", 0);
	
	$pdf->AliasNbPages(); 
	$pdf->Output("entrega_coleta_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>