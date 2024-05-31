<?
require_once("conexao.php");
if (pode_algum("rh", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Relatório de vale-transporte</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
            	<form action="index2.php?pagina=rh/vt_relatorio" target="_blank" method="post" name="formVTBuscar" id="formVTBuscar">
                    <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                    <input class="escondido" type="hidden" name="impressao" value="1" />
                    
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
                    
                    <label>Tipo:</label>
                    <select class="tamanho25p" name="tipo_relatorio" id="tipo_relatorio" title="Tipo de relatório">	  		
                        <option class="cor_sim" value="1">Sintético</option>
                        <option value="2">Analítico</option>
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