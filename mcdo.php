<?php


function get_All_Data() {
    $curl = curl_init('https://mcdonaldsfrance.webgeoservices.com/api/stores/search/?authToken=AIzaSyAiX19QNdei5Ja7TA2ahlg3Wb-p6eAUNOc&center=6.128885%3A45.899235&db=prod&dist=50000&limit=20&nb=20&orderDir=desc');

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  // Permet de désactiver le certificat (Verif SSL)
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Permet de ne pas afficher les données recup à l'écran et les stocker dans $data à la place

    $data_all_mcdo = curl_exec($curl);   // Execute l'url et renvoie true --> GOOD       OR      false --> BAD donc problèmes avec URL

    // Si problèmes avec l'URL 
    if($data_all_mcdo === false) {
        var_dump(curl_error($curl));    // Afficher l'erreur
    } else {
        $data_all_mcdo = json_decode($data_all_mcdo, true);   // On met les données JSON dans un tableau associatif
    }


    curl_close($curl);     // On ferme l'URL

    return $data_all_mcdo;
}





function clean_All_Data($data_all_mcdo) {

    $i = 0 ;

    $infoClean_All_Mcdo = [];

    foreach($data_all_mcdo['poiList'] as $unMcDo) {
        $infoClean_All_Mcdo[$i]["Distance"] = round( $data_all_mcdo['poiList'][$i]['dist'] / 1000, 1) ;
        $infoClean_All_Mcdo[$i]["Ville"] = $data_all_mcdo['poiList'][$i]['poi']['location']['city'];
        $infoClean_All_Mcdo[$i]["Adresse"] = $data_all_mcdo['poiList'][$i]['poi']['location']['streetLabel'];
        $infoClean_All_Mcdo[$i]["id"] = $data_all_mcdo['poiList'][$i]['poi']['id'];
        $infoClean_All_Mcdo[$i]["Location"]["latitude"] = $data_all_mcdo['poiList'][$i]['poi']['location']['coords']['lat'];
        $infoClean_All_Mcdo[$i]["Location"]["longitude"] = $data_all_mcdo['poiList'][$i]['poi']['location']['coords']['lon'];
    
        $i++;
    }
    
    return $infoClean_All_Mcdo;

}


