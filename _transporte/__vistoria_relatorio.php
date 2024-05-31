<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("ey", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(235,235,235);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "VISTORIA DE VEÍCULOS", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
	
	$pdf->Ln();
	
	$result= mysql_query("select * from tr_vistorias, op_veiculos
							where tr_vistorias.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   tr_vistorias.id_vistoria = '". $_GET["id_vistoria"] ."'
							and   tr_vistorias.id_veiculo = op_veiculos.id_veiculo
							");
						
	$rs= mysql_fetch_object($result);
	
	$pdf->Cell(0, 0.6, "DADOS DO VEÍCULO:", 'B', 1);
	$pdf->LittleLn();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(3, 0.6, "DATA/HORA:", 1, 0, 1, 1);
	$pdf->SetFont('ARIALNARROW', '', 10);
	$pdf->Cell(0, 0.6, desformata_data($rs->data_vistoria) ." ". $rs->hora_vistoria, 1, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(3, 0.6, "MODELO:", 1, 0, 1, 1);
	$pdf->SetFont('ARIALNARROW', '', 10);
	$pdf->Cell(0, 0.6, $rs->veiculo, 1, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(3, 0.6, "PLACA:", 1, 0, 1, 1);
	$pdf->SetFont('ARIALNARROW', '', 10);
	$pdf->Cell(0, 0.6, $rs->placa, 1, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(3, 0.6, "CHASSI:", 1, 0, 1, 1);
	$pdf->SetFont('ARIALNARROW', '', 10);
	$pdf->Cell(0, 0.6, $rs->chassi, 1, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(3, 0.6, "CÓD. COR:", 1, 0, 1, 1);
	$pdf->SetFont('ARIALNARROW', '', 10);
	$pdf->Cell(0, 0.6, $rs->cod_cor, 1, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(3, 0.6, "MOTOR:", 1, 0, 1, 1);
	$pdf->SetFont('ARIALNARROW', '', 10);
	$pdf->Cell(0, 0.6, $rs->motor, 1, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(3, 0.6, "PESO BRUTO:", 1, 0, 1, 1);
	$pdf->SetFont('ARIALNARROW', '', 10);
	$pdf->Cell(0, 0.6, fnum($rs->peso_bruto) ." kg", 1, 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(3, 0.6, "ENTRE EIXOS:", 1, 0, 1, 1);
	$pdf->SetFont('ARIALNARROW', '', 10);
	$pdf->Cell(0, 0.6, $rs->entre_eixos, 1, 1);
	
	$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "CHECKLIST:", 'B', 1);
	$pdf->LittleLn();
	
	$result_itens= mysql_query("select * from tr_vistorias_itens
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								order by ordem asc
								");
	
	$i=1;
	
	while ($rs_itens= mysql_fetch_object($result_itens)) {
		$result_che= mysql_query("select * from tr_vistorias_itens_checklist
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_vistoria = '". $rs->id_vistoria ."'
									and   id_item = '". $rs_itens->id_item ."'
									");
		$rs_che= mysql_fetch_object($result_che);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(4.2, 0.6, strtoupper($rs_itens->item) .":", 1, 0, 1, 1);
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(0, 0.6, $rs_che->valor, 1, 1);
		
	}
	
	$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "OBSERVAÇÕS:", 'B', 1);
	$pdf->LittleLn();
	
	$pdf->SetFont('ARIALNARROW', '', 10);
	$pdf->MultiCell(0, 0.6, strip_tags($rs->obs), 0, 1);
	$pdf->Ln();$pdf->Ln();$pdf->Ln();
	
	$pdf->SetFont('ARIALNARROW', '', 9);
	$pdf->Cell(7, 0.75, "DIREÇÃO", 'T', 0);
	$pdf->Cell(3, 0.75, "", 0, 0);
	$pdf->Cell(7, 0.75, "RESPONSÁVEL PELA REVISÃO", 'T', 1, 'R');
	
	
	$pdf->AliasNbPages(); 
	$pdf->Output("vistoria_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>