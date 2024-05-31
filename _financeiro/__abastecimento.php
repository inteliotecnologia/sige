<?
require_once("conexao.php");
if (pode("u", $_SESSION["permissao"])) {
	if ($acao=="") $acao= $_GET["acao"];
	if ($acao=='e') {
		if ($_GET["id_abastecimento"]!="") $id_abastecimento= $_GET["id_abastecimento"];
		if ($_POST["id_abastecimento"]!="") $id_abastecimento= $_POST["id_abastecimento"];
		
		$result= mysql_query("select * from fi_abastecimentos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_abastecimento = '". $id_abastecimento ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
	}
?>
<h2>Controle de abastecimentos</h2>

<?
if (($acao=='e') && (mysql_num_rows($result)==0)) {
	echo "Requisição não encontrada!";
	die();
}
?>

<form action="<?= AJAX_FORM; ?>formAbastecimento&amp;acao=<?= $acao; ?>" method="post" name="formAbastecimento" id="formAbastecimento" onsubmit="return ajaxForm('conteudo', 'formAbastecimento', 'validacoes', true);">
    
    <?
    if ($acao=='e') {
		$validacao_adicional= "tipo_comb@vazio|valor_litro@vazio|litros@vazio|valor_total@vazio";
	?>
    <input name="id_abastecimento" class="escondido" type="hidden" id="id_abastecimento" value="<?= $rs->id_abastecimento; ?>" />
    <? } ?>
    <input class="escondido" type="hidden" id="validacoes" value="data@data|id_veiculo@vazio|id_funcionario@vazio|<?=$validacao_adicional;?>id_usuario_at@vazio" />
    
    <fieldset>
        <legend>Formulário</legend>
        
        <div class="parte50">
            
            <?
			if ($acao=='i') $data= date("d/m/Y");
			else $data= desformata_data($rs->data);
			?>
            <label for="data">* Data:</label>
            <input id="data" name="data" class="tamanho25p" value="<?= $data; ?>" title="Data" onkeyup="formataData(this);" maxlength="10" />
            
			<?
			/*
			if ($acao=='i') $hora= date("H:i:s");
			else $hora= $rs->hora;
			?>
            <input id="hora" name="hora" <? if ($acao=='i') echo "disabled=\"disabled\""; ?> class="tamanho25p" value="<?= $hora; ?>" title="Hora da lavagem"  onkeyup="formataHora(this);" maxlength="8" />
            */ ?>
            <br />
            
            <label for="id_veiculo">* Veículo:</label>
            <select name="id_veiculo" id="id_veiculo" title="Veículo">
				<option value="">---</option>
				<?
                $result_vei= mysql_query("select *
                                            from  op_veiculos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   status_veiculo = '1'
                                            order by veiculo asc
                                            ") or die(mysql_error());
                $i=0;
                while ($rs_vei= mysql_fetch_object($result_vei)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_vei->id_veiculo; ?>"<? if ($rs_vei->id_veiculo==$rs->id_veiculo) echo "selected=\"selected\""; ?>><?= $rs_vei->codigo .") ". $rs_vei->veiculo ." (". $rs_vei->placa .")"; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_funcionario">* Motorista:</label>
            <select name="id_funcionario" id="id_funcionario" title="Motorista">
            	<option value="">---</option>
                <?
                $result_fun= mysql_query("select *
                                            from  pessoas, rh_funcionarios, rh_carreiras
                                            where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                            and   pessoas.tipo = 'f'
                                            and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                            and   rh_carreiras.atual = '1'
											and   rh_funcionarios.status_funcionario <> '2'
											and   rh_funcionarios.status_funcionario <> '0'
                                            and   
											(
											 	(rh_carreiras.id_departamento = '1' and rh_carreiras.id_cargo = '10')
												or
												(rh_carreiras.id_departamento = '3')
												or
												(rh_carreiras.id_departamento = '5')
												or
												(rh_carreiras.id_departamento = '6')
												or
												(rh_carreiras.id_departamento = '8')
												or
												(rh_carreiras.id_departamento = '12')
												or
												(rh_carreiras.id_departamento = '13')
												or
												(rh_carreiras.id_departamento = '14')
												or
												(rh_carreiras.id_departamento = '15')
												or
												(rh_carreiras.id_departamento = '16')
												or
												(rh_carreiras.id_departamento = '17')
												or
												(rh_carreiras.id_departamento = '19')
												or
												(rh_carreiras.id_departamento = '20')
												or
												(rh_carreiras.id_departamento = '22')
												or
												(rh_carreiras.id_departamento = '23')
												or
												(rh_carreiras.id_funcionario = '14')
												or
												(rh_carreiras.id_funcionario = '21')
												
											)
											
											/* and   rh_funcionarios.afastado <> '1' */
											
                                            and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1' or rh_funcionarios.status_funcionario = '0')
                                            order by pessoas.nome_rz asc
                                            ") or die(mysql_error());
                $i=0;
                while ($rs_fun= mysql_fetch_object($result_fun)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <? /*
            <label for="id_departamento">* Departamento:</label>
            <select name="id_departamento" id="id_departamento" title="Departamento">
                <option value="">---</option>
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
            <br />
            */ ?>
            
            <label for="obs1">Observações:</label>
            <textarea title="Observações" name="obs1" id="obs1"><?= $rs->obs; ?></textarea>
            <br />
            
        </div>
        <div class="parte50">
			<? //if ($acao=="e") { ?>
            <label for="tipo_comb">* Tipo:</label>
            <select name="tipo_comb" id="tipo_comb" title="Tipo de combustível">
                <option value="">---</option>
                <?
                $vetor= pega_tipo_combustivel('l');
                $i=1;
                while ($vetor[$i]) {
                ?>
                <option value="<?=$i;?>" <? if (($i%2)==0) echo "class=\"cor_sim\""; ?> <? if ($i==$rs->tipo_comb) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="valor_litro">* Valor por litro:</label>
            <? if ($acao=='e') { if ($rs->valor_litro=="0") $valor_litro= ""; else $valor_litro= fnum($rs->valor_litro); } ?>
            <input id="valor_litro" name="valor_litro" class="espaco_dir tamanho25p" value="<?= $valor_litro; ?>" title="Valor por litro"  onblur="calculaValorTotalAbastecimento();" />
            <br />
            
            <label for="litros">* Litros:</label>
            <? if ($acao=='e') { if ($rs->litros=="0") $litros= ""; else $litros= fnum($rs->litros); } ?>
            <input id="litros" name="litros" class="espaco_dir tamanho25p" value="<?= $litros; ?>" onkeydown="formataValor(this,event);" title="Valor por litro" onblur="calculaValorTotalAbastecimento();" />
            <br />
            <? //} ?>
            
            <label for="valor_total">* Valor total:</label>
            <? if ($acao=='e') { if ($rs->valor_total=="0") $valor_total= ""; else $valor_total= fnum($rs->valor_total); } ?>
            <input id="valor_total" name="valor_total" class="espaco_dir tamanho25p" value="<?= $valor_total; ?>" onkeydown="formataValor(this,event);" title="Valor por litro" />
            <br />
            
            <label for="id_usuario_at">* Autorizado por:</label>
            <select name="id_usuario_at" id="id_usuario_at" title="Autorizado por">
                <option value="">---</option>
				<?
                $result_fun= mysql_query("select *
                                            from  pessoas, rh_funcionarios, rh_carreiras
                                            where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                            and   pessoas.tipo = 'f'
                                            and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                            and   rh_carreiras.atual = '1'
                                            and   
											(
											 	(rh_carreiras.id_departamento = '1' and rh_carreiras.id_cargo = '10')
												or
												(rh_carreiras.id_departamento = '3')
												or
												(rh_carreiras.id_departamento = '5')
												or
												(rh_carreiras.id_departamento = '6')
												or
												(rh_carreiras.id_departamento = '8')
												or
												(rh_carreiras.id_departamento = '12')
												or
												(rh_carreiras.id_departamento = '13')
												or
												(rh_carreiras.id_departamento = '14')
												or
												(rh_carreiras.id_departamento = '15')
												or
												(rh_carreiras.id_departamento = '16')
												or
												(rh_carreiras.id_departamento = '17')
												or
												(rh_carreiras.id_departamento = '19')
												or
												(rh_carreiras.id_departamento = '20')
												or
												(rh_carreiras.id_departamento = '22')
												or
												(rh_carreiras.id_departamento = '23')
												or
												(rh_carreiras.id_funcionario = '14')
												or
												(rh_carreiras.id_funcionario = '21')
												
											)
											/* and   rh_funcionarios.afastado <> '1' */
											
                                            and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1' or rh_funcionarios.status_funcionario = '0')
                                            order by pessoas.nome_rz asc
                                            ") or die(mysql_error());
                $i=0;
                while ($rs_fun= mysql_fetch_object($result_fun)) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_usuario_at) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
        </div>
    </fieldset>
                
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>

<script language="javascript" type="text/javascript">
	daFoco("data");
</script>

<? } ?>