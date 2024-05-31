<?
require_once("conexao.php");
if (pode_algum("rm", $_SESSION["permissao"])) {
?>

<h2>Aniversariantes</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca</legend>
        
        <form action="index2.php?pagina=rh/aniversariantes_relatorio" target="_blank" method="post" name="formAniversariantes" id="formAniversariantes">

            <div class="parte50">
                
                <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                                
                <label for="periodo">Mês:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    for ($i=1; $i<13; $i++) {
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> <? if ($i==date("m")) echo "selected=\"selected\""; ?> value="<?= $i; ?>"><?= traduz_mes($i); ?></option>
                    <? } ?>
                </select>
                <br />
                
                <label for="situacao">Situação:</label>
                <select name="situacao" id="situacao" title="Situação">	  		
                    <option value="1" class="cor_sim">Somente funcionários ativos presentes</option>
                    <option value="2">Todos os funcionários ativos</option>
                </select>
                <br />
                
                <!--
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1">Datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="" title="Data 1" />
                <br />
                -->
                
                
            </div>
            <br /><br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>

<? } ?>