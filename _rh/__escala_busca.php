<?
require_once("conexao.php");
if (pode_algum("rw", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Escala</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
                <form action="<?= AJAX_FORM; ?>formEscalaBuscar" method="post" name="formEscalaBuscar" id="formEscalaBuscar" onsubmit="return ajaxForm('conteudo_interno', 'formEscalaBuscar', 'validacoes');">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                    
                    <label for="id_departamento">* Departamento:</label>
                    <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSoh();">
                        <option value="">- DEPARTAMENTO -</option>
						<?
                        $result_dep= mysql_query("select * from rh_departamentos
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
													order by departamento asc
                                                     ");
                        $i=0;
                        while ($rs_dep = mysql_fetch_object($result_dep)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                        <? $i++; } ?>
                    </select>
                    <br />
                    
                    <label for="id_turno">Turno:</label>
                    <div id="id_turno_atualiza">
                        <select name="id_turno" id="id_turno" title="Turno">	  		
                            <option value="">- TURNO -</option>
                            <?
                            $result_tur= mysql_query("select * from rh_turnos
                                                        where id_departamento = '". $rs->id_departamento ."'
														order by turno asc
                                                         ");
                            $i=0;
                            while ($rs_tur = mysql_fetch_object($result_tur)) {
                            ?>
                            <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                            <? $i++; } ?>
                        </select>
                    </div>
                    <br />
                    
                    <label for="periodo">Período:</label>
                    <select name="periodo" id="periodo" title="Período">	  		
                        <?
                        $i=0;
                        $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%m/%Y')) as data_batida2
													from rh_ponto order by data_batida desc ");
                        while ($rs_per= mysql_fetch_object($result_per)) {
                            $data_batida= explode('/', $rs_per->data_batida2);
							if ((date("d")>=25) && (date("m")==$data_batida[0]) && (date("Y")==$data_batida[1]) ) {
								$proximo_mes= date("m/Y", mktime(0, 0, 0, $data_batida[0]+1, 1, $data_batida[1]));
								$data_batida2= explode('/', $proximo_mes);
						?>
						<option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $proximo_mes; ?>"><?= traduz_mes($data_batida2[0]) .'/'. $data_batida2[1]; ?></option>
						<? } ?>
                        <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_batida2; ?>"><?= traduz_mes($data_batida[0]) .'/'. $data_batida[1]; ?></option>
                        <? $i++; } ?>
                    </select>
                    <br />
                    
                    <label for="modo">Modo:</label>
                    <select name="modo" id="modo" title="Modo">	  		
                        <option value="1">Ativos atualmente</option>
                        <option value="2" class="cor_sim">Ativos no período selecionado</option>
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
                    
                    <br /><br />
                    <center>
                        <button type="submit" id="enviar">Enviar &raquo;</button>
                    </center>
                    
                </form>
            </div>
        
    </fieldset>
</div>

<? } ?>