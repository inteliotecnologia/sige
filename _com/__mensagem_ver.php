<?
require_once("conexao.php");
if (pode("n", $_SESSION["permissao"])) {
	if ($_GET["tipo"]=="e")
		$result= mysql_query("select *, com_mensagens.auth as auth2, DATE_FORMAT(com_mensagens.data_mensagem, '%d/%m/%Y') as data_mensagem
								from com_mensagens, pessoas
								where com_mensagens.de = pessoas.id_pessoa
								and   com_mensagens.id_mensagem= '". $_GET["id_mensagem"] ."'") or die(mysql_error());
	if ($_GET["tipo"]=="r") {
		$result= mysql_query("select *, com_mensagens.auth as auth2, DATE_FORMAT(com_mensagens.data_mensagem, '%d/%m/%Y') as data_mensagem
								from com_mensagens, pessoas
								where com_mensagens.para = pessoas.id_pessoa
								and   com_mensagens.id_mensagem= '". $_GET["id_mensagem"] ."'
								and   com_mensagens.para= '". pega_id_pessoa_do_usuario($_SESSION["id_usuario"]) ."' ") or die(mysql_error());
		
		if ($result) $result2= mysql_query("update com_mensagens set lida='1'
										   	where id_mensagem= '". $_GET["id_mensagem"] ."' ") or die(mysql_error());
	}
	
	$rs= mysql_fetch_object($result);
?>
<h2>Ver mensagem</h2>

<fieldset>
    <legend>Dados da mensagem</legend>
    
    <div class="parte70">
        
        <span class="dest">De:</span> <br />
        <?= pega_pessoa($rs->de); ?>
        <br /><br />
        
        <span class="dest">Para:</span> <br />
        <?= pega_pessoa($rs->para); ?>
        <br /><br />
        
        <span class="dest">Data de envio:</span> <br />
        <?= $rs->data_mensagem ." ". $rs->hora_mensagem; ?>
        <br /><br />
        
        <span class="dest">Assunto:</span> <br />
        <?= $rs->titulo; ?>
        <br /><br />
        
        <span class="dest">Mensagem:</span> <br />
        <?= $rs->mensagem; ?>
        <br /><br /><br />
        
        <button onclick="ajaxLink('conteudo', 'mensagemExcluir&amp;id_mensagem=<?= $rs->id_mensagem; ?>&amp;tipo=<?= $tipo; ?>');">excluir</button>
        
        <? if (($tipo=="r") && ($rs->de!=0)) { ?>
        <button onclick="window.location.href='./?pagina=com/mensagem&amp;acao=i&amp;id_pessoa=<?= $rs->de; ?>';">responder</button>
        <br />
        <? } ?>
        
    </div>
    <div class="parte30">
        
        
        <fieldset>
            <legend>Anexos:</legend>
            
            <?
			
			if ($rs->anexos=="")
				echo "Nenhum anexo nesta mensagem.";
			else {
				$anexos= explode(" |+| ", $rs->anexos);
				$num_anexos= count($anexos);
				
				echo "<ul class=\"recuo1\">";
				
				for ($i=0; $i<$num_anexos; $i++) {
					$j= $i+1;
					
					$nome_arquivo= trim(CAMINHO ."mensagem_". $rs->auth2 ."_". $anexos[$i]);
					
					//echo $nome_arquivo;
					
					if (file_exists($nome_arquivo))
						echo "<li><a href=\"". $nome_arquivo ."\" target=\"_blank\">". $anexos[$i] ."</a></li>";
				}
				
				echo "</ul>";
			}
			?>
        </fieldset>
  </div>
</fieldset>
            
<? } ?>