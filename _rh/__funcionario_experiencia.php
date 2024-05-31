<?
require_once("conexao.php");
require_once("funcoes.php");

define('FPDF_FONTPATH','includes/fpdf/font/');
require("includes/fpdf/fpdf.php");
require("includes/fpdf/modelo_retrato.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 1);
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
	
	$result = mysql_query("select *, DATE_FORMAT(carreiras.data, '%d/%m/%Y') as data_admissao
										from funcionarios, pessoas, carreiras
										where funcionarios.id_funcionario = carreiras.id_funcionario
										and   carreiras.id_acao_carreira = '1'
										and   funcionarios.id_pessoa = pessoas.id_pessoa
										and   funcionarios.status_funcionario = '1'
										order by carreiras.data asc, pessoas.nome_rz asc
										") or die(mysql_error());
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	
	$pdf->Cell(6, 0.6, "NOME", 1, 0, 'L', 1);
	$pdf->Cell(3.5, 0.6, "DEPARTAMENTO", 1, 0, 'L', 1);
	$pdf->Cell(2.5, 0.6, "ADMISSÃO", 1, 0, 'C', 1);
	$pdf->Cell(2.5, 0.6, "EXP. 30 DIAS", 1, 0, 'C', 1);
	$pdf->Cell(2.5, 0.6, "EXP. 90 DIAS", 1, 1, 'C', 1);
		
	$pdf->SetFont('ARIALNARROW', '', 9);
	$pdf->SetFillColor(240,240,240);
	
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		
		$data30= soma_data($rs->data, 29);
		$data90= soma_data($rs->data, 89);
		
		$data_atual= date("Ymd");
		$data90_nova= str_replace("-", "", $data90);
		
		if ($data_atual<=$data90_nova) {
			if (($i%2)==0) $fill= 0;
			else $fill= 1;
			
			$pdf->Cell(6, 0.6, $rs->nome_rz, 1, 0, 'L', $fill);
			$pdf->Cell(3.5, 0.6, pega_departamento($rs->id_departamento), 1, 0, 'L', $fill);
			$pdf->Cell(2.5, 0.6, desformata_data($rs->data), 1, 0, 'C', $fill);
			$pdf->Cell(2.5, 0.6, desformata_data($data30), 1, 0, 'C', $fill);
			$pdf->Cell(2.5, 0.6, desformata_data($data90), 1, 1, 'C', $fill);
			
			$i++;
		}
	}
	
	$pdf->Ln();
	
	$pdf->Output("funcionario_experiencia_". date("d-m-Y_H:i:s") .".pdf", "I");
?>