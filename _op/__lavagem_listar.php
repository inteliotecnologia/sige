<?
if (pode_algum("ps", $_SESSION["permissao"])) {
	
	if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
	if ($_GET["periodo"]!="") $periodo= $_GET["periodo"];
	if ($periodo!="") $str.= " and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". $periodo ."' ";
	
	if ($_POST["data"]!="") $data= $_POST["data"];
	if ($_GET["data"]!="") $data= $_GET["data"];
	if ($data!="") $str.= " and   op_suja_lavagem.data_lavagem= '". formata_data_hifen($data) ."' ";
	
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($id_cliente!="") $str.= " and   op_suja_lavagem.id_lavagem IN 
									(
									select op_suja_lavagem_cestos.id_lavagem from op_suja_lavagem_cestos
									where op_suja_lavagem_cestos.id_cliente= '". $id_cliente ."'
									)
									";
	
	if ($_POST["id_turno"]!="") $id_turno= $_POST["id_turno"];
	if ($_GET["id_turno"]!="") $id_turno= $_GET["id_turno"];
	if ($id_turno!="") $str.= " and   id_turno= '". $id_turno ."' ";
	
	if ($_POST["id_equipamento"]!="") $id_equipamento= $_POST["id_equipamento"];
	if ($_GET["id_equipamento"]!="") $id_equipamento= $_GET["id_equipamento"];
	if ($id_equipamento!="") $str.= " and   id_equipamento= '". $id_equipamento ."' ";
	
	if ($_POST["id_processo"]!="") $id_processo= $_POST["id_processo"];
	if ($_GET["id_processo"]!="") $id_processo= $_GET["id_processo"];
	if ($id_processo!="") $str.= " and   id_processo= '". $id_processo ."' ";
	
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($id_funcionario!="") $str.= " and   id_funcionario= '". $id_funcionario ."' ";
	
	$result= mysql_query("select * from op_suja_lavagem, op_suja_remessas
							where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
							". $str ."
							order by op_suja_remessas.data_remessa desc, op_suja_lavagem.data_lavagem desc, op_suja_lavagem.hora_lavagem desc, op_suja_lavagem.id_lavagem desc
							");
	
	$num= 75;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;
	
	$result= mysql_query("select * from op_suja_lavagem, op_suja_remessas
							where op_suja_lavagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   op_suja_lavagem.id_remessa = op_suja_remessas.id_remessa
							". $str ."
							order by /* op_suja_remessas.data_remessa desc, op_suja_remessas.hora_chegada desc, */ op_suja_lavagem.data_lavagem desc, op_suja_lavagem.hora_lavagem desc, op_suja_lavagem.id_lavagem desc
							limit $inicio, $num
							");
	
	$periodo2= explode("/", $periodo);
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Área suja - Lavagens</h2>

<ul class="recuo1">
	<li><a href="./?pagina=op/lavagem&amp;acao=i">inserir</a></li>
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
			echo " <a href=\"./?pagina=op/lavagem_listar&amp;data=". $data ."&amp;periodo=". $periodo ."&amp;id_cliente=". $id_cliente ."&amp;id_turno=". $id_turno ."&amp;id_equipamento=". $id_equipamento ."&amp;id_processo=". $id_processo ."&amp;id_funcionario=". $id_funcionario ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
	*/
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/lavagem_listar&amp;data=<?=$data;?>&amp;periodo=<?=$periodo;?>&amp;id_cliente=<?=$id_cliente;?>&amp;id_turno=<?=$id_turno;?>&amp;id_equipamento=<?=$id_equipamento;?>&amp;id_processo=<?=$id_processo;?>&amp;id_funcionario=<?=$id_funcionario;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<table cellspacing="0" width="100%">
	<tr>
		<th width="5%">Cód.</th>
		<th width="13%" align="left">Remessa</th>
	  <th width="27%" align="left">Cliente</th>
		<th width="16%">Data da lavagem</th>
	    <th width="14%">M&aacute;quina/processo</th>
	    <th width="13%">Peso total</th>
      <th width="12%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_lavagem; ?></td>
		<td><?= desformata_data(pega_dado_remessa("data_remessa", $rs->id_remessa)) ." nº <strong>". pega_dado_remessa("num_remessa", $rs->id_remessa) ."</strong>"; ?></td>
		<td>
		<?
		$result_cestos= mysql_query("select * from op_suja_lavagem_cestos
										where id_lavagem = '". $rs->id_lavagem ."'
										order by id_cesto asc
										");
		$linhas_cestos= mysql_num_rows($result_cestos);
		
		$c=1;
		while ($rs_cestos= mysql_fetch_object($result_cestos)) {
			
			if ($rs_cestos->id_cliente==0) echo "Relave";
			else echo pega_sigla_pessoa($rs_cestos->id_cliente);
			
			if ($linhas_cestos!=$c) echo " / ";
			
			$c++;
		}
		?>
        
        <?
		$result_pecas= mysql_query("select distinct(id_tipo_roupa)
										from op_suja_lavagem_pecas
										where id_lavagem = '". $rs->id_lavagem ."'
										");
		$linhas_pecas= mysql_num_rows($result_pecas);
		
		if ($linhas_pecas>0) echo "<span class='vermelho menor'><strong>";
		
		$c=1;
		while ($rs_pecas= mysql_fetch_object($result_pecas)) {
			
			echo pega_pecas_roupa($rs_pecas->id_tipo_roupa);
			
			if ($linhas_cestos!=$c) echo " / ";
			
			$c++;
		}
		
		if ($linhas_pecas>0) echo "</strong></span>";
		
        //if ($rs->id_cliente=="0") echo "Diversos"; else echo pega_pessoa($rs->id_cliente);
		?>
        </td>
		<td align="center"><?= desformata_data($rs->data_lavagem) ." ". $rs->hora_lavagem; ?></td>
		<td align="center"><?= pega_equipamento($rs->id_equipamento) ."/". pega_processo($rs->id_processo); ?></td>
		<td align="center"><?= fnum($rs->peso_total); ?> kg</td>
		<td align="center">
			<? if ($_SESSION["id_departamento_sessao"]!="") { ?>
            <button class="tamanho50 espaco_dir" onclick="window.top.location.href='./?pagina=op/lavagem&amp;acao=e&amp;id_lavagem=<?= $rs->id_lavagem; ?>';" id="link_edita<?=$i;?>">edita</button>
            <button class="tamanho50" onclick="var confirma= confirm('Tem certeza que deseja excluir?'); if (confirma) ajaxLink('conteudo', 'lavagemExcluir&amp;id_lavagem=<?= $rs->id_lavagem; ?>');" id="link_exclui<?=$i;?>">exclui</button>
            <? } else { ?>
            <a href="./?pagina=op/lavagem&amp;acao=e&amp;id_lavagem=<?= $rs->id_lavagem; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('conteudo', 'lavagemExcluir&amp;id_lavagem=<?= $rs->id_lavagem; ?>');" onclick="return confirm('Tem certeza que deseja excluir esta lavagem?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? } ?>
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
			echo " <a href=\"./?pagina=op/lavagem_listar&amp;data=". $data ."&amp;periodo=". $periodo ."&amp;id_cliente=". $id_cliente ."&amp;id_turno=". $id_turno ."&amp;id_equipamento=". $id_equipamento ."&amp;id_processo=". $id_processo ."&amp;id_funcionario=". $id_funcionario ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
	*/
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/lavagem_listar&amp;data=<?=$data;?>&amp;periodo=<?=$periodo;?>&amp;id_cliente=<?=$id_cliente;?>&amp;id_turno=<?=$id_turno;?>&amp;id_equipamento=<?=$id_equipamento;?>&amp;id_processo=<?=$id_processo;?>&amp;id_funcionario=<?=$id_funcionario;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<script type="text/javascript">
	daFoco("link_edita0");
</script>

<? } ?>