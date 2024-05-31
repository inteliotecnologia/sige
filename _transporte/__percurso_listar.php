<?
if (pode("ey", $_SESSION["permissao"])) {
	
	if ($_POST["id_veiculo"]!="") $id_veiculo= $_POST["id_veiculo"];
	if ($_GET["id_veiculo"]!="") $id_veiculo= $_GET["id_veiculo"];
	if ($id_veiculo!="") $str .= " and   tr_percursos.id_veiculo = '". $id_veiculo ."' ";
	
	if ($_POST["tipo"]!="") $tipo= $_POST["tipo"];
	if ($_GET["tipo"]!="") $tipo= $_GET["tipo"];
	if ($tipo!="") {
		//if ($tipo==1) $str .= " and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4') ";
		//if ($tipo==2) $str .= " and   (tr_percursos.tipo = '2' or tr_percursos.tipo = '5') ";
		
		$str .= " and   tr_percursos.tipo = '". $tipo ."' ";
	}
	
	if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
	if ($_GET["periodo"]!="") $periodo= $_GET["periodo"];
	if ($periodo!="") $str .= " and   DATE_FORMAT(tr_percursos_passos.data_percurso, '%m/%Y') = '". $_POST["periodo"] ."' ";
	
	if ($_POST["data_percurso"]!="") $data_percurso= $_POST["data_percurso"];
	if ($_GET["data_percurso"]!="") $data_percurso= $_GET["data_percurso"];
	if ($data_percurso!="") $str .= " and   tr_percursos_passos.data_percurso = '". formata_data($_POST["data_percurso"]) ."' ";
	
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($id_cliente!="") $str .= " and   tr_percursos.id_percurso IN
									(
									 select tr_percursos_clientes.id_percurso from tr_percursos_clientes, tr_percursos
									 where  tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
									 and    tr_percursos_clientes.id_cliente = '". $id_cliente ."'
									)
									";
							
	$result= mysql_query("select * from  tr_percursos, tr_percursos_passos
							where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
							and   tr_percursos_passos.passo = '1'
							$str
							order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
							") or die(mysql_error());
	
	$num= 40;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result= mysql_query("select * from  tr_percursos, tr_percursos_passos
							where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
							and   tr_percursos_passos.passo = '1'
							$str
							order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
							limit $inicio, $num
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Percursos</h2>

<ul class="recuo1">
	<li class="flutuar_esquerda tamanho80"><a href="./?pagina=transporte/percurso&amp;acao=i">inserir</a></li>
    <li class="flutuar_esquerda tamanho130"><a href="./?pagina=transporte/percurso_listar&amp;tipo=1">listar coleta</a></li>
    <li class="flutuar_esquerda tamanho130"><a href="./?pagina=transporte/percurso_listar&amp;tipo=2">listar entrega</a></li>
    <li class="flutuar_esquerda tamanho130"><a href="./?pagina=transporte/percurso_listar">listar todos</a></li>
    <li><a href="./?pagina=transporte/percurso_busca">buscar</a></li>
</ul>
<br />

<?
if ($num_paginas > 1) {
	/*echo "<br /><strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link = $i + 1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=transporte/percurso_listar&amp;id_veiculo=". $id_veiculo ."&amp;tipo=". $tipo ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
	*/
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=transporte/percurso_listar&amp;id_veiculo=<?=$id_veiculo;?>&amp;tipo=<?=$tipo;?>&amp;id_cliente=<?=$id_cliente;?>&amp;data_percurso=<?=$data_percurso;?>&amp;periodo=<?=$periodo;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>


<table cellspacing="0" cellpadding="2" width="100%" id="tabela">
	<tr>
	  <th width="9%" align="left">ID/Data/hora</th>
	  <th width="19%" align="left">Veículo</th>
	  <th width="8%">Tipo</th>
      <th width="25%" align="left">Clientes</th>
      <th width="7%">Situa&ccedil;&atilde;o</th>
      <th width="12%" class="unsortable">Registros</th>
      <th width="10%" class="unsortable">Lan&ccedil;ado por</th>
	  <th width="10%" class="unsortable">Ações</th>
  </tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		$data_percurso2[$i]= $rs->data_percurso;
		
		if ( ($i>0) && ($data_percurso2[$i]!=$data_percurso2[$i-1]) ) {
		?>
		<tr>
			<th colspan="9">&nbsp;</th>
		</tr>
		<tr>
		  <th align="left">ID/Data/hora</th>
		  <th align="left">Veículo</th>
		  <th>Tipo</th>
	      <th align="left">Clientes</th>
	      <th>Situa&ccedil;&atilde;o</th>
	      <th class="unsortable">Registros</th>
	      <th class="unsortable">Lan&ccedil;ado por</th>
		  <th class="unsortable">Ações</th>
	  </tr>
		
		<?
		}
	?>
	<tr id="linha_<?=$i;?>" class="menor <?= $classe; ?> corzinha">
	  <td valign="top"><?= "<b>". fnumi($rs->id_percurso) ."</b><br /><br />". desformata_data($rs->data_percurso) ." ". substr($rs->hora_percurso, 0, 5); ?></td>
		<td valign="top"><?= pega_veiculo($rs->id_veiculo); ?> <br /><br /> <b><?= primeira_palavra(pega_funcionario($rs->id_motorista)); ?></b></td>
		<td valign="top" align="center">
			<?
			$tipo_percurso= pega_coleta_entrega($rs->tipo);
			echo $tipo_percurso;
			?>
        </td>
        <td valign="top">
        	<?
        	$ontem= soma_data($rs->data_percurso, -1, 0, 0);
        	$hoje= soma_data($rs->data_percurso, 0, 0, 0);
        	
			$mostrar_dados_pesagem=1;
			$mostrar_dados_pesagem_count=1;
			
			$result_clientes= mysql_query("select * from pessoas, pessoas_tipos, tr_percursos_clientes
											where pessoas.id_pessoa = pessoas_tipos.id_pessoa
											and   pessoas_tipos.tipo_pessoa = 'c'
											and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   pessoas.status_pessoa = '1'
											and   pessoas.id_cliente_tipo = '1'
											and   pessoas.id_pessoa = tr_percursos_clientes.id_cliente
											and   tr_percursos_clientes.id_percurso = '". $rs->id_percurso ."'
											order by  pessoas.nome_rz asc
											") or die(mysql_error());
			
			$linhas_clientes= mysql_num_rows($result_clientes);
			
			$k=1;
			while ($rs_clientes= mysql_fetch_object($result_clientes)) {
			
				$str_link= "";
				
				$basear_nota_data= pega_basear_nota_data($rs_clientes->id_pessoa);
				
				if ($basear_nota_data==1) {
					$data_vale= $ontem;
					$data_tipo="c";
				}
				else {
					$data_vale= $hoje;
					$data_tipo="e";
				}
				
				if ( ($rs->tipo==2) || ($rs->tipo==5) ) $str_link= " <a target=\"_blank\" href=\"index2.php?pagina=op/entrega_relatorio5&amp;id_cliente=". $rs_clientes->id_pessoa ."&amp;data=". desformata_data($data_vale) ."&amp;id_percurso=". $rs->id_percurso ."&amp;denominacao=&amp;data_tipo=". $data_tipo ."&amp;obs=&amp;origem=2\" onmouseover=\"Tip('Gerar nota de entrega<br /> para ". pega_coleta_entrega($rs->tipo) ." nº ". $rs_clientes->num_percurso_tipo .".');\">". $rs_clientes->sigla ."</a> ";
				else $str_link= " ". $rs_clientes->sigla ." ";
				
				echo "<span class='item_roupa'>$str_link</span>";
				
				//if ($k!=$linhas_clientes) echo ", ";
				
				switch($rs->tipo) {
					case 1:
					case 4:
						$balanca= $rs_clientes->balanca_coleta;
						break;
					case 2:
					case 5:
						$balanca= $rs_clientes->balanca_entrega;
						break;
					default: $balanca= 0; break;
				}
				
				if (!$balanca) $mostrar_dados_pesagem_count++;
				
				$k++;
			}
			
			if ($mostrar_dados_pesagem_count==$k) $mostrar_dados_pesagem=0;
			?>
            
            <?
			$result_remessa= mysql_query("select * from op_suja_remessas
										 	where id_percurso = '". $rs->id_percurso ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											");
			$linhas_remessa= mysql_num_rows($result_remessa);
			
			if ($linhas_remessa>0) {
				$rs_remessa= mysql_fetch_object($result_remessa);
	            
				echo "<br /><br /> <strong>REMESSA ". desformata_data($rs_remessa->data_remessa) ." nº ". $rs_remessa->num_remessa ."</strong>";
			}
            ?>
        </td>
        <td valign="top" align="center">
		<?
		/*$result_passo= mysql_query("select * from tr_percursos_passos
								   	where id_percurso= '". $rs->id_percurso ."' 
									order by passo desc limit 1
									");
		$rs_passo= mysql_fetch_object($result_passo);
		
        if ($rs_passo->passo==3) echo "Concluído";
		else echo "Em rota";*/
		
		echo pega_passo_percurso_resumido($rs->id_situacao_atual);
		?>
        </td>
        <td valign="top" align="center">
        	
            <?
            
			//coleta ou coleta extra
			if (($rs->tipo==1) || ($rs->tipo==4)) $str_balanca= " and   pessoas.balanca_coleta = '1' ";
			elseif (($rs->tipo==2) || ($rs->tipo==5)) $str_balanca= " and   pessoas.balanca_entrega = '1' ";
			
			//se for entrega ou coleta
			if ($rs->tipo!=3) {
				
				$result_teste_pesos= mysql_query("select * from tr_percursos_passos, pessoas
													where tr_percursos_passos.id_cliente = pessoas.id_pessoa
													$str_balanca
													and   tr_percursos_passos.id_percurso = '". $rs->id_percurso ."'
													and   (
														   (tr_percursos_passos.peso is not NULL and   tr_percursos_passos.peso <> '0')
														   or
														   (tr_percursos_passos.pnr is not NULL and tr_percursos_passos.pnr <> '0')
														  )
													and   tr_percursos_passos.passo = '2'
													") or die(mysql_error());
				$linhas_teste_pesos= mysql_num_rows($result_teste_pesos);
				
				$result_teste_hora= mysql_query("select * from tr_percursos_passos
													where id_percurso = '". $rs->id_percurso ."'
													and   hora_percurso <> '00:00:00'
													and   passo = '3'
													");
				$linhas_teste_hora= mysql_num_rows($result_teste_hora);
				
				//mostrar o link com indicação para registrar pesos e horários
				if ($mostrar_dados_pesagem) {
					if ($linhas_teste_pesos==0) $situacao_percurso= "<span class=\"vermelho\">PESOS</span>";
					else $situacao_percurso= "<span class=\"verde\">PESOS</span>";
					
					if ($linhas_teste_hora==0) $situacao_percurso .= " | <span class=\"vermelho\">HORÁRIO</span>";
					else $situacao_percurso .= " | <span class=\"verde\">HORÁRIO</span>";
				}
				//mostra somente link com horários, pois os clientes atuais não possuem balança
				else {
					if ($linhas_teste_hora==0) $situacao_percurso= "<span class=\"vermelho\">HORÁRIO</span>";
					else $situacao_percurso= "<span class=\"verde\">HORÁRIO</span>";
				}
            }
			else {
				$result_teste= mysql_query("select * from tr_percursos_passos
											where id_percurso = '". $rs->id_percurso ."'
											and   passo = '3'
											");
				$linhas_teste= mysql_num_rows($result_teste);
				
				if ($linhas_teste==0) $situacao_percurso= "<span class=\"vermelho\">HORÁRIOS</span>";
				else $situacao_percurso= "<span class=\"verde\">HORÁRIOS</span>";
			}
			?>
            
            <a href="./?pagina=transporte/percurso_dados&amp;id_percurso=<?= $rs->id_percurso; ?>"><?= $situacao_percurso; ?></a>
        </td>
        <td valign="top" align="center"><?= primeira_palavra(pega_nome_pelo_id_usuario($rs->id_usuario)); ?></td>
        <td valign="top" align="center">
			<a href="./?pagina=transporte/percurso&amp;acao=e&amp;id_percurso=<?= $rs->id_percurso; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
            |
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'transpPercursoExcluir&amp;id_percurso=<?= $rs->id_percurso; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Excluir" /></a>
        </td>
	</tr>
	<? $i++; } ?>
</table>
<br /><br />

<?
if ($num_paginas > 1) {
	/*echo "<br /><strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link = $i + 1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=transporte/percurso_listar&amp;id_veiculo=". $id_veiculo ."&amp;tipo=". $tipo ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
	*/
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=transporte/percurso_listar&amp;id_veiculo=<?=$id_veiculo;?>&amp;tipo=<?=$tipo;?>&amp;id_cliente=<?=$id_cliente;?>&amp;data_percurso=<?=$data_percurso;?>&amp;periodo=<?=$periodo;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<? } ?>