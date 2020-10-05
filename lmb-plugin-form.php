<?php
/**
 * Plugin Name: LMB Plugin Form
 * Author: Tonio Ruiz
 * Description: Plugin par crear un formulario personalizado. Utilizando el shortcode [lmb_plugin_form]
 */

 // Definir el shortcode que pinta el formulario
 add_shortcode('lmb_plugin_form', 'LMB_Plugin_form');

 // Definir directamente la función que pintará el formulario
 function LMB_Plugin_form()
 {
    // Para no pintar el código HTML del formulario a través de echo o print, vamos a abrir un bufer de salida en HTML y luego cerrarlo
    
    ob_start(); // abrir bufer
    ?>
    <form action="<?php get_the_permalink(); ?>" method="POST" class="cuestionario">
        <div class="form-input">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" required>
        </div>
        <div class="form-input">
            <label for="correo">Correo</label>
            <input type="email" name="correo" id="correo" required>
        </div>
        <div class="form-input">
            <label for="nivel_html">¿Cuál es tu nivel de HTML?</label>
            <input type="radio" name="nivel_html" value="1" required> Nada
            <input type="radio" name="nivel_html" value="2" required> Estoy aprendiendo
            <input type="radio" name="nivel_html" value="3" required> Tengo experiencia
            <input type="radio" name="nivel_html" value="4" required> Lo domino al dedillo
        </div>
        <div class="form-input">
            <label for="nivel_html">¿Cuál es tu nivel de CSS?</label>
            <input type="radio" name="nivel_css" value="1" required> Nada
            <input type="radio" name="nivel_css" value="2" required> Estoy aprendiendo
            <input type="radio" name="nivel_css" value="3" required> Tengo experiencia
            <input type="radio" name="nivel_css" value="4" required> Lo domino al dedillo
        </div>
        <div class="form-input">
            <label for="nivel_html">¿Cuál es tu nivel de JavaScript?</label>
            <input type="radio" name="nivel_js" value="1" required> Nada
            <input type="radio" name="nivel_js" value="2" required> Estoy aprendiendo
            <input type="radio" name="nivel_js" value="3" required> Tengo experiencia
            <input type="radio" name="nivel_js" value="4" required> Lo domino al dedillo
        </div>
        <div class="form-input">
            <label for="aceptacion">La información facilitada se tratará con respeto y adminración</label>
            <input type="checkbox" name="aceptacion" id="aceptacion" value="1" required>Entiendo y acepto las condiciones
        </div>
        <div class="form-input">
            <input type="submit" value="Enviar">
        </div>
    </form>
    <?php
    return ob_get_clean(); // cerrar bufer y obtener lo que se ha escrito
 }