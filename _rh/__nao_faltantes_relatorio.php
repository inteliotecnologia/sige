<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

define('FPDF_FONTPATH','includes/fpdf/font/');
require("includes/fpdf/fpdf.php");
require("includes/fpdf/modelo_retrato_rh.php");
	
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
	$pdf->Cell(0, 0.6, "NÃO FALTANTES", 0 , 1, 'R');
	
	$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
	$pdf->Cell(0, 0.6, "ANO DE ". $_POST["periodo"], 0 , 1, 'R');
	
	$pdf->Ln();$pdf->Ln();
	
	$j=0;
	$result_fun= mysql_query("select rh_funcionarios.id_funcionario
								from  rh_funcionarios, rh_carreiras
								where rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.atual = '1'
								and   rh_funcionarios.status_funcionario = '1'
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								/* and   rh_funcionarios.id_funcionario >= '120' */
								") or die(mysql_error());
	while ($rs_fun= mysql_fetch_object($result_fun)) {
		$vetor[$j]= $rs_fun->id_funcionario;
		
		$j++;
	}
	
	for ($i=1; $i<4; $i++) {
		switch ($i) {
			case 1:
					$data1= $_POST["periodo"] ."-01-01";
					$data2= $_POST["periodo"] ."-04-31";
					$trimestre= "JANEIRO À ABRIL";
					break;
			case 2:
					$data1= $_POST["periodo"] ."-05-01";
					$data2= $_POST["periodo"] ."-08-31";
					$trimestre= "MAIO À AGOSTO";
					break;
			case 3:
					$data1= $_POST["periodo"] ."-09-01";
					$data2= $_POST["periodo"] ."-12-31";
					$trimestre= "SETEMBRO À DEZEMBRO";
					break;
		}
		
		unset($vetor_nao_faltantes);
		
		$l=0;
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		
		$pdf->Cell(0, 0.8, $trimestre, 'B', 1, 'L', 0);
		$pdf->Ln();
		
		for ($k=0; $k<$j; $k++) {
			
			$result= mysql_query("select * from rh_escala
								 	where id_funcionario = '". $vetor[$k] ."'
									and   data_escala >= '$data1'
									and   data_escala <= '$data2'
									and   trabalha = '1'
									");
			//echo $k . " ". $j ."<br />";
			/*echo "select * from rh_escala
								 	where id_funcionario = '". $vetor[$k] ."'
									and   data_escala >= '$data1'
									and   data_escala <= '$data2'
									and   trabalha = '1'
									<br />
									";
			
			*/
			
			//echo mysql_num_rows($result) ." ";
			
			if (mysql_num_rows($result)>0) {
				$passa_nao_faltante= true;
				
				while ( ($rs= mysql_fetch_object($result)) && ($passa_nao_faltante) ) {
					$result_ponto= mysql_query("select * from rh_ponto
												where id_funcionario = '". $vetor[$k] ."'
												and   vale_dia = '". $rs->data_escala ."'
												limit 1
												");
					/*
					echo "select * from rh_ponto
												where id_funcionario = '". $vetor[$k] ."'
												and   data_batida = '". $rs->data_escala ."'
												limit 1
												<br />";
					*/
					
					//funcionario faltou em dia que deveria trabalhar...
					if (mysql_num_rows($result_ponto)==0) {
						$passa_nao_faltante= false;
					}
					
					/*
					echo $rs_fun->nome_rz ." &raquo; ". $rs->data_escala ."<br />";
					*/
				}
				
				if ($passa_nao_faltante) {
					$vetor_nao_faltantes[$l]= $vetor[$k];
					$l++;
				}
			}
		}
		
		//echo $l ."<br />";
		
		$pdf->SetFont('ARIALNARROW', '', 10);
		
		for ($m=0; $m<$l; $m++) {
			$pdf->Cell(0, 0.6, "    ". pega_funcionario($vetor_nao_faltantes[$m]), 0, 1, 'L');
		}
		
		$pdf->Ln();
	}

	$pdf->AliasNbPages(); 
	$pdf->Output("nao_faltantes_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
?>