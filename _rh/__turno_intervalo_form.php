<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	if ($_GET["acao"]!="") $acao= $_GET["acao"];
	if ($_POST["acao"]!="") $acao= $_POST["acao"];
	
	if ($acao=='e') {
		$result= mysql_query("select * from rh_turnos_intervalos
								where id_intervalo = '". $_GET["id_intervalo"] ."'
								");
		if (mysql_num_rows($result)==1) {
			$rs= mysql_fetch_object($result);
			$id_turno= $rs->id_turno;
		}
	}
	$id_departamento= pega_id_departamento_do_id_turno($id_turno);
?>
    <form action="<?= AJAX_FORM; ?>formIntervaloTurno&amp;acao=<?= $acao; ?>" method="post" name="formIntervaloTurno" id="formIntervaloTurno" onsubmit="return ajaxForm('conteudo_interno', 'formIntervaloTurno', 'validacoes', true);">
    
        <input class="escondido" type="hidden" id="validacoes" value="id_turno@vazio|intervalo@vazio" />
        <input class="escondido" type="hidden" id="id_turno" name="id_turno" value="<?= $id_turno; ?>" />
        <? if ($acao=='e') { ?>
        <input class="escondido" type="hidden" id="id_intervalo" name="id_intervalo" value="<?= $rs->id_intervalo; ?>" />
        <? } ?>
        
        <label>Departamento:</label>
		<?= pega_departamento($id_departamento); ?>
        <br />
        
        <label>Turno:</label>
        <?= pega_turno($id_turno); ?>
        <br />
        
        <label for="intervalo">* Intervalo:</label>
        <input title="Intervalo" name="intervalo" value="<?= $rs->intervalo; ?>" id="intervalo" />
        <br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar</button>
        </center>
    </form>
<? } ?>