<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("psl", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_paisagem.php");


	if ( ($_POST["data1"]!="") && ($_POST["data2"]!="") ) {
		$data1= formata_data_hifen($_POST["data1"]); $data1f= $_POST["data1"];
		$data2= formata_data_hifen($_POST["data2"]); $data2f= $_POST["data2"];
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
		
		$dataq= explode("/", $_POST["data1"]);
		$mes_extenso= traduz_mes($dataq[1]);
	}
	
	$altura=0.5;
	$largura_item= 3;
	
	if ($_POST["modo"]==1) {
		$tit_aux= " - GOMAS RECEBIDAS";
	}
	elseif ($_POST["modo"]==2) {
		$tit_aux= " - GOMAS ENTREGUES";
	}
	elseif ($_POST["modo"]==3) {
		$tit_aux= " - COMPARAÇÃO RECEBIDA/ENTREGUE";
		$altura=1;
		
		$largura_item= 2.6;
	}
	
	$pdf=new PDF("L", "cm", "A4");
	$pdf->SetMargins(1.5, 1.5, 1.5);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(230,230,230);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
		
	if ($_POST["id_cliente"]!="") {
		$str .= " and   pessoas.id_pessoa =  '". $_POST["id_cliente"] ."'";
	}
	
	$periodo= explode("/", $_POST["periodo"]);
	$periodo_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
	
	$total_dias_mes= date("t", $periodo_mk);
	
	$largura=(22.4/$total_dias_mes);
	
	$result= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
							where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
							". $str ."
							and   pessoas_tipos.tipo_pessoa = 'c'
							and   pessoas.id_cliente_tipo = '1'
							order by 
							pessoas.apelido_fantasia asc
							") or die(mysql_error());
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		$pdf->AddPage();
		
		unset($total_dia);
		$total_geral_periodo= 0;
		
		$pdf->SetXY(16.9, 1.3);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "RELATÓRIO DE GOMAS". $tit_aux, 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, traduz_mes($periodo[0]) ." de ". $periodo[1], 0, 1, "R");
		
		$pdf->LittleLn();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(1.75, 0.6, "CLIENTE:", 0, 0, "L");
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(0, 0.6, $rs->apelido_fantasia, 0, 1, "L");
		
		$pdf->Cell(0, 0.3, "", 0, 1);
		
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		$pdf->SetFillColor(200,200,200);
		
		$pdf->Cell(3, 0.5, "PEÇAS", 1 , 0, "L", 1);
		
		//repetir todos os dias do intervalo
		for ($t=1; $t<=$total_dias_mes; $t++) {
			$pdf->Cell($largura, 0.5, $t, 1, 0, "C", 1);
            //echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->Cell(1.2, 0.5, "Total", 1, 1, "C", 1);
				
		$pdf->SetFont('ARIALNARROW', '', 7);
		
		/*if ($_POST["modo"]==2)
			$result_pecas= mysql_query("select distinct(op_limpa_pecas.id_peca), op_limpa_pecas.* from op_limpa_pecas, op_limpa_pesagem, op_limpa_pesagem_pecas
										where op_limpa_pecas.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   op_limpa_pecas.status_peca = '1'
										and   op_limpa_pecas.id_peca = op_limpa_pesagem_pecas.id_tipo_roupa
										and   op_limpa_pesagem.id_pesagem = op_limpa_pesagem_pecas.id_pesagem
										and   DATE_FORMAT(op_limpa_pesagem.data_pesagem, '%m/%Y') = '". $_POST["periodo"] ."'
										and   op_limpa_pesagem.id_cliente = '". $rs->id_cliente ."'
										and   op_limpa_pesagem.goma = '1'
										order by op_limpa_pecas.peca asc
										") or die(mysql_error());
		else*/
			$result_pecas= mysql_query("select distinct(op_limpa_pecas.id_peca), op_limpa_pecas.* from op_limpa_pecas, op_suja_gomas, op_suja_remessas
										where op_limpa_pecas.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   op_limpa_pecas.status_peca = '1'
										and   op_limpa_pecas.id_peca = op_suja_gomas.id_peca
										and   op_suja_gomas.id_remessa = op_suja_remessas.id_remessa
										and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". $_POST["periodo"] ."'
										and   op_suja_gomas.id_cliente = '". $rs->id_cliente ."'
										order by op_limpa_pecas.peca asc
										") or die(mysql_error());
		
		$j=1;
		while ($rs_peca= mysql_fetch_object($result_pecas)) {
			if (($j%2)==0) $fill= 0;
			else {
				$fill=1;
				$pdf->SetFillColor(230,230,230);
			}
			
			$pdf->Cell(3, $altura, $rs_peca->peca, 1, 0, "L", $fill);
			
			if ($_POST["modo"]==3) {
				$pdf->SetXY($pdf->GetX()-0.5, $pdf->GetY());
				$pdf->Cell(0.5, 0.5, "AS", 1, 0, "L", $fill);
				
				$pdf->SetXY($pdf->GetX()-0.5, $pdf->GetY()+0.5);
				$pdf->Cell(0.5, 0.5, "AL", 1, 0, "L", $fill);
				
				$pdf->SetXY($pdf->GetX(), $pdf->GetY()-0.5);
			}
			
			$total_peca= 0;
			$total_peca1= 0;
			$total_peca2= 0;
			//$total_dia= array();
			
			//repetir todos os dias do intervalo
			for ($t=1; $t<=$total_dias_mes; $t++) {
				
				if ($t==1) $total_dia[$t]= 0;
				
				$data_aqui= $periodo[1] ."-". $periodo[0] ."-". formata_saida($t, 2);
				
				if (($_POST["modo"]==1) || ($_POST["modo"]==3)) {
				
					$result_goma= mysql_query("select sum(qtde) as soma from op_suja_gomas, op_suja_remessas
													where op_suja_gomas.id_cliente = '". $rs->id_cliente ."'
													and   op_suja_remessas.id_remessa = op_suja_gomas.id_remessa
													and   op_suja_remessas.data_remessa = '". $data_aqui ."'
													and   op_suja_gomas.id_peca = '". $rs_peca->id_peca ."'
													") or die(mysql_error());
					$rs_goma= mysql_fetch_object($result_goma);
					
					$total_peca+= $rs_goma->soma;
					$total_peca1+= $rs_goma->soma;
					
					$total_dia[$t]+= $rs_goma->soma;
					$total_dia1[$t]+= $rs_goma->soma;
					
					$total_geral_periodo+= $rs_goma->soma;
					$total_geral_periodo1+= $rs_goma->soma;
					
					$valor= $rs_goma->soma;
					$valor1= $rs_goma->soma;
					
				}
				//área limpa
				
				if (($_POST["modo"]==2) || ($_POST["modo"]==3)) {
					
					$result_pesagem= mysql_query("select ((num_pacotes*qtde_pacote)+qtde_pecas_sobra) as total from op_limpa_pesagem, op_limpa_pesagem_pecas
													where op_limpa_pesagem.id_pesagem = op_limpa_pesagem_pecas.id_pesagem
													and   op_limpa_pesagem.data_pesagem = '". $data_aqui ."'
													and   op_limpa_pesagem_pecas.id_tipo_roupa = '". $rs_peca->id_peca ."'
													and   op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_limpa_pesagem.id_cliente = '". $rs->id_cliente ."'
													and   op_limpa_pesagem.goma = '1'
													") or die(mysql_error());
					$rs_pesagem= mysql_fetch_object($result_pesagem);
					$total_pecas_peca= $rs_pesagem->total;
					
					/*$peso_total_peca= 0;
					$total_pacotes_peca= 0;
					$total_pecas_peca= 0;
					$peso_informa_extra="";
					
					while ($rs_pesagem= mysql_fetch_object($result_pesagem)) {
						//$peso_total_peca += $rs_pesagem->peso;
						//$total_pacotes_peca += $rs_pesagem->num_pacotes;
						//$total_pacotes_peca += $rs_pesagem->pacotes_sobra;
						
						//if ($rs_pesagem->qtde_pecas_sobra>0) $total_pacotes_peca++;
						
						$total_pecas_aqui= ($rs_pesagem->qtde_pacote*$rs_pesagem->num_pacotes)+$rs_pesagem->qtde_pecas_sobra;
						$total_pecas_peca += $total_pecas_aqui;
					}*/
					
					$total_peca+= $total_pecas_peca;
					$total_peca2+= $total_pecas_peca;
					
					$total_dia[$t]+= $total_pecas_peca;
					$total_dia2[$t]+= $total_pecas_peca;
					
					$total_geral_periodo+= $total_pecas_peca;
					$total_geral_periodo2+= $total_pecas_peca;
					
					$valor= $total_pecas_peca;
					$valor2= $total_pecas_peca;
				}
				
				if (($_POST["modo"]==1) || ($_POST["modo"]==2)) {
					$pdf->Cell($largura, 0.5, fnumi($valor), 1, 0, "C", $fill);
				}
				else {
					$pdf->Cell($largura, 0.5, fnumi($valor1), 1, 0, "C", $fill);
					
					$pdf->SetXY($pdf->GetX()-$largura, $pdf->GetY()+0.5);
					$pdf->Cell($largura, 0.5, fnumi($valor2), 1, 0, "C", $fill);
					
					$pdf->SetXY($pdf->GetX(), $pdf->GetY()-0.5);
				}
			}
			
			if (($_POST["modo"]==1) || ($_POST["modo"]==2)) {
				$pdf->Cell(1.2, $altura, fnumi($total_peca), 1, 1, "C", $fill);
			}
			else {
				$pdf->Cell(1.2, 0.5, fnumi($total_peca1), 1, 0, "C", $fill);
				
				$pdf->SetXY($pdf->GetX()-1.2, $pdf->GetY()+0.5);
				$pdf->Cell(1.2, 0.5, fnumi($total_peca2), 1, 0, "C", $fill);
				
				$pdf->SetXY(1.5, $pdf->GetY()+0.5);
			}
			
			$j++;
		}
		
		$pdf->SetFillColor(200,200,200);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(3, $altura, "TOTAL", 1 , 0, "L", 1);
		
		if ($_POST["modo"]==3) {
			$pdf->SetXY($pdf->GetX()-0.5, $pdf->GetY());
			$pdf->Cell(0.5, 0.5, "AS", 1, 0, "L", $fill);
			
			$pdf->SetXY($pdf->GetX()-0.5, $pdf->GetY()+0.5);
			$pdf->Cell(0.5, 0.5, "AL", 1, 0, "L", $fill);
			
			$pdf->SetXY($pdf->GetX(), $pdf->GetY()-0.5);
		}
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		//repetir todos os dias do intervalo
		for ($t=1; $t<=$total_dias_mes; $t++) {
			
			if (($_POST["modo"]==1) || ($_POST["modo"]==2)) {
				$pdf->Cell($largura, 0.5, fnumi($total_dia[$t]), 1, 0, "C", 1);
			}
			else {
				$pdf->Cell($largura, 0.5, fnumi($total_dia1[$t]), 1, 0, "C", $fill);
				
				$pdf->SetXY($pdf->GetX()-$largura, $pdf->GetY()+0.5);
				$pdf->Cell($largura, 0.5, fnumi($total_dia2[$t]), 1, 0, "C", $fill);
				
				$pdf->SetXY($pdf->GetX(), $pdf->GetY()-0.5);
			}
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		if (($_POST["modo"]==1) || ($_POST["modo"]==2)) {
			$pdf->Cell(1.2, $altura, fnumi($total_geral_periodo), 1, 1, "C", 1);
		}
		else {
			$pdf->Cell(1.2, 0.5, fnumi($total_geral_periodo1), 1, 0, "C", $fill);
			
			$pdf->SetXY($pdf->GetX()-1.2, $pdf->GetY()+0.5);
			$pdf->Cell(1.2, 0.5, fnumi($total_geral_periodo2), 1, 0, "C", $fill);
			
			$pdf->SetXY($pdf->GetX(), $pdf->GetY()-0.5);
		}
		
    }//fim while
	
	$pdf->AliasNbPages();
	$pdf->Output("goma_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>