<?
require_once("conexao.php");
if (pode("rhv", $_SESSION["permissao"])) {
?>

<h2>Relatório de turnos/horários</h2>

<div class="parte50">
    <fieldset>
        <legend>Turnover</legend>
            
        <form action="index2.php?pagina=rh/turno_resumido_relatorio" target="_blank" method="post">
            
            <input class="escondido" type="hidden" id="validacoes" value="" />
            <input class="escondido" type="hidden" name="tipo_relatorio" value="t" />
            
            <label for="id_departamento">Departamento:</label>
            <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '1');">
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
            <br />
            
            <label for="id_turno">Turno:</label>
            <div id="id_turno_atualiza_1">
                <select name="id_turno" id="id_turno" title="Turno">	  		
                    <option value="">- TURNO -</option>
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
            <br />
            
            <label>&nbsp;</label>
            <button type="submit" id="enviar">Enviar &raquo;</button>
            <br />
        
        </form>
    </fieldset>
</div>

</fieldset>

<? } ?>