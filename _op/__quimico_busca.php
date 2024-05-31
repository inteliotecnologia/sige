<?
require_once("conexao.php");
if (pode("ps", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Área suja - Troca de químicos</h2>

<div id="conteudo_interno">
	<? if ($_GET["geral"]!=1) { ?>
    <div class="parte50">
        <fieldset>
            <legend>Diário</legend>
        	
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=op/quimico_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=op/quimico_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>

                <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="d" />
                
                <label for="data">Data:</label>
                <input name="data" id="data" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="<?=date("d/m/Y");?>" title="Data 1" />
                <br />
                
                <label for="id_quimico">Químico:</label>
                <select id="id_quimico" name="id_quimico">
                    <option value="">- TODOS -</option>
                    <?
                    $j=1;
                    $vetor= pega_quimico('l');
                    
                    while ($vetor[$j]) {
                    ?>
                    <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $j; ?>" <? if ($j==$rs->id_quimico) echo "selected=\"selected\""; ?>><?= $vetor[$j]; ?></option>
                    <? $j++; } ?>
                </select>
                <br />
                
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
        
    	</fieldset>
    </div>
    <? } ?>
    
    <div class="parte50">
        <fieldset>
            <legend>Mensal</legend>
        	
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=op/quimico_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=op/quimico_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>

                <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="m" />
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_troca, '%m/%Y')) as data_troca2
												from op_suja_quimicos_trocas order by data_troca desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_troca= explode('/', $rs_per->data_troca2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_troca2; ?>"><?= traduz_mes($data_troca[0]) .'/'. $data_troca[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="id_quimico">Químico:</label>
                <select id="id_quimico" name="id_quimico">
                    <option value="">- TODOS -</option>
                    <?
                    $j=1;
                    $vetor= pega_quimico('l');
                    
                    while ($vetor[$j]) {
                    ?>
                    <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $j; ?>" <? if ($j==$rs->id_quimico) echo "selected=\"selected\""; ?>><?= $vetor[$j]; ?></option>
                    <? $j++; } ?>
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

<script language="javascript" type="text/javascript">
	daFoco("data");
</script>

<? } ?>