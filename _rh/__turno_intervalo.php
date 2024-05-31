<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	
	if ($_GET["acao"]!="") $acao= $_GET["acao"];
	if ($_POST["acao"]!="") $acao= $_POST["acao"];
	
	if ($_GET["id_turno"]!="") $id_turno= $_GET["id_turno"];
	else $id_turno= $_POST["id_turno"];
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<div class="parte50">
    <fieldset>
        <legend>Dados de intervalo</legend>
        
        <div id="intervalo_form_atualiza">
	        <? require_once("_rh/__turno_intervalo_form.php"); ?>
        </div>
    
    </fieldset>
</div>
<div class="parte50">
    <fieldset>
    <legend>Intervalos cadastrados para este turno</legend>
    	
        <table cellspacing="0" width="100%">
        <tr>
          <th width="18%" align="left" valign="bottom">C&oacute;d.</th>
          <th width="67%" align="left" valign="bottom">Intervalo</th>
          <th width="15%" align="left" valign="bottom">A&ccedil;&otilde;es</th>
      </tr>
        <?
		$result= mysql_query("select * from rh_turnos_intervalos
									where id_turno = '". $id_turno ."'
									order by intervalo asc
									");
		
		while ($rs= mysql_fetch_object($result)) {
        ?>
        <tr>
            <td><?= $rs->id_intervalo; ?></td>
            <td><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=rh/turno_intervalo_horarios&amp;id_intervalo=<?= $rs->id_intervalo; ?>');"><?= $rs->intervalo; ?></a></td>
            <td>
            <a href="javascript:void(0);" onclick="ajaxLink('intervalo_form_atualiza', 'carregaPagina&amp;pagina=rh/turno_intervalo_form&amp;id_intervalo=<?= $rs->id_intervalo; ?>&amp;acao=e');">
				<img border="0" src="images/ico_lapis.png" alt="Editar" />
            </a>
            |
            <a href="javascript:ajaxLink('conteudo_interno', 'intervaloExcluir&amp;id_intervalo=<?= $rs->id_intervalo; ?>&amp;id_turno=<?= $id_turno; ?>');" onclick="return confirm('Tem certeza que deseja excluir este intervalo?');">
				<img border="0" src="images/ico_lixeira.png" alt="Excluir" /></a>
            </td>
        </tr>
        <? } ?>
    </table>
    
    </fieldset>
</div>
<? } ?>