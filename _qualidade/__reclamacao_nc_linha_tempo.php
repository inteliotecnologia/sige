<?
require_once("conexao.php");
if (pode("12o", $_SESSION["permissao"])) {
?>
<h2>Linha do tempo de registro de reclamações/não conformidades</h2>

<?
$result= mysql_query("select * from qual_reclamacoes_andamento, com_livro
                        where com_livro.id_livro= qual_reclamacoes_andamento.id_livro
                        and   qual_reclamacoes_andamento.id_empresa = '". $_SESSION["id_empresa"] ."'
                        order by qual_reclamacoes_andamento.id_reclamacao_andamento desc
                        ") or die(mysql_error());

$num=30;
$total = mysql_num_rows($result);
$num_paginas = ceil($total/$num);
if ($_GET["num_pagina"]=="") $num_pagina= 0;
else $num_pagina= $_GET["num_pagina"];
$inicio = $num_pagina*$num;

$result= mysql_query("select *, DATE_FORMAT(data_livro, '%Y') as ano, qual_reclamacoes_andamento.id_usuario as id_usuario_atualizacao
						from qual_reclamacoes_andamento, com_livro
                        where com_livro.id_livro= qual_reclamacoes_andamento.id_livro
                        and   qual_reclamacoes_andamento.id_empresa = '". $_SESSION["id_empresa"] ."'
                        order by qual_reclamacoes_andamento.id_reclamacao_andamento desc
                        limit $inicio, $num
                        ") or die(mysql_error());


?>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
    <tr>
        <th width="9%" align="left">Data/hora</th>
        <th width="20%" align="left">Reclamante/Reclamação</th>
        <th width="13%" align="left">Atualizado por</th>
        <th width="12%" align="left">Tipo de ação</th>
        <th width="38%" align="left" class="unsortable">Mensagem</th>
        <th width="8%" class="unsortable">Ações</th>
    </tr>

<?
$i=0;
while ($rs= mysql_fetch_object($result)) {
?>
    <tr>
        <td valign="top">
            <span class="escondido"><?= $rs->data_livro ." ". $rs->hora_livro; ?></span><?= desformata_data($rs->data_livro) ."<br />". $rs->hora_livro; ?>
            <br /><br />
            <strong>Nº <?= fnumi($rs->num_livro)."/".$rs->ano; ?></strong>
        </td>
        <td valign="top">
            <?
            if ($rs->tipo_de=="f") {
                if (($rs->id_outro_departamento!="") && ($rs->id_outro_departamento!="0")) $id_departamento= $rs->id_outro_departamento;	
                else $id_departamento= pega_dado_carreira("id_departamento", $rs->de);	
                
                echo pega_funcionario($rs->de);
                
                $id_deixou= $rs->de;
                $id_agora= $_SESSION["id_funcionario_sessao"];
            }
            else {
                $id_departamento= $rs->de;
                $id_deixou= $rs->de;
                $id_agora= $_SESSION["id_departamento_sessao"];
            }
            ?>
            
            <br /><br />

        	<span class="menor"><strong><?= pega_motivo($rs->id_motivo); ?></strong></span><br />
            
            <span class="menor"><a href="javascript:void(0);" class="contexto">Veja aqui
            <span>
				<?= $rs->mensagem; ?>
                <? if (($rs->id_motivo==34) && ($rs->reclamacao_id_cliente!=0)) { ?>
                <br />
                <br />
                <span class="menor"><strong>CLIENTE:</strong> <?= pega_pessoa($rs->reclamacao_id_cliente); ?></span> <br />
                <span class="menor caps"><strong>CAUSA:</strong> <?= pega_reclamacao_causa($rs->id_causa); ?></span> <br />
                <? } ?>
			</span></a></span>
        </td>
        <td valign="top">
            <?= pega_nome_pelo_id_usuario($rs->id_usuario_atualizacao); ?>
        </td>
        <td valign="top">
            <?= pega_situacao_reclamacao($rs->id_situacao); ?>
        </td>
        <td valign="top">
            <strong><?= desformata_data($rs->data_andamento) ." ". $rs->hora_andamento; ?></strong><br />
			<?= $rs->obs; ?>
        </td>
        <td align="center" valign="top">
        	<a id="link_edita<?=$i;?>" href="./?pagina=qualidade/reclamacao&amp;acao=e&amp;id_livro=<?= $rs->id_livro; ?>&amp;num_pagina=<?= $num_pagina; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
        </td>
    </tr>
    <? $i++; } ?>
</table>        
<br /><br />

<?
if ($num_paginas > 1) {
	echo "<br /><strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link = $i + 1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=qualidade/reclamacao_nc_linha_tempo&amp;motivo=". $_GET["motivo"] ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
}

$_SESSION["reclamacao_num_pagina"]= "";
$_SESSION["reclamacao_ancora"]= "";
$_SESSION["reclamacao_origem"]= "";
?>

<? } ?>