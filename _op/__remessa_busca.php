<?
require_once("conexao.php");
if (pode_algum("ps", $_SESSION["permissao"])) {
?>

<h2>Remessas</h2>

<div id="conteudo_interno">
    <div class="parte50">
        <fieldset>
            <legend>Diário</legend>
        	
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=op/remessa_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=op/remessa_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
    
                <input class="escondido" type="hidden" id="validacoes" value="data@vazio" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="d" />
                
                <label for="data">Data:</label>
                <input name="data" id="data" class="tamanho25p espaco_dir" onkeyup="formataData(this);" value="<?=date("d/m/Y");?>" title="Data 1" />
                <br />
                
                <label for="id_veiculo">Veículo:</label>
                <select name="id_veiculo" id="id_veiculo" title="Veículo">	  		
                    <option value="">- TODOS -</option>
                    <?
                    $result_vei= mysql_query("select * from op_veiculos
                                                order by veiculo asc
                                                ");
                    $i=0;
                    while ($rs_vei = mysql_fetch_object($result_vei)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_vei->id_veiculo; ?>" <? if ($rs_vei->id_veiculo==$rs->id_veiculo) echo "selected=\"selected\""; ?>><?= $rs_vei->veiculo ." ". $rs_vei->placa; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
                <br />
            </form>
            
        </fieldset>
    </div>
    
    <div class="parte50">
        <fieldset>
            <legend>Mensal</legend>
        
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=op/remessa_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=op/remessa_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
    
                <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                
                <input class="escondido" type="hidden" name="tipo_relatorio" value="m" />
                
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
				
                <label for="id_veiculo">Veículo:</label>
                <select name="id_veiculo" id="id_veiculo" title="Veículo">	  		
                    <option value="">- TODOS -</option>
                    <?
                    $result_vei= mysql_query("select * from op_veiculos
                                                order by veiculo asc
                                                ");
                    $i=0;
                    while ($rs_vei = mysql_fetch_object($result_vei)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_vei->id_veiculo; ?>" <? if ($rs_vei->id_veiculo==$rs->id_veiculo) echo "selected=\"selected\""; ?>><?= $rs_vei->veiculo ." ". $rs_vei->placa; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <? /*
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1">Datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="" title="Data 1" />
                <br />
                */ ?>
                
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
                <br />
            </form>
            
        </fieldset>
    </div>
    
</div>

<script language="javascript" type="text/javascript">
	daFoco("data");
</script>

<? } ?>