<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	if ($_GET["id_vt"]!="") $id_vt= $_GET["id_vt"];
	if ($_POST["id_vt"]!="") $id_vt= $_POST["id_vt"];
	
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
	if ($acao=="") $acao= $_GET["acao"];

	if ($acao=="e") {
		$result= mysql_query("select * from rh_vt
							 	where id_vt = '". $id_vt ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$id_funcionario= $rs->id_funcionario;
	}
?>
<form action="<?= AJAX_FORM; ?>formVT&amp;acao=<?= $acao; ?>" method="post" name="formVT" id="formVT" onsubmit="return ajaxForm('conteudo_interno', 'formVT', 'validacoes', true);">

    <input class="escondido" type="hidden" id="validacoes" value="id_funcionario@vazio|trajeto@vazio|id_linha@vazio" />
    <input name="id_funcionario" class="escondido" type="hidden" id="id_funcionario" value="<?= $id_funcionario; ?>" />
    
    <? if ($acao=="e") { ?>
    <input name="id_vt" class="escondido" type="hidden" id="id_vt" value="<?= $rs->id_vt; ?>" />
    <? } ?>
       
	<label for="id_funcionario">Funcionário:</label>
    <select name="id_funcionario" id="id_funcionario" title="Funcionário">
        <?
        $result_fun= mysql_query("select *
                                    from  pessoas, rh_funcionarios, rh_carreiras
                                    where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                    and   pessoas.tipo = 'f'
                                    and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
                                    and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                    and   rh_carreiras.atual = '1'
                                    and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                    order by pessoas.nome_rz asc
                                    ") or die(mysql_error());
        $i=0;
        while ($rs_fun= mysql_fetch_object($result_fun)) {
        ?>
        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
        <? $i++; } ?>
    </select>
    <br />
    
    <label for="trajeto">Trajeto:</label>
    <select name="trajeto" id="trajeto" title="Trajeto">
    	<? if ($acao=="i") { ?>
        <option value="" selected="selected">-</option>
        <option value="2">Ida/volta</option>
        <? } ?>
        
        <option class="cor_sim" value="1" <? if ($rs->trajeto==1) echo "selected=\"selected\""; ?>>Ida</option>
        <option value="0" <? if (($acao=="e") && ($rs->trajeto==0)) echo "selected=\"selected\""; ?>>Volta</option>
    </select>
    <br />
    
    <label for="id_linha">Linha:</label>
    <select name="id_linha" id="id_linha" title="Linha">
        <?
        $result_linha= mysql_query("select *
									from  rh_vt_linhas
									where rh_vt_linhas.id_empresa = '". $_SESSION["id_empresa"] ."'
									order by rh_vt_linhas.linha asc
									") or die(mysql_error());
        $i=0;
        while ($rs_linha= mysql_fetch_object($result_linha)) {
        ?>
        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_linha->id_linha; ?>"<? if ($rs_linha->id_linha==$rs->id_linha) echo "selected=\"selected\""; ?>><?= $rs_linha->linha; ?></option>
        <? $i++; } ?>
    </select>
    <br /><br />
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>
<? } ?>