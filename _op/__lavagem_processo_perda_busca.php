<?
require_once("conexao.php");
if (pode_algum("ps", $_SESSION["permissao"])) {
?>

<h2>Relatório de perda de processo - Área Suja</h2>

<div id="conteudo_interno">
    
    <div class="parte50">
        <fieldset>
            <legend>Diário</legend>
            
            <form action="index2.php?pagina=op/lavagem_processo_perda_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes1');">
            
                <input class="escondido" type="hidden" id="validacoes1" value="data@data" />                
                <input class="escondido" type="hidden" name="tipo_relatorio" value="d" />
               
                <label for="data">Data:</label>
                <input name="data" id="data" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" class="tamanho25p espaco_dir" onkeyup="formataData(this);" value="<?=date("d/m/Y");?>" title="Data" />
                <br />
                
                <label for="id_equipamento">Máquina:</label>
                <select name="id_equipamento" id="id_equipamento" title="Máquina">	  		
                    <option value="">- TODAS -</option>
                    <?
                    $result_equi= mysql_query("select * from op_equipamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   tipo_equipamento = '1'
                                                order by equipamento asc
                                                 ");
                    $i=0;
                    while ($rs_equi = mysql_fetch_object($result_equi)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_equi->id_equipamento; ?>" <? if ($rs_equi->id_equipamento==$rs->id_equipamento) echo "selected=\"selected\""; ?>><?= $rs_equi->equipamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_processo">Processo:</label>
                <select name="id_processo" id="id_processo" title="Processo">	  		
                    <option value="">- TODAS -</option>
                    <?
                    $result_proc= mysql_query("select * from op_equipamentos_processos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   status_processo = '1'
                                                order by codigo asc
                                                 ");
                    $i=0;
                    while ($rs_proc= mysql_fetch_object($result_proc)) {
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_proc->id_processo; ?>"><?= $rs_proc->codigo .") ". $rs_proc->processo; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    <div class="parte50">
        <fieldset>
            <legend>Mensal</legend>
            
            <form action="index2.php?pagina=op/lavagem_processo_perda_relatorio" target="_blank" method="post" name="formLavagemBuscar" id="formLavagemBuscar" onsubmit="return validaFormNormal('validacoes2');">
            
                <input class="escondido" type="hidden" id="validacoes2" value="" />
                
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
                
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1">Datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br />
                
                <label for="id_equipamento">Máquina:</label>
                <select name="id_equipamento" id="id_equipamento2" title="Máquina">	  		
                    <option value="">- TODAS -</option>
                    <?
                    $result_equi= mysql_query("select * from op_equipamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   tipo_equipamento = '1'
                                                order by equipamento asc
                                                 ");
                    $i=0;
                    while ($rs_equi = mysql_fetch_object($result_equi)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_equi->id_equipamento; ?>" <? if ($rs_equi->id_equipamento==$rs->id_equipamento) echo "selected=\"selected\""; ?>><?= $rs_equi->equipamento; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_processo">Processo:</label>
                <select name="id_processo" id="id_processo" title="Processo">	  		
                    <option value="">- TODAS -</option>
                    <?
                    $result_proc= mysql_query("select * from op_equipamentos_processos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   status_processo = '1'
                                                order by codigo asc
                                                 ");
                    $i=0;
                    while ($rs_proc= mysql_fetch_object($result_proc)) {
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_proc->id_processo; ?>"><?= $rs_proc->codigo .") ". $rs_proc->processo; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
</div>

<? } ?>