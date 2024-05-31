<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode("r", $_SESSION["permissao"])) {
	
	if ($_POST["id_treinamento"]!="") $id_treinamento= $_POST["id_treinamento"];
	if ($_GET["id_treinamento"]!="") $id_treinamento= $_GET["id_treinamento"];
	
	if ($_POST["tipo_relatorio"]!="") $tipo_relatorio= $_POST["tipo_relatorio"];
	if ($_GET["tipo_relatorio"]!="") $tipo_relatorio= $_GET["tipo_relatorio"];
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato_rh.php");
		
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
	
	//funcionários por treinamento
	if ($tipo_relatorio=="1") {
		
		$result= mysql_query("select * from rh_treinamentos
								where rh_treinamentos.id_treinamento = '". $id_treinamento ."'
								") or die(mysql_error());
		
		$rs= mysql_fetch_object($result);
		
		$tipo_treinamento= $rs->tipo_treinamento;
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "TREINAMENTOS ". strtoupper(pega_tipo_treinamento($tipo_treinamento)) ."S", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, "LISTAGEM DE PARTICIPANTES", 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(3, 0.6, "TREINAMENTO:", 0 , 0);
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(6, 0.6, $rs->treinamento, 0, 0);
		
		if ($rs->monitor!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "MONITOR:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->monitor, 0 , 1);
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(3, 0.6, "DATA:", 0 , 0);
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(6, 0.6, desformata_data($rs->data_treinamento), 0 , 0);
		
		if ($rs->instituicao!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "INSTITUIÇÃO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->instituicao, 0 , 1);
		}
		
		if ($rs->carga_horaria!="0") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CARGA HORÁRIA:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->carga_horaria ." horas", 0 , 1);
		}
		
		$pdf->Ln();$pdf->Ln();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(0, 0.6, "PARTICIPANTES:", "B", 1);
		$pdf->LittleLn();
		
		if ($tipo_treinamento==1) {
		
			$pdf->Cell(12, 0.6, "NOME", 1, 0, "L", 1);
			$pdf->Cell(5, 0.6, "DEPARTAMENTO", 1, 1, "L", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->SetFillColor(235,235,235);
			
			$result_participantes= mysql_query("select rh_treinamentos_funcionarios.*, pessoas.nome_rz, rh_carreiras.id_departamento
												from rh_treinamentos_funcionarios, rh_funcionarios, rh_carreiras, pessoas
												where rh_treinamentos_funcionarios.id_treinamento = '". $rs->id_treinamento ."'
												and   rh_treinamentos_funcionarios.id_funcionario = rh_funcionarios.id_funcionario
												and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
												and   rh_carreiras.atual = '1'
												and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
												order by pessoas.nome_rz asc
												") or die(mysql_error());
			$i=0;
			while ($rs_participantes= mysql_fetch_object($result_participantes)) {
				if (($i%2)==0) $fill=0;
				else $fill=1;
				
				$pdf->Cell(12, 0.6, $rs_participantes->nome_rz, 1, 0, "L", $fill);
				$pdf->Cell(5, 0.6, string_maior_que(pega_departamento($rs_participantes->id_departamento), 32), 1, 1, "L", $fill);
				
				$i++;
			}
		
		}
		else {
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->SetFillColor(235,235,235);
			
			$pdf->MultiCell(12, 0.5, strip_tags($rs->participantes), 0, "L", 0);
		}
		
		
	}
	//treinamentos por pessoa
	else {
		
	
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "TREINAMENTOS", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, pega_funcionario($_POST["id_funcionario"]), 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$result= mysql_query("select * from rh_treinamentos, rh_treinamentos_funcionarios
								where rh_treinamentos.id_treinamento = rh_treinamentos_funcionarios.id_treinamento
								and   rh_treinamentos_funcionarios.id_funcionario = '". $_POST["id_funcionario"] ."'
								order by rh_treinamentos.data_treinamento asc
								") or die(mysql_error());
		
		while ($rs= mysql_fetch_object($result)) {
		
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			$pdf->Cell(3, 0.6, "TREINAMENTO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(6, 0.6, $rs->treinamento, 0, 0);
			
			if ($rs->monitor!="") {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				$pdf->Cell(3, 0.6, "MONITOR:", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 9);
				$pdf->Cell(0, 0.6, $rs->monitor, 0 , 1);
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			$pdf->Cell(3, 0.6, "DATA:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(6, 0.6, desformata_data($rs->data_treinamento), 0 , 0);
			
			if ($rs->instituicao!="") {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				$pdf->Cell(3, 0.6, "INSTITUIÇÃO:", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 9);
				$pdf->Cell(0, 0.6, $rs->instituicao, 0 , 1);
			}
			
			if ($rs->carga_horaria!="0") {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				$pdf->Cell(3, 0.6, "CARGA HORÁRIA:", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 9);
				$pdf->Cell(0, 0.6, $rs->carga_horaria ." horas", 0 , 1);
			}
			
			$pdf->LittleLn();
			$pdf->Cell(0, 0.6, "", "B", 1);
			
			$pdf->Ln();
		}
		
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("aniversariantes_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>