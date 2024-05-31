<?
require_once("conexao.php");
if (pode_algum("rhw", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Impressão de escala</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
                <form action="index2.php?pagina=rh/escala_relacao_relatorio" target="_blank" method="post" name="formEscalaBuscar" id="formEscalaBuscar" onsubmit="return validaFormNormal('validacoes');">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="id_departamento@vazio|periodo@vazio" />
                    <input class="escondido" type="hidden" name="impressao" value="1" />
                    
                    <label for="id_departamento">Departamento:</label>
                    <div id="id_departamento_atualiza">
                        <select name="id_departamento" id="id_departamento" title="Departamento">
                            <option value="">- DEPARTAMENTO -</option>
                            <?
                            $result_dep= mysql_query("select * from rh_departamentos
                                                        where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                         ");
                            $i=0;
                            while ($rs_dep = mysql_fetch_object($result_dep)) {
                            ?>
                            <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                            <? $i++; } ?>
                        </select>
                    </div>
                    <br />
                    
                    <label for="data">Data:</label>
                    <input name="data" id="data" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="<?= date("d/m/Y"); ?>" title="Data" />
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
                    
                    <br /><br />
                    <center>
                        <button type="submit" id="enviar">Enviar &raquo;</button>
                    </center>
                    
                </form>
            </div>
        
    </fieldset>
</div>

<? } ?>