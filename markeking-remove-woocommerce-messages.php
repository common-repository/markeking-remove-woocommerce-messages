<?php
/*
Plugin Name: MarkeKing Remove Woocommerce Messages
Plugin URI: http://markeking.es/markeking-remove-woocommerce-messages-plugin/
Version: 0.1
Author: Markeking.es 
Description: Remove Woocommerce messages if the messages contain phrases that you indicate.
*/



//===================================================================================================================
//AGREGO UN FILTRO PARA TOMAR LAS PLANTILLAS DE MENSAJE PERSONALIZADAS MIAS
//===================================================================================================================

add_filter( 'wc_get_template', 'mrwm_custom_template', 20, 3 );
function mrwm_custom_template( $located, $template_name, $args ){
	
	if( strpos($template_name, 'notices/') !== false){
		
		switch( $template_name ){
			
			case 'notices/error.php':
			$located = dirname( __FILE__ ) . '/notices/error.php';
			break;
			
			case 'notices/notice.php':
			$located = dirname( __FILE__ ) . '/notices/notice.php';
			break;
			
			case 'notices/success.php':
			$located = dirname( __FILE__ ) . '/notices/success.php';
			break;
			
		}
		
	}
	
	return $located;

}


//===================================================================================================================
//FUNCION PARA DETECTAR SI LOS MENSAJES PENDIENTES DE MOSTRAR CONTIENEN FRASES PROHIBIDAS, Y EN SU CASO ELIMINARLOS
//===================================================================================================================
function mrwm_messages_filter( $messages ){
	
	//aqui obtengo los mensajes prohibidos
	$frases_a_ocultar = unserialize( get_option('frases_a_ocultar') );
	
	
	foreach( $frases_a_ocultar as $frase){
		
		foreach( $messages as $key => $message ){
			
			//retiro las etiquetas html con strip tags porque si el mensaje contiene etiquetas, el usuario no las va a poner y entonces no va a coincidir
			if( stripos( strip_tags($message), $frase) !== false )
			unset( $messages[$key] );
			
		}
		
	}
	
	return $messages;
	
}



//===================================================================================================================
// MENUS DE ADMINISTRACION
//===================================================================================================================


//===================================================================================================================
//ANADE LA OPCION EN EL MENU Y CREA LA PAGINA
//===================================================================================================================

add_action( 'admin_menu', 'mrwm_menu_markeking_remove_woocommerce_messages' );

function mrwm_menu_markeking_remove_woocommerce_messages() {
	add_options_page( 
		'MarkeKing Remove Woocommerce Messages Options',	//titulo a mostrar en la pagina de destino
		'MarkeKing Remove Woocommerce Messages',	//el texto a mostrar en el menu
		'manage_options',	//nivel del usuario necesario para manejarla
		'markeking-remove-woocommmerce-messages',	//slug que quiero que tenga la pagina
		'mrwm_markeking_remove_woocommerce_messages_admin_render'	//funcion que muestra el formulario o contenido de la pagina
	);
}



//===================================================================================================================
// FUNCION QUE CONTIENE EL FORMULARIO O CONTENIDO DE LA PAGINA DE OPCIONES
//===================================================================================================================

