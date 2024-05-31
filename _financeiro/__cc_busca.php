<?
require_once("conexao.php");
if (pode("i", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Centro de custos - Gastos</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
                <form action="index2.php?pagina=financeiro/cc_relatorio" target="_blank" method="post" name="formCCCustosBuscar" id="formCCCustosBuscar">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                    
                    <label for="id_centro_custo">Centro de custo:</label>
                    <select name="id_centro_custo" id="id_centro_custo" title="Centro de custo">
                        <option value="">--- TODOS ---</option>
						<?
                        $result_cc= mysql_query("select * from fi_centro_custos
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                     ");
                        $i=0;
                        while ($rs_cc = mysql_fetch_object($result_cc)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cc->id_centro_custo; ?>"><?= $rs_cc->centro_custo; ?></option>
                        <? $i++; } ?>
                    </select>
                    <br />
                    
                    <label for="periodo">Período:</label>
                    <select name="periodo" id="periodo" title="Período">	  		
                        <?
                        $i=0;
                        $result_per= mysql_query("select distinct(DATE_FORMAT(data, '%m/%Y')) as data2
													from fi_custos order by data desc ");
                        while ($rs_per= mysql_fetch_object($result_per)) {
                            $data= explode('/', $rs_per->data2);
						?>
                        <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data2; ?>"><?= traduz_mes($data[0]) .'/'. $data[1]; ?></option>
                        <? $i++; } ?>
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