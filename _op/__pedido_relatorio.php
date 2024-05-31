<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("pl", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH', 'includes/fpdf/font/');
	
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	else $id_cliente= $_GET["id_cliente"];
	
	if ($_POST["data"]!="") $data= $_POST["data"];
	else $data= $_GET["data"];
	
	if ($_POST["entrega"]!="") $entrega= $_POST["entrega"];
	else $entrega= $_GET["entrega"];
	
	$tipo= pega_tipo_pedido($id_cliente);
	
	$id_empresa_atendente_aqui= pega_empresa_atendente($id_cliente);
	
	$id_contrato= pega_id_contrato($id_cliente);
	
	//if ($id_empresa_atendente_aqui!="") $_SESSION["id_empresa_atendente2"]= $id_empresa_atendente_aqui;
	//else $_SESSION["id_empresa_atendente2"]= $_SESSION["id_empresa"];
	
	// GERAR NÚMERO DA NOTA DE ENTREGA
	
	inicia_transacao();
	$var= 0;
	
	
	
	$result_pedido_pre= mysql_query("select * from op_pedidos 
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_cliente = '$id_cliente'
									and   id_contrato = '$id_contrato'
									and   data_pedido = '". formata_data($data) ."'
									and   entrega = '$entrega'
									");
	$linhas_pedido_pre= mysql_num_rows($result_pedido_pre);
	
	//já tem o pedido, pega o número
	if ($linhas_pedido_pre==1) {
		$rs_pedido_pre= mysql_fetch_object($result_pedido_pre);
		
		$id_pedido_aqui= $rs_pedido_pre->num_pedido;
	}
	//não tem o pedido, cadastra
	else {
		
		$result_pedido= mysql_query("select * from op_pedidos 
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_contrato = '$id_contrato'
										order by id_pedido desc limit 1
										");
		
		$rs_pedido= mysql_fetch_object($result_pedido);
	
		$num_pedido= ($rs_pedido->num_pedido+1);
		
		$result_pedido_cria= mysql_query("insert into op_pedidos
											(id_empresa, id_contrato, num_pedido, id_cliente, data_pedido, entrega, id_usuario)
											values
											('". $_SESSION["id_empresa"] ."', '$id_contrato', '$num_pedido', '$id_cliente',
											'". formata_data($data) ."', '$entrega', '". $_SESSION["id_usuario"] ."')
											") or die(mysql_error());
		$id_pedido_aqui= $num_pedido;
	}
		
	// -------------------------------
	
	//$id_pedido= pega_id_pedido($_SESSION["id_empresa"], $id_cliente, $data, $_SESSION["id_usuario"]);
	
	if ($tipo==1) {
		$tabela= "op_limpa_pesagem";
		$obs= "Peso da roupa limpa.";
		
		$linha1= "CONTROLE OPERACIONAL";
		$linha2= "CONTRATO 543/2009";
		$linha3= "RELATÓRIO DE ENTREGA Nº ". $id_pedido_aqui;
	}
	else {
		$tabela= "op_suja_pesagem";
		$obs= "Peso da roupa suja.";
		
		$linha1= "PEDIDO DE LAVANDERIA ". $id_pedido_aqui;
		$linha2= "FORMULÁRIO DE COLETA E ENTREGA";
		$linha3= "";
	}
	
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
	
	$amanha= soma_data($data, 1, 0, 0);
		
	
	
	$altura_padrao= 13.5;

	$pdf->Ln();$pdf->Ln();
	
	for ($i=0; $i<2; $i++) {
		$pdf->AddPage();
		
		//$altura_nota=($altura_padrao*$i)+1.75;
		
		if (file_exists(CAMINHO ."empresa_". $id_empresa_atendente_aqui .".jpg"))
			$pdf->Image("". CAMINHO ."empresa_". $id_empresa_atendente_aqui .".jpg", 2, 1.5, 5, 1.9287);
		
		$pdf->SetXY(7,1.75);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $linha1, 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $linha2, 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $linha3, 0 , 1, 'R');
		
		$pdf->Ln(); $pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(1.5, 0.6, "CLIENTE:", 'LTB', 0, 'L');
		
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(11.5, 0.6, pega_pessoa($id_cliente), 'TB', 0, 'L');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(1.1, 0.6, "", 'TB', 0, 'L');
		
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(2.9, 0.6, "", 'TBR', 1, 'L');
		
		$pdf->Ln();
		
		//roupa limpa
		if ($tipo==1) {
				
			$entrega_aqui= $entrega-1;
			
			$result_entrega= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
											where tr_percursos.tipo = '2'
											and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
											and   tr_percursos_passos.passo = '1'
											and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
											and   tr_percursos_clientes.id_cliente = '". $id_cliente ."'
											and   tr_percursos_passos.data_percurso = '". formata_data($amanha) ."'
											order by tr_percursos_passos.data_percurso asc, tr_percursos_passos.hora_percurso asc
											
											
											");
			
			$linhas_entrega= mysql_num_rows($result_entrega);
			
			//echo $linhas_entrega;
			
			//se tem entrega cadastrada
			if ($linhas_entrega>0) {
				while ($rs_entrega= mysql_fetch_object($result_entrega)) {
					
					if ($entrega==1) {
						$str_geral = " and  
									 (
										
										( op_limpa_pesagem.data_pesagem = '". $rs_entrega->data_percurso ."' and op_limpa_pesagem.hora_pesagem < '". $rs_entrega->hora_percurso ."' )
										or
										( op_limpa_pesagem.data_pesagem < '". $rs_entrega->data_percurso ."' )
										
										
										
									 )
									";
					}
					else {
						$str_geral = " and  
									 (
										
										 ( op_limpa_pesagem.data_pesagem = '". $rs_entrega->data_percurso ."' and op_limpa_pesagem.hora_pesagem >= '". $rs_entrega->hora_percurso ."' )
										or
										( op_limpa_pesagem.data_pesagem > '". $rs_entrega->data_percurso ."' )
										
										
										
									 )
									";
					}
					
					break;
					
				}
				
				if ($linhas_entrega==0) {
					$var++;
					die ("Entrega <strong>". $entrega ."</strong> não encontrada!");
				}
			}
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->Cell(9.5, 0.5, "TIPO DE ROUPA", 1, 0, "L", 1);
			$pdf->Cell(2.5, 0.5, "NUM. PACOTES", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, "NUM. PEÇAS", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, "PESO", 1, 1, "C", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			
			$result_total= mysql_query("select sum(op_limpa_pesagem.peso) as peso_total from op_limpa_pesagem, op_suja_remessas
												where op_suja_remessas.id_remessa = op_limpa_pesagem.id_remessa
												and   op_suja_remessas.data_remessa = '". formata_data($data) ."'
												and   op_suja_remessas.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   op_limpa_pesagem.id_cliente = '". $id_cliente ."'
												$str_geral
												");
			$rs_total= mysql_fetch_object($result_total);
			$peso_total= $rs_total->peso_total;
			
			$result_pesagem_pecas= mysql_query("select distinct(op_limpa_pesagem.id_tipo_roupa)
												from op_limpa_pecas, op_limpa_pesagem, op_suja_remessas
												where op_suja_remessas.id_remessa = op_limpa_pesagem.id_remessa
												and   op_suja_remessas.data_remessa = '". formata_data($data) ."'
												and   op_limpa_pesagem.id_tipo_roupa = op_limpa_pecas.id_peca
												and   op_suja_remessas.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   op_limpa_pesagem.id_cliente = '". $id_cliente ."'
												$str_geral
												order by op_limpa_pecas.peca asc
												") or die(mysql_error());
			
			$pdf->SetFillColor(235,235,235);
			
			$peso_total_peca_geral= 0;
			$total_pacotes_peca_geral= 0;
			$total_pecas_peca_geral= 0;
			
			$j=0;
			while ($rs_pesagem_pecas= mysql_fetch_object($result_pesagem_pecas)) {
				
				if (($j%2)==0) $fill=0; else $fill= 1;
				
				$result_pesagem= mysql_query("select *
												from op_limpa_pesagem, op_suja_remessas
												where op_suja_remessas.id_remessa = op_limpa_pesagem.id_remessa
												and   op_suja_remessas.data_remessa = '". formata_data($data) ."'
												and   op_suja_remessas.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   op_limpa_pesagem.id_cliente = '". $id_cliente ."'
												and   op_limpa_pesagem.id_tipo_roupa = '". $rs_pesagem_pecas->id_tipo_roupa ."'
												$str_geral
												");
				
				$peso_total_peca= 0;
				$total_pacotes_peca= 0;
				$total_pecas_peca= 0;
				
				while ($rs_pesagem= mysql_fetch_object($result_pesagem)) {
					$peso_total_peca += $rs_pesagem->peso;
					$total_pacotes_peca += $rs_pesagem->num_pacotes;
					
					if ($rs_pesagem->qtde_pecas_sobra>0) $total_pacotes_peca++;
					
					$total_pecas_aqui= ($rs_pesagem->qtde_pacote*$rs_pesagem->num_pacotes)+$rs_pesagem->qtde_pecas_sobra;
					$total_pecas_peca += $total_pecas_aqui;
				}
					
				$pdf->Cell(9.5, 0.45, pega_pecas_roupa($rs_pesagem_pecas->id_tipo_roupa), 1, 0, "L", $fill);
				$pdf->Cell(2.5, 0.45, $total_pacotes_peca, 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.45, $total_pecas_peca, 1, 0, "C", $fill);
				$pdf->Cell(2.5, 0.45, fnum($peso_total_peca) ." kg", 1, 1, "C", $fill);
				
				$peso_total_peca_geral += $peso_total_peca;
				$total_pacotes_peca_geral += $total_pacotes_peca;
				$total_pecas_peca_geral += $total_pecas_peca;
				
				$j++;
			}
			
			$pdf->SetFillColor(210,210,210);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
			$pdf->Cell(9.5, 0.5, "", 0, 0, "L", 0);
			$pdf->Cell(2.5, 0.5, $total_pacotes_peca_geral, 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, $total_pecas_peca_geral, 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, fnum($peso_total_peca_geral) ." kg", 1, 1, "C", 1);
		}
		//roupa suja
		else {
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(4, 0.5, "PESO", 1, 0, "C", 1);
			$pdf->Cell(13, 0.5, "DESCRIÇÃO DO SERVIÇO", 1, 1, "C", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			
			$result_remessa= mysql_query("select * from op_suja_remessas
											where data_remessa = '". formata_data($data) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											");
			
			$peso_total= 0;
		
			while ($rs_remessa= mysql_fetch_object($result_remessa)) {
				$result_pesagem= mysql_query("select sum(peso) as total from ". $tabela ."
												where id_remessa = '". $rs_remessa->id_remessa ."'
												and   id_cliente = '". $id_cliente ."'
												");
				
				$rs_pesagem= mysql_fetch_object($result_pesagem);
				
				$peso_total+= $rs_pesagem->total;
			}
		
			$pdf->Cell(4, 0.45, fnum($peso_total), 1, 0, "C", 0);
			$pdf->Cell(13, 0.45, $obs, 1, 1, "C", 0);
		}
		
		$pdf->SetFillColor(210,210,210);
		
		$pdf->Ln();
		
		/*
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.5, "OBSERVAÇÕES", 1, 1, "C", 1);
		
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(0, 0.5, "", "LTR", 1, "L", 0);
		$pdf->Cell(0, 0.5, "", "LR", 1, "L", 0);
		$pdf->Cell(0, 0.5, "", "LR", 1, "L", 0);
		$pdf->Cell(0, 0.5, "", "LRB", 1, "L", 0);
		
		
		$pdf->Cell(0, 0.5, "", 0, 1, "C", 0);
		*/
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		
		$pdf->Cell(4, 0.5, "PESO TOTAL", 1, 0, "C", 1);
		$pdf->Cell(6.5, 0.5, "PROTOCOLO DE COLETA", 1, 0, "C", 1);
		$pdf->Cell(6.5, 0.5, "PROTOCOLO DE ENTREGA", 1, 1, "C", 1);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(4, 0.5, "", "TRL", 0, "L", 0);
		$pdf->Cell(6.5, 0.5, "DATA: ". $data, 1, 0, "L", 0);
		$pdf->Cell(6.5, 0.5, "DATA: ". soma_data($data, 1, 0, 0) ." ENTREGA Nº ". $entrega, 1, 1, "L", 0);
		
		$pdf->SetFont('ARIALNARROW', '', 16);
		$pdf->Cell(4, 0.5, fnum($peso_total) ." kg", "RL", 0, "C", 0);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(6.5, 0.5, "ASS. CLIENTE:", 1, 0, "L", 0);
		$pdf->Cell(6.5, 0.5, "ASS. CLIENTE:", 1, 1, "L", 0);
		
		$pdf->Cell(4, 0.5, "", "BRL", 0, "L", 0);
		$pdf->Cell(6.5, 0.5, "ASS. EMPRESA:", 1, 0, "L", 0);
		$pdf->Cell(6.5, 0.5, "ASS. EMPRESA:", 1, 1, "L", 0);
	}
	
	finaliza_transacao($var);
	
	// -------------------------------------------------------------------------
	
	if ($id_contrato==2) {
		$pdf->AddPage();
				
		$altura_padrao= 13;
	
		$pdf->Ln();$pdf->Ln();
		
		for ($i=0; $i<1; $i++) {
			
			$altura_nota=($altura_padrao*$i)+1.5;
			
			if (file_exists(CAMINHO ."empresa_". $id_empresa_atendente_aqui .".jpg"))
				$pdf->Image("". CAMINHO ."empresa_". $id_empresa_atendente_aqui .".jpg", 2, ($altura_nota-0.3), 5, 1.9287);
			
			$pdf->SetXY(7,$altura_nota);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
			$pdf->Cell(0, 0.6, "FORMULÁRIO DE REGISTRO OPERACIONAL - CONTRATO 543/2009", 0, 1, 'C');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
			$pdf->Cell(5, 0.6, "", 0, 0, 'C');
			$pdf->Cell(0, 0.6, "CONTROLE DE ENTREGA DE ROUPA LIMPA", 0, 1, 'C');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
			$pdf->Cell(5, 0.6, "", 0, 0, 'C');
			$pdf->Cell(0, 0.6, "", 0, 1, 'C');
			
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(1.5, 0.8, "CLIENTE:", 'LTB', 0, 'L');
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(11.5, 0.8, "", 'TB', 0, 'L');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(1.1, 0.8, "DATA:", 'TB', 0, 'L');
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(2.9, 0.8, "", 'TBR', 1, 'L');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(1.5, 0.8, "REMESSA:", 'LB', 0, 'L');
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(7, 0.8, "", 'B', 0, 'L');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(1.5, 0.8, "COLETA:", 'B', 0, 'L');
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(7, 0.8, "", 'BR', 1, 'L');
			
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(9.5, 0.5, "TIPO DE ROUPA", 1, 0, "L", 1);
			$pdf->Cell(2.5, 0.5, "NUM. PACOTES", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, "NUM. PEÇAS", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, "PESO", 1, 1, "C", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			
			/*
			$result_remessa= mysql_query("select * from op_suja_remessas
											where data_remessa = '". formata_data($data) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											");
			
			$peso_total= 0;
		
			while ($rs_remessa= mysql_fetch_object($result_remessa)) {
				$result_pesagem= mysql_query("select sum(peso) as total from ". $tabela ."
												where id_remessa = '". $rs_remessa->id_remessa ."'
												and   id_cliente = '". $id_cliente ."'
												");
				
				$rs_pesagem= mysql_fetch_object($result_pesagem);
				
				$peso_total+= $rs_pesagem->total;
			}
			*/
			
			for ($j=0; $j<24; $j++) {
			
				$pdf->Cell(9.5, 0.7, "", 1, 0, "L", 0);
				$pdf->Cell(2.5, 0.7, "", 1, 0, "C", 0);
				$pdf->Cell(2.5, 0.7, "", 1, 0, "C", 0);
				$pdf->Cell(2.5, 0.7, "", 1, 1, "C", 0);
				
			}
			
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Ln();
			
			/*
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 0.5, "OBSERVAÇÕES", 1, 1, "C", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.5, "", "LTR", 1, "L", 0);
			$pdf->Cell(0, 0.5, "", "LR", 1, "L", 0);
			$pdf->Cell(0, 0.5, "", "LR", 1, "L", 0);
			$pdf->Cell(0, 0.5, "", "LRB", 1, "L", 0);
			
			
			$pdf->Cell(0, 0.5, "", 0, 1, "C", 0);
			*/
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				
			$pdf->Cell(4, 0.6, "PESO TOTAL", 1, 0, "C", 1);
			$pdf->Cell(13, 0.6, "PROTOCOLO DE ENTREGA", 1, 1, "C", 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->Cell(4, 0.6, "", "LR", 0, "C", 0);
			$pdf->Cell(13, 0.6, "ASS. CLIENTE:", 1, 1, "L", 0);
			
			$pdf->Cell(4, 0.6, "", "RL", 0, "L", 0);
			$pdf->Cell(13, 0.6, "ASS. EMPRESA:", 1, 1, "L", 0);
			
			$pdf->Cell(4, 0.6, "", "BRL", 0, "L", 0);
			$pdf->Cell(13, 0.6, "CONFERIDO POR:", 1, 1, "L", 0);
		}
	}
	// -------------------------------------------------------------------------
	
	$pdf->AliasNbPages(); 
	$pdf->Output("pedido_lavanderia_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]="";
}
?>