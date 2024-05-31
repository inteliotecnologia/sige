<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode("r", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato_rh.php");
		
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "RELATÓRIO DE FÉRIAS", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, $_POST["ano"], 0 , 1, 'R');
	
	$pdf->Ln();$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$result= mysql_query("select *
								from rh_funcionarios, pessoas, rh_carreiras
								where rh_funcionarios.id_pessoa = pessoas.id_pessoa
								and   rh_funcionarios.status_funcionario = '1'
								and   rh_carreiras.atual = '1'
								and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
								order by pessoas.nome_rz asc
								") or die(mysql_error());
	
	$pdf->Cell(5.8, 0.7, "NOME", 1, 0, "L", 1);
	$pdf->Cell(2.1, 0.7, "ADMISSÃO", 1, 0, "L", 1);
	$pdf->Cell(3.6, 0.7, "PERÍODO AQUISITIVO", 1, 0, "L", 1);
	$pdf->Cell(2.75, 0.7, "FÉRIAS VENCIDAS", 1, 0, "L", 1);
	$pdf->Cell(2.75, 0.7, "PERÍODO CRÍTICO", 1, 1, "L", 1);
	
	$pdf->SetFont('ARIALNARROW', '', 9);
	$pdf->SetFillColor(235,235,235);
	
	$j=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($j%2)==0) $fill= 0;
		else $fill= 1;
		
		$data_admissao= pega_data_admissao($rs->id_funcionario);
		
		$periodo_aquisitivo1= soma_data($data_admissao, 0, 0, 1);
		
		$periodo_aquisitivo2= soma_data($periodo_aquisitivo1, 0, 0, 1);
		$periodo_aquisitivo2= soma_data($periodo_aquisitivo2, -1, 0, 0);
		
		$ferias_vencidas= $periodo_aquisitivo2;
		
		$periodo_critico= soma_data($ferias_vencidas, 0, 6, 0);
		
		$pdf->Cell(5.8, 0.7, $rs->nome_rz, 1, 0, "L", $fill);
		$pdf->Cell(2.1, 0.7, $data_admissao, 1, 0, "L", $fill);
		$pdf->Cell(3.6, 0.7, $periodo_aquisitivo1 ." à ". $periodo_aquisitivo2, 1, 0, "L", $fill);
		$pdf->Cell(2.75, 0.7, $ferias_vencidas, 1, 0, "L", $fill);
		$pdf->Cell(2.75, 0.7, $periodo_critico, 1, 1, "L", $fill);
		
		$j++;
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("ferias_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>