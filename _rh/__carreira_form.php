<?
require_once("conexao.php");
if (pode("rv", $_SESSION["permissao"])) {
	if ($_GET["id_carreira"]!="") $id_carreira= $_GET["id_carreira"];
	if ($_POST["id_carreira"]!="") $id_carreira= $_POST["id_carreira"];
	
	$mostra_carreira_formulario= true;
	
	if ($acao=="") $acao= $_GET["acao"];

	if ($acao=="e") {
		$result= mysql_query("select * from rh_carreiras
							 	where id_carreira = '". $id_carreira ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
	else {
		$result_atual= mysql_query("select * from rh_carreiras
									where id_funcionario = '". $_GET["id_funcionario"] ."'
									and   atual = '1'
									");
		$rs_atual= mysql_fetch_object($result_atual);
		
		if ($rs_atual->id_acao_carreira==2) $mostra_carreira_formulario= false;
	}
	
	if ($id_funcionario=="") $id_funcionario= $rs->id_funcionario;
	
	if (!$mostra_carreira_formulario)
		echo "Funcionário inativo, não é possível alterar a carreira.";
	else {
?>
<form action="<?= AJAX_FORM; ?>formCarreira&amp;acao=<?= $acao; ?>" method="post" name="formCarreira" id="formCarreira" onsubmit="return ajaxForm('conteudo_interno', 'formCarreira', 'validacoes', true);">

    <input class="escondido" type="hidden" id="validacoes" value="id_funcionario@vazio|data@data|id_acao_carreira@vazio" />
    <input name="id_funcionario" class="escondido" type="hidden" id="id_funcionario" value="<?= $id_funcionario; ?>" />
    
    <? if ($acao=="e") { ?>
    <input name="id_carreira" class="escondido" type="hidden" id="id_carreira" value="<?= $rs->id_carreira; ?>" />
    <? } ?>
       
    <?
    if ($acao=="i") $data= date("d/m/Y");
	else $data= desformata_data($rs->data);
	?>
    <label for="data">* Data:</label>
    <input name="data" id="data" onkeyup="formataData(this);" value="<?= $data; ?>" title="Data">
    <br />
    
    <label for="id_acao_carreira">* Ação:</label>
    <select name="id_acao_carreira" id="id_acao_carreira" title="Ação" onchange="trataAcaoCarreira(this.value);">
        <? if ($acao=="i") { ?>
        <option value="">---</option>
        <? } ?>
        
        <?
        if (($acao=='e') && ($rs->id_acao_carreira==1)) $i=1;
		else $i=2;
		$vetor= pega_acao_carreira('l');
        
        while ($vetor[$i]) {
        ?>
        <option value="<?=$i;?>" <? if (($i%2)==0) echo "class=\"cor_sim\""; ?> <? if ($rs->id_acao_carreira==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
        <? $i++; } ?>
    </select>
    <br />
    
    
    <div id="carreira_dados_desligamento" <? if (($acao=='i') || ($rs->id_acao_carreira!=2)) { ?> class="escondido" <? } ?>>
        <label for="id_detalhe_carreira">Motivo:</label>
        <select title="Motivo" name="id_detalhe_carreira" id="id_detalhe_carreira">
        	<option value="">- SELECIONE -</option> 
            <?
            $vetor= pega_detalhe_carreira_desligamento('l');
            $i=1;
            while ($vetor[$i]) {
            ?>
            <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?=$i;?>" <? if ($rs->id_detalhe_carreira==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
            <? $i++; } ?>
        </select>
        <br />
    </div>
    
    <div id="carreira_dados" <? if (($acao=='e') && ($rs->id_acao_carreira==2)) { ?> class="escondido" <? } ?>>
        <label for="id_departamento">* Departamento:</label>
        <div id="id_departamento_atualiza">
            <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnos(); alteraCargos();">
                <? //if ($acao=="i") { ?>
                <option value="">- SELECIONE -</option> 
                <? //} ?>
                
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
        </div>
        <br />
        
        <label for="id_cargo">* Cargo:</label>
        <div id="id_cargo_atualiza">
            <select name="id_cargo" id="id_cargo" title="Cargo">	  		
                <? //if ($acao=="i") { ?>
                <option value="">- SELECIONE -</option> 
                <? //} ?>
                <?
                $result_car= mysql_query("select * from rh_cargos
                                            where id_departamento = '". $rs->id_departamento ."'
                                             ");
                $i=0;
                while ($rs_car = mysql_fetch_object($result_car)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_car->id_cargo; ?>" <? if ($rs_car->id_cargo==$rs->id_cargo) echo "selected=\"selected\""; ?>><?= $rs_car->cargo; ?></option>
                <? $i++; } ?>
            </select>
        </div>
        <br />
        
        <label for="id_turno">* Turno:</label>
        <div id="id_turno_atualiza">
            <select name="id_turno" id="id_turno" title="Turno" onchange="alteraIntervalos();">	  		
                <? if ($acao=="i") { ?>
                <option value="">- SELECIONE -</option> 
                <? } ?>
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
        
        <label for="id_intervalo">* Intervalo:</label>
        <div id="id_intervalo_atualiza">
            <select name="id_intervalo" id="id_intervalo" title="Intervalo">	  		
                <? //if ($acao=="i") { ?>
                <option value="">- SELECIONE -</option> 
                <? //} ?>
                <?
                $result_int= mysql_query("select * from rh_turnos_intervalos
                                            where id_turno = '". $rs->id_turno ."'
                                            order by intervalo asc 
                                             ");
                $i=0;
                while ($rs_int = mysql_fetch_object($result_int)) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_int->id_intervalo; ?>" <? if ($rs_int->id_intervalo==$rs->id_intervalo) echo "selected=\"selected\""; ?>><?= $rs_int->intervalo; ?></option>
                <? $i++; } ?>
            </select>
        </div>
        <br />
            
        <label for="turnante">* Turnante:</label>
        <input type="radio" class="tamanho15" name="turnante" id="turnante_1" value="1" <? if ($rs->turnante=="1") echo "checked=\"checked\""; ?> /> <label class="tamanho50 nao_negrito alinhar_esquerda" for="turnante_1">Sim</label>
        <input type="radio" class="tamanho15" name="turnante" id="turnante_2" value="2" <? if (($rs->turnante=="2") || ($acao=='i')) echo "checked=\"checked\""; ?> /> <label class="tamanho50 nao_negrito alinhar_esquerda" for="turnante_2">Não</label>
        <br />
        
        <label for="insalubridade">* Insalubridade:</label>
        <select name="insalubridade" id="insalubridade" title="Insalubridade">	  		
            <option value="0">N/A</option>
            <option value="10" class="cor_sim" <? if ($rs->insalubridade==10) echo "selected=\"selected\""; ?>>10%</option>
            <option value="20" <? if ($rs->insalubridade==20) echo "selected=\"selected\""; ?>>20%</option>
            <option value="40" class="cor_sim" <? if ($rs->insalubridade==40) echo "selected=\"selected\""; ?>>40%</option>
        </select>
        <br />
    </div>
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>
<? } } ?>