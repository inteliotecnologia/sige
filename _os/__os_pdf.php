<?
require_once("conexao.php");
if ((isset($_SESSION["id_empresa"])) && ($_GET["id_os"]!="")) {
	
	$result= mysql_query("select oss.*, DATE_FORMAT(oss.data_os, '%d/%m/%Y') as data_os,
							DATE_FORMAT(oss.data_os, '%H:%i:%s') as hora_os,
							empresas.*, cidades.cidade, ufs.uf,
							servicos.servico
							from oss, empresas, servicos, cidades, ufs
							where oss.id_servico = servicos.id_servico
							and   oss.id_empresa = empresas.id_empresa
							and   empresas.id_cidade = cidades.id_cidade
							and   cidades.id_uf = ufs.id_uf
							and   oss.id_os = '". $_GET["id_os"] ."'
							") or die(mysql_error());
	
	//Se tiver alguma caixa no envio
	if (mysql_num_rows($result)>0) {
		
		define('FPDF_FONTPATH','');
		require_once("fdpf/fpdf.php");
		
		$pdf= new FPDF('P', 'mm', 'A4');
		$pdf->SetDisplayMode('real', 'single') ;
		$pdf->SetMargins(15, 15, 10);
		$pdf->SetDrawColor(105, 105, 105);
		$pdf->SetFillColor(225, 225, 225);
		
		$pdf->SetAuthor('Webalize Sistemas');
		$pdf->SetCreator('Prospital '. $versao);
		$pdf->SetSubject('Ordem de serviço da prospital nº'. $rs->id_os);
		$pdf->SetTitle('Ordem de serviço da prospital nº'. $rs->id_os);
		$pdf->SetAutoPageBreak(true, 10);
		
		$rs= mysql_fetch_object($result);
		
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',12);
		
		$pdf->Image("images/logo_prospital.png", 15, $pdf->GetY()+1, 40, 6.22);
		$pdf->Cell(92.5, 7, "", 0, 0, 'L');
		$pdf->Cell(92.5, 7, "ORDEM DE SERVIÇO Nº ". $rs->id_os, 0, 1, 'R');
		
		$pdf->Ln();
		
		//inicio do cabecalho
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(25, 6, "Cliente:", '1', 0, 'R', 1);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(117.5, 6, $rs->nome_fantasia, '1', 0, 'L');
		
		$pdf->Cell(2.5, 6, "", 0, 0, 'L');
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(16, 6, "Data:", '1', 0, 'R', 1);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(24, 6, $rs->data_os, '1', 1, 'L');
	
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(25, 6, "Endereço:", '1', 0, 'R', 1);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(117.5, 6, $rs->endereco .", ". $rs->numero .". ". $rs->bairro .". ". $rs->cidade ."/". $rs->uf, '1', 0, 'L');
		
		$pdf->Cell(2.5, 6, "", 0, 0, 'L');
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(16, 6, "Hora:", '1', 0, 'R', 1);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(24, 6, $rs->hora_os, '1', 1, 'L');
	
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(25, 6, "Solicitante:", '1', 0, 'R', 1);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(117.5, 6, $rs->solicitante ." - ". $rs->tel_solicitante, '1', 0, 'L');
	
		$pdf->Cell(2.5, 6, "", 0, 0, 'L');
	
		$pdf->Cell(16, 6, "", '0', 0, 'R');
		$pdf->Cell(24, 6, "", '0', 1, 'L');
		
		$pdf->Ln();
		
		//Titulo grande da pagina
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(0, 10, "DADOS DA ORDEM DE SERVIÇO", 0, 1, 'C');
		
		//inicio do cabecalho
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(35, 6, "Técnico destacado:", '1', 0, 'R', 1);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(150, 6, pega_tecnico($rs->id_tecnico), '1', 1, 'L');
			
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(35, 6, "Tipo de atendimento:", '1', 0, 'R', 1);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(110, 6, pega_tipo_atendimento($rs->tipo_atendimento), 'R', 0, 'L');	
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(22, 6, "Prioridade:", '1', 0, 'R', 1);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(18, 6, pega_prioridade($rs->prioridade), '1', 1, 'L');		

		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(35, 6, "Serviço:", '1', 0, 'R', 1);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(150, 6, $rs->servico, '1', 1, 'L');
		
		$pdf->Ln();

		// --------------------------------------- TABELA
	
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(70, 6, 'Equipamento', 1, 0, 'C', 1);
		$pdf->Cell(30, 6, 'Nº de série', 1, 0, 'C', 1);
		$pdf->Cell(85, 6, 'Observações', 1, 1, 'C', 1);
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(70, 6, $rs->equipamento, 1, 0, 'C');
		$pdf->Cell(30, 6, $rs->nserie, 1, 0, 'C');
		$pdf->MultiCell(85, 6, $rs->obs, 1, 1, 0);

		$pdf->Ln();
		
		switch ($rs->id_servico) {
			case "1":
				$result2= mysql_query("select * from os_hemodialise where id_os = '$rs->id_os' ");
				$rs2= mysql_fetch_object($result2);
				
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(38, 6, "Serviço executado:", '1', 0, 'R', 1);
				$pdf->SetFont('Arial','',10);
				$pdf->MultiCell(147, 6, $rs2->servico_executado, '1', 'L');
				
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(38, 6, "Material utilizado:", '1', 0, 'R', 1);
				$pdf->SetFont('Arial','',10);
				$pdf->MultiCell(147, 6, $rs2->material_utilizado, '1', 'L');
				$pdf->SetFont('Arial','B',10);
				
				break;
			case "2":
				$result2= mysql_query("select * from os_agua where id_os = '$rs->id_os' ");
				$rs2= mysql_fetch_object($result2);
				
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(38, 6, "Serviço executado:", '1', 0, 'R', 1);
				$pdf->SetFont('Arial','',10);
				$pdf->MultiCell(147, 6, $rs2->servico_executado, '1', 'L');
				
				$pdf->Ln();
		
				//Titulo grande da pagina
				$pdf->SetFont('Arial','B',11);
				$pdf->Cell(0, 10, "TESTES REALIZADOS (ANÁLISE DA ÁGUA TRATADA)", 0, 1, 'C');
				
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(54, 6, 'Características', 1, 0, 'C', 1);
				$pdf->Cell(38, 6, 'Parâmetro aceitável', 1, 0, 'C', 1);
				$pdf->Cell(48, 6, 'Freqüência de verificação', 1, 0, 'C', 1);
				$pdf->Cell(45, 6, 'Parâmetros encontrados', 1, 1, 'C', 1);
				
				$pdf->SetFont('Arial','',9);
				
				$i=1;
				$vetor= pega_analise_agua_tratada("l");
				
				while ($vetor[$i][0]) {
					$rs_par= mysql_fetch_object(mysql_query("select * from os_agua_analise
																			where id_os = '". $rs->id_os ."'
																			and   id_carac = '". $i ."'
																			"));
					$pdf->Cell(54, 6, $vetor[$i][0], 1, 0, 'C');
					$pdf->Cell(38, 6, $vetor[$i][1], 1, 0, 'C');
					$pdf->Cell(48, 6, 'Diária', 1, 0, 'C');
					
					switch ($i) {
						case 1: $valor= pega_cor_aparente($rs_par->parametro); break;
						case 2: $valor= pega_turvacao($rs_par->parametro); break;
						case 3: $valor= pega_sabor($rs_par->parametro); break;
						case 4: $valor= pega_odor($rs_par->parametro); break;
						case 5: $valor= pega_cloro_residual_livre($rs_par->parametro); break;
						case 6: $valor= pega_ph($rs_par->parametro); break;
						case 7: $valor= pega_cloro_livre($rs_par->parametro); break;
						case 8: $valor= pega_temperatura($rs_par->parametro); break;
						case 9: $valor= pega_dureza($rs_par->parametro); break;
					}
					
					$pdf->Cell(45, 6, $valor, 1, 1, 'C');
					$i++;
				}
				
				/*for ($i=1; $i<10; $i++) {
					switch($i) {
						case 1: $valor= $rs2->parametro1; break;
						case 2: $valor= $rs2->parametro2; break;
						case 3: $valor= $rs2->parametro3; break;
						case 4: $valor= $rs2->parametro4; break;
						case 5: $valor= $rs2->parametro5; break;
						case 6: $valor= $rs2->parametro6; break;
						case 7: $valor= $rs2->parametro7; break;
						case 8: $valor= $rs2->parametro8; break;
						case 9: $valor= $rs2->parametro9; break;
					}
					$pdf->Cell(54, 6, $caracteristica[$i], 1, 0, 'C');
					$pdf->Cell(38, 6, $parametro[$i], 1, 0, 'C');
					$pdf->Cell(48, 6, 'Diária', 1, 0, 'C');
					$pdf->Cell(45, 6, $valor, 1, 1, 'C');
				}*/
				
				$pdf->Ln();
				
				$pdf->SetFont('Arial','B',11);
				$pdf->Cell(0, 10, strtoupper("Verificação e/ou Substituição de Filtros de Cartuchos"), 0, 1, 'C');
				
				$pos_y= $pdf->GetY();
				
				$pdf->SetFont('Arial','B',9);
				
				$pdf->Cell(60, 6, "         50/50 Micras", '1', 0, 'L', 1);
				if ($rs2->ver_50micras==1)
					$pdf->Image('images/preto.png', 17, $pos_y+1, 4, 4);
				else
					$pdf->Image('images/branco.png', 17, $pos_y+1, 4, 4);
				$pdf->Cell(2.5, 6, "", 0, 0);
				
				$pdf->Cell(60, 6, "         20 Micras", '1', 0, 'L', 1);
				if ($rs2->ver_20micras==1)
					$pdf->Image('images/preto.png', 79, $pos_y+1, 4, 4);
				else
					$pdf->Image('images/branco.png', 79, $pos_y+1, 4, 4);
				$pdf->Cell(2.5, 6, "", 0, 0);
				
				$pdf->Cell(60, 6, "         01 Micra", '1', 1, 'L', 1);
				if ($rs2->ver_01micra==1)
					$pdf->Image('images/preto.png', 142, $pos_y+1, 4, 4);
				else
					$pdf->Image('images/branco.png', 142, $pos_y+1, 4, 4);
				
				$pdf->SetFont('Arial','',9);
				
				if (mysql_num_rows($result2)>0) {
					$pdf->Cell(60, 6, $rs2->obs_50micras, '1', 0, '1', 0);
					$pdf->Cell(2.5, 6, "", 0, 0);
					
					$pdf->Cell(60, 6, $rs2->obs_20micras, '1', 0, '1', 0);
					$pdf->Cell(2.5, 6, "", 0, 0);
					
					$pdf->Cell(60, 6, $rs2->obs_01micra, '1', 1, '1', 0);
				}
				else {				
					for ($i=0; $i<5; $i++) {
						$pdf->Cell(60, 6, "", '1', 0, '1', 0);
						$pdf->Cell(2.5, 6, "", 0, 0);
						
						$pdf->Cell(60, 6, "", '1', 0, '1', 0);
						$pdf->Cell(2.5, 6, "", 0, 0);
						
						$pdf->Cell(60, 6, "", '1', 1, '1', 0);
					}
				}
			
				$pdf->Ln();
				
				$pdf->SetFont('Arial','',9);
				
				$pdf->Cell(185, 8, "Não é necessário troca", '0', 0, 'C');
				if ($rs2->ntroca==1)
					$pdf->Image('images/preto.png', 85, $pdf->GetY()+2, 4, 4);
				else
					$pdf->Image('images/branco.png', 85, $pdf->GetY()+2, 4, 4);
				
				$pdf->Ln();
				
				$pdf->SetFont('Arial','B',11);
				$pdf->Cell(0, 10, strtoupper("Desinfecção: Produtos e Quantidades Utilizadas"), 0, 1, 'C');
				
				$pos_y= $pdf->GetY();
				
				$pdf->SetFont('Arial','B',9);
				
				$pdf->Cell(44, 6, "         Formol 40%", '1', 0, 'L', 1);
				if ($rs2->ver_formol40==1)
					$pdf->Image('images/preto.png', 17, $pos_y+1, 4, 4);
				else
					$pdf->Image('images/branco.png', 17, $pos_y+1, 4, 4);
				$pdf->Cell(2.5, 6, "", 0, 0);
				
				$pdf->Cell(44, 6, "         Hipoclorito 12%", '1', 0, 'L', 1);
				if ($rs2->ver_hipoclorito12==1)
					$pdf->Image('images/preto.png', 63, $pos_y+1, 4, 4);
				else
					$pdf->Image('images/branco.png', 63, $pos_y+1, 4, 4);
				$pdf->Cell(2.5, 6, "", 0, 0);
				
				$pdf->Cell(44, 6, "         Sal Grosso", '1', 0, 'L', 1);
				if ($rs2->ver_salgrosso==1)
					$pdf->Image('images/preto.png', 110, $pos_y+1, 4, 4);
				else
					$pdf->Image('images/branco.png', 110, $pos_y+1, 4, 4);
				$pdf->Cell(2.5, 6, "", 0, 0);
				
				$pdf->Cell(45, 6, "         Desibac", '1', 1, 'L', 1);
				if ($rs2->ver_desibac==1)
					$pdf->Image('images/preto.png', 157, $pos_y+1, 4, 4);
				else
					$pdf->Image('images/branco.png', 157, $pos_y+1, 4, 4);
				
				$pdf->SetFont('Arial','',9);
				
				$pdf->Cell(44, 6, $rs2->obs_formol40, '1', 0, '1', 0);
				$pdf->Cell(2.5, 6, "", 0, 0);
				
				$pdf->Cell(44, 6, $rs2->obs_hipoclorito12, '1', 0, '1', 0);
				$pdf->Cell(2.5, 6, "", 0, 0);
				
				$pdf->Cell(44, 6, $rs2->obs_salgrosso, '1', 0, '1', 0);
				$pdf->Cell(2.5, 6, "", 0, 0);
				
				$pdf->Cell(45, 6, $rs2->obs_desibac, '1', 1, '1', 0);
				
				break;
			case "3":
			
				break;
		}
		
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(38, 6, "Obs. gerais cliente:", '1', 0, 'R', 1);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(147, 6, $rs->obs_gerais, '1', 'L');
		
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(38, 6, "Obs. gerais técnico:", '1', 0, 'R', 1);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(147, 6, $rs->obs_gerais_tecnico, '1', 'L');
		
		if ($rs->obs_gerais_tecnico=="") {
			$pdf->Cell(38, 6, "", '0', 0, 'R', 0);
			$pdf->MultiCell(147, 6, "", '1', 'L');
			$pdf->Cell(38, 6, "", '0', 0, 'R', 0);
			$pdf->MultiCell(147, 6, "", '1', 'L');
		}
				
		$pdf->SetFont('Arial','B',10);
		
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(0, 10, "FINALIZAÇÃO DA ORDEM DE SERVIÇO", 0, 1, 'C');
		
		$pdf->SetFont('Arial','B',9);
		
		$pdf->Cell(40, 5, "Data e horário", 'LTR', 0, 'C', 1);
		$pdf->Cell(44, 5, "Data e horário", 'LTR', 0, 'C', 1);
		$pdf->Cell(53, 5, "", 'LTR', 0, 'C', 1);
		$pdf->Cell(48, 5, "", 'LTR', 1, 'C', 1);
		
		$pdf->Cell(40, 6, "(Início do atendimento)", 'LBR', 0, 'C', 1);
		$pdf->Cell(44, 6, "(Término do atendimento)", 'LBR', 0, 'C', 1);
		$pdf->Cell(53, 6, "Assinatura do cliente", 'LBR', 0, 'C', 1);
		$pdf->Cell(48, 6, "Assinatura do técnico", 'LBR', 1, 'C', 1);
		
		$pdf->Cell(40, 7, "___/___/______ ___:___", 'LBR', 0, 'C');
		$pdf->Cell(44, 7, "___/___/______ ___:___", 'LBR', 0, 'C');
		$pdf->Cell(53, 7, "", 'LBR', 0, 'C');
		$pdf->Cell(48, 7, "", 'LBR', 1, 'C');
		
		$pdf->Output("caixa_relatorio_". $id_usuario_sessao .".pdf", "I"); 
	}
	else
		echo "<center>Relatório não disponível!</center>";
}
?>