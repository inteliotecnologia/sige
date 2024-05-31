<?
if (pode("aiz12", $_SESSION["permissao"])) {
	
	if ($_GET["esquema"]!="") $esquema= $_GET["esquema"];
	if ($_POST["esquema"]!="") $esquema= $_POST["esquema"];
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	if ($_GET["tipo_pessoa"]!="") $tipo_pessoa= $_GET["tipo_pessoa"];
	if ($_POST["tipo_pessoa"]!="") $tipo_pessoa= $_POST["tipo_pessoa"];
	
	if ($tipo_pessoa=="") $tipo_pessoa="c";
	
	if ($id_cliente!="") $str= "and   id_cliente = '". $id_cliente ."' ";
	
	$result= mysql_query("select tr_clientes_ad.*, pessoas.apelido_fantasia from tr_clientes_ad, pessoas, pessoas_tipos
							where tr_clientes_ad.id_cliente = pessoas.id_pessoa
							and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
							and   pessoas_tipos.tipo_pessoa = '". $tipo_pessoa ."'
							". $str ."
							and   tr_clientes_ad.situacao = '1'
							order by pessoas.nome_rz asc, tr_clientes_ad.nome asc
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<? if ($esquema=="") { ?>
<div id="conteudo_interno">

<h2>Contatos - <?= pega_tipo_pessoa($tipo_pessoa); ?></h2>
<? } ?>

<ul class="recuo1">
	<li><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/ad&acao=i&esquema=<?=$esquema;?>&id_cliente=<?=$id_cliente;?>&tipo_pessoa=<?=$tipo_pessoa;?>');">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="9%">Cód.</th>
		<th width="30%" align="left">Cliente</th>
		<th width="28%" align="left">Nome</th>
        <th width="16%" align="left">Setor</th>
        <th width="17%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		if ($rs->status_usuario==1) $status= 0;
		else $status= 1;
	?>
	<tr id="linha_<?= $i; ?>" class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_ad; ?></td>
		<td><?= $rs->apelido_fantasia; ?></td>
		<td><?= $rs->nome; ?></td>
        <td><?= $rs->setor; ?></td>
        <td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/ad&acao=e&amp;id_ad=<?= $rs->id_ad; ?>&esquema=1');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
        
	        <? if ($_SESSION["tipo_usuario"]=='a') { ?>
            |
			<a href="javascript:ajaxLink('linha_<?= $i; ?>', 'adExcluir&amp;id_ad=<?= $rs->id_ad; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? } ?>
        
        </td>
	</tr>
	<? $i++; } ?>
</table>

<? if ($esquema=="") { ?>
</div>
<? } ?>

<? } ?>