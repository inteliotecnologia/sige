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
	$pdf->SetMargins(2, 1.5, 2);
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
	$pdf->Cell(0, 0.6, "RELATÓRIO DE ATESTADOS", 0, 1, "R");
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ."/". $periodo2[1], 0 , 1, "R");
	
	$pdf->Ln();$pdf->Ln();
	
	// ------------- tabela
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	//2
	
	$pdf->Cell(11, 0.6, "DEPARTAMENTO", 1, 0, "L", 1);
	$pdf->Cell(3, 0.6, "QUANTIDADE", 1, 0, "C", 1);
	$pdf->Cell(3, 0.6, "%", 1, 1, "C", 1);
	
	if ($_POST["id_departamento"]!="") $str_depto= " and   id_departamento = '". $_POST["id_departamento"] ."' ";
	
	$result_depto= mysql_query("select * from rh_departamentos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								". $str_depto ."
								order by departamento asc
								");
	
	$total_atestado= 0;
	$j=1;
	
	$pdf->SetFillColor(230,230,230);
	
	$result_atestado_geral= mysql_query("select *
										from  rh_afastamentos, rh_carreiras
										where rh_carreiras.atual = '1'
										and   rh_afastamentos.tipo_afastamento = 'a'
										and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_carreiras.id_funcionario = rh_afastamentos.id_funcionario
										and   DATE_FORMAT(rh_afastamentos.data_emissao, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
	
	$linhas_atestado_geral= mysql_num_rows($result_atestado_geral);
	
	while ($rs_depto = mysql_fetch_object($result_depto)) {
		
		if (($j%2)==0) $fill=1;
		else $fill= 0;
		
		$result_atestado= mysql_query("select *
										from  rh_afastamentos, rh_carreiras
										where rh_carreiras.id_departamento = '". $rs_depto->id_departamento ."'
										and   rh_afastamentos.tipo_afastamento = 'a'
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_carreiras.id_funcionario = rh_afastamentos.id_funcionario
										and   DATE_FORMAT(rh_afastamentos.data_emissao, '%m/%Y') = '". $_POST["periodo"] ."'
										") or die(mysql_error());
		
		$linhas_atestado= mysql_num_rows($result_atestado);
		
		$percent= (($linhas_atestado*100)/$linhas_atestado_geral);
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->Cell(11, 0.6, $rs_depto->departamento, 1, 0, "L", $fill);
		$pdf->Cell(3, 0.6, $linhas_atestado, 1, 0, "C", $fill);
		$pdf->Cell(3, 0.6, fnum($percent) ."%", 1, 1, "C", $fill);
		
		$total_atestado+= $linhas_atestado;
		
		$j++;
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(200,200,200);
	
	$pdf->Cell(11, 0.6, "", 0, 0, "L", 0);
	$pdf->Cell(3, 0.6, $total_atestado, 1, 1, "C", 1);
	
	$pdf->Ln();$pdf->Ln();
		
	
	$pdf->AliasNbPages(); 
	$pdf->Output("", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>