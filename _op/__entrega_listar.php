<?
if (pode_algum("psl", $_SESSION["permissao"])) {
	
	if ($_POST["data"]!="") $data2= $_POST["data"];
	if ($_GET["data"]!="") $data2= $_GET["data"];
	if ($data2!="") $str .= " and   data_pedido = '". formata_data_hifen($data2) ."' ";
	
	if ($_POST["num_pedido"]!="") $num_pedido= $_POST["num_pedido"];
	if ($_GET["num_pedido"]!="") $num_pedido= $_GET["num_pedido"];
	if ($num_pedido!="") $str .= " and   num_pedido = '". $num_pedido ."' ";
	
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($id_cliente!="") $str .= " and   id_cliente= '". $id_cliente ."' ";
	
	$result= mysql_query("select * from op_pedidos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							$str
							order by id_pedido desc, data_pedido desc, entrega desc, num_pedido desc
							");
	
	$num= 50;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result= mysql_query("select * from op_pedidos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							$str
							order by id_pedido desc, data_pedido desc, entrega desc, num_pedido desc
							limit $inicio, $num
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Relatórios de entrega</h2>

<ul class="recuo1">
	<li class="flutuar_esquerda tamanho200"><a href="./?pagina=op/entrega_busca5">gerar nota de entrega</a></li>
    <li class="flutuar_esquerda"><a href="./?pagina=op/entrega_busca2">buscar nota gerada</a></li>
</ul>
<br /><br />

<table cellspacing="0" width="100%" class="sortable" id="tabela">
	<tr>
		<th width="7%">N&uacute;m.</th>
		<th width="12%" align="left">Contrato</th>
	  <th width="30%" align="left">Cliente</th>
		<th width="11%">Data</th>
	    <th width="8%">Entrega</th>
	    <th width="10%">Peso total</th>
	    <th width="9%" class="unsortable">Gerada por:</th>
      <th width="13%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		//se a nota for baseada na data de coleta, ele vai ter que fazer a busca por um percurso no próximo dia (entrega)
		if ($rs->basear_pedido==1) {
			$data_basear= "coleta";
			$data_percurso= soma_data($rs->data_pedido, 1, 0, 0);
		}
		//no dia do percurso
		else {
			$data_percurso= $rs->data_pedido;
			$data_basear= "entrega";
		}
		
		if ($_SESSION[id_usuario]==13) {
			/*echo "select * from tr_percursos_clientes, tr_percursos
										where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
										and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') = '". $data_percurso ."'
										and   tr_percursos_clientes.id_cliente = '". $rs->id_cliente ."'
										";
		*/ }
		
		if ($rs->extra=="1") $tipo_percurso= 5;
		else $tipo_percurso= 2;
		
		$result_percurso= mysql_query("select * from tr_percursos_clientes, tr_percursos
										where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
										and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') = '". $data_percurso ."'
										and   tr_percursos_clientes.id_cliente = '". $rs->id_cliente ."'
										and   tr_percursos_clientes.num_percurso_tipo = '". $rs->entrega ."'
										and   tr_percursos.tipo = '$tipo_percurso'
										") or die(mysql_error());
		$linhas_percurso= mysql_num_rows($result_percurso);
		$rs_percurso= mysql_fetch_object($result_percurso);
		
		$ontem= soma_data($rs->data_pedido, -1, 0, 0);
		$hoje= soma_data($rs->data_pedido, 0, 0, 0);
	?>
	<tr id="linha_<?=$i;?>"  class="<?= $classe; ?> corzinha">
		<td align="center"><?= fnumi($rs->num_pedido); ?></td>
		<td><?= pega_contrato($rs->id_contrato); ?></td>
		<td>
			<?= pega_pessoa($rs->id_cliente); ?>
        	<?
			if ($rs->extra==1) echo " <span class=\"menor\"><strong>EXTRA</strong></span>";
			?>
        </td>
		<td align="center"><?= desformata_data($rs->data_pedido) ." <span class=\"". menor ."\">(". $data_basear .")</span>"; ?></td>
		<td align="center"><?= $rs->entrega; ?></td>
		<td align="center"><?= fnum($rs->peso_total); ?> kg</td>
		<td align="center" class="menor"><?= primeira_palavra(pega_nome_pelo_id_usuario($rs->id_usuario)); ?></td>
		<td align="center">
            <? if ($linhas_percurso>0) { ?>
            <a onmouseover="Tip('Com peso.');" href="index2.php?pagina=op/entrega_relatorio5&amp;id_cliente=<?= $rs->id_cliente; ?>&amp;data=<?= desformata_data($hoje); ?>&amp;id_percurso=<?= $rs_percurso->id_percurso; ?>&amp;denominacao=<?= $rs->denominacao; ?>&amp;data_tipo=<?= $rs->data_tipo; ?>&amp;obs=<?= $rs->obs; ?>&amp;mostrar_peso=1" target="_blank">
            	<img border="0" src="images/ico_pdf.png" alt="Relatório" /></a>
            |
            <a onmouseover="Tip('Sem peso.');" href="index2.php?pagina=op/entrega_relatorio5&amp;id_cliente=<?= $rs->id_cliente; ?>&amp;data=<?= desformata_data($hoje); ?>&amp;id_percurso=<?= $rs_percurso->id_percurso; ?>&amp;extra=<?= $rs->extra; ?>&amp;denominacao=<?= $rs->denominacao; ?>&amp;data_tipo=<?= $rs->data_tipo; ?>&amp;obs=<?= $rs->obs; ?>&amp;mostrar_peso=0" target="_blank">
            	<img border="0" src="images/ico_pdf.png" alt="Relatório" /></a>
            | <? } else echo "N/A"; ?>
            
            <? /*<a href="./?pagina=op/pedido_edita&amp;acao=e&amp;id_pedido=<?= $rs->id_pedido; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|*/ ?>
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'pedidoExcluir&amp;id_pedido=<?= $rs->id_pedido; ?>');" onclick="return confirm('Tem certeza que deseja excluir esta nota de entrega?');">
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
			echo " <a href=\"./?pagina=op/entrega_listar&amp;id_cliente=". $id_cliente ."&amp;data=". $data2 ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
}
?>

<? } ?>