<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode_algum("ps", $_SESSION["permissao"])) {

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
	
	if ($_POST["data"]!="") $data= formata_data_hifen($_POST["data"]);
	else $data= date("Y-m-d");
	
	//if ($_POST["id_cliente"]!="") $str .= " and   id_cliente= '". $_POST["id_cliente"] ."' ";	
	if ($_POST["id_turno"]!="") $str .= " and   id_turno= '". $_POST["id_turno"] ."' ";
	if ($_POST["id_equipamento"]!="") $str .= " and   id_equipamento= '". $_POST["id_equipamento"] ."' ";
	if ($_POST["id_processo"]!="") $str .= " and   id_processo= '". $_POST["id_processo"] ."' ";
	if ($_POST["id_funcionario"]!="") $str .= " and   id_funcionario= '". $_POST["id_funcionario"] ."' ";
	
	$i=0;
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	if ($_POST["tipo_relatorio"]=="d") {
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "LAVAGEM DE ROUPA SUJA", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $_POST["data"], 0 , 1, 'R');
		
		$pdf->Ln();
		
		if ($_POST["data"]!="") $data= formata_data_hifen($_POST["data"]);
		else $data= date("Y-m-d");
		
		$result_lavagem= mysql_query("select * from op_suja_lavagem, op_suja_remessas
										where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
										and   op_suja_lavagem.data_lavagem = '". formata_data($data) ."'
										". $str ."
										order by op_suja_lavagem.data_lavagem asc, op_suja_lavagem.hora_lavagem asc
										") or die(mysql_error());
		
		$linhas_lavagem= mysql_num_rows($result_lavagem);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(3, 0.6, "LAVAGENS:", 0, 0);
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(3, 0.6, $linhas_lavagem, 0, 1);
		
		$pdf->LittleLn();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(2, 0.6, "REMESSA", 1, 0, 'C', 1);
		$pdf->Cell(1.4, 0.6, "DATA", 1, 0, 'C', 1);
		$pdf->Cell(1.5, 0.6, "HORA", 1, 0, 'C', 1);
		$pdf->Cell(1.6, 0.6, "HORA FIM", 1, 0, 'C', 1);
		$pdf->Cell(2.2, 0.6, "MÁQUINA", 1, 0, 'C', 1);
		$pdf->Cell(1.1, 0.6, "PROC.", 1, 0, 'C', 1);
		$pdf->Cell(1.4, 0.6, "PESO", 1, 0, 'C', 1);
		$pdf->Cell(5.5, 0.6, "CESTOS", 1, 1, 'C', 1);
		//$pdf->Cell(2, 0.6, "LAVADOR", 1, 1, 'C', 1);
		
		$d=0;
		while ($rs_lavagem= mysql_fetch_object($result_lavagem)) {
			
			$pdf->SetFillColor(235,235,235);
			$pdf->SetFont('ARIALNARROW', '', 8);
			
			if (($d%2)==1) $fill= 0;
			else $fill= 1;
			
			$pdf->Cell(2, 0.6, desformata_data($rs_lavagem->data_remessa) ." nº ". $rs_lavagem->num_remessa, 1, 0, 'C', $fill);
			$pdf->Cell(1.4, 0.6, desformata_data($rs_lavagem->data_lavagem), 1, 0, 'C', $fill);
			$pdf->Cell(1.5, 0.6, $rs_lavagem->hora_lavagem, 1, 0, 'C', $fill);
			$pdf->Cell(1.6, 0.6, $rs_lavagem->hora_fim_lavagem, 1, 0, 'C', $fill);
			$pdf->Cell(2.2, 0.6, pega_equipamento($rs_lavagem->id_equipamento), 1, 0, 'C', $fill);
			$pdf->Cell(1.1, 0.6, pega_processo($rs_lavagem->id_processo), 1, 0, 'C', $fill);
			
			$result_cestos_peso= mysql_query("select sum(peso) as peso from op_suja_lavagem_cestos
												where id_lavagem = '". $rs_lavagem->id_lavagem ."'
												");
			$rs_cestos_peso= mysql_fetch_object($result_cestos_peso);
			
			$peso_total+=$rs_cestos_peso->peso;
			
			$pdf->Cell(1.4, 0.6, fnum($rs_cestos_peso->peso) ." kg", 1, 1, 'C', $fill);
			//$pdf->Cell(2, 0.6, primeira_palavra(pega_funcionario($rs_lavagem->id_funcionario)), 1, 1, 'C', $fill);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
			//$pdf->SetFillColor(220,220,220);
			
			$clientes= "";
			
			$result_cestos= mysql_query("select * from op_suja_lavagem_cestos
											where id_lavagem = '". $rs_lavagem->id_lavagem ."'
											order by id_cesto asc
											limit 2
											");
			$linhas_cestos= mysql_num_rows($result_cestos);
			
			if ($linhas_cestos>0) {
				//$fill= 0;
				
				$i=1;
				while ($rs_cestos= mysql_fetch_object($result_cestos)) {
					
					if ($i==1) $y= $pdf->GetY()-0.6;
					else $y= $pdf->GetY();
					
					$pdf->SetXY(13.2, $y);
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 6);
					$pdf->Cell(1.5, 0.3, "CESTO ". $rs_cestos->id_cesto, 1, 0, 'C', $fill);
					
					$pdf->SetFont('ARIALNARROW', '', 6);
					$pdf->Cell(1.6, 0.3, fnum($rs_cestos->peso) ." kg", 1, 0, 'C', $fill);
					
					if ($rs_cestos->id_cliente!=0) $cliente= pega_sigla_pessoa($rs_cestos->id_cliente);
					else $cliente= "-";
					
					$pdf->Cell(2.4, 0.3, $cliente, 1, 1, 'C', $fill);
					
					$i++;
				}
				
				$pdf->SetXY(2, $pdf->GetY());
			
			}
			
			$d++;
		}
		
		$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(2.2, 0.6, "PESO TOTAL:", 0, 0, 'L', 0);
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(3, 0.6, fnum($peso_total) ." kg", 0, 1, 'L', 0);
	}
	//mensal
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
			$periodo2= explode('/', $_POST["periodo"]);
			
			$data1_mk= mktime(22, 0, 0, $periodo2[0], 1, $periodo2[1]);
			$dias_mes= date("t", $data1_mk);
			
			$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
			
			$data1= date("Y-m-d", $data1_mk);
			$data2= date("Y-m-d", $data2_mk);
			
			$data1f= desformata_data($data1);
			$data2f= desformata_data($data2);
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "LAVAGEM DE ROUPA SUJA", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $data1f ." à ". $data2f, 0 , 1, 'R');
		
		$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		if ($_POST["id_cliente"]!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2.5, 0.6, "CLIENTE:", 0, 0, "L");
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_pessoa($_POST["id_cliente"]), 0, 1, "L");
			
			$str .= " and   op_suja_lavagem_cestos.id_cliente= '". $_POST["id_cliente"] ."' ";	
		}
		
		if ($_POST["id_turno"]!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2.5, 0.6, "TURNO:", 0, 0, "L");
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_turno($_POST["id_turno"]), 0, 1, "L");
			
			$str .= " and   op_suja_lavagem.id_turno= '". $_POST["id_turno"] ."' ";
		}
		
		if ($_POST["id_equipamento"]!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2.5, 0.6, "MÁQUINA:", 0, 0, "L");
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_equipamento($_POST["id_equipamento"]), 0, 1, "L");
			
			$str .= " and   op_suja_lavagem.id_equipamento= '". $_POST["id_equipamento"] ."' ";
		}
		
		if ($_POST["id_processo"]!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2.5, 0.6, "PROCESSO:", 0, 0, "L");
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_processo_nome($_POST["id_processo"]), 0, 1, "L");
			
			$str .= " and   op_suja_lavagem.id_processo= '". $_POST["id_processo"] ."' ";
		}
		
		if ($_POST["id_funcionario"]!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2.5, 0.6, "LAVADOR:", 0, 0, "L");
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_funcionario($_POST["id_funcionario"]), 0, 1, "L");
			
			$str .= " and   op_suja_lavagem.id_funcionario= '". $_POST["id_funcionario"] ."' ";
		}
		
		$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		
		$pdf->Cell(4.5, 0.5, "", 0, 0, "L", 0);
		$pdf->Cell(4, 0.5, "DATA", 1, 0, "C", 1);
		$pdf->Cell(4, 0.5, "PESO", 1, 0, "C", 1);
		$pdf->Cell(4.5, 0.5, "", 0, 1, "L", 0);
		
		$peso_total_mes= 0;
		
		$diferenca= ceil(($data2_mk-$data1_mk)/86400);
		
		for ($d=0; $d<=$diferenca; $d++) {
			
			$calculo_data= $data1_mk+(86400*$d);
			$data= date("d/m/Y", $calculo_data);
			$data_valida= date("Y-m-d", $calculo_data);
			$id_dia= date("w", $calculo_data);
			
			$amanha= soma_data($data_valida, 1, 0, 0);
			$ontem= soma_data($data_valida, -1, 0, 0);
			$anteontem= soma_data($data_valida, -2, 0, 0);
			
			if (($d%2)!=1) $fill= 0;
			else $fill= 1;
			
			$result_lavagem= mysql_query("select sum(op_suja_lavagem_cestos.peso) as peso_total from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
											where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
											and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
											and   op_suja_remessas.data_remessa = '". $data_valida ."'
											". $str ."
											") or die(mysql_error());
			
			$rs_lavagem= mysql_fetch_object($result_lavagem);
			
			$pdf->SetFillColor(235,235,235);
			$pdf->SetFont('ARIALNARROW', '', 8);
			
			$pdf->Cell(4.5, 0.5, "", 0, 0, "L", 0);
			$pdf->Cell(4, 0.5, " ". desformata_data($data_valida), 1, 0, "C", $fill);
			$pdf->Cell(4, 0.5, fnumf($rs_lavagem->peso_total) ." kg", 1, 0, "C", $fill);
			$pdf->Cell(4.5, 0.5, "", 0, 1, "L", 0);
								
			$peso_total_mes += $rs_lavagem->peso_total;
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(8.5, 0.5, "", 0, 0, "L", 0);
		$pdf->Cell(4, 0.5, fnumf($peso_total_mes) ." kg", 1, 0, "C", $fill);
		$pdf->Cell(4.5, 0.5, "", 0, 1, "L", 0);	
	}	
	
	$pdf->AliasNbPages();
	$pdf->Output("lavagem_suja_". date("d-m-Y_H:i:s") .".pdf", "I");
}

?>