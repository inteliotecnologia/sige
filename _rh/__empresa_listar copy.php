<?
if (pode_algum("ap", $_SESSION["permissao"])) {
	
	if ($_GET["tipo_empresa"]!="") $tipo_empresa= $_GET["tipo_empresa"];
	else $tipo_empresa= $_POST["tipo_empresa"];
	
	if ( (pode("pi", $_SESSION["permissao"])) && ($tipo_empresa=="c") ) {
		$tipo_empresa2= "c";
		$tit= "Clientes";
		$str= "and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'";
	}
	elseif ( (pode("i", $_SESSION["permissao"])) && ($tipo_empresa=="f") ) {
		$tipo_empresa2= "f";
		$tit= "Fornecedores";
		$str= "and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'";
	}
	elseif (pode("a", $_SESSION["permissao"])) {
		$tipo_empresa2= "a";
		$tit= "Empresas com acesso ao sistema";
	}
	else $tit= "Empresas";
	
	$result= mysql_query("select * from pessoas, empresas
							where pessoas.id_pessoa = empresas.id_pessoa
							and   empresas.tipo_empresa = '$tipo_empresa2'
							". $str ."
							order by empresas.codigo asc,
							pessoas.nome_rz asc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2><?= $tit; ?></h2>

<ul class="recuo1">
	<li><a href="./?pagina=rh/empresa&amp;acao=i&amp;tipo_empresa=<?=$tipo_empresa;?>">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="6%">Cód.</th>
		<th width="11%" align="left">Identifica&ccedil;&atilde;o</th>
		<th width="23%" align="left">Raz&atilde;o Social</th>
		<th width="22%" align="left">Nome fantasia</th>
		<th width="21%">CNPJ</th>
		<th width="17%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		if ($rs->status_empresa==1) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_empresa; ?></td>
		<td><?= $rs->codigo; ?></td>
		<td><?= $rs->nome_rz; ?></td>
		<td><?= $rs->apelido_fantasia; ?></td>
		<td align="center"><?= $rs->cpf_cnpj; ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=rh/empresa&amp;acao=e&amp;id_empresa=<?= $rs->id_empresa; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
			<a href="javascript:ajaxLink('conteudo', 'empresaStatus&amp;id_empresa=<?= $rs->id_empresa; ?>&amp;status=<?= $status; ?>');" onclick="return confirm('Tem certeza que deseja alterar o status desta empresa?');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
	        |
	        <a href="javascript:ajaxLink('conteudo', 'empresaExcluir&amp;id_empresa=<?= $rs->id_empresa; ?>');" onclick="return confirm('Tem certeza que deseja excluir esta empresa?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>        </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>