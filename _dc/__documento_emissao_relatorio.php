<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("c3", $_SESSION["permissao"])) {
	
	$result= mysql_query("select *
							from  dc_documentos_emissoes
							where id_documento_emissao = '". $_GET["id_documento_emissao"] ."'
							") or die(mysql_error());
	$rs= mysql_fetch_object($result);
	
	$id_empresa_atendente_aqui= $rs->id_empresa;
	if ($id_empresa_atendente_aqui!="") $_SESSION["id_empresa_atendente2"]= $id_empresa_atendente_aqui;
	else $_SESSION["id_empresa_atendente2"]= $_SESSION["id_empresa"];
	
	$result_empresa= mysql_query("select * from  pessoas, rh_enderecos, empresas, cidades, ufs
									where empresas.id_empresa = '". $_SESSION["id_empresa_atendente2"] ."'
									and   empresas.id_pessoa = pessoas.id_pessoa
									and   pessoas.id_pessoa = rh_enderecos.id_pessoa
									and   rh_enderecos.id_cidade = cidades.id_cidade
									and   cidades.id_uf = ufs.id_uf
									") or die(mysql_error());
	$rs_empresa= mysql_fetch_object($result_empresa);
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	
	if ($rs->tipo==9) require("includes/fpdf/modelo_retrato_direita.php");
	elseif ($rs->tipo==4) require("includes/fpdf/modelo_retrato_oficio.php");
	else require("includes/fpdf/modelo_retrato.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 3, 2);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(230,230,230);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	
	
	$i=0;
	
	//protocolo
	if ($rs->tipo==9) {
		$pdf->AddPage();
		
		//$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		//$pdf->Cell(0, 0.55, strtoupper(pega_tipo_documento_emissao($rs->tipo, 0)) ." Nº ". $rs->num ."/". $rs->ano, 0 , 1, 'R');
		
		//$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		//$pdf->Cell(0, 0.55, "", 0 , 1, 'R');
		
		//if (file_exists(CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg"))
		//	$pdf->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 14, 1.3, 5, 1.9287);
		
		$pdf->SetXY(0,3.5);
		
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->Cell(0, 0.55, ucwords($rs_empresa->cidade) .'/'. $rs_empresa->uf .", ". data_extenso_param($rs->data_emissao), 0 , 1, 'R');
		$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 13);
		$pdf->Cell(0, 0.55, strtoupper(pega_tipo_documento_emissao($rs->tipo, 0)) ." Nº ". $rs->num ."/". $rs->ano, 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		if ($rs->de!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.55, "DE:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.55, $rs->de, 0 , 1);
		}
		
		if ($rs->para!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.55, "PARA:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.55, $rs->para, 0 , 1);
		}
		
		if ($rs->cc!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.55, "CC:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.55, $rs->cc, 0 , 1);
		}
		
		if ($rs->para!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.55, "ASSUNTO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.55, $rs->assunto, 0 , 1);
		}
		
		$pdf->SetFont('ARIALNARROW', '', 10);
		
		$pdf->Ln();
			
		$pdf->MultiCell(0, 0.55, strip_tags(html_entity_decode($rs->mensagem)), 0, 1);
		
		$pdf->Ln();
		
		if ($rs->tipo==8) {
			if ($rs->relator!="") {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(3.5, 0.55, "RELATOR:", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(0, 0.55, $rs->relator, 0 , 1);
			}
		}
		
		if (($rs->tipo==9) || ($rs->tipo==2)) {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
			$pdf->Cell(10, 0.55, "", 0, 0);
			$pdf->Cell(0, 0.55, " Recebido:", "LTR", 1);
			
			$pdf->Cell(10, 0.55, "", 0, 0);
			$pdf->Cell(0, 0.55, " Nome:", "LR", 1);
			
			$pdf->Cell(10, 0.55, "", 0, 0);
			$pdf->Cell(0, 0.55, " Data:", "LBR", 1);
		}
	}
	//se nao for assinado para todos os departamentos separados
	elseif (($rs->tipo!=7) && ($rs->metodo!=1)) {
		
		if ($rs->tipo==4) {
			
			$pdf->AddPage();
			
			$pdf->SetY(3);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.55, ucwords($rs_empresa->cidade) .", ". data_extenso_param($rs->data_emissao), 0 , 1, 'L');
			$pdf->Ln();$pdf->Ln();
			
			//$pdf->SetFont('ARIALNARROW', '', 13);
			$pdf->Cell(0, 0.55, (pega_tipo_documento_emissao($rs->tipo, 0)) ." nº ". $rs->num ."/". $rs->ano, 0 , 1, 'L');
			
			$_SESSION["oficio_num"]= $rs->num ."/". $rs->ano;
			$_SESSION["oficio_num_paginas"]= $rs->num_paginas;
			$_SESSION["oficio_datado"]= desformata_data($rs->data_emissao);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->Ln();
			$pdf->Cell(0, 0.2, "", 0 , 1);
				
			$pdf->MultiCell(0, 0.55, strip_tags(html_entity_decode($rs->mensagem)), 0, 1);
			
			$pdf->Ln();$pdf->Ln();
			
			//echo count($pdf->pages) ."<br />";
			
			/*
			if ($rs->de!="") {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(2, 0.55, "DE:", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(0, 0.55, $rs->de, 0 , 1);
			}*/
			
			if ($rs->para!="") {
				
				//$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				//$pdf->Cell(2, 0.55, "PARA:", 0 , 0);
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(0, 0.55, "A ", 0 , 1);
				if ($rs->para!="") $pdf->Cell(0, 0.55, $rs->para, 0 , 1);
				if ($rs->para2!="") $pdf->Cell(0, 0.55, $rs->para2, 0 , 1);
				$pdf->Cell(0, 0.55, $rs->cidade_uf, 0 , 1);
			}
		
		}
		else {
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.55, strtoupper(pega_tipo_documento_emissao($rs->tipo, 0)) ." Nº ". $rs->num ."/". $rs->ano, 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 0.55, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			if ($rs->tipo!=7) {
				if ($rs->de!="") {
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					$pdf->Cell(3.5, 0.55, "DE:", 0 , 0);
					$pdf->SetFont('ARIALNARROW', '', 10);
					$pdf->Cell(0, 0.55, $rs->de, 0 , 1);
				}
				
				if ($rs->para!="") {
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					$pdf->Cell(3.5, 0.55, "PARA:", 0 , 0);
					$pdf->SetFont('ARIALNARROW', '', 10);
					$pdf->Cell(0, 0.55, $rs->para, 0 , 1);
				}
				
				if ($rs->cc!="") {
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					$pdf->Cell(3.5, 0.55, "CC:", 0 , 0);
					$pdf->SetFont('ARIALNARROW', '', 10);
					$pdf->Cell(0, 0.55, $rs->cc, 0 , 1);
				}
				
				if ($rs->tipo==6) {
					if ($rs->fax!="") {
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
						$pdf->Cell(3.5, 0.55, "FAX:", 0 , 0);
						$pdf->SetFont('ARIALNARROW', '', 10);
						$pdf->Cell(0, 0.55, $rs->fax, 0 , 1);
					}
					
					if ($rs->telefone!="") {
						$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
						$pdf->Cell(3.5, 0.55, "TELEFONE:", 0 , 0);
						$pdf->SetFont('ARIALNARROW', '', 10);
						$pdf->Cell(0, 0.55, $rs->telefone, 0 , 1);
					}
				}
			}
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3.5, 0.55, "DATA:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.55, desformata_data($rs->data_emissao), 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3.5, 0.55, "ASSUNTO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.55, $rs->assunto, 0 , 1);
			
			if ($rs->tipo==8) {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(3.5, 0.55, "HORA DE INÍCIO:", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(0, 0.55, $rs->hora_inicio, 0 , 1);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(3.5, 0.55, "HORA DE TÉRMINO:", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(0, 0.55, $rs->hora_termino, 0 , 1);
				
				if ($rs->dirigente!="") {
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					$pdf->Cell(3.5, 0.55, "DIRIGENTE:", 0 , 0);
					$pdf->SetFont('ARIALNARROW', '', 10);
					$pdf->Cell(0, 0.55, $rs->dirigente, 0 , 1);
				}
				
				if ($rs->participantes!="") {
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					$pdf->Cell(3.5, 0.55, "PARTICIPANTES:", 0 , 0);
					$pdf->SetFont('ARIALNARROW', '', 10);
					$pdf->Cell(0, 0.55, $rs->participantes, 0 , 1);
				}
			}
			
			$pdf->Ln();
			
			$pdf->Line($pdf->getX(), $pdf->getY(), 19, $pdf->getY());
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.55, strip_tags(html_entity_decode($rs->mensagem)), 0, 1);
			
			$pdf->Ln();$pdf->Ln();
			
			if ($rs->tipo==8) {
				if ($rs->relator!="") {
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					$pdf->Cell(3.5, 0.55, "RELATOR:", 0 , 0);
					$pdf->SetFont('ARIALNARROW', '', 10);
					$pdf->Cell(0, 0.55, $rs->relator, 0 , 1);
				}
			}
			
			if (($rs->tipo==9) || ($rs->tipo==2)) {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				
				$pdf->Cell(10, 0.55, "", 0, 0);
				$pdf->Cell(0, 0.55, " Recebido:", "LTR", 1);
				
				$pdf->Cell(10, 0.55, "", 0, 0);
				$pdf->Cell(0, 0.55, " Nome:", "LR", 1);
				
				$pdf->Cell(10, 0.55, "", 0, 0);
				$pdf->Cell(0, 0.55, " Data:", "LBR", 1);
			}
		}
	}//fim metodo 1
	
	
	
	
	
	if ($rs->metodo==1) {
		if (($rs->id_turno!="") && ($rs->id_turno!="0")) {
			$str = " and   rh_carreiras.id_turno = '". $rs->id_turno ."'";
		}
		
		if (($rs->id_departamento!="") && ($rs->id_departamento!="0")) {
			$str .= " and   rh_carreiras.id_departamento = '". $rs->id_departamento ."'";
			
		}
		
		$result_tur= mysql_query("select distinct(rh_turnos.id_turno)
									from  rh_carreiras, rh_turnos, rh_departamentos
									where rh_carreiras.id_departamento = rh_departamentos.id_departamento
									and   rh_carreiras.id_turno = rh_turnos.id_turno
									and   rh_carreiras.atual = '1'
									". $str ."
									and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
									order by rh_carreiras.id_departamento asc, rh_carreiras.id_turno asc
									") or die(mysql_error());
		
		$pdf->SetFont('ARIALNARROW', '', 10);
		
		while ($rs_tur= mysql_fetch_object($result_tur)) {
			$chamada_departamento= pega_departamento(pega_id_departamento_do_id_turno($rs_tur->id_turno));
			$chamada_turno= " / ". pega_turno($rs_tur->id_turno);
			
			$pdf->AddPage();
		
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.55, strtoupper(pega_tipo_documento_emissao($rs->tipo, 1)) ." Nº ". $rs->num ."/". $rs->ano, 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 0.55, "", 0 , 1, 'R');
			
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3.5, 0.55, "DATA:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.55, desformata_data($rs->data_emissao), 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3.5, 0.55, "ASSUNTO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.55, $rs->assunto, 0 , 1);
			
			$pdf->Ln();
			
			$pdf->Line($pdf->getX(), $pdf->getY(), 19, $pdf->getY());
			$pdf->Ln();$pdf->Ln();
			//$pdf->WriteHTML(html_entity_decode($rs->mensagem));
			$pdf->MultiCell(0, 0.55, strip_tags(html_entity_decode($rs->mensagem)), 0, 1);
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 1, "FUNCIONÁRIOS ". $chamada_departamento . $chamada_turno ." :", 0 , 1, 'L');
			
			$j=0;
			$result_fun= mysql_query("select *
										from  pessoas, rh_funcionarios, rh_carreiras
										where pessoas.id_pessoa = rh_funcionarios.id_pessoa
										and   pessoas.tipo = 'f'
										and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_turno = '". $rs_tur->id_turno ."'
										and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_funcionarios.status_funcionario = '1'
										order by pessoas.nome_rz asc
										") or die(mysql_error());
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(7, 0.55, "NOME", 1, 0, 1, 1);
			$pdf->Cell(10, 0.55, "ASSINATURA", 1, 1, 1, 1);
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			
			$i=0;
			
			while ($rs_fun= mysql_fetch_object($result_fun)) {
				
				$result_presente= mysql_query("select * from rh_afastamentos, rh_afastamentos_dias
												where rh_afastamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   rh_afastamentos.id_funcionario = '". $rs_fun->id_funcionario ."'
												and   (
													  rh_afastamentos.tipo_afastamento = 'p'
												or    rh_afastamentos.tipo_afastamento = 'o'
												or    rh_afastamentos.tipo_afastamento = 'f'
													  )
												and   rh_afastamentos.id_afastamento = rh_afastamentos_dias.id_afastamento
												and   rh_afastamentos_dias.data = '". date("Ymd") ."'
												") or die(mysql_error());
				$linhas_presente= mysql_num_rows($result_presente);
				
				if ( (($rs->situacao==1) && ($linhas_presente==0)) || ($rs->situacao==2) ) {
					
					if (($i%2)==0) $fill= 0;
					else $fill= 1;
					$pdf->SetFillColor(235,235,235);
					
					$pdf->Cell(7, 0.55, $rs_fun->nome_rz, 1, 0, "L", $fill);
					$pdf->Cell(10, 0.55, "", 1, 1, "L", $fill);
					
					$i++;
				}
				
			}
		}
	}
	elseif ($rs->metodo==2) {
		
		$pdf->AddPage();
		$pdf->SetXY(7,1.75);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.55, $rs->assunto, 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.55, "", 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();

		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->MultiCell(0, 0.5, str_replace("&nbsp;", "", strip_tags(html_entity_decode($rs->mensagem))), 0 , 1);
		$pdf->Ln();
				
		$pdf->Ln();
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		
		$pdf->Cell(0, 1, "FUNCIONÁRIOS: ", 0 , 1, 'L');
		
		$pdf->Cell(0, 0.5, "", 0 , 1, 'L');
		
		$j=0;
		$result_fun= mysql_query("select *
									from  pessoas, rh_funcionarios, rh_carreiras
									where pessoas.id_pessoa = rh_funcionarios.id_pessoa
									and   pessoas.tipo = 'f'
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_carreiras.atual = '1'
									and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_funcionarios.status_funcionario = '1'
									order by pessoas.nome_rz asc
									") or die(mysql_error());
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		$pdf->SetFillColor(210,210,210);
		
		$pdf->Cell(7, 0.55, "NOME", 1, 0, 1, 1);
		$pdf->Cell(10, 0.55, "ASSINATURA", 1, 1, 1, 1);
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		$i=0;
		
		while ($rs_fun= mysql_fetch_object($result_fun)) {
			
			$result_presente= mysql_query("select * from rh_afastamentos, rh_afastamentos_dias
											where rh_afastamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   rh_afastamentos.id_funcionario = '". $rs_fun->id_funcionario ."'
											and   (
												  rh_afastamentos.tipo_afastamento = 'p'
											or    rh_afastamentos.tipo_afastamento = 'o'
											or    rh_afastamentos.tipo_afastamento = 'f'
												  )
											and   rh_afastamentos.id_afastamento = rh_afastamentos_dias.id_afastamento
											and   rh_afastamentos_dias.data = '". date("Ymd") ."'
											") or die(mysql_error());
			$linhas_presente= mysql_num_rows($result_presente);
								
			if ( (($rs->situacao==1) && ($linhas_presente==0)) || ($rs->situacao==2) ) {
				if (($i%2)==0) $fill= 0;
				else $fill= 1;
				$pdf->SetFillColor(235,235,235);
				
				$pdf->Cell(7, 0.55, $rs_fun->nome_rz, 1, 0, "L", $fill);
				$pdf->Cell(10, 0.55, "", 1, 1, "L", $fill);
				
				$i++;
			}
			
		}
	}
	elseif ($rs->metodo==3) {
		$pdf->AddPage();
		$pdf->SetXY(7,1.75);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.55, $rs->assunto, 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(0, 0.55, desformata_data($rs->data_emissao), 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
		if ($rs->relator!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2.5, 0.55, "RELATOR:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.55, $rs->relator, 0 , 1);
		}
		
		if ($rs->turno!="") {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2.5, 0.55, "TURNO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.55, $rs->turno, 0 , 1);
		}
		
		$pdf->Ln();
		
		$pdf->SetFont('ARIALNARROW', '', 10);
		$pdf->MultiCell(0, 0.5, str_replace("&nbsp;", "", strip_tags(html_entity_decode($rs->mensagem))), 0 , 1);
		
		$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		//$pdf->Cell(0, 1, "LISTA DE PRESENÇA: ", 0 , 1, 'L');
		
		$pdf->Cell(0, 0.5, "", 0 , 1, 'L');
		$pdf->SetFillColor(210,210,210);
		
		$pdf->Cell(7, 0.55, "NOME", 1, 0, 1, 1);
		$pdf->Cell(5, 0.55, "SETOR", 1, 0, 1, 1);
		$pdf->Cell(5, 0.55, "ASSINATURA", 1, 1, 1, 1);
		
		for ($i=0; $i<$rs->metodo_num; $i++) {
			
			if (($i%2)==0) $fill= 0;
			else $fill= 1;
			$pdf->SetFillColor(235,235,235);
			
			$pdf->Cell(7, 0.55, "", 1, 0, "L", $fill);
			$pdf->Cell(5, 0.55, "", 1, 0, "L", $fill);
			$pdf->Cell(5, 0.55, "", 1, 1, "L", $fill);
		}
	}
	
	
	
	
	
	
	
	
	$numero_paginas= count($pdf->pages);
			
	$result_update= mysql_query("update dc_documentos_emissoes
									set num_paginas= '". $numero_paginas ."'
									where id_documento_emissao = '". $_GET["id_documento_emissao"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
	
	
	
	
	
	$pdf->AliasNbPages(); 
	
	//$_SESSION["id_empresa_atendente2"]="";
	
	$pdf->Output("documento_emissao_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>