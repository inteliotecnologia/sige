<?
require_once("conexao.php");
if (pode("t", $_SESSION["permissao"])) {
?>
<h2>Busca de contato</h2>

<form action="./?pagina=contatos/contato_listar" method="post" name="formContatoBuscar" id="formContatoBuscar">
    
    <input class="escondido" type="hidden" id="validacoes" value="" />
    <input class="escondido" type="hidden" name="geral" value="1" />
    
    <fieldset>
        <legend>Dados da busca</legend>
        
        <div class="parte50">
            <br />
            
            <label for="nome">Nome:</label>
            <input title="Nome" name="nome" id="nome" value="<?= $rs->nome; ?>" />
            <br />
            
            <? /*
            <label for="tipo_contato">Tipo:</label>
            <select name="tipo_contato" id="tipo_contato" title="Tipo de contato">
                <?
                $vetor= pega_tipo_contato('l');
                $i=1;
                while ($vetor[$i]) {
                ?>
                <option value="<?=$i;?>" <? if (($i%2)==0) echo "class=\"cor_sim\""; ?> <? if ($_GET["tipo_contato"]==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            */ ?>
            
            <label for="email">E-mail:</label>
            <input title="E-mail" name="email" id="email" value="<?= $rs->email; ?>" />
            <br />
            
        </div>
        <div class="parte50">
			
        </div>
    </fieldset>
                
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>