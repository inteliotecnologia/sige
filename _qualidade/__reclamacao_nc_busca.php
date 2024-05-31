<?
require_once("conexao.php");
if (pode_algum("12", $_SESSION["permissao"])) {
?>

<h2>Relatório de NC/Reclamações</h2>

<div id="conteudo_interno">
    
    <div class="parte50">
        <fieldset>
            <legend>Relatório geral de NC/reclamações</legend>
            
            <form action="index2.php?pagina=qualidade/reclamacao_nc_relatorio" target="_blank" method="post" name="formRMBuscar" id="formRMBuscar" onsubmit="return validaFormNormal('validacoes');">
            
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
               
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_remessa, '%m/%Y')) as data_remessa2 from op_suja_remessas order by data_remessa desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_remessa= explode('/', $rs_per->data_remessa2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_remessa2; ?>"><?= traduz_mes($data_remessa[0]) .'/'. $data_remessa[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="motivo">Tipo:</label>
                <select name="motivo" id="motivo" title="Tipo">	  		
                    <option class="cor_sim" value="r">Reclamação</option>
                    <option value="n">Não-conformidade</option>
                </select>
                <br />
                <br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
</div>

<script language="javascript" type="text/javascript">
	daFoco("periodo");
</script>

<? } ?>