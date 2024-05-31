<?
require_once("conexao.php");
require_once("funcoes.php");
require_once("funcoes_graficos.php");

if (pode_algum("ps", $_SESSION["permissao"])) {

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
	
	if ($_POST["data"]!="") $data= formata_data_hifen($_POST["data"]);
	else $data= date("Y-m-d");
	
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
	$pdf->Cell(0, 0.6, "RELATÓRIO GERAL DE RM", 0 , 1, 'R');
	
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
	
	$result_deptos= mysql_query("select distinct(rh_carreiras.id_departamento) as id_departamento from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
									where man_rms.id_usuario = usuarios.id_usuario
									and   man_rms.id_rm = man_rms_andamento.id_rm
									and   man_rms_andamento.id_situacao = '1'
									and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
									and   rh_carreiras.atual = '1'
									and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
									and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
									order by rh_departamentos.departamento asc
									") or die(mysql_error());
	
	$result_total= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
									where man_rms.id_usuario = usuarios.id_usuario
									and   man_rms.id_rm = man_rms_andamento.id_rm
									and   man_rms_andamento.id_situacao = '1'
									and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
									and   rh_carreiras.atual = '1'
									and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
									and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
									") or die(mysql_error());
	
	$linhas_total= mysql_num_rows($result_total);
	
	$total_rm= 0;
	
	unset($valores);
	unset($labels);
	
	$valores= array();
	$labels= array();
	
	$i=0;
	
	while ($rs_deptos= mysql_fetch_object($result_deptos)) {
	
		$result_total_depto= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
										where man_rms.id_usuario = usuarios.id_usuario
										and   man_rms.id_rm = man_rms_andamento.id_rm
										and   man_rms_andamento.id_situacao = '1'
										and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
										and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
										and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
										and   rh_carreiras.id_departamento = '". $rs_deptos->id_departamento ."'
										") or die(mysql_error());
		
		$linhas_total_depto= mysql_num_rows($result_total_depto);
		
		if (($i%2)==0) $fill=1;
		else $fill= 0;
		
		$pdf->SetFillColor(235,235,235);
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		$percent= ($linhas_total_depto*100)/$linhas_total;
		
		$departamento= pega_departamento($rs_deptos->id_departamento);
		
		$pdf->Cell(9, 0.5, $departamento, 1, 0, "L", $fill);
		$pdf->Cell(4, 0.5, $linhas_total_depto, 1, 0, "C", $fill);
		$pdf->Cell(4, 0.5, fnum($percent) ." %", 1, 1, "C", $fill);
		
		$total_rm += $linhas_total_depto;
		
		$valores[$i]= $linhas_total_depto;
		$labels[$i]= $departamento;
		
		$i++;
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(9, 0.5, "", 0, 0, "L", 0);
	$pdf->Cell(4, 0.5, $total_rm, 1, 0, "C", !$fill);
	$pdf->Cell(4, 0.5, "", 0, 1, "L", 0);
	
	
	
	$pdf->Ln();
	
	$pizza= pizza($valores, $labels, 1);
	if ($pizza) {
		$pdf->Ln();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, "Solicitações por departamento", 0 , 1, 'L');
		$pdf->Cell(0, 0.2, "", 0, 1);
		
		$pdf->Cell(0, 0, $pdf->Image($pizza, $pdf->GetX(), $pdf->GetY(), 18, 13), 0, 1, 'L', false );	
	}
	unlink($pizza);
	
	$pdf->Ln();
	
	$pdf->AddPage();
	
	$pdf->SetXY(0,1.75);
	
	$barra_horizontal= barra_horizontal($valores, $labels, 1);
	if ($barra_horizontal) {
	
		$pdf->Ln();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, "Solicitações por departamento", 0 , 1, 'L');
		$pdf->Cell(0, 0.2, "", 0, 1);
	
		$pdf->Cell(0, 0, $pdf->Image($barra_horizontal, $pdf->GetX(), $pdf->GetY(), 18, 18), 0, 1, 'L', false );	
	}
	unlink($barra_horizontal);
	
	
	
	
	
	// ---------------------------------------------------------------
	// ---------------------------------------------------------------
	// ---------------------------------------------------------------
	
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->Ln();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "Execuções por funcionário:", 0 , 1, 'L');
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(210,210,210);
	
	$pdf->Cell(9, 0.6, "FUNCIONÁRIO", 1, 0, "L", 1);
	$pdf->Cell(4, 0.6, "TOTAL", 1, 0, "C", 1);
	$pdf->Cell(4, 0.6, "%", 1, 1, "C", 1);
	
	$result_fun= mysql_query("select distinct(man_rms_andamento.id_usuario) as id_usuario, pessoas.nome_rz
									from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, pessoas
									where man_rms_andamento.id_usuario = usuarios.id_usuario
									and   man_rms.id_rm = man_rms_andamento.id_rm
									and   man_rms_andamento.id_situacao = '5'
									and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
									and   rh_carreiras.atual = '1'
									and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   pessoas.id_pessoa = rh_funcionarios.id_pessoa
									and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
									order by pessoas.nome_rz asc
									") or die(mysql_error());
	
	$result_total= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
									where man_rms.id_usuario = usuarios.id_usuario
									and   man_rms.id_rm = man_rms_andamento.id_rm
									and   man_rms_andamento.id_situacao = '5'
									and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
									and   rh_carreiras.atual = '1'
									and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
									and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
									") or die(mysql_error());
	
	$linhas_total= mysql_num_rows($result_total);
	
	$total_rm= 0;
	
	unset($valores);
	unset($labels);
	
	$valores= array();
	$labels= array();
	
	$i=0;
	
	while ($rs_fun= mysql_fetch_object($result_fun)) {
	
		$result_total_execucoes= mysql_query("select distinct(man_rms.id_rm)
												from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
												where man_rms_andamento.id_usuario = usuarios.id_usuario
												and   man_rms.id_rm = man_rms_andamento.id_rm
												and   man_rms_andamento.id_situacao = '5'
												and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
												and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
												and   rh_carreiras.atual = '1'
												and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
												and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
												and   man_rms_andamento.id_usuario = '". $rs_fun->id_usuario ."'
												") or die(mysql_error());
		
		$linhas_total_execucoes= mysql_num_rows($result_total_execucoes);
		
		if (($i%2)==0) $fill=1;
		else $fill= 0;
		
		$pdf->SetFillColor(235,235,235);
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		$percent= ($linhas_total_execucoes*100)/$linhas_total;
		
		$pdf->Cell(9, 0.5, $rs_fun->nome_rz, 1, 0, "L", $fill);
		$pdf->Cell(4, 0.5, $linhas_total_execucoes, 1, 0, "C", $fill);
		$pdf->Cell(4, 0.5, fnum($percent) ." %", 1, 1, "C", $fill);
		
		$total_rm += $linhas_total_execucoes;
		
		$valores[$i]= $linhas_total_execucoes;
		$labels[$i]= $rs_fun->nome_rz;
		
		$i++;
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(9, 0.5, "", 0, 0, "L", 0);
	$pdf->Cell(4, 0.5, $total_rm, 1, 0, "C", !$fill);
	$pdf->Cell(4, 0.5, "", 0, 1, "L", 0);
	
	
	
	$pdf->Ln();
	
	$pizza= pizza($valores, $labels, 1);
	if ($pizza) {
		$pdf->Ln();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, "Execuções por funcionário", 0 , 1, 'L');
		$pdf->Cell(0, 0.2, "", 0, 1);
		
		$pdf->Cell(0, 0, $pdf->Image($pizza, $pdf->GetX(), $pdf->GetY(), 18, 13), 0, 1, 'L', false );	
	}
	unlink($pizza);
	
	$pdf->Ln();
	
	$pdf->AddPage();
	
	$pdf->SetXY(0,1.75);
	
	$barra_horizontal= barra_horizontal($valores, $labels, 1);
	if ($barra_horizontal) {
	
		$pdf->Ln();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, "Execuções por funcionário", 0 , 1, 'L');
		$pdf->Cell(0, 0.2, "", 0, 1);
	
		$pdf->Cell(0, 0, $pdf->Image($barra_horizontal, $pdf->GetX(), $pdf->GetY(), 18, 18), 0, 1, 'L', false );	
	}
	unlink($barra_horizontal);
	
	
	
	
	// ---------------------------------------------------------------
	// ---------------------------------------------------------------
	// ---------------------------------------------------------------
	
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	$pdf->Ln();
	
	
	
	
	$pdf->Ln();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "Abertas por Setor", 0 , 1, 'L');
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(210,210,210);
	
	$pdf->Cell(9, 0.6, "SETOR", 1, 0, "L", 1);
	$pdf->Cell(4, 0.6, "TOTAL", 1, 0, "C", 1);
	$pdf->Cell(4, 0.6, "%", 1, 1, "C", 1);
	
	$result_dep= mysql_query("select distinct(man_rms.id_departamento) as id_departamento, rh_departamentos.departamento
									from man_rms, man_rms_andamento, rh_departamentos
									where man_rms_andamento.id_rm = man_rms.id_rm
									and   man_rms_andamento.id_situacao = '1'
									and   man_rms.id_departamento = rh_departamentos.id_departamento
									and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
									and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
									order by rh_departamentos.departamento asc
									") or die(mysql_error());
	
	$result_total= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
									where man_rms.id_rm = man_rms_andamento.id_rm
									and   man_rms_andamento.id_situacao = '1'
									and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
									") or die(mysql_error());
	
	$linhas_total= mysql_num_rows($result_total);
	
	$total_rm= 0;
	
	unset($valores);
	unset($labels);
	
	$valores= array();
	$labels= array();
	$i=0;
	
	while ($rs_dep= mysql_fetch_object($result_dep)) {
	
		$result_total_execucoes= mysql_query("select * from man_rms, man_rms_andamento
												where man_rms_andamento.id_rm = man_rms.id_rm
												and   man_rms_andamento.id_situacao = '1'
												and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
												and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   man_rms.id_departamento = '". $rs_dep->id_departamento ."'
												") or die(mysql_error());
		
		$linhas_total_execucoes= mysql_num_rows($result_total_execucoes);
		
		if (($i%2)==0) $fill=1;
		else $fill= 0;
		
		$pdf->SetFillColor(235,235,235);
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		$percent= ($linhas_total_execucoes*100)/$linhas_total;
		
		$pdf->Cell(9, 0.5, $rs_dep->departamento, 1, 0, "L", $fill);
		$pdf->Cell(4, 0.5, $linhas_total_execucoes, 1, 0, "C", $fill);
		$pdf->Cell(4, 0.5, fnum($percent) ." %", 1, 1, "C", $fill);
		
		$total_rm += $linhas_total_execucoes;
		
		$valores[$i]= $linhas_total_execucoes;
		$labels[$i]= $rs_dep->departamento;
		
		$i++;
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(9, 0.5, "", 0, 0, "L", 0);
	$pdf->Cell(4, 0.5, $total_rm, 1, 0, "C", !$fill);
	$pdf->Cell(4, 0.5, "", 0, 1, "L", 0);
	
	$pdf->Ln();
	
	$pdf->AddPage();
	
	$pdf->SetXY(0,1.75);
	
	$pizza= pizza($valores, $labels, 1);
	if ($pizza) {
		$pdf->Ln();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, "Abertas por Setor", 0 , 1, 'L');
		$pdf->Cell(0, 0.2, "", 0, 1);
		
		$pdf->Cell(0, 0, $pdf->Image($pizza, $pdf->GetX(), $pdf->GetY(), 18, 13), 0, 1, 'L', false );	
	}
	unlink($pizza);
	
	$pdf->Ln();
	
	$pdf->AddPage();
	
	$pdf->SetXY(0,1.75);
	
	$barra_horizontal= barra_horizontal($valores, $labels, 1);
	if ($barra_horizontal) {
	
		$pdf->Ln();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, "Abertas por Setor", 0 , 1, 'L');
		$pdf->Cell(0, 0.2, "", 0, 1);
	
		$pdf->Cell(0, 0, $pdf->Image($barra_horizontal, $pdf->GetX(), $pdf->GetY(), 18, 18), 0, 1, 'L', false );	
	}
	unlink($barra_horizontal);
	
	
	
	// ---------------------------------------------------------------
	// ---------------------------------------------------------------
	// ---------------------------------------------------------------
	
	
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->Ln();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "Requisições abertas x fechadas:", 0 , 1, 'L');
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(210,210,210);
	
	//$pdf->Cell(9, 0.6, "", 1, 0, "L", 1);
	$pdf->Cell(2, 0.6, "ABERTAS", 1, 0, "C", 1);
	$pdf->Cell(2.2, 0.6, "FINALIZADAS", 1, 0, "C", 1);
	$pdf->Cell(4, 0.6, "ABERTAS E FINALIZADAS", 1, 0, "C", 1);
	$pdf->Cell(4.2, 0.6, "ACUMULADAS PRÓXIMO MÊS", 1, 0, "C", 1);
	$pdf->Cell(4.5, 0.6, "TOTAL ABERTAS EM ". date("d/m/Y"), 1, 1, "C", 1);
	
	$result_total_abertas= mysql_query("select distinct(man_rms.id_rm)
											from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
											where man_rms.id_usuario = usuarios.id_usuario
											and   man_rms.id_rm = man_rms_andamento.id_rm
											and   man_rms_andamento.id_situacao = '1'
											and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
											and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
											and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
											") or die(mysql_error());
	$linhas_total_abertas= mysql_num_rows($result_total_abertas);
	
	$result_total_finalizadas= mysql_query("select distinct(man_rms.id_rm)
											from man_rms, man_rms_andamento, usuarios, rh_funcionarios, rh_carreiras, rh_departamentos
											where man_rms.id_usuario = usuarios.id_usuario
											and   man_rms.id_rm = man_rms_andamento.id_rm
											and   man_rms_andamento.id_situacao = '5'
											and   usuarios.id_funcionario = rh_funcionarios.id_funcionario
											and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
											and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
											") or die(mysql_error());
	$linhas_total_finalizadas= mysql_num_rows($result_total_finalizadas);
	
	$result_total_abertas_finalizadas= mysql_query("select distinct(man_rms.id_rm)
													from man_rms
													where man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   man_rms.id_rm IN
													(
													select man_rms_andamento.id_rm from man_rms_andamento
													where  DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
													and   man_rms_andamento.id_situacao = '1'
													)
													and   man_rms.id_rm IN
													(
													select man_rms_andamento.id_rm from man_rms_andamento
													where  DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
													and   man_rms_andamento.id_situacao = '5'
													)
													") or die(mysql_error());
	$linhas_total_abertas_finalizadas= mysql_num_rows($result_total_abertas_finalizadas);
	
	$result_total_abertas_geral= mysql_query("select distinct(man_rms.id_rm)
											from man_rms, man_rms_andamento
											where man_rms.id_rm = man_rms_andamento.id_rm
											and   man_rms_andamento.id_situacao = '1'
											and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
	
	unset($valores);
	unset($labels);
	
	$valores= array();
	$labels= array();
	
	$total_abertas=0;
	
	while ($rs_total_abertas_geral= mysql_fetch_object($result_total_abertas_geral)) {
		$result_andamento_atual= mysql_query("select * from man_rms_andamento
												where id_rm= '". $rs_total_abertas_geral->id_rm ."'
												order by id_rm_andamento
												desc limit 1");
		$rs_andamento_atual= mysql_fetch_object($result_andamento_atual);
		
		if ($rs_andamento_atual->id_situacao!=5) $total_abertas++;
	}
	
	$fill= 0;
	
	$pdf->SetFillColor(235,235,235);
	$pdf->SetFont('ARIALNARROW', '', 8);
	
	$linhas_acumuladas= $linhas_total_abertas-$linhas_total_abertas_finalizadas;
	
	//$pdf->Cell(9, 0.5, "", 1, 0, "L", $fill);
	$pdf->Cell(2, 0.5, $linhas_total_abertas, 1, 0, "C", $fill);
	$pdf->Cell(2.2, 0.5, $linhas_total_finalizadas, 1, 0, "C", $fill);
	$pdf->Cell(4, 0.5, $linhas_total_abertas_finalizadas, 1, 0, "C", $fill);
	$pdf->Cell(4.2, 0.5, $linhas_acumuladas, 1, 0, "C", $fill);
	$pdf->Cell(4.5, 0.5, $total_abertas, 1, 1, "C", $fill);
	
	
	
	
	// ---------------------------------------------------------------
	// ---------------------------------------------------------------
	// ---------------------------------------------------------------
	
	
	/*
	
	$pdf->Ln();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "Requisições por equipamento:", 0 , 1, 'L');
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(210,210,210);
	
	$pdf->Cell(9, 0.6, "EQUIPAMENTO", 1, 0, "L", 1);
	$pdf->Cell(4, 0.6, "TOTAL", 1, 0, "C", 1);
	$pdf->Cell(4, 0.6, "%", 1, 1, "C", 1);
	
	$result_eq= mysql_query("select distinct(man_rms.id_equipamento) as id_equipamento, op_equipamentos.equipamento
									from man_rms, man_rms_andamento, op_equipamentos
									where man_rms.id_rm = man_rms_andamento.id_rm
									and   man_rms_andamento.id_situacao = '1'
									and   man_rms.id_equipamento = op_equipamentos.id_equipamento
									and   man_rms.tipo_rm = 'e'
									and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
									and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
									order by op_equipamentos.equipamento asc
									") or die(mysql_error());
	
	$result_total= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
									where man_rms.id_rm = man_rms_andamento.id_rm
									and   man_rms_andamento.id_situacao = '1'
									and   man_rms.tipo_rm = 'e'
									and   man_rms.id_equipamento <> '0'
									and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
									") or die(mysql_error());
	
	$linhas_total= mysql_num_rows($result_total);
	
	$total_rm= 0;
	
	unset($valores);
	unset($labels);
	
	$valores= array();
	$labels= array();
	
	$i=0;
	
	while ($rs_eq= mysql_fetch_object($result_eq)) {
	
		$result_total_execucoes= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
												where man_rms.id_rm = man_rms_andamento.id_rm
												and   man_rms_andamento.id_situacao = '1'
												and   man_rms.tipo_rm = 'e'
												and   man_rms.id_equipamento <> '0'
												and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
												and   man_rms.id_equipamento = '". $rs_eq->id_equipamento ."'
												") or die(mysql_error()); 
		
		$linhas_total_execucoes= mysql_num_rows($result_total_execucoes);
		
		if (($i%2)==0) $fill=1;
		else $fill= 0;
		
		$pdf->SetFillColor(235,235,235);
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		$percent= ($linhas_total_execucoes*100)/$linhas_total;
		
		$pdf->Cell(9, 0.5, $rs_eq->equipamento, 1, 0, "L", $fill);
		$pdf->Cell(4, 0.5, $linhas_total_execucoes, 1, 0, "C", $fill);
		$pdf->Cell(4, 0.5, fnum($percent) ." %", 1, 1, "C", $fill);
		
		$total_rm += $linhas_total_execucoes;
		
		$i++;
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(9, 0.5, "", 0, 0, "L", 0);
	$pdf->Cell(4, 0.5, $total_rm, 1, 0, "C", !$fill);
	$pdf->Cell(4, 0.5, "", 0, 1, "L", 0);
	
	
	*/
	
	
	
	
	$pdf->Ln();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "Requisições por tipo de serviço:", 0 , 1, 'L');
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(210,210,210);
	
	$pdf->Cell(9, 0.6, "TIPO", 1, 0, "L", 1);
	$pdf->Cell(4, 0.6, "TOTAL", 1, 0, "C", 1);
	$pdf->Cell(4, 0.6, "%", 1, 1, "C", 1);
	
	$result_eq= mysql_query("select * from man_servicos_tipos
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									order by servico_tipo asc
									") or die(mysql_error());
	
	$result_total= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
									where man_rms.id_rm = man_rms_andamento.id_rm
									and   man_rms_andamento.id_situacao = '1'
									
									and   man_rms.id_servico_tipo is not NULL
									and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
									") or die(mysql_error());
	
	$linhas_total= mysql_num_rows($result_total);
	
	$total_rm= 0;
	
	
	unset($valores);
	unset($labels);
	
	$valores= array();
	$labels= array();
	
	$i=0;
	
	while ($rs_eq= mysql_fetch_object($result_eq)) {
	
		$result_total_execucoes= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
												where man_rms.id_rm = man_rms_andamento.id_rm
												and   man_rms_andamento.id_situacao = '1'
												
												and   man_rms.id_servico_tipo is not NULL
												and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
												and   man_rms.id_servico_tipo = '". $rs_eq->id_servico_tipo ."'
												") or die(mysql_error()); 
		
		$linhas_total_execucoes= mysql_num_rows($result_total_execucoes);
		
		if (($i%2)==0) $fill=1;
		else $fill= 0;
		
		$pdf->SetFillColor(235,235,235);
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		if ($linhas_total>0)
			$percent= ($linhas_total_execucoes*100)/$linhas_total;
		else
			$percent=0;
		
		$pdf->Cell(9, 0.5, pega_tipo_servico($rs_eq->id_servico_tipo), 1, 0, "L", $fill);
		$pdf->Cell(4, 0.5, $linhas_total_execucoes, 1, 0, "C", $fill);
		$pdf->Cell(4, 0.5, fnum($percent) ." %", 1, 1, "C", $fill);
		
		$valores[$i]= $linhas_total_execucoes;
		$labels[$i]= pega_tipo_servico($rs_eq->id_servico_tipo);
		
		$total_rm += $linhas_total_execucoes;
		
		$i++;
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(9, 0.5, "", 0, 0, "L", 0);
	$pdf->Cell(4, 0.5, $total_rm, 1, 0, "C", !$fill);
	$pdf->Cell(4, 0.5, "", 0, 1, "L", 0);
	
	
	
	$pdf->Ln();
	
	
	$pizza= pizza($valores, $labels, 1);
	if ($pizza) {
		$pdf->Ln();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, "Requisições por tipo de serviço", 0 , 1, 'L');
		$pdf->Cell(0, 0.2, "", 0, 1);
		
		$pdf->Cell(0, 0, $pdf->Image($pizza, $pdf->GetX(), $pdf->GetY(), 18, 13), 0, 1, 'L', false );	
	}
	unlink($pizza);
	
	$pdf->Ln();
	
	$pdf->AddPage();
	
	$pdf->SetXY(0,1.75);
	
	$barra_horizontal= barra_horizontal($valores, $labels, 1);
	if ($barra_horizontal) {
	
		$pdf->Ln();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, "Requisições por tipo de serviço", 0 , 1, 'L');
		$pdf->Cell(0, 0.2, "", 0, 1);
	
		$pdf->Cell(0, 0, $pdf->Image($barra_horizontal, $pdf->GetX(), $pdf->GetY(), 18, 18), 0, 1, 'L', false );	
	}
	unlink($barra_horizontal);
	
	
	
	
	
	// ---------------------------------------------------------------
	// ---------------------------------------------------------------
	// ---------------------------------------------------------------
	
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->Ln();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "Preventiva x Corretiva:", 0 , 1, 'L');
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(210,210,210);
	
	$pdf->Cell(9, 0.6, "TIPO", 1, 0, "L", 1);
	$pdf->Cell(4, 0.6, "TOTAL", 1, 0, "C", 1);
	$pdf->Cell(4, 0.6, "%", 1, 1, "C", 1);
	
	$result_eq= mysql_query("select * from man_servicos_tipos
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									order by servico_tipo asc
									") or die(mysql_error());
	
	$result_total= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
									where man_rms.id_rm = man_rms_andamento.id_rm
									and   man_rms_andamento.id_situacao = '1'
									
									and   man_rms.finalidade_rm is not NULL
									and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
									") or die(mysql_error());
	
	$linhas_total= mysql_num_rows($result_total);
	
	unset($valores);
	unset($labels);
	
	$valores= array();
	$labels= array();
	
	$total_rm= 0;
	
	$i=0;
	
	for ($i=0; $i<2; $i++) {
		
		if ($i==0) {
			$tipo= "Preventiva";
			$t= 'p';
		}
		else {
			$tipo= "Corretiva";
			$t= 'c';
		}
			
		$result_total_execucoes= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
												where man_rms.id_rm = man_rms_andamento.id_rm
												and   man_rms_andamento.id_situacao = '1'
												
												and   man_rms.finalidade_rm = '". $t ."'
												and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
												") or die(mysql_error()); 
		
		$linhas_total_execucoes= mysql_num_rows($result_total_execucoes);
		
		if (($i%2)==0) $fill=1;
		else $fill= 0;
		
		$pdf->SetFillColor(235,235,235);
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		if ($linhas_total>0)
			$percent= ($linhas_total_execucoes*100)/$linhas_total;
		else
			$percent=0;
		
		$pdf->Cell(9, 0.5, $tipo, 1, 0, "L", $fill);
		$pdf->Cell(4, 0.5, $linhas_total_execucoes, 1, 0, "C", $fill);
		$pdf->Cell(4, 0.5, fnum($percent) ." %", 1, 1, "C", $fill);
		
		$valores[$i]= $linhas_total_execucoes;
		$labels[$i]= $tipo;
		
		$total_rm += $linhas_total_execucoes;
		
		//$i++;
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(9, 0.5, "", 0, 0, "L", 0);
	$pdf->Cell(4, 0.5, $total_rm, 1, 0, "C", !$fill);
	$pdf->Cell(4, 0.5, "", 0, 1, "L", 0);
	
	
	
	$pdf->Ln();
	
	
	$pizza= pizza($valores, $labels, 1);
	if ($pizza) {
		$pdf->Ln();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, "Preventiva x Corretiva", 0 , 1, 'L');
		$pdf->Cell(0, 0.2, "", 0, 1);
		
		$pdf->Cell(0, 0, $pdf->Image($pizza, $pdf->GetX(), $pdf->GetY(), 18, 13), 0, 1, 'L', false );	
	}
	unlink($pizza);
	
	$pdf->Ln();
	
	$pdf->AddPage();
	
	$pdf->SetXY(0,1.75);
	
	$barra_horizontal= barra_horizontal($valores, $labels, 1);
	if ($barra_horizontal) {
	
		$pdf->Ln();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.6, "Preventiva x Corretiva", 0 , 1, 'L');
		$pdf->Cell(0, 0.2, "", 0, 1);
	
		$pdf->Cell(0, 0, $pdf->Image($barra_horizontal, $pdf->GetX(), $pdf->GetY(), 18, 18), 0, 1, 'L', false );	
	}
	unlink($barra_horizontal);
	
	
		
	
	/*
	
	$pdf->Ln();
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, "Equipamento x Predial:", 0 , 1, 'L');
	$pdf->Cell(0, 0.2, "", 0, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(210,210,210);
	
	//$pdf->Cell(9, 0.6, "", 1, 0, "L", 1);
	$pdf->Cell(4, 0.6, "PREDIAL", 1, 0, "C", 1);
	$pdf->Cell(4, 0.6, "EQUIPAMENTO", 1, 1, "C", 1);
	
	$result_total_eq= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
									where man_rms.id_rm = man_rms_andamento.id_rm
									and   man_rms_andamento.id_situacao = '1'
									and   man_rms.tipo_rm = 'e'
									and   man_rms.id_equipamento <> '0'
									and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
									") or die(mysql_error());
	
	$linhas_total_eq= mysql_num_rows($result_total_eq);
	
	$result_total_pre= mysql_query("select distinct(man_rms.id_rm) from man_rms, man_rms_andamento
									where man_rms.id_rm = man_rms_andamento.id_rm
									and   man_rms_andamento.id_situacao = '1'
									and   man_rms.tipo_rm = 'p'
									and   man_rms.id_equipamento = '0'
									and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y') = '". $_POST["periodo"] ."'
									") or die(mysql_error());
	
	$linhas_total_pre= mysql_num_rows($result_total_pre);
	
	$fill= 0;
	
	$pdf->SetFillColor(235,235,235);
	$pdf->SetFont('ARIALNARROW', '', 8);
	
	//$pdf->Cell(9, 0.5, "", 1, 0, "L", $fill);
	$pdf->Cell(4, 0.5, $linhas_total_pre, 1, 0, "C", $fill);
	$pdf->Cell(4, 0.5, $linhas_total_eq, 1, 1, "C", $fill);
	
	*/
	
	$pdf->AliasNbPages();
	$pdf->Output("rm_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}

?>