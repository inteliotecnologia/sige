<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("iq|", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	if ($_POST["id_centro_custo_tipo"]!="") {
		$str .= " and   fi_centro_custos_tipos.id_centro_custo_tipo =  '". $_POST["id_centro_custo_tipo"] ."'";
		$txt= pega_centro_custo_tipo($_POST["id_centro_custo_tipo"]);
	}
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(235,235,235);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	//$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	if ($_POST["tipo_relatorio"]=="p") {
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "ESTOQUE POR TIPOS DE CENTRO DE CUSTOS - PREÇO", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $txt, 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		
		$result_cct= mysql_query("select *, id_centro_custo_tipo as id_centro_custo_tipo_pai from fi_centro_custos_tipos
										where fi_centro_custos_tipos.id_empresa= '". $_SESSION["id_empresa"] ."'
										". $str ."
										
										and id_centro_custo_tipo IN (
										
											select fi_estoque_iv.id_centro_custo_tipo
											from  fi_estoque, fi_estoque_iv, fi_itens
											where fi_estoque.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   fi_estoque.id_item = fi_estoque_iv.id_item
											and   fi_estoque_iv.id_centro_custo_tipo = id_centro_custo_tipo_pai
											and   fi_estoque.id_item = fi_itens.id_item
											and   fi_estoque_iv.qtde > '0'
											order by fi_itens.item asc
										
										)
										
										order by fi_centro_custos_tipos.centro_custo_tipo asc
										") or die(mysql_error());
		//$total_cc= mysql_num_rows($result_cc);
		
		while ($rs_cct= mysql_fetch_object($result_cct)) {
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 0.8, $rs_cct->centro_custo_tipo, 0, 1, "L");
			
			$result_est= mysql_query("select distinct(fi_itens.id_item) as id_item, item, qtde, tipo_apres, valor_unitario
											from  fi_estoque, fi_estoque_iv, fi_itens
											where fi_estoque.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   fi_estoque.id_item = fi_estoque_iv.id_item
											and   fi_estoque_iv.id_centro_custo_tipo = '". $rs_cct->id_centro_custo_tipo ."'
											and   fi_estoque.id_item = fi_itens.id_item
											and   fi_estoque_iv.qtde > '0'
											order by fi_itens.item asc
											") or die(mysql_error());
			
			if (mysql_num_rows($result_est)>0) {
				$pdf->SetFillColor(200,200,200);
				
				$pdf->Cell(9, 0.6, "PRODUTO", 1, 0, "L", 1);
				$pdf->Cell(4, 0.6, "QTDE", 1, 0, "C", 1);
				$pdf->Cell(4, 0.6, "VALOR UNITÁRIO", 1, 1, "R", 1);
				
				$soma_valor=0;
				$qtde_estoque_atual=0;
				$apres= "";
				
				$i=0;
				while ($rs_est= mysql_fetch_object($result_est)) {
					
					$pdf->SetFillColor(235,235,235);
					
					if (($i%2)==0) $fill=1;
					else $fill=0;
					
					/*$result_custos= mysql_query("select sum(valor) as valor_mes
													from  fi_custos
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_centro_custo = '". $rs_cc->id_centro_custo ."'
													and   id_centro_custo_tipo = '". $rs_cct->id_centro_custo_tipo ."'
													and   DATE_FORMAT(data, '%m/%Y') = '". $_POST["periodo"] ."'
												");
					$rs_custos= mysql_fetch_object($result_custos);*/
					
					$pdf->SetFont('ARIALNARROW', '', 9);
					
					$pdf->Cell(9, 0.6, $rs_est->item, 1, 0, "L", $fill);
					$pdf->Cell(4, 0.6, fnumf($rs_est->qtde) ." ". pega_tipo_apres($rs_est->tipo_apres), 1, 0, "C", $fill);
					$pdf->Cell(4, 0.6, "R$ ". fnum($rs_est->valor_unitario), 1, 1, "R", $fill);
					
					$qtde_estoque_atual+= $rs_est->qtde;
					$soma_valor+= $rs_est->valor_unitario;
					$apres= pega_tipo_apres($rs_est->tipo_apres);
					
					$i++;
				}
				
				$media_valor= ($soma_valor/$i);
				
				$pdf->SetFillColor(200,200,200);
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				$pdf->Cell(9, 0.6, "ESTOQUE ATUAL/MÉDIA DE PREÇO:", 1, 0, "R", 1);
				$pdf->Cell(4, 0.6, fnumf($qtde_estoque_atual) ." ". $apres, 1, 0, "C", 1);
				$pdf->Cell(4, 0.6, "R$ ". fnum($media_valor), 1, 1, "R", 1);
				
				$pdf->Ln();
			}
		}
		
		/*
		$largura=12/$total_turnos;
		
		while ($rs_turnos= mysql_fetch_object($result_turnos))
			$pdf->Cell($largura, 0.6, $rs_turnos->turno, 1, 0, "C", 1);
		
		$pdf->Cell(3, 0.6, "EQUIPE", 1, 1, "C", 1);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 6);
		
		$pdf->Cell(2, 0.6, "", 1, 0, "L", 0);
		
		for ($k=-1; $k<4; $k++) {
			if ($k==3) $quebra= 1;
			else $quebra= 0;
			
			$pdf->Cell(1.5, 0.6, "peso turno (kg)", 1, 0, "C", 0);
			$pdf->Cell(1.5, 0.6, "média individual", 1, $quebra, "C", 0);
		}
		
		$pdf->SetFillColor(240, 240, 240);
		
		for ($i=1; $i<=$total_dias_mes; $i++) {
			$hoje_mk= mktime(0, 0, 0, $periodo[0], $i, $periodo[1]);
			$id_dia= date("w", $hoje_mk);
			
			if (($i%2)==0) $fill=0;
			else $fill= 1;
			
			if (($id_dia==0) || ($id_dia==6))
				$pdf->Cell(0, 0.2, "", 0, 1, 0, 0);
			else {
				$data_formatada= date("d/m/Y", $hoje_mk);
				$data= date("Y-m-d", $hoje_mk);
				$data_mesmo= date("Y-m-d", $hoje_mk);
				
				$total_dia=0;
				$media_dia=0;
				
				$pdf->SetFont('ARIALNARROW', '', 9);
				
				$pdf->Cell(2, 0.6, $data_formatada, 1, 0, "L", $fill);
				
				$result_turnos= mysql_query("select * from rh_turnos, rh_departamentos
												where rh_turnos.id_departamento = rh_departamentos.id_departamento
												and   rh_departamentos.id_empresa= '". $_SESSION["id_empresa"] ."'
												and   rh_turnos.id_departamento = '1'
												and   rh_turnos.fixo = '1'
												order by rh_turnos.ordem asc
												") or die(mysql_error());
				$total_turnos= mysql_num_rows($result_turnos);
				
				$largura=(12/$total_turnos)/2;
				
				$j=1;
				while ($rs_turnos= mysql_fetch_object($result_turnos)) {
					
					$horario= pega_horarios_turno($rs_turnos->id_turno, $id_dia);
					
					if (tem_hl_turno($rs_turnos->id_turno, $id_dia)) $data= soma_data($data, 1, 0, 0);
					
					if ($horario[1]=="00:00:00") $horario[1]= "23:59:59";
					
					$str= " and   hora_pesagem >= '". $horario[0] ."'
							and   hora_pesagem < '". $horario[1] ."'
							";
					
					/*if ($data_mesmo=="2009-01-12")
						echo " <br />
	<br />
	".$rs_turnos->turno ."<br />
	<br />
	select sum(peso) as soma, avg(qtde_funcionarios) as media from op_limpa_pesagem
												where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   data_pesagem = '". $data ."'
												". $str ."
												order by data_pesagem desc, hora_pesagem desc
												";
					$result_soma= mysql_query("select sum(peso) as soma, avg(qtde_funcionarios) as media from op_limpa_pesagem
												where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   data_pesagem = '". $data ."'
												". $str ."
												order by data_pesagem desc, hora_pesagem desc
												");
					$rs_soma= mysql_fetch_object($result_soma);
					
					/*$result_soma= mysql_query("select avg(qtde_funcionarios) as media from op_limpa_pesagem
												where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   data_pesagem = '". $data ."'
												". $str ."
												order by data_pesagem desc, hora_pesagem desc
												");
					$rs_soma= mysql_fetch_object($result_soma);
					
					$total_dia+= $rs_soma->soma;
					$total_turno[$j]+= $rs_soma->soma;
					
					if ($rs_soma->media!=0) {
						$media_dia+= ($rs_soma->soma/$rs_soma->media);
						$media_turno[$j]+= ($rs_soma->soma/$rs_soma->media);
					}
					
					$pdf->Cell($largura, 0.6, fnum($rs_soma->soma), 1, 0, "C", $fill);
					$pdf->Cell($largura, 0.6, fnum($media_dia), 1, 0, "C", $fill);
					
					$j++;
				}
				
				$total_mes+= $total_dia;
				$media_mes+= $media_dia/$total_turnos;
				
				$pdf->Cell(1.5, 0.6, fnum($total_dia), 1, 0, "C", $fill);
				$pdf->Cell(1.5, 0.6, fnum($media_dia/$total_turnos), 1, 1, "C", $fill);
			}//fim else
		}
		
		$pdf->SetFillColor(210,210,210);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(2, 0.6, "TOTAL", 1, 0, "L", 1);
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		
		$j=1;
		$result_turnos= mysql_query("select * from rh_turnos, rh_departamentos
										where rh_turnos.id_departamento = rh_departamentos.id_departamento
										and   rh_departamentos.id_empresa= '". $_SESSION["id_empresa"] ."'
										and   rh_turnos.id_departamento = '1'
										and   rh_turnos.fixo = '1'
										order by rh_turnos.ordem asc
										") or die(mysql_error());
		$total_turnos= mysql_num_rows($result_turnos);
		
		$largura=(12/$total_turnos)/2;
		
		while ($rs_turnos= mysql_fetch_object($result_turnos)) {
			$pdf->Cell($largura, 0.6, fnum($total_turno[$j]) ." kg", 1, 0, "C", 1);
			$pdf->Cell($largura, 0.6, fnum($media_turno[$j]) ." kg", 1, 0, "C", 1);
			$j++;
		}
		
		$pdf->Cell(1.5, 0.6, fnum($total_mes) ." kg", 1, 0, "C", 1);
		$pdf->Cell(1.5, 0.6, fnum($media_mes/$total_dias_mes) ." kg", 1, 1, "C", 1);
		
		*/
	}
	//por item
	elseif ($_POST["tipo_relatorio"]=="i") {
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "ESTOQUE POR TIPOS DE CENTRO DE CUSTOS - ITENS", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $txt, 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		
		$result_cct= mysql_query("select *, id_centro_custo_tipo as id_centro_custo_tipo_pai from fi_centro_custos_tipos
										where fi_centro_custos_tipos.id_empresa= '". $_SESSION["id_empresa"] ."'
										". $str ."
										
										and id_centro_custo_tipo IN (
										
											select fi_estoque_iv.id_centro_custo_tipo
											from  fi_estoque, fi_estoque_iv, fi_itens
											where fi_estoque.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   fi_estoque.id_item = fi_estoque_iv.id_item
											and   fi_estoque_iv.id_centro_custo_tipo = id_centro_custo_tipo_pai
											and   fi_estoque.id_item = fi_itens.id_item
											and   fi_estoque_iv.qtde > '0'
											order by fi_itens.item asc
										
										)
										
										order by fi_centro_custos_tipos.centro_custo_tipo asc
										") or die(mysql_error());
		//$total_cc= mysql_num_rows($result_cc);
		
		while ($rs_cct= mysql_fetch_object($result_cct)) {
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 0.8, $rs_cct->centro_custo_tipo, 0, 1, "L");
			
			$result_est= mysql_query("select distinct(fi_itens.id_item) as id_item, tipo_apres
											from  fi_estoque, fi_estoque_iv, fi_itens
											where fi_estoque.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   fi_estoque.id_item = fi_estoque_iv.id_item
											and   fi_estoque_iv.id_centro_custo_tipo = '". $rs_cct->id_centro_custo_tipo ."'
											and   fi_estoque.id_item = fi_itens.id_item
											and   fi_estoque_iv.qtde > '0'
											order by fi_itens.item asc
											") or die(mysql_error());
			
			if (mysql_num_rows($result_est)>0) {
				$pdf->SetFillColor(200,200,200);
				
				$pdf->Cell(12, 0.6, "PRODUTO", 1, 0, "L", 1);
				$pdf->Cell(5, 0.6, "QTDE", 1, 1, "C", 1);
				
				$soma_valor=0;
				$qtde_estoque_atual=0;
				$apres= "";
				
				$i=0;
				while ($rs_est= mysql_fetch_object($result_est)) {
					
					$pdf->SetFillColor(235,235,235);
					
					if (($i%2)==0) $fill=1;
					else $fill=0;
					
					/*$result_dados= mysql_query("select sum(fi_estoque_iv.qtde) as qtde
												from  fi_estoque, fi_estoque_iv, fi_itens
												where fi_estoque.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   fi_estoque.id_item = fi_estoque_iv.id_item
												and   fi_estoque_iv.id_centro_custo_tipo = '". $rs_cct->id_centro_custo_tipo ."'
												and   fi_estoque.id_item = fi_itens.id_item
												and   fi_estoque_iv.qtde > '0'
												and   fi_estoque.id_item = '". $rs_est->id_item ."'
												order by fi_itens.item asc
												");
					$rs_dados= mysql_fetch_object($result_dados);
					
					$result_dados_estoque= mysql_query("select qtde_atual
														from  fi_estoque
														where id_item = '". $rs_est->id_item ."'
														");
					$rs_dados_estoque= mysql_fetch_object($result_dados_estoque);
					
					if ($rs_dados_estoque->qtde_atual!=$rs_dados->qtde) {
						
						$nada="";
						
						$result_count_ultimo= mysql_query("select distinct(id_nota) as id_nota from fi_estoque_iv
															where id_item = '". $rs_est->id_item ."'
															and   id_mov = '0'
															order by id_iv desc
															");
						while ($rs_count_ultimo= mysql_fetch_object($result_count_ultimo)) {
							
							$result_count_nota= mysql_query("select * from fi_estoque_iv
																where id_nota = '". $rs_count_ultimo->id_nota ."'
																and   id_item = '". $rs_est->id_item ."'
																order by id_iv desc limit 1
																");
							$rs_count_nota= mysql_fetch_object($result_count_nota);
							
							$result_count= mysql_query("select * from fi_estoque_iv
														where id_item = '". $rs_est->id_item ."'
														
														 and   qtde = '". $rs_count_nota->qtde ."'
														and   valor_unitario = '". $rs_count_nota->valor_unitario ."'
														
														
														and   id_nota = '". $rs_count_ultimo->id_nota ."'
														and   id_mov = '". $rs_count_nota->id_mov ."'
														and   id_iv <> '". $rs_count_nota->id_iv ."'
														");
														
							$linhas_count= mysql_num_rows($result_count);
							
							$nada .= "(". $rs_count_ultimo->id_nota ."-". $linhas_count .") ";
						}
						
						
						
						$nada= $nada ." [". $rs_est->id_item ."] ";
						
					}
					else $nada= "";
					*/
					
					/*$result_custos= mysql_query("select sum(valor) as valor_mes
													from  fi_custos
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_centro_custo = '". $rs_cc->id_centro_custo ."'
													and   id_centro_custo_tipo = '". $rs_cct->id_centro_custo_tipo ."'
													and   DATE_FORMAT(data, '%m/%Y') = '". $_POST["periodo"] ."'
												");
					$rs_custos= mysql_fetch_object($result_custos);*/
					
					$result_dados_estoque= mysql_query("select qtde_atual
														from  fi_estoque
														where id_item = '". $rs_est->id_item ."'
														");
					$rs_dados_estoque= mysql_fetch_object($result_dados_estoque);
					
					$pdf->SetFont('ARIALNARROW', '', 9);
					
					$pdf->Cell(12, 0.6, pega_item($rs_est->id_item), 1, 0, "L", $fill);
					$pdf->Cell(5, 0.6, fnumf($rs_dados_estoque->qtde_atual) ." ". pega_tipo_apres($rs_est->tipo_apres), 1, 1, "C", $fill);
					
					$qtde_estoque_atual+= $rs_dados->qtde;
					$soma_valor+= $rs_dados->valor_unitario;
					$apres= pega_tipo_apres($rs_dados->tipo_apres);
					
					$i++;
				}
				
				$media_valor= ($soma_valor/$i);
				
				//$pdf->SetFillColor(200,200,200);
				//$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				//$pdf->Cell(9, 0.6, "ESTOQUE ATUAL/MÉDIA DE PREÇO:", 1, 0, "R", 1);
				//$pdf->Cell(4, 0.6, fnumf($qtde_estoque_atual) ." ". $apres, 1, 1, "C", 1);
				
				$pdf->Ln();
			}
		}
	}
	//centro de custo
	elseif ($_POST["tipo_relatorio"]=="c") {
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "ESTOQUE POR CENTRO DE CUSTOS - ITENS", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $txt, 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		
		if ($_POST["id_centro_custo"]!="") $str= " and   fi_centro_custos.id_centro_custo = '". $_POST["id_centro_custo"] ."' ";
		
		$result_cct= mysql_query("select * from fi_centro_custos
										where fi_centro_custos.id_empresa= '". $_SESSION["id_empresa"] ."'
										". $str ."
										
										/* and id_centro_custo IN (
										
											select fi_cc_ct.id_centro_custo 
											from  fi_estoque, fi_estoque_iv, fi_itens, fi_cc_ct
											where fi_estoque.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   fi_estoque.id_item = fi_estoque_iv.id_item
											and   fi_estoque.id_item = fi_itens.id_item
											and   fi_estoque_iv.qtde > '0'
											and   fi_estoque_iv.id_centro_custo_tipo = fi_cc_ct.id_centro_custo_tipo
											order by fi_itens.item asc
										
										) */
										
										order by fi_centro_custos.centro_custo asc
										") or die(mysql_error());
		//$total_cc= mysql_num_rows($result_cc);
		
		while ($rs_cct= mysql_fetch_object($result_cct)) {
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 0.8, $rs_cct->centro_custo, 0, 1, "L");
			
			$result_est= mysql_query("select distinct(fi_itens.id_item) as id_item, tipo_apres
											from  fi_estoque, fi_estoque_iv, fi_itens, fi_cc_ct
											where fi_estoque.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   fi_estoque.id_item = fi_estoque_iv.id_item
											and   fi_cc_ct.id_centro_custo = '". $rs_cct->id_centro_custo ."'
											and   fi_estoque.id_item = fi_itens.id_item
											and   fi_estoque_iv.qtde > '0'
											and   fi_estoque_iv.id_centro_custo_tipo = fi_cc_ct.id_centro_custo_tipo
											order by fi_itens.item asc
											") or die(mysql_error());
			
			if (mysql_num_rows($result_est)>0) {
				$pdf->SetFillColor(200,200,200);
				
				$pdf->Cell(12, 0.6, "PRODUTO", 1, 0, "L", 1);
				$pdf->Cell(5, 0.6, "QTDE", 1, 1, "C", 1);
				
				$soma_valor=0;
				$qtde_estoque_atual=0;
				$apres= "";
				
				$i=0;
				while ($rs_est= mysql_fetch_object($result_est)) {
					
					$result_dados_estoque= mysql_query("select qtde_atual
														from  fi_estoque
														where id_item = '". $rs_est->id_item ."'
														");
					$rs_dados_estoque= mysql_fetch_object($result_dados_estoque);
					
					if ($rs_dados_estoque->qtde_atual>0) {
					
						$pdf->SetFillColor(235,235,235);
						
						if (($i%2)==0) $fill=1;
						else $fill=0;
						
						$pdf->SetFont('ARIALNARROW', '', 9);
						
						$pdf->Cell(12, 0.6, pega_item($rs_est->id_item), 1, 0, "L", $fill);
						$pdf->Cell(5, 0.6, fnumf($rs_dados_estoque->qtde_atual) ." ". pega_tipo_apres($rs_est->tipo_apres), 1, 1, "C", $fill);
						
						$qtde_estoque_atual+= $rs_dados->qtde;
						$soma_valor+= $rs_dados->valor_unitario;
						$apres= pega_tipo_apres($rs_dados->tipo_apres);
					}
					
					$i++;
				}
				
				$media_valor= ($soma_valor/$i);
				
				//$pdf->SetFillColor(200,200,200);
				//$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				//$pdf->Cell(9, 0.6, "ESTOQUE ATUAL/MÉDIA DE PREÇO:", 1, 0, "R", 1);
				//$pdf->Cell(4, 0.6, fnumf($qtde_estoque_atual) ." ". $apres, 1, 1, "C", 1);
				
				$pdf->Ln();
			}
		}
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("cc_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>