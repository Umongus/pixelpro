<?php
namespace AppBundle\Funciones;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\ParteTrabajo;

class TratArray {

public function comparaProducto ($partesTrabajo, $fincas, $cosecha){
  $horas = 0;
  $peonadas = 0;
  $j = 0;
  $totHoras = 0;
  $totPeonadas = 0;
if ($partesTrabajo != NULL) {
  foreach ($fincas as $finca) {
    foreach ($partesTrabajo as $parte) {
      if ($finca->getNombre() == $parte->getFinca()->getNombre() && $cosecha == $parte->getProducto()->getNombre()) {
        if ($parte->getTipo()->getNombre() == 'Hora') {
          $horas = $horas + $parte->getCantidad();
          $totHoras = $totHoras + $parte->getCantidad();
        }else{
          $peonadas = $peonadas + $parte->getCantidad();
          $totPeonadas = $totPeonadas + $parte->getCantidad();
        }
      }
    }
    $ACfinca[$finca->getNombre()][0] = $peonadas;
    $ACfinca[$finca->getNombre()][1] = $horas;
    $peonadas = 0;
    $horas = 0;
  }
}else {
  foreach ($fincas as $finca) {
    $ACfinca[$finca->getNombre()][0] = $peonadas;
    $ACfinca[$finca->getNombre()][1] = $horas;
  }
}
$ACfinca['TOTAL'][0]=$totPeonadas;
$ACfinca['TOTAL'][1]=$totHoras;
return $ACfinca;
}

