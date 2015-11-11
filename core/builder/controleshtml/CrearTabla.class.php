<?php

require_once ("core/builder/HtmlBase.class.php");

class CrearTabla extends HtmlBase {

    function tablaReporte($datos, $estilo=' ') {
        $this->cadenaHTML = "";
        $this->setAtributos($datos);
        $this->campoSeguro();
        $this->cadenaHTML = array('');

        $encabezado = array();

        foreach ($datos[0] as $key => $values) {
            if (!is_numeric($key)) {
                $encabezado[$key] = '<th>' . strtoupper(str_replace("_", " ", $key)) . '</th>';
            }
        }

        $encabezadof = implode($encabezado);

        if ($estilo = true) {
            $estilo = 'style="border-spacing:10px;width:80%;font-size:12px"';
            $estilo_encabezado = 'style="color:#6584a9;"';
        } else {
            $estilo = '';
            $estilo_encabezado = '';
        }

        foreach ($this->cadenaHTML as $key => $values) {
            if (is_array($datos)) {
                $this->cadenaHTML[$key] = '<table ' . $estilo . ' id="tablaReporte"><thead><tr ' . $estilo_encabezado . '>';
                $this->cadenaHTML[$key].=$encabezadof;
                $this->cadenaHTML[$key].='</tr></thead><tbody>';
                foreach ($datos as $nodo => $fila) {
                    $this->cadenaHTML[$key].= '<tr>';
                    foreach ($fila as $columna => $valor) {
                        if (is_numeric($columna)) {
                            $this->cadenaHTML[$key].= "<td>" . $valor . "</td> ";
                        }
                    }
                    $this->cadenaHTML[$key].= '</tr>';
                }
                $this->cadenaHTML[$key].= '</tbody>';
                $this->cadenaHTML[$key].= '</table>';
            } else {
                $this->cadenaHTML[$key].= '<tr>';
                $this->cadenaHTML[$key].= '</tr>';
            }
        }
        return $this->cadenaHTML[0];
    }

}
