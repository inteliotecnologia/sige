<?
require_once("conexao.php");
if (pode("i", $_SESSION["permissao"])) {
	
	if ($_GET["tipo_nota"]!="") $tipo_nota= $_GET["tipo_nota"];
	if ($_POST["tipo_nota"]!="") $tipo_nota= $_POST["tipo_nota"];
	
	$acao= $_GET["acao"];
	if ($acao=='e') {
		if ($_GET["id_nota"]!="") $id_nota= $_GET["id_nota"];
		if ($_POST["id_nota"]!="") $id_nota= $_POST["id_nota"];
	
		$result= mysql_query("select *
								from  fi_notas
								where id_nota = '". $id_nota ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		$tipo_nota= $rs->tipo_nota;
		$tit1= "Edição";
	}
	else
		$tit1= "Cadastro";
	
	if ($tipo_nota=='p') {
		$tit2= "à pagar";
		$txt_cedente= "Fornecedor";
		$tipo_cedente= "f";
	}
	else {
		$tit2= "à receber";
		$txt_cedente= "Cliente";
		$tipo_cedente= "c";
	}
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<? if ($acao=='i') { ?>
<h2><?= $tit1; ?> de duplicata <?= $tit2; ?></h2>
<? } ?>

<? if (($acao=='e') && ($rs->status_nota==1)) { ?>

<fieldset>
    <legend>Dados da duplicata</legend>
    
    <div class="parte50">
        <label><?= $txt_cedente; ?>:</label>
        <?= pega_pessoa($rs->id_cedente);?>
        <br />
        
        <label>Número da nota:</label>
        <?= $rs->num_nota; ?>
        <br />
        
        <label>Data de emissão:</label>
        <?= desformata_data($rs->data_emissao); ?>
        <br />
    </div>
    
</fieldset>

<? } else { ?>
<form action="<?= AJAX_FORM; ?>formNota&amp;acao=<?= $acao; ?>" method="post" name="formNota" id="formNota" onsubmit="return ajaxForm('conteudo', 'formNota', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="tipo_nota@vazio|id_cedente@vazio|num_nota@vazio|data_emissao@data|data_vencimento@data|valor_total@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_nota" class="escondido" type="hidden" id="id_nota" value="<?= $rs->id_nota; ?>" />
    <? } ?>
    <input name="tipo_nota" class="escondido" type="hidden" id="tipo_nota" value="<?= $tipo_nota; ?>" />
    
    <fieldset>
        <legend>Dados da duplicata</legend>
        
        <div class="parte50">
            <label for="id_cedente">* <?= $txt_cedente; ?>:</label>
            <select name="id_cedente" id="id_cedente" title="<?= $txt_cedente; ?>">
            	<? if ($acao=='i') { ?>
                <option value="">- TODOS -</option>
                <? } ?>
                
                <?
                $result_ced= mysql_query("select *, pessoas.id_pessoa as id_cedente from pessoas, pessoas_tipos
                                            where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                            and   pessoas_tipos.tipo_pessoa = '$tipo_cedente'
                                            order by
                                            pessoas.apelido_fantasia asc
                                            ") or die(mysql_error());
                $i=0;
                while ($rs_ced = mysql_fetch_object($result_ced)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cedente; ?>" <? if ($rs_ced->id_cedente==$rs->id_cedente) echo "selected=\"selected\""; ?>><?= $rs_ced->apelido_fantasia; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="num_nota">* Número da nota:</label>
            <input title="Número da nota" name="num_nota" value="<?= $rs->num_nota; ?>" id="num_nota" />
            <br />
            
            <?
			if ($acao=='i') $data_emissao= date("d/m/Y");
			else $data_emissao= desformata_data($rs->data_emissao);
			?>
            <label for="data_emissao">* Data de emissão:</label>
            <input id="data_emissao" name="data_emissao" class="tamanho25p" value="<?= $data_emissao; ?>" title="Data de emissão" onkeyup="formataData(this);" maxlength="10" />
            <br />
            
            
            <?
			if ($acao=='i') {
				if ($acao=='i') $data_vencimento= "";
				else $data_vencimento= desformata_data($rs->data_vencimento);
				?>
				<label for="data_vencimento">* Data de vencimento:</label>
				<input id="data_vencimento" name="data_vencimento" class="tamanho25p" onblur="checaMesmaDataNota();" value="<?= $data_vencimento; ?>" title="Data de vencimento" onkeyup="formataData(this);" maxlength="10" />
				<br />
            <? } ?>
        </div>
        
        <? if ($acao=='i') { ?>
        <div class="parte50">
        	
            <? /* if ($tipo_nota=="r") { ?>
            <label for="valor">* Valor da nota:</label>
            <? if ($rs->valor=="") $valor= ""; else $valor= fnum($rs->valor); ?>
            <input id="valor" name="valor" class="tamanho15p" value="<?= $valor; ?>" onkeydown="formataValor(this,event);" title="Valor" />
            <br />
            
            <label for="imposto_iss_percent">* Imposto ISS (%):</label>
            <? if ($rs->imposto_iss_percent=="") $imposto_iss_percent= ""; else $imposto_iss_percent= fnum($rs->imposto_iss_percent); ?>
            <input id="imposto_iss_percent" name="imposto_iss_percent" class="tamanho15p espaco_dir" value="<?= $imposto_iss_percent; ?>" onkeydown="formataValor(this,event);" title="Imposto ISS %" />
            
            <label for="imposto_iss_valor">* Imposto ISS (R$):</label>
            <? if ($rs->imposto_iss_valor=="") $imposto_iss_valor= ""; else $imposto_iss_valor= fnum($rs->imposto_iss_valor); ?>
            <input id="imposto_iss_valor" name="imposto_iss_valor" class="tamanho15p espaco_dir" value="<?= $imposto_iss_valor; ?>" onkeydown="formataValor(this,event);" title="Imposto ISS R$" />
            <br />
            
            <label for="imposto_outros_percent">* Outros impostos (%):</label>
            <? if ($rs->imposto_outros_percent=="") $imposto_outros_percent= ""; else $imposto_outros_percent= fnum($rs->imposto_outros_percent); ?>
            <input id="imposto_outros_percent" name="imposto_outros_percent" class="tamanho15p espaco_dir" value="<?= $imposto_outros_percent; ?>" onkeydown="formataValor(this,event);" title="Outros impostos %" />
            
            <label for="imposto_outros_valor">* Outros impostos (R$):</label>
            <? if ($rs->imposto_outros_valor=="") $imposto_outros_valor= ""; else $imposto_outros_valor= fnum($rs->imposto_outros_valor); ?>
            <input id="imposto_outros_valor" name="imposto_outros_valor" class="tamanho15p espaco_dir" value="<?= $imposto_outros_valor; ?>" onkeydown="formataValor(this,event);" title="Outros impostos R$" />
            <br />
            <? } */ ?>
            
            <label for="valor_total">* Valor total:</label>
            <? if ($rs->valor_total=="") $valor_total= ""; else $valor_total= fnum($rs->valor_total); ?>
            <input id="valor_total" name="valor_total" class="tamanho15p" value="<?= $valor_total; ?>" onkeydown="formataValor(this,event);" title="Valor total" />
            <br />
            
            <div id="div_nota_pagar" class="nao_mostra">
                <label>&nbsp;</label>
                <input type="checkbox" class="tamanho20" name="pagar" id="pagar" value="1" />
                <label for="pagar" class="nao_negrito alinhar_esquerda">Marcar esta duplicata como paga integralmente.</label>
            </div>
        </div>
        <? } ?>
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } } ?>