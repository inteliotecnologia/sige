<?
require_once("conexao.php");
if (pode("rhm", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Relatório de funcionários</h2>

<div id="conteudo_interno">
	<div class="parte50">
        <fieldset>
            <legend>Ativos/inativos/geral</legend>
            
            <form action="index2.php?pagina=rh/funcionario_situacao_relatorio" method="post" target="_blank" name="formSituacao" id="formSituacao" onsubmit="return validaFormNormal('validacoes');">
                
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="opcao_relatorio" value="a" />
                
                <? /*
                <label for="id_departamento">Departamento:</label>
                <div id="id_departamento_atualiza">
                    <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnos();">
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
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TURNO -</option>
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
                */ ?>
                
                <? if ($_GET["ativo_periodo"]==1) { ?>
                <label for="periodo">Situação:</label>
                Ativos
                <input type="hidden" name="status_funcionario" class="escondido" value="3" />
                <br /><br />
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%m/%Y')) as data_batida2
                                                from rh_ponto order by data_batida desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_batida= explode('/', $rs_per->data_batida2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_batida2; ?>"><?= traduz_mes($data_batida[0]) .'/'. $data_batida[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="ordenacao">Ordenar por:</label>
                <select name="ordenacao" id="ordenacao" title="Ordenar por">	  		
                    <option value="rh_funcionarios.num_func">Matrícula (empresa)</option>
                    <option class="cor_sim" value="pessoas.nome_rz">Nome</option>
                    <option value="rh_carreiras.id_departamento asc, rh_carreiras.id_turno">Departamento</option>
                </select>
                <br />
                
                <? } else { ?>
                <label for="status_funcionario">Situação:</label>
                <select name="status_funcionario" id="status_funcionario" title="Situação">	  		
                    <option value="1" class="cor_sim">Ativos</option>
                    <option value="0">Inativos</option>
                    <option value="2" class="cor_sim">Geral</option>
                </select>
                <br />
                <? } ?>
                
                <? /*
                <fieldset>
                    <legend>Idade:</legend>
                    
                    <label for="p1">* Entre:</label>
                    <input name="p1" id="p1" class="tamanho25p" />
                    <br />
                    
                    <label for="p2">* E:</label>
                    <input name="p2" id="p2" class="tamanho25p" />
                    <br />
                
                </fieldset>
                
                
                <label for="sexo">Sexo:</label>
                <select name="sexo" id="sexo" title="Sexo">	  		
                    <option value="">-</option>
                    <option value="m">Masculino</option>
                    <option value="f" class="cor_sim">Feminino</option>
                </select>
                <br />
                
                
                
                <label>&nbsp;</label>
                <input type="checkbox" class="tamanho30" id="filhos" name="filhos" value="1" />
                <label for="filhos" class="alinhar_esquerda nao_negrito tamanho300">Tenha filhos</label>
                <br />
                
                <label>&nbsp;</label>
                <input type="checkbox" class="tamanho30" id="filhos18" name="filhos18" value="1" />
                <label for="filhos18" class="alinhar_esquerda nao_negrito tamanho300" onclick="checaCampo('filhos');">Filhos menores de 18 anos</label>
                <br />
                
                <label>&nbsp;</label>
                <input type="checkbox" class="tamanho30" id="mostrar_filhos" name="mostrar_filhos" value="1" />
                <label for="mostrar_filhos" class="alinhar_esquerda nao_negrito tamanho300">Mostrar dados dos filhos</label>
                <br />
                */ ?>
                
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br /><br />
            
            </form>
            
        </fieldset>
    </div>
    <br />
    
    <div class="parte50">
    	
        <fieldset>
            <legend>Relação de desligamentos por tipo/experiência</legend>
            
            <form action="index2.php?pagina=rh/funcionario_situacao_relatorio" method="post" target="_blank" name="formSituacao" id="formSituacao" onsubmit="return validaFormNormal('validacoes');">
            	
                <input class="escondido" type="hidden" name="opcao_relatorio" value="d" />
                
                <label for="periodo">* Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%m/%Y')) as data_batida2 from rh_ponto order by data_batida desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_batida= explode('/', $rs_per->data_batida2);
                        if ((date("d")>=25) && ($i==0)) {
                            $proximo_mes= date("m/Y", mktime(0, 0, 0, $data_batida[0]+1, 1, $data_batida[1]));
                            $data_batida2= explode('/', $proximo_mes);
                    ?>
                    <option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> value="<?= $proximo_mes; ?>"><?= traduz_mes($data_batida2[0]) .'/'. $data_batida2[1]; ?></option>
                    <? } ?>
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_batida2; ?>"><?= traduz_mes($data_batida[0]) .'/'. $data_batida[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br /><br />
                
            </form>
            
        </fieldset>
    </div>
    <div class="parte50">
    	
        <fieldset>
            <legend>Quantitativo de demissões por período</legend>
            
            <form action="index2.php?pagina=rh/funcionario_situacao_relatorio" method="post" target="_blank" name="formSituacao" id="formSituacao" onsubmit="return validaFormNormal('validacoes');">
            	
                <input class="escondido" type="hidden" name="opcao_relatorio" value="o" />
                
                <label for="periodo">* Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%Y')) as ano from rh_ponto order by data_batida desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->ano; ?>"><?= $rs_per->ano; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br /><br />
                
            </form>
            
        </fieldset>
    </div>
    <br />
    <? /*
    <div class="parte50">
    	
        <fieldset>
            <legend>Quantitativo de desligamentos por tipo/experiência</legend>
            
            <form action="index2.php?pagina=rh/funcionario_situacao_relatorio" method="post" target="_blank" name="formSituacao" id="formSituacao" onsubmit="return validaFormNormal('validacoes');">
            	
                <input class="escondido" type="hidden" name="opcao_relatorio" value="q" />
                
                <label for="periodo">* Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%Y')) as ano from rh_ponto order by data_batida desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->ano; ?>"><?= $rs_per->ano; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br /><br />
                
            </form>
            
        </fieldset>
    </div>
    <br />
    */ ?>
    <div class="parte50">
    	
        <fieldset>
            <legend>Quantitativo de admissões por período</legend>
            
            <form action="index2.php?pagina=rh/funcionario_situacao_relatorio" method="post" target="_blank" name="formSituacao" id="formSituacao" onsubmit="return validaFormNormal('validacoes');">
            	
                <input class="escondido" type="hidden" name="opcao_relatorio" value="m" />
                
                <label for="periodo">* Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%Y')) as ano from rh_ponto order by data_batida desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->ano; ?>"><?= $rs_per->ano; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="situacao_atual">* Situação atual:</label>
                <select name="situacao_atual" id="situacao_atual" title="Situação atual">	  		
                    <option value="1">Todos os funcionários admitidos</option>
                    <option class="cor_sim" value="2">Ativos atualmente</option>
                </select>
                <br />
                
                <br />
                
                <label>&nbsp;</label>
                <button type="submit" id="enviar">Enviar &raquo;</button>
                <br /><br />
                
            </form>
            
        </fieldset>
    </div>
</div>

<? } ?>