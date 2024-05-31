<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	$acao= 'i'; //$_GET["acao"];
	
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>
    
<div class="parte50">
    <fieldset>
        <legend>Insalubridade temporária</legend>
        <div id="conteudo_form">
			<? require_once("_rh/__insalubridade_form.php"); ?>
        </div>
    </fieldset>
</div>

<br />

<fieldset>
    <legend>Lançamentos</legend>
    	
        <table cellspacing="0" width="100%">
        <tr>
          <th width="9%" align="left" valign="bottom">C&oacute;d.</th>
          <th width="31%" align="left" valign="bottom">Funcionário</th>
          <th width="18%" align="left" valign="bottom">Data</th>
          <th width="14%" align="left" valign="bottom">Horário</th>
          <th width="15%" align="left" valign="bottom">Departamento</th>
          <th width="13%" align="left" valign="bottom">A&ccedil;&otilde;es</th>
        </tr>
        <?
	    $result_insalubridade= mysql_query("select * from rh_insalubridade
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_funcionario = '". $id_funcionario ."'
									order by  data_insalubridade desc
									") or die(mysql_error());
		$linhas_insalubridade= mysql_num_rows($result_insalubridade);
		
		$i=0;
		while ($rs_insalubridade= mysql_fetch_object($result_insalubridade)) {
        ?>
        <tr <? if ($i%2==0) echo "class=\"cor_sim\""; ?>>
          <td><?= $rs_insalubridade->id_insalubridade; ?></td>
            <td><?= pega_funcionario($rs_insalubridade->id_funcionario); ?></td>
            <td><?= desformata_data($rs_insalubridade->data_insalubridade); ?></td>
            <td><?= $rs_insalubridade->hora_inicio ." -> ". $rs_insalubridade->hora_fim; ?></td>
            <td><?= pega_departamento($rs_insalubridade->id_departamento); ?></td>
            <td>
                <a target="_blank" href="index2.php?pagina=rh/documento&amp;tipo=15&amp;id_insalubridade=<?= $rs_insalubridade->id_insalubridade; ?>">
                    <img border="0" src="images/ico_pdf.png" alt="Editar" />
                </a>
                |
                <a href="javascript:void(0);" onclick="ajaxLink('conteudo_form', 'carregaPagina&amp;pagina=rh/insalubridade_form&amp;acao=e&amp;id_insalubridade=<?= $rs_insalubridade->id_insalubridade; ?>');">
                    <img border="0" src="images/ico_lapis.png" alt="Editar" />
                </a>
                |
                <a href="javascript:ajaxLink('conteudo_interno', 'insalubridadeExcluir&amp;id_insalubridade=<?= $rs_insalubridade->id_insalubridade; ?>&amp;id_funcionario=<?= $rs_insalubridade->id_funcionario; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
                    <img border="0" src="images/ico_lixeira.png" alt="Status" />
                </a>
            </td>
        </tr>
        <?
			$i++;
        }
		?>
    </table>
    
</fieldset>

<? } ?>