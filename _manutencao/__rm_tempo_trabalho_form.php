<?
require_once("conexao.php");
if (pode_algum("kj", $_SESSION["permissao"])) {
	if ($_GET["id_rm_servico"]!="") $id_rm_servico= $_GET["id_rm_servico"];
	if ($_POST["id_rm_servico"]!="") $id_rm_servico= $_POST["id_rm_servico"];
	
	$acao='e';
	
	if ($acao=='e') {
		
		$result= mysql_query("select *
								from  man_rms_servicos, man_rms
								where man_rms_servicos.id_rm_servico = '". $id_rm_servico ."'
								and   man_rms_servicos.id_rm = man_rms.id_rm
								and   man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
								
		if (mysql_num_rows($result)==1) {
			$rs= mysql_fetch_object($result);
		} else die();
			
	}
?>

<a href="javascript:void(0);" onclick="fechaDiv('tela_tempo_servico');" class="fechar">x</a>                
<h2>Tempo de servi&ccedil;o</h2>

<div id="conteudo_flutuante">
    
    <form action="<?= AJAX_FORM; ?>formRMTempoTrabalho&amp;acao=<?=$acao;?>" method="post" name="formRMTempoTrabalho" id="formRMTempoTrabalho" onsubmit="return ajaxForm('conteudo_flutuante', 'formRMTempoTrabalho', 'validacoes_tempo_trabalho');">
        
        <input class="escondido" type="hidden" id="validacoes_tempo_trabalho" value="id_rm_servico@vazio|data_inicio@data|hora_inicio@vazio|data_fim@data|hora_fim@vazio" />
        
        <input type="hidden" title="Tempo de serviço" class="escondido" id="id_rm_servico" name="id_rm_servico" value="<?= $rs->id_rm_servico; ?>" />
        <input type="hidden" class="escondido" id="id_rm" name="id_rm" value="<?= $rs->id_rm; ?>" />
        
        <label>RM:</label>
        <?= $rs->num_rm; ?>
        <br /><br />
        
        <label for="data_inicio">Data/hora de início:</label>
        <input name="data_inicio" id="data_inicio"  onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?= desformata_data($rs->data_inicio); ?>" title="Data de início" class="tamanho80 espaco_dir" />
        <input name="hora_inicio" id="hora_inicio" onkeyup="formataHora(this);" maxlength="8" value="<?= $rs->hora_inicio; ?>" title="Hora de início" class="tamanho80" />
        <br />
        
        <label for="data_fim">Data de fim:</label>
        <input name="data_fim" id="data_fim" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?= desformata_data($rs->data_fim); ?>" title="Data de fim" class="tamanho80 espaco_dir" />
        <input name="hora_fim" id="hora_fim" onkeyup="formataHora(this);" maxlength="8" value="<?= $rs->hora_fim; ?>" title="Hora de fim" class="tamanho80" />
        <br />
        <br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
        
    </form>
</div>
<? } ?>