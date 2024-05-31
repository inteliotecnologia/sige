<?
require_once("conexao.php");
if (pode_algum("iaprz", $_SESSION["permissao"])) {
?>

<input class="escondido" type="hidden" id="validacoes" value="tipo@vazio|nome_rz@vazio|id_cidade@vazio" />

<fieldset>
    <legend>Dados da pessoa física</legend>
    
    <div class="parte50">
        <label for="cpf">CPF:</label>
        <input title="CPF" name="cpf" id="cpf" onblur="verificaCpf('<?=$acao;?>');" value="<?= $rs->cpf_cnpj; ?>" onkeypress="return formataCampo(form, this.name, '999.999.999-99', event);" maxlength="14" />
        <br />
        
        <label>&nbsp;</label>
        <div id="cpf_testa" class="lado_campo">
            <input title="CPF" name="passa_cpf" id="passa_cpf" type="hidden" class="escondido" value="" />
            <span id="span_cpf_testa" class="vermelho">Não testado!</span>
        </div>
        <br />
        
        <script language="javascript">
            verificaCpf('<?=$acao;?>');
        </script>
        <br />
        
        <label for="nome_rz">* Nome:</label>
        <input title="Nome" name="nome_rz" id="nome_rz" value="<?= $rs->nome_rz; ?>" />
        <br />		
        
        <label for="apelido_fantasia">Apelido:</label>
        <input title="Apelido" name="apelido_fantasia" value="<?= $rs->apelido_fantasia; ?>" id="apelido_fantasia" />
        <br />
        
        <label for="sexo">Sexo:</label> 
        <select name="sexo" id="sexo" title="Sexo"> 
            <option selected="selected" value="">- SELECIONE -</option> 
            <option value="m" <? if ($rs->sexo=='m') echo "selected=\"selected\""; ?> class="cor_sim">Masculino</option>
            <option value="f" <? if ($rs->sexo=='f') echo "selected=\"selected\""; ?>>Feminino</option>
        </select>
        <br />
        
    </div>
    <div class="parte50">
        <label for="data_nasc">Data de nasc.:</label>
        <input title="Data de nascimento" name="data_nasc" id="data_nasc" onkeyup="formataData(this);" value="<?= desformata_data($rs->data); ?>" maxlength="10" /> 
        <br />
       
        <label for="rg_ie">RG:</label>
        <input title="RG" name="rg_ie" id="rg_ie" value="<?= $rs->rg_ie; ?>" />
        <br />
       
        <? /*
        <label for="data">Data de fundação:</label>
        <input title="Data de fundação" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" name="data" id="data" onkeyup="formataData(this);" value="<?= $rs->data2; ?>" maxlength="10" /> 
        <br /> 
        */ ?>
    </div>
</fieldset>

<? } ?>