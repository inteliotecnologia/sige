<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode("rh", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	/*
	$periodo2= explode('/', $_POST["periodo"]);
		
	$data1_mk= mktime(14, 0, 0, $periodo2[0], 1, $periodo2[1]);
	
	$dias_mes= date("t", $data1_mk);
	$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
	
	$data1= date("Y-m-d", $data1_mk);
	$data2= date("Y-m-d", $data2_mk);
	
	$data1f= desformata_data($data1);
	$data2f= desformata_data($data2);
	*/
	
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
	$pdf->Cell(0, 0.6, "DEPARTAMENTOS/TURNOS", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
	
	$pdf->Ln();
		
	if ($_POST["id_departamento"]!="") $str_dep= "and   id_departamento = '". $_POST["id_departamento"] ."' ";
	
	$result_dep= mysql_query("select * from rh_departamentos
								where   id_empresa = '". $_SESSION["id_empresa"] ."'
								". $str_dep ."
								order by departamento asc ");
	
	while ($rs_dep= mysql_fetch_object($result_dep)) {
		
		if ($_POST["id_turno"]!="") $str_turno= "and   id_turno = '". $_POST["id_turno"] ."' ";
		
		$result_turno= mysql_query("select * from rh_turnos
									where id_departamento = '". $rs_dep->id_departamento ."'
									order by ordem asc
									") or die(mysql_error());
		
		if (mysql_num_rows($result_turno)>0) {
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.7, pega_departamento($rs_dep->id_departamento), 0, 1, "L", 0);
			$pdf->Cell(0, 0.3, "", 0, 1);
			
			while ($rs_turno= mysql_fetch_object($result_turno)) {
				
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(0, 0.7, "  ". $rs_turno->turno, "B", 1, "L", 0);
				$pdf->Cell(0, 0.3, "", 0, 1);
				
				$pdf->SetFillColor(200,200,200);
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				$pdf->Cell(4, 0.6, "DIA DA SEMANA", 1, 0, "C", 1);
				$pdf->Cell(3, 0.6, "ENTRADA", 1, 0, "C", 1);
				$pdf->Cell(5, 0.6, "INTERVALO", 1, 0, "C", 1);
				$pdf->Cell(3, 0.6, "SAÍDA", 1, 0, "C", 1);
				$pdf->Cell(2, 0.6, "HL", 1, 1, "C", 1);
				
				for ($i=0; $i<=6; $i++) {
					$pdf->SetFillColor(235,235,235);
					
					if (($i%2)==0) $fill=1;
					else $fill= 0;
					
					$result_dia= mysql_query("select * from rh_turnos_horarios
												where id_turno = '". $rs_turno->id_turno ."'
												and   id_dia = '$i'
												");
					$rs_dia= mysql_fetch_object($result_dia);
					
					$result_intervalo= mysql_query("select * from rh_turnos_intervalos
												   	where id_turno = '". $rs_turno->id_turno ."'
													order by id_intervalo desc limit 1
													");
					$rs_intervalo= mysql_fetch_object($result_intervalo);
					
					$pdf->SetFont('ARIALNARROW', '', 9);
					
					$pdf->Cell(4, 0.5, strtoupper(traduz_dia($i)), 1, 0, "C", $fill);
					$pdf->Cell(3, 0.5, substr($rs_dia->entrada, 0, 5), 1, 0, "C", $fill);
					$pdf->Cell(5, 0.5, pega_detalhes_intervalo($rs_intervalo->id_intervalo, $i, 0), 1, 0, "C", $fill);
					$pdf->Cell(3, 0.5, substr($rs_dia->saida, 0, 5), 1, 0, "C", $fill);
					$pdf->Cell(2, 0.5, sim_nao_pdf($rs_dia->hl), 1 , 1, "C", $fill);
					
				}
				
				$pdf->Ln();
			}
		}//fim linhas turnos
	}
	
	$pdf->Ln(); $pdf->Ln();
	
	$pdf->AliasNbPages(); 
	$pdf->Output("turno_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>