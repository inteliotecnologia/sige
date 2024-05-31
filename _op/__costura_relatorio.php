<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("pl(", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_paisagem.php");

	$pdf=new PDF("L", "cm", "A4");
	$pdf->SetMargins(1.5, 1.5, 1.5);
	$pdf->SetAutoPageBreak(true, 1.5);
	$pdf->SetFillColor(230,230,230);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
		
	if ($_POST["id_cliente"]!="") $str_cliente .= " and   pessoas.id_pessoa =  '". $_POST["id_cliente"] ."'";
	
	$periodo= explode("/", $_POST["periodo"]);
	$periodo_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
	
	$total_dias_mes= date("t", $periodo_mk);
		
	$largura= (22.2/$total_dias_mes);
		
	$result= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
												where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
												". $str_cliente ."
												and   pessoas_tipos.tipo_pessoa = 'c'
												and   pessoas.id_cliente_tipo = '1'
												order by 
												pessoas.apelido_fantasia asc
												") or die(mysql_error());
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		$pdf->AddPage();
		
		unset($total_dia);
		$total_geral_periodo= 0;
		
		$pdf->SetXY(16.9, 1.25);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, "RELATÓRIO DE PEÇAS PARA COSTURA (". traduz_mes($periodo[0]) ." de ". $periodo[1] .")", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $rs->apelido_fantasia, 0, 1, "R");
		
		$pdf->Ln();
		
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		$pdf->SetFillColor(200,200,200);
		
		$pdf->Cell(3.2, 0.45, "PEÇAS", 1 , 0, "L", 1);
		
		//repetir todos os dias do intervalo
		for ($t=1; $t<=$total_dias_mes; $t++) {
			$pdf->Cell($largura, 0.45, $t, 1, 0, "C", 1);
            //echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->Cell(1.2, 0.45, "TOTAL", 1, 1, "C", 1);
				
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		$result_pecas= mysql_query("select * from op_limpa_pecas
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   status_peca = '1'
												order by peca asc
												");
		$j=1;
		while ($rs_peca= mysql_fetch_object($result_pecas)) {
			if (($j%2)==0) $fill= 0;
			else {
				$fill=1;
				$pdf->SetFillColor(230,230,230);
			}
			
			$pdf->Cell(3.2, 0.475, $rs_peca->peca, 1, 0, "L", $fill);
			
			$total_peca= 0;
			//$total_dia= array();
			
			//repetir todos os dias do intervalo
			for ($t=1; $t<=$total_dias_mes; $t++) {
				
				//if ($t==1) $total_dia[$t]= 0;
				
				$data_aqui= $periodo[1] ."-". $periodo[0] ."-". formata_saida($t, 2);
				
				$result_costura= mysql_query("select sum(qtde) as soma from op_limpa_costura
												where id_cliente = '". $rs->id_cliente ."'
												and   data_costura = '". $data_aqui ."'
												and   id_peca = '". $rs_peca->id_peca ."'
												");
				$rs_costura= mysql_fetch_object($result_costura);
				
				$total_peca+= $rs_costura->soma;
				$total_dia[$t]+= $rs_costura->soma;
				$total_geral_periodo+= $rs_costura->soma;
				
				$pdf->Cell($largura, 0.475, $rs_costura->soma, 1, 0, "C", $fill);
				//echo traduz_dia_resumido($dia_semana_tit);
			}
			
			$pdf->Cell(1.2, 0.475, $total_peca, 1, 1, "C", $fill);
			
			$j++;
		}
		
		$pdf->SetFillColor(200,200,200);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell(3.2, 0.45, "TOTAL", 1 , 0, "L", 1);
		$pdf->SetFont('ARIALNARROW', '', 8);
		//repetir todos os dias do intervalo
		for ($t=1; $t<=$total_dias_mes; $t++) {
			$pdf->Cell($largura, 0.45, $total_dia[$t], 1, 0, "C", 1);
            //echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		$pdf->Cell(1.2, 0.45, $total_geral_periodo, 1, 1, "C", 1);
		
    }//fim while
	
	$pdf->AliasNbPages(); 
	$pdf->Output("costura_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>