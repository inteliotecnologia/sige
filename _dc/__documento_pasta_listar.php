<?
if (pode("d5", $_SESSION["permissao"])) {
	
	if ($_POST["id_empresa_aqui"]!="") $id_empresa_aqui= $_POST["id_empresa_aqui"];
	if ($_GET["id_empresa_aqui"]!="") $id_empresa_aqui= $_GET["id_empresa_aqui"];
	if ($id_empresa_aqui!="") $str.= " and   id_empresa = '". $id_empresa_aqui ."' ";
	
	if ($_POST["id_departamento"]!="") $id_departamento= $_POST["id_departamento"];
	if ($_GET["id_departamento"]!="") $id_departamento= $_GET["id_departamento"];
	if ($id_departamento!="") $str.= " and   id_departamento = '". $id_departamento ."' ";
	
	if ($_POST["nome_pasta"]!="") $nome_pasta= $_POST["nome_pasta"];
	if ($_GET["nome_pasta"]!="") $nome_pasta= $_GET["nome_pasta"];
	if ($nome_pasta!="") $str.= " and   nome_pasta like '%". $nome_pasta ."%' ";
	
	if ($_POST["status_pasta"]!="") $status_pasta= $_POST["status_pasta"];
	if ($_GET["status_pasta"]!="") $status_pasta= $_GET["status_pasta"];
	if ($status_pasta=="3") {
		$_SESSION["status_pasta"]= "";
		$status_pasta="";
	}
	elseif ($status_pasta!="") {
		$_SESSION["status_pasta"]= $status_pasta;
	}
	if ($_SESSION["status_pasta"]!="") $status_pasta= $_SESSION["status_pasta"];
	if ($status_pasta!="") $str.= " and   status_pasta = '". $status_pasta ."' ";
	
	$result= mysql_query("select * from dc_documentos_pastas
						 	where 1=1
							$str
							order by id_departamento asc, pasta asc
							");
	
	$num= 100;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;
	
	$result= mysql_query("select * from dc_documentos_pastas
						 	where 1=1
							$str
							order by id_departamento asc, pasta asc
							limit $inicio, $num
							");
	
	
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Todas as pastas</h2>

<div class="div_abas screen" id="aba_departamentos">
    <ul class="abas">
		<li id="aba_departamento" <? if ($id_departamento=="") echo "class=\"atual\""; ?>><a href="./?pagina=dc/documento_pasta_listar&amp;id_departamento=">Todos</a></li>
    <?
	$result_deptos= mysql_query("select distinct(dc_documentos_pastas.id_departamento), rh_departamentos.departamento, rh_departamentos.id_empresa
									from  dc_documentos_pastas, rh_departamentos
									where dc_documentos_pastas.id_departamento = rh_departamentos.id_departamento
									order by rh_departamentos.id_empresa asc, rh_departamentos.departamento asc
									");
	while ($rs_deptos= mysql_fetch_object($result_deptos)) {
	?>
        <li id="aba_departamento" <? if ($id_departamento==$rs_deptos->id_departamento) echo "class=\"atual\""; ?>><a href="./?pagina=dc/documento_pasta_listar&amp;id_departamento=<?= $rs_deptos->id_departamento; ?>"><?= pega_empresa($rs_deptos->id_empresa) ." - ". ucfirst(strtolower($rs_deptos->departamento)); ?></a></li>
    <? } ?>
    </ul>
</div>
<br /><br />

<ul class="recuo1">
	<li class="flutuar_esquerda tamanho120"><a href="./?pagina=dc/documento_pasta&amp;acao=i&amp;id_departamento=<?=$id_departamento;?>">inserir</a></li>
	<li class="<? if ($status_pasta==="1") echo "negrito"; ?> flutuar_esquerda tamanho120"><a href="./?pagina=dc/documento_pasta_listar&amp;id_departamento=<?=$id_departamento;?>&amp;status_pasta=1">listar ativas</a></li>
	<li class="<? if ($status_pasta==="0") echo "negrito"; ?> flutuar_esquerda tamanho120"><a href="./?pagina=dc/documento_pasta_listar&amp;id_departamento=<?=$id_departamento;?>&amp;status_pasta=0">listar inativas</a></li>
	<li class="<? if ($status_pasta==="") echo "negrito"; ?>"><a href="./?pagina=dc/documento_pasta_listar&amp;id_departamento=<?=$id_departamento;?>&amp;status_pasta=3">listar todas</a></li>
</ul>
<br />

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="5%">Cód.</th>
		<th width="14%" align="left">Empresa</th>
		<th width="22%" align="left">Departamento</th>
		<th width="5%" align="left">Pasta</th>
		<th width="31%" align="left">Nome</th>
		<th width="9%">Situa&ccedil;&atilde;o</th>
		<th width="14%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		if ($rs->status_pasta==1) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha" id="linha_<?=$i;?>">
		<td align="center"><?= $rs->id_pasta; ?></td>
		<td><?= pega_empresa($rs->id_empresa); ?></td>
		<td><?= pega_departamento($rs->id_departamento); ?></td>
		<td><a href="./?pagina=dc/documento_listar&amp;id_pasta=<?= $rs->id_pasta; ?>"><?= $rs->pasta; ?></a></td>
		<td><a href="./?pagina=dc/documento_listar&amp;id_pasta=<?= $rs->id_pasta; ?>">
		  <?= $rs->nome_pasta; ?>
		</a></td>
		<td align="center"><?= ativo_inativo($rs->status_pasta); ?></td>
		<td align="center">
			<? if (pode("d", $_SESSION["permissao"])) { ?>
            <a href="./?pagina=dc/documento_pasta&amp;acao=e&amp;id_pasta=<?= $rs->id_pasta; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'documentoPastaStatus&amp;id_pasta=<?= $rs->id_pasta; ?>&amp;status=<?= $status; ?>');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
            |
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'documentoPastaExcluir&amp;id_pasta=<?= $rs->id_pasta; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
            <? } else echo "-"; ?>
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
			echo " <a href=\"./?pagina=dc/documento_pasta_listar&amp;id_empresa_aqui=". $id_empresa_aqui ."&amp;id_departamento=". $id_departamento ."&amp;nome_pasta=". $nome_pasta ."&amp;status_pasta=". $status_pasta ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
}
?>

<? } ?>