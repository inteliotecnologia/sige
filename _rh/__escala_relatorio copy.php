<?
require_once("conexao.php");
require_once("funcoes.php");

$_SESSION["id_empresa_atendente2"]= 4;

if (pode_algum("rhw", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_paisagem.php");
	
	$periodo= explode('/', $_POST["periodo"]);
		
	$data1_mk= mktime(14, 0, 0, $periodo[0], 1, $periodo[1]);
	
	$dias_mes= date("t", $data1_mk);
	$data2_mk= mktime(14, 0, 0, $periodo[0], $dias_mes, $periodo[1]);
	
	$data1= date("Y-m-d", $data1_mk);
	$data2= date("Y-m-d", $data2_mk);
	
	$data1f= desformata_data($data1);
	$data2f= desformata_data($data2);
	
	$dataq= explode("/", $data1f);
	$mes_extenso= traduz_mes($dataq[1]);
	
	$calculo_data_aqui= (soma_data($data1, 0, 1, 0));
	$calculo_data_aqui_mk= faz_mk_data($calculo_data_aqui);
	
	$pdf=new PDF("L", "cm", "A4");
	$pdf->SetMargins(1.5, 1.5, 1.5);
	$pdf->SetAutoPageBreak(true, 3);
	$pdf->SetFillColor(200,200,200);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	if ($_POST["id_turno"]=="") {
		$result= mysql_query("select * from  rh_departamentos
								where 1=1
								and   rh_departamentos.id_departamento =  '". $_POST["id_departamento"] ."'
								order by rh_departamentos.departamento asc
								") or die(mysql_error());
	}
	else
		$result= mysql_query("select * from  rh_departamentos, rh_turnos
								where 1=1
								and   rh_departamentos.id_departamento = rh_turnos.id_departamento
								and   rh_turnos.id_turno = '". $_POST["id_turno"] ."'
								order by rh_departamentos.departamento asc
								") or die(mysql_error());
	$i=0;
	while ($rs= mysql_fetch_object($result)) {		
		$pdf->AddPage();
		
		$pdf->SetXY(16.9, 1.75);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		
		if ($rs->turno!="") $str_turno= " - ". $rs->turno;
		else $str_turno= "";
		
		if (($_POST["id_departamento"]==5) || ($_POST["id_departamento"]==6) || ($_POST["id_departamento"]==23)) {
			$str = " and   (rh_carreiras.id_departamento =  '5'
							or rh_carreiras.id_departamento =  '6'
							or rh_carreiras.id_departamento =  '23') ";
			$titulo_pg= "TRANSPORTE";
		}
		else {
			$str = " and   rh_carreiras.id_departamento = '". $rs->id_departamento ."' ";
			$titulo_pg= $rs->departamento . $str_turno;
		}
		
		$pdf->Cell(0, 0.6, "ESCALA DE FUNCIONÁRIOS (". $titulo_pg .")", 0, 1, "R");
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
		$pdf->Cell(0, 0.6, strtoupper($mes_extenso ."/". $dataq[2]), 0 , 1, "R");
		
		$pdf->Ln();
		
		// ------------- tabela
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		
		$pdf->Cell(5.5, 1.4, "FUNCIONÁRIOS", 1 , 0, "L", 1);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$diferenca_tit= date("d", $data2_mk-$data1_mk);
		$total_dias_mes= date("t", $data1_mk);
		
		if ($total_dias_mes==28) $largura= 0.72;
		if ($total_dias_mes==29) $largura= 0.71;
		if ($total_dias_mes==30) $largura= 0.70;
		if ($total_dias_mes==31) $largura= 0.69;
		
		//repetir todos os dias do intervalo
		for ($t=0; $t<=$diferenca_tit; $t++) {
			$calculo_data_tit= $data1_mk+(86400*$t);
			
			$dia_tit= date("d", $calculo_data_tit);
			$dia_semana_tit= date("w", $calculo_data_tit);
			$vale_dia_tit= date("Y-m-d", $calculo_data_tit);
			
			$pdf->Cell($largura, 0.7, $dia_tit, 1 , 0, "C", 1);
			//echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->SetXY(7, 4.85);
		
		//repetir todos os dias do intervalo
		for ($t=0; $t<=$diferenca_tit; $t++) {
			$calculo_data_tit= $data1_mk+(86400*$t);
			
			$dia_tit= date("d", $calculo_data_tit);
			$dia_semana_tit= date("w", $calculo_data_tit);
			$vale_dia_tit= date("Y-m-d", $calculo_data_tit);
			
			$pdf->Cell($largura, 0.7, traduz_dia_resumido($dia_semana_tit), 1 , 0, "C", 1);
			//echo traduz_dia_resumido($dia_semana_tit);
		}
		
		$pdf->SetXY(1.5, 5.5);
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->SetFillColor(230,230,230);
		
		if ($_POST["id_turno"]!="") $str .= " and   rh_carreiras.id_turno =  '". $_POST["id_turno"] ."'";
		else $str .= "";
		
		$j=0;
		$result_fun= mysql_query("select *
									from  pessoas, rh_funcionarios, rh_carreiras
									where pessoas.id_pessoa = rh_funcionarios.id_pessoa
									and   pessoas.tipo = 'f'
									". $str ."
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_carreiras.atual = '1'
									
									and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_funcionarios.status_funcionario <> '2'
									and   rh_funcionarios.status_funcionario <> '0'
									order by pessoas.nome_rz asc
									") or die(mysql_error());
		
		while ($rs_fun= mysql_fetch_object($result_fun)) {
			
			$data_admissao= formata_data_hifen(pega_data_admissao($rs_fun->id_funcionario));
			$data_admissao_mk= faz_mk_data($data_admissao);
			
			//01-02-2009
			
			/*
			if ($calculo_data_aqui_mk>=$data_admissao_mk) {
				echo "<strong>". $rs_fun->nome_rz .") ". $calculo_data_aqui ." - ";
				echo $data_admissao ." </strong><br /><br />";
			}
			else {
				echo $rs_fun->nome_rz .") ". $calculo_data_aqui ." - ";
				echo $data_admissao ." <br /><br />";
			} */
			
			//se a data atual for maior ou igual a data de admissao === ELE JÁ É FUNCIONÁRIO DA EMPRESA
			if ($calculo_data_aqui_mk>=$data_admissao_mk) {
			//if (1==1) {
				if (($j%2)==0) {
					$fill=1;
					$pdf->SetFillColor(230,230,230);
				}
				else $fill= 0;
				
				$linhas_afastamento= array();
				
				//repetir todos os dias do intervalo
				for ($d=0; $d<=$diferenca; $d++) {
					$calculo_data= $data1_mk+(86400*$d);
					$dia_tit= date("d", $calculo_data);
					$dia_semana= date("w", $calculo_data);
					$vale_dia= date("Y-m-d", $calculo_data);
					$mes= date("m", $calculo_data);
					
					$result_afa= mysql_query("select * from rh_afastamentos_dias
												where id_funcionario = '". $rs_fun->id_funcionario ."'
												and   data = '". $vale_dia ."'
												") or die(mysql_error());
					$linhas_afastamento[$d]= mysql_num_rows($result_afa);
					
				}
				
				$result_afa_periodo= mysql_query("select * from rh_afastamentos_dias
												where id_funcionario = '". $rs_fun->id_funcionario ."'
												and   DATE_FORMAT(data, '%m/%Y') = '". $_POST["periodo"] ."'
												") or die(mysql_error());
				$linhas_afastamento_periodo= mysql_num_rows($result_afa_periodo);
				
				if ($linhas_afastamento_periodo>0) {
					$mostrar_esse= false;
					
					//repetir todos os dias do intervalo
					for ($d=0; $d<=$diferenca; $d++) {
						if ($linhas_afastamento[$d]==0) {
							$mostrar_esse= true;
							break;
						}
					}
				}
				else {
					$mostrar_esse= true;
				}
				
				if (1==1) {
					$pdf->Cell(5.5, 0.5, $rs_fun->nome_rz, 1 , 0, "L", $fill);
					
					$diferenca= date("d", $data2_mk-$data1_mk);
								
					//repetir todos os dias do intervalo
					for ($d=0; $d<=$diferenca; $d++) {
						$calculo_data= $data1_mk+(86400*$d);
						$dia_tit= date("d", $calculo_data);
						$dia_semana= date("w", $calculo_data);
						$vale_dia= date("Y-m-d", $calculo_data);
						$mes= date("m", $calculo_data);
						
						$result_esc= mysql_query("select * from rh_escala
													where id_funcionario = '". $rs_fun->id_funcionario ."'
													and   data_escala = '". $vale_dia ."'
													") or die(mysql_error());
						$rs_esc= mysql_fetch_object($result_esc);
						
						$result_afa= mysql_query("select * from rh_afastamentos_dias
													where id_funcionario = '". $rs_fun->id_funcionario ."'
													and   data = '". $vale_dia ."'
													") or die(mysql_error());
						$linhas_afastamento= mysql_num_rows($result_afa);
						
						if ($linhas_afastamento>0) {
							$rs_afa= mysql_fetch_object($result_afa);
							
							$result_af_dia= mysql_query("select *, DATE_FORMAT(data_emissao, '%d/%m/%Y') as data_emissao2 from rh_afastamentos
														where id_afastamento = '". $rs_afa->id_afastamento ."'
														") or die(mysql_error());
							$rs_af_dia= mysql_fetch_object($result_af_dia);
							
							$afastamento= "<strong>". pega_tipo_afastamento($rs_af_dia->tipo_afastamento) ."</strong><br />";
							$afastamento .= "<strong>Data de emissão:</strong> ". $rs_af_dia->data_emissao2 ."<br />";
							$afastamento .= "<strong>Dias:</strong> ". $rs_af_dia->qtde_dias ."<br />";
							
							$trabalha= "-";
						}
						else {
							if ($rs_esc->trabalha==1) $trabalha= "T";
							else $trabalha= "F";
						}
						
						if ($dia_tit==$total_dias_mes) $flutua=1;
						else $flutua= 0;
						
						$pdf->Cell($largura, 0.5, $trabalha, 1 , $flutua, "C", $fill);
						
						$i++;
					}
					$j++;
				}//fim mostrar_esse
			}//fim admissao
		}
		
	}//fim while turnos
	
	$pdf->AliasNbPages(); 
	$pdf->Output("escala_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>