  //Dadas una finca y un año, devuelve un array con la distribucion de gastos de ese año
public function comparafinca ($partesTrabajo, $trabajos, $cosecha){
  $horas = 0;
  $peonadas = 0;
  $j = 0;
  $totHoras = 0;
  $totPeonadas = 0;
if ($partesTrabajo != NULL) {
  foreach ($trabajos as $trabajo) {
    foreach ($partesTrabajo as $parte) {
      if ($trabajo->getNombre() == $parte->getTrabajo()->getNombre() && $cosecha == $parte->getProducto()->getNombre()) {
        if ($parte->getTipo()->getNombre() == 'Hora') {
          $horas = $horas + $parte->getCantidad();
          $totHoras = $totHoras + $parte->getCantidad();
        }else{
          $peonadas = $peonadas + $parte->getCantidad();
          $totPeonadas = $totPeonadas + $parte->getCantidad();
        }
      }
    }
    $ACfinca[$trabajo->getNombre()][0] = $peonadas;
    $ACfinca[$trabajo->getNombre()][1] = $horas;
    $peonadas = 0;
    $horas = 0;
  }
}else {
  foreach ($trabajos as $trabajo) {
    $ACfinca[$trabajo->getNombre()][0] = $peonadas;
    $ACfinca[$trabajo->getNombre()][1] = $horas;
  }
}
$ACfinca['TOTAL'][0]=$totPeonadas;
$ACfinca['TOTAL'][1]=$totHoras;
return $ACfinca;
}


public function dameArrayPeonadas($trabajadores, $partes, $mes, $ano, $tipo, $altas, $precio){
  $fecha = new \DateTime($ano.'-'.$mes.'-1');
  for ($x=1; $x < 36 ; $x++) {//NUEVO
    $totales[$x] = 0; //NUEVO
  }//NUEVO

for ($h=0; $h < count($trabajadores); $h++) {
  $trabajador = $trabajadores[$h];
  //$arrayPeonadas[$h][0]=$trabajadores[$h];
  $contador = 0;
    for ($i=1; $i < 32 ; $i++) {
      $fecha->setDate($ano, $mes, $i);
      $arrayPeonadas[$trabajador][$i]=0;
      //$arrayPeonadas[$trabajador][31]=0;
      for ($j=0; $j < count($partes); $j++) {
        if ($fecha == $partes[$j]->getFecha() && $trabajador == $partes[$j]->getTrabajador()->getNombre() && $partes[$j]->getTipo()->getNombre()==$tipo) {
          $arrayPeonadas[$trabajador][$i]=$arrayPeonadas[$trabajador][$i]+$partes[$j]->getCantidad();
          //$arrayPeonadas[$trabajador][31]=$arrayPeonadas[$trabajador][31]+$partes[$j]->getCantidad();
          $contador = $contador + $partes[$j]->getCantidad();
          $totales[$i] = $totales[$i] + $partes[$j]->getCantidad();//NUEVO
        }
      }
      if ($i == 31) {
        $arrayPeonadas[$trabajador][32]=$contador;
        $totales[32]=$totales[32]+$contador;//NUEVO
        if ($tipo == 'Peonada') {
          $arrayPeonadas[$trabajador][33]=0;
          $arrayPeonadas[$trabajador][34]=$arrayPeonadas[$trabajador][32]*$precio[0]->getValor();
          $arrayPeonadas[$trabajador][35]=0;

          //if (count($altas) == 0) {  }

          $totales[34]=$totales[34]+$arrayPeonadas[$trabajador][34];

          for ($g=0; $g < count($altas) ; $g++) {
            if ($trabajador == $altas[$g]->getNombre()->getNombre()) {
              $arrayPeonadas[$trabajador][33]=$altas[$g]->getCantidad();
              $totales[33]=$totales[33]+$altas[$g]->getCantidad();//NUEVO
              $peo = $arrayPeonadas[$trabajador][32];
              $alt = $arrayPeonadas[$trabajador][33];
              $CS = ($alt*$precio[0]->getValor())-($alt*3);
              $CC =($peo - $alt)*$precio[0]->getValor();
              $arrayPeonadas[$trabajador][34]=$CC;
              $arrayPeonadas[$trabajador][35]=$CS;
              //$totales[34]=$totales[34]+$CC;//NUEVO
              $toPeo = $totales[32];
              $toAlt = $totales[33];
              $totales[34]= ($toPeo-$toAlt)*$precio[0]->getValor();
              $totales[35]=$totales[35]+$CS;//NUEVO

            }
          }
        }else {
          //$arrayPeonadas[$trabajador][32]=$contador;
          //$totales[32]=$totales[32]+$contador;
          $arrayPeonadas[$trabajador][33]=$contador*$precio[0]->getValor();
          $totales[33]=$totales[33]+$contador*$precio[0]->getValor();
        }
      }
    }
  }
    $arrayPeonadas['TOTAL'] = $totales;
    return $arrayPeonadas;
  }

public function dameArrayTrabajadores($partes){
    $arrayTrabajadores = array();
    $arrayTrabajadores[0]= $partes[0]->getTrabajador()->getNombre();


    $j=1;
    for ($i=1; $i < count($partes) ; $i++) {
      $nombreEnCurso = $partes[$i]->getTrabajador()->getNombre();
      $Contabilizado = false;

      for ($h=0; $h < count($arrayTrabajadores); $h++) {
        if ($nombreEnCurso == $arrayTrabajadores[$h]) {
          $Contabilizado = true;
        }
      }

      if ($Contabilizado == false) {
        $arrayTrabajadores[$j] = $nombreEnCurso;

        $j++;
      }
    }
    return $arrayTrabajadores;
  }

public function dameElIntervalo($mes, $ano){
    switch ($mes) {
      case 'Todos':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 1;
        $arrayIntervalo[2] = $ano+1;
        $arrayIntervalo[3] = 1;
         break;
      case 'Enero':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 1;
        $arrayIntervalo[2] = $ano;
        $arrayIntervalo[3] = 2;
          break;
      case 'Febrero':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 2;
        $arrayIntervalo[2] = $ano;
        $arrayIntervalo[3] = 3;
          break;
      case 'Marzo':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 3;
        $arrayIntervalo[2] = $ano;
        $arrayIntervalo[3] = 4;
          break;
      case 'Abril':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 4;
        $arrayIntervalo[2] = $ano;
        $arrayIntervalo[3] = 5;
          break;
      case 'Mayo':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 5;
        $arrayIntervalo[2] = $ano;
        $arrayIntervalo[3] = 6;
          break;
      case 'Junio':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 6;
        $arrayIntervalo[2] = $ano;
        $arrayIntervalo[3] = 7;
          break;
      case 'Julio':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 7;
        $arrayIntervalo[2] = $ano;
        $arrayIntervalo[3] = 8;
          break;
      case 'Agostò':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 8;
        $arrayIntervalo[2] = $ano;
        $arrayIntervalo[3] = 9;
          break;
      case 'Septiembre':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 9;
        $arrayIntervalo[2] = $ano;
        $arrayIntervalo[3] = 10;
          break;
      case 'Octubre':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 10;
        $arrayIntervalo[2] = $ano;
        $arrayIntervalo[3] = 11;
          break;
      case 'Noviembre':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 11;
        $arrayIntervalo[2] = $ano;
        $arrayIntervalo[3] = 12;
          break;
      case 'Diciembre':
        $arrayIntervalo[0] = $ano;
        $arrayIntervalo[1] = 12;
        $arrayIntervalo[2] = $ano+1;
        $arrayIntervalo[3] = 1;
          break;
    }
    return $arrayIntervalo;
  }

public function calcula($aceituna2017)
  {
    $nuevaBusqueda = count($aceituna2017);
    //$tipo = $aceituna2017[0]->getTipo();
    $arrayResultado =array();
    $arrayResultado['Peonada'] = 0;
    $arrayResultado['Hora'] = 0;
    $horas = 0;
    $peonadas = 0;
    for ($i=0; $i < $nuevaBusqueda ; $i++) {
       $tipo = $aceituna2017[$i]->getTipo()->getNombre();
       if ($tipo == 'Peonada') {
         $arrayResultado['Peonada'] = $arrayResultado['Peonada'] + $aceituna2017[$i]->getCantidad();
       }else {
         $arrayResultado['Hora'] = $arrayResultado['Hora'] + $aceituna2017[$i]->getCantidad();
       }
     }
     return $arrayResultado;
  }

public function calculaCuadrillas ($parteTrabajo)
  {
    $arrayCuadrilla = array();
    $cuadrilla = 0;
    $elementos = count($parteTrabajo);
    for ($i=0; $i < $elementos ; $i++)
    {
        if ($cuadrilla <> $parteTrabajo[$i]->getCuadrilla()) {
          $cuadrilla = $parteTrabajo[$i]->getCuadrilla();
          $arrayCuadrilla[$cuadrilla] = $cuadrilla;
        }
    }
    return $arrayCuadrilla;
  }

  public function calculaTipos ($arrayCuadrilla, $parteTrabajos, $tipo)
  {
    $array = array();
    $array = $arrayCuadrilla;

    foreach ($arrayCuadrilla as $cuadrilla) {
      $array[$cuadrilla] = 0;
      foreach ($parteTrabajos as $parte) {
        if (($cuadrilla == $parte->getCuadrilla()) && ($parte->getTipo()->getNombre() == $tipo)) {
            $array[$cuadrilla] = $array[$cuadrilla] + $parte->getCantidad();
        }
      }
    }

    return $array;
  }

}
?>
