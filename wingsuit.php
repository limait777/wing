<?php
/*
Plugin Name: Wingsuit
Description: 
Version: 0.1
Author: lm
Text Domain: wingsuit
Domain Path: /languages
*/
function wingsuit_cloth(){
	return array(
		'wingsuit_front'=>array(
			'name'=>__('Передняя часть', 'wingsuit'),
			'elements'=>array(
			    'center_foot_wing'=>__('Центральная часть/ножное крыло', 'wingsuit'),
				'left_wing'=>__('Левое крыло', 'wingsuit'),
				'right_wing'=>__('Правое крыло', 'wingsuit'),
				'left_side_chest'=>__('Левая часть груди', 'wingsuit'),
				'right_side_chest'=>__('Правая часть груди', 'wingsuit'),
				'left_airintake'=>__('Левый воздухозаборник', 'wingsuit'),
				'right_airintake'=>__('Правый воздухозаборник', 'wingsuit'),
				'foot_airintakes'=>__('Ножные воздухозаборники', 'wingsuit'),
				'butisy'=>__('Бутисы', 'wingsuit'),
				'hendly'=>__('Хендлы', 'wingsuit')
			)
		),
		'wingsuit_back'=>array(
			'name'=>__('Задняя часть', 'wingsuit'),
			'elements'=>array(
				'back'=>__('Спина', 'wingsuit'),
				'foot_wing'=>__('Ножное крыло', 'wingsuit'),
				'left_wing_back'=>__('Левое крыло', 'wingsuit'),
				'right_wing_back'=>__('Правое крыло', 'wingsuit'),
				'foot_left_back'=>__('Левая нога', 'wingsuit'),
				'foot_right_back'=>__('Правая нога', 'wingsuit'),
				'left_airintake_back'=>__('Левый воздухозаборник', 'wingsuit'),
				'right_airintake_back'=>__('Правый воздухозаборник', 'wingsuit'),
				'foot_airintakes_back'=>__('Ножные воздухозаборники', 'wingsuit'),
			)
		),
		'wingsuit_logo'=>array(
			'name'=>__('Логотип', 'wingsuit'),
			'elements'=>array(
				'line'=>__('Цвет полоски', 'wingsuit'),
				'model_name'=>__('Цвет названия модели', 'wingsuit'),
				'baza_team'=>__('Цвет baza team', 'wingsuit'),
			)
		)
	);
}
function add_wingsuit_page(){
	add_options_page( 'Настройки костюма', 'Настройки костюма', 'manage_options', 'wingsuit', 'wingsuit_options_page_output' );
}
add_action('admin_menu', 'add_wingsuit_page');

