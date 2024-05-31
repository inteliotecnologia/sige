<?
require_once("conexao.php");
require_once("funcoes.php");

if ($_GET[id_funcionario]!="") $id_funcionario= $_GET[id_funcionario];
if ($_POST[id_funcionario]!="") $id_funcionario= $_POST[id_funcionario];

if ($id_funcionario!="") $_SESSION["id_empresa_atendente2"]= pega_empresa_rel($id_funcionario);
else $_SESSION["id_empresa_atendente2"]= 4;

if (pode_algum("mrhv4", $_SESSION["permissao"])) {
	define('FPDF_FONTPATH','includes/fpdf/font/');
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato_rh.php");

	$result_empresa= mysql_query("select * from  pessoas, rh_enderecos, empresas, cidades, ufs
									where empresas.id_empresa = '". $_SESSION["id_empresa_atendente2"] ."'
									and   empresas.id_pessoa = pessoas.id_pessoa
									and   pessoas.id_pessoa = rh_enderecos.id_pessoa
									and   rh_enderecos.id_cidade = cidades.id_cidade
									and   cidades.id_uf = ufs.id_uf
									") or die(mysql_error());
	$rs_empresa= mysql_fetch_object($result_empresa);
		
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
	
	//echo $_GET["tipo"];
	
	switch($_GET["tipo"]) {
		//contrato de experiencia
		case 2:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_carreiras.id_carreira =  '". $_GET["id_carreira"] ."'
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by pessoas.nome_rz asc
								") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "PRORROGA��O DO CONTRATO DE EXPERI�NCIA", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CONTRATANTE:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs_empresa->nome_rz, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CONTRATADO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->nome_rz, 0 , 1);
			$pdf->Ln();
			
			$data= desformata_data($rs->data);
			$data30= soma_data($data, 29, 0, 0);
			$data31= soma_data($data30, 1, 0, 0);
			$data90= soma_data($data, 89, 0, 0);
			
			//$pdf->MultiCell(0, 0.6, "               Pelo presente termo de prorroga��o de contrato de trabalho, fica prorrogado o prazo do contrato assinado em ". $data ."  a  ". $data30 .", e acertam contratante e contratado, acima identificados um novo prazo de 60 (sessenta) dias, iniciando-se em ". $data31 ." e terminando em ". $data90 .".", 0, 1);
			
			$pdf->WriteText("               Pelo <presente> termo de prorroga��o de contrato de trabalho, fica prorrogado o prazo do contrato assinado em ". $data ."  a  ". $data30 .", e acertam contratante e contratado, acima identificados um novo prazo de 60 (sessenta) dias, iniciando-se em ". $data31 ." e terminando em ". $data90 .".");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "               E por estarem de acordo assinam em duas vias de igual teor e forma.", 0 , 1);
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "               ". ucwords(strtolower($rs_empresa->cidade)) .'/'. $rs_empresa->uf .", ". data_extenso_param($data30) .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
		//mudan�a de departamento/cargo
		case 3:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_carreiras.id_carreira =  '". $_GET["id_carreira"] ."'
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by pessoas.nome_rz asc
								") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "ALTERA��O DE CONTRATO DE TRABALHO", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CONTRATANTE:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs_empresa->nome_rz, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CONTRATADO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->nome_rz, 0 , 1);
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "               Pelo presente instrumento, de um lado ". $rs_empresa->nome_rz ." doravante denominada CONTRATANTE, e de outro lado, ". $rs->nome_rz .", portadora da Carteira de Trabalho n� ". $rs->ctps ." s�rie ". $rs->serie_ctps .", doravante denominado CONTRATADO(A), ambos j� qualificados anteriormente pelo contrato de trabalho, celebram entre si, com m�tuo consentimento e de acordo com a C.L.T., a altera��o na cl�usula 2� do Contrato de Trabalho, que passa a ter o seguinte teor:", 0 , 1);
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "Cl�usula 2� - A partir do dia ". desformata_data($rs->data) .", o empregado mudar� de setor, passando a cumprir as seguintes fun��es e hor�rios:", 0 , 1);
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3.5, 0.6, "DEPARTAMENTO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_departamento($rs->id_departamento), 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3.5, 0.6, "CARGO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_cargo($rs->id_cargo), 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3.5, 0.6, "TURNO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_turno($rs->id_turno), 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3.5, 0.6, "REGIME:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_regime_turno($rs->id_regime), 0 , 1);
			
			$pdf->SetXY(12, 11.2);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->Cell(2.1, 0.3, "DIA DA SEMANA", 1 , 0, "C", 1);
			$pdf->Cell(1.3, 0.3, "ENTRADA", 1 , 0, "C", 1);
			$pdf->Cell(1.7, 0.3, "INTERVALO", 1 , 0, "C", 1);
			$pdf->Cell(1.3, 0.3, "SA�DA", 1 , 1, "C", 1);
			
			for ($i=0; $i<=6; $i++) {
				$result_dia= mysql_query("select * from rh_turnos_horarios
											where id_turno = '". $rs->id_turno ."'
											and   id_dia = '$i'
											");
				$rs_dia= mysql_fetch_object($result_dia);
				
				if (($i%2)==0) $fill= 1;
				else $fill= 0;
				
				$pdf->SetX(12);
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
				$pdf->Cell(2.1, 0.3, strtoupper(traduz_dia($i)), 1 , 0, "C", $fill);
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->Cell(1.3, 0.3, substr($rs_dia->entrada, 0, 5), 1 , 0, "C", $fill);
				$pdf->Cell(1.7, 0.3, pega_detalhes_intervalo($rs->id_intervalo, $i, 0), 1 , 0, "C", $fill);
				$pdf->Cell(1.3, 0.3, substr($rs_dia->saida, 0, 5), 1 , 1, "C", $fill);
				
			}
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->MultiCell(0, 0.5, "E por estarem de acordo assinam em duas vias de igual teor e forma.", 0 , 1);
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.5, "               ". ucwords(strtolower($rs_empresa->cidade)) .'/'. $rs_empresa->uf .", ". data_extenso_param($rs->data) .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
		//advertencia
		case 4:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_afastamentos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_afastamentos.id_afastamento =  '". $_GET["id_afastamento"] ."'
								and   rh_afastamentos.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by pessoas.nome_rz asc
								") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			if ($rs->tipo_afastamento=='d') $titulo= "ADVERT�NCIA";
			if ($rs->tipo_afastamento=='s') $titulo= "AVISO DE SUSPENS�O";
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, $titulo, 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CONTRATANTE:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs_empresa->nome_rz, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CONTRATADO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->nome_rz, 0 , 1);
			$pdf->Ln();
			
			$data= desformata_data($rs->data);
			
			$pdf->MultiCell(0, 0.6, "               Tendo V.S.a cometido falta(s) abaixo discriminada(s), sem nenhuma justificativa que nos satisfa�a, vimos pela presente adverti-lo para que tal fato n�o se repita, pois caso contr�rio seremos obrigados a tomar medidas mais en�rgicas que nos s�o facultadas por Lei.", 0 , 1);
			
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 1, "DISCRIMINA��O DA FALTA: ", 0 , 1);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->MultiCell(0, 0.6, "". pega_motivo($rs->id_motivo) .".", 0 , 1);
			
			$pdf->MultiCell(0, 0.6, "". $rs->obs, 0 , 1);
			
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "E por estarem de acordo assinam em duas vias de igual teor e forma.", 0 , 1);
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "               ". ucwords(strtolower($rs_empresa->cidade)) .'/'. $rs_empresa->uf .", ". data_extenso() .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
		
		//suspens�o
		case 5:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_afastamentos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_afastamentos.id_afastamento =  '". $_GET["id_afastamento"] ."'
								and   rh_afastamentos.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by pessoas.nome_rz asc
								") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			if ($rs->tipo_afastamento=='d') $titulo= "ADVERT�NCIA";
			if ($rs->tipo_afastamento=='s') $titulo= "AVISO DE SUSPENS�O";
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, $titulo, 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CONTRATANTE:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs_empresa->nome_rz, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CONTRATADO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->nome_rz, 0 , 1);
			$pdf->Ln();
			
			$data= desformata_data($rs->data_emissao);
			
			$pdf->MultiCell(0, 0.6, "               Tendo V.S.a cometido falta(s) abaixo discriminada(s), sem nenhuma justificativa que nos satisfa�a, vimos pela presente suspend�-lo(a) pelo(s) pr�ximo(s) ". $rs->qtde_dias ." dia(s) de trabalho a partir de ". $data .". Caso este fato se repita, fica claro que a Empresa poder� dispens�-lo(a) por justa causa.", 0 , 1);
			
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 1, "DISCRIMINA��O DA FALTA: ", 0 , 1);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->MultiCell(0, 0.6, pega_motivo($rs->id_motivo) .".", 0 , 1);
			
			$pdf->MultiCell(0, 0.6, $rs->obs, 0 , 1);
			
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "E por estarem de acordo assinam em duas vias de igual teor e forma.", 0 , 1);
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "               ". ucwords(strtolower($rs_empresa->cidade)) .'/'. $rs_empresa->uf .", ". data_extenso() .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
		
		//documento qqer
		case 6:
			
			if ($_POST["metodo"]==1) {
				if ($_POST["id_turno"]!="") {
					$str = " and   rh_carreiras.id_turno = '". $_POST["id_turno"] ."'";
				}
				
				if ($_POST["id_departamento"]!="") {
					$str .= " and   rh_carreiras.id_departamento = '". $_POST["id_departamento"] ."'";
					
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
					$pdf->Cell(0, 0.6, $_POST["titulo"], 0 , 1, 'R');
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
					$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
					
					$pdf->Ln();$pdf->Ln();
	
					$pdf->SetFont('ARIALNARROW', '', 10);
					$pdf->MultiCell(0, 0.5, str_replace("&nbsp;", "", strip_tags(html_entity_decode($_POST["texto"]))), 0 , 1);
					$pdf->Ln();
					
					$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
					
					$pdf->Cell(0, 1, "FUNCION�RIOS ". $chamada_departamento . $chamada_turno ." :", 0 , 1, 'L');
					
					$pdf->Cell(0, 0.5, "", 0 , 1, 'L');
					
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
					
					$pdf->Cell(10, 0.6, "NOME", 1, 0, 1, 1);
					$pdf->Cell(8, 0.6, "ASSINATURA", 1, 1, 1, 1);
					
					$pdf->SetFont('ARIALNARROW', '', 8);
					
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
						
						if ( (($_POST["situacao"]==1) && ($linhas_presente==0)) || ($_POST["situacao"]==2) ) {
							$pdf->Cell(10, 0.6, $rs_fun->nome_rz, 1, 0);
							$pdf->Cell(8, 0.6, "", 1, 1);
						}
						
					}
				}
			}
			elseif ($_POST["metodo"]==2) {
				$pdf->AddPage();
				$pdf->SetXY(7,1.75);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
				$pdf->Cell(0, 0.6, $_POST["titulo"], 0 , 1, 'R');
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
				
				$pdf->Ln();$pdf->Ln();

				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->MultiCell(0, 0.5, str_replace("&nbsp;", "", strip_tags(html_entity_decode($_POST["texto"]))), 0 , 1);
				$pdf->Ln();
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				
				$pdf->Cell(0, 1, "FUNCION�RIOS: ", 0 , 1, 'L');
				
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
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
					
				$pdf->Cell(10, 0.6, "NOME", 1, 0, 1, 1);
				$pdf->Cell(8, 0.6, "ASSINATURA", 1, 1, 1, 1);
				
				$pdf->SetFont('ARIALNARROW', '', 8);
				
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
										
					if ( (($_POST["situacao"]==1) && ($linhas_presente==0)) || ($_POST["situacao"]==2) ) {
						$pdf->Cell(10, 0.6, $rs_fun->nome_rz, 1, 0);
						$pdf->Cell(8, 0.6, "", 1, 1);
					}
					
				}
			}
			else {
				$pdf->AddPage();
				$pdf->SetXY(7,1.75);
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
				$pdf->Cell(0, 0.6, $_POST["titulo"], 0 , 1, 'R');
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
				$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
				
				$pdf->Ln();$pdf->Ln();

				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->MultiCell(0, 0.5, str_replace("&nbsp;", "", strip_tags(html_entity_decode($_POST["texto"]))), 0 , 1);
				$pdf->Ln();
				
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				
				$pdf->Cell(0, 1, "LISTA DE PRESEN�A: ", 0 , 1, 'L');
				
				$pdf->Cell(0, 0.5, "", 0 , 1, 'L');
				
				$pdf->Cell(7, 0.6, "NOME", 1, 0, 1, 1);
				$pdf->Cell(5, 0.6, "SETOR", 1, 0, 1, 1);
				$pdf->Cell(6, 0.6, "ASSINATURA", 1, 1, 1, 1);
				
				for ($i=0; $i<$_POST["metodo_num"]; $i++) {
					$pdf->Cell(7, 0.6, "", 1, 0);
					$pdf->Cell(5, 0.6, "", 1, 0);
					$pdf->Cell(6, 0.6, "", 1, 1);
				}
			}
		break;
		
		//troca de escala
		case 7:
			
			$result= mysql_query("select * from rh_escala_troca
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_escala_troca = '". $_GET["id_escala_troca"] ."'
										") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "ALTERA��O DE ESCALA DE TRABALHO", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 13);
			$pdf->Cell(0, 0.6, "FUNCION�RIO SOLICITANTE", 'B', 1);
			
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "NOME:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_funcionario($rs->id_funcionario_solicitante), 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "DEPARTAMENTO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(5.5, 0.6, pega_departamento(pega_dado_carreira("id_departamento", $rs->id_funcionario_solicitante)), 0, 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.6, "CARGO:", 0, 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(6.5, 0.6, pega_cargo(pega_dado_carreira("id_cargo", $rs->id_funcionario_solicitante)), 0, 1);
			
			
			$id_turno= pega_dado_carreira("id_turno", $rs->id_funcionario_solicitante);
			$data2= explode("-", $rs->data_escala_troca);
			
			$id_dia= date("w", mktime(0, 0, 0, $data2[1], $data2[2], $data2[0]));
			$horarios= pega_horarios_turno($id_turno, $id_dia);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "DIA:", 0, 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(5.5, 0.6, desformata_data($rs->data_escala_troca) ." (". strtoupper(traduz_dia($id_dia)) .")", 0, 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.6, "HOR�RIO:", 0, 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(6.5, 0.6, $horarios[0] ." -> ". $horarios[1], 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "JUSTIFICATIVA:", 0 , 1);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->MultiCell(0, 0.6, $rs->justificativa, 0, 1);
			
			$pdf->Ln(); $pdf->Ln();$pdf->Ln();
					
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(11, 0.75, pega_funcionario($rs->id_funcionario_solicitante), 'T', 0, 'C');
			$pdf->Cell(3, 0.75, "", 0, 1);
			
			$pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 13);
			$pdf->Cell(0, 0.6, "FUNCION�RIO QUE ASSUME A TROCA", 'B', 1);
			
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "NOME:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_funcionario($rs->id_funcionario_assume), 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "DEPARTAMENTO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(5.5, 0.6, pega_departamento(pega_dado_carreira("id_departamento", $rs->id_funcionario_assume)), 0, 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.6, "CARGO:", 0, 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(6.5, 0.6, pega_cargo(pega_dado_carreira("id_cargo", $rs->id_funcionario_assume)), 0, 1);
			
			
			$id_turno= pega_dado_carreira("id_turno", $rs->id_funcionario_solicitante);
			$data2= explode("-", $rs->data_escala_troca);
			$id_dia= date("w", mktime(0, 0, 0, $data2[1], $data2[2], $data2[0]));
			$horarios= pega_horarios_turno($id_turno, $id_dia);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "DIA:", 0, 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(5.5, 0.6, desformata_data($rs->data_escala_troca) ." (". strtoupper(traduz_dia($id_dia)) .")", 0, 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.6, "HOR�RIO:", 0, 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(6.5, 0.6, $horarios[0] ." -> ". $horarios[1], 0, 1);
			
			$pdf->Ln(); $pdf->Ln();$pdf->Ln();
					
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(11, 0.75, pega_funcionario($rs->id_funcionario_assume), 'T', 0, 'C');
			$pdf->Cell(3, 0.75, "", 0, 1);
			
			$pdf->Ln(); $pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9, 'C');
			$pdf->Cell(7, 0.75, "SUPERVISOR", 'T', 0, 'C');
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, "GERENTE", 'T', 1, 'C');
			
		break;
		
		//contrato de trabalho por experiencia
		case 8:
		
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos, rh_cargos
									where pessoas.id_pessoa = rh_funcionarios.id_pessoa
									and   rh_carreiras.id_carreira =  '". $_GET["id_carreira"] ."'
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_carreiras.id_turno = rh_turnos.id_turno
									and   rh_carreiras.id_cargo = rh_cargos.id_cargo
									and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
									order by pessoas.nome_rz asc
									") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "CONTRATO DE TRABALHO POR EXPERI�NCIA", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$data= desformata_data($rs->data);
			$data30= soma_data($data, 30, 0, 0);
			$data31= soma_data($data30, 1, 0, 0);
			$data90= soma_data($data, 90, 0, 0);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			if ($rs->sexo=='m') { $termo= "Sr."; $termo2= ""; }
			else { $termo= "Sra."; $termo2= "a"; }
			
			$pdf->WriteText("               Por este instrumento particular, que entre si fazem a empresa <". pega_empresa($_SESSION["id_empresa_atendente2"]) .">, CNPJ n� <". pega_cnpj($_SESSION["id_empresa_atendente2"]) .">, neste ato denominada simplesmente <EMPREGADORA> e o ". $termo ." <". $rs->nome_rz .">, portador". $termo2 ." da carteira de trabalho n� <". $rs->ctps .">, s�rie <". $rs->serie_ctps .">, doravante chamado <EMPREGADO>, fica justo e acertado o presente contrato individual de trabalho, regido pelas seguintes cl�usulas:");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("<CL�USULA PRIMEIRA> - Este contrato de prazo determinado por experi�ncia � firmado entre as partes por um per�odo de trinta dias a contar de sua assinatura, podendo ser prorrogado por mais sessenta dias, mediante conven��o entre as partes, conforme estatui os arts. 443, �2�, �c� e art. 445, par�grafo �nico, da CLT.");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("<CL�USULA SEGUNDA> - A carga hor�ria contratada � de 220 horas mensais e 44 horas semanais, sendo estas �ltimas distribu�das durante a semana conforme acordo de compensa��o hor�ria a ser pactuada entre as partes ou por for�a de acordos ou conven��es coletivas de trabalho.");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("<CL�USULA TERCEIRA> - O Empregado trabalhar� na fun��o de <". pega_cargo($rs->id_cargo) ."> e demais atribui��es que lhe forem correlatadas ou que com ela guardarem qualquer afinidade para realizar, conforme descri��o da ordem de servi�o. Compromete-se, ainda ao cumprimento das ordens de servi�os, verbais e ou escritas, que lhe forem dadas. O local de trabalho ser� na sede da empresa Esterilav Servi�os Hospitalares Ltda , ficando desde j� pactuado que a empregadora poder� transferir o empregado para outro local de sua conveni�ncia conforme necessidade de servi�o, respeitando-se as regras dispostas no art. 469 e par�grafos 1�, 2� e 3�, da CLT.");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("<CL�USULA QUARTA> - O Empregado perceber� o sal�rio inicial de <R$ ". fnum($rs->val_salario_experiencia) ."> (". valor_extenso($rs->val_salario_experiencia) .") por m�s, na sede de seu empregador, em contra-recibo, atendendo o disposto no art. 464 da CLT. Tamb�m fica desde j� pactuado a possibilidade do Empregador, no futuro, depositar em organiza��o banc�ria de sua escolha os sal�rios e quaisquer outros vencimentos que se refiram � remunera��o do empregado, conforme regra  determinada pelo art. 464, par�grafo �nico, da CLT.");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("<CL�USULA QUINTA> - A Empregadora fica autorizada a descontar da remunera��o ou de quaisquer outros direitos de natureza trabalhista do Empregado, as contribui��es legais e/ou convencionadas, os eventuais adiantamentos e empr�stimos concedidos, outros valores devidamente autorizados, e tamb�m qualquer preju�zo ou dano causado ao seu patrim�nio, por culpa ou dolo, inclusive, os advindos de extravio, perda ou quebra de equipamentos e ferramentas de trabalho, uniforme, vestimentas, equipamento de prote��o individual ou outros materiais de qualquer natureza, posto sob sua responsabilidade, consoante regra do art. 462, par�grafo 2�, da CLT.");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("<CL�USULA SEXTA> � O hor�rio de trabalho a ser cumprido pelo Empregado ser� de acordo com a escala prevista pela empresa, respeitando-se a carga hor�ria disposta na cl�usula segunda deste contrato, podendo o hor�rio ser alterado a crit�rio da Empregadora, inclusive de jornada di�ria para noturna e vice-versa ou em hor�rio misto e, quando necess�rio em regime de revezamento, prorroga��o e compensa��o de hor�rio extraordin�rio e banco de horas.");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("<CL�USULA S�TIMA> � O Empregado se compromete a respeitar o regulamento interno da empresa, bem como os memorandos e ordens de servi�o (verbais ou formais), defendendo os interesses da Empregadora dentro e fora do ambiente trabalho, agindo com fidelidade, dedica��o e profissionalismo com as metas da empresa, seus colegas de trabalho, clientes, prestadores de servi�os e todos que, em decorr�ncia deste, como eles mantiveram contato.");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("<CL�USULA OITAVA> - Durante a vig�ncia do contrato de trabalho, a Empregadora poder� alterar as condi��es de sal�rio, fun��o, hor�rio ou outras condi��es laborais � adapta��o do empregado, respeitando as suas caracter�sticas individuais, desde que n�o resultem em preju�zo na forma disciplinada pelo art. 468 da CLT.");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("<CL�USULA NONA> - Ao termo deste contrato, conforme o que determina a CLT e a Lei 8.036/90, nenhuma indeniza��o a t�tulo de aviso pr�vio e indeniza��o do FGTS ser� devida pela Empregadora ao Empregado. Vencido o per�odo experimental e continuando o empregado a prestar servi�os a EMPREGADORA, por tempo indeterminado, ficam prorrogadas as demais cl�usulas aqui estabelecidas.");
			
			//$pdf->WriteText("               Pelo <presente> termo de prorroga��o de contrato de trabalho, fica prorrogado o prazo do contrato assinado em ". $data ."  a  ". $data30 .", e acertam contratante e contratado, acima identificados um novo prazo de 60 (sessenta) dias, iniciando-se em ". $data31 ." e terminando em ". $data90 .".");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "               E por estarem justos e contratados, o Empregado e o representante legal da Empregadora, firmam o presente instrumento em 2 vias de igual teor e forma.", 0 , 1);
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "               ". ucwords(strtolower($rs_empresa->cidade)) .'/'. $rs_empresa->uf .", ". data_extenso_param($rs->data) .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
		
		//recibo de entrega de uniforme
		case 9:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos, rh_cargos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_carreiras.id_carreira =  '". $_GET["id_carreira"] ."'
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								and   rh_carreiras.id_cargo = rh_cargos.id_cargo
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by pessoas.nome_rz asc
								") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "RECIBO DE ENTREGA DE UNIFORME", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->WriteText("               Eu, <". $rs->nome_rz .">, declaro ter recebido da empresa <". pega_empresa($_SESSION["id_empresa_atendente2"]) .">, _______ cal�as e _______ camisetas (uniforme). Declaro utilizar somente nas �reas da empresa e manter seu asseio e conserva��o. Responsabilizo-me pela devolu��o do mesmo em caso de interrup��o do v�nculo empregat�cio.");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "               ". ucwords(strtolower($rs_empresa->cidade)) .'/'. $rs_empresa->uf .", ". data_extenso_param($rs->data) .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
		
		//cadastramento do vale transporte
		case 10:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos, rh_cargos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_carreiras.id_carreira =  '". $_GET["id_carreira"] ."'
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								and   rh_carreiras.id_cargo = rh_cargos.id_cargo
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by pessoas.nome_rz asc
								") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "CADASTRAMENTO DE VALE-TRANSPORTE", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "(DECRETO N� 95.247, DE 17 DE NOVEMBRO DE 1987)*", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2.5, 0.6, "FUNCION�RIO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->nome_rz, 0 , 1);
			$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->WriteText("<1. Da op��o:>");
			
			$pdf->Ln();
			
			$pdf->WriteText("(    ) opto por usufruir do transporte coletivo previsto pela Lei 7.418/85, tendo em vista que necessito de locomo��o coletiva para me deslocar da resid�ncia-trabalho e vice-versa.");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("<2. Atualiza��o do endere�o residencial:>");
			
			$pdf->Ln();
			
			$pdf->WriteText("________________________________________________________________________________________________________________________________________________________________________________________________________________");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("<3. N�o opto pelo Vale-Transporte pelos seguintes motivos:>");
			
			$pdf->Ln();
			
			$pdf->WriteText("(    ) Resid�ncia pr�xima ao local de Trabalho.");
			$pdf->Ln();
			$pdf->WriteText("(    ) Meio de transporte pr�prio.");
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("Lembramos que:");
			$pdf->Ln();
			$pdf->WriteText("* o Vale-Transporte ser� custeado pelo benefici�rio, na parcela de 6% de seu sal�rio b�sico, exclu�dos quaisquer adicionais ou vantagens;");
			$pdf->Ln();
			$pdf->WriteText("* a declara��o falsa ou o uso indevido do benef�cio constituem FALTA GRAVE, pun�vel com justa causa (art. 7�, � 3�, do Dec.95.247/87).");
			$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "               ". ucwords(strtolower($rs_empresa->cidade)) .'/'. $rs_empresa->uf .", ". data_extenso_param($rs->data) .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
			
			$pdf->Ln(); $pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->Cell(7, 0.1, "", 'T', 1, 'R');
			
			$pdf->WriteText("* <Art. 7�.> Para o exerc�cio do direito de receber o Vale-Transporte o empregado informar� ao empregador, por escrito:I - seu endere�o residencial;II - os servi�os e meios de transporte mais adequados ao seu deslocamento resid�ncia-trabalho e vice-versa.");
			$pdf->Ln();
			
			$pdf->WriteText("<� 1�. A informa��o de que trata este artigo ser� atualizada anualmente ou sempre que ocorrer altera��o das circunst�ncias mencionadas nos itens I e II, sob pena de suspens�o do benef�cio at� o cumprimento dessa exig�ncia.>");
			
		break;
		
		//cartao de identificacao
		case 11:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos, rh_cargos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_carreiras.id_carreira =  '". $_GET["id_carreira"] ."'
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								and   rh_carreiras.id_cargo = rh_cargos.id_cargo
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by pessoas.nome_rz asc
								") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "CART�O DE IDENTIFICA��O", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "TERMO DE RESPONSABILIDADE", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->WriteText("               O  empregado <". $rs->nome_rz ."> da ci�ncia que na data <". desformata_data($rs->data) ."> recebeu o cart�o de identifica��o, se comprometendo a respeitar as finalidades e obriga��es do documento abaixo especificadas:");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("1.	o cart�o de identifica��o possui finalidade de n�o apenas atestar a entrada e sa�da do empregado, computando-se o hor�rio de trabalho executado pelo empregado, como tamb�m a de identifica��o pessoal de cada colaborador no estabelecimento da empresa;");
			$pdf->Ln();
			
			$pdf->WriteText("2.	o cart�o de identifica��o � de uso pessoal n�o podendo ser utilizado por qualquer outra pessoa, seja outro empregado ou superior hier�rquico;");
			$pdf->Ln();
			
			$pdf->WriteText("3. o empregado ficar� respons�vel pelo mau uso do cart�o de identifica��o, seja por danifica��o ou at� mesmo extravio ou perda do mesmo, garantindo-se ao empregador descontar do seu sal�rio o valor do cart�o com base no artigo 462, par�grafo primeiro, da CLT;");
			$pdf->Ln();
			
			$pdf->WriteText("4. fica desde j� o empregado ciente que as regras prevista no regulamento da empresa fazem parte das obriga��es aqui estatu�das.");
			$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			
			$pdf->MultiCell(0, 0.6, "               ". ucwords(strtolower($rs_empresa->cidade)) .'/'. $rs_empresa->uf .", ". data_extenso_param($rs->data) .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
		//cartao de identificacao
		case 12:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos, rh_cargos, rh_vt_descontos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_vt_descontos.id_vt_desconto =  '". $_GET["id_vt_desconto"] ."'
								and   rh_vt_descontos.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								and   rh_carreiras.id_cargo = rh_cargos.id_cargo
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by pessoas.nome_rz asc
								") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "ENTREGA DE VALE TRANSPORTE", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "DECLARA��O", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->WriteText("               Eu, <". $rs->nome_rz .">, declaro, para os devidos fins e direitos, que recebi o vale transporte conforme descrito abaixo, estando ciente que a declara��o falsa ou o uso indevido do benef�cio constituem FALTA GRAVE, pun�vel com justa causa (art. 7�, � 3�, do Dec.95.247/87), e que os mesmos s�o de uso exclusivo para meu deslocamento casa/empresa ou vice e versa.");
			
			$pdf->Ln();$pdf->Ln();
			
			
			$result_dias= mysql_query("select * from rh_escala
										where id_funcionario = '". $rs->id_funcionario ."'
										and   trabalha = '1'
										and   DATE_FORMAT(data_escala, '%m/%Y') = '". $rs->mes."/".$rs->ano ."'
										");
			$qtde= mysql_num_rows($result_dias);
			
			if ($qtde>$rs->qtde) {
				$qtde= $qtde-$rs->qtde;
				
				//$pdf->WriteText("               <Descontado:> ". $rs->qtde .".");
				//$pdf->Ln();$pdf->Ln();
			}
			
			
			$result_vt= mysql_query("select * from rh_vt, rh_vt_linhas
										where rh_vt.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_vt.id_linha = rh_vt_linhas.id_linha
										and   rh_vt.id_funcionario = '". $rs->id_funcionario ."'
										order by  rh_vt.trajeto desc, rh_vt_linhas.id_linha asc
										") or die(mysql_error());
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			
			$pdf->SetFillColor(210,210,210);
			
			$pdf->Cell(8, 0.6, "LINHA", 1, 0, "L", 1);
			$pdf->Cell(5, 0.6, "TRAJETO", 1, 0, "L", 1);
			$pdf->Cell(4, 0.6, "QUANTIDADE", 1, 1, "L", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->SetFillColor(235,235,235);
			
			$qtde_funcionario= 0;
			$total_funcionario= 0;
			
			
			$j=0;
			while ($rs_vt= mysql_fetch_object($result_vt)) {
				if (($j%2)==0) $fill= 0;
				else $fill= 1;
				
				$qtde_funcionario+=$qtde;
	
				$total= $qtde*$rs_vt->valor;
				$total_funcionario+=$total;
				
				$pdf->Cell(8, 0.6, $rs_vt->linha, 1, 0, "L", $fill);
				$pdf->Cell(5, 0.6, pega_trajeto($rs_vt->trajeto), 1, 0, "L", $fill);
				$pdf->Cell(4, 0.6, $qtde, 1, 1, "L", $fill);
				
				$j++;
			}
			
			
			$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			if ($rs->data_entrega!="") $data_entrega= $rs->data_entrega;
			else $data_entrega= $rs->data;
			
			$pdf->MultiCell(0, 0.6, "               ". ucwords(strtolower($rs_empresa->cidade)) .'/'. $rs_empresa->uf .", ". data_extenso_param($data_entrega) .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
		//devolu��o de carteira de trabalho
		case 13:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos, rh_cargos
									where pessoas.id_pessoa = rh_funcionarios.id_pessoa
									and   rh_carreiras.id_carreira =  '". $_GET["id_carreira"] ."'
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_carreiras.id_turno = rh_turnos.id_turno
									and   rh_carreiras.id_cargo = rh_cargos.id_cargo
									and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
									order by pessoas.nome_rz asc
									") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "DEVOLU��O DA CARTEIRA DE TRABALHO", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->WriteText("               Eu, <". $rs->nome_rz ."> , portador(a) da Carteira de Trabalho n� <". $rs->ctps ."> s�rie <". $rs->serie_ctps .">, declaro receber a carteira de trabalho da Empresa <". $rs_empresa->nome_rz .">, com as devidas anota��es nesta data.");
			
			$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
            $nova_data= soma_data($rs->data, 2, 0, 0);
			
			$pdf->MultiCell(0, 0.6, "               ". $rs_empresa->cidade .'/'. $rs_empresa->uf .", ". data_extenso_param($nova_data) .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
		//dados para admiss�o
		case 14:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos, rh_cargos, rh_enderecos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_carreiras.id_carreira =  '". $_GET["id_carreira"] ."'
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								and   rh_carreiras.id_cargo = rh_cargos.id_cargo
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   rh_funcionarios.id_pessoa = rh_enderecos.id_pessoa
								order by pessoas.nome_rz asc
								") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "DADOS PARA ADMISS�O", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "EMPRESA:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs_empresa->nome_rz, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "FUNCION�RIO:", 0, 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->nome_rz, 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "DATA/ADMISS�O:", 0, 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(2.5, 0.6, pega_data_admissao($rs->id_funcionario), 0, 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(1.25, 0.6, "SETOR:", 0, 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(4, 0.6, pega_departamento($rs->id_departamento), 0, 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(1.25, 0.6, "CARGO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_cargo($rs->id_cargo), 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "SAL�RIO (EXP.):", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(2.5, 0.6, "R$ ". fnum($rs->val_salario_experiencia), 0 , 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.6, "N� PIS:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->pis, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "RG:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(2.5, 0.6, $rs->rg_ie, 0 , 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.6, "CPF:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->cpf_cnpj, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CTPS:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(2.5, 0.6, $rs->ctps, 0 , 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.6, "S�RIE:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->serie_ctps, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "ESTADO CIVIL:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(2.5, 0.6, pega_estado_civil($rs->estado_civil), 0 , 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2.75, 0.6, "ESCOLARIDADE:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(4.75, 0.6, pega_escolaridade($rs->escolaridade), 0 , 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(1.75, 0.6, "N� FILHOS:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_num_filhos($rs->id_funcionario), 0, 1);
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(4, 0.5, "HOR�RIOS", 0 , 1);
			$pdf->Cell(4, 0.5, "DE TRABALHO:", 0 , 0);
			
			$pdf->SetXY(5, 9);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			$pdf->Cell(2.5, 0.4, "DIA DA SEMANA", 1 , 0, "C", 1);
			$pdf->Cell(1.6, 0.4, "ENTRADA", 1 , 0, "C", 1);
			$pdf->Cell(1.8, 0.4, "INTERVALO", 1 , 0, "C", 1);
			$pdf->Cell(1.6, 0.4, "SA�DA", 1 , 1, "C", 1);
			
			for ($i=0; $i<=6; $i++) {
				$result_dia= mysql_query("select * from rh_turnos_horarios
											where id_turno = '". $rs->id_turno ."'
											and   id_dia = '$i'
											");
				$rs_dia= mysql_fetch_object($result_dia);
				
				if (($i%2)==0) $fill= 1;
				else $fill= 0;
				
				$pdf->SetX(5);
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
				$pdf->Cell(2.5, 0.4, strtoupper(traduz_dia($i)), 1 , 0, "C", $fill);
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->Cell(1.6, 0.4, substr($rs_dia->entrada, 0, 5), 1 , 0, "C", $fill);
				$pdf->Cell(1.8, 0.4, pega_detalhes_intervalo($rs->id_intervalo, $i, 0), 1 , 0, "C", $fill);
				$pdf->Cell(1.6, 0.4, substr($rs_dia->saida, 0, 5), 1 , 1, "C", $fill);
				
			}
			
			$pdf->SetXY(2, 13);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "ENDERE�O:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(11, 0.6, $rs->rua, 0 , 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0.5, 0.6, "N�:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(1, 0.6, $rs->numero, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "COMPLEMENTO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->complemento, 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "BAIRRO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(6, 0.6, $rs->bairro, 0 , 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(2, 0.6, "CIDADE/UF:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(3, 0.6, pega_cidade($rs->id_cidade), 0 , 1);
			$pdf->Ln();
			
			$result_tel= mysql_query("select tel_contatos_telefones.* from tel_contatos, tel_contatos_telefones
										where tel_contatos.id_pessoa = '". $rs->id_pessoa ."'
										and   tel_contatos.id_contato = tel_contatos_telefones.id_contato
										");
			while ($rs_tel= mysql_fetch_object($result_tel)) {
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
				$pdf->Cell(3, 0.6, "TEL. ". strtoupper(pega_tipo_telefone($rs_tel->tipo)) .":", 0 , 0);
				$pdf->SetFont('ARIALNARROW', '', 10);
				$pdf->Cell(1, 0.6, $rs_tel->telefone, 0 , 1);
			}
			
			$pdf->Ln();
			
			$result_vt= mysql_query("select * from rh_vt, rh_vt_linhas
										where rh_vt.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_vt.id_linha = rh_vt_linhas.id_linha
										and   rh_vt.id_funcionario = '". $rs->id_funcionario ."'
										order by  rh_vt.trajeto desc, rh_vt_linhas.id_linha asc
										") or die(mysql_error());
			$linhas_vt= mysql_num_rows($result_vt);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.5, "VALE", 0 , 0);
			
			if ($linhas_vt>0) $usa_vt= "SIM"; else $usa_vt= "N�O";
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(6, 0.5, $usa_vt, 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.5, "TRANSPORTE:", 0 , 0);
			
			$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(3, 0.8, "DOCUMENTOS NECESS�RIOS PARA ADMISS�O:", 0, 1);
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(3, 0.5, "    1. UMA FOTO 3X4;", 0, 1);
			$pdf->Cell(3, 0.5, "    2. CARTEIRA PROFISSIONAL (CTPS);", 0, 1);
			$pdf->Cell(3, 0.5, "    3. XEROX RG (IDENTIDADE) E DO CPF;", 0, 1);
			$pdf->Cell(3, 0.5, "    4. XEROX TITULO ELEITORAL;", 0, 1);
			$pdf->Cell(3, 0.5, "    5. XEROX DA CARTEIRA DE MOTORISTA;", 0, 1);
			$pdf->Cell(3, 0.5, "    6. COMPROVANTE DE RESID�NCIA (COM END. COMPLETO);", 0, 1);
			$pdf->Cell(3, 0.5, "    7. CERTIFICADO DE RESERVISTA;", 0, 1);
			$pdf->Cell(3, 0.5, "    8. ATESTADO M�DICO ADMISSIONAL;", 0, 1);
			$pdf->Cell(3, 0.5, "    9. CERTID�O DE NASCIMENTO DOS FILHOS MENORES DE 14 ANOS;", 0, 1);
			
		break;
		
		//insalubridade tempoir�ria
		case 15:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_insalubridade
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_insalubridade.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   rh_insalubridade.id_insalubridade = '". $_GET["id_insalubridade"] ."'
								order by pessoas.nome_rz asc
								") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "ACORDO DE PAGAMENTO DE INSALUBRIDADE", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->WriteText("               Fica acordado que no dia <". data_extenso_param($rs->data_insalubridade) ."> das <". $rs->hora_inicio ."> �s <". $rs->hora_fim .">, o(a) funcion�rio(a) <". $rs->nome_rz ."> ir� atuar, excepcionalmente nesta data e hor�rio, como funcion�rio substituto no setor <". pega_departamento($rs->id_departamento) .">. Sendo que neste dia o funcion�rio receber� insalubridade correspondente a exposi��o ao risco e receber� os EPI's necess�rios para o desempenho da fun��o conforme PPRA da empresa. ");
			
			$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			
			$pdf->MultiCell(0, 0.6, "               ". ucwords(strtolower($rs_empresa->cidade)) .'/'. $rs_empresa->uf .", ". data_extenso_param($rs->data_insalubridade) .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
		//mudan�a de turno
		case 16:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_carreiras.id_carreira =  '". $_GET["id_carreira"] ."'
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.id_turno = rh_turnos.id_turno
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by pessoas.nome_rz asc
								") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "ALTERA��O DE CONTRATO DE TRABALHO", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CONTRATANTE:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs_empresa->nome_rz, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CONTRATADO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->nome_rz, 0 , 1);
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "               Pelo presente instrumento, de um lado ". $rs_empresa->nome_rz ." doravante denominada CONTRATANTE, e de outro lado, ". $rs->nome_rz .", portadora da Carteira de Trabalho n� ". $rs->ctps ." s�rie ". $rs->serie_ctps .", doravante denominado CONTRATADO(A), ambos j� qualificados anteriormente pelo contrato de trabalho, celebram entre si, com m�tuo consentimento e de acordo com a C.L.T., a altera��o na cl�usula 2� do Contrato de Trabalho, que passa a ter o seguinte teor:", 0 , 1);
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "Cl�usula 2� - A partir do dia ". desformata_data($rs->data) .", o empregado mudar� de turno, passando a cumprir os seguintes hor�rios:", 0 , 1);
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3.5, 0.6, "DEPARTAMENTO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_departamento($rs->id_departamento), 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3.5, 0.6, "CARGO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_cargo($rs->id_cargo), 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3.5, 0.6, "TURNO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_turno($rs->id_turno), 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3.5, 0.6, "REGIME:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_regime_turno($rs->id_regime), 0 , 1);
			
			$pdf->SetXY(12, 10.9);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			$pdf->Cell(2.1, 0.3, "DIA DA SEMANA", 1 , 0, "C", 1);
			$pdf->Cell(1.3, 0.3, "ENTRADA", 1 , 0, "C", 1);
			$pdf->Cell(1.7, 0.3, "INTERVALO", 1 , 0, "C", 1);
			$pdf->Cell(1.3, 0.3, "SA�DA", 1 , 1, "C", 1);
			
			for ($i=0; $i<=6; $i++) {
				$result_dia= mysql_query("select * from rh_turnos_horarios
											where id_turno = '". $rs->id_turno ."'
											and   id_dia = '$i'
											");
				$rs_dia= mysql_fetch_object($result_dia);
				
				if (($i%2)==0) $fill= 1;
				else $fill= 0;
				
				$pdf->SetX(12);
				$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
				$pdf->Cell(2.1, 0.3, strtoupper(traduz_dia($i)), 1 , 0, "C", $fill);
				$pdf->SetFont('ARIALNARROW', '', 8);
				$pdf->Cell(1.3, 0.3, substr($rs_dia->entrada, 0, 5), 1 , 0, "C", $fill);
				$pdf->Cell(1.7, 0.3, pega_detalhes_intervalo($rs->id_intervalo, $i, 0), 1 , 0, "C", $fill);
				$pdf->Cell(1.3, 0.3, substr($rs_dia->saida, 0, 5), 1 , 1, "C", $fill);
				
			}
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->MultiCell(0, 0.5, "E por estarem de acordo assinam em duas vias de igual teor e forma.", 0 , 1);
			$pdf->Ln();
			
			$pdf->MultiCell(0, 0.5, "               ". ucwords(strtolower($rs_empresa->cidade)) .'/'. $rs_empresa->uf .", ". data_extenso_param($rs->data) .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
		
		//autorizacao de hora extra
		case 17:
		
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_turnos, rh_cargos, rh_enderecos, rh_he_autorizacao
									where pessoas.id_pessoa = rh_funcionarios.id_pessoa
									and   rh_he_autorizacao.id_he_autorizacao =  '". $_GET["id_he_autorizacao"] ."'
									and   rh_he_autorizacao.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_carreiras.id_turno = rh_turnos.id_turno
									and   rh_carreiras.id_cargo = rh_cargos.id_cargo
									and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   rh_funcionarios.id_pessoa = rh_enderecos.id_pessoa
									order by pessoas.nome_rz asc
									") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "AUTORIZA��O DE HORA EXTRA", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "EMPRESA:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs_empresa->nome_rz, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "FUNCION�RIO:", 0, 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->nome_rz, 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "DEPARTAMENTO:", 0, 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(4, 0.6, pega_departamento($rs->id_departamento), 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "TURNO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(5, 0.6, pega_turno($rs->id_turno), 0, 0);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(1.5, 0.6, "CARGO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, pega_cargo($rs->id_cargo), 0, 1);
			
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "DATA/HORA:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(2, 0.6, desformata_data($rs->data_he) ." ". $rs->hora_he, 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "COMPENSA��O:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, desformata_data($rs->data_compensacao), 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "MOTIVO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(2, 0.6, $rs->motivo, 0, 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 0, 'C');
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, "", 0, 1, 'R');
			
			$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, "SUPERVISOR", 'T', 0, 'C');
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, "GERENTE", 'T', 1, 'C');
			
		break;
		
		//termo de substitui��o
		case 18:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras, rh_substituicao_funcao
									where pessoas.id_pessoa = rh_funcionarios.id_pessoa
									and   rh_substituicao_funcao.id_substituicao_funcao =  '". $_GET["id_substituicao_funcao"] ."'
									and   rh_substituicao_funcao.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
									order by pessoas.nome_rz asc
									") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "TERMO DE SUBSTITUI��O TEMPOR�RIA DE FUN��O", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 10);
			
			$pdf->WriteText("               A empresa  <". $rs_empresa->nome_rz .">, ora empregadora, e o empregado <". $rs->nome_rz ."> ajustam que a partir da assinatura deste termo ocorrer� uma substitui��o tempor�ria de fun��o, conforme permiss�o contida na cl�usula 40 da Conven��o Coletiva de Trabalho da categoria, passando o mesmo da fun��o <". pega_cargo($rs->id_cargo_atual) ."> para a de <". $rs->funcao_substituicao ."> pelo per�odo de <". $rs->periodo_substituicao .">.");
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("               Com o ajuste firmado, o empregado cumprir� temporariamente as obriga��es da fun��o substituta conforme regras internas da empregadora.");
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("               Importante ficar ajustado que a empresa somente pagar� temporariamente o sal�rio pertinente � fun��o tempor�ria assumida pelo empregado  no caso do sal�rio do substitu�do for maior que o do substituto. No entanto, o pagamento suplementar a ser pago pela empresa n�o incorporar� ao sal�rio do mesmo para nenhum fim, passando o empregado a receber o mesmo patamar salarial quando retornar ao cargo a que foi contratado.");
			$pdf->Ln();$pdf->Ln();
			
			$pdf->WriteText("               <Em face do ajustado, as  partes assinam o presente termo para que a altera��o contratual em car�ter tempor�rio surta seus efeitos jur�dicos no contrato de trabalho do empregado pelo prazo determinado acordado previamente.>");
			$pdf->Ln();
			$pdf->Ln();$pdf->Ln();
			
			$pdf->MultiCell(0, 0.6, "               ". ucwords(strtolower($rs_empresa->cidade)) .'/'. $rs_empresa->uf .", ". data_extenso_param($rs->data_substituicao) .".", 0 , 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
		
		//justificativa de falta
		case 19:
			$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_afastamentos
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_afastamentos.id_afastamento =  '". $_GET["id_afastamento"] ."'
								and   rh_afastamentos.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by pessoas.nome_rz asc
								") or die(mysql_error());
			$rs= mysql_fetch_object($result);
			
			$pdf->AddPage();
			
			if ($rs->tipo_afastamento=='d') $titulo= "ADVERT�NCIA";
			if ($rs->tipo_afastamento=='s') $titulo= "AVISO DE SUSPENS�O";
			
			$pdf->SetXY(7,1.75);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 14);
			$pdf->Cell(0, 0.6, "JUSTIFICATIVA DE FALTA", 0 , 1, 'R');
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 12);
			$pdf->Cell(0, 0.6, "", 0 , 1, 'R');
			
			$pdf->Ln();$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CONTRATANTE:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs_empresa->nome_rz, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "CONTRATADO:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, $rs->nome_rz, 0 , 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(3, 0.6, "DATA:", 0 , 0);
			$pdf->SetFont('ARIALNARROW', '', 10);
			$pdf->Cell(0, 0.6, "____ / ____ / ________", 0 , 1);
			$pdf->Ln();
			
			$data= desformata_data($rs->data);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->MultiCell(0, 0.6, "DESCREVA O MOTIVO QUE LEVOU A FALTAR NO DIA:", 0 , 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->MultiCell(0, 0.6, "AVALIA��O DO GESTOR:", 0 , 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Ln();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->MultiCell(0, 0.6, "AVALIA��O RECURSOS HUMANOS:", 0 , 1);
			
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			$pdf->Cell(0, 0.6, "", "B", 1);
			
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(7, 0.75, $rs_empresa->nome_rz, 'T', 0);
			$pdf->Cell(3, 0.75, "", 0, 0);
			$pdf->Cell(7, 0.75, $rs->nome_rz, 'T', 1, 'R');
		break;
	}
	
	
	$pdf->AliasNbPages();
	$pdf->Output("documento_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]= "";
}
?>	
