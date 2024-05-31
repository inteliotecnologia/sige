<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("d5", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato.php");
	
	/*
	$periodo2= explode('/', $_POST["periodo"]);
		
	$data1_mk= mktime(14, 0, 0, $periodo2[0], 1, $periodo2[1]);
	
	$dias_mes= date("t", $data1_mk);
	$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
	
	$data1= date("Y-m-d", $data1_mk);
	$data2= date("Y-m-d", $data2_mk);
	
	$data1f= desformata_data($data1);
	$data2f= desformata_data($data2);
	*/
	
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
	
	//relacao geral
	if ($_POST["tipo_relatorio"]==1) {
		
		$pdf->SetXY(7,1.75);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "ARQUIVO DE DOCUMENTOS", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, date("d/m/Y"), 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
			
		if ($_POST["id_empresa"]!="") $str_emp= "and   empresas.id_empresa = '". $_POST["id_empresa"] ."' ";
		
		$result_emp= mysql_query("select * from pessoas, pessoas_tipos, empresas
									where pessoas.id_pessoa = empresas.id_pessoa
									and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
									and   pessoas_tipos.tipo_pessoa = 'a'
									". $str_emp ."
									order by 
									pessoas.nome_rz asc ");
		
		$i=0;
		while ($rs_emp= mysql_fetch_object($result_emp)) {
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.7, $rs_emp->nome_rz, "B", 1, "L", 0);
			$pdf->Cell(0, 0.3, "", 0, 1);
			
			if ($_POST["id_departamento"]!="") $str_dep= "and   rh_departamentos.id_departamento = '". $_POST["id_departamento"] ."' ";
			
			$result_dep= mysql_query("select distinct(rh_departamentos.id_departamento) from rh_departamentos, dc_documentos_pastas
										where rh_departamentos.id_departamento = dc_documentos_pastas.id_departamento
										and   rh_departamentos.id_empresa = '". $rs_emp->id_empresa ."'
										". $str_dep ."
										order by rh_departamentos.departamento asc ");
			
			while ($rs_dep= mysql_fetch_object($result_dep)) {
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.7, pega_departamento($rs_dep->id_departamento), 0, 1, "L", 0);
				$pdf->Cell(0, 0.3, "", 0, 1);
				
				if ($_POST["id_pasta"]!="") $str_pasta= "and   dc_documentos_pastas.id_pasta = '". $_POST["id_pasta"] ."' ";
				
				if ($_POST["status_pasta"]!="") $str_pasta.= "and   dc_documentos_pastas.status_pasta = '". $_POST["status_pasta"] ."' ";
				
				$result_pasta= mysql_query("select * from dc_documentos_pastas
											where id_departamento = '". $rs_dep->id_departamento ."'
											". $str_pasta ."
											order by pasta asc
											") or die(mysql_error());
				
				if (mysql_num_rows($result_pasta)>0) {
					
					while ($rs_pasta= mysql_fetch_object($result_pasta)) {
						
						$pdf->SetFont('ARIALNARROW', '', 10);
						$pdf->Cell(0, 0.7, "  ". $rs_pasta->pasta ." - ". $rs_pasta->nome_pasta ." (". strip_tags(ativo_inativo($rs_pasta->status_pasta)) .")", "B", 1, "L", 0);
						$pdf->Cell(0, 0.3, "", 0, 1);
						
						if ($_POST["id_documento"]!="") $str .= " and dc_documentos.id_documento = '". $_POST["id_documento"] ."' ";
						if ($_POST["documento"]!="") $str .= " and dc_documentos.documento like '%". $_POST["documento"] ."%' ";
						
						$result_doc= mysql_query("select * from dc_documentos
												". $str ."
												where dc_documentos.id_pasta = '". $rs_pasta->id_pasta ."'
												order by dc_documentos.id_documento desc
												") or die(mysql_error());
						
						
						if (mysql_num_rows($result_doc)>0) {
							
							$pdf->SetFillColor(210,210,210);
							$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
							
							$pdf->Cell(0.5, 0.5, "", 0, 0, "L", 0);
							$pdf->Cell(12.5, 0.5, "DOCUMENTO", 1, 0, "L", 1);
							$pdf->Cell(2, 0.5, "EMISSÃO", 1,0, "C", 1);
							$pdf->Cell(2, 0.5, "VENCIMENTO", 1, 1, "C", 1);
							
							$pdf->SetFillColor(235,235,235);
							
							$d=0;
							while ($rs_doc= mysql_fetch_object($result_doc)) {
								if (($d%2)==0) $fill= 0;
								else $fill= 1;
								
								$pdf->Cell(0.5, 0.5, "", 0, 0, "L", 0);
								
								$pdf->SetFont('ARIALNARROW', '', 7);
								$pdf->Cell(12.5, 0.5, $rs_doc->documento, 1, 0, "L", $fill);
								
								$pdf->SetFont('ARIALNARROW', '', 8);
								$pdf->Cell(2, 0.5, desformata_data($rs_doc->data_emissao), 1, 0, "C", $fill);
								$pdf->Cell(2, 0.5, desformata_data($rs_doc->data_vencimento), 1, 1, "C", $fill);
								
								$d++;
							}
							
							$pdf->Ln();
							
						}
					}
				}//fim linhas pastas
			}
			
			$pdf->Ln(); $pdf->Ln();
			
			/*
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(2.5, 0.6, "DATA", 1, 0, "L", 1);
			$pdf->Cell(3.5, 0.6, "KM INICIAL", 1, 0, "L", 1);
			$pdf->Cell(3.5, 0.6, "KM FINAL", 1, 0, "L", 1);
			$pdf->Cell(3.5, 0.6, "KM DIA", 1, 0, "L", 1);
			$pdf->Cell(4, 0.6, "ABASTECIMENTO", 1, 1, "L", 1);
			
			$diferenca= ceil(($data2_mk-$data1_mk)/86400);
			
			for ($d=0; $d<=$diferenca; $d++) {
				$calculo_data= $data1_mk+(86400*$d);
				$data= date("d/m/Y", $calculo_data);
				$data_valida= date("Y-m-d", $calculo_data);
				$id_dia= date("w", $calculo_data);
				
				$result_ini= mysql_query("select * from tr_percursos, tr_percursos_passos
											where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   tr_percursos.id_veiculo = '". $rs_vei->id_veiculo ."'
											and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
											and   tr_percursos_passos.data_percurso = '". $data_valida ."'
											and   tr_percursos_passos.passo = '1'
											order by hora_percurso asc limit 1
											") or die(mysql_error());
				$rs_ini= mysql_fetch_object($result_ini);
				
				$result_fin= mysql_query("select * from tr_percursos, tr_percursos_passos
											where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   tr_percursos.id_veiculo = '". $rs_vei->id_veiculo ."'
											and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
											and   tr_percursos_passos.data_percurso = '". $data_valida ."'
											and   tr_percursos_passos.passo = '3'
											order by hora_percurso desc limit 1
											") or die(mysql_error());
				$rs_fin= mysql_fetch_object($result_fin);
				
				$result_aba= mysql_query("select sum(valor_total) as valor_total from fi_abastecimentos
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_veiculo = '". $rs_vei->id_veiculo ."'
										and   data = '". $data_valida ."'
										") or die(mysql_error());
				$rs_aba= mysql_fetch_object($result_aba);
				
				if (($d%2)==0) $fill= 0;
				else $fill= 1;
				
				$pdf->SetFont('ARIALNARROW', '', 9);
				$pdf->SetFillColor(235,235,235);
				
				$km_dia= ($rs_fin->km-$rs_ini->km);
				$total_km_mes+= $km_dia;
				
				$total_abastecido+= $rs_aba->valor_total;
				
				$pdf->Cell(2.5, 0.6, $data, 1, 0, "L", $fill);
				$pdf->Cell(3.5, 0.6, fnumf($rs_ini->km) ." km", 1, 0, "L", $fill);
				$pdf->Cell(3.5, 0.6, fnumf($rs_fin->km) ." km", 1, 0, "L", $fill);
				$pdf->Cell(3.5, 0.6, fnumf($km_dia) ." km", 1, 0, "L", $fill);
				$pdf->Cell(4, 0.6, "R$ ". fnum($rs_aba->valor_total), 1, 0, "L", $fill);
				
				$pdf->Ln();
			}
			
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(2.5, 0.6, "", 0, 0, "L", 0);
			$pdf->Cell(3.5, 0.6, "", 0, 0, "L", 0);
			$pdf->Cell(3.5, 0.6, "", 0, 0, "L", 0);
			$pdf->Cell(3.5, 0.6, fnumf($total_km_mes) ." km", 1, 0, "L", 1);
			$pdf->Cell(4, 0.6, "R$ ". fnum($total_abastecido), 1, 0, "L", 1);
			
			$i++;
			*/
			
		}//fim empresas
	}
	//relação de pastas
	else {
		$pdf->SetXY(7,1.75);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "ARQUIVO DE PASTAS", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, date("d/m/Y"), 0 , 1, 'R');
		
		$pdf->Ln();$pdf->Ln();
		
			
		if ($_POST["id_empresa_aqui"]!="") $str_emp= "and   empresas.id_empresa = '". $_POST["id_empresa_aqui"] ."' ";
		
		$result_emp= mysql_query("select * from pessoas, pessoas_tipos, empresas
									where pessoas.id_pessoa = empresas.id_pessoa
									and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
									and   pessoas_tipos.tipo_pessoa = 'a'
									". $str_emp ."
									order by 
									pessoas.nome_rz asc ");
		
		$i=0;
		while ($rs_emp= mysql_fetch_object($result_emp)) {
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.7, $rs_emp->nome_rz, "B", 1, "L", 0);
			$pdf->Cell(0, 0.3, "", 0, 1);
			
			if ($_POST["id_departamento"]!="") $str_dep= "and   rh_departamentos.id_departamento = '". $_POST["id_departamento"] ."' ";
			
			$result_dep= mysql_query("select distinct(rh_departamentos.id_departamento) from rh_departamentos, dc_documentos_pastas
										where rh_departamentos.id_departamento = dc_documentos_pastas.id_departamento
										and   rh_departamentos.id_empresa = '". $rs_emp->id_empresa ."'
										". $str_dep ."
										order by rh_departamentos.departamento asc ");
			
			while ($rs_dep= mysql_fetch_object($result_dep)) {
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.7, pega_departamento($rs_dep->id_departamento), 0, 1, "L", 0);
				$pdf->Cell(0, 0.3, "", 0, 1);
				
				$str_pasta= "";
				
				if ($_POST["id_pasta"]!="") $str_pasta .= "and   dc_documentos_pastas.id_pasta = '". $_POST["id_pasta"] ."' ";
				if ($_POST["status_pasta"]!="") $str_pasta .= "and   dc_documentos_pastas.status_pasta = '". $_POST["status_pasta"] ."' ";
				if ($_POST["nome_pasta"]!="") $str_pasta .= "and   dc_documentos_pastas.nome_pasta like '%". $_POST["nome_pasta"] ."%' ";
				
				$result_pasta= mysql_query("select * from dc_documentos_pastas
											where id_departamento = '". $rs_dep->id_departamento ."'
											". $str_pasta ."
											order by pasta asc
											") or die(mysql_error());
				
				if (mysql_num_rows($result_pasta)>0) {
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					
					$pdf->Cell(0.5, 0.5, "", 0, 0, "L", 0);
					$pdf->Cell(1, 0.5, "Nº", 1, 0, "C", 1);
					$pdf->Cell(14, 0.5, "NOME DA PASTA", 1, 0, "L", 1);
					$pdf->Cell(2, 0.5, "SITUAÇÃO", 1, 1, "L", 1);
					
					$pdf->SetFillColor(235,235,235);
					
					$d=0;
					while ($rs_pasta= mysql_fetch_object($result_pasta)) {
						
						if (($d%2)==0) $fill= 0;
						else $fill= 1;
						
						$pdf->Cell(0.5, 0.5, "", 0, 0, "L", 0);
						$pdf->SetFont('ARIALNARROW', '', 8);
						$pdf->Cell(1, 0.5, $rs_pasta->pasta, 1, 0, "C", $fill);
						
						$pdf->SetFont('ARIALNARROW', '', 7);
						$pdf->Cell(14, 0.5, $rs_pasta->nome_pasta, 1, 0, "L", $fill);
						
						$pdf->Cell(2, 0.5, strip_tags(ativo_inativo($rs_pasta->status_pasta)), 1, 1, "L", $fill);
						
						$d++;
						
					}
					
					$pdf->Ln();
					
				}//fim linhas pastas
			}
			
			$pdf->Ln(); $pdf->Ln();
			
		}//fim empresas
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("documento_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>