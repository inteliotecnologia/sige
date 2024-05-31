<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode_algum("mrhv4", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");

	$result_empresa= mysql_query("select * from  pessoas, rh_enderecos, empresas, cidades, ufs
									where empresas.id_empresa = '". $_SESSION["id_empresa_atendente2"] ."'
									and   empresas.id_pessoa = pessoas.id_pessoa
									and   pessoas.id_pessoa = rh_enderecos.id_pessoa
									and   rh_enderecos.id_cidade = cidades.id_cidade
									and   cidades.id_uf = ufs.id_uf
									") or die(mysql_error());
	$rs_empresa= mysql_fetch_object($result_empresa);
		
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(230,230,230);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$i=0;

	$result= mysql_query("select *, DATE_FORMAT(data_livro, '%Y') as ano from com_livro
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							and   id_livro = '". $_GET["id_livro"] ."'
							and   reclamacao_original = '1'
							") or die(mysql_error());
	$rs= mysql_fetch_object($result);
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, pega_motivo($rs->id_motivo), 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "Nº ". fnumi($rs->num_livro)."/".$rs->ano, 0 , 1, 'R');
	
	$pdf->Ln();$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(3, 0.6, "DATA/HORA:", 0 , 0);
	$pdf->SetFont('ARIALNARROW', '', 10);
	$pdf->Cell(0, 0.6, desformata_data($rs->data_livro) ." às ". $rs->hora_livro, 0 , 1);
	
	if (($rs->id_motivo==34) && ($rs->reclamacao_id_cliente!=0)) {
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(3, 0.6, "CLIENTE:", 0 , 0);
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(0, 0.6, pega_pessoa($rs->reclamacao_id_cliente), 0 , 1);
	}
	
	if ($rs->tipo_de=="f") {
		if (($rs->id_outro_departamento!="") && ($rs->id_outro_departamento!="0")) $id_departamento= $rs->id_outro_departamento;	
		else $id_departamento= pega_dado_carreira("id_departamento", $rs->de);	
	}
	else {
		$id_departamento= $rs->de;
		$id_deixou= $rs->de;
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(3, 0.6, "RECLAMANTE:", 0 , 0);
	$pdf->SetFont('ARIALNARROW', '', 10);
	$pdf->Cell(0, 0.6, pega_funcionario($rs->de), 0 , 1);
	
	if ($rs->id_departamento_principal!="") {
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(3, 0.6, "DPTO. REFERENTE:", 0 , 0);
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(0, 0.6, pega_departamento($rs->id_departamento_principal), 0 , 1);
		$pdf->Ln();
	}
	
	$data= desformata_data($rs->data);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->MultiCell(0, 0.6, "DESCRIÇÃO:", 0 , 1);
	$pdf->SetFont('ARIALNARROW', '', 10);
	$pdf->MultiCell(0, 0.5, strip_tags(html_entity_decode($rs->mensagem)),"", 1);
	$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->MultiCell(0, 0.6, "OBSERVAÇÕES:", 0 , 1);
	$pdf->Cell(0, 0.65, "", "B", 1);
	$pdf->Cell(0, 0.65, "", "B", 1);
	$pdf->Cell(0, 0.65, "", "B", 1);
	$pdf->Cell(0, 0.65, "", "B", 1);
	$pdf->Cell(0, 0.65, "", "B", 1);
	$pdf->Cell(0, 0.65, "", "B", 1);
	$pdf->Cell(0, 0.65, "", "B", 1);
	$pdf->Cell(0, 0.65, "", "B", 1);
	$pdf->Ln();
	
	$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
	
	$pdf->SetFont('ARIALNARROW', '', 9);
	$pdf->Cell(7, 0.75, "", '', 0);
	$pdf->Cell(3, 0.75, "", 0, 0);
	$pdf->Cell(7, 0.75, "ASSINATURA", 'T', 1, 'C');
	
	$pdf->AliasNbPages();
	$pdf->Output("documento_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>	
