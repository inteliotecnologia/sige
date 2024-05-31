<?
require_once("conexao.php");
require_once("funcoes.php");

if ($_GET[id_funcionario]!="") $id_funcionario= $_GET[id_funcionario];
if ($_POST[id_funcionario]!="") $id_funcionario= $_POST[id_funcionario];

if ($id_funcionario!="") $_SESSION["id_empresa_atendente2"]= pega_empresa_rel($id_funcionario);
else $_SESSION["id_empresa_atendente2"]= 4;

//if ($_SESSION["id_empresa_atendente2"]=="") $_SESSION["id_empresa_atendente2"]= $_SESSION["id_empresa"];

if (pode("rvh4", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	
	class PDF extends FPDF {
		//Page header
		function Header() {
			//Title
			$this->Image("". CAMINHO ."empresa_". $_SESSION["id_empresa_atendente2"] .".jpg", 0.5, 0.3, 5, 1.9287);
		} 

		/*//Page footer
		function Footer() {
			$this->SetXY(0.5,27.5);
			$this->SetFont('ARIALNARROW', '', 11);
			$this->Cell(19, 1, "Esterilav - Lavação e Esterilização Hospitalar", 0 , 1, "L");			
		}
		*/
	}
	
	$result_empresa= mysql_query("select * from  pessoas, rh_enderecos, empresas, cidades, ufs
									where empresas.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   empresas.id_pessoa = pessoas.id_pessoa
									and   pessoas.id_pessoa = rh_enderecos.id_pessoa
									and   rh_enderecos.id_cidade = cidades.id_cidade
									and   cidades.id_uf = ufs.id_uf
									") or die(mysql_error());
	$rs_empresa= mysql_fetch_object($result_empresa);
	
	if ( ($_GET["data1"]!="") && ($_GET["data2"]!="") ) {
		$data1= formata_data_hifen($_GET["data1"]); $data1f= $_GET["data1"];
		$data2= formata_data_hifen($_GET["data2"]); $data2f= $_GET["data2"];
		
		$data1_mk= faz_mk_data($data1);
		$data2_mk= faz_mk_data($data2)+14400;
	}
	else {
		$periodo= explode('/', $_GET["periodo"]);
		
		$data1_mk= mktime(20, 0, 0, $periodo[0]-1, 26, $periodo[1]);
		$data2_mk= mktime(16, 0, 0, $periodo[0], 25, $periodo[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
	
	$pdf=new PDF("L", "cm", "A4");
	$pdf->SetLeftMargin(0.5);
	$pdf->SetRightMargin(0.5);
	$pdf->SetAutoPageBreak(true, 1);
	$pdf->SetFillColor(240,240,240);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	if ($_GET["geral"]=="1") $str .= " and   (rh_funcionarios.status_funcionario =  '1' or rh_funcionarios.status_funcionario =  '-1') ";
	
	if ($_GET["id_funcionario"]!="") $str .= " and   rh_funcionarios.id_funcionario =  '". $_GET["id_funcionario"] ."'";
	else $str.= " and   rh_funcionarios.id_empresa =  '". $_SESSION["id_empresa"] ."'";
	
	if ($_GET["id_departamento"]!="") $str .= " and   rh_carreiras.id_departamento =  '". $_GET["id_departamento"] ."'";
	if ($_GET["id_turno"]!="") $str .= " and   rh_carreiras.id_turno =  '". $_GET["id_turno"] ."'";
	
	$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos
							where pessoas.id_pessoa = rh_funcionarios.id_pessoa
							". $str ."
							and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
							and   rh_carreiras.atual = '1'
							and   rh_carreiras.id_turno = rh_turnos.id_turno
							order by pessoas.nome_rz asc
							") or die(mysql_error());
	
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		
		//setando variáveis que são de cada funcionário
		$total_he_normais_60[0]= 0; $total_he_normais_60[1]= 0;
		$total_he_normais_100[0]= 0; $total_he_normais_100[1]= 0;
		$total_he_dsr[0]= 0; $total_he_dsr[1]= 0;
		$total_he_folga[0]= 0; $total_he_folga[1]= 0;
		$total_he_feriado[0]= 0; $total_he_feriado[1]= 0;
		$total_faixa[0]= 0; $total_faixa[1]= 0;
		
		$id_funcionario= $rs->id_funcionario;
		$id_turno= $rs->id_turno;
		
		$total_desconto_feriado=0;
		
		$pdf->AddPage();
		
		$pdf->SetXY(6, 0.6);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
		$pdf->Cell(0, 1, "ESPELHO DO CARTÃO", 0, 0, "R");
		$pdf->SetFont('ARIALNARROW', '', 9);
		
		$pdf->SetXY(6, 1.3);
		$pdf->Cell(0, 0.8, "EM CONFORMIDADE COM A PORTARIA DO MTb N.º 3.626, DE 13/11/1991, ART. 13.", 0 , 1, "R");
		
		/*$pdf->SetXY(6,0.6);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
		$pdf->Cell(2, 0.5, $rs_empresa->nome_rz , 0 , 1);
		
		$pdf->SetX(6);
		$pdf->SetFont('ARIALNARROW', '', 9);$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->Cell(2.9, 0.5, $rs_empresa->rua, 0 , 0);
		$pdf->Cell(0.6, 0.5, $rs_empresa->numero, 0 , 0);
	
		$pdf->Cell(3.3, 0.5, $rs_empresa->complemento, 0 , 0);
		$pdf->Cell(2, 0.5, "BAIRRO ". $rs_empresa->bairro, 0 , 1);
		
		$pdf->SetX(6);
		
		$pdf->Cell(1.3, 0.45, $rs_empresa->cidade .'/'. $rs_empresa->uf, 0 , 0);
	
		$pdf->SetX(13.3);
		$pdf->Cell(1, 0.45, $rs_empresa->cep, 0 , 0);*/
		
		//---------------------------------------------------------------------------
		//DADOS FUNCIONÁRIO
		
		$pdf->SetXY(0.5,2.4);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(10, 0.6, "CONTROLE DE FREQÜÊNCIA AO TRABALHO NO PERÍODO DE " . $_GET["data1"] . " À " . $_GET["data2"], 0 , 1);
		
		$pdf->SetXY(0.5,2.8);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(2.2, 0.6, "FUNCIONÁRIO(A):", 0 , 0);
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->Cell(5.5, 0.6, $rs->nome_rz, 0 , 0);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(2.2, 0.6, "DEPARTAMENTO:", 0 , 0);
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->Cell(4.2, 0.6, pega_departamento($rs->id_departamento), 0 , 0);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(1.1, 0.6, "CARGO:", 0 , 0);
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->Cell(4.2, 0.6, pega_cargo($rs->id_cargo), 0 , 0);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(2.2, 0.6, "MATR. EMPRESA:", 0 , 0);
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->Cell(1.9, 0.6, $id_funcionario, 0 , 0);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(2.9, 0.6, "MATR. CONTABILIDADE:", 0 , 0);
		$pdf->SetFont('ARIALNARROW', '', 8);
		$pdf->Cell(2, 0.6, $rs->matr_cont, 0 , 1);
		
		
		//INICIO DA TABELA DE HORAS DO TURNO DO FUNCIONARIO
		$pdf->SetXY(0.5,17.2);
		
		$pdf->SetFillColor(200,200,200);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
		$pdf->Cell(2.3, 0.33, "TURNO: ", 1 , 0, 'L', 1);
		$pdf->SetFont('ARIALNARROW', '', 7);
		$pdf->Cell(2.5, 0.33, pega_turno($id_turno), 1 , 1);
		
		$pdf->SetX(0.5);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
		$pdf->Cell(2.3, 0.33, "REGIME:", 1 , 0, 'L', 1);
		$pdf->SetFont('ARIALNARROW', '', 7);
		$pdf->Cell(2.5, 0.33, pega_regime_turno($rs->id_regime), 1 , 1);
		
		$pdf->SetX(0.5);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
		$pdf->Cell(2.3, 0.33, "CARGA HORÁRIA:", 1 , 0, 'L', 1);
		$pdf->SetFont('ARIALNARROW', '', 7);
		$pdf->Cell(2.5, 0.33, "220 HORAS/MÊS", 1 , 1);
	
		$pdf->SetXY(5.7,17.2);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
		
		$pdf->SetFillColor(200,200,200);
		$pdf->Cell(2, 0.33, "DIA DA SEMANA", 1 , 0, "C", 1);
		$pdf->Cell(1.2, 0.33, "ENTRADA", 1 , 0, "C", 1);
		$pdf->Cell(1.6, 0.33, "INTERVALO", 1 , 0, "C", 1);
		$pdf->Cell(1.2, 0.33, "SAÍDA", 1 , 1, "C", 1);
		
		$pdf->SetX(10);
		
		for ($i=0; $i<=6; $i++) {
			$result_dia= mysql_query("select * from rh_turnos_horarios
										where id_turno = '". $id_turno ."'
										and   id_dia = '$i'
										");
			$rs_dia= mysql_fetch_object($result_dia);
			
			$jornada_entrada_hora= explode(':', $rs_dia->entrada);
			$jornada_saida_hora= explode(':', $rs_dia->saida);
			
			$a_jornada= 2008;
			$m_jornada= 10;
			
			$d_jornada_entrada= 10;
			$d_jornada_saida= 10;
			
			if ($jornada_entrada_hora[0]>$jornada_saida_hora[0]) $d_jornada_saida++;
			
			$jornada_entrada[$i]= @mktime($jornada_entrada_hora[0], $jornada_entrada_hora[1], $jornada_entrada_hora[2], $m_jornada, $d_jornada_entrada, $a_jornada);
			$jornada_saida[$i]= @mktime($jornada_saida_hora[0], $jornada_saida_hora[1], $jornada_saida_hora[2], $m_jornada, $d_jornada_saida, $a_jornada);
			
			$jornada_entrada_hora2[$i]= @mktime($jornada_entrada_hora[0], $jornada_entrada_hora[1], $jornada_entrada_hora[2], 0, 0, 0);
			$jornada_saida_hora2[$i]= @mktime($jornada_saida_hora[0], $jornada_saida_hora[1], $jornada_saida_hora[2], 0, 0, 0);
			
			$pdf->SetX(5.7);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
			$pdf->Cell(2, 0.33, strtoupper(traduz_dia($i)), 1 , 0, "C");
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(1.2, 0.33, substr($rs_dia->entrada, 0, 5), 1 , 0, "C");
			$pdf->Cell(1.6, 0.33, pega_detalhes_intervalo($rs->id_intervalo, $i, 0), 1 , 0, "C");
			$pdf->Cell(1.2, 0.33, substr($rs_dia->saida, 0, 5), 1 , 1, "C");
			
		}
		
		// ------------- tabela
		
		$pdf->SetFillColor(200,200,200);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
		
		$pdf->SetXY(0.5,3.6);
		$pdf->Cell(1, 0.7, "DIA", 1 , 0, "C", 1);
		$pdf->Cell(1.2, 0.7, "SEMANA", 1 , 0, "C", 1);
		
		$j=1;
		$num_operacoes= 4;
		
		for ($i=1; $i<($num_operacoes*2)+1; $i++) {
			if (($i%2)==0) $operacao= "SAÍDA";
			else $operacao= "ENTRADA";
		
			$pdf->Cell(2.15, 0.7, $operacao .' '. $j, 1 , 0, "C", 1);
		
			if (($i%2)==0) $j++;
		}
	
		$pdf->Cell(3.2, 0.7, "MOTIVO", 1 , 0, "C", 1);
		$pdf->SetXY(23.1,3.6);
		$pdf->Cell(2.5, 0.35, "HORAS TRABALHADAS", 1 , 1, "C", 1);
		$pdf->SetX(23.1);
		$pdf->Cell(1.25, 0.35, "DIURNO", 1 , 0, "C", 1);
		$pdf->Cell(1.25, 0.35, "NOTURNO", 1 , 0, "C", 1);
		$pdf->SetXY(25.6,3.6);
		$pdf->Cell(1, 0.7, "FALTAS", 1 , 0, "C", 1);
		$pdf->SetXY(26.6,3.6);
		$pdf->Cell(2.5, 0.35, "HORAS EXTRAS", 1, 1, "C", 1);
		$pdf->SetX(26.6);
		$pdf->Cell(1.25, 0.35, "DIURNO", 1 , 0, "C", 1);
		$pdf->Cell(1.25, 0.35, "NOTURNO", 1 , 1, "C", 1);
		
		$pdf->SetFont('ARIALNARROW', '', 7);
		
		$diferenca= date("d", $data2_mk-$data1_mk);
		
		//echo date("Y-m-d H:i:s", $data2_mk) . " | ". date("Y-m-d H:i:s", $data1_mk);
		//echo " | ". $diferenca; die();
		
		$hoje= date("Y-m-d");
		$hoje_mk= faz_mk_data2($hoje);
		
		$erro=0;
		
		$total_horas_diurnas=0;
		$total_horas_noturnas=0;
		$total_faltas=0;
		
		$total_faltas_diurnas=0;
		$total_faltas_noturnas=0;
		
		$total_extras_diurnas=0;
		$total_extras_noturnas=0;
		
		$saldo_extras_diurnas=0;
		$saldo_extras_noturnas=0;
		
		unset($erro_geral_linha);
		
		//repetir todos os dias do intervalo
		for ($d= 0; $d<$diferenca; $d++) {
			$extras_diurnas=0;
			$extras_noturnas=0;
			
			$falta_justificada=0;
			$falta_nao_justificada=0;
			$suspensao=0;
			
			$total_he_normais_60_dia[0]= 0; $total_he_normais_60_dia[1]= 0;
			$total_he_normais_100_dia[0]= 0; $total_he_normais_100_dia[1]= 0;
			
			$total_he_dsr_dia[0]=0; $total_he_dsr_dia[1]=0;
			$total_he_folga_dia[0]=0; $total_he_folga_dia[1]=0;
			$total_he_feriado_dia[0]=0; $total_he_feriado_dia[1]=0;
			
			//$total_he_normais_60_dia[0]= 0; $total_he_normais_60_dia[1]= 0;
			
			$total_saidas[$d]= 0;
			
			$e= $d+1;
			$calculo_data= $data1_mk+(86400*$d);
			
			$dia= date("d/m", $calculo_data);
			$data= date("d/m/Y", $calculo_data);
			$id_dia= date("w", $calculo_data);
			$vale_dia= date("Y-m-d", $calculo_data);
			
			if ($hoje_mk>= $calculo_data) {
			
			$result_adm= mysql_query("select * from rh_carreiras
										where id_funcionario = '". $id_funcionario ."'
										and   id_acao_carreira = '1'
										") or die(mysql_error());
			$linhas_admissao= mysql_num_rows($result_adm);
			
			if ($linhas_admissao>0) {
				$rs_adm= mysql_fetch_object($result_adm);
				//echo $rs_adm->data; die();
				$data_admissao_mk= faz_mk_data($rs_adm->data);
			}
			
			$result_dem= mysql_query("select * from rh_carreiras
										where id_funcionario = '". $id_funcionario ."'
										and   id_acao_carreira = '2'
										") or die(mysql_error());
			$linhas_demissao= mysql_num_rows($result_dem);
			
			if ($linhas_demissao>0) {
				$rs_dem= mysql_fetch_object($result_dem);
				//echo $rs_adm->data; die();
				$data_demissao_mk= faz_mk_data($rs_dem->data);
				$demitido=1;
			} else $demitido=0;
			
			//if ($_SESSION["tipo_usuario"]=="a")
			//	echo date("d/m/Y", $calculo_data) ." | ". date("d/m/Y H:i:s", $data_admissao_mk) ."<br /><br />";
			
			$data_atual_aqui= date("d/m/Y", $calculo_data);
			$data_admissao_aqui= date("d/m/Y", $data_admissao_mk);
			
			//vai mostrar o dia...
			//se a data atual for maior ou igual a data de admissao === ELE JÁ É FUNCIONÁRIO DA EMPRESA
			//ou
			//se o funcionário não foi demitido ou se foi demitido e a data atual é menor a data da demissão
			if ((($calculo_data>=$data_admissao_mk) || ($data_atual_aqui==$data_admissao_aqui)) && ((!$demitido) || (($demitido) && ($calculo_data<=$data_demissao_mk)) ) ) {
				
				//se sábado ou domingo
				if (($id_dia==0) || ($id_dia==6)) {
					$fill=1;
					$pdf->SetFillColor(240,240,240);
				}
				else $fill= 0;
				
				$pdf->Cell(1, 0.4, $dia, 1 , 0, "C", $fill);
				$pdf->Cell(1.2, 0.4, strtoupper(traduz_dia($id_dia)), 1 , 0, "C", $fill);
				
				$result_hor= mysql_query("select *, DATE_FORMAT(data_batida, '%d/%m/%Y') as data_batida2
											from rh_ponto
											where vale_dia= '$vale_dia'
											and   id_funcionario = '". $id_funcionario ."'
											order by data_batida, hora
											");
				$total_operacoes= mysql_num_rows($result_hor);
				
				$h=0;
				$z=0;
				$m= 0;
				unset($entrada); unset($saida);
				
				while ($rs_hor= mysql_fetch_object($result_hor)) {
					$horario[$z]= faz_mk_data_completa($rs_hor->data_batida2 .' '. $rs_hor->hora);
										
					if (($rs_hor->tipo==0) && (($rs->id_regime==2) || ($rs->id_regime==3)) ) {
						$total_saidas[$d]++;
						//echo $total_saidas[$d];
						//echo $intervalo_automatico[0] ." - ";
						//echo $intervalo_automatico[1];
						//echo $atual;
						
						if ( (($total_operacoes/$total_saidas[$d])==2) ) {
							//passando
							$intervalo_automatico= retorna_intervalo_automatico($horario[$z-1], $horario[$z]);
							
							for ($p=0; $p<2; $p++) {
								//se tem, pelo menos 5:40 trabalhadas... faz o intervalo
								if ($horario[$z]-$horario[$z-1]>=4500) {
									$pdf->Cell(2.15, 0.4, date("d/m/Y H:i:s", $intervalo_automatico[$p]), 1 , 0, "C", $fill);
									$h++;
								}
							}
						}//fim 
					}
					
					//*--------------------------------------------------------
					
					$pdf->Cell(2.15, 0.4, $rs_hor->data_batida2 ." ". $rs_hor->hora, 1 , 0, "C", $fill);
					
					$data_hora_batida= $rs_hor->data_batida ." ". $rs_hor->hora;
					
					if ($rs_hor->tipo==1){ 
						$entrada[$m]= $data_hora_batida;
						
						$entrada_mk= faz_mk_hora($rs_hor->hora);
						$diferenca_entrada= ($jornada_entrada_hora2[$id_dia]-$entrada_mk);
						
						if (($diferenca_entrada>0) && ($diferenca_entrada<=600) )
							$entrada[$m]= $rs_hor->data_batida ." ". date("H:i:s", $jornada_entrada_hora2[$id_dia]);
					}
					else {
						$saida[$m]= $data_hora_batida;
						$m++;
					}
	
					$z++;
					$h++;
				}//fim while horarios
				
				if (($total_operacoes%2)==1) {
					$erro=1;
					$pdf->SetTextColor(153, 0, 0);
					$pdf->Cell(2.15, 0.4, "ERRO", 1 , 0, "C", $fill);
					$pdf->SetTextColor(0, 0, 0);
					$h++;
					
					$erro_geral_linha[$d]=1;
				}
				
				for ($i=$h; $i<($num_operacoes*2); $i++)
					$pdf->Cell(2.15, 0.4, "-", 1 , 0, "C", $fill);
				
				//--- consultando escala
				
				$result_esc= mysql_query("select * from rh_escala
											where id_funcionario = '". $id_funcionario ."'
											and   data_escala = '". $vale_dia ."'
											") or die(mysql_error());
				$linhas_escala= mysql_num_rows($result_esc);
				
				$result_feriado= mysql_query("select * from rh_feriados
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   data_feriado = '". $vale_dia ."'
											") or die(mysql_error());
				$linhas_feriado= mysql_num_rows($result_feriado);
				
				//se nao bateu o ponto nesse dia
				if ($total_operacoes==0) {
					$result_afa= mysql_query("select * from rh_afastamentos_dias
												where id_funcionario = '". $id_funcionario ."'
												and   data = '". $vale_dia ."'
												") or die(mysql_error());
					$linhas_afastamento= mysql_num_rows($result_afa);
					$rs_afa= mysql_fetch_object($result_afa);
					
					if (($linhas_afastamento>0) && ($rs_afa->tipo_afastamento!='s') && ($rs_afa->tipo_afastamento!='b')) {
						$tipo_afastamento= pega_tipo_afastamento_pelo_id_afastamento($rs_afa->id_afastamento);
						$id_motivo_afastamento= pega_id_motivo_pelo_id_afastamento($rs_afa->id_afastamento);
						
						if ($tipo_afastamento=='o') $motivo= pega_motivo($id_motivo_afastamento);
						else $motivo= pega_tipo_afastamento($tipo_afastamento);
						
						$motivo= strtoupper($motivo);
						
						//se for atestado, considerar primeiros 15 dias
						if ($rs_afa->tipo_afastamento=='a') {
							$result_afastamento= mysql_query("select distinct(rh_afastamentos.id_afastamento) as id_afastamento, rh_afastamentos.data_inicial
																from rh_afastamentos, rh_afastamentos_dias
																where rh_afastamentos.id_afastamento = rh_afastamentos_dias.id_afastamento
																and   rh_afastamentos.id_funcionario = '". $id_funcionario ."'
																and   rh_afastamentos_dias.data = '". $vale_dia ."'
																") or die(mysql_error());
							$rs_afastamento= mysql_fetch_object($result_afastamento);
							
							$total_horas_diferenca_mk= abs(retorna_intervalo($rs_afastamento->data_inicial ." 14:00:00", $vale_dia ." 14:00:00"));
							$total_horas_diferenca= calcula_total_horas_ss($total_horas_diferenca_mk);
							$dias_diferenca= calcula_dias_pelas_horas($total_horas_diferenca);
						}
							
						if (($rs_afa->tipo_afastamento=='o') || (($rs_afa->tipo_afastamento=='a') && ($dias_diferenca<15)) ) {
							$falta_justificada=1;
						}
					}
					else {
						if ($linhas_afastamento>0) {
							if ($rs_afa->tipo_afastamento=='s') {
								$motivo= "SUSPENSÃO";
								$suspensao=1;
							}
							else $motivo= "INÍCIO DO ABANDONO";
						}
						else {
							if ($linhas_escala==1) {
								$motivo= "FALTA";
								$falta_nao_justificada=1;
							}
							else {
								if (($id_dia==0) || ($id_dia==6))
									$motivo= "DSR";
								else
									$motivo= "FOLGA";
							}
						}
					}
					
					$horas_diurnas= 0;
					$horas_noturnas= 0;
					$extras_diurnas= 0;
					$extras_noturnas= 0;
				}
				//se tem batidas neste dia
				else {
					$motivo_apriori= "";
					$motivo= "";
					
					$horas_trabalhadas= calcula_diurno_noturno($entrada, $saida);
					
					//echo calcula_total_horas($horas_diurnas) ."<br /><br />";
					
					$horas_diurnas= $horas_trabalhadas[0];
					$horas_noturnas= $horas_trabalhadas[1];
					
					//se não deveria trabalhar este dia, é hora extra
					if (($linhas_escala==0) || ($linhas_feriado==1)) {
						$extras_diurnas= $horas_diurnas;
						$extras_noturnas= $horas_noturnas;
						
						//se for feriado, computa para horas extras em dsr
						if ($linhas_feriado==1) {
							$motivo= "HE (FERIADO)";
							$total_he_feriado[0]+= $extras_diurnas;
							$total_he_feriado[1]+= $extras_noturnas;
							
							$total_he_feriado_dia[0]+= $extras_diurnas;
							$total_he_feriado_dia[1]+= $extras_noturnas;
						}
						else {
							//se for fim de semana, computa para horas extras em dsr
							if (($id_dia==0) || ($id_dia==6)) {
								$motivo= "HE (DSR)";
								$total_he_dsr[0]+= $extras_diurnas;
								$total_he_dsr[1]+= $extras_noturnas;
								
								$total_he_dsr_dia[0]+= $extras_diurnas;
								$total_he_dsr_dia[1]+= $extras_noturnas;
							}
							//dia de semana, horas extras em folgas
							else {
								$motivo= "HE (FOLGA)";
								$total_he_folga[0]+= $extras_diurnas;
								$total_he_folga[1]+= $extras_noturnas;
								
								$total_he_folga_dia[0]+= $extras_diurnas;
								$total_he_folga_dia[1]+= $extras_noturnas;
							}
						}
					}
					//se trabalha este dia, são horas normais
					else {
						$extras_diurnas= 0;
						$extras_noturnas= 0;
					}
				}
				
				if ($_SERVER['REMOTE_ADDR']=="186.220.2.154") {
					//echo $vale_dia .": ".$total_he_dsr[0] ." ". $total_he_dsr[1] ."<br /><br />";
				}
				
				$total_horas_diurnas += $horas_diurnas;
				$total_horas_noturnas += $horas_noturnas;
				
				$jornada_diaria= pega_jornada_diaria($id_turno, $id_dia);
				
				$intervalo_dia= pega_duracao_intervalo_dia($id_turno, $id_dia);
				if ($intervalo_dia!=0) {
					$jornada_diaria= $jornada_diaria-$intervalo_dia; //*************************
					
					//echo "<strong>$vale_dia</strong>: ". calcula_total_horas($intervalo_dia) ."<br />";
					
					//if ($horas_diurnas>$horas_noturnas) $horas_diurnas+=$intervalo_dia;
					//else $horas_noturnas+=$intervalo_dia;
				}
				
				$total_horas= $horas_diurnas+$horas_noturnas;
				
				//echo calcula_total_Horas($total_horas); die();
				//se a pessoa trabalha neste dia, calcula a falta
				
				//se a pessoa trabalha neste dia, calcula a falta
				//ou se está suspensa
				if (($linhas_escala==1) || ($rs_afa->tipo_afastamento=='s') || ($rs_afa->tipo_afastamento=='b')) {
					$calculo_faltas= ($jornada_diaria-$total_horas);//+$intervalo_dia;
					
					//$total_faltas_diurnas= $horas_diurnas;
					//$total_faltas_noturnas= $horas_noturnas;
				}
				else
					$calculo_faltas= 0;
				
				
				
				if ($erro_geral_linha[$d]==1) $calculo_faltas=0;
				
				
				//echo "<br>". $vale_dia ." - ". $calculo_faltas;
				
				//se trabalhou mais que a carga horária
				if ($calculo_faltas<=0) {
					$falta_string= "00:00:00";
					$falta_dia= 0;
				}
				else {
					//se veio trabalhar
					if ($total_operacoes>0) {
					//if ($motivo=="") {
						//20 minutos de atraso (entrada e saída)
						$desconto= 1200;
						//se o dia tem um intervalo de batida (adiciona mais 20minutos)
						if ($intervalo_dia!=0) {
							//$jornada_diaria= $jornada_diaria+$intervalo_dia;
							$desconto+=1200;
						}
						
						//se faltou até o limite (20 minutos ou 40, dependendo da batida)
						if ($calculo_faltas<=$desconto) {
							$falta_string= "00:00:00";
							$falta_dia= 0;
						}
						else {
							$falta_string= calcula_total_horas($calculo_faltas);
							$falta_dia= $calculo_faltas;
						}
					}
					//se não veio trabalhar, mas a falta foi justificada
					else {
						if (($motivo=="FALTA") || ($motivo=="SUSPENSÃO") || ($motivo=="INÍCIO DO ABANDONO")) {
							/*if ($intervalo_dia!=0)
								$jornada_diaria= $jornada_diaria;//+$intervalo_dia; */
							//echo " motivo: ". $motivo ." | jornada diaria: ". $jornada_diaria ."<br><br>";
							$falta_string= calcula_total_horas($jornada_diaria);
							$falta_dia= $jornada_diaria;
							
							//echo $falta_dia ."<br>";
						}
						else {
							$falta_string= "00:00:00";
							$falta_dia= 0;
						}
					}
				}
				
				// --------------------------------------------------------------------------- calculo de horas extras
				//echo date("H:i:s", mktime(0, 0, $adicional, 0, 0, 0)); die();
				//echo date("d/m/Y H:i:s", $jornada_entrada[$id_dia]); die();
				$entrada_aqui[0]= date("Y-m-d H:i:s", $jornada_entrada[$id_dia]);
				$saida_aqui[0]= date("Y-m-d H:i:s", $jornada_saida[$id_dia]);
				
				$horas_jornada_periodo= calcula_diurno_noturno($entrada_aqui, $saida_aqui);
				
				//echo $horas_diurnas; die();
				//echo $horas_jornada_periodo[0] ." | ";
				//echo $horas_jornada_periodo[1] . " <br> ";
				
				//echo $jornada_diaria ."<br>";
				//echo "hd: ". $horas_diurnas ." hn: ". $horas_noturnas ."<br>";
				
				//se tem intervalo no dia...
				//a variavel recebe a jornada diaria total + o intervalo
				//if ($intervalo_dia!=0) $jornada_diaria_para_extra= $jornada_diaria;//+$intervalo_dia;
				//se nao tem intervalo no dia...
				//a variavel recebe a jornada diaria
				$jornada_diaria_para_extra= $jornada_diaria;//+$intervalo_dia; //***************************
				
				//echo calcula_total_horas($jornada_diaria_para_extra+$intervalo_dia); die();
				
				//total de horas trabalhadas no dia, no total...
				$total_horas_trabalhadas_dia= ($horas_diurnas+$horas_noturnas);
				
				$diferenca_horas_trabalhadas_jornada= $total_horas_trabalhadas_dia-$jornada_diaria_para_extra;
				
				//echo "<strong>$vale_dia</strong><br />Jornada diária para extra: ". calcula_total_horas($jornada_diaria_para_extra) ."<br />Total horas trab do dia: ". calcula_total_horas($total_horas_trabalhadas_dia) ."<br />Diferença: ". calcula_total_horas($diferenca_horas_trabalhadas_jornada) ."<br /><br />";
				
				//echo $vale_dia ." -> horas trabalhadas: ". calcula_total_horas($total_horas_trabalhadas_dia) ." horas da jornada: ". calcula_total_horas($jornada_diaria_para_extra) ."<br>";
				
				//se o total de horas que foi trabalhada for maior que a jornada diária, a sobra é hora extra
				//e se não tem nenhuma extra noturna e diurna vindo de dsr, feriado ou folga
				if (($diferenca_horas_trabalhadas_jornada>=1200) && ($extras_diurnas==0) && ($extras_noturnas==0)) {
					//trabalha no periodo diurno
					//if ($horas_jornada_periodo[0]>0)
						
						//echo $vale_dia ." -> horas diurnas: ". calcula_total_horas($horas_diurnas) ." jornada diurna: ". calcula_total_horas($horas_jornada_periodo[0]) ."<br>";
						
						//$extras_diurnas= $diferenca_horas_trabalhadas_jornada;
						$extras_diurnas= ($horas_diurnas-$horas_jornada_periodo[0]);
						if (($extras_diurnas<=1200) || ($extras_diurnas>46800)) $extras_diurnas= 0;
						
						$extras_noturnas= ($horas_noturnas-$horas_jornada_periodo[1]);
						if (($extras_noturnas<=1200) || ($extras_noturnas>46800)) $extras_noturnas= 0;
						
						if (($extras_diurnas>0) && ($extras_diurnas>$diferenca_horas_trabalhadas_jornada)) $extras_diurnas= $diferenca_horas_trabalhadas_jornada;
						if (($extras_noturnas>0) && ($extras_noturnas>$diferenca_horas_trabalhadas_jornada)) $extras_noturnas= $diferenca_horas_trabalhadas_jornada;
						
						//echo $extras_diurnas;
						
						if ($intervalo_dia!=0) {
							$calculo_he_final= ($horas_diurnas+$horas_noturnas)-$jornada_diaria_para_extra;
							
							//se a diferença trabalhada for maior que 20 minutos...
							if ($calculo_he_final>1200) {
								//if ($extras_diurnas>=$extras_noturnas) $extras_noturnas+= $calculo_he_final;
								//else $extras_diurnas+= $calculo_he_final;
							}
						}
						
						//echo "horas diurnas: ". ($horas_diurnas) ." jornada diurna: ". ($horas_jornada_periodo[0]) ."<br><br />";
						//echo "horas noturnas: ". ($horas_noturnas) ." jornada noturna: ". ($horas_jornada_periodo[1]) ."<br>";
						
						/*
						if (($horas_noturnas<$horas_jornada_periodo[1]) && ($horas_diurnas>$horas_jornada_periodo[0]) && ($extras_diurnas>1200)) {
							//echo " !deu! ";
							
							$diferenca_noturna_aqui= $horas_jornada_periodo[1]-$horas_noturnas;
							$extras_diurnas -= $diferenca_noturna_aqui;
						}
						*/
				}
				else {
					if (($extras_diurnas==0) && ($extras_noturnas==0)) {
						$extras_diurnas=0;
						$extras_noturnas=0;
					}
				}
				
				//se for regime integral, pode ter hora extra quando tem intervalo
				if (($rs->id_regime==1) && ($motivo=="")) {
					//manipular extras como sendo diurnas
					if ($horas_diurnas>=$horas_noturnas) {
						$extras_diurnas= ($total_horas_trabalhadas_dia-$jornada_diaria);
						$extras_noturnas= 0;
					}
					//manipular extras como sendo noturnas
					else {
						$extras_diurnas= 0;
						$extras_noturnas= ($total_horas_trabalhadas_dia-$jornada_diaria);
					}
					
					if (($extras_diurnas<=1200) || ($extras_diurnas>46800)) $extras_diurnas= 0;
					if (($extras_noturnas<=1200) || ($extras_noturnas>46800)) $extras_noturnas= 0;
				}
				
				//-----------gambiarra
				if ($extras_diurnas>86400) $extras_diurnas -= 86400;
				if ($extras_noturnas>86400) $extras_noturnas -= 86400;
				
				
				//echo "<br>";
				
				//echo $vale_dia ." -> extras diurnas: ". calcula_total_horas($extras_diurnas) ." extras noturnas: ". calcula_total_horas($extras_noturnas) ."<br><br>";
				
				//echo $horas_trabalhadas_aqui[0] ."|"; //total de horas diurnas da jornada
				//echo $horas_trabalhadas_aqui[1]; //total de horas noturnas da jornada
				
				//die();
				
				//date("H:i:s", mktime(0, 0, $jornada_diaria, 0, 0, 0))
				
				//if ($vale_dia=="2008-09-08") echo calcula_total_horas($falta_dia);
				
				//procurar no banco de horas algo referente a este dia
				$result_bh= mysql_query("select sum(he) as he, operacao from rh_ponto_banco
											where id_funcionario = '". $id_funcionario ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   data_he = '". $vale_dia ."'
											group by operacao
											") or die(mysql_error());
				
				$total_bh_dia=0;
				$sinal_bh= "";
				
				while ($rs_bh= mysql_fetch_object($result_bh)) {
					
					if ($rs_bh->operacao==0) $classe2="vermelho";
					else $classe2="azul";
										
					/*if ($rs_bh->tipo_he==0) {
						$hed= $rs_bh->he; $hen= 0;
						
						if ($rs_bh->operacao==0) $bh_d-=$hed;
						else $bh_d+=$hed;
					}
					else {
						$hed= 0; $hen= $rs_bh->he;
						
						if ($rs_bh->operacao==0) $bh_n-=$hen;
						else $bh_n+=$hen;
					}
					*/
					
					$total_bh_dia= $rs_bh->he;
										
					if ($rs_bh->operacao==0) $sinal_bh= "-";
					else $sinal_bh= "+";
					
				}
				
				$motivo_apriori= $motivo;
				
				if ($total_bh_dia!=0) $motivo= "  [". $sinal_bh . calcula_total_horas($total_bh_dia) ." BH]";
				//else $motivo_string= $motivo;
				
				$pdf->Cell(3.2, 0.4, $motivo, 1 , 0, "C", $fill);
				
				//$motivo_bh= "";
				
				$pdf->Cell(1.25, 0.4, calcula_total_horas($horas_diurnas), 1 , 0, "C", $fill);
				$pdf->Cell(1.25, 0.4, calcula_total_horas($horas_noturnas), 1 , 0, "C", $fill);
				
				if ($sinal_bh=="-") //&& ($total_bh_dia>=$falta_dia))
					$falta_dia -= $total_bh_dia;
				
				if ($falta_dia<0) $falta_dia= 0;
				
				$total_faltas += $falta_dia;
				
				if (($motivo_apriori=="FALTA") && ($falta_dia==0)) {
					//$total_faltas_dias--;
					$falta_nao_justificada= 0;
				}
				
				$pdf->Cell(1, 0.4, calcula_total_horas($falta_dia), 1 , 0, "C", $fill);
				
				$saldo_extras_diurnas= $extras_diurnas;
				$saldo_extras_noturnas= $extras_noturnas;
				$total_saldo_extras= $saldo_extras_diurnas+$saldo_extras_noturnas;
				
				//if ( (($extras_diurnas>0) || ($extras_noturnas>0)) && ($saldo_extras_diurnas>$saldo_extras_noturnas)) $saldo_extras_diurnas+= $intervalo_dia;
				//elseif (($extras_diurnas>0) || ($extras_noturnas>0)) $saldo_extras_noturnas+= $intervalo_dia;
				
				$total_horas_dia_bh= 0;
				
				if (($extras_diurnas>0) || ($extras_noturnas>0) ) {
					//procurar no banco de horas algo referente a este dia
					$result_bhd= mysql_query("select sum(he) as total_horas_dia from rh_ponto_banco
												where id_funcionario = '". $id_funcionario ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												and   data_he = '". $vale_dia ."'
												") or die(mysql_error());
					
					$rs_bhd= mysql_fetch_object($result_bhd);
					$total_horas_dia_bh= $rs_bhd->total_horas_dia;
					
					if ($rs_bhd->total_horas_dia>0) {
						//echo "$vale_dia 123: ". $rs_bhd->total_horas_dia ."<br />";
						//$fill=1;
						//$pdf->SetFillColor(100,100,100);
						//$pdf->SetTextColor(255,255,255);
						
						$total_saldo_extras-= $rs_bhd->total_horas_dia;
									
						//if ($saldo_extras_diurnas>$saldo_extras_noturnas) $saldo_extras_diurnas-= $rs_bhd->total_horas_dia;
						//else $saldo_extras_noturnas-= $rs_bhd->total_horas_dia;
						
						if ($saldo_extras_diurnas<$rs_bhd->total_horas_dia) {
							$sobra_noturna= $rs_bhd->total_horas_dia-$saldo_extras_diurnas;
							$saldo_extras_diurnas= 0;											
						}
						
						if ($saldo_extras_noturnas>=$sobra_noturna) {
							$saldo_extras_noturnas-= $sobra_noturna;
						}
						
						if ($saldo_extras_diurnas>=$rs_bhd->total_horas_dia) {
							$saldo_extras_diurnas -= $rs_bhd->total_horas_dia;
						}

						
						//if ($saldo_extras_diurnas>$saldo_extras_noturnas) $saldo_extras_diurnas-= $rs_bhd->total_horas_dia;
						//else $saldo_extras_noturnas-= $rs_bhd->total_horas_dia;
						
						//$saldo_extras_diurnas= $extras_diurnas-$rs_bhd->total_horas_dia;
						
						//aqui ele desconta do geral o banco de horas diurnas............
						
						//$total_he_normais_60[0]-= $rs_bhd->total_horas_dia;
						//$total_he_normais_100[0]-= $rs_bhd->total_horas_dia;
						
						//if ($_SESSION["tipo_usuario"]=="a")
						//echo $vale_dia ." (". $motivo .") : ". ($total_he_dsr[0]) ." - ". ($rs_bhd->total_horas_dia) ." -> ";
						
						//if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-06-29")) echo $total_he_folga[0] ." | ". $motivo;
						
						if ($_SERVER['REMOTE_ADDR']=="186.220.2.154") {
							//echo $total_he_dsr[0] ." ". $total_he_dsr[1] ."<br />_____________<br />";
						}
						
						//fazer o teste pra ver qual é o maior valor entre horas noturnas e diurnas, o maior valor é decrescido as horas do banco, não influindo se é noturna ou diurna... pois pro banco de horas vai só o total de horas e não o tipo...
						if ($total_he_dsr[0]>$total_he_dsr[1]) {
							$total_he_dsr_controle[0]=1;
							$total_he_dsr_controle[1]=0;
						}
						else {
							$total_he_dsr_controle[0]=0;
							$total_he_dsr_controle[1]=1;
						}
						
						if ($total_he_dsr_dia[0]>$total_he_dsr_dia[1]) {
							$total_he_dsr_dia_controle[0]=1;
							$total_he_dsr_dia_controle[1]=0;
						}
						else {
							$total_he_dsr_dia_controle[0]=0;
							$total_he_dsr_dia_controle[1]=1;
						}
						
						if ($total_he_folga[0]>$total_he_folga[1]) {
							$total_he_folga_controle[0]=1;
							$total_he_folga_controle[1]=0;
						}
						else {
							$total_he_folga_controle[0]=0;
							$total_he_folga_controle[1]=1;
						}
						
						if ($total_he_folga_dia[0]>$total_he_folga_dia[1]) {
							$total_he_folga_dia_controle[0]=1;
							$total_he_folga_dia_controle[1]=0;
						}
						else {
							$total_he_folga_dia_controle[0]=0;
							$total_he_folga_dia_controle[1]=1;
						}
						
						
						if (($total_he_dsr_controle[0]) && ($total_he_dsr[0]>0) && (($motivo_apriori=="DSR") || ($motivo_apriori=="HE (DSR)")) ) $total_he_dsr[0]-= $rs_bhd->total_horas_dia;
						if (($total_he_dsr_dia_controle[0]) && ($total_he_dsr_dia[0]>0) && (($motivo_apriori=="DSR") || ($motivo_apriori=="HE (DSR)")) ) $total_he_dsr_dia[0]-= $rs_bhd->total_horas_dia;
						
						if (($total_he_folga_controle[0]) && ($total_he_folga[0]>0) && (($motivo_apriori=="FOLGA") || ($motivo_apriori=="HE (FOLGA)")) ) $total_he_folga[0]-= $rs_bhd->total_horas_dia;
						if (($total_he_folga_dia_controle[0]) && ($total_he_folga_dia[0]>0) && (($motivo_apriori=="FOLGA") || ($motivo_apriori=="HE (FOLGA)")) ) $total_he_folga_dia[0]-= $rs_bhd->total_horas_dia;
						
						if (($total_he_dsr_controle[1]) && ($total_he_dsr[1]>0) && (($motivo_apriori=="DSR") || ($motivo_apriori=="HE (DSR)")) ) $total_he_dsr[1]-= $rs_bhd->total_horas_dia;
						if (($total_he_dsr_dia_controle[1]) && ($total_he_dsr_dia[1]>0) && (($motivo_apriori=="DSR") || ($motivo_apriori=="HE (DSR)")) ) $total_he_dsr_dia[1]-= $rs_bhd->total_horas_dia;
						
						if (($total_he_folga_controle[1]) && ($total_he_folga[1]>0) && (($motivo_apriori=="FOLGA") || ($motivo_apriori=="HE (FOLGA)")) ) $total_he_folga[1]-= $rs_bhd->total_horas_dia;
						if (($total_he_folga_dia_controle[1]) && ($total_he_folga_dia[1]>0) && (($motivo_apriori=="FOLGA") || ($motivo_apriori=="HE (FOLGA)")) ) $total_he_folga_dia[1]-= $rs_bhd->total_horas_dia;
						
						if ($total_he_feriado[0]>$total_he_feriado[1]) {
							//echo "<strong>0. ".$total_he_feriado[0] ." - $rs_bhd->total_horas_dia</strong><br />";
							if ($total_he_feriado[0]>0) {
								//echo "q";
								$total_he_feriado[0]-= $rs_bhd->total_horas_dia;
								
								if ($total_he_feriado[0]<0) {
									$total_he_feriado[1]+= $total_he_feriado[0];
									$total_he_feriado[0]=0;
								}
							}
						}
						else {
							//echo "<strong>1. ".$total_he_feriado[1] ." - $rs_bhd->total_horas_dia </strong><br />";
							if ($total_he_feriado[1]>0) {
								//echo "w";
								$total_he_feriado[1]-= $rs_bhd->total_horas_dia;
								
								if ($total_he_feriado[1]<0) {
									$total_he_feriado[0]+= $total_he_feriado[1];
									$total_he_feriado[1]=0;
								}
							}
						}
						
						if ($_SERVER['REMOTE_ADDR']=="186.220.2.154") {
							//echo $total_he_dsr[0] ." ". $total_he_dsr[1] ."<br /><br />";
						}
						
						if ($total_he_feriado_dia[0]>$total_he_feriado_dia[1]) {
							//echo "<strong>0. ".$total_he_feriado[0] ." - $rs_bhd->total_horas_dia</strong><br />";
							if ($total_he_feriado_dia[0]>0) {
								//echo "q";
								$total_he_feriado_dia[0]-= $rs_bhd->total_horas_dia;
								
								if ($total_he_feriado_dia[0]<0) {
									$total_he_feriado_dia[1]+= $total_he_feriado_dia[0];
									$total_he_feriado_dia[0]=0;
								}
							}
						}
						else {
							//echo "<strong>1. ".$total_he_feriado[1] ." - $rs_bhd->total_horas_dia </strong><br />";
							if ($total_he_feriado_dia[1]>0) {
								//echo "w";
								$total_he_feriado_dia[1]-= $rs_bhd->total_horas_dia;
								
								if ($total_he_feriado_dia[1]<0) {
									$total_he_feriado_dia[0]+= $total_he_feriado_dia[1];
									$total_he_feriado_dia[1]=0;
								}
							}
						}
						
						
						
						
						if ($total_he_dsr[0]<0) $total_he_dsr[0]=0;
						if ($total_he_dsr[1]<0) $total_he_dsr[1]=0;
						
						if ($total_he_dsr_dia[0]<0) $total_he_dsr_dia[0]=0;
						if ($total_he_dsr_dia[1]<0) $total_he_dsr_dia[1]=0;
						
						
						if ($total_he_folga[0]<0) $total_he_folga[0]=0;
						if ($total_he_folga[1]<0) $total_he_folga[1]=0;
						
						if ($total_he_folga_dia[0]<0) $total_he_folga_dia[0]=0;
						if ($total_he_folga_dia[1]<0) $total_he_folga_dia[1]=0;
						
					}
					//else
					//	$saldo_extras_diurnas= $extras_diurnas;
					
					//echo $rs_bhd->total_horas_dia ."<br>";
				}
				
				//echo $vale_dia ." = ". calcula_total_horas($total_faltas_diurnas) ." | ". calcula_total_horas($total_faltas_noturnas) ."<br />";
				
				$total_faltas_diurnas_aqui=0;
				$total_faltas_noturnas_aqui=0;
				
				if ($falta_dia>0) {
					//se tem batidas do ponto
					if ($total_operacoes>0) {
						$total_operacoes_calculo= $m-1;
						
						unset($resolvido);
						
						for ($o=0; $o<=$total_operacoes_calculo; $o++) {
							
							$a_jornada= 2008;
							$m_jornada= 10;
							
							$d_jornada_entrada= 10;
							$d_jornada_saida= 10;
							
							$anterior_entrada= explode(" ", $entrada[$o]);
							$hora1_entrada= explode(":", $anterior_entrada[1]);
							$data1_entrada= explode("-", $anterior_entrada[0]);
							$completa_entrada[$o]= @mktime($hora1_entrada[0], $hora1_entrada[1], $hora1_entrada[2], $m_jornada, $d_jornada_entrada, $a_jornada);
							
							$anterior_saida= explode(" ", $saida[$o]);
							$hora1_saida= explode(":", $anterior_saida[1]);
							$data1_saida= explode("-", $anterior_saida[0]);
							if ($hora1_entrada[0]>$hora1_saida[$total_operacoes_calculo]) $d_jornada_saida++;
							$completa_saida[$o]= @mktime($hora1_saida[0], $hora1_saida[1], $hora1_saida[2], $m_jornada, $d_jornada_saida, $a_jornada);
							
							//if (eh_diurno($hora1_entrada[0])) $total_faltas_diurnas= $calculo_faltas;
							//else $total_faltas_noturnas= $calculo_faltas;
							
							//$completa_entrada_jornada= substr($jornada_entrada[$id_dia], 11, 8);
							
							//echo $o ." ";
							//echo $vale_dia ." = <br />";
							//echo date("Y-m-d H:i:s", $jornada_entrada[$id_dia]) . " -> ". date("Y-m-d H:i:s", $jornada_saida[$id_dia]) ."<br />";
							//echo date("Y-m-d H:i:s", $completa_entrada[$o]) . " -> ". date("Y-m-d H:i:s", $completa_saida[$o]) ."<br />";
							
							if ($resolvido[$d]!=1) {
							
								$diferenca_entrada_falta[$o]= $completa_entrada[$o]-$jornada_entrada[$id_dia];
								$diferenca_saida_falta[$o]= $jornada_saida[$id_dia]-$completa_saida[$o];
								
								$total_faltas_diurnas_aqui=0;
								$total_faltas_noturnas_aqui=0;
								
								$mostrar_isso=0;
								$mostrar_isso2=0;
								
								if ($mostrar_isso) if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-05-25")) echo "> ". calcula_total_horas($diferenca_entrada_falta[$o]) ." - ". calcula_total_horas($diferenca_saida_falta[$o]) ." < <br />";
								
								if ($diferenca_entrada_falta[$o]>$falta_dia) $diferenca_entrada_falta[$o]=$falta_dia;
								if ($diferenca_saida_falta[$o]<$falta_dia) $diferenca_entrada_falta[$o]=$falta_dia;
								
								if (abs($diferenca_entrada_falta[$o])>14400) {
									if (eh_diurno($hora1_saida[$total_operacoes_calculo])) $total_faltas_diurnas_aqui= $falta_dia;
									else $total_faltas_noturnas_aqui+= $falta_dia;
									
									if ($mostrar_isso) if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-05-25")) echo 1;
								}
								else {
									
									//if ($diferenca_entrada_falta[$o]>600) {
									
									//se entrou depois dos 10 min de tolerância, gera falta aqui...
									if ($diferenca_entrada_falta[$o]>600) {
										//$hora1_entrada[0]=$hora_entrada[0]+0;
										
										if (eh_diurno($hora1_entrada[$o])) $total_faltas_diurnas_aqui= abs($diferenca_entrada_falta[$o]);
										else $total_faltas_noturnas_aqui= abs($diferenca_entrada_falta[$o]);
										
										if ($mostrar_isso) if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-05-25")) echo 2;
									}
									
									//se entrou depois dos 10 min de tolerância, gera falta aqui...
									elseif ($diferenca_saida_falta[$o]>600) {
										//$hora1_saida[0]=$hora1_saida[0]+0;
										
										if (eh_diurno($hora1_saida[$o])) $total_faltas_diurnas_aqui= abs($diferenca_saida_falta[$o]);
										else $total_faltas_noturnas_aqui= abs($diferenca_saida_falta[$o]);
										
										if ($mostrar_isso) if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-05-25")) echo 3;
									}
									else {
										if ($mostrar_isso) if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-05-25")) echo 4;
									}
								}
								
								if (($total_faltas_diurnas_aqui>$total_faltas_noturnas_aqui) && ($total_faltas_diurnas_aqui>$falta_dia)) $total_faltas_diurnas_aqui= $falta_dia;
								if (($total_faltas_noturnas_aqui>$total_faltas_diurnas_aqui) && ($total_faltas_noturnas_aqui>$falta_dia)) $total_faltas_noturnas_aqui= $falta_dia;
								
								if ($mostrar_isso2) if ($_SESSION["id_usuario"]==13) {
									if (($total_faltas_diurnas_aqui>0) || ($total_faltas_noturnas_aqui>0)) {
										echo " <strong>> Dia (". calcula_total_horas($falta_dia) ."): ". $vale_dia .": ". calcula_total_horas($total_faltas_diurnas_aqui) ." - ". calcula_total_horas($total_faltas_noturnas_aqui) ." < <br /><br /></strong>";
									}
								}
								
								$total_faltas_diurnas+= $total_faltas_diurnas_aqui;
								$total_faltas_noturnas+= $total_faltas_noturnas_aqui;
								
								if ($mostrar_isso) if (($_SESSION["id_usuario"]==13) && ($vale_dia=="2010-05-25")) echo $hora1_entrada[$o] ." * ". calcula_total_horas($total_faltas_diurnas) ." | ". calcula_total_horas($total_faltas_noturnas);
								
								$resolvido[$d]=1;
							}
						}
					}
					//nao tem batidas
					else {
						
						
						
						//$jornada_entrada_calculo_faltas[0]= date("Y-m-d H:i:s", $jornada_entrada[$id_dia]);
						//$jornada_saida_calculo_faltas[0]= date("Y-m-d H:i:s", $jornada_saida[$id_dia]);
						
						//$horas_jornada_periodo_calculo_faltas= calcula_diurno_noturno($jornada_entrada_calculo_faltas, $jornada_saida_calculo_faltas);
						
						$total_faltas_diurnas_aqui= $horas_jornada_periodo[0];
						$total_faltas_noturnas_aqui= $horas_jornada_periodo[1];
						
						
						$total_faltas_diurnas+= $horas_jornada_periodo[0]; //$horas_jornada_periodo_calculo_faltas[0];
						$total_faltas_noturnas+= $horas_jornada_periodo[1]; //$horas_jornada_periodo_calculo_faltas[1];
						
						if (($intervalo_dia!=0) && ($horas_jornada_periodo[0]>$intervalo_dia) && ($horas_jornada_periodo[0]>$horas_jornada_periodo[1])) {
							$total_faltas_diurnas= $total_faltas_diurnas-$intervalo_dia;
							$total_faltas_diurnas_aqui= $total_faltas_diurnas_aqui-$intervalo_dia;
						}
						
						if (($intervalo_dia!=0) && ($horas_jornada_periodo[1]>$intervalo_dia) && ($horas_jornada_periodo[1]>$horas_jornada_periodo[0])) {
							$total_faltas_noturnas= $total_faltas_noturnas-$intervalo_dia;
							$total_faltas_noturnas_aqui= $total_faltas_noturnas_aqui-$intervalo_dia;
						}
						
						//echo $vale_dia .") ";
						//echo calcula_total_horas($total_faltas_diurnas) ." | ". calcula_total_horas($total_faltas_noturnas) ."<br />";
						
						//if (($intervalo_dia!=0) && ($horas_jornada_periodo[0]>$intervalo_dia)) $total_faltas_diurnas-= $intervalo_dia;
						//if (($intervalo_dia!=0) && ($horas_jornada_periodo[1]>$intervalo_dia)) $total_faltas_noturnas-= $intervalo_dia;
						
					}
				}
				
				
				//echo $vale_dia ." -> ". $total_he_feriado[1] ." -> ". $rs_bhd->total_horas_dia ."<br />";
				
				$pdf->Cell(1.25, 0.4, calcula_total_horas($saldo_extras_diurnas), 1 , 0, "C", $fill);
				
				$pdf->SetTextColor(0,0,0);
				
				//se sábado ou domingo
				if (($id_dia==0) || ($id_dia==6)) {
					$fill=1;
					$pdf->SetFillColor(240,240,240);
				}
				else $fill= 0;
				
				/*if ($extras_noturnas>0) {
					//procurar no banco de horas algo referente a este dia
					$result_bhn= mysql_query("select sum(he) as total_horas_dia from rh_ponto_banco
												where id_funcionario = '". $id_funcionario ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												and   data_he = '". $vale_dia ."'
												and   tipo_he = '1'
												") or die(mysql_error());
					$rs_bhn= mysql_fetch_object($result_bhn);
					
					if ($rs_bhn->total_horas_dia>0) {
						$fill=1;
						$pdf->SetFillColor(100,100,100);
						$pdf->SetTextColor(255,255,255);
						
						$saldo_extras_noturnas= $extras_noturnas-$rs_bhn->total_horas_dia;
						
						//aqui ele desconta do geral o banco de horas noturnas............
						//$total_he_normais_60[1]-= $rs_bhn->total_horas_dia;
						//$total_he_normais_100[1]-= $rs_bhn->total_horas_dia;
						if ($total_he_dsr[1]>0) $total_he_dsr[1]-= $rs_bhn->total_horas_dia;
						if ($total_he_folga[1]>0) $total_he_folga[1]-= $rs_bhn->total_horas_dia;
						if ($total_he_feriado[1]>0) $total_he_feriado[1]-= $rs_bhn->total_horas_dia;
					}
					else
						$saldo_extras_noturnas= $extras_noturnas;
				}
				else $saldo_extras_noturnas= $extras_noturnas;
				*/
				
				$pdf->Cell(1.25, 0.4, calcula_total_horas($saldo_extras_noturnas), 1 , 1, "C", $fill); 
				//FIM DA TABELA DE HORARIOS BATIDOS
				
				$pdf->SetTextColor(0,0,0);
				
				//se sábado ou domingo
				if ($id_dia==0) {
					$fill=1;
					$pdf->SetFillColor(210,210,210);
				}
				else {
					 if ($id_dia==6) {
						$fill=1;
						$pdf->SetFillColor(235,235,235);
					 }
					 else $fill= 0;
				}			
				
				//echo $saldo_extras_diurnas ." <br> ";
				
				$total_extras_diurnas += $saldo_extras_diurnas;
				$total_extras_noturnas += $saldo_extras_noturnas;
				
				/*if ($_SESSION["tipo_usuario"]=="a") {
					echo calcula_total_horas($saldo_extras_diurnas) ."|". calcula_total_horas($saldo_extras_noturnas);
				}*/
				
				$primeira_faixa= 7200;
				
				//se for um dia comum (nao feriado, nao dsr, nao folga)
				if (($linhas_feriado==0) && ($linhas_escala==1)) {
					if ($saldo_extras_diurnas>$primeira_faixa) {
						$total_he_normais_60[0]+= $primeira_faixa;
						$total_he_normais_100[0]+= $saldo_extras_diurnas-$primeira_faixa;
						
						$total_he_normais_60_dia[0]= $primeira_faixa;
						$total_he_normais_100_dia[0]= $saldo_extras_diurnas-$primeira_faixa;
					}
					else {
						$total_he_normais_60[0]+= $saldo_extras_diurnas;
						$total_he_normais_100[0]+= 0;
						
						$total_he_normais_60_dia[0]= $saldo_extras_diurnas;
						$total_he_normais_100_dia[0]= 0;
					}
					
					if ($saldo_extras_noturnas>$primeira_faixa) {
						$total_he_normais_60[1]+= $primeira_faixa;
						$total_he_normais_100[1]+= $saldo_extras_noturnas-$primeira_faixa;
						
						$total_he_normais_60_dia[1]= $primeira_faixa;
						$total_he_normais_100_dia[1]= $saldo_extras_noturnas-$primeira_faixa;
					}
					else {
						$total_he_normais_60[1]+= $saldo_extras_noturnas;
						$total_he_normais_100[1]+= 0;
						
						$total_he_normais_60_dia[1]= $saldo_extras_noturnas;
						$total_he_normais_100_dia[1]= 0;
					}
				}
				
				
				
				//considerando além das he trabalhadas, dsr, folga e feriado.
				$total_he_normais_100_dia[0]+= $total_he_dsr_dia[0]+$total_he_folga_dia[0]+$total_he_feriado_dia[0];
				$total_he_normais_100_dia[1]+= $total_he_dsr_dia[1]+$total_he_folga_dia[1]+$total_he_feriado_dia[1];
				
				//echo $vale_dia ." : ". $total_he_normais_100_dia[1] ."<br />";
				
				$ht_funcao= $horas_diurnas+$horas_noturnas;
				$he_funcao= $saldo_extras_diurnas+$saldo_extras_noturnas;
				
				ajusta_dados_rh($_SESSION["id_empresa"], $id_funcionario, $vale_dia, $ht_funcao, $horas_diurnas, $horas_noturnas, $falta_dia, $total_faltas_diurnas_aqui, $total_faltas_noturnas_aqui,
												$he_funcao, $saldo_extras_diurnas, $saldo_extras_noturnas, $total_he_normais_60_dia[0], $total_he_normais_100_dia[0], $total_he_normais_60_dia[1], $total_he_normais_100_dia[1],
												$falta_justificada, $falta_nao_justificada, $suspensao, $_SESSION["id_usuario"]);
				
				
				
				
				$i++;
			}//FIM DO IF SE JÁ TRABALHA NA EMPRESA
			}//fim hoje_mk
		}//fim dos dias
		
		//echo "\$total_desconto_feriado: ". $total_desconto_feriado;
		
		$pdf->SetX(19.9);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
		$pdf->Cell(3.2, 0.4, "TOTAL:", 1 , 0, "C");
		$pdf->SetFont('ARIALNARROW', '', 7);
		
		$pdf->Cell(1.25, 0.4, calcula_total_horas($total_horas_diurnas), 1 , 0, "C");
		$pdf->Cell(1.25, 0.4, calcula_total_horas($total_horas_noturnas), 1 , 0, "C");
		$pdf->Cell(1, 0.4, calcula_total_horas($total_faltas), 1 , 0, "C");
		$pdf->Cell(1.25, 0.4, calcula_total_horas($total_extras_diurnas), 1 , 0, "C");
		$pdf->Cell(1.25, 0.4, calcula_total_horas($total_extras_noturnas), 1 , 0, "C");
		
		$pdf->SetXY(20, 17.8);
		$pdf->SetFont('ARIALNARROW', '', 7);
		$pdf->Cell(7, 0.33, "DECLARO QUE ESTOU DE ACORDO E SÃO VERDADEIRAS", 0 , 1, "L");
		$pdf->SetX(20);
		$pdf->Cell(7, 0.33, "AS MARCAÇÕES DE HORÁRIOS ACIMA RELACIONADAS.", 0 , 1, "L");
		$pdf->SetXY(20, 19.4);
		$pdf->Cell(7, 0.33, "ASSINATURA:  _______________________________________________________", 0 , 1, "L");
		
		//if ($_SESSION["id_usuario"]=="13") {	
			$pdf->SetXY(0.5,18.85);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
			$pdf->Cell(2.3, 0.33, "TOTAL FALTAS:", 1 , 0, 'L', 1);
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(2.5, 0.33, calcula_total_horas($total_faltas), 1 , 1);
			
			$pdf->SetX(0.5);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
			$pdf->Cell(2.3, 0.33, "FALTAS DIURNAS:", 1 , 0, 'L', 1);
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(2.5, 0.33, calcula_total_horas($total_faltas_diurnas), 1 , 1);
			
			$pdf->SetX(0.5);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
			$pdf->Cell(2.3, 0.33, "FALTAS NOTURNAS:", 1 , 0, 'L', 1);
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(2.5, 0.33, calcula_total_horas($total_faltas_noturnas), 1 , 1);
		//}
		
		//tabela de cálculo de horas extras
		$pdf->SetXY(12.1, 17.2);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
		$pdf->SetFillColor(200,200,200);
		
		$pdf->Cell(3.2, 0.33, "EXTRAS DIURNAS", 1 , 0, "C", 1);
		$pdf->Cell(3.2, 0.33, "EXTRAS NOTURNAS", 1 , 1, "C", 1);
		$pdf->SetFont('ARIALNARROW', '', 7, 1);
		
		$total_faixa60[0]= $total_he_normais_60[0];
		$total_faixa60[1]= $total_he_normais_60[1];
		
		$total_faixa100[0]= $total_he_normais_100[0]+$total_he_dsr[0]+$total_he_folga[0]+$total_he_feriado[0];
		$total_faixa100[1]= $total_he_normais_100[1]+$total_he_dsr[1]+$total_he_folga[1]+$total_he_feriado[1];
		
		for ($i=0; $i<2; $i++) {
			if ($i==0) {
				$x= 12.1; $y= 17.53;
				$faixa= 60;
			}
			else {
				$x= 15.3; $y= 17.53;
				$faixa= 100;
			}
			
			$pdf->SetXY($x, $y);
			$pdf->Cell(2, 0.33, "NORMAIS 60%", 1 , 0, "L");
			$pdf->Cell(1.2, 0.33, calcula_total_horas($total_he_normais_60[$i]), 1 , 0, "C");
			
			$pdf->SetXY($x, $y+0.33);
			$pdf->Cell(2, 0.33, "NORMAIS 100%", 1 , 0, "L");
			$pdf->Cell(1.2, 0.33, calcula_total_horas($total_he_normais_100[$i]), 1 , 1, "C");
	
			$pdf->SetXY($x, $y+0.66);
			$pdf->Cell(2, 0.33, "DSR 100%", 1 , 0, "L");
			$pdf->Cell(1.2, 0.33, calcula_total_horas($total_he_dsr[$i]), 1 , 1, "C");
			
			$pdf->SetXY($x, $y+0.99);
			$pdf->Cell(2, 0.33, "FOLGA 100%", 1 , 0, "L");
			$pdf->Cell(1.2, 0.33, calcula_total_horas($total_he_folga[$i]), 1 , 1, "C");
			
			$pdf->SetXY($x, $y+1.32);
			$pdf->Cell(2, 0.33, "FERIADO 100%", 1 , 0, "L");
			$pdf->Cell(1.2, 0.33, calcula_total_horas($total_he_feriado[$i]), 1 , 1, "C");
			
			$pdf->SetFillColor(235,235,235);
			$fill= 1;
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
			$pdf->SetXY($x, $y+1.65);
			$pdf->Cell(2, 0.33, "TOTAL 60%", 1 , 0, "L", $fill);
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(1.2, 0.33, calcula_total_horas($total_faixa60[$i]), 1 , 1, "C", $fill);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 7);
			$pdf->SetXY($x, $y+1.98);
			$pdf->Cell(2, 0.33, "TOTAL 100%", 1 , 0, "L", $fill);
			$pdf->SetFont('ARIALNARROW', '', 7);
			$pdf->Cell(1.2, 0.33, calcula_total_horas($total_faixa100[$i]), 1 , 1, "C", $fill);
		}//fim for
	
	}//fim while funcionarios
	
	$pdf->Output("espelho_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>