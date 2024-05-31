<?
if (pode("iq|", $_SESSION["permissao"])) {
	if ($_GET["id_item"]) $id_item= $_GET["id_item"];
	if ($_POST["id_item"]) $id_item= $_POST["id_item"];
	
	$result= mysql_query("select *, fi_estoque_mov.id_usuario as id_usuario2
							from fi_estoque_mov, fi_itens
							where fi_estoque_mov.id_item = '". $id_item ."'
							and   fi_estoque_mov.id_item = fi_itens.id_item
							and   fi_estoque_mov.tipo_trans = 'e'
							and   fi_estoque_mov.id_empresa = '". $_SESSION["id_empresa"] ."'
							order by fi_estoque_mov.id_mov asc
							") or die(mysql_error());
	
	//@logs($_SESSION["id_acesso"], $_SESSION["id_usuario_sessao"], $_SESSION["id_cidade_sessao"], $_SESSION["id_posto_sessao"], 1, "tira extrato do remédio ". $_GET["id_remedio"] ." | ". pega_remedio($_GET["id_remedio"]), $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
?>

<h3><?= pega_item($id_item); ?></h3>
<p>Foram encontrados <strong><?= mysql_num_rows($result); ?></strong> registro(s) para sua solicitação.</p>
<br />

<table cellspacing="0" width="100%">
    <tr>
        <th width="11%">Cód.</th>
        <th width="25%">Data</th>
        <th width="23%" align="right">Quantidade</th>
        <th width="19%" align="right">Valor unitário</th>
        <th width="22%" align="right">Valor total</th>
    </tr>
    <?
    $i= 0;
    while ($rs= mysql_fetch_object($result)) {
		$valor_total= $rs->valor_unitario*$rs->qtde;
    ?>
    <tr class="corzinha">
        <td align="center"><?= $rs->id_mov; ?></td>
        <td align="center"><?= desformata_data($rs->data_trans) ." ". $rsx->hora_trans; ?></td>
        <td align="right"><?= fnumf($rs->qtde) ." ". pega_tipo_apres($rs->tipo_apres); ?></td>
      <td align="right">R$ <?= fnum($rs->valor_unitario); ?></td>
        <td align="right">R$ <?= fnum($valor_total); ?></td>
    </tr>
    <?
	$i++;
	}
    ?>
</table>

<br /><br />
<? } ?>