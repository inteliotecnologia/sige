<?
require_once("conexao.php");
require_once("funcoes.php");

$marcador_inicial = microtime(1);

if (pode("pl12", $_SESSION["permissao"])) {
	
	define('FPDF_FONTPATH', 'includes/fpdf/font/');
	
	if ($_POST["origem"]!="") $origem= $_POST["origem"];
	else $origem= $_GET["origem"];
	
	if ($_POST["denominacao"]!="") $denominacao= $_POST["denominacao"];
	else $denominacao= $_GET["denominacao"];
	
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	else $id_cliente= $_GET["id_cliente"];
	
	if ($_POST["id_percurso"]!="") $id_percurso= $_POST["id_percurso"];
	else $id_percurso= $_GET["id_percurso"];
	if ($id_percurso=="") die("Selecione a entrega.");
	
	if ($_POST["data"]!="") $data= $_POST["data"];
	else $data= $_GET["data"];
	
	if ($_POST["denominacao"]!="") $denominacao= $_POST["denominacao"];
	else $denominacao= $_GET["denominacao"];
		
	if ($_POST["data_tipo"]!="") $data_tipo= $_POST["data_tipo"];
	else $data_tipo= $_GET["data_tipo"];
	
	if ($_POST["mostrar_peso"]!="") $mostrar_peso= $_POST["mostrar_peso"];
	else $mostrar_peso= $_GET["mostrar_peso"];
	
	if ($_POST["obs"]!="") $obs= $_POST["obs"];
	elseif ($_GET["obs"]!="") $obs= $_GET["obs"];
	
	//busca a empresa atendente
	$id_empresa_atendente_aqui= pega_empresa_atendente($id_cliente);
	
	//busca os dados de como � cobrado do cliente, contrato, etc
	$result_contrato= mysql_query("select fi_contratos.*,
									pessoas.tipo_pedido, pessoas.basear_nota_data, pessoas.basear_peso from pessoas, fi_contratos
							 		where pessoas.id_pessoa = '$id_cliente'
									and   pessoas.id_contrato = fi_contratos.id_contrato
									");
	$rs_contrato= mysql_fetch_object($result_contrato);
	
	//busca se o pedido � baseado em roupa limpa ou roupa suja, independente de ser peso empresa ou peso cliente
	$tipo= $rs_contrato->tipo_pedido;
	$tipo_pedido= $rs_contrato->tipo_pedido;
	//salva o id do contrato nesta var
	$id_contrato= $rs_contrato->id_contrato;
	
	//est� gerando a partir da lista de percursos
	if ($origem==2) {
		if ($rs_contrato->peso_nota==1) $mostrar_peso=1;
	}
	
	if ( ($rs_contrato->basear_peso=="2") || ($tipo==2) ) $mostrar_peso=0;
	
	/*if ($_SESSION[id_usuario]==13) { echo "select *, DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') as data_percurso, 
									DATE_FORMAT(tr_percursos.data_hora_percurso, '%H:%i:%s') as hora_percurso
									from tr_percursos, tr_percursos_clientes
									where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
									and   ( tr_percursos.tipo = '2' or tr_percursos.tipo = '5' )
									and   tr_percursos_clientes.id_cliente = '". $id_cliente ."'
									and   tr_percursos.id_percurso= '". $id_percurso ."'
									order by tr_percursos.data_hora_percurso asc
									"; die(); }
	*/
	
	$result_percurso= mysql_query("select *, DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') as data_percurso, 
									DATE_FORMAT(tr_percursos.data_hora_percurso, '%H:%i:%s') as hora_percurso
									from tr_percursos, tr_percursos_clientes
									where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
									and   ( tr_percursos.tipo = '2' or tr_percursos.tipo = '5' )
									and   tr_percursos_clientes.id_cliente = '". $id_cliente ."'
									and   tr_percursos.id_percurso= '". $id_percurso ."'
									order by tr_percursos.data_hora_percurso asc
									");
	
	$linhas_percurso= mysql_num_rows($result_percurso);
	$rs_percurso= mysql_fetch_object($result_percurso);
	
	$data_percurso= explode(" ", $rs_percurso->data_hora_percurso);
	$data_hora_ultima_coleta= soma_data_hora($rs_percurso->data_hora_percurso, 0, 0, 0, -10, 0, 0);
	
	//if ($_SESSION[id_usuario]==13) {
	//echo $data_hora_ultima_coleta; die; }
	
	//somente decide se o nome � extra ou complementar
	if ($rs_percurso->tipo=="5") {
		if ($denominacao!="") $tit_extra= pega_denominacao_extra($denominacao);
		else $tit_extra= "EXTRA";
		
		$extra=1;
	}
	else {
		$tit_extra= "";
		$extra=0;
	}
	
	//echo $rs_percurso->tipo;
	
	$entrega= $rs_percurso->num_percurso_tipo;
	
	//inicia_transacao();
	$var= 0;
	
	/*if ($_SESSION[id_usuario]==13) {
		echo "select * from op_pedidos 
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_cliente = '$id_cliente'
										and   id_contrato = '$id_contrato'
										and   basear_pedido = '". $rs_contrato->basear_nota_data ."'
										and   data_pedido = '". formata_data_hifen($data) ."'
										and   entrega = '". $rs_percurso->num_percurso_tipo ."'
										and   extra = '$extra'
										limit 1
										";
										die();
	}*/									
	
	$result_pedido_pre= mysql_query("select * from op_pedidos 
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_cliente = '$id_cliente'
										and   id_contrato = '$id_contrato'
										and   basear_pedido = '". $rs_contrato->basear_nota_data ."'
										and   data_pedido = '". formata_data_hifen($data) ."'
										and   entrega = '". $rs_percurso->num_percurso_tipo ."'
										and   extra = '$extra'
										limit 1
										");
	
	$linhas_pedido_pre= mysql_num_rows($result_pedido_pre);
	
	//j� tem o pedido para este cliente, contrato, basear pedido, data, n�mero de entrega e se � extra ou n�o, pega o n�mero...
	if ($linhas_pedido_pre>0) {
		$rs_pedido_pre= mysql_fetch_object($result_pedido_pre);
		
		$id_pedido_aqui= $rs_pedido_pre->num_pedido;
		
		$id_pedido= $rs_pedido_pre->id_pedido;
		$data_tipo= $rs_pedido_pre->data_tipo;
		
		if ($denominacao=="") $denominacao= $rs_pedido_pre->denominacao;
	}
	//n�o tem o pedido, cadastra ele e salva na mesma vari�vel
	else {
		$result_pedido= mysql_query("select * from op_pedidos 
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_contrato = '$id_contrato'
										order by num_pedido desc limit 1
										");
		
		$rs_pedido= mysql_fetch_object($result_pedido);
	
		$num_pedido= ($rs_pedido->num_pedido+1);
		
		$result_pedido_cria= mysql_query("insert into op_pedidos
											(id_empresa, id_contrato, num_pedido, id_cliente, data_pedido, basear_pedido, entrega, extra, denominacao, data_tipo, id_usuario)
											values
											('". $_SESSION["id_empresa"] ."', '$id_contrato', '$num_pedido', '$id_cliente',
											'". formata_data($data) ."', '". $rs_contrato->basear_nota_data ."', '". $rs_percurso->num_percurso_tipo ."', '$extra', '$denominacao', '$data_tipo', '". $_SESSION["id_usuario"] ."')
											") or die(mysql_error());
		$id_pedido_aqui= $num_pedido;
		
		$id_pedido= mysql_insert_id();
	}
	
	if ( ($denominacao=="") || ($denominacao=="0") ) $denominacao=1;
	
	//if ($_SESSION[id_usuario]==13) echo $denominacao; die();
	
	
	
	//cobrar pela roupa limpa
	if ($tipo==1) {
		$tabela= "op_limpa_pesagem";
		$obs2= "Peso da roupa limpa.";
		
		$linha1= "CONTROLE OPERACIONAL";
		
		//se tiver algum contrato, n�o for particular
		if ($rs_contrato->tipo_contrato!=0) {
			$linha2= "CONTRATO ". $rs_contrato->contrato;
			$linha3= "RELAT�RIO DE ENTREGA ". $tit_extra ." N� ". fnumi($id_pedido_aqui);
		}
		else {
			$linha2= "RELAT�RIO DE ENTREGA ". $tit_extra ." N� ". fnumi($id_pedido_aqui);
		}
	}
	//suja
	else {
		$tabela= "op_suja_pesagem";
		$obs2= "Peso da roupa suja.";
		
		/*$linha1= "PEDIDO DE LAVANDERIA N� ". fnumi($id_pedido_aqui);
		$linha2= "FORMUL�RIO DE COLETA E ENTREGA";
		$linha3= "";*/
		
		$linha1= "CONTROLE OPERACIONAL";
		
		//se tiver algum contrato, n�o for particular
		if ($rs_contrato->tipo_contrato!=0) {
			$linha2= "CONTRATO ". $rs_contrato->contrato;
			$linha3= "RELAT�RIO DE ENTREGA ". $tit_extra ." N� ". fnumi($id_pedido_aqui);
		}
		else {
			$linha2= "RELAT�RIO DE ENTREGA ". $tit_extra ." N� ". fnumi($id_pedido_aqui);
		}
	}
	
	require("includes/fpdf/fpdf.php");
	require("includes/fpdf/modelo_retrato_seco.php");
	
	$pdf=new PDF("P", "cm", "A4");
	$pdf->SetMargins(2, 2, 2);
	$pdf->SetAutoPageBreak(true, 1.5);
	$pdf->SetFillColor(210,210,210);
	$pdf->AddFont('ARIALNARROW');
	$pdf->AddFont('ARIAL_N_NEGRITO');
	$pdf->AddFont('ARIAL_N_ITALICO');
	$pdf->AddFont('ARIAL_N_NEGRITO_ITALICO');
	$pdf->SetFont('ARIALNARROW');
	
	//se estiver buscando pela data de entrega
	if ($data_tipo=="e") {
		$data_que_vale= $data;
		$data_pedido_fecha= soma_data($data, 0, 0, 0);
		$data= soma_data($data, 0, 0, 0);
		
		$data_de_entrega= $data;
	}
	//buscando pela coleta
	else {
		$data_que_vale= soma_data($data, 1, 0, 0);
		$data_pedido_fecha= $data;
		
		$data_de_entrega= soma_data($data, 1, 0, 0);
	}
		
	$altura_padrao= 16;

	$pdf->Ln();$pdf->Ln();
	
	for ($i=0; $i<2; $i++) {
		
		//faz somente o c�lculo da divis�o da folha
		/*if (($id_contrato==1) && ($i==1) && ($id_cliente!=240) && ($rs_contrato->basear_peso=="1")) {
			$pdf->SetXY(2, 13);
			$altura_nota=($altura_padrao*$i)+1.75;
		}
		else {*/
			$pdf->AddPage();
			$altura_nota=1.75;
		//}
		
		if (file_exists(CAMINHO ."empresa_". $id_empresa_atendente_aqui .".jpg"))
			$pdf->Image("". CAMINHO ."empresa_". $id_empresa_atendente_aqui .".jpg", 2, $altura_nota, 5, 1.9287);
		
		$pdf->SetXY(7,$altura_nota);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(0, 0.6, $linha1, 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(0, 0.6, $linha2, 0 , 1, 'R');
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 11);
		$pdf->Cell(0, 0.6, $linha3, 0 , 1, 'R');
		
		$pdf->Ln();
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		$pdf->Cell(3, 0.6, "CLIENTE:", 'LTB', 0, 'L');
		
		$pdf->SetFont('ARIALNARROW', '', 9);
		$pdf->Cell(0, 0.6, pega_pessoa($id_cliente), 'TBR', 1, 'L');
		
		//se o peso total da nota deve ser o peso suja do cliente (coleta)
		if ($rs_contrato->basear_peso=="2")
			$str_coleta = " and   tr_percursos.data_hora_percurso <= '". $data_hora_ultima_coleta ."' ";
		else
			$str_coleta = " and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') < '". $data_percurso[0] ."' ";
				
		//pegar a �ltima coleta efetuada dentro do prazo
		$result_coleta_ref= mysql_query("select * from tr_percursos, tr_percursos_clientes, tr_percursos_passos
												where tr_percursos.tipo = '1'
												and   tr_percursos_passos.id_percurso = tr_percursos.id_percurso
												and   tr_percursos_passos.passo = '2'
												and   tr_percursos_passos.id_cliente = tr_percursos_clientes.id_cliente
												and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
												and   tr_percursos_clientes.id_cliente = '". $id_cliente ."'
												$str_coleta
												order by tr_percursos.data_hora_percurso desc
												limit 1
												") or die(mysql_error());
												
		$rs_coleta_ref= mysql_fetch_object($result_coleta_ref);
		
		
		//se o peso total da nota deve ser o peso suja do cliente (coleta)
		if ($rs_contrato->basear_peso=="2")
			$data_coleta= explode(" ", $rs_coleta_ref->data_hora_percurso);
		else
			$data_coleta[0]= soma_data($data_percurso[0], -1, 0, 0);
		
		/*if ($_SESSION[id_usuario]==13) {
			echo "select * from tr_percursos, tr_percursos_clientes, tr_percursos_passos
												where tr_percursos.tipo = '1'
												and   tr_percursos_passos.id_percurso = tr_percursos.id_percurso
												and   tr_percursos_passos.passo = '2'
												and   tr_percursos_passos.id_cliente = tr_percursos_clientes.id_cliente
												and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
												and   tr_percursos_clientes.id_cliente = '". $id_cliente ."'
												$str_coleta
												order by tr_percursos.data_hora_percurso desc
												limit 1
												<br><br>
												";
												
												echo desformata_data($data_coleta[0]);
												
												die();
		}*/
		
		if ($rs_contrato->basear_nota_data==1) {
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			$pdf->Cell(3, 0.6, "DATA DE COLETA:", 'LTB', 0, 'L');
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->Cell(0, 0.6, desformata_data($data_coleta[0]), 'TRB', 1, 'L');
		}
		
		$pdf->Ln();
		
		//se o percurso � v�lido
		if ($linhas_percurso>0) {
			
			if ($extra==1) {
				$str_detalhe= " and   ( tr_percursos.tipo = '2' or tr_percursos.tipo = '5' ) ";
			}
			else {
				$str_detalhe= " and   tr_percursos.tipo = '2' ";
			}
			
			//pegar a entrega normal anterior a esta, para ter como par�metro
			$result_ultima_entrega_normal= mysql_query("select *, DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') as data_percurso,
														DATE_FORMAT(tr_percursos.data_hora_percurso, '%H:%i:%s') as hora_percurso
														from tr_percursos, tr_percursos_clientes
														where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
														$str_detalhe
														and   tr_percursos_clientes.id_cliente = '". $id_cliente ."'
														and   tr_percursos.data_hora_percurso < '". $rs_percurso->data_hora_percurso ."'
														order by tr_percursos.data_hora_percurso desc
														limit 1
														");
			$linhas_ultima_entrega_normal= mysql_num_rows($result_ultima_entrega_normal);
			$rs_ultima_entrega_normal= mysql_fetch_object($result_ultima_entrega_normal);		
			
			$peso_coleta_temp=0;
			
			//se o peso total da nota deve ser o peso suja do cliente (coleta)
			if ($rs_contrato->basear_peso=="2") {										
				
				$peso_coleta_temp= $rs_coleta_ref->peso;
				$peso_coleta_pnr= $rs_coleta_ref->pnr;
				
				if ( ($peso_coleta_temp==0) || ($peso_coleta_temp=="") || ($peso_coleta_temp=="0")  || ($peso_coleta_temp=="0,00") || ($peso_coleta_temp==NULL) )
					die("O peso da coleta ainda n�o foi lan�ado para este cliente/nota.<br><br><a target='_blank' href='./?pagina=transporte/percurso_dados&id_percurso=". $rs_coleta_ref->id_percurso ."'>Clique aqui</a> para cadastrar o peso.<br><br>Ap�s cadastrar o peso, d� F5 nesta p�gina para gerar o relat�rio.");
				
				//if ($_SESSION["id_usuario"]==13) { echo $rs_coleta_ref->id_percurso ." - ". $rs_coleta_ref->peso; die("<-- ");
			}
				
			//se encontrou uma �ltima entrega normal para ter como refer�ncia�
			if ($linhas_ultima_entrega_normal>0) {
				
				$data_inicio_relatorio= $rs_ultima_entrega_normal->data_percurso;
				$hora_inicio_relatorio= $rs_ultima_entrega_normal->hora_percurso;
			}
			//� a primeira entrega ever, o sistema vai fazer a conta de pesagens at� um dia antes para ter um come�o
			else {
				$data_inicio_relatorio= soma_data($rs_percurso->data_percurso, -1, 0, 0);
				$hora_inicio_relatorio= $rs_percurso->hora_percurso;
			}
			
			$data_fim_relatorio= $rs_percurso->data_percurso;
			$hora_fim_relatorio= $rs_percurso->hora_percurso;
			
			$id_percurso_referencia= $rs_ultima_entrega_normal->id_percurso;
			$data_hora_percurso_atual= $rs_percurso->data_percurso ." ". $rs_percurso->hora_percurso;
			
			$str_geral = " 
			
						and data_hora_pesagem < '". $data_fim_relatorio ." ". $hora_fim_relatorio ."'
						and data_hora_pesagem >= '". $data_inicio_relatorio ." ". $hora_inicio_relatorio ."'
						";
			
		}
		//nao tem nenhuma entrega no dia em quest�o, d� um erro
		else {
						
			$var++;
			finaliza_transacao($var);
			
			die("Nenhuma entrega encontrada!");
		
		}
		
		//echo die("oi!");
		
		// ----------------------------- tentando otimizar ----------------------------
		
		if ($i==0) {
			$result_tt1= mysql_query("CREATE TEMPORARY TABLE op_limpa_pesagem_virtual
									 ( id_pesagem int, id_empresa int, id_cliente int, data_pesagem date, hora_pesagem time, data_hora_pesagem timestamp, id_grupo int, peso float(5,2), costura int,
									  goma int, roupa_alheia int, id_turno int, extra int )
									 TYPE=MEMORY;
									 ") or die("1xxx: ". mysql_error());
									 
			$result_tt2= mysql_query("CREATE TEMPORARY TABLE op_limpa_pesagem_pecas_virtual
									 ( id_pesagem_peca int, id_pesagem int, id_tipo_roupa int, num_pacotes int, qtde_pacote int, qtde_pecas_sobra int, pacotes_sobra int )
									 TYPE=MEMORY;
									 ") or die("2: ". mysql_error());
			
			
			$result_tt_pesagem= mysql_query("select * from op_limpa_pesagem
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_cliente = '". $id_cliente ."'
												$str_geral
												");
			while ($rs_tt_pesagem= mysql_fetch_object($result_tt_pesagem)) {
				
				$result_tt_pecas= mysql_query("select * from op_limpa_pesagem_pecas
												where id_pesagem = '". $rs_tt_pesagem->id_pesagem ."'
												");
												
				while ($rs_tt_pecas= mysql_fetch_object($result_tt_pecas)) {
					
					$result_tt_insere1= mysql_query("insert into op_limpa_pesagem_pecas_virtual
														(id_pesagem_peca, id_pesagem, id_tipo_roupa, num_pacotes, qtde_pacote, qtde_pecas_sobra, pacotes_sobra)
														values
														('". $rs_tt_pecas->id_pesagem_peca ."', '". $rs_tt_pecas->id_pesagem ."', '". $rs_tt_pecas->id_tipo_roupa ."', '". $rs_tt_pecas->num_pacotes ."', 
														'". $rs_tt_pecas->qtde_pacote ."', '". $rs_tt_pecas->qtde_pecas_sobra ."', '". $rs_tt_pecas->pacotes_sobra ."')
														") or die("3: ". mysql_error());
					
				}
				
				$result_tt_insere2= mysql_query("insert into op_limpa_pesagem_virtual
														(id_pesagem, id_empresa, id_cliente, data_pesagem, hora_pesagem, data_hora_pesagem, id_grupo, peso, costura, goma, roupa_alheia, id_turno, extra)
														values
														('". $rs_tt_pesagem->id_pesagem ."', '". $rs_tt_pesagem->id_empresa ."', '". $rs_tt_pesagem->id_cliente ."', '". $rs_tt_pesagem->data_pesagem ."', '". $rs_tt_pesagem->hora_pesagem ."', 
														'". $rs_tt_pesagem->data_hora_pesagem ."', '". $rs_tt_pesagem->id_grupo ."', '". $rs_tt_pesagem->peso ."',
														'". $rs_tt_pesagem->costura ."', '". $rs_tt_pesagem->goma ."', '". $rs_tt_pesagem->roupa_alheia ."',
														'". $rs_tt_pesagem->id_turno ."', '". $rs_tt_pesagem->extra ."'
														)
														") or die("4: ". mysql_error());
				
			}
		}
		
		// /----------------------------- tentando otimizar ----------------------------
		
		//o pedido � baseado na roupa suja, ent�o n�o precisa aparecer coluna de peso aqui
		if ($tipo_pedido=="2") {
			$quebra_num_pecas=1;
			$largura_tipo_roupa=12;
		}
		else {
			$quebra_num_pecas=0;
			$largura_tipo_roupa=9.5;
		}
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		$pdf->Cell($largura_tipo_roupa, 0.5, "TIPO DE ROUPA", 1, 0, "L", 1);
		$pdf->Cell(2.5, 0.5, "NUM. PACOTES", 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.5, "NUM. PE�AS", 1, $quebra_num_pecas, "C", 1);
		//if ($_POST["mostrar_peso"]==1)

		if ($tipo_pedido!="2")
			$pdf->Cell(2.5, 0.5, "PESO", 1, 1, "C", 1);
		
		$pdf->SetFont('ARIALNARROW', '', 8);
		
		if ($extra==1) $str_geral .= " and   extra = '1' ";
		else $str_geral .= " and   extra = '0' ";
		
		$result_total= mysql_query("select sum(op_limpa_pesagem_virtual.peso) as peso_total from op_limpa_pesagem_virtual
											where op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
											and   goma = '0'
											and   roupa_alheia = '0'
											$str_geral
											");
		
		$rs_total= mysql_fetch_object($result_total);
		
		if ($rs_total->peso_total>=$rs_total_entrega_extra->peso_total) $peso_total= $rs_total->peso_total-$rs_total_entrega_extra->peso_total;
		else $peso_total= $rs_total->peso_total;
		
		$peso_total_peca_geral= 0;
		$total_pacotes_peca_geral= 0;
		$total_pecas_peca_geral= 0;
		$peso_informa_extra_texto="";
		
		
		for ($g=1; $g<3; $g++) {
			if ($g==1) {
				
				$result_pesagem_pecas= mysql_query("select distinct(op_limpa_pesagem_pecas_virtual.id_tipo_roupa) as id_tipo_roupa
													from op_limpa_pecas, op_limpa_pesagem_virtual, op_limpa_pesagem_pecas_virtual
													where op_limpa_pesagem_virtual.id_pesagem = op_limpa_pesagem_pecas_virtual.id_pesagem
													and   op_limpa_pesagem_pecas_virtual.id_tipo_roupa = op_limpa_pecas.id_peca
													and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
													and   op_limpa_pecas.id_grupo = '$g'
													and   op_limpa_pesagem_virtual.goma = '0'
													and   op_limpa_pesagem_virtual.roupa_alheia = '0'
													$str_geral
													order by op_limpa_pecas.peca asc
													") or die(mysql_error());
				
				$pdf->SetFillColor(235,235,235);
				
				$j=0;
				while ($rs_pesagem_pecas= mysql_fetch_object($result_pesagem_pecas)) {
					
					//if (($j%2)==0) $fill=0; else $fill= 1;
					$fill= 0;
				
					$result_pesagem= mysql_query("select * from op_limpa_pesagem_virtual, op_limpa_pesagem_pecas_virtual
													where op_limpa_pesagem_virtual.id_pesagem = op_limpa_pesagem_pecas_virtual.id_pesagem
													and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
													and   op_limpa_pesagem_virtual.goma = '0'
													and   op_limpa_pesagem_virtual.roupa_alheia = '0'
													and   op_limpa_pesagem_pecas_virtual.id_tipo_roupa = '". $rs_pesagem_pecas->id_tipo_roupa ."'
													$str_geral
													") or die(mysql_error());
					
					
					$linhas_pesagem= mysql_num_rows($result_pesagem);
					
					if ($linhas_pesagem>0) {
						$peso_total_peca= 0;
						$total_pacotes_peca= 0;
						$total_pecas_peca= 0;
						$peso_informa_extra="";
						
						while ($rs_pesagem= mysql_fetch_object($result_pesagem)) {
							$peso_total_peca += $rs_pesagem->peso;
							$total_pacotes_peca += $rs_pesagem->num_pacotes;
							$total_pacotes_peca += $rs_pesagem->pacotes_sobra;
							
							//if ($rs_pesagem->qtde_pecas_sobra>0) $total_pacotes_peca++;
							
							$total_pecas_aqui= ($rs_pesagem->qtde_pacote*$rs_pesagem->num_pacotes)+$rs_pesagem->qtde_pecas_sobra;
							$total_pecas_peca += $total_pecas_aqui;
						}
						
						$pdf->Cell($largura_tipo_roupa, 0.45, pega_pecas_roupa($rs_pesagem_pecas->id_tipo_roupa) . $peso_informa_extra, 1, 0, "L", $fill);
						$pdf->Cell(2.5, 0.45, $total_pacotes_peca, 1, 0, "C", $fill);
						$pdf->Cell(2.5, 0.45, $total_pecas_peca, 1, $quebra_num_pecas, "C", $fill);
						
						if ($mostrar_peso==1) $peso2_aqui= fnum($peso_total_peca) ." kg";
						else $peso2_aqui= "";
						
						if ($tipo_pedido!="2") $pdf->Cell(2.5, 0.45, $peso2_aqui, 1, 1, "C", $fill);
						
						$peso_total_peca_geral += $peso_total_peca;
						$total_pacotes_peca_geral += $total_pacotes_peca;
						$total_pecas_peca_geral += $total_pecas_peca;
						
						$j++;
					}
				}
			}
			elseif ($g==2) {
				
				$result_pesagem_pecas= mysql_query("select distinct(op_limpa_pesagem_pecas_virtual.id_tipo_roupa)
													from op_limpa_pecas, op_limpa_pesagem_virtual, op_limpa_pesagem_pecas_virtual
													where op_limpa_pesagem_virtual.id_pesagem = op_limpa_pesagem_pecas_virtual.id_pesagem
													and   op_limpa_pesagem_pecas_virtual.id_tipo_roupa = op_limpa_pecas.id_peca
													and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
													and   op_limpa_pecas.id_grupo = '$g'
													and   op_limpa_pesagem_virtual.goma = '0'
													and   op_limpa_pesagem_virtual.roupa_alheia = '0'
													$str_geral
													order by op_limpa_pecas.peca asc
													") or die(mysql_error());
								
				$pdf->SetFillColor(235,235,235);
				
				$linhas_pesagem_pecas= mysql_num_rows($result_pesagem_pecas);
				
				//ver se � par ou �mpar
				if (($linhas_pesagem_pecas%2)==0) $paridade=1;
				else $paridade= 0;
				
				$result_pesagem= mysql_query("select sum(op_limpa_pesagem_virtual.peso) as peso_total_roupa
												from op_limpa_pesagem_virtual
												where op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
												and   op_limpa_pesagem_virtual.id_grupo = '2'
												and   op_limpa_pesagem_virtual.goma = '0'
												and   op_limpa_pesagem_virtual.roupa_alheia = '0'
												$str_geral
												") or die(mysql_error());
				$rs_pesagem= mysql_fetch_object($result_pesagem);
				
				$j=1;
				while ($rs_pesagem_pecas= mysql_fetch_object($result_pesagem_pecas)) {
					
					//if (($j%2)==1) $fill=0; else $fill= 1;
					$fill= 0;
					
					$result_contagem= mysql_query("select * from op_limpa_pesagem_virtual, op_limpa_pesagem_pecas_virtual
													where op_limpa_pesagem_virtual.id_pesagem = op_limpa_pesagem_pecas_virtual.id_pesagem
													and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
													and   op_limpa_pesagem_pecas_virtual.id_tipo_roupa = '". $rs_pesagem_pecas->id_tipo_roupa ."'
													and   op_limpa_pesagem_virtual.goma = '0'
													and   op_limpa_pesagem_virtual.roupa_alheia = '0'
													$str_geral
													") or die(mysql_error());
					$linhas_contagem= mysql_num_rows($result_contagem);
					
					if ($linhas_contagem>0) {
						//$peso_total_peca= 0;
						$total_pacotes_peca= 0;
						$total_pecas_peca= 0;
						
						while ($rs_contagem= mysql_fetch_object($result_contagem)) {
							//$peso_total_peca= $rs_pesagem->peso_total_roupa;
							$total_pacotes_peca += $rs_contagem->num_pacotes;
							$total_pacotes_peca += $rs_contagem->pacotes_sobra;
							
							//if ($rs_contagem->qtde_pecas_sobra>0) $total_pacotes_peca++;
							
							$total_pecas_aqui= ($rs_contagem->qtde_pacote*$rs_contagem->num_pacotes)+$rs_contagem->qtde_pecas_sobra;
							$total_pecas_peca += $total_pecas_aqui;
						}
						
						$pdf->Cell($largura_tipo_roupa, 0.45, pega_pecas_roupa($rs_pesagem_pecas->id_tipo_roupa) . $peso_informa_extra, 1, 0, "L", $fill);
						$pdf->Cell(2.5, 0.45, $total_pacotes_peca, 1, 0, "C", $fill);
						$pdf->Cell(2.5, 0.45, $total_pecas_peca, 1, $quebra_num_pecas, "C", $fill);
						
						//mostra peso e soma peso geral somente de uma pe�a
						
						if ($paridade==1) $local= ($linhas_pesagem_pecas/2)+1;
						else $local= (($linhas_pesagem_pecas+1)/2);
						
						if ($j==$local) {
							if ($rs_pesagem->peso_total_roupa>=$rs_pesagem_extra->peso_total_roupa) $peso_real_aqui= $rs_pesagem->peso_total_roupa-$rs_pesagem_extra->peso_total_roupa;
							else $peso_real_aqui= $rs_pesagem->peso_total_roupa;
							
							$peso_aqui= fnum($peso_real_aqui) ." kg ";
							$peso_total_peca_geral += $peso_real_aqui;
						}
						else {
							$peso_aqui= "";
						}
						
						if ($mostrar_peso==1) $peso2_aqui= $peso_aqui;
						else $peso2_aqui= "";
						
						if ($tipo_pedido!="2") $pdf->Cell(2.5, 0.45, $peso2_aqui, "LR", 1, "C", $fill);
						
						$total_pacotes_peca_geral += $total_pacotes_peca;
						$total_pecas_peca_geral += $total_pecas_peca;
						
						$j++;
					}
					
				}
				
			}
		}
		
		
		$pdf->SetFillColor(210,210,210);
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		$pdf->Cell($largura_tipo_roupa, 0.5, "", 0, 0, "L", 0);
		$pdf->Cell(2.5, 0.5, $total_pacotes_peca_geral, 1, 0, "C", 1);
		$pdf->Cell(2.5, 0.5, $total_pecas_peca_geral, 1, $quebra_num_pecas, "C", 1);
		
		if ($mostrar_peso==1) $peso2_aqui= fnum($peso_total_peca_geral) ." kg";
		else $peso2_aqui= "";
		
		if ($tipo_pedido!="2") $pdf->Cell(2.5, 0.5, $peso2_aqui, 1, 1, "C", 1);
		
		
		if ($peso_informa_extra_texto!="") {
			$pdf->SetFont('ARIALNARROW', '', 8);
			$pdf->Cell(0, 1, $peso_informa_extra_texto, 0, 1, "R");
		}
		
		
		//------------------------------------------------ /FIM GOMA
		
		$result_total_goma= mysql_query("select sum(op_limpa_pesagem_virtual.peso) as peso_total from op_limpa_pesagem_virtual
											where op_limpa_pesagem_virtual.goma= '1'
											and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
											$str_geral
											");
		
		$rs_total_goma= mysql_fetch_object($result_total_goma);
		$peso_total_goma= $rs_total_goma->peso_total;
		
		if ($peso_total_goma>0) {
			$pdf->LittleLn();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 0.6, "GOMAS:", 0, 1, 'L');
			
			$pdf->Cell(0, 0.2, "", 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$pdf->Cell($largura_tipo_roupa, 0.5, "TIPO DE ROUPA", 1, 0, "L", 1);
			$pdf->Cell(2.5, 0.5, "NUM. PACOTES", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, "NUM. PE�AS", 1, $quebra_num_pecas, "C", 1);
			
			if ($tipo_pedido!="2") $pdf->Cell(2.5, 0.5, "PESO", 1, 1, "C", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			
			$peso_total_peca_geral_goma= 0;
			$total_pacotes_peca_geral_goma= 0;
			$total_pecas_peca_geral_goma= 0;
			
			for ($g=1; $g<3; $g++) {
				
				if ($g==1) {
					$result_pesagem_pecas_goma= mysql_query("select distinct(op_limpa_pesagem_pecas_virtual.id_tipo_roupa)
															from op_limpa_pecas, op_limpa_pesagem_virtual, op_limpa_pesagem_pecas_virtual
															where op_limpa_pesagem_virtual.id_pesagem = op_limpa_pesagem_pecas_virtual.id_pesagem
															and   op_limpa_pesagem_virtual.goma= '1'
															and   op_limpa_pesagem_pecas_virtual.id_tipo_roupa = op_limpa_pecas.id_peca
															and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
															and   op_limpa_pecas.id_grupo = '$g'
															$str_geral
															order by op_limpa_pecas.peca asc
															") or die(mysql_error());
					
					$pdf->SetFillColor(235,235,235);
					
					$j=0;
					while ($rs_pesagem_pecas_goma= mysql_fetch_object($result_pesagem_pecas_goma)) {
						
						//if (($j%2)==0) $fill=0; else $fill= 1;
						$fill= 0;
					
						$result_pesagem_goma= mysql_query("select * from op_limpa_pesagem_virtual, op_limpa_pesagem_pecas_virtual
															where op_limpa_pesagem_virtual.id_pesagem = op_limpa_pesagem_pecas_virtual.id_pesagem
															and   op_limpa_pesagem_virtual.goma= '1'
															and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
															and   op_limpa_pesagem_pecas_virtual.id_tipo_roupa = '". $rs_pesagem_pecas_goma->id_tipo_roupa ."'
															$str_geral
															") or die(mysql_error());
						
						$linhas_pesagem_goma= mysql_num_rows($result_pesagem_goma);
						
						if ($linhas_pesagem_goma>0) {
							$peso_total_peca_goma= 0;
							$total_pacotes_peca_goma= 0;
							$total_pecas_peca_goma= 0;
							
							while ($rs_pesagem_goma= mysql_fetch_object($result_pesagem_goma)) {
								
								$peso_total_peca_goma += $rs_pesagem_goma->peso;
								$total_pacotes_peca_goma += $rs_pesagem_goma->num_pacotes;
								$total_pacotes_peca_goma += $rs_pesagem_goma->pacotes_sobra;
								
								if ($rs_pesagem_goma->qtde_pecas_sobra>0) $total_pacotes_peca_goma++;
								
								$total_pecas_aqui_goma= ($rs_pesagem_goma->qtde_pacote*$rs_pesagem_goma->num_pacotes)+$rs_pesagem_goma->qtde_pecas_sobra;
								$total_pecas_peca_goma += $total_pecas_aqui_goma;
							}
								
							$pdf->Cell($largura_tipo_roupa, 0.45, pega_pecas_roupa($rs_pesagem_pecas_goma->id_tipo_roupa), 1, 0, "L", $fill);
							$pdf->Cell(2.5, 0.45, $total_pacotes_peca_goma, 1, 0, "C", $fill);
							$pdf->Cell(2.5, 0.45, $total_pecas_peca_goma, 1, $quebra_num_pecas, "C", $fill);
							
							if ($mostrar_peso==1) $peso2_aqui= fnum($peso_total_peca_goma) ." kg";
							else $peso2_aqui= "";
							
							if ($tipo_pedido!="2") $pdf->Cell(2.5, 0.45, $peso2_aqui, 1, 1, "C", $fill);
							
							$peso_total_peca_geral_goma += $peso_total_peca_goma;
							$total_pacotes_peca_geral_goma += $total_pacotes_peca_goma;
							$total_pecas_peca_geral_goma += $total_pecas_peca_goma;
							
							$j++;
						}
					}
				}
				elseif ($g==2) {
					
					$result_pesagem_pecas_goma= mysql_query("select distinct(op_limpa_pesagem_pecas_virtual.id_tipo_roupa)
															from op_limpa_pecas, op_limpa_pesagem_virtual, op_limpa_pesagem_pecas_virtual
															where op_limpa_pesagem_virtual.id_pesagem = op_limpa_pesagem_pecas_virtual.id_pesagem
															and   op_limpa_pesagem_virtual.goma= '1'
															and   op_limpa_pesagem_pecas_virtual.id_tipo_roupa = op_limpa_pecas.id_peca
															and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
															and   op_limpa_pecas.id_grupo = '$g'
															$str_geral
															order by op_limpa_pecas.peca asc
															") or die(mysql_error());
					
					$pdf->SetFillColor(235,235,235);
					
					$linhas_pesagem_pecas_goma= mysql_num_rows($result_pesagem_pecas_goma);
					
					//ver se � par ou �mpar
					if (($linhas_pesagem_pecas_goma%2)==0) $paridade_goma=1;
					else $paridade_goma= 0;
					
					$j=1;
					while ($rs_pesagem_pecas_goma= mysql_fetch_object($result_pesagem_pecas_goma)) {
						
						$fill= 0;
						
						$result_pesagem_goma= mysql_query("select sum(op_limpa_pesagem_virtual.peso) as peso_total_roupa
															from op_limpa_pesagem_virtual
															where op_limpa_pesagem_virtual.goma= '1'
															and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
															and   op_limpa_pesagem_virtual.id_grupo = '2'
															$str_geral
															") or die(mysql_error());
						$rs_pesagem_goma= mysql_fetch_object($result_pesagem_goma);
						
						$result_contagem_goma= mysql_query("select *
															from op_limpa_pesagem_virtual, op_limpa_pesagem_pecas_virtual
															where op_limpa_pesagem_virtual.goma= '1'
															and   op_limpa_pesagem_virtual.id_pesagem = op_limpa_pesagem_pecas_virtual.id_pesagem
															and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
															and   op_limpa_pesagem_pecas_virtual.id_tipo_roupa = '". $rs_pesagem_pecas_goma->id_tipo_roupa ."'
															$str_geral
															") or die(mysql_error());
						$linhas_contagem_goma= mysql_num_rows($result_contagem_goma);
						
						if ($linhas_contagem_goma>0) {
							//$peso_total_peca= 0;
							$total_pacotes_peca_goma= 0;
							$total_pecas_peca_goma= 0;
							
							while ($rs_contagem_goma= mysql_fetch_object($result_contagem_goma)) {
								//$peso_total_peca= $rs_pesagem->peso_total_roupa;
								$total_pacotes_peca_goma += $rs_contagem_goma->num_pacotes;
								$total_pacotes_peca_goma += $rs_contagem_goma->pacotes_sobra;
								
								if ($rs_contagem_goma->qtde_pecas_sobra>0) $total_pacotes_peca_goma++;
								
								$total_pecas_aqui_goma= ($rs_contagem_goma->qtde_pacote*$rs_contagem_goma->num_pacotes)+$rs_contagem_goma->qtde_pecas_sobra;
								$total_pecas_peca_goma += $total_pecas_aqui_goma;
							}
								
							$pdf->Cell($largura_tipo_roupa, 0.45, pega_pecas_roupa($rs_pesagem_pecas_goma->id_tipo_roupa), 1, 0, "L", $fill);
							$pdf->Cell(2.5, 0.45, $total_pacotes_peca_goma, 1, 0, "C", $fill);
							$pdf->Cell(2.5, 0.45, $total_pecas_peca_goma, 1, $quebra_num_pecas, "C", $fill);
							
							//mostra peso e soma peso geral somente de uma pe�a
							
							if ($paridade_goma==1) $local_goma= ($linhas_pesagem_pecas_goma/2)+1;
							else $local_goma= (($linhas_pesagem_pecas_goma+1)/2);
							
							if ($j==$local_goma) {
								$peso_aqui_goma= fnum($rs_pesagem_goma->peso_total_roupa) ." kg";
								$peso_total_peca_geral_goma += $rs_pesagem_goma->peso_total_roupa;
							}
							else $peso_aqui_goma= "";
							
							if ($mostrar_peso==1) $peso2_aqui= $peso_aqui_goma;
							else $peso2_aqui= "";
							
							if ($tipo_pedido!="2") $pdf->Cell(2.5, 0.45, $peso2_aqui, "LR", 1, "C", $fill);
							
							$total_pacotes_peca_geral_goma += $total_pacotes_peca_goma;
							$total_pecas_peca_geral_goma += $total_pecas_peca_goma;
							
							$j++;
						}
					}
					
				}
			}
			
			$pdf->SetFillColor(210,210,210);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
			$pdf->Cell($largura_tipo_roupa, 0.5, "", 0, 0, "L", 0);
			$pdf->Cell(2.5, 0.5, $total_pacotes_peca_geral_goma, 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, $total_pecas_peca_geral_goma, 1, $quebra_num_pecas, "C", 1);
			
			if ($mostrar_peso==1) $peso2_aqui= fnum($peso_total_peca_geral_goma) ." kg";
			else $peso2_aqui= "";
			
			if ($tipo_pedido!="2") $pdf->Cell(2.5, 0.5, $peso2_aqui, 1, 1, "C", 1);
		}
		//------------------------------------------------ /FIM GOMA
		
		
		//------------------------------------------------ INICIO ROUPA ALHEIA
		
		$result_total_roupa_alheia= mysql_query("select sum(op_limpa_pesagem_virtual.peso) as peso_total from op_limpa_pesagem_virtual
													where op_limpa_pesagem_virtual.goma= '0'
													and   op_limpa_pesagem_virtual.roupa_alheia = '1'
													and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
													and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
													$str_geral
													");
		
		$rs_total_roupa_alheia= mysql_fetch_object($result_total_roupa_alheia);
		$peso_total_roupa_alheia= $rs_total_roupa_alheia->peso_total;
		
		if ($peso_total_roupa_alheia>0) {
			$pdf->LittleLn();
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 0.6, "ROUPAS DE OUTRAS UNIDADES:", 0, 1, 'L');
			
			$pdf->Cell(0, 0.2, "", 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$pdf->Cell($largura_tipo_roupa, 0.5, "TIPO DE ROUPA", 1, 0, "L", 1);
			$pdf->Cell(2.5, 0.5, "NUM. PACOTES", 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, "NUM. PE�AS", 1, $quebra_num_pecas, "C", 1);
			
			if ($tipo_pedido!="2") $pdf->Cell(2.5, 0.5, "PESO", 1, 1, "C", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 8);
			
			$peso_total_peca_geral_roupa_alheia= 0;
			$total_pacotes_peca_geral_roupa_alheia= 0;
			$total_pecas_peca_geral_roupa_alheia= 0;
			
			for ($g=1; $g<3; $g++) {
				
				if ($g==1) {
					$result_pesagem_pecas_roupa_alheia= mysql_query("select distinct(op_limpa_pesagem_pecas_virtual.id_tipo_roupa) from op_limpa_pecas, op_limpa_pesagem_virtual, op_limpa_pesagem_pecas_virtual
																		where op_limpa_pesagem_virtual.id_pesagem = op_limpa_pesagem_pecas_virtual.id_pesagem
																		and   op_limpa_pesagem_virtual.goma= '0'
																		and   op_limpa_pesagem_virtual.roupa_alheia = '1'
																		and   op_limpa_pesagem_pecas_virtual.id_tipo_roupa = op_limpa_pecas.id_peca
																		and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
																		and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
																		and   op_limpa_pecas.id_grupo = '$g'
																		$str_geral
																		order by op_limpa_pecas.peca asc
																		") or die(mysql_error());
					
					$pdf->SetFillColor(235,235,235);
					
					$j=0;
					while ($rs_pesagem_pecas_roupa_alheia= mysql_fetch_object($result_pesagem_pecas_roupa_alheia)) {
						
						//if (($j%2)==0) $fill=0; else $fill= 1;
						$fill= 0;
					
						$result_pesagem_roupa_alheia= mysql_query("select * from op_limpa_pesagem_virtual, op_limpa_pesagem_pecas_virtual
																	where op_limpa_pesagem_virtual.id_pesagem = op_limpa_pesagem_pecas_virtual.id_pesagem
																	and   op_limpa_pesagem_virtual.goma= '0'
																	and   op_limpa_pesagem_virtual.roupa_alheia = '1'
																	and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
																	and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
																	and   op_limpa_pesagem_pecas_virtual.id_tipo_roupa = '". $rs_pesagem_pecas_roupa_alheia->id_tipo_roupa ."'
																	$str_geral
																	") or die(mysql_error());
						
						$linhas_pesagem_roupa_alheia= mysql_num_rows($result_pesagem_roupa_alheia);
						
						if ($linhas_pesagem_roupa_alheia>0) {
							$peso_total_peca_roupa_alheia= 0;
							$total_pacotes_peca_roupa_alheia= 0;
							$total_pecas_peca_roupa_alheia= 0;
							
							while ($rs_pesagem_roupa_alheia= mysql_fetch_object($result_pesagem_roupa_alheia)) {
								
								$peso_total_peca_roupa_alheia += $rs_pesagem_roupa_alheia->peso;
								$total_pacotes_peca_roupa_alheia += $rs_pesagem_roupa_alheia->num_pacotes;
								$total_pacotes_peca_roupa_alheia += $rs_pesagem_roupa_alheia->pacotes_sobra;
								
								if ($rs_pesagem_roupa_alheia->qtde_pecas_sobra>0) $total_pacotes_peca_roupa_alheia++;
								
								$total_pecas_aqui_roupa_alheia= ($rs_pesagem_roupa_alheia->qtde_pacote*$rs_pesagem_roupa_alheia->num_pacotes)+$rs_pesagem_roupa_alheia->qtde_pecas_sobra;
								$total_pecas_peca_roupa_alheia += $total_pecas_aqui_roupa_alheia;
							}
								
							$pdf->Cell($largura_tipo_roupa, 0.45, pega_pecas_roupa($rs_pesagem_pecas_roupa_alheia->id_tipo_roupa), 1, 0, "L", $fill);
							$pdf->Cell(2.5, 0.45, $total_pacotes_peca_roupa_alheia, 1, 0, "C", $fill);
							$pdf->Cell(2.5, 0.45, $total_pecas_peca_roupa_alheia, 1, $quebra_num_pecas, "C", $fill);
							
							if ($mostrar_peso==1) $peso2_aqui= fnum($peso_total_peca_roupa_alheia) ." kg";
							else $peso2_aqui= fnum($peso_total_peca_roupa_alheia) ." kg";
							
							if ($tipo_pedido!="2") $pdf->Cell(2.5, 0.45, $peso2_aqui, 1, 1, "C", $fill);
							
							$peso_total_peca_geral_roupa_alheia += $peso_total_peca_roupa_alheia;
							$total_pacotes_peca_geral_roupa_alheia += $total_pacotes_peca_roupa_alheia;
							$total_pecas_peca_geral_roupa_alheia += $total_pecas_peca_roupa_alheia;
							
							$j++;
						}
					}
				}
				elseif ($g==2) {
					
					$result_pesagem_pecas_roupa_alheia= mysql_query("select distinct(op_limpa_pesagem_pecas_virtual.id_tipo_roupa)
															from op_limpa_pecas, op_limpa_pesagem_virtual, op_limpa_pesagem_pecas_virtual
															where op_limpa_pesagem_virtual.id_pesagem = op_limpa_pesagem_pecas_virtual.id_pesagem
															and   op_limpa_pesagem_virtual.goma= '0'
															and   op_limpa_pesagem_virtual.roupa_alheia = '1'
															and   op_limpa_pesagem_pecas_virtual.id_tipo_roupa = op_limpa_pecas.id_peca
															and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
															and   op_limpa_pecas.id_grupo = '$g'
															$str_geral
															order by op_limpa_pecas.peca asc
															") or die(mysql_error());
					
					$pdf->SetFillColor(235,235,235);
					
					$linhas_pesagem_pecas_roupa_alheia= mysql_num_rows($result_pesagem_pecas_roupa_alheia);
					
					//ver se � par ou �mpar
					if (($linhas_pesagem_pecas_roupa_alheia%2)==0) $paridade_roupa_alheia=1;
					else $paridade_roupa_alheia= 0;
					
					$j=1;
					while ($rs_pesagem_pecas_roupa_alheia= mysql_fetch_object($result_pesagem_pecas_roupa_alheia)) {
						
						$fill= 0;
						
						$result_pesagem_roupa_alheia= mysql_query("select sum(op_limpa_pesagem_virtual.peso) as peso_total_roupa
															from op_limpa_pesagem_virtual
															where op_limpa_pesagem_virtual.goma= '0'
															and   op_limpa_pesagem_virtual.roupa_alheia = '1'
															and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
															and   op_limpa_pesagem_virtual.id_grupo = '2'
															$str_geral
															") or die(mysql_error());
						$rs_pesagem_roupa_alheia= mysql_fetch_object($result_pesagem_roupa_alheia);
						
						$result_contagem_roupa_alheia= mysql_query("select *
															from op_limpa_pesagem_virtual, op_limpa_pesagem_pecas_virtual
															where op_limpa_pesagem_virtual.goma= '0'
															and   op_limpa_pesagem_virtual.roupa_alheia = '1'
															and   op_limpa_pesagem_virtual.id_pesagem = op_limpa_pesagem_pecas_virtual.id_pesagem
															and   op_limpa_pesagem_virtual.id_empresa = '". $_SESSION["id_empresa"] ."'
															and   op_limpa_pesagem_virtual.id_cliente = '". $id_cliente ."'
															and   op_limpa_pesagem_pecas_virtual.id_tipo_roupa = '". $rs_pesagem_pecas_roupa_alheia->id_tipo_roupa ."'
															$str_geral
															") or die(mysql_error());
						$linhas_contagem_roupa_alheia= mysql_num_rows($result_contagem_roupa_alheia);
						
						if ($linhas_contagem_roupa_alheia>0) {
							//$peso_total_peca= 0;
							$total_pacotes_peca_roupa_alheia= 0;
							$total_pecas_peca_roupa_alheia= 0;
							
							while ($rs_contagem_roupa_alheia= mysql_fetch_object($result_contagem_roupa_alheia)) {
								//$peso_total_peca= $rs_pesagem->peso_total_roupa;
								$total_pacotes_peca_roupa_alheia += $rs_contagem_roupa_alheia->num_pacotes;
								$total_pacotes_peca_roupa_alheia += $rs_contagem_roupa_alheia->pacotes_sobra;
								
								if ($rs_contagem_roupa_alheia->qtde_pecas_sobra>0) $total_pacotes_peca_roupa_alheia++;
								
								$total_pecas_aqui_roupa_alheia= ($rs_contagem_roupa_alheia->qtde_pacote*$rs_contagem_roupa_alheia->num_pacotes)+$rs_contagem_roupa_alheia->qtde_pecas_sobra;
								$total_pecas_peca_roupa_alheia += $total_pecas_aqui_roupa_alheia;
							}
								
							$pdf->Cell($largura_tipo_roupa, 0.45, pega_pecas_roupa($rs_pesagem_pecas_roupa_alheia->id_tipo_roupa), 1, 0, "L", $fill);
							$pdf->Cell(2.5, 0.45, $total_pacotes_peca_roupa_alheia, 1, 0, "C", $fill);
							$pdf->Cell(2.5, 0.45, $total_pecas_peca_roupa_alheia, 1, $quebra_num_pecas, "C", $fill);
							
							//mostra peso e soma peso geral somente de uma pe�a
							
							if ($paridade_roupa_alheia==1) $local_roupa_alheia= ($linhas_pesagem_pecas_roupa_alheia/2)+1;
							else $local_roupa_alheia= (($linhas_pesagem_pecas_roupa_alheia+1)/2);
							
							if ($j==$local_roupa_alheia) {
								$peso_aqui_roupa_alheia= fnum($rs_pesagem_roupa_alheia->peso_total_roupa) ." kg";
								$peso_total_peca_geral_roupa_alheia += $rs_pesagem_roupa_alheia->peso_total_roupa;
							}
							else $peso_aqui_roupa_alheia= "";
							
							if ($mostrar_peso==1) $peso2_aqui= $peso_aqui_roupa_alheia;
							else $peso2_aqui= "";
							
							if ($tipo_pedido!="2") $pdf->Cell(2.5, 0.45, $peso2_aqui, "LR", 1, "C", $fill);
							
							$total_pacotes_peca_geral_roupa_alheia += $total_pacotes_peca_roupa_alheia;
							$total_pecas_peca_geral_roupa_alheia += $total_pecas_peca_roupa_alheia;
							
							$j++;
						}
					}
					
				}
			}
			
			$pdf->SetFillColor(210,210,210);
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			
			$pdf->Cell($largura_tipo_roupa, 0.5, "", 0, 0, "L", 0);
			$pdf->Cell(2.5, 0.5, $total_pacotes_peca_geral_roupa_alheia, 1, 0, "C", 1);
			$pdf->Cell(2.5, 0.5, $total_pecas_peca_geral_roupa_alheia, 1, $quebra_num_pecas, "C", 1);
			
			if ($mostrar_peso==1) $peso2_aqui= fnum($peso_total_peca_geral_roupa_alheia) ." kg";
			else $peso2_aqui= "";
			
			if ($tipo_pedido!="2") $pdf->Cell(2.5, 0.5, $peso2_aqui, 1, 1, "C", 1);
		}
		//------------------------------------------------ /FIM ROUPA ALHEIA
		
		//se o peso final for calculado pela suja empresa e n�o for suja cliente (coleta)
		if ( ($tipo==2) && ($rs_contrato->basear_peso!="2") ) {
		//else {
			
			/*$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
			$pdf->Cell(4, 0.5, "PESO", 1, 0, "C", 1);
			$pdf->Cell(13, 0.5, "DESCRI��O DO SERVI�O", 1, 1, "C", 1);
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			*/
			
			/*if ($_SESSION[id_usuario]==13) {
				echo "select * from op_suja_remessas
											where data_remessa > '". $data_inicio_relatorio ."'
											and   data_remessa < '". $data_fim_relatorio ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											";
				
				die();
			}*/
			
			$result_remessa= mysql_query("select * from op_suja_remessas
											where data_remessa >= '". $data_inicio_relatorio ."'
											and   data_remessa < '". $data_fim_relatorio ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											");
			
			$peso_total= 0;
		
			while ($rs_remessa= mysql_fetch_object($result_remessa)) {
				$result_pesagem= mysql_query("select sum(peso) as total from ". $tabela ."
												where id_remessa = '". $rs_remessa->id_remessa ."'
												and   id_cliente = '". $id_cliente ."'
												");
				
				$rs_pesagem= mysql_fetch_object($result_pesagem);
				
				$peso_total+= $rs_pesagem->total;
			}
		
			//if ($peso_total==0) die("Nenhuma pesagem de roupa suja encontrada para esta nota. (2)");
			
		}
		
		if ($obs!="") {
			$pdf->Cell(0, 0.3, "", 0, 1);
			
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 10);
			$pdf->Cell(0, 0.6, "OBSERVA��ES:", 0, 1, 'L');
			
			$pdf->Cell(0, 0.2, "", 0, 1);
			
			$pdf->SetFont('ARIALNARROW', '', 9);
			$pdf->MultiCell(0, 0.2, "", "TRL", "L", 0);
			$pdf->MultiCell(0, 0.4, html_entity_decode($obs), "RL", "L", 0);
			$pdf->MultiCell(0, 0.2, "", "BRL", "L", 0);
			
			$pdf->Ln();
		}
		
		$pdf->SetFillColor(210,210,210);
		
		$pdf->Ln();
		
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 9);
		
		if ($rs_contrato->basear_peso=="2") $tit_peso= "PESO COLETADO";
		elseif ($tipo=="2") $tit_peso= "PESO DE ROUPA SUJA";
		else $tit_peso= "PESO";
		
		$pdf->Cell(8.5, 0.6, $tit_peso, 1, 0, "C", 1);
		
		$pdf->Cell(8.5, 0.6, "PROTOCOLO DE ENTREGA", 1, 1, "C", 1);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		
		//if (strpos($data_de_entrega, "-")===false) $data_ex
		
		$pdf->Cell(8.5, 0.6, "", "TRL", 0, "L", 0);
		$pdf->Cell(8.5, 0.6, "DATA: ". $data_de_entrega ." | ENTREGA ". $tit_extra ." N� ". $entrega, 1, 1, "L", 0);
		
		$peso_asterisco= "";
		
		//if ($_SESSION[id_usuario]==13) echo 
		
		//se o peso da nota de entrega for o peso cliente
		if ($rs_contrato->basear_peso=="2") {
			if ($peso_coleta_temp>0) {
				$peso_total_aqui= fnum($peso_coleta_temp) ."kg";
				$peso_total= $peso_coleta_temp;
				$peso_total_somado= $peso_coleta_temp;
			}
			else {
				if ($peso_coleta_pnr==1) $peso_asterisco="*";
				
				$peso_total_somado= $peso_total+$peso_total_peca_geral_goma+$peso_total_peca_geral_roupa_alheia;
			}
		}
		else $peso_total_somado= $peso_total+$peso_total_peca_geral_goma+$peso_total_peca_geral_roupa_alheia;
		
		if ( ($mostrar_peso==1) || ($tipo==2) || ($rs_contrato->basear_peso=="2") ) $peso_total_aqui= fnum($peso_total_somado) ." kg";
		else $peso_total_aqui= "";
		
		
		$pdf->SetFont('ARIALNARROW', '', 16);
		$pdf->Cell(8.5, 1.2, $peso_total_aqui . $peso_asterisco, "RL", 0, "C", 0);
		
		$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
		$pdf->Cell(4.25, 0.6, "CHEGADA:", 1, 0, "L", 0);
		$pdf->Cell(4.25, 0.6, "PARTIDA:", 1, 1, "L", 0);
		
		$pdf->Cell(8.5, 0, "", "RL", 0, "L", 0);
		$pdf->Cell(8.5, 0.6, "ASS. CLIENTE:", 1, 1, "L", 0);
		
		$pdf->Cell(8.5, 0.6, "", "BRL", 0, "L", 0);
		$pdf->Cell(8.5, 0.6, "ASS. MOTORISTA:", 1, 1, "L", 0);
		
		if ($id_contrato==2) {
			$pdf->LittleLn();
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$pdf->Cell(0, 0.6, "A presente nota deve ser conferida e reconhecida pelo transportador e recebedor quanto a sua quantidade e volume entregado.", 0, 1, "C");
			
			$pdf->LittleLn();
			$pdf->SetFont('ARIAL_N_NEGRITO', '', 8);
			
			$pdf->Cell(0, 0.6, "OBS CLIENTE:", 1, 1, "L", 1);
			$pdf->Cell(0, 2, "", 1, 1, "L", 0);
			
			$pdf->SetXY(10.5, $pdf->GetY()-0.6);
			$pdf->Cell(8, 0.5, "CONFERIDO POR:", 'T', 1, "C", 0);
		}
		
	}
	
	/*if ($_SESSION[id_usuario]=="13") {
		echo "update op_pedidos
									  	set peso_total = '". $peso_total_somado ."',
										obs= '". $obs ."',
										id_percurso= '". $id_percurso ."',
										denominacao= '". $denominacao ."'
										where id_cliente = '". $id_cliente ."'
										and   id_contrato = '$id_contrato'
										and   data_pedido = '". formata_data($data_pedido_fecha) ."'
										and   entrega = '". $rs_percurso->num_percurso_tipo ."'
										and   extra = '$extra'
										";
										die();
	}*/
	
	//atualiza na pr�pria nota de entrega o peso total
	$result_peso_entrega= mysql_query("update op_pedidos
									  	set peso_total = '". $peso_total_somado ."',
										obs= '". $obs ."',
										id_percurso= '". $id_percurso ."',
										denominacao= '". $denominacao ."'
										where id_cliente = '". $id_cliente ."'
										and   id_contrato = '$id_contrato'
										and   data_pedido = '". formata_data($data_pedido_fecha) ."'
										and   entrega = '". $rs_percurso->num_percurso_tipo ."'
										and   extra = '$extra'
										") or die(mysql_error());
	
	if (!$result_peso_entrega) $var++;
	
	//finaliza_transacao($var);
	
	$result_tt3= mysql_query("DROP TABLE IF EXISTS op_limpa_pesagem_virtual ") or die(mysql_error());
	$result_tt4= mysql_query("DROP TABLE IF EXISTS op_limpa_pesagem_pecas_virtual ") or die(mysql_error());
	
	$result_tt3= mysql_query("DROP TABLE IF EXISTS tr_percursos_virtual ") or die(mysql_error());
	$result_tt4= mysql_query("DROP TABLE IF EXISTS tr_percursos_clientes_virtual ") or die(mysql_error());
	$result_tt3= mysql_query("DROP TABLE IF EXISTS tr_percursos_passos_virtual ") or die(mysql_error());
	

	@$pdf->AliasNbPages();
	
	@$pdf->Output("entrega_relatorio_". date("d-m-Y_H:i:s") .".pdf", "I");
	
	$_SESSION["id_empresa_atendente2"]="";
}
?>