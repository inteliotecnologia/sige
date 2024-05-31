<?
require_once("conexao.php");
if (pode("jk", $_SESSION["permissao"])) {
	
	if ($_GET["id_rm"]!="") $id_rm= $_GET["id_rm"];
	
	$result_estoque= mysql_query("select * from fi_estoque_mov, fi_itens
                                        where fi_estoque_mov.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   fi_estoque_mov.id_item = fi_itens.id_item
                                        and   fi_estoque_mov.id_rm = '". $id_rm ."'
										and   fi_estoque_mov.tipo_trans= 's'
										and   fi_estoque_mov.subtipo_trans = 'n'
                                        order by fi_estoque_mov.data_trans desc, fi_estoque_mov.hora_trans desc
                                        ");
	
	if (mysql_num_rows($result_estoque)==0)
		echo "Nenhum item retirado do estoque.";
	else {
?>
    <table cellpadding="0" cellspacing="0" width="100%" id="tabela" class="sortable">
        <tr>
            <th width="32%" align="left">Item</th>
            <th width="21%" align="left">Qtde</th>
            <th width="16%" align="left">Custo</th>
            <th width="21%" align="left">Data</th>
            <th width="10%" align="left" class="unsortable">Excluir</th>
        </tr>
        <?
        $i=0;
		
		$valor_total= 0;
		
        while ($rs_estoque= mysql_fetch_object($result_estoque)) {
			if (($j%2)==0) $classe= "odd";
			else $classe= "even";
        ?>
        <tr class="<?= $classe; ?> menor">
            <td><?= pega_item($rs_estoque->id_item); ?></td>
            <td><?= fnumf($rs_estoque->qtde) ." ". pega_tipo_apres($rs_estoque->tipo_apres); ?></td>
            <td>
            <?
			$result_custo= mysql_query("select valor from fi_custos
										where id_mov = '". $rs_estoque->id_mov ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										");
			$rs_custo= mysql_fetch_object($result_custo);
			
			$valor_total+= $rs_custo->valor;
			
			echo "R$ ". fnum($rs_custo->valor);
			?>
            </td>
            <td>
            <span class="escondido"><?= $rs_estoque->data_trans ." ". $rs_estoque->hora_trans; ?></span>
            <?= desformata_data($rs_estoque->data_trans) ." ". $rs_estoque->hora_trans; ?>
            </td>
            <td align="center">
                <a href="javascript:ajaxLink('estoque_rm', 'excluiRMEstoque&amp;id_rm=<?= $rs_estoque->id_rm; ?>&amp;id_mov=<?= $rs_estoque->id_mov; ?>');" onclick="return confirm('Tem certeza que deseja desfazer a retirada do estoque?');">
                    <img border="0" src="images/ico_lixeira.png" alt="Excluir" />
                </a>
            </td>
        </tr>
        <? } ?>
        <tr class="menor">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>
            <strong><?= "R$ ". fnum($valor_total); ?></strong>
            </td>
            <td>&nbsp;</td>
            <td align="center">&nbsp;</td>
        </tr>
    </table>
    <? } ?>
<? } ?>