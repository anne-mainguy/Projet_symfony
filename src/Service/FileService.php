<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileService {

    /**
     * Nettoie la chaine en remplaçant les lettres accentuées
     *
     * @param [type] $string
     * @return string
     */
    public function clear($string){
        $accentComparison = [
            'À' => 'a','Á' => 'a','Â' => 'a','Ã' => 'a','Ä' => 'a','Å' => 'a','à' => 'a','á' => 'a','â' => 'a','ã' => 'a','ä' => 'a','å' => 'a',
            'Ò' => 'o','Ó' => 'o','Ô' => 'o','Õ' => 'o','Ö' => 'o','Ø' => 'o','ò' => 'o','ó' => 'o','ô' => 'o','õ' => 'o','ö' => 'o','ø' => 'o',
            'È' => 'e','É' => 'e','Ê' => 'e','Ë' => 'e','è' => 'e','é' => 'e','ê' => 'e','ë' => 'e',
            'Ç' => 'c','ç' => 'c',
            'Ì' => 'i','Í' => 'i','Î' => 'i','Ï' => 'i','ì' => 'i','í' => 'i','î' => 'i','ï' => 'i',
            'Ù' => 'i','Ú' => 'i','Û' => 'i','Ü' => 'i','ù' => 'i','ú' => 'i','û' => 'i','ü' => 'i',
            'ÿ' => 'y',
            'Ñ' => 'n','ñ' => 'n'
        ];

        return strtr($string, $accentComparison);
    }

    public function uploadFile($nameUser, $imageFile, $fileDirectory, $folder , $oldFile = null){
            
        //methode pour créer le nom du fichier
        $clearPseudo = $this->clear($nameUser);
        $date = date("d-m-Y_H-i-s");
        $newFilename = '/img/' . $folder . '/' . $clearPseudo . '-' . $date .'.' . $imageFile->guessExtension();//ex resultat : pseudo-25-04-2020.jpg
        
        //Déplace le fichier vers l'annuaire où les images sont stockées
        try {
            $imageFile->move(
                $fileDirectory,// image_directory fait référence a un parameter dans config/service.yaml qui indique l'endroit ou doit être enregistré l'image une fois téléchargé (soit ex : /public/img/adsImages) utilisé grace à AbstractController
                $newFilename
            );

            //si il y a un ancien fichier c'est qu'on est en train de modifier l'avatar du profil, il faut donc le supprime
            if($oldFile != null){
                if($oldFile != '/img/avatar/avatar-icon2.png'){
                    //supprime le fichier de l'ancien avatar
                    $filesystem = new Filesystem();
                    $filesystem->remove('.' . $oldFile);
                }
            }
            return $newFilename;
        } catch (FileException $e) {
            return false;
        }
 
    }
}