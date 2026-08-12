
<?php
if (!function_exists('get_all_records')) {
    /**
     * Récupère les données de tous les utilisateurs/fermes (Admin seulement)
     */
    function get_all_records($modelClass) {
        return $modelClass::withoutGlobalScope('ferme')->get();
    }
}