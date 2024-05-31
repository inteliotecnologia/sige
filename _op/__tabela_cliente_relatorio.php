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
	
	switch($_GET["status_pessoa"]) {
		case 1: $tit2= " ATIVOS"; break;
		case 0: $tit2= " INATIVOS"; break;
		case 3: $tit2= " EM VISTA"; break;
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "CLIENTES". $tit2, 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "", 0 , 1, 'R');

	$pdf->Ln();$pdf->Ln();
	
	$result= mysql_query("select * from pessoas, pessoas_tipos
								where pessoas.id_pessoa = pessoas_tipos.id_pessoa
								and   pessoas_tipos.tipo_pessoa = 'c'
								and   pessoas.status_pessoa = '". $_GET["status_pessoa"] ."'
								/* and   pessoas.id_cliente_tipo = '1' */
								order by pessoas.codigo asc
								") or die(mysql_error());
	
	$i=1;
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
	
	$pdf->Cell(2, 0.8, "CÓDIGO", 1, 0, "C", 1);
	$pdf->Cell(3, 0.8, "SIGLA", 1, 0, "C", 1);
	$pdf->Cell(12, 0.8, " CLIENTE", 1, 1, "L", 1);
	//$pdf->Cell(2.5, 0.8, "AT", 1, 1, "L", 1);
	
	$pdf->SetFillColor(240,240,240);
	
	while ($rs = mysql_fetch_object($result)) {
		
		if (($i%2)==0) $fill=1;
		else $fill= 0;
		
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(2, 0.8, $rs->codigo, 1, 0, "C", $fill);
		$pdf->Cell(3, 0.8, $rs->sigla, 1, 0, "C", $fill);
		$pdf->Cell(12, 0.8, " ". $rs->nome_rz, 1, 1, "L", $fill);
		//$pdf->Cell(2.5, 0.8, primeira_palavra(pega_empresa($rs->id_empresa_atendente)), 1, 1, "L", $fill);
		
		//if ($rs->id_empresa_atendente!=0) $id_empresa_atendente= $rs->id_empresa_atendente;
		//else $id_empresa_atendente= 1;
		
		//$x_aqui= $pdf->GetX(); $y_aqui= $pdf->GetY();
		//$pdf->Image("". CAMINHO ."empresa_". $id_empresa_atendente .".jpg", $x_aqui+0.05, $y_aqui+0.05, 2, 0.8);
		
		$i++;
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("tabela_cliente_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>