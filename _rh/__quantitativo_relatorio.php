<?
require_once("conexao.php");
require_once("funcoes.php");
require_once("funcoes_espelho.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode_algum("rh", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato_rh.php");
	
	$periodo2= explode('/', $_POST["periodo"]);
	
	$data1_mk= mktime(14, 0, 0, $periodo[0], 1, $periodo[1]);
	
	$dias_mes= date("t", $data1_mk);
	$data2_mk= mktime(14, 0, 0, $periodo[0], $dias_mes, $periodo[1]);
	
	$data1= date("d/m/Y", $data1_mk);
	$data2= date("d/m/Y", $data2_mk);
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(1.5, 1.5, 1.5);
	$pdf->SetAutoPageBreak(true, 2.5);
	$pdf->SetFillColor(200,200,200);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
		
	$pdf->AddPage();
	
	$pdf->SetXY(16.9, 1.75);
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "RELATÓRIO QUANTITATIVO DE FUNCIONÁRIOS", 0, 1, "R");
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ."/". $periodo2[1], 0 , 1, "R");
	
	$pdf->Ln();$pdf->Ln();
	
	// ------------- tabela
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	//2
	
	$pdf->Cell(5, 0.6, "DEPARTAMENTO", 1, 0, "L", 1);
	$pdf->Cell(1.5, 0.6, "ATIVOS", 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, "FÉRIAS", 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, "PERÍCIA", 1, 0, "C", 1);
	$pdf->Cell(3, 0.6, "LIC. MATERNIDADE", 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, "ADMISSÕES", 1, 0, "C", 1);
	$pdf->Cell(2.5, 0.6, "DESLIGAMENTOS", 1, 1, "C", 1);
	
	if ($_POST["id_departamento"]!="") $str_depto= " and   id_departamento = '". $_POST["id_departamento"] ."' ";
	
	$result_depto= mysql_query("select * from rh_departamentos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								". $str_depto ."
								order by departamento asc
								");
	
	$total_ativos= 0;
	$total_ferias= 0;
	$total_pericia= 0;
	$total_lm= 0;
	$total_admissoes= 0;
	$total_desligamentos= 0;
	
	$j=1;
	
	$pdf->SetFillColor(230,230,230);
	
	while ($rs_depto = mysql_fetch_object($result_depto)) {
		
		if (($j%2)==0) $fill=1;
		else $fill= 0;
		
		$periodo2= explode("/", $_POST["periodo"]);
		$proximo_mes= $periodo2[1] . $periodo2[0] ."01";
		
		$result_ativos= mysql_query(" select * from rh_funcionarios, pessoas, rh_carreiras
										where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										and   rh_carreiras.atual = '1'
										and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
										and   rh_funcionarios.status_funcionario <> '2'
										and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
										
										and   rh_funcionarios.id_funcionario NOT IN
										(
										select id_funcionario from rh_carreiras
										where id_acao_carreira = '2'
										and   data < '". $proximo_mes ."'
										)
										
										and   rh_funcionarios.id_funcionario IN
										(
										select id_funcionario from rh_carreiras
										where id_acao_carreira = '1'
										and   data <= '". $proximo_mes ."'
										)
										") or die(mysql_error());
		$linhas_ativos= mysql_num_rows($result_ativos);
		
		$result_ferias= mysql_query("select distinct(rh_carreiras.id_funcionario) from rh_carreiras, rh_afastamentos
										where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_funcionario = rh_afastamentos.id_funcionario
										and   rh_afastamentos.tipo_afastamento = 'f'
										and   DATE_FORMAT(rh_afastamentos.data_inicial, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
		
		$rs_ferias= mysql_fetch_object($result_ferias);
		$linhas_ferias= mysql_num_rows($result_ferias);
		
		$result_pericia= mysql_query("select distinct(rh_carreiras.id_funcionario) from rh_carreiras, rh_afastamentos
										where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_funcionario = rh_afastamentos.id_funcionario
										and   rh_afastamentos.tipo_afastamento = 'p'
										and   DATE_FORMAT(rh_afastamentos.data_inicial, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
		$rs_pericia= mysql_fetch_object($result_pericia);
		$linhas_pericia= mysql_num_rows($result_pericia);
		
		$result_lm= mysql_query("select distinct(rh_carreiras.id_funcionario) from rh_carreiras, rh_afastamentos
										where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_funcionario = rh_afastamentos.id_funcionario
										and   DATE_FORMAT(rh_afastamentos.data_inicial, '%m/%Y') = '". $_POST["periodo"] ."'
										and   rh_afastamentos.tipo_afastamento = 'o'
										and   rh_afastamentos.id_motivo = '16'
										") or die(mysql_error());
		$rs_lm= mysql_fetch_object($result_lm);
		$linhas_lm= mysql_num_rows($result_lm);
		
		$result_admissao= mysql_query("select count(rh_carreiras.id_carreira) as total_admissoes from rh_carreiras
										where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
										and   rh_carreiras.id_acao_carreira = '1'
										and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
		$rs_admissao= mysql_fetch_object($result_admissao);
		
		$result_desligamento= mysql_query("select count(rh_carreiras.id_carreira) as total_desligamentos from rh_carreiras
											where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
											and   rh_carreiras.id_acao_carreira = '2'
											and   DATE_FORMAT(rh_carreiras.data, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
		$rs_desligamento= mysql_fetch_object($result_desligamento);
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->Cell(5, 0.6, $rs_depto->departamento, 1, 0, "L", $fill);
		$pdf->Cell(1.5, 0.6, $linhas_ativos, 1, 0, "C", $fill);
		$pdf->Cell(2, 0.6, $linhas_ferias, 1, 0, "C", $fill);
		$pdf->Cell(2, 0.6, $linhas_pericia, 1, 0, "C", $fill);
		$pdf->Cell(3, 0.6, $linhas_lm, 1, 0, "C", $fill);
		$pdf->Cell(2, 0.6, $rs_admissao->total_admissoes, 1, 0, "C", $fill);
		$pdf->Cell(2.5, 0.6, $rs_desligamento->total_desligamentos, 1, 1, "C", $fill);
		
		$total_ativos+= $linhas_ativos;
		$total_ferias+= $linhas_ferias;
		$total_pericia+= $linhas_pericia;
		$total_lm+= $linhas_lm;
		$total_admissoes+= $rs_admissao->total_admissoes;
		$total_desligamentos+= $rs_desligamento->total_desligamentos;
		
		$j++;
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(200,200,200);
	
	$pdf->Cell(5, 0.6, "", 0, 0, "L", 0);
	$pdf->Cell(1.5, 0.6, $total_ativos, 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, $total_ferias, 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, $total_pericia, 1, 0, "C", 1);
	$pdf->Cell(3, 0.6, $total_lm, 1, 0, "C", 1);
	$pdf->Cell(2, 0.6, $total_admissoes, 1, 0, "C", 1);
	$pdf->Cell(2.5, 0.6, $total_desligamentos, 1, 1, "C", 1);
	
	$pdf->Ln();$pdf->Ln();
		
	
	$pdf->AliasNbPages(); 
	$pdf->Output("", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>