function wingsuit_options_page_output(){
	?>
	<div class="wingsuit" class="wrap">
		<h2><?php echo get_admin_page_title() ?></h2>

		<form action="options.php" method="POST">
			<?php settings_fields( 'wingsuit_group' ); ?>
			<?php do_settings_sections( 'wingsuit_page' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?
}
function wingsuit_settings(){
	add_action( 'admin_enqueue_scripts', 'wingsuit_options_script_and_style' );
	add_action( 'admin_print_footer_scripts', 'wingsuit_options_print_scripts' );
	
	register_setting( 'wingsuit_group', 'wingsuit_main' );
	register_setting( 'wingsuit_group', 'wingsuit_front' );
	register_setting( 'wingsuit_group', 'wingsuit_back' );
	register_setting( 'wingsuit_group', 'wingsuit_logo' );
	
	add_settings_section( 'wingsuit_main', 'Основные настройки', '', 'wingsuit_page' );
	add_settings_field('wingsuit_pages', 'Страницы в меню:', 'fill_pages', 'wingsuit_page', 'wingsuit_main' );
	add_settings_field('wingsuit_max_colors', 'Максимальное количество цветов:', 'fill_max_colors', 'wingsuit_page', 'wingsuit_main' );
	add_settings_field('wingsuit_email', 'E-mail для заказов:', 'fill_email', 'wingsuit_page', 'wingsuit_main' );
	add_settings_field('wingsuit_zippers', 'Молнии:', 'fill_zippers', 'wingsuit_page', 'wingsuit_main' );
	add_settings_field('wingsuit_threads', 'Нитки:', 'fill_threads', 'wingsuit_page', 'wingsuit_main' );
	add_settings_field('wingsuit_cloth', 'Ткань:', 'fill_cloth', 'wingsuit_page', 'wingsuit_main' );
		
	foreach(wingsuit_cloth() as $section=>$side){
		add_settings_section( $section, $side['name'], '', 'wingsuit_page' );
		foreach($side['elements'] as $area=>$name){
			add_settings_field($area, $name, 'wingsuit_fill', 'wingsuit_page', $section, array($section,$area) );
		}
	}
}
add_action('admin_init', 'wingsuit_settings');

function wingsuit_fill($args){
	$option = get_option($args[0]);
	echo '<input type="text" name="'.$args[0].'['.$args[1].'][selector]" size="60" value="'.esc_attr( $option[$args[1]]['selector'] ).'" required placeholder="Селекторы" />';
}
function fill_pages(){
	$wingsuit_main = get_option('wingsuit_main');
	$pages = get_pages( array( 
		'sort_column' => 'post_date', 
		'sort_order' => 'desc' 
	) );
	echo '<div class="wp-tab-panel">';
	echo '<ul class="form-no-clear">';
	foreach( $pages as $page ) 
	{
		if(in_array($page->ID,$wingsuit_main['pages'])) $check = 'checked';
		else $check = '';
		echo '<li id="'.$page->post_name.'"><label class="selectit"><input value="'. $page->ID .'" type="checkbox" name="wingsuit_main[pages][]" id="'.$page->post_name.'" '.$check.'> '. $page->post_title .'</label></li>';
	}
	echo '</ul>';
	echo '</div>';
}
function fill_max_colors(){
	$wingsuit_main = get_option('wingsuit_main');
	echo '<input type="number" name="wingsuit_main[max_colors]" value="'.((isset($wingsuit_main['max_colors']) && !empty($wingsuit_main['max_colors'])) ? $wingsuit_main['max_colors'] : 6).'" />';
}
function fill_email(){
	$wingsuit_main = get_option('wingsuit_main');
	echo '<input type="email" class="regular-text ltr" name="wingsuit_main[email]" value="'.$wingsuit_main['email'].'" />';
}
function fill_zippers(){
	$wingsuit_main = get_option('wingsuit_main');
	echo '<input type="text" name="wingsuit_main[zippers][selector]" size="60" value="'.esc_attr( $wingsuit_main['zippers']['selector'] ).'" required placeholder="Селектор" />';
	echo '<p>Доступные цвета</p>';
	wingsuit_colors($wingsuit_main, 'wingsuit_main', 'zippers');
}
function fill_threads(){
	$wingsuit_main = get_option('wingsuit_main');
	echo '<input type="text" name="wingsuit_main[threads][selector]" size="60" value="'.esc_attr( $wingsuit_main['threads']['selector'] ).'" required placeholder="Селектор" />';
	echo '<p>Доступные цвета</p>';
	wingsuit_colors($wingsuit_main, 'wingsuit_main', 'threads');
}
function fill_cloth(){
	$wingsuit_main = get_option('wingsuit_main');
	wingsuit_colors($wingsuit_main, 'wingsuit_main', 'cloth');
}

function wingsuit_colors($data, $section, $area){
	$wingsuit_main = get_option('wingsuit_main');
	$numbers = (isset($wingsuit_main['max_colors']) && !empty($wingsuit_main['max_colors'])) ? $wingsuit_main['max_colors'] : 6;
	echo '<div class="wingsuit-colors">';
	for($i=0; $i<$numbers; $i++){
		echo '<div class="color">';
		echo sprintf( '<input type="text" class="wp-color-picker-field" name="%1$s[%2$s][colors][%3$s][color]" value="%4$s" data-default-color="" />', $section, $area, $i, $data[$area]['colors'][$i]['color'] );
		echo sprintf( '<input type="text" class="ws-color-name" name="%1$s[%2$s][colors][%3$s][name]" value="%4$s" placeholder="Обозначение цвета" />', $section, $area, $i, $data[$area]['colors'][$i]['name'] );
		echo '</div>';
	}
	echo '</div>';
}

function wingsuit_options_script_and_style(){
	if( isset($_GET["page"]) && $_GET["page"] == "wingsuit" ){
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'wingsuit_admin', plugins_url('/css/wingsuit_admin.css',__FILE__));

        wp_enqueue_script( 'wp-color-picker' );
	}
}

