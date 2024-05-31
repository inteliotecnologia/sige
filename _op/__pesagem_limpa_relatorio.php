<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("psl", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	
	if ($_POST["tipo_relatorio"]=="d") {
		require("includes/fpdf/modelo_retrato.php");
		$pdf=new PDF("P", "cm", "A4");
		$pdf->SetMargins(2, 3, 2);
		$pdf->SetAutoPageBreak(true, 3);
		$pdf->SetFillColor(200,200,200);
		$pdf->AddFont('ARIALNARROW');
		$pdf->AddFont('ARIAL_N_NEGRITO');
		$pdf->AddFont('ARIAL_N_ITALICO');
		$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
		$pdf->SetFont('ARIALNARROW');
		
		$i=0;
		
		$pdf->AddPage();
		
		$pdf->SetXY(7,1.75);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "PESAGEM DE ROUPA LIMPA", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, "". $_POST["data"], 0 , 1, 'R');
	
		$pdf->Ln();$pdf->Ln();
		
		if ($_POST["data"]!="") $data= formata_data_hifen($_POST["data"]);
		else $data= date("Y-m-d");
		
		if ($_POST["id_cliente"]!="") $str= " and   id_cliente= '". $_POST["id_cliente"] ."' ";
		
		if ($_POST["id_tipo_roupa"]!="") $str= " and   id_tipo_roupa= '". $_POST["id_tipo_roupa"] ."' ";
		
		if ($_POST["id_turno"]!="") $str= " and   id_tipo_roupa= '". $_POST["id_tipo_roupa"] ."' ";
		
		$result= mysql_query("select * from op_limpa_pesagem
										where op_limpa_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   op_limpa_pesagem.data_pesagem = '". formata_data($data) ."'
										and   extra = '0'
										". $str ."
										order by op_limpa_pesagem.data_pesagem asc, op_limpa_pesagem.hora_pesagem asc
										");
										
										
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell(3, 0.5, "HORA", 1, 0, 'C', 1);
		$pdf->Cell(2.5, 0.5, "CLIENTE", 1, 0, 'C', 1);
		$pdf->Cell(2, 0.5, "PESO", 1, 0, 'C', 1);
		$pdf->Cell(10, 0.5, "TIPO DE ROUPA", 1, 1, 'L', 1);
		
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->SetFillColor(235,235,235);
		$i=0;
		
		while ($rs= mysql_fetch_object($result)) {
			if (($i%2)==0) $fill=1;
			else $fill= 0;
			
			$peso_total += $rs->peso;
			
			$result_pesagens_pecas= mysql_query("select * from op_limpa_pesagem_pecas
													where id_pesagem = '". $rs->id_pesagem ."'
													order by id_pesagem_peca asc
													");
			$linhas_pesagens_pecas= mysql_num_rows($result_pesagens_pecas);
			
			$k=1;
			$pecas_roupa= "";
			
			while ($rs_pesagens_pecas= mysql_fetch_object($result_pesagens_pecas)) {
				$pecas_roupa.= pega_pecas_roupa($rs_pesagens_pecas->id_tipo_roupa);
				
				if ($k!=$linhas_pesagens_pecas) $pecas_roupa.= ", ";
				
				$k++;
			}
			
			if (strlen($pecas_roupa)>100) $pecas_roupa_mostra= substr($pecas_roupa, 0, 100). "...";
			else $pecas_roupa_mostra= $pecas_roupa;
			
			$pdf->Cell(3, 0.5, desformata_data($rs->data_pesagem) ." ". $rs->hora_pesagem, 1, 0, 'C', $fill);
			$pdf->Cell(2.5, 0.5, pega_sigla_pessoa($rs->id_cliente), 1, 0, 'C', $fill);
			$pdf->Cell(2, 0.5, fnum($rs->peso) ." kg", 1, 0, 'C', $fill);
			
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(10, 0.5, $pecas_roupa_mostra, 1, 1, 'L', $fill);
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			
			$i++;
		}
		
		$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		$pdf->Cell(2.2, 0.5, "PESO TOTAL:", 0, 0, 'L', 0);
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->Cell(3, 0.5, fnum($peso_total) ." kg", 0, 1, 'L', 0);
	}//fim diário
	
	else {
		require("includes/fpdf/modelo_paisagem_estendido.php");	
		
		$pdf=new PDF("L", "cm", "A4");
		$pdf->SetMargins(0.75, 0.75, 0.75);
		$pdf->SetAutoPageBreak(true, 1);
		$pdf->SetFillColor(230,230,230);
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
			
			$data1_mk= mktime(22, 0, 0, $periodo2[0], 1, $periodo2[1]);
			$dias_mes= date("t", $data1_mk);
			
			$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
			
			$data1= date("Y-m-d", $data1_mk);
			$data2= date("Y-m-d", $data2_mk);
			
			$data1f= desformata_data($data1);
			$data2f= desformata_data($data2);
		}
		
		$pdf->AddPage();
		
		$pdf->SetXY(7,1);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "PESAGEM DE ROUPA LIMPA (EM KG)", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $data1f ." à ". $data2f, 0 , 1, 'R');
	
		$pdf->Ln();$pdf->Ln();
		
		$total_dias_mes= date("t", $data2_mk);
	
		$largura= (24.9/$total_dias_mes);
			
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->SetFillColor(200,200,200);
		
		$pdf->Cell(2, 0.6, "CLIENTES", 1 , 0, "L", 1);
		
		$diferenca= ceil(($data2_mk-$data1_mk)/86400);
		
		for ($d=0; $d<=$diferenca; $d++) {
			$calculo_data= $data1_mk+(86400*$d);
			$data= date("d/m/Y", $calculo_data);
			$dia= date("d", $calculo_data);
			$data_valida= date("Y-m-d", $calculo_data);
			$id_dia= date("w", $calculo_data);
			
			$amanha= soma_data($data_valida, 1, 0, 0);
			$ontem= soma_data($data_valida, -1, 0, 0);
			$anteontem= soma_data($data_valida, -2, 0, 0);
			
			$pdf->Cell($largura, 0.6, $dia, 1, 0, "C", 1);
			//echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->Cell(1.2, 0.6, "Total", 1, 1, "C", 1);
		
		$total_geral_periodo= 0;
		
		if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
		if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
		if ($id_cliente!="") $str_cliente .= " and   pessoas.id_pessoa= '". $id_cliente ."' ";
		
		if ($_POST["id_turno"]!="") $id_cliente= $_POST["id_turno"];
		if ($_GET["id_turno"]!="") $id_turno= $_GET["id_turno"];
		if ($id_turno!="") $str .= " and   op_limpa_pesagem.id_turno= '". $id_turno ."' ";
		
		if ($_POST["id_tipo_roupa"]!="") $id_tipo_roupa= $_POST["id_tipo_roupa"];
		if ($_GET["id_tipo_roupa"]!="") $id_tipo_roupa= $_GET["id_tipo_roupa"];
		if ($id_tipo_roupa!="") $str .= " and   op_limpa_pesagem.id_tipo_roupa= '". $id_tipo_roupa ."' ";
		
		$result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
									where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
									and   pessoas_tipos.tipo_pessoa = 'c'
									and   pessoas.status_pessoa = '1'
									and   pessoas.id_cliente_tipo = '1'
									". $str_cliente ."
									order by 
									pessoas.apelido_fantasia asc
									") or die(mysql_error());
		$j=0;
		while ($rs_cli = mysql_fetch_object($result_cli)) {
			if (($j%2)==0) $fill= 0;
			else {
				$fill=1;
				$pdf->SetFillColor(230,230,230);
			}
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->Cell(2, 0.6, $rs_cli->sigla, 1, 0, "L", $fill);
			
			$total_mes_cliente= 0;
			
			$diferenca= ceil(($data2_mk-$data1_mk)/86400);
		
			for ($d=0; $d<=$diferenca; $d++) {
				$calculo_data= $data1_mk+(86400*$d);
				$data= date("d/m/Y", $calculo_data);
				$dia= date("d", $calculo_data);
				$data_valida= date("Y-m-d", $calculo_data);
				$id_dia= date("w", $calculo_data);
				
				$amanha= soma_data($data_valida, 1, 0, 0);
				$ontem= soma_data($data_valida, -1, 0, 0);
				$anteontem= soma_data($data_valida, -2, 0, 0);
				
				if ($d==0) $total_dia[$d]= 0;
				
				$result_peso= mysql_query("select sum(peso) as soma from op_limpa_pesagem
												where op_limpa_pesagem.id_cliente = '". $rs_cli->id_cliente ."'
												and   op_limpa_pesagem.data_pesagem = '". $data_valida ."'
												and   extra = '0'
												". $str ."
												");
				$rs_peso= mysql_fetch_object($result_peso);
				
				$total_mes_cliente+= $rs_peso->soma;
				$total_dia[$d]+= $rs_peso->soma;
				$total_geral_periodo+= $rs_peso->soma;
				
				$pdf->SetFont('ARIALNARROW', '', 6);
				$pdf->Cell($largura, 0.6, fnum($rs_peso->soma), 1, 0, "C", $fill);
			}
			
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(1.2, 0.6, fnum($total_mes_cliente), 1, 1, "C", $fill);
			
			$j++;
		}
		
		$pdf->SetFillColor(200,200,200);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(2, 0.6, "TOTAL", 1 , 0, "L", 1);
		
		$diferenca= ceil(($data2_mk-$data1_mk)/86400);
		
		for ($d=0; $d<=$diferenca; $d++) {
			$calculo_data= $data1_mk+(86400*$d);
			$data= date("d/m/Y", $calculo_data);
			$dia= date("d", $calculo_data);
			$data_valida= date("Y-m-d", $calculo_data);
			$id_dia= date("w", $calculo_data);
			
			$amanha= soma_data($data_valida, 1, 0, 0);
			$ontem= soma_data($data_valida, -1, 0, 0);
			$anteontem= soma_data($data_valida, -2, 0, 0);
			
			$pdf->SetFont('ARIALNARROW', '', 6);
			$pdf->Cell($largura, 0.6, fnum($total_dia[$d]), 1, 0, "C", 1);
			//echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
		$pdf->Cell(1.2, 0.6, fnum($total_geral_periodo), 1, 1, "C", 1);
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("pesagem_limpa_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>