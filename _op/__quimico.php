<?
require_once("conexao.php");
if (pode("ps", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_suja_quimicos_trocas
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_troca_quimico = '". $_GET["id_troca_quimico"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<h2>Área suja - Troca de químicos</h2>

<form action="<?= AJAX_FORM; ?>formTrocaQuimico&amp;acao=<?= $acao; ?>" method="post" name="formQuimico" id="formQuimico" onsubmit="return validaFormNormal('validacoes', false, 1);">
    
    <input class="escondido" type="hidden" id="validacoes" value="data_troca@data|hora_troca@vazio|num_galao@vazio|id_quimico@vazio|qtde@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_troca_quimico" class="escondido" type="hidden" id="id_troca_quimico" value="<?= $rs->id_troca_quimico; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50 gigante">
            
            <?
			if ($acao=='i') $data_troca= date("d/m/Y");
			else $data_troca= desformata_data($rs->data_troca);
			?>
            <label for="data_troca">* Data/hora:</label>
            <input id="data_troca" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="data_troca" class="tamanho25p" value="<?= $data_troca; ?>" title="Data da troca" onkeyup="formataData(this);" maxlength="10" />
            
			<?
			if ($acao=='i') $hora_troca= date("H:i:s");
			else $hora_troca= $rs->hora_troca;
			?>
            <input id="hora_troca" <? //if ($acao=='i') echo "disabled=\"disabled\""; ?> name="hora_troca" class="tamanho25p" value="<?= $hora_troca; ?>" title="Hora da troca"  onkeyup="formataHora(this);" maxlength="8" />
            <br />
            
            <label for="id_funcionario">* Responsável:</label>
            <select id="id_funcionario" name="id_funcionario">
                <?
                $j=0;
                $result_fun= mysql_query("select *
                                            from  pessoas, rh_funcionarios, rh_carreiras
                                            where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                            and   pessoas.tipo = 'f'
                                            and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                            and   rh_carreiras.atual = '1'
                                            and   rh_carreiras.id_departamento = '2'
                                            /* and   rh_carreiras.id_cargo = '4' */
                                            and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   rh_funcionarios.status_funcionario = '1'
                                            order by pessoas.nome_rz asc
                                            ") or die(mysql_error());
                while ($rs_fun= mysql_fetch_object($result_fun)) {
                ?>
                <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_funcionario) echo "selected=\"selected\""; ?>><?= primeira_palavra($rs_fun->nome_rz); ?></option>
                <? $j++; } ?>
            </select>
            <br />
            
        </div>
        <div class="parte50 gigante">
            
            <label for="num_galao">* Nº galão:</label>
            <input id="num_galao" name="num_galao" value="<?= $rs->num_galao; ?>" title="Número do galão" class="tamanho25p"/>
            <br />
            
            <label for="id_quimico">* Químico:</label>
            <select id="id_quimico" name="id_quimico">
            	<?
				$j=1;
				$vetor= pega_quimico('l');
				
				while ($vetor[$j]) {
				?>
                <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $j; ?>" <? if ($j==$rs->id_quimico) echo "selected=\"selected\""; ?>><?= $vetor[$j]; ?></option>
                <? $j++; } ?>
            </select>
            <br />
            
            <label for="qtde">* Quantidade:</label>
            <? if ($acao=='i') $qtde= ""; else $qtde= fnum($rs->qtde); ?>
            <input id="qtde" name="qtde" class="espaco_dir tamanho25p" value="<?= $qtde; ?>" onkeydown="formataValor(this,event);" title="Quantidade" /> litros
            <br />
            
        </div>
    	<br /><br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>

<script language="javascript" type="text/javascript">
	daFoco("data_troca");
</script>
<? } ?>