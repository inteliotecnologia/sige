<?
require_once("conexao.php");
if (pode("c3", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	
	if ( (pode("c", $_SESSION["permissao"])) && ($_GET["tipo_documento"]==1) ) $tipo_documento=1;
	else $tipo_documento=2;
	
	if ($_GET["tipo"]!="") $tipo= $_GET["tipo"];
	if ($_POST["tipo"]!="") $tipo= $_POST["tipo"];
	
	if ($_GET["metodo"]!="") $metodo= $_GET["metodo"];
	if ($_POST["metodo"]!="") $metodo= $_POST["metodo"];
	
	if ($acao=='e') {
		$result= mysql_query("select *
								from  dc_documentos_emissoes
								where id_documento_emissao = '". $_GET["id_documento_emissao"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$tipo= $rs->tipo;
		$metodo= $rs->metodo;
	} else unset($rs);
?>
<h2>Emissão de documento - <?= pega_tipo_documento_emissao($tipo, $tipo_documento); ?></h2>

<form action="<?= AJAX_FORM; ?>formDocumentoEmissao&amp;acao=<?= $acao; ?>" method="post" name="formDocumentoEmissao" id="formDocumentoEmissao" onsubmit="return validaFormNormal('validacoes', false, true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="data_emissao@data|assunto@vazio" />
    
	<? if ($acao=='e') { ?>
    <input name="id_documento_emissao" class="escondido" type="hidden" id="id_documento_emissao" value="<?= $rs->id_documento_emissao; ?>" />
    <? } ?>
    <input name="tipo" class="escondido" type="hidden" id="tipo" value="<?= $tipo; ?>" />
    <input name="tipo_documento" class="escondido" type="hidden" id="tipo_documento" value="<?= $tipo_documento; ?>" />
    
    <?
    if ($acao=='i') $metodo= $_GET["metodo"];
	else $metodo= $rs->metodo;
	?>
    <input name="metodo" class="escondido" type="hidden" id="metodo" value="<?= $metodo; ?>" />
    
    <fieldset>
        <legend><?= pega_tipo_documento_emissao($tipo, $tipo_documento); ?></legend>
        
        <div class="parte50">
        	
            <label for="id_empresa">* Empresa:</label>
            <select name="id_empresa" id="id_empresa" title="Empresa">
                <? if ($acao=="i") { ?>
                <option value="">---</option>
                <? } ?>
                
                <?
                $result_emp= mysql_query("select * from pessoas, pessoas_tipos, empresas
                                            where pessoas.id_pessoa = empresas.id_pessoa
                                            and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                            and   pessoas_tipos.tipo_pessoa = 'a'
                                            order by 
                                            pessoas.nome_rz asc");
                $i=0;
                while ($rs_emp = mysql_fetch_object($result_emp)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_emp->id_empresa; ?>"<? if ($rs_emp->id_empresa==$rs->id_empresa) echo "selected=\"selected\""; ?>><?= $rs_emp->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <? if ($tipo!=7) { ?>
            
            <? if ($tipo!=8) { ?>
            <label for="de">* De:</label>
            <input title="De" name="de" value="<?= $rs->de; ?>" id="de" />
            <br />
            
            <label for="para">* Destinatário:</label>
            <input title="Para" name="para" value="<?= $rs->para; ?>" id="para" />
            <br />
            <? if ($tipo!=4) { ?>
            <label for="cc">* CC:</label>
            <input title="CC" name="cc" value="<?= $rs->cc; ?>" id="cc" />
            <br />
            <? } else { ?>
            <label for="para2">* Destinatário (2ª linha):</label>
            <input title="Para" name="para2" value="<?= $rs->para2; ?>" id="para2" />
            <br />
            <? } ?>
            
            <? } ?>
            
            <? if ($tipo==6) { ?>
            <label for="fax">* Fax:</label>
            <input title="Fax" name="fax" value="<?= $rs->fax; ?>" id="fax" />
            <br />
            
            <label for="telefone">* Telefone:</label>
            <input title="Telefone" name="telefone" value="<?= $rs->telefone; ?>" id="telefone" />
            <br />
            <? } ?>
            
            <? } else { ?>
            	<label>Método:</label>
				<?= pega_metodo_documento_assinado($metodo); ?>
                <br /><br />

	            <? if ($metodo==3) { ?>                    
                <label for="metodo_num">Número de assinaturas:</label>
                <input name="metodo_num" id="metodo_num" class="tamanho25p" />
                <br />
                
                <label for="relator">Relator:</label>
                <input title="Relator" name="relator" value="<?= $rs->relator; ?>" id="relator" />
                <br />
            	
                <label for="turno">Turno:</label>
                <input title="Turno" name="turno" value="<?= $rs->turno; ?>" id="turno" />
                <br />
                
                <? } ?>
                
                <? if ($metodo==1) { ?>
                <label for="id_departamento">Departamento:</label>
                <div id="id_departamento_atualiza">
                    <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSoh();">
                        <option value="">- TODOS -</option>
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
                        <option value="">- TODOS -</option>
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
                <? } ?>
                
                <? if (($metodo==1) || ($metodo==2)) { ?>
                <label for="situacao">Situação:</label>
                <select name="situacao" id="situacao" title="Situação">	  		
                    <option value="1" class="cor_sim">Somente funcionários ativos presentes</option>
                    <option value="2">Todos os funcionários ativos</option>
                </select>
                <br />
                <? } ?>
            <? } ?>
            
            <label for="data_emissao">* Data:</label>
            <input id="data_emissao" name="data_emissao" class="tamanho25p" value="<?= desformata_data($rs->data_emissao); ?>" title="Data da emissão" onkeyup="formataData(this);" maxlength="10" />
            <br />
            
            <?
            //ofício
			if ($tipo==4) {
			?>
            <label for="cidade_uf">Cidade/UF:</label>
            <input id="cidade_uf" name="cidade_uf" value="<?= $rs->cidade_uf; ?>" />
            <br />
            <? } ?>
            
            <?
            //pauta de reunião
			if ($tipo==8) {
			?>
            <label for="hora_inicio">Hora início:</label>
            <input title="Hora início" name="hora_inicio" value="<?= substr($rs->hora_inicio, 0, 5); ?>" id="hora_inicio" onkeyup="formataHora(this);" maxlength="5" />
            <br />
            
            <label for="hora_termino">Hora término:</label>
            <input title="Hora término" name="hora_termino" value="<?= substr($rs->hora_termino, 0, 5); ?>" id="hora_termino" onkeyup="formataHora(this);" maxlength="5" />
            <br />
            
            <label for="dirigente">Dirigente:</label>
            <input title="Dirigente" name="dirigente" value="<?= $rs->dirigente; ?>" id="dirigente" />
            <br />
            
            <label for="participantes">Participantes:</label>
            <input title="Participantes" name="participantes" value="<?= $rs->participantes; ?>" id="participantes" />
            <br />
            
            <label for="relator">Relator:</label>
            <input title="Relator" name="relator" value="<?= $rs->relator; ?>" id="relator" />
            <br />
            <? } ?>
            
            <label for="assunto">* Assunto:</label>
            <input title="Ref." name="assunto" value="<?= $rs->assunto; ?>" id="assunto" />
            <br />
            
            <? if ($acao=="e") { ?>
            <label for="num">Número:</label>
            <input title="Número" name="num" value="<?= $rs->num; ?>" id="num" />
            <br />
            <? } ?>

      </div>
      
      <div class="parte50 textarea_grande">
      	
        <label class="tamanho100" for="mensagem">* Texto:</label>
        <textarea name="mensagem" id="mensagem" title="Texto"><?= $rs->mensagem; ?></textarea>
        <br />
        
      </div>
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>