function clean_All_Data_Products($infoClean_All_Mcdo) {

    $i = 0;

    $infoClean_All_Mcdo_Products = [];

    $liste_Index_Qui_Merde = [1, 5, 7, 8, 13, 14];

    foreach($infoClean_All_Mcdo as $unMcDo) {
        /*---------------------------------------- On charge les données ----------------------------------------*/
        $curl = curl_init('https://ws.mcdonalds.fr/api/catalog/gomcdo?eatType=EAT_IN&responseGroups=RG.CATEGORY.PICTURES&responseGroups=RG.CATEGORY.POPINS&responseGroups=RG.PRODUCT.CAPPING&responseGroups=RG.PRODUCT.CHOICE_FINISHED_DETAILS&responseGroups=RG.PRODUCT.INGREDIENTS&responseGroups=RG.PRODUCT.PICTURES&responseGroups=RG.PRODUCT.POPINS&responseGroups=RG.PRODUCT.RESTAURANT_STATUS&responseGroups=RG.PROMOTION.POPINS&restaurantRef='.$infoClean_All_Mcdo[$i]['id']);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  // Permet de désactiver le certificat (Verif SSL)
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Permet de ne pas afficher les données recup à l'écran et les stocker dans $data à la place

        $data_all_mcdo_products_dict = curl_exec($curl);   // Execute l'url et renvoie true --> GOOD       OR      false --> BAD donc problèmes avec URL

        // Si problèmes avec l'URL 
        if($data_all_mcdo_products_dict === false) {
            var_dump(curl_error($curl));    // Afficher l'erreur
        } else {
            $data_all_mcdo_products_dict = json_decode($data_all_mcdo_products_dict, true);   // On met les données JSON dans un tableau associatif
        }


        curl_close($curl);     // On ferme l'URL
        /*---------------------------------------- FIN On charge les données ----------------------------------------*/
    

        if (in_array($i, $liste_Index_Qui_Merde)) {
            $infoClean_All_Mcdo_Products[$i]['Produit'] = $data_all_mcdo_products_dict['children'][7]['children'][0]['products'][0]['designation'];
            $infoClean_All_Mcdo_Products[$i]['Disponible'] = $data_all_mcdo_products_dict['children'][7]['children'][0]['products'][0]['available'];
        } 
        elseif ($i == 2) {
            $infoClean_All_Mcdo_Products[$i]['Produit'] = $data_all_mcdo_products_dict['children'][7]['children'][0]['products'][1]['designation'];
            $infoClean_All_Mcdo_Products[$i]['Disponible'] = $data_all_mcdo_products_dict['children'][7]['children'][0]['products'][1]['available'];
        }
        elseif ($i == 10) {
            $infoClean_All_Mcdo_Products[$i]['Produit'] = $data_all_mcdo_products_dict['children'][9]['children'][0]['products'][0]['designation'];
            $infoClean_All_Mcdo_Products[$i]['Disponible'] = $data_all_mcdo_products_dict['children'][9]['children'][0]['products'][0]['available'];
        }
        elseif ($i == 17) {
            $infoClean_All_Mcdo_Products[$i]['Produit'] = $data_all_mcdo_products_dict['children'][7]['children'][0]['products'][1]['designation'];
            $infoClean_All_Mcdo_Products[$i]['Disponible'] = $data_all_mcdo_products_dict['children'][7]['children'][0]['products'][1]['available'];
        }
        else {
            $infoClean_All_Mcdo_Products[$i]['Produit'] = $data_all_mcdo_products_dict['children'][8]['children'][0]['products'][0]['designation'];
            $infoClean_All_Mcdo_Products[$i]['Disponible'] = $data_all_mcdo_products_dict['children'][8]['children'][0]['products'][0]['available'];
        }

        $i++;
    }

    return $infoClean_All_Mcdo_Products;
}




function save_to_json($infoClean_All_Mcdo, $infoClean_All_Mcdo_Products) {

    $i = 0;

    $allCleanInfo = [];

    foreach($infoClean_All_Mcdo as $unMcDo) {
        $allCleanInfo[$i]['Distance'] = $infoClean_All_Mcdo[$i]['Distance'];
        $allCleanInfo[$i]['Ville'] = $infoClean_All_Mcdo[$i]['Ville'];
        $allCleanInfo[$i]['Adresse'] = $infoClean_All_Mcdo[$i]['Adresse'];
        $allCleanInfo[$i]['id'] = $infoClean_All_Mcdo[$i]['id'];
        $allCleanInfo[$i]['Location']['latitude'] = $infoClean_All_Mcdo[$i]["Location"]["latitude"];
        $allCleanInfo[$i]['Location']['longitude'] = $infoClean_All_Mcdo[$i]["Location"]["longitude"];
    
        $allCleanInfo[$i]['Produit'] = $infoClean_All_Mcdo_Products[$i]['Produit'];
        $allCleanInfo[$i]['Disponible'] = $infoClean_All_Mcdo_Products[$i]['Disponible'];
    
        $i++;
    }


    $file = fopen("mcdoUnvailable.js", "w") or die("Unable to open file!");
    $infoClean_All_Mcdo_JSON = json_encode($allCleanInfo, JSON_PRETTY_PRINT);
    fwrite($file, "export const lesMcDo = ".$infoClean_All_Mcdo_JSON);
    fclose($file);

}






















// -------------------------------------------- Execution des fonctions

$data_all_mcdo = get_All_Data();

$infoClean_All_Mcdo = clean_All_Data($data_all_mcdo);

$infoClean_All_Mcdo_Products = clean_All_Data_Products($infoClean_All_Mcdo);

save_to_json($infoClean_All_Mcdo, $infoClean_All_Mcdo_Products);

