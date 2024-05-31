<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("i12", $_SESSION["permissao"])) {
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
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "HISTÓRICO DE CLIENTE", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, pega_pessoa($_GET["id_cliente"]), 0 , 1, 'R');
	
	$pdf->Ln();$pdf->Ln();
	
	$result_historico= mysql_query("select * from com_livro
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   reclamacao_id_cliente = '". $_GET["id_cliente"] ."'
									order by data_livro desc, hora_livro desc
									") or die(mysql_error());
	$linhas_historico= mysql_num_rows($result_historico);
	
	
	
	$i=0;
	while ($rs_historico= mysql_fetch_object($result_historico)) {
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		$pdf->Cell(3, 0.5, desformata_data($rs_historico->data_livro) ." ". $rs_historico->hora_livro, 0, 0, 'C');
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->MultiCell(0, 0.5, strip_tags($rs_historico->mensagem), 0, 'L');
		
		$pdf->Cell(0, 0.2, "", 'B', 1, 'C');
		
		$pdf->Ln();
	}
	
	
	
	$pdf->AliasNbPages(); 
	$pdf->Output("cliente_historico_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>