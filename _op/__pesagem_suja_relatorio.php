<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("pls", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	
	if ($_POST["tipo_relatorio"]=="d") {
		require("includes/fpdf/modelo_retrato.php");
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
		$pdf->Cell(0, 0.6, "PESAGEM DE ROUPA SUJA (". strtoupper(pega_tipo_detalhamento_relatorio($_POST["tipo_detalhamento_relatorio"])) .")", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $_POST["data"], 0 , 1, 'R');
	
		$pdf->Ln();$pdf->Ln();
		
		//sintético
		if ($_POST["tipo_detalhamento_relatorio"]==1) {
			$result_remessas= mysql_query("select * from op_suja_remessas
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   data_remessa = '". formata_data($_POST["data"]) ."'
											");
			$linha_remessas= mysql_num_rows($result_remessas);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(4, 0.6, "NÚMERO DE REMESSAS:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $linha_remessas, 0 , 1);
			$pdf->Ln();
			
			$result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
													where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
													and   pessoas_tipos.tipo_pessoa = 'c'
													and   pessoas.status_pessoa = '1'
													and   pessoas.id_cliente_tipo = '1'
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
			*/
			
			$pdf->Cell(8, 0.5, "CLIENTE", 1, 0, 1, 1);
			$pdf->Cell(3, 0.5, "TOTAL MANHÃ", 1, 0, "C", 1);
			$pdf->Cell(3, 0.5, "TOTAL TARDE", 1, 0, "C", 1);
			$pdf->Cell(3, 0.5, "TOTAL DIA", 1, 1, "C", 1);
			
			$total_manha= 0;
			$total_tarde= 0;
			$total_dia= 0;
			$i=0;
			
			while ($rs_cli = mysql_fetch_object($result_cli)) {
				if (($i%2)==0) $fill= 0;
				else $fill= 1;
				
				$pdf->SetFillColor(235,235,235);
				$pdf->SetFont('ARIALNARROW', '', 9);
				
				$pdf->Cell(8, 0.5, $rs_cli->apelido_fantasia, 1, 0, 1, $fill);
				
				$j=0;
				
				$result_total1= mysql_query("select sum(peso) as total_cliente_manha from op_suja_pesagem, op_suja_remessas
											where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
											and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
											and   op_suja_remessas.data_remessa= '". formata_data($_POST["data"]) ."'
											and   op_suja_pesagem.id_cliente = '". $rs_cli->id_cliente ."'
											and   DATE_FORMAT(op_suja_remessas.hora_chegada, '%H') < '14'
											order by data_pesagem asc, hora_pesagem asc
											");
				$rs_total1= mysql_fetch_object($result_total1);
				
				$result_total2= mysql_query("select sum(peso) as total_cliente_tarde from op_suja_pesagem, op_suja_remessas
											where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
											and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
											and   op_suja_remessas.data_remessa= '". formata_data($_POST["data"]) ."'
											and   op_suja_pesagem.id_cliente = '". $rs_cli->id_cliente ."'
											and   DATE_FORMAT(op_suja_remessas.hora_chegada, '%H') >= '14'
											order by data_pesagem asc, hora_pesagem asc
											");
				$rs_total2= mysql_fetch_object($result_total2);
				
				$total_manha += $rs_total1->total_cliente_manha;
				$total_tarde += $rs_total2->total_cliente_tarde;
				
				
				$pdf->Cell(3, 0.5, fnum($rs_total1->total_cliente_manha) ." kg", 1, 0, "C", $fill);
				$pdf->Cell(3, 0.5, fnum($rs_total2->total_cliente_tarde) ." kg", 1, 0, "C", $fill);
				
				
				$result_total= mysql_query("select sum(peso) as total_cliente_remessa from op_suja_pesagem, op_suja_remessas
											where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
											and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
											and   op_suja_remessas.data_remessa= '". formata_data($_POST["data"]) ."'
											and   op_suja_pesagem.id_cliente = '". $rs_cli->id_cliente ."'
											order by data_pesagem asc, hora_pesagem asc
											");
				$rs_total= mysql_fetch_object($result_total);
				$total_cliente_remessa= $rs_total->total_cliente_remessa;
				
				$pdf->Cell(3, 0.5, fnum($total_cliente_remessa) ." kg", 1, 1, "C", $fill);
				
				$total_dia += $total_cliente_remessa;
				
				$i++;

			}
			
			if ($fill==1) $fill= 0;
			else $fill= 1;
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
			$pdf->Cell(8, 0.5, "TOTAL:", 1, 0, "L", $fill);
			$pdf->Cell(3, 0.5, fnum($total_manha) ." kg", 1, 0, "C", $fill);
			$pdf->Cell(3, 0.5, fnum($total_tarde) ." kg", 1, 0, "C", $fill);
			$pdf->Cell(3, 0.5, fnum($total_dia) ." kg", 1, 0, "C", $fill);
			
			$pdf->Ln(); $pdf->Ln();
			
		}//fim sintético
		//analítico
		if ($_POST["tipo_detalhamento_relatorio"]==2) {
			$result_remessas= mysql_query("select * from op_suja_remessas
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   data_remessa = '". formata_data($_POST["data"]) ."'
											");
			if (mysql_num_rows($result_remessas)==0) {
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(4, 0.6, "Nenhuma remessa neste dia.", 0 , 0);
			}
			
			$m=0;
			while ($rs_remessa= mysql_fetch_object($result_remessas)) {
				//echo "oi";
				$total_remessa= 0;
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.6, "REMESSA Nº ". $rs_remessa->num_remessa, 1, 1, 1, 1);
				
				$pdf->Cell(0, 0.2, "", 0, 1);
				
				/*
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(1, 0.6, "DATA:", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(4, 0.6, desformata_data($rs_remessa->data_remessa), 0 , 1);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(1.4, 0.6, "RELAVE:", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(4.6, 0.6, fnum($rs_remessa->relave) ." kg", 0 , 1);
				*/
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(2.6, 0.4, "HORA CHEGADA:", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(2.4, 0.4, $rs_remessa->hora_chegada, 0 , 0);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(3.8, 0.4, "HORA INÍCIO DESCARGA:", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(2.2, 0.4, $rs_remessa->hora_inicio_descarga, 0 , 0);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(3.4, 0.4, "HORA FIM DESCARGA:", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(2.6, 0.4, $rs_remessa->hora_fim_descarga, 0 , 1);
				
				$pdf->Ln();
				
				$result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
														where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
														and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
														and   pessoas_tipos.tipo_pessoa = 'c'
														and   pessoas.status_pessoa = '1'
														and   pessoas.id_cliente_tipo = '1'
														order by 
														pessoas.apelido_fantasia asc
														") or die(mysql_error());
				
				$i=1;
				$limite_pesagens= 12;
				$linhas_clientes= mysql_num_rows($result_cli);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				$largura_celula= (13/$limite_pesagens);
				
				$pdf->Cell(2, 0.5, "CLIENTE", 1, 0, 1, 1);
				for ($k= 1; $k<=$limite_pesagens; $k++)
					$pdf->Cell($largura_celula, 0.5, "". $k, 1, 0, "C", 1);
				$pdf->Cell(2, 0.5, "TOTAL", 1, 1, "C", 1);
				
				
				while ($rs_cli = mysql_fetch_object($result_cli)) {
					
					$result_ps= mysql_query("select * from op_suja_pesagem
												where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
												and   op_suja_pesagem.id_remessa= '". $rs_remessa->id_remessa ."'
												and   op_suja_pesagem.id_cliente = '". $rs_cli->id_cliente ."'
												order by data_pesagem asc, hora_pesagem asc
												");
					
					if (mysql_num_rows($result_ps)>0) {
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
						$pdf->Cell(2, 0.5, $rs_cli->sigla, 1, 0, 1, 1);
						
						$j=0;
						
						$pdf->SetFont('ARIALNARROW', '', 8);
						$total_cliente_remessa=0;
						
						while ($rs_ps= mysql_fetch_object($result_ps)) {
							$total_cliente_remessa += $rs_ps->peso;
							
							$pdf->Cell($largura_celula, 0.5, fnum($rs_ps->peso), 1, 0, "C", 0);
							$j++;
						}
						
						for ($k= $j; $k<$limite_pesagens; $k++)
							$pdf->Cell($largura_celula, 0.5, "", 1, 0, 1, 0);
						
						$result_total= mysql_query("select sum(peso) as total_cliente_remessa from op_suja_pesagem
													where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
													and   op_suja_pesagem.id_remessa= '". $rs_remessa->id_remessa ."'
													and   op_suja_pesagem.id_cliente = '". $rs_cli->id_cliente ."'
													order by data_pesagem asc, hora_pesagem asc
													");
						$rs_total= mysql_fetch_object($result_total);
						$total_cliente_remessa= $rs_total->total_cliente_remessa;
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
						$pdf->Cell(2, 0.5, fnum($total_cliente_remessa) ." kg", 1, 1, "C", 0);
						
						$total_remessa += $total_cliente_remessa;
						
						$i++;
					}
				}
		
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				$pdf->Cell(2, 0.5, "", 0, 0, 1);
				for ($k= 1; $k<=$limite_pesagens; $k++)
					$pdf->Cell($largura_celula, 0.5, "", 0, 0, "C");
				$pdf->Cell(2, 0.5, fnum($total_remessa) ." kg", 1, 1, "C");
				
				$pdf->Ln(); $pdf->Ln();
				
				$m++;
				
				if (($m%3)==0) $pdf->AddPage();
			}
		}//fim analitico
	}//fim diário
	else {
		require("includes/fpdf/modelo_paisagem_estendido.php");	
		$pdf=new PDF("L", "cm", "A4");
		$pdf->SetMargins(0.75, 0.75, 0.75);
		$pdf->SetAutoPageBreak(true, 1);
		$pdf->SetFillColor(230,230,230);
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
		
		$pdf->AddPage();
		
		$pdf->SetXY(7,1);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "PESAGEM DE ROUPA SUJA (EM KG)", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $data1f ." à ". $data2f, 0 , 1, 'R');
	
		$pdf->Ln();$pdf->Ln();
		
		$total_dias_mes= date("t", $data2_mk);
	
		$largura= (24.9/$total_dias_mes);
			
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->SetFillColor(200,200,200);
		
		$pdf->Cell(2, 0.6, "CLIENTES", 1 , 0, "L", 1);
		
		$diferenca= ceil(($data2_mk-$data1_mk)/86400);
		
		for ($d=0; $d<=$diferenca; $d++) {
			$calculo_data= $data1_mk+(86400*$d);
			$data= date("d/m/Y", $calculo_data);
			$dia= date("d", $calculo_data);
			$data_valida= date("Y-m-d", $calculo_data);
			$id_dia= date("w", $calculo_data);
			
			$amanha= soma_data($data_valida, 1, 0, 0);
			$ontem= soma_data($data_valida, -1, 0, 0);
			$anteontem= soma_data($data_valida, -2, 0, 0);
			
			$pdf->Cell($largura, 0.6, $dia, 1, 0, "C", 1);
			//echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->Cell(1.2, 0.6, "Total", 1, 1, "C", 1);
		
		$total_geral_periodo= 0;
		
		if ($_POST["id_cliente"]!="") $str_cliente = " and   pessoas.id_pessoa = '". $_POST["id_cliente"] ."' ";
		
		$result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
									where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
									and   pessoas_tipos.tipo_pessoa = 'c'
									and   pessoas.status_pessoa = '1'
									and   pessoas.id_cliente_tipo = '1'
									$str_cliente
									order by 
									pessoas.apelido_fantasia asc
									") or die(mysql_error());
		$j=0;
		while ($rs_cli = mysql_fetch_object($result_cli)) {
			if (($j%2)==0) $fill= 0;
			else {
				$fill=1;
				$pdf->SetFillColor(230,230,230);
			}
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->Cell(2, 0.6, $rs_cli->sigla, 1, 0, "L", $fill);
			
			$total_mes_cliente= 0;
			
			$diferenca= ceil(($data2_mk-$data1_mk)/86400);
		
			for ($d=0; $d<=$diferenca; $d++) {
				$calculo_data= $data1_mk+(86400*$d);
				$data= date("d/m/Y", $calculo_data);
				$dia= date("d", $calculo_data);
				$data_valida= date("Y-m-d", $calculo_data);
				$id_dia= date("w", $calculo_data);
				
				$amanha= soma_data($data_valida, 1, 0, 0);
				$ontem= soma_data($data_valida, -1, 0, 0);
				$anteontem= soma_data($data_valida, -2, 0, 0);
				
				//if ($t==1) $total_dia[$t]= 0;
				
				$result_peso= mysql_query("select sum(peso) as soma from op_suja_pesagem, op_suja_remessas
												where op_suja_pesagem.id_cliente = '". $rs_cli->id_cliente ."'
												and   op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
												and   op_suja_remessas.data_remessa= '". $data_valida ."'
												");
				$rs_peso= mysql_fetch_object($result_peso);
				
				$total_mes_cliente+= $rs_peso->soma;
				$total_dia[$d]+= $rs_peso->soma;
				$total_geral_periodo+= $rs_peso->soma;
				
				$pdf->SetFont('ARIALNARROW', '', 6);
				$pdf->Cell($largura, 0.6, fnum($rs_peso->soma), 1, 0, "C", $fill);
			}
			
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(1.2, 0.6, fnum($total_mes_cliente), 1, 1, "C", $fill);
			
			$j++;
		}
		
		$pdf->SetFillColor(200,200,200);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(2, 0.6, "TOTAL", 1 , 0, "L", 1);
		$pdf->SetFont('ARIALNARROW', '', 10);
		
		$diferenca= ceil(($data2_mk-$data1_mk)/86400);
		
		for ($d=0; $d<=$diferenca; $d++) {
			$calculo_data= $data1_mk+(86400*$d);
			$data= date("d/m/Y", $calculo_data);
			$dia= date("d", $calculo_data);
			$data_valida= date("Y-m-d", $calculo_data);
			$id_dia= date("w", $calculo_data);
			
			$amanha= soma_data($data_valida, 1, 0, 0);
			$ontem= soma_data($data_valida, -1, 0, 0);
			$anteontem= soma_data($data_valida, -2, 0, 0);
			
			$pdf->SetFont('ARIALNARROW', '', 6);
			$pdf->Cell($largura, 0.6, fnum($total_dia[$d]), 1, 0, "C", 1);
			//echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
		$pdf->Cell(1.2, 0.6, fnum($total_geral_periodo), 1, 1, "C", 1);
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("pesagem_suja_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>