function wingsuit_options_print_scripts(){
	if( isset($_GET["page"]) && $_GET["page"] == "wingsuit" ){
        ?>
        <script>
            jQuery(document).ready(function($) {
                //Initiate Color Picker
                $('.wp-color-picker-field').wpColorPicker();
			})
		</script>
<?php
	}
}
//Shortcode
function wingsuit_display_widget($atts){
	wp_enqueue_style('wingsuit');
	wp_enqueue_script('pdfmake');
	wp_enqueue_script('canvg');
	wp_enqueue_script('vfs_fonts');
	wp_enqueue_script('wingsuit');
	wp_localize_script( 'wingsuit', 'wingsuit', 
		array(
			'ajax' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('wingsuit-nonce'),
			'common' => __('Общее', 'wingsuit'),
			'yes' => __('Да', 'wingsuit'),
			'no' => __('Нет', 'wingsuit'),
			'sizes' => __('Размеры', 'wingsuit'),
			'contacts' => __('Контакты', 'wingsuit'),
			'success' => __('Ваш заказ отправлен!', 'wingsuit'),
			'error' => __('Ошибка! Ваш заказ не отправлен.', 'wingsuit'),
			'warning' => __('Заполните все поля!', 'wingsuit'),
			'site' => __('www.baza.team', 'wingsuit'),
			'phone' => __('+7 (905) 777-10-85', 'wingsuit'),
			'email' => __('info@baza.team', 'wingsuit')
		)
	); 
	extract( shortcode_atts( array(
		'svg_file' => null,
		'hendly_price' => false,
		'logo_price' => false,
		'logo_svg' => false,
		'suit_price' => 0,
		'base_mod_price' => false,
		'base_mod_description' => false,
		'hidden_elements' => false,
		'disable_colors' => false,
		'discount_page' => false,
		'customizer_page' => false,
		'discount_description' => false,
	), $atts ) );
	if($hidden_elements !==false) $hidden_elements = explode(",", $hidden_elements);
	if(empty($svg_file))return null;
	$wingsuit_main = get_option( 'wingsuit_main' );
	$wingsuit_front = get_option( 'wingsuit_front' );
	$wingsuit_back = get_option( 'wingsuit_back' );
	$wingsuit_logo = get_option( 'wingsuit_logo' );
	$menu_pages = get_pages(['include' => $wingsuit_main['pages'],'sort_order' => 'ASC', 'sort_column' => 'post_date',]);
	$html = '<div class="wingsuit">';
	$html .= '<div class="wingsuit-menu">';
	$html .= '<ul class="wingsuit-list">';
	$page_id=get_the_ID();
	$pagename = get_the_title();
	foreach($menu_pages as $page){
		$thumb = get_the_post_thumbnail_url($page->ID,'full');
		$html .= '<li'.(($page_id == $page->ID)?' class="active"':'').'><a href="'.get_permalink($page).'">';
		if($thumb===false) $html .= '<span>'.$page->post_title.'</span>';
		else $html .=  '<img src="'.$thumb.'">';
		$html .= '</a></li>';
	}
	$html .= '</ul>';
	$html .= '</div>';
	/*$html .= '<h3>Дизайн костюма</h3>';*/
	$html .= '<div class="wingsuit-row customizer">';
	$html .= '<div class="wingsuit-svg'.(($disable_colors !== false)?' no-colors':'').'" data-name="'.$pagename.'">';
	$html .= file_get_contents($svg_file);

	if( $customizer_page !== false )$html .= '<a class="button" href="'.$customizer_page.'">'.__('Вернуться к стандартной форме дизайна','wingsuit').'</a>';
	$html .= '<h3 class="wingsuit-base-price">'.__('Базовая цена:  ','wingsuit').$suit_price.__('р.','wingsuit').'</h3>';
	if($disable_colors === false)$html .= '<button class="button randomcolors">'.__('Случайные цвета','wingsuit').'</button> ';
	if( $discount_page !== false )$html .= '<a class="wingsuit-discount" href="'.$discount_page.'">'.__('Акция: -10% на костюм','wingsuit').'</a>';
	$html .= '</div>';
	$html .= '<div class="wingsuit-colors'.(($disable_colors !== false)?' no-colors':'').'">';
	if( $discount_description !== false )$html .= '<p>'.$discount_description.'</p>';
	$options = wingsuit_cloth();
	if($disable_colors === false){
		$html .= '<ul class="wingsuit_main">';
		if(isset($wingsuit_main['zippers']['selector']) && !empty($wingsuit_main['zippers']['selector'])){
			$html .= '<li class="wingsuit-area">';
			$html .= '<label>'.__('Молнии','wingsuit').'</label>';
			$html .= '<ul class="element" data-property="stroke" data-selector="'.$wingsuit_main['zippers']['selector'].'">';
			foreach($wingsuit_main['zippers']['colors'] as $option){
				if($option['color'] == '')continue;
				$html .= '<li data-color="'.$option['name'].'" style="background-color:'.$option['color'].'">';
				$html .= '</li>';
			}
			$html .= '</ul>';
			$html .= '</li>';
		}
		if(isset($wingsuit_main['threads']['selector']) && !empty($wingsuit_main['threads']['selector'])){
			$html .= '<li class="wingsuit-area">';
			$html .= '<label>'.__('Нитки','wingsuit').'</label>';
			$html .= '<ul class="element" data-property="stroke" data-selector="'.$wingsuit_main['threads']['selector'].'">';
			foreach($wingsuit_main['threads']['colors'] as $option){
				if($option['color'] == '')continue;
				$html .= '<li data-color="'.$option['name'].'" style="background-color:'.$option['color'].'">';
				$html .= '</li>';
			}
			$html .= '</ul>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		foreach($options as $section=>$side){
			if($section=='wingsuit_front') $elements = $wingsuit_front;
			elseif($section=='wingsuit_back') $elements = $wingsuit_back;
			else continue;
			$html .= '<h4 class="'.$section.'-title">'.$side['name'].'</h4>';
			$html .= '<ul class="'.$section.'">';
			foreach($side['elements'] as $area=>$name){
				if($hidden_elements !== false && in_array($area, $hidden_elements))continue;
				if($area == 'hendly')continue;
				if(isset($elements[$area]['selector']) && !empty($elements[$area]['selector'])){
					$html .= '<li class="wingsuit-area">';
					$html .= '<label>'.$name.'</label>';
					$html .= '<ul class="element" data-selector="'.$elements[$area]['selector'].'">';
					
					foreach($wingsuit_main['cloth']['colors'] as $option){
						if($option['color'] == '')continue;
						$html .= '<li data-color="'.$option['name'].'" style="background-color:'.$option['color'].'">';
						$html .= '</li>';
					}
					$html .= '</ul>';
					$html .= '</li>';
				}
			}
			$html .= '</ul>';
		}
	
		$html .= '</div>';
		$html .= '</div>';
	}
	if(($logo_price!==false && $logo_svg!==false) || $hendly_price!==false || $base_mod_price!==false){
		$html .= '<div id="addons" class="wingsuit-row">';
		$html .= '<h4 class="addons-title">'.__('Дополнительно','wingsuit').'</h4>';
		if($hendly_price!==false){
			$html .= '<div class="wingsuit-row addon">';
			$html .= '<label><input class="hendly" type="checkbox" data-price="'.$hendly_price.'" data-selector="'.$wingsuit_front['hendly']['selector'].'" autocomplete="off"><strong> '.__('Добавить','wingsuit').' '.$options['wingsuit_front']['elements']['hendly'].' (+'.$hendly_price.__('р.','wingsuit').')</strong></label>';
			if($disable_colors === false){
				$html .= '<ul class="hendles-color">';
				$html .= '<li class="wingsuit-area">';
				$html .= '<label>'.__('Цвет','wingsuit').'</label>';
				$html .= '<ul class="element disabled" data-selector="'.$wingsuit_front['hendly']['selector'].'">';
						foreach($wingsuit_main['cloth']['colors'] as $option){
							if($option['color'] == '')continue;
							$html .= '<li data-color="'.$option['name'].'" style="background-color:'.$option['color'].'">';
							$html .= '</li>';
						}
				$html .= '</ul>';
				$html .= '</li>';
				$html .= '</ul>';
			}
			$html .= '</div>';
			$html .= '<style>'.$wingsuit_front['hendly']['selector'].'{display:none}</style>';
		}
		if($logo_price!==false && $logo_svg!==false) {
			$html .= '<div class="wingsuit-row addon">';
			     $html .= '<label><input class="wingsuit-logo" type="checkbox" data-price="'.$logo_price.'" autocomplete="off"><strong> '.__('Изменить цвет логотипа','wingsuit').' (+'.$logo_price.__('р.','wingsuit').')</strong></label>';
			$html .= '<div class="logo-svg">'.file_get_contents($logo_svg).'</div>';
			$html .= '<ul class="wingsuit_logo">';
			foreach($options['wingsuit_logo']['elements'] as $area=>$name){
				if($area == 'hendly' && $hendly_price===false)continue;
				if(isset($wingsuit_logo[$area]['selector']) && !empty($wingsuit_logo[$area]['selector'])){
					$html .= '<li class="wingsuit-area">';
					$html .= '<label>'.$name.'</label>';
					$html .= '<ul class="element disabled" data-selector="'.$wingsuit_logo[$area]['selector'].'">';
					
					foreach($wingsuit_main['threads']['colors'] as $option){
						if($option['color'] == '')continue;
						$html .= '<li data-color="'.$option['name'].'" style="background-color:'.$option['color'].'">';
						$html .= '</li>';
					}
					$html .= '</ul>';
					$html .= '</li>';
				}
			}
			$html .= '</ul>';
			$html .= '</div>';
		}
		if($base_mod_price!==false){
			$html .= '<div class="addon">';
			$html .= '<label><input class="base-mod" type="checkbox" data-price="'.$base_mod_price.'" autocomplete="off"><strong> '.__('B.A.S.E. модификация','wingsuit').' (+'.$base_mod_price.__('р.','wingsuit').')</strong></label> <span class="hint"><span>'.$base_mod_description.'</span></span>';
			$html .= '</div>';
		}
	             $html .= '<h3 class="suit-price" data-price="'.$suit_price.'">'.__('Итоговая цена:','wingsuit').' <span>'.$suit_price.'</span>'.__('р.','wingsuit').'</h3>';

		$html .= '</div>';
	}
	if($disable_colors !== false){
		$html .= '</div>';
		$html .= '</div>';
	}
	$html .= '<div class="wingsuit-row sizes">';
	$html .= '<h3>'.__('Измерьте себя','wingsuit').'</h3>';
	$html .= '<div class="wingsuit-fields">';
	/*$html .= '<div class="wingsuit-row">';
	$html .= '<label class="control-label">Пол</label>
			<div><select class="custom-select">
				<option value="Мужской">Мужской </option>
				<option value="Женский">Женский</option>
			</select></div>';
	$html .= '</div>';*/
	$html .= '<div class="wingsuit-field"><label class="control-label">'.__('1. Рост без обуви <span class="hint"><span> Прислонитесь спиной к ровной вертикальной поверхности и точно замерьте свой рост без обуви.</span></span>','wingsuit').'</label><div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/1,2.svg',__FILE__).'"></div></div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('2. Рост в обуви <span class="hint"><span>Теперь наденьте тот тип обуви, в котором вы собираетесь летать, и сделайте то же самое.</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/1,2.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('3. Размер от яремной впадины до паха <span class="hint"><span>Замерьте расстояние от яремной впадины до паха (место, где сходятся 4 шва брюк)
</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/3.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('4. Размер от паха до пола <span class="hint"><span>Замерьте расстояние от паха (см.выше) до пола. Ноги прямо, вместе.</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/4.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('5. Размер от яремной впадины до косточки на плече <span class="hint"><span>Нащупайте на плече явно выступающую косточку. Измерьте расстояние от яремной впадины до косточки.</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/5.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('6. Размер от косточки на плече до косточки на запястье <span class="hint"><span>Замерьте расстояние от косточки на плече, найденной на предыдущем шаге, до косточки на запястье.</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/6.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('7. Объем груди <span class="hint"><span>Поднимите руки, запустите метр под мышки и опустите руки. Сделайте вздох. Это и есть объем груди.</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/7.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('8. Объем талии <span class="hint"><span>Объем талии замеряем на уровне пупка.</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/8.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('9. Объем бедер (таза) <span class="hint"><span>Замеряем самое широкое место бедер</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/9.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('10. Расстояние от яремной ямки до пола <span class="hint"><span>Поставьте ноги на ширине плеч. Замерьте расстояние от яремной впадины до пола. Точка на полу снаружи ступни.</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/10.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('11. Объем одного бедра <span class="hint"><span>Замерьте объем самой широкой части бедра.</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/11.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('12. Объем одной голени <span class="hint"><span>Замерьте объем самой широкой части голени.</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/12.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('13. Объем бицепса <span class="hint"><span>Согните руку в локте под 90 градусов. Не напрегайте бицепс. Замерьте объем.</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/13.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('14. Ширина плеч <span class="hint"><span>Станьте ровно, разверните плечи. Замерьте расстояние между косточками на плечах.</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/14.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('15. Расстояние от верхнего позвонка до поясницы <span class="hint"><span>Замерьте расстояние от верхнего позвонка (явно выраженная косточка внизу шеи сзади) до поясницы (место на спине напротив пупка).</span></span>','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/15.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('16. Вес','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('кг.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/obuv.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('17. Размер обуви','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('размер','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/obuv.svg',__FILE__).'"></div>
			  </div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">'.__('18. Объем обуви','wingsuit').'</label>
				<div><input type="text" class="form-control" required placeholder="'.__('см.','wingsuit').'"  pattern="[0-9]*" data-img="'.plugins_url('/img/obuv.svg',__FILE__).'"></div>
			  </div>';
	$html .= '</div>';
	$html .= '<div class="wingsuit-human">';
	$html .= '<img src="'.plugins_url('/img/1,2.svg',__FILE__).'">';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="wingsuit-form">';
	$html .= '<h3>'.__('Оформить заказ','wingsuit').'</h3>';
	$html .= '<div class="wingsuit-field"><label class="control-label">'.__('Фамилия','wingsuit').'</label><div><input type="text" class="form-control" required></div> </div>';
	$html .= '<div class="wingsuit-field"><label class="control-label">'.__('Имя','wingsuit').'</label><div><input type="text" class="form-control" required></div></div>';
	$html .= '<div class="wingsuit-field"><label class="control-label">'.__('Страна','wingsuit').'</label><div><input type="text" class="form-control" required></div></div>';
	$html .= '<div class="wingsuit-field"><label class="control-label">'.__('Индекс','wingsuit').'</label><div><input type="text" class="form-control" required></div></div>';
    $html .= '<div class="wingsuit-field"><label class="control-label">'.__('Город, улица, номер дома, квартира','wingsuit').'</label><div><input type="text" class="form-control" required></div></div>';
    $html .= '<div class="wingsuit-field"><label class="control-label">'.__('Номер телефона','wingsuit').'</label><div><input type="phone" class="form-control" required></div></div>';
	$html .= '<div class="wingsuit-field">
				<label class="control-label">E-mail</label>
				<div><input type="email" class="form-control" required></div>
			  </div>';
	             $html .= '<div class="wingsuit-textarea"> <label class="control-label">'.__('Комментарий','wingsuit').'</label> <div><textarea class="form-control"></textarea></div></div>';
  $html .= '<label><input class="wingsuit-logo" type="checkbox" required><strong> '.__('Я ознакомлен с <a href="https://bazateam.ru/privacy-policy/">политикой конфиденциальности.</a>','wingsuit').'</strong></label>';
	$html .= '<div class="wingsuit-checkout">';
	$html .= '<h3 class="suit-price left" data-price="'.$suit_price.'">'.__('Итоговая цена:','wingsuit').' <span>'.$suit_price.'</span>'.__('р.','wingsuit').'</h3>';
	$html .= '<div>';

    $html .= '<button class="button makepdf">'.__('Скачать в pdf','wingsuit').'</button> ';
	$html .= '<button class="button send">'.__('Разместить заказ','wingsuit').'</button> ';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	return $html;
}

add_shortcode( 'wingsuit-shortcode', 'wingsuit_display_widget' );


add_action('wp_ajax_wingsuit_send', 'wingsuit_send');
add_action('wp_ajax_nopriv_wingsuit_send', 'wingsuit_send');

function wingsuit_send(){
	check_ajax_referer( 'wingsuit-nonce', 'nonce_code' );
	$wingsuit_main = get_option( 'wingsuit_main' );
	if(isset($wingsuit_main['email']) && !empty($wingsuit_main['email'])){
		
		$pdf = base64_decode($_POST['pdf']);
		$tempname = tempnam('', 'pdf-form-');
		rename($tempname, $tempname .= '.pdf');
		
		file_put_contents($tempname, $pdf);
		
		$headers = array(
			'content-type: text/html',
		);
if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == '128.72.92.5') $wingsuit_main['email'] ='romik-17@mail.ru';
		$res = wp_mail( $wingsuit_main['email'], 'Новый заказ на WingSuit', $_POST['order'], $headers, array($tempname));

		unlink( $tempname );
	}
	die();
}
function wingsuit_scripts(){
	wp_register_style( 'wingsuit', plugins_url('/css/wingsuit.css',__FILE__) );
	wp_register_script('vfs_fonts',plugins_url( '/js/vfs_fonts.js', __FILE__ ),array('pdfmake'),null,true);
	wp_register_script('pdfmake',plugins_url( '/js/pdfmake.min.js', __FILE__ ),null,null,true);
	wp_register_script('canvg',plugins_url( '/js/canvg.min.js', __FILE__ ),null,null,true);
	wp_register_script('wingsuit',plugins_url( '/js/wingsuit.js', __FILE__ ),array('jquery','pdfmake','canvg'),null,true);
}
add_action( 'wp_enqueue_scripts', 'wingsuit_scripts' );
?>