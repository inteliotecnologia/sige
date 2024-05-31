<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode_algum("12", $_SESSION["permissao"])) {

	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
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
	
	if ($_POST["motivo"]=="r") {
		$tit_pagina= "RECLAMAÇÕES";
		$str .= " and   ( com_livro.id_motivo = '37' or com_livro.id_motivo = '34' ) ";
		
		$id_motivo_interno=37; $id_motivo_cliente=34;
	}
	else {
		$tit_pagina= "NÃO-CONFORMIDADES";
		$str .= " and   ( com_livro.id_motivo = '41' or com_livro.id_motivo = '42' ) ";
		
		$id_motivo_interno=41; $id_motivo_cliente=42;
	}
	
	$i=0;
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
		
	$periodo2= explode('/', $_POST["periodo"]);
		
	$data1_mk= mktime(14, 0, 0, $periodo2[0], 1, $periodo2[1]);
	
	$dias_mes= date("t", $data1_mk);
	$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
	
	$data1= date("Y-m-d", $data1_mk);
	$data2= date("Y-m-d", $data2_mk);
	
	$data1f= desformata_data($data1);
	$data2f= desformata_data($data2);
	
	$periodo_mk= mktime(0, 0, 0, $periodo2[0], 1, $periodo2[1]);
	$total_dias_mes= date("t", $periodo_mk);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "RELATÓRIO GERAL DE ". $tit_pagina, 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ." de ". $periodo2[1], 0 , 1, 'R');
	
	$pdf->Ln();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "Solicitações por departamento:", 0 , 1, 'L');
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(9, 0.6, "SOLICITADA POR", 1, 0, "L", 1);
	$pdf->Cell(4, 0.6, "TOTAL", 1, 0, "C", 1);
	$pdf->Cell(4, 0.6, "%", 1, 1, "C", 1);
	
	$result_deptos= mysql_query("select distinct(rh_carreiras.id_departamento) as id_departamento from com_livro, rh_funcionarios, rh_carreiras, rh_departamentos
									where com_livro.de = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
									and   rh_carreiras.atual = '1'
									$str
									and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
									and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
									order by rh_departamentos.departamento asc
									") or die(mysql_error());
	
	$result_total= mysql_query("select distinct(com_livro.id_livro) from com_livro, rh_funcionarios, rh_carreiras, rh_departamentos
									where com_livro.de = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
									$str
									and   rh_carreiras.atual = '1'
									and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
									and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
									") or die(mysql_error());
	
	$linhas_total= mysql_num_rows($result_total);
	
	$total_rn= 0;
	
	$i=0;
	
	while ($rs_deptos= mysql_fetch_object($result_deptos)) {
	
		$result_total_depto= mysql_query("select distinct(com_livro.id_livro) from com_livro, rh_funcionarios, rh_carreiras, rh_departamentos
											where com_livro.de = rh_funcionarios.id_funcionario
											and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											$str
											and   rh_carreiras.atual = '1'
											and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
											and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
											and   rh_carreiras.id_departamento = '". $rs_deptos->id_departamento ."'
											") or die(mysql_error());
		
		$linhas_total_depto= mysql_num_rows($result_total_depto);
		
		if (($i%2)==0) $fill=1;
		else $fill= 0;
		
		$pdf->SetFillColor(235,235,235);
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		$percent= ($linhas_total_depto*100)/$linhas_total;
		
		$pdf->Cell(9, 0.5, pega_departamento($rs_deptos->id_departamento), 1, 0, "L", $fill);
		$pdf->Cell(4, 0.5, $linhas_total_depto, 1, 0, "C", $fill);
		$pdf->Cell(4, 0.5, fnum($percent) ." %", 1, 1, "C", $fill);
		
		$total_rn += $linhas_total_depto;
		
		$i++;
	}
	
	$total_rn1= $total_rn;
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(9, 0.5, "", 0, 0, "L", 0);
	$pdf->Cell(4, 0.5, $total_rn, 1, 0, "C", !$fill);
	$pdf->Cell(4, 0.5, "", 0, 1, "L", 0);
	
	$pdf->Ln();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "Departamentos reclamados:", 0 , 1, 'L');
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(210,210,210);
	
	$pdf->Cell(9, 0.6, "SOLICITADA POR", 1, 0, "L", 1);
	$pdf->Cell(4, 0.6, "TOTAL", 1, 0, "C", 1);
	$pdf->Cell(4, 0.6, "%", 1, 1, "C", 1);
	
	$result_deptos= mysql_query("select distinct(com_livro.id_departamento_principal) as id_departamento from com_livro, rh_departamentos
									where com_livro.id_departamento_principal = rh_departamentos.id_departamento
									$str
									and   com_livro.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
									order by rh_departamentos.departamento asc
									") or die(mysql_error());
	
	$result_total= mysql_query("select distinct(com_livro.id_livro) from com_livro, rh_funcionarios, rh_carreiras, rh_departamentos
									where com_livro.de = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
									$str
									and   rh_carreiras.atual = '1'
									and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
									and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
									") or die(mysql_error());
	
	$linhas_total= mysql_num_rows($result_total);
	
	$total_rn= 0;
	
	$i=0;
	
	while ($rs_deptos= mysql_fetch_object($result_deptos)) {
	
		$result_total_depto= mysql_query("select distinct(com_livro.id_livro) from com_livro, rh_funcionarios, rh_carreiras, rh_departamentos
											where com_livro.de = rh_funcionarios.id_funcionario
											and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											$str
											and   rh_carreiras.atual = '1'
											and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
											and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
											and   com_livro.id_departamento_principal = '". $rs_deptos->id_departamento ."'
											") or die(mysql_error());
		
		$linhas_total_depto= mysql_num_rows($result_total_depto);
		
		if (($i%2)==0) $fill=1;
		else $fill= 0;
		
		$pdf->SetFillColor(235,235,235);
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		$percent= ($linhas_total_depto*100)/$linhas_total;
		
		$pdf->Cell(9, 0.5, pega_departamento($rs_deptos->id_departamento), 1, 0, "L", $fill);
		$pdf->Cell(4, 0.5, $linhas_total_depto, 1, 0, "C", $fill);
		$pdf->Cell(4, 0.5, fnum($percent) ." %", 1, 1, "C", $fill);
		
		$total_rn += $linhas_total_depto;
		
		$i++;
	}
	
	if ($total_rn1>$total_rn) $info_aux= "*";
	else $info_aux= "";
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(9, 0.5, "", 0, 0, "L", 0);
	$pdf->Cell(4, 0.5, $total_rn . $info_aux, 1, 0, "C", !$fill);
	$pdf->Cell(4, 0.5, "", 0, 1, "L", 0);
	
	if ($total_rn1>$total_rn) {
		$pdf->Cell(0, 0.2, "", 0, 1, "L", 0);
		$pdf->SetFont('ARIALNARROW', '', 7);
		$pdf->Cell(0, 0.5, "*Este valor difere do total pois em alguns casos não foi apontado um departamento responsável pela reclamação/não-conformidade.", 0, 1, "L", 0);
		$pdf->Ln();
	}
	
	$pdf->Ln();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "Abertas x fechadas:", 0 , 1, 'L');
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(210,210,210);
	
	//$pdf->Cell(9, 0.6, "", 1, 0, "L", 1);
	$pdf->Cell(4, 0.6, "ABERTAS", 1, 0, "C", 1);
	$pdf->Cell(4, 0.6, "FINALIZADAS", 1, 1, "C", 1);
	
	$result_total_abertas= mysql_query("select distinct(com_livro.id_livro)
											from com_livro
											where 1=1
											$str
											and   com_livro.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
											") or die(mysql_error());
	$linhas_total_abertas= mysql_num_rows($result_total_abertas);
	
	$result_total_finalizadas= mysql_query("select distinct(com_livro.id_livro)
											from com_livro, qual_reclamacoes_andamento, rh_funcionarios, rh_carreiras, rh_departamentos
											where com_livro.de = rh_funcionarios.id_funcionario
											and   com_livro.id_livro = qual_reclamacoes_andamento.id_livro
											and   qual_reclamacoes_andamento.id_situacao = '6'
											$str
											and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
											and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
											") or die(mysql_error());
	$linhas_total_finalizadas= mysql_num_rows($result_total_finalizadas);
	
	$fill= 0;
	
	$pdf->SetFillColor(235,235,235);
	$pdf->SetFont('ARIALNARROW', '', 8);
	
	//$pdf->Cell(9, 0.5, "", 1, 0, "L", $fill);
	$pdf->Cell(4, 0.5, $linhas_total_abertas, 1, 0, "C", $fill);
	$pdf->Cell(4, 0.5, $linhas_total_finalizadas, 1, 1, "C", $fill);
	
	
	$pdf->Ln();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "Interna x Cliente:", 0 , 1, 'L');
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(210,210,210);
	
	//$pdf->Cell(9, 0.6, "", 1, 0, "L", 1);
	$pdf->Cell(4, 0.6, "INTERNA", 1, 0, "C", 1);
	$pdf->Cell(4, 0.6, "CLIENTE", 1, 1, "C", 1);
	
	$result_total_interna= mysql_query("select distinct(com_livro.id_livro)
										from com_livro
										where 1=1
										and   com_livro.id_motivo = '". $id_motivo_interno ."'
										and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
	$linhas_total_interna= mysql_num_rows($result_total_interna);
	
	$result_total_cliente= mysql_query("select distinct(com_livro.id_livro)
										from com_livro
										where 1=1
										and   com_livro.id_motivo = '". $id_motivo_cliente ."'
										and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
	$linhas_total_cliente= mysql_num_rows($result_total_cliente);
	
	
	
	
	$fill= 0;
	
	$pdf->SetFillColor(235,235,235);
	$pdf->SetFont('ARIALNARROW', '', 8);
	
	//$pdf->Cell(9, 0.5, "", 1, 0, "L", $fill);
	$pdf->Cell(4, 0.5, $linhas_total_interna, 1, 0, "C", $fill);
	$pdf->Cell(4, 0.5, $linhas_total_cliente, 1, 1, "C", $fill);
	
	$pdf->Ln();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "Clientes:", 0 , 1, 'L');
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(210,210,210);
	
	$pdf->Cell(8, 0.6, " CLIENTE", 1, 0, "L", 1);
	$pdf->Cell(4, 0.6, "Nº", 1, 1, "C", 1);
	
	$pdf->SetFillColor(235,235,235);
	$pdf->SetFont('ARIALNARROW', '', 8);
	
	$result_recl_cliente= mysql_query("select distinct(com_livro.reclamacao_id_cliente) as reclamacao_id_cliente
										from com_livro
										where 1=1
										$str
										and   com_livro.reclamacao_id_cliente <> '0'
										and   com_livro.reclamacao_id_cliente is not NULL
										and   com_livro.id_motivo = '". $id_motivo_cliente ."'
										and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
	while ($rs_recl_cliente= mysql_fetch_object($result_recl_cliente)) {
		
		$result_recl_cliente_especifico= mysql_query("select count(com_livro.id_livro) as total
													from com_livro
													where 1=1
													and   com_livro.reclamacao_id_cliente <> '0'
													and   com_livro.reclamacao_id_cliente is not NULL
													and   com_livro.reclamacao_id_cliente = '". $rs_recl_cliente->reclamacao_id_cliente ."'
													and   com_livro.id_motivo = '". $id_motivo_cliente ."'
													and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
													") or die(mysql_error());
		
		$rs_recl_cliente_especifico= mysql_fetch_object($result_recl_cliente_especifico);
		
		$pdf->Cell(8, 0.5, " ". pega_pessoa($rs_recl_cliente->reclamacao_id_cliente), 1, 0, "L", $fill);
		$pdf->Cell(4, 0.5, $rs_recl_cliente_especifico->total, 1, 1, "C", $fill);
		
	}
	
	$result_recl_cliente_especifico= mysql_query("select count(com_livro.id_livro) as total
													from com_livro
													where 1=1
													and   (com_livro.reclamacao_id_cliente = '0' or
															com_livro.reclamacao_id_cliente is NULL )
													and   com_livro.id_motivo = '". $id_motivo_cliente ."'
													and   DATE_FORMAT(com_livro.data_livro, '%m/%Y') = '". $_POST["periodo"] ."'
													") or die(mysql_error());
	$rs_recl_cliente_especifico= mysql_fetch_object($result_recl_cliente_especifico);
	
	$pdf->Cell(8, 0.5, " NÃO ESPECIFICADO", 1, 0, "L", $fill);
	$pdf->Cell(4, 0.5, $rs_recl_cliente_especifico->total, 1, 1, "C", $fill);
	
	$pdf->AliasNbPages();
	$pdf->Output("reclamacao_nc_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}

?>