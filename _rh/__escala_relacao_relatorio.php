<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode("rhw", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato_rh.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 2.5);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$i=0;

	$result= mysql_query("select * from  rh_departamentos
							where 1=1
							and   rh_departamentos.id_departamento =  '". $_POST["id_departamento"] ."'
							order by rh_departamentos.departamento asc
							") or die(mysql_error());

	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		
		$pdf->AddPage();
		
		$pdf->SetXY(7,1.5);
		
		$result_fun= mysql_query("select * from rh_funcionarios, pessoas, rh_carreiras, rh_escala
								 	where pessoas.id_pessoa = rh_funcionarios.id_pessoa
									and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
									and   rh_carreiras.atual = '1'
									and   rh_carreiras.id_departamento = '". $_POST["id_departamento"] ."'
									and   rh_escala.id_funcionario = rh_carreiras.id_funcionario
									and   rh_escala.data_escala = '". formata_data($_POST["data"]) ."'
									and   rh_escala.trabalha = '1'
									order by rh_carreiras.id_turno asc, pessoas.nome_rz asc
									");
		$linhas_fun= mysql_num_rows($result_fun);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.75, "RELAÇÃO DE ESCALA - ". pega_departamento($rs->id_departamento) ." (". $_POST["data"] .")", 0, 1, 'R');
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.55, $linhas_fun ." FUNCIONÁRIOS", 0, 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(1.5, 0.55, "MAT.", 1, 0, 'C', 1);
		$pdf->Cell(10.5, 0.55, "NOME", 1, 0, 'L', 1);
		$pdf->Cell(5, 0.55, "HORÁRIO", 1, 1, 'C', 1);
			
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(230,230,230);
		
		$data_mk= faz_mk_data($_POST["data"]);
		$id_dia= date("w", $data_mk);
		
		$i= 0;
		while ($rs_fun= mysql_fetch_object($result_fun)) {
			
			if (($i%2)==0) $fill= 0;
			else $fill= 1;
			
			$result_dia= mysql_query("select * from rh_turnos_horarios
										where id_turno = '". $rs_fun->id_turno ."'
										and   id_dia = '$id_dia'
										");
			
			$rs_dia= mysql_fetch_object($result_dia);
			
			$horario= $rs_dia->entrada ." (". pega_detalhes_intervalo($rs_fun->id_intervalo, $id_dia, 0) .") ". $rs_dia->saida;
			
			$pdf->Cell(1.5, 0.55, $rs_fun->num_func, 1, 0, 'C', $fill);
			$pdf->Cell(10.5, 0.55, $rs_fun->nome_rz, 1, 0, 'L', $fill);
			$pdf->Cell(5, 0.55, $horario, 1, 1, 'C', $fill);
						
			$i++;
		}
	}
	
	$pdf->Ln();
	
	$pdf->AliasNbPages(); 
	$pdf->Output("escala_relacao_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>