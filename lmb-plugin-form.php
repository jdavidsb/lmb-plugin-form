<?php
/**
 * Plugin Name: LMB Plugin Form
 * Author: Tonio Ruiz
 * Description: Plugin par crear un formulario personalizado. Utilizando el shortcode [lmb_plugin_form]
 * License: GPL2
 * Licence URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// vamos a registrar lo que ocurrirá cuando se active el plugin a traves de un hook
register_activation_hook(__FILE__, 'lmb_aspirante_init');

// realizaremos la función que se ejecutará cuando se active el plugin, que en nuestro caso creará una nueva tabla en la BD
function lmb_aspirante_init()
{
    // necesitamos invocar una variable global de wordpress wpdb que es la que apunta a la DB de wordpress 
    global $wpdb;
    // para establecer el nombre de la tabla cogemos el prefijo $wpdb->prefix + nombre de la nueva tabla
    $tabla_aspirante = $wpdb->prefix.'aspirante';
    // utilizar el mismo orden que esté utilizando la base de datos
    $charset_collate = $wpdb->get_charset_collate();
    // escribir la consulta para creación de la tabla
    $query = "CREATE TABLE IF NOT EXISTS $tabla_aspirante (
        id int(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(40) NOT NULL,
        correo varchar(100) NOT NULL,
        nivel_html smallint(4) NOT NULL,
        nivel_css smallint(4) NOT NULL,
        nivel_js smallint(4) NOT NULL,
        aceptacion smallint(4) NOT NULL,
        created_at datetime NOT NULL,
        UNIQUE (id)
        ) $charset_collate";
    // incluir upgrade.php para ejecución de consulta, que es donde está dbDelta
    include_once ABSPATH. 'wp-admin/includes/upgrade.php';
    // ejecutar la consulta en sí de manera segura
    dbDelta($query);
}

// Definir el shortcode que pinta el formulario
add_shortcode('lmb_plugin_form', 'LMB_Plugin_form');

// Definir directamente la función que pintará el formulario
function LMB_Plugin_form()
{
    /* 
    Ya que vamos a redireccionar nuestro formulario a la misma página en la que estamos (en la que se inserta el shortcode)
    Vamos a tener que comprobar si se han enviado datos del formulario antes de volver a pintarlo, ya que si se han enviado datos
    tenemos que guardarlos en la BD 
    */
    global $wpdb;    
    if( !empty($_POST) &&
        $_POST['nombre'] != '' && 
        is_email($_POST['correo']) != '' && 
        $_POST['nivel_html'] != '' && 
        $_POST['nivel_css'] != '' && 
        $_POST['nivel_js'] != '' && 
        $_POST['aceptacion'] == '1'){
        
        $tabla_aspirante = $wpdb->prefix.'aspirante';
        // sanear las variables
        $nombre = sanitize_text_field($_POST['nombre']);
        $correo = sanitize_email($_POST['correo']);
        $nivelhtml = (int)$_POST['nivel_html'];
        $nivelcss = (int)$_POST['nivel_css'];
        $niveljs = (int)$_POST['nivel_js'];
        $aceptacion = (int)$_POST['aceptacion'];
        $created_at = date('Y-m-d H:i:s');

        // grabamos en la DB
        $wpdb->insert($tabla_aspirante, array(
            'nombre' => $nombre,
            'correo' => $correo,
            'nivel_html' => $nivelhtml,
            'nivel_css' => $nivelcss,
            'nivel_js' => $niveljs,
            'aceptacion' => $aceptacion,
            'created_at' => $created_at
        ));
    }

    // Para no pintar el código HTML del formulario a través de echo o print, vamos a abrir un bufer de salida en HTML y luego cerrarlo
    
    ob_start(); // abrir bufer
    ?>
    <form action="<?php get_the_permalink(); ?>" method="POST" class="cuestionario">
        <?php wp_nonce_field('graba_aspirante', 'aspirante_nonce'); ?>
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
            <label for="nivel_css">¿Cuál es tu nivel de CSS?</label>
            <input type="radio" name="nivel_css" value="1" required> Nada
            <input type="radio" name="nivel_css" value="2" required> Estoy aprendiendo
            <input type="radio" name="nivel_css" value="3" required> Tengo experiencia
            <input type="radio" name="nivel_css" value="4" required> Lo domino al dedillo
        </div>
        <div class="form-input">
            <label for="nivel_js">¿Cuál es tu nivel de JavaScript?</label>
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

// Llamada al hook que se encarga de meter una nueva opción en el menú lateral de la administración.
add_action("admin_menu", "LMB_Aspirante_menu");

/**
 * Agrega el menú del plugin al menú de wordpress
 * 
 * @return void
 */
function LMB_Aspirante_menu()
{
    // parámetro 1: Texto una vez abramos el menú de administración de nuestro plugin
    // parámetro 2: Texto que aparecerá en el menú
    // parámetro 3: Permisos que necesita el usuario para acceder al menú
    // parámetro 4: El Slug que va a tener este elemento del menú
    // parámetro 5: Función que se encargará de pintar en el interior del panel de administración
    // parámetro 6: podemos añadirle una URL con un icono para distinguirla del resto de opciones del menú.
    // parámetro 7: el lugar en el que aparecerá el menú (posición)
    add_menu_page("Formulario Aspirantes", "Aspirantes", "manage_options", "lmb_aspirante_menu", "LMB_Aspirante_admin", "dashicons-feedback", 75);
}

function LMB_Aspirante_admin()
{

}