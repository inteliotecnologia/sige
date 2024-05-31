<?
if (pode_algum("psl", $_SESSION["permissao"])) {
	
	$result= mysql_query("select * from op_pedidos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							order by num_pedido desc
							");
	
	$num= 500;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result= mysql_query("select * from op_pedidos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							order by num_pedido desc
							limit $inicio, $num
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Relatórios de entrega</h2>

<ul class="recuo1">
	<li><a href="./?pagina=op/pedido_busca">gerar novo</a></li>
</ul>

<table cellspacing="0" width="100%" class="sortable" id="tabela">
	<tr>
		<th width="7%">N&uacute;m.</th>
		<th width="15%" align="left">Contrato</th>
	  <th width="23%" align="left">Cliente</th>
		<th width="16%">Data</th>
	    <th width="11%">Entrega</th>
      <th width="15%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->num_pedido; ?></td>
		<td><?= pega_contrato($rs->id_contrato); ?></td>
		<td>
			<?= pega_pessoa($rs->id_cliente); ?>
            <?
			if ($rs->extra==1) echo " <span class=\"menor\"><strong>EXTRA</strong></span>";
			?>
        </td>
		<td align="center"><?= desformata_data($rs->data_pedido); ?></td>
		<td align="center"><?= $rs->entrega; ?></td>
		<td align="center">
            <a href="index2.php?pagina=op/pedido_relatorio&amp;id_cliente=<?= $rs->id_cliente; ?>&amp;data=<?= desformata_data($rs->data_pedido); ?>&amp;entrega=<?= $rs->entrega; ?>" target="_blank">
                    <img border="0" src="images/ico_pdf.png" alt="Relatório" /></a>
            |
            <a href="./?pagina=op/pedido_edita&amp;acao=e&amp;id_pedido=<?= $rs->id_pedido; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			
			<a href="javascript:ajaxLink('conteudo', 'pedidoExcluir&amp;id_pedido=<?= $rs->id_pedido; ?>');" onclick="return confirm('Tem certeza que deseja excluir esta pesagem?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<?
if ($num_paginas > 1) {
	echo "<br /><strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link = $i + 1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=op/pedido_listar&amp;id_cliente=". $id_cliente ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
}
?>

<? } ?>