<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("pls", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato_seco.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
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
		
		$str_periodo= " and   op_suja_remessas.data_remessa >= '". $data1 ."' and op_suja_remessas.data_remessa <= '". $data2 ."' ";
	}
	else {
		$periodo2= explode('/', $_POST["periodo"]);
		
		if ($_POST["periodo"]=="09/2009") $dia_inicio=11;
		else $dia_inicio= 1;
		
		$dias_mes= date("t", $data1_mk);
		
		$data1_mk= mktime(14, 0, 0, $periodo2[0], $dia_inicio, $periodo2[1]);
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
		$str_periodo= " and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". $_POST["periodo"] ."' ";
	}
	
	if ($_POST["id_contrato"]!="") $str= " and   pessoas.id_contrato= '". $_POST["id_contrato"] ."'  ";
	
	$ic=0;
	
	//while ($rs= mysql_fetch_object($result)) {
		
		// ---------------------------------------------------------------------------------------------------------------
		
		//se estiver gerando por periodo...
		//if (($_POST["data1"]=="") && ($_POST["data2"]=="")) {
		
			//começando a discriminação das peças e pacotes
			$pdf->AddPage();
			
			if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg"))
				$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 2, 1.3, 5, 1.9287);
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "QUANTITATIVO DE PACOTES", 0 , 1, 'R');
			
			//$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, traduz_mes($periodo2[0]) ."/". $periodo2[1], 0 , 1, 'R');
			
			$pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(9.5, 0.5, "TIPO DE ROUPA", 1, 0, "L", 1);
			$pdf->Cell(3.75, 0.5, "NUM. PACOTES", 1, 0, "C", 1);
			$pdf->Cell(3.75, 0.5, "NUM. PEÇAS", 1, 1, "C", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			
			if (($_POST["data1"]=="") && ($_POST["data2"]=="")) {
				$periodo_anterior= date("m/Y", mktime(0, 0, 0, $periodo2[0]-1, 1, $periodo2[1]));
				
				$str_pecas1= " and   DATE_FORMAT(tr_percursos_passos.data_percurso, '%m/%Y') = '". $periodo_anterior ."'
								order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
								";
				
				$str_pecas2= " and   DATE_FORMAT(tr_percursos_passos.data_percurso, '%m/%Y') = '". $_POST["periodo"] ."'
								order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
								";
			}
			else {
				$str_pecas1= " and   tr_percursos_passos.data_percurso < '". $data1 ."'
								order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
								";
				$str_pecas2= " and   tr_percursos_passos.data_percurso <= '". $data2 ."'
								order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
									";
			}
			
			
			//pegar a ultima entrega do MÊS/PERÍODO ANTERIOR
			$result_entrega_ultima_periodo= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes, pessoas
														where (tr_percursos.tipo = '2' /* or tr_percursos.tipo = '4' */ )
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														and   tr_percursos_passos.passo = '1'
														and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
														and   tr_percursos_clientes.id_cliente = pessoas.id_pessoa
														and   pessoas.status_pessoa = '1'
														$str
														$str_pecas1
														limit 1
														");
			$rs_entrega_ultima_periodo= mysql_fetch_object($result_entrega_ultima_periodo);
			
			$data_formatada= formata_data_hifen("01/". $_POST["periodo"]);
			$inicio_proximo_mes= soma_data($data_formatada, 0, 1, 0);
			
			//pegar a ultima entrega do MÊS ATUAL
			$result_entrega_proximo= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes, pessoas
														where (tr_percursos.tipo = '2' /* or tr_percursos.tipo = '5' */ )
														and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
														and   tr_percursos_passos.passo = '1'
														and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
														and   tr_percursos_clientes.id_cliente = pessoas.id_pessoa
														and   pessoas.status_pessoa = '1'
														$str
														$str_pecas2
														limit 1
														");
			
			$rs_entrega_proximo= mysql_fetch_object($result_entrega_proximo);
			
			$str_geral_periodo= "
								and op_limpa_pesagem.data_hora_pesagem >= '". $rs_entrega_ultima_periodo->data_percurso ." ". $rs_entrega_ultima_periodo->hora_percurso ."'
								and op_limpa_pesagem.data_hora_pesagem < '". $rs_entrega_proximo->data_percurso ." ". $rs_entrega_proximo->hora_percurso ."'
								";
			
			//echo "dia $data_aqui ";
			//echo $str_geral_periodo ."<br /><br />";
			
			/*$result_total= mysql_query("select sum(op_limpa_pesagem.peso) as peso_total from op_limpa_pesagem, op_suja_remessas
										where op_suja_remessas.id_remessa = op_limpa_pesagem.id_remessa
										and   op_suja_remessas.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   op_limpa_pesagem.id_cliente = '". $rs->id_cliente ."'
										$str_geral_periodo
										");
			
			$rs_total= mysql_fetch_object($result_total);
			$peso_total= $rs_total->peso_total;
			*/
			
			$peso_total_peca_geral= 0;
			$total_pacotes_peca_geral= 0;
			$total_pecas_peca_geral= 0;
			
			/*
			echo "select distinct(op_limpa_pesagem_pecas.id_tipo_roupa)
												from op_limpa_pecas, op_limpa_pesagem, op_limpa_pesagem_pecas, pessoas
												where op_limpa_pesagem.id_pesagem = op_limpa_pesagem_pecas.id_pesagem
												and   op_limpa_pesagem_pecas.id_tipo_roupa = op_limpa_pecas.id_peca
												and   op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   op_limpa_pesagem.id_cliente = pessoas.id_pessoa
												and   op_limpa_pesagem.extra = '0'
												$str
												
												$str_geral_periodo
												order by op_limpa_pecas.peca asc
												";
			*/
			
			$result_pesagem_pecas= mysql_query("select distinct(op_limpa_pesagem_pecas.id_tipo_roupa)
												from op_limpa_pecas, op_limpa_pesagem, op_limpa_pesagem_pecas, pessoas
												where op_limpa_pesagem.id_pesagem = op_limpa_pesagem_pecas.id_pesagem
												and   op_limpa_pesagem_pecas.id_tipo_roupa = op_limpa_pecas.id_peca
												and   op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   op_limpa_pesagem.id_cliente = pessoas.id_pessoa
												and   op_limpa_pesagem.extra = '0'
												$str
												
												$str_geral_periodo
												order by op_limpa_pecas.peca asc
												") or die(mysql_error());
			
			$pdf->SetFillColor(235,235,235);
			
			$j=0;
			while ($rs_pesagem_pecas= mysql_fetch_object($result_pesagem_pecas)) {
				
				if (($j%2)==0) $fill=0; else $fill= 1;
			
				$result_pesagem= mysql_query("select *
												from op_limpa_pesagem, op_limpa_pesagem_pecas, pessoas
												where op_limpa_pesagem.id_pesagem = op_limpa_pesagem_pecas.id_pesagem
												and   op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   op_limpa_pesagem.id_cliente = pessoas.id_pessoa
												and   op_limpa_pesagem_pecas.id_tipo_roupa = '". $rs_pesagem_pecas->id_tipo_roupa ."'
												and   op_limpa_pesagem.extra = '0'
												$str
												
												$str_geral_periodo
												") or die(mysql_error());
				$linhas_pesagem= mysql_num_rows($result_pesagem);
				
				if ($linhas_pesagem>0) {
					$peso_total_peca= 0;
					$total_pacotes_peca= 0;
					$total_pecas_peca= 0;
					
					while ($rs_pesagem= mysql_fetch_object($result_pesagem)) {
						$peso_total_peca += $rs_pesagem->peso;
						$total_pacotes_peca += $rs_pesagem->num_pacotes;
						$total_pacotes_peca += $rs_pesagem->pacotes_sobra;
						
						//if ($rs_pesagem->qtde_pecas_sobra>0) $total_pacotes_peca++;
						
						$total_pecas_aqui= ($rs_pesagem->qtde_pacote*$rs_pesagem->num_pacotes)+$rs_pesagem->qtde_pecas_sobra;
						$total_pecas_peca += $total_pecas_aqui;
					}
						
					$pdf->Cell(9.5, 0.45, pega_pecas_roupa($rs_pesagem_pecas->id_tipo_roupa), 1, 0, "L", $fill);
					$pdf->Cell(3.75, 0.45, fnumf($total_pacotes_peca), 1, 0, "C", $fill);
					$pdf->Cell(3.75, 0.45, fnumf($total_pecas_peca), 1, 1, "C", $fill);
					
					$peso_total_peca_geral += $peso_total_peca;
					$total_pacotes_peca_geral += $total_pacotes_peca;
					$total_pecas_peca_geral += $total_pecas_peca;
					
					$j++;
				}
			}
			
			$pdf->SetFillColor(210,210,210);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
			$pdf->Cell(9.5, 0.5, "TOTAL:", 1, 0, "L", 1);
			$pdf->Cell(3.75, 0.5, fnumf($total_pacotes_peca_geral), 1, 0, "C", 1);
			$pdf->Cell(3.75, 0.5, fnumf($total_pecas_peca_geral), 1, 1, "C", 1);
			
			
			// -------------------------------------------------------------------------------------
			
			$pdf->Ln();
	//}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("movimentacao_pacotes_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>