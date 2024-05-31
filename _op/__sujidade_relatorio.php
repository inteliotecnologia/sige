<?
require_once("conexao.php");
require_once("funcoes.php");

//------------ gráfico

$GRAFICO_PATH= "includes/pchart/";

// Standard inclusions   
include($GRAFICO_PATH ."pChart/pData.class");
include($GRAFICO_PATH ."pChart/pChart.class");

if (pode("pl", $_SESSION["permissao"])) {
	
	//por ano
	if ($_POST["tipo_relatorio"]=="a") {
		define('FPDF_FONTPATH','includes/fpdf/font/');
		require("includes/fpdf/fpdf.php");
		require("includes/fpdf/modelo_paisagem.php");
		
		$pdf=new PDF("L", "cm", "A4");
		$pdf->SetMargins(1.5, 1.5, 1.5);
		$pdf->SetAutoPageBreak(true, 3);
		$pdf->SetFillColor(210,210,210);
		$pdf->AddFont('ARIALNARROW');
		$pdf->AddFont('ARIAL_N_NEGRITO');
		$pdf->AddFont('ARIAL_N_ITALICO');
		$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
		$pdf->SetFont('ARIALNARROW');
		
		//geral
		if ($_POST["modo"]==1) {
			
			$pdf->AddPage();
			
			$total_geral_periodo= 0;
			
			if ($_POST["dados"]==1) $tit_aux= " - PESO EMPRESA";
			else $tit_aux= " - PESO CLIENTE";
			
			$pdf->SetXY(16.9, 1.2);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "QUEDA DE SUJIDADE - GERAL". $tit_aux, 0, 1, "R");
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, $_POST["periodo"], 0, 1, "R");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
			$pdf->Cell(3, 0.6, "PERÍODO", 1, 0, 'C', 1);
			$pdf->Cell(3, 0.6, "ROUPA SUJA", 1, 0, 'C', 1);
			$pdf->Cell(3, 0.6, "ROUPA LIMPA", 1, 0, 'C', 1);
			$pdf->Cell(5, 0.6, "QUEDA DE SUJIDADE BRUTA", 1, 0, 'C', 1);
			$pdf->Cell(2, 0.6, "QUEDA (%)", 1, 0, 'C', 1);
			$pdf->Cell(3.6, 0.6, "RESÍDUOS", 1, 0, 'C', 1);
			$pdf->Cell(5, 0.6, "QUEDA DE SUJIDADE LÍQUIDA", 1, 0, 'C', 1);
			$pdf->Cell(2, 0.6, "QUEDA (%)", 1, 1, 'C', 1);
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->SetFillColor(235,235,235);
			$i=0;
			
			for ($i=1; $i<13; $i++) {
				if (($i%2)==0) $fill=1;
				else $fill= 0;
				
				if ($_POST["dados"]==1) {
					$result_suja= mysql_query("select sum(op_suja_pesagem.peso) as total_suja from op_suja_pesagem, op_suja_remessas
														where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
														and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["periodo"] ."'
														");
					$rs_suja= mysql_fetch_object($result_suja);
					
					
					$result_limpa= mysql_query("select sum(op_limpa_pesagem.peso) as total_limpa from op_limpa_pesagem
														where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   DATE_FORMAT(op_limpa_pesagem.data_pesagem, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["periodo"] ."'
														");
					$rs_limpa= mysql_fetch_object($result_limpa);
					
					/*
					//peso limpa externo
					
					$result_limpa= mysql_query("select sum(peso_total) as total_limpa from op_pedidos
												where (DATE_FORMAT(data_pedido, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["periodo"] ."' )
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												") or die(mysql_error());
					$rs_limpa= mysql_fetch_object($result_limpa);
					*/
					
					$result_residuos= mysql_query("select sum(op_suja_devolucao.peso) as total_residuos from op_suja_devolucao, op_suja_remessas
														where op_suja_devolucao.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   op_suja_devolucao.id_remessa = op_suja_remessas.id_remessa
														and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["periodo"] ."'
														");
					$rs_residuos= mysql_fetch_object($result_residuos);
				}
				//peso cliente
				else {
					$result_suja= mysql_query("select sum(tr_percursos_passos.peso) as total_suja from tr_percursos, tr_percursos_passos
														where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["periodo"] ."'
														and   tr_percursos_passos.passo = '2'
														and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
														") or die(mysql_error());
					$rs_suja= mysql_fetch_object($result_suja);
					
					$result_limpa= mysql_query("select sum(tr_percursos_passos.peso) as total_limpa from tr_percursos, tr_percursos_passos
														where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["periodo"] ."'
														and   tr_percursos_passos.passo = '2'
														and   (tr_percursos.tipo = '2' or tr_percursos.tipo = '5')
														");
					$rs_limpa= mysql_fetch_object($result_limpa);
					

				}
				$queda_sujidade_bruta= ($rs_suja->total_suja-$rs_limpa->total_limpa);
				
				if ($rs_suja->total_suja>0) $percent_queda_bruta= (($queda_sujidade_bruta*100)/$rs_suja->total_suja);
				else $percent_queda_bruta= 0;
				
				$queda_sujidade_liquida= ($rs_suja->total_suja-$rs_limpa->total_limpa-$rs_residuos->total_residuos);
				
				if ($rs_suja->total_suja>0) $percent_queda_liquida= (($queda_sujidade_liquida*100)/$rs_suja->total_suja);
				else $percent_queda_liquida= 0;
				
				$pdf->Cell(3, 0.6, traduz_mes($i) ."/". $_POST["periodo"], 1, 0, 'C', $fill);
				$pdf->Cell(3, 0.6, fnum($rs_suja->total_suja) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(3, 0.6, fnum($rs_limpa->total_limpa) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(5, 0.6, fnum($queda_sujidade_bruta) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(2, 0.6, fnum($percent_queda_bruta) ." %", 1, 0, 'C', $fill);
				$pdf->Cell(3.6, 0.6, fnum($rs_residuos->total_residuos) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(5, 0.6, fnum($queda_sujidade_liquida) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(2, 0.6, fnum($percent_queda_liquida) ." %", 1, 1, 'C', $fill);
			}
			
			$i++;
			
		}
		//por cliente
		else {
			
			$total_clientes= count($_POST["id_cliente"]);
		
			$i=0;
			while ($_POST["id_cliente"][$i]!="") {
				
				$str = " and   id_cliente =  '". $_POST["id_cliente"][$i] ."'";
			
				$titulo_pagina= " - ". pega_sigla_pessoa($_POST["id_cliente"][$i]);
				
				$pdf->AddPage();
			
				$total_geral_periodo= 0;
				
				if ($_POST["dados"]==1) $tit_aux= " - PESO EMPRESA";
				else $tit_aux= " - PESO CLIENTE";
				
				$pdf->SetXY(16.9, 1.2);
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
				$pdf->Cell(0, 0.6, "QUEDA DE SUJIDADE" . $titulo_pagina . $tit_aux, 0, 1, "R");
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.6, $_POST["periodo"], 0, 1, "R");
				
				$pdf->Ln();$pdf->Ln();
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
					
				$pdf->Cell(3, 0.6, "PERÍODO", 1, 0, 'C', 1);
				$pdf->Cell(3, 0.6, "ROUPA SUJA", 1, 0, 'C', 1);
				$pdf->Cell(3, 0.6, "ROUPA LIMPA", 1, 0, 'C', 1);
				$pdf->Cell(5, 0.6, "QUEDA DE SUJIDADE BRUTA", 1, 0, 'C', 1);
				$pdf->Cell(2, 0.6, "QUEDA (%)", 1, 0, 'C', 1);
				$pdf->Cell(3.6, 0.6, "RESÍDUOS", 1, 0, 'C', 1);
				$pdf->Cell(5, 0.6, "QUEDA DE SUJIDADE LÍQUIDA", 1, 0, 'C', 1);
				$pdf->Cell(2, 0.6, "QUEDA (%)", 1, 1, 'C', 1);
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->SetFillColor(235,235,235);
				
				for ($j=1; $j<13; $j++) {
					if (($j%2)==0) $fill=1;
					else $fill= 0;
					
					if ($_POST["dados"]==1) {
						$result_suja= mysql_query("select sum(op_suja_pesagem.peso) as total_suja from op_suja_pesagem, op_suja_remessas
															where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
															$str
															and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
															and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															");
						$rs_suja= mysql_fetch_object($result_suja);
						
						$result_limpa= mysql_query("select sum(op_limpa_pesagem.peso) as total_limpa from op_limpa_pesagem
															where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
															$str
															and   DATE_FORMAT(op_limpa_pesagem.data_pesagem, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															");
						$rs_limpa= mysql_fetch_object($result_limpa);
						
						$result_residuos= mysql_query("select sum(op_suja_devolucao.peso) as total_residuos from op_suja_devolucao, op_suja_remessas
															where op_suja_devolucao.id_empresa = '". $_SESSION["id_empresa"] ."' 
															$str
															and   op_suja_devolucao.id_remessa = op_suja_remessas.id_remessa
															and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															");
						$rs_residuos= mysql_fetch_object($result_residuos);
					}
					else {
						
						/*
						$result_suja= mysql_query("select sum(tr_percursos_passos.peso) as total_suja from tr_percursos, tr_percursos_passos
														where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["periodo"] ."'
														and   tr_percursos_passos.passo = '2'
														and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
														") or die(mysql_error());
					$rs_suja= mysql_fetch_object($result_suja);
					
					$result_limpa= mysql_query("select sum(tr_percursos_passos.peso) as total_limpa from tr_percursos, tr_percursos_passos
														where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["periodo"] ."'
														and   tr_percursos_passos.passo = '2'
														and   (tr_percursos.tipo = '2' or tr_percursos.tipo = '5')
														");
					$rs_limpa= mysql_fetch_object($result_limpa);
					
					
					*/
						
						
						$result_suja= mysql_query("select sum(tr_percursos_passos.peso) as total_suja from tr_percursos, tr_percursos_passos
															where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
															and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
															and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															and   tr_percursos_passos.passo = '2'
															and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
															$str
															") or die(mysql_error());
															
						$rs_suja= mysql_fetch_object($result_suja);
						
						$result_limpa= mysql_query("select sum(tr_percursos_passos.peso) as total_limpa from tr_percursos, tr_percursos_passos
															where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
															and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
															and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															and   tr_percursos_passos.passo = '2'
															and   (tr_percursos.tipo = '2' or tr_percursos.tipo = '5')
															$str
															");
						$rs_limpa= mysql_fetch_object($result_limpa);
					}
					
					$queda_sujidade_bruta= ($rs_suja->total_suja-$rs_limpa->total_limpa);
					
					if ($rs_suja->total_suja>0) $percent_queda_bruta= (($queda_sujidade_bruta*100)/$rs_suja->total_suja);
					else $percent_queda_bruta= 0;
					
					$queda_sujidade_liquida= ($rs_suja->total_suja-$rs_limpa->total_limpa-$rs_residuos->total_residuos);
					
					if ($rs_suja->total_suja>0) $percent_queda_liquida= (($queda_sujidade_liquida*100)/$rs_suja->total_suja);
					else $percent_queda_liquida= 0;
					
					$pdf->Cell(3, 0.6, traduz_mes($j) ."/". $_POST["periodo"], 1, 0, 'C', $fill);
					$pdf->Cell(3, 0.6, fnum($rs_suja->total_suja) ." kg", 1, 0, 'C', $fill);
					$pdf->Cell(3, 0.6, fnum($rs_limpa->total_limpa) ." kg", 1, 0, 'C', $fill);
					$pdf->Cell(5, 0.6, fnum($queda_sujidade_bruta) ." kg", 1, 0, 'C', $fill);
					$pdf->Cell(2, 0.6, fnum($percent_queda_bruta) ." %", 1, 0, 'C', $fill);
					$pdf->Cell(3.6, 0.6, fnum($rs_residuos->total_residuos) ." kg", 1, 0, 'C', $fill);
					$pdf->Cell(5, 0.6, fnum($queda_sujidade_liquida) ." kg", 1, 0, 'C', $fill);
					$pdf->Cell(2, 0.6, fnum($percent_queda_liquida) ." %", 1, 1, 'C', $fill);
				}
				
				$i++;
			}
		}
	}
	//por mes
	elseif ($_POST["tipo_relatorio"]=="m") {
		
		define('FPDF_FONTPATH','includes/fpdf/font/');
		require("includes/fpdf/fpdf.php");
		require("includes/fpdf/modelo_paisagem.php");
	
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
		
		$pdf=new PDF("L", "cm", "A4");
		$pdf->SetMargins(1.5, 1.5, 1.5);
		$pdf->SetAutoPageBreak(true, 3);
		$pdf->SetFillColor(210,210,210);
		$pdf->AddFont('ARIALNARROW');
		$pdf->AddFont('ARIAL_N_NEGRITO');
		$pdf->AddFont('ARIAL_N_ITALICO');
		$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
		$pdf->SetFont('ARIALNARROW');
		
		
		//geral
		if ($_POST["modo"]==1) {
			
			$pdf->AddPage();
			
			$total_geral_periodo= 0;
			
			if ($_POST["dados"]==1) $tit_aux= " - PESO EMPRESA";
			else $tit_aux= " - PESO CLIENTE";
			
			$pdf->SetXY(16.9, 1.2);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "QUEDA DE SUJIDADE - GERAL". $tit_aux, 0, 1, "R");
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, $data1f ." à ". $data2f, 0, 1, "R");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
			$pdf->Cell(3, 0.6, "PERÍODO", 1, 0, 'C', 1);
			$pdf->Cell(3, 0.6, "ROUPA SUJA", 1, 0, 'C', 1);
			$pdf->Cell(3, 0.6, "ROUPA LIMPA", 1, 0, 'C', 1);
			$pdf->Cell(5, 0.6, "QUEDA DE SUJIDADE BRUTA", 1, 0, 'C', 1);
			$pdf->Cell(2, 0.6, "QUEDA (%)", 1, 0, 'C', 1);
			$pdf->Cell(3.6, 0.6, "RESÍDUOS", 1, 0, 'C', 1);
			$pdf->Cell(5, 0.6, "QUEDA DE SUJIDADE LÍQUIDA", 1, 0, 'C', 1);
			$pdf->Cell(2, 0.6, "QUEDA (%)", 1, 1, 'C', 1);
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->SetFillColor(235,235,235);
			$i=0;
			
			//for ($i=1; $i<13; $i++) {
				if (($i%2)!=0) $fill=1;
				else $fill= 0;
				
				//peso empresa
				if ($_POST["dados"]==1) {
					$result_suja= mysql_query("select sum(op_suja_pesagem.peso) as total_suja from op_suja_pesagem, op_suja_remessas
														where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
														and   op_suja_remessas.data_remessa >= '". $data1 ."'
														and   op_suja_remessas.data_remessa <= '". $data2 ."'
														");
					$rs_suja= mysql_fetch_object($result_suja);
					
					$result_limpa= mysql_query("select sum(op_limpa_pesagem.peso) as total_limpa from op_limpa_pesagem
														where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   op_limpa_pesagem.data_pesagem >= '". $data1 ."'
														and   op_limpa_pesagem.data_pesagem <= '". $data2 ."'
														
														");
					$rs_limpa= mysql_fetch_object($result_limpa);
					
					$result_residuos= mysql_query("select sum(op_suja_devolucao.peso) as total_residuos from op_suja_devolucao, op_suja_remessas
														where op_suja_devolucao.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   op_suja_devolucao.id_remessa = op_suja_remessas.id_remessa
														and   op_suja_remessas.data_remessa >= '". $data1 ."'
														and   op_suja_remessas.data_remessa <= '". $data2 ."'
														");
					$rs_residuos= mysql_fetch_object($result_residuos);
				}
				//peso cliente
				else {
					$result_suja= mysql_query("select sum(tr_percursos_passos.peso) as total_suja from tr_percursos, tr_percursos_passos
														where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														
														and   tr_percursos.data_hora_percurso >= '". $data1 ."'
														and   tr_percursos.data_hora_percurso <= '". $data2 ."'
														
														and   tr_percursos_passos.passo = '2'
														and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
														") or die(mysql_error());
					$rs_suja= mysql_fetch_object($result_suja);
					
					$result_limpa= mysql_query("select sum(tr_percursos_passos.peso) as total_limpa from tr_percursos, tr_percursos_passos
														where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														and   tr_percursos.data_hora_percurso >= '". $data1 ."'
														and   tr_percursos.data_hora_percurso <= '". $data2 ."'
														and   tr_percursos_passos.passo = '2'
														and   (tr_percursos.tipo = '2' or tr_percursos.tipo = '5')
														");
					$rs_limpa= mysql_fetch_object($result_limpa);
					

				}
				$queda_sujidade_bruta= ($rs_suja->total_suja-$rs_limpa->total_limpa);
				
				if ($rs_suja->total_suja>0) $percent_queda_bruta= (($queda_sujidade_bruta*100)/$rs_suja->total_suja);
				else $percent_queda_bruta= 0;
				
				$queda_sujidade_liquida= ($rs_suja->total_suja-$rs_limpa->total_limpa-$rs_residuos->total_residuos);
				
				if ($rs_suja->total_suja>0) $percent_queda_liquida= (($queda_sujidade_liquida*100)/$rs_suja->total_suja);
				else $percent_queda_liquida= 0;
				
				$pdf->Cell(3, 0.6, $data1f ." à ". $data2f, 1, 0, 'C', $fill);
				$pdf->Cell(3, 0.6, fnum($rs_suja->total_suja) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(3, 0.6, fnum($rs_limpa->total_limpa) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(5, 0.6, fnum($queda_sujidade_bruta) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(2, 0.6, fnum($percent_queda_bruta) ." %", 1, 0, 'C', $fill);
				$pdf->Cell(3.6, 0.6, fnum($rs_residuos->total_residuos) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(5, 0.6, fnum($queda_sujidade_liquida) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(2, 0.6, fnum($percent_queda_liquida) ." %", 1, 1, 'C', $fill);
			//}
			
			$i++;
			
		}
		//por cliente
		else {
		
			$periodo= explode("/", $_POST["periodo"]);
			$periodo_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
			
			$total_dias_mes= date("t", $periodo_mk);
		
			$pdf->AddPage();
			
			$total_geral_periodo= 0;
			
			if ($_POST["dados"]==1) $tit_aux= " - PESO EMPRESA";
			else $tit_aux= " - PESO CLIENTE";
			
			$pdf->SetXY(16.9, 1.2);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "QUEDA DE SUJIDADE". $tit_aux, 0, 1, "R");
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ."/". $periodo2[1], 0, 1, "R");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
			$pdf->Cell(3.5, 0.6, "CLIENTE", 1, 0, 'L', 1);
			$pdf->Cell(3, 0.6, "ROUPA SUJA", 1, 0, 'C', 1);
			$pdf->Cell(3, 0.6, "ROUPA LIMPA", 1, 0, 'C', 1);
			$pdf->Cell(5, 0.6, "QUEDA DE SUJ. BRUTA", 1, 0, 'C', 1);
			$pdf->Cell(2, 0.6, "QUEDA (%)", 1, 0, 'C', 1);
			$pdf->Cell(3, 0.6, "RESÍDUOS", 1, 0, 'C', 1);
			$pdf->Cell(5, 0.6, "QUEDA DE SU. LÍQUIDA", 1, 0, 'C', 1);
			$pdf->Cell(2, 0.6, "QUEDA (%)", 1, 1, 'C', 1);
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->SetFillColor(235,235,235);
			
			
			$i=0;
			while ($_POST["id_cliente"][$i]!="") {
				
				if (($i%2)==1) $fill=1;
				else $fill= 0;
				
				//peso empresa
				if ($_POST["dados"]==1) {
					$result_suja= mysql_query("select sum(op_suja_pesagem.peso) as total_suja from op_suja_pesagem, op_suja_remessas
														where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   id_cliente = '". $_POST["id_cliente"][$i] ."'
														and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
														and   op_suja_remessas.data_remessa >= '". $data1 ."'
														and   op_suja_remessas.data_remessa <= '". $data2 ."'
														");
					$rs_suja= mysql_fetch_object($result_suja);
					
					$result_limpa= mysql_query("select sum(op_limpa_pesagem.peso) as total_limpa from op_limpa_pesagem
														where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   id_cliente = '". $_POST["id_cliente"][$i] ."'
														and   op_limpa_pesagem.data_pesagem >= '". $data1 ."'
														and   op_limpa_pesagem.data_pesagem <= '". $data2 ."'
														");
					$rs_limpa= mysql_fetch_object($result_limpa);
					
					$result_residuos= mysql_query("select sum(op_suja_devolucao.peso) as total_residuos from op_suja_devolucao, op_suja_remessas
														where op_suja_devolucao.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   id_cliente = '". $_POST["id_cliente"][$i] ."'
														and   op_suja_devolucao.id_remessa = op_suja_remessas.id_remessa
														and   op_suja_remessas.data_remessa >= '". $data1 ."'
														and   op_suja_remessas.data_remessa <= '". $data2 ."'
														");
					$rs_residuos= mysql_fetch_object($result_residuos);
				}
				//peso cliente
				else {
					$result_suja= mysql_query("select sum(tr_percursos_passos.peso) as total_suja from tr_percursos, tr_percursos_passos
														where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														
														and   tr_percursos.data_hora_percurso >= '". $data1 ."'
														and   tr_percursos.data_hora_percurso <= '". $data2 ."'
														
														and   tr_percursos_passos.passo = '2'
														and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
														and   tr_percursos_passos.id_cliente = '". $_POST["id_cliente"][$i] ."'
														") or die(mysql_error());
					$rs_suja= mysql_fetch_object($result_suja);
					
					$result_limpa= mysql_query("select sum(tr_percursos_passos.peso) as total_limpa from tr_percursos, tr_percursos_passos
														where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														and   tr_percursos.data_hora_percurso >= '". $data1 ."'
														and   tr_percursos.data_hora_percurso <= '". $data2 ."'
														and   tr_percursos_passos.passo = '2'
														and   (tr_percursos.tipo = '2' or tr_percursos.tipo = '5')
														and   tr_percursos_passos.id_cliente = '". $_POST["id_cliente"][$i] ."'
														");
					$rs_limpa= mysql_fetch_object($result_limpa);
				}
				
				$queda_sujidade_bruta= ($rs_suja->total_suja-$rs_limpa->total_limpa);
				
				if ($rs_suja->total_suja>0) $percent_queda_bruta= (($queda_sujidade_bruta*100)/$rs_suja->total_suja);
				else $percent_queda_bruta= 0;
				
				$queda_sujidade_liquida= ($rs_suja->total_suja-$rs_limpa->total_limpa-$rs_residuos->total_residuos);
				
				if ($rs_suja->total_suja>0) $percent_queda_liquida= (($queda_sujidade_liquida*100)/$rs_suja->total_suja);
				else $percent_queda_liquida= 0;
				
				$pdf->Cell(3.5, 0.6, pega_sigla_pessoa($_POST["id_cliente"][$i]), 1, 0, 'L', $fill);
				$pdf->Cell(3, 0.6, fnum($rs_suja->total_suja) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(3, 0.6, fnum($rs_limpa->total_limpa) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(5, 0.6, fnum($queda_sujidade_bruta) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(2, 0.6, fnum($percent_queda_bruta) ." %", 1, 0, 'C', $fill);
				$pdf->Cell(3, 0.6, fnum($rs_residuos->total_residuos) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(5, 0.6, fnum($queda_sujidade_liquida) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(2, 0.6, fnum($percent_queda_liquida) ." %", 1, 1, 'C', $fill);
				
				$i++;
			}
		}
		
	}
	//porcentagem
	elseif ($_POST["tipo_relatorio"]=="p") {
		
		
		define('FPDF_FONTPATH','includes/fpdf/font/');
		require("includes/fpdf/fpdf.php");
		require("includes/fpdf/modelo_paisagem.php");
		
		$pdf=new PDF("L", "cm", "A4");
		$pdf->SetMargins(1.5, 1.5, 1.5);
		$pdf->SetAutoPageBreak(true, 3);
		$pdf->SetFillColor(210,210,210);
		$pdf->AddFont('ARIALNARROW');
		$pdf->AddFont('ARIAL_N_NEGRITO');
		$pdf->AddFont('ARIAL_N_ITALICO');
		$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
		$pdf->SetFont('ARIALNARROW');
		
		$pdf->AddPage();
		
		$total_geral_periodo= 0;
		
		if ($_POST["dados"]==1) $tit_aux= " - PESO EMPRESA";
		else $tit_aux= " - PESO CLIENTE";
		
		$pdf->SetXY(16.9, 1.2);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "QUEDA DE SUJIDADE (%)". $tit_aux, 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $_POST["periodo"], 0, 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
		$pdf->Cell(2, 0.6, "Cliente", 1, 0, 'C', 1);
		
		for ($j=1; $j<13; $j++) {
			if ($j==12) $quebra=1;
			else $quebra=0;
			
			$pdf->Cell(2.05, 0.6, traduz_mes($j), 1, $quebra, 'C', 1);
		}
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(235,235,235);
		
		
		//geral
		if ($_POST["modo"]==1) {
			
					
			$pdf->Cell(2, 0.6, "Todos", 1, 0, 'C', $fill);
				
			for ($j=1; $j<13; $j++) {
				if ($j==12) $quebra=1;
				else $quebra=0;
			
				if ($_POST["dados"]==1) {
					$result_suja= mysql_query("select sum(op_suja_pesagem.peso) as total_suja from op_suja_pesagem, op_suja_remessas
														where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
														and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
														");
					$rs_suja= mysql_fetch_object($result_suja);
					
					$result_limpa= mysql_query("select sum(op_limpa_pesagem.peso) as total_limpa from op_limpa_pesagem
														where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   DATE_FORMAT(op_limpa_pesagem.data_pesagem, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
														");
					$rs_limpa= mysql_fetch_object($result_limpa);
					
					$result_residuos= mysql_query("select sum(op_suja_devolucao.peso) as total_residuos from op_suja_devolucao, op_suja_remessas
														where op_suja_devolucao.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   op_suja_devolucao.id_remessa = op_suja_remessas.id_remessa
														and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
														");
					$rs_residuos= mysql_fetch_object($result_residuos);
				}
				//peso cliente
				else {
					$result_suja= mysql_query("select sum(tr_percursos_passos.peso) as total_suja from tr_percursos, tr_percursos_passos
														where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
														and   tr_percursos_passos.passo = '2'
														and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
														") or die(mysql_error());
					$rs_suja= mysql_fetch_object($result_suja);
					
					$result_limpa= mysql_query("select sum(tr_percursos_passos.peso) as total_limpa from tr_percursos, tr_percursos_passos
														where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
														and   tr_percursos_passos.passo = '2'
														and   (tr_percursos.tipo = '2' or tr_percursos.tipo = '5')
														");
					$rs_limpa= mysql_fetch_object($result_limpa);
	
				}
				
				$queda_sujidade_bruta= ($rs_suja->total_suja-$rs_limpa->total_limpa);
				
				if ($rs_suja->total_suja>0) $percent_queda_bruta= (($queda_sujidade_bruta*100)/$rs_suja->total_suja);
				else $percent_queda_bruta= 0;
				
				$grafico_dado[$i][$j]= $percent_queda_bruta;
				
				/*$queda_sujidade_liquida= ($rs_suja->total_suja-$rs_limpa->total_limpa-$rs_residuos->total_residuos);
				
				if ($rs_suja->total_suja>0) $percent_queda_liquida= (($queda_sujidade_liquida*100)/$rs_suja->total_suja);
				else $percent_queda_liquida= 0;
				*/
				
				
				$pdf->Cell(2.05, 0.6, fnum($percent_queda_bruta) ."%", 1, $quebra, 'C', $fill);
			
				$i++;
			} //fim for
		}
		//por cliente
		else {
		
			$i=0;
			while ($_POST["id_cliente"][$i]!="") {
				if (($i%2)!=0) $fill=1;
				else $fill= 0;
				
				$pdf->Cell(2, 0.6, pega_sigla_pessoa($_POST["id_cliente"][$i]), 1, 0, 'C', $fill);
				
				for ($j=1; $j<13; $j++) {
					if ($j==12) $quebra=1;
					else $quebra=0;
					
					
					if ($_POST["dados"]==1) {
						$result_suja= mysql_query("select sum(op_suja_pesagem.peso) as total_suja from op_suja_pesagem, op_suja_remessas
															where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
															and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
															and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															and   op_suja_pesagem.id_cliente = '". $_POST["id_cliente"][$i] ."'
															");
						$rs_suja= mysql_fetch_object($result_suja);
						
						$result_limpa= mysql_query("select sum(op_limpa_pesagem.peso) as total_limpa from op_limpa_pesagem
															where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
															and   DATE_FORMAT(op_limpa_pesagem.data_pesagem, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															and   op_limpa_pesagem.id_cliente = '". $_POST["id_cliente"][$i] ."'
															");
						$rs_limpa= mysql_fetch_object($result_limpa);
						
						$result_residuos= mysql_query("select sum(op_suja_devolucao.peso) as total_residuos from op_suja_devolucao, op_suja_remessas
															where op_suja_devolucao.id_empresa = '". $_SESSION["id_empresa"] ."' 
															and   op_suja_devolucao.id_remessa = op_suja_remessas.id_remessa
															and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															and   op_suja_devolucao.id_cliente = '". $_POST["id_cliente"][$i] ."'
															");
						$rs_residuos= mysql_fetch_object($result_residuos);
					}
					//peso cliente
					else {
						$result_suja= mysql_query("select sum(tr_percursos_passos.peso) as total_suja from tr_percursos, tr_percursos_passos
															where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
															and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
															and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															and   tr_percursos_passos.passo = '2'
															and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
															and   tr_percursos_passos.id_cliente = '". $_POST["id_cliente"][$i] ."'
															") or die(mysql_error());
						$rs_suja= mysql_fetch_object($result_suja);
						
						$result_limpa= mysql_query("select sum(tr_percursos_passos.peso) as total_limpa from tr_percursos, tr_percursos_passos
															where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."' 
															and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
															and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%m/%Y') = '". formata_saida($j, 2) ."/". $_POST["periodo"] ."'
															and   tr_percursos_passos.passo = '2'
															and   (tr_percursos.tipo = '2' or tr_percursos.tipo = '5')
															and   tr_percursos_passos.id_cliente = '". $_POST["id_cliente"][$i] ."'
															");
						$rs_limpa= mysql_fetch_object($result_limpa);
						
		
					}
					$queda_sujidade_bruta= ($rs_suja->total_suja-$rs_limpa->total_limpa);
					
					if ($rs_suja->total_suja>0) $percent_queda_bruta= (($queda_sujidade_bruta*100)/$rs_suja->total_suja);
					else $percent_queda_bruta= 0;
					
					$grafico_dado[$i][$j]= $percent_queda_bruta;
					
					/*$queda_sujidade_liquida= ($rs_suja->total_suja-$rs_limpa->total_limpa-$rs_residuos->total_residuos);
					
					if ($rs_suja->total_suja>0) $percent_queda_liquida= (($queda_sujidade_liquida*100)/$rs_suja->total_suja);
					else $percent_queda_liquida= 0;
					*/
					
					
					
					
					$pdf->Cell(2.05, 0.6, fnum($percent_queda_bruta) ."%", 1, $quebra, 'C', $fill);
				}
				
				$i++;
			}
			
			
			$i=0;
			while ($_POST["id_cliente"][$i]!="") {
				
				$pdf->AddPage();
				
				// Dataset definition 
				$DataSet = new pData;
				$DataSet->AddPoint($grafico_dado[$i],"Serie1");
				$DataSet->AddPoint(array("Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"),"Serie2"); 
				$DataSet->AddSerie("Serie1");
				$DataSet->SetAbsciseLabelSerie("Serie2"); 
				$DataSet->SetSerieName("Queda de sujidade","Serie1");
				$DataSet->SetYAxisUnit("%");
				
				// Initialise the graph
				$Test= new pChart(800,450);
				$Test->setColorPalette(0,35,35,142);
				$Test->setFontProperties($GRAFICO_PATH ."Fonts/ArialNarrow.ttf",11);
				$Test->setGraphArea(75,50,740,300);
				
				$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
				$Test->drawGrid(4,TRUE,230,230,230,50);
				
				// Draw the line graph
				$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
				$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);
				
				// Finish the graph
				$Test->setFontProperties($GRAFICO_PATH ."Fonts/ArialNarrowBold.ttf",12);
				$Test->drawTitle(100,30,"QUEDA DE SUJIDADE - ". pega_sigla_pessoa($_POST["id_cliente"][$i]) . " - ". $_POST["periodo"],50,50,50,585);
				$ar= gera_auth();
				$Test->Render("uploads/grafico_". $ar .".png");
				
				if (file_exists("uploads/grafico_". $ar .".png")) {
					$pdf->Image("uploads/grafico_". $ar .".png", $pdf->GetX()+0.5, $pdf->GetY()+1, 16, 9);
					unlink("uploads/grafico_". $ar .".png");
				}
				
				$pdf->Ln();
				
				$i++;
			}
		}//fim else
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("sujidade_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>