<?
require_once("conexao.php");
if (pode_algum("rv", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
	
	$acao='i';
	if ($_GET["acao"]!="") $acao= $_GET["acao"];
	if ($_POST["acao"]!="") $acao= $_POST["acao"];
	
	if ($acao=='e') {
		if (($_GET["data1"]!="") && ($_GET["data2"]!="")) {
			$data1f= $_GET["data1"];
			$data2f= $_GET["data2"];
		}
		else {
			$data1f= $_POST["data1"];
			$data2f= $_POST["data2"];
		}
		
		if ($_GET["id_horario"]!="") $id_horario= $_GET["id_horario"];
		if ($_POST["id_horario"]!="") $id_horario= $_POST["id_horario"];
		
		$result= mysql_query("select *, DATE_FORMAT(data_batida, '%d/%m/%Y') as data_batida2,
								DATE_FORMAT(vale_dia, '%d/%m/%Y') as vale_dia2
								from rh_ponto
								where id_horario = '". $id_horario ."'
								") or die(mysql_error());
								
		if (mysql_num_rows($result)==1) {
			$rs= mysql_fetch_object($result);
			$id_funcionario= $rs->id_funcionario;
		} else die();
			
	}
?>
<fieldset>
	<legend>Formulário de alteração de horário</legend>

    <form action="<?= AJAX_FORM; ?>formHorario&amp;acao=<?=$acao;?>" method="post" name="formHorario" id="formHorario" onsubmit="return ajaxForm('conteudo_interno', 'formHorario', 'validacoes', true);">
        
        <input class="escondido" type="hidden" id="validacoes" value="id_funcionario@vazio|data_batida@data|vale_dia@data|hora@vazio" />
        <input type="hidden" title="Funcionário" class="escondido" id="id_funcionario" name="id_funcionario" value="<?= $id_funcionario; ?>" />
        
        <? if ($acao=="e") { ?>
        <input type="hidden" class="escondido" id="id_horario" name="id_horario" value="<?= $rs->id_horario; ?>" />
        <? } ?>
        
        <input class="escondido" type="hidden" name="data1" value="<?= $data1f; ?>" />
        <input class="escondido" type="hidden" name="data2" value="<?= $data2f; ?>" />
        
        <label>Tipo:</label>
        <? if ($acao=='i') { ?>
        <input type="radio" class="tamanho30" name="tipo" id="tipo_1" value="1" checked="checked" />
        <label for="tipo_1" class="tamanho50 nao_negrito alinhar_esquerda">Entrada</label>
        
        <input type="radio" class="tamanho30" name="tipo" id="tipo_0" value="0" />
        <label for="tipo_0" class="tamanho50 nao_negrito alinhar_esquerda">Saída</label>
        <?
        }
		else echo entrada_saida($rs->tipo);
		?>
        <br />
        
        <label for="data_batida">Data batida:</label>
        <input <? if ($acao=='i') { ?> onblur="atribuiValor('vale_dia', this.value);" <? } ?> name="data_batida" id="data_batida" class="tamanho20p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?= $rs->data_batida2; ?>" title="Data batida" />
        <div class="flutuar_esquerda espaco_dir">vale para</div>
        <input name="vale_dia" id="vale_dia" class="tamanho20p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?= $rs->vale_dia2; ?>" title="Vale p/ dia" />
        <br />
        
        <label for="hora">Hora:</label>
        <input name="hora" id="hora" class="tamanho20p espaco_dir" onkeyup="formataHora(this);" value="<?= $rs->hora; ?>" maxlength="8" title="Hora" />
        <br />
        
        <label for="id_motivo">Motivo:</label>
        <select name="id_motivo" id="id_motivo" title="Motivo">	  		
            <?
            $i=0;
            $result_mot= mysql_query("select * from rh_motivos where tipo_motivo = 'p' order by motivo asc ");
            while ($rs_mot= mysql_fetch_object($result_mot)) {
            ?>
            <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_mot->id_motivo; ?>"><?= $rs_mot->motivo; ?></option>
            <? $i++; } ?>
        </select>
        <br />
        
        <br /><br />
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
        
    </form>
</fieldset>
<? } ?>