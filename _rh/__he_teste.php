<?
/*
$entrada[0]= "2008-12-01 23:55:00";
$saida[0]= "2008-12-02 06:05:00";

$entrada[1]= "2008-12-02 06:45:00";
$saida[1]= "2008-12-02 12:05:00";

$horas_trabalhadas= calcula_diurno_noturno($entrada, $saida);
                                    
$horas_diurnas= $horas_trabalhadas[0];
$horas_noturnas= $horas_trabalhadas[1];

echo calcula_total_horas($horas_diurnas) ."<br />";
echo calcula_total_horas($horas_noturnas) ."<br />";
*/
require_once("funcoes_espelho.php");

$retorno= pega_dados_rh(1, 0, 0, 120, "26/10/2008", "25/11/2008");

//echo $retorno;

$novo= explode("@", $retorno);


echo "<br /> 0- motivo (qdo for só um dia): ". ($novo[0]);
echo "<br /> 1- horas trabalhadas dirunas: ". calcula_total_horas($novo[1]);
echo "<br /> 2- horas trabalhadas noturnas: ". calcula_total_horas($novo[2]);
echo "<br /> 3- faltas: ". calcula_total_horas($novo[3]);
echo "<br /> 4- horas extras diurnas 60%: ". calcula_total_horas($novo[4]);
echo "<br /> 5- horas extras diurnas 100%: ". calcula_total_horas($novo[5]);
echo "<br /> 6- horas extras noturnas 60%: ". calcula_total_horas($novo[6]);
echo "<br /> 7- horas extras noturnas 100%: ". calcula_total_horas($novo[7]);


?>