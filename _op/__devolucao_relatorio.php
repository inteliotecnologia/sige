<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("psl", $_SESSION["permissao"])) {
	
	if ($_GET["tipo_relatorio"]=="g") {
		
		define('FPDF_FONTPATH','includes/fpdf/font/');
		require("includes/fpdf/fpdf.php");
		require("includes/fpdf/modelo_retrato.php");
		
		$pdf=new PDF("P", "cm", "A4");
		$pdf->SetMargins(2, 2, 2);
		$pdf->SetAutoPageBreak(true, 2);
		$pdf->SetFillColor(230,230,230);
		$pdf->AddFont('ARIALNARROW');
		$pdf->AddFont('ARIAL_N_NEGRITO');
		$pdf->AddFont('ARIAL_N_ITALICO');
		$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
		$pdf->SetFont('ARIALNARROW');
		
		$periodo_in= explode("/", $_POST["periodo"]);
		
		if ($_POST["id_devolucao"]!="") $id_devolucao= $_POST["id_devolucao"];
		if ($_GET["id_devolucao"]!="") $id_devolucao= $_GET["id_devolucao"];
		
		if ($id_devolucao!="") $str.= " and   op_suja_devolucao.id_devolucao = '". $id_devolucao ."' ";
	
		
		$result= mysql_query("select * from  op_suja_devolucao, op_suja_remessas
									where op_suja_devolucao.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   op_suja_devolucao.id_remessa = op_suja_remessas.id_remessa
									". $str ."
									") or die(mysql_error());
		
		$altura_padrao= 14.4;
		
		if (mysql_num_rows($result)==0) {
			$pdf->AddPage();
			$pdf->Cell(0, 0.6, "Nada encontrado.", 0, 1);
		}
		else {
			$i=0;
			while ($rs= mysql_fetch_object($result)) {		
				$pdf->AddPage();
				
				for ($k=0; $k<2; $k++) {
					
					$altura_aqui=($altura_padrao*$k);
					
					if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa"] .".jpg"))
						$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa"] .".jpg", 2, 1.2+$altura_aqui, 5, 1.9287);
					
					$pdf->SetXY(16.9, 1.75+$altura_aqui);
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
					$pdf->Cell(0, 0.6, "FORMULÁRIO DE DEVOLUÇÃO DE RESÍDUOS/MATERIAIS", 0, 1, "R");
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
					
					if ($_POST["id_devolucao"]!="") $periodo_str= "";
					elseif ($_POST["periodo"]!="") $periodo_str= traduz_mes($periodo_in[0]) ." de ". $periodo_in[1];
					else $periodo_str= $_POST["data"];
					
					$pdf->Cell(0, 0.6, $periodo_str, 0, 1, "R");
					
					$pdf->Ln();
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					$pdf->Cell(2.5, 0.6, "CLIENTE:", 0, 0, "L");
					$pdf->SetFont('ARIALNARROW', '', 10);
					$pdf->Cell(0, 0.6, pega_pessoa($rs->id_cliente), 0, 1, "L");
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					$pdf->Cell(2.5, 0.6, "REMESSA:", 0, 0, "L");
					$pdf->SetFont('ARIALNARROW', '', 10);
					$pdf->Cell(2.5, 0.6, desformata_data($rs->data_remessa) ." nº ". $rs->num_remessa, 0, 1, "L");
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					$pdf->Cell(2.5, 0.6, "DATA/HORA:", 0, 0, "L");
					$pdf->SetFont('ARIALNARROW', '', 10);
					$pdf->Cell(2.5, 0.6, desformata_data($rs->data_devolucao) ." ". $rs->hora_devolucao, 0, 1, "L");
					
					$pdf->Ln();
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
					
					$i=1;
					$vetor= pega_itens_devolucao('l');
					while ($vetor[$i]) {
						$item= $vetor[$i];
						
						if (strlen($item)>11) $largura= 2;
						else $largura= 1.18;
						
						if ($i==11) $quebra=1; else $quebra= 0;
						
						$pdf->Cell($largura, 0.6, $item, 1, $quebra, "C", 1);
						$i++;
					}
					
					
					
					$pdf->SetFont('ARIALNARROW', '', 8);
					
					$result_dev= mysql_query("select * from op_suja_devolucao_itens
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_devolucao = '". $rs->id_devolucao ."'
												order by id_item asc
												");
					while ($rs_dev= mysql_fetch_object($result_dev)) {
						$item= pega_itens_devolucao($rs_dev->id_item);
						
						if (strlen($item)>11) $largura= 2;
						else $largura= 1.18;
						
						if (($rs_dev->id_item)==1) {
							$medida= "kg";
							$valor= fnum($rs_dev->peso_qtde);
						}
						else {
							$medida= "un.";
							$valor= number_format($rs_dev->peso_qtde, 0);
						}
						
						$pdf->Cell($largura, 0.6, $valor ." ". $medida, 1, 0, "C");
					}
					
					$pdf->Ln();$pdf->Ln();
					
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					$pdf->Cell(2.5, 0.7, "PESO TOTAL:", 0, 0, "L");
					$pdf->SetFont('ARIALNARROW', '', 10);
					$pdf->Cell(6, 0.7, fnum($rs->peso) ." kg", 0, 0, "L");
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					$pdf->Cell(2, 0.7, "PACOTES:", 0, 0, "L");
					$pdf->SetFont('ARIALNARROW', '', 10);
					$pdf->Cell(6.5, 0.7, $rs->pacotes, 0, 1, "L");
					
					$pdf->Ln();
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					$pdf->Cell(8.5, 0.7, "PROTOCOLO DE RECEBIMENTO:", 1, 0, "L");
					$pdf->Cell(8.5, 0.7, "ENTREGUE POR:", 1, 1, "L");
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					
					for ($i=0; $i<4; $i++) {
						switch ($i) {
							case 0: $titulo= "NOME:"; break;
							case 1: $titulo= "ASSINATURA:"; break;
							case 2: $titulo= "DATA:"; break;
							case 3: $titulo= "HORA:"; break;
						}
						$pdf->Cell(8.5, 0.7, $titulo, 1, 0, "L");
						$pdf->Cell(8.5, 0.7, $titulo, 1, 1, "L");
					}
				}
			}//fim while
		}
		
		$c++;
		
	}
	//diario
	elseif (($_POST["tipo_relatorio"]=="") || ($_POST["tipo_relatorio"]=="d")) {
		define('FPDF_FONTPATH','includes/fpdf/font/');
		require("includes/fpdf/fpdf.php");
		require("includes/fpdf/modelo_retrato.php");
		
		$pdf=new PDF("P", "cm", "A4");
		$pdf->SetMargins(2, 2, 2);
		$pdf->SetAutoPageBreak(true, 2);
		$pdf->SetFillColor(230,230,230);
		$pdf->AddFont('ARIALNARROW');
		$pdf->AddFont('ARIAL_N_NEGRITO');
		$pdf->AddFont('ARIAL_N_ITALICO');
		$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
		$pdf->SetFont('ARIALNARROW');
		
		$periodo_in= explode("/", $_POST["periodo"]);
		
		if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
		if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
		if ($id_cliente!="") $str .= "  ";
		
		if ($_POST["id_devolucao"]!="") $id_devolucao= $_POST["id_devolucao"];
		if ($_GET["id_devolucao"]!="") $id_devolucao= $_GET["id_devolucao"];
		
		if ($id_devolucao!="") $str.= " and   op_suja_devolucao.id_devolucao = '". $id_devolucao ."' ";
		elseif ($_POST["periodo"]!="") $str.= " and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". $_POST["periodo"] ."' ";
		else $str.= " and   op_suja_remessas.data_remessa = '". formata_data($_POST["data"]) ."' ";
		
		$c=0;
		while ($_POST["id_cliente_entrega"][$c]!="") {
			
			
			$result= mysql_query("select * from  op_suja_devolucao, op_suja_remessas
										where op_suja_devolucao.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   op_suja_devolucao.id_remessa = op_suja_remessas.id_remessa
										and   op_suja_devolucao.id_cliente= '". $_POST["id_cliente_entrega"][$c] ."'
										". $str ."
										") or die(mysql_error());
			
			$altura_padrao= 14.4;
			
			if (mysql_num_rows($result)==0) {
				$pdf->AddPage();
				
				if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa"] .".jpg"))
					$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa"] .".jpg", 2, 1.2+$altura_aqui, 5, 1.9287);
				
				$pdf->SetXY(16.9, 1.75+$altura_aqui);
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.6, "FORMULÁRIO DE DEVOLUÇÃO DE RESÍDUOS/MATERIAIS", 0, 1, "R");
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				
				if ($_POST["id_devolucao"]!="") $periodo_str= "";
				elseif ($_POST["periodo"]!="") $periodo_str= traduz_mes($periodo_in[0]) ." de ". $periodo_in[1];
				else $periodo_str= $_POST["data"];
				
				$pdf->Cell(0, 0.6, $periodo_str, 0, 1, "R");
				
				$pdf->Ln();
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(2.5, 0.6, "CLIENTE:", 0, 0, "L");
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(0, 0.6, pega_pessoa($_POST["id_cliente_entrega"][$c]), 0, 1, "L");
				
				$pdf->Ln();
				
				$pdf->Cell(0, 0.6, "Nada encontrado.", 0, 1);
			}
			else {
				$i=0;
				while ($rs= mysql_fetch_object($result)) {		
					$pdf->AddPage();
					
					for ($k=0; $k<2; $k++) {
						
						$altura_aqui=($altura_padrao*$k);
						
						if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa"] .".jpg"))
							$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa"] .".jpg", 2, 1.2+$altura_aqui, 5, 1.9287);
						
						$pdf->SetXY(16.9, 1.75+$altura_aqui);
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
						$pdf->Cell(0, 0.6, "FORMULÁRIO DE DEVOLUÇÃO DE RESÍDUOS/MATERIAIS", 0, 1, "R");
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
						
						if ($_POST["id_devolucao"]!="") $periodo_str= "";
						elseif ($_POST["periodo"]!="") $periodo_str= traduz_mes($periodo_in[0]) ." de ". $periodo_in[1];
						else $periodo_str= $_POST["data"];
						
						$pdf->Cell(0, 0.6, $periodo_str, 0, 1, "R");
						
						$pdf->Ln();
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
						$pdf->Cell(2.5, 0.6, "CLIENTE:", 0, 0, "L");
						$pdf->SetFont('ARIALNARROW', '', 10);
						$pdf->Cell(0, 0.6, pega_pessoa($rs->id_cliente), 0, 1, "L");
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
						$pdf->Cell(2.5, 0.6, "REMESSA:", 0, 0, "L");
						$pdf->SetFont('ARIALNARROW', '', 10);
						$pdf->Cell(2.5, 0.6, desformata_data($rs->data_remessa) ." nº ". $rs->num_remessa, 0, 1, "L");
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
						$pdf->Cell(2.5, 0.6, "DATA/HORA:", 0, 0, "L");
						$pdf->SetFont('ARIALNARROW', '', 10);
						$pdf->Cell(2.5, 0.6, desformata_data($rs->data_devolucao) ." ". $rs->hora_devolucao, 0, 1, "L");
						
						$pdf->Ln();
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
						
						$i=1;
						$vetor= pega_itens_devolucao('l');
						while ($vetor[$i]) {
							$item= $vetor[$i];
							
							if (strlen($item)>11) $largura= 2;
							else $largura= 1.18;
							
							if ($i==11) $quebra=1; else $quebra= 0;
							
							$pdf->Cell($largura, 0.6, $item, 1, $quebra, "C", 1);
							$i++;
						}
						
						
						
						$pdf->SetFont('ARIALNARROW', '', 8);
						
						$result_dev= mysql_query("select * from op_suja_devolucao_itens
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_devolucao = '". $rs->id_devolucao ."'
													order by id_item asc
													");
						while ($rs_dev= mysql_fetch_object($result_dev)) {
							$item= pega_itens_devolucao($rs_dev->id_item);
							
							if (strlen($item)>11) $largura= 2;
							else $largura= 1.18;
							
							if (($rs_dev->id_item)==1) {
								$medida= "kg";
								$valor= fnum($rs_dev->peso_qtde);
							}
							else {
								$medida= "un.";
								$valor= number_format($rs_dev->peso_qtde, 0);
							}
							
							$pdf->Cell($largura, 0.6, $valor ." ". $medida, 1, 0, "C");
						}
						
						$pdf->Ln();$pdf->Ln();
						
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
						$pdf->Cell(2.5, 0.7, "PESO TOTAL:", 0, 0, "L");
						$pdf->SetFont('ARIALNARROW', '', 10);
						$pdf->Cell(6, 0.7, fnum($rs->peso) ." kg", 0, 0, "L");
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
						$pdf->Cell(2, 0.7, "PACOTES:", 0, 0, "L");
						$pdf->SetFont('ARIALNARROW', '', 10);
						$pdf->Cell(6.5, 0.7, $rs->pacotes, 0, 1, "L");
						
						$pdf->Ln();
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
						$pdf->Cell(8.5, 0.7, "PROTOCOLO DE RECEBIMENTO:", 1, 0, "L");
						$pdf->Cell(8.5, 0.7, "ENTREGUE POR:", 1, 1, "L");
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
						
						for ($i=0; $i<4; $i++) {
							switch ($i) {
								case 0: $titulo= "NOME:"; break;
								case 1: $titulo= "ASSINATURA:"; break;
								case 2: $titulo= "DATA:"; break;
								case 3: $titulo= "HORA:"; break;
							}
							$pdf->Cell(8.5, 0.7, $titulo, 1, 0, "L");
							$pdf->Cell(8.5, 0.7, $titulo, 1, 1, "L");
						}
						}
				}//fim while
			}
			
			$c++;
		}//fim clientes
	}
	//mensal
	else {
		
		define('FPDF_FONTPATH','includes/fpdf/font/');
		require("includes/fpdf/fpdf.php");
		require("includes/fpdf/modelo_paisagem.php");
	
	
		if ( ($_POST["data1"]!="") && ($_POST["data2"]!="") ) {
			$data1= formata_data_hifen($_POST["data1"]); $data1f= $_POST["data1"];
			$data2= formata_data_hifen($_POST["data2"]); $data2f= $_POST["data2"];
			
			$data1_mk= faz_mk_data($data1);
			$data2_mk= faz_mk_data($data2);
			
			$dataq= explode("/", $_POST["data1"]);
			$mes_extenso= traduz_mes($dataq[1]);
		}
		
		$pdf=new PDF("L", "cm", "A4");
		$pdf->SetMargins(1.5, 1.5, 1.5);
		$pdf->SetAutoPageBreak(true, 1.5);
		$pdf->SetFillColor(230,230,230);
		$pdf->AddFont('ARIALNARROW');
		$pdf->AddFont('ARIAL_N_NEGRITO');
		$pdf->AddFont('ARIAL_N_ITALICO');
		$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
		$pdf->SetFont('ARIALNARROW');
			
		$periodo= explode("/", $_POST["periodo"]);
		$periodo_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
		
		$total_dias_mes= date("t", $periodo_mk);
	
		$largura= 23/$total_dias_mes;
		
		$pdf->AddPage();
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "RELATÓRIO DE DEVOLUÇÃO", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, traduz_mes($periodo[0]) ." de ". $periodo[1], 0, 1, "R");
		
		$pdf->Ln();
		
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		$pdf->SetFillColor(200,200,200);
		
		$pdf->Cell(2.5, 0.5, "CLIENTE", 1 , 0, "L", 1);
		
		//repetir todos os dias do intervalo
		for ($t=1; $t<=$total_dias_mes; $t++) {
			$pdf->Cell($largura, 0.5, $t, 1, 0, "C", 1);
			//echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->Cell(1.2, 0.5, "Total", 1, 1, "C", 1);
				
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		if ($_POST["id_cliente"]!="") $str .= " and   pessoas.id_pessoa =  '". $_POST["id_cliente"] ."'";
		
		$result= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
													where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
													". $str ."
													and   pessoas_tipos.tipo_pessoa = 'c'
													and   pessoas.id_cliente_tipo = '1'
													order by 
													pessoas.apelido_fantasia asc
													") or die(mysql_error());
		$i=0;
		while ($rs= mysql_fetch_object($result)) {
			if (($j%2)==0) $fill= 0;
			else {
				$fill=1;
				$pdf->SetFillColor(230,230,230);
			}
			
			$pdf->Cell(2.5, 0.5, $rs->sigla, 1, 0, "L", $fill);
			
			$total_cliente= 0;
			//$total_dia= array();
			
			//repetir todos os dias do intervalo
			for ($t=1; $t<=$total_dias_mes; $t++) {
				
				if ($t==1) $total_dia[$t]= 0;
				
				$data_aqui= $periodo[1] ."-". $periodo[0] ."-". formata_saida($t, 2);
				
				$result_dev= mysql_query("select sum(peso) as peso from op_suja_devolucao, op_suja_remessas
												where op_suja_devolucao.id_cliente = '". $rs->id_cliente ."'
												and   op_suja_remessas.id_remessa = op_suja_devolucao.id_remessa
												and   op_suja_remessas.data_remessa = '". $data_aqui ."'
												") or die(mysql_error());
				
				$rs_dev= mysql_fetch_object($result_dev);
				
				$total_cliente += $rs_dev->peso;
				$total_dia[$t]+= $rs_dev->peso;
				$total_geral_periodo+= $rs_dev->peso;
				
				$pdf->Cell($largura, 0.5, fnum($rs_dev->peso), 1, 0, "C", $fill);
				//echo traduz_dia_resumido($dia_semana_tit);
			}
			
			$pdf->Cell(1.2, 0.5, fnum($total_cliente), 1, 1, "C", $fill);
			
			$j++;
		}
			
		$pdf->SetFillColor(200,200,200);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(2.5, 0.5, "TOTAL", 1 , 0, "L", 1);
		$pdf->SetFont('ARIALNARROW', '', 8);
		//repetir todos os dias do intervalo
		for ($t=1; $t<=$total_dias_mes; $t++) {
			$pdf->Cell($largura, 0.5, fnum($total_dia[$t]), 1, 0, "C", 1);
			//echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(1.2, 0.5, fnum($total_geral_periodo), 1, 1, "C", 1);
		
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("devolucao_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>