<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("12(", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	
	require("includes/fpdf/modelo_retrato_logo.php");
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(200,200,200);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$result_empresa= mysql_query("select * from  pessoas, rh_enderecos, empresas, cidades, ufs
									where empresas.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   empresas.id_pessoa = pessoas.id_pessoa
									and   pessoas.id_pessoa = rh_enderecos.id_pessoa
									and   rh_enderecos.id_cidade = cidades.id_cidade
									and   cidades.id_uf = ufs.id_uf
									") or die(mysql_error());
	$rs_empresa= mysql_fetch_object($result_empresa);
	
	//diário, por item
	if (($_POST["tipo_relatorio"]=="d") || ($_POST["tipo_relatorio"]=="")) {
	
		$result= mysql_query("select *
								from  op_limpa_costura_consertos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_costura_conserto = '". $_GET["id_costura_conserto"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		for ($j=0; $j<2; $j++) {
		
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "CONTROLE DE COSTURA", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, pega_sigla_pessoa($rs->id_cliente), 0 , 1, 'R');
		
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2.5, 0.5, "RECEBIMENTO:", 0, 0, 'L', 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(3, 0.5, desformata_data($rs->data_chegada), 0, 0, 'L', 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.5, "ENTREGA:", 0, 0, 'L', 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(3, 0.5, desformata_data($rs->data_entrega) ." ". $rs->hora_entrega, 0, 1, 'L', 0);
			
			$pdf->Ln();$pdf->Ln();
		
			$result_costura_conserto_pecas= mysql_query("select distinct(op_limpa_costura_consertos_pecas.id_tipo_roupa)
															from op_limpa_costura_consertos_pecas, op_limpa_pecas
															where op_limpa_costura_consertos_pecas.id_costura_conserto = '". $rs->id_costura_conserto ."'
															and   op_limpa_costura_consertos_pecas.id_tipo_roupa = op_limpa_pecas.id_peca
															order by op_limpa_pecas.peca asc
															") or die(mysql_error());
											
			$pdf->SetFillColor(200,200,200);		
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
			$pdf->Cell(4.5, 0.6, "TIPO DE ROUPA", 1, 0, "L", 1);
			$pdf->Cell(2.5, 0.6, "RECEBIDO", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.6, "CONSERTADO", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.6, "SUBSTITUÍDO", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.6, "BAIXA", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.6, "DEVOLVIDO", 1, 1, "C", 1);
			
			$total_recebido= 0;
			$total_consertado= 0;
			$total_substituido= 0;
			$total_baixa= 0;
			$total_devolvido= 0;
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->SetFillColor(235,235,235);
			
			$i=0;
			
			while ($rs_costura_conserto_pecas= mysql_fetch_object($result_costura_conserto_pecas)) {
				if (($i%2)==0) $fill=1;
				else $fill= 0;
				
				$result_costura_conserto= mysql_query("select sum(qtde_recebido) as qtde_recebido, sum(qtde_consertado) as qtde_consertado,
															  sum(qtde_substituido) as qtde_substituido, sum(qtde_baixa) as qtde_baixa, sum(qtde_devolvido) as qtde_devolvido
																	from op_limpa_costura_consertos_pecas
																	where id_costura_conserto = '". $rs->id_costura_conserto ."'
																	and   id_tipo_roupa = '". $rs_costura_conserto_pecas->id_tipo_roupa ."' 
																	") or die(mysql_error());
				
				$rs_costura_conserto= mysql_fetch_object($result_costura_conserto);
				
				$pdf->Cell(4.5, 0.55, pega_pecas_roupa($rs_costura_conserto_pecas->id_tipo_roupa), 1, 0, "L", $fill);
				$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_recebido, 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_consertado, 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_substituido, 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_baixa, 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_devolvido, 1, 1, "C", $fill);
				
				$total_recebido+=$rs_costura_conserto->qtde_recebido;
				$total_consertado+=$rs_costura_conserto->qtde_consertado;
				$total_substituido+=$rs_costura_conserto->qtde_substituido;
				$total_baixa+=$rs_costura_conserto->qtde_baixa;
				$total_devolvido+=$rs_costura_conserto->qtde_devolvido;
				
				$i++;
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->Cell(4.5, 0.55, "", 0, 0, "L", 0);
			$pdf->Cell(2.5, 0.55, $total_recebido, 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.55, $total_consertado, 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.55, $total_substituido, 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.55, $total_baixa, 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.55, $total_devolvido, 1, 1, "C", !$fill);
			
			$pdf->Ln();
			
			if (($rs->peso_entregue!="") && ($rs->peso_entregue!="0")) $peso_entregue= fnumf($rs->peso_entregue) ." kg";
			else $peso_entregue= "";
			
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(12, 0.55, "", 0, 0);
			$pdf->Cell(5, 0.55, "PESO (kg):", "LTR", 1, "L", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 14);
			
			$pdf->Cell(12, 0.55, "", 0, 0);
			$pdf->Cell(5, 1.5, $peso_entregue, 1, 1, "C");
			
			$pdf->Ln();
			
			if ($rs->obs!="") {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(2.2, 0.5, "OBS:", 0, 1, 'L', 0);
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->MultiCell(0, 0.5, html_entity_decode(strip_tags($rs->obs)), 0, 'L', 0);
			}
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); 
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->Cell(7, 0.75, $rs_empresa->apelido_fantasia, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, pega_pessoa($rs->id_cliente), 'T', 1, 'R');
		}
	}
	//mensal
	elseif ($_POST["tipo_relatorio"]=="m") {
		
		$periodo2= explode('/', $_POST["periodo"]);
		
		if ($_POST["periodo"]=="09/2009") $dia_inicio=11;
		else $dia_inicio= 1;
			
		$data1_mk= mktime(14, 0, 0, $periodo2[0], $dia_inicio, $periodo2[1]);
		
		$dias_mes= date("t", $data1_mk);
		$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
		
		//por cliente
		if ($_POST["modo"]==1) {
		
			if ($_POST["id_contrato"]!="") $str= " and   pessoas.id_contrato= '". $_POST["id_contrato"] ."'  ";
			
			$result= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
										where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
										and   pessoas_tipos.tipo_pessoa = 'c'
										and   pessoas.status_pessoa = '1'
										and   pessoas.id_cliente_tipo = '1'
										$str
										order by 
										pessoas.apelido_fantasia asc
										") or die(mysql_error());
			
			while ($rs= mysql_fetch_object($result)) {
				
				$pdf->AddPage();
				
				$pdf->SetXY(7,1.75);
				
				if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg"))
					$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 2, 1.3, 5, 1.9287);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.6, "CONTROLE DE COSTURA", 0 , 1, 'R');
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.6, pega_sigla_pessoa($rs->id_cliente) ." (". traduz_mes($periodo2[0]) ."/". $periodo2[1] .")", 0 , 1, 'R');
				
				$pdf->Ln();$pdf->Ln();
			
				$result_costura_conserto_pecas= mysql_query("select distinct(op_limpa_costura_consertos_pecas.id_tipo_roupa)
																from op_limpa_costura_consertos_pecas, op_limpa_costura_consertos, op_limpa_pecas
																where op_limpa_costura_consertos.id_costura_conserto = op_limpa_costura_consertos_pecas.id_costura_conserto
																and   DATE_FORMAT(op_limpa_costura_consertos.data_entrega, '%m/%Y') = '". $_POST["periodo"] ."'
																and   op_limpa_costura_consertos.id_cliente = '". $rs->id_cliente ."'
																and   op_limpa_costura_consertos_pecas.id_tipo_roupa = op_limpa_pecas.id_peca
																order by op_limpa_pecas.peca asc
																") or die(mysql_error());
												
				$pdf->SetFillColor(200,200,200);		
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				
				$pdf->Cell(4.5, 0.6, "TIPO DE ROUPA", 1, 0, "L", 1);
				$pdf->Cell(2.5, 0.6, "RECEBIDO", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "CONSERTADO", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "SUBSTITUÍDO", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "BAIXA", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "DEVOLVIDO", 1, 1, "C", 1);
				
				$total_recebido= 0;
				$total_consertado= 0;
				$total_substituido= 0;
				$total_baixa= 0;
				$total_devolvido= 0;
				
				$pdf->SetFont('ARIALNARROW', '', 9);
				$pdf->SetFillColor(235,235,235);
				
				$i=0;
				
				while ($rs_costura_conserto_pecas= mysql_fetch_object($result_costura_conserto_pecas)) {
					if (($i%2)==0) $fill=1;
					else $fill= 0;
					
					$result_costura_conserto= mysql_query("select sum(qtde_recebido) as qtde_recebido, sum(qtde_consertado) as qtde_consertado,
																  sum(qtde_substituido) as qtde_substituido, sum(qtde_baixa) as qtde_baixa, sum(qtde_devolvido) as qtde_devolvido
																		from op_limpa_costura_consertos_pecas, op_limpa_costura_consertos
																		where op_limpa_costura_consertos.id_costura_conserto = op_limpa_costura_consertos_pecas.id_costura_conserto
																		and   op_limpa_costura_consertos.id_cliente = '". $rs->id_cliente ."'
																		and   op_limpa_costura_consertos_pecas.id_tipo_roupa = '". $rs_costura_conserto_pecas->id_tipo_roupa ."' 
																		and   DATE_FORMAT(op_limpa_costura_consertos.data_entrega, '%m/%Y') = '". $_POST["periodo"] ."'
																		") or die(mysql_error());
					
					$rs_costura_conserto= mysql_fetch_object($result_costura_conserto);
					
					$pdf->Cell(4.5, 0.55, pega_pecas_roupa($rs_costura_conserto_pecas->id_tipo_roupa), 1, 0, "L", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_recebido, 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_consertado, 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_substituido, 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_baixa, 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_devolvido, 1, 1, "C", $fill);
					
					$total_recebido+=$rs_costura_conserto->qtde_recebido;
					$total_consertado+=$rs_costura_conserto->qtde_consertado;
					$total_substituido+=$rs_costura_conserto->qtde_substituido;
					$total_baixa+=$rs_costura_conserto->qtde_baixa;
					$total_devolvido+=$rs_costura_conserto->qtde_devolvido;
					
					$i++;
				}
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				
				$pdf->Cell(4.5, 0.55, "", 0, 0, "L", 0);
				$pdf->Cell(2.5, 0.55, $total_recebido, 1, 0, "C", !$fill);
				$pdf->Cell(2.5, 0.55, $total_consertado, 1, 0, "C", !$fill);
				$pdf->Cell(2.5, 0.55, $total_substituido, 1, 0, "C", !$fill);
				$pdf->Cell(2.5, 0.55, $total_baixa, 1, 0, "C", !$fill);
				$pdf->Cell(2.5, 0.55, $total_devolvido, 1, 1, "C", !$fill);
				
				$pdf->Ln();$pdf->Ln();
				
			}
		}
		//por peça geral
		else {
			
			$pdf->AddPage();
				
			$pdf->SetXY(7,1.75);
			
			if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg"))
				$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 2, 1.3, 5, 1.9287);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "CONTROLE DE COSTURA", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "POR TIPO DE PEÇA (". traduz_mes($periodo2[0]) ."/". $periodo2[1] .")", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			
			if ($_POST["id_contrato"]!="") $str= " and   pessoas.id_contrato= '". $_POST["id_contrato"] ."'  ";
			
			
			
			$result_costura_conserto_pecas= mysql_query("select distinct(op_limpa_costura_consertos_pecas.id_tipo_roupa)
															from op_limpa_costura_consertos_pecas, op_limpa_costura_consertos, op_limpa_pecas, pessoas, pessoas_tipos
															where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
															and   pessoas_tipos.tipo_pessoa = 'c'
															and   pessoas.status_pessoa = '1'
															and   pessoas.id_cliente_tipo = '1'
															$str
															and   op_limpa_costura_consertos.id_costura_conserto = op_limpa_costura_consertos_pecas.id_costura_conserto
															and   DATE_FORMAT(op_limpa_costura_consertos.data_entrega, '%m/%Y') = '". $_POST["periodo"] ."'
															and   pessoas.id_pessoa = op_limpa_costura_consertos.id_cliente
															and   op_limpa_costura_consertos_pecas.id_tipo_roupa = op_limpa_pecas.id_peca
															order by op_limpa_pecas.peca asc
															") or die(mysql_error());
											
			$pdf->SetFillColor(200,200,200);		
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->Cell(4.5, 0.6, "TIPO DE ROUPA", 1, 0, "L", 1);
			$pdf->Cell(2.5, 0.6, "RECEBIDO", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.6, "CONSERTADO", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.6, "SUBSTITUÍDO", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.6, "BAIXA", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.6, "DEVOLVIDO", 1, 1, "C", 1);
			
			$total_recebido= 0;
			$total_consertado= 0;
			$total_substituido= 0;
			$total_baixa= 0;
			$total_devolvido= 0;
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->SetFillColor(235,235,235);
			
			$i=0;
			
			while ($rs_costura_conserto_pecas= mysql_fetch_object($result_costura_conserto_pecas)) {
				if (($i%2)==0) $fill=1;
				else $fill= 0;
				
				$result_costura_conserto= mysql_query("select sum(qtde_recebido) as qtde_recebido, sum(qtde_consertado) as qtde_consertado,
															  sum(qtde_substituido) as qtde_substituido, sum(qtde_baixa) as qtde_baixa, sum(qtde_devolvido) as qtde_devolvido
																	from op_limpa_costura_consertos_pecas, op_limpa_costura_consertos, pessoas, pessoas_tipos
																	where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
																	and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
																	and   pessoas_tipos.tipo_pessoa = 'c'
																	and   pessoas.status_pessoa = '1'
																	and   pessoas.id_cliente_tipo = '1'
																	$str
																	and   op_limpa_costura_consertos.id_costura_conserto = op_limpa_costura_consertos_pecas.id_costura_conserto
																	and   pessoas.id_pessoa = op_limpa_costura_consertos.id_cliente
																	and   op_limpa_costura_consertos_pecas.id_tipo_roupa = '". $rs_costura_conserto_pecas->id_tipo_roupa ."' 
																	and   DATE_FORMAT(op_limpa_costura_consertos.data_entrega, '%m/%Y') = '". $_POST["periodo"] ."'
																	") or die(mysql_error());
				
				$rs_costura_conserto= mysql_fetch_object($result_costura_conserto);
				
				$pdf->Cell(4.5, 0.55, pega_pecas_roupa($rs_costura_conserto_pecas->id_tipo_roupa), 1, 0, "L", $fill);
				$pdf->Cell(2.5, 0.55, fnumi($rs_costura_conserto->qtde_recebido), 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, fnumi($rs_costura_conserto->qtde_consertado), 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, fnumi($rs_costura_conserto->qtde_substituido), 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_baixa, 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_devolvido, 1, 1, "C", $fill);
				
				$total_recebido+=$rs_costura_conserto->qtde_recebido;
				$total_consertado+=$rs_costura_conserto->qtde_consertado;
				$total_substituido+=$rs_costura_conserto->qtde_substituido;
				$total_baixa+=$rs_costura_conserto->qtde_baixa;
				$total_devolvido+=$rs_costura_conserto->qtde_devolvido;
				
				$i++;
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->Cell(4.5, 0.55, "", 0, 0, "L", 0);
			$pdf->Cell(2.5, 0.55, $total_recebido, 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.55, $total_consertado, 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.55, $total_substituido, 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.55, $total_baixa, 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.55, $total_devolvido, 1, 1, "C", !$fill);
			
			$pdf->Ln();$pdf->Ln();
			
		}//fim por peça geral
		
	}
	//anual
	elseif ($_POST["tipo_relatorio"]=="a") {
		
		switch ($_POST["periodicidade"]) {
			case 1: 
					$per_mes1= 1;
					
					$data_teste_mk= mktime(14, 0, 0, 3, 1, $_POST["periodo"]);
					$dias_mes= date("t", $data_teste_mk);
					
					$per_dia2= $dias_mes;
					$per_mes2= 3;
					break;
			case 2: 
					$per_mes1= 4;
					
					$data_teste_mk= mktime(14, 0, 0, 6, 1, $_POST["periodo"]);
					$dias_mes= date("t", $data_teste_mk);
					
					$per_dia2= $dias_mes;
					
					$per_dia2= $dias_mes;
					$per_mes2= 6;
					break;
			case 3: 
					$per_mes1= 7;
					
					$data_teste_mk= mktime(14, 0, 0, 9, 1, $_POST["periodo"]);
					$dias_mes= date("t", $data_teste_mk);
					
					$per_dia2= $dias_mes;
					
					$per_mes2= 9;
					break;
			case 4: 
					$per_mes1= 10;
					
					$data_teste_mk= mktime(14, 0, 0, 12, 1, $_POST["periodo"]);
					$dias_mes= date("t", $data_teste_mk);
					
					$per_dia2= $dias_mes;
					$per_mes2= 12;
					break;
			default:
					$per_mes1= 1;
					
					$data_teste_mk= mktime(14, 0, 0, 12, 1, $_POST["periodo"]);
					$dias_mes= date("t", $data_teste_mk);
					
					$per_dia2= $dias_mes;
					$per_mes2= 12;
					break;
		}
		
		$ponto_inicio= date("Y-m-d", mktime(14, 0, 0, $per_mes1, 1, $_POST["periodo"]));
		$ponto_fim= date("Y-m-d", mktime(14, 0, 0, $per_mes2, $per_dia2, $_POST["periodo"]));
		
		if ($_POST["id_contrato"]!="") $str= " and   pessoas.id_contrato= '". $_POST["id_contrato"] ."'  ";
		
		if ($_POST["modo"]==1) {
		
			$result= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
										where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
										and   pessoas_tipos.tipo_pessoa = 'c'
										and   pessoas.status_pessoa = '1'
										and   pessoas.id_cliente_tipo = '1'
										$str
										order by 
										pessoas.apelido_fantasia asc
										") or die(mysql_error());
			
			while ($rs= mysql_fetch_object($result)) {
				
				$pdf->AddPage();
				
				$pdf->SetXY(7,1.75);
				
				if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg"))
					$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 2, 1.3, 5, 1.9287);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.6, "CONTROLE DE COSTURA", 0 , 1, 'R');
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.6, pega_sigla_pessoa($rs->id_cliente) ." (". pega_periodicidade_anual($_POST["periodicidade"]) . " ". $_POST["periodo"] .")", 0 , 1, 'R');
				
				$pdf->Ln();$pdf->Ln();
			
				$result_costura_conserto_pecas= mysql_query("select distinct(op_limpa_costura_consertos_pecas.id_tipo_roupa)
																from op_limpa_costura_consertos_pecas, op_limpa_costura_consertos, op_limpa_pecas
																where op_limpa_costura_consertos.id_costura_conserto = op_limpa_costura_consertos_pecas.id_costura_conserto
																/* and   DATE_FORMAT(op_limpa_costura_consertos.data_entrega, '%m/%Y') = '". $_POST["periodo"] ."' */
																and   op_limpa_costura_consertos.data_entrega >= '". $ponto_inicio ."'
																and   op_limpa_costura_consertos.data_entrega <= '". $ponto_fim ."'
																and   op_limpa_costura_consertos.id_cliente = '". $rs->id_cliente ."'
																and   op_limpa_costura_consertos_pecas.id_tipo_roupa = op_limpa_pecas.id_peca
																order by op_limpa_pecas.peca asc
																") or die(mysql_error());
												
				$pdf->SetFillColor(200,200,200);		
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				
				$pdf->Cell(4.5, 0.6, "TIPO DE ROUPA", 1, 0, "L", 1);
				$pdf->Cell(2.5, 0.6, "RECEBIDO", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "CONSERTADO", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "SUBSTITUÍDO", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "BAIXA", 1, 0, "C", 1);
				$pdf->Cell(2.5, 0.6, "DEVOLVIDO", 1, 1, "C", 1);
				
				$total_recebido= 0;
				$total_consertado= 0;
				$total_substituido= 0;
				$total_baixa= 0;
				$total_devolvido= 0;
				
				$pdf->SetFont('ARIALNARROW', '', 9);
				$pdf->SetFillColor(235,235,235);
				
				$i=0;
				
				while ($rs_costura_conserto_pecas= mysql_fetch_object($result_costura_conserto_pecas)) {
					if (($i%2)==0) $fill=1;
					else $fill= 0;
					
					$result_costura_conserto= mysql_query("select sum(qtde_recebido) as qtde_recebido, sum(qtde_consertado) as qtde_consertado,
																  sum(qtde_substituido) as qtde_substituido, sum(qtde_baixa) as qtde_baixa, sum(qtde_devolvido) as qtde_devolvido
																		from op_limpa_costura_consertos_pecas, op_limpa_costura_consertos
																		where op_limpa_costura_consertos.id_costura_conserto = op_limpa_costura_consertos_pecas.id_costura_conserto
																		and   op_limpa_costura_consertos.id_cliente = '". $rs->id_cliente ."'
																		and   op_limpa_costura_consertos_pecas.id_tipo_roupa = '". $rs_costura_conserto_pecas->id_tipo_roupa ."' 
																		/* and   DATE_FORMAT(op_limpa_costura_consertos.data_entrega, '%m/%Y') = '". $_POST["periodo"] ."' */
																		and   op_limpa_costura_consertos.data_entrega >= '". $ponto_inicio ."'
																		and   op_limpa_costura_consertos.data_entrega <= '". $ponto_fim ."'
																		") or die(mysql_error());
					
					$rs_costura_conserto= mysql_fetch_object($result_costura_conserto);
					
					$pdf->Cell(4.5, 0.55, pega_pecas_roupa($rs_costura_conserto_pecas->id_tipo_roupa), 1, 0, "L", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_recebido, 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_consertado, 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_substituido, 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_baixa, 1, 0, "C", $fill);
					$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_devolvido, 1, 1, "C", $fill);
					
					$total_recebido+=$rs_costura_conserto->qtde_recebido;
					$total_consertado+=$rs_costura_conserto->qtde_consertado;
					$total_substituido+=$rs_costura_conserto->qtde_substituido;
					$total_baixa+=$rs_costura_conserto->qtde_baixa;
					$total_devolvido+=$rs_costura_conserto->qtde_devolvido;
					
					$i++;
				}
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				
				$pdf->Cell(4.5, 0.55, "", 0, 0, "L", 0);
				$pdf->Cell(2.5, 0.55, $total_recebido, 1, 0, "C", !$fill);
				$pdf->Cell(2.5, 0.55, $total_consertado, 1, 0, "C", !$fill);
				$pdf->Cell(2.5, 0.55, $total_substituido, 1, 0, "C", !$fill);
				$pdf->Cell(2.5, 0.55, $total_baixa, 1, 0, "C", !$fill);
				$pdf->Cell(2.5, 0.55, $total_devolvido, 1, 1, "C", !$fill);
				
				$pdf->Ln();$pdf->Ln();
				
			}
		}//fim por cliente
		
		//por peça geral
		else {
			
			$pdf->AddPage();
				
			$pdf->SetXY(7,1.75);
			
			if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg"))
				$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 2, 1.3, 5, 1.9287);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "CONTROLE DE COSTURA", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "POR TIPO DE PEÇA (". pega_periodicidade_anual($_POST["periodicidade"]) . " ". $_POST["periodo"] .")", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$result_costura_conserto_pecas= mysql_query("select distinct(op_limpa_costura_consertos_pecas.id_tipo_roupa)
															from op_limpa_costura_consertos_pecas, op_limpa_costura_consertos, op_limpa_pecas, pessoas, pessoas_tipos
															where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
															and   pessoas_tipos.tipo_pessoa = 'c'
															and   pessoas.status_pessoa = '1'
															and   pessoas.id_cliente_tipo = '1'
															$str
															and   op_limpa_costura_consertos.id_costura_conserto = op_limpa_costura_consertos_pecas.id_costura_conserto
															/* and   DATE_FORMAT(op_limpa_costura_consertos.data_entrega, '%m/%Y') = '". $_POST["periodo"] ."' */
															and   op_limpa_costura_consertos.data_entrega >= '". $ponto_inicio ."'
															and   op_limpa_costura_consertos.data_entrega <= '". $ponto_fim ."'
															and   pessoas.id_pessoa = op_limpa_costura_consertos.id_cliente
															and   op_limpa_costura_consertos_pecas.id_tipo_roupa = op_limpa_pecas.id_peca
															order by op_limpa_pecas.peca asc
															") or die(mysql_error());
											
			$pdf->SetFillColor(200,200,200);		
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->Cell(4.5, 0.6, "TIPO DE ROUPA", 1, 0, "L", 1);
			$pdf->Cell(2.5, 0.6, "RECEBIDO", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.6, "CONSERTADO", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.6, "SUBSTITUÍDO", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.6, "BAIXA", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.6, "DEVOLVIDO", 1, 1, "C", 1);
			
			$total_recebido= 0;
			$total_consertado= 0;
			$total_substituido= 0;
			$total_baixa= 0;
			$total_devolvido= 0;
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->SetFillColor(235,235,235);
			
			$i=0;
			
			while ($rs_costura_conserto_pecas= mysql_fetch_object($result_costura_conserto_pecas)) {
				if (($i%2)==0) $fill=1;
				else $fill= 0;
				
				$result_costura_conserto= mysql_query("select sum(qtde_recebido) as qtde_recebido, sum(qtde_consertado) as qtde_consertado,
															  sum(qtde_substituido) as qtde_substituido, sum(qtde_baixa) as qtde_baixa, sum(qtde_devolvido) as qtde_devolvido
																	from op_limpa_costura_consertos_pecas, op_limpa_costura_consertos, pessoas, pessoas_tipos
																	where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
																	and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
																	and   pessoas_tipos.tipo_pessoa = 'c'
																	and   pessoas.status_pessoa = '1'
																	and   pessoas.id_cliente_tipo = '1'
																	$str
																	and   op_limpa_costura_consertos.id_costura_conserto = op_limpa_costura_consertos_pecas.id_costura_conserto
																	and   pessoas.id_pessoa = op_limpa_costura_consertos.id_cliente
																	and   op_limpa_costura_consertos_pecas.id_tipo_roupa = '". $rs_costura_conserto_pecas->id_tipo_roupa ."' 
																	/* and   DATE_FORMAT(op_limpa_costura_consertos.data_entrega, '%m/%Y') = '". $_POST["periodo"] ."' */
																	and   op_limpa_costura_consertos.data_entrega >= '". $ponto_inicio ."'
																	and   op_limpa_costura_consertos.data_entrega <= '". $ponto_fim ."'
																	") or die(mysql_error());
				
				$rs_costura_conserto= mysql_fetch_object($result_costura_conserto);
				
				$pdf->Cell(4.5, 0.55, pega_pecas_roupa($rs_costura_conserto_pecas->id_tipo_roupa), 1, 0, "L", $fill);
				$pdf->Cell(2.5, 0.55, fnumi($rs_costura_conserto->qtde_recebido), 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, fnumi($rs_costura_conserto->qtde_consertado), 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, fnumi($rs_costura_conserto->qtde_substituido), 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_baixa, 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.55, $rs_costura_conserto->qtde_devolvido, 1, 1, "C", $fill);
				
				$total_recebido+=$rs_costura_conserto->qtde_recebido;
				$total_consertado+=$rs_costura_conserto->qtde_consertado;
				$total_substituido+=$rs_costura_conserto->qtde_substituido;
				$total_baixa+=$rs_costura_conserto->qtde_baixa;
				$total_devolvido+=$rs_costura_conserto->qtde_devolvido;
				
				$i++;
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->Cell(4.5, 0.55, "", 0, 0, "L", 0);
			$pdf->Cell(2.5, 0.55, $total_recebido, 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.55, $total_consertado, 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.55, $total_substituido, 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.55, $total_baixa, 1, 0, "C", !$fill);
			$pdf->Cell(2.5, 0.55, $total_devolvido, 1, 1, "C", !$fill);
			
			$pdf->Ln();$pdf->Ln();
			
		}//fim por peça geral
		
	}
	//comparativo
	elseif ($_POST["tipo_relatorio"]=="c") {
		
		
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
			
			$data1_mk= mktime(22, 0, 0, $periodo2[0], 1, $periodo2[1]);
			$dias_mes= date("t", $data1_mk);
			
			$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
			
			$data1= date("Y-m-d", $data1_mk);
			$data2= date("Y-m-d", $data2_mk);
			
			$data1f= desformata_data($data1);
			$data2f= desformata_data($data2);
		}
		
			
		$pdf->AddPage();
		
		$pdf->SetXY(7,1.75);
		
		if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg"))
			$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 2, 1.3, 5, 1.9287);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, "COMPARATIVO ENVIADO X RECEBIDO DE COSTURA", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, "ENTRE ". $data1f ." E ". $data2f, 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		$pdf->SetFillColor(200,200,200);		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(5, 0.6, "CLIENTE", 1, 0, "L", 1);
		$pdf->Cell(4, 0.6, "ENVIADAS (TRIAGEM)", 1, 0, "C", 1);
		$pdf->Cell(4, 0.6, "RECEBIDAS DO CLIENTE", 1, 0, "C", 1);
		$pdf->Cell(4, 0.6, "BAIXA", 1, 1, "C", 1);
		
		$total_recebido= 0;
		$total_consertado= 0;
		$total_substituido= 0;
		$total_baixa= 0;
		$total_pecas_enviadas_cliente=0;
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->SetFillColor(235,235,235);
		
		$i=0;
		while ($_POST["id_cliente"][$i]!="") {
			if (($i%2)==0) $fill=1;
			else $fill= 0;

			
			$result_costura_conserto= mysql_query("select sum(qtde_recebido) as qtde_recebido, sum(qtde_consertado) as qtde_consertado,
													sum(qtde_substituido) as qtde_substituido, sum(qtde_baixa) as qtde_baixa, sum(qtde_devolvido) as qtde_devolvido
													from op_limpa_costura_consertos_pecas, op_limpa_costura_consertos
													where op_limpa_costura_consertos.id_costura_conserto = op_limpa_costura_consertos_pecas.id_costura_conserto
													and   op_limpa_costura_consertos.id_cliente = '". $_POST["id_cliente"][$i] ."'
													/* and   op_limpa_costura_consertos_pecas.id_tipo_roupa = '". $rs_costura_conserto_pecas->id_tipo_roupa ."' */
													and   op_limpa_costura_consertos.data_chegada >= '". $data1 ."'
													and   op_limpa_costura_consertos.data_chegada <= '". $data2 ."'
													") or die(mysql_error());
			
			$rs_costura_conserto= mysql_fetch_object($result_costura_conserto);
			
			$total_recebido+= $rs_costura_conserto->qtde_recebido;
			//$total_baixa+= $rs_costura_conserto->qtde_baixa;
			
			
			$result_costura_envio= mysql_query("select sum(qtde) as qtde from op_limpa_costura
													where id_cliente = '". $_POST["id_cliente"][$i] ."'
													and   data_costura >= '". $data1 ."'
													and   data_costura <= '". $data2 ."'
													") or die(mysql_error());
			
			$rs_costura_envio= mysql_fetch_object($result_costura_envio);
					
			/*
						
			//pegar a ultima entrega do dia anterior
			$result_entrega_primeira= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
													where (tr_percursos.tipo = '2' )
													and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
													and   tr_percursos_passos.passo = '1'
													and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
													and   tr_percursos_clientes.id_cliente = '". $_POST["id_cliente"][$i] ."'
													and   tr_percursos_passos.data_percurso < '". $data1 ."'
													order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
													limit 1
													");
			
			$linhas_entrega_primeira= mysql_num_rows($result_entrega_primeira);
			$rs_entrega_primeira= mysql_fetch_object($result_entrega_primeira);
			
			//pegar a ultima entrega do dia anterior
			$result_entrega_ultima= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
													where (tr_percursos.tipo = '2' )
													and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
													and   tr_percursos_passos.passo = '1'
													and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
													and   tr_percursos_clientes.id_cliente = '". $_POST["id_cliente"][$i] ."'
													and   tr_percursos_passos.data_percurso <= '". $data2 ."'
													order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
													limit 1
													");
			
			$linhas_entrega_ultima= mysql_num_rows($result_entrega_ultima);
			$rs_entrega_ultima= mysql_fetch_object($result_entrega_ultima);
					
					
			$str_geral_periodo = "
								and op_limpa_pesagem.data_hora_pesagem < '". $rs_entrega_ultima->data_percurso ." ". $rs_entrega_ultima->hora_percurso ."'
								and op_limpa_pesagem.data_hora_pesagem > '". $rs_entrega_primeira->data_percurso ." ". $rs_entrega_primeira->hora_percurso ."'
								";
			
			$result_pesagem= mysql_query("select * from op_limpa_pesagem, op_limpa_pesagem_pecas
											where op_limpa_pesagem.id_pesagem = op_limpa_pesagem_pecas.id_pesagem
											and   op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   op_limpa_pesagem.id_cliente = '". $_POST["id_cliente"][$i] ."'
											and   op_limpa_pesagem.costura = '1'
											
											and   op_limpa_pesagem.extra = '0'
											$str_geral_periodo
											") or die(mysql_error());
			$linhas_pesagem= mysql_num_rows($result_pesagem);
			
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
			*/
			
			
			
			$total_pecas_enviadas_cliente+=$rs_costura_envio->qtde;
			
			$baixa_aqui= $rs_costura_envio->qtde-$rs_costura_conserto->qtde_recebido;
			
			$pdf->Cell(5, 0.55, pega_sigla_pessoa($_POST["id_cliente"][$i]), 1, 0, "L", $fill);
			$pdf->Cell(4, 0.55, $rs_costura_envio->qtde, 1, 0, "C", $fill);
			$pdf->Cell(4, 0.55, $rs_costura_conserto->qtde_recebido, 1, 0, "C", $fill);
			$pdf->Cell(4, 0.55, $baixa_aqui, 1, 1, "C", $fill);
			
			$i++;
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->SetFillColor(210,210,210);
		
		$total_baixa_geral= $total_pecas_enviadas_cliente-$total_recebido;
		
		$pdf->Cell(5, 0.55, "", 0, 0, "L", 0);
		$pdf->Cell(4, 0.55, $total_pecas_enviadas_cliente, 1, 0, "C", 1);
		$pdf->Cell(4, 0.55, $total_recebido, 1, 0, "C", 1);
		$pdf->Cell(4, 0.55, $total_baixa_geral, 1, 1, "C",1);
		
		$pdf->Ln();$pdf->Ln();
		
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("costura_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>