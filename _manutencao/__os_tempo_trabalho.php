<?
require_once("conexao.php");
if (pode("kj", $_SESSION["permissao"])) {
	
	$result_tempo= mysql_query("select * from man_oss_servicos
                                        where id_empresa = '". $_SESSION["id_empresa"] ."'
                                        and   id_os = '". $id_os ."'
                                        order by data_inicio asc
                                        ");
	
	if (mysql_num_rows($result_tempo)==0)
		echo "Nenhum serviço efetuado.";
	else {
?>
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <th width="33%" align="left">Funcionário</th>
            <th width="30%" align="left">Início</th>
            <th width="27%" align="left">Fim</th>
            <th width="10%" align="left">Excluir</th>
        </tr>
        <?
        $i=0;
        while ($rs_tempo= mysql_fetch_object($result_tempo)) {
        ?>
        <tr>
            <td><?= primeira_palavra(pega_funcionario($rs_tempo->id_funcionario)); ?></td>
            <td>
            <span class="menor">
				<?
                echo desformata_data($rs_tempo->data_inicio) ." ". $rs_tempo->hora_inicio;
                ?>
            </span>
            </td>
            <td>
            <span class="menor">
				<?
                if ($rs_tempo->data_fim=="") echo "<a href=\"javascript:ajaxLink('tempo_servico', 'finalizaOSServico&amp;id_os=". $id_os ."&amp;id_os_servico=". $rs_tempo->id_os_servico ."&amp;data_fim=". date("d/m/Y") ."&amp;hora_fim=". date("H:i:s") ."');\" onclick=\"return confirm('Tem certeza que deseja finalizar este serviço?');\">&laquo; finaliza serviço</a>";
                else echo desformata_data($rs_tempo->data_fim) ." ". $rs_tempo->hora_fim;
                ?>
            </span>
            </td>
            <td align="center"><a href="javascript:ajaxLink('tempo_servico', 'excluiOSServico&amp;id_os=<?= $rs_tempo->id_os; ?>&amp;id_os_servico=<?= $rs_tempo->id_os_servico; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Excluir" /></a></td>
        </tr>
        <? } ?>
    </table>
    <? } ?>
<? } ?>