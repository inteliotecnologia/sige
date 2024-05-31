<?
if (isset($_SESSION["id_empresa"])) {
	$result= mysql_query("select oss.*, DATE_FORMAT(oss.data_os, '%d/%m/%Y %H:%i:%s') as data_os, empresas.nome_fantasia,
							empresas.cnpj, servicos.servico from oss, empresas, servicos
							where oss.id_servico = servicos.id_servico
							and   oss.id_empresa = empresas.id_empresa
							and   oss.id_os = '". $id_os ."'
							") or die(mysql_error());
	$rs= mysql_fetch_object($result);
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2 class="titulos">Visualização de ordem de serviço</h2>

<br />
<div class="parte50">
    <fieldset>
        <legend>Dados da OS</legend>
        
        <label>Cód. OS:</label>
        <?= $rs->id_os; ?>
        <br class="limpa" />
        
        <label>Situação:</label>
        <?= pega_status_os($rs->status_os); ?>
        <br class="limpa" />
        
        <label>Empresa:</label>
        <?= $rs->nome_fantasia; ?>
        <br class="limpa" />
        
        <label>Data/Hora:</label>
        <?= $rs->data_os; ?>
        <br class="limpa" />
        
        <label>Solicitante:</label>
        <?= $rs->solicitante; ?>
        <br class="limpa" />
        
        <label>Tel. solicitante:</label>
        <?= $rs->tel_solicitante; ?>
        <br class="limpa" />
        
        <!--
        <label>Técnico destacado:</label>
        <?= pega_tecnico($rs->id_tecnico); ?>
        <br class="limpa" />
        -->
        
        <label>Tipo de atendimento:</label>
        <?= pega_tipo_atendimento($rs->tipo_atendimento); ?>
        <br class="limpa" />
        
        <label>Prioridade:</label>
        <?= pega_prioridade($rs->prioridade); ?>
        <br class="limpa" />
        
        <label>Serviço:</label>
        <?= $rs->servico; ?>
        <br class="limpa" />
        
        <label>Obs. gerais cliente:</label>
        <?
        if ($rs->obs_gerais!="")
            echo nl2br($rs->obs_gerais);
        else
            echo "Não informado!";
        ?>
        <br class="limpa" />
	</fieldset>
</div>
<div class="parte50">
    <fieldset>
        <legend>Detalhamento do serviço</legend>
        <table cellspacing="0">
            <tr>
                <th align="left" width="25%">Equipamento</th>
                <th align="left" width="25%">Nº de série</th>
                <th align="left" width="50%">Observações</th>
            </tr>
            <tr>
                <td class="sem"><?= $rs->equipamento; ?></td>
                <td class="sem"><?= $rs->nserie; ?></td>
                <td class="sem">
                <?
                if ($rs->obs!="")
                    echo nl2br($rs->obs);
                else
                    echo "Não informado!";
                ?>
                </td>
            </tr>
        </table>
        <br />
        
        <?
        if ($rs->status_os==2) {
            switch ($rs->id_servico) {
                case "1":
                    $result2= mysql_query("select * from os_hemodialise where id_os = '$rs->id_os' ");
                    $rs2= mysql_fetch_object($result2);
                ?>
                <label>Serviço executado:</label>
                <?= $rs2->servico_executado; ?>
                <br class="limpa" />
                
                <label>Material utilizado:</label>
                <?= $rs2->material_utilizado; ?>
                <br class="limpa" />
                
                <?
                break;
                case "2":
                ?>
                
                <?
                break;
                case "3":
                ?>
                
                <?
                break;
            }//fim scwitch
        }
        ?>
    </fieldset>
    <br />
</div>
<br class="limpa" /><br class="limpa" />

<center>
    <? if ($_SESSION["tipo_empresa"]=="a") { ?>
    <a href="index2.php?pagina=_os/os_pdf&amp;id_os=<?= $rs->id_os; ?>" target="_blank">versão p/ impressão</a> |
    <a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=_os/os_editar&amp;id_os=<?= $rs->id_os; ?>');" id="enviar">completar dados</a>
    <? } ?>
</center>
<? } ?>