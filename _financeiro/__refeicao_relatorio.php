<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode('u', $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 2, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$i=0;
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "CONTROLE DE REFEIÇÕES", 0, 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "ENTRE ". desformata_data($data1) ." E ". desformata_data($data2), 0, 1, 'R');
	
	$pdf->Ln();$pdf->Ln();
	
	$data1= $_GET["data1"];
	$data2= $_GET["data2"];
	
	if ($data1!="") $str .= " and   fi_refeicoes.data >= '$data1' ";
	if ($data2!="") $str .= " and   fi_refeicoes.data <= '$data2' ";

	/*if ($_GET["id_funcionario"]!="") $str .= " and   fi_refeicoes.id_funcionario= '". $_GET["id_funcionario"] ."' ";
	if ($_GET["id_departamento"]!="") $str .= " and   rh_carreiras.id_departamento= '". $_GET["id_departamento"] ."' ";
	*/
	
	if ($_GET["id_motivo"]!="") $str .= " and   fi_refeicoes.id_motivo= '". $_GET["id_motivo"] ."' ";
	if ($_GET["id_usuario_at"]!="") $str .= " and   fi_refeicoes.id_usuario_at= '". $_GET["id_usuario_at"] ."' ";
	
	if ($_GET["id_departamento"]!="") $str .= " and   fi_refeicoes.id_departamento= '". $_GET["id_departamento"] ."' ";
	if ($_GET["id_turno"]!="") $str .= " and   fi_refeicoes.id_turno= '". $_GET["id_turno"] ."' ";

	$result= mysql_query("select * from fi_refeicoes
									where fi_refeicoes.id_empresa = '". $_SESSION["id_empresa"] ."'
									". $str ."
									order by fi_refeicoes.data asc
									") or die(mysql_error());
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(2.5, 0.6, "DATA", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.6, "MOTIVO", 1, 0, 'L', 1);
	$pdf->Cell(5, 0.6, "DEPTO", 1, 0, 'L', 1);
	$pdf->Cell(4, 0.6, "TURNO", 1, 0, 'L', 1);
	$pdf->Cell(2.5, 0.6, "VALOR", 1, 1, 'R', 1);
		
	$pdf->SetFont('ARIALNARROW', '', 8);
	$pdf->SetFillColor(240,240,240);
	
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $fill= 0;
		else $fill= 1;
			
		$valor_total += $rs->valor_total;
		
		$pdf->Cell(2.5, 0.6, desformata_data($rs->data), 1, 0, 'C', $fill);
		$pdf->Cell(3, 0.6, pega_motivo($rs->id_motivo), 1, 0, 'L', $fill);
		$pdf->Cell(5, 0.6, pega_departamento($rs->id_departamento), 1, 0, 'L', $fill);
		$pdf->Cell(4, 0.6, pega_turno($rs->id_turno), 1, 0, 'L', $fill);
		$pdf->Cell(2.5, 0.6, "R$ ". fnum($rs->valor_total), 1, 1, 'R', $fill);
		
		$i++;
	}
	
	$fill= 0;
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(2.5, 0.6, "", 0, 0, 'L', $fill);
	$pdf->Cell(12, 0.6, "", 0, 0, 'L', $fill);
	$pdf->Cell(2.5, 0.6, "R$ ". fnum($valor_total), 1, 1, 'R', $fill);
	
	$pdf->Ln();
	
	$pdf->AliasNbPages(); 
	$pdf->Output("refeicao_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>