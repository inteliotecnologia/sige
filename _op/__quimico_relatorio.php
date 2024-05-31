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
	$pdf->Cell(0, 0.6, "TROCA DE QUÍMICOS", 0 , 1, 'R');
	
	$periodo2= explode("/", $_POST["periodo"]);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ." de ". $periodo2[1], 0, 1, "R");

	$pdf->Ln();$pdf->Ln();
	
	if ($_POST["id_quimico"]!="") $str= " and   id_quimico= '". $_POST["id_quimico"] ."' ";
	
	$result= mysql_query("select * from op_suja_quimicos_trocas
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							and   DATE_FORMAT(data_troca, '%m/%Y') = '". $_POST["periodo"] ."'
							order by data_troca desc, hora_troca desc
							");
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(4, 0.6, "DATA DA TROCA", 1, 0, 'C', 1);
	$pdf->Cell(4, 0.6, "GALÃO/QUÍMICO", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.6, "LITROS", 1, 0, 'C', 1);
	$pdf->Cell(6, 0.6, "RESPONSÁVEL", 1, 1, 'C', 1);
	
	$pdf->SetFont('ARIALNARROW', '', 9);
	$pdf->SetFillColor(240,240,240);
	$i=0;
	
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $fill=1;
		else $fill= 0;
		
		$qtde_total += $rs->qtde;
		
		$pdf->Cell(4, 0.6, desformata_data($rs->data_troca) ." ". $rs->hora_troca, 1, 0, 'C', $fill);
		$pdf->Cell(4, 0.6, $rs->num_galao ." ". pega_quimico($rs->id_quimico), 1, 0, 'C', $fill);
		$pdf->Cell(3, 0.6, fnum($rs->qtde) ." litros", 1, 0, 'C', $fill);
		$pdf->Cell(6, 0.6, primeira_palavra(pega_funcionario($rs->id_funcionario)), 1, 1, 'C', $fill);
		
		$i++;
	}
	
	$pdf->Ln();$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
	$pdf->Cell(2.2, 0.6, "TOTAL:", 0, 0, 'L', 0);
	$pdf->SetFont('ARIALNARROW', '', 11);
	$pdf->Cell(3, 0.6, fnum($qtde_total) ." litros", 0, 1, 'L', 0);
	
	$pdf->AliasNbPages(); 
	$pdf->Output("quimico_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>