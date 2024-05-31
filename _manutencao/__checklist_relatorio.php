<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode_algum("rhv", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_paisagem.php");
	
	$periodo= explode('/', $_POST["periodo"]);
	
	$mes_extenso= traduz_mes($periodo[0]);
	$ano_aqui= $periodo[1];
	
	$data1_mk= mktime(20, 0, 0, $periodo[0], 1, $periodo[1]);
	
	$dias_mes= date("t", $data1_mk);
	$data2_mk= mktime(13, 0, 0, $periodo[0], $dias_mes, $periodo[1]);
	
	$data1= date("Y-m-d", $data1_mk);
	$data2= date("Y-m-d", $data2_mk);
	
	$data1f= desformata_data($data1);
	$data2f= desformata_data($data2);
	
	$pdf=new PDF("L", "cm", "A4");
	$pdf->SetMargins(1.5, 0, 1.5);
	$pdf->SetAutoPageBreak(true, 1.75);
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
		
		$pdf->SetXY(16.9, 1.25);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		
		$pdf->Cell(0, 0.6, "CHECKLIST - MANUTENÇÃO", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, strtoupper($mes_extenso ."/". $ano_aqui) ."", 0 , 1, "R");
		
		$pdf->Cell(0, 0.2, "", 0, 1);
		
		$z=1;
		//$vetor= pega_manutencao_checklist_categorias('l');
		
		//while ($vetor[$z]) {
		
		$result= mysql_query("select * from op_equipamentos
					where id_empresa = '". $_SESSION["id_empresa"] ."' 
					". $str ."
					order by tipo_equipamento asc, equipamento asc
					");
					
				
			
	     $i=0;
	     while ($rs= mysql_fetch_object($result)) {
		
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->Cell(0, 0.6, pega_equipamento($rs->id_equipamento), 'B', 1);
			$pdf->Cell(0, 0.2, "", 0, 1);
			
				
			
			// ------------- tabela
			$pdf->SetFillColor(200,200,200);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->Cell(5.5, 0.9, "ATIVIDADES", 1 , 0, "L", 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
			
			$diferenca_tit= date("d", $data2_mk-$data1_mk);
			$total_dias_mes= date("t", $data1_mk);
			
			//echo $total_dias_mes; die();
			
			$largura= 20.2/$total_dias_mes;
			
			//repetir todos os dias do intervalo
			for ($t=0; $t<=$diferenca_tit; $t++) {
				$calculo_data_tit= $data1_mk+(86400*$t);
				
				$dia_tit= date("d", $calculo_data_tit);
				$dia_semana_tit= date("w", $calculo_data_tit);
				$vale_dia_tit= date("Y-m-d", $calculo_data_tit);
				
				$pdf->Cell($largura, 0.45, $dia_tit, 1 , 0, "C", 1);
				//echo traduz_dia_resumido($dia_semana_tit);
			}
			
			$y= $pdf->GetY();
			$pdf->SetXY(7, $y+0.45);
			
			
			//repetir todos os dias do intervalo
			for ($t=0; $t<=$diferenca_tit; $t++) {
				$calculo_data_tit= $data1_mk+(86400*$t);
				
				$dia_tit= date("d", $calculo_data_tit);
				$dia_semana_tit= date("w", $calculo_data_tit);
				$vale_dia_tit= date("Y-m-d", $calculo_data_tit);
				
				$pdf->Cell($largura, 0.45, traduz_dia_resumido($dia_semana_tit), 1, 0, "C", 1);
				//echo traduz_dia_resumido($dia_semana_tit);
			}
			
			$y= $pdf->GetY();
			$pdf->SetXY(1.5, $y+0.45);
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->SetFillColor(230,230,230);
			
			
			$result_manutencao_itens= mysql_query("select * from op_acompanhamento_itens
													  	where id_empresa = '". $_SESSION["id_empresa"] ."'
														");
				
			while ($rs_manutencao_itens= mysql_fetch_object($result_manutencao_itens)) {
			
				if (($j%2)==0) {
					$fill=1;
					$pdf->SetFillColor(230,230,230);
				}
				else $fill= 0;
				
				$pdf->SetFont('ARIALNARROW', '', 7);
				
				$pdf->Cell(5.5, 0.45, $rs_manutencao_itens->acompanhamento_item, 1 , 0, "L", $fill);
				
				$diferenca= date("d", $data2_mk-$data1_mk);
							
				//repetir todos os dias do intervalo
				for ($d=0; $d<=$diferenca; $d++) {
					$calculo_data= $data1_mk+(86400*$d);
					$dia_tit= date("d", $calculo_data);
					$dia_semana= date("w", $calculo_data);
					$vale_dia= date("Y-m-d", $calculo_data);
					$mes= date("m", $calculo_data);
					
					$inf_hoje= "";
					
					$result_ac= mysql_query("select * from man_checklist
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												/* and   id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."' */
												and   data_checklist = '". $vale_dia ."'
												and   id_checklist_item = '". $rs_manutencao_itens->id_acompanhamento_item ."'
												") or die(mysql_error());
					
					$pdf->SetFont('ARIALNARROW', '', 6);
					
					while ($rs_ac= mysql_fetch_object($result_ac)) {
						
						/*$funcionario_aqui= pega_funcionario($rs_ac->id_funcionario);
						$inf_hoje .= strtoupper($funcionario_aqui[0]) ." ";
						*/
						
						$inf_hoje .= pega_manutencao_num_tecnico($rs_ac->id_tecnico) ." ";
						
					}
					
					if ($dia_tit==$total_dias_mes) $flutua=1;
					else $flutua= 0;
					
					$pdf->SetFont('ARIALNARROW', '', 7);
					
					$pdf->Cell($largura, 0.45, $inf_hoje, 1 , 0, "C", $fill);
					
					$i++;
				}
				
				$pdf->Cell(1, 0.45, traduz_periodicidade($rs_manutencao_itens->periodicidade), 1, 1, "C", $fill);
				
				$j++;
				
			}//fim for p
			
			
			$pdf->Cell(0, 0.2, "", 0, 1);
			
			$z++;
		}
		
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		
		$pdf->Cell(7, 0.6, "TÉCNICOS", 'B', 1);
		$pdf->Cell(7, 0.2, "", 0, 1);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
		$pdf->SetFillColor(200,200,200);
		
		$pdf->Cell(1, 0.45, "NUM.", 1, 0, "L", 1);
		$pdf->Cell(6, 0.45, "TÉCNICO", 1, 1, "L", 1);
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		$result_tec= mysql_query("select *
									from  man_tecnicos
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									order by num_tecnico asc
									") or die(mysql_error());
		
		$pdf->SetFillColor(230,230,230);
		
		$i=0;
		while ($rs_tec= mysql_fetch_object($result_tec)) {
			
			if (($i%2)==0) $fill=1;
			else $fill= 0;
			
			if ($rs_tec->id_funcionario==0) $nome_tecnico= $rs_tec->nome_tecnico;
			else $nome_tecnico= pega_funcionario($rs_tec->id_funcionario);
			
			$pdf->Cell(1, 0.45, $rs_tec->num_tecnico, 1, 0, "L", $fill);
			$pdf->Cell(6, 0.45, strtoupper($nome_tecnico), 1, 1, "L", $fill);
			
			$i++;
		}
		
	//}//fim while turnos
	
	$pdf->AliasNbPages();
	$pdf->Output("checklist_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>