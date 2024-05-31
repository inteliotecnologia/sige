<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		if ($_SESSION["id_empresa"]!="")
			$str= "and   rh_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."' ";
	
		$result= mysql_query("select * from rh_cargos, rh_departamentos
								where rh_cargos.id_departamento = rh_departamentos.id_departamento
								and   rh_cargos.id_cargo = '". $_GET["id_cargo"] ."'
								". $str ."
								order by rh_departamentos.departamento asc,
										 rh_cargos.cargo asc
								");
							
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Cargos</h2>

<form action="<?= AJAX_FORM; ?>formCargo&amp;acao=<?= $acao; ?>" method="post" name="formCargo" id="formCargo" onsubmit="return ajaxForm('conteudo', 'formCargo', 'validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="id_departamento@vazio|cargo@vazio|val_salario@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_cargo" class="escondido" type="hidden" id="id_cargo" value="<?= $rs->id_cargo; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            
            <label for="id_departamento">* Departamento:</label>
            <div id="id_departamento_atualiza">
                <select name="id_departamento" id="id_departamento" title="Departamento">
                    <option value="">- DEPARTAMENTO -</option>
                    <?
					if ($acao=='i') {
						$id_empresa= $_SESSION["id_empresa"];
					}
					else {
						$id_empresa= $rs->id_empresa;
					}
					
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $id_empresa ."'
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <br />
            
            <label for="cargo">* Cargo:</label>
            <input title="Cargo" name="cargo" value="<?= $rs->cargo; ?>" id="cargo" />
            <br />
            
            <label for="val_salario">* Salário base:</label>
            <input title="Salário base" name="val_salario" value="<?= number_format($rs->val_salario, 2, ',', '.'); ?>" id="val_salario" onkeydown="formataValor(this,event);" />
            <br />
            
            <label for="val_salario_experiencia">* Salário de experiência:</label>
            <input title="Salário de experiência" name="val_salario_experiencia" value="<?= number_format($rs->val_salario_experiencia, 2, ',', '.'); ?>" id="val_salario_experiencia" onkeydown="formataValor(this,event);" />
            <br />
            
            <label for="descricao">Descrição:</label>
            <textarea title="Descrição" name="descricao" id="descricao"><?= $rs->descricao; ?></textarea>
            <br />
        </div>
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>