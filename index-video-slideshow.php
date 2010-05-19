<?php
/**

Plugin Name: Index Video Slideshow
Plugin URI: http://www.indexdigital.com.br
Description: Create a slideshow that returns a string php containing a list of videos registered by the administrator enabling to use a jquery plugin (http://jquery.malsup.com/cycle/) for slider transition effects.
Author: Index Agency
Version: 1.0
Author URI: http://www.indexdigital.com.br
Contributors: Ghabriel Rodrigues <ghabrielsp@hotmail.com, ghabriel@indexvirtual.com>, Silvio Lucena Junior <silvio_lucena_junior@hotmail.com ,silvio@indexvirtual.com>, M. Malsup (http://jquery.malsup.com/cycle/)

**/


class Video{
    private static $wpdb;
    private static $info;
	
	public static function getVideos(){
		$videos = Video::$wpdb->get_results("SELECT id_video, url_video FROM ".Video::$wpdb->prefix."video_posts ORDER BY ordem_video ASC;");
				
		$contador = 0;
		if (count($videos) > 0) { 
			if (count($videos) == 1) {
				foreach($videos as $video){
					$video_url = str_replace("watch?v=", "v/",$video->url_video);
				
					$itens = "<li class=\"".$contador."\">
					<object width=\"258\" height=\"300\"><param name=\"movie\" value=".$video_url."&hl=pt_BR&fs=1& \"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=".$video_url."&hl=pt_BR&fs=1& \" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"258\" height=\"300\"></embed></object></li>";
					$contador += 1; 
				}
			}
			else {
				foreach($videos as $video){
					$video_url = str_replace("watch?v=", "v/",$video->url_video);
					
					$itens .= "<li class=\"".$contador."\"><object width=\"258\" height=\"300\"><param name=\"movie\" value=".$video_url."&hl=pt_BR&fs=1&\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=".$video_url."&hl=pt_BR&fs=1&\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"258\" height=\"300\"></embed></object></li>";
					$contador += 1; 
				}			
			}
					
			$lista_videos = "
			<a id=\"prev-video\" class=\"arrow-left\" href=\"#\">&lt;&lt; anterior</a>
			
			<ul id=\"videos-list\">".$itens."</ul>
			
			<a id=\"next-video\" class=\"arrow-right\" href=\"#\">pr&oacute;ximo &gt;&gt;</a> ";
			
		}			
		
		echo $lista_videos;		
		
	}
	
    public static function inicializar(){
        global $wpdb;
        add_action('admin_menu', array('Video','adicionarMenu'));  
		
		add_action('init', 'add_menu');
		add_action('init', 'reg_function' );			
		      
        Video::$wpdb = $wpdb;
        Video::$info['plugin_fpath'] = dirname(__FILE__);
		
		function add_menu() {
			$page = add_options_page('Index Video Slideshow Options', 'Index Video Slideshow', 'administrator', 'ivs_menu', 'abaOpcoes');
			add_action('admin_print_scripts-' . $page, 'admin_styles');
		}
		
	   function reg_function() {
			wp_register_script('ivs_jcycle', WP_PLUGIN_URL.'/index-video-slideshow/js/jquery.cycle.all.min.js', array('jquery'));
		}
		
	   function admin_styles() {
			wp_enqueue_script ('jquery');
			wp_enqueue_script('ivs_jcycle');
		}
		
    }

    public static function instalar(){
        if (is_null(Video::$wpdb)) {
            Video::inicializar();
        }
        $query_tabela_video = "CREATE TABLE IF NOT EXISTS `".Video::$wpdb->prefix."video_posts` (
            `id_video` INT NOT NULL AUTO_INCREMENT,            
            `url_video` VARCHAR(255) NULL,
            `ordem_video` INT NULL, 
            PRIMARY KEY(`id_video`)
        )";
        
