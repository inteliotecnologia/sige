<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("i", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 2.25);
	$pdf->SetFillColor(200,200,200);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	if ( ($_POST["data1"]!="") || ($_POST["data2"]!="") ) {
		$data1= $_POST["data1"];
		$data2= $_POST["data2"];
	}
	else {
		if ( ($_GET["data1"]!="") || ($_GET["data2"]!="") ) {
			$data1= $_GET["data1"];
			$data2= $_GET["data2"];
		}
	}
	
	if ( ($data1!="") || ($data2!="") ) {
		if ($data1!="") {
			$data1f= $data1;
			$data1= formata_data_hifen($data1);
		}
		if ($data2!="") {
			$data2f= $data2;
			$data2= formata_data_hifen($data2);
		}
		
		if ($data1!="") $data1_mk= faz_mk_data($data1);
		if ($data2!="") $data2_mk= faz_mk_data($data2);
	}
	elseif ($_POST["periodo"]!="") {
		$periodo= explode('/', $_POST["periodo"]);
		
		$data1_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
		$dias_mes= date("t", $data1_mk);
		
		$data2_mk= mktime(0, 0, 0, $periodo[0], $dias_mes, $periodo[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
	//if ($data1!="") $str .= " and   fi_notas.data_vencimento >= '$data1' ";
	//if ($data2!="") $str .= " and   fi_notas.data_vencimento <= '$data2' ";
	
	if ($data1!="") $str .= " and   fi_notas.id_nota IN
											(
											select id_nota from fi_notas_parcelas
											where data_vencimento >= '$data1'
											and   data_vencimento <= '$data2'
											)
											";
	
	
	
	if ( ($_POST["data1_emissao"]!="") || ($_POST["data2_emissao"]!="") ) {
		$data1_emissao= $_POST["data1_emissao"];
		$data2_emissao= $_POST["data2_emissao"];
	}
	else {
		if ( ($_GET["data1_emissao"]!="") || ($_GET["data2_emissao"]!="") ) {
			$data1_emissao= $_GET["data1_emissao"];
			$data2_emissao= $_GET["data2_emissao"];
		}
	}
	
	if ( ($data1_emissao!="") || ($data2_emissao!="") ) {
		if ($data1_emissao!="") {
			$data1f_emissao= $data1_emissao;
			$data1_emissao= formata_data_hifen($data1_emissao);
		}
		if ($data2_emissao!="") {
			$data2f_emissao= $data2_emissao;
			$data2_emissao= formata_data_hifen($data2_emissao);
		}
		
		if ($data1_emissao!="") $data1_mk_emissao= faz_mk_data($data1_emissao);
		if ($data2_emissao!="") $data2_mk_emissao= faz_mk_data($data2_emissao);
	}
	elseif ($_POST["periodo_emissao"]!="") {
		$periodo_emissao= explode('/', $_POST["periodo_emissao"]);
		
		$data1_mk_emissao= mktime(0, 0, 0, $periodo_emissao[0], 1, $periodo_emissao[1]);
		$dias_mes_emissao= date("t", $data1_mk_emissao);
		
		$data2_mk_emissao= mktime(0, 0, 0, $periodo_emissao[0], $dias_mes_emissao, $periodo_emissao[1]);
		
		$data1_emissao= date("Y-m-d", $data1_mk_emissao);
		$data2_emissao= date("Y-m-d", $data2_mk_emissao);
		
		$data1f_emissao= desformata_data($data1_emissao);
		$data2f_emissao= desformata_data($data2_emissao);
	}
	if ($data1_emissao!="") $str .= " and   fi_notas.data_emissao >= '$data1_emissao' ";
	if ($data2_emissao!="") $str .= " and   fi_notas.data_emissao <= '$data2_emissao' ";
	
	
	
	if ( ($_POST["data1_pagamento"]!="") || ($_POST["data2_pagamento"]!="") ) {
		$data1_pagamento= $_POST["data1_pagamento"];
		$data2_pagamento= $_POST["data2_pagamento"];
	}
	else {
		if ( ($_GET["data1_pagamento"]!="") || ($_GET["data2_pagamento"]!="") ) {
			$data1_pagamento= $_GET["data1_pagamento"];
			$data2_pagamento= $_GET["data2_pagamento"];
		}
	}
	
	if ( ($data1_pagamento!="") || ($data2_pagamento!="") ) {
		if ($data1_pagamento!="") {
			$data1f_pagamento= $data1_pagamento;
			$data1_pagamento= formata_data_hifen($data1_pagamento);
		}
		if ($data2_pagamento!="") {
			$data2f_pagamento= $data2_pagamento;
			$data2_pagamento= formata_data_hifen($data2_pagamento);
		}
		
		if ($data1_pagamento!="") $data1_mk_pagamento= faz_mk_data($data1_pagamento);
		if ($data2_pagamento!="") $data2_mk_pagamento= faz_mk_data($data2_pagamento);
	}
	elseif ($_POST["periodo_pagamento"]!="") {
		$periodo_pagamento= explode('/', $_POST["periodo_pagamento"]);
		
		$data1_mk_pagamento= mktime(0, 0, 0, $periodo_pagamento[0], 1, $periodo_pagamento[1]);
		$dias_mes_pagamento= date("t", $data1_mk_pagamento);
		
		$data2_mk_pagamento= mktime(0, 0, 0, $periodo_pagamento[0], $dias_mes_pagamento, $periodo_pagamento[1]);
		
		$data1_pagamento= date("Y-m-d", $data1_mk_pagamento);
		$data2_pagamento= date("Y-m-d", $data2_mk_pagamento);
		
		$data1f_pagamento= desformata_data($data1_pagamento);
		$data2f_pagamento= desformata_data($data2_pagamento);
	}
	if ($data1_pagamento!="") $str .= " and   fi_notas.id_nota IN
											(
											select id_nota from fi_notas_parcelas_pagamentos
											where data_pagamento >= '$data1_pagamento'
											and   data_pagamento <= '$data2_pagamento'
											)
											";
	
	/*if ($data2_pagamento!="") $str .= " and   fi_notas.id_nota IN
											(
											select id_nota from fi_notas_parcelas_pagamentos
											where data_pagamento <= '$data2_pagamento'
											)
											";*/
	
	
	if ($_POST["num_nota"]!="") $str .= " and fi_notas.num_nota like '%". $_POST["num_nota"] ."%' ";
	if ($_POST["id_cedente"]!="") $str .= " and fi_notas.id_cedente = '". $_POST["id_cedente"] ."' ";
	
	if ($_GET["status_nota"]!="") $status_nota= $_GET["status_nota"];
	if ($_POST["status_nota"]!="") $status_nota= $_POST["status_nota"];
	
	if ($_GET["tipo_nota"]!="") $tipo_nota= $_GET["tipo_nota"];
	if ($_POST["tipo_nota"]!="") $tipo_nota= $_POST["tipo_nota"];
	
	if ($tipo_nota=='p') {
		if ($status_nota==1) $tit2= "PAGAS";
		else $tit2= "À PAGAR";
		$tit3= "PAGO";
		$txt_cedente= "FORNECEDOR";
		$tipo_cedente= "f";
		$tit6= "A PAGAR";
		
	}
	else {
		if ($status_nota==1) $tit2= "RECEBIDAS";
		else $tit2= "À RECEBER";
		$tit3= "RECEBIDO";
		$txt_cedente= "CLIENTE";
		$tipo_cedente= "c";
		
		$tit6= "A RECEBER";
	}
	
	if ($status_nota=="0") $str.= " and   fi_notas.status_nota = '$status_nota' ";
	
	//if ($_POST["agrupar"]!="")
	$str .= " order by pessoas.nome_rz asc, fi_notas.data_vencimento desc";
	//else $str .= " order by fi_notas.data_emissao asc";
	
	//if ($_SESSION["id_usuario"]==13) { echo $sql; die(); }
	
	/*if ($_SESSION["id_usuario"]==13) :
		echo "select * from fi_notas, pessoas, pessoas_tipos
							where fi_notas.id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   fi_notas.tipo_nota = '$tipo_nota'
							and   fi_notas.id_cedente = pessoas.id_pessoa
							and   pessoas.id_pessoa = pessoas_tipos.id_pessoa 
							and   pessoas_tipos.tipo_pessoa = '$tipo_cedente'
							". $str ."
							
							";
		die();
	endif;
	*/
	
	$result= mysql_query("select * from fi_notas, pessoas, pessoas_tipos
							where fi_notas.id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   fi_notas.tipo_nota = '$tipo_nota'
							and   fi_notas.id_cedente = pessoas.id_pessoa
							and   pessoas.id_pessoa = pessoas_tipos.id_pessoa 
							and   pessoas_tipos.tipo_pessoa = '$tipo_cedente'
							". $str ."
							
							") or die(mysql_error());
	
	$i=0;
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "DUPLICATAS ". $tit2, 0 , 1, 'R');
	
	if ( (($data1!="") || ($data2!="")) && (($data1_emissao!="") || ($data2_emissao!="")) ) {
		$separador= " | ";
	}
	
	if (($data1!="") || ($data2!="")) {
		$periodo_nota= "VENCIMENTO: ";
		
		if (($data1!="") && ($data2!=""))
			$periodo_nota.= " ". $data1f ." A ". $data2f;
		elseif ($data1!="")
			$periodo_nota.= "A PARTIR DE ". $data1f;
		elseif ($data2!="")
			$periodo_nota.= "ATÉ ". $data2f;
		//else
		//	$periodo_nota= "TODO O PERÍODO";
	}
	
	if (($data1_emissao!="") || ($data2_emissao!="")) {
		$periodo_nota_emissao= "EMISSÃO: ";
		
		if (($data1_emissao!="") && ($data2_emissao!=""))
			$periodo_nota_emissao.= "". $data1f_emissao ." A ". $data2f_emissao;
		elseif ($data1_emissao!="")
			$periodo_nota_emissao.= "A PARTIR DE ". $data1f_emissao;
		elseif ($data2_emissao!="")
			$periodo_nota_emissao.= "ATÉ ". $data2f_emissao;
		//else
		//	$periodo_nota= "TODO O PERÍODO";
	}
	
	if (($data1_pagamento!="") || ($data2_pagamento!="")) {
		$periodo_nota_pagamento= "PAGAMENTO: ";
		
		if (($data1_pagamento!="") && ($data2_pagamento!=""))
			$periodo_nota_pagamento.= "". $data1f_pagamento ." A ". $data2f_pagamento;
		elseif ($data1_pagamento!="")
			$periodo_nota_pagamento.= "A PARTIR DE ". $data1f_pagamento;
		elseif ($data2_pagamento!="")
			$periodo_nota_pagamento.= "ATÉ ". $data2f_pagamento;
		//else
		//	$periodo_nota= "TODO O PERÍODO";
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
	$pdf->Cell(0, 0.6, $periodo_nota_emissao ." ". $separador ." ". $periodo_nota ." ". $separador ." ". $periodo_nota_pagamento, 0 , 1, 'R');

	$pdf->Ln();$pdf->Ln();
	
	if ($_POST["id_cedente"]!="") {
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(3, 0.6, $txt_cedente .":", 0, 0, 'L', 0);
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->Cell(14, 0.6, pega_pessoa($_POST["id_cedente"]), 0, 1, 'L', 0);
		
		$pdf->Ln();
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 6);
	
	//if ($status_nota==1)
	$largura_cedente= 10.8;
	//else $largura_cedente= 8;
	
	$pdf->Cell(1.5, 0.5, "Nº", 1, 0, 'C', 1);
	
	//if ($_POST["id_cedente"]=="")
	$pdf->Cell($largura_cedente, 0.5, $txt_cedente, 1, 0, 'L', 1);
	
	$pdf->Cell(1.4, 0.5, "EMISSÃO", 1, 0, 'C', 1);
	//$pdf->Cell(1.5, 0.5, "VENCIMENTO", 1, 0, 'C', 1);
	$pdf->Cell(1.2, 0.5, "PARCELAS", 1, 0, 'C', 1);
	$pdf->Cell(2, 0.5, "VALOR TOTAL", 1, 1, 'C', 1);
	
	$pdf->SetFont('ARIALNARROW', '', 6);
	$pdf->SetFillColor(240,240,240);
	$i=0;
	
	$linhas= mysql_num_rows($result);
	
	while ($rs= mysql_fetch_object($result)) {
		$j= $i+1;
		
		//echo $rs->id_nota ." ";
		
		$id_cedente[$i]= $rs->id_cedente;
		
		//echo $linhas ." ". $j;
		
		if ( ($i>0) && ($_POST["agrupar"]!="") && (($id_cedente[$i]!=$id_cedente[$i-1])) ) {
			
			//$pdf->Cell(0, 0.1, "", 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->Cell(6.5, 0.5, "", 0, 0, 'C', 0);
			//$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(1.85, 0.5, "R$ ". fnum($valor_total_pago_cedente[$id_cedente[$i-1]]), 'T', 0, 'R', 0);
			$pdf->Cell(1.85, 0.5, "R$ ". fnum($valor_total_pago_cedente2[$id_cedente[$i-1]]), 'T', 0, 'R', 0);
			$pdf->Cell(1.85, 0.5, "R$ ". fnum($juros_cedente[$id_cedente[$i-1]]), 'T', 1, 'R', 0);
			
			if ($tipo_nota=='p') $tit4= "A PAGAR";
			else $tit4= "A RECEBER";
			
			//se não pagou tudo, mostra...
			/*if ($saldo_total_cedente[$id_cedente[$i-1]]>0) {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
				$pdf->Cell(2.5, 0.4, $tit4 .":", 0, 0, 'L', 0);
				$pdf->SetFont('ARIALNARROW', '', 7);
				$pdf->Cell(3, 0.4, "R$ ". fnum($saldo_total_cedente[$id_cedente[$i-1]]), 0, 1, 'L', 0);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
				$pdf->Cell(2.5, 0.4, "TOTAL:", 0, 0, 'L', 0);
				$pdf->SetFont('ARIALNARROW', '', 7);
				$pdf->Cell(3, 0.4, "R$ ". fnum($valor_total_cedente[$id_cedente[$i-1]]), 0, 1, 'L', 0);
			}*/
			
			$pdf->Cell(0, 0.1, "", 'B', 1);
			
			$pdf->LittleLn();
			
			// ---------------------------------------------------------
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 6);
			$pdf->SetFillColor(200,200,200);
			
			$pdf->Cell(1.5, 0.5, "Nº", 1, 0, 'C', 1);
	
			//if ($_POST["id_cedente"]=="")
			$pdf->Cell($largura_cedente, 0.5, $txt_cedente, 1, 0, 'L', 1);
			
			$pdf->Cell(1.4, 0.5, "EMISSÃO", 1, 0, 'C', 1);
			//$pdf->Cell(1.5, 0.5, "VENCIMENTO", 1, 0, 'C', 1);
			$pdf->Cell(1.2, 0.5, "PARCELAS", 1, 0, 'C', 1);
			$pdf->Cell(2, 0.5, "VALOR TOTAL", 1, 1, 'C', 1);
			
			$pdf->SetFillColor(240,240,240);
			
			// ---------------------------------------------------------
			
		}
		
		if (($i%2)!=0) $fill=1;
		else $fill= 0;
		
		$pdf->SetFont('ARIALNARROW', '', 7);
		
		$valor_total_nota= pega_valor_total_nota($rs->id_nota);
		//$primeiro_vencimento= pega_primeiro_vencimento_nota($rs->id_nota);
		$num_parcelas= pega_num_parcelas_nota($rs->id_nota);
		//$valor_pago= pega_valor_total_pagamento_nota($rs->id_nota, $data1_pagamento, $data2_pagamento);
		
		if ($_SESSION["id_usuario"]!=13) {
			if (strlen($rs->num_nota)>9) $num_nota_aqui= substr($rs->num_nota, 0, 9) ."...";
			else $num_nota_aqui= $rs->num_nota;
		}
		else $num_nota_aqui= $rs->id_nota;
		
		$pdf->Cell(1.5, 0.6, $num_nota_aqui, 1, 0, 'C', $fill);
		//if ($_POST["id_cedente"]=="")
		
		$pessoa_aqui= pega_pessoa($rs->id_cedente);
		
		if (strlen($pessoa_aqui)>74) $nome_pessoa_aqui= substr($pessoa_aqui, 0, 74) ."...";
		else $nome_pessoa_aqui= $pessoa_aqui;
		
		$pdf->Cell($largura_cedente, 0.6, $nome_pessoa_aqui, 1, 0, 'L', $fill);
		
		$pdf->Cell(1.4, 0.6, desformata_data($rs->data_emissao), 1, 0, 'C', $fill);
		//$pdf->Cell(1.5, 0.6, desformata_data($primeiro_vencimento), 1, 0, 'C', $fill);
		$pdf->Cell(1.2, 0.6, $num_parcelas, 1, 0, 'C', $fill);
		$pdf->Cell(2, 0.6, "R$ ". fnum($valor_total_nota), 1, 1, 'C', $fill);
		
		if ($status_nota==1) {
			
			if ($data1_pagamento!="") $str_pag = " and fi_notas_parcelas_pagamentos.data_pagamento >= '$data1_pagamento'
												   and fi_notas_parcelas_pagamentos.data_pagamento <= '$data2_pagamento'
													";
													
			$result_parcela= mysql_query("select * from fi_notas_parcelas, fi_notas_parcelas_pagamentos
										  	where fi_notas_parcelas.id_nota = '". $rs->id_nota ."'
										  	and   fi_notas_parcelas.id_parcela = fi_notas_parcelas_pagamentos.id_parcela
										  	and   fi_notas_parcelas.id_nota = fi_notas_parcelas_pagamentos.id_nota
										  	$str_pag
											and   fi_notas_parcelas.status_parcela = '1'
											");
			$linhas_parcela= mysql_num_rows($result_parcela);
			
			if ($linhas_parcela>0) {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 6);
				
				$pdf->Cell(0, 0.1, "", 0, 1);
				
				$pdf->Cell(1.5, 0.4, "", 0, 0);
				$pdf->Cell(1.5, 0.4, "PARCELA", 0, 0, 'L', 1);
				
				$pdf->Cell(1.75, 0.4, "PAGAMENTO", 0, 0, 'C', 1);
				$pdf->Cell(1.75, 0.4, "VENCIMENTO", 0, 0, 'C', 1);
				$pdf->Cell(1.85, 0.4, "VALOR", 0, 0, 'R', 1);
				$pdf->Cell(1.85, 0.4, "PAGO", 0, 0, 'R', 1);
				$pdf->Cell(1.85, 0.4, "JUROS", 0, 1, 'R', 1);
				
				//$pdf->Cell(1.6, 0.5, "SALDO", 'B', 1, 'R', 1);
			}
			
			while ($rs_parcela= mysql_fetch_object($result_parcela)) {
				
				$valor_total += $rs_parcela->valor_pago;
				$valor_total_cedente[$id_cedente[$i]] += $rs_parcela->valor_pago;
				
				$valor_total_pago += $rs_parcela->valor_pago;
				$valor_total_pago_cedente[$id_cedente[$i]] += $rs_parcela->valor;
				
				$valor_total_notas += $rs_parcela->valor;
				$valor_total_pago_cedente2[$id_cedente[$i]] += $rs_parcela->valor_pago;
				
				$juros_nota= $rs_parcela->valor_pago-$rs_parcela->valor;
				
				$juros_cedente[$id_cedente[$i]] += $juros_nota;
				$valor_total_juros += $juros_nota;
				
				$saldo_a_pagar= $valor_total_nota-$rs_parcela->valor_pago;
				
				if ($saldo_a_pagar>=0) {
					$saldo_total += $saldo_a_pagar;
					$saldo_total_cedente[$id_cedente[$i]] += $saldo_a_pagar;
					
					$saldo_str= "R$ ". fnum($saldo_a_pagar);
				}
				else {
					$saldo_juros_total += abs($saldo_a_pagar);
					$saldo_juros_total_cedente[$id_cedente[$i]] += abs($saldo_a_pagar);
					
					$saldo_str= "R$ ". fnum(abs($saldo_a_pagar)) ." J";
				}
				
				$pdf->SetFont('ARIALNARROW', '', 7);
				$pdf->Cell(1.5, 0.4, "", 0, 0);
				$pdf->Cell(1.5, 0.45, pega_num_parcela_nota($rs_parcela->id_nota, $rs_parcela->id_parcela), 0, 0, 'L', 0);
				$pdf->Cell(1.75, 0.45, desformata_data($rs_parcela->data_pagamento), 0, 0, 'C', 0);
				$pdf->Cell(1.75, 0.45, desformata_data($rs_parcela->data_vencimento), 0, 0, 'C', 0);
				$pdf->Cell(1.85, 0.45, "R$ ". fnum($rs_parcela->valor), 0, 0, 'R', 0);
				$pdf->Cell(1.85, 0.45, "R$ ". fnum($rs_parcela->valor_pago), 0, 0, 'R', 0);
				$pdf->Cell(1.85, 0.45, "R$ ". fnum($juros_nota), 0, 1, 'R', 0);
				
				$pdf->Cell(1, 0.1, "", 0, 1);
			}
			
			//$pdf->Cell(0, 0.1, "", 0, 1);
			
			/*$result_pagamento= mysql_query("select * from fi_notas_parcelas_pagamentos
										  	where id_nota = '". $rs->id_nota ."'
											and   id_parcela = '". $rs_parcela->id_parcela ."'
											order by data_pagamento desc limit 1
											");
			$rs_pagamento= mysql_fetch_object($result_pagamento);*/
			
			//$pdf->Cell(1.2, 0.6, desformata_data($rs_pagamento->data_pagamento), 1, 0, 'R', $fill);
		}
			
		//$pdf->Cell(1.6, 0.6, "R$ ". fnum($valor_pago), 1, 0, 'R', $fill);
		//$pdf->Cell(1.6, 0.6, $saldo_str, 1, 0, 'R', $fill);
		//$pdf->Cell(1.6, 0.6, "R$ ". fnum($valor_total_nota), 1, 1, 'R', $fill);
		
		/*if ( ($linhas==$j) && ($_POST["agrupar"]!="") ) {
			
			$pdf->Cell(0, 0.2, "", 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
			$pdf->Cell(2.5, 0.4, "". $tit3 .":", 0, 0, 'L', 0);
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(3, 0.4, "R$ ". fnum($valor_total_pago_cedente[$id_cedente[$i]]), 0, 0, 'L', 0);
			$pdf->Cell(3, 0.4, "R$ ". fnum($valor_total_pago_cedente2[$id_cedente[$i]]), 0, 1, 'L', 0);
			
			
			if ($tipo_nota=='p') $tit4= "A PAGAR";
			else $tit4= "A RECEBER";
			*/
			
			//se não pagou tudo, mostra...
			/*if ($saldo_total_cedente[$id_cedente[$i-1]]>0) {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
				$pdf->Cell(2.5, 0.4, $tit4 .":", 0, 0, 'L', 0);
				$pdf->SetFont('ARIALNARROW', '', 7);
				$pdf->Cell(3, 0.4, "R$ ". fnum($saldo_total_cedente[$id_cedente[$i]]), 0, 1, 'L', 0);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
				$pdf->Cell(2.5, 0.4, "TOTAL:", 0, 0, 'L', 0);
				$pdf->SetFont('ARIALNARROW', '', 7);
				$pdf->Cell(3, 0.4, "R$ ". fnum($valor_total_cedente[$id_cedente[$i]]), 0, 1, 'L', 0);
			}*/
			
			//$pdf->Ln();
		//}
		
		$i++;
	}
	
	$pdf->Ln();
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
	$pdf->Cell(2.5, 0.5, "TOTAL NOTAS:", 0, 0, 'L', 0);
	$pdf->SetFont('ARIALNARROW', '', 8);
	$pdf->Cell(3, 0.5, "R$ ". fnum($valor_total_notas), 0, 1, 'L', 0);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
	$pdf->Cell(2.5, 0.5, "TOTAL ". $tit3 .":", 0, 0, 'L', 0);
	$pdf->SetFont('ARIALNARROW', '', 8);
	$pdf->Cell(3, 0.5, "R$ ". fnum($valor_total_pago), 0, 1, 'L', 0);
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
	$pdf->Cell(2.5, 0.5, "JUROS:", 0, 0, 'L', 0);
	$pdf->SetFont('ARIALNARROW', '', 8);
	$pdf->Cell(3, 0.5, "R$ ". fnum($valor_total_juros), 0, 1, 'L', 0);
	
	/*if ($status_nota==1) $txt_saldo= "SALDO:";
	else $txt_saldo= "SALDO:";
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
	$pdf->Cell(2.5, 0.5, "SALDO ". $tit6 .":", 0, 0, 'L', 0);
	$pdf->SetFont('ARIALNARROW', '', 8);
	$pdf->Cell(3, 0.5, "R$ ". fnum($saldo_total), 0, 1, 'L', 0);
	
	if ($saldo_juros_total>0) {
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(2.5, 0.5, "JUROS:", 0, 0, 'L', 0);
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->Cell(3, 0.5, "R$ ". fnum($saldo_juros_total), 0, 1, 'L', 0);
	}
	*/
	
	/*
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
	$pdf->Cell(2.5, 0.5, "TOTAL GERAL:", 0, 0, 'L', 0);
	$pdf->SetFont('ARIALNARROW', '', 8);
	$pdf->Cell(3, 0.5, "R$ ". fnum($valor_total), 0, 1, 'L', 0);
	*/
	
	/*if ($status_nota==1) {
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(2.5, 0.5, "TOTAL PAGO:", 0, 0, 'L', 0);
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->Cell(3, 0.5, "R$ ". fnum($valor_total_pago), 0, 1, 'L', 0);
	}*/
	
	$pdf->AliasNbPages(); 
	$pdf->Output("nota_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>