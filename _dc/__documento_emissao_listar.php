<?
if (pode("c3", $_SESSION["permissao"])) {
	
	if ( (pode("c", $_SESSION["permissao"])) && ($_GET["tipo_documento"]==1) ) $tipo_documento=1;
	else $tipo_documento=2;
	
	if ($_GET["tipo"]!="") $tipo= $_GET["tipo"];
	if ($_POST["tipo"]!="") $tipo= $_POST["tipo"];
	
	if ($_GET["metodo"]!="") $metodo= $_GET["metodo"];
	if ($_POST["metodo"]!="") $metodo= $_POST["metodo"];
	
	if ($_GET["id_documento_emissao"]!="") $id_documento_emissao= $_GET["id_documento_emissao"];
	if ($_POST["id_documento_emissao"]!="") $id_documento_emissao= $_POST["id_documento_emissao"];
	
	if (isset($_POST["geral"])) {
		if ($tipo!="") $str .= " and dc_documentos_emissoes.tipo = '". $tipo ."' ";
	}
	
	$result= mysql_query("select * from dc_documentos_emissoes
							where tipo = '". $tipo ."'
							and   tipo_documento = '". $tipo_documento ."'
							". $str ."
							order by dc_documentos_emissoes.data_emissao desc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Emissão de documentos - <?= pega_tipo_documento_emissao($tipo, $tipo_documento); ?></h2>

<ul class="recuo1">
	<li><a href="./?pagina=dc/documento_emissao&amp;acao=i&amp;tipo=<?=$tipo;?>&amp;metodo=<?=$metodo;?>&amp;tipo_documento=<?=$tipo_documento;?>">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
	  <th width="14%" align="left">Empresa</th>
		<th width="6%" align="left">Número</th>
		<th width="8%" align="left">Data</th>
        <th width="17%" align="left">Assunto</th>
        <? if ($_GET["tipo"]!=7) { ?>
        <th width="14%" align="left">De</th>
        <th width="13%" align="left">Para</th>
        <? } else { ?>
        <th width="14%" align="left">Método</th>
        <? } ?>
		<th width="14%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		/*
		if ($rs->status_centro_custo==1) $status= 0;
		else $status= 1;*/
	?>
	<tr class="<?= $classe; ?> corzinha">
	  <td><?= pega_empresa($rs->id_empresa); ?></td>
		<td><?= $rs->num ."/". $rs->ano; ?></td>
		<td><?= desformata_data($rs->data_emissao); ?></td>
        <td><?= $rs->assunto; ?></td>
        <? if ($rs->tipo!=7) { ?>
        <td><?= $rs->de; ?></td>
        <td><?= $rs->para; ?></td>
        <? } else { ?>
        <td><?= pega_metodo_documento_assinado($rs->metodo); ?></td>
        <? } ?>
		<td align="center">
			<a target="_blank" href="index2.php?pagina=dc/documento_emissao_relatorio&amp;id_documento_emissao=<?= $rs->id_documento_emissao; ?>">
				<img border="0" src="images/ico_pdf.png" alt="Edita" /></a>
			|
            <a href="./?pagina=dc/documento_emissao&amp;acao=e&amp;id_documento_emissao=<?= $rs->id_documento_emissao; ?>&amp;tipo_documento=<?= $rs->tipo_documento; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			<? //if ($_SESSION["tipo_usuario"]=="a") { ?>
            |
			<a href="javascript:ajaxLink('conteudo', 'documentoEmissaoExcluir&amp;id_documento_emissao=<?= $rs->id_documento_emissao; ?>&amp;tipo=<?= $rs->tipo; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
            <? //} ?>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>