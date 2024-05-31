<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("psl", $_SESSION["permissao"])) {
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
	
	$pdf->AddPage();
	
	if ($_POST["id_cliente"]!="") $str .= " and   pessoas.id_pessoa =  '". $_POST["id_cliente"] ."'";
	
	$periodo= explode("/", $_POST["periodo"]);
	$periodo_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
	
	$total_dias_mes= date("t", $periodo_mk);
		
	$largura= (22.2/$total_dias_mes);		
		
	$total_geral_periodo= 0;
	
	$pdf->SetXY(16.9, 1.25);
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
	$pdf->Cell(0, 0.6, "RELATÓRIO DE HAMPERS", 0, 1, "R");
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, traduz_mes($periodo[0]) ." de ". $periodo[1], 0, 1, "R");
	
	$pdf->Ln();
		
	// ------------- tabela
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->SetFillColor(200,200,200);
	
	$pdf->Cell(3.1, 0.5, "CLIENTE", 1 , 0, "L", 1);
	
	//repetir todos os dias do intervalo
	for ($t=1; $t<=$total_dias_mes; $t++) {
		$pdf->Cell($largura, 0.5, $t, 1, 0, "C", 1);
		//echo traduz_dia_resumido($dia_semana_tit);
	}
	
	$pdf->Cell(1.2, 0.5, "Total", 1, 1, "C", 1);
			
	$pdf->SetFont('ARIALNARROW', '', 9);
	
	
	$result= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
												where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
												". $str ."
												and   pessoas_tipos.tipo_pessoa = 'c'
												and   pessoas.id_cliente_tipo = '1'
												order by 
												pessoas.apelido_fantasia asc
												") or die(mysql_error());
	
	$j=1;
	
	while ($rs= mysql_fetch_object($result)) {
		if (($j%2)==0) $fill= 0;
		else {
			$fill=1;
			$pdf->SetFillColor(230,230,230);
		}
		
		$pdf->Cell(3.1, 0.5, $rs->sigla, 1, 0, "L", $fill);
		
		$total_cliente= 0;
		//$total_dia= array();
		
		//repetir todos os dias do intervalo
		for ($t=1; $t<=$total_dias_mes; $t++) {
			
			//if ($t==1) $total_dia[$t]= 0;
			
			$data_aqui= $periodo[1] ."-". $periodo[0] ."-". formata_saida($t, 2);
			
			$result_hamper= mysql_query("select sum(hampers) as soma from op_suja_pesagem, op_suja_remessas
											where op_suja_pesagem.id_cliente = '". $rs->id_cliente ."'
											and   op_suja_remessas.id_remessa = op_suja_pesagem.id_remessa
											and   op_suja_remessas.data_remessa = '". $data_aqui ."'
											");
			$rs_hamper= mysql_fetch_object($result_hamper);
			
			$total_cliente+= $rs_hamper->soma;
			$total_dia[$t]+= $rs_hamper->soma;
			$total_geral_periodo+= $rs_hamper->soma;
			
			$pdf->Cell($largura, 0.5, $rs_hamper->soma, 1, 0, "C", $fill);
			//echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->Cell(1.2, 0.5, fnumf($total_cliente), 1, 1, "C", $fill);
		
		$j++;
	}
	
	$pdf->SetFillColor(200,200,200);
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	
	$pdf->Cell(3.1, 0.5, "TOTAL", 1 , 0, "L", 1);
	$pdf->SetFont('ARIALNARROW', '', 9);
	//repetir todos os dias do intervalo
	for ($t=1; $t<=$total_dias_mes; $t++) {
		$pdf->Cell($largura, 0.5, fnumf($total_dia[$t]), 1, 0, "C", 1);
		//echo traduz_dia_resumido($dia_semana_tit);
	}
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
	$pdf->Cell(1.2, 0.5, fnumf($total_geral_periodo), 1, 1, "C", 1);
	
	$pdf->AliasNbPages(); 
	$pdf->Output("hamper_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>