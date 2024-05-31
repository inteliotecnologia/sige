<?
require_once("conexao.php");
if (pode_algum("iaprz", $_SESSION["permissao"])) {
?>

<input class="escondido" type="hidden" id="validacoes" value="tipo@vazio|nome_rz@vazio|id_cidade@vazio" />

<fieldset>
    <legend>Dados da pessoa jurídica</legend>
    
    <div class="parte50">
        <label for="cnpj">CNPJ:</label>
        <input title="CNPJ" name="cnpj" id="cnpj" onblur="verificaCnpj('<?=$acao;?>');" value="<?= $rs->cpf_cnpj; ?>" onkeypress="return formataCampo(form, this.name, '99.999.999/9999-99', event);" maxlength="18" />
        <br />
        
        <label>&nbsp;</label>
        <div id="cnpj_testa" class="lado_campo">
            <input title="CNPJ" name="passa_cnpj" id="passa_cnpj" type="hidden" class="escondido" value="" />
            <span id="span_cnpj_testa" class="vermelho">Não testado!</span>
        </div>
        <br />
        
        <script language="javascript">
            verificaCnpj('<?=$acao;?>');
        </script>
        
        <label for="nome_rz">* Razão Social:</label>
        <input title="Razão Social" name="nome_rz" id="nome_rz" value="<?= $rs->nome_rz; ?>" />
        <br />		
        
        <label for="apelido_fantasia">Nome Fantasia:</label>
        <input title="Nome Fantasia" name="apelido_fantasia" value="<?= $rs->apelido_fantasia; ?>" id="apelido_fantasia" />
        <br />
        
    </div>
    <div class="parte50">
        <label for="rg_ie">Inscr. estadual:</label>
        <input title="Inscrição Estadual" name="rg_ie" id="rg_ie" value="<?= $rs->rg_ie; ?>" />
        <br />
        
        <label for="contato">Contato:</label>
        <input title="Contato" name="contato" value="<?= $rs->contato; ?>" id="contato" />
        <br />
        
        <label for="codigo">Código de identificação:</label>
        <input title="Código de identificação" name="codigo" id="codigo" value="<?= $rs->codigo; ?>" />
        <br />
        
        <label for="sigla">Sigla:</label>
        <input title="Sigla" name="sigla" id="sigla" value="<?= $rs->sigla; ?>" />
        <br />
        
        <? /*
        <label for="data">Data de fundação:</label>
        <input title="Data de fundação" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" name="data" id="data" onkeyup="formataData(this);" value="<?= $rs->data2; ?>" maxlength="10" /> 
        <br /> 
        */ ?>
    </div>
</fieldset>

<? } ?>