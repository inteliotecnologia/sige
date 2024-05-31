<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode('u', $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");

	class PDF extends FPDF {
		//Page header
		function Header() {
		    //Title
			//$this->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa"] .".jpg", 0.5, 0.3, 5, 1.9287);
		} 

		//Page footer
		function Footer() {
			//$this->SetXY(0.5,27.5);
			//$this->SetFont('ARIALNARROW', '', 11);
			//$this->Cell(19, 1, "Esterilav - Lavação e Esterilização Hospitalar", 0 , 1, "L");			
		}
	}
		
	$pdf=new PDF("L", "cm", "A4");
	$pdf->SetLeftMargin(0.5);
	$pdf->SetRightMargin(0.5);
	$pdf->SetAutoPageBreak(true, 1);
	$pdf->SetFillColor(230,230,230);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	$y= 0;
	
	$result= mysql_query("select * from fi_abastecimentos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_abastecimento = '". $_GET["id_abastecimento"] ."'
								") or die(mysql_error());
	$rs= mysql_fetch_object($result);
	
	$pdf->AddPage();
	
	for ($i=0; $i<2; $i++) {
		if ($i==1) $soma= 10.5;
		
		$pdf->SetXY(0, 0+$soma);
		
		//$pdf->Cell(9.9, 10.4, "", 1, 1);
		
		$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa"] .".jpg", 0.5, 0.5+$soma, 4, 1.54);
		
		$pdf->SetXY(4.75, 0.9+$soma);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->MultiCell(4.9, 0.45, "ORDEM DE ABASTECIMENTO Nº ". $rs->id_abastecimento, 0, "C");
		
		$pdf->SetXY(0.5, 2.5+$soma);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(2.5, 0.9, "DATA:", 0, 0, "R");
		$pdf->SetFont('ARIALNARROW', '', 11);
		$pdf->Cell(6.4, 0.9, desformata_data($rs->data), 0, 1, "L");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(2.5, 0.9, "VEÍCULO:", 0, 0, "R");
		$pdf->SetFont('ARIALNARROW', '', 11);
		$pdf->Cell(6.4, 0.9, pega_veiculo($rs->id_veiculo), 0, 1, "L");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(2.5, 0.9, "PLACA:", 0, 0, "R");
		$pdf->SetFont('ARIALNARROW', '', 11);
		$pdf->Cell(6.4, 0.9, pega_placa_veiculo($rs->id_veiculo), 0, 1, "L");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(2.5, 0.9, "MOTORISTA:", 0, 0, "R");
		$pdf->SetFont('ARIALNARROW', '', 11);
		$pdf->Cell(6.4, 0.9, pega_funcionario($rs->id_funcionario), 0, 1, "L");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(2.5, 0.9, "PRODUTO:", 0, 0, "R");
		$pdf->SetFont('ARIALNARROW', '', 11);
		
		if ($rs->tipo_comb!="0") $sublinhado= "";
		else $sublinhado= "B";
		
		$pdf->Cell(6.4, 0.9, strtoupper(pega_tipo_combustivel($rs->tipo_comb)), $sublinhado, 1, "L");
		
		$pdf->Cell(1, 1.5, "", 0, 1);
		
		$pdf->SetX(1);
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(7.9, 0.9, pega_funcionario($rs->id_usuario_at), "T", 1, "C");
	}
	
	$pdf->SetDrawColor(200,200,200);
	$pdf->Line(9.9, 0, 9.9, 21);
	$pdf->Line(0, 10.5, 9.9, 10.5);
	
	$pdf->Output("abastecimento_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>