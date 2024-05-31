<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode("rm", $_SESSION["permissao"])) {
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
	$pdf->Cell(0, 0.6, "ANIVERSARIANTES - ". pega_tipo_pessoa($_POST["tipo_pessoa"]), 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, strtoupper(traduz_mes($_POST["periodo"])), 0 , 1, 'R');
	
	$pdf->Ln();$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$result= mysql_query("select *, DATE_FORMAT(tr_clientes_ad.data_nasc, '%d/%m') as data_aniversario
								from tr_clientes_ad, pessoas, pessoas_tipos
								where DATE_FORMAT(tr_clientes_ad.data_nasc, '%m') = '". formata_saida($_POST["periodo"], 2) ."'
								and   tr_clientes_ad.id_cliente = pessoas.id_pessoa
								and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
								and   pessoas_tipos.tipo_pessoa = '". $_POST["tipo_pessoa"] ."'
								and   tr_clientes_ad.situacao = '1'
								order by DATE_FORMAT(tr_clientes_ad.data_nasc, '%d') asc, nome asc
								") or die(mysql_error());
	
	$pdf->Cell(6.5, 0.7, "NOME", 1, 0, "L", 1);
	$pdf->Cell(3.5, 0.7, "CARGO", 1, 0, "L", 1);
	$pdf->Cell(3, 0.7, "SETOR", 1, 0, "L", 1);
	$pdf->Cell(3, 0.7, strtoupper(pega_tipo_pessoa($_POST["tipo_pessoa"])), 1, 0, "L", 1);
	$pdf->Cell(1, 0.7, "DATA", 1, 1, "L", 1);
	
	$pdf->SetFont('ARIALNARROW', '', 8);
	$pdf->SetFillColor(235,235,235);
	
	$j=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($j%2)==0) $fill= 0;
		else $fill= 1;
		
		$pdf->Cell(6.5, 0.7, $rs->nome, 1, 0, "L", $fill);
		$pdf->Cell(3.5, 0.7, $rs->cargo, 1, 0, "L", $fill);
		$pdf->Cell(3, 0.7, $rs->setor, 1, 0, "L", $fill);
		$pdf->Cell(3, 0.7, string_maior_que($rs->apelido_fantasia, 18), 1, 0, "L", $fill);
		$pdf->Cell(1, 0.7, $rs->data_aniversario, 1, 1, "L", $fill);
		
		$j++;
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("pessoa_aniversariantes_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>