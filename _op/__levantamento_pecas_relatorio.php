<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("pls", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_paisagem.php");
	
	$pdf=new PDF("L", "cm", "A4");
	$pdf->SetMargins(1.5, 1.5, 1.5);
	$pdf->SetAutoPageBreak(true, 2);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	if ( ($_POST["data1"]!="") && ($_POST["data2"]!="") ) {
		$data1= $_POST["data1"];
		$data2= $_POST["data2"];
	}
	else {
		if ( ($_GET["data1"]!="") && ($_GET["data2"]!="") ) {
			$data1= $_GET["data1"];
			$data2= $_GET["data2"];
		}
	}
	
	if ( ($data1!="") && ($data2!="") ) {
		$data1f= $data1; $data1= formata_data_hifen($data1);
		$data2f= $data2; $data2= formata_data_hifen($data2);
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2);
	}
	else {
		$periodo2= explode('/', $_POST["periodo"]);
		$periodo_mk= mktime(0, 0, 0, $periodo2[0], 1, $periodo2[1]);
		
		$dias_mes= date("t", $data1_mk);
		
		$data1_mk= mktime(14, 0, 0, $periodo2[0], $dia_inicio, $periodo2[1]);
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
	
	//---------------------------------------------
	
	$total_dias_mes= date("t", $periodo_mk);
		
	$largura= (22.2/$total_dias_mes);
	
	if ($_POST["id_contrato"]!="") $str= " and   pessoas.id_contrato= '". $_POST["id_contrato"] ."'  ";
	if ($_POST["id_cliente"]!="") $str= " and   pessoas.id_pessoa= '". $_POST["id_cliente"] ."'  ";
	
	$result= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos, fi_contratos
								where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
								and   pessoas_tipos.tipo_pessoa = 'c'
								and   pessoas.status_pessoa = '1'
								and   pessoas.id_cliente_tipo = '1'
								and   pessoas.id_contrato = fi_contratos.id_contrato
								$str
								order by 
								pessoas.apelido_fantasia asc
								") or die(mysql_error());
	
	while ($rs= mysql_fetch_object($result)) {
		
		if ($_POST["modo"]==1) $limite=2;
		else $limite=1;
		
		for ($k=0; $k<$limite; $k++) {
			$total_geral_periodo= 0;
			
			$pdf->AddPage();
			
			if ($_POST["modo"]==1) {
				if ($k==0) $valor_goma=0;
				else $valor_goma=1;
			}
			elseif ($_POST["modo"]==2) $valor_goma=0;
			elseif ($_POST["modo"]==3) $valor_goma=1;
			
			if ($valor_goma==0) $tit_pagina= "PEÇAS";
			else $tit_pagina= "GOMAS";
			
			//echo $rs->apelido_fantasia."<br />";
			
			//if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg"))
			//	$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 2, 1.3, 5, 1.9287);
			
			$pdf->SetXY(7,1.25);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "LEVANTAMENTO DE ". $tit_pagina ." - ". traduz_mes($periodo2[0]) ." de ". $periodo2[1], 0 , 1, 'R');
			
			//$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, $rs->apelido_fantasia, 0 , 1, 'R');
			
			$pdf->Ln();
			
			
			
			
			
			
			
			// ------------- tabela
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			$pdf->SetFillColor(200,200,200);
			
			$pdf->Cell(3.2, 0.45, "PEÇAS", 1 , 0, "L", 1);
			
			//repetir todos os dias do intervalo
			for ($t=1; $t<=$total_dias_mes; $t++) {
				$pdf->Cell($largura, 0.45, $t, 1, 0, "C", 1);
				//echo traduz_dia_resumido($dia_semana_tit);
				
				$total_dia[$t]= 0;
			}
			
			$pdf->Cell(1.2, 0.45, "TOTAL", 1, 1, "C", 1);
					
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->SetFillColor(230,230,230);
			
			$j=0;
			while ($_POST["id_peca"][$j]) {
				
				if (($j%2)==0) $fill= 1;
				else $fill=0;
				
				$pdf->Cell(3.2, 0.5, pega_pecas_roupa($_POST["id_peca"][$j]), 1, 0, "L", $fill);
				
				$total_peca= 0;
				//$total_dia= array();
				
				//repetir todos os dias do intervalo
				for ($t=1; $t<=$total_dias_mes; $t++) {
					
					//if ($t==1) $total_dia[$t]= 0;
					
					$data_aqui= $periodo2[1] ."-". $periodo2[0] ."-". formata_saida($t, 2);
					
					
					
					
					
					
					
					
					$str_pecas= " and   tr_percursos_passos.data_percurso = '". $data_aqui ."' ";
					
					
					//pegar a ultima entrega do MÊS/PERÍODO ANTERIOR
					$result_entrega_ultima_periodo= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
																where (tr_percursos.tipo = '2')
																and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
																and   tr_percursos_passos.passo = '1'
																and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
																and   tr_percursos_clientes.id_cliente = '". $_POST["id_cliente"] ."'
																and   tr_percursos_passos.data_percurso < '". $data_aqui ."'
																order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
																limit 1
																");
					$rs_entrega_ultima_periodo= mysql_fetch_object($result_entrega_ultima_periodo);
					
					$data_formatada= formata_data_hifen("01/". $_POST["periodo"]);
					$inicio_proximo_mes= soma_data($data_formatada, 0, 1, 0);
					
					//pegar a ultima entrega do MÊS ATUAL
					$result_entrega_proximo= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
																where (tr_percursos.tipo = '2')
																and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
																and   tr_percursos_passos.passo = '1'
																and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
																and   tr_percursos_clientes.id_cliente = '". $_POST["id_cliente"] ."'
																$str_pecas
																order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
																limit 1
																");
					
					$rs_entrega_proximo= mysql_fetch_object($result_entrega_proximo);
					
					$str_geral_periodo= "
										and op_limpa_pesagem.data_hora_pesagem >= '". $rs_entrega_ultima_periodo->data_percurso ." ". $rs_entrega_ultima_periodo->hora_percurso ."'
										and op_limpa_pesagem.data_hora_pesagem < '". $rs_entrega_proximo->data_percurso ." ". $rs_entrega_proximo->hora_percurso ."'
										";
					
					//echo "dia $data_aqui peça: ". pega_pecas_roupa($_POST["id_peca"][$j]) ."= ";
					//echo $str_geral_periodo ."<br /><br />";
					
					$result_pesagem= mysql_query("select *
													from op_limpa_pesagem, op_limpa_pesagem_pecas
													where op_limpa_pesagem.id_pesagem = op_limpa_pesagem_pecas.id_pesagem
													and   op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_limpa_pesagem.id_cliente = '". $_POST["id_cliente"] ."'
													and   op_limpa_pesagem_pecas.id_tipo_roupa = '". $_POST["id_peca"][$j] ."'
													and   op_limpa_pesagem.extra = '0'
													and   op_limpa_pesagem.goma = '". $valor_goma ."'
													$str_geral_periodo
													") or die(mysql_error());
					$linhas_pesagem= mysql_num_rows($result_pesagem);
					
					$peso_total_peca= 0;
					$total_pacotes_peca= 0;
					$total_pecas_peca= 0;
					
					if ($linhas_pesagem>0) {
						
						while ($rs_pesagem= mysql_fetch_object($result_pesagem)) {
							$peso_total_peca += $rs_pesagem->peso;
							$total_pacotes_peca += $rs_pesagem->num_pacotes;
							$total_pacotes_peca += $rs_pesagem->pacotes_sobra;
							
							//if ($rs_pesagem->qtde_pecas_sobra>0) $total_pacotes_peca++;
							
							$total_pecas_aqui= ($rs_pesagem->qtde_pacote*$rs_pesagem->num_pacotes)+$rs_pesagem->qtde_pecas_sobra;
							$total_pecas_peca += $total_pecas_aqui;
						}
					}
					
					
					
					
					$total_peca+= $total_pecas_peca;
					$total_dia[$t]+= $total_pecas_peca;
					$total_geral_periodo+= $total_pecas_peca;
					
					$pdf->Cell($largura, 0.475, fnumf($total_pecas_peca), 1, 0, "C", $fill);
					//echo traduz_dia_resumido($dia_semana_tit);
				}
				
				$pdf->Cell(1.2, 0.475, fnumf($total_peca), 1, 1, "C", $fill);
				
				$j++;
			}
			
			$pdf->SetFillColor(200,200,200);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$pdf->Cell(3.2, 0.45, "TOTAL", 1 , 0, "L", 1);
			$pdf->SetFont('ARIALNARROW', '', 7);
			//repetir todos os dias do intervalo
			for ($t=1; $t<=$total_dias_mes; $t++) {
				$pdf->Cell($largura, 0.45, fnumf($total_dia[$t]), 1, 0, "C", 1);
				//echo traduz_dia_resumido($dia_semana_tit);
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->Cell(1.2, 0.45, fnumf($total_geral_periodo), 1, 1, "C", 1);
			
		
		
		
		}
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("levantamento_pecas_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>