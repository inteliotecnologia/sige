<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("ps", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	$pdf->AddPage();
	
	$pdf->SetXY(7,1.75);
	
	if ($_POST["id_equipamento"]!="") $str_geral_equi= "and   op_suja_lavagem.id_equipamento = '". $_POST["id_equipamento"] ."' ";
	if ($_POST["id_cliente"]!="") $str_geral_cliente= "and   op_suja_lavagem_cestos.id_cliente= '". $_POST["id_cliente"] ."' ";
	
	//diário
	if ($_POST["tipo_relatorio"]=='d') {
		
		$peso_total_dia= 0;
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "LAVAGEM DE ROUPA SUJA - POR PROCESSO", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $_POST["data"], 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		if ($_POST["id_equipamento"]!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(3, 0.6, "EQUIPAMENTO:", 0, 0);
			
			$pdf->SetFont('ARIALNARROW', '', 12);
			$pdf->Cell(3, 0.6, pega_equipamento($_POST["id_equipamento"]), 0, 1);
			
			$pdf->Ln();
		}
		
		/*
		$result_equi= mysql_query("select * from op_equipamentos
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   tipo_equipamento = '1'
									". $str_equi ."
									order by equipamento asc
									 ") or die(mysql_error());
		$i=0;
		while ($rs_equi = mysql_fetch_object($result_equi)) {
		
			$result_equi_teste= mysql_query("select id_lavagem from op_suja_lavagem, op_suja_remessas
											where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   op_suja_lavagem.id_equipamento = '". $rs_equi->id_equipamento ."'
											and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
											and   op_suja_remessas.data_remessa = '". formata_data($_POST["data"]) ."'
											") or die(mysql_error());
			
			if (mysql_num_rows($result_equi_teste)>0) {
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
				$pdf->Cell(0, 0.7, $rs_equi->equipamento, "B", 1, "L", 0);
				$pdf->Cell(0, 0.3, "", 0, 1);
				*/	
				
				if ($_POST["id_processo"]!="") $str_proc= "and   op_equipamentos_processos.id_processo = '". $_POST["id_processo"] ."' ";
			
				$result_proc= mysql_query("select * from op_equipamentos_processos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											". $str_proc ."
											and   status_processo = '1'
											order by codigo asc
											 ");
				$i=0;
				while ($rs_proc= mysql_fetch_object($result_proc)) {
					
					$peso_total_processo= 0;
					
					$result_proc_teste= mysql_query("select op_suja_lavagem.id_lavagem from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
													where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
													". $str_geral_equi ."
													". $str_geral_cliente ."
													and   op_suja_lavagem.id_processo = '". $rs_proc->id_processo ."'
													and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
													and   op_suja_remessas.data_remessa = '". formata_data($_POST["data"]) ."'
													") or die(mysql_error());
					
					if (mysql_num_rows($result_proc_teste)>0) {
					
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
						$pdf->Cell(0, 0.7, $rs_proc->processo, 0, 1, "L", 0);
						$pdf->Cell(0, 0.3, "", 0, 1);
							
						$pdf->SetFillColor(210,210,210);
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
						
						$pdf->Cell(12, 0.5, "CLIENTE", 1, 0, "L", 1);
						$pdf->Cell(0, 0.5, "PESO", 1, 1, "C", 1);
						
						if ($_POST["id_cliente"]!="") $str_cliente= "and   pessoas.id_pessoa = '". $_POST["id_cliente"] ."' ";
						
						$result_cliente= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
														where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
														and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
														and   pessoas_tipos.tipo_pessoa = 'c'
														and   pessoas.id_cliente_tipo = '1'
														". $str_cliente ."
														order by 
														pessoas.apelido_fantasia asc
														") or die(mysql_error());
						
						$d=0;
						while ($rs_cliente= mysql_fetch_object($result_cliente)) {
							
							$result_cliente_teste= mysql_query("select op_suja_lavagem.id_lavagem from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
															where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
															". $str_geral_equi ."
															". $str_geral_cliente ."
															and   op_suja_lavagem.id_processo = '". $rs_proc->id_processo ."'
															and   op_suja_lavagem_cestos.id_cliente = '". $rs_cliente->id_cliente ."'
															and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
															and   op_suja_remessas.data_remessa = '". formata_data($_POST["data"]) ."'
															") or die(mysql_error());
							
							if (mysql_num_rows($result_cliente_teste)>0) {
							
								if (($d%2)==0) $fill= 0;
								else $fill= 1;
								
								$pdf->SetFillColor(235,235,235);
								$pdf->SetFont('ARIALNARROW', '', 9);
								
								$result_peso= mysql_query("select sum(op_suja_lavagem_cestos.peso) as peso_total from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
															where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
															". $str_geral_equi ."
															and   op_suja_lavagem.id_processo = '". $rs_proc->id_processo ."'
															and   op_suja_lavagem_cestos.id_cliente = '". $rs_cliente->id_cliente ."'
															and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
															and   op_suja_remessas.data_remessa = '". formata_data($_POST["data"]) ."'
															") or die(mysql_error());
								
								$rs_peso= mysql_fetch_object($result_peso);
							
								$pdf->Cell(12, 0.5, "  ". $rs_cliente->apelido_fantasia, 1, 0, "L", $fill);
								$pdf->Cell(0, 0.5, fnum($rs_peso->peso_total) ." kg", 1, 1, "C", $fill);
								
								$peso_total_processo+= $rs_peso->peso_total;
								$peso_total_dia+= $rs_peso->peso_total;
								
								$d++;
							}//fim teste clientes
						}
						
						
						// ----------- gambiarra VARIADAS ------------------------------------------------------------------------
						if ($_POST["id_cliente"]=="") {
							if (($d%2)==0) $fill= 0;
							else $fill= 1;
							
							$result_peso_variadas= mysql_query("select sum(op_suja_lavagem_cestos.peso) as peso_total
																from  op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
																where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
																". $str_geral_equi ."
																and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
																and   op_suja_lavagem.id_processo = '". $rs_proc->id_processo ."'
																and   op_suja_lavagem_cestos.id_cliente = '0'
																and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
																and   op_suja_remessas.data_remessa = '". formata_data($_POST["data"]) ."'
																") or die(mysql_error());
							
							$rs_peso_variadas= mysql_fetch_object($result_peso_variadas);
							
							if ($rs_peso_variadas->peso_total>0) {
								
								$pdf->SetFillColor(235,235,235);
								$pdf->SetFont('ARIALNARROW', '', 9);
								
								$pdf->Cell(12, 0.5, "  RELAVE", 1, 0, "L", $fill);
								$pdf->Cell(0, 0.5, fnum($rs_peso_variadas->peso_total) ." kg", 1, 1, "C", $fill);
								
								$peso_total_processo+= $rs_peso_variadas->peso_total;
								$peso_total_dia+= $rs_peso_variadas->peso_total;
							}
						}
						// ----------- gambiarra VARIADAS ------------------------------------------------------------------------
						
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
						
						$pdf->Cell(12, 0.5, "", 0, 0, "L");
						$pdf->Cell(0, 0.5, fnum($peso_total_processo) ." kg", 1, 1, "C", 0);
						
						$pdf->Ln();
						
					}//fim teste processo
				}//fim processo
				
				
				$pdf->Ln();
			
			/*
			}//fim teste equipamentos
		}//fim equipamentos
		*/
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(2.2, 0.6, "PESO TOTAL:", 0, 0, 'L', 0);
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(3, 0.6, fnum($peso_total_dia) ." kg", 0, 1, 'L', 0);
		
	}
	//mensal
	else {
		
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
		
		$peso_total_mes= 0;
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "LAVAGEM DE ROUPA SUJA - POR PROCESSO", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, $data1f ." à ". $data2f, 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		if ($_POST["id_equipamento"]!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(3, 0.6, "EQUIPAMENTO:", 0, 0);
			
			$pdf->SetFont('ARIALNARROW', '', 12);
			$pdf->Cell(3, 0.6, pega_equipamento($_POST["id_equipamento"]), 0, 1);
			
			$pdf->Ln();
		}
		
		/*
		$result_equi= mysql_query("select * from op_equipamentos
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   tipo_equipamento = '1'
									". $str_equi ."
									order by equipamento asc
									 ") or die(mysql_error());
		$i=0;
		while ($rs_equi = mysql_fetch_object($result_equi)) {
			
			$result_equi_teste= mysql_query("select id_lavagem from op_suja_lavagem, op_suja_remessas
											where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   op_suja_lavagem.id_equipamento = '". $rs_equi->id_equipamento ."'
											and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
											and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". $_POST["periodo"] ."'
											") or die(mysql_error());
			
			if (mysql_num_rows($result_equi_teste)>0) {
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
				$pdf->Cell(0, 0.7, $rs_equi->equipamento, "B", 1, "L", 0);
				$pdf->Cell(0, 0.3, "", 0, 1);
				*/
				
				if ($_POST["id_processo"]!="") $str_proc= "and   op_equipamentos_processos.id_processo = '". $_POST["id_processo"] ."' ";
			
				$result_proc= mysql_query("select * from op_equipamentos_processos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											". $str_proc ."
											and   status_processo = '1'
											order by codigo asc
											 ");
				$i=0;
				while ($rs_proc= mysql_fetch_object($result_proc)) {
					
					$peso_total_processo= 0;
					
					$result_proc_teste= mysql_query("select op_suja_lavagem.id_lavagem from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
													where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
													". $str_geral_equi ."
													". $str_geral_cliente ."
													and   op_suja_lavagem.id_processo = '". $rs_proc->id_processo ."'
													and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
													and   op_suja_remessas.data_remessa >= '". $data1 ."'
													and   op_suja_remessas.data_remessa <= '". $data2 ."'
													") or die(mysql_error());
					
					if (mysql_num_rows($result_proc_teste)>0) {
					
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
						$pdf->Cell(0, 0.7, $rs_proc->processo, 0, 1, "L", 0);
						$pdf->Cell(0, 0.3, "", 0, 1);
							
						$pdf->SetFillColor(210,210,210);
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
						
						$pdf->Cell(12, 0.5, "CLIENTE", 1, 0, "L", 1);
						$pdf->Cell(0, 0.5, "PESO", 1, 1, "C", 1);
						
						if ($_POST["id_cliente"]!="") $str_cliente= "and   pessoas.id_pessoa = '". $_POST["id_cliente"] ."' ";
						
						$result_cliente= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
														where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
														and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
														and   pessoas_tipos.tipo_pessoa = 'c'
														and   pessoas.id_cliente_tipo = '1'
														". $str_cliente ."
														order by 
														pessoas.apelido_fantasia asc
														") or die(mysql_error());
						
						$d=0;
						while ($rs_cliente= mysql_fetch_object($result_cliente)) {
							
							$result_cliente_teste= mysql_query("select op_suja_lavagem.id_lavagem from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
															where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
															". $str_geral_equi ."
															and   op_suja_lavagem.id_processo = '". $rs_proc->id_processo ."'
															and   op_suja_lavagem_cestos.id_cliente = '". $rs_cliente->id_cliente ."'
															and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
															and   op_suja_remessas.data_remessa >= '". $data1 ."'
															and   op_suja_remessas.data_remessa <= '". $data2 ."'
															") or die(mysql_error());
							
							if (mysql_num_rows($result_cliente_teste)>0) {
							
								if (($d%2)==0) $fill= 0;
								else $fill= 1;
								
								$pdf->SetFillColor(235,235,235);
								$pdf->SetFont('ARIALNARROW', '', 9);
								
								$result_peso= mysql_query("select sum(op_suja_lavagem_cestos.peso) as peso_total from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
															where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
															". $str_geral_equi ."
															and   op_suja_lavagem.id_processo = '". $rs_proc->id_processo ."'
															and   op_suja_lavagem_cestos.id_cliente = '". $rs_cliente->id_cliente ."'
															and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
															and   op_suja_remessas.data_remessa >= '". $data1 ."'
															and   op_suja_remessas.data_remessa <= '". $data2 ."'
															") or die(mysql_error());
								
								$rs_peso= mysql_fetch_object($result_peso);
							
								$pdf->Cell(12, 0.5, "  ". $rs_cliente->apelido_fantasia, 1, 0, "L", $fill);
								$pdf->Cell(0, 0.5, fnum($rs_peso->peso_total) ." kg", 1, 1, "C", $fill);
								
								$peso_total_processo+= $rs_peso->peso_total;
								$peso_total_mes+= $rs_peso->peso_total;
								
								$d++;
							}//fim teste clientes
						}
						
						
						// ----------- gambiarra VARIADAS ------------------------------------------------------------------------
						if ($_POST["id_cliente"]=="") {
							if (($d%2)==0) $fill= 0;
							else $fill= 1;
							
							$result_peso_variadas= mysql_query("select sum(op_suja_lavagem_cestos.peso) as peso_total from op_suja_lavagem, op_suja_lavagem_cestos, op_suja_remessas
														where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."'
														and   op_suja_lavagem.id_lavagem = op_suja_lavagem_cestos.id_lavagem
														". $str_geral_equi ."
														and   op_suja_lavagem.id_processo = '". $rs_proc->id_processo ."'
														and   op_suja_lavagem_cestos.id_cliente = '0'
														and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
														and   op_suja_remessas.data_remessa >= '". $data1 ."'
														and   op_suja_remessas.data_remessa <= '". $data2 ."'
														") or die(mysql_error());
							
							$rs_peso_variadas= mysql_fetch_object($result_peso_variadas);
							
							if ($rs_peso_variadas->peso_total>0) {
								
								$pdf->SetFillColor(235,235,235);
								$pdf->SetFont('ARIALNARROW', '', 9);
								
								$pdf->Cell(12, 0.5, "  RELAVE", 1, 0, "L", $fill);
								$pdf->Cell(0, 0.5, fnum($rs_peso_variadas->peso_total) ." kg", 1, 1, "C", $fill);
								
								$peso_total_processo+= $rs_peso_variadas->peso_total;
								$peso_total_mes+= $rs_peso_variadas->peso_total;
							}
						}
						// ----------- gambiarra VARIADAS ------------------------------------------------------------------------
						
						
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
						
						$pdf->Cell(12, 0.5, "", 0, 0, "L");
						$pdf->Cell(0, 0.5, fnum($peso_total_processo) ." kg", 1, 1, "C", 0);
						
						$pdf->Ln();
						
					}//fim teste processo
				}//fim processo
				
				
				$pdf->Ln();
				
			/*
			}//fim teste equipamentos
		}//fim equipamentos
		*/
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(2.2, 0.6, "PESO TOTAL:", 0, 0, 'L', 0);
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(3, 0.6, fnum($peso_total_mes) ." kg", 0, 1, 'L', 0);
		
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("documento_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>