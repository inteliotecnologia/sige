<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("ps", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");	
	
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(200,200,200);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$i=0;
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "TABELA DE VEÍCULOS", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "", 0 , 1, 'R');

	$pdf->Ln();$pdf->Ln();
	
	$result= mysql_query("select * from op_veiculos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							order by codigo asc
							") or die(mysql_error());
	
	$i=1;
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	
	$pdf->Cell(3, 1, "CÓDIGO", 1, 0, "C", 1);
	$pdf->Cell(14, 1, " VEÍCULO", 1, 1, "L", 1);
	
	$pdf->SetFillColor(240,240,240);
	
	while ($rs = mysql_fetch_object($result)) {
		
		if (($i%2)==0) $fill=1;
		else $fill= 0;
		
		$pdf->SetFont('ARIALNARROW', '', 12);
		$pdf->Cell(3, 0.8, $rs->codigo, 1, 0, "C", $fill);
		$pdf->Cell(14, 0.8, " ". $rs->veiculo, 1, 1, "L", $fill);
		
		$i++;
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("tabela_veiculo_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>