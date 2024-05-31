<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("iq", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	if ($_POST["id_centro_custo"]!="") $str .= " and   fi_centro_custos.id_centro_custo =  '". $_POST["id_centro_custo"] ."'";
	
	$periodo= explode("/", $_POST["periodo"]);
	$periodo_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
	$total_dias_mes= date("t", $periodo_mk);
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(235,235,235);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	//$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "DISTRIBUIÇÃO DE CUSTOS", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, traduz_mes($periodo[0]) ." de ". $periodo[1], 0 , 1, 'R');
	
	$pdf->Ln();$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	
	$result_cc= mysql_query("select * from fi_centro_custos
									where fi_centro_custos.id_empresa= '". $_SESSION["id_empresa"] ."'
									$str
									order by fi_centro_custos.centro_custo asc
									") or die(mysql_error());
	//$total_cc= mysql_num_rows($result_cc);
	
	while ($rs_cc= mysql_fetch_object($result_cc)) {
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.8, $rs_cc->centro_custo, 0, 1, "L");
		
		$result_cct= mysql_query("select *
                                        from  fi_centro_custos_tipos, fi_cc_ct
                                        where fi_centro_custos_tipos.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   fi_centro_custos_tipos.id_centro_custo_tipo = fi_cc_ct.id_centro_custo_tipo
										and   fi_cc_ct.id_centro_custo = '". $rs_cc->id_centro_custo ."'
                                        order by fi_centro_custos_tipos.centro_custo_tipo asc
                                        ") or die(mysql_error());
		
	    $i=0;
		while ($rs_cct= mysql_fetch_object($result_cct)) {
        	
			if (($i%2)==0) $fill=1;
			else $fill=0;
			
			$result_custos= mysql_query("select sum(valor) as valor_mes
											from  fi_custos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_centro_custo = '". $rs_cc->id_centro_custo ."'
											and   id_centro_custo_tipo = '". $rs_cct->id_centro_custo_tipo ."'
											and   DATE_FORMAT(data, '%m/%Y') = '". $_POST["periodo"] ."'
										");
			$rs_custos= mysql_fetch_object($result_custos);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->Cell(12, 0.6, $rs_cct->centro_custo_tipo, 1, 0, "L", $fill);
			$pdf->Cell(5, 0.6, "R$ ". fnum($rs_custos->valor_mes), 1, 1, "R", $fill);
			
			$i++;
		}
		
		$pdf->Ln();
	}
	
	/*
	$largura=12/$total_turnos;
	
	while ($rs_turnos= mysql_fetch_object($result_turnos))
		$pdf->Cell($largura, 0.6, $rs_turnos->turno, 1, 0, "C", 1);
	
	$pdf->Cell(3, 0.6, "EQUIPE", 1, 1, "C", 1);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 6);
	
	$pdf->Cell(2, 0.6, "", 1, 0, "L", 0);
	
	for ($k=-1; $k<4; $k++) {
		if ($k==3) $quebra= 1;
		else $quebra= 0;
		
		$pdf->Cell(1.5, 0.6, "peso turno (kg)", 1, 0, "C", 0);
		$pdf->Cell(1.5, 0.6, "média individual", 1, $quebra, "C", 0);
	}
	
	$pdf->SetFillColor(240, 240, 240);
	
	for ($i=1; $i<=$total_dias_mes; $i++) {
		$hoje_mk= mktime(0, 0, 0, $periodo[0], $i, $periodo[1]);
		$id_dia= date("w", $hoje_mk);
		
		if (($i%2)==0) $fill=0;
		else $fill= 1;
		
		if (($id_dia==0) || ($id_dia==6))
			$pdf->Cell(0, 0.2, "", 0, 1, 0, 0);
		else {
			$data_formatada= date("d/m/Y", $hoje_mk);
			$data= date("Y-m-d", $hoje_mk);
			$data_mesmo= date("Y-m-d", $hoje_mk);
			
			$total_dia=0;
			$media_dia=0;
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			
			$pdf->Cell(2, 0.6, $data_formatada, 1, 0, "L", $fill);
			
			$result_turnos= mysql_query("select * from rh_turnos, rh_departamentos
											where rh_turnos.id_departamento = rh_departamentos.id_departamento
											and   rh_departamentos.id_empresa= '". $_SESSION["id_empresa"] ."'
											and   rh_turnos.id_departamento = '1'
											and   rh_turnos.fixo = '1'
											order by rh_turnos.ordem asc
											") or die(mysql_error());
			$total_turnos= mysql_num_rows($result_turnos);
			
			$largura=(12/$total_turnos)/2;
			
			$j=1;
			while ($rs_turnos= mysql_fetch_object($result_turnos)) {
				
				$horario= pega_horarios_turno($rs_turnos->id_turno, $id_dia);
				
				if (tem_hl_turno($rs_turnos->id_turno, $id_dia)) $data= soma_data($data, 1, 0, 0);
				
				if ($horario[1]=="00:00:00") $horario[1]= "23:59:59";
				
				$str= " and   hora_pesagem >= '". $horario[0] ."'
						and   hora_pesagem < '". $horario[1] ."'
						";
				
				/*if ($data_mesmo=="2009-01-12")
					echo " <br />
<br />
".$rs_turnos->turno ."<br />
<br />
select sum(peso) as soma, avg(qtde_funcionarios) as media from op_limpa_pesagem
											where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
											and   data_pesagem = '". $data ."'
											". $str ."
											order by data_pesagem desc, hora_pesagem desc
											";
				$result_soma= mysql_query("select sum(peso) as soma, avg(qtde_funcionarios) as media from op_limpa_pesagem
											where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
											and   data_pesagem = '". $data ."'
											". $str ."
											order by data_pesagem desc, hora_pesagem desc
											");
				$rs_soma= mysql_fetch_object($result_soma);
				
				/*$result_soma= mysql_query("select avg(qtde_funcionarios) as media from op_limpa_pesagem
											where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
											and   data_pesagem = '". $data ."'
											". $str ."
											order by data_pesagem desc, hora_pesagem desc
											");
				$rs_soma= mysql_fetch_object($result_soma);
				
				$total_dia+= $rs_soma->soma;
				$total_turno[$j]+= $rs_soma->soma;
				
				if ($rs_soma->media!=0) {
					$media_dia+= ($rs_soma->soma/$rs_soma->media);
					$media_turno[$j]+= ($rs_soma->soma/$rs_soma->media);
				}
				
				$pdf->Cell($largura, 0.6, fnum($rs_soma->soma), 1, 0, "C", $fill);
				$pdf->Cell($largura, 0.6, fnum($media_dia), 1, 0, "C", $fill);
				
				$j++;
			}
			
			$total_mes+= $total_dia;
			$media_mes+= $media_dia/$total_turnos;
			
			$pdf->Cell(1.5, 0.6, fnum($total_dia), 1, 0, "C", $fill);
			$pdf->Cell(1.5, 0.6, fnum($media_dia/$total_turnos), 1, 1, "C", $fill);
		}//fim else
	}
	
	$pdf->SetFillColor(210,210,210);
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(2, 0.6, "TOTAL", 1, 0, "L", 1);
	
	$pdf->SetFont('ARIALNARROW', '', 9);
	
	$j=1;
	$result_turnos= mysql_query("select * from rh_turnos, rh_departamentos
									where rh_turnos.id_departamento = rh_departamentos.id_departamento
									and   rh_departamentos.id_empresa= '". $_SESSION["id_empresa"] ."'
									and   rh_turnos.id_departamento = '1'
									and   rh_turnos.fixo = '1'
									order by rh_turnos.ordem asc
									") or die(mysql_error());
	$total_turnos= mysql_num_rows($result_turnos);
	
	$largura=(12/$total_turnos)/2;
	
	while ($rs_turnos= mysql_fetch_object($result_turnos)) {
		$pdf->Cell($largura, 0.6, fnum($total_turno[$j]) ." kg", 1, 0, "C", 1);
		$pdf->Cell($largura, 0.6, fnum($media_turno[$j]) ." kg", 1, 0, "C", 1);
		$j++;
	}
	
	$pdf->Cell(1.5, 0.6, fnum($total_mes) ." kg", 1, 0, "C", 1);
	$pdf->Cell(1.5, 0.6, fnum($media_mes/$total_dias_mes) ." kg", 1, 1, "C", 1);
	
	*/
	
	$pdf->AliasNbPages(); 
	$pdf->Output("cc_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>