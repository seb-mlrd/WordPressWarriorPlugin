<?php
/*
Plugin Name: Photo Wall
Description: Un plugin pour afficher un mur de photos avec un shortcode.
Version: 1.0
Author: Sébastien Maillard
*/

if (!defined('ABSPATH')) {
    exit; // Empêche l'accès direct
}

function mon_plugin_photo_wall_page(){
    add_options_page(
        'Photo Wall',
        'Photo Wall',
        'manage_options',
        'photo-wall',
        'mon_plugin_photo_wall_config'
    );
}
add_action('admin_menu', 'mon_plugin_photo_wall_page');


function mon_plugin_photo_wall_config(){

    if (!current_user_can('manage_options')) {
        wp_die(__('Vous n\'avez pas les droits nécessaires pour accéder à cette page.'));
    }
    extract($_POST);
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $file_name = $file['name'];
        $file_tmp_name = $file['tmp_name'];
        $file_type = $file['type'];


        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file_type, $allowed_types)) {
            echo "Seules les images JPEG, PNG, GIF et WebP sont autorisées.";
            exit;
        }
        $date = new DateTime();
        $mounth = $date->format('m');
        $year = $date->format('Y');
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'] . '/' . $year . '/' . $mounth . '/';

        $file_name_unique = wp_unique_filename($upload_path, $file_name);


        if (move_uploaded_file($file_tmp_name, $upload_path . '/' . $file_name_unique)) {
            $attachment = array(
                'guid' => $upload_dir['url'] . '/' . $file_name_unique,
                'post_mime_type' => $file_type,
                'post_title' => sanitize_file_name($file_name),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            
            $attachment_id = wp_insert_attachment($attachment, $upload_dir['path'] . '/' . $file_name_unique);

            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $upload_dir['path'] . '/' . $file_name_unique);
            wp_update_attachment_metadata($attachment_id, $attachment_metadata);

            echo "Fichier téléchargé avec succès : " . $file_name_unique;
        } else {
            echo "Erreur lors du téléchargement de l'image.";
        }
    }

    $images = get_option('mon_plugin_photo_wall_options', '');
    ?>
    <div class="wrap">
        <h1>Photo Wall</h1>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="photo_wall_images">Images :</label><br>
            <input type="hidden" name="MAX_FILE_SIZE" value="30000000"/>
            <input type="file" id="image" name="image" style="width: 100%; height: 200px;"/>
            <p>Entrez les URL des images à afficher</p>
            <p><input id="btnImage" type="submit" name="btnImage" value="Enregistrer" class="button button-primary"></p>
        </form>
        <h2>Formation photo-waaaalllllll</h2>
        
    </div>
    <?php
}



function mon_plugin_photo_wall($content){
    $images = get_option('mon_plugin_photo_wall_options', '');
    if (!empty($images)) {
        $content .= '<div class="photo-wall">';
        $images = explode("\n", $images);
        foreach ($images as $image) {
            $content .= '<div class="photo-wall-item">';
            $content .= '<img src="' . esc_url(trim($image)) . '" alt="Photo" />';
            $content .= '</div>';
        }
        $content .= '</div>';
    }
    return $content;
}
add_filter('the_content', 'mon_plugin_photo_wall');




// Chargement des scripts et styles
function photo_wall_enqueue_assets() {
    wp_enqueue_style('photo-wall-style', plugin_dir_url(__FILE__) . 'css/photo-wall.css');
    wp_enqueue_script('photo-wall-script', plugin_dir_url(__FILE__) . 'js/photo-wall.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'photo_wall_enqueue_assets');

// Shortcode pour afficher le mur de photos
function photo_wall_shortcode($atts) {
    $atts = shortcode_atts(array(
        'images' => "http://localhost:8888/wordpress/wp-content/uploads/2024/12/dandadan-scaled.jpg,http://localhost:8888/wordpress/wp-content/uploads/2024/12/miles-morales-2-scaled.jpg,http://localhost:8888/wordpress/wp-content/uploads/2024/12/cowboy-bebop-scaled.jpg,http://localhost:8888/wordpress/wp-content/uploads/2024/12/chateau-ambulant-scaled.jpg,http://localhost:8888/wordpress/wp-content/uploads/2024/12/fma-scaled.jpg,http://localhost:8888/wordpress/wp-content/uploads/2024/12/your-name.jpg", 
    ), $atts);

    if (empty($atts['images'])) {
        return '<p>Aucune image fournie.</p>';
    }

    $images = explode(',', $atts['images']);
    $output = '<div class="photo-wall">';

    foreach ($images as $image) {
        $output .= '<div class="photo-wall-item">';
        $output .= '<img src="' . esc_url(trim($image)) . '" alt="Photo" />';
        $output .= '</div>';
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('photo_wall', 'photo_wall_shortcode');
