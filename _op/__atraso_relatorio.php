<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("pl", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");

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
	}
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 1.5, 2);
	$pdf->SetAutoPageBreak(true, 2.5);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	//$periodo= explode("/", $_POST["periodo"]);
	//$periodo_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
	
	//$total_dias_mes= date("t", $periodo_mk);

	$pdf->AddPage();
	
	$total_geral_periodo= 0;
	
	$pdf->SetXY(7, 1.75);
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "ATRASOS", 0, 1, "R");
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "ENTRE ". $data1f ." E ". $data2f, 0, 1, "R");
	
	$pdf->Ln();
	
	for ($p=0; $p<3; $p++) {
		
		if ($p==0) $tit_sessao= "ROUPA SUJA";
		elseif ($p==1) {
			$tit_sessao= "RESÍDUOS";
		}
		else {
			$tit_sessao= "ROUPA LIMPA";
			
			$data1= soma_data($data1, 1, 0, 0);
			$data2= soma_data($data2, 1, 0, 0);
			
			$data1f= desformata_data($data1);
			$data2f= desformata_data($data2);
			
			$data1_mk= faz_mk_data($data1);
			$data2_mk= faz_mk_data($data2);
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $tit_sessao ." (kg)", 'B', 1, 'L', 0);
		$pdf->Cell(0, 0.2, "", 0, 1);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(1.4, 0.6, "DATA", 1, 0, 'L', 1);
		
		$total_clientes= count($_POST["id_cliente"]);
		$largura= 14.1/$total_clientes;
		
		$i=0;
		while ($_POST["id_cliente"][$i]!="") {
			
			$pdf->Cell($largura, 0.6, pega_sigla_pessoa($_POST["id_cliente"][$i]), 1, 0, 'C', 1);
			
			$i++;
		}
		
		$pdf->Cell(1.5, 0.6, "TOTAL", 1, 1, 'C', 1);
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(235,235,235);
		
		$diferenca= ceil(($data2_mk-$data1_mk)/86400);
		
		for ($d=0; $d<=$diferenca; $d++) {
			$calculo_data= $data1_mk+(86400*$d);
			$data= date("d/m/Y", $calculo_data);
			$data_valida= date("Y-m-d", $calculo_data);
			$id_dia= date("w", $calculo_data);
			
			$amanha= soma_data($data_valida, 1, 0, 0);
			$ontem= soma_data($data_valida, -1, 0, 0);
			$anteontem= soma_data($data_valida, -2, 0, 0);
			
			if ($rs->basear_nota_data==1) $data_valida_mesmo= $data_valida;
			else $data_valida_mesmo= $data_valida;
			
			$total_empresa_dia= 0;
			$total_cliente_dia= 0;
			
			if (($d%2)!=0) $fill=1;
			else $fill= 0;
			
			$pdf->SetFont('ARIALNARROW', '', 7);
			
			$pdf->Cell(1.4, 0.5, $data, 1, 0, "L", $fill);
			
			$i=0;
			while ($_POST["id_cliente"][$i]!="") {
				
				$result_cliente= mysql_query("select * from pessoas
												where id_pessoa = '". $_POST["id_cliente"][$i] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												");
				$rs_cliente= mysql_fetch_object($result_cliente);
				
				//suja
				if ($p==0) {
				
					$result_pesagens_empresa= mysql_query("select sum(peso) as peso_total from op_suja_pesagem, op_suja_remessas
															where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
															and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
															and   op_suja_remessas.data_remessa= '". $data_valida ."'
															and   op_suja_pesagem.id_cliente = '". $_POST["id_cliente"][$i] ."'
															");
					$rs_pesagens_empresa= mysql_fetch_object($result_pesagens_empresa);
					
					$peso_aqui[$p][$i]= $rs_pesagens_empresa->peso_total;
					
					//$peso_suja[$d]= $peso_aqui;
					
					$total_peso_cliente[$p][$i]+= $peso_aqui[$p][$i];
					$total_peso_dia[$p][$d]+= $peso_aqui[$p][$i];
					
					$total_peso_geral[$p]+= $peso_aqui[$p][$i];
				}
				//residuos
				elseif ($p==1) {
					
					$result_residuos= mysql_query("select sum(op_suja_devolucao.peso) as total_residuos from op_suja_devolucao, op_suja_remessas
														where op_suja_devolucao.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   op_suja_devolucao.id_remessa = op_suja_remessas.id_remessa
														and   op_suja_remessas.data_remessa = '". $data_valida ."'
														and   op_suja_devolucao.id_cliente = '". $_POST["id_cliente"][$i] ."'
														");
					$rs_residuos= mysql_fetch_object($result_residuos);
					
					$peso_aqui[$p][$i]= $rs_residuos->total_residuos;
					
					$total_peso_cliente[$p][$i]+= $peso_aqui[$p][$i];
					$total_peso_dia[$p][$d]+= $peso_aqui[$p][$i];
					
					$total_peso_geral[$p]+= $peso_aqui[$p][$i];
					
				}
				//limpa
				else {
					
					
					/*
					// ---------------- PESAGEM CLIENTE LIMPA
					$result_pesagens_cliente_limpa= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
																where tr_percursos_passos.id_cliente = '". $_POST["id_cliente"][$i] ."'
																and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
																and   tr_percursos_clientes.id_cliente = tr_percursos_passos.id_cliente
																and   tr_percursos_passos.id_percurso = tr_percursos.id_percurso
																and   tr_percursos_passos.passo = '2'
																and   (tr_percursos.tipo = '2' or tr_percursos.tipo = '5' )
																and   tr_percursos_passos.data_percurso = '". $data_valida ."'
																order by tr_percursos_passos.data_percurso asc, tr_percursos_passos.hora_percurso asc
																");
					
					$peso_cliente_limpa_aqui= 0;
					$entrega=1;
					$asterisco= "";
					
					while ($rs_pesagens_cliente_limpa= mysql_fetch_object($result_pesagens_cliente_limpa)) {
						//if ($rs_pesagens_cliente_limpa->pnr==1) {
							
							// ----------------------------------------------------------------------------------------------------------
							
							$result_entrega= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
															where (tr_percursos.tipo = '2' or tr_percursos.tipo = '5' )
															and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
															and   tr_percursos_passos.passo = '1'
															and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
															and   tr_percursos_clientes.id_cliente = '". $_POST["id_cliente"][$i] ."'
															and   tr_percursos_passos.data_percurso = '". $data_valida ."'
															order by tr_percursos_passos.data_percurso asc, tr_percursos_passos.hora_percurso asc
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
								if ($entrega==1) {
									
									//pegar a ultima entrega do dia anterior
									$result_entrega_ultima= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
																		where (tr_percursos.tipo = '2' )
																		and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
																		and   tr_percursos_passos.passo = '1'
																		and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
																		and   tr_percursos_clientes.id_cliente = '". $_POST["id_cliente"][$i] ."'
																		and   tr_percursos_passos.data_percurso = '". $ontem ."'
																		order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
																		limit 1
																		");
									
									$linhas_entrega_ultima= mysql_num_rows($result_entrega_ultima);
									$rs_entrega_ultima= mysql_fetch_object($result_entrega_ultima);
									
									if ($linhas_entrega_ultima>0)
										$str_geral = "
													and op_limpa_pesagem.data_hora_pesagem < '". $data_percurso[1] ." ". $hora_percurso[1] ."'
													and op_limpa_pesagem.data_hora_pesagem > '". $rs_entrega_ultima->data_percurso ." ". $rs_entrega_ultima->hora_percurso ."'
													";
									else
										$str_geral = "
													and op_limpa_pesagem.data_hora_pesagem < '". $data_percurso[1] ." ". $hora_percurso[1] ."'
													and op_limpa_pesagem.data_hora_pesagem > '". $data1 ." 00:00:00'
													";
												
								}
								//demais, pegar as pesagens com data/hora maiores que a anterior, porém menores que a atual
								else {
									
									$str_geral = "
												and op_limpa_pesagem.data_hora_pesagem < '". $data_percurso[$entrega] ." ". $hora_percurso[$entrega] ."'
												and op_limpa_pesagem.data_hora_pesagem > '". $data_percurso[$entrega-1] ." ". $hora_percurso[$entrega-1] ."'	
												";
								}
								
								$result_total= mysql_query("select sum(op_limpa_pesagem.peso) as peso_total from op_limpa_pesagem
															where   op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_limpa_pesagem.id_cliente = '". $_POST["id_cliente"][$i] ."'
															and   extra = '0'
															$str_geral
															") or die(mysql_error());
								
								$rs_total= mysql_fetch_object($result_total);
								
								$peso_cliente_limpa_aqui+= $rs_total->peso_total;
								
							}
							
							// -----------------------------------------------------------------------------------
							
							//$total_cliente[2]+= $rs_total->peso_total;
							//$peso_cliente_limpa_aqui+= $rs_total->peso_total;
							
							//$asterisco.="*". $entrega ." ";
						//}
						//else {
						//	$total_cliente[2]+= $rs_pesagens_cliente_limpa->peso;
						//	$peso_cliente_limpa_aqui+= $rs_pesagens_cliente_limpa->peso;
						//}
						
						$entrega++;
					}
					
					*/
					
					//coleta
					if ($rs_cliente->basear_nota_data==1) $data_pedido= $ontem;
					//entrega
					else $data_pedido= $data_valida;
					
					$result_nota= mysql_query("select sum(peso_total) as peso_total from op_pedidos
												where id_cliente= '". $_POST["id_cliente"][$i] ."'
												and   data_pedido = '". $data_pedido ."' 
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												") or die(mysql_error());
					$rs_nota= mysql_fetch_object($result_nota);
					
					$peso_cliente_limpa_aqui= $rs_nota->peso_total;
					
					$peso_aqui[$p][$i]= $peso_cliente_limpa_aqui;
					
					$total_peso_cliente[$p][$i]+= $peso_aqui[$p][$i];
					$total_peso_dia[$p][$d]+= $peso_aqui[$p][$i];
					
					$total_peso_geral[$p]+= $peso_aqui[$p][$i];
					
					
					
					
					
				}
				
				$pdf->Cell($largura, 0.5, fnum($peso_aqui[$p][$i]), 1, 0, 'C', $fill);
				
				$i++;
			}
			
			$pdf->Cell(1.5, 0.5, fnum($total_peso_dia[$p][$d]), 1, 1, "C", $fill);
			
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->SetFillColor(210,210,210);
		
		$pdf->Cell(1.4, 0.5, "", 0, 0, "L", 0);
		
		$i=0;
		while ($_POST["id_cliente"][$i]!="") {
			
			$pdf->Cell($largura, 0.5, fnum($total_peso_cliente[$p][$i]), 1, 0, 'C', 1);
			
			$i++;
		}
		
		$pdf->Cell(1.5, 0.5, fnum($total_peso_geral[$p]) ."kg", 1, 1, "C", 1);
		$pdf->Ln();
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "DIFERENÇA", 'B', 1, 'L', 0);
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
	$pdf->Cell(2, 0.6, "CLIENTE", 1, 0, 'L', 1);
	$pdf->Cell(3, 0.6, "ROUPA SUJA", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.6, "RESÍDUOS", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.6, "ROUPA LIMPA", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.6, "QUEDA DE SUJIDADE", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.6, "DIFERENÇA", 1, 1, 'C', 1);
	
	//$total_clientes= count($_POST["id_cliente"]);
	//$largura= 12.5/$total_clientes;
	
	$pdf->SetFont('ARIALNARROW', '', 7);
	$pdf->SetFillColor(235,235,235);
	
	$queda_soma=0;
	
	$i=0;
	while ($_POST["id_cliente"][$i]!="") {
		
		if (($i%2)!=0) $fill=1;
		else $fill= 0;
		
		$diferenca_peso[$i]= $total_peso_cliente[0][$i]-$total_peso_cliente[2][$i];
		
		if ($total_peso_cliente[0][$i]>0) $queda_sujidade[$i]= (($diferenca_peso[$i]-$total_peso_cliente[1][$i])*100)/$total_peso_cliente[0][$i];
		else $queda_sujidade[$i]=0;
		
		$queda_soma+=$queda_sujidade[$i];
		
		$diferenca_total+= $diferenca_peso[$i];
		
		$pdf->Cell(2, 0.5, pega_sigla_pessoa($_POST["id_cliente"][$i]), 1, 0, 'L', $fill);
		$pdf->Cell(3, 0.5, fnum($total_peso_cliente[0][$i]) ."kg", 1, 0, 'C', $fill);
		$pdf->Cell(3, 0.5, fnum($total_peso_cliente[1][$i]) ."kg", 1, 0, 'C', $fill);
		$pdf->Cell(3, 0.5, fnum($total_peso_cliente[2][$i]) ."kg", 1, 0, 'C', $fill);
		$pdf->Cell(3, 0.5, fnum($queda_sujidade[$i]) ."%", 1, 0, 'C', $fill);
		$pdf->Cell(3, 0.5, fnum($diferenca_peso[$i]) ."kg", 1, 1, 'C', $fill);
		
		$i++;
	}
	if ($i>0) $queda_media=$queda_soma/$i;
	else $queda_media=0;
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
	$pdf->SetFillColor(210,210,210);
	
	$diferenca_geral= $total_peso_geral[0]-$total_peso_geral[2];
	
	$pdf->Cell(2, 0.5, "", 0, 0, 'C', 0);
	$pdf->Cell(3, 0.5, fnum($total_peso_geral[0]) ."kg", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.5, fnum($total_peso_geral[1]) ."kg", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.5, fnum($total_peso_geral[2]) ."kg", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.5, "MÉDIA: ". fnum($queda_media) ."%", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.5, fnum($diferenca_geral) ."kg", 1, 1, 'C', 1);
	
	$pdf->AliasNbPages(); 
	$pdf->Output("atraso_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>