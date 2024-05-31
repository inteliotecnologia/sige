<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode("rh", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_paisagem_rh.php");
	
	$pdf=new PDF("L", "cm", "A4");
	$pdf->SetMargins(1.5, 1.5, 1);
	$pdf->SetAutoPageBreak(true, 2);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$i=0;
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1);
	
	if ($_POST["id_turno"]!="") $str.= "and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."' ";
	if ($_POST["id_departamento"]!="") $str.= "and   rh_carreiras.id_departamento = '". $_POST["id_departamento"] ."' ";
	
	$result = mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras, rh_turnos
										where rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										and   rh_carreiras.atual = '1'
										and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
										and   rh_funcionarios.status_funcionario <> '2'
										and   rh_funcionarios.status_funcionario <> '0'
										and   rh_carreiras.id_turno = rh_turnos.id_turno
										$str
										/* and   rh_carreiras.id_departamento < '9' */
										". $str ."
										order by rh_turnos.turno asc, pessoas.nome_rz asc
										") or die(mysql_error());
	$linhas= mysql_num_rows($result);
	
	if ($_POST["id_departamento"]!="") $subtitulo.= pega_departamento($_POST["id_departamento"]);
	if ($_POST["id_turno"]!="") $subtitulo.= " - ". pega_turno($_POST["id_turno"]);	
	
	$subtitulo .= " - ". $linhas ." FUNCIONÁRIOS";
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.75, "FUNCIONÁRIOS/TURNOS", 0, 1, 'R');
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, $subtitulo, 0, 1, 'R');
	$pdf->Ln();$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(1.2, 0.6, "MAT.", 1, 0, 'C', 1);
	$pdf->Cell(8, 0.6, "NOME", 1, 0, 'L', 1);
	$pdf->Cell(5, 0.6, "DEPTO/TURNO", 1, 0, 'L', 1);
	$pdf->Cell(3.5, 0.6, "SEMANA", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.6, "INTERVALO", 1, 0, 'C', 1);
	$pdf->Cell(3.5, 0.6, "FIM DE SEMANA", 1, 0, 'C', 1);
	$pdf->Cell(3, 0.6, "INTERVALO", 1, 1, 'C', 1);
		
	$pdf->SetFont('ARIALNARROW', '', 8);
	$pdf->SetFillColor(240,240,240);
	
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		$j= $i+1;
		
		if (($i%2)==0) $fill= 0;
		else $fill= 1;
		
		$result_semana= mysql_query("select * from rh_turnos_horarios
									where id_turno = '". $rs->id_turno ."'
									and   id_dia = '1'
									");
		$rs_semana= mysql_fetch_object($result_semana);
		
		$result_intervalo= mysql_query("select * from rh_turnos_intervalos
												where id_turno = '". $rs->id_turno ."'
												order by id_intervalo desc limit 1
												");
		$rs_intervalo= mysql_fetch_object($result_intervalo);
					
		// ----------------------------------
		
		$result_find= mysql_query("select * from rh_turnos_horarios
									where id_turno = '". $rs->id_turno ."'
									and   id_dia = '0'
									");
		$rs_find= mysql_fetch_object($result_find);
		
		$id_turno_posicao[$i]= $rs->id_turno;
		
		if (($i>0) && ($id_turno_posicao[$i]!=$id_turno_posicao[$i-1])) $pdf->LittleLn();
		
		$pdf->Cell(1.2, 0.6, $rs->num_func, 1, 0, 'C', $fill);
		$pdf->Cell(8, 0.6, $rs->nome_rz, 1, 0, 'L', $fill);
		$pdf->Cell(5, 0.6, pega_departamento($rs->id_departamento). "/". pega_turno($rs->id_turno), 1, 0, 'L', $fill);
		$pdf->Cell(3.5, 0.6, substr($rs_semana->entrada, 0, 5) ." -> ". substr($rs_semana->saida, 0, 5), 1, 0, 'C', $fill);
		$pdf->Cell(3, 0.6, pega_detalhes_intervalo($rs_intervalo->id_intervalo, 1, 0), 1, 0, 'C', $fill);
		$pdf->Cell(3.5, 0.6, substr($rs_find->entrada, 0, 5) ." -> ". substr($rs_find->saida, 0, 5), 1, 0, 'C', $fill);
		$pdf->Cell(3, 0.6, pega_detalhes_intervalo($rs_intervalo->id_intervalo, 0, 0), 1, 1, 'C', $fill);
					
		$i++;
	}
	
	$pdf->Ln();
	
	$pdf->AliasNbPages(); 
	$pdf->Output("funcionario_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>