<?
require_once("conexao.php");
require_once("funcoes.php");

if (pode("ey", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	
	if ($_POST["id_contrato"]!="") $str = " and   pessoas.id_contrato = '". $_POST["id_contrato"] ."' ";
	
	if ($_POST["tipo_relatorio"]=="c") {
		
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
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "CRONOGRAMA DE COLETA/ENTREGA", 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
		
		$pdf->Ln();
		
		for ($k=1; $k<3; $k++) {
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
			
			$pdf->Cell(0, 0.7, strtoupper(pega_coleta_entrega($k)), "B", 1, "L", 0);
			$pdf->Cell(0, 0.3, "", 0, 1);
			
			$result= mysql_query("select DISTINCT(tr_cronograma.id_cliente)
										from pessoas, tr_cronograma
										where pessoas.id_pessoa = tr_cronograma.id_cliente
										and   pessoas.id_cliente_tipo = '1'
										and   tr_cronograma.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   tr_cronograma.tipo = '$k'
										". $str ."
										order by tr_cronograma.id_cliente asc,
										tr_cronograma.id_dia asc,
										tr_cronograma.tipo asc,
										tr_cronograma.hora_cronograma asc
										") or die(mysql_error());
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(3, 0.6, "CLIENTE", 1, 0, "L", 1);
			
			for ($i=0; $i<7; $i++) {
				if ($i==6) $quebra=1;
				else $quebra=0;
				
				$pdf->Cell(2, 0.6, strtoupper(substr(traduz_dia($i), 0, 3)), 1, $quebra, "L", 1);
			}
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->SetFillColor(235,235,235);
			
			$j=0;
			while ($rs= mysql_fetch_object($result)) {
				
				if (($j%2)==0) $fill= 0;
				else $fill= 1;
				
				$pdf->Cell(3, 0.6, pega_sigla_pessoa($rs->id_cliente), 1, 0, "L", $fill);
			
				for ($i=0; $i<7; $i++) {
					if ($i==6) $quebra=1;
					else $quebra=0;
					
					$result_hor= mysql_query("select * from tr_cronograma
												where id_cliente = '". $rs->id_cliente ."'
												and   id_dia = '". $i ."'
												and   tipo = '$k'
												order by hora_cronograma
												limit 2
												");
					$var="";
					
					$linhas_hor= mysql_num_rows($result_hor);
					
					if ($linhas_hor>0) $largura= (2/$linhas_hor);
					else $largura=1;
					
					$l=1;
					while ($rs_hor= mysql_fetch_object($result_hor)) {
						if ($l==$linhas_hor) $add= "";
						else $add= ", ";
						
						$var[$l]= substr($rs_hor->hora_cronograma, 0, 5);
						//$var.= substr($rs_hor->hora_cronograma, 0, 5) . $add;
						
						$l++;
					}
					
					$pdf->Cell(1, 0.6, $var[1], 1, 0, "L", $fill);
					$pdf->Cell(1, 0.6, $var[2], 1, $quebra, "L", $fill);
					
					unset($var);
				}
				
				$j++;
			}
			
			$pdf->Ln();
		}
	}
	else {
		require("includes/fpdf/modelo_paisagem.php");
		
		$pdf=new PDF("L", "cm", "A4");
		$pdf->SetMargins(1.5, 1, 1.5);
		$pdf->SetAutoPageBreak(true, 1.5);
		$pdf->SetFillColor(210,210,210);
		$pdf->AddFont('ARIALNARROW');
		$pdf->AddFont('ARIAL_N_NEGRITO');
		$pdf->AddFont('ARIAL_N_ITALICO');
		$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
		$pdf->SetFont('ARIALNARROW');
		
		if ($_POST["id_contrato"]!="") $str_contrato =" and   id_contrato = '". $_POST["id_contrato"] ."' ";
		
		$result_contrato= mysql_query("select * from fi_contratos
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										$str_contrato
										order by id_contrato asc ") or die(mysql_error());
		$i=0;
		while ($rs_contrato = mysql_fetch_object($result_contrato)) {
			
		
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$periodo2= explode('/', $_POST["periodo"]);
		
			$data1_mk= mktime(14, 0, 0, $periodo2[0], 1, $periodo2[1]);
			
			$dias_mes= date("t", $data1_mk);
			$data2_mk= mktime(14, 0, 0, $periodo2[0], $dias_mes, $periodo2[1]);
			
			$data1= date("Y-m-d", $data1_mk);
			$data2= date("Y-m-d", $data2_mk);
			
			$data1f= desformata_data($data1);
			$data2f= desformata_data($data2);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "CRONOGRAMA DE ". strtoupper(pega_coleta_entrega($_POST["tipo"])), 0 , 1, 'R');
		
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			
			$pdf->Cell(0, 0.5, pega_contrato($rs_contrato->id_contrato) ." (". traduz_mes($periodo2[0]) ."/". $periodo2[1] .")", 0, 1, "R", 0);
			$pdf->Cell(0, 0.5, "", 0, 1);
			
			
			
			$result= mysql_query("select DISTINCT(tr_cronograma.id_cliente)
										from pessoas, tr_cronograma
										where pessoas.id_pessoa = tr_cronograma.id_cliente
										and   pessoas.id_cliente_tipo = '1'
										and   tr_cronograma.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   tr_cronograma.tipo = '$k'
										". $str ."
										order by tr_cronograma.id_cliente asc,
										tr_cronograma.id_dia asc,
										tr_cronograma.tipo asc,
										tr_cronograma.hora_cronograma asc
										") or die(mysql_error());
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(1.6, 0.5, "DATA", "LTR", 0, "L", 1);
			
			$result_clientes= mysql_query("select * from pessoas, pessoas_tipos
												where pessoas.id_pessoa = pessoas_tipos.id_pessoa
												and   pessoas_tipos.tipo_pessoa = 'c'
												and   pessoas.status_pessoa = '1'
												and   pessoas.id_cliente_tipo = '1'
												and   pessoas.id_contrato = '". $rs_contrato->id_contrato ."'
												order by pessoas_tipos.num_pessoa asc,
												pessoas.nome_rz asc
												") or die(mysql_error());
			$linhas_clientes= mysql_num_rows($result_clientes);
			
			$largura= (25/$linhas_clientes);
			
			$i=1;
			while ($rs_clientes= mysql_fetch_object($result_clientes)) {
				
				if ($i==$linhas_clientes) $quebra=1;
				else $quebra=0;
				
				$pdf->Cell($largura, 0.5, $rs_clientes->sigla, "LTR", $quebra, "C", 1);
				
				$i++;
			}
			
			// ------------------------------
			$pdf->SetFont('ARIALNARROW', '', 6);
			
			$pdf->Cell(1.6, 0.2, "", "LRB", 0, "L", 1);
			
			$result_clientes= mysql_query("select * from pessoas, pessoas_tipos
												where pessoas.id_pessoa = pessoas_tipos.id_pessoa
												and   pessoas_tipos.tipo_pessoa = 'c'
												and   pessoas.status_pessoa = '1'
												and   pessoas.id_cliente_tipo = '1'
												and   pessoas.id_contrato = '". $rs_contrato->id_contrato ."'
												order by pessoas_tipos.num_pessoa asc,
												pessoas.nome_rz asc
												") or die(mysql_error());
			$linhas_clientes= mysql_num_rows($result_clientes);
			
			$largura= (25/$linhas_clientes);
			$meia_largura= ($largura/2);
			
			$i=1;
			while ($rs_clientes= mysql_fetch_object($result_clientes)) {
				
				if ($i==$linhas_clientes) $quebra=1;
				else $quebra=0;
					
				$pdf->Cell($meia_largura, 0.2, "P", "LRB", 0, "C", 1);
				$pdf->Cell($meia_largura, 0.2, "R", "LRB", $quebra, "C", 1);
				
				$i++;
			}
			// ------------------------------
			
			$diferenca= ceil(($data2_mk-$data1_mk)/86400);
			
			for ($d=0; $d<=$diferenca; $d++) {
				$calculo_data= $data1_mk+(86400*$d);
				$data= date("d/m/Y", $calculo_data);
				$data_valida= date("Y-m-d", $calculo_data);
				$id_dia= date("w", $calculo_data);
				
				if (($d%2)!=0) $fill=1;
				else $fill= 0;
				
				$pdf->SetFont('ARIALNARROW', '', 9);
				$pdf->SetFillColor(235,235,235);
				
				$pdf->Cell(1.6, 0.475, $data, 1, 0, "L", $fill);
			
				$result_clientes= mysql_query("select * from pessoas, pessoas_tipos
													where pessoas.id_pessoa = pessoas_tipos.id_pessoa
													and   pessoas_tipos.tipo_pessoa = 'c'
													and   pessoas.status_pessoa = '1'
													and   pessoas.id_cliente_tipo = '1'
													and   pessoas.id_contrato = '". $rs_contrato->id_contrato ."'
													order by pessoas_tipos.num_pessoa asc,
													pessoas.nome_rz asc
													") or die(mysql_error());
				$linhas_clientes= mysql_num_rows($result_clientes);
				
				$largura= (25/$linhas_clientes);
				$meia_largura= ($largura/2);
				
				$pdf->SetFont('ARIALNARROW', '', 7);
				
				$i=1;
				while ($rs_clientes= mysql_fetch_object($result_clientes)) {
					
					if ($i==$linhas_clientes) $quebra=1;
					else $quebra=0;
					
					$cronograma_aqui= "";
					
					$result_cronograma= mysql_query("select *
														from tr_cronograma
														where tr_cronograma.id_cliente = '". $rs_clientes->id_pessoa ."'
														and   tr_cronograma.id_empresa = '". $_SESSION["id_empresa"] ."'
														and   tr_cronograma.tipo = '". $_POST["tipo"] ."'
														and   tr_cronograma.id_dia = '$id_dia'
														order by 
														tr_cronograma.hora_cronograma asc
														") or die(mysql_error());
					
					$linhas_cronograma= mysql_num_rows($result_cronograma);
					$j=1;
					while ($rs_cronograma= mysql_fetch_object($result_cronograma)) {
						$cronograma_aqui.= substr($rs_cronograma->hora_cronograma, 0, 5);
						if ($j!=$linhas_cronograma) $cronograma_aqui.="/";
						
						$j++;
					}
					
					$real_aqui= "";
					
					$result_real= mysql_query("select *
												from tr_percursos, tr_percursos_passos
												where tr_percursos.id_percurso = tr_percursos_passos.id_percurso
												and   tr_percursos_passos.id_cliente = '". $rs_clientes->id_pessoa ."'
												and   tr_percursos_passos.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   tr_percursos.tipo = '". $_POST["tipo"] ."'
												and   tr_percursos_passos.data_percurso = '$data_valida'
												and   tr_percursos_passos.hora_percurso <> '00:00:00'
												order by 
												tr_percursos_passos.hora_percurso asc
												") or die(mysql_error());
					
					$linhas_real= mysql_num_rows($result_real);
					$j=1;
					while ($rs_real= mysql_fetch_object($result_real)) {
						$real_aqui.= substr($rs_real->hora_percurso, 0, 5);
						if ($j!=$linhas_real) $real_aqui.="/";
						
						$j++;
					}
					
					$pdf->Cell($meia_largura, 0.475, $cronograma_aqui, 1, 0, "C", $fill);
					$pdf->Cell($meia_largura, 0.475, $real_aqui, 1, $quebra, "C", $fill);
					
					$i++;
				}
				
			}
			
			$pdf->Cell(0, 0.2, "", 0, 1, "L", 0);
			
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(0, 0.3, "P= PROGRAMADA | R= REAL", 0, 1, "L", 0);
			
			/*
			for ($i=0; $i<7; $i++) {
				if ($i==6) $quebra=1;
				else $quebra=0;
				
				$pdf->Cell(2, 0.6, strtoupper(substr(traduz_dia($i), 0, 3)), 1, $quebra, "L", 1);
			}
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->SetFillColor(235,235,235);
			
			$j=0;
			while ($rs= mysql_fetch_object($result)) {
				
				if (($j%2)==0) $fill= 0;
				else $fill= 1;
				
				$pdf->Cell(3, 0.6, pega_sigla_pessoa($rs->id_cliente), 1, 0, "L", $fill);
			
				for ($i=0; $i<7; $i++) {
					if ($i==6) $quebra=1;
					else $quebra=0;
					
					$result_hor= mysql_query("select * from tr_cronograma
												where id_cliente = '". $rs->id_cliente ."'
												and   id_dia = '". $i ."'
												and   tipo = '$k'
												order by hora_cronograma
												");
					$var="";
					
					$linhas_hor= mysql_num_rows($result_hor);
					
					$l=1;
					while ($rs_hor= mysql_fetch_object($result_hor)) {
						if ($l==$linhas_hor) $add= "";
						else $add= ", ";
						
						$var.= substr($rs_hor->hora_cronograma, 0, 5) . $add;
						
						$l++;
					}
					
					$pdf->Cell(2, 0.6, $var, 1, $quebra, "L", $fill);
				}
				
				$j++;
			}
			
			$pdf->Ln();
			*/
		}//fim contrato
	}
	
	$pdf->AliasNbPages(); 
	$pdf->Output("cronograma_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
}
?>