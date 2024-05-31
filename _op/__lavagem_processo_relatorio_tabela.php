<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("ps", $_SESSION["permissao"])) {
	
	if ($_POST["tipo_relatorio"]=="o") {
	
		define('FPDF_FONTPATH','includes/fpdf/font/');
		require("includes/fpdf/fpdf.php");
		require("includes/fpdf/modelo_paisagem.php");
	
		$pdf=new PDF("L", "cm", "A4");
		$pdf->SetMargins(1.5, 3, 2);
		$pdf->SetAutoPageBreak(true, 1.5);
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
		
		$pdf->SetXY(7,1.25);
		
		if ($_POST["id_equipamento"]!="") $str_geral_equi= "and   op_suja_lavagem.id_equipamento = '". $_POST["id_equipamento"] ."' ";
		
		if ($_POST["id_processo"]!="") $str_processo= "and   id_processo = '". $_POST["id_processo"] ."' ";
		
		if ($_POST["id_cliente"]!="") $str_geral_cliente= "and   op_suja_lavagem_cestos.id_cliente= '". $_POST["id_cliente"] ."' ";
			
		$peso_total_dia= 0;
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, "LAVAGEM DE ROUPA SUJA - POR PROCESSO", 0 , 1, 'R');
		
		//$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		//$pdf->Cell(0, 0.6, $_POST["data"], 0 , 1, 'R');
		
		$pdf->Ln(); $pdf->LittleLn();
		
		$str_geral= "";
		
		if ($_POST["id_equipamento"]!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "EQUIPAMENTO:", 0, 0);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(3, 0.6, pega_equipamento($_POST["id_equipamento"]), 0, 1);
			
			$str_geral .= "and   op_suja_lavagem.id_equipamento = '". $_POST["id_equipamento"] ."' ";
			
			$pdf->Ln();
		}
		
		if ($_POST["id_cliente"]!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CLIENTE:", 0, 0);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(3, 0.6, pega_pessoa($_POST["id_cliente"]), 0, 1);
			
			$str_geral .= "and   op_suja_lavagem_cestos.id_cliente = '". $_POST["id_cliente"] ."' ";
			
			$pdf->Ln();
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(1.5, 0.5, "DATA", 1, 0, 'C', 1);
		
		$result_lavagem_total= mysql_query("select sum(op_suja_lavagem_cestos.peso) as peso_total
												from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
												where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
												". $str_geral ."
												and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
												and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". $_POST["periodo"] ."'
												") or die(mysql_error());

		$rs_lavagem_total= mysql_fetch_object($result_lavagem_total);
		
		
		$result_processos= mysql_query("select * from op_equipamentos_processos
										where id_empresa = '". $_SESSION["id_empresa"] ."' 
										and   status_processo = '1'
										". $str_processo ."
										order by codigo asc
										");
		$linhas_processos= mysql_num_rows($result_processos);
		
		$largura= (23.25/$linhas_processos);
		
		while ($rs_processos= mysql_fetch_object($result_processos))
			$pdf->Cell($largura, 0.5, "PROC. ". $rs_processos->codigo, 1, 0, 'C', 1);
		
		$pdf->Cell(1.5, 0.5, "TOTAL", 1, 1, 'C', 1);
		
		$peso_total_mes= 0;
		
		$diferenca= ceil(($data2_mk-$data1_mk)/86400);
		
		//repetir todos os dias do intervalo
		for ($d=0; $d<=$diferenca; $d++) {
			
			$calculo_data= $data1_mk+(86400*$d);
			$data= date("d/m/Y", $calculo_data);
			$data_valida= date("Y-m-d", $calculo_data);
			$id_dia= date("w", $calculo_data);
			
			$amanha= soma_data($data_valida, 1, 0, 0);
			$ontem= soma_data($data_valida, -1, 0, 0);
			$anteontem= soma_data($data_valida, -2, 0, 0);
			
			//$data_aqui= $periodo2[1] ."-". $periodo2[0] ."-". formata_saida($t, 2);
			
			if (($d%2)!=1) $fill= 0;
			else $fill= 1;
			
			$pdf->SetFillColor(235,235,235);
			$pdf->SetFont('ARIALNARROW', '', 7);
			
			$pdf->Cell(1.5, 0.45, desformata_data($data_valida), 1, 0, "C", $fill);
			
			$result_processos= mysql_query("select * from op_equipamentos_processos
											where id_empresa = '". $_SESSION["id_empresa"] ."' 
											and   status_processo = '1'
											". $str_processo ."
											order by codigo asc
											");
			
			$linhas_processos= mysql_num_rows($result_processos);
		
			$largura= (23.25/$linhas_processos);
			
			$peso_total_dia= 0;
			
			$i= 0;
			while ($rs_processos= mysql_fetch_object($result_processos)) {
				
				$result_lavagem= mysql_query("select sum(op_suja_lavagem_cestos.peso) as peso_total from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
														where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
														and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
														". $str_geral ."
														and   op_suja_lavagem.id_processo = '". $rs_processos->id_processo ."'
														and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
														and   op_suja_remessas.data_remessa = '". $data_valida ."'
														") or die(mysql_error());
		
				$rs_lavagem= mysql_fetch_object($result_lavagem);
				
				$pdf->Cell($largura, 0.45, fnum($rs_lavagem->peso_total) ." kg", 1, 0, 'C', $fill);
				
				$peso_total_processo[$i] += $rs_lavagem->peso_total;
				$peso_total_dia += $rs_lavagem->peso_total;
				$peso_total_mes += $rs_lavagem->peso_total;
				
				$i++;
			}
			
			$pdf->Cell(1.5, 0.45, fnum($peso_total_dia) ." kg", 1, 1, "C", $fill);
		}
		
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->SetFillColor(210,210,210);
		
		$pdf->Cell(1.5, 0.45, "", 0, 0, 'C', 0);
		
		$result_processos= mysql_query("select * from op_equipamentos_processos
										where id_empresa = '". $_SESSION["id_empresa"] ."' 
										". $str_processo ."
										and  status_processo = '1'
										");
		$linhas_processos= mysql_num_rows($result_processos);
		
		$largura= (23.25/$linhas_processos);
		
		$i= 0;
		while ($rs_processos= mysql_fetch_object($result_processos)) {
			
			if ($rs_lavagem_total->peso_total>0) $percent= fnum(($peso_total_processo[$i]*100)/$rs_lavagem_total->peso_total) ."%";
			else $percent= "0%";
			
			$pdf->Cell($largura, 0.45, fnum($peso_total_processo[$i]) ." kg", 1, 0, 'C', 1);
			
			$i++;
		}
		
		$pdf->Cell(1.5, 0.45, fnum($peso_total_mes) ." kg", 1, 1, 'C', 1);
		
		
		$pdf->Cell(1.5, 0.45, "", 0, 0, 'C', 0);
		
		$result_processos= mysql_query("select * from op_equipamentos_processos
										where id_empresa = '". $_SESSION["id_empresa"] ."' 
										". $str_processo ."
										and  status_processo = '1'
										");
		$linhas_processos= mysql_num_rows($result_processos);
		
		$largura= (23.25/$linhas_processos);
		
		$i= 0;
		while ($rs_processos= mysql_fetch_object($result_processos)) {
			
			if ($rs_lavagem_total->peso_total>0) $percent= fnum(($peso_total_processo[$i]*100)/$rs_lavagem_total->peso_total) ."%";
			else $percent= "0%";
			
			$pdf->Cell($largura, 0.45, $percent, 1, 0, 'C', 1);
			
			$i++;
		}	
		
		
		$pdf->AddPage();
		
		$pdf->SetFillColor(210,210,210);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(1.5, 0.6, "CÓD.", 1, 0, 'C', 1);
		$pdf->Cell(6, 0.6, " PROCESSO", 1, 1, 'L', 1);
		
		$result_processos= mysql_query("select * from op_equipamentos_processos
											where id_empresa = '". $_SESSION["id_empresa"] ."' 
											". $str_processo ."
											and   status_processo = '1'
											order by codigo asc
											");
		
		$t=0;
		while ($rs_processos= mysql_fetch_object($result_processos)) {
			
			$pdf->SetFillColor(235,235,235);
			$pdf->SetFont('ARIALNARROW', '', 8);
			
			if (($t%2)==1) $fill= 0;
			else $fill= 1;
			
			$pdf->Cell(1.5, 0.45, $rs_processos->codigo, 1, 0, 'C', $fill);
			$pdf->Cell(6, 0.45, " ". $rs_processos->processo, 1, 1, 'L', $fill);
			
			$t++;
		}
	
	}
	//anual
	else {
		
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
			
			$dia_inicio= 1;
			
			$data1_mk= mktime(14, 0, 0, $periodo2[0], $dia_inicio, $periodo2[1]);
			$dias_mes= date("t", $data1_mk);
			
			$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
			
			$data1= date("Y-m-d", $data1_mk);
			$data2= date("Y-m-d", $data2_mk);
			
			$data1f= desformata_data($data1);
			$data2f= desformata_data($data2);
		}
		
		$pdf=new PDF("L", "cm", "A4");
		$pdf->SetMargins(1.5, 3, 2);
		$pdf->SetAutoPageBreak(true, 1.5);
		$pdf->SetFillColor(210,210,210);
		$pdf->AddFont('ARIALNARROW');
		$pdf->AddFont('ARIAL_N_NEGRITO');
		$pdf->AddFont('ARIAL_N_ITALICO');
		$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
		$pdf->SetFont('ARIALNARROW');
		
		
		if ($_POST["id_cliente"]!="") {
			$str = " and   op_suja_lavagem_cestos.id_cliente =  '". $_POST["id_cliente"] ."'";
			$linha2= pega_pessoa($_POST["id_cliente"]);
		}
		else $linha2= "GERAL";
		
		$periodo= explode("/", $_POST["periodo"]);
		$periodo_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
		
		$total_dias_mes= date("t", $periodo_mk);
	
		$pdf->AddPage();
		
		$total_geral_periodo= 0;
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "POR PROCESSO - ". $_POST["periodo"], 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $linha2, 0, 1, "R");
		
		$pdf->Ln();
		
		for ($t=0; $t<2; $t++) {
		
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->SetFillColor(210,210,210);
			
			if ($t==0) $tit= "PESOS:";
			else $tit= "PORCENTAGENS:";
			
			$pdf->Cell(0, 0.6, $tit, 'B', 1, 'L', 0);
			$pdf->Cell(0, 0.2, "", 0, 1);
			
			$pdf->Cell(5, 0.6, "PROCESSO", 1, 0, 'L', 1);
			
			for ($i=1; $i<13; $i++) {
				if ($i==12) $quebra=1;
				else $quebra= 0;
				
				$pdf->Cell(1.75, 0.6, substr(traduz_mes($i), 0, 3), 1, $quebra, 'C', 1);
			}
			
			
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->SetFillColor(235,235,235);
			
			$result_processo= mysql_query("select distinct(op_equipamentos_processos.id_processo) as id_processo, op_equipamentos_processos.processo
											from op_equipamentos_processos, op_suja_lavagem, op_suja_remessas
											where op_equipamentos_processos.id_processo = op_suja_lavagem.id_processo
											and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
											and   op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   DATE_FORMAT(op_suja_remessas.data_remessa, '%Y') = '". $_POST["periodo"] ."'
											order by op_equipamentos_processos.codigo asc
											") or die(mysql_error());
			$j=0;
			while ($rs_processo= mysql_fetch_object($result_processo)) {
				if (($j%2)==0) $fill=1;
				else $fill= 0;
				
				$pdf->SetFont('ARIALNARROW', '', 7);
				
				$pdf->Cell(5, 0.6, $rs_processo->processo, 1, 0, 'L', $fill);
				
				for ($i=1; $i<13; $i++) {
					if ($i==12) $quebra=1;
					else $quebra= 0;
					
					if ($t==1) {
						$result_lavagem_total= mysql_query("select sum(op_suja_lavagem_cestos.peso) as peso_total from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
																where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
																and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
																". $str ."
																and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
																and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["periodo"] ."'
																") or die(mysql_error());
				
						$rs_lavagem_total= mysql_fetch_object($result_lavagem_total);
					}
					
					$result_lavagem= mysql_query("select sum(op_suja_lavagem_cestos.peso) as peso_total from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
															where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
															". $str ."
															and   op_suja_lavagem.id_processo = '". $rs_processo->id_processo ."'
															and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
															and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". formata_saida($i, 2) ."/". $_POST["periodo"] ."'
															") or die(mysql_error());
			
					$rs_lavagem= mysql_fetch_object($result_lavagem);
					
					
					
					if ($t==0) {
						$campo= fnum($rs_lavagem->peso_total) ." kg";
						$peso_total_aqui[$i]+=$rs_lavagem->peso_total;
					}
					else {
						if ($rs_lavagem_total->peso_total==0) $campo= "-";
						else $campo= fnum(($rs_lavagem->peso_total*100)/$rs_lavagem_total->peso_total) ."%";
					}
					
					$pdf->Cell(1.75, 0.6, $campo, 1, $quebra, 'C', $fill);
				}
				
				$j++;
			}
			
			if ($t==0) {
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
				
				$pdf->Cell(5, 0.6, "", 0, 0, 'L');
				
				for ($i=1; $i<13; $i++) {
					if ($i==12) $quebra=1;
					else $quebra= 0;
					
					$pdf->Cell(1.75, 0.6, fnum($peso_total_aqui[$i]) ." kg", 1, $quebra, 'C', 1);
				}
				
				$pdf->Ln();
			}
		}//fim t
	
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("lavagem_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>