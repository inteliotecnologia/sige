<?
if (pode("d5", $_SESSION["permissao"])) {
	
	if ($_GET["id_departamento"]!="") $id_departamento= $_GET["id_departamento"];
	if ($_POST["id_departamento"]!="") $id_departamento= $_POST["id_departamento"];
	
	if ($_GET["id_pasta"]!="") $id_pasta= $_GET["id_pasta"];
	if ($_POST["id_pasta"]!="") $id_pasta= $_POST["id_pasta"];
	
	if ($_GET["nome_pasta"]!="") $nome_pasta= $_GET["nome_pasta"];
	if ($_POST["nome_pasta"]!="") $nome_pasta= $_POST["nome_pasta"];
	
	if ($_GET["id_documento"]!="") $id_documento= $_GET["id_documento"];
	if ($_POST["id_documento"]!="") $id_documento= $_POST["id_documento"];
	
	if ($_GET["documento"]!="") $documento= $_GET["documento"];
	if ($_POST["documento"]!="") $documento= $_POST["documento"];
	
	if ($_GET["status_pasta"]!="") $status_pasta= $_GET["status_pasta"];
	if ($_POST["status_pasta"]!="") $status_pasta= $_POST["status_pasta"];
	
	if ((isset($_POST["geral"])) && ($id_documento!="")) $str .= " and dc_documentos.id_documento = '". $id_documento ."' ";
	if ((isset($_POST["geral"])) && ($documento!="")) $str .= " and dc_documentos.documento like '%". $documento ."%' ";
	if ((isset($_POST["geral"])) && ($status_pasta!="")) $str .= " and dc_documentos_pastas.status_pasta ='". $status_pasta ."' ";
	
	if ($_POST["id_empresa"]!="") $str .= " and dc_documentos_pastas.id_empresa= '". $_POST["id_empresa"] ."' ";
	if ($id_departamento!="") $str .= " and dc_documentos_pastas.id_departamento = '". $id_departamento ."' ";
	if ($id_pasta!="") $str .= " and dc_documentos.id_pasta = '". $id_pasta ."' ";
	if ($nome_pasta!="") $str .= " and dc_documentos_pastas.nome_pasta like '%". $nome_pasta ."%' ";
	
	if ($documento!="") $str .= " and dc_documentos.documento like '%". $documento ."%' ";
	
	$result= mysql_query("select * from dc_documentos, dc_documentos_pastas
							where dc_documentos.id_pasta = dc_documentos_pastas.id_pasta
							". $str ."
							order by dc_documentos.id_documento desc
							") or die(mysql_error());

	$id_departamento_pasta= pega_departamento_pasta($id_pasta);
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2><?= pega_departamento($id_departamento_pasta); ?> > <?= pega_pasta($id_pasta); ?></h2>

<a href="./?pagina=dc/documento_pasta_listar&amp;id_departamento=<?=$id_departamento_pasta;?>">&laquo; voltar</a>
<br />

<ul class="recuo1">
	<li><a href="./?pagina=dc/documento&amp;acao=i&amp;id_departamento=<?=$id_departamento_pasta;?>&amp;id_pasta=<?=$id_pasta;?>">inserir nesta pasta</a></li>
</ul>
<br />

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="6%">Cód.</th>
		<th width="15%" align="left">Empresa</th>
		<th width="19%" align="left">Departamento</th>
		<th width="23%" align="left">Pasta</th>
        <th width="27%" align="left">Documento</th>
		<th width="10%" class="unsortable">Ações</th>
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
		<td align="center"><?= $rs->id_documento; ?></td>
		<td><?= pega_empresa($rs->id_empresa); ?></td>
		<td><?= pega_departamento($rs->id_departamento); ?></td>
		<td class="menor"><?= $rs->pasta ." - ". $rs->nome_pasta ." (". ativo_inativo($rs->status_pasta) .")"; ?></td>
        <td class="menor"><?= $rs->documento; ?></td>
		<td align="center">
			<? if (pode("d", $_SESSION["permissao"])) { ?>
            <a href="./?pagina=dc/documento&amp;acao=e&amp;id_documento=<?= $rs->id_documento; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('conteudo', 'documentoExcluir&amp;id_documento=<?= $rs->id_documento; ?>&amp;id_pasta=<?= $rs->id_pasta; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
            <? } else echo "-"; ?>
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>