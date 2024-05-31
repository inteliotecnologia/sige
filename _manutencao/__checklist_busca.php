<?
require_once("conexao.php");
if (pode_algum("j", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
	
	/*
	for ($i=1; $i<10; $i++) {
		$result= mysql_query("insert into man_checklist_itens (id_empresa, id_categoria, item, periodicidade)
															   values
															   ('1', '9', 'xxxxxxxxxx', '1m')
															   ");
	}*/
	
?>

<h2>Manutenção - Checklist</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
            	<? if ($_GET["geral"]==1) { ?>
                <form action="index2.php?pagina=manutencao/checklist_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
                <? } else { ?>
                <form action="<?= AJAX_FORM; ?>formManutencaoChecklistBuscar" method="post" name="formManutencaoChecklistBuscar" id="formManutencaoChecklistBuscar" onsubmit="return ajaxForm('conteudo_interno', 'formManutencaoChecklistBuscar', 'validacoes');">
                <? } ?>
                    
                    <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio|id_tecnico@vazio" />
                    
                   <label for="periodo">Período:</label>
	                <select name="periodo" id="periodo" title="Período">	  		
	                    <?
	                    $i=0;
	                    $result_per= mysql_query("select distinct(DATE_FORMAT(man_rms_andamento.data_rm_andamento, '%m/%Y')) as data_remessa2 from man_rms, man_rms_andamento
	                    where man_rms.id_rm = man_rms_andamento.id_rm
	                    and   man_rms_andamento.id_situacao = '1'
	                    and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
	                    order by man_rms_andamento.data_rm_andamento desc ");
	                    
	                    while ($rs_per= mysql_fetch_object($result_per)) {
	                        $data_remessa= explode('/', $rs_per->data_remessa2);
	                    ?>
	                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_remessa2; ?>"><?= traduz_mes($data_remessa[0]) .'/'. $data_remessa[1]; ?></option>
	                    <? $i++; } ?>
	                </select>
	                <br /><br />
                    
                    <? if ($_GET["geral"]!=1) { ?>
                    <label for="id_categoria">Equipamento:</label>
                    <select name="id_categoria" id="id_categoria" title="Categoria">
                        <option value="">- TODAS -</option> 
                        <?
                        
                        $result= mysql_query("select * from op_equipamentos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							order by tipo_equipamento asc, equipamento asc
							");
							
							
						
				        $i=0;
				        while ($rs= mysql_fetch_object($result)) {
				            if (($i%2)==0) $classe= "cor_sim";
				            else $classe= "cor_nao";
				        ?>
				        <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?=$rs->id_equipamento;?>"><?= $rs->equipamento; ?></option>
				        <? } ?>
                        
                        <?
                        /*$vetor= pega_manutencao_checklist_categorias('l');
                        $i=1;
                        while ($vetor[$i]) {
                        ?>
                        <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?=$i;?>"><?= $vetor[$i]; ?></option>
                        <? $i++; } */ ?>
                    </select>
                    <br />
                    
                    <label for="id_tecnico">Técnico:</label>
                    <select name="id_tecnico" id="id_tecnico" title="Técnico">
                        <option value="">- SELECIONE -</option>
						<?
                        $result_tec= mysql_query("select *
                                                    from  man_tecnicos
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
													order by num_tecnico asc
													") or die(mysql_error());
                        $i=0;
                        while ($rs_tec= mysql_fetch_object($result_tec)) {
							if ($rs_tec->id_funcionario==0) $nome_tecnico= $rs_tec->nome_tecnico;
							else $nome_tecnico= pega_funcionario($rs_tec->id_funcionario);
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tec->id_tecnico; ?>"><?= $rs_tec->num_tecnico .". ". $nome_tecnico; ?></option>
                        <? $i++; } ?>
                    </select>
                    <br />
                    <? } ?>
                    
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