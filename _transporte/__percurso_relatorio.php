<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("ey", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	if ($_POST["periodo"]!="") {
		$periodo2= explode('/', $_POST["periodo"]);
			
		$data1_mk= mktime(14, 0, 0, $periodo2[0], 1, $periodo2[1]);
		
		$dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
		$linha2= traduz_mes($periodo2[0]) ."/". $periodo2[1];
	}
	else {
		$data1= formata_data($_POST["data_percurso"]);
		$data2= formata_data($_POST["data_percurso"]);
		
		$data1f= $_POST["data_percurso"];
		$data2f= $_POST["data_percurso"];
		
		// 10/10/2010
		
		$data1_mk= mktime(14, 0, 0, substr($_POST["data_percurso"], 3, 2), substr($_POST["data_percurso"], 0, 2), substr($_POST["data_percurso"], 6, 4));
		$data2_mk= mktime(14, 0, 0, substr($_POST["data_percurso"], 3, 2), substr($_POST["data_percurso"], 0, 2), substr($_POST["data_percurso"], 6, 4));
		
		$linha2= $_POST["data_percurso"];
	}
		
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
	$pdf->Cell(0, 0.6, "CONTROLE DE PERCURSOS (". strtoupper(pega_tipo_detalhamento_relatorio($_POST["tipo_relatorio"])) .")", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, $linha2, 0 , 1, 'R');
	
	$pdf->Ln();
	
	if ($_POST["tipo_relatorio"]==1) {
		
		if ($_POST["id_veiculo"]!="") $str_veiculo= "and   id_veiculo = '". $_POST["id_veiculo"] ."' ";
		
		$result_vei= mysql_query("select * from op_veiculos
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									". $str_veiculo ."
									and   status_veiculo = '1'
									order by veiculo asc,
									placa asc
									") or die(mysql_error());
		$i=0;
		while ($rs_vei = mysql_fetch_object($result_vei)) {
			
			$total_km_mes= 0;
			$total_abastecido= 0;
			
			if ($i>0) $pdf->AddPage();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.7, $rs_vei->veiculo ." ". $rs_vei->placa, "B", 1, "L", 0);
			$pdf->Cell(0, 0.3, "", 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(2.5, 0.6, "DATA", 1, 0, "L", 1);
			$pdf->Cell(3.5, 0.6, "KM INICIAL", 1, 0, "L", 1);
			$pdf->Cell(3.5, 0.6, "KM FINAL", 1, 0, "L", 1);
			$pdf->Cell(3.5, 0.6, "KM DIA", 1, 0, "L", 1);
			$pdf->Cell(4, 0.6, "ABASTECIMENTO", 1, 1, "L", 1);
			
			$diferenca= ceil(($data2_mk-$data1_mk)/86400);
			
			for ($d=0; $d<=$diferenca; $d++) {
				$calculo_data= $data1_mk+(86400*$d);
				$data= date("d/m/Y", $calculo_data);
				$data_valida= date("Y-m-d", $calculo_data);
				$id_dia= date("w", $calculo_data);
				
				$result_ini= mysql_query("select * from tr_percursos, tr_percursos_passos
											where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   tr_percursos.id_veiculo = '". $rs_vei->id_veiculo ."'
											and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
											and   tr_percursos_passos.data_percurso = '". $data_valida ."'
											and   tr_percursos_passos.passo = '1'
											order by hora_percurso asc limit 1
											") or die(mysql_error());
				$rs_ini= mysql_fetch_object($result_ini);
				
				$result_fin= mysql_query("select * from tr_percursos, tr_percursos_passos
											where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   tr_percursos.id_veiculo = '". $rs_vei->id_veiculo ."'
											and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
											and   tr_percursos_passos.data_percurso = '". $data_valida ."'
											and   tr_percursos_passos.passo = '3'
											order by hora_percurso desc limit 1
											") or die(mysql_error());
				$rs_fin= mysql_fetch_object($result_fin);
				
				$result_aba= mysql_query("select sum(valor_total) as valor_total from fi_abastecimentos
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_veiculo = '". $rs_vei->id_veiculo ."'
										and   data = '". $data_valida ."'
										") or die(mysql_error());
				$rs_aba= mysql_fetch_object($result_aba);
				
				if (($d%2)==0) $fill= 0;
				else $fill= 1;
				
				$pdf->SetFont('ARIALNARROW', '', 9);
				$pdf->SetFillColor(235,235,235);
				
				$km_dia= ($rs_fin->km-$rs_ini->km);
				$total_km_mes+= $km_dia;
				
				$total_abastecido+= $rs_aba->valor_total;
				
				$pdf->Cell(2.5, 0.6, $data, 1, 0, "L", $fill);
				$pdf->Cell(3.5, 0.6, fnumf($rs_ini->km) ." km", 1, 0, "L", $fill);
				$pdf->Cell(3.5, 0.6, fnumf($rs_fin->km) ." km", 1, 0, "L", $fill);
				$pdf->Cell(3.5, 0.6, fnumf($km_dia) ." km", 1, 0, "L", $fill);
				$pdf->Cell(4, 0.6, "R$ ". fnum($rs_aba->valor_total), 1, 0, "L", $fill);
				
				$pdf->Ln();
			}
			
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(2.5, 0.6, "", 0, 0, "L", 0);
			$pdf->Cell(3.5, 0.6, "", 0, 0, "L", 0);
			$pdf->Cell(3.5, 0.6, "", 0, 0, "L", 0);
			$pdf->Cell(3.5, 0.6, fnumf($total_km_mes) ." km", 1, 0, "L", 1);
			$pdf->Cell(4, 0.6, "R$ ". fnum($total_abastecido), 1, 0, "L", 1);
			
			$i++;
			
		}//fim veiculos
	}//fim sintético
	
	if ($_POST["tipo_relatorio"]==2) {
		if ($_POST["id_veiculo"]!="") $str_veiculo= "and   id_veiculo = '". $_POST["id_veiculo"] ."' ";
		
		$result_vei= mysql_query("select * from op_veiculos
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									". $str_veiculo ."
									order by veiculo asc,
									placa asc
									") or die(mysql_error());
		
		$i=0;
		while ($rs_vei = mysql_fetch_object($result_vei)) {
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			
			$pdf->Cell(0, 0.7, $rs_vei->veiculo ." ". $rs_vei->placa, "B", 1, "L", 0);
			$pdf->Cell(0, 0.3, "", 0, 1);
			
			$diferenca = ceil(($data2_mk-$data1_mk)/86400);
			
			for ($d=0; $d<=$diferenca; $d++) {
				$calculo_data= $data1_mk+(86400*$d);
				$data= date("d/m/Y", $calculo_data);
				$data_valida= date("Y-m-d", $calculo_data);
				$id_dia= date("w", $calculo_data);
				
				$result_perc= mysql_query("select * from tr_percursos, tr_percursos_passos
											where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   tr_percursos.id_veiculo = '". $rs_vei->id_veiculo ."'
											and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
											and   tr_percursos_passos.data_percurso = '". $data_valida ."'
											order by tr_percursos.id_percurso asc, passo asc, hora_percurso asc
											") or die(mysql_error());
				$linhas_perc= mysql_num_rows($result_perc);
				
				if ($linhas_perc>0) {
					
					$pdf->SetFillColor(210,210,210);
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					
					$pdf->Cell(0, 0.6, $data, 0, 1, "L", 0);
					$pdf->Cell(0, 0.2, "", 0, 1);
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
					
					$pdf->Cell(1.8, 0.6, "TIPO", 1, 0, "L", 1);
					$pdf->Cell(2.7, 0.6, "AÇÃO", 1, 0, "L", 1);
					$pdf->Cell(2.5, 0.6, "DATA/HORA", 1, 0, "L", 1);
					$pdf->Cell(2, 0.6, "CLIENTE", 1, 0, "L", 1);
					$pdf->Cell(3, 0.6, "ASS. POR", 1, 0, "L", 1);
					$pdf->Cell(2, 0.6, "KM", 1, 0, "L", 1);
					$pdf->Cell(3, 0.6, "MOTORISTA", 1, 1, "L", 1);
					
					$m=0;
					while ($rs_perc= mysql_fetch_object($result_perc)) {
						
						if (($m%2)==0) $fill= 0;
						else $fill= 1;
						
						$pdf->SetFont('ARIALNARROW', '', 8);
						$pdf->SetFillColor(235,235,235);
						
						if ($rs_perc->passo==2) {
							$cliente= pega_sigla_pessoa($rs_perc->id_cliente);
							$ass= pega_nome_ad($rs_perc->id_ad);
						}
						else {
							$cliente= "";
							$ass= "";
						}
						
						$pdf->Cell(1.8, 0.6, pega_coleta_entrega($rs_perc->tipo), 1, 0, "L", $fill);
						$pdf->Cell(2.7, 0.6, pega_passo_percurso($rs_perc->passo), 1, 0, "L", $fill);
						$pdf->Cell(2.5, 0.6, desformata_data($rs_perc->data_percurso) ." ". substr($rs_perc->hora_percurso, 0, 5), 1, 0, "L", $fill);
						$pdf->Cell(2, 0.6, $cliente, 1, 0, "L", $fill);
						$pdf->Cell(3, 0.6, $ass, 1, 0, "L", $fill);
						$pdf->Cell(2, 0.6, fnumf($rs_perc->km) ." km", 1, 0, "L", $fill);
						$pdf->Cell(3, 0.6, primeira_palavra(pega_funcionario($rs_perc->id_motorista)), 1, 1, "L", $fill);
						
						$m++;
					}
					
					$pdf->Cell(0, 0.3, "", 0, 1);
				}
				
			}//fim for
			
		}//fim veiculo
		
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("percurso_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>