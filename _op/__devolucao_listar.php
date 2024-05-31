<?
if (pode_algum("psl", $_SESSION["permissao"])) {
	
	if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
	if ($_GET["periodo"]!="") $periodo= $_GET["periodo"];
	if ($periodo!="") $str.= " and   DATE_FORMAT(op_suja_remessas.data_remessa, '%m/%Y') = '". $periodo ."' ";
	
	if ($_POST["data"]!="") $data2= $_POST["data"];
	if ($_GET["data"]!="") $data2= $_GET["data"];
	if ($data2!="") $str .= " and   op_suja_remessas.data_remessa = '". formata_data_hifen($data2) ."' ";
	
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($id_cliente!="") $str .= " and   id_cliente= '". $id_cliente ."' ";
	
	$result= mysql_query("select * from op_suja_devolucao, op_suja_remessas
							where op_suja_devolucao.id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   op_suja_devolucao.id_remessa = op_suja_remessas.id_remessa
							". $str ."
							order by op_suja_remessas.data_remessa desc, op_suja_devolucao.data_devolucao desc,
							op_suja_devolucao.hora_devolucao desc, op_suja_devolucao.id_devolucao desc
							");
	
	$num= 50;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result= mysql_query("select * from op_suja_devolucao, op_suja_remessas
							where op_suja_devolucao.id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   op_suja_devolucao.id_remessa = op_suja_remessas.id_remessa
							". $str ."
							order by op_suja_remessas.data_remessa desc, op_suja_devolucao.data_devolucao desc,
							op_suja_devolucao.hora_devolucao desc, op_suja_devolucao.id_devolucao desc
							limit $inicio, $num
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Área suja - Devoluções de resíduos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=op/devolucao&amp;acao=i">inserir</a></li>
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
			echo " <a href=\"./?pagina=op/devolucao_listar&amp;data=". $data2 ."&amp;periodo=". $periodo ."&amp;id_cliente=". $id_cliente ."&amp;id_turno=". $id_turno ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
	*/
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/devolucao_listar&amp;data=<?=$data2;?>&amp;periodo=<?=$periodo;?>&amp;id_cliente=<?=$id_cliente;?>&amp;id_turno=<?=$id_turno;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<table cellspacing="0" width="100%">
	<tr>
		<th width="5%">Cód.</th>
		<th width="12%" align="left">Remessa</th>
	  <th width="23%" align="left">Cliente</th>
		<th width="17%">Data da devolu&ccedil;&atilde;o</th>
        <th width="13%">Peso total</th>
        <th width="13%">Pacotes</th>
      <th width="17%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_devolucao; ?></td>
		<td><?= desformata_data(pega_dado_remessa("data_remessa", $rs->id_remessa)) ." nº <strong>". pega_dado_remessa("num_remessa", $rs->id_remessa) ."</strong>"; ?></td>
		<td><?= pega_pessoa($rs->id_cliente); ?></td>
		<td align="center"><?= desformata_data($rs->data_devolucao) ." ". $rs->hora_devolucao; ?></td>
		<td align="center"><?= fnum($rs->peso); ?> kg</td>
		<td align="center"><?= $rs->pacotes; ?></td>
		<td align="center">
			<? if ($_SESSION["id_departamento_sessao"]!="") { ?>
            <button class="tamanho50 espaco_dir" onclick="window.top.location.href='./pagina=op/devolucao&amp;acao=e&amp;id_devolucao=<?= $rs->id_devolucao; ?>';" id="link_edita<?=$i;?>">edita</button>
            <button class="tamanho50" onclick="var confirma= confirm('Tem certeza que deseja excluir?'); if (confirma) ajaxLink('conteudo', 'devolucaoExcluir&amp;id_devolucao=<?= $rs->id_devolucao; ?>');" id="link_exclui<?=$i;?>">exclui</button>
            <? } else { ?>
            <a href="index2.php?pagina=op/devolucao_relatorio&amp;tipo_relatorio=g&amp;id_devolucao=<?= $rs->id_devolucao; ?>" target="_blank">
                <img border="0" src="images/ico_pdf.png" alt="Relatório" />
            </a>
            |
            <a href="./?pagina=op/devolucao&amp;acao=e&amp;id_devolucao=<?= $rs->id_devolucao; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('conteudo', 'devolucaoExcluir&amp;id_devolucao=<?= $rs->id_devolucao; ?>');" onclick="return confirm('Tem certeza que deseja excluir esta pesagem?');">
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
			echo " <a href=\"./?pagina=op/devolucao_listar&amp;data=". $data2 ."&amp;periodo=". $periodo ."&amp;id_cliente=". $id_cliente ."&amp;id_turno=". $id_turno ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
	*/
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/devolucao_listar&amp;data=<?=$data2;?>&amp;periodo=<?=$periodo;?>&amp;id_cliente=<?=$id_cliente;?>&amp;id_turno=<?=$id_turno;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<script type="text/javascript">
	daFoco("link_edita0");
</script>

<? } ?>