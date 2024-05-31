<?
if (pode_algum("p", $_SESSION["permissao"])) {
	$result= mysql_query("select * from op_limpa_pecas
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							order by peca asc
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Peças de roupa</h2>

<ul class="recuo1">
	<li><a href="./?pagina=op/peca&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="6%">Cód.</th>
		<th width="46%" align="left">Peça</th>
		<th width="15%">Grupo</th>
		<th width="16%">Qtde por pacote</th>
		<th width="17%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		if ($rs->status_peca==1) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_peca; ?></td>
		<td><?= $rs->peca; ?></td>
		<td align="center"><?= pega_grupo_roupa($rs->id_grupo); ?></td>
		<td align="center"><?= $rs->qtde_padrao_pacote; ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=op/peca&amp;acao=e&amp;id_peca=<?= $rs->id_peca; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
            <a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'pecaStatus&amp;id_peca=<?= $rs->id_peca; ?>&amp;status=<?= $status; ?>');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
            
	        <? if ($_SESSION["tipo_usuario"]=='a') { ?>
            |
			<a href="javascript:ajaxLink('conteudo', 'pecaExcluir&amp;id_peca=<?= $rs->id_peca; ?>&amp;tipo_peca=<?= $rs->tipo_peca; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? } ?>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>