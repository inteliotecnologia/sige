<?
require_once("conexao.php");
if (pode_algum("j", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  man_tecnicos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_tecnico = '". $_GET["id_tecnico"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Técnicos</h2>

<form action="<?= AJAX_FORM; ?>formManutencaoTecnico&amp;acao=<?= $acao; ?>" method="post" name="formManutencaoTecnico" id="formManutencaoTecnico" onsubmit="return validaFormNormal('validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="num_tecnico@vazio|tipo_tecnico@vazio|placa@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_tecnico" class="escondido" type="hidden" id="id_tecnico" value="<?= $rs->id_tecnico; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="num_tecnico">* Número:</label>
            <input title="Número" name="num_tecnico" value="<?= $rs->num_tecnico; ?>" id="num_tecnico" />
            <br />
            
            <label for="tipo_tecnico">Tipo:</label>
            <select name="tipo_tecnico" id="tipo_tecnico" title="Tipo" onchange="alteraTipoTecnico(this.value);">
                <option value="">-</option>
                <?
                $vetor= pega_tipo_tecnico('l');
                $i=1;
                while ($i<3) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?=$i;?>" <? if ($rs->tipo_tecnico==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <div id="tipo_tecnico_1" style="<? if (($acao=='e') && ($rs->tipo_tecnico==1)) echo ""; else echo "display:none;"; ?>" >
            	<label for="id_funcionario">Funcionário:</label>
                <select name="id_funcionario" id="id_funcionario" title="Funcionário">
                    <?
                    $result_fun= mysql_query("select *
                                                from  pessoas, rh_funcionarios, rh_carreiras
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
												and   rh_funcionarios.status_funcionario <> '2'
												and   rh_funcionarios.status_funcionario <> '0'
                                                and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                and   rh_carreiras.atual = '1'
                                                and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by pessoas.nome_rz asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>"<? if ($rs_fun->id_funcionario==$rs->id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
            </div>
            <div id="tipo_tecnico_2" style="<? if (($acao=='e') && ($rs->tipo_tecnico==2)) echo ""; else echo "display:none;"; ?>">
            	<label for="nome_tecnico">Nome:</label>
                <input title="Nome" name="nome_tecnico" value="<?= $rs->nome_tecnico; ?>" id="nome_tecnico" />
                <br />
            </div>
        </div>
        
    	<br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>
<? } ?>