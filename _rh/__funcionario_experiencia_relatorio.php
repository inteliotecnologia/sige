<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode_algum("rvh", $_SESSION["permissao"])) {
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
	
	$i=0;
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,2);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.75, "CONTROLE DO PERÍODO DE EXPERIÊNCIA", 0, 1, 'R');
	$pdf->Ln();$pdf->Ln();
	
	$result= mysql_query("select *, DATE_FORMAT(rh_carreiras.data, '%d/%m/%Y') as data_admissao
										from rh_funcionarios, pessoas, rh_carreiras
										where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										and   rh_carreiras.id_acao_carreira = '1'
										and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
										and   rh_funcionarios.status_funcionario <> '2'
										and   rh_funcionarios.status_funcionario <> '0'
										order by rh_carreiras.data asc, pessoas.nome_rz asc
										") or die(mysql_error());
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(5.5, 0.6, "NOME", 1, 0, 'L', 1);
	$pdf->Cell(3.6, 0.6, "DEPARTAMENTO", 1, 0, 'L', 1);
	$pdf->Cell(2.5, 0.6, "TURNO", 1, 0, 'L', 1);
	$pdf->Cell(1.8, 0.6, "ADMISSÃO", 1, 0, 'C', 1);
	$pdf->Cell(1.8, 0.6, "30 DIAS", 1, 0, 'C', 1);
	$pdf->Cell(1.8, 0.6, "90 DIAS", 1, 1, 'C', 1);
		
	$pdf->SetFont('ARIALNARROW', '', 8);
	$pdf->SetFillColor(240,240,240);
	
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		
		$data30= soma_data($rs->data, 29, 0, 0);
		$data90= soma_data($rs->data, 89, 0, 0);
		
		$data_atual= date("Ymd");
		$data90_nova= str_replace("-", "", $data90);
		
		if ($data_atual<=$data90_nova) {
			if (($i%2)==0) $fill= 0;
			else $fill= 1;
			
			$pdf->Cell(5.5, 0.6, $rs->nome_rz, 1, 0, 'L', $fill);
			$pdf->Cell(3.6, 0.6, pega_departamento($rs->id_departamento), 1, 0, 'L', $fill);
			$pdf->Cell(2.5, 0.6, pega_turno($rs->id_turno), 1, 0, 'L', $fill);
			$pdf->Cell(1.8, 0.6, desformata_data($rs->data), 1, 0, 'C', $fill);
			$pdf->Cell(1.8, 0.6, desformata_data($data30), 1, 0, 'C', $fill);
			$pdf->Cell(1.8, 0.6, desformata_data($data90), 1, 1, 'C', $fill);
			
			$i++;
		}
	}
	
	$pdf->Ln();
	
	$pdf->AliasNbPages(); 
	$pdf->Output("funcionario_experiencia_". date("d-m-Y_H:i:s") .".pdf", "I");
}
$_SESSION["id_empresa_atendente2"]= "";

?>