        Video::$wpdb->query($query_tabela_video); 
    }  

    public static function adicionarMenu(){
       add_menu_page('Index Slideshow Videos - Gerenciamento', 'Index Slideshow', 10, __FILE__, array("Video","abaOpcoes"));
    }

    public static function abaOpcoes(){
        echo "<div class='wrap'><h2>Cadastrar/Editar V&iacute;deos</h2></div>";
        if (isset($_POST['cadastrar'])){
            if (isset($_POST['url']) && !empty($_POST['url'])){   
               $query = "INSERT INTO ".Video::$wpdb->prefix."video_posts (url_video, ordem_video) VALUES ('".$_POST['url']."', '".$_POST['ordem']."');"; 
            } 			
            $retorno = Video::$wpdb->query($query); 
            if ($retorno) { 
                echo "V&iacute;deo adicionado com sucesso!";
            } else {
                echo "Houve um erro ao cadastrar o V&iacute;deo."; 
            }             
         }
         if (isset($_POST['editar'])){
            if (isset($_POST['url']) && !empty($_POST['url'])){
                $query = "UPDATE ".Video::$wpdb->prefix."video_posts SET url_video='".$_POST['url']."', ordem_video='".$_POST['ordem']."' WHERE id_video='".$_POST['editar']."'";  
            } else {
                $query = "UPDATE ".Video::$wpdb->prefix."video_posts SET url_video='".$_POST['url']."', ordem_video='".$_POST['ordem']."' WHERE id_video='".$_POST['editar']."'"; 
            } 

            $retorno = Video::$wpdb->query($query); 
            if ($retorno) { 
                echo "V&iacute;deo editado com sucesso!";
            } else {
                echo "Houve um erro ao editar o V&iacute;deo."; 
            }
                          
         } 
         if (isset($_GET['deletar'])){
            $query = "DELETE FROM ".Video::$wpdb->prefix."video_posts  WHERE id_video = '".$_GET['id-video']."';"; 
            $retorno = Video::$wpdb->query($query); 
            if ($retorno) { 
                echo "V&iacute;deo deletado com sucesso!";
            } else {
                echo "Houve um erro ao deletar o V&iacute;deo."; 
            }  
                        
         }

         if (isset($_GET['editar'])){
             $video = Video::$wpdb->get_row("SELECT * FROM ".Video::$wpdb->prefix."video_posts WHERE id_video='".$_GET['editar']."'");
    ?> <div class="wrap">
         <form method="POST" enctype="multipart/form-data">
             <table class="form-table"> 
                 <tbody>
                    <tr class="form-field">
                        <th scope="row">Url do V&iacute;deo: </th><td> <input type="text" name="url" value="<?= $video->url_video?>"/></td>
                    </tr>         
                    <tr class="form-field">
                        <th scope="row">Ordem: </th> <td><input type="text" name="ordem" value="<?= $video->ordem_video?>"/></td>           </tr>
                    	<input type="hidden" name="editar" value="<?= $video->id_video?>" />
                 </tbody>
              </table>
              <p class="submit"><input class="button-primary" type="submit" value="Cadastrar" /></p>
          </form> 
       </div>  
    <?php 
         } else {  
    ?>
         <div class="wrap">
         <form method="POST" enctype="multipart/form-data">
             <table class="form-table"> 
             	 <tbody>
                    <tr class="form-field">
                        <th scope="row">Url do V&iacute;deo: </th><td> <input type="text" name="url" value=""/></td>
                    </tr>         
                    <tr class="form-field">
                        <th scope="row">Ordem: </th> <td><input type="text" name="ordem" value="0"/></td>           </tr>
                    	<input type="hidden" name="cadastrar" value="1" />
                 </tbody>                 
              </table>
              <p class="submit"><input class="button-primary" type="submit" value="Cadastrar" /></p>
          </form> 
       </div>  

    <?php
        }
        $videos = Video::$wpdb->get_results("SELECT id_video, url_video, ordem_video FROM ".Video::$wpdb->prefix."video_posts;");
        echo "<div class='wrap'><h2>Lista de V&iacute;deos</h2></div>";
        echo "<table class='widefat post fixed'>";
        echo "    <thead>";
        echo "    <tr>";
        echo "        <th class='manage-column column-title'>Url do V&iacute;deo</th>";         
        echo "        <th class='manage-column column-title'>Ordem</th>"; 
        echo "        <th class='manage-column column-title'>Editar</th>";
        echo "        <th class='manage-column column-title'>Deletar</th>";
        echo "    </tr>";
        echo "    </thead>"; 
        echo "    <tbody>"; 
        foreach ($videos as $video){

         ?>
              <tr>
                  <td><?= $video->url_video?></td>                  
                  <td><?= $video->ordem_video?></td>
                  <td><a href="?page=index-video-slideshow/index-video-slideshow.php&editar=<?= $video->id_video?>">Editar</a></td>
                  <td><a href="?page=index-video-slideshow/index-video-slideshow.php&deletar=1&id-video=<?= $video->id_video?>">Deletar</a></td> 
              </tr>
        <?php
        }
        echo "    </tbody>";
        echo "</table>";
    }
}

$videoPluginFile = substr(strrchr(dirname(__FILE__),DIRECTORY_SEPARATOR),1).DIRECTORY_SEPARATOR.basename(__FILE__);
register_activation_hook($videoPluginFile, array('Video','instalar'));
add_filter('init', array('Video','inicializar'));

//Renderiza o widget
function widget_slideshow_video(){
	echo("renderizando widget de video!");
}

//Inicializa e regista o widget na sidebar do tema.
function slideshow_video_init(){
    register_sidebar_widget(__('Index Video Slideshow'), 'widget_slideshow_video');
}

//Inicializa o Widget
add_action("plugins_loaded", "slideshow_video_init");
?>