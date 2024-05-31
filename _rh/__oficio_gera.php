<?
require_once("conexao.php");
if (pode("rm", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Gerar ofício</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
                <form target="_blank" action="index2.php?pagina=rh/documento&amp;tipo=6" method="post" name="formEscalaBuscar" id="formEscalaBuscar">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                    <input class="escondido" type="hidden" name="metodo" value="<?= $_GET["metodo"]; ?>" />
                    
                    <? if ($_GET["metodo"]==3) { ?>                    
                    <label for="metodo_num">Número de assinaturas:</label>
                    <input name="metodo_num" id="metodo_num" class="tamanho25p" />
                    <br />
                    <? } ?>
                    
                    <? if ($_GET["metodo"]==1) { ?>
                    <label for="id_departamento">Departamento:</label>
                    <div id="id_departamento_atualiza">
                        <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSoh();">
                            <option value="">- TODOS -</option>
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
                    
                    <label for="id_turno">Turno:</label>
                    <div id="id_turno_atualiza">
                        <select name="id_turno" id="id_turno" title="Turno">	  		
                            <option value="">- TODOS -</option>
                            <?
                            $result_tur= mysql_query("select * from rh_turnos
                                                        where id_departamento = '". $rs->id_departamento ."'
                                                         ");
                            $i=0;
                            while ($rs_tur = mysql_fetch_object($result_tur)) {
                            ?>
                            <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                            <? $i++; } ?>
                        </select>
                    </div>
                    <br />
                    <? } ?>
                    
                    <? if (($_GET["metodo"]==1) || ($_GET["metodo"]==2)) { ?>
                    <label for="situacao">Situação:</label>
                    <select name="situacao" id="situacao" title="Situação">	  		
                        <option value="1" class="cor_sim">Somente funcionários ativos presentes</option>
                        <option value="2">Todos os funcionários ativos</option>
                    </select>
                    <br />
                    <? } ?>
                    
                    <label for="titulo">Título:</label>
                    <input id="titulo" name="titulo" />
                    <br />
                    
                    <label for="texto">Observações:</label>
                    <textarea name="texto" id="texto" title="Texto"></textarea>
                    <br />
                    
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
        	<div class="parte50">
            	
            </div>
    </fieldset>
</div>

<? } ?>