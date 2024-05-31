<?
//require_once("conexao.php");
//require_once("funcoes.php");

if (pode("pls", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 2);
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
		
		$str_periodo= " and   op_suja_remessas.data_remessa >= '". $data1 ."' and op_suja_remessas.data_remessa <= '". $data2 ."' ";
		
		$competencia= $data1f ." à ". $data2f;
	}
	else {
		$periodo2= explode('/', $_POST["periodo"]);
		
		if ($_POST["periodo"]=="09/2009") $dia_inicio=11;
		else $dia_inicio= 1;
		
		$data1_mk= mktime(14, 0, 0, $periodo2[0], $dia_inicio, $periodo2[1]);
		$dias_mes= date("t", $data1_mk);
		
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
		$str_periodo= " and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". $_POST["periodo"] ."' ";
		
		$competencia= traduz_mes($periodo2[0]) ."/". $periodo2[1];
	}
		
	$result_lavagem_total= mysql_query("select sum(op_suja_lavagem_cestos.peso) as peso_total
										from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
										where op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
										$str_periodo
										and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
										");
	$rs_lavagem_total= mysql_fetch_object($result_lavagem_total);
	
	$result_relave= mysql_query("select sum(op_suja_lavagem_cestos.peso) as peso_total
									from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas, op_equipamentos_processos
									where op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
									$str_periodo
									and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
									and   op_suja_lavagem.id_processo = op_equipamentos_processos.id_processo
									and   op_equipamentos_processos.relave = '1'
									");
	$rs_relave= mysql_fetch_object($result_relave);
	
	$pdf->Ln();
	
	if ($rs_lavagem_total->peso_total>0)
		$percent_relave= (100*$rs_relave->peso_total)/$rs_lavagem_total->peso_total;
	
	
	
	//---------------------------------------------
	
	if ($_POST["id_contrato"]!="") $str= " and   pessoas.id_contrato= '". $_POST["id_contrato"] ."'  ";
	if ($_POST["id_cliente"]!="") $str= " and   pessoas.id_pessoa= '". $_POST["id_cliente"] ."'  ";
	
	$result= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos, fi_contratos
								where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
								and   pessoas_tipos.tipo_pessoa = 'c'
								/* and   pessoas.status_pessoa = '1' */
								and   pessoas.id_cliente_tipo = '1'
								and   pessoas.id_contrato = fi_contratos.id_contrato
								$str
								order by 
								pessoas.apelido_fantasia asc
								") or die(mysql_error());
	
	$ic=0;
	
	while ($rs= mysql_fetch_object($result)) {
		
		
		
		//particular
		if ($rs->tipo_contrato==0) {
			
			$total_cliente_periodo_geral= 0;
			
			$pdf->AddPage();
			
			//echo $rs->apelido_fantasia."<br />";
			
			if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg"))
				$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 2, 1.3, 5, 1.9287);
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
			$pdf->Cell(0, 0.5, "MOVIMENTAÇÃO DO QUANTITATIVO DE ROUPA", 0 , 1, 'R');
			
			$pdf->Cell(0, 0.5, $rs->apelido_fantasia, 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			$pdf->Cell(0, 0.5, $competencia, 0, 1, 'R');
			
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			$pdf->SetFillColor(210,210,210);
			
			if ($rs->tipo_pedido==1) $titulo_coluna_peso="ROUPA LIMPA";
			else $titulo_coluna_peso="ROUPA SUJA";
			
			//se basear na data da coleta
			if ($rs->basear_nota_data==1) {
				$tit_coluna= "COLETA";
			}
			//se basear na data da entreda
			else {
				$tit_coluna= "ENTREGA";
			}
			
			$pdf->Cell(3, 0.5, "DATA DE ". $tit_coluna, 1, 0, "L", 1);
			$pdf->Cell(7, 0.5, $titulo_coluna_peso, 1, 0, "C", 1);
			$pdf->Cell(7, 0.5, "NÚMERO DA NOTA", 1, 1, "C", 1);
			
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
				
				if ($rs->basear_nota_data==1) $data_valida_mesmo= $data_valida;
				else $data_valida_mesmo= $data_valida;
				
				$total_empresa_dia= 0;
				$total_cliente_dia= 0;
				
				if (($d%2)!=0) $fill=1;
				else $fill= 0;
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->SetFillColor(235,235,235);
				
				$result_nota= mysql_query("select * from op_pedidos
										  	where id_cliente= '". $rs->id_cliente ."'
											and   data_pedido = '". $data_valida_mesmo ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											");
				$num_pedidos_aqui= "";
				$peso_total_notas_aqui= 0;
				
				while ($rs_nota= mysql_fetch_object($result_nota)) {
					$num_pedidos_aqui.= $rs_nota->num_pedido ." ";
					$peso_total_notas_aqui+= $rs_nota->peso_total;
				}
				
				
				
				$result_peso= mysql_query("select sum(peso) as soma from op_suja_pesagem, op_suja_remessas
												where op_suja_pesagem.id_cliente = '". $rs->id_cliente ."'
												and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
												and   op_suja_remessas.data_remessa= '". $data_valida_mesmo ."'
												and   op_suja_remessas.id_empresa = '". $_SESSION["id_empresa"] ."'
												");
				$rs_peso= mysql_fetch_object($result_peso);
			
				
				//se o pedido é de roupa limpa
				//ou é cobrado pelo peso suja da coleta
				if (($rs->tipo_pedido==1) || ($rs->basear_peso=="2")) $peso_aqui= $peso_total_notas_aqui;
				//pedido de roupa suja
				else $peso_aqui= $rs_peso->soma;
				
				$total_cliente_periodo_geral+= $peso_aqui;
				
				if ($peso_aqui==0) $num_pedido="-";
				else $num_pedido= $num_pedidos_aqui;
				
				//se o peso é baseado no peso cliente da coleta
				if ($rs_contrato->basear_peso=="2") $peso_final=
				else $peso_final= $peso_aqui;
				
				
				
				
				
				
				
				$pdf->Cell(3, 0.5, $data, 1, 0, "L", $fill);
				$pdf->Cell(7, 0.5, fnum($peso_final) ." kg", 1, 0, "C", $fill);
				$pdf->Cell(7, 0.5, fnumi($num_pedido), 1, 1, "C", $fill);
				
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(3, 0.5, "", 0, 0, "L", 0);
			$pdf->Cell(7, 0.5, fnum($total_cliente_periodo_geral) ." kg", 1, 0, "C", 1);
			$pdf->Cell(7, 0.5, "", 0, 1, "C", 0);
			
			if ($_POST["obs"]!="") {
				$pdf->Ln();
				
				$pdf->Cell(0, 0.3, "", 0, 1);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				$pdf->Cell(0, 0.6, "OBSERVAÇÕES:", 0, 1, 'L');
				
				$pdf->Cell(0, 0.2, "", 0, 1);
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->MultiCell(0, 0.2, "", "TRL", "L", 0);
				$pdf->MultiCell(0, 0.4, html_entity_decode($_POST["obs"]), "RL", "L", 0);
				$pdf->MultiCell(0, 0.2, "", "BRL", "L", 0);
				
				$pdf->Ln();
			}
		}
		//contrato mais complexo
		else {
			$total_cliente_periodo_geral= 0;
			$total_empresa_periodo_geral= 0;
			
			$total_cliente[1]=0;
			$total_cliente[2]=0;
			$total_cliente[3]=0;
			
			$pdf->AddPage();
			
			//echo $rs->apelido_fantasia."<br />";
			
			if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg"))
				$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 2, 1.3, 5, 1.9287);
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
			$pdf->Cell(0, 0.5, "MOVIMENTAÇÃO DO QUANTITATIVO DE ROUPA", 0 , 1, 'R');
			
			if (($_POST["data1"]!="") && ($_POST["data2"]!="")) $texto_subtitulo= $rs->apelido_fantasia;
			else $texto_subtitulo= $rs->apelido_fantasia;
			
			$pdf->Cell(0, 0.5, $texto_subtitulo, 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			$pdf->Cell(0, 0.5, $competencia, 0, 1, 'R');
			
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(2.5, 0.5, "DATA COLETA", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, "ROUPA SUJA", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, "DATA ENTREGA", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, "ROUPA LIMPA", 1, 0, "C", 1);
			$pdf->Cell(3, 0.5, "NOTAS", 1, 0, "C", 1);
			$pdf->Cell(4, 0.5, "RESÍDUOS", 1, 1, "C", 1);
			
			$diferenca= ceil(($data2_mk-$data1_mk)/86400);
			
			unset($total_cliente_periodo);
			unset($total_empresa_periodo);
			
			//echo $diferenca; die();
			
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
				
				$pdf->Cell(2.5, 0.5, $data, 1, 0, "C", $fill);
				
				// ---------------- PESAGEM CLIENTE SUJA
				
				
				$result_pesagens_cliente_suja= mysql_query("select * from tr_percursos, tr_percursos_passos
															where tr_percursos_passos.id_cliente = '". $rs->id_cliente ."'
															and   tr_percursos_passos.id_percurso = tr_percursos.id_percurso
															and   tr_percursos_passos.passo = '2'
															and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
															and   tr_percursos_passos.data_percurso = '". $data_valida ."'
															order by tr_percursos_passos.data_percurso asc, tr_percursos_passos.hora_percurso asc
															");
				
				$peso_cliente_suja_aqui= 0;
				$coleta=1;
				$asterisco= "";
				
				while ($rs_pesagens_cliente_suja= mysql_fetch_object($result_pesagens_cliente_suja)) {
					//se nao registrou peso nesta passagem
					if ($rs_pesagens_cliente_suja->pnr==1) {
						//$peso_cliente_suja_string= "PNR*";
						
						//if ($coleta==1) {
							$result_pesagens_empresa= mysql_query("select sum(peso) as peso_total from op_suja_pesagem, op_suja_remessas
																	where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
																	and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
																	and   op_suja_remessas.data_remessa= '". $data_valida ."'
																	and   op_suja_pesagem.id_cliente = '". $rs->id_cliente ."'
																	and   op_suja_remessas.id_percurso = '". $rs_pesagens_cliente_suja->id_percurso ."'
																	");
							$rs_pesagens_empresa= mysql_fetch_object($result_pesagens_empresa);
							//$total_cliente_remessa= $rs_total->total_cliente_remessa;
							
							$total_cliente[1]+= $rs_pesagens_empresa->peso_total;
							$peso_cliente_suja_aqui+= $rs_pesagens_empresa->peso_total;
						//}
						
						$asterisco.="*". $coleta ." ";
					}
					else {
						$total_cliente[1]+= $rs_pesagens_cliente_suja->peso;
						$peso_cliente_suja_aqui+= $rs_pesagens_cliente_suja->peso;
						
						//$peso_cliente_suja_string= fnum($rs_pesagens_cliente_suja->peso) ." kg";
					}
					
					$coleta++;
				}
				
				if ($_POST["identifica"]==1) $asterisco_string= " ". $asterisco;
				else $asterisco_string= "";
				
				//$total_cliente[1]+= $peso_cliente_suja_aqui;
				
				$pdf->Cell(2.5, 0.5, fnum($peso_cliente_suja_aqui) ." kg" . $asterisco_string, 1, 0, "C", $fill);
				
				$pdf->Cell(2.5, 0.5, desformata_data($amanha), 1, 0, "C", $fill);
				
				// ---------------- FIM PESAGEM CLIENTE SUJA
				
				
				
				// ---------------- PESAGEM CLIENTE LIMPA
				$result_pesagens_cliente_limpa= mysql_query("select * from tr_percursos, tr_percursos_passos
															where tr_percursos_passos.id_cliente = '". $rs->id_cliente ."'
															
															
															and   tr_percursos_passos.id_percurso = tr_percursos.id_percurso
															and   tr_percursos_passos.passo = '2'
															and   (tr_percursos.tipo = '2' or tr_percursos.tipo = '5' )
															and   tr_percursos_passos.data_percurso = '". $amanha ."'
															order by tr_percursos_passos.data_percurso asc, tr_percursos_passos.hora_percurso asc
															");
				
				$peso_cliente_limpa_aqui= 0;
				$entrega=1;
				$asterisco= "";
				
				while ($rs_pesagens_cliente_limpa= mysql_fetch_object($result_pesagens_cliente_limpa)) {
					
					$entrega_index= $entrega-1;
					
					if ($rs_pesagens_cliente_limpa->pnr==1) {
						
						/*
						// ----------------------------------------------------------------------------------------------------------
						
						$result_entrega= mysql_query("select * from tr_percursos, tr_percursos_passos
														where (tr_percursos.tipo = '2' or tr_percursos.tipo = '5' )
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														and   tr_percursos_passos.passo = '1'
														
														and   tr_percursos_passos.id_cliente = '". $rs->id_cliente ."'
														and   tr_percursos_passos.data_percurso = '". $amanha ."'
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
								$result_entrega_ultima= mysql_query("select * from tr_percursos, tr_percursos_passos
																	where (tr_percursos.tipo = '2' )
																	and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
																	and   tr_percursos_passos.passo = '1'
																	
																	and   tr_percursos_passos.id_cliente = '". $rs->id_cliente ."'
																	and   tr_percursos_passos.data_percurso = '". $data_valida ."'
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
														and   op_limpa_pesagem.id_cliente = '". $rs->id_cliente ."'
														and   extra = '0'
														$str_geral
														") or die(mysql_error());
							
							$rs_total= mysql_fetch_object($result_total);
							
						}
						
						// -----------------------------------------------------------------------------------
						
						$total_cliente[2]+= $rs_total->peso_total;
						$peso_cliente_limpa_aqui+= $rs_total->peso_total;
						
						$asterisco.="*". $entrega ." ";
						
						*/
						
						$result_nota= mysql_query("select peso_total from op_pedidos
													where id_cliente= '". $rs->id_cliente ."'
													and   
														(
															(data_pedido = '". $data_valida ."' and   data_tipo = 'c')
															
															or
															
															(data_pedido = '". $amanha ."' and   data_tipo = 'e')
														)
														
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													order by entrega asc limit ". $entrega_index .", 1
													") or die(mysql_error());
													
						$rs_nota= mysql_fetch_object($result_nota);
						
						$total_cliente[2]+= $rs_nota->peso_total;
						$peso_cliente_limpa_aqui+= $rs_nota->peso_total;
						
						$asterisco.="*". $entrega ." ";
						
					}
					else {
						$total_cliente[2]+= $rs_pesagens_cliente_limpa->peso;
						$peso_cliente_limpa_aqui+= $rs_pesagens_cliente_limpa->peso;
					}
					
					$entrega++;
				}
				
				if ($_POST["identifica"]==1) $asterisco_string= " ". $asterisco;
				else $asterisco_string= "";
				
				$pdf->Cell(2.5, 0.5, fnum($peso_cliente_limpa_aqui) ." kg" . $asterisco_string, 1, 0, "C", $fill);
				
				$result_nota= mysql_query("select * from op_pedidos
										  	where id_cliente= '". $rs->id_cliente ."'
											and   data_pedido = '". $data_valida ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											order by entrega asc
											");
				
				$numero_notas= "";
				while ($rs_nota= mysql_fetch_object($result_nota))
					$numero_notas .= $rs_nota->num_pedido . " ";
				
				$pdf->Cell(3, 0.5, $numero_notas, 1, 0, "C", $fill);
				
				// ---------------- FIM PESAGEM CLIENTE LIMPA
				
				$result_devolucao= mysql_query("select sum(op_suja_devolucao.peso) as peso from op_suja_devolucao, op_suja_remessas
												where op_suja_devolucao.id_cliente = '". $rs->id_cliente ."'
												and   op_suja_devolucao.id_remessa = op_suja_remessas.id_remessa
												and   op_suja_remessas.data_remessa = '". $data_valida ."'
												");
				$rs_devolucao= mysql_fetch_object($result_devolucao);
				
				$total_cliente[3]+= $rs_devolucao->peso;
				
				$pdf->Cell(4, 0.5, fnum($rs_devolucao->peso) ." kg", 1, 1, "C", $fill);
				
				
				
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->Cell(2.5, 0.5, "", 0, 0);
			
			$pdf->Cell(2.5, 0.5, fnum($total_cliente[1]) ." kg", 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.5, "", 0, 0);
			$pdf->Cell(2.5, 0.5, fnum($total_cliente[2]) ." kg", 1, 0, "C", !$fill);
			$pdf->Cell(3, 0.5, "", 0, 0);
			
			$pdf->Cell(4, 0.5, fnum($total_cliente[3]) ." kg", 1, 1, "C", !$fill);
			
			$pdf->Ln();
			
			if ($_POST["obs"]!="") {
				$pdf->Cell(0, 0.3, "", 0, 1);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				$pdf->Cell(0, 0.6, "OBSERVAÇÕES:", 0, 1, 'L');
				
				$pdf->Cell(0, 0.2, "", 0, 1);
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->MultiCell(0, 0.2, "", "TRL", "L", 0);
				$pdf->MultiCell(0, 0.4, html_entity_decode($_POST["obs"]), "RL", "L", 0);
				$pdf->MultiCell(0, 0.2, "", "BRL", "L", 0);
				
				$pdf->Ln();
			}
			
			if ($_POST["discriminar_relave"]==1) {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
				$pdf->Cell(2.5, 0.8, "Total lavado:", 0, 0, "L", 0);
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->Cell(5, 0.8, fnum($rs_lavagem_total->peso_total) ." kg", 0, 0, "L", 0);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
				$pdf->Cell(2.5, 0.8, "Total relavado:", 0, 0, "L", 0);
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->Cell(5, 0.8, fnum($rs_relave->peso_total) ." kg", 0, 1, "L", 0);
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->Cell(2.5, 0.8, "Índice de relave:", 0, 0, "L", 0);
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->Cell(0, 0.8, fnum($percent_relave) ."%", 0, 1, "L", 0);
			
			// ---------------------------------------------------------------------------------------------------------------
			
			//se estiver gerando por periodo...
			//if (($_POST["data1"]=="") && ($_POST["data2"]=="")) {
			
				//começando a discriminação das peças e pacotes
				
				
				
				
				$pdf->AddPage();
				
				if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg"))
					$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 2, 1.3, 5, 1.9287);
				
				$pdf->SetXY(7,1.75);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
				$pdf->Cell(0, 0.5, "DISCRIMINAÇÃO DE PEÇAS DE ROUPA", 0 , 1, 'R');
				
				$pdf->Cell(0, 0.5, $rs->apelido_fantasia, 0 , 1, 'R');
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				$pdf->Cell(0, 0.5, $competencia, 0, 1, 'R');
			
				$pdf->Ln();
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				$pdf->SetFillColor(210,210,210);
				
				$pdf->Cell(9.5, 0.5, "TIPO DE ROUPA", 1, 0, "L", 1);
				$pdf->Cell(3.75, 0.5, "NUM. PACOTES", 1, 0, "C", 1);
				$pdf->Cell(3.75, 0.5, "NUM. PEÇAS", 1, 1, "C", 1);
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				
				if (($_POST["data1"]=="") && ($_POST["data2"]=="")) {
					$periodo_anterior= date("m/Y", mktime(0, 0, 0, $periodo2[0]-1, 1, $periodo2[1]));
					
					$str_pecas1= " and   DATE_FORMAT(tr_percursos_passos.data_percurso, '%m/%Y') = '". $periodo_anterior ."'
									order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
									";
					
					$str_pecas2= " and   DATE_FORMAT(tr_percursos_passos.data_percurso, '%m/%Y') = '". $_POST["periodo"] ."'
									order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
									";
				}
				else {
					$str_pecas1= " and   tr_percursos_passos.data_percurso < '". $data1 ."'
									order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
									";
					$str_pecas2= " and   tr_percursos_passos.data_percurso <= '". $data2 ."'
									order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
										";
				}
				
				
				//pegar a ultima entrega do MÊS/PERÍODO ANTERIOR
				$result_entrega_ultima_periodo= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
															where (tr_percursos.tipo = '2' )
															and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
															and   tr_percursos_passos.passo = '1'
															and   tr_percursos_clientes.id_percurso = tr_percursos.id_percurso
															and   tr_percursos_clientes.id_cliente = '". $rs->id_cliente ."'
															$str_pecas1
															limit 1
															");
				$rs_entrega_ultima_periodo= mysql_fetch_object($result_entrega_ultima_periodo);
				
				$data_formatada= formata_data_hifen("01/". $_POST["periodo"]);
				$inicio_proximo_mes= soma_data($data_formatada, 0, 1, 0);
				
				//pegar a ultima entrega do MÊS ATUAL
				$result_entrega_proximo= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
															where (tr_percursos.tipo = '2' )
															and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
															and   tr_percursos_passos.passo = '1'
															and   tr_percursos_clientes.id_percurso = tr_percursos.id_percurso
															and   tr_percursos_clientes.id_cliente = '". $rs->id_cliente ."'
															$str_pecas2
															limit 1
															");
				
				$rs_entrega_proximo= mysql_fetch_object($result_entrega_proximo);
				
				$str_geral_periodo= "
									and op_limpa_pesagem.data_hora_pesagem >= '". $rs_entrega_ultima_periodo->data_percurso ." ". $rs_entrega_ultima_periodo->hora_percurso ."'
									and op_limpa_pesagem.data_hora_pesagem < '". $rs_entrega_proximo->data_percurso ." ". $rs_entrega_proximo->hora_percurso ."'
									";
				
				//echo "dia $data_aqui ";
				//echo $str_geral_periodo ."<br /><br />";
				
				
				
				$peso_total_peca_geral= 0;
				$total_pacotes_peca_geral= 0;
				$total_pecas_peca_geral= 0;
				
				
				
				$result_pesagem_pecas= mysql_query("select distinct(op_limpa_pesagem_pecas.id_tipo_roupa)
													from op_limpa_pecas, op_limpa_pesagem, op_limpa_pesagem_pecas
													where op_limpa_pesagem.id_pesagem = op_limpa_pesagem_pecas.id_pesagem
													and   op_limpa_pesagem_pecas.id_tipo_roupa = op_limpa_pecas.id_peca
													and   op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_limpa_pesagem.id_cliente = '". $rs->id_cliente ."'
													and   op_limpa_pesagem.extra = '0'
													$str_geral_periodo
													order by op_limpa_pecas.peca asc
													") or die(mysql_error());
				
				$pdf->SetFillColor(235,235,235);
				
				$j=0;
				while ($rs_pesagem_pecas= mysql_fetch_object($result_pesagem_pecas)) {
					
					if (($j%2)==0) $fill=0; else $fill= 1;
				
					$result_pesagem= mysql_query("select * from op_limpa_pesagem, op_limpa_pesagem_pecas
													where op_limpa_pesagem.id_pesagem = op_limpa_pesagem_pecas.id_pesagem
													and   op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_limpa_pesagem.id_cliente = '". $rs->id_cliente ."'
													and   op_limpa_pesagem_pecas.id_tipo_roupa = '". $rs_pesagem_pecas->id_tipo_roupa ."'
													and   op_limpa_pesagem.extra = '0'
													$str_geral_periodo
													") or die(mysql_error());
					$linhas_pesagem= mysql_num_rows($result_pesagem);
					
					if ($linhas_pesagem>0) {
						$peso_total_peca= 0;
						$total_pacotes_peca= 0;
						$total_pecas_peca= 0;
						
						while ($rs_pesagem= mysql_fetch_object($result_pesagem)) {
							$peso_total_peca += $rs_pesagem->peso;
							$total_pacotes_peca += $rs_pesagem->num_pacotes;
							$total_pacotes_peca += $rs_pesagem->pacotes_sobra;
							
							//if ($rs_pesagem->qtde_pecas_sobra>0) $total_pacotes_peca++;
							
							$total_pecas_aqui= ($rs_pesagem->qtde_pacote*$rs_pesagem->num_pacotes)+$rs_pesagem->qtde_pecas_sobra;
							$total_pecas_peca += $total_pecas_aqui;
						}
							
						$pdf->Cell(9.5, 0.45, pega_pecas_roupa($rs_pesagem_pecas->id_tipo_roupa), 1, 0, "L", $fill);
						$pdf->Cell(3.75, 0.45, fnumf($total_pacotes_peca), 1, 0, "C", $fill);
						$pdf->Cell(3.75, 0.45, fnumf($total_pecas_peca), 1, 1, "C", $fill);
						
						$peso_total_peca_geral += $peso_total_peca;
						$total_pacotes_peca_geral += $total_pacotes_peca;
						$total_pecas_peca_geral += $total_pecas_peca;
						
						$j++;
					}
				}
				
				$pdf->SetFillColor(210,210,210);
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
				
				$pdf->Cell(9.5, 0.5, "", 0, 0);
				$pdf->Cell(3.75, 0.5, fnumf($total_pacotes_peca_geral), 1, 0, "C", 1);
				$pdf->Cell(3.75, 0.5, fnumf($total_pecas_peca_geral), 1, 1, "C", 1);
				
				
				// -------------------------------------------------------------------------------------
				
				$pdf->AddPage();
					
				$pdf->SetXY(7,1.75);
				
				if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg"))
					$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 2, 1.3, 5, 1.9287);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
				$pdf->Cell(0, 0.5, "CONTROLE DE COSTURA", 0 , 1, 'R');
				
				$pdf->Cell(0, 0.5, pega_pessoa($rs->id_cliente), 0 , 1, 'R');
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				$pdf->Cell(0, 0.5, $competencia, 0, 1, 'R');
				
				$pdf->Ln();
				
				if (($_POST["data1"]=="") && ($_POST["data2"]=="")) {
					$str_costura= " and   DATE_FORMAT(op_limpa_costura_consertos.data_chegada, '%m/%Y') = '". $_POST["periodo"] ."' ";
				}
				else {
					$str_costura= " and   op_limpa_costura_consertos.data_chegada >= '". $data1 ."'
										 and   op_limpa_costura_consertos.data_chegada <= '". $data2 ."'
										";
				}
				
				$result_costura_conserto_pecas= mysql_query("select distinct(op_limpa_costura_consertos_pecas.id_tipo_roupa)
																from op_limpa_costura_consertos_pecas, op_limpa_costura_consertos, op_limpa_pecas
																where op_limpa_costura_consertos.id_costura_conserto = op_limpa_costura_consertos_pecas.id_costura_conserto
																$str_costura
																and   op_limpa_costura_consertos.id_cliente = '". $rs->id_cliente ."'
																and   op_limpa_costura_consertos_pecas.id_tipo_roupa = op_limpa_pecas.id_peca
																order by op_limpa_pecas.peca asc
																") or die(mysql_error());
												
				$pdf->SetFillColor(200,200,200);		
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				$pdf->Cell(4.5, 0.6, "TIPO DE ROUPA", 1, 0, "L", 1);
				$pdf->Cell(2.5, 0.6, "RECEBIDO", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "CONSERTADO", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "SUBSTITUÍDO", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "BAIXA", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "DEVOLVIDO", 1, 1, "C", 1);
				
				$total_recebido= 0;
				$total_consertado= 0;
				$total_substituido= 0;
				$total_baixa= 0;
				$total_devolvido= 0;
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->SetFillColor(235,235,235);
				
				$i=0;
				
				while ($rs_costura_conserto_pecas= mysql_fetch_object($result_costura_conserto_pecas)) {
					if (($i%2)==0) $fill=1;
					else $fill= 0;
					
					$result_costura_conserto= mysql_query("select sum(qtde_recebido) as qtde_recebido, sum(qtde_consertado) as qtde_consertado,
																  sum(qtde_substituido) as qtde_substituido, sum(qtde_baixa) as qtde_baixa, sum(qtde_devolvido) as qtde_devolvido
																		from op_limpa_costura_consertos_pecas, op_limpa_costura_consertos
																		where op_limpa_costura_consertos.id_costura_conserto = op_limpa_costura_consertos_pecas.id_costura_conserto
																		and   op_limpa_costura_consertos.id_cliente = '". $rs->id_cliente ."'
																		and   op_limpa_costura_consertos_pecas.id_tipo_roupa = '". $rs_costura_conserto_pecas->id_tipo_roupa ."' 
																		$str_costura
																		") or die(mysql_error());
					
					$rs_costura_conserto= mysql_fetch_object($result_costura_conserto);
					
					$pdf->Cell(4.5, 0.55, pega_pecas_roupa($rs_costura_conserto_pecas->id_tipo_roupa), 1, 0, "L", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_recebido, 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_consertado, 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_substituido, 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_baixa, 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_devolvido, 1, 1, "C", $fill);
					
					$total_recebido+=$rs_costura_conserto->qtde_recebido;
					$total_consertado+=$rs_costura_conserto->qtde_consertado;
					$total_substituido+=$rs_costura_conserto->qtde_substituido;
					$total_baixa+=$rs_costura_conserto->qtde_baixa;
					$total_devolvido+=$rs_costura_conserto->qtde_devolvido;
					
					$i++;
				}
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
				
				$pdf->Cell(4.5, 0.55, "", 0, 0, "L", 0);
				$pdf->Cell(2.5, 0.55, $total_recebido, 1, 0, "C", !$fill);
				$pdf->Cell(2.5, 0.55, $total_consertado, 1, 0, "C", !$fill);
				$pdf->Cell(2.5, 0.55, $total_substituido, 1, 0, "C", !$fill);
				$pdf->Cell(2.5, 0.55, $total_baixa, 1, 0, "C", !$fill);
				$pdf->Cell(2.5, 0.55, $total_devolvido, 1, 1, "C", !$fill);
				
				$pdf->Ln();$pdf->Ln();
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(0, 0.6, "HISTÓRICO DO PERÍODO:", 0, 1, "L", 0);
				
				$pdf->Ln();
				
				$pdf->SetFillColor(200,200,200);		
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				$pdf->Cell(4, 0.6, "DATA DE CHEGADA", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "RECEBIDO", 1, 0, "C", 1);
				$pdf->Cell(4, 0.6, "DATA DE ENTREGA", 1, 1, "C", 1);
				
				$result_costura_conserto_pecas_situacao= mysql_query("select * from op_limpa_costura_consertos
																		where id_empresa = '". $_SESSION["id_empresa"] ."' 
																		and   id_cliente = '". $rs->id_cliente ."'
																		$str_costura
																		order by data_chegada asc
																		") or die(mysql_error());
				
				$i=1;
				while ($rs_costura_conserto_pecas_situacao= mysql_fetch_object($result_costura_conserto_pecas_situacao)) {
					
					if (($i%2)==0) $fill=1;
					else $fill= 0;
					
					$pdf->SetFont('ARIALNARROW', '', 8);
					$pdf->SetFillColor(235,235,235);
					
					$result_costura= mysql_query("select sum(qtde_recebido) as qtde_recebido, sum(qtde_consertado) as qtde_consertado,
													sum(qtde_substituido) as qtde_substituido, sum(qtde_baixa) as qtde_baixa, sum(qtde_devolvido) as qtde_devolvido
													from op_limpa_costura_consertos_pecas
													where id_costura_conserto = '". $rs_costura_conserto_pecas_situacao->id_costura_conserto ."'
													");
					$rs_costura= mysql_fetch_object($result_costura);
					
					if ((trim($rs_costura_conserto_pecas_situacao->data_entrega)=="") || (trim($rs_costura_conserto_pecas_situacao->data_entrega)=="0000-00-00")) $data_entrega_aqui= "Consertando";
					else $data_entrega_aqui= desformata_data($rs_costura_conserto_pecas_situacao->data_entrega);
					
					$pdf->Cell(4, 0.6, desformata_data($rs_costura_conserto_pecas_situacao->data_chegada), 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.6, $rs_costura->qtde_recebido, 1, 0, "C", $fill);
					$pdf->Cell(4, 0.6, $data_entrega_aqui, 1, 1, "C", $fill);
					
					$i++;
				}
				
				
				
				$pdf->Ln();
			//}
			
		}//fim contrato 2
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("movimentacao_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	mysql_close($conexao);
}
?>