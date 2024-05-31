<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("ps", $_SESSION["permissao"])) {
	
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
	
	if ($_POST["id_equipamento"]!="") $str_equipamento= "and   op_equipamentos.id_equipamento = '". $_POST["id_equipamento"] ."' ";
	
	if ($_POST["tipo_relatorio"]=="d") {
		
		$result_equi= mysql_query("select distinct(op_equipamentos.id_equipamento), equipamento from op_equipamentos, op_suja_lavagem
									where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   op_equipamentos.tipo_equipamento = '1'
									and   op_suja_lavagem.id_equipamento = op_equipamentos.id_equipamento
									and   op_suja_lavagem.data_lavagem = '". formata_data($_POST["data"]) ."' 
									$str_equipamento
									order by equipamento asc
									 ") or die(mysql_error());
		$i=0;
		while ($rs_equi = mysql_fetch_object($result_equi)) {
		
			$pdf->AddPage();
			$pdf->SetXY(7,1.75);
		
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.55, "RELATÓRIO DE MÁQUINA PARADA - ". $rs_equi->equipamento, 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.55, $_POST["data"], 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$ontem= soma_data($_POST["data"], -1, 0, 0);
			
			$result_lavagem_anterior= mysql_query("select * from op_suja_lavagem, op_suja_remessas
													where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
													and   op_suja_lavagem.data_lavagem = '". formata_data($ontem) ."'
													and   op_suja_lavagem.id_equipamento = '". $rs_equi->id_equipamento ."'
													order by op_suja_lavagem.data_lavagem desc, op_suja_lavagem.hora_lavagem desc
													limit 1
													") or die(mysql_error());
			
			$rs_lavagem_anterior= mysql_fetch_object($result_lavagem_anterior);
			
			$data_fim[-1]= $rs_lavagem_anterior->data_fim_lavagem ." ". $rs_lavagem_anterior->hora_fim_lavagem;
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->Cell(5.5, 0.55, "FIM DA ÚLTIMA LAVAGEM DO DIA ANTERIOR:", 0, 0, 'L');
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->Cell(0, 0.55, desformata_data($rs_lavagem_anterior->data_fim_lavagem) ." ". $rs_lavagem_anterior->hora_fim_lavagem, 0, 1, 'L');
			
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(1.5, 0.55, "CÓD.", 1, 0, 'C', 1);
			$pdf->Cell(3, 0.55, "TEMPO PARADA", 1, 0, 'C', 1);
			$pdf->Cell(3, 0.55, "INÍCIO", 1, 0, 'C', 1);
			$pdf->Cell(3, 0.55, "FIM", 1, 0, 'C', 1);
			$pdf->Cell(1.5, 0.55, "PROC.", 1, 0, 'C', 1);
			$pdf->Cell(2, 0.55, "PESO", 1, 0, 'C', 1);
			$pdf->Cell(3, 0.55, "LAVADOR", 1, 1, 'C', 1);
			
			$total_parada=0;
			
			$result_lavagem= mysql_query("select * from op_suja_lavagem, op_suja_remessas
											where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
											and   op_suja_lavagem.data_lavagem = '". formata_data($_POST["data"]) ."'
											and   op_suja_lavagem.id_equipamento = '". $rs_equi->id_equipamento ."'
											order by op_suja_lavagem.data_lavagem asc, op_suja_lavagem.hora_lavagem asc
											") or die(mysql_error());
			
			$d=0;
			while ($rs_lavagem= mysql_fetch_object($result_lavagem)) {
				
				$id_funcionario_aqui[$d]= $rs_lavagem->id_funcionario;
				
				if (($d>0) && ($id_funcionario_aqui[$d]!=$id_funcionario_aqui[$d-1])) {
					$pdf->Cell(1.5, 0.55, "", 0, 0);
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
					$pdf->Cell(3, 0.55, calcula_total_horas($total_parada_funcionario_aqui[$id_funcionario_aqui[$d-1]]), 1, 1, 'C', !$fill);
					
					$pdf->SetFont('ARIALNARROW', '', 8);
					$pdf->LittleLn();
				}
				
				$pdf->SetFillColor(235,235,235);
				$pdf->SetFont('ARIALNARROW', '', 8);
				
				if (($d%2)==1) $fill= 0;
				else $fill= 1;
				
				$data_fim[$d]= $rs_lavagem->data_fim_lavagem ." ". $rs_lavagem->hora_fim_lavagem;
				$data_inicio[$d]= $rs_lavagem->data_lavagem ." ". $rs_lavagem->hora_lavagem;
				
				if ($data_fim[$d-1]!=" ") $tempo_parada= @retorna_intervalo($data_fim[$d-1], $data_inicio[$d]);
				else $tempo_parada= 0;
				
				if ($tempo_parada<0) $tempo_parada=0;
				
				$total_parada+= $tempo_parada;
				
				$total_parada_funcionario_aqui[$rs_lavagem->id_funcionario]+= $tempo_parada;
				$total_parada_funcionario_total[$rs_lavagem->id_funcionario]+= $tempo_parada;
				
				//echo $data_fim[$d-1] ." -> ". $data_inicio[$d] ." = ". calcula_total_horas($tempo_parada) ."<br />";
				
				$pdf->Cell(1.5, 0.55, $rs_lavagem->id_lavagem, 1, 0, 'C', $fill);
				$pdf->Cell(3, 0.55, calcula_total_horas($tempo_parada), 1, 0, 'C', $fill);
				$pdf->Cell(3, 0.55, desformata_data($rs_lavagem->data_lavagem) ." ". $rs_lavagem->hora_lavagem, 1, 0, 'C', $fill);
				$pdf->Cell(3, 0.55, desformata_data($rs_lavagem->data_fim_lavagem) ." ". $rs_lavagem->hora_fim_lavagem, 1, 0, 'C', $fill);
				$pdf->Cell(1.5, 0.55, pega_processo($rs_lavagem->id_processo), 1, 0, 'C', $fill);
				
				$result_cestos_peso= mysql_query("select sum(peso) as peso from op_suja_lavagem_cestos
													where id_lavagem = '". $rs_lavagem->id_lavagem ."'
													");
				$rs_cestos_peso= mysql_fetch_object($result_cestos_peso);
				
				$peso_total+=$rs_cestos_peso->peso;
				
				$pdf->Cell(2, 0.55, fnum($rs_cestos_peso->peso) ." kg", 1, 0, 'C', $fill);
				$pdf->Cell(3, 0.55, primeira_palavra(pega_funcionario($rs_lavagem->id_funcionario)), 1, 1, 'C', $fill);
				
				$d++;
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$pdf->Cell(1.5, 0.55, "", 0, 0, 'C');
			$pdf->Cell(3, 0.55, calcula_total_horas($total_parada), 1, 0, 'C', !$fill);
			$pdf->Cell(0, 0.55, "", 0, 0, 'C');
			
			$pdf->Ln();
			
			unset($total_parada_funcionario_aqui);
		}
		
		$pdf->AddPage();
		
		$pdf->SetXY(7,1.75);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.55, "RELATÓRIO DE MÁQUINA PARADA", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.55, $_POST["data"], 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$result_func= mysql_query("select distinct(op_suja_lavagem.id_funcionario) from op_suja_lavagem
									where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   op_suja_lavagem.data_lavagem = '". formata_data($_POST["data"]) ."' 
									 ") or die(mysql_error());
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		$pdf->SetFillColor(210,210,210);
		
		$pdf->Cell(8, 0.55, "LAVADOR", 1, 0, 'L', 1);
		$pdf->Cell(4, 0.55, "TEMPO PARADA", 1, 1, 'C', 1);
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->SetFillColor(235,235,235);
		
		$i=0;
		while ($rs_func= mysql_fetch_object($result_func)) {
			if (($i%2)==1) $fill= 0;
			else $fill= 1;
			
			$pdf->Cell(8, 0.55, pega_funcionario($rs_func->id_funcionario), 1, 0, 'L', $fill);
			$pdf->Cell(4, 0.55, calcula_total_horas($total_parada_funcionario_total[$rs_func->id_funcionario]), 1, 1, 'C', $fill);
			
			$i++;
		}
	
	}
	//anual
	else {
		
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
			$periodo2= explode("/", $_POST["periodo"]);
			
			$data1_mk= mktime(14, 0, 0, $periodo2[0], 1, $periodo2[1]);
			$dias_mes= date("t", $data1_mk);
			
			$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
			
			$data1= date("Y-m-d", $data1_mk);
			$data2= date("Y-m-d", $data2_mk);
			
			$data1f= desformata_data($data1);
			$data2f= desformata_data($data2);
			
			$ultimo_dia_periodo_anterior= date("Y-m-d", mktime(14, 0, 0, $periodo2[0], 0, $periodo2[1]));
		}
		
		
		
		$result_equi= mysql_query("select distinct(op_equipamentos.id_equipamento), op_equipamentos.equipamento from op_equipamentos, op_suja_lavagem
									where op_equipamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   op_equipamentos.tipo_equipamento = '1'
									and   op_suja_lavagem.id_equipamento = op_equipamentos.id_equipamento
									$str_equipamento
									and   op_suja_lavagem.data_lavagem >= '". $data1 ."'
									and   op_suja_lavagem.data_lavagem <= '". $data2 ."'
									order by op_equipamentos.equipamento asc
									 ");
		$i=0;
		while ($rs_equi = mysql_fetch_object($result_equi)) {
		
			$pdf->AddPage();
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.55, "RELATÓRIO DE MÁQUINA PARADA - ". $rs_equi->equipamento, 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.55, traduz_mes($periodo2[0]) ."/". $periodo2[1], 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$ontem= soma_data($_POST["data"], -1, 0, 0);
			
			$result_lavagem_anterior= mysql_query("select * from op_suja_lavagem, op_suja_remessas
													where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
													and   op_suja_lavagem.data_lavagem = '". $ultimo_dia_periodo_anterior ."'
													and   op_suja_lavagem.id_equipamento = '". $rs_equi->id_equipamento ."'
													order by op_suja_lavagem.data_lavagem desc, op_suja_lavagem.hora_lavagem desc
													limit 1
													") or die(mysql_error());
			
			$rs_lavagem_anterior= mysql_fetch_object($result_lavagem_anterior);
			
			$data_fim[-1]= $rs_lavagem_anterior->data_fim_lavagem ." ". $rs_lavagem_anterior->hora_fim_lavagem;
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->Cell(7.5, 0.55, "FIM DA ÚLTIMA LAVAGEM DO PERÍODO ANTERIOR (". desformata_data($ultimo_dia_periodo_anterior) ."):", 0, 0, 'L');
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->Cell(0, 0.55, desformata_data($rs_lavagem_anterior->data_fim_lavagem) ." ". $rs_lavagem_anterior->hora_fim_lavagem, 0, 1, 'L');
			
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$pdf->Cell(2, 0.55, "DATA", 1, 0, 'C', 1);
			$pdf->Cell(1.2, 0.55, "DIA", 1, 0, 'C', 1);
			$pdf->Cell(3.5, 0.55, "PRIMEIRA", 1, 0, 'C', 1);
			$pdf->Cell(3.5, 0.55, "ÚLTIMA", 1, 0, 'C', 1);
			$pdf->Cell(3.5, 0.55, "FINALIZAÇÃO", 1, 0, 'C', 1);
			$pdf->Cell(3, 0.55, "TEMPO PARADA", 1, 1, 'C', 1);
			
			$total_parada_geral=0;
			
			
			//repetir todos os dias do intervalo
			for ($t=1; $t<=$dias_mes; $t++) {
				
				$data_aqui= $periodo2[1] ."-". $periodo2[0] ."-". formata_saida($t, 2);
				
				$data_aqui_mk= faz_mk_data($data_aqui);
				$dia_semana= date("w", $data_aqui_mk);
				
				if (($t%2)==1) $fill= 0;
				else $fill= 1;
				
				$pdf->SetFillColor(235,235,235);
				$pdf->SetFont('ARIALNARROW', '', 8);
				
				$pdf->Cell(2, 0.55, desformata_data($data_aqui), 1, 0, "C", $fill);
				$pdf->Cell(1.2, 0.55, strtoupper(traduz_dia_resumido($dia_semana)), 1, 0, "C", $fill);
				
				$result_lavagem_primeira= mysql_query("select * from op_suja_lavagem, op_suja_remessas
														where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
														and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
														and   op_suja_lavagem.data_lavagem = '". $data_aqui ."'
														and   op_suja_lavagem.id_equipamento = '". $rs_equi->id_equipamento ."'
														order by op_suja_lavagem.data_lavagem asc, op_suja_lavagem.hora_lavagem asc
														limit 1
														") or die(mysql_error());
				$rs_lavagem_primeira= mysql_fetch_object($result_lavagem_primeira);
				
				$pdf->Cell(3.5, 0.55, desformata_data($rs_lavagem_primeira->data_lavagem) ." ". $rs_lavagem_primeira->hora_lavagem, 1, 0, 'C', $fill);
				
				$result_lavagem_ultima= mysql_query("select * from op_suja_lavagem, op_suja_remessas
														where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
														and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
														and   op_suja_lavagem.data_lavagem = '". $data_aqui ."'
														and   op_suja_lavagem.id_equipamento = '". $rs_equi->id_equipamento ."'
														order by op_suja_lavagem.data_lavagem asc, op_suja_lavagem.hora_lavagem desc
														limit 1
														") or die(mysql_error());
				$rs_lavagem_ultima= mysql_fetch_object($result_lavagem_ultima);
				
				$pdf->Cell(3.5, 0.55, desformata_data($rs_lavagem_ultima->data_lavagem) ." ". $rs_lavagem_ultima->hora_lavagem, 1, 0, 'C', $fill);
				$pdf->Cell(3.5, 0.55, desformata_data($rs_lavagem_ultima->data_fim_lavagem) ." ". $rs_lavagem_ultima->hora_fim_lavagem, 1, 0, 'C', $fill);
				
				
				//---------------------------
				
				$ontem= soma_data($data_aqui, -1, 0, 0);
			
				$result_lavagem_anterior= mysql_query("select * from op_suja_lavagem, op_suja_remessas
														where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
														and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
														and   op_suja_lavagem.data_lavagem = '". formata_data($ontem) ."'
														and   op_suja_lavagem.id_equipamento = '". $rs_equi->id_equipamento ."'
														order by op_suja_lavagem.data_lavagem desc, op_suja_lavagem.hora_lavagem desc
														limit 1
														") or die(mysql_error());
				
				$rs_lavagem_anterior= mysql_fetch_object($result_lavagem_anterior);
				
				$data_fim[-1]= $rs_lavagem_anterior->data_fim_lavagem ." ". $rs_lavagem_anterior->hora_fim_lavagem;
				
				$total_parada=0;
				
				$result_lavagem= mysql_query("select * from op_suja_lavagem, op_suja_remessas
												where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
												and   op_suja_lavagem.data_lavagem = '". $data_aqui ."'
												and   op_suja_lavagem.id_equipamento = '". $rs_equi->id_equipamento ."'
												order by op_suja_lavagem.data_lavagem asc, op_suja_lavagem.hora_lavagem asc
												") or die(mysql_error());
				
				$d=0;
				while ($rs_lavagem= mysql_fetch_object($result_lavagem)) {
					
					$data_fim[$d]= $rs_lavagem->data_fim_lavagem ." ". $rs_lavagem->hora_fim_lavagem;
					$data_inicio[$d]= $rs_lavagem->data_lavagem ." ". $rs_lavagem->hora_lavagem;
					
					if ($data_fim[$d-1]!=" ") $tempo_parada= @retorna_intervalo($data_fim[$d-1], $data_inicio[$d]);
					else $tempo_parada= 0;
				
					if ($tempo_parada<0) $tempo_parada=0;
				
					$total_parada+= $tempo_parada;
					//echo $data_fim[$d-1] ." -> ". $data_inicio[$d] ." = 
					
					$d++;
				}
				
				$total_parada_geral+=$total_parada;
				
				$pdf->Cell(3, 0.55, calcula_total_horas($total_parada), 1, 1, 'C', $fill);
				
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$pdf->Cell(13.7, 0.55, "", 0, 0, 'C');
			$pdf->Cell(3, 0.55, calcula_total_horas($total_parada_geral), 1, 0, 'C', !$fill);
			$pdf->Cell(0, 0.55, "", 0, 0, 'C');
			
			$pdf->Ln();
		}//fim while equi
	
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("lavagem_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>