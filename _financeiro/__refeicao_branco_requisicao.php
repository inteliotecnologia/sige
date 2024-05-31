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
	
	/*$result= mysql_query("select * from fi_refeicoes
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_refeicao = '". $_GET["id_refeicao"] ."'
								") or die(mysql_error());
	$rs= mysql_fetch_object($result);
	*/
	
	$pdf->AddPage();
	
	for ($i=0; $i<6; $i++) {
		switch($i) {
			case 0: $soma_x=0; $soma_y=0;
			break;
			case 1: $soma_x=0; $soma_y=10.5;
			break;
			case 2: $soma_x=9.9; $soma_y=0;
			break;
			case 3: $soma_x=9.9; $soma_y=10.5;
			break;
			case 4: $soma_x=19.8; $soma_y=0;
			break;
			case 5: $soma_x=19.8; $soma_y=10.5;
			break;
		}
		
		$pdf->SetXY($soma_x, $soma_y);
		
		//$pdf->Cell(9.9, 10.4, "", 1, 1);
		
		$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa"] .".jpg", 0.5+$soma_x, 0.5+$soma_y, 4, 1.54);
		
		$pdf->SetXY(5+$soma_x, 0.75+$soma_y);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->MultiCell(4.0, 0.45, "ORDEM DE REFEIÇÃO Nº ______", 0, "C");
		
		$pdf->SetXY(0.5+$soma_x, 2.5+$soma_y);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(3, 0.75, "DATA:", 0, 0, "R");
		$pdf->SetFont('ARIALNARROW', '', 11);
		$pdf->Cell(5.9, 0.75, "", "B", 1, "L");
		
		$pdf->SetX(0.5+$soma_x);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(3, 0.75, "MOTIVO:", 0, 0, "R");
		$pdf->SetFont('ARIALNARROW', '', 11);
		$pdf->Cell(5.9, 0.75, "", "B", 1, "L");
		
		$pdf->SetX(0.5+$soma_x);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(3, 0.75, "Nº ALMOÇOS:", 0, 0, "R");
		$pdf->SetFont('ARIALNARROW', '', 11);
		$pdf->Cell(5.9, 0.75, "", "B", 1, "L");
		
		$pdf->SetY($pdf->GetY()+0.25);
		
		$pdf->SetX(0.5+$soma_x);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(3, 0.75, "", 0, 0, "R");
		$pdf->SetFont('ARIALNARROW', '', 11);
		$pdf->Cell(5.9, 0.75, "(   ) LIVRE     (   ) À QUILO ", "", 1, "L");
		
		$pdf->SetX(0.5+$soma_x);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(3, 0.75, "", 0, 0, "R");
		$pdf->SetFont('ARIALNARROW', '', 11);
		$pdf->Cell(5.9, 0.75, "(   ) COM BEBIDA     (   ) SEM BEBIDA ", "", 1, "L");
		
		$pdf->SetX(0.5+$soma_x);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(3, 0.75, "VALOR:", 0, 0, "R");
		$pdf->SetFont('ARIALNARROW', '', 11);
		$pdf->Cell(5.9, 0.75, "", "B", 1, "L");
		
		$pdf->Cell(1, 1.35, "", 0, 1);
		
		$pdf->SetX(1+$soma_x);
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(7.9, 0.75, "AUTORIZADO POR", "T", 1, "C");
	}
	
	$pdf->SetDrawColor(200,200,200);
	
	$pdf->Line(9.9, 0, 9.9, 21);
	$pdf->Line(0, 10.5, 29.7, 10.5);
	$pdf->Line(19.8, 0, 19.8, 21);
	
	$pdf->Output("refeicao_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>