<?
require_once("conexao.php");
if (pode_algum("rm", $_SESSION["permissao"])) {
?>

<h2>Clientes - Aniversariantes</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca</legend>
        
        <form action="index2.php?pagina=qualidade/pessoa_aniversariantes_relatorio" target="_blank" method="post" name="formAniversariantes" id="formAniversariantes">

            <div class="parte50">
                
                <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <label for="tipo_pessoa">Tipo:</label>
                <select name="tipo_pessoa" id="tipo_pessoa" title="Tipo">
                    <option value="c" <? if ($_GET["tipo_pessoa"]=="c") echo "selected=\"selected\""; ?>>Clientes</option>
                    <option value="f" <? if ($_GET["tipo_pessoa"]=="f") echo "selected=\"selected\""; ?> class="cor_sim">Fornecedores</option>
                </select>
                <br />
                
                <label for="periodo">Mês:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    for ($i=1; $i<13; $i++) {
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> <? if ($i==date("m")) echo "selected=\"selected\""; ?> value="<?= $i; ?>"><?= traduz_mes($i); ?></option>
                    <? } ?>
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