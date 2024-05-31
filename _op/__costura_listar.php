<?
if (pode_algum("pl(", $_SESSION["permissao"])) {
	
	if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
	if ($_GET["periodo"]!="") $periodo= $_GET["periodo"];
	if ($periodo!="") $str.= " and   DATE_FORMAT(data_costura, '%m/%Y') = '". $periodo ."' ";
	
	if ($_POST["data"]!="") $data2= $_POST["data"];
	if ($_GET["data"]!="") $data2= $_GET["data"];
	if ($data2!="") $str .= " and   data_costura = '". formata_data_hifen($data2) ."' ";
	
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($id_cliente!="") $str .= " and   id_cliente= '". $id_cliente ."' ";
	
	if ($_POST["id_peca"]!="") $id_peca= $_POST["id_peca"];
	if ($_GET["id_peca"]!="") $id_peca= $_GET["id_peca"];
	if ($id_peca!="") $str .= " and   id_peca= '". $id_peca ."' ";
	
	$result= mysql_query("select * from op_limpa_costura
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							order by data_costura desc
							");
	
	$num= 50;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result= mysql_query("select * from op_limpa_costura
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							order by data_costura desc
							limit $inicio, $num
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Área limpa - Costura</h2>

<ul class="recuo1">
	<li><a href="./?pagina=op/costura&amp;acao=i">inserir</a></li>
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
			echo " <a href=\"./?pagina=op/costura_listar&amp;periodo=". $periodo ."&amp;data=". $data2."&amp;id_peca=". $id_peca ."&amp;id_cliente=". $id_cliente ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
	*/
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/costura_listar&amp;periodo=<?=$periodo;?>&amp;data=<?=$data2;?>&amp;id_peca=<?=$id_peca;?>&amp;id_cliente=<?=$id_cliente;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<table cellspacing="0" width="100%">
	<tr>
		<th width="7%">Cód.</th>
	  <th width="32%" align="left">Cliente</th>
		<th width="11%">Data</th>
      <th width="22%">Pe&ccedil;a</th>
      <th width="13%">Quantidade</th>
      <th width="15%">Ações</th>
  </tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_costura; ?></td>
		<td><?= pega_pessoa($rs->id_cliente); ?></td>
		<td align="center"><?= desformata_data($rs->data_costura); ?></td>
		<td align="center"><?= pega_pecas_roupa($rs->id_peca); ?></td>
		<td align="center"><?= $rs->qtde; ?></td>
		<td align="center">
            <? if ($_SESSION["id_departamento_sessao"]!="") { ?>
            <button class="tamanho50 espaco_dir" onclick="window.top.location.href='./?pagina=op/costura&amp;acao=e&amp;id_costura=<?= $rs->id_costura; ?>';" id="link_edita<?=$i;?>">edita</button>
            <button class="tamanho50" onclick="var confirma= confirm('Tem certeza que deseja excluir?'); if (confirma) ajaxLink('conteudo', 'costuraExcluir&amp;id_costura=<?= $rs->id_costura; ?>');" id="link_exclui<?=$i;?>">exclui</button>
            <? } else { ?>
            <a href="./?pagina=op/costura&amp;acao=e&amp;id_costura=<?= $rs->id_costura; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('conteudo', 'costuraExcluir&amp;id_costura=<?= $rs->id_costura; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
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
			echo " <a href=\"./?pagina=op/costura_listar&amp;periodo=". $periodo ."&amp;data=". $data2."&amp;id_peca=". $id_peca ."&amp;id_cliente=". $id_cliente ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
	*/
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/costura_listar&amp;periodo=<?=$periodo;?>&amp;data=<?=$data2;?>&amp;id_peca=<?=$id_peca;?>&amp;id_cliente=<?=$id_cliente;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<script type="text/javascript">
	daFoco("link_edita0");
</script>

<? } ?>