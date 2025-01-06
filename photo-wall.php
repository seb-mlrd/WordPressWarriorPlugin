<?php
/*
Plugin Name: Photo Wall
Description: Un plugin pour afficher un mur de photos avec un shortcode.
Version: 1.0
Author: Sébastien Maillard
*/

// Chargement des scripts et styles
function photo_wall_enqueue_assets() {
    wp_enqueue_style('photo-wall-style', plugin_dir_url(__FILE__) . 'css/photo-wall.css');
}
add_action('wp_enqueue_scripts', 'photo_wall_enqueue_assets');


if (!defined('ABSPATH')) {
    exit; // Empêche l'accès direct au fichier
}

// **1. Fonction pour charger les scripts et styles**
add_action('admin_enqueue_scripts', 'photo_wall_admin_scripts');
function photo_wall_admin_scripts($hook) {
    // Chargez les scripts uniquement sur la page de votre plugin
    if ($hook !== 'toplevel_page_photo-wall') {
        return;
    }

    // Inclure la médiathèque WordPress
    wp_enqueue_media();

    // Inclure le fichier JavaScript
    wp_enqueue_script(
        'photo-wall-script', // Identifiant unique
        plugin_dir_url(__FILE__) . 'js/photo-wall.js', // Chemin vers le JS
        ['media-editor', 'media-views'], // Dépendances WordPress
        '1.0',
        true // Charger dans le footer
    );
}

// **2. Fonction pour créer le menu d'administration**
function photo_wall_admin_menu() {
    add_menu_page(
        'Photo Wall', // Titre de la page
        'Photo Wall', // Nom du menu
        'manage_options', // Autorisation requise
        'photo-wall', // Slug unique
        'photo_wall_admin_page', // Fonction de callback pour le contenu
        'dashicons-format-gallery', // Icône
        20 // Position dans le menu
    );
}
add_action('admin_menu', 'photo_wall_admin_menu');

// **3. Contenu de la page d'administration**
function photo_wall_admin_page() {
    // Récupérez les images sauvegardées (sous forme d'IDs séparés par des virgules)
    $images = isset($_POST['photo_wall_images']) ? esc_attr($_POST['photo_wall_images']) : '';

    // Si le formulaire est soumis, sauvegardez les données
    if (isset($_POST['valider'])) {
        // $typePhotoWall = $_POST["photoWallChoice"];

        global $wpdb;

        // Récupérer les données du formulaire
        $image_ids = isset($_POST['photo_wall_images']) ? sanitize_text_field($_POST['photo_wall_images']) : '';

        $table_image_url = "";
        $image_table = explode(",", $image_ids);
        foreach($image_table as $image){
            $image_url = wp_get_attachment_url($image);
            $table_image_url .= $image_url . ",";
        }
        echo $table_image_url;
        $mode = isset($_POST['photoWallChoice']) ? sanitize_text_field($_POST['photoWallChoice']) : 'classic';
    
        // Vérifiez que les données sont valides avant d'insérer
        if (!empty($image_ids) && !empty($mode)) {
            $table_name = $wpdb->prefix . 'photo_wall';
            // Insérer les données dans la table
            $wpdb->insert(
                $table_name,
                [
                    'image' => $table_image_url, // IDs des images (séparés par des virgules)
                    'mode' => $mode,           // Mode choisi (modern/classic)
                ],
                [
                    '%s', // Format de `image` (chaîne)
                    '%s', // Format de `mode` (chaîne)
                ]
            );
    
            // Message de confirmation
            echo '<div class="updated"><p>Les images et le mode ont été enregistrés dans la base de données.</p></div>';
        } else {
            echo '<div class="error"><p>Veuillez sélectionner des images et un mode.</p></div>';
        }
    
        update_option('photo_wall_images', $images);
        echo '<div class="updated"><p>Mur de photos mis à jour.</p></div>';
    }

    // Récupération des images enregistrées
    $images = get_option('photo_wall_images', '');
    ?>
    <div class="wrap">
        <h1>Configurer le Photo Wall</h1>
        <form action="" method="post">
            <!-- Champ pour choisir le mode -->
            <legend>Choisi un putain de mode:</legend>
            <input type="radio" name="photoWallChoice" value="modern" id="modern" checked>
            <label for="modern">Modern</label>

            <input type="radio" name="photoWallChoice"  value="classic" id="classic">
            <label for="classic">Classic</label>

            <!-- Champ caché pour stocker les IDs des images -->
            <input type="hidden" name="photo_wall_images" id="photo_wall_images" value="<?php echo esc_attr($images); ?>">

            <!-- Bouton pour ouvrir la médiathèque -->
            <button type="button" class="button" id="select-images-button">Choisir des images</button>
            <p>
                <input type="submit" name="valider" class="button-primary" value="Enregistrer">
            </p>
            <!-- Conteneur pour prévisualiser les images -->

            <!-- ************* -->
            <div id="selected-images" style="margin-top: 15px;">
                <?php
                if (!empty($images)) {
                    // $image_ids = explode(',', $images);
                    // $all_image_url =  "";
                    // echo '<table style="width:100%; border: 1px solid #ccc; border-collapse: collapse;">';
                    // echo '<tr><th>Image</th><th>Actions</th></tr>';  // En-têtes du tableau
                    // foreach ($image_ids as $id) {
                        // $image_url = wp_get_attachment_url($id); // Récupère l'URL de l'image
                        // $all_image_url .= $image_url . ',';
                        // $array_url = [$image_url];
                        // $image_thumb = wp_get_attachment_image_src($id, 'thumbnail'); // Miniature de l'image
                        // echo '<tr>';
                        // echo '<td><img src="' . esc_url($image_thumb[0]) . '" alt="" style="max-width: 100px; max-height: 100px;"></td>';
                        
                        // echo '<td><button class="remove-image-button" data-id="' . esc_attr($id) . '">Supprimer</button></td>';
                        // echo '</tr>';
                    // }
                    // echo '</table>';
                }
                global $wpdb;
                // Récupérez toutes les entrées de la table
                $table_name = $wpdb->prefix . 'photo_wall';
                $results = $wpdb->get_results("SELECT * FROM $table_name");
                // var_dump($results);
                if (!empty($results)) {
                    echo '<h2>Murs de photos enregistrés</h2>';
                    ?>
                    <div>
                    <?php
                    foreach ($results as $row) {
                        echo "<div style='border: 3px solid black; margin-bottom: 10px;'";
                        echo '<h3>Mode sélectionné : ' . esc_html($row->mode) . '</h3>';
                        echo '<h4>Id du Photo wall :' . esc_html($row->id) . '</h4>';
                        // Récupérer les IDs des images et les convertir en tableau
                        $image_ids = explode(',', $row->image);
                        echo '<div class="photo-wall-gallery">';
                        foreach ($image_ids as $id) {
                           if($id != ""){
                            echo '<img src="' . esc_html($id) . '" alt="Photo" style="max-width: 100px; max-height: 100px;">';
                           }
                        }
                        // foreach ($row as $id) {
                        //     echo wp_get_attachment_image($id, 'thumbnail');
                        // }
                            echo '</div>';
                        echo '</div>';
                    }
                    
                } else {
                    echo '<p>Aucun mur de photos enregistré.</p>';
                }
                ?>
                </div>
            </div>
        </form>

        
    </div>
    <?php
}















function photo_wall_shortcode($atts) {
    global $wpdb;

    // Nom de la table
    $table_name = $wpdb->prefix . 'photo_wall';

    // Vérification et traitement des attributs du shortcode
    $atts = shortcode_atts(
        array(
            'id' => '', // L'ID du photo wall
        ),
        $atts
    );

    // Vérifier si un ID est fourni
    $photo_wall_id = intval($atts['id']);
    if (!$photo_wall_id) {
        return '<p>Veuillez spécifier un ID valide pour le Photo Wall.</p>';
    }

    // Requête pour récupérer les informations du Photo Wall dans la base de données
    $results = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $photo_wall_id)
    );

    // Vérifier si un résultat existe
    if (empty($results)) {
        return '<p>Aucun Photo Wall trouvé pour cet ID.</p>';
    }

    // Construire la sortie HTML
    $output = '';

    foreach ($results as $row) {
        // Récupérer les données
        $mode = esc_html($row->mode); // Mode choisi (modern/classic)
        $image_urls = explode(',', $row->image); // Convertir les URLs des images en tableau
        if($mode == "classic"){
            $output .= '<div class="photo-wall-gallery ' . $mode . '">'; // Début du conteneur
            $increment_class = 0;
                foreach ($image_urls as $url) {
                    $increment_class++;
                    $url = trim($url); // Supprimer les espaces inutiles
                    if($increment_class > 4){
                        if (!empty($url)) {
                            $output .= '<div class="transparent-block lower_photo"><img class="increment_class_'.$increment_class.'" src="' . esc_url($url) . '" alt="Photo"></div>';
                        }
                    }else{
                        if (!empty($url)) {
                            $output .= '<div class="transparent-block top_photo"><img class="increment_class_'.$increment_class.'" src="' . esc_url($url) . '" alt="Photo"></div>';
                        }
                    }
                }
                $output .= '</div>'; // Fin de la galerie
        }else{
            $output .= '<div class="photo-wall-gallery ' . $mode . '">'; // Début du conteneur
            foreach ($image_urls as $url) {
                $url = trim($url); // Supprimer les espaces inutiles
                if (!empty($url)) {
                    $output .= '<img src="' . esc_url($url) . '" alt="Photo">';
                }
            }
            $output .= '</div>'; // Fin de la galerie
        }
        $output .= '</div>'; // Fin du conteneur
    }

    return $output; // Retourner le HTML pour affichage
}

add_shortcode('photo_wall', 'photo_wall_shortcode');