function mrwm_markeking_remove_woocommerce_messages_admin_render() {
    ?>
	
	<style>
		
		.donar{
			text-align: center;
			float: right;
			width: 200px;
			margin: 20px;
			border: 3px solid #0085ba;
			border-radius: 20px;
			padding: 10px;
		}
		.formulario_frases_prohibidas{
			text-align: center;
			border: 3px solid #0085ba;
			display: table;
			margin: auto;
			padding: 20px;
			border-radius: 30px;
		}
		
		.formulario_frases_prohibidas p{
			text-align: center;
		}
		
		.frases_prohibidas th{
			background-color: #f72f2f;
			border-bottom: 4px solid #ddd;
			padding: 10px;
			color: #fff;
		}
		
		.frases_prohibidas td{
			border-bottom: 4px solid #e8e8e8;
			padding: 5px;
		}
		
		.celda_frase{
			text-align: left;
		}
		
		table.frases_prohibidas{
			margin: auto;
			background-color: #fff;
		}
		
	</style>
	
    <div class="wrap">
		
        <h1>MarkeKing Remove Woocommerce Messages</h1>
		
		<div class="donar">
			Did you liked this plugin?
			<br>Would you like to help me to make more plugins?
			<br>Thank you very much!
		<form style="text-align: center;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input name="cmd" type="hidden" value="_s-xclick" /> <input name="encrypted" type="hidden" value="-----BEGIN PKCS7-----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-----END PKCS7----- " /> <input style="border: none;" alt="PayPal. La forma rápida y segura de pagar en Internet." name="submit" src="https://www.paypalobjects.com/es_ES/ES/i/btn/btn_donateCC_LG.gif" type="image" />
<img src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" alt="" width="1" height="1" border="0" /></form>
		</div>
		
		<h2>INSTRUCTIONS</h2>
		<p>The operation is very simple. Enter a phrase and if a WooCommerce message contains that phrase, the message will not be displayed.</p>
		<p>You can enter multiple phrases, but you have to do it one at a time. Enter a phrase and press the button, then enter another and press the button again and so again and again.</p>
		<p>There are certain things you should know about how the plugin works. I recommend you visit our website for complete instructions, examples and more!:</p>
		<p style="text-align: center"><a href="http://markeking.es/markeking-remove-woocommerce-messages-plugin/">MARKEKING REMOVE WOOCOMMERCE MESSAGES INSTRUCTIONS</a></p>
		
		<form class="formulario_frases_prohibidas" method="post" action="options.php">
		<?php
		settings_fields( 'markeking_remove_woocommerce_messages_option_group' );
		do_settings_sections( 'markeking_remove_woocommerce_messages_option_group' );
		?>
			<h2>ENTER A FORBIDDEN SENTENCE HERE</h2>
			<label>I do not want to display messages containing the following sentence: </label><br>
			<input type="text" name="frases_a_ocultar" id="frases_a_ocultar" value="">
		
			<?php submit_button(); ?>
			
			<br>
			<h2>YOUR BLOCKED PHRASES</h2>
			<p>In the future will not be displayed messages containing the following phrases:</p>
			<?php
			
			$frases_prohibidas = unserialize( get_option('frases_a_ocultar') );
			
			echo'<table class="frases_prohibidas">';
			
			echo '<tr><th>BLOCKED PHRASES</th><th>REMOVE</th></tr>';
			
			foreach( $frases_prohibidas as $indice => $frase ){
				
				echo '<tr>';
				echo '<td class="celda_frase">' . esc_html( $frase ) . '</td>';
				echo '<td class="centrado"><input type="checkbox" name="delete_fb_sentence[' . esc_html( $indice ) . ']"></td>';
				echo '</tr>';
				
			}
			
			echo'</table>';
			
			?>
			
			<?php submit_button(); ?>
		
		</form>
		
    </div>
    <?php
}


//===================================================================================================================
// PROCESAMIENTO DE LOS DATOS DEL FORMULARIO DE ADMINISTRACION
//===================================================================================================================

add_action( 'admin_init', 'mrwm_markeking_remove_woocommerce_messages_init' );

function mrwm_markeking_remove_woocommerce_messages_init(){
	
	register_setting( 'markeking_remove_woocommerce_messages_option_group', 'este_texto_no_hace_falta' );
	
	if( isset($_POST["delete_fb_sentence"]) ||
	   ( isset($_POST["frases_a_ocultar"]) && !empty($_POST["frases_a_ocultar"]) )
	){
		
		$frases_prohibidas = unserialize( get_option('frases_a_ocultar') );
		
		if( isset($_POST["delete_fb_sentence"])){
			
			foreach( $_POST["delete_fb_sentence"] as $indice => $on ){
				
				$safe_indice = intval($indice);
				
				if($safe_indice)
				unset( $frases_prohibidas[$indice] );
				
			}
			
		}
		
		if( isset($_POST["frases_a_ocultar"]) && !empty($_POST["frases_a_ocultar"]) ){
			
			$frases_prohibidas[] = sanitize_text_field( $_POST["frases_a_ocultar"] );
			
		}
		
		update_option( 'frases_a_ocultar', serialize( $frases_prohibidas ) );
		
	}
	
}



?>