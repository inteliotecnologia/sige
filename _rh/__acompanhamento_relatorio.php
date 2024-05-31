<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode_algum("rhv4&", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_paisagem_rh.php");
	
	$periodo= explode('/', $_POST["periodo"]);
	
	$mes_extenso= traduz_mes($periodo[0]);
	$ano_aqui= $periodo[1];
	
	$data1_mk= mktime(14, 0, 0, $periodo[0], 1, $periodo[1]);
	
	$dias_mes= date("t", $data1_mk);
	$data2_mk= mktime(14, 0, 0, $periodo[0], $dias_mes, $periodo[1]);
	
	$data1= date("Y-m-d", $data1_mk);
	$data2= date("Y-m-d", $data2_mk);
	
	$data1f= desformata_data($data1);
	$data2f= desformata_data($data2);
	
	$pdf=new PDF("L", "cm", "A4");
	$pdf->SetMargins(1.5, 1.5, 1.5);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	/*if ($_POST["id_funcionario"]!="") $str= " and   rh_acompanhamento_atividades.id_funcionario = '". $_POST["id_funcionario"] ."' ";
	
	$result= mysql_query("select distinct(rh_acompanhamento_atividades.id_funcionario) from rh_acompanhamento_atividades, rh_funcionarios, pessoas
								where rh_acompanhamento_atividades.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   rh_acompanhamento_atividades.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
								and   DATE_FORMAT(rh_acompanhamento_atividades.data_acompanhamento, '%m/%Y') = '". $_POST["periodo"] ."'
								". $str ."
								order by pessoas.nome_rz asc
								") or die(mysql_error());
	$i=0;
	while ($rs= mysql_fetch_object($result)) {( */
		$pdf->AddPage();
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		
		$pdf->Cell(0, 0.6, "ACOMPANHAMENTO DE ATIVIDADES", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, strtoupper($mes_extenso ."/". $ano_aqui) .")", 0 , 1, "R");
		
		$pdf->Ln();$pdf->Ln();
		
		
		$z=1;
		$vetor= pega_acompanhamento_atividades('l');
		
		while ($vetor[$z]) {
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			
			$pdf->Cell(0, 0.6, $vetor[$z], 'B', 1);
			$pdf->Cell(0, 0.2, "", 0, 1);
			
			
			
			
			
			
			
			
			
			// ------------- tabela
			$pdf->SetFillColor(200,200,200);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->Cell(5.5, 1.2, "TURNO", 1 , 0, "L", 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$diferenca_tit= date("d", $data2_mk-$data1_mk);
			$total_dias_mes= date("t", $data1_mk);
			
			if ($total_dias_mes==28) $largura= 0.72;
			if ($total_dias_mes==29) $largura= 0.71;
			if ($total_dias_mes==30) $largura= 0.70;
			if ($total_dias_mes==31) $largura= 0.69;
			
			//repetir todos os dias do intervalo
			for ($t=0; $t<=$diferenca_tit; $t++) {
				$calculo_data_tit= $data1_mk+(86400*$t);
				
				$dia_tit= date("d", $calculo_data_tit);
				$dia_semana_tit= date("w", $calculo_data_tit);
				$vale_dia_tit= date("Y-m-d", $calculo_data_tit);
				
				$pdf->Cell($largura, 0.6, $dia_tit, 1 , 0, "C", 1);
				//echo traduz_dia_resumido($dia_semana_tit);
			}
			
			$y= $pdf->GetY();
			$pdf->SetXY(7, $y+0.6);
			
			
			//repetir todos os dias do intervalo
			for ($t=0; $t<=$diferenca_tit; $t++) {
				$calculo_data_tit= $data1_mk+(86400*$t);
				
				$dia_tit= date("d", $calculo_data_tit);
				$dia_semana_tit= date("w", $calculo_data_tit);
				$vale_dia_tit= date("Y-m-d", $calculo_data_tit);
				
				$pdf->Cell($largura, 0.6, traduz_dia_resumido($dia_semana_tit), 1, 0, "C", 1);
				//echo traduz_dia_resumido($dia_semana_tit);
			}
			
			$y= $pdf->GetY();
			$pdf->SetXY(1.5, $y+0.6);
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->SetFillColor(230,230,230);
			
			for ($p=1; $p<5; $p++) {
				if (($j%2)==0) {
					$fill=1;
					$pdf->SetFillColor(230,230,230);
				}
				else $fill= 0;
				
				$pdf->Cell(5.5, 0.6, pega_periodo_turno($p), 1 , 0, "L", $fill);
				
				$diferenca= date("d", $data2_mk-$data1_mk);
							
				//repetir todos os dias do intervalo
				for ($d=0; $d<=$diferenca; $d++) {
					$calculo_data= $data1_mk+(86400*$d);
					$dia_tit= date("d", $calculo_data);
					$dia_semana= date("w", $calculo_data);
					$vale_dia= date("Y-m-d", $calculo_data);
					$mes= date("m", $calculo_data);
					
					$result_esc= mysql_query("select * from rh_acompanhamento_atividades
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_acompanhamento = '$z'
                                                    and   data_acompanhamento = '". $vale_dia ."'
													and   periodo = '$p'
												") or die(mysql_error());
					$rs_esc= mysql_fetch_object($result_esc);
					
					if ($rs_esc->valor==1) $trabalha= "V";
					else $trabalha= "";
					
					if ($dia_tit==$total_dias_mes) $flutua=1;
					else $flutua= 0;
					
					$pdf->Cell($largura, 0.6, $trabalha, 1 , $flutua, "C", $fill);
					
					$i++;
				}
				$j++;
				
			}//fim for p
			
			
			$pdf->Cell(0, 0.2, "", 0, 1);
			
			
			
			
			
			
			
			
			
			
			
			
			$z++;
		}
	//}//fim while turnos
	
	$pdf->AliasNbPages(); 
	$pdf->Output("acompanhamento_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>