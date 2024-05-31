<?
	require_once("conexao.php");
	require_once("funcoes.php");
	
	$id_versao= checa_versao_contatos('i', $_SESSION["id_empresa"], $_SESSION["id_usuario"]);
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
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
	
	//if ($_GET["tipo_contato"]==2) $limite=2;
	//else $limite=1;
	
	//for ($r=0; $r<$limite; $r++) {
	
		$pdf->AddPage();
		
		if ($_GET["tipo_contato"]==2) {
			$str_tit= strip_tags(strtoupper(ativo_inativo($_GET["status_funcionario"]))) ."S";
		}
		else {
			$str_tit="";
		}
		
		$pdf->SetXY(7,1.75);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 0.6, "LISTA TELEFÔNICA - ". strtoupper(pega_tipo_contato($_GET["tipo_contato"])) ." ". $str_tit, 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, "VERSÃO Nº ". $id_versao ." DE ". date("d/m/Y H:i:s"), 0, 1, 'R');
		
		$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		
		if ($_GET["rel"]!="") $str .= " and   INSTR(rel, '". $_GET["rel"] ."')<>'0' ";
		
		$i_letra= 0;
		for ($i='a'; $i!="aa"; $i++) {
			
			//funcionários
			if ($_GET["tipo_contato"]==2) {
				//ilhados
				if ($_GET["status_funcionario"]==-2)
					$result= mysql_query("select * from  tel_contatos
												where tel_contatos.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   tel_contatos.id_pessoa = '0'
												and   tel_contatos.nome like '". $i ."%'
												". $str ."
												order by nome asc
												") or die(mysql_error());
				else
					$result= mysql_query("select * from  tel_contatos, pessoas, rh_funcionarios
												where tel_contatos.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   tel_contatos.id_pessoa = pessoas.id_pessoa
												and   pessoas.id_pessoa = rh_funcionarios.id_pessoa
												and   ABS(rh_funcionarios.status_funcionario) = '". $_GET["status_funcionario"] ."'
												and   tel_contatos.nome like '". $i ."%'
												". $str ."
												order by nome asc
												") or die(mysql_error());
			}
			else {
					
					$result= mysql_query("select * from  tel_contatos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   tipo_contato = '". $_GET["tipo_contato"] ."'
											and   nome like '". $i ."%'
											$str
											order by nome asc
											") or die(mysql_error());
			}
	
			if (mysql_num_rows($result)>0) {
				
				if ((($i!='a') && ($i_letra>0)) && ($_GET["rel"]!="s")) $pdf->AddPage();
				
				$pdf->SetFillColor(200,200,200);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 15);
				$pdf->Cell(0, 1, strtoupper($i), 'B', 1, "L", 0);
				
				$pdf->Cell(0, 0.35, "", 0, 1, "L", 0);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
				$pdf->Cell(8, 0.6, "NOME", 1, 0, "L", 1);
				
				for ($j=1; $j<5; $j++) {
					if ($j==4) $quebra=1;
					else $quebra= 0;
					
					$pdf->Cell(2.25, 0.6, "TELEFONE ". $j, 1, $quebra, "C", 1);
				}
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->SetFillColor(230,230,230);
		
				
				$m=0;
				while ($rs= mysql_fetch_object($result)) {
					if (($m%2)==0) $fill= 0;
					else $fill= 1;
					
					$result_tel= mysql_query("select * from tel_contatos_telefones
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_contato = '". $rs->id_contato ."'
												order by tipo asc
												");
					unset($telefone);
					unset($classe_tel);
					
					$k=1;
					while ($rs_tel= mysql_fetch_object($result_tel)) {
						$telefone[$k] = $rs_tel->telefone ."";
						$obs[$k] = $rs_tel->obs ."";
						
						$k++;
					}
					
					$pdf->Cell(8, 0.5, $rs->nome, 1, 0, "L", $fill);
					
					for ($j=1; $j<5; $j++) {
						if ($j==4) $quebra=1;
						else $quebra= 0;
						
						$pdf->Cell(2.25, 0.5, $telefone[$j], 1, $quebra, "C", $fill);
					}
					
					$m++;
				}//fim while contatos	
				
				$pdf->Cell(0, 0.25, "", 0, 1, "L", 0);
				
				$i_letra++;
			}//fim mysql_num_rows
			
		}//fim for $i
	//}//fim r
	
	$pdf->AliasNbPages(); 
	
	$pdf->Output("contatos_". date("d-m-Y_H:i:s") .".pdf", "